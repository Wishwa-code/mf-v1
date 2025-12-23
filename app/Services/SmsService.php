<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SmsService
{
    public function __construct(private ?Client $http = null)
    {
        $this->http = $http ?: new Client(['timeout' => 15]);
    }

    /** Normalize as 94xxxxxxxxx (digits only) */
    public static function normalizeLk(string $raw): string
    {
        $clean = preg_replace('/[^\d+]/', '', $raw ?? '');
        if ($clean === '' || $clean === null) {
            throw new \InvalidArgumentException('Empty number');
        }

        if (Str::startsWith($clean, '00')) $clean = '+' . substr($clean, 2);
        $digits = ltrim($clean, '+');

        if (Str::startsWith($digits, '94')) $nsn = substr($digits, 2);
        elseif (Str::startsWith($digits, '0')) $nsn = substr($digits, 1);
        else $nsn = $digits;

        if (!preg_match('/^\d{9}$/', $nsn)) throw new \InvalidArgumentException('Number must be 9 digits');
        if ($nsn[0] !== '7') throw new \InvalidArgumentException('Not a mobile prefix');
        if (!in_array(substr($nsn, 0, 2), ['70','71','72','74','75','76','77','78'], true)) {
            throw new \InvalidArgumentException('Unknown mobile prefix');
        }

        return '94' . $nsn;
    }

    // -------------------------------------------------------------------------
    // Dialog Token (cached)
    // -------------------------------------------------------------------------
    private function getDialogToken(): string
    {
        return Cache::remember('dialog_sms_token', now()->addMinutes(25), function () {
            $lock = Cache::lock('dialog_sms_token_lock', 10);

            try {
                $lock->block(10);

                if ($token = Cache::get('dialog_sms_token')) return $token;

                $resp = $this->http->post('https://e-sms.dialog.lk/api/v1/login', [
                    'headers' => ['Content-Type' => 'application/json'],
                    'json'    => [
                        'username' => config('sms.dialog.username'),
                        'password' => config('sms.dialog.password'),
                    ],
                ]);

                $data = json_decode($resp->getBody()->getContents(), true);
                if (empty($data['token'])) {
                    throw new \RuntimeException('Dialog login failed: no token');
                }

                return $data['token'];
            } finally {
                optional($lock)->release();
            }
        });
    }

    // -------------------------------------------------------------------------
    // Hutch Tokens (cached) + Renew Flow
    // -------------------------------------------------------------------------
    private function getHutchTokens(): array
    {
        // Cache both tokens together
        return Cache::remember('hutch_sms_tokens', now()->addMinutes(25), function () {
            $lock = Cache::lock('hutch_sms_token_lock', 10);

            try {
                $lock->block(10);

                if ($tokens = Cache::get('hutch_sms_tokens')) return $tokens;

                return $this->hutchLogin();
            } finally {
                optional($lock)->release();
            }
        });
    }

    private function hutchLogin(): array
    {
        // ✅ Correct login URL (NOT /api/login/login)
        $url = 'https://bsms.hutch.lk/api/login';

        try {
            $resp = $this->http->post($url, [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => '*/*',
                    'X-API-VERSION' => 'v1',
                ],
                'json' => [
                    'username' => config('sms.hutch.username'),
                    'password' => config('sms.hutch.password'),
                ],
            ]);

            $data = json_decode($resp->getBody()->getContents(), true) ?: [];

            if (empty($data['accessToken'])) {
                throw new \RuntimeException('Hutch login failed: no accessToken');
            }

            $tokens = [
                'accessToken'  => $data['accessToken'],
                'refreshToken' => $data['refreshToken'] ?? null,
            ];

            Cache::put('hutch_sms_tokens', $tokens, now()->addMinutes(25));

            return $tokens;

        } catch (ClientException $e) {
            $status = $e->getResponse()?->getStatusCode();
            Log::info('[SMS][Hutch] Login ClientException', [
                'status' => $status,
                'body'   => (string) $e->getResponse()?->getBody(),
            ]);
            throw $e;
        }
    }

    private function hutchRenewAccessToken(string $refreshToken): array
    {
        // Doc: GET /api/token/accessToken with Authorization Bearer refreshToken
        $url = 'https://bsms.hutch.lk/api/token/accessToken';

        $resp = $this->http->get($url, [
            'headers' => [
                'Accept'        => '*/*',
                'X-API-VERSION' => 'v1',
                'Authorization' => 'Bearer ' . $refreshToken,
            ],
        ]);

        $data = json_decode($resp->getBody()->getContents(), true) ?: [];

        // Different APIs sometimes return: accessToken or token. Keep both.
        $newAccess = $data['accessToken'] ?? $data['token'] ?? null;

        if (!$newAccess) {
            throw new \RuntimeException('Hutch renew failed: no accessToken');
        }

        $tokens = Cache::get('hutch_sms_tokens') ?: [];
        $tokens['accessToken'] = $newAccess;

        // Keep same refresh token
        if (empty($tokens['refreshToken'])) {
            $tokens['refreshToken'] = $refreshToken;
        }

        Cache::put('hutch_sms_tokens', $tokens, now()->addMinutes(25));

        return $tokens;
    }

    private function forgetHutchTokens(): void
    {
        Cache::forget('hutch_sms_tokens');
    }

    // -------------------------------------------------------------------------
    // Provider-agnostic send
    // -------------------------------------------------------------------------
    public function send(string $provider, string $mask, string $rawMsisdn, string $message): array
    {
        $msisdn = self::normalizeLk($rawMsisdn);

        if ($provider === 'Dialog') {
            $token = $this->getDialogToken();

            usleep(300_000);

            $resp = $this->http->post('https://e-sms.dialog.lk/api/v2/sms', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'json' => [
                    'msisdn'         => [['mobile' => $msisdn]],
                    'message'        => $message,
                    'sourceAddress'  => $mask ?: 'DefaultMask',
                    'transaction_id' => (int) (now()->format('ymdHis') . random_int(10, 99)),
                    'payment_method' => 0,
                ],
            ]);

            $data = json_decode($resp->getBody()->getContents(), true) ?: [];
            Log::info('Dialog SMS resp', $data);

            return $data;
        }

        if ($provider === 'Hutch') {
            // 1) get cached token
            $tokens = $this->getHutchTokens();

            // 2) send with retry-once if 401
            return $this->sendViaHutchWithRetry(
                mask: $mask,
                msisdn: $msisdn,
                message: $message,
                accessToken: (string) ($tokens['accessToken'] ?? ''),
                refreshToken: (string) ($tokens['refreshToken'] ?? '')
            );
        }

        throw new \InvalidArgumentException('Unknown provider ' . $provider);
    }

    private function sendViaHutchWithRetry(
        string $mask,
        string $msisdn,
        string $message,
        string $accessToken,
        string $refreshToken
    ): array {
        usleep(300_000);

        try {
            return $this->hutchSend($mask, $msisdn, $message, $accessToken);

        } catch (ClientException $e) {
            $status = $e->getResponse()?->getStatusCode();

            // ✅ If 401, try renew -> retry once.
            if ($status === 401) {
                Log::info('[SMS][Hutch] Send got 401, trying renew/login', [
                    'body' => (string) $e->getResponse()?->getBody(),
                ]);

                // Clear current cached tokens to avoid reuse
                $this->forgetHutchTokens();

                // Try renew if refresh token exists
                if (!empty($refreshToken)) {
                    try {
                        $newTokens = $this->hutchRenewAccessToken($refreshToken);
                        $newAccess = (string) ($newTokens['accessToken'] ?? '');

                        usleep(300_000);
                        return $this->hutchSend($mask, $msisdn, $message, $newAccess);

                    } catch (\Throwable $renewErr) {
                        Log::info('[SMS][Hutch] Renew failed, will try fresh login', [
                            'err' => $renewErr->getMessage(),
                        ]);
                    }
                }

                // If renew failed or refresh missing -> login and retry once
                $tokens = $this->hutchLogin();
                $newAccess = (string) ($tokens['accessToken'] ?? '');

                usleep(300_000);
                return $this->hutchSend($mask, $msisdn, $message, $newAccess);
            }

            throw $e;
        }
    }

    private function hutchSend(string $mask, string $msisdn, string $message, string $accessToken): array
    {
        $resp = $this->http->post('https://bsms.hutch.lk/api/sendsms', [
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'        => '*/*',
                'X-API-VERSION' => 'v1',
                'Authorization' => 'Bearer ' . $accessToken,
            ],
            'json' => [
                'campaignName'          => 'campaign_' . now()->format('YmdHis'),
                'mask'                  => $mask ?: 'DefaultMask',
                'numbers'               => $msisdn,
                'content'               => $message,
                'deliveryReportRequest' => true,
            ],
        ]);

        $data = json_decode($resp->getBody()->getContents(), true) ?: [];
        Log::info('Hutch SMS resp', $data);

        return $data;
    }

}

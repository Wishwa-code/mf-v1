<?php

namespace App\Services;

use GuzzleHttp\Client;
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
        if ($clean === '' || $clean === null) throw new \InvalidArgumentException('Empty number');

        if (Str::startsWith($clean, '00')) $clean = '+' . substr($clean, 2);
        $digits = ltrim($clean, '+');

        if (Str::startsWith($digits, '94')) $nsn = substr($digits, 2);
        elseif (Str::startsWith($digits, '0')) $nsn = substr($digits, 1);
        else $nsn = $digits;

        if (!preg_match('/^\d{9}$/', $nsn)) throw new \InvalidArgumentException('Number must be 9 digits');
        if ($nsn[0] !== '7') throw new \InvalidArgumentException('Not a mobile prefix');
        if (!in_array(substr($nsn,0,2), ['70','71','72','74','75','76','77','78'], true))
            throw new \InvalidArgumentException('Unknown mobile prefix');

        return '94'.$nsn;
    }

    /** Cached Dialog token with locking */
    private function getDialogToken(): string
    {
        return Cache::remember('dialog_sms_token', now()->addMinutes(25), function () {
            $lock = Cache::lock('dialog_sms_token_lock', 10);
            try {
                $lock->block(10);
                // Double-check (another process may have set it)
                if ($token = Cache::get('dialog_sms_token')) return $token;

                $resp = $this->http->post('https://e-sms.dialog.lk/api/v1/login', [
                    'headers' => ['Content-Type'=>'application/json'],
                    'json' => ['username'=>config('sms.dialog.username'), 'password'=>config('sms.dialog.password')],
                ]);
                $data = json_decode($resp->getBody()->getContents(), true);
                if (empty($data['token'])) throw new \RuntimeException('Dialog login failed: no token');

                return $data['token'];
            } finally {
                optional($lock)->release();
            }
        });
    }

    /** Cached Hutch access token with locking */
    private function getHutchToken(): string
    {
        return Cache::remember('hutch_sms_token', now()->addMinutes(25), function () {
            $lock = Cache::lock('hutch_sms_token_lock', 10);
            try {
                $lock->block(10);
                if ($token = Cache::get('hutch_sms_token')) return $token;

                $resp = $this->http->post('https://bsms.hutch.lk/api/login/login', [
                    'headers' => ['Content-Type'=>'application/json', 'Accept'=>'*/*', 'X-API-VERSION'=>'v1'],
                    'json' => ['username'=>config('sms.hutch.username'), 'password'=>config('sms.hutch.password')],
                ]);
                $data = json_decode($resp->getBody()->getContents(), true);
                if (empty($data['accessToken'])) throw new \RuntimeException('Hutch login failed: no accessToken');

                return $data['accessToken'];
            } finally {
                optional($lock)->release();
            }
        });
    }

    /** Provider-agnostic send */
    public function send(string $provider, string $mask, string $rawMsisdn, string $message): array
    {
        $msisdn = self::normalizeLk($rawMsisdn);

        if ($provider === 'Dialog') {
            $token = $this->getDialogToken();

            // simple rate limiter: at most 1 msg / 300ms
            usleep(300_000);

            $resp = $this->http->post('https://e-sms.dialog.lk/api/v2/sms', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ],
                'json' => [
                    'msisdn'         => [['mobile' => $msisdn]],
                    'message'        => $message,
                    'sourceAddress'  => $mask ?: 'DefaultMask',
                    'transaction_id' => (int) (now()->format('ymdHis').random_int(10,99)),
                    'payment_method' => 0,
                ],
            ]);
            $data = json_decode($resp->getBody()->getContents(), true) ?: [];
            Log::info('Dialog SMS resp', $data);
            return $data;
        }

        if ($provider === 'Hutch') {
            $token = $this->getHutchToken();

            usleep(300_000);

            $resp = $this->http->post('https://bsms.hutch.lk/api/sendsms/sendsms', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => '*/*',
                    'X-API-VERSION' => 'v1',
                    'Authorization' => 'Bearer '.$token,
                ],
                'json' => [
                    'campaignName'          => 'campaign_'.now()->format('YmdHis'),
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

        throw new \InvalidArgumentException('Unknown provider '.$provider);
    }
}

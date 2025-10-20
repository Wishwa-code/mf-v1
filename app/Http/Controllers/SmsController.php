<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SmsController extends Controller
{
    public function index($customer_id, $message, $type)
    {
        Log::info('[SMS] index() start', compact('customer_id', 'type'));

        try {
            $company  = tableWithBranch('company')->first();
            $customer = tableWithBranch('customer')->where('idCustomer', $customer_id)->first();

            if (!$company || !$customer) {
                Log::info('[SMS] Missing company or customer', [
                    'company' => (bool)$company,
                    'customer' => (bool)$customer,
                    'customer_id' => $customer_id
                ]);
                return response()->json(['ok' => true, 'message' => 'Skipped, company or customer missing (Logged)'], 200);
            }

            // Ensure provider column exists
            try {
                if (!Schema::hasColumn('company', 'provider')) {
                    DB::statement("ALTER TABLE company ADD COLUMN provider VARCHAR(50) NOT NULL DEFAULT 'Dialog'");
                    Log::info('[SMS] Added missing provider column');
                }
            } catch (\Exception $e) {
                Log::info('[SMS] Provider column check failed', ['error' => $e->getMessage()]);
            }

            // Normalize mobile
            [$normalized, $err] = $this->normalizeSriLankanMobile($customer->Contact_No);
            if ($err) {
                Log::info('[SMS] Invalid mobile number', ['raw' => $customer->Contact_No, 'error' => $err]);
                return response()->json(['ok' => true, 'message' => 'Invalid mobile (Logged)'], 200);
            }

            $provider = $company->provider ?? 'Dialog';
            Log::info('[SMS] Provider selected', ['provider' => $provider]);

            if ($provider === 'Dialog') {
                $this->sendViaDialog($company, $customer_id, $customer, $message, $type, $normalized);
            } else {
                $this->sendViaHutch($company, $customer_id, $customer, $message, $type, $normalized);
            }

            return response()->json(['ok' => true, 'message' => 'SMS process completed (Logged)'], 200);
        } catch (\Exception $e) {
            Log::info('[SMS] Unhandled Exception', ['error' => $e->getMessage()]);
            return response()->json(['ok' => true, 'message' => 'Process continued, logged internally'], 200);
        }
    }

    private function sendViaDialog($company, $customer_id, $customer, $message, $type, $normalized)
    {
        try {
            $dialogToken = Session::get('token');
            if (!$dialogToken) {
                try {
                    Log::info('[SMS][Dialog] Logging in');
                    $client   = new Client(['base_uri' => 'https://e-sms.dialog.lk/api/v1/']);
                    $response = $client->post('login', [
                        'headers' => ['Content-Type' => 'application/json'],
                        'json' => [
                            'username' => 'ASIPIYA',
                            'password' => 'Dialog@123',
                        ],
                    ]);
                    $responseData = json_decode($response->getBody()->getContents(), true);
                    if (!empty($responseData['token'])) {
                        $dialogToken = $responseData['token'];
                        Session::put('token', $dialogToken);
                        Session::put('userData', $responseData['userData'] ?? []);
                    }
                } catch (\Exception $e) {
                    Log::info('[SMS][Dialog] Login error', ['error' => $e->getMessage()]);
                }
            }

            if ($dialogToken) {
                $client = new Client(['base_uri' => 'https://e-sms.dialog.lk/api/v2/']);
                $txid   = intval($company->id . date('ymdHis') . rand(10, 99));
                $mask   = $company->mask ?? 'DefaultMask';

                $response = $client->post('sms', [
                    'headers' => [
                        'Content-Type'  => 'application/json',
                        'Authorization' => 'Bearer ' . $dialogToken,
                    ],
                    'json' => [
                        'msisdn'         => [['mobile' => $normalized]],
                        'message'        => $message,
                        'sourceAddress'  => $mask,
                        'transaction_id' => $txid,
                        'payment_method' => 0,
                    ],
                ]);

                $data = json_decode($response->getBody()->getContents(), true);
                Log::info('[SMS][Dialog] Send Response', $data);

                if (($data['status'] ?? '') === 'success') {
                    $this->logSMS($customer_id, $customer, $message, $type);
                }
            }
        } catch (\Exception $e) {
            Log::info('[SMS][Dialog] Exception', ['error' => $e->getMessage()]);
        }
    }

    private function sendViaHutch($company, $customer_id, $customer, $message, $type, $normalized)
    {
        try {
            $accessToken = Session::get('hutch_access_token');
            if (!$accessToken) {
                try {
                    Log::info('[SMS][Hutch] Logging in');
                    $client = new Client(['base_uri' => 'https://bsms.hutch.lk/api/login']);
                    $response = $client->post('login', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => '*/*',
                            'X-API-VERSION' => 'v1',
                        ],
                        'json' => [
                            'username' => 'finance.asipiya@gmail.com',
                            'password' => 'Asipiya@hutch123',
                        ],
                    ]);
                    $data = json_decode($response->getBody()->getContents(), true);
                    if (!empty($data['accessToken'])) {
                        $accessToken = $data['accessToken'];
                        Session::put('hutch_access_token', $accessToken);
                        Session::put('hutch_refresh_token', $data['refreshToken'] ?? null);
                    }
                } catch (\Exception $e) {
                    Log::info('[SMS][Hutch] Login Error', ['error' => $e->getMessage()]);
                }
            }

            if ($accessToken) {
                $client = new Client(['base_uri' => 'https://bsms.hutch.lk/api/sendsms']);
                $mask = $company->mask ?? 'DefaultMask';
                $response = $client->post('sendsms', [
                    'headers' => [
                        'Content-Type'  => 'application/json',
                        'Accept'        => '*/*',
                        'X-API-VERSION' => 'v1',
                        'Authorization' => 'Bearer ' . $accessToken,
                    ],
                    'json' => [
                        'campaignName' => 'campaign_' . date('YmdHis'),
                        'mask' => $mask,
                        'numbers' => $normalized,
                        'content' => $message,
                        'deliveryReportRequest' => true,
                    ],
                ]);

                $result = json_decode($response->getBody()->getContents(), true);
                Log::info('[SMS][Hutch] Send Response', $result);

                if (!empty($result['serverRef'])) {
                    $this->logSMS($customer_id, $customer, $message, $type);
                }
            }
        } catch (\Exception $e) {
            Log::info('[SMS][Hutch] Exception', ['error' => $e->getMessage()]);
        }
    }

    private function normalizeSriLankanMobile(string $raw): array
    {
        $clean = preg_replace('/[^\d+]/', '', $raw ?? '');
        if ($clean === null || $clean === '') return [null, 'Empty number'];
        if (Str::startsWith($clean, '00')) $clean = '+' . substr($clean, 2);
        $digits = ltrim($clean, '+');
        if (Str::startsWith($digits, '94')) $nsn = substr($digits, 2);
        elseif (Str::startsWith($digits, '0')) $nsn = substr($digits, 1);
        else $nsn = $digits;
        if (!preg_match('/^\d{9}$/', $nsn)) return [null, 'Number must be 9 digits'];
        if ($nsn[0] !== '7') return [null, 'Not mobile'];
        $prefix = substr($nsn, 0, 2);
        $valid = ['70','71','72','74','75','76','77','78'];
        if (!in_array($prefix, $valid, true)) return [null, "Invalid prefix {$prefix}"];
        return ['94' . $nsn, null];
    }

    private function logSMS($customer_id, $customer, $message, $type)
    {
        try {
            DB::table('sms')->insert([
                'cus_id' => $customer_id,
                'cus_name' => $customer->First_Name . ' ' . $customer->Last_Name,
                'contact_no' => $customer->Contact_No,
                'message' => $message,
                'type' => $type,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'branch_id' => session('branch_id'),
            ]);
            Log::info('[SMS] Logged SMS', ['cus_id' => $customer_id, 'type' => $type]);
        } catch (\Exception $e) {
            Log::info('[SMS] Log error', ['error' => $e->getMessage()]);
        }
    }

    public function create() { return view('pages.SMS_Format'); }

    public function store(Request $request)
    {
        try {
            DB::table('sms_template')
                ->where('branch_id', session('branch_id'))
                ->where('type', '=', $request->smsTypeSelect)
                ->update(['template' => $request->smsContent]);
            Log::info('[SMS] Template updated', ['type' => $request->smsTypeSelect]);
        } catch (\Exception $e) {
            Log::info('[SMS] Template update error', ['error' => $e->getMessage()]);
        }
        return response()->json(['ok' => true, 'message' => 'Saved (Logged)'], 200);
    }

    public function show(Request $request)
    {
        try {
            $sms_template = tableWithBranch('sms_template')->where('type', '=', $request->smsTypeSelect)->first();
            Log::info('[SMS] Template fetched', ['type' => $request->smsTypeSelect]);
            return response()->json(['sms_template' => $sms_template, 'ok' => true], 200);
        } catch (\Exception $e) {
            Log::info('[SMS] Template fetch error', ['error' => $e->getMessage()]);
            return response()->json(['sms_template' => null, 'ok' => true], 200);
        }
    }

    public function edit(Request $request)
    {
        try {
            DB::table('sms_template')
                ->where('branch_id', session('branch_id'))
                ->where('type', '=', $request->smsTypeSelect)
                ->update(['status' => $request->smsSendStatus]);
            Log::info('[SMS] Status updated', ['type' => $request->smsTypeSelect]);
        } catch (\Exception $e) {
            Log::info('[SMS] Status update error', ['error' => $e->getMessage()]);
        }
        return response()->json(['ok' => true, 'message' => 'Updated (Logged)'], 200);
    }

    public function SMS_History()
    {
        try {
            $smsRecords = tableWithBranch('sms')->get();
            Log::info('[SMS] History loaded', ['count' => $smsRecords->count()]);
            return view('pages.SMS_History', compact('smsRecords'));
        } catch (\Exception $e) {
            Log::info('[SMS] History load error', ['error' => $e->getMessage()]);
            $smsRecords = collect();
            return view('pages.SMS_History', compact('smsRecords'));
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($customer_id, $message, $type)
    {
        $company  = tableWithBranch('company')->first();
        $customer = tableWithBranch('customer')->where('idCustomer', $customer_id)->first();

        if (!$company || !$customer) {
            return response()->json(['error' => 'Company or Customer not found'], 404);
        }

        // Ensure provider column exists (your comment mentioned "status" but the code checks provider)
        if (!Schema::hasColumn('company', 'provider')) {
            DB::statement("ALTER TABLE company ADD COLUMN provider VARCHAR(50) NOT NULL DEFAULT 'Dialog'");
        }

        // 1) Normalize & validate customer phone number to "94xxxxxxxxx" (no plus)
        [$normalized, $err] = $this->normalizeSriLankanMobile($customer->Contact_No);
        if ($err) {
            return response()->json(['error' => "Invalid mobile number for SMS: {$err}"], 422);
        }

        try {
            if (($company->provider ?? 'Dialog') === "Dialog") {
                // ====== DIALOG LOGIN (if needed) ======
                $dialogToken = Session::get('token');
                if (!$dialogToken) {
                    try {
                        $client   = new Client(['base_uri' => 'https://e-sms.dialog.lk/api/v1/']);
                        $response = $client->post('login', [
                            'headers' => ['Content-Type' => 'application/json'],
                            'json'    => [
                                'username' => 'ASIPIYA',
                                'password' => 'Dialog@123',
                            ],
                        ]);
                        $responseData = json_decode($response->getBody()->getContents(), true);
                        if (!empty($responseData['token'])) {
                            $dialogToken = $responseData['token'];
                            Session::put('token', $dialogToken);
                            Session::put('userData', $responseData['userData'] ?? []);
                        } else {
                            return response()->json(['error' => 'Dialog login failed: no token'], 500);
                        }
                    } catch (\Exception $e) {
                        Log::error('Dialog SMS Login Error: ' . $e->getMessage());
                        return response()->json(['error' => 'Dialog login failed'], 500);
                    }
                }

                // ====== DIALOG SEND ======
                try {
                    $client            = new Client(['base_uri' => 'https://e-sms.dialog.lk/api/v2/']);
                    $newTransactionId  = intval($company->id . date('ymdHis') . rand(10, 99));
                    $mask              = $company->mask ?? 'DefaultMask';

                    $response = $client->post('sms', [
                        'headers' => [
                            'Content-Type'  => 'application/json',
                            'Authorization' => 'Bearer ' . $dialogToken,
                        ],
                        'json' => [
                            // Dialog expects: [{"mobile":"9477xxxxxxx"}]
                            'msisdn'          => [['mobile' => $normalized]],
                            'message'         => $message,
                            'sourceAddress'   => $mask,
                            'transaction_id'  => $newTransactionId,
                            'payment_method'  => 0,
                        ],
                    ]);

                    $responseData = json_decode($response->getBody()->getContents(), true);
                    Log::info(['Dialog SMS response' => $responseData]);

                    if (($responseData['status'] ?? null) === "success") {
                        $this->logSMS($customer_id, $customer, $message, $type);
                    }
                    return response()->json($responseData);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }

            } else {
                // ====== HUTCH LOGIN (if needed) ======
                $accessToken = Session::get('hutch_access_token');
                if (!$accessToken) {
                    try {
                        $client   = new Client(['base_uri' => 'https://bsms.hutch.lk/api/login']);
                        $response = $client->post('login', [
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'Accept'       => '*/*',
                                'X-API-VERSION'=> 'v1',
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
                        } else {
                            return response()->json(['error' => 'Hutch login failed: no accessToken'], 500);
                        }
                    } catch (\Exception $e) {
                        Log::error('Hutch SMS Login Error: ' . $e->getMessage());
                        return response()->json(['error' => 'Hutch login failed'], 500);
                    }
                }

                // ====== HUTCH SEND ======
                try {
                    $client   = new Client(['base_uri' => 'https://bsms.hutch.lk/api/sendsms']);
                    $mask     = $company->mask ?? 'DefaultMask';

                    $response = $client->post('sendsms', [
                        'headers' => [
                            'Content-Type'  => 'application/json',
                            'Accept'        => '*/*',
                            'X-API-VERSION' => 'v1',
                            'Authorization' => 'Bearer ' . $accessToken,
                        ],
                        'json' => [
                            'campaignName'           => 'campaign_' . date('YmdHis'),
                            'mask'                   => $mask,
                            // Hutch accepts a string or CSV; here we pass one normalized number
                            'numbers'                => $normalized,
                            'content'                => $message,
                            'deliveryReportRequest'  => true,
                        ],
                    ]);

                    $result = json_decode($response->getBody()->getContents(), true);
                    Log::info(['Hutch SMS response' => $result]);

                    if (!empty($result['serverRef'])) {
                        $this->logSMS($customer_id, $customer, $message, $type);
                    }
                    return response()->json($result);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unhandled error: ' . $e->getMessage()], 500);
        }
    }



    /**
     * Normalize a Sri Lankan mobile number to "94xxxxxxxxx" (digits only, no plus).
     * Accepts inputs like: 0771234567, +94771234567, 94771234567, 077-123-4567, etc.
     * Validates as a mobile number (starts with 7 and total 9 digits after stripping trunk/country).
     *
     * @param  string $raw
     * @return array [string|null $normalized, string|null $error]
     */
    private function normalizeSriLankanMobile(string $raw): array
    {
        // Strip everything except digits and leading plus
        $clean = preg_replace('/[^\d+]/', '', $raw ?? '');

        if ($clean === null || $clean === '') {
            return [null, 'Empty number'];
        }

        // Convert leading 00 to +
        if (Str::startsWith($clean, '00')) {
            $clean = '+' . substr($clean, 2);
        }

        // Remove plus for internal processing
        $digits = ltrim($clean, '+');

        // If starts with 94 (country code) drop it to get national significant number
        if (Str::startsWith($digits, '94')) {
            $nsn = substr($digits, 2); // 9 digits expected
        } elseif (Str::startsWith($digits, '0')) {
            // Local trunk prefix -> drop it
            $nsn = substr($digits, 1); // 9 digits expected
        } else {
            // Maybe they already supplied 9 digits
            $nsn = $digits;
        }

        // Must be exactly 9 digits for Sri Lanka NSN
        if (!preg_match('/^\d{9}$/', $nsn)) {
            return [null, 'Number must be 9 digits after removing country/trunk codes'];
        }

        // Mobile-only validation (prefix 7xxxxxxxx). Adjust list if needed.
        if ($nsn[0] !== '7') {
            return [null, 'Not a mobile prefix (must start with 7)'];
        }

        // Optionally, validate known mobile ranges (70,71,72,75,76,77,78)
        $prefix2 = substr($nsn, 0, 2);
        $validMobilePrefixes = ['70','71','72','75','76','77','78'];
        if (!in_array($prefix2, $validMobilePrefixes, true)) {
            return [null, "Unknown mobile prefix '{$prefix2}'"];
        }

        // Return in desired format: 94 + 9 digits (no plus)
        return ['94' . $nsn, null];
    }


    private function logSMS($customer_id, $customer, $message, $type)
    {
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
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
//        $userData= DB::table('center')->get();
        return view('pages.SMS_Format');
//        SMS_Format
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $smsContent=$request->smsContent;
        $smsTypeSelect=$request->smsTypeSelect;

        DB::table('sms_template')->where('branch_id', session('branch_id'))->where('type','=',$smsTypeSelect)->update([
            'template'=>$smsContent
        ]);

        return response()->json(['message' => 'Data saved successfully','id'=>'1'], 200);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $smsTypeSelect=$request->smsTypeSelect;
        $sms_template= tableWithBranch('sms_template')->where('type','=',$smsTypeSelect)->first();
        return response()->json(['sms_template' =>$sms_template,'id'=>'1'], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $smsSendStatus=$request->smsSendStatus;
        $smsTypeSelect=$request->smsTypeSelect;

        DB::table('sms_template')->where('branch_id', session('branch_id'))->where('type','=',$smsTypeSelect)->update([
            'status'=>$smsSendStatus
        ]);

        return response()->json(['message' => 'Data saved successfully','id'=>'1'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }



    public function SMS_History()
    {
        $smsRecords = tableWithBranch('sms')->get();
        return view('pages.SMS_History', compact('smsRecords'));
    }



}

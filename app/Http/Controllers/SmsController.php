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
        $company = tableWithBranch('company')->first();
        $customer = tableWithBranch('customer')->where('idCustomer', $customer_id)->first();

        if (!$company || !$customer) {
            return response()->json(['error' => 'Company or Customer not found'], 404);
        }

// Dynamically add 'status' column if it does not exist
        if (!Schema::hasColumn('company', 'provider')) {
            DB::statement("ALTER TABLE company ADD COLUMN provider VARCHAR(50) NOT NULL DEFAULT 'Dialog'");
        }


        try {
            if ($company->provider === "Dialog") {
                // Login to Dialog
                $dialogToken = Session::get('token');
                if (!$dialogToken) {
                    try {
                        $client = new Client(['base_uri' => 'https://e-sms.dialog.lk/api/v1/']);
                        $response = $client->post('login', [
                            'headers' => ['Content-Type' => 'application/json'],
                            'json' => [
                                'username' => 'ASIPIYA',
                                'password' => 'Dialog@123',
                            ],
                        ]);
                        $responseData = json_decode($response->getBody()->getContents(), true);
                        if (isset($responseData['token'])) {
                            $dialogToken = $responseData['token'];
                            Session::put('token', $dialogToken);
                            Session::put('userData', $responseData['userData'] ?? []);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Dialog SMS Login Error: ' . $e->getMessage());
                        return response()->json(['error' => 'Dialog login failed'], 500);
                    }
                }

                // Send Dialog SMS
                try {
                    $client = new Client(['base_uri' => 'https://e-sms.dialog.lk/api/v2/']);
                    $newTransactionId = intval($company->id . date('ymdHis') . rand(10, 99));
                    $response = $client->post('sms', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Authorization' => 'Bearer ' . $dialogToken,
                        ],
                        'json' => [
                            'msisdn' => [['mobile' => $customer->Contact_No]],
                            'message' => $message,
                            'sourceAddress' => $company->mask,
                            'transaction_id' => $newTransactionId,
                            'payment_method' => 0,
                        ],
                    ]);
                    $responseData = json_decode($response->getBody()->getContents(), true);
                    Log::info($responseData);
                    if ($responseData['status'] === "success") {
                        $this->logSMS($customer_id, $customer, $message, $type);
                    }
                    return response()->json($responseData);
                } catch (\Exception $e) {
                    return response()->json(['error' => $e->getMessage()], 500);
                }

            } else {
                // Login to Hutch
                $accessToken = Session::get('hutch_access_token');
                if (!$accessToken) {
                    try {
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
                        if (isset($data['accessToken'])) {
                            $accessToken = $data['accessToken'];
                            Session::put('hutch_access_token', $accessToken);
                            Session::put('hutch_refresh_token', $data['refreshToken'] ?? null);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Hutch SMS Login Error: ' . $e->getMessage());
                        return response()->json(['error' => 'Hutch login failed'], 500);
                    }
                }

                // Send Hutch SMS
                try {
                    $client = new Client(['base_uri' => 'https://bsms.hutch.lk/api/sendsms']);
                    $response = $client->post('sendsms', [
                        'headers' => [
                            'Content-Type' => 'application/json',
                            'Accept' => '*/*',
                            'X-API-VERSION' => 'v1',
                            'Authorization' => 'Bearer ' . $accessToken,
                        ],
                        'json' => [
                            'campaignName' => 'campaign_' . date('YmdHis'),
                            'mask' => $company->mask ?? 'DefaultMask',
                            'numbers' => $customer->Contact_No,
                            'content' => $message,
                            'deliveryReportRequest' => true,
                        ],
                    ]);
                    $result = json_decode($response->getBody()->getContents(), true);
                    if (isset($result['serverRef'])) {
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

<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SmsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($customer_id,$message,$type)
    {
        // Retrieve the token from session
        $token = Session::get('token');

        if (!$token) {
            return response()->json(['error' => 'Token not found in session'], 401);
        }else{

        }

        $client = new Client([
            'base_uri' => 'https://e-sms.dialog.lk/api/v2/',
        ]);
        $customer=tableWithBranch('customer')->where('idCustomer',$customer_id)->first();
        try {

            $newTransactionId = random_int(1, 999999999999999999);

            $company=DB::table('company')->first();

            $response = $client->post('sms', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'json' => [
                    'msisdn' => [
                        [
                            'mobile' => $customer->Contact_No,
                        ]
                    ],
                    'message' => $message,
                    'sourceAddress' => $company->mask,
                    'transaction_id' => $newTransactionId,
                    'payment_method' => 0,

                ],
            ]);
            $responseData = json_decode($response->getBody()->getContents(), true);

            if ($responseData['status']==="success") {
                DB::table('sms')->insert([
                    'cus_id' => $customer_id,
                    'cus_name' => $customer->First_Name . ' ' . $customer->Last_Name,
                    'contact_no' => $customer->Contact_No,
                    'message' => $message,
                    'type' => $type,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                    'branch_id' => session('branch_id')
                ]);
            }

            return response()->json($responseData);
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
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

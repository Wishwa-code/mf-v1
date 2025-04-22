<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KYCController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers=tableWithBranch('customer')->get();
        return view('pages.Insurance.KYC',compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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

    // KYCController.php
    public function loadSection($section, $id)
    {
        $customer = DB::table('customer')->where('idCustomer', $id)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer not found'], 404);
        }
        Log::info($section);
        switch ($section) {
            case 'basic':
                return view('pages.Insurance.kyc.basic', compact('customer'));
            case 'guardian':
                return view('pages.Insurance.kyc.guardian', compact('customer'));
            case 'documents':
                $documents = DB::table('customer_documents')
                    ->where('Customer_idCustomer', $id)
                    ->get();
                return view('pages.Insurance.kyc.documents', compact('documents'));
            case 'loans':
                $loans = DB::table('customer_loan')
                    ->where('Customer_idCustomer', $id)
                    ->orderByDesc('Date_Time')
                    ->get();

                return view('pages.Insurance.kyc.loans', compact('loans'));
            case 'loanSummary':
                $guaranteedLoans = DB::table('witness as w')
                    ->join('customer_loan as cl', 'w.Customer_Loan_idCustomer_Loan', '=', 'cl.idCustomer_Loan')
                    ->join('customer as c', 'cl.Customer_idCustomer', '=', 'c.idCustomer')
                    ->where('w.cus_id', $id)
                    ->select(
                        'cl.idCustomer_Loan',
                        'cl.Loan_No',
                        'cl.Date_Time',
                        'cl.Amount',
                        'cl.Interest_Rate',
                        'cl.Installment_Count',
                        'cl.Balance_Amount',
                        'cl.Status',
                        'c.cus_number',
                        'c.First_Name',
                        'c.Last_Name'
                    )
                    ->orderByDesc('cl.Date_Time')
                    ->get();
                return view('pages.Insurance.kyc.guranteed_loan', compact('guaranteedLoans'));
            case 'RoadMap':
                $customer_log=tableWithBranch('customer_log','customer_log')
                    ->join('user','customer_log.user', '=', 'user.id')
                    ->where('customer_id','=',$id)
                    ->get();
                return view('pages.Insurance.kyc.RoadMap', compact('customer_log'));
            case 'insurance':
                return view('pages.Insurance.kyc.insurance', compact('customer'));
            case 'history':
                return view('pages.Insurance.kyc.history', compact('customer'));
            default:
                return response()->json(['error' => 'Invalid section'], 400);
        }
    }


}

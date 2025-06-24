<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CapitalBalanceController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index($loan_id,$check=0)
    {
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $loan_id)->first();
        if ($loan) {
            $installment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                ->get();

            $interestBalanceSum = round($installment->sum('Interest_Balance'),2);
            $capitalBalanceSum = round($installment->sum('capital_balance'),2);
            $totalBalance = round($installment->sum('Total_Balance'),2);

            $status=0;
            if ($totalBalance<1){
                $status=1;
                $interestBalanceSum=0;
                $capitalBalanceSum=0;
                $totalBalance=0;


                DB::table('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                    ->update([
                        'Interest_Balance' => 0.00,
                        'Panalty_Balance' => 0.00,
                        'capital_balance' => 0.00,
                        'Total_Balance' => 0.00,
                        'Status' => '1',
                        'Panelty_status' => '2',
                    ]);

            }

            updateWithBranch('customer_loan', 'idCustomer_Loan', $loan_id, [
                'capital_balance' => $capitalBalanceSum,
                'installment_balance' => $interestBalanceSum,
                'Balance_Amount' => $totalBalance,
                'Status' => $status,
            ]);

        }
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
}

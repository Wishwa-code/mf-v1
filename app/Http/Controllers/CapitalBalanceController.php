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
    public function index($loan_id,$status=0)
    {
        $loan = tableWithBranch('customer_loan')->where('idCustomer_Loan', $loan_id)->first();
        if ($loan) {
            $installment = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                ->get();

            $penaltyBalanceSum = $installment->sum('Panalty_Balance');
            $interestBalanceSum = $installment->sum('Interest_Balance');
            $capitalBalanceSum = $installment->sum('capital_balance');
            $totalBalance = $capitalBalanceSum + $interestBalanceSum + $penaltyBalanceSum;

            updateWithBranch('customer_loan', 'idCustomer_Loan', $loan_id, [
                'capital_balance' => $capitalBalanceSum,
                'installment_balance' => $interestBalanceSum,
                'Balance_Amount' => $totalBalance,
            ]);

            // Re-fetch the loan after update
            $loan_2 = tableWithBranch('customer_loan')->where('idCustomer_Loan', $loan_id)->where('Status','=','0')->first();

            if($loan_2){
                if ($loan_2->Balance_Amount < 1) {
                    updateWithBranch('customer_loan', 'idCustomer_Loan', $loan_id, [
                        'Balance_Amount' => '0.00',
                        'capital_balance' => '0.00',
                        'installment_balance' => '0.00',
                        'Status' => '1',
                    ]);

                    updateWithBranch('installments', 'Customer_Loan_idCustomer_Loan', $loan_id, [
                        'Panalty_Balance' => '0.00',
                        'Interest_Balance' => '0.00',
                        'capital_balance' => '0.00',
                        'Saving_balance' => '0.00',
                        'Total_Balance' => '0.00',
                        'Status' => '1',
                    ]);
                } else {
                    // Balance is greater than or equal to 2
                    updateWithBranch('customer_loan', 'idCustomer_Loan', $loan_id, [
                        'Status' => '0'
                    ]);
                }
            }

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

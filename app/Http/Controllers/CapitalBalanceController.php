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
    public function create($loan_id)
    {
        $loans = tableWithBranch('customer_loan')->where('idCustomer_Loan','=',$loan_id)->where('Status','!=','1')->first();
        if ($loans) {
            $loan_id=$loans->idCustomer_Loan;
            $capital_amount=$loans->Amount;
            $interest_amount=$loans->Interest_Amount;

            $ins_capital=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan_id)->sum('capital_amount');
            $ins_interest=tableWithBranch('installments')->where('Customer_Loan_idCustomer_Loan','=',$loan_id)->sum('interest_amount');
            $capital_additional_amount=0;
            $interest_additional_amount=0;

            if ($capital_amount!=$ins_capital){
                $capital_additional_amount=$capital_amount-$ins_capital;
            }

            if ($interest_amount!=$ins_interest){
                $interest_additional_amount=$interest_amount-$ins_interest;
            }

            if ($capital_additional_amount!=0 || $interest_additional_amount!=0){
                $lastInstallment = DB::table('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $loan_id)
                    ->orderByDesc('idInstallments')
                    ->first();

                if ($lastInstallment) {

                    $capital_additional_amount = (float) $capital_additional_amount;
                    $interest_additional_amount = (float) $interest_additional_amount;


                    DB::table('installments')
                        ->where('idInstallments', $lastInstallment->idInstallments)
                        ->update([
                            'Installment_Amount' => DB::raw("Installment_Amount + $capital_additional_amount + $interest_additional_amount"),
                            'capital_amount'     => DB::raw("capital_amount + $capital_additional_amount"),
                            'interest_amount'    => DB::raw("interest_amount + $interest_additional_amount"),
                            'Total_Amount'       => DB::raw("Total_Amount + $capital_additional_amount + $interest_additional_amount"),
                            'capital_balance'    => DB::raw("capital_balance + $capital_additional_amount"),
                            'Interest_Balance'   => DB::raw("Interest_Balance + $interest_additional_amount"),
                            'Total_Balance'      => DB::raw("Total_Balance + $capital_additional_amount + $interest_additional_amount"),
                        ]);
                }

            }


        }
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

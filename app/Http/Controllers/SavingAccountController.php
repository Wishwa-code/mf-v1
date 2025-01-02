<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SavingAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($savingAccountId, $type, $description, $credit, $debit, $balance,$deposit_type, $payment_id = 0)
    {
        $user_id = (int)session('userid');

// Retrieve the current balance from the database
        $currentBalance = DB::table('Savings_Account_Log')
            ->where('Saving_Acount_Id', $savingAccountId)
            ->where('branch_id', session('branch_id'))
            ->orderBy('id', 'desc')
            ->first();



// Calculate the new balance
        if ($deposit_type == "Credit") {
            $newBalance = $currentBalance->Balance + $balance;
        } else {
            $newBalance = $currentBalance->Balance - $balance;
        }

// Insert the data into the Savings_Account_Log table
        DB::table('Savings_Account_Log')->insert([
            'Saving_Acount_Id' => $savingAccountId,
            'Date_Time' => date('Y-m-d H:i:s'),
            'Type' => $type,
            'Description' => $description,
            'Credit' => $credit,
            'Debit' => $debit,
            'Balance' => $newBalance,
            'User' => $user_id,
            'Payment_id' => $payment_id,
            'branch_id' => session('branch_id')
        ]);

        $Customer_Saving_AccountsBalance = tableWithBranch('Customer_Saving_Accounts')
            ->where('id', $savingAccountId)
            ->first();
        if ($deposit_type == "Credit") {
            $newBalanceSaving = $Customer_Saving_AccountsBalance->Balance + $balance;
        } else {
            $newBalanceSaving = $Customer_Saving_AccountsBalance->Balance - $balance;
        }

        DB::table('Customer_Saving_Accounts')->where('branch_id', session('branch_id'))->where('id','=',$savingAccountId)
            ->update([
            'Balance' => $newBalanceSaving,
        ]);

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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LoanLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($Loan_ID,$Type,$Type_ID,$Description,$Amount,$Panelty_Payment,$Interest_Payment,$Capital_Payment,$Savings_Payment,$Panelty_Balance,$Interest_Balance,$Capital_Balance,$Total_Pending_Balance,$Saving_Account_Balance)
    {
        $user_id = (int)session('userid');
        // Insert the data into the Savings_Account_Log table
        DB::table('Loan_Log')->insert([
            'Loan_ID' => $Loan_ID,
            'Date_Time' => date('Y-m-d H:i:s'),
            'Type' => $Type,
            'Type_ID' => $Type_ID,
            'Description' => $Description,
            'Amount' => $Amount,
            'Panelty_Payment' => $Panelty_Payment,
            'Interest_Payment' => $Interest_Payment,
            'Capital_Payment' => $Capital_Payment,
            'Savings_Payment' => $Savings_Payment,
            'Panelty_Balance' => $Panelty_Balance,
            'Interest_Balance' => $Interest_Balance,
            'Capital_Balance' => $Capital_Balance,
            'Total_Pending_Balance' => $Total_Pending_Balance,
            'Saving_Account_Balance' => $Saving_Account_Balance,
            'User_idUser' => $user_id,
            'branch_id' => session('branch_id')
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

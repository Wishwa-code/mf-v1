<?php

namespace App\Http\Controllers;

use App\Models\BankLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($bank_id, $type, $description, $note, $system, $amount, $contra_account, $payment_id = 0, $reconsilation_status = "0", $date_time = null)
    {
        $user_id = (int)user_data('user');
        $isHeadOffice = session('head_branch') == session('branch_id');
        // Create a new BankLog entry
        $BankLog = new BankLog();
        $BankLog->Bank_Account_Id = $bank_id;
        $BankLog->Date_Time = $date_time ?? now();
        $BankLog->Type = $type;
        $BankLog->Description = $description;
        $BankLog->Note = $note;

        // For head office or cross-branch operations, don't filter by branch
        if ($isHeadOffice) {
            $currentBalance = DB::table('company_bank_accounts')->where('Idbank', '=', $bank_id)->value('Account_Balance');
            $bank = DB::table('company_bank_accounts')->where('Idbank', '=', $bank_id)->first();
        } else {
            $currentBalance = tableWithBranch('company_bank_accounts')->where('Idbank', '=', $bank_id)->value('Account_Balance');
            $bank = tableWithBranch('company_bank_accounts')->where('Idbank', '=', $bank_id)->first();
        }

        if (!$currentBalance) {
            $currentBalance = 0.00; // Default balance if no records exist
        }

        $acc_type = $bank->acc_type_group;
        if ($type == "Account Creation") {
            $BankLog->Credit = '0.00';
            $BankLog->Debit = '0.00';
            $BankLog->Balance = $amount;
        } else {
            if ($system == "debit") {

                $BankLog->Credit = '0.00';
                $BankLog->Debit = $amount;

                if ($acc_type == "Liabilities") {
                    $new_current_balance = $currentBalance - $amount;
                } else if ($acc_type == "Equity") {
                    $new_current_balance = $currentBalance - $amount;
                } else if ($acc_type == "Revenue") {
                    $new_current_balance = $currentBalance - $amount;
                } else {
                    $new_current_balance = $currentBalance + $amount;
                }

                $BankLog->Balance = $new_current_balance;

                // Update the account balance
                if ($isHeadOffice) {
                    DB::table('company_bank_accounts')->where('Idbank', $bank_id)->update([
                        'Account_Balance' => $new_current_balance
                    ]);
                } else {
                    updateWithBranch('company_bank_accounts', 'Idbank', $bank_id, [
                        'Account_Balance' => $new_current_balance
                    ]);
                }
            } else {
                $BankLog->Credit = $amount;
                $BankLog->Debit = '0.00';

                if ($acc_type == "Liabilities") {
                    $new_current_balance = $currentBalance + $amount;
                } else if ($acc_type == "Equity") {
                    $new_current_balance = $currentBalance + $amount;
                } else if ($acc_type == "Revenue") {
                    $new_current_balance = $currentBalance + $amount;
                } else {
                    $new_current_balance = $currentBalance - $amount;
                }

                $BankLog->Balance = $new_current_balance;

                // Update the account balance
                if ($isHeadOffice) {
                    DB::table('company_bank_accounts')->where('Idbank', $bank_id)->update([
                        'Account_Balance' => $new_current_balance
                    ]);
                } else {
                    updateWithBranch('company_bank_accounts', 'Idbank', $bank_id, [
                        'Account_Balance' => $new_current_balance
                    ]);
                }
            }
        }

        $BankLog->User = $user_id;

        if ($isHeadOffice) {
            $prefix = DB::table('company_bank_accounts')
                ->where('Idbank', '=', $bank_id)
                ->value('tracking_no');
        } else {
            $prefix = tableWithBranch('company_bank_accounts')
                ->where('Idbank', '=', $bank_id)
                ->value('tracking_no');
        }


        // Now your prefix generation logic...

        if ($prefix != '-') {
            if ($isHeadOffice) {
                $lastTrackingNo = DB::table('company_bank_has_log')
                    ->where('log_tracking_no', 'like', $prefix . '%')
                    ->orderByDesc('log_tracking_no')
                    ->value('log_tracking_no');
            } else {
                $lastTrackingNo = tableWithBranch('company_bank_has_log')
                    ->where('log_tracking_no', 'like', $prefix . '%')
                    ->orderByDesc('log_tracking_no')
                    ->value('log_tracking_no');
            }

            if ($lastTrackingNo) {
                $numberPart = (int)substr($lastTrackingNo, strlen($prefix));
                $nextNumber = str_pad($numberPart + 1, 4, '0', STR_PAD_LEFT);
                $newLogCode = $prefix . $nextNumber;
            } else {
                $newLogCode = $prefix . '0001';
            }
        } else {
            $newLogCode = '-';
        }

        $bankLogData = [
            'Bank_Account_Id' => $BankLog->Bank_Account_Id,
            'Date_Time' => $BankLog->Date_Time,
            'Type' => $BankLog->Type,
            'Description' => $BankLog->Description,
            'Note' => $BankLog->Note,
            'Credit' => $BankLog->Credit,
            'Debit' => $BankLog->Debit,
            'Balance' => $BankLog->Balance,
            'User' => $BankLog->User,
            'payment_id' => $payment_id,
            'contra_account' => $contra_account,
            'reconsilation_status' => $reconsilation_status,
            'log_tracking_no' => $newLogCode, // can be null
        ];

        insertWithBranch('company_bank_has_log', $bankLogData);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

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

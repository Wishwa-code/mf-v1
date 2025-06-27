<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BankBalanceService
{
    /**
     * @throws \Exception
     */
    public function updateRunningBalance($bankAccountId)
    {
        $branchId = session('branch_id');
        // Step 1: Get the account type group
        $account = DB::table('company_bank_accounts')
            ->where('Idbank', $bankAccountId)
            ->select('acc_type_group')
            ->first();

        if (!$account) {
            throw new \Exception("Bank account not found.");
        }

        $calculation = match ($account->acc_type_group) {
            'Liabilities', 'Revenue', 'Equity' => '@running_balance := @running_balance + IFNULL(Credit, 0) - IFNULL(Debit, 0)',
            default => '@running_balance := @running_balance + IFNULL(Debit, 0) - IFNULL(Credit, 0)',
        };


        // Step 3: Set MySQL session variable
        DB::statement("SET @running_balance := 0");

        // Step 4: Execute the update query with dynamic formula
        DB::statement("
        UPDATE company_bank_has_log
        JOIN (
            SELECT
                id,
                {$calculation} AS new_balance
            FROM company_bank_has_log
            WHERE branch_id = ? AND Bank_Account_Id = ?
            ORDER BY Date_Time ASC, id ASC
        ) AS updated_balances ON company_bank_has_log.id = updated_balances.id
        SET company_bank_has_log.Balance = updated_balances.new_balance
    ", [$branchId, $bankAccountId]);
    }



}

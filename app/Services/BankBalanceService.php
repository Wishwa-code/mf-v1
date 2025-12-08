<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BankBalanceService
{
    /**
     * Recalculate running balance for ONE bank account (for current branch).
     *
     * NOTE:
     *  - Your formulas and value logic are kept exactly the same.
     *  - Added:
     *      * Cache lock (per bank+branch) to avoid concurrent recalcs
     *      * DB transaction + lockForUpdate on that account’s rows
     *      * Logging on error
     *
     * @throws \Exception
     */
    public function updateRunningBalance($bankAccountId)
    {
        $branchId = session('branch_id');

        // Unique lock per branch + bank account
        $lockKey = "bank_balance_recalc_{$branchId}_{$bankAccountId}";
        $lock    = Cache::lock($lockKey, 300); // 300s = 5 minutes

        if (! $lock->get()) {
            // Another process is already recalculating this account
            Log::warning("Skip updateRunningBalance: lock already held for bank {$bankAccountId}, branch {$branchId}");
            return;
        }

        try {
            DB::transaction(function () use ($branchId, $bankAccountId) {

                // Step 1: Get the account type group
                $account = DB::table('company_bank_accounts')
                    ->where('Idbank', $bankAccountId)
                    ->select('acc_type_group')
                    ->first();

                if (!$account) {
                    throw new \Exception("Bank account not found.");
                }

                // === YOUR ORIGINAL FORMULA (UNCHANGED) ===
                $calculation = match ($account->acc_type_group) {
                    'Liabilities', 'Revenue', 'Equity' =>
                    '@running_balance := @running_balance + IFNULL(Credit, 0) - IFNULL(Debit, 0)',
                    default =>
                    '@running_balance := @running_balance + IFNULL(Debit, 0) - IFNULL(Credit, 0)',
                };
                // =========================================

                // Optional: lock all rows for this account in this branch
                // so others wait instead of deadlocking
                DB::table('company_bank_has_log')
                    ->where('branch_id', $branchId)
                    ->where('Bank_Account_Id', $bankAccountId)
                    ->orderBy('Date_Time')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get();

                // Step 3: Set MySQL session variable
                DB::statement("SET @running_balance := 0");

                // Step 4: Execute the update query with dynamic formula (UNCHANGED)
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

                // Step 5: Get the last log's updated balance
                $lastBalance = DB::table('company_bank_has_log')
                    ->where('branch_id', $branchId)
                    ->where('Bank_Account_Id', $bankAccountId)
                    ->orderBy('Date_Time', 'desc')
                    ->orderBy('id', 'desc')
                    ->value('Balance');

                // Step 6: Update main account balance
                if (!is_null($lastBalance)) {
                    DB::table('company_bank_accounts')
                        ->where('Idbank', $bankAccountId)
                        ->update(['Account_Balance' => $lastBalance]);
                }

            }, 3); // up to 3 attempts if deadlock occurs

        } catch (\Throwable $e) {
            Log::error("updateRunningBalance failed for bank {$bankAccountId} (branch {$branchId}): " . $e->getMessage());
            throw $e;
        } finally {
            // Release the cache lock if we still own it
            try {
                if (isset($lock) && method_exists($lock, 'release')) {
                    $lock->release();
                }
            } catch (\Throwable $e) {
                // ignore release errors
            }
        }
    }
}

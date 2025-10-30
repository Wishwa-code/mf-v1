<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecoveryAutoService
{
    /**
     * Sweep recovery accounts and auto-pay earliest due installments
     * using recovery balances.
     *
     * @param int $branchId Branch we are processing
     * @param int $userId   User triggering (for audit/logs)
     */
    public function runDailyRecoverySweep(int $branchId, int $userId): void
    {
        $now   = now();
        $today = $now->toDateString();
        $time  = $now->format('H:i:s');

        // 1) load all active recovery accounts in this branch with a positive balance
        $recoveryAccounts = DB::table('recovery_account')
            ->where('branch_id', $branchId)
            ->where('status', 'Active')
            ->where('current_balance', '>', 0)
            ->get();

        foreach ($recoveryAccounts as $account) {

            $recoveryBalance = (float)$account->current_balance;
            if ($recoveryBalance <= 0) {
                continue;
            }

            $customerId = $account->customer_id;

            // 2) get this customer's active loans (Status '0' in your system = active/current)
            $loans = DB::table('customer_loan')
                ->where('Customer_idCustomer', $customerId)
                ->where('branch_id', $branchId)
                ->where('Status', '0')
                ->get();

            foreach ($loans as $loanRow) {

                // stop if no money left in recovery for this customer
                if ($recoveryBalance <= 0) {
                    break;
                }

                $loanId     = $loanRow->idCustomer_Loan;
                $loanNumber = $loanRow->Loan_No;

                // 3) get OLDEST due/overdue unpaid installment for this loan
                //    NOTE: we are NOT going to close Status anymore (we won't touch Status)
                $installment = DB::table('installments')
                    ->where('Customer_Loan_idCustomer_Loan', $loanId)
                    ->where('branch_id', $branchId)
                    ->where('Status', '0') // still unpaid/active
                    ->whereDate('Installment_Date', '<=', $today)
                    ->orderBy('idInstallments', 'asc')
                    ->first();

                if (!$installment) {
                    continue; // nothing due yet for this loan
                }

                // snapshot balances from this installment row
                $idInstallments      = $installment->idInstallments;
                $Panalty_Balance     = (float)$installment->Panalty_Balance;
                $Interest_Balance    = (float)$installment->Interest_Balance;
                $capital_balance     = (float)$installment->capital_balance;
                $Saving_balance      = (float)$installment->Saving_balance;
                $Total_Balance       = (float)$installment->Total_Balance;

                // we are IGNORING Status changes now
                // $Status = $installment->Status;

                // trackers: how much we actually paid this round by bucket
                $Panalty_Balance_tot_paid   = 0.00;
                $Interest_Balance_tot_paid  = 0.00;
                $capital_balance_tot_paid   = 0.00;
                $Saving_balance_tot_paid    = 0.00;

                // how much we can spend on THIS installment from recovery
                $current_payment_amount = $recoveryBalance;

                // this will become the amount we actually use
                $New_Total_paid = 0.00;

                // ========== WATERFALL LOGIC ==========
                // Same order as manual payment:
                // 1. Penalty
                // 2. Interest
                // 3. Capital
                // 4. Saving

                // CASE A: we can pay everything including Saving_balance
                if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance + $capital_balance + $Saving_balance)) {

                    $Panalty_Balance_tot_paid  += $Panalty_Balance;
                    $Interest_Balance_tot_paid += $Interest_Balance;
                    $capital_balance_tot_paid  += $capital_balance;
                    $Saving_balance_tot_paid   += $Saving_balance;

                    $New_Total_paid        = $Total_Balance;
                    $current_payment_amount -= $Total_Balance;

                    // all balances drop to zero
                    $Panalty_Balance  = 0;
                    $Interest_Balance = 0;
                    $capital_balance  = 0;
                    $Saving_balance   = 0;
                    $Total_Balance    = 0;

                    // IMPORTANT CHANGE:
                    // we do NOT set $Status = "1"
                    // we leave installment.Status as it was (probably "0")

                }
                // CASE B: we can pay P+I+Capital fully, and part/all Savings
                else if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance + $capital_balance)) {

                    $New_Saving_payment  = $current_payment_amount - ($Panalty_Balance + $Interest_Balance + $capital_balance);
                    $New_Saving_balance  = $Saving_balance - $New_Saving_payment;

                    $New_Total_paid      = $Panalty_Balance + $Interest_Balance + $capital_balance + $New_Saving_payment;
                    $New_Total_Balance   = $New_Saving_balance;

                    $current_payment_amount -= $New_Total_paid;

                    $Panalty_Balance_tot_paid  += $Panalty_Balance;
                    $Interest_Balance_tot_paid += $Interest_Balance;
                    $capital_balance_tot_paid  += $capital_balance;
                    $Saving_balance_tot_paid   += $New_Saving_payment;

                    $Panalty_Balance  = 0;
                    $Interest_Balance = 0;
                    $capital_balance  = 0;
                    $Saving_balance   = $New_Saving_balance;
                    $Total_Balance    = $New_Total_Balance;

                }
                // CASE C: we can pay P+I fully, and part/all Capital
                else if ($current_payment_amount >= ($Panalty_Balance + $Interest_Balance)) {

                    $New_Capital_payment  = $current_payment_amount - ($Panalty_Balance + $Interest_Balance);
                    $New_Capital_balance  = $capital_balance - $New_Capital_payment;

                    $New_Total_paid       = $Panalty_Balance + $Interest_Balance + $New_Capital_payment;
                    $New_Total_Balance    = $New_Capital_balance + $Saving_balance;

                    $current_payment_amount -= $New_Total_paid;

                    $Panalty_Balance_tot_paid  += $Panalty_Balance;
                    $Interest_Balance_tot_paid += $Interest_Balance;
                    $capital_balance_tot_paid  += $New_Capital_payment;
                    $Saving_balance_tot_paid   += 0.00;

                    $Panalty_Balance  = 0;
                    $Interest_Balance = 0;
                    $capital_balance  = $New_Capital_balance;
                    $Total_Balance    = $New_Total_Balance;

                }
                // CASE D: we can pay full Penalty, and part/all Interest
                else if ($current_payment_amount >= ($Panalty_Balance)) {

                    $New_Interest_payment  = $current_payment_amount - ($Panalty_Balance);
                    $New_Interest_balance  = $Interest_Balance - $New_Interest_payment;

                    $New_Total_paid        = $Panalty_Balance + $New_Interest_payment;
                    $New_Total_Balance     = $New_Interest_balance + $Saving_balance + $capital_balance;

                    $current_payment_amount -= $New_Total_paid;

                    $Panalty_Balance_tot_paid  += $Panalty_Balance;
                    $Interest_Balance_tot_paid += $New_Interest_payment;
                    $capital_balance_tot_paid  += 0.00;
                    $Saving_balance_tot_paid   += 0.00;

                    $Panalty_Balance  = 0;
                    $Interest_Balance = $New_Interest_balance;
                    $Total_Balance    = $New_Total_Balance;

                }
                // CASE E: we can only pay part of Penalty
                else {

                    $New_Panelty_payment  = $current_payment_amount;
                    $New_Panelty_balance  = $Panalty_Balance - $New_Panelty_payment;

                    $New_Total_paid       = $New_Panelty_payment;
                    $New_Total_Balance    = $Interest_Balance + $Saving_balance + $capital_balance + $New_Panelty_balance;

                    $current_payment_amount -= $New_Total_paid;

                    $Panalty_Balance_tot_paid  += $New_Panelty_payment;
                    $Interest_Balance_tot_paid += 0.00;
                    $capital_balance_tot_paid  += 0.00;
                    $Saving_balance_tot_paid   += 0.00;

                    $Panalty_Balance  = $New_Panelty_balance;
                    $Total_Balance    = $New_Total_Balance;
                }
                // ========== END WATERFALL ==========

                // If we didn't actually pay anything, skip logging/updates
                if ($New_Total_paid <= 0) {
                    continue;
                }

                DB::beginTransaction();
                try {
                    // 4) UPDATE INSTALLMENT
                    //    NOTE: WE DO NOT TOUCH 'Status' ANYMORE
                    //    and we are not touching 'Panelty_status' anymore either.
                    DB::table('installments')
                        ->where('idInstallments', $idInstallments)
                        ->where('branch_id', $branchId)
                        ->update([
                            'Paid_Amount'      => DB::raw('Paid_Amount + ' . $New_Total_paid),
                            'Total_Balance'    => $Total_Balance,
                            'Panalty_Balance'  => $Panalty_Balance,
                            'Interest_Balance' => $Interest_Balance,
                            'capital_balance'  => $capital_balance,
                            'Saving_balance'   => $Saving_balance,
                        ]);

                    // 5) INSTALLMENT LOG
                    DB::table('installment_log')->insert([
                        'Installments_idInstallments' => $idInstallments,
                        'Date'                        => $today . ' ' . $time,
                        'Description'                 => 'Auto Payment from Recovery : ' . $New_Total_paid,
                        'Amount'                      => $New_Total_paid,
                        'Panalty_Total'               => $Panalty_Balance,
                        'Interest_Balance'            => $Interest_Balance,
                        'Capital_balance'             => $capital_balance,
                        'Saving_balance'              => $Saving_balance,
                        'Total_Balance'               => $Total_Balance,
                        'User_idUser'                 => $userId,
                        'branch_id'                   => $branchId,
                    ]);

                    // 6) UPDATE RECOVERY ACCOUNT BALANCE
                    $newRecoveryBalance = $recoveryBalance - $New_Total_paid;
                    if ($newRecoveryBalance < 0) {
                        $newRecoveryBalance = 0;
                    }

                    DB::table('recovery_account')
                        ->where('idRecovery_Account', $account->idRecovery_Account)
                        ->update([
                            'current_balance' => $newRecoveryBalance,
                            'updated_at'      => $now,
                            'updated_by'      => $userId,
                        ]);

                    // 7) RECOVERY ACCOUNT LOG
                    DB::table('recovery_account_log')->insert([
                        'recovery_account_id' => $account->idRecovery_Account,
                        'loan_id'             => $loanId,
                        'customer_id'         => $customerId,
                        'action_type'         => 'Auto Debit',
                        'description'         => "Auto recovery payment {$New_Total_paid} for installment {$idInstallments} of Loan {$loanNumber}",
                        'amount'              => $New_Total_paid,
                        'balance_after'       => $newRecoveryBalance,
                        'created_at'          => $now,
                        'created_by'          => $userId,
                        'branch_id'           => $branchId,
                    ]);

                    // 8) RE-CALCULATE LOAN TOTALS AFTER THIS PAYMENT
                    $totalsAfter = DB::table('installments')
                        ->where('Customer_Loan_idCustomer_Loan', $loanId)
                        ->where('branch_id', $branchId)
                        ->where('Status', '0') // still open installments
                        ->selectRaw('
                            SUM(Panalty_Balance)  as pan,
                            SUM(Interest_Balance) as intr,
                            SUM(capital_balance)  as cap,
                            SUM(Saving_balance)   as sav,
                            SUM(Total_Balance)    as tot
                        ')
                        ->first();

                    $Total_Pending_Balance_Log = $totalsAfter->tot  ?? 0;
                    $Saving_Balance_Log        = $totalsAfter->sav  ?? 0;
                    $Panelty_Balance_Log       = $totalsAfter->pan  ?? 0;
                    $Interest_Balance_Log      = $totalsAfter->intr ?? 0;
                    $Capital_Balance_Log       = $totalsAfter->cap  ?? 0;

                    // 9) LOAN LOG (direct insert, no controller call)
                    DB::table('Loan_Log')->insert([
                        'Loan_ID'                => $loanId,
                        'Date_Time'              => $now,
                        'Type'                   => 'Auto Recovery Payment',
                        'Type_ID'                => $idInstallments,
                        'Description'            => "Auto Recovery Payment {$New_Total_paid}",
                        'Amount'                 => $New_Total_paid,
                        'Panelty_Payment'        => $Panalty_Balance_tot_paid,
                        'Interest_Payment'       => $Interest_Balance_tot_paid,
                        'Capital_Payment'        => $capital_balance_tot_paid,
                        'Savings_Payment'        => $Saving_balance_tot_paid,
                        'Extra_Payment'          => 0,
                        'Recovery_Amount'        => $New_Total_paid,
                        'Panelty_Balance'        => $Panelty_Balance_Log,
                        'Interest_Balance'       => $Interest_Balance_Log,
                        'Capital_Balance'        => $Capital_Balance_Log,
                        'Total_Pending_Balance'  => $Total_Pending_Balance_Log,
                        'Saving_Account_Balance' => $Saving_Balance_Log,
                        'Extra_Balance'          => 0,
                        'Recovery_Balance'       => $newRecoveryBalance,
                        'User_idUser'            => $userId,
                        'branch_id'              => $branchId,
                    ]);

                    DB::commit();

                    // update our working balance before the next loop
                    $recoveryBalance = $newRecoveryBalance;

                } catch (\Throwable $e) {
                    DB::rollBack();

                    Log::error('Auto recovery sweep failed', [
                        'error'           => $e->getMessage(),
                        'branch_id'       => $branchId,
                        'loan_id'         => $loanId,
                        'installment_id'  => $idInstallments,
                        'recovery_ac_id'  => $account->idRecovery_Account,
                    ]);

                    // don't throw, we just continue
                }
            }
        }
    }
}

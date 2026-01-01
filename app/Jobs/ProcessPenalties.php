<?php

// app/Jobs/ProcessPenalties.php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Carbon\Carbon;

class ProcessPenalties implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // how long we allow this job to run (seconds)
    public $timeout = 900; // 15m

    public function handle(): void
    {
        $lock = Cache::lock('process-penalties-lock', 60 * 30); // 30m lock to avoid overlaps
        if (! $lock->get()) {
            // another run is active
            return;
        }

        try {
            $tz      = 'Asia/Colombo';
            $today   = Carbon::today($tz)->toDateString();

            // Chunk to control memory + reduce row locks
            DB::table('installments as i')
                ->join('customer_loan as cl', 'i.Customer_Loan_idCustomer_Loan', '=', 'cl.idCustomer_Loan')
                ->where('cl.Status', '=', 0)
                ->where('i.Status', '=', 0)
                ->whereDate('i.Panelty_date', '<=', $today)
                ->select([
                    'i.idInstallments',
                    'i.branch_id',
                    'i.Panalty_Amount',
                    'i.Panalty_Balance',
                    'i.Total_Amount',
                    'i.Interest_Balance',
                    'i.capital_balance',
                    'i.Panelty_count',
                    'i.Panelty_date',
                    'cl.Panalty_Rate',
                    'cl.panelty_method',
                    'cl.Panelty_period',
                    'cl.idCustomer_Loan',
                    'cl.Customer_idCustomer'
                ])
                ->orderBy('i.idInstallments')
                ->chunkById(500, function ($rows) use ($tz) {
                    $today = Carbon::today($tz);

                    foreach ($rows as $item) {
                        // compute "missing" only once
                        $penaltyDate   = Carbon::parse($item->Panelty_date, $tz)->startOfDay();
                        $days          = max(0, $penaltyDate->diffInDays($today, false));
                        $paneltyCount  = (int) ($item->Panelty_count ?? 0);
                        $missing       = max(0, $days - $paneltyCount);

                        if ($missing <= 0) continue;

                        // base for penalty (you can switch to your method rules if needed)
                        $insAmount       = (float)$item->capital_balance + (float)$item->Interest_Balance;
                        $perDayPenalty   = round(($insAmount * (float)$item->Panalty_Rate) / 100, 2);
                        $batchTotal      = round($perDayPenalty * $missing, 2);

                        // Single DB update (no loop) for balances
                        DB::table('installments')
                            ->where('idInstallments', $item->idInstallments)
                            ->where('branch_id', $item->branch_id)
                            ->update([
                                'Panalty_Amount'  => DB::raw("ROUND(Panalty_Amount + {$batchTotal}, 2)"),
                                'Panalty_Balance' => DB::raw("ROUND(Panalty_Balance + {$batchTotal}, 2)"),
                                'Total_Amount'    => DB::raw("ROUND(Total_Amount + {$batchTotal}, 2)"),
                                'Total_Balance'   => DB::raw("ROUND(capital_balance + Interest_Balance + Panalty_Balance, 2)"),
                                'Panelty_status'  => 1,
                                'Panelty_count'   => DB::raw('COALESCE(Panelty_count,0) + ' . $missing),
                            ]);

                        // --- LOGGING ---
                        // If you MUST keep per-day entries, do a bulk insert array instead of looping DB::insert per day.
                        // Otherwise (recommended), write one aggregated entry that mentions the count.

                        // Aggregated customer_log
                        $userId = session('user_data')["idUser"] ?? null; // scheduler may not have a session—fallback to system user id if you have one
                        $now    = Carbon::now($tz);
                        $cust   = DB::table('customer')->where('idCustomer', $item->Customer_idCustomer)->first();

                        if ($cust) {
                            DB::table('customer_log')->insert([
                                'customer_id'   => $item->Customer_idCustomer,
                                'customer_name' => trim(($cust->First_Name ?? '') . ' ' . ($cust->Last_Name ?? '')),
                                'date'          => $now->toDateString(),
                                'time'          => $now->format('H:i:s'),
                                'description'   => number_format($batchTotal, 2, '.', '') . " LKR Penalty added for ({$item->idCustomer_Loan})" .
                                    "\nInstallment No : {$item->idInstallments}" .
                                    "\nPenalty Count +{$missing} (since " . Carbon::parse($item->Panelty_date, $tz)->toDateString() . ")",
                                'description_id' => $item->idInstallments,
                                'comment'       => ' ',
                                'type'          => 'Penalty',
                                'user'          => $userId,
                                'branch_id'     => $item->branch_id
                            ]);
                        }

                        // Loan_Log (aggregated)
                        $last = DB::table('Loan_Log')->where('Loan_ID', $item->idCustomer_Loan)
                            ->orderByDesc('Loan_Log_ID')->first();

                        if ($last) {
                            $Panelty_Balance        = round(((float)$last->Panelty_Balance + $batchTotal), 2);
                            $Total_Pending_Balance  = round(((float)$last->Total_Pending_Balance + $batchTotal), 2);

                            // Call your controller method (or extract to service)
                            app(\App\Http\Controllers\LoanLogController::class)->index(
                                $item->idCustomer_Loan,
                                'Penalty',
                                $item->idInstallments,
                                'Penalty (aggregated) - Installment No: ' . $item->idInstallments . ' | +' . $missing . ' day(s)',
                                number_format($batchTotal, 2, '.', ''), // amount
                                '0.00',
                                '0.00',
                                '0.00',
                                '0.00',
                                number_format($Panelty_Balance, 2, '.', ''),
                                $last->Interest_Balance,
                                $last->Capital_Balance,
                                number_format($Total_Pending_Balance, 2, '.', ''),
                                $last->Saving_Account_Balance
                            );
                        }

                        // Bank logs (aggregated)
                        $acc5 = DB::table('company_bank_accounts')->where('branch_id', $item->branch_id)->where('Bank_Type', '=', 'System_default_5')->first();
                        $acc6 = DB::table('company_bank_accounts')->where('branch_id', $item->branch_id)->where('Bank_Type', '=', 'System_default_6')->first();

                        if ($acc5 && $acc6) {
                            app(\App\Http\Controllers\BankLogController::class)
                                ->index($acc5->Idbank, "Penalty", "Penalty", "-", "debit", number_format($batchTotal, 2, '.', ''), $acc6->Idbank);
                            app(\App\Http\Controllers\BankLogController::class)
                                ->index($acc6->Idbank, "Penalty", "Penalty", "-", "credit", number_format($batchTotal, 2, '.', ''), $acc5->Idbank);
                        }
                    }
                }, 'i.idInstallments'); // chunkById key
        } finally {
            optional($lock)->release();
        }
    }
}

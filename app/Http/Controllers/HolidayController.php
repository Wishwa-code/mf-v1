<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HolidayController extends Controller
{
    /**
     * Run the global due-skip process.
     *
     * @param string      $skipFor  all | loan | branch | center | product
     * @param int|null    $targetId target id (loan_id, branch_id, etc.)
     * @param string      $skipType installment | day
     *
     * @return int        number of installments affected
     */
    public function index($skipFor, $targetId, $skipType)
    {
        $companySetting = tableWithBranch('company')->value('saturday_sunday');
        $holidays       = tableWithBranch('holidays')->get();

        // Default branch from session
        $branch_id = session('branch_id');

        // Loans to work on (your custom helper)
        $loans = getTargetLoans($skipFor, $targetId);

        // If skipping for a specific branch, override branch id
        if ($skipFor === "branch" && $targetId) {
            $branch_id = $targetId;
        }

        $affected = 0;

        foreach ($holidays as $holiday) {
            $holidayDate = $holiday->date;

            foreach ($loans as $loan) {
                $installments = tableWithBranch('installments')
                    ->where('Installment_Date', $holidayDate)
                    ->where('Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
                    ->get();

                foreach ($installments as $installment) {
                    if ($skipType === 'installment') {
                        // your existing helper
                        processInstallmentSkip($loan, $installment, $companySetting, $branch_id);
                    } else {
                        // your existing helper
                        processDaySkip($loan, $installment, $holidayDate, $companySetting, $branch_id);
                    }
                    $affected++;
                }
            }
        }

        // Resort installments inside each loan after all changes
        $this->create($branch_id);

        return $affected;
    }

    /**
     * Resort installment and penalty dates for all loans of a branch.
     */
    public function create($branch_id = null)
    {
        // Fallback to session branch for cases like store()
        if ($branch_id === null) {
            $branch_id = session('branch_id');
        }

        $loans = DB::table('customer_loan')
            ->where('branch_id', '=', $branch_id)
            ->get();

        foreach ($loans as $loan) {
            $installments = DB::table('installments')
                ->where('branch_id', '=', $branch_id)
                ->where('Customer_Loan_idCustomer_Loan', '=', $loan->idCustomer_Loan)
                ->get();

            if ($installments->isEmpty()) {
                continue;
            }

            $dates        = [];
            $penaltyDates = [];

            foreach ($installments as $inst) {
                $dates[]        = $inst->Installment_Date;
                $penaltyDates[] = $inst->Panelty_date;
            }

            sort($dates);
            sort($penaltyDates);

            $count = 0;
            foreach ($installments as $inst) {
                DB::table('installments')
                    ->where('branch_id', '=', $branch_id)
                    ->where('idInstallments', $inst->idInstallments)
                    ->update([
                        'Installment_Date' => $dates[$count],
                        'Panelty_date'     => $penaltyDates[$count],
                    ]);
                $count++;
            }
        }
    }

    /**
     * Shift installments for a single loan when it is issued (your old logic).
     */
    public function store($loan_id)
    {
        $companySetting = tableWithBranch('company')->value('saturday_sunday');
        $holidays       = tableWithBranch('holidays')->get();

        // Get this loan & branch
        $loan      = tableWithBranch('customer_loan')
            ->where('idCustomer_Loan', '=', $loan_id)
            ->first();
        $branch_id = $loan->branch_id ?? session('branch_id');

        foreach ($holidays as $holiday) {
            $holidayDate  = $holiday->date;
            $installments = tableWithBranch('installments')
                ->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)
                ->where('Installment_Date', $holidayDate)
                ->get();

            foreach ($installments as $installment) {
                $loan_id = $installment->Customer_Loan_idCustomer_Loan;

                // Start one day after the holiday
                $newDate = Carbon::parse($holidayDate)->addDay();
                Log::info("Normal installment date:" . $newDate);

                // find the next valid date
                while (
                    tableWithBranch('holidays')->where('date', $newDate->toDateString())->exists() ||    // avoid holidays
                    ($companySetting == "1" && ($newDate->isSaturday() || $newDate->isSunday())) ||      // avoid weekend
                    tableWithBranch('installments')
                        ->where('Customer_Loan_idCustomer_Loan', '=', $loan_id)
                        ->where('Installment_Date', $newDate->toDateString())
                        ->exists()                                                                      // avoid duplicate installment dates
                ) {
                    $newDate->addDay();
                }

                $loanRow = tableWithBranch('customer_loan')
                    ->where('idCustomer_Loan', '=', $installment->Customer_Loan_idCustomer_Loan)
                    ->first();

                $newPenaltyDate = $newDate->toDateString();

                if ($loanRow) {
                    $productId = $loanRow->Loan_Category_idLoan_Category;
                    $product   = tableWithBranch('loan_category')
                        ->where('idLoan_Category', '=', $productId)
                        ->first();

                    if ($product) {
                        $penaltyGapDays = (int) $product->Panelty_date;
                        $newPenaltyDate = Carbon::parse($newDate)
                            ->addDays($penaltyGapDays)
                            ->toDateString();
                    }
                }

                tableWithBranch('installments')
                    ->where('idInstallments', $installment->idInstallments)
                    ->update([
                        'Installment_Date' => $newDate->toDateString(),
                        'Panelty_date'     => $newPenaltyDate,
                    ]);
            }
        }

        // re-sort dates for that branch
        $this->create($branch_id);
    }

    public function show(string $id)  { /* not used */ }
    public function edit(string $id)  { /* not used */ }
    public function update(Request $request, string $id) { /* not used */ }
    public function destroy(string $id) { /* not used */ }

    public function fetchPoya()
    {
        $response = Http::get('https://nextpoyawhen.com/poya.json');

        if ($response->successful()) {
            Log::info($response->json());
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Unable to fetch Poya days.'], 500);
    }
}

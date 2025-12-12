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
     * Fetch Poya days from external API
     */
    public function fetchPoya()
    {
        $response = Http::get('https://nextpoyawhen.com/poya.json');

        if ($response->successful()) {
            Log::info($response->json());
            return response()->json($response->json());
        }

        return response()->json(['error' => 'Unable to fetch Poya days.'], 500);
    }

    /**
     * Core Due Skip runner – used by UserController@generateDueSkip
     *
     * @return array
     */
    public function runDueSkip(string $skipFor, ?int $targetId, string $skipType): array
    {
        $companySetting = tableWithBranch('company')->value('saturday_sunday'); // 1 = skip Sat/Sun
        $holidays       = tableWithBranch('holidays')->get();
        $branchId       = (int) session('branch_id');

        // If skipFor is branch, we override branch id
        if ($skipFor === 'branch' && $targetId) {
            $branchId = $targetId;
        }

        // Get relevant loans only once
        $loans = $this->getTargetLoansForSkip($skipFor, $targetId);

        $affectedInstallments = 0;

        foreach ($holidays as $holiday) {
            $holidayDate = $holiday->date;

            foreach ($loans as $loan) {
                $installments = tableWithBranch('installments')
                    ->whereDate('Installment_Date', $holidayDate)
                    ->where('Customer_Loan_idCustomer_Loan', $loan->idCustomer_Loan)
                    ->get();

                foreach ($installments as $installment) {
                    if ($skipType === 'installment') {
                        // your existing helper
                        processInstallmentSkip($loan, $installment, $companySetting, $branchId);
                    } else {
                        // skipType = day
                        processDaySkip($loan, $installment, $holidayDate, $companySetting, $branchId);
                    }
                    $affectedInstallments++;
                }
            }
        }

        // Re-sequence installments for the branch
        $this->resequenceInstallments($branchId);

        return [
            'processed_loans'        => $loans->count(),
            'processed_installments' => $affectedInstallments,
            'branch_id'              => $branchId,
        ];
    }

    /**
     * Decide which loans to process based on skipFor / targetId
     */
    protected function getTargetLoansForSkip(string $skipFor, ?int $targetId)
    {
        // Base query with joins to reach center + route
        $query = tableWithBranch('customer_loan','customer_loan')
            ->leftJoin('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'center.route_id', '=', 'route.id_route')
            ->select('customer_loan.*');   // 👈 only loan columns in the result

        switch ($skipFor) {
            case 'loan':
                if ($targetId) {
                    $query->where('customer_loan.idCustomer_Loan', $targetId);
                }
                break;

            case 'center':
                if ($targetId) {
                    $query->where('center.idCenter', $targetId);
                }
                break;

            case 'route':
                if ($targetId) {
                    $query->where('route.id_route', $targetId);
                }
                break;

            case 'product':
                if ($targetId) {
                    $query->where('customer_loan.Loan_Category_idLoan_Category', $targetId);
                }
                break;

            case 'all':
            default:
                // no extra filter; tableWithBranch already scopes by company/branch
                break;
        }

        return $query->get();
    }


    /**
     * Re-sequence installment dates and panelty dates per loan for a branch
     */
    protected function resequenceInstallments(int $branchId): void
    {
        $loans = DB::table('customer_loan')
            ->where('branch_id', '=', $branchId)
            ->get();

        foreach ($loans as $loan) {
            $installments = DB::table('installments')
                ->where('branch_id', '=', $branchId)
                ->where('Customer_Loan_idCustomer_Loan', '=', $loan->idCustomer_Loan)
                ->orderBy('Installment_Date')
                ->get();

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
                    ->where('branch_id', '=', $branchId)
                    ->where('idInstallments', $inst->idInstallments)
                    ->update([
                        'Installment_Date' => $dates[$count] ?? $inst->Installment_Date,
                        'Panelty_date'     => $penaltyDates[$count] ?? $inst->Panelty_date,
                    ]);
                $count++;
            }
        }
    }
}

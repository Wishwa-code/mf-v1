<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArrearsReportExecutiveSummaryController extends Controller
{
    public function index(Request $request)
    {
        $branch = tableWithBranch('branch')->where('status', '=', '1')->get();
        $officer = tableWithBranch('user')->where('Status', '=', '1')->get();
        $branch_access = session('branch_access');

        // Get report data
        $data = $this->getReportData($request);

        return view('pages.ArrearsReportExecutiveSummary', compact('branch', 'branch_access', 'officer', 'data'));
    }

    private function getReportData(Request $request)
    {
        // Filters
        $collectorId = $request->filled('loan_officer')
            ? $request->input('loan_officer')
            : null;

        // Date inputs
        $startInput = $request->input('from');
        $endInput   = $request->input('to');

        if (!empty($startInput) && !empty($endInput)) {
            $today = \Carbon\Carbon::parse($startInput)->startOfDay();
            $end   = \Carbon\Carbon::parse($endInput)->endOfDay();
        } else {
            $today = \Carbon\Carbon::now()->startOfMonth()->startOfDay();
            $end   = \Carbon\Carbon::now()->endOfDay();
        }

        // Use END date as "today" for reports (arrears logic)
        // $today = $end->toDateString();

        // Column name (kept configurable)
        $customerIdCol = 'Customer_idCustomer';


        // Total arrears per collector
        $totalArrears = DB::query()->fromSub(function ($q) use ($today, $end) {
            $q->from('installments as i')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'i.Customer_Loan_idCustomer_Loan')
                ->whereBetween('i.Installment_Date', [$today, $end])
                ->where('i.Total_Balance', '>', 0)
                ->where('i.Status', '=', '0')
                ->when(session()->has('branch_id'), function ($qq) {
                    $qq->where('cl.branch_id', session('branch_id'));
                })
                ->selectRaw('
                    cl.collector_id,
                    SUM(i.Total_Balance) as total_arrears,
                    SUM(i.Capital_Balance) as capital_arrears,
                    SUM(i.Interest_Balance) as interest_arrears,
                    COUNT(DISTINCT cl.idCustomer_Loan) as arrears_loan_count,
                    COUNT(DISTINCT cl.Customer_idCustomer) as arrears_customer_count
                ')
                ->groupBy('cl.collector_id');
        }, 'ta');

        // Debtor ratio calculation (arrears / current stock)
        $currentStock = DB::query()->fromSub(function ($q) use ($end) {
            $capitalPaidUntilEnd = DB::table('installments as ins')
                ->selectRaw('ins.Customer_Loan_idCustomer_Loan as Loan_ID, COALESCE(SUM(ins.capital_amount), 0) as capital_paid')
                ->whereDate('ins.Installment_Date', '<=', $end->toDateString())
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');

            $q->from('customer_loan as cl')
                ->leftJoinSub($capitalPaidUntilEnd, 'p', 'p.Loan_ID', '=', 'cl.idCustomer_Loan')
                ->selectRaw('
                    cl.collector_id,
                    SUM(GREATEST(COALESCE(cl.Amount,0) - COALESCE(p.capital_paid,0), 0)) as current_stock
                ')
                ->when(session()->has('branch_id'), function ($qb) {
                    $qb->where('cl.branch_id', session('branch_id'));
                })
                ->where('cl.Date_Time', '<=', $end)
                ->groupBy('cl.collector_id');
        }, 'cs');


        $totalDueAmount = DB::query()->fromSub(function ($q) use ($today, $end) {
            $q->from('installments as i')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'i.Customer_Loan_idCustomer_Loan')
                ->whereBetween('i.Installment_Date', [$today, $end])
                ->where('i.Total_Balance', '>', 0)
                ->when(session()->has('branch_id'), function ($qq) {
                    $qq->where('cl.branch_id', session('branch_id'));
                })
                ->selectRaw('SUM(i.Total_Amount) as total_due_amount,cl.collector_id')
                ->groupBy('cl.collector_id');
        }, 'tda');

        $totalCollectedAmount = DB::query()->fromSub(function ($q) use ($today, $end) {
            $q->from('installments as i')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'i.Customer_Loan_idCustomer_Loan')
                ->whereBetween('i.Installment_Date', [$today, $end])
                ->where('i.Total_Balance', '>', 0)
                ->when(session()->has('branch_id'), function ($qq) {
                    $qq->where('cl.branch_id', session('branch_id'));
                })
                ->selectRaw('SUM(i.Paid_Amount) as total_collected_amount,cl.collector_id')
                ->groupBy('cl.collector_id');
        }, 'tca');

        // // Arrears breakdown by week brackets
        $arrearsBreakdown = DB::query()->fromSub(function ($q) use ($today, $end) {
            $q->from('installments as i')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'i.Customer_Loan_idCustomer_Loan')
                ->whereBetween('i.Installment_Date', [$today, $end])
                ->where('i.Total_Balance', '>', 0)
                ->when(session()->has('branch_id'), function ($qq) {
                    $qq->where('cl.branch_id', session('branch_id'));
                })
                ->selectRaw("
                    cl.collector_id,
                    CASE 
                        WHEN DATEDIFF(i.Installment_Date, ?) <= 7 THEN '01_week'
                        WHEN DATEDIFF(i.Installment_Date, ?) BETWEEN 8 AND 21 THEN '1to3_weeks'
                        WHEN DATEDIFF(i.Installment_Date, ?) BETWEEN 22 AND 56 THEN '4to8_weeks'
                        WHEN DATEDIFF(i.Installment_Date, ?) BETWEEN 57 AND 84 THEN '9to12_weeks'
                        ELSE '13plus_weeks'
                    END as week_bracket,
                    COUNT(DISTINCT cl.idCustomer_Loan) as loan_count,
                    SUM(i.Total_Balance) as amount
                ", [$today, $today, $today, $today])
                ->groupBy('cl.collector_id', 'week_bracket');
        }, 'ab')
            ->selectRaw("
                collector_id,
                GROUP_CONCAT(CONCAT(week_bracket, ':', loan_count, ':', amount) ORDER BY week_bracket SEPARATOR ',') as week_data
            ")
            ->groupBy('collector_id');

        // OC Clients
        $ocClients = DB::query()->fromSub(function ($q) {
            $q->from('installments as ins')
                ->selectRaw('ins.Customer_Loan_idCustomer_Loan as Loan_ID, MAX(ins.Installment_Date) as maturity_date')
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');
        }, 'm')
            ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'm.Loan_ID')
            ->whereBetween('m.maturity_date', [$today, $end])
            ->when(session()->has('branch_id'), function ($qq) {
                $qq->where('cl.branch_id', session('branch_id'));
            })
            ->selectRaw("cl.collector_id, COUNT(DISTINCT cl.Customer_idCustomer) as oc_clients_count, COUNT(*) as oc_loan_count")
            ->groupBy('cl.collector_id');

        // Main query
        $collectorRows = DB::table('user as u')
            ->leftJoinSub($totalArrears, 'ta', fn($j) => $j->on('ta.collector_id', '=', 'u.id'))
            ->leftJoinSub($currentStock, 'cs', fn($j) => $j->on('cs.collector_id', '=', 'u.id'))
            ->leftJoinSub($arrearsBreakdown, 'ab', fn($j) => $j->on('ab.collector_id', '=', 'u.id'))
            ->leftJoinSub($ocClients, 'oc', fn($j) => $j->on('oc.collector_id', '=', 'u.id'))
            ->leftJoinSub($totalDueAmount, 'tda', fn($j) => $j->on('tda.collector_id', '=', 'u.id'))
            ->leftJoinSub($totalCollectedAmount, 'tca', fn($j) => $j->on('tca.collector_id', '=', 'u.id'))
            ->where('u.Status', '=', '1')
            ->when($collectorId, fn($q) => $q->where('u.id', $collectorId))
            ->when(session()->has('branch_id'), function ($q) {
                $q->where('u.branch_id', session('branch_id'));
            })
            ->selectRaw('
                u.id as collector_id,
                u.Full_Name as collector_name,
                COALESCE(MAX(ta.total_arrears), 0) as total_arrears,
                COALESCE(MAX(ta.capital_arrears), 0) as capital_arrears,
                COALESCE(MAX(ta.interest_arrears), 0) as interest_arrears,
                COALESCE(MAX(ta.arrears_loan_count), 0) as arrears_loan_count,
                COALESCE(MAX(ta.arrears_customer_count), 0) as arrears_customer_count,
                COALESCE(MAX(cs.current_stock), 0) as current_stock,
                COALESCE(MAX(ab.week_data), "") as week_data,
                COALESCE(MAX(oc.oc_clients_count), 0) as oc_clients_count,
                COALESCE(MAX(oc.oc_loan_count), 0) as oc_loan_count,
                COALESCE(MAX(tda.total_due_amount), 0) as total_due_amount,
                COALESCE(MAX(tca.total_collected_amount), 0) as total_collected_amount
            ')
            ->groupBy('u.id', 'u.Full_Name')
            ->orderBy('u.Full_Name')
            ->get();

        // Shape response
        $data = collect($collectorRows)->map(function ($r) {
            $totalDueAmount = (float) ($r->total_due_amount ?? 0);
            $totalCollectedAmount = (float) ($r->total_collected_amount ?? 0);
            $totalArrears = (float) ($r->total_arrears ?? 0);
            $currentStock = (float) ($r->current_stock ?? 0);
            $debtorRatio = $totalCollectedAmount != 0 ? ($totalCollectedAmount / $totalDueAmount) * 100 : 0;

            // Parse week_data
            $weekBreakdown = [
                '01_week' => ['loan' => 0, 'amount' => 0],
                '1to3_weeks' => ['loan' => 0, 'amount' => 0],
                '4to8_weeks' => ['loan' => 0, 'amount' => 0],
                '9to12_weeks' => ['loan' => 0, 'amount' => 0],
                '13plus_weeks' => ['loan' => 0, 'amount' => 0],
            ];

            $weekData = (string)($r->week_data ?? '');
            if ($weekData !== '') {
                foreach (explode(',', $weekData) as $pair) {
                    $parts = explode(':', $pair);
                    if (count($parts) >= 3) {
                        $bracket = $parts[0];
                        $loan = (int)$parts[1];
                        $amount = (float)$parts[2];
                        if (isset($weekBreakdown[$bracket])) {
                            $weekBreakdown[$bracket] = ['loan' => $loan, 'amount' => round($amount, 2)];
                        }
                    }
                }
            }

            return [
                'Total_Due_Amount' => $totalDueAmount,
                'Collected_Total_Amount' => $totalCollectedAmount,
                'Loan_Officer' => $r->collector_name ?? '—',
                'Arrears' => round($totalArrears, 2),
                'Debtor_Ratio' => round($debtorRatio, 2),
                'Week_01' => $weekBreakdown['01_week'],
                'Week_1to3' => $weekBreakdown['1to3_weeks'],
                'Week_4to8' => $weekBreakdown['4to8_weeks'],
                'Week_9to12' => $weekBreakdown['9to12_weeks'],
                'Week_13plus' => $weekBreakdown['13plus_weeks'],
                'OC_Clients_Loan' => (int)($r->oc_loan_count ?? 0),
                'OC_Clients_Amount' => 0, // Can be calculated if needed
                'Total_Arrears_Loan' => (int)($r->arrears_loan_count ?? 0),
                'Total_Arrear_Customer' => (int)($r->arrears_customer_count ?? 0),
                'Total_Capital_Arrears' => round((float)($r->capital_arrears ?? 0), 2),
                'Total_Interest_Arrears' => round((float)($r->interest_arrears ?? 0), 2),
                'Installment_Count' => (int)($r->installment_count ?? 0),
                'IC_Loan_Count' => (int)($r->ic_loan_count ?? 0),
                'IC_Total_Balance' => round((float)($r->ic_total_balance ?? 0), 2),
            ];
        });

        return $data;
    }
}

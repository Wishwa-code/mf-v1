<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentReportExecutiveSummaryController extends Controller
{
    public function index(Request $request){
        $branch = tableWithBranch('branch')->where('status','=','1')->get();
        $product = tableWithBranch('loan_category')->where('status','=','1')->get();
        $officer = tableWithBranch('user')->where('Status','=','1')->get();
        $branch_access=session('branch_access');
        
        // Get report data
        $data = $this->getReportData($request);
        
        return view('pages.InvestmentReportExecutiveSummary',compact('branch','branch_access','product','officer','data'));
    }

    private function getReportData(Request $request)
    {
        // Filters ("" => null = All)
        $productId   = $request->filled('product') ? $request->input('product') : null;
        $collectorId = $request->filled('loan_officer') ? $request->input('loan_officer') : null;

        // Dates (defaults to current month start to today)
        $startInput = $request->input('from');
        $endInput   = $request->input('to');
        
        if ($startInput && $endInput) {
            $start = \Carbon\Carbon::parse($startInput)->startOfDay();
            $end = \Carbon\Carbon::parse($endInput)->endOfDay();
        } else {
            $start = \Carbon\Carbon::now()->startOfMonth()->startOfDay();
            $end = \Carbon\Carbon::now()->endOfDay();
        }

        $today = now()->toDateString();                 // maturity cutoff for OC
        $customerIdCol = 'Customer_idCustomer';         // FK column in customer_loan (adjust if different)

        // ---------- as-of START snapshot (keep your existing logic) ----------
        $beginningOne = DB::query()->fromSub(function ($q) use ($start) {

            // 1) Sum of capital actually paid on/before $start, per loan  (DATE FIX)
            $capitalPaidBefore = DB::table('installments as ins')
                ->selectRaw('ins.Customer_Loan_idCustomer_Loan as Loan_ID, COALESCE(SUM(ins.capital_amount), 0) as capital_paid_before')
                ->whereDate('ins.Installment_Date', '<', $start->toDateString())
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');

            // 2) Join to loan table to get original capital (Amount) and compute balance
            $inner = DB::table('customer_loan as cl')
                ->leftJoinSub($capitalPaidBefore, 'p', 'p.Loan_ID', '=', 'cl.idCustomer_Loan')
                ->selectRaw("
                cl.idCustomer_Loan as Loan_ID,
                GREATEST(COALESCE(cl.Amount,0) - COALESCE(p.capital_paid_before,0), 0) as Capital_Balance,
                cl.Date_Time as Date_Time,
                0 as Loan_Log_ID,
                1 as rn
            ")
                ->when(session()->has('branch_id'), function ($qb) {
                    // Optional: keep branch scope if you need it
                    $qb->where('cl.branch_id', session('branch_id'));
                })
                ->orderBy('cl.idCustomer_Loan'); // not required; just to keep things deterministic

            // Push the built SELECT into the outer subquery
            $q->fromSub($inner, 't');

        }, 'x')
            ->where('rn', 1)
            ->where('Date_Time', '<', $start);

        // ---------- as-of END snapshot (NEW) ----------
        $endingOne = DB::query()->fromSub(function ($q) use ($end) {

            // (DATE FIX)
            $capitalPaidUntilEnd = DB::table('installments as ins')
                ->selectRaw('ins.Customer_Loan_idCustomer_Loan as Loan_ID, COALESCE(SUM(ins.capital_amount), 0) as capital_paid_until_end')
                ->whereDate('ins.Installment_Date', '<=', $end->toDateString())
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');

            DB::table('customer_loan as cl')
                ->leftJoinSub($capitalPaidUntilEnd, 'p', 'p.Loan_ID', '=', 'cl.idCustomer_Loan')
                ->selectRaw('
                cl.idCustomer_Loan as Loan_ID,
                GREATEST(COALESCE(cl.Amount,0) - COALESCE(p.capital_paid_until_end,0), 0) as Capital_Balance,
                ? as Date_Time,
                0 as Loan_Log_ID,
                1 as rn
            ', [$end])
                ->when(session()->has('branch_id'), function ($qb) {
                    $qb->where('cl.branch_id', session('branch_id'));
                })
                ->orderBy('cl.idCustomer_Loan')
                ->tap(function ($builder) use ($q) {
                    $q->fromSub($builder, 't');
                });

        }, 'y')->where('rn', 1);

        // 2) Investment sum per loan in [start, end]  (cl.Date_Time likely DATETIME → keep as-is)
        $investment = DB::query()->fromSub(function ($q) use ($start, $end) {
            $q->from('customer_loan as cl')
                ->selectRaw('cl.idCustomer_Loan as Loan_ID, SUM(cl.Amount) as Investment_Sum')
                ->whereBetween('cl.Date_Time', [$start, $end])
                ->groupBy('cl.idCustomer_Loan');
        }, 'inv');

        // 3) Depletion (capital payments) per loan in [start, end]  (DATE FIX)
        $depletion = DB::query()->fromSub(function ($q) use ($start,$end) {
            $q->from('installments as i')
                ->selectRaw('i.Customer_Loan_idCustomer_Loan as Loan_ID, SUM(COALESCE(i.capital_amount, 0)) as Depletion_Sum')
                ->whereDate('i.Installment_Date', '>=', $start->toDateString())
                ->whereDate('i.Installment_Date', '<=', $end->toDateString())
                ->groupBy('i.Customer_Loan_idCustomer_Loan');
        }, 'dep');

        // Collections (customer_payments) per COLLECTOR in [start, end]  (already DATE-safe)
        $collectionsByCollector = DB::query()->fromSub(function ($q) use ($start, $end) {
            $q->from('customer_payments as cp')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'cp.Customer_Loan_idCustomer_Loan')
                ->whereDate('cp.Date', '>=', $start->toDateString())
                ->whereDate('cp.Date', '<=', $end->toDateString())
                ->when(session()->has('branch_id'), function ($qq) {
                    $qq->where('cp.branch_id', session('branch_id'));
                })
                ->selectRaw('cl.collector_id as collector_id, SUM(cp.Amount) as Collection_Sum')
                ->groupBy('cl.collector_id');
        }, 'cbc');

        // A) Installments sum by assigned collector within [start, end]  (uses <= end only; leave logic as-is)
        $installmentSum = DB::query()->fromSub(function ($q) use ($start, $end) {
            $q->from('installments as ins')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'ins.Customer_Loan_idCustomer_Loan')
                ->where('ins.Installment_Date','<=',$end->toDateString())
                ->selectRaw('cl.collector_id as collector_id, SUM(ins.Installment_Amount) as Installment_Sum')
                ->groupBy('cl.collector_id');
        }, 'insx');

        // Arrears per LOAN (sum of Total_Balance for installments before $end date)
        $arrearsPerLoan = DB::query()->fromSub(function ($q) use ($end) {
            $q->from('installments as i')
                ->where('i.Installment_Date', '<', $end->toDateString())
                ->selectRaw('
                i.Customer_Loan_idCustomer_Loan as Loan_ID,
                SUM(COALESCE(i.Total_Balance, 0)) as Arrears_Sum
            ')
                ->groupBy('i.Customer_Loan_idCustomer_Loan');
        }, 'arrl');

        // B) Payments sum by assigned collector (<= end)  (keep logic; just ensure DATE-safe compare)
        $paymentsAssigned = DB::query()->fromSub(function ($q) use ($start, $end) {
            $q->from('customer_payments as cp')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'cp.Customer_Loan_idCustomer_Loan')
                ->whereDate('cp.Date','<=',$end->toDateString())
                ->selectRaw('cl.collector_id as collector_id, SUM(cp.Amount) as Pay_Sum')
                ->groupBy('cl.collector_id');
        }, 'payx');

        // C) Savings credits per LOAN (<= end)  (sal.Date_Time likely DATETIME → keep as-is)
        $savingsCredits = DB::query()->fromSub(function ($q) use ($end) {
            $q->from('Customer_Saving_Accounts as csa')
                ->join('Savings_Account_Log as sal', 'sal.Saving_Acount_Id', '=', 'csa.id')
                ->where('sal.Date_Time', '<=', $end->toDateString())
                ->when(session()->has('branch_id'), function ($qq) {
                    // keep branch scoping (use csa's branch; if you prefer the log's, switch to sal.branch_id)
                    $qq->where('csa.branch_id', session('branch_id'));
                })
                ->selectRaw('
                csa.Loan_Id as Loan_ID,
                SUM(COALESCE(sal.Credit, 0)) as Savings_Credit_Sum
            ')
                ->groupBy('csa.Loan_Id');
        }, 'sav');

        // TOTAL LOANS per collector (filtered by date and optional product filter)
        $collectorLoanTotals = DB::query()->fromSub(function ($q) use ($productId, $end) {
            $q->from('customer_loan as cl')
                ->where('cl.Date_Time', '<=', $end)
                ->when(session()->has('branch_id'), function ($qq) {
                    $qq->where('cl.branch_id', session('branch_id'));
                })
                ->when($productId, fn($qq) => $qq->where('cl.Loan_Category_idLoan_Category', $productId))
                ->selectRaw('cl.collector_id as collector_id,
               COUNT(*) as Loans_Count_Total,
               SUM(cl.Amount) as Loans_Amount_Total')
                ->groupBy('cl.collector_id');
        }, 'lt');

        // PENALTY ARREARS per collector (<= end)  (already using <= end string)
        $penaltyArrears = DB::query()->fromSub(function ($q) use ($start, $end) {
            $q->from('installments as ins')
                ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'ins.Customer_Loan_idCustomer_Loan')
                ->where('ins.Installment_Date','<=',$end->toDateString())
                ->selectRaw('cl.collector_id as collector_id, SUM(ins.Panalty_Balance) as Penalty_Sum')
                ->groupBy('cl.collector_id');
        }, 'pnlx');

        // OC loans per collector (maturity <= end date, loan created <= end)
        $ocLoans = DB::query()->fromSub(function ($q) {
            $q->from('installments as ins')
                ->selectRaw('ins.Customer_Loan_idCustomer_Loan as Loan_ID, MAX(ins.Installment_Date) as maturity_date')
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');
        }, 'm')
            ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'm.Loan_ID')
            ->where('m.maturity_date', '<=', $end->toDateString())
            ->where('cl.Date_Time', '<=', $end)
            ->when(session()->has('branch_id'), function ($qq) {
                $qq->where('cl.branch_id', session('branch_id'));
            })
            ->selectRaw('cl.collector_id as collector_id, COUNT(*) as oc_count')
            ->groupBy('cl.collector_id');

        // TOTAL CLIENTS per collector (filtered by date and optional product filter)
        $totalClients = DB::query()->fromSub(function ($q) use ($productId, $customerIdCol, $end) {
            $q->from('customer_loan as cl')
                ->where('cl.Date_Time', '<=', $end)
                ->when(session()->has('branch_id'), function ($qq) {
                    $qq->where('cl.branch_id', session('branch_id'));
                })
                ->when($productId, fn($qq) => $qq->where('cl.Loan_Category_idLoan_Category', $productId))
                ->selectRaw("cl.collector_id as collector_id, COUNT(DISTINCT cl.`$customerIdCol`) as Clients_Total")
                ->groupBy('cl.collector_id');
        }, 'ct');

        // OC CLIENTS per collector (maturity <= end date, loan created <= end)
        $ocClients = DB::query()->fromSub(function ($q) {
            $q->from('installments as ins')
                ->selectRaw('ins.Customer_Loan_idCustomer_Loan as Loan_ID, MAX(ins.Installment_Date) as maturity_date')
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');
        }, 'm')
            ->join('customer_loan as cl', 'cl.idCustomer_Loan', '=', 'm.Loan_ID')
            ->where('m.maturity_date','<=', $end->toDateString())
            ->where('cl.Date_Time', '<=', $end)
            ->when(session()->has('branch_id'), function ($qq) {
                $qq->where('cl.branch_id', session('branch_id'));
            })
            ->selectRaw("cl.collector_id as collector_id, COUNT(DISTINCT cl.`$customerIdCol`) as OC_Clients")
            ->groupBy('cl.collector_id');

        // Outstanding (sum of installments.Total_Balance) per LOAN (NO date filter)
        $outstandingPerLoan = DB::query()->fromSub(function ($q) {
            $q->from('installments as ins')
                ->selectRaw('
                ins.Customer_Loan_idCustomer_Loan as Loan_ID,
                SUM(COALESCE(ins.Total_Balance, 0)) as Outstanding_Sum
            ')
                ->groupBy('ins.Customer_Loan_idCustomer_Loan');
        }, 'out');

        /*
         * Collector ↔ Product loan counts AND amounts, filtered by date.
         * Format: "productId:count:amount,productId:count:amount,..."
         */
        $collectorProductCounts = DB::query()->fromSub(function ($q) use ($end) {
            $q->from('customer_loan as cl')
                ->where('cl.Date_Time', '<=', $end)
                ->when(session()->has('branch_id'), function ($qq) {
                    $qq->where('cl.branch_id', session('branch_id'));
                })
                ->selectRaw('cl.collector_id,
               cl.Loan_Category_idLoan_Category as product_id,
               COUNT(*) as cnt,
               SUM(COALESCE(cl.Amount, 0)) as total_amount')
                ->groupBy('cl.collector_id', 'cl.Loan_Category_idLoan_Category');
        }, 'pp')
            ->selectRaw('pp.collector_id,
         GROUP_CONCAT(CONCAT(pp.product_id, ":", pp.cnt, ":", pp.total_amount)
                      ORDER BY pp.product_id SEPARATOR ",") as product_kv')
            ->groupBy('pp.collector_id');

        // ---------- Main rollup per collector ----------
        $collectorRows = DB::table('customer_loan as cl')
            ->join('loan_category as lc', 'lc.idLoan_Category', '=', 'cl.Loan_Category_idLoan_Category')
            ->leftJoin('user as u', 'u.id', '=', 'cl.collector_id')

            ->leftJoinSub($beginningOne, 'bs',   fn($j) => $j->on('bs.Loan_ID', '=', 'cl.idCustomer_Loan'))
            ->leftJoinSub($endingOne,    'es',   fn($j) => $j->on('es.Loan_ID', '=', 'cl.idCustomer_Loan'))  // NEW
            ->leftJoinSub($investment,   'inv',  fn($j) => $j->on('inv.Loan_ID', '=', 'cl.idCustomer_Loan'))
            ->leftJoinSub($depletion,    'dep',  fn($j) => $j->on('dep.Loan_ID', '=', 'cl.idCustomer_Loan'))
            ->leftJoinSub($collectionsByCollector, 'cbc', fn($j) => $j->on('cbc.collector_id', '=', 'u.id'))
            ->leftJoinSub($installmentSum,'insx',fn($j) => $j->on('insx.collector_id', '=', 'u.id'))
            ->leftJoinSub($paymentsAssigned,'payx',fn($j) => $j->on('payx.collector_id', '=', 'u.id'))
            ->leftJoinSub($collectorLoanTotals,'lt', fn($j) => $j->on('lt.collector_id', '=', 'u.id'))
            ->leftJoinSub($penaltyArrears, 'pnlx', fn($j) => $j->on('pnlx.collector_id', '=', 'u.id'))
            ->leftJoinSub($ocLoans, 'ocx', fn($j) => $j->on('ocx.collector_id', '=', 'u.id'))
            ->leftJoinSub($totalClients, 'ct', fn($j) => $j->on('ct.collector_id', '=', 'u.id'))
            ->leftJoinSub($ocClients, 'occt', fn($j) => $j->on('occt.collector_id', '=', 'u.id'))
            ->leftJoinSub($savingsCredits, 'sav', fn($j) => $j->on('sav.Loan_ID', '=', 'cl.idCustomer_Loan'))
            ->leftJoinSub($outstandingPerLoan, 'out', fn($j) => $j->on('out.Loan_ID', '=', 'cl.idCustomer_Loan'))

            // NEW: join collector ↔ product counts
            ->leftJoinSub($collectorProductCounts, 'pc', fn($j) => $j->on('pc.collector_id', '=', 'u.id'))
            // NEW: join arrears per collector
            ->leftJoinSub($arrearsPerLoan, 'arrl', fn($j) => $j->on('arrl.Loan_ID', '=', 'cl.idCustomer_Loan'))

            // optional filters
            ->when($productId,   fn($q) => $q->where('lc.idLoan_Category', $productId))
            ->when($collectorId, fn($q) => $q->where('cl.collector_id', $collectorId))
            ->where('cl.Date_Time', '<=', $end)

            // group by collector
            ->groupBy('u.id', 'u.Full_Name')

            ->selectRaw('
            COALESCE(u.id, 0)                      as collector_id,
            COALESCE(u.Full_Name, "—")             as collector,
            COUNT(DISTINCT cl.idCustomer_Loan)     as loan_count,

            SUM(COALESCE(bs.Capital_Balance, 0))   as beginning_total,
            SUM(COALESCE(es.Capital_Balance, 0))   as ending_total,       -- NEW
            SUM(COALESCE(inv.Investment_Sum, 0))   as investment_total,
            SUM(COALESCE(dep.Depletion_Sum, 0))    as depletion_total,

            COALESCE(MAX(cbc.Collection_Sum), 0)    as collection_total,
            COALESCE(MAX(insx.Installment_Sum), 0) as installment_total,
            COALESCE(MAX(payx.Pay_Sum), 0)         as payment_total_assigned,
            COALESCE(MAX(sav.Savings_Credit_Sum), 0)         as payment_saving,
            /* Use the pre-aggregated arrears by collector */
            COALESCE(SUM(arrl.Arrears_Sum), 0) as arrears_total,

            COALESCE(MAX(lt.Loans_Count_Total), 0)  as total_loans,
            COALESCE(MAX(lt.Loans_Amount_Total), 0) as total_loans_amount,

            COALESCE(MAX(pnlx.Penalty_Sum), 0)      as penalty_arrears_total,
            COALESCE(MAX(ocx.oc_count), 0)          as oc_loan_count,

            COALESCE(MAX(ct.Clients_Total), 0)      as total_clients,
            COALESCE(MAX(occt.OC_Clients), 0)       as oc_clients,

            /* NEW: flattened product counts like "12:34,15:7" */
            COALESCE(MAX(pc.product_kv), "")        as product_kv, SUM(COALESCE(out.Outstanding_Sum, 0)) as outstanding_total
        ')
            ->where('cl.branch_id', session('branch_id'))   // keep branch scoping
            ->whereIn('cl.Status', [0, 1])
            ->orderBy('collector')
            ->get();

        // Shape response
        $data = collect($collectorRows)->map(function($r) {
            $begin   = (float) ($r->beginning_total ?? 0);
            $invest  = (float) ($r->investment_total ?? 0);
            $deplete = (float) ($r->depletion_total ?? 0);

            // Use snapshot as-of $end (DON'T change other fields)
            $endStock    = $begin+$invest-$deplete;

            $arrears     = (float) ($r->arrears_total ?? 0);

            $portfolio   = $endStock+$arrears;
            $debtorRatio = $endStock > 0 ? ($arrears / $endStock) * 100 : 0;

            $totalClients  = (int) ($r->total_clients ?? 0);
            $ocClients     = (int) ($r->oc_clients ?? 0);
            $activeClients = max(0, $totalClients - $ocClients);

            // Parse pc.product_kv → { product_id: {count, amount}, ... }
            // Format: "productId:count:amount,productId:count:amount,..."
            $perProduct = [];
            $kv = (string)($r->product_kv ?? '');
            if ($kv !== '') {
                foreach (explode(',', $kv) as $pair) {
                    $parts = explode(':', $pair);
                    if (count($parts) >= 3) {
                        $pid = (string)(int)$parts[0];
                        $cnt = (int)$parts[1];
                        $amt = (float)$parts[2];
                        $perProduct[$pid] = [
                            'count' => $cnt,
                            'amount' => round($amt, 2)
                        ];
                    }
                }
            }

            return [
                'Loan_Officer'               => $r->collector ?? '—',
                'Beginning_Stock'            => round($begin, 2),
                'Current_End_Stock'          => round($endStock, 2),
                'Investment'                 => round($invest, 2),
                'Depletion'                  => round($deplete, 2),
                'Collection'                 => round((float) ($r->collection_total ?? 0), 2),
                'Arrears'                    => round($arrears, 2),
                'Portfolio'                  => round($portfolio, 2),
                'Debtor_Ratio'               => round($debtorRatio, 2),
                'Penalty_Arrears'            => round((float) ($r->penalty_arrears_total ?? 0), 2),
                'Total_Loans'                => (int)   ($r->total_loans ?? 0),
                'OC_Loans'                   => (int)   ($r->oc_loan_count ?? 0),
                'Total_Clients'              => $totalClients,
                'OC_Clients'                 => $ocClients,
                'Active_Clients'             => $activeClients,
                'Total_Outstanding_Balance'  => round((float) ($r->outstanding_total ?? 0), 2),
                // per-collector product data with count and amount
                'product_data'               => (object)$perProduct,
            ];
        });

        return $data;
    }
}

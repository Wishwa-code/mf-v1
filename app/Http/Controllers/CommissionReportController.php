<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionReportController extends Controller
{
    public function index()
    {
        $branches = DB::table('branch')
            ->select('branch_id', 'Name')
            ->orderBy('Name')
            ->get();

        return view('reports.commission_report', [
            'branches' => $branches,
            'dateFrom' => now()->startOfMonth()->toDateString(),
            'dateTo'   => now()->toDateString(),
        ]);
    }



    public function load(Request $request)
    {
        $branchId = (int) $request->branch_id;
        $from     = $request->date_from;
        $to       = $request->date_to;

        if ($branchId <= 0) {
            return response()->json(['message' => 'Please select a branch'], 422);
        }
        if (!$from || !$to) {
            return response()->json(['message' => 'Please select date range'], 422);
        }

        // 1) People
        $people = DB::table('commission_people')
            ->where('branch_id', $branchId)
            ->where('status', 1)
            ->orderByRaw("FIELD(type,'Collector','commission') DESC")
            ->orderBy('full_name')
            ->get(['id', 'full_name', 'type', 'user_id']);

        // 2) Rates
        $rates = DB::table('commission_rates')
            ->where('branch_id', $branchId)
            ->get(['commission_person_id', 'product_id', 'rate']);

        $rateMap = [];
        foreach ($rates as $r) {
            $rateMap[$r->commission_person_id . '_' . $r->product_id] = (float) $r->rate;
        }

        // 3) Last payment date (only date, no user)
        $lastPaySub = DB::table('customer_payments')
            ->select([
                DB::raw('Customer_Loan_idCustomer_Loan as loan_id'),
                DB::raw('MAX(DATE(`Date`)) as last_payment_date')
            ])
            ->where('branch_id', $branchId)
            ->groupBy('Customer_Loan_idCustomer_Loan');

        // 4) Loans: settle date from last payment date, collector from customer_loan.collector_id
        $productIdCol = 'Loan_Category_idLoan_Category';

        $customerNameExpr = DB::raw("CONCAT(IFNULL(c.First_Name,''),' ',IFNULL(c.Last_Name,'')) as customer_name");

        $loans = DB::table('customer_loan as l')
            ->joinSub($lastPaySub, 'lp', function ($join) {
                $join->on('lp.loan_id', '=', 'l.idCustomer_Loan');
            })
            ->leftJoin('customer as c', 'c.idCustomer', '=', 'l.Customer_idCustomer')
            ->where('l.branch_id', $branchId)
            ->where('l.Status', 1)
            ->whereBetween('lp.last_payment_date', [$from, $to])
            ->select([
                'l.idCustomer_Loan as loan_id',
                'l.Loan_No as loan_no',
                $customerNameExpr,
                DB::raw("IFNULL(l.Amount,0) as capital_amount"),
                DB::raw("IFNULL(l.Interest_Amount,0) as interest_amount"),
                DB::raw("IFNULL(l.Installment_Count,0) as months"),
                DB::raw("IFNULL(l.$productIdCol,0) as product_id"),
                DB::raw("lp.last_payment_date as settle_date"),
                DB::raw("IFNULL(l.collector_id,0) as collector_id"), // ✅ IMPORTANT
            ])
            ->orderBy('lp.last_payment_date', 'desc')
            ->orderBy('l.idCustomer_Loan', 'desc')
            ->get();

        // 5) Build rows + totals
        $rows = [];
        $summaryTotals = [];
        $summaryByType = ['Collector' => 0, 'commission' => 0];

        foreach ($people as $p) {
            $summaryTotals[$p->id] = 0;
        }

        $i = 1;
        $totalInterest = 0;

        foreach ($loans as $loan) {

            $interest = (float) $loan->interest_amount;
            if ($interest < 0) $interest = 0;
            $totalInterest += $interest;

            $row = [
                'no' => $i++,
                'loan_no' => $loan->loan_no ?: '-',
                'name' => trim($loan->customer_name) ?: '-',
                'capital' => (float)$loan->capital_amount,
                'months' => (int)$loan->months,
                'interest' => (float)$interest,
                'product_id' => (int)$loan->product_id,
                'settle_date' => $loan->settle_date,
                'collector_id' => (int)$loan->collector_id,
                'commissions' => [],
            ];

            foreach ($people as $p) {

                $key  = $p->id . '_' . $loan->product_id;
                $rate = $rateMap[$key] ?? 0;

                // base commission for everyone
                $commission = round($interest * ($rate / 100), 2);

                /**
                 * ✅ RULE:
                 * - type = commission => always gets commission
                 * - type = Collector  => ONLY if commission_people.user_id == customer_loan.collector_id
                 */
                if ($p->type === 'Collector') {
                    $personUserId = (int)($p->user_id ?? 0);
                    if (!($personUserId > 0 && $personUserId === (int)$loan->collector_id)) {
                        $commission = 0;
                    }
                }

                $row['commissions'][$p->id] = $commission;
                $summaryTotals[$p->id] += $commission;

                if (isset($summaryByType[$p->type])) {
                    $summaryByType[$p->type] += $commission;
                }
            }

            $rows[] = $row;
        }
        // =========================
// 6) Column Totals (Per Person)
// =========================
        $columnTotals = [];

        foreach ($people as $p) {
            $columnTotals[$p->id] = 0;
        }

        foreach ($rows as $r) {
            foreach ($r['commissions'] as $pid => $val) {
                $columnTotals[$pid] += $val;
            }
        }

// =========================
// 7) Grand Total
// =========================
        $grandTotal = array_sum($columnTotals);


        $summary = [];
        foreach ($people as $p) {
            $summary[] = [
                'id' => $p->id,
                'name' => $p->full_name,
                'type' => $p->type,
                'total' => round($summaryTotals[$p->id] ?? 0, 2),
            ];
        }

        return response()->json([
            'people' => $people,
            'summary' => $summary,
            'rows' => $rows,
            'columnTotals' => $columnTotals,
            'grandTotal' => round($grandTotal, 2),
            'meta' => [
                'branch_id' => $branchId,
                'date_from' => $from,
                'date_to' => $to,
                'total_interest' => round($totalInterest, 2),
                'collector_total' => round($summaryByType['Collector'] ?? 0, 2),
                'commission_total' => round($summaryByType['commission'] ?? 0, 2),
            ]
        ]);
    }


    public function print(Request $request)
    {
        // same output as load()
        $resp = $this->load($request);

        if ($resp->getStatusCode() !== 200) {
            abort(422, 'Invalid request for print.');
        }

        $data = $resp->getData(true);

        $branch = DB::table('branch')
            ->where('branch_id', (int)$request->branch_id)
            ->first();

        return view('reports.commission_report_print', [
            'branch' => $branch,
            'people' => $data['people'],
            'summary' => $data['summary'],
            'rows' => $data['rows'],
            'columnTotals' => $data['columnTotals'],
            'grandTotal' => $data['grandTotal'],
            'meta' => $data['meta'],
        ]);

    }
}

<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoansController
{
    /**
     * GET /api/loans
     * Query params (same as your normal controller):
     *  - group, category, customer, center_details, route, loan_number_search
     *  - per_page (default 10)
     *
     * Loan Status mapping:
     *   -1 = pending   |  0 = ongoing  |  1 = settled  | -2 = deleted
     */
    public function index(Request $request)
    {
        $branchId = (int) $request->attributes->get('branch_id');
        $user     = $request->user();
        $userId   = (int) $user->id;
        $collectorFlag = (int) ($user->collector ?? 0);

        // Filters (string '0' means "All")
        $groupId       = $request->query('group', '0');
        $categoryId    = $request->query('category', '0');
        $customerId    = $request->query('customer', '0');
        $centerId      = $request->query('center_details', '0');
        $routeId       = $request->query('route', '0');
        $loanNoSearch  = $request->query('loan_number_search', '');

        $perPage = (int) $request->query('per_page', 10);

        // ---------------- Subqueries ----------------
        // (A) Customer -> Group/Center mapping
        $cusGroupSub = DB::raw("
            (
                SELECT
                    ghc.cus_id,
                    ghc.group_id,
                    cg.Name      AS group_name,
                    cg.center_id AS center_id
                FROM group_has_customer ghc
                LEFT JOIN customer_group cg
                    ON ghc.group_id = cg.idCustomer_Group
            ) AS subquery
        ");

        // (B) Total penalty per loan (branch-scoped)
        $penaltySub = DB::raw("
            (
                SELECT
                    ins.Customer_Loan_idCustomer_Loan,
                    CAST(SUM(ins.Panalty_Balance) AS DECIMAL(18,2)) AS total_penalty
                FROM installments ins
                WHERE ins.branch_id = {$branchId}
                GROUP BY ins.Customer_Loan_idCustomer_Loan
            ) AS penalty_summary
        ");

        // ---------------- Main list query ----------------
        $q = DB::table('customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin($cusGroupSub, 'customer.idCustomer', '=', 'subquery.cus_id')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->join('user as u1', 'customer_loan.User_idUser', '=', 'u1.id')
            ->join('user as u2', 'customer_loan.lending_officer_id', '=', 'u2.id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->leftJoin($penaltySub, 'customer_loan.idCustomer_Loan', '=', 'penalty_summary.Customer_Loan_idCustomer_Loan')
            ->where('customer_loan.branch_id', $branchId)
            // You used only ongoing in normal flow; keep it. If you want to list all, remove next line.
            ->where('customer_loan.Status', '0')
            ->select([
                // Loan
                'customer_loan.idCustomer_Loan',
                'customer_loan.Loan_No',
                'customer_loan.Loan_Category_idLoan_Category',
                'customer_loan.Customer_idCustomer',
                'customer_loan.Date_Time',
                'customer_loan.Amount',
                'customer_loan.Installment_Amount',
                'customer_loan.Installment_Count',
                'customer_loan.capital_balance',
                'customer_loan.Balance_Amount',
                'customer_loan.Status',
                'customer_loan.type',
                'customer_loan.Vehicle_No',
                'customer_loan.collector_id',
                'customer_loan.lending_officer_id',
                'customer_loan.branch_id',

                // Loan category name
                DB::raw('loan_category.Name AS loan_name'),

                // Customer
                'customer.idCustomer',
                'customer.First_Name',
                'customer.Last_Name',
                'customer.Nic',
                'customer.cus_number',
                'customer.route_id',

                // Center/Route/Group
                'center.idCenter',
                DB::raw('IFNULL(subquery.group_name, "-") AS group_name'),
                DB::raw('IFNULL(center.No, "-") AS center_no'),
                DB::raw('IFNULL(route.name, "-") AS route_name'),

                // Penalty
                DB::raw('IFNULL(penalty_summary.total_penalty, 0.00) AS total_penalty'),

                // Users
                DB::raw('u1.Full_Name AS user_name'),
                DB::raw('u2.Full_Name AS lending_officer'),

                // Status text
                DB::raw("
                    CASE customer_loan.Status
                        WHEN -1 THEN 'Pending'
                        WHEN  0 THEN 'Ongoing'
                        WHEN  1 THEN 'Settled'
                        WHEN -2 THEN 'Deleted'
                        ELSE 'Unknown'
                    END AS loan_status_text
                "),
            ]);

        // Collector restriction (if user is a collector)
        if ($collectorFlag === 1) {
            $q->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', $userId);
        }

        // ----------- Filters ------------
        if ($groupId !== '0') {
            $q->where('subquery.group_id', $groupId);
        }
        if ($categoryId !== '0') {
            $q->where('loan_category.idLoan_Category', $categoryId);
        }
        if ($customerId !== '0') {
            $q->where('customer.idCustomer', $customerId);
        }
        if ($centerId !== '0') {
            $q->where('center.idCenter', $centerId);
        }
        if ($routeId !== '0') {
            $q->where('customer.route_id', $routeId);
        }
        if (!empty($loanNoSearch)) {
            $q->where('customer_loan.Loan_No', 'LIKE', '%' . $loanNoSearch . '%');
        }

        $q->orderBy('customer_loan.idCustomer_Loan', 'desc');

        // Paginate
        $items = $q->paginate($perPage);

        // Money fields to 2 decimals for each row
        $items->setCollection(
            $items->getCollection()->map(function ($r) {
                $r->Amount             = $this->fix2($r->Amount);
                $r->Installment_Amount = $this->fix2($r->Installment_Amount);
                $r->capital_balance    = $this->fix2($r->capital_balance);
                $r->Balance_Amount     = $this->fix2($r->Balance_Amount);
                $r->total_penalty      = $this->fix2($r->total_penalty);
                return $r;
            })
        );

        // ---------------- Totals query (apply same filters!) ----------------
        $t = DB::table('customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin($cusGroupSub, 'customer.idCustomer', '=', 'subquery.cus_id')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->where('customer_loan.branch_id', $branchId)
            ->where('customer_loan.Status', '0');

        if ($collectorFlag === 1) {
            $t->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', $userId);
        }
        if ($groupId !== '0')     { $t->where('subquery.group_id', $groupId); }
        if ($categoryId !== '0')  { $t->where('loan_category.idLoan_Category', $categoryId); }
        if ($customerId !== '0')  { $t->where('customer.idCustomer', $customerId); }
        if ($centerId !== '0')    { $t->where('center.idCenter', $centerId); }
        if ($routeId !== '0')     { $t->where('customer.route_id', $routeId); }
        if (!empty($loanNoSearch)) { $t->where('customer_loan.Loan_No', 'LIKE', '%' . $loanNoSearch . '%'); }

        // Totals (cast to 2 decimals at SQL level)
        $totalsRow = $t->selectRaw('
                COUNT(*) AS totalLoanCount,
                CAST(SUM(customer_loan.capital_balance) AS DECIMAL(18,2)) AS totalCapitalBalance,
                CAST(SUM(customer_loan.Balance_Amount)  AS DECIMAL(18,2)) AS totalPendingAmount,
                CAST(SUM(customer_loan.Amount)          AS DECIMAL(18,2)) AS totalLoanAmount
            ')
            ->first();

        // Normalize to numeric with 2 decimals
        $totals = [
            'totalLoanCount'      => (int)   ($totalsRow->totalLoanCount ?? 0),
            'totalCapitalBalance' => $this->fix2($totalsRow->totalCapitalBalance ?? 0),
            'totalPendingAmount'  => $this->fix2($totalsRow->totalPendingAmount ?? 0),
            'totalLoanAmount'     => $this->fix2($totalsRow->totalLoanAmount ?? 0),
        ];

        // ---------------- Permissions & designation ----------------
        $permissions = DB::table('user_privileges_has_user')
            ->where('user_id', $userId)
            ->pluck('value', 'permission_key'); // [permission_key => value]

        $designation   = $user->Designation ?? null;
        $current_loan  = (int) ($permissions['current_loan_delete']   ?? 0);
        $loan_agreement= (int) ($permissions['current_loan_agreement']?? 0);
        $extra_charge  = (int) ($permissions['loan_extra_charges']    ?? 0);

        return response()->json([
            'item'           => $items, // paginator with rows
            'designation'    => $designation,
            'current_loan'   => $current_loan,
            'loan_agreement' => $loan_agreement,
            'extra_charge'   => $extra_charge,
            'totals'         => $totals,
            'message'        => 'notall',
        ], 200);
    }

    // ---------------- helpers ----------------

    private function fix2($v): float
    {
        return round((float)$v, 2);
    }

    public function show(Request $request, int $id)
    {
        $branchId = (int) $request->attributes->get('branch_id');
        $user     = $request->user();
        $userId   = (int) $user->id;
        $collectorFlag = (int) ($user->collector ?? 0);

        // --- Subqueries (same style as index) ---
        $cusGroupSub = DB::raw("
        (
            SELECT
                ghc.cus_id,
                ghc.group_id,
                cg.Name      AS group_name,
                cg.center_id AS center_id
            FROM group_has_customer ghc
            LEFT JOIN customer_group cg
                ON ghc.group_id = cg.idCustomer_Group
        ) AS subquery
    ");

        $penaltySub = DB::raw("
        (
            SELECT
                ins.Customer_Loan_idCustomer_Loan,
                CAST(SUM(ins.Panalty_Balance) AS DECIMAL(18,2)) AS total_penalty
            FROM installments ins
            WHERE ins.branch_id = {$branchId}
            GROUP BY ins.Customer_Loan_idCustomer_Loan
        ) AS penalty_summary
    ");

        $instSummarySub = DB::raw("
        (
            SELECT
                i.Customer_Loan_idCustomer_Loan,
                CAST(SUM(i.Total_Balance) AS DECIMAL(18,2)) AS total_balance,
                CAST(SUM(i.Paid_Amount)   AS DECIMAL(18,2)) AS total_paid_amount,
                MAX(i.Installment_Date)                      AS last_installment_date
            FROM installments i
            WHERE i.branch_id = {$branchId}
            GROUP BY i.Customer_Loan_idCustomer_Loan
        ) AS inst_summary
    ");

        // --- Main row ---
        $q = DB::table('customer_loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin($cusGroupSub, 'customer.idCustomer', '=', 'subquery.cus_id')
            ->join('loan_category', 'customer_loan.Loan_Category_idLoan_Category', '=', 'loan_category.idLoan_Category')
            ->join('user as u1', 'customer_loan.User_idUser', '=', 'u1.id')
            ->join('user as u2', 'customer_loan.lending_officer_id', '=', 'u2.id')
            ->leftJoin('center', 'subquery.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->leftJoin($penaltySub, 'customer_loan.idCustomer_Loan', '=', 'penalty_summary.Customer_Loan_idCustomer_Loan')
            ->leftJoin($instSummarySub, 'customer_loan.idCustomer_Loan', '=', 'inst_summary.Customer_Loan_idCustomer_Loan')
            ->where('customer_loan.branch_id', $branchId)
            ->where('customer_loan.idCustomer_Loan', $id)
            ->select([
                // Loan
                'customer_loan.idCustomer_Loan',
                'customer_loan.Loan_No',
                'customer_loan.Loan_Category_idLoan_Category',
                'customer_loan.Customer_idCustomer',
                'customer_loan.Date_Time',
                'customer_loan.Amount',
                'customer_loan.Installment_Amount',
                'customer_loan.Installment_Count',
                'customer_loan.capital_balance',
                'customer_loan.Balance_Amount',
                'customer_loan.Status',
                'customer_loan.type',
                'customer_loan.Vehicle_No',
                'customer_loan.collector_id',
                'customer_loan.lending_officer_id',
                'customer_loan.branch_id',

                // Loan category
                DB::raw('loan_category.Name AS loan_name'),

                // Customer
                'customer.idCustomer',
                'customer.First_Name',
                'customer.Last_Name',
                'customer.Nic',
                'customer.cus_number',
                'customer.route_id',

                // Center/Route/Group
                'center.idCenter',
                DB::raw('IFNULL(subquery.group_name, "-") AS group_name'),
                DB::raw('IFNULL(center.No, "-") AS center_no'),
                DB::raw('IFNULL(route.name, "-") AS route_name'),

                // Penalty & installment summary
                DB::raw('IFNULL(penalty_summary.total_penalty, 0.00) AS total_penalty'),
                DB::raw('IFNULL(inst_summary.total_balance, 0.00)  AS total_balance'),
                DB::raw('IFNULL(inst_summary.total_paid_amount, 0.00) AS total_paid_amount'),
                'inst_summary.last_installment_date',

                // Users
                DB::raw('u1.Full_Name AS user_name'),
                DB::raw('u2.Full_Name AS lending_officer'),

                // Status text
                DB::raw("
                CASE customer_loan.Status
                    WHEN -1 THEN 'Pending'
                    WHEN  0 THEN 'Ongoing'
                    WHEN  1 THEN 'Settled'
                    WHEN -2 THEN 'Deleted'
                    ELSE 'Unknown'
                END AS loan_status_text
            "),
            ]);

        // If the logged-in user is a collector, enforce route restriction
        if ($collectorFlag === 1) {
            $q->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', $userId);
        }

        $row = $q->first();

        if (!$row) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Loan not found',
            ], 404);
        }

        // Normalize money fields to 2 decimals
        $row->Amount             = $this->fix2($row->Amount);
        $row->Installment_Amount = $this->fix2($row->Installment_Amount);
        $row->capital_balance    = $this->fix2($row->capital_balance);
        $row->Balance_Amount     = $this->fix2($row->Balance_Amount);
        $row->total_penalty      = $this->fix2($row->total_penalty);
        $row->total_balance      = $this->fix2($row->total_balance);
        $row->total_paid_amount  = $this->fix2($row->total_paid_amount);

        return response()->json([
            'status' => 'success',
            'loan'   => $row,
        ], 200);
    }

    public function payments(Request $request, int $id)
    {
        $branchId = (int) $request->attributes->get('branch_id');

        // Optional date range filters (YYYY-MM-DD)
        $request->validate([
            'date_from' => 'nullable|date_format:Y-m-d',
            'date_to'   => 'nullable|date_format:Y-m-d',
            'order'     => 'nullable|in:asc,desc',
        ]);

        $dateFrom = $request->query('date_from'); // YYYY-MM-DD
        $dateTo   = $request->query('date_to');   // YYYY-MM-DD
        $order    = strtolower($request->query('order', 'asc')) === 'desc' ? 'desc' : 'asc';

        $q = DB::table('customer_payments as cp')
            ->where('cp.branch_id', $branchId)
            ->where('cp.Customer_Loan_idCustomer_Loan', $id)
            ->select([
                'cp.idCustomer_Payments',
                'cp.Date',
                'cp.time',
                'cp.Description',
                DB::raw('CAST(cp.Amount AS DECIMAL(18,2)) as Amount'),
                'cp.status',
                'cp.Payment_type',
                'cp.comment',
                'cp.Slip',
            ])
            ->orderBy('cp.Date', $order)
            ->orderBy('cp.time', $order);

        if ($dateFrom) {
            $q->where('cp.Date', '>=', $dateFrom);
        }
        if ($dateTo) {
            $q->where('cp.Date', '<=', $dateTo);
        }

        $payments = $q->get()->map(function ($r) {
            // ensure numeric with two decimals in JSON
            $r->Amount = round((float) $r->Amount, 2);
            return $r;
        });

        return response()->json([
            'status'    => 'success',
            'loan_id'   => $id,
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
            'payments'  => $payments,
        ], 200);
    }


    public function byCustomer(Request $request)
    {
        $branchId = (int) $request->attributes->get('branch_id');
        $user     = $request->user();
        $userId   = (int) $user->id;
        $collectorFlag = (int) ($user->collector ?? 0);

        // inputs
        $request->validate([
            'q'        => 'required|string|min:2', // name / NIC / contact / cus_number
            'per_page' => 'nullable|integer|min:1|max:200',
            'order'    => 'nullable|in:asc,desc',
        ]);

        $qstr    = trim($request->query('q'));
        $perPage = (int) $request->query('per_page', 10);
        $order   = strtolower($request->query('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        $query = DB::table('customer_loan as cl')
            ->join('customer as c', 'cl.Customer_idCustomer', '=', 'c.idCustomer')
            ->join('loan_category as lc', 'cl.Loan_Category_idLoan_Category', '=', 'lc.idLoan_Category')
            ->where('cl.branch_id', $branchId)
            ->where('cl.Status', 0) // 🔒 only ongoing loans
            ->where('cl.Customer_idCustomer', '=', $qstr) // ✅ Exact match on Customer ID
            ->select([
                // Only required fields
                'cl.idCustomer_Loan',
                'cl.Loan_No',
                'c.cus_number',
                'c.First_Name',
                'c.Last_Name',
                'c.Contact_No',
                DB::raw('CAST(cl.Balance_Amount     AS DECIMAL(18,2)) AS Total_Loan_Balance'),
                DB::raw('CAST(cl.Installment_Amount AS DECIMAL(18,2)) AS Installment_Amount'),
                DB::raw('CAST(cl.Interest_Rate         AS DECIMAL(18,2)) AS Interest'),       // <-- if your column is named differently, change here
                DB::raw('CAST(cl.Amount             AS DECIMAL(18,2)) AS Loan_Amount'),
                DB::raw('lc.Name AS Loan_Category_Name'),
                DB::raw('cl.Collection_Type AS Collection_Type') // <-- if you have cl.Collection_Type, use that instead
            ])
            ->orderBy('cl.idCustomer_Loan', $order);

        // Optional: enforce collector route restriction (no extra fields returned)
        if ($collectorFlag === 1) {
            $query->join('collector_has_route as chr', 'c.route_id', '=', 'chr.route_id')
                ->where('chr.collector_id', $userId);
        }

        $items = $query->paginate($perPage);

        // Ensure numeric two-decimals in JSON
        $items->setCollection(
            $items->getCollection()->map(function ($r) {
                $r->Total_Loan_Balance  = round((float)$r->Total_Loan_Balance, 2);
                $r->Installment_Amount  = round((float)$r->Installment_Amount, 2);
                $r->Interest            = round((float)$r->Interest, 2);
                $r->Loan_Amount         = round((float)$r->Loan_Amount, 2);
                return $r;
            })
        );

        return response()->json([
            'status' => 'success',
            'query'  => $qstr,
            'loans'  => $items, // paginator with only requested fields
        ], 200);
    }




}

<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CollectionsController
{
    /**
     * GET /api/collections
     * Query params:
     *  - center_details, route, group, customer, status, loan_number_search
     *  - recovery, lending
     *  - per_page (default 10)
     *
     * Status accepted:
     *  - "All" | "Pending" | "Today Collection" | "Late Payments"
     *  - or numeric "0|1|2" → pending|today|late
     */
    public function index(Request $request)
    {
        $branchId = (int) $request->attributes->get('branch_id');
        $user     = $request->user();
        $userId   = (int) $user->id;
        $collectorFlag = (int) ($user->collector ?? 0);

        $centerId   = $request->query('center_details', '0');
        $routeId    = $request->query('route', '0');
        $groupId    = $request->query('group', '0');
        $customerId = $request->query('customer', '0');
        $statusIn   = $request->query('status', 'All');
        $loanNo     = $request->query('loan_number_search', '0');

        $recoveryOfficer = $request->query('recovery', '0');
        $lendingOfficer  = $request->query('lending',  '0');

        $perPage = (int) $request->query('per_page', 10);

        // Normalize status & compute "today" with app timezone
        $status = $this->normalizeStatus($statusIn);
        $today  = Carbon::today(config('app.timezone', 'Asia/Colombo'))->toDateString();

        // ---------- Subquery over installments (2-decimals via CAST) ----------
        $subquery = DB::table('installments')
            ->select('Customer_Loan_idCustomer_Loan')
            ->selectRaw('COUNT(idInstallments) as Calculated_Installment_Count')
            ->selectRaw('CAST(SUM(Total_Balance) AS DECIMAL(18,2)) as Total_Balance')
            ->selectRaw('CAST(SUM(Paid_Amount)   AS DECIMAL(18,2)) as Total_Paid_Amount')
            ->selectRaw('CAST(SUM(CASE WHEN Installment_Date <= ? THEN Total_Balance ELSE 0 END) AS DECIMAL(18,2)) as Total_Balance_until', [$today])
            ->selectRaw('CAST(SUM(CASE WHEN DATE(Installment_Date) = ? THEN Total_Balance ELSE 0 END) AS DECIMAL(18,2)) as Today_installment', [$today])
            ->selectRaw('CAST(SUM(CASE WHEN Installment_Date <  ? THEN Total_Balance ELSE 0 END) AS DECIMAL(18,2)) as arrease', [$today])
            ->where('installments.branch_id', $branchId)
            ->groupBy('Customer_Loan_idCustomer_Loan');

        // ---------- Main query ----------
        $loanQuery = DB::table('customer_loan')
            ->joinSub($subquery, 'installment_summary', function ($join) {
                $join->on('customer_loan.idCustomer_Loan', '=', 'installment_summary.Customer_Loan_idCustomer_Loan');
            })
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->where('customer_loan.branch_id', $branchId)
            ->where('customer_loan.Status', '0')
            ->select(
                'customer.First_Name as customer_name',
                'customer.Last_Name as customer_lastname',
                'customer.Nic as NIC',
                'customer.cus_number as cus_number',
                'customer_loan.Loan_No as Loan_No',
                'customer_loan.Amount as Loan_Amount',
                'customer_loan.idCustomer_Loan as idCustomer_Loan',
                'customer_loan.type as type',
                'customer_loan.Installment_Count as Installment_Count',
                'customer_loan.capital_balance as capital_balance',
                'customer_loan.Installment_Amount as Installment_Amount',
                'customer_loan.Vehicle_No as Vehicle_No',
                'installment_summary.Calculated_Installment_Count',
                'installment_summary.Total_Balance',
                'installment_summary.Total_Paid_Amount',
                'installment_summary.Total_Balance_until',
                'installment_summary.Today_installment',
                'installment_summary.arrease'
            );

        // Filters
        if ($centerId !== '0')   { $loanQuery->where('center.idCenter', $centerId); }
        if ($routeId !== '0')    { $loanQuery->where('route.id_route', $routeId); }
        if ($groupId !== '0')    { $loanQuery->where('customer_group.idCustomer_Group', $groupId); }
        if ($customerId !== '0') { $loanQuery->where('customer.idCustomer', $customerId); }
        if ($recoveryOfficer !== '0') { $loanQuery->where('customer_loan.collector_id', $recoveryOfficer); }
        if ($lendingOfficer  !== '0') { $loanQuery->where('customer_loan.lending_officer_id', $lendingOfficer); }
        if ($collectorFlag === 1) {
            $loanQuery->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', $userId);
        }

        // Status-specific
        if ($status === 'pending') {
            $loanQuery->where('installment_summary.Total_Balance_until', '>', 0);
        } elseif ($status === 'today') {
            $loanQuery->where('installment_summary.Today_installment', '>', 0);
        } elseif ($status === 'late') {
            $loanQuery->where('installment_summary.arrease', '>', 0);
        }

        if ($loanNo !== '0') {
            $loanQuery->where('customer_loan.idCustomer_Loan', $loanNo);
        }

        $loanQuery->orderBy('customer_loan.idCustomer_Loan', 'asc');

        // Paginated items
        $items = $loanQuery->paginate($perPage);

        // Round money fields in paginator collection (safety)
        $items->setCollection(
            $items->getCollection()->map(function ($r) {
                return $this->mapMoneyFields($r);
            })
        );

        // ---------- Totals per loan (same filters) ----------
        $loanQuery2 = DB::table('installments')
            ->join('customer_loan', 'installments.Customer_Loan_idCustomer_Loan', '=', 'customer_loan.idCustomer_Loan')
            ->join('customer', 'customer_loan.Customer_idCustomer', '=', 'customer.idCustomer')
            ->leftJoin('group_has_customer', 'customer.idCustomer', '=', 'group_has_customer.cus_id')
            ->leftJoin('customer_group', 'group_has_customer.group_id', '=', 'customer_group.idCustomer_Group')
            ->leftJoin('center', 'customer_group.center_id', '=', 'center.idCenter')
            ->leftJoin('route', 'customer.route_id', '=', 'route.id_route')
            ->join('user', 'customer_loan.User_idUser', '=', 'user.id')
            ->where('installments.branch_id', $branchId)
            ->where('customer_loan.Status', '0')
            ->groupBy('Customer_Loan_idCustomer_Loan')
            ->select('Customer_Loan_idCustomer_Loan')
            ->selectRaw('COUNT(idInstallments) as Calculated_Installment_Count')
            ->selectRaw('CAST(SUM(Total_Balance) AS DECIMAL(18,2)) as Total_Balance')
            ->selectRaw('CAST(SUM(Paid_Amount)   AS DECIMAL(18,2)) as Total_Paid_Amount')
            ->selectRaw('CAST(SUM(CASE WHEN Installment_Date <= ? THEN Total_Balance ELSE 0 END) AS DECIMAL(18,2)) as Total_Balance_until', [$today])
            ->selectRaw('CAST(SUM(CASE WHEN DATE(Installment_Date) = ? THEN Total_Balance ELSE 0 END) AS DECIMAL(18,2)) as Today_installment', [$today])
            ->selectRaw('CAST(SUM(CASE WHEN Installment_Date <  ? THEN Total_Balance ELSE 0 END) AS DECIMAL(18,2)) as arrease', [$today]);

        if ($collectorFlag === 1) {
            $loanQuery2->join('collector_has_route', 'customer.route_id', '=', 'collector_has_route.route_id')
                ->where('collector_has_route.collector_id', $userId);
        }
        if ($centerId !== '0')   { $loanQuery2->where('center.idCenter', $centerId); }
        if ($routeId !== '0')    { $loanQuery2->where('route.id_route', $routeId); }
        if ($groupId !== '0')    { $loanQuery2->where('customer_group.idCustomer_Group', $groupId); }
        if ($customerId !== '0') { $loanQuery2->where('customer.idCustomer', $customerId); }
        if ($recoveryOfficer !== '0') { $loanQuery2->where('customer_loan.collector_id', $recoveryOfficer); }
        if ($lendingOfficer  !== '0') { $loanQuery2->where('customer_loan.lending_officer_id', $lendingOfficer); }

        if ($status === 'pending') {
            $loanQuery2->havingRaw('SUM(CASE WHEN Installment_Date <= ? THEN Total_Balance ELSE 0 END) > 0', [$today]);
        } elseif ($status === 'today') {
            $loanQuery2->havingRaw('SUM(CASE WHEN DATE(Installment_Date) = ? THEN Total_Balance ELSE 0 END) > 0', [$today]);
        } elseif ($status === 'late') {
            $loanQuery2->havingRaw('SUM(CASE WHEN Installment_Date < ? THEN Total_Balance ELSE 0 END) > 0', [$today]);
        }
        if ($loanNo !== '0') {
            $loanQuery2->where('customer_loan.idCustomer_Loan', $loanNo);
        }

        $totalsPerLoan = $loanQuery2->get()->map(function ($r) {
            return $this->mapMoneyFields($r);
        });

        return response()->json([
            'status'    => 'success',
            'message'   => 'Collections',
            'filters'   => [
                'status' => $status,
                'date'   => $today,
                'branch_id' => $branchId,
            ],
            'items'     => $items,          // paginator (data + meta)
            'gettotal'  => $totalsPerLoan,  // per-loan totals
        ], 200);
    }

    /**
     * GET /api/collections/today
     * Shortcut for today-only.
     */
    public function today(Request $request)
    {
        $request->query->set('status', 'Today Collection');
        return $this->index($request);
    }

    // ---- helpers ------------------------------------------------------------

    private function normalizeStatus($statusIn): string
    {
        $s = trim(strtolower((string)$statusIn));
        if ($s === '0' || $s === 'pending')                           return 'pending';
        if ($s === '1' || $s === 'today' || $s === 'today collection') return 'today';
        if ($s === '2' || $s === 'late'  || $s === 'late payments')    return 'late';
        return 'all';
    }

    private function fix2($v): float
    {
        // returns numeric with two decimals
        return round((float)$v, 2);
    }

    private function mapMoneyFields($r)
    {
        // Ensure consistent 2-decimal numbers in JSON
        if (isset($r->Total_Balance))        $r->Total_Balance = $this->fix2($r->Total_Balance);
        if (isset($r->Total_Paid_Amount))    $r->Total_Paid_Amount = $this->fix2($r->Total_Paid_Amount);
        if (isset($r->Total_Balance_until))  $r->Total_Balance_until = $this->fix2($r->Total_Balance_until);
        if (isset($r->Today_installment))    $r->Today_installment = $this->fix2($r->Today_installment);
        if (isset($r->arrease))              $r->arrease = $this->fix2($r->arrease);
        return $r;
    }
}

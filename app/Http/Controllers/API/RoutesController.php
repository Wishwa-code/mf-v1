<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoutesController
{
    /**
     * GET /api/routes
     * Optional query params:
     *  - search (matches name/root_code)
     *  - officer_id
     *  - collection_type
     *  - date_from, date_to (YYYY-MM-DD) filter on collection_date
     *  - order: asc|desc (default asc by name)
     * Returns: ALL matching routes (no pagination)
     */
    public function index(Request $request)
    {
        $branchId = (int) $request->attributes->get('branch_id');
        $user     = $request->user();
        $userId   = (int) $user->id;
        $collectorFlag = (int) ($user->collector ?? 0);


        // Base query (branch-wise)
        $q = DB::table('route as r')
            ->where('r.branch_id', $branchId)
            ->select([
                'r.id_route',
                'r.name',
                'r.root_code',
                'r.id_officer',
                'r.branch_id',
            ]);

        // 🔒 If collector, only show routes assigned to this collector
        if ($collectorFlag === 1) {
            $q->join('collector_has_route as chr', 'chr.route_id', '=', 'r.id_route')
                ->where('chr.collector_id', $userId);
        }

        $routes = $q->orderBy('r.name', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'routes' => $routes,
        ], 200);
    }



    /**
     * GET /api/routes/{id}/customers
     * Optional query params:
     *  - search (NIC, cus_number, phones, name)
     *  - status (exact match on customers.Status)
     *  - order: asc|desc (default desc by idCustomer)
     * Returns: ALL customers for the route (no pagination)
     */
    public function customers(Request $request, int $id)
    {
        $branchId = (int) $request->attributes->get('branch_id');

        // Optional: allow sorting direction, default desc
        $order = strtolower($request->query('order', 'desc')) === 'asc' ? 'asc' : 'desc';

        // Subquery: pick the latest active loan (Status=0) per customer
        $latestActiveLoan = DB::table('customer_loan as l1')
            ->select('l1.Customer_idCustomer', DB::raw('MAX(l1.idCustomer_Loan) as latest_loan_id'))
            ->where('l1.Status', 0)
            ->groupBy('l1.Customer_idCustomer');

        $customers = DB::table('customer as c')
            // Only customers who have an active loan (inner join with subquery)
            ->joinSub($latestActiveLoan, 'al', function ($join) {
                $join->on('al.Customer_idCustomer', '=', 'c.idCustomer');
            })
            ->join('customer_loan as l', 'l.idCustomer_Loan', '=', 'al.latest_loan_id')
            ->where('c.branch_id', $branchId)
            ->where('c.route_id', $id)
            ->where('c.Status', 1) // only active customers
            ->select(
                'c.idCustomer as idCustomer',
                'c.First_Name as First_Name',
                'c.Last_Name as Last_Name',
                'l.Loan_No as cus_number',          // keep the frontend field name
                'l.idCustomer_Loan as Loan_ID'
            )
            ->orderBy('c.idCustomer', $order)
            ->get();

        return response()->json([
            'status'    => 'success',
            'route_id'  => $id,
            'customers' => $customers,
        ], 200);
    }


}

<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $routes = DB::table('route')
            ->where('branch_id', $branchId)
            ->select([
                'id_route',
                'name',
                'root_code',
                'id_officer',
                'branch_id',
                'collection_type',
                'collection_date',
            ])
            ->orderBy('name', 'asc') // or ->orderBy('id_route', 'asc')
            ->get();

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

        $customers = DB::table('customer as c')
            ->where('c.branch_id', $branchId)
            ->where('c.route_id', $id)
            ->where('c.Status', 1)          // 🔒 only status = 1
            ->select('c.*')
            ->orderBy('c.idCustomer', $order)
            ->get();

        return response()->json([
            'status'    => 'success',
            'route_id'  => $id,
            'customers' => $customers,
        ], 200);
    }

}

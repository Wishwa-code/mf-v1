<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomersController
{
    /**
     * GET /api/customers
     * Query params:
     *  - per_page (default 10)
     *  - search   (matches First_Name, Last_Name, NIC, cus_number, Contact_No)
     *  - route_id, status
     */
    public function index(Request $request)
    {
        $branchId = (int) $request->attributes->get('branch_id');
        $search = trim((string) $request->query('search', ''));
        $routeId = $request->query('route_id');
        $status = $request->query('status'); // optional, but we'll default to 1
        $perPage = (int) $request->query('per_page', 100);

        // Subquery: aggregated groups per customer
        $groupsSub = DB::table('group_has_customer as ghc')
            ->leftJoin('customer_group as cg', 'cg.idCustomer_Group', '=', 'ghc.group_id')
            ->select('ghc.cus_id', DB::raw('GROUP_CONCAT(DISTINCT cg.Name ORDER BY cg.Name SEPARATOR ", ") as group_names'))
            ->groupBy('ghc.cus_id');

        $q = DB::table('customer as c')
            ->leftJoin('route as r', 'r.id_route', '=', 'c.route_id')
            ->leftJoinSub($groupsSub, 'gsub', function ($j) {
                $j->on('gsub.cus_id', '=', 'c.idCustomer');
            })
            ->where('c.branch_id', $branchId)
            ->where('c.Status', 1) // ✅ Only active customers
            ->select([
                'c.idCustomer',
                'c.Customer_Group_idCustomer_Group',
                'c.cus_number',
                'c.Title',
                'c.First_Name',
                'c.Last_Name',
                'c.Email',
                'c.Contact_No',
                'c.contact_number_2',
                'c.Nic',
                'c.Gender',
                'c.Dob',
                'c.Customer_Risk_Level',
                'c.Address',
                'c.Address_02',
                'c.Address_03',
                'c.Per_Address_01',
                'c.Per_Address_02',
                'c.Per_Address_03',
                'c.City',
                'c.State',
                'c.Landline',
                'c.Note',
                'c.Longitude',
                'c.Latitude',
                'c.Gua_title',
                'c.Gua_name',
                'c.Guardian_gender',
                'c.Gua_relation',
                'c.Gua_occu',
                'c.Gua_contact',
                'c.Gua_address',
                'c.Gua_nic',
                'c.Cus_phto',
                'c.Status',
                'c.civil_status',
                'c.occu_job_position',
                'c.occu_monthly_salary',
                'c.occu_address_01',
                'c.occu_address_02',
                'c.occu_address_03',
                'c.occu_contact_no',
                'c.occu_longitude',
                'c.occu_latitude',
                'c.points',
                'c.route_id',
                'c.Comment',
                'c.business_registration',
                'c.branch_id',
                'r.name as route_name',
                DB::raw('IFNULL(gsub.group_names, "") as group_names'),
            ])
            ->orderBy('c.idCustomer', 'desc');

        if ($routeId) {
            $q->where('c.route_id', $routeId);
        }

        if ($status !== null && $status !== '') {
            $q->where('c.Status', $status);
        }

        if ($search !== '') {
            $like = '%' . $search . '%';
            $q->where(function ($w) use ($like) {
                $w->where('c.First_Name', 'LIKE', $like)
                    ->orWhere('c.Last_Name', 'LIKE', $like)
                    ->orWhere('c.Nic', 'LIKE', $like)
                    ->orWhere('c.cus_number', 'LIKE', $like)
                    ->orWhere('c.Contact_No', 'LIKE', $like);
            });
        }

        // ✅ Get all records (no pagination)
        $items = $q->get();

        return response()->json([
            'status' => 'success',
            'customers' => $items,
        ], 200);
    }


    /**
     * GET /api/customers/{id}
     * Full details for one customer (branch-scoped), with route & groups.
     */
    public function show(Request $request, int $id)
    {
        $branchId = (int) $request->attributes->get('branch_id');

        $groupsSub = DB::table('group_has_customer as ghc')
            ->leftJoin('customer_group as cg', 'cg.idCustomer_Group', '=', 'ghc.group_id')
            ->leftJoin('center as cen', 'cen.idCenter', '=', 'cg.center_id')
            ->select(
                'ghc.cus_id',
                DB::raw('GROUP_CONCAT(DISTINCT cg.Name ORDER BY cg.Name SEPARATOR ", ") as group_names'),
                DB::raw('GROUP_CONCAT(DISTINCT cen.Name ORDER BY cen.Name SEPARATOR ", ") as center_names'),
                DB::raw('MAX(cen.idCenter) as center_id') // Assuming one center per customer primarily, or taking one if multiple
            )
            ->groupBy('ghc.cus_id');

        $row = DB::table('customer as c')
            ->leftJoin('route as r', 'r.id_route', '=', 'c.route_id')
            ->leftJoinSub($groupsSub, 'gsub', function ($j) {
                $j->on('gsub.cus_id', '=', 'c.idCustomer');
            })
            ->where('c.branch_id', $branchId)
            ->where('c.idCustomer', $id)
            ->select([
                'c.*',
                'r.name as route_name',
                DB::raw('IFNULL(gsub.group_names, "") as group_names'),
                DB::raw('IFNULL(gsub.center_names, "") as center_names'),
                'gsub.center_id',
            ])
            ->first();

        if (!$row) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'customer' => $row,
        ], 200);
    }
}

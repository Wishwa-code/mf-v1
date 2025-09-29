<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DirectoryController
{
    // GET /api/routes
    public function listRoutes(Request $request)
    {
        $branchId = (int) $request->attributes->get('branch_id');

        $rows = DB::table('route as r')
            ->select('r.id_route','r.name','r.root_code','r.id_officer','r.branch_id')
            ->where('r.branch_id', $branchId)
            ->orderBy('r.name')
            ->get();

        return response()->json(['status' => 'success', 'routes' => $rows]);
    }

    // GET /api/centers  (optionally filter by route_id, but still same branch)
    public function listCenters(Request $request)
    {
        $branchId = (int) $request->attributes->get('branch_id');
        $routeId  = $request->query('route_id');

        // group count per center
        $groupsSub = DB::table('customer_group as cg')
            ->select('cg.center_id', DB::raw('COUNT(*) as groups_count'))
            ->where('cg.branch_id', $branchId)
            ->groupBy('cg.center_id');

        // member count per center
        $membersSub = DB::table('customer_group as cg2')
            ->join('group_has_customer as ghc', 'ghc.group_id', '=', 'cg2.idCustomer_Group')
            ->select('cg2.center_id', DB::raw('COUNT(DISTINCT ghc.cus_id) as members_count'))
            ->where('cg2.branch_id', $branchId)
            ->groupBy('cg2.center_id');

        $q = DB::table('center as c')
            ->leftJoinSub($groupsSub, 'gsub', fn($j) => $j->on('gsub.center_id','=','c.idCenter'))
            ->leftJoinSub($membersSub, 'msub', fn($j) => $j->on('msub.center_id','=','c.idCenter'))
            ->leftJoin('route as r', 'r.id_route', '=', 'c.route_id')
            ->where('c.branch_id', $branchId)
            ->when($routeId, fn($qq) => $qq->where('c.route_id', $routeId))
            ->orderBy('c.Name')
            ->select([
                'c.idCenter','c.No','c.Name','c.Contact_no','c.Address',
                'c.Center_incharge','c.Location','c.route_id','c.branch_id',
                DB::raw('COALESCE(gsub.groups_count,0)  as Groups'),
                DB::raw('COALESCE(msub.members_count,0) as Members'),
                'r.name as Route',
            ]);

        return response()->json(['status'=>'success', 'centers'=>$q->get()]);
    }

    // GET /api/groups  (optionally ?center_id=)
    public function listGroups(Request $request)
    {
        $branchId = (int) $request->attributes->get('branch_id');
        $centerId = $request->query('center_id');

        $q = DB::table('customer_group as cg')
            ->where('cg.branch_id', $branchId)
            ->when($centerId, fn($qq) => $qq->where('cg.center_id', $centerId))
            ->orderBy('cg.Group_No')
            ->select('cg.idCustomer_Group','cg.Group_No','cg.Name','cg.Leader_name','cg.Contact_no','cg.center_id','cg.branch_id');

        return response()->json(['status'=>'success','groups'=>$q->get()]);
    }

    // GET /api/centers/{center_id}/groups
    public function groupsByCenter(Request $request, $center_id)
    {
        $branchId = (int) $request->attributes->get('branch_id');

        $rows = DB::table('customer_group as cg')
            ->where('cg.center_id', $center_id)
            ->where('cg.branch_id', $branchId)
            ->orderBy('cg.Group_No')
            ->select('cg.idCustomer_Group','cg.Group_No','cg.Name','cg.Leader_name','cg.Contact_no','cg.center_id','cg.branch_id')
            ->get();

        return response()->json(['status'=>'success','center_id'=>(int)$center_id,'groups'=>$rows]);
    }
}

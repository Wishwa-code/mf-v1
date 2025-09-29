<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userData = DB::table('route')
            ->where('route.branch_id', session('branch_id'))
            ->select('route.*')
            // has_loans: 1 if any customer on this route has any loan
            ->selectRaw("
            EXISTS (
              SELECT 1
              FROM customer
              JOIN customer_loan ON customer_loan.Customer_idCustomer = customer.idCustomer
              WHERE customer.route_id = route.id_route
            ) AS has_loans
        ")
            ->get();

        $user = tableWithBranch('user')->get();

        return view('pages.Route', compact('userData','user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    private function allowedRouteDays(): array
    {
        return [
            'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday',
            'First Week Monday','First Week Tuesday','First Week Wednesday',
            'Second Week Monday','Second Week Tuesday','Second Week Wednesday',
            'Third Week Monday','Third Week Tuesday','Third Week Wednesday',
            'Fourth Week Monday','Fourth Week Tuesday','Fourth Week Wednesday',
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'route_name'      => 'required|string|max:255',
            'route_incharge'  => 'required|integer',
            'root_code'       => 'required|string|max:255',
            // accept both spellings
            'collection_type' => 'required|in:customizable,customable,fixed',
            'collection_date' => ['nullable','required_if:collection_type,fixed', Rule::in($this->allowedRouteDays())],
        ]);

        // normalize type
        $type = $validated['collection_type'] === 'customable' ? 'customizable' : $validated['collection_type'];
        // force Monday when not fixed
        $collectionDate = $type === 'fixed' ? ($validated['collection_date'] ?? 'Monday') : 'Monday';

        DB::table('route')->insert([
            'name'            => $validated['route_name'],
            'root_code'       => $validated['root_code'],
            'id_officer'      => $validated['route_incharge'],
            'collection_type' => $type,
            'collection_date' => $collectionDate,
            'branch_id'       => session('branch_id'),
        ]);

        return response()->json(['message' => 'Route added successfully!'], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'center_id'       => 'required|integer',
            'route'           => 'required|string|max:255',
            'route_code'      => 'required|string|max:255',
            // accept both spellings
            'collection_type' => 'required|in:customizable,customable,fixed',
            'collection_date' => ['nullable','required_if:collection_type,fixed', Rule::in($this->allowedRouteDays())],
        ]);

        $type = $validated['collection_type'] === 'customable' ? 'customizable' : $validated['collection_type'];
        $collectionDate = $type === 'fixed' ? ($validated['collection_date'] ?? 'Monday') : 'Monday';

        DB::table('route')
            ->where('id_route', $validated['center_id'])
            ->where('branch_id', session('branch_id'))
            ->update([
                'name'            => $validated['route'],
                'root_code'       => $validated['route_code'],
                'collection_type' => $type,
                'collection_date' => $collectionDate,
            ]);

        return response()->json(['message' => 'Route updated successfully!'], 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $check=DB::table('center')->where('branch_id', session('branch_id'))->where('route_id', $id)->first();
        if($check){
            return response()->json(['item' => 0], 200);
        }
        DB::table('route')->where('branch_id', session('branch_id'))->where('id_route', $id)->delete();
        return response()->json(['item' => 1], 200);
    }
}

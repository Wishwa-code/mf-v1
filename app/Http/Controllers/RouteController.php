<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RouteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userData = tableWithBranch('route','route')
            ->get();
        $user=tableWithBranch('user')->get();

        return view('pages.Route', compact('userData','user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation can be added if required
        $validated = $request->validate([
            'route_name' => 'required',
            'route_incharge' => 'required',
            'root_code' => 'required',
        ]);

        // Insert new route into the database
        DB::table('route')->insert([
            'name' => $validated['route_name'],
            'root_code' => $validated['root_code'],
            'id_officer' => $validated['route_incharge'],
            'branch_id' => session('branch_id')
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
        DB::table('route')
            ->where('id_route', $request->center_id)
            ->where('branch_id', session('branch_id'))
            ->update([
                'name' => $request->route,
                'root_code' => $request->route_code,
            ]);

        return redirect()->route('routes.index')->with('success', 'Route updated successfully!');
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

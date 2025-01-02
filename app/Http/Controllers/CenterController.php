<?php

namespace App\Http\Controllers;

use App\Models\Center;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CenterController extends Controller
{


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userData = tableWithBranch('center','center')
            ->join('route', 'center.route_id', '=', 'route.id_route')
            ->get();
        $route = tableWithBranch('route')->get();

        return view('pages.Center',compact('userData','route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(string $id)
    {
        $customer = tableWithBranch('customer')->where('Customer_Group_idCustomer_Group', '=', $id)->get();
        $center = tableWithBranch('center')->where('idCenter', '=', $id)->first();
        $customercount = tableWithBranch('customer')->where('Customer_Group_idCustomer_Group', '=', $id)->count();

        return view('pages.ViewCenter', compact('customer','center','customercount'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request data
        $request->validate([
            'center_name' => 'required|string|max:255',
            'contact' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'center_incharge' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'center_number' => 'required|string|max:255',
            'route_id' => 'required',
        ]);

        // Use tableWithBranch to check if the center already exists
        $check = tableWithBranch('center')->where('No', '=', $request->center_number)->first();

        if ($check) {
            return response()->json(['message' => 'Center already exists.', 'id' => '0'], 200);
        } else {
            // Prepare the data for insertion
            $centerData = [
                'No' => $request->center_number,
                'Name' => $request->center_name,
                'Contact_no' => $request->contact,
                'Address' => $request->address,
                'Route' => $request->route,
                'Center_incharge' => $request->center_incharge,
                'Location' => $request->location,
                'Groups' => "0",
                'Members' => "0",
                'route_id' => $request->route_id,
            ];

            // Use the helper function to insert the new center with the branch ID
            $insertedId = insertWithBranch('center', $centerData);

            if ($insertedId) {
                return response()->json(['message' => 'Data saved successfully.', 'id' => '1'], 200);
            } else {
                return response()->json(['message' => 'Failed to save data.', 'id' => '0'], 500);
            }
        }
    }


    /**
     * Display the specified resource.
     */
    public function show()
    {
        $route = tableWithBranch('route')->get();
        return view('pages.CreateCenter',compact('route'));
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
        // Update the center record using the helper function
        $updated = updateWithBranch('center', 'idCenter', $request->center_id, [
            'No' => $request->center_number,
            'Name' => $request->center_name,
            'Contact_no' => $request->contact,
            'Address' => $request->address,
            'Route' => $request->route,
            'Center_incharge' => $request->center_incharge,
            'Location' => $request->location,
            'route_id' => $request->route_id,
        ]);

        if ($updated) {
            return response()->json(['message' => 'Data updated successfully.'], 200);
        } else {
            return response()->json(['message' => 'Failed to update data.'], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Delete the center record using the helper function
        $deleted = deleteWithBranch('center', 'idCenter', $id);

        if ($deleted) {
            return response()->json(['message' => 'Data deleted successfully.'], 200);
        } else {
            return response()->json(['message' => 'Failed to delete data.'], 500);
        }

    }
}

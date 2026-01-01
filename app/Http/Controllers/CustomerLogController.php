<?php

namespace App\Http\Controllers;

use App\Models\CustomerLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $user_id = session('user_data')["idUser"];
        $date = date('Y-m-d');
        $time = date('H:i:s');

        $customer_table = tableWithBranch('customer')
            ->where('idCustomer', '=', $request->customer_id)
            ->first();

        // Prepare the customer log data for insertion
        $logData = [
            'customer_id' => $request->customer_id, // Customer ID
            'customer_name' => $customer_table->First_Name . ' ' . $customer_table->Last_Name, // Full customer name
            'date' => $date, // Log date
            'time' => $time, // Log time
            'description' => $request->description, // Log description
            'description_id' => $request->description_id, // Description ID
            'comment' => $request->comment, // Additional comment
            'type' => $request->type, // Log type
            'user' => $user_id, // User ID
        ];

        // Use the insertWithBranch helper function to insert the log data
        $customerLog = insertWithBranch('customer_log', $logData);


        return response()->json(['message' => 'Customer log created successfully', 'data' => $customerLog], 200);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

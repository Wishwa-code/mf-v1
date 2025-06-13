<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GuardianController extends Controller
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
    public function create(Request $request)
    {

        // Validation rules
        $validatedData = $request->validate([
            'customer' => 'required',
            'description' => 'required',
        ]);

// Retrieve data from the request
        $customer = $validatedData['customer'];
        $description = $validatedData['description'];
        $file = $request->file('file');

// Define the directory where the file will be stored
        $directory = 'guardian_documents';

// Check if the directory exists on the public disk, create it if not
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

// Store the file on the public disk
        $documentPath = Storage::disk('public')->putFile($directory, $file);

// $documentPath now contains the path to the stored file relative to the 'public' disk

        insertWithBranch('guardian_has_documents', [
            'Guardian_idGuardian' => $customer,
            'Description' => $description,
            'Path' => $documentPath,
        ]);


        return response()->json(['message' => 'Document saved successfully'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request
        $validatedData = $request->validate([
            'f_name' => 'required',
            'last_name' => 'required',
            'contact_number' => 'required',
            'nic' => 'required',
        ]);

        // Instantiate a new Customer object
        $guardian = new Guardian();
        $guardian->Title = $request->title;
        $guardian->First_Name = $request->f_name;
        $guardian->Last_Name = $request->last_name;
        $guardian->Email = $request->email;
        $guardian->Contact_No = $request->contact_number;
        $guardian->Nic = $request->nic;
        $guardian->Gender = $request->gender;
        $guardian->Dob = $request->dob;
        $guardian->Address = $request->address;
        $guardian->Address_02 = $request->address_2;
        $guardian->Address_03 = $request->address_3;
        $guardian->City = $request->city;
        $guardian->State = $request->state;
        $guardian->Landline = $request->landline;
        $guardian->Note = $request->note;
        $guardian->Longitude = $request->longitude;
        $guardian->Latitude = $request->latitude;
        $guardian->branch_id = session('branch_id');

        // Handle file upload
        if ($request->hasFile('cus_phto')) {
            $file = $request->file('cus_phto');
            $directory = 'guardian_documents';

            // Check if the directory exists on the public disk, create it if not
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Store the file on the public disk
            $documentPath = Storage::disk('public')->putFile($directory, $file);
            $guardian->Cus_phto = $documentPath;
        }

        // Save the customer data
        $guardian->save();

        // Retrieve the ID of the newly saved customer
        $id = $guardian->id; // Assuming 'idCustomer' is the primary key column name


        if ($guardian->save()) {
            // If the data is saved successfully, return a success response
            return response()->json(['message' => 'Data saved successfully', 'id' => $id], 200);
        } else {
            // If the data failed to save, return an error response
            return response()->json(['message' => 'Failed to save data'], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer_doc = tableWithBranch('guardian_has_documents')->where('Guardian_idGuardian', '=', $id)->get();
        return response()->json(['item' => $customer_doc], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        $guardian = tableWithBranch('guardian')
            ->get();
        return view('pages.ViewGuardian', compact('guardian'));
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
        tableWithBranch('guardian_has_documents')->where('idGuardian_Documents', '=', $id)->delete();
        return response()->json(['message' => 'Data deleted successfully'], 200);
    }

    public function updateCustomer(Request $request)
    {
        updateWithBranch('guardian', 'idGuardian', $request->id, [
            'title' => $request->title,
            'First_Name' => $request->f_name,
            'Last_Name' => $request->last_name,
            'Email' => $request->email,
            'Contact_No' => $request->contact_number,
            'Nic' => $request->nic,
            'Gender' => $request->gender,
            'Dob' => $request->dob,
            'Address' => $request->address,
            'City' => $request->city,
            'State' => $request->state,
            'Landline' => $request->landline,
            'Note' => $request->note,
            'Longitude' => $request->longitude,
            'Latitude' => $request->latitude,
        ]);


        // Optionally, return a response
        return response()->json(['message' => 'Guarantee updated successfully'], 200);
    }


    public function load()
    {
        return view('pages.Guardian');
    }

    public function saveFiles(Request $request)
    {
        // Define the directory where the file will be stored
        $directory = 'guardian_documents';

// Check if the directory exists, create it if not
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {

                // Store the file
                $storedFile = Storage::disk('public')->putFile($directory, $file);

                // Get corresponding name
                $documentName = $request->documentNames[$index] ?? 'Unnamed Document';

                // Build the data array
                $documentData = [
                    'Description' => $documentName,
                    'Path' => $storedFile,
                    'Guardian_idGuardian' => $request->id,
                ];

                // Insert into the DB
                insertWithBranch('guardian_has_documents', $documentData);
            }
        }



        return response()->json(['success' => true]);

    }

    public function change_status(string $id)
    {
        // Retrieve the current status of the customer
        $customer = tableWithBranch('guardian')->where('idGuardian', $id)->first();

        // Check if the customer exists
        if ($customer) {
            // Toggle the status
            $newStatus = ($customer->Status == 1) ? 0 : 1;

            // Update the status in the database
            $data = ['Status' => $newStatus];

// Update the guardian's status with branch-specific logic
            updateWithBranch('guardian', 'idGuardian', $id, $data);


            // Optionally, return a response
            return response()->json(['message' => 'Status updated successfully', 'newStatus' => $newStatus], 200);
        } else {
            // Return an error response if the customer is not found
            return response()->json(['message' => 'Guarantee not found'], 404);
        }
    }

    public function load_customer(string $id,string $cus)
    {

        if ($id === "1") {
            $guardian = tableWithBranch('guardian')
                ->select('guardian.idGuardian as idCustomer', 'guardian.First_Name as First_Name', 'guardian.Last_Name as Last_Name', 'guardian.Nic as Nic', 'guardian.Email as Email', 'guardian.Contact_No as Contact_No')
                ->get();
            return response()->json(['message' => 'Status updated successfully', 'customer' => $guardian], 200);
        } else {
            $cus_group = tableWithBranch('group_has_customer', 'group_has_customer')
                ->join('customer', 'group_has_customer.cus_id', 'customer.idCustomer')
                ->join('customer_group', 'group_has_customer.group_id', 'customer_group.idCustomer_Group')
                ->where('customer.idCustomer', $cus)
                ->first();

            if (!$cus_group) {
                $customers = tableWithBranch('customer', 'customer')
                    ->select('customer.idCustomer as idCustomer',
                        'customer.First_Name as First_Name',
                        'customer.Last_Name as Last_Name',
                        'customer.Nic as Nic',
                        'customer.Email as Email',
                        'customer.Contact_No as Contact_No')
                    ->get();
            } else {
                $customer_group_id = $cus_group->group_id;
                $customers = tableWithBranch('customer', 'customer')
                    ->join('group_has_customer', 'group_has_customer.cus_id', 'customer.idCustomer')
                    ->join('customer_group', 'group_has_customer.group_id', 'customer_group.idCustomer_Group')
                    ->where('customer_group.idCustomer_Group', $customer_group_id)
                    ->select('customer.idCustomer as idCustomer',
                        'customer.First_Name as First_Name',
                        'customer.Last_Name as Last_Name',
                        'customer.Nic as Nic',
                        'customer.Email as Email',
                        'customer.Contact_No as Contact_No')
                    ->get();
            }

// Remove the customer with id equal to $cus
            $customers = $customers->reject(function ($item) use ($cus) {
                return $item->idCustomer == $cus; // Exclude the customer with this ID
            });

// Convert to array and ensure it's indexed numerically
            $customersArray = $customers->values()->toArray(); // This ensures it is indexed correctly

// Return the data as a JSON response in the desired format
            return response()->json([
                'message' => 'Status updated successfully',
                'customer' => $customersArray // Return the array
            ], 200);


        }
    }

    public function load_customer_details(string $id, string $type)
    {
        if ($type === "1") {
            $guardian = tableWithBranch('guardian')
                ->where('idGuardian', $id)
                ->select('First_Name','Last_Name','Nic','Address as Address_01','Address_02 as Address_02','Address_03 as Address_03')
                ->get();
            return response()->json(['customer' => $guardian], 200);
        } else {
            $customer = tableWithBranch('customer','customer')
                ->leftJoin('group_has_customer','group_has_customer.cus_id','customer.idCustomer')
                ->select('First_Name','Last_Name','Nic','Address as Address_01','Address_02 as Address_02','Address_03 as Address_03')
                ->where('idCustomer', $id)
                ->get();
            return response()->json(['customer' => $customer], 200);
        }
    }

    public function delete_guardian(string $id){

        $cus_group = tableWithBranch('witness')
            ->where('witness.type', 'Guarantor')
            ->where('witness.cus_id', $id)
            ->first();

        if (!$cus_group) {
            DB::table('guardian')->where('branch_id', session('branch_id'))->where('idGuardian', '=', $id)->delete();
            return response()->json(['message' => 'Data deleted successfully'], 200);
        }else{
            return response()->json(['message' => 0], 201);
        }


    }

}

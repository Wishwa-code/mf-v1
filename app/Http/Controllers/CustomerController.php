<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $customers = Customer::where('branch_id', session('branch_id'))->orderBy('id', 'desc')->get();
            return view('pages.Customers.index', compact('customers')); // Assuming there will be an index view, currently using 'viewcustomers' in sidebar but user asked for CRUD for 'Customer.blade.php' which is CREATE.
            // Wait, the user said "create crud fothe ... Customer.blade.php". 
            // Customer.blade.php seems to be the CREATE/EDIT form.
            // The list view might be 'pages.ViewCustomer' from the copy controller.
            // For now, I will redirect to the create page if index is hit, OR better, check sidebar.
            // Sidebar -> /showcustomers -> ViewCustomer.
            // Sidebar -> /customers -> Add Customer (create).
            // So index() might not be used or should return the ViewCustomer page if I'm replacing the old controller.
            // But I'm creating 'CustomerController' resource.
            // Let's make index return the list view if it exists, or json for now.
            return view('pages.ViewCustomer', compact('customers'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading customers: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            // Needed data for the view
            $route = Route::where('branch_id', session('branch_id'))->get();
            $settings = \App\Models\AppSettings::where('branch_id', session('branch_id'))->pluck('value', 'key');

            return view('pages.Customers.Customer', compact('route', 'settings'));
        } catch (\Exception $e) {
            dd($e);
            // dd($e); // Debugging removed
            return back()->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        try {
            $data = [
                'branch_id' => session('branch_id'),
                'customer_code' => $request->cus_number,
                'title' => $request->title,
                'first_name' => $request->f_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_no' => $request->contact_number,
                'new_nic' => $request->nic,
                'route_id' => $request->root,
                'created_by' => auth()->id(),
            ];

            // Handle acc_center_cus_id requirement
            if ($request->has('acc_center_cus_id')) {
                $data['acc_center_cus_id'] = $request->acc_center_cus_id;
            } else {
                $data['acc_center_cus_id'] = 1; // Default fallback
            }

            $customer = Customer::create($data);

            return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create customer: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        return view('pages.Customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        try {
            $route = \DB::table('route')->where('branch_id', session('branch_id'))->get();
            // Fetch settings for the customer's branch if possible, else session branch
            $branchId = $customer->branch_id ?? session('branch_id');
            $settings = \App\Models\AppSettings::where('branch_id', $branchId)->pluck('value', 'key');

            return view('pages.Customers.Customer', compact('customer', 'route', 'settings'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        try {
            $data = [
                'customer_code' => $request->cus_number,
                'title' => $request->title,
                'first_name' => $request->f_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_no' => $request->contact_number,
                'new_nic' => $request->nic,
                'route_id' => $request->root,
                'updated_by' => auth()->id(),
            ];

            $customer->update($data);

            return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to update customer: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete(); // Soft delete as per model trait
            return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete customer: ' . $e->getMessage());
        }
    }

    /**
     * Preview Customer Number
     */
    public function previewCustomerNumber(Request $request)
    {
        $company = \DB::table('company')->first();
        $customer_max = Customer::max('id');
        $customer_max = $customer_max + 1;
        $formatted_customer_id = str_pad($customer_max, 3, '0', STR_PAD_LEFT);
        $format = $company?->customer_format ?? '';
        $newnum = str_replace(
            ['@Center_No@', '@Group_No@', '@Customize_No@', '@Auto_ID@', '@Branch_No@', '@Root@', '@Center_Cus_Count@'],
            ['C000', 'G000', $request->input('custom_val', 'Customize No'), $formatted_customer_id, '@Branch_No@', '@Root@', 'CenterCustomerCount'],
            $format
        );
        return response()->json(['formatted_number' => $newnum]);
    }

    public function saveFiles(Request $request)
    {
        if (!$request->hasFile('documents')) {
            return response()->json(['success' => false, 'message' => 'No files were received.']);
        }
        $directory = 'customer_documents';
        if (!\Storage::disk('public')->exists($directory)) {
            \Storage::disk('public')->makeDirectory($directory);
        }
        foreach ($request->file('documents') as $index => $file) {
            $storedFile = \Storage::disk('public')->putFile($directory, $file);
            $documentName = $request->documentNames[$index];
            $documentData = [
                'Description' => $documentName,
                'Path' => $storedFile,
                'Customer_idCustomer' => $request->id,
            ];
            if (function_exists('insertWithBranch')) {
                insertWithBranch('customer_documents', $documentData);
            } else {
                $documentData['branch_id'] = session('branch_id');
                \DB::table('customer_documents')->insert($documentData);
            }
        }
        return response()->json(['success' => true]);
    }

    public function saveBank(Request $request)
    {
        $id = $request->input('id');
        $tableBankNames = $request->input('tableBankNames');
        $tableAccountNames = $request->input('tableAccountNames');
        $tableAccountNumbers = $request->input('tableAccountNumbers');
        $tableBranches = $request->input('tableBranches');
        $tableBankCodes = $request->input('tableBankCodes');

        if (!empty($tableBankNames)) {
            foreach ($tableBankNames as $index => $tableBankName) {
                $tableAccountName = $tableAccountNames[$index] ?? '';
                $tableAccountNumber = $tableAccountNumbers[$index] ?? '';
                $tableBranch = $tableBranches[$index] ?? '';
                $tableBankCode = $tableBankCodes[$index] ?? null;

                $documentData = [
                    'cus_id' => $id,
                    'bank_name' => $tableBankName,
                    'account_name' => $tableAccountName,
                    'account_number' => $tableAccountNumber,
                    'branch' => $tableBranch,
                    'bank_code' => $tableBankCode,
                ];

                if (function_exists('insertWithBranch')) {
                    insertWithBranch('customer_has_bank', $documentData);
                } else {
                    $documentData['branch_id'] = session('branch_id');
                    \DB::table('customer_has_bank')->insert($documentData);
                }
            }
        }
        return response()->json(['message' => 'Bank details saved successfully']);
    }

    public function load_bank(string $id)
    {
        if (function_exists('tableWithBranch')) {
            $customer_acc = tableWithBranch('customer_has_bank')->where('cus_id', '=', $id)->get();
        } else {
            $customer_acc = \DB::table('customer_has_bank')->where('branch_id', session('branch_id'))->where('cus_id', '=', $id)->get();
        }
        return response()->json(['message' => 'Bank details loaded successfully', 'item' => $customer_acc], 200);
    }

    public function remove_bank(string $id)
    {
        if (function_exists('tableWithBranch')) {
            tableWithBranch('customer_has_bank')->where('id', '=', $id)->delete();
        } else {
            \DB::table('customer_has_bank')->where('branch_id', session('branch_id'))->where('id', '=', $id)->delete();
        }
        return response()->json(['message' => 'Bank details deleted successfully'], 200);
    }

    public function deletecus(string $id)
    {
        try {
            if (function_exists('deleteWithBranch')) {
                deleteWithBranch('customers', 'id', $id);
            } else {
                Customer::where('id', $id)->delete();
            }
            return response()->json(['message' => 'Data deleted successfully', 'item' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error deleting: ' . $e->getMessage(), 'item' => 'error'], 200);
        }
    }
}

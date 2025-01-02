<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company= DB::table('company')->first();
        return view('pages.Company',compact('company'));
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

        // Fetch the existing company record
        $company = DB::table('company')->first();

// Initialize an array to store the fields to be updated
        $updateData = [
            'company_name' => isset($request->company_name) ? $request->company_name : '',
            'branch' => isset($request->branch) ? $request->branch : '',
            'address' => isset($request->address) ? $request->address : '',
            'contact_no' => isset($request->con) ? $request->con : '',
            'customer_num_type' => isset($request->customer_format_selection) ? $request->customer_format_selection : '',
            'customer_seperate_from' => isset($request->separate_from) ? $request->separate_from : '',
            'customer_num_start_from' => isset($request->auto_number) ? $request->auto_number : '',
            'customer_format' => isset($request->field_output_customer) ? $request->field_output_customer : '',
            'loan_num_type' => isset($request->loan_format_selection) ? $request->loan_format_selection : '',
            'loan_seperate_from' => isset($request->separate_from_loan) ? $request->separate_from_loan : '',
            'loan_format' => isset($request->field_output_loan) ? $request->field_output_loan : '',
            'points' => isset($request->activatePoints) ? $request->activatePoints : '',
            'points_percentage' => isset($request->pointsPercentage) ? $request->pointsPercentage : '',
            'account_saving_type' => isset($request->saving_selection) ? $request->saving_selection : '',
            'saving_seperate_from' => isset($request->separate_from_savings) ? $request->separate_from_savings : '',
            'saving_format' => isset($request->field_output_saving) ? $request->field_output_saving : '',

            'inv_loan_num_type' => isset($request->inv_loan_format_selection) ? $request->inv_loan_format_selection : '',
            'inv_loan_seperate_from' => isset($request->separate_from_inv_loan) ? $request->separate_from_inv_loan : '',
            'inv_loan_format' => isset($request->field_output_inv_loan) ? $request->field_output_inv_loan : '',
        ];



// Handle file uploads and update file paths if new files are provided
        foreach (['logo', 'company_header', 'company_footer'] as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $file = $request->file($fileKey);
                $directory = 'Company';

                // Check if the directory exists on the public disk, create it if not
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                // Store the file on the public disk and update the path in the array
                $updateData[$fileKey] = Storage::disk('public')->putFile($directory, $file);
            } else {
                // If no new file is provided, retain the existing value
                $updateData[$fileKey] = $company->$fileKey;
            }
        }

// Update the database with the constructed $updateData array
        DB::table('company')->update($updateData);

        return response()->json(['message' => 'Data saved successfully', 'id' => '1'], 200);


    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $userData = tableWithBranch('shortcut')->get();
        return response()->json(['items' => $userData], 200);
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

    public function setting(){
        return view('pages.Settings');
    }

    public function shortcuts(Request $request)
    {
        $checkboxValues = $request->input('checkboxValues', []);
        $user_id = session('userid');

        DB::table('shortcut')
            ->where('branch_id', session('branch_id'))
            ->delete();

        foreach ($checkboxValues as $key => $value) {
            $data = ['name' => $key, 'user_id' => $user_id];
            // Call the helper function
            insertWithBranch('shortcut', $data);
        }

        return response()->json(['message' => 'Shortcuts updated successfully']);
    }


}

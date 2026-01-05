<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = tableWithBranch('company')->first();
        return view('pages.Company', compact('company'));
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


        if (!Schema::hasColumn('company', 'customer_format_scope')) {
            DB::statement(
                "ALTER TABLE `company`
         ADD COLUMN `customer_format_scope` VARCHAR(45) NOT NULL
         DEFAULT '0'"
            );
        }

        // Initialize an array to store the fields to be updated
        $updateData = [
            'company_name' => isset($request->company_name) ? $request->company_name : '',
            'branch' => isset($request->branch) ? $request->branch : '',
            'address' => isset($request->address) ? $request->address : '',
            'contact_no' => isset($request->con) ? $request->con : '',
            'points' => isset($request->activatePoints) ? $request->activatePoints : '',
            'points_percentage' => isset($request->pointsPercentage) ? $request->pointsPercentage : '',
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
        DB::table('company')->where('branch_id', '=', session('branch_id'))->update($updateData);


        if ($request->customer_format_scope == "all") {
            $cus = tableWithBranch('customer')->get();
            foreach ($cus as $item) {
                customer_number($item->idCustomer);
            }
        }



        return response()->json(['message' => 'Data saved successfully', 'id' => '1'], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $userData = tableWithBranch('shortcut')->where('user_id', session('user_data')['idUser'])->get();
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

    public function setting()
    {
        return view('pages.Settings');
    }

    public function shortcuts(Request $request)
    {
        $checkboxValues = $request->input('checkboxValues', []);
        $user_id = session('user_data')["idUser"];

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

    public function updateNumberFormats(Request $request)
    {
        $updateData = [
            'customer_num_type' => $request->customer_format_selection ?? '',
            'customer_seperate_from' => $request->separate_from ?? '',
            'customer_num_start_from' => $request->auto_number ?? '',
            'customer_format' => $request->field_output_customer ?? '',
            'customer_format_scope' => $request->customer_format_scope ?? '0',

            'loan_num_type' => $request->loan_format_selection ?? '',
            'loan_seperate_from' => $request->separate_from_loan ?? '',
            'loan_format' => $request->field_output_loan ?? '',

            'inv_loan_num_type' => $request->inv_loan_format_selection ?? '',
            'inv_loan_seperate_from' => $request->separate_from_inv_loan ?? '',
            'inv_loan_format' => $request->field_output_inv_loan ?? '',

            'account_saving_type' => $request->saving_selection ?? '',
            'saving_seperate_from' => $request->separate_from_savings ?? '',
            'saving_format' => $request->field_output_saving ?? '',
        ];

        DB::table('company')->where('branch_id', '=', session('branch_id'))->update($updateData);

        if ($request->customer_format_scope == "all") {
            $cus = tableWithBranch('customer')->get();
            foreach ($cus as $item) {
                customer_number($item->idCustomer);
            }
        }

        return response()->json(['message' => 'Number formats updated successfully']);
    }


    public function getBranchesProxy(Request $request)
    {
        try {

            $serverUrl = rtrim(env('ACCOUNT_CENTER_SERVER_URL', 'https://accountcenterserver.asipbook.com'), '/');

            $token = $request->cookie('access_token') ?? $request->bearerToken();
            if ($token === null) {
                $loginUrl = rtrim(env('ACCOUNT_CENTER_URL', 'https://accountcenter.asipbook.com'), '/');
                return redirect($loginUrl);
            }

            $response = Http::timeout(60)->withHeaders([
                'Authorization' => "Bearer $token"
            ])->post("$serverUrl/api/auth/micro-finance-auth-verify");

            return response()->json(
                $response->json(),
                $response->status()
            );
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Proxy error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

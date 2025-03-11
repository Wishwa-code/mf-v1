<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssetManagementController extends Controller
{

    protected $bankLogController;

    public function __construct(BankLogController $bankLogController)
    {
        $this->bankLogController = $bankLogController;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $asset_management = tableWithBranch('asset_type')->get();
        $bank = tableWithBranch('company_bank_accounts')->get();

        return view('pages.Accounting.AddAssetManagment',compact('asset_management','bank'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $asset_management = tableWithBranch('asset_type')->get();
        $bank = tableWithBranch('company_bank_accounts')->get();

        $asset = tableWithBranch('asset_management','asset_management')
            ->join('asset_type', 'asset_management.type', '=', 'asset_type.id')
            ->join('company_bank_accounts', 'asset_management.sold_bank_account', '=', 'company_bank_accounts.Idbank')
            ->get();

//        dd($asset);
        return view('pages.Accounting.AssetManagement',compact('asset_management','bank','asset'));
    }

    public function search(Request $request)
    {
        $asset_management = tableWithBranch('asset_type')->get();
        $bank = tableWithBranch('company_bank_accounts')->get();

        $query = tableWithBranch('asset_management','asset_management')
            ->join('asset_type', 'asset_management.type', '=', 'asset_type.id')
            ->leftJoin('company_bank_accounts', 'asset_management.sold_bank_account', '=', 'company_bank_accounts.Idbank');

// Apply filters if set
        if ($request->filterOption !== '0') {
            $query->where('asset_management.type', $request->filterOption);
        }

        if ($request->bankAccounts_purchase !== '0') {
            $query->where('asset_management.bank_id', $request->bankAccounts_purchase);
        }

        if ($request->bankAccounts_sold !== '0') {
            $query->where('asset_management.sold_bank_account', $request->bankAccounts_sold);
        }

        $asset = $query->get();


        return view('pages.Accounting.AssetManagement', compact('asset_management', 'bank', 'asset'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the form data
        $validated = $request->validate([
            'type' => 'required',
            'date_option' => 'required',
            'purchase_date_value' => 'required|date',
            'purchase_value' => 'required|numeric',
            'source_funds' => 'required',
            'replacement_value' => 'nullable|string',
            'serial_number' => 'nullable|string',
            'bought_from' => 'nullable|string',
            'description' => 'nullable|string',
            'upload' => 'nullable|file'  // Allow null file upload
        ]);

        // Define the directory name correctly
        $directory = 'Assets';

        // Ensure the file is uploaded before processing
        $documentPath = null;
        if ($request->hasFile('upload')) {
            $file = $request->file('upload');

            // Check if the directory exists, create it if not
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Store the file and get its path
            $documentPath = Storage::disk('public')->putFile($directory, $file);
        }

        // Insert the data into the database
        insertWithBranch('asset_management', [
            'type' => $validated['type'],
            'purchase_open' => $validated['date_option'],
            'purchase_date' => $validated['purchase_date_value'],
            'purchase_value' => number_format($validated['purchase_value'], 2, '.', ''),
            'bank_id' => $validated['source_funds'],
            'description' => $validated['replacement_value'],
            'serial_number' => $validated['serial_number'],
            'bought_from' => $validated['bought_from'],
            'long_description' => $validated['description'],
            'img' => $documentPath,  // Save the file path or null if no file was uploaded
            'current_value' => number_format($validated['purchase_value'], 2, '.', ''),
        ]);

        // Return a success message
        return response()->json(['success' => 'Asset added successfully']);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        // Insert the data into the database
        insertWithBranch('asset_type', [
            'name' => $request->newType,
            'category' => $request->category,
        ]);


        return response()->json(['success' => 'Asset Type added successfully']);
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

        $check = tableWithBranch('asset_management')->where('type', $id)->first();

        if ($check) {
            return response()->json(['success' => false, 'message' => 'Asset Type is in use']);
        }

        $deleted = deleteWithBranch('asset_type', 'id', $id);

        if ($deleted) {
            return response()->json(['success' => true, 'message' => 'Asset Type deleted successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Asset Type not found or not in your branch']);
        }

    }


    public function updateAsset(Request $request)
    {

        // Perform the update using DB::table()
        $updated = updateWithBranch('asset_management', 'id_assest', $request->assetId, [
            'sold_status' => 1,
            'sold_date' => $request->soldDate,
            'sold_amount' => number_format($request->soldValue, 2, '.', ''),
            'sold_bank_account' => $request->sold_bank,
            'note' => $request->notes,
        ]);


        if ($updated) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => $request->assetId], 500);
        }
    }


    public function book_value(Request $request){
        // Insert into asset_book
        insertWithBranch('asset_book', [
            'date' => $request->book_date,
            'value' => number_format($request->book_value, 2, '.', ''),
            'asset_id' => $request->assetId_2
        ]);

// Update asset_management
        $updated = updateWithBranch('asset_management', 'id_assest', $request->assetId_2, [
            'current_value' => number_format($request->book_value, 2, '.', '')
        ]);


        if ($updated) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => $request->assetId_2], 500);
        }

    }


}

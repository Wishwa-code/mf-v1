<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return view('pages.ViewProduct', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.CreateProduct');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();

            // Map frontend fields to database columns
            $productData = array_merge($validated, [
                'interest_apply_type' => $validated['interest_period'],
                'minimum_loan_period' => $validated['default_loan_period'],
                'maximum_collection_period' => $validated['loan_duration'],
                'collection_period_type' => $validated['loan_duration_type'],
                'penalty_apply_type' => $validated['penalty_period'],
                'penalty_start_after_days' => $validated['penalty_start_after'],
                'saving_payment' => $validated['saving_payment_type'] ?? null,
                'status' => 'Active',
            ]);

            $product = Product::create($productData);

            // Save Additional Charges
            if ($request->has('charges') && is_array($request->charges)) {
                foreach ($request->charges as $charge) {
                    if (isset($charge['description'], $charge['value_type'], $charge['value'])) {
                        $product->additional_charges()->create([
                            'description' => $charge['description'],
                            'value_type' => $charge['value_type'],
                            'value' => $charge['value'],
                            'deduction_type' => $charge['deduction_type'] ?? 'On Loan Disbursement',
                        ]);
                    }
                }
            }

            // Save Required Documents
            if ($request->has('documents') && is_array($request->documents)) {
                foreach ($request->documents as $doc) {
                    if (isset($doc['name'])) {
                        $product->required_documents()->create([
                            'name' => $doc['name'],
                            'required_status' => isset($doc['required']) ? 1 : 0,
                        ]);
                    }
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Product Created Successfully',
                    'redirect_url' => route('product.create')
                ]);
            }

            return redirect()->route('product.create')->with('success', 'Product Created Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error Creating Product: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error Creating Product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $product = Product::with(['additional_charges', 'required_documents'])->find($id);
        $designation = DB::table('designation')->get();
        return view('pages.CreateProduct', compact('product', 'designation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);
            $product->product_name = $request->product_name;
            $product->product_code = $request->product_code;
            $product->interest_method = $request->interest_method;
            $product->minimum_loan_amount = $request->minimum_loan_amount;
            $product->maximum_loan_amount = $request->maximum_loan_amount;
            $product->minimum_interest = $request->minimum_interest;
            $product->maximum_interest = $request->maximum_interest;
            $product->interest_apply_type = $request->interest_period;
            $product->minimum_loan_period = $request->default_loan_period;
            $product->loan_period_type = $request->loan_period_type;
            $product->guarantee_count = $request->guarantee_count;
            $product->maximum_collection_period = $request->loan_duration;
            $product->collection_period_type = $request->loan_duration_type;
            $product->repayment_type = $request->repayment_type;
            $product->collection_date_type = $request->collection_date_type;
            $product->penalty_method = $request->penalty_method;
            $product->penalty_percentage = $request->penalty_percentage;
            $product->penalty_apply_type = $request->penalty_period;
            $product->penalty_start_after_days = $request->penalty_start_after;

            $product->enable_saving = $request->enable_saving;
            $product->saving_amount_type = $request->saving_amount_type;
            $product->saving_amount = $request->saving_amount;
            $product->saving_payment = $request->saving_payment_type;

            $product->save();

            // Additional Charges: Delete and Re-create
            $product->additional_charges()->delete();
            if ($request->has('charges') && is_array($request->charges)) {
                foreach ($request->charges as $charge) {
                    if (isset($charge['description'], $charge['value_type'], $charge['value'])) {
                        $product->additional_charges()->create([
                            'description' => $charge['description'],
                            'value_type' => $charge['value_type'],
                            'value' => $charge['value'],
                            'deduction_type' => $charge['deduction_type'] ?? 'On Loan Disbursement',
                        ]);
                    }
                }
            }

            // Required Documents: Delete and Re-create
            // Required Documents: Delete and Re-create
            $product->required_documents()->delete();
            if ($request->has('documents') && is_array($request->documents)) {
                foreach ($request->documents as $doc) {
                    if (isset($doc['name'])) {
                        $product->required_documents()->create([
                            'name' => $doc['name'],
                            'required_status' => isset($doc['required']) ? 1 : 0,
                        ]);
                    }
                }
            }

            // Approval Levels: Delete and Re-create
            // Find existing levels for this product
            $existingLevels = DB::table('level')->where('product_id', $id)->pluck('id');

            // Delete child data first
            if ($existingLevels->isNotEmpty()) {
                DB::table('level_has_designation')->whereIn('level_id', $existingLevels)->delete();
                DB::table('approval_checklist')->whereIn('level_id', $existingLevels)->delete();
                DB::table('level')->whereIn('id', $existingLevels)->delete();
            }

            if ($request->has('level_data') && is_array($request->level_data)) {
                foreach ($request->level_data as $lData) {
                    if (isset($lData['level'])) {
                        $levelId = DB::table('level')->insertGetId([
                            'product_id' => $product->id,
                            'type' => $lData['level'],
                            'description' => $lData['description'] ?? '',
                            'branch_id' => session('branch_id')
                        ]);

                        // Save Designations
                        if (isset($lData['designations']) && is_array($lData['designations'])) {
                            foreach ($lData['designations'] as $desigId) {
                                DB::table('level_has_designation')->insert([
                                    'level_id' => $levelId,
                                    'designation_id' => $desigId,
                                    'branch_id' => session('branch_id')
                                ]);
                            }
                        }

                        // Save Checklist
                        if (isset($lData['checklist']) && is_array($lData['checklist'])) {
                            foreach ($lData['checklist'] as $checkItem) {
                                if (!empty($checkItem)) {
                                    DB::table('approval_checklist')->insert([
                                        'level_id' => $levelId,
                                        'description' => $checkItem,
                                        'branch_id' => session('branch_id')
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Product Updated Successfully',
                    'redirect_url' => route('product.index')
                ]);
            }

            return redirect()->route('product.index')->with('success', 'Product Updated Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error Updating Product: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error Updating Product: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $productName = $product->product_name; // Store for log
            $product->delete(); // Soft delete if trait used

            // Log Activity
            if (auth()->check()) {
                activity()
                    ->performedOn($product)
                    ->causedBy(auth()->user())
                    ->log('Product Deleted: ' . $productName);
            }

            return redirect()->route('product.index')->with('success', 'Product Deleted Successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error Deleting Product');
        }
    }

    public function getProductData($id)
    {
        $product = Product::with(['additional_charges', 'required_documents'])->findOrFail($id);

        return response()->json([
            'product' => $product
        ]);
    }
}

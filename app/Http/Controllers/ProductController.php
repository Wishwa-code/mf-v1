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
    public function index(Request $request)
    {
        if ($request->ajax()) {
            try {
                $data = Product::with(['product_has_items', 'additional_charges'])->latest();

                return \Yajra\DataTables\Facades\DataTables::of($data)
                    ->addIndexColumn()
                    ->editColumn('product_name', function ($row) {
                        return '<span class="fw-bold">' . $row->product_name . '</span>';
                    })
                    ->editColumn('interest_method', function ($row) {
                        return ucwords(str_replace('_', ' ', $row->interest_method));
                    })
                    ->editColumn('loan_period_type', function ($row) {
                        return ucwords(str_replace('_', ' ', $row->loan_period_type));
                    })
                    ->addColumn('items_list', function ($row) {
                        if ($row->product_has_items->isEmpty()) return '<span class="text-muted">No Items</span>';
                        $items = $row->product_has_items->take(2)->map(function ($item) {
                            return '<span class="fw-bold">' . $item->product_item_name . '</span>';
                        })->implode(', ');

                        $count = $row->product_has_items->count();
                        if ($count > 2) $items .= '... (+' . ($count - 2) . ')';
                        return '<small>' . $items . '</small>';
                    })
                    ->addColumn('charges_list', function ($row) {
                        if ($row->additional_charges->isEmpty()) return '<span class="text-muted">No Charges</span>';
                        $charges = $row->additional_charges->take(2)->map(function ($c) {
                            $type = $c->value_type === 'Percentage' ? '(%)' : '(Fixed)';
                            return '<span class="fw-bold">' . $c->description . '</span>: <span class="fw-bold">' . number_format($c->value, 2) . '</span> <span class="text-muted small" style="font-size: 1.00em;">' . $type . '</span>';
                        })->implode('<br>');

                        $count = $row->additional_charges->count();
                        if ($count > 2) $charges .= '<br><small class="text-primary">+' . ($count - 2) . ' more</small>';
                        return '<small>' . $charges . '</small>';
                    })
                    ->addColumn('action', function ($row) {
                        $viewBtn = '<a href="javascript:void(0)" class="btn btn-info btn-sm rounded-pill me-1 js-view-product" data-id="' . $row->id . '" data-bs-toggle="tooltip" title="View Details"><i class="bi bi-eye"></i> View</a>';
                        $editBtn = '<a href="' . route('product.edit', $row->id) . '" class="btn btn-success btn-sm rounded-pill me-1" data-bs-toggle="tooltip" title="Edit Product"><i class="bi bi-pencil-square"></i> Edit</a>';
                        $deleteBtn = '<a href="' . route('product.destroy', $row->id) . '" class="btn btn-danger btn-sm rounded-pill js-delete-trigger" data-url="' . route('product.destroy', $row->id) . '" data-bs-toggle="tooltip" title="Delete Product"><i class="bi bi-trash"></i></a>';
                        return '<div class="d-flex justify-content-end">' . $viewBtn . $editBtn . $deleteBtn . '</div>';
                    })
                    ->addColumn('status', function ($row) {
                        $checked = $row->status == 'active' ? 'checked' : '';
                        return '<div class="form-check form-switch d-flex justify-content-center" data-bs-toggle="tooltip" title="Change Status">
                                    <input class="form-check-input status-toggle" type="checkbox" data-id="' . $row->id . '" ' . $checked . '>
                                </div>';
                    })
                    ->rawColumns(['product_name', 'items_list', 'charges_list', 'action', 'status'])
                    ->make(true);
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
        return view('pages.ViewProduct');
    }

    public function updateStatus(Request $request)
    {
        $product = Product::find($request->id);
        if ($product) {
            $product->status = $request->status;
            $product->save();
            return response()->json(['success' => 'Status saved successfully.']);
        }
        return response()->json(['error' => 'Product not found.'], 404);
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

            $product = Product::create($validated);

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

            // Save Financial Configuration Items
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $product->product_has_items()->create([
                        'product_item_name' => $item['product_item_name'] ?? $product->product_item_name,
                        'minimum_loan_amount' => $item['minimum_loan_amount'] ?? null,
                        'maximum_loan_amount' => $item['maximum_loan_amount'] ?? null,
                        'minimum_interest' => $item['minimum_interest'] ?? null,
                        'maximum_interest' => $item['maximum_interest'] ?? null,
                        'minimum_loan_period' => $item['minimum_loan_period'] ?? null,
                        'maximum_loan_period' => $item['maximum_loan_period'] ?? null,
                        'minimum_collection_period' => $item['minimum_collection_period'] ?? $item['minimum_loan_period'],
                        'maximum_collection_period' => $item['maximum_collection_period'] ?? $item['maximum_loan_period'],
                        'required_guarantee_count' => $item['required_guarantee_count'] ?? null,
                        // Penalty configs per item
                        'penalty_method' => $item['penalty_method'] ?? null,
                        'penalty_percentage' => $item['penalty_percentage'] ?? null,
                        'penalty_apply_type' => $item['penalty_apply_type'] ?? null,
                        'penalty_start_after_days' => $item['penalty_start_after_days'] ?? null,
                        // Savings per item if needed, but we kept global. Table supports it though.
                        'saving_amount' => $item['saving_amount'] ?? null,
                        'saving_interest_rate' => $item['saving_interest_rate'] ?? null,
                    ]);
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
        return view('pages.CreateProduct', compact('product'));
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

            // Sync Financial Configuration Items
            $product->product_has_items()->delete();
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    $product->product_has_items()->create([
                        'product_item_name' => $item['product_item_name'] ?? $product->product_item_name,
                        'minimum_loan_amount' => $item['minimum_loan_amount'] ?? null,
                        'maximum_loan_amount' => $item['maximum_loan_amount'] ?? null,
                        'minimum_interest' => $item['minimum_interest'] ?? null,
                        'maximum_interest' => $item['maximum_interest'] ?? null,
                        'minimum_loan_period' => $item['minimum_loan_period'] ?? null,
                        'maximum_loan_period' => $item['maximum_loan_period'] ?? null,
                        'minimum_collection_period' => $item['minimum_collection_period'] ?? null,
                        'maximum_collection_period' => $item['maximum_collection_period'] ?? null,
                        'required_guarantee_count' => $item['required_guarantee_count'] ?? null,
                        'penalty_method' => $item['penalty_method'] ?? null,
                        'penalty_percentage' => $item['penalty_percentage'] ?? null,
                        'penalty_apply_type' => $item['penalty_apply_type'] ?? null,
                        'penalty_start_after_days' => $item['penalty_start_after_days'] ?? null,
                    ]);
                }
            }
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

    public function getProductDetails($id)
    {
        $product = Product::with(['additional_charges', 'required_documents', 'product_has_items'])->findOrFail($id);

        return response()->json([
            'product' => $product
        ]);
    }
}

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
            $userData = session('user_data');
            $validated['created_by'] = isset($userData['userData']['idUser']) ? $userData['userData']['idUser'] : null;

            $validated['saving_collection_type'] = $request->saving_payment_type;

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

            // Log Activity
            $userData = session('user_data');
            activity()
                ->performedOn($product)
                ->withProperties([
                    'causer_name' => $userData['userData']['full_name'] ?? 'Unknown User',
                    'email' => $userData['userData']['Email'] ?? '',
                    'user_id' => $userData['userData']['idUser'] ?? null,
                    'ip' => request()->ip(),
                    'attributes' => $product->toArray()
                ])
                ->log('Product Created: ' . $product->product_name);

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
            $validated = $request->validated();
            $userData = session('user_data');
            $validated['updated_by'] = isset($userData['userData']['idUser']) ? $userData['userData']['idUser'] : null;

            $validated['saving_collection_type'] = $request->saving_payment_type;

            // saving_account_status is directly in $validated keys

            $product->update($validated);

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

            DB::commit();

            // Log Activity
            $userData = session('user_data');
            activity()
                ->performedOn($product)
                ->withProperties([
                    'causer_name' => $userData['userData']['full_name'] ?? 'Unknown User',
                    'email' => $userData['userData']['Email'] ?? '',
                    'user_id' => $userData['userData']['idUser'] ?? null,
                    'ip' => request()->ip(),
                    'attributes' => $product->toArray()
                ])
                ->log('Product Updated: ' . $product->product_name);

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
            $userData = session('user_data');
            $product->deleted_by = isset($userData['userData']['idUser']) ? $userData['userData']['idUser'] : null;
            $product->save();

            $product->delete(); // Soft delete if trait used

            // Log Activity
            activity()
                ->performedOn($product)
                ->withProperties([
                    'causer_name' => $userData['userData']['full_name'] ?? 'Unknown User',
                    'email' => $userData['userData']['Email'] ?? '',
                    'user_id' => $userData['userData']['idUser'] ?? null,
                    'ip' => request()->ip(),
                    'attributes' => $product->toArray()
                ])
                ->log('Product Deleted: ' . $productName);

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

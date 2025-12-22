<?php

namespace App\Http\Controllers;

use App\Models\BusinessCategory;
use App\Http\Requests\BusinessCategoryRequest;
use Yajra\DataTables\DataTables;
use Illuminate\Http\Request;

class BusinessCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $categories = BusinessCategory::select(['id', 'name']);
            return DataTables::of($categories)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $editUrl = route('business-categories.edit', $row->id);
                    $deleteUrl = route('business-categories.destroy', $row->id);
                    $btn = '<div class="text-end">';
                    $btn .= '<button type="button" class="btn btn-sm btn-outline-primary me-2 edit-btn" data-id="'.$row->id.'" data-name="'.$row->name.'" data-url="'.$editUrl.'"><i class="bi bi-pencil"></i> Edit</button>';
                    $btn .= '<button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-id="'.$row->id.'" data-url="'.$deleteUrl.'"><i class="bi bi-trash"></i> Delete</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pages.business_categories.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.business_categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BusinessCategoryRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = auth()->id() ?? 1; // Default to 1 if no auth for now
            
            BusinessCategory::create($data);
            
            return response()->json(['success' => true, 'message' => 'Business Category created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error creating category: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BusinessCategory $businessCategory)
    {
        return view('pages.business_categories.edit', compact('businessCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BusinessCategoryRequest $request, BusinessCategory $businessCategory)
    {
        try {
            $data = $request->validated();
            $data['updated_by'] = auth()->id() ?? 1;
            
            $businessCategory->update($data);
            
            return response()->json(['success' => true, 'message' => 'Business Category updated successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating category: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BusinessCategory $businessCategory)
    {
        try {
            $businessCategory->delete();
            return response()->json(['success' => true, 'message' => 'Business Category deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting category: ' . $e->getMessage()], 500);
        }
    }
}

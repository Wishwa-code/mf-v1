<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (!session()->has('branch_id')) {
            return response()->json(['message' => 'Session expired, please login again.'], 401);
        }

        $query = tableWithBranch('suppliers');
        if ($query instanceof RedirectResponse) {
            return response()->json(['message' => 'Session expired, please login again.'], 401);
        }

        $query->where('status', 1);

        if ($request->filled('search')) {
            $search = '%' . trim($request->input('search')) . '%';
            $query->where(function ($builder) use ($search) {
                $builder->where('supplier_no', 'like', $search)
                    ->orWhere('company_name', 'like', $search)
                    ->orWhere('contact_number', 'like', $search);
            });
        }

        $suppliers = $query
            ->orderByDesc('created_at')
            ->get([
                'id',
                'supplier_no',
                'company_name',
                'contact_number',
                'address',
                'status',
            ]);

        return response()->json(['data' => $suppliers]);
    }
}

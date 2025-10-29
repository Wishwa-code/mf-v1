<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function store(Request $request): JsonResponse
    {
        if (!session()->has('branch_id')) {
            return response()->json(['message' => 'Session expired, please login again.'], 401);
        }

        $validator = Validator::make($request->all(), [
            'supplier_no' => 'required|string|max:50|unique:suppliers,supplier_no',
            'company_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'bank_name' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'account_holder_name' => 'nullable|string|max:255',
            'business_reg_no' => 'nullable|string|max:50',
            'tax_vat_no' => 'nullable|string|max:50',
            'nic_passport' => 'nullable|string|max:50',
            'file1' => 'nullable|file|max:5120',
            'file2' => 'nullable|file|max:5120',
            'file3' => 'nullable|file|max:5120',
            'file4' => 'nullable|file|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = [
            'supplier_no' => trim($request->input('supplier_no')),
            'company_name' => trim($request->input('company_name')),
            'contact_number' => trim((string)$request->input('contact_number')) ?: null,
            'address' => trim((string)$request->input('address')) ?: null,
            'email' => trim((string)$request->input('email')) ?: null,
            'bank_name' => trim((string)$request->input('bank_name')) ?: null,
            'branch' => trim((string)$request->input('branch')) ?: null,
            'account_number' => trim((string)$request->input('account_number')) ?: null,
            'account_holder_name' => trim((string)$request->input('account_holder_name')) ?: null,
            'business_reg_no' => trim((string)$request->input('business_reg_no')) ?: null,
            'tax_vat_no' => trim((string)$request->input('tax_vat_no')) ?: null,
            'nic_passport' => trim((string)$request->input('nic_passport')) ?: null,
            'attachment_1' => $this->storeAttachment($request, 'file1'),
            'attachment_2' => $this->storeAttachment($request, 'file2'),
            'attachment_3' => $this->storeAttachment($request, 'file3'),
            'attachment_4' => $this->storeAttachment($request, 'file4'),
            'status' => 1,
            'created_by' => session('userid'),
            'created_by_name' => session('username'),
            'updated_by' => null,
            'updated_by_name' => null,
        ];

        $supplierId = insertWithBranch('suppliers', $payload);

        return response()->json([
            'message' => 'Supplier created successfully',
            'id' => $supplierId,
        ], 201);
    }

    public function show($id): JsonResponse
    {
        if (!session()->has('branch_id')) {
            return response()->json(['message' => 'Session expired, please login again.'], 401);
        }

        $query = tableWithBranch('suppliers');
        if ($query instanceof RedirectResponse) {
            return response()->json(['message' => 'Session expired, please login again.'], 401);
        }

        $supplier = $query->where('id', $id)->where('status', 1)->first();

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        return response()->json(['data' => $supplier]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        if (!session()->has('branch_id')) {
            return response()->json(['message' => 'Session expired, please login again.'], 401);
        }

        $query = tableWithBranch('suppliers');
        if ($query instanceof RedirectResponse) {
            return response()->json(['message' => 'Session expired, please login again.'], 401);
        }

        $supplier = $query->where('id', $id)->where('status', 1)->first();

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'supplier_no' => 'required|string|max:50|unique:suppliers,supplier_no,' . $id,
            'company_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => 'nullable|email|max:255',
            'bank_name' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:50',
            'account_holder_name' => 'nullable|string|max:255',
            'business_reg_no' => 'nullable|string|max:50',
            'tax_vat_no' => 'nullable|string|max:50',
            'nic_passport' => 'nullable|string|max:50',
            'file1' => 'nullable|file|max:5120',
            'file2' => 'nullable|file|max:5120',
            'file3' => 'nullable|file|max:5120',
            'file4' => 'nullable|file|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $payload = [
            'supplier_no' => trim($request->input('supplier_no')),
            'company_name' => trim($request->input('company_name')),
            'contact_number' => trim((string)$request->input('contact_number')) ?: null,
            'address' => trim((string)$request->input('address')) ?: null,
            'email' => trim((string)$request->input('email')) ?: null,
            'bank_name' => trim((string)$request->input('bank_name')) ?: null,
            'branch' => trim((string)$request->input('branch')) ?: null,
            'account_number' => trim((string)$request->input('account_number')) ?: null,
            'account_holder_name' => trim((string)$request->input('account_holder_name')) ?: null,
            'business_reg_no' => trim((string)$request->input('business_reg_no')) ?: null,
            'tax_vat_no' => trim((string)$request->input('tax_vat_no')) ?: null,
            'nic_passport' => trim((string)$request->input('nic_passport')) ?: null,
            'updated_by' => session('userid'),
            'updated_by_name' => session('username'),
        ];

        $attachment1 = $this->storeAttachment($request, 'file1');
        if ($attachment1) {
            $payload['attachment_1'] = $attachment1;
        }

        $attachment2 = $this->storeAttachment($request, 'file2');
        if ($attachment2) {
            $payload['attachment_2'] = $attachment2;
        }

        $attachment3 = $this->storeAttachment($request, 'file3');
        if ($attachment3) {
            $payload['attachment_3'] = $attachment3;
        }

        $attachment4 = $this->storeAttachment($request, 'file4');
        if ($attachment4) {
            $payload['attachment_4'] = $attachment4;
        }

        updateWithBranch('suppliers', 'id', $id, $payload);

        return response()->json(['message' => 'Supplier updated successfully']);
    }

    private function storeAttachment(Request $request, string $fieldName): ?string
    {
        if (!$request->hasFile($fieldName)) {
            return null;
        }

        $file = $request->file($fieldName);
        $path = $file->store('uploads/suppliers', 'public');

        if (!$path) {
            return null;
        }

        return '/storage/' . ltrim($path, '/');
    }
}

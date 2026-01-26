<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $branchId = session('branch_id');
        $customerId = $this->route('customer') ? $this->route('customer')->id : null;

        return [
            'cus_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('customers', 'customer_code')
                    ->ignore($customerId)
                    ->where(function ($query) use ($branchId) {
                        return $query->where('branch_id', $branchId);
                    }),
            ],
            'nic' => [
                'nullable', // Changed to nullable based on migration nullable()
                'string',
                'max:20',
                Rule::unique('customers', 'new_nic')
                    ->ignore($customerId)
                    ->where(function ($query) use ($branchId) {
                        return $query->where('branch_id', $branchId);
                    }),
            ],
            'contact_number' => [
                'nullable',
                'string',
                'max:15',
                Rule::unique('customers', 'contact_no')
                    ->ignore($customerId)
                    ->where(function ($query) use ($branchId) {
                        return $query->where('branch_id', $branchId);
                    }),
            ],
            'title' => 'nullable|string|max:10',
            'f_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
            'root' => 'nullable|integer', // Made nullable as per migration allow, but logically required for business? View has required *

            // Other fields from view that we validate but might not save to 'customers' table directly yet
            // 'contact_number_2' => 'nullable|string|max:15',
            // 'gender' => 'nullable|string',
            // ...
        ];
    }
}

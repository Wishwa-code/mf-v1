<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $branchId = session('branch_id');

        return [
            'cus_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('customers', 'customer_code')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                }),
            ],
            'nic' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customers', 'new_nic')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                }),
            ],
            'contact_number' => [
                'required',
                'string',
                'max:15',
                Rule::unique('customers', 'contact_no')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                }),
            ],
            'title' => 'nullable|string|max:10',
            'f_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email|max:150',
            'root' => 'required|integer',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'cus_number.required' => 'Customer Number is required.',
            'cus_number.unique' => 'This Customer Number already exists in this branch.',
            'nic.required' => 'NIC is required.',
            'nic.unique' => 'This NIC already exists in this branch.',
            'contact_number.required' => 'Contact Number is required.',
            'contact_number.unique' => 'This Contact Number already exists in this branch.',
            'root.required' => 'Route/Root selection is required.',
            'f_name.required' => 'First Name is required.',
            'last_name.required' => 'Last Name is required.',
            'gua_name.required' => 'Guardian Name is required.',
            'gua_nic.required' => 'Guardian NIC is required.',
        ];
    }
}

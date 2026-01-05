<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
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
        // Access session branch_id safely
        $branchId = session('branch_id');

        return [
            'cus_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('customer', 'cus_number')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                }),
            ],
            // 'new_nic' check was used in controller, but 'nic' is the actual field. 
            // We'll validate 'nic' as the unique field.
            'nic' => [
                'required',
                'string',
                'max:20',
                Rule::unique('customer', 'Nic')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                }),
            ],
            'contact_number' => [
                'required',
                'string',
                'max:15',
                Rule::unique('customer', 'Contact_No')->where(function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                }),
            ],
            'title' => 'nullable|string|max:10',
            'f_name' => 'required|string|max:100', // First Name
            'last_name' => 'required|string|max:100', // Last Name
            'email' => 'nullable|email|max:150',
            'contact_number_2' => 'nullable|string|max:15',
            'gender' => 'required|string',
            'dob' => 'nullable|date',
            'root' => 'required|integer|exists:route,id_route', // Ensure route exists

            // Address
            'curr_address_01' => 'nullable|string|max:255',
            'curr_address_02' => 'nullable|string|max:255',
            'curr_address_03' => 'nullable|string|max:255',

            'per_address_01' => 'nullable|string|max:255',
            'per_address_02' => 'nullable|string|max:255',
            'per_address_03' => 'nullable|string|max:255',

            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'landline' => 'nullable|string|max:15',

            // Guardian
            'gua_name' => 'required|string|max:200',
            'gua_nic' => 'required|string|max:20',
            'gua_contact' => 'nullable|string|max:15',

            // Occupation
            'occu_job_position' => 'nullable|string|max:100',
            'occu_monthly_salary' => 'nullable|numeric',

            // Files
            'cus_phto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
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

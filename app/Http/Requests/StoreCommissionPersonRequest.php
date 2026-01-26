<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommissionPersonRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'branch_id' => 'required|integer|min:1',
            'full_name' => 'required|string|max:150',
            'contact_number' => 'nullable|string|max:30',
            'nic_number' => 'nullable|string|max:30',
            'brief_description' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:120',
            'bank_branch' => 'nullable|string|max:120',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_account_name' => 'nullable|string|max:120',
        ];
    }
}

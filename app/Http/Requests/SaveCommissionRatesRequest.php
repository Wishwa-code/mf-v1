<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveCommissionRatesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'branch_id' => 'required|integer|min:1',
            'rates' => 'required|array',
            'rates.*.person_id' => 'required|integer|min:1',
            'rates.*.product_id' => 'required|integer|min:1',
            'rates.*.rate' => 'nullable|numeric|min:0',
        ];
    }
}

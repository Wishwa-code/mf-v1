<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        return [
            'product_name' => 'required|string|max:255',
            'product_code' => 'required|string|max:50|unique:products,product_code,' . $this->route('product')?->id,
            'interest_method' => 'required|string',
            'minimum_loan_amount' => 'required|numeric|min:0',
            'maximum_loan_amount' => 'required|numeric|gte:minimum_loan_amount',
            'minimum_interest' => 'required|numeric|min:0',
            'maximum_interest' => 'required|numeric|min:0', // Logic for gte:minimum_interest can be complex if ranges overlap, keep simple for now
            'interest_period' => 'required|string', // Mapped to collection_period_type or interest_apply_type? Need to verify mapping.
            // Based on view: interest_period maps to "Loan Interest Period"

            'default_loan_period' => 'required|integer|min:1', // "Default Loan Period" -> period_count in view?
            'loan_period_type' => 'required|in:Days,Weeks,Months', // "Type" next to Default Loan Period

            'guarantee_count' => 'required|integer|min:0',

            // Loan Duration and Repayments
            'loan_duration' => 'required|integer|min:1',
            'loan_duration_type' => 'required|in:Days,Weeks,Months',

            'repayment_type' => 'required|string', // collection_type
            'collection_date_type' => 'required|string',

            // Penalty
            'penalty_method' => 'required|string',
            'penalty_percentage' => 'required|numeric|min:0',
            'penalty_period' => 'required|string',
            'penalty_start_after' => 'required|integer|min:0',
            'penalty_duration_type' => 'required|string', // duration_period_panelty

            // Savings (Optional based on enable_saving)
            'enable_saving' => 'required|in:Yes,No',
            'saving_amount_type' => 'nullable|required_if:enable_saving,Yes|string',
            'saving_amount' => 'nullable|required_if:enable_saving,Yes|numeric|min:0',
            'saving_payment_type' => 'nullable|required_if:enable_saving,Yes|string',
        ];
    }
}

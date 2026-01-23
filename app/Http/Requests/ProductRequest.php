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

            // Global Settings
            'loan_period_type' => 'required|in:Days,Weeks,Months',
            'interest_period_type' => 'required|string',
            'collection_period_type' => 'required|string',
            'collection_date_type' => 'required|string',
            'guarantee_count' => 'nullable|integer|min:0',
            'recovery_account_status' => 'nullable|in:active,inactive',

            // Items (Financial Configuration)
            'items' => 'required|array|min:1',
            'items.*.product_item_name' => 'nullable|string',
            'items.*.minimum_loan_amount' => 'required|numeric|min:0',
            'items.*.maximum_loan_amount' => 'required|numeric|gte:items.*.minimum_loan_amount',
            'items.*.minimum_interest' => 'required|numeric|min:0',
            'items.*.maximum_interest' => 'required|numeric|min:0',
            'items.*.minimum_loan_period' => 'required|integer|min:1',
            'items.*.maximum_loan_period' => 'required|numeric|gte:items.*.minimum_loan_period',

            // Optional / Conditional in Items
            'items.*.minimum_collection_period' => 'nullable|integer|min:1',
            'items.*.maximum_collection_period' => 'nullable|integer|gte:items.*.minimum_collection_period',
            'items.*.required_guarantee_count' => 'nullable|integer|min:0',

            'items.*.penalty_method' => 'nullable|string',
            'items.*.penalty_percentage' => 'nullable|numeric|min:0',
            'items.*.penalty_start_after_days' => 'nullable|integer|min:0',
            'items.*.penalty_apply_type' => 'nullable|string',

            // Additional Charges
            'charges' => 'nullable|array',
            'charges.*.description' => 'required|string',
            'charges.*.value_type' => 'required|string',
            'charges.*.value' => 'required|numeric|min:0',
            'charges.*.deduction_type' => 'required|string',

            // Documents
            'documents' => 'nullable|array',
            'documents.*.name' => 'required|string',
            'documents.*' => 'nullable', // Basic check for array structure

            // Legacy/Top-level optional
            'minimum_loan_amount' => 'nullable',
            'maximum_loan_amount' => 'nullable',
            'minimum_interest' => 'nullable',
            'maximum_interest' => 'nullable',
            'default_loan_period' => 'nullable',
            'interest_period' => 'nullable',
            'loan_duration' => 'nullable',
            'loan_duration_type' => 'nullable',
            'penalty_method' => 'nullable',
            'penalty_percentage' => 'nullable',
            'penalty_period' => 'nullable',
            'penalty_start_after' => 'nullable',
            'penalty_duration_type' => 'nullable',

            // Savings (Optional based on enable_saving)
            'saving_account_status' => 'required|in:active,inactive',
            'saving_amount_type' => 'nullable|required_if:saving_account_status,active|string',
            'saving_amount' => 'nullable|numeric|min:0',
            'saving_payment_type' => 'nullable|required_if:saving_account_status,active|string',
            'saving_interest_cal_type' => 'nullable|required_if:saving_account_status,active|string',
        ];
    }
}

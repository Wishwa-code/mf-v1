<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string'],

            'value' => [
                'required',
                function ($attribute, $value, $fail) {

                    $key = (string) $this->key;

                    /*
                     |--------------------------------------------------------------------------
                     | Allowed key patterns
                     |--------------------------------------------------------------------------
                     */
                    $fixedKeys = [
                        'payment_member_name',
                        'loan_disbursement_policy',
                        'payment_backdate',
                        'loan_order',
                        'max_allowed_loans',
                        'document_types',
                        'image_types', // ✅ ADDED
                        'collector_txn_modes',
                        'fund_request_columns',
                        'disbursement_columns',
                        'document_upload_restriction',
                        'guarantees_restriction',
                        'change_product_details',
                        'first_installment_daily',
                        'first_installment_weekly',
                        'first_installment_monthly',
                        'recovery_account_status',
                        'collection_days',
                        'due_skip_type',
                    ];

                    $isFixed              = in_array($key, $fixedKeys, true);
                    $isSheetEmptyRows     = preg_match('/^empty_row_count_[A-Za-z0-9_]+$/', $key);
                    $isSheetOrderBy       = preg_match('/^repayment_order_[A-Za-z0-9_]+$/', $key);
                    $isHeadOfficeApproval = preg_match('/^headoffice_approval_[0-9]+$/', $key);

                    if (
                        !$isFixed &&
                        !$isSheetEmptyRows &&
                        !$isSheetOrderBy &&
                        !$isHeadOfficeApproval
                    ) {
                        return $fail('Invalid key.');
                    }

                    /*
                     |--------------------------------------------------------------------------
                     | Fixed keys validation
                     |--------------------------------------------------------------------------
                     */
                    if ($isFixed) {

                        match ($key) {
                            'payment_member_name' => in_array($value, [
                                'full_name',
                                'with_initial',
                                'only_first_name',
                                'only_last_name'
                            ], true) ?: $fail('Invalid value for payment_member_name.'),

                            'loan_disbursement_policy' =>
                                in_array($value, ['strict', 'flexible'], true)
                                ?: $fail('Invalid value for loan_disbursement_policy.'),

                            'payment_backdate' =>
                                in_array($value, ['enabled', 'disabled'], true)
                                ?: $fail('Invalid value for payment_backdate.'),

                            'loan_order' =>
                                in_array($value, ['create_date', 'loan_number', 'issue_date'], true)
                                ?: $fail('Invalid value for loan_order.'),

                            'recovery_account_status' =>
                                in_array($value, ['active', 'inactive'], true)
                                ?: $fail('Invalid value for recovery_account_status.'),

                            'due_skip_type' =>
                                in_array($value, ['skip_installment', 'skip_day'], true)
                                ?: $fail('Invalid value for due_skip_type.'),

                            default => null,
                        };

                        /*
                         |---------------------------------------------
                         | Numeric rules
                         |---------------------------------------------
                         */
                        if (
                            $key === 'max_allowed_loans' &&
                            (!is_numeric($value) || $value < 1 || $value > 50)
                        ) {
                            return $fail('Max allowed loans must be between 1 and 50.');
                        }

                        /*
                         |---------------------------------------------
                         | Generic JSON arrays
                         |---------------------------------------------
                         */
                        if (
                            in_array($key, [
                                'document_types',
                                'fund_request_columns',
                                'disbursement_columns'
                            ], true)
                        ) {
                            $decoded = json_decode($value, true);

                            if (json_last_error() !== JSON_ERROR_NONE) {
                                return $fail("{$key} must be valid JSON.");
                            }

                            if (!is_array($decoded) || count($decoded) === 0) {
                                return $fail("{$key} must be a non-empty JSON array.");
                            }
                        }

                        /*
                         |---------------------------------------------
                         | IMAGE TYPES (custom structure)
                         |---------------------------------------------
                         */
                        if ($key === 'image_types') {
                            $decoded = json_decode($value, true);

                            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                                return $fail('image_types must be a valid JSON array.');
                            }

                            if (count($decoded) === 0) {
                                return $fail('image_types must contain at least one item.');
                            }

                            foreach ($decoded as $index => $item) {

                                if (
                                    !isset($item['name']) ||
                                    !is_string($item['name']) ||
                                    trim($item['name']) === ''
                                ) {
                                    return $fail("Image type name is required at index {$index}.");
                                }

                                if (
                                    !array_key_exists('is_required', $item) ||
                                    !is_bool($item['is_required'])
                                ) {
                                    return $fail("Image type 'is_required' must be boolean at index {$index}.");
                                }
                            }
                        }

                        /*
                         |---------------------------------------------
                         | Collector transaction modes
                         |---------------------------------------------
                         */
                        if ($key === 'collector_txn_modes') {
                            $allowed = [
                                'cash_bank',
                                'bank_deposit',
                                'cheques',
                                'collector_account'
                            ];

                            $decoded = json_decode($value, true);
                            $modes   = is_array($decoded)
                                ? $decoded
                                : array_filter(array_map('trim', explode(',', (string) $value)));

                            foreach ($modes as $mode) {
                                if (!in_array($mode, $allowed, true)) {
                                    return $fail("Invalid mode '{$mode}' for collector_txn_modes.");
                                }
                            }
                        }

                        /*
                         |---------------------------------------------
                         | Collection days
                         |---------------------------------------------
                         */
                        if ($key === 'collection_days') {
                            $decoded = json_decode($value, true);

                            if (
                                json_last_error() !== JSON_ERROR_NONE ||
                                !is_array($decoded) ||
                                count($decoded) === 0
                            ) {
                                return $fail('collection_days must be a non-empty JSON array.');
                            }

                            foreach ($decoded as $day) {
                                if (!is_numeric($day) || (int) $day < 0 || (int) $day > 6) {
                                    return $fail('collection_days contains invalid weekday code.');
                                }
                            }
                        }

                        return;
                    }

                    /*
                     |--------------------------------------------------------------------------
                     | Sheet scoped keys
                     |--------------------------------------------------------------------------
                     */
                    if (
                        $isSheetEmptyRows &&
                        (!is_numeric($value) || $value < 0 || $value > 100)
                    ) {
                        return $fail('Empty row count must be between 0 and 100.');
                    }

                    if ($isSheetOrderBy) {
                        $allowed = [
                            'name_asc',
                            'name_desc',
                            'loan_asc',
                            'loan_desc',
                            'create_asc',
                            'create_desc',
                            'loan_issue',
                            'cus_number',
                        ];

                        if (!in_array($value, $allowed, true)) {
                            return $fail('Invalid value for repayment_order.');
                        }
                    }

                    if (
                        $isHeadOfficeApproval &&
                        !in_array($value, ['required', 'not_required'], true)
                    ) {
                        return $fail('Head office approval must be either required or not_required.');
                    }
                },
            ],
        ];
    }
}

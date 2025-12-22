<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AppSettings;
use App\Models\LeadHasImages;

class UpdateCustomerLeadRequest extends FormRequest
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
        $rules = [
            'full_name' => 'required|string|max:255',
            'address' => 'required|string|max:1000',
            'phone_number' => 'required|string|max:20',
            'type' => 'required|string',
            'periods' => 'required|integer|min:1',
            'loan_amount' => 'required|numeric',
            'business_category_id' => 'required|exists:business_categories,id',
            // Using latitude/longitude to match the form inputs in edit.blade.php
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'route_id' => 'nullable|exists:route,id_route',
            'district' => 'nullable|string',
            'city' => 'nullable|string',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
        ];

        // Dynamic Image Validation
        $this->addImageRules($rules, 'image_types');
        $this->addImageRules($rules, 'guardian_image_types');
        $this->addImageRules($rules, 'guarantor_image_types');

        return $rules;
    }

    /**
     * Helper to add image rules based on AppSettings and existing images.
     */
    private function addImageRules(array &$rules, string $settingKey)
    {
        $setting = AppSettings::where('key', $settingKey)->value('value');
        if ($setting) {
            $types = json_decode($setting, true);
            if (is_array($types)) {
                $lead = $this->route('lead'); // Process the lead from the route

                foreach ($types as $type) {
                    $fieldName = 'image_' . str_replace(' ', '_', strtolower($type['name']));
                    $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);

                    // Check if file is required based on settings AND if it doesn't already exist
                    $isRequired = isset($type['is_required']) && $type['is_required'];

                    // Check if image already exists for this lead
                    $imageExists = false;
                    if ($lead) {
                        $imageExists = LeadHasImages::where('lead_id', $lead->id)
                            ->where('image_type', $type['name'])
                            ->exists();
                    }

                    if ($isRequired && !$imageExists) {
                        $rules[$fieldName] = 'required|image|mimes:jpeg,png,jpg,gif';
                    } else {
                        $rules[$fieldName] = 'nullable|image|mimes:jpeg,png,jpg,gif';
                    }

                    // Also validate location if image is uploaded? 
                    // Usually we might want to validate the hidden lat/lng fields if the image is present.
                    // But for now sticking to the core image validation request.
                }
            }
        }
    }
}

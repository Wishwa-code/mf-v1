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
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'type' => ['required', 'string', 'in:group,individual,business,leasing'],
            'periods' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'loan_amount' => ['required', 'numeric', 'min:0'],
            'business_category_id' => ['required', 'exists:business_categories,id'],
            // Using latitude/longitude to match the form inputs in edit.blade.php
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'route_id' => 'nullable|exists:route,id_route',
            'district' => 'nullable|string',
            'city' => 'nullable|string',
            'source' => 'nullable|string',
            'notes' => 'nullable|string',
            'recovery_officer_id' => 'nullable|exists:user,id',
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

                    // For update, if it's required and no image exists, we need at least one file.
                    // But if an image exists, we can still accept uploads (appending).
                    // So 'required' applies if NO images exist.

                    if ($isRequired && !$imageExists) {
                        $rules[$fieldName] = 'required|array';
                    } else {
                        $rules[$fieldName] = 'nullable|array';
                    }

                    // Validate contents of the array
                    $rules[$fieldName . '.*'] = 'file|mimes:jpeg,jpg,png,gif,webp,pdf';
                }
            }
        }
    }

    public function messages(): array
    {
        $messages = [
            'full_name.required' => 'Full name is required.',
            'phone_number.required' => 'Phone number is required.',
            'type.required' => 'Type is required.',
            'type.in' => 'Type must be one of: group, individual, business, or leasing.',
            'periods.required' => 'Periods is required.',
            'email.email' => 'Please provide a valid email address.',
            'latitude.numeric' => 'Latitude must be a valid number.',
            'longitude.numeric' => 'Longitude must be a valid number.',
            'business_category_id.required' => 'Business category is required.',
        ];

        // Get image types from app_settings for custom messages
        $imageTypesSetting = AppSettings::where('key', 'image_types')->value('value');

        if ($imageTypesSetting) {
            $imageTypes = json_decode($imageTypesSetting, true);

            if (is_array($imageTypes) && count($imageTypes) > 0) {
                foreach ($imageTypes as $imageType) {
                    $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                    $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);
                    $displayName = $imageType['name'];

                    $messages[$fieldName . '.required'] = "{$displayName} image is required.";
                    $messages[$fieldName . '.image'] = "{$displayName} must be an image file.";
                    $messages[$fieldName . '.mimes'] = "{$displayName} must be a jpeg, jpg, png, gif, or webp file.";
                    $messages[$fieldName . '.max'] = "{$displayName} file size must not exceed 10MB.";
                }
            }
        }

        return $messages;
    }
}

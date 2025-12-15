<?php

namespace App\Http\Requests;

use App\Models\AppSettings;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerLeadRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $rules = [
            'full_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];

        // Get image types from app_settings
        $imageTypesSetting = AppSettings::where('key', 'image_types')->value('value');
        
        if ($imageTypesSetting) {
            $imageTypes = json_decode($imageTypesSetting, true);
            
            if (is_array($imageTypes) && count($imageTypes) > 0) {
                foreach ($imageTypes as $imageType) {
                    $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                    $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);
                    
                    if (isset($imageType['is_required']) && $imageType['is_required']) {
                        // Required field
                        $rules[$fieldName] = ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240']; // 10MB max
                    } else {
                        // Optional field (nullable)
                        $rules[$fieldName] = ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:10240'];
                    }
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        $messages = [
            'full_name.required' => 'Full name is required.',
            'phone_number.required' => 'Phone number is required.',
            'email.email' => 'Please provide a valid email address.',
            'latitude.numeric' => 'Latitude must be a valid number.',
            'longitude.numeric' => 'Longitude must be a valid number.',
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

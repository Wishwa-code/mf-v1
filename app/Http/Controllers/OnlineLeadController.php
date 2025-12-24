<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerLeadRequest;
use App\Models\AppSettings;
use App\Models\CustomerLead;
use App\Models\LeadHasImages;
use App\Models\BusinessCategory;
use App\Models\Route; // Using the Route model
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OnlineLeadController extends Controller
{
    /**
     * Show the form for creating a new online lead.
     */
    public function create()
    {
        // Get image types from app_settings
        $imageTypesSetting = AppSettings::where('key', 'image_types')->value('value');
        $imageTypes = $imageTypesSetting ? json_decode($imageTypesSetting, true) : [];

        $guardianImageTypesSetting = AppSettings::where('key', 'guardian_image_types')->value('value');
        $guardianImageTypes = $guardianImageTypesSetting ? json_decode($guardianImageTypesSetting, true) : [];

        $guarantorImageTypesSetting = AppSettings::where('key', 'guarantor_image_types')->value('value');
        $guarantorImageTypes = $guarantorImageTypesSetting ? json_decode($guarantorImageTypesSetting, true) : [];

        $businessCategories = BusinessCategory::all();

        // Fetch all routes for the dropdown
        $routes = Route::with('officer')->get();

        return view('pages.leads.create_online', compact('imageTypes', 'guardianImageTypes', 'guarantorImageTypes', 'businessCategories', 'routes'));
    }

    /**
     * Get Recovery Officers for a specific Route via AJAX.
     */
    public function getRecoveryOfficersByRoute($routeId)
    {
        try {
            $officers = DB::table('collector_has_route')
                ->join('user', 'collector_has_route.collector_id', '=', 'user.id')
                ->where('collector_has_route.route_id', $routeId)
                ->where('user.Status', 1)
                ->select('user.id', 'user.Full_Name as text') // formatted for Select2
                ->get();

            return response()->json($officers);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created online lead.
     */
    public function store(Request $request)
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'type' => 'required|string|in:group,individual,business,leasing',
            'business_category_id' => 'required|exists:business_categories,id',
            'loan_amount' => 'required|numeric|min:0',
            'periods' => 'required|integer|min:1',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'route_id' => 'nullable|exists:route,id_route',
            'city' => 'nullable|string',
            'source' => 'required|string',
            'recovery_officer_id' => 'nullable|exists:user,id',
        ];

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $data = $request->only([
                'full_name',
                'phone_number',
                'email',
                'type',
                'business_category_id',
                'loan_amount',
                'periods',
                'address',
                'notes',
                'route_id',
                'district',
                'city',
                'source',
                'recovery_officer_id'
            ]);

            $data['status'] = 'pending';
            $data['created_at_lead'] = now();
            $data['created_by'] = session('userid', 1);

            // Create the lead
            $lead = CustomerLead::create($data);
            $lead->logActivity('created', 'Online Lead created successfully', ['attributes' => $lead->toArray()]);

            // Handle Images (Reusing logic from CustomerLeadController)
            $this->processImages($request, $lead, 'image_types', 'lead_images');
            $this->processImages($request, $lead, 'guardian_image_types', 'guardian_images');
            $this->processImages($request, $lead, 'guarantor_image_types', 'guarantor_images');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Online Lead saved successfully',
                'lead_id' => $lead->id
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save lead: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to process images
     */
    private function processImages($request, $lead, $settingKey, $directory)
    {
        $setting = AppSettings::where('key', $settingKey)->value('value');
        if ($setting) {
            $types = json_decode($setting, true);
            if (is_array($types)) {
                // Ensure directory exists
                if (!Storage::disk('public')->exists($directory)) {
                    Storage::disk('public')->makeDirectory($directory);
                }

                foreach ($types as $type) {
                    $fieldName = 'image_' . str_replace(' ', '_', strtolower($type['name']));
                    $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);

                    if ($request->hasFile($fieldName)) {
                        $file = $request->file($fieldName);
                        $path = Storage::disk('public')->putFile($directory, $file);

                        $imageLat = $request->input($fieldName . '_latitude');
                        $imageLng = $request->input($fieldName . '_longitude');

                        LeadHasImages::create([
                            'lead_id' => $lead->id,
                            'image_path' => $path,
                            'image_type' => $type['name'],
                            'latitude' => $imageLat,
                            'longitude' => $imageLng,
                        ]);
                    }
                }
            }
        }
    }
}

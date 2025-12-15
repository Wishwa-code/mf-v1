<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerLeadRequest;
use App\Models\AppSettings;
use App\Models\CustomerLead;
use App\Models\LeadHasImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CustomerLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $leads = CustomerLead::with('images')->get();
            
            // Get image types from app_settings
            $imageTypesSetting = AppSettings::where('key', 'image_types')->value('value');
            $imageTypes = [];
            
            if ($imageTypesSetting) {
                $decoded = json_decode($imageTypesSetting, true);
                $imageTypes = is_array($decoded) ? $decoded : [];
            }

            return view('pages.leads.index', compact('leads', 'imageTypes'));
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()],500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerLeadRequest $request)
    {
        DB::beginTransaction();
        try {
            // Create the lead
            $lead = new CustomerLead();
            $lead->full_name = $request->full_name;
            $lead->phone_number = $request->phone_number;
            $lead->email = $request->email;
            $lead->address = $request->address;
            $lead->notes = $request->notes;
            $lead->latitude = $request->latitude;
            $lead->longitude = $request->longitude;
            $lead->created_by = session('userid', 1);
            $lead->save();

            // Get image types from app_settings
            $imageTypesSetting = AppSettings::where('key', 'image_types')->value('value');
            
            if ($imageTypesSetting) {
                $imageTypes = json_decode($imageTypesSetting, true);
                
                if (is_array($imageTypes) && count($imageTypes) > 0) {
                    // Ensure directory exists
                    $directory = 'lead_images';
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }

                    // Process each image type
                    foreach ($imageTypes as $imageType) {
                        $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                        $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);
                        
                        if ($request->hasFile($fieldName)) {
                            $file = $request->file($fieldName);
                            $path = Storage::disk('public')->putFile($directory, $file);
                            
                            // Get image location if provided
                            $imageLat = $request->input($fieldName . '_latitude');
                            $imageLng = $request->input($fieldName . '_longitude');
                            
                            // Save image record
                            LeadHasImages::create([
                                'lead_id' => $lead->id,
                                'image_path' => $path,
                                'image_type' => $imageType['name'],
                                'latitude' => $imageLat,
                                'longitude' => $imageLng,
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lead saved successfully',
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
     * Display the specified resource.
     */
    public function show(CustomerLead $customerLead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerLead $customerLead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerLead $customerLead)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerLead $customerLead)
    {
        //
    }
}

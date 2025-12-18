<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerLeadRequest;
use App\Models\AppSettings;
use App\Models\CustomerLead;
use App\Models\LeadHasImages;
use App\Models\BusinessCategory;
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
            $leads = CustomerLead::with(['images', 'businessCategory'])->get();
        $businessCategories = BusinessCategory::all();
            
            // Get image types from app_settings
            $imageTypesSetting = AppSettings::where('key', 'image_types')->value('value');
            $imageTypes = [];
            
            if ($imageTypesSetting) {
                $decoded = json_decode($imageTypesSetting, true);
                $imageTypes = is_array($decoded) ? $decoded : [];
            }

            return view('pages.leads.create', compact('leads', 'imageTypes','businessCategories'));
        } catch (\Exception $e) {
            return response()->json(['message'=>$e->getMessage()],500);
        }
    }

    /**
     * List leads that are waiting for approval.
     *
     * This shows only leads that are still in "pending" status.
     * The view uses jQuery DataTables to render the table.
     */
    public function approvalIndex()
    {
        return view('pages.leads.approvals');
    }

    /**
     * JSON data source for the leads approval DataTable.
     */
    public function approvalData(Request $request)
    {
        // For now we keep things simple and just return all pending leads.
        // If the dataset grows large, we can add pagination and filters later.
        $leads = CustomerLead::where('status', 'pending')
            ->orderByDesc('created_at_lead')
            ->get();

        $data = $leads->map(function (CustomerLead $lead) {
            return [
                'id'          => $lead->id,
                'full_name'   => $lead->full_name,
                'phone_number'=> $lead->phone_number,
                'email'       => $lead->email,
                'type'        => $lead->type,
                'periods'     => $lead->periods,
                'status'      => $lead->status,
                'address'     => $lead->address,
                'latitude'    => $lead->latitude,
                'longitude'   => $lead->longitude,
                'created_at'  => optional($lead->created_at_lead)->format('Y-m-d H:i'),
            ];
        })->values();

        return response()->json([
            'data' => $data,
        ]);
    }
    
    /**
     * Handle form submission and return JSON response for AJAX
     */
    public function create(Request $request)
    {
        // Get image types from app_settings
        $imageTypesSetting = AppSettings::where('key', 'image_types')->value('value');
        $imageTypes = [];
        
        if ($imageTypesSetting) {
            $decoded = json_decode($imageTypesSetting, true);
            $imageTypes = is_array($decoded) ? $decoded : [];
        }

        $businessCategories = BusinessCategory::all();

        return view('pages.leads.create', compact('imageTypes', 'businessCategories'));
    }

    

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerLeadRequest $request)
    {
        DB::beginTransaction();
        try {
            // Get validated data from request
            $data = $request->validated();
            
            // Add additional fields
            $data['created_at_lead'] = now();
            $data['created_by'] = session('userid', 1);
            
            // Create the lead using create method
            $lead = CustomerLead::create($data);

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
     * Display a single lead with full details and images.
     */
    public function show( $customerLead)
    {
        $customerLead = CustomerLead::findOrFail($customerLead);
        $lead = $customerLead->load(['images', 'businessCategory']);

        return view('pages.leads.show', compact('lead'));
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

    /**
     * Approve a lead after verifying that it has a valid location.
     */
    public function approve(Request $request, CustomerLead $lead)
    {
        // Require location before approval
        if (empty($lead->latitude) || empty($lead->longitude)) {
            return response()->json([
                'success' => false,
                'message' => 'Lead location is missing. Capture latitude and longitude before approval.',
            ], 422);
        }

        $lead->status = 'pending-approved';
        $lead->updated_by = session('userid', 1);
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead approved successfully.',
        ]);
    }

    /**
     * Show the Verify Action Page (Dropdown + Form).
     */
    public function verifyActionPage()
    {
        // Get leads that are approved but NOT visited yet
        $leads = CustomerLead::where('status', 'pending-approved')
            ->where('is_visited', 0) // Only unvisited
            ->orderBy('full_name')
            ->get();
            
        return view('pages.leads.verify_action', compact('leads'));
    }

    /**
     * Show the Verified Leads List Page (Table).
     */
    public function verifiedListPage()
    {
        return view('pages.leads.verified_list');
    }

    /**
     * API to get lead details for the dropdown.
     */
    public function getLeadDetails(CustomerLead $lead)
    {
        return response()->json([
            'success' => true,
            'id' => $lead->id,
            'full_name' => $lead->full_name,
            'address' => $lead->address,
            'phone_number' => $lead->phone_number,
            'latitude' => $lead->latitude,
            'longitude' => $lead->longitude,
        ]);
    }

    /**
     * JSON data for verified leads table using Yajra DataTables.
     */
    public function verifiedData(Request $request)
    {
        $leads = CustomerLead::where('status', 'pending-approved')
            ->orderByDesc('created_at_lead');

        return \Yajra\DataTables\Facades\DataTables::of($leads)
            ->addColumn('action', function ($row) {
                 // No action button on the list view anymore, maybe just a 'View' or disabled 'Visited'
                if ($row->is_visited) {
                    return '<span class="badge bg-success">VISITED</span>';
                }
                return '<span class="badge bg-warning text-dark">PENDING VISIT</span>';
            })
            ->editColumn('status', function ($row) {
                return '<span class="badge bg-info">' . strtoupper($row->status) . '</span>';
            })
            ->editColumn('is_visited', function ($row) {
                if ($row->is_visited) {
                    return '<span class="badge bg-success">VISITED</span>';
                }
                return '<span class="badge bg-warning text-dark">PENDING</span>';
            })
            ->rawColumns(['action', 'status', 'is_visited']) // Allow HTML in these columns
            ->make(true);
    }

    /**
     * Mark a lead as verified/visited.
     */
    public function markAsVisited(Request $request, CustomerLead $lead)
    {
        $request->validate([
            'visited_latitude'  => 'required|numeric',
            'visited_longitude' => 'required|numeric',
            'visit_notes'       => 'required|string|max:5000',
        ]);

        $lead->is_visited = 1;
        $lead->visited_latitude = $request->visited_latitude;
        $lead->visited_longitude = $request->visited_longitude;
        $lead->visit_notes = $request->visit_notes;
        $lead->updated_by = session('userid', 1);
        $lead->save();

        return response()->json([
            'success' => true,
            'message' => 'Lead marked as visited successfully.',
        ]);
    }
}

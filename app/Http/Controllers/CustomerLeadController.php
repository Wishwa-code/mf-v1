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

            // Get Agreement Image Types
            $agreementImageTypesSetting = AppSettings::where('key', 'agreement_image_types')->value('value');
            $agreementImageTypes = [];
            
            if ($agreementImageTypesSetting) {
                $decodedAgreement = json_decode($agreementImageTypesSetting, true);
                $agreementImageTypes = is_array($decodedAgreement) ? $decodedAgreement : [];
            }

            return view('pages.leads.create', compact('leads', 'imageTypes', 'businessCategories', 'agreementImageTypes'));
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
        $images = LeadHasImages::where('lead_id', $customerLead)->get();

        return view('pages.leads.show', compact('lead','images'));
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
        $leads = CustomerLead::orderByDesc('created_at_lead');

        return \Yajra\DataTables\Facades\DataTables::of($leads)
            ->addColumn('action', function ($row) {
                $btn = '';
                
                // View Details Button (Triggers Modal)
                $btn .= '<button onclick="viewLeadModal('.$row->id.')" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 me-1" style="border-radius: 6px;" title="View Details"><i class="bi bi-eye"></i> View</button>';

                // Reject Button (if not visited)
                if (!$row->is_visited && $row->status != 'rejected') { 
                    $btn .= '<button onclick="rejectLead('.$row->id.')" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1" style="border-radius: 6px;" title="Reject Lead"><i class="bi bi-x"></i> Reject</button>';
                }
                
                return $btn;
            })  
            ->editColumn('status', function ($row) {
                $status = $row->status;
                $badgeClass = 'bg-soft-secondary text-secondary';
                if ($status == 'pending') $badgeClass = 'bg-soft-warning text-warning';
                elseif ($status == 'pending-approved') $badgeClass = 'bg-soft-info text-info';
                elseif ($status == 'agreement-signed') $badgeClass = 'bg-soft-primary text-primary';
                elseif ($status == 'loan-issued') $badgeClass = 'bg-soft-success text-success';
                elseif ($status == 'rejected') $badgeClass = 'bg-soft-danger text-danger';
                
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($status) . '</span>';
            })
            ->editColumn('is_visited', function ($row) {
                if ($row->is_visited) {
                    return '<span class="badge bg-soft-success text-success">Visited</span>';
                }
                return '<span class="badge bg-soft-secondary text-secondary">Not Visited</span>';
            })
            ->rawColumns(['action', 'status', 'is_visited'])
            ->make(true);
    }

    /**
     * Display the specified resource for verified leads (Modal Content).
     */
    public function getVerifiedLeadDetailsModal($id)
    {
        $lead = CustomerLead::with('images', 'businessCategory')->findOrFail($id);
        
        return view('pages.leads.partials.verified_lead_modal_content', compact('lead'));
    }

    /**
     * Display the specified resource for verified leads (Full Page).
     */
    public function showVerifiedDetails($id)
    {
        $lead = CustomerLead::with('images')->findOrFail($id);
        $images = $lead->images;

        return view('pages.leads.show', compact('lead', 'images'));
    }

    /**
     * Display the specified resource.
     */
    // public function show($id)
    // {
    //     $lead = CustomerLead::findOrFail($id);
    //     $images = \App\Models\LeadHasImages::where('lead_id', $id)->get();

    //     return view('pages.leads.show', compact('lead', 'images'));
    // }

    /**
     * Reject the specified lead.
     */
    public function reject($id)
    {
        $lead = CustomerLead::find($id);
        if ($lead) {
            $lead->status = 'rejected';
            $lead->save();
            return response()->json(['success' => true, 'message' => 'Lead rejected successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Lead not found.'], 404);
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

    /**
     * Show global map with all leads.
     */
    public function globalMap()
    {
        // Get all leads that have a location captured
        $leads = CustomerLead::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'full_name', 'address', 'latitude', 'longitude', 'status', 'is_visited')
            ->get();

        return view('pages.leads.global_map', compact('leads'));
    }

    /**
     * Handle Agreement Image Upload.
     */
    public function uploadAgreementImages(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:customer_leads,id',
        ]);

        // Fetch Agreement Image Types to build dynamic validation rules
        $imageTypesSetting = AppSettings::where('key', 'agreement_image_types')->value('value');
        $validationRules = [];
        $customAttributes = [];
        $imageTypes = [];

        if ($imageTypesSetting) {
            $imageTypes = json_decode($imageTypesSetting, true);
            if (is_array($imageTypes)) {
                foreach ($imageTypes as $imageType) {
                    $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                    $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);
                    
                    if (isset($imageType['is_required']) && $imageType['is_required']) {
                        $validationRules[$fieldName] = 'required|image|mimes:jpeg,png,jpg,gif';
                    } else {
                        $validationRules[$fieldName] = 'nullable|image|mimes:jpeg,png,jpg,gif';
                    }
                    $customAttributes[$fieldName] = $imageType['name'];
                }
            }
        }

        // Apply dynamic validation
        $request->validate($validationRules, [], $customAttributes);

        DB::beginTransaction();
        try {
            $leadId = $request->input('lead_id');
            $directory = 'agreement_images'; // Separate directory for agreements
            
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

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
                        'lead_id' => $leadId,
                        'image_path' => $path,
                        'image_type' => $imageType['name'], 
                        'latitude' => $imageLat,
                        'longitude' => $imageLng,
                    ]);
                }
            }

            // Update Lead Status to 'agreement-signed'
            $lead = CustomerLead::find($leadId);
            if ($lead) {
                $lead->status = 'agreement-signed';
                $lead->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Agreement images uploaded and lead status updated successfully.',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
             return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', Arr::flatten($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images: ' . $e->getMessage()
            ], 500);
        }
    }
}

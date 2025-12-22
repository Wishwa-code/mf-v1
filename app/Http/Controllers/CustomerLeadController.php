<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerLeadRequest;
use App\Models\AppSettings;
use App\Models\CustomerLead;
use App\Models\LeadHasImages;
use App\Models\BusinessCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateCustomerLeadRequest;
use Yajra\DataTables\Facades\DataTables;

class CustomerLeadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $leads = CustomerLead::with(['images', 'businessCategory'])->where('status', 'pending-approved')->get();
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

            // Get Guardian Image Types
            $guardianImageTypesSetting = AppSettings::where('key', 'guardian_image_types')->value('value');
            $guardianImageTypes = [];
            if ($guardianImageTypesSetting) {
                $decodedGuardian = json_decode($guardianImageTypesSetting, true);
                $guardianImageTypes = is_array($decodedGuardian) ? $decodedGuardian : [];
            }

            // Get Guarantor Image Types
            $guarantorImageTypesSetting = AppSettings::where('key', 'guarantor_image_types')->value('value');
            $guarantorImageTypes = [];
            if ($guarantorImageTypesSetting) {
                $decodedGuarantor = json_decode($guarantorImageTypesSetting, true);
                $guarantorImageTypes = is_array($decodedGuarantor) ? $decodedGuarantor : [];
            }

            return view('pages.leads.create', compact('leads', 'imageTypes', 'businessCategories', 'agreementImageTypes', 'guardianImageTypes', 'guarantorImageTypes'));
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
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
        $leads = CustomerLead::where('status', 'pending')
            ->orderByDesc('created_at_lead')
            ->get();

        $data = $leads->map(function (CustomerLead $lead) {
            return [
                'id'          => $lead->id,
                'full_name'   => $lead->full_name,
                'phone_number' => $lead->phone_number,
                'email'       => $lead->email,
                'type'        => $lead->type,
                'periods'     => $lead->periods,
                'status'      => $lead->status,
                'address'     => $lead->address,
                'latitude'    => $lead->latitude,
                'longitude'   => $lead->longitude,
                'created_at'  => optional($lead->created_at_lead)->format('Y-m-d H:i'),
                'route_name'  => optional($lead->route)->name,
                'source'      => $lead->source,
                'district'    => $lead->district,
                'city'        => $lead->city,
            ];
        })->values();

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Handle form submission and return JSON response for AJAX
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

        return view('pages.leads.create', compact('imageTypes', 'guardianImageTypes', 'guarantorImageTypes', 'businessCategories'));
    }

    public function agreement_index()
    {
        $leads = CustomerLead::where('status', 'pending-approved')->where('is_visited','!=',0)->get();
        $agreementImageTypesSetting = AppSettings::where('key', 'agreement_image_types')->value('value');
        $agreementImageTypes = $agreementImageTypesSetting ? json_decode($agreementImageTypesSetting, true) : [];

        return view('pages.leads.agreements', compact('leads', 'agreementImageTypes'));
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
            $lead->logActivity('created', 'Lead created successfully', ['attributes' => $lead->toArray()]);

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

            // Process Guardian Images
            $guardianImageTypesSetting = AppSettings::where('key', 'guardian_image_types')->value('value');
            if ($guardianImageTypesSetting) {
                $guardianImageTypes = json_decode($guardianImageTypesSetting, true);
                if (is_array($guardianImageTypes) && count($guardianImageTypes) > 0) {
                    // Ensure directory exists
                    $directory = 'guardian_images';
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }

                    foreach ($guardianImageTypes as $imageType) {
                        $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                        $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);

                        if ($request->hasFile($fieldName)) {
                            $file = $request->file($fieldName);
                            $path = Storage::disk('public')->putFile($directory, $file);

                            $imageLat = $request->input($fieldName . '_latitude');
                            $imageLng = $request->input($fieldName . '_longitude');

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

            // Process Guarantor Images
            $guarantorImageTypesSetting = AppSettings::where('key', 'guarantor_image_types')->value('value');
            if ($guarantorImageTypesSetting) {
                $guarantorImageTypes = json_decode($guarantorImageTypesSetting, true);
                if (is_array($guarantorImageTypes) && count($guarantorImageTypes) > 0) {
                    // Ensure directory exists
                    $directory = 'guarantor_images';
                    if (!Storage::disk('public')->exists($directory)) {
                        Storage::disk('public')->makeDirectory($directory);
                    }

                    foreach ($guarantorImageTypes as $imageType) {
                        $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                        $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);

                        if ($request->hasFile($fieldName)) {
                            $file = $request->file($fieldName);
                            $path = Storage::disk('public')->putFile($directory, $file);

                            $imageLat = $request->input($fieldName . '_latitude');
                            $imageLng = $request->input($fieldName . '_longitude');

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
    public function show($customerLead)
    {
        $customerLead = CustomerLead::findOrFail($customerLead);
        $lead = $customerLead->load(['images', 'businessCategory']);
        $images = LeadHasImages::where('lead_id', $customerLead)->get();

        return view('pages.leads.show', compact('lead', 'images'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerLead $lead)
    {
        // Get image types from app_settings
        $imageTypesSetting = AppSettings::where('key', 'image_types')->value('value');
        $imageTypes = $imageTypesSetting ? json_decode($imageTypesSetting, true) : [];

        $guardianImageTypesSetting = AppSettings::where('key', 'guardian_image_types')->value('value');
        $guardianImageTypes = $guardianImageTypesSetting ? json_decode($guardianImageTypesSetting, true) : [];

        $guarantorImageTypesSetting = AppSettings::where('key', 'guarantor_image_types')->value('value');
        $guarantorImageTypes = $guarantorImageTypesSetting ? json_decode($guarantorImageTypesSetting, true) : [];

        $businessCategories = BusinessCategory::all();
        $routes = \App\Models\Route::all();

        if ($lead->source !== 'recovery-officer') {
            return view('pages.leads.edit_online', compact('lead', 'imageTypes', 'guardianImageTypes', 'guarantorImageTypes', 'businessCategories', 'routes'));
        }

        // Pass the lead ID to the view so JS can fetch data
        return view('pages.leads.edit', compact('lead', 'imageTypes', 'guardianImageTypes', 'guarantorImageTypes', 'businessCategories', 'routes'));
    }

    /**
     * Get Lead Data for Edit (AJAX)
     */
    public function getLeadData(CustomerLead $lead)
    {
        $lead->load(['images', 'businessCategory']);
        return response()->json([
            'success' => true,
            'lead' => $lead,
            'images' => $lead->images->groupBy('image_type')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerLeadRequest $request, CustomerLead $lead)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $lead->fill($data);

            // Calculate changes for standard logging
            $logProperties = [];
            if ($lead->isDirty()) {
                $dirty = $lead->getDirty();
                $original = array_intersect_key($lead->getOriginal(), $dirty);

                $logProperties['attributes'] = $dirty;
                $logProperties['old'] = $original;
            }

            $lead->save(); // Use save instead of update to persist the filled data

            // Handle Image Updates
            $imageUpdates = [];
            $imageUpdates = array_merge($imageUpdates, $this->processImageUpdates($request, $lead, 'image_types', 'lead_images'));
            $imageUpdates = array_merge($imageUpdates, $this->processImageUpdates($request, $lead, 'guardian_image_types', 'guardian_images'));
            $imageUpdates = array_merge($imageUpdates, $this->processImageUpdates($request, $lead, 'guarantor_image_types', 'guarantor_images'));

            if (!empty($logProperties) || !empty($imageUpdates)) {
                if (!empty($imageUpdates)) {
                    $logProperties['updated_images'] = $imageUpdates;
                }

                $description = 'Lead updated';
                if (!empty($imageUpdates)) {
                    $description .= ' (Images: ' . implode(', ', $imageUpdates) . ')';
                }

                $lead->logActivity('updated', $description, $logProperties);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lead updated successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update lead: ' . $e->getMessage()
            ], 500);
        }
    }

    private function processImageUpdates($request, $lead, $settingKey, $directory)
    {
        $updatedImages = [];
        $setting = AppSettings::where('key', $settingKey)->value('value');
        if ($setting) {
            $types = json_decode($setting, true);
            if (is_array($types)) {
                foreach ($types as $type) {
                    $fieldName = 'image_' . str_replace(' ', '_', strtolower($type['name']));
                    $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);

                    if ($request->hasFile($fieldName)) {
                        $file = $request->file($fieldName);
                        $path = Storage::disk('public')->putFile($directory, $file);

                        $imageLat = $request->input($fieldName . '_latitude');
                        $imageLng = $request->input($fieldName . '_longitude');

                        // Delete old image of this type if exists
                        LeadHasImages::where('lead_id', $lead->id)->where('image_type', $type['name'])->delete();

                        LeadHasImages::create([
                            'lead_id' => $lead->id,
                            'image_path' => $path,
                            'image_type' => $type['name'],
                            'latitude' => $imageLat,
                            'longitude' => $imageLng,
                        ]);

                        $updatedImages[] = $type['name'];
                    }
                }
            }
        }
        return $updatedImages;
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

        $lead->logActivity('approved', 'Lead approved');

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
        $leads = CustomerLead::with('route')->orderByDesc('created_at_lead');

        return \Yajra\DataTables\Facades\DataTables::of($leads)
            ->addColumn('route_name', function ($row) {
                return $row->route ? $row->route->name : '-';
            })
            ->addColumn('source', function ($row) {
                return $row->source ? ucfirst($row->source) : '-';
            })
            ->addColumn('district', function ($row) {
                return $row->district ?? '-';
            })
            ->addColumn('city', function ($row) {
                return $row->city ?? '-';
            })
            ->addColumn('action', function ($row) {
                $btn = '';

                // Edit Button
                $btn .= '<a href="' . route('leads.edit', $row->id) . '" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1 me-1" style="border-radius: 6px;" title="Edit Lead"><i class="bi bi-pencil-square"></i> Edit</a>';

                // View Details Button (Triggers Modal)
                $btn .= '<button onclick="viewLeadModal(' . $row->id . ')" class="btn btn-sm btn-outline-info d-inline-flex align-items-center gap-1 me-1" style="border-radius: 6px;" title="View Details"><i class="bi bi-eye"></i> View</button>';

                // Reject Button (if not visited)
                if (!$row->is_visited && $row->status != 'rejected') {
                    $btn .= '<button onclick="rejectLead(' . $row->id . ')" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1" style="border-radius: 6px;" title="Reject Lead"><i class="bi bi-x"></i> Reject</button>';
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
            $lead->logActivity('rejected', 'Lead rejected');
            return response()->json(['success' => true, 'message' => 'Lead rejected successfully.']);
        }
        return response()->json(['success' => false, 'message' => 'Lead not found.'], 404);
    }


    /**
     * Mark a lead as verified/visited.
     */
    public function markAsVisited(Request $request, CustomerLead $lead)
    {
        try {
            //code...
            $request->validate([
                'visited_latitude'  => 'required|numeric',
                'visited_longitude' => 'required|numeric',
                'visit_notes'       => 'required|string|max:5000',
                'verification_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB
            ]);

            $lead->is_visited = 1;
            $lead->visited_latitude = $request->visited_latitude;
            $lead->visited_longitude = $request->visited_longitude;
            $lead->visit_notes = $request->visit_notes;
            $lead->updated_by = session('userid', 1);

            // Handle Image Upload
            if ($request->hasFile('verification_image')) {
                $file = $request->file('verification_image');
                $path = \Illuminate\Support\Facades\Storage::disk('public')->put('verification_images', $file);
                $lead->verification_image = $path;
            }

            $lead->save();

            $logProperties = [
                'visited_latitude' => $request->visited_latitude,
                'visited_longitude' => $request->visited_longitude,
                'visit_notes' => $request->visit_notes
            ];

            if (isset($lead->verification_image)) {
                $logProperties['verification_image'] = $lead->verification_image;
            }

            $lead->logActivity('visited', 'Lead marked as visited', $logProperties);

            return response()->json([
                'success' => true,
                'message' => 'Lead marked as visited successfully.',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
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
                $lead->logActivity('agreement_signed', 'Agreement images uploaded and lead status updated');
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

    /**
     * Display a listing of lead activities.
     */
    public function activityLogs()
    {
        $users = \App\Models\User::orderBy('Full_Name')->get();
        $actions = \App\Models\LeadActivity::distinct()->pluck('action');
        return view('pages.leads.activity_logs', compact('users', 'actions'));
    }

    public function activityLogsData(Request $request)
    {
        $query = \App\Models\LeadActivity::with(['lead', 'user'])->select('lead_activities.*');

        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->has('user_id') && !empty($request->user_id) && $request->user_id != 'all') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('action') && !empty($request->action) && $request->action != 'all') {
            $query->where('action', $request->action);
        }

        return DataTables::of($query)
            ->editColumn('created_at', function ($activity) {
                return $activity->created_at->format('Y-m-d H:i:s');
            })
            ->editColumn('user_id', function ($activity) {
                return $activity->user ? $activity->user->Full_Name : 'System';
            })
            ->editColumn('lead_id', function ($activity) {
                return $activity->lead ? $activity->lead->full_name : 'Unknown Lead';
            })
            ->editColumn('action', function ($activity) {
                return ucfirst(str_replace('_', ' ', $activity->action));
            })
            ->addColumn('action_btn', function ($activity) {
                return '<button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="viewActivityDetails(' . $activity->id . ')"><i class="bi bi-eye-fill me-1"></i> Detailed</button>';
            })
            ->rawColumns(['action_btn'])
            ->make(true);
    }

    public function getActivityDetails($id)
    {
        $activity = \App\Models\LeadActivity::with(['lead', 'user'])->findOrFail($id);
        return response()->json($activity);
    }
}

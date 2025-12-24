@extends('layout.admin')

@section('content')
<div class="container-fluid pb-5">
    <!-- Page Header -->
    <div class="row align-items-center mb-4 mt-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-1 fw-bold fs-3 text-dark">Edit Online Lead</h4>
                    <ol class="breadcrumb m-0 small text-muted">
                        <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('leads.verifiedList') }}" class="text-decoration-none text-muted">Verified Leads</a></li>
                        <li class="breadcrumb-item active text-primary">Edit Lead</li>
                    </ol>
                </div>
                <div>
                    <a href="{{ route('leads.verifiedList') }}" class="btn btn-light border btn-sm rounded-pill px-3 fw-bold">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Check for validation errors -->
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Modern Styles -->
    <style>
        /* Card Styling */
        .card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #fff;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        /* Form Controls */
        .form-label {
            font-weight: 600;
            color: #343a40;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .form-control,
        .form-select {
            height: 50px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 0.6rem 1rem;
            font-size: 0.95rem;
            background-color: #f8f9fa;
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #556ee6;
            box-shadow: 0 0 0 0.25rem rgba(85, 110, 230, 0.1);
            background-color: #fff;
        }

        textarea.form-control {
            height: auto;
            min-height: 120px;
        }

        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            height: 50px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
        }

        .select2-container--default .select2-selection--single:focus-within {
            border-color: #556ee6;
            box-shadow: 0 0 0 0.25rem rgba(85, 110, 230, 0.1);
            background-color: #fff;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 50px;
            right: 10px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 15px;
            font-size: 0.95rem;
            color: #495057;
            font-weight: 500;
        }

        .select2-dropdown {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-top: 8px;
        }

        .select2-search__field {
            border-radius: 8px !important;
            padding: 8px 12px !important;
        }

        /* Upload Areas */
        .upload-area {
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            padding: 1.5rem;
            background: #fff;
            transition: all 0.2s;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .upload-area:hover {
            border-color: #556ee6;
            background: #f8f9fa;
        }

        /* Buttons */
        .btn-modern {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-modern:active {
            transform: scale(0.98);
        }

        /* Section Headers */
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            font-size: 1.25rem;
        }

        /* Card Background Variants */
        /* Lead Info: Deep Purple - Distinct */
        .card-modern.bg-card-primary {
            background: linear-gradient(135deg, rgba(117, 106, 182, 0.15) 0%, rgba(117, 106, 182, 0.35) 100%) !important;
            border: 1px solid rgba(117, 106, 182, 0.4) !important;
        }

        /* Required Docs: Light Violet - Distinct */
        .card-modern.bg-card-info {
            background: linear-gradient(135deg, rgba(172, 135, 197, 0.15) 0%, rgba(172, 135, 197, 0.35) 100%) !important;
            border: 1px solid rgba(172, 135, 197, 0.4) !important;
        }

        /* Guardian: Pinkish Lavender - Distinct */
        .card-modern.bg-card-warning {
            background: linear-gradient(135deg, rgba(224, 174, 208, 0.15) 0%, rgba(224, 174, 208, 0.35) 100%) !important;
            border: 1px solid rgba(224, 174, 208, 0.4) !important;
        }

        /* Guarantor: Very Light Pink - Distinct */
        .card-modern.bg-card-secondary {
            background: linear-gradient(135deg, #ffeaea 0%, #ffcdd2 100%) !important;
            border: 1px solid #ef9a9a !important;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form id="leadForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="lead_id" id="lead_id" value="{{ $lead->id }}">
                <input type="hidden" id="current_province_name" value="{{ $lead->district }}">


                {{-- SECTION 1: LEAD INFORMATION --}}
                <div class="card card-modern bg-card-primary">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="section-icon bg-primary-subtle text-primary">
                                <i class="bi bi-person-lines-fill"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">Lead Information</h5>
                        </div>

                        <div class="row g-4">
                            <!-- Route Selection First -->
                            <div class="col-md-6">
                                <label class="form-label">Route</label>
                                <select name="route_id" class="form-control select2-search @error('route_id') is-invalid @enderror">
                                    <option value="">Select Route</option>
                                    @if(isset($routes))
                                    @foreach($routes as $route)
                                    <option value="{{ $route->id_route }}"
                                        {{ (old('route_id') ?? $lead->route_id) == $route->id_route ? 'selected' : '' }}>
                                        {{ $route->name }} - {{ $route->root_code }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Source Selection Second -->
                            <div class="col-md-6">
                                <label class="form-label">Source</label>
                                <select name="source" class="form-control select2-search @error('source') is-invalid @enderror">
                                    <option value="">Select Source</option>
                                    @foreach(['online', 'facebook', 'whatsapp', 'instagram', 'tiktok', 'website', 'other'] as $src)
                                    <option value="{{ $src }}" {{ (old('source') ?? $lead->source) == $src ? 'selected' : '' }}>{{ ucfirst($src) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Recovery Officer Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Recovery Officer</label>
                                <input type="hidden" id="current_recovery_officer_id" value="{{ old('recovery_officer_id') ?? $lead->recovery_officer_id }}">
                                <select name="recovery_officer_id" id="recovery_officer_id" class="form-control select2-officers @error('recovery_officer_id') is-invalid @enderror">
                                    <option value="">Select Route First</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" placeholder="Ex: John Doe" value="{{ old('full_name') ?? $lead->full_name }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="phone_number" class="form-control" placeholder="Ex: 0771234567" value="{{ old('phone_number') ?? $lead->phone_number }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') ?? $lead->email }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Loan Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control select2-search">
                                    <option value="">Select Type</option>
                                    @foreach(['group', 'individual', 'business', 'leasing'] as $type)
                                    <option value="{{ $type }}" {{ (old('type') ?? $lead->type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Business Category <span class="text-danger">*</span></label>
                                <select name="business_category_id" class="form-control select2-search">
                                    <option value="">Select Category</option>
                                    @if(isset($businessCategories))
                                    @foreach($businessCategories as $category)
                                    <option value="{{ $category->id }}" {{ (old('business_category_id') ?? $lead->business_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Loan Amount <span class="text-danger">*</span></label>
                                <input type="number" name="loan_amount" step="0.01" class="form-control" placeholder="0.00" value="{{ old('loan_amount') ?? $lead->loan_amount }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Periods (Months) <span class="text-danger">*</span></label>
                                <input type="number" name="periods" class="form-control" placeholder="12" value="{{ old('periods') ?? $lead->periods }}" min="1">
                            </div>

                            <div class="col-md-6">
                                <label for="state" class="form-label">Province / District</label>
                                <input type="hidden" name="district" id="district_text" value="{{ $lead->district }}">
                                <select id="state" class="form-control select2-districts">
                                    <option value="">Select Province/District</option>
                                    @if($lead->district)
                                    <option value="{{ $lead->district }}" selected>{{ $lead->district }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="city" class="form-label">City / Town</label>
                                <input type="hidden" name="city" id="city_text" value="{{ $lead->city }}">
                                <select id="city" class="form-control select2-cities">
                                    <option value="">Select City / Town</option>
                                    @if($lead->city)
                                    <option value="{{ $lead->city }}" selected>{{ $lead->city }}</option>
                                    @endif
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address') ?? $lead->address }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notes / Remarks</label>
                                <textarea name="notes" class="form-control" rows="3">{{ old('notes') ?? $lead->notes }}</textarea>
                            </div>

                            {{-- Optional Location Display --}}
                            <div class="col-12">
                                <label class="form-label">Location (Read-Only/Update)</label>
                                <div class="p-3 bg-light rounded-3 border d-flex flex-column flex-md-row gap-3 align-items-md-end">
                                    <div class="flex-grow-1 row g-3">
                                        <div class="col-12 col-md-6">
                                            <small class="text-muted fw-bold">Latitude</small>
                                            <input type="text" name="latitude" id="lead_lat" class="form-control" value="{{ $lead->latitude }}">
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <small class="text-muted fw-bold">Longitude</small>
                                            <input type="text" name="longitude" id="lead_lng" class="form-control" value="{{ $lead->longitude }}">
                                        </div>
                                    </div>
                                    <div class="d-grid d-md-block">
                                        <button type="button" class="btn btn-primary btn-modern w-100" onclick="getLeadLocation()">
                                            <i class="bi bi-geo-alt-fill"></i> Get GPS
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- IMAGES SECTION --}}
                @if(isset($imageTypes) && count($imageTypes) > 0)
                <div class="card card-modern bg-card-info">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="section-icon bg-info-subtle text-info">
                                <i class="bi bi-file-earmark-image"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">Identity Documents</h5>
                        </div>
                        <div class="row g-4">
                            @foreach($imageTypes as $imageType)
                            @php
                            $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                            $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);

                            // Check if image exists (multiple)
                            $existingImages = $lead->images->where('image_type', $imageType['name']);
                            @endphp
                            <div class="col-md-6">
                                <div class="upload-area">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <label class="form-label mb-0">{{ $imageType['name'] }}</label>
                                    </div>
                                    <input type="file" name="{{ $fieldName }}[]" id="{{ $fieldName }}" class="d-none" accept="image/*" multiple
                                        onchange="handleImageUpload(this, '{{ $fieldName }}_preview')"
                                        data-image-type="{{ $imageType['name'] }}">

                                    <div class="mt-auto">
                                        <div class="d-flex flex-column flex-sm-row gap-2 mb-3">
                                            <button type="button" class="btn btn-light btn-modern flex-grow-1 border text-dark py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="triggerFileSelect('{{ $fieldName }}')">
                                                <i class="bi bi-folder2-open me-1"></i> {{ $existingImages->count() > 0 ? 'Add More' : 'Upload' }}
                                            </button>
                                        </div>
                                        <div class="text-center" style="min-height: 100px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 8px;">
                                            <div id="{{ $fieldName }}_preview_container" class="d-flex flex-wrap justify-content-center gap-2 p-2">
                                                @if($existingImages->count() > 0)
                                                @foreach($existingImages as $img)
                                                <div class="position-relative d-inline-block">
                                                    <a href="{{ Storage::url($img->image_path) }}" target="_blank">
                                                        <img src="{{ Storage::url($img->image_path) }}" class="rounded shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;">
                                                    </a>
                                                </div>
                                                @endforeach
                                                @endif
                                                <span class="text-muted small d-block my-auto {{ $existingImages->count() > 0 ? 'd-none' : '' }}" id="{{ $fieldName }}_placeholder">No image</span>
                                            </div>
                                        </div>
                                        @error($fieldName) <div class="text-danger small mt-2 text-center fw-bold">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- SECTION 3: GUARDIAN INFO --}}
                @if(isset($guardianImageTypes) && count($guardianImageTypes) > 0)
                <div class="card card-modern bg-card-warning">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="section-icon bg-warning-subtle text-warning">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">Guardian Documents</h5>
                        </div>

                        <div class="row g-4">
                            @foreach($guardianImageTypes as $imageType)
                            @php
                            $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                            $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);

                            // Check if image exists
                            $existingImage = $lead->images->where('image_type', $imageType['name'])->first();
                            $existingUrl = $existingImage ? Storage::url($existingImage->image_path) : null;
                            @endphp
                            <div class="col-md-6">
                                <div class="upload-area">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <label class="form-label mb-0">{{ $imageType['name'] }}</label>
                                    </div>

                                    <input type="file" name="{{ $fieldName }}" id="{{ $fieldName }}" class="d-none" accept="image/*"
                                        onchange="handleImageUpload(this, '{{ $fieldName }}_preview')"
                                        data-image-type="{{ $imageType['name'] }}">

                                    <div class="mt-auto">
                                        <div class="d-flex flex-column flex-sm-row gap-2 mb-3">
                                            <button type="button" class="btn btn-light btn-modern flex-grow-1 border text-dark py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="triggerFileSelect('{{ $fieldName }}')">
                                                <i class="bi bi-folder2-open me-1"></i> {{ $existingUrl ? 'Change' : 'Upload' }}
                                            </button>
                                        </div>
                                        <div class="text-center" style="min-height: 100px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 8px;">
                                            <img id="{{ $fieldName }}_preview" src="{{ $existingUrl ?? '' }}" class="{{ $existingUrl ? '' : 'd-none' }} rounded shadow-sm" style="max-height: 100px; max-width: 100%;">
                                            <span class="text-muted small {{ $existingUrl ? 'd-none' : 'd-block' }}" id="{{ $fieldName }}_placeholder">No image</span>
                                        </div>
                                        @error($fieldName) <div class="text-danger small mt-2 text-center fw-bold">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                {{-- SECTION 4: GUARANTOR INFO --}}
                @if(isset($guarantorImageTypes) && count($guarantorImageTypes) > 0)
                <div class="card card-modern bg-card-secondary">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="section-icon bg-secondary-subtle text-secondary">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">Guarantor Documents</h5>
                        </div>

                        <div class="row g-4">
                            @foreach($guarantorImageTypes as $imageType)
                            @php
                            $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                            $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);

                            // Check if image exists
                            $existingImage = $lead->images->where('image_type', $imageType['name'])->first();
                            $existingUrl = $existingImage ? Storage::url($existingImage->image_path) : null;
                            @endphp
                            <div class="col-md-6">
                                <div class="upload-area">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <label class="form-label mb-0">{{ $imageType['name'] }}</label>
                                    </div>

                                    <input type="file" name="{{ $fieldName }}" id="{{ $fieldName }}" class="d-none" accept="image/*"
                                        onchange="handleImageUpload(this, '{{ $fieldName }}_preview')"
                                        data-image-type="{{ $imageType['name'] }}">

                                    <div class="mt-auto">
                                        <div class="d-flex flex-column flex-sm-row gap-2 mb-3">
                                            <button type="button" class="btn btn-light btn-modern flex-grow-1 border text-dark py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="triggerFileSelect('{{ $fieldName }}')">
                                                <i class="bi bi-folder2-open me-1"></i> {{ $existingUrl ? 'Change' : 'Upload' }}
                                            </button>
                                        </div>
                                        <div class="text-center" style="min-height: 100px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 8px;">
                                            <img id="{{ $fieldName }}_preview" src="{{ $existingUrl ?? '' }}" class="{{ $existingUrl ? '' : 'd-none' }} rounded shadow-sm" style="max-height: 100px; max-width: 100%;">
                                            <span class="text-muted small {{ $existingUrl ? 'd-none' : 'd-block' }}" id="{{ $fieldName }}_placeholder">No image</span>
                                        </div>
                                        @error($fieldName) <div class="text-danger small mt-2 text-center fw-bold">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="d-grid mb-5">
                    <div class="d-flex flex-column flex-md-row gap-2 justify-content-center">
                        <a href="{{ route('leads.verifiedList') }}" class="btn btn-light btn-lg py-3 rounded-pill shadow-sm fw-bold px-5 border">
                            Close
                        </a>
                        <button type="button" class="btn btn-success btn-lg py-3 rounded-pill shadow bw-bold flex-grow-1" onclick="updateLead()" id="updateLeadBtn">
                            <i class="bi bi-check-circle-fill me-2"></i> Update Lead Information
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const GOOGLE_MAPS_API_KEY = "{{ config('services.google_maps.key') }}";

    $(document).ready(function() {
        // Initialize Select2
        $('.select2-search').select2({
            width: '100%',
            placeholder: 'Select an option',
            allowClear: true
        });

        // ===================== PROVINCE & CITY (SL LOCATIONS) =====================
        $('#state').select2({
            placeholder: "Select Province/District",
            allowClear: true,
            ajax: {
                url: '/sl-locations/provinces',
                dataType: 'json',
                delay: 200,
                processResults: function(data) {
                    console.log(data);

                    return {
                        results: data.provinces || []
                    };
                }
            },
            width: '100%'
        });

        $('#city').select2({
            placeholder: "Select City / Town",
            allowClear: true,
            ajax: {
                url: '/sl-locations/cities',
                dataType: 'json',
                delay: 200,
                data: function(params) {
                    return {
                        province_id: $('#state').val(),
                        q: params.term || ''
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.cities || []
                    };
                }
            },
            width: '100%'
        });

        $('#state').on('change', function() {
            $('#city').val(null).trigger('change');
        });

        // Update hidden inputs with TEXT when selection changes
        $('#state').on('select2:select', function(e) {
            var data = e.params.data;
            console.log('district', data);

            $('#district_text').val(data.text);
            $('#state').append(data).trigger('change');
        });

        $('#city').on('select2:select', function(e) {
            var data = e.params.data;
            $('#city_text').val(data.text);
        });

        // Initialize Select2 for Officers
        $('.select2-officers').select2({
            width: '100%',
            placeholder: 'Select Route First',
            allowClear: true
        });

        // Function to load officers
        function loadOfficers(routeId, selectedOfficerId = null) {
            var officerSelect = $('#recovery_officer_id');

            // Clear current options
            officerSelect.empty().trigger('change');

            if (routeId) {
                // Show loading state
                var loadingOption = new Option('Loading...', '', false, false);
                officerSelect.append(loadingOption).trigger('change');
                officerSelect.prop('disabled', true);

                $.ajax({
                    url: '/leads/routes/' + routeId + '/officers',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        officerSelect.empty();
                        officerSelect.append(new Option('Select Recovery Officer', '', true, true)).trigger('change');

                        if (data.length > 0) {
                            $.each(data, function(index, officer) {
                                var isSelected = (selectedOfficerId && officer.id == selectedOfficerId);
                                var option = new Option(officer.text, officer.id, isSelected, isSelected);
                                officerSelect.append(option);
                            });

                            // If explicit selection provided, ensure it's selected (Select2 needs trigger)
                            if (selectedOfficerId) {
                                officerSelect.val(selectedOfficerId).trigger('change');
                            }
                        } else {
                            officerSelect.append(new Option('No officers found', '', false, false));
                        }
                    },
                    error: function() {
                        officerSelect.empty();
                        officerSelect.append(new Option('Error loading officers', '', false, false));
                    },
                    complete: function() {
                        officerSelect.prop('disabled', false);
                    }
                });
            } else {
                officerSelect.append(new Option('Select Route First', '', true, true)).trigger('change');
            }
        }

        // Auto-select Recovery Officer based on Route
        $('select[name="route_id"]').on('change', function() {
            var routeId = $(this).val();
            loadOfficers(routeId);
        });

        // Initial Load
        var initialRouteId = $('select[name="route_id"]').val();
        var initialOfficerId = $('#current_recovery_officer_id').val();

        if (initialRouteId) {
            loadOfficers(initialRouteId, initialOfficerId);
        }

        // ===================== INITIAL DATA LOADING =====================
        var dbDistrict = "{{ $lead->district }}";
        var dbCity = "{{ $lead->city }}";

        if (dbDistrict) {
            // Fetch provinces to find match (ID or Text)
            $.get('/sl-locations/provinces', function(data) {
                var foundProv = data.provinces.find(p => p.id == dbDistrict || p.text === dbDistrict);
                if (foundProv) {
                    // We found the province object. 
                    // Set Select2 to ID (so child dropdown works)
                    var option = new Option(foundProv.text, foundProv.text, true, true);

                    console.log(option);
                    $('#state').append(option).trigger('change');

                    // Set Hidden Input to TEXT (so we save text)
                    $('#district_text').val(foundProv.text);

                    // Load Cities if we have a city value
                    if (dbCity) {
                        $.get('/sl-locations/cities', {
                            province_id: foundProv.id
                        }, function(cityData) {
                            var foundCity = cityData.cities.find(c => c.id == dbCity || c.text === dbCity);
                            if (foundCity) {
                                var cityOption = new Option(foundCity.text, foundCity.id, true, true);
                                $('#city').append(cityOption).trigger('change');
                                $('#city_text').val(foundCity.text);
                            } else {
                                // If city not found in list (maybe manual entry?), just set it as text option?
                                // Select2 needs an ID. If we use text as ID, API might fail if accessed again.
                                // But for display it's fine.
                                // However, keeping previous value if no match logic:
                                // $('#city_text').val(dbCity);
                            }
                        });
                    }
                } else {
                    // If existing value wasn't found in API list, maybe it's custom text?
                    // Just show it as is.
                    var option = new Option(dbDistrict, dbDistrict, true, true);
                    $('#state').append(option).trigger('change');
                }
            });
        }
    });

    // --- Location Functions ---
    function getLeadLocation() {
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Geolocation is not supported by this browser.', 'error');
            return;
        }

        Swal.fire({
            title: 'Acquiring GPS Signal',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                $('#lead_lat').val(lat);
                $('#lead_lng').val(lng);

                Swal.close();
                Swal.fire('Success', 'Location captured!', 'success');
            },
            (error) => {
                Swal.fire('Error', 'Unable to retrieve location.', 'error');
            }, {
                enableHighAccuracy: true
            }
        );
    }

    function triggerFileSelect(id) {
        $('#' + id).click();
    }

    function handleImageUpload(input, previewId) {
        var container = $('#' + previewId + '_container');
        var placeholder = $('#' + previewId.replace('_preview', '_placeholder'));

        if (input.files && input.files.length > 0) {
            placeholder.addClass('d-none'); // Hide placeholder

            Array.from(input.files).forEach(file => {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var html = '';
                    if (file.type === 'application/pdf') {
                        html = `
                            <div class="position-relative d-inline-block">
                                <i class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                <div class="small fw-bold text-truncate" style="max-width: 80px;">${file.name}</div>
                            </div>
                         `;
                    } else {
                        html = `
                            <div class="position-relative d-inline-block">
                                <img src="${e.target.result}" class="rounded shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;">
                            </div>
                        `;
                    }
                    container.append(html);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // --- Form Submission ---
    function updateLead() {
        const btn = $('#updateLeadBtn');
        const id = $('#lead_id').val();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

        const formData = new FormData($('#leadForm')[0]);

        $.ajax({
            url: "/leads/" + id,
            method: "POST", // Method spoofing _method=PUT is handled by input
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Lead Updated!',
                        text: 'Lead information has been updated.',
                        confirmButtonText: 'Go to Leads'
                    }).then((result) => {
                        window.location.href = "{{ route('leads.verifiedList') }}";
                    });
                } else {
                    Swal.fire('Error', res.message || 'Something went wrong', 'error');
                    btn.prop('disabled', false).html('<i class="bi bi-check-circle-fill me-2"></i> Update Lead Information');
                }
            },
            error: function(xhr) {
                let msg = 'Failed to update lead.';
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    // Cleanup old errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.text-danger.small').remove();

                    // Show new errors
                    $.each(errors, function(field, messages) {
                        const input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.after('<div class="text-danger small mt-1">' + messages[0] + '</div>');
                    });

                    Swal.fire('Validation Error', 'Please check the form for errors.', 'warning');
                } else {
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                }
                btn.prop('disabled', false).html('<i class="bi bi-check-circle-fill me-2"></i> Update Lead Information');
            }
        });
    }
</script>
@endsection
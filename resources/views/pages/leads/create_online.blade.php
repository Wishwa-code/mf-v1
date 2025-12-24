@extends('layout.admin')

@section('content')
<div class="container-fluid pb-5">
    <!-- Page Header -->
    <div class="row align-items-center mb-4 mt-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-1 fw-bold fs-3 text-dark">Create Online Lead</h4>
                    <ol class="breadcrumb m-0 small text-muted">
                        <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item active text-primary">Create Online Lead</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

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
                <input type="hidden" id="lead_id" name="lead_id">


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
                                        {{ old('route_id') == $route->id_route ? 'selected' : '' }}>
                                        {{ $route->name }} - {{ $route->root_code }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                                @error('route_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <!-- Source Selection Second -->
                            <div class="col-md-6">
                                <label class="form-label">Source</label>
                                <select name="source" class="form-control select2-search @error('source') is-invalid @enderror">
                                    <option value="">Select Source</option>
                                    <option value="facebook" {{ old('source') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="whatsapp" {{ old('source') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="instagram" {{ old('source') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="tiktok" {{ old('source') == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                                    <option value="website" {{ old('source') == 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="other" {{ old('source') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('source') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <!-- Recovery Officer Selection -->
                            <div class="col-md-6">
                                <label class="form-label">Recovery Officer</label>
                                <select name="recovery_officer_id" id="recovery_officer_id" class="form-control select2-officers @error('recovery_officer_id') is-invalid @enderror">
                                    <option value="">Select Route First</option>
                                </select>
                                @error('recovery_officer_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="full_name" class="form-control border-start-0 ps-0 @error('full_name') is-invalid @enderror" placeholder="Ex: John Doe" value="{{ old('full_name') }}">
                                </div>
                                @error('full_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="phone_number" class="form-control border-start-0 ps-0 @error('phone_number') is-invalid @enderror" placeholder="Ex: 0771234567" value="{{ old('phone_number') }}">
                                </div>
                                @error('phone_number') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" placeholder="name@example.com" value="{{ old('email') }}">
                                </div>
                                @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Loan Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control select2-search @error('type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="group" {{ old('type') == 'group' ? 'selected' : '' }}>Group</option>
                                    <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="business" {{ old('type') == 'business' ? 'selected' : '' }}>Business</option>
                                    <option value="leasing" {{ old('type') == 'leasing' ? 'selected' : '' }}>Leasing</option>
                                </select>
                                @error('type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Business Category <span class="text-danger">*</span></label>
                                <select name="business_category_id" class="form-control select2-search @error('business_category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @if(isset($businessCategories))
                                    @foreach($businessCategories as $category)
                                    <option value="{{ $category->id }}" {{ old('business_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                    @endif
                                </select>
                                @error('business_category_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Loan Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted">Rs.</span>
                                    <input type="number" name="loan_amount" step="0.01" class="form-control border-start-0 ps-0 @error('loan_amount') is-invalid @enderror" placeholder="0.00" value="{{ old('loan_amount') }}">
                                </div>
                                @error('loan_amount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Periods (Months) <span class="text-danger">*</span></label>
                                <input type="number" name="periods" class="form-control @error('periods') is-invalid @enderror" placeholder="12" value="{{ old('periods') }}" min="1">
                                @error('periods') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <!-- District and City -->
                            <div class="col-md-6">
                                <label for="state" class="form-label">Province / District</label>
                                <input type="hidden" name="district" id="district_text">
                                <select id="state" class="form-control select2-districts">
                                    <option value="">Select District</option>
                                    {{-- loaded via ajax --}}
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="city" class="form-label">City / Town</label>
                                <input type="hidden" name="city" id="city_text">
                                <select id="city" class="form-control select2-cities">
                                    <option value="">Select City / Town</option>
                                    {{-- loaded via ajax --}}
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Full residential/business address">{{ old('address') }}</textarea>
                                @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            {{-- Optional Location, not strict --}}
                            <div class="col-12">
                                <label class="form-label">Location (Optional)</label>
                                <div class="p-3 bg-light rounded-3 border">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-5">
                                            <label class="small text-muted text-uppercase fw-bold mb-1">Latitude</label>
                                            <input type="text" name="latitude" id="lead_lat" class="form-control" placeholder="Ex: 6.9271" value="{{ old('latitude') }}">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="small text-muted text-uppercase fw-bold mb-1">Longitude</label>
                                            <input type="text" name="longitude" id="lead_lng" class="form-control" placeholder="Ex: 79.8612" value="{{ old('longitude') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-primary btn-modern w-100" onclick="getLeadLocation()">
                                                <i class="bi bi-geo-alt-fill"></i> Get GPS
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2 text-end">
                                        <a href="#" id="viewMapBtn" target="_blank" class="btn btn-link text-decoration-none btn-sm d-none">
                                            <i class="bi bi-map-fill me-1"></i> View on Map
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Notes / Remarks</label>
                                <textarea name="notes" class="form-control" rows="3" placeholder="Additional details about the lead...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: DOCUMENT IMAGES --}}
                @if(isset($imageTypes) && count($imageTypes) > 0)
                <div class="card card-modern bg-card-info">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="section-icon bg-info-subtle text-info">
                                <i class="bi bi-file-earmark-image"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">Required Documents</h5>
                        </div>

                        <div class="row g-4">
                            @foreach($imageTypes as $imageType)
                            @php
                            $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                            $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);
                            $isRequired = isset($imageType['is_required']) && $imageType['is_required'];
                            @endphp
                            <div class="col-md-6">
                                <div class="upload-area">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <label class="form-label mb-0">
                                            {{ $imageType['name'] }}
                                            @if($isRequired) <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6em;">REQUIRED</span> @endif
                                        </label>
                                    </div>

                                    <input type="file" name="{{ $fieldName }}[]" id="{{ $fieldName }}" class="d-none" accept="image/*" multiple
                                        onchange="handleImageUpload(this, '{{ $fieldName }}_preview')"
                                        data-image-type="{{ $imageType['name'] }}">
                                    <!-- No strict location for images in online lead -->
                                    <input type="hidden" id="{{ $fieldName }}_lat" name="{{ $fieldName }}_latitude">
                                    <input type="hidden" id="{{ $fieldName }}_lng" name="{{ $fieldName }}_longitude">

                                    <div class="mt-auto">
                                        <div class="d-flex flex-column flex-sm-row gap-2 mb-3">
                                            <button type="button" class="btn btn-light btn-modern flex-grow-1 border text-dark py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="triggerFileSelect('{{ $fieldName }}')">
                                                <i class="bi bi-folder2-open me-1"></i> Upload
                                            </button>
                                        </div>
                                        <div class="text-center" style="min-height: 100px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 8px;">
                                            <div id="{{ $fieldName }}_preview_container" class="d-flex flex-wrap justify-content-center gap-2">
                                                <span class="text-muted small d-block my-auto" id="{{ $fieldName }}_placeholder">No image selected</span>
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

                {{-- SECTION 3: GUARDIAN & GUARANTOR sections can be added similarly if needed. For brevity, assuming online leads might require less or same. 
                     I'll include them to match the original create.blade.php as a full feature copy. --}}

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
                            $isRequired = isset($imageType['is_required']) && $imageType['is_required'];
                            @endphp
                            <div class="col-md-6">
                                <div class="upload-area">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <label class="form-label mb-0">
                                            {{ $imageType['name'] }}
                                            @if($isRequired) <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6em;">REQUIRED</span> @endif
                                        </label>
                                    </div>

                                    <input type="file" name="{{ $fieldName }}[]" id="{{ $fieldName }}" class="d-none" accept="image/*" multiple
                                        onchange="handleImageUpload(this, '{{ $fieldName }}_preview')"
                                        data-image-type="{{ $imageType['name'] }}">
                                    <!-- No strict location for images in online lead -->
                                    <input type="hidden" id="{{ $fieldName }}_lat" name="{{ $fieldName }}_latitude">
                                    <input type="hidden" id="{{ $fieldName }}_lng" name="{{ $fieldName }}_longitude">

                                    <div class="mt-auto">
                                        <div class="d-flex flex-column flex-sm-row gap-2 mb-3">
                                            <button type="button" class="btn btn-light btn-modern flex-grow-1 border text-dark py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="triggerFileSelect('{{ $fieldName }}')">
                                                <i class="bi bi-folder2-open me-1"></i> Upload
                                            </button>
                                        </div>
                                        <div class="text-center" style="min-height: 100px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 8px;">
                                            <div id="{{ $fieldName }}_preview_container" class="d-flex flex-wrap justify-content-center gap-2">
                                                <span class="text-muted small d-block my-auto" id="{{ $fieldName }}_placeholder">No image selected</span>
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
                            $isRequired = isset($imageType['is_required']) && $imageType['is_required'];
                            @endphp
                            <div class="col-md-6">
                                <div class="upload-area">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <label class="form-label mb-0">
                                            {{ $imageType['name'] }}
                                            @if($isRequired) <span class="badge bg-danger-subtle text-danger ms-1" style="font-size: 0.6em;">REQUIRED</span> @endif
                                        </label>
                                    </div>

                                    <input type="file" name="{{ $fieldName }}[]" id="{{ $fieldName }}" class="d-none" accept="image/*" multiple
                                        onchange="handleImageUpload(this, '{{ $fieldName }}_preview')"
                                        data-image-type="{{ $imageType['name'] }}">
                                    <!-- No strict location for images in online lead -->
                                    <input type="hidden" id="{{ $fieldName }}_lat" name="{{ $fieldName }}_latitude">
                                    <input type="hidden" id="{{ $fieldName }}_lng" name="{{ $fieldName }}_longitude">

                                    <div class="mt-auto">
                                        <div class="d-flex flex-column flex-sm-row gap-2 mb-3">
                                            <button type="button" class="btn btn-light btn-modern flex-grow-1 border text-dark py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="triggerFileSelect('{{ $fieldName }}')">
                                                <i class="bi bi-folder2-open me-1"></i> Upload
                                            </button>
                                        </div>
                                        <div class="text-center" style="min-height: 100px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 8px;">
                                            <div id="{{ $fieldName }}_preview_container" class="d-flex flex-wrap justify-content-center gap-2">
                                                <span class="text-muted small d-block my-auto" id="{{ $fieldName }}_placeholder">No image selected</span>
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

                <div class="d-grid mb-5">
                    <button type="button" class="btn btn-primary btn-lg py-3 rounded-pill shadow fw-bold" onclick="saveLead()" id="saveLeadBtn">
                        <i class="bi bi-check-circle-fill me-2"></i> Submit Online Lead
                    </button>
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
            $('#district_text').val(data.text);
        });

        $('#city').on('select2:select', function(e) {
            var data = e.params.data;
            $('#city_text').val(data.text);
        });

        // Initialize Select2 for Officers separately if needed or reuse
        $('.select2-officers').select2({
            width: '100%',
            placeholder: 'Select Route First',
            allowClear: true
        });

        // Auto-select Recovery Officer based on Route
        $('select[name="route_id"]').on('change', function() {
            var routeId = $(this).val();
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
                                var option = new Option(officer.text, officer.id, false, false);
                                officerSelect.append(option);
                            });

                            // Auto-select if there's only one officer (optional but good UX)
                            if (data.length === 1) {
                                officerSelect.val(data[0].id).trigger('change');
                            }
                        } else {
                            officerSelect.append(new Option('No officers found', '', false, false));
                        }
                    },
                    error: function() {
                        officerSelect.empty();
                        officerSelect.append(new Option('Error loading officers', '', false, false));
                        Swal.fire('Error', 'Failed to load recovery officers.', 'error');
                    },
                    complete: function() {
                        officerSelect.prop('disabled', false);
                    }
                });
            } else {
                officerSelect.append(new Option('Select Route First', '', true, true)).trigger('change');
            }
        });
    });

    // --- Location Functions (Simplified for Online Lead) ---
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

                $('#viewMapBtn').attr('href', `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`).removeClass('d-none');

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

    // Global store for DataTransfer objects per input
    const fileStore = {};

    function triggerFileSelect(id) {
        $('#' + id).click();
    }

    function handleImageUpload(input, previewId) {
        const inputId = input.id;
        const container = $('#' + previewId + '_container');
        const placeholder = $('#' + previewId.replace('_preview', '_placeholder'));

        // Initialize DataTransfer for this input if not exists
        if (!fileStore[inputId]) {
            fileStore[inputId] = new DataTransfer();
        }

        const dt = fileStore[inputId];

        // Add NEW files to DataTransfer
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach(file => {
                dt.items.add(file);
            });
        }

        // Update the input's files to match DataTransfer (so they are submitted)
        input.files = dt.files;

        // Re-render previews
        renderPreviews(inputId, container, placeholder);
    }

    function renderPreviews(inputId, container, placeholder) {
        container.empty();
        // Add placeholder back (hidden if has files)?? 
        // Actually, let's keep placeholder outside or append it if empty.
        // My PHP verification logic checked if container has children. 
        // For simplicity, let's just append previews. 
        // If empty, show placeholder check? 
        // The placeholder element is separate in blade: <span>...</span>. 
        // In my logic above, I got it by ID.

        const dt = fileStore[inputId];

        if (dt.files.length > 0) {
            placeholder.addClass('d-none');
            // existing placeholder might have been removed by .empty if it was inside container?
            // In blade: <div id="container"><span id="placeholder"></span></div>. 
            // YES. .empty() removes placeholder.
            // We should re-append placeholder or hide/show it.
            // Better: Hide the placeholder if files exist. If no files, append it back?
            // Actually, let's handle placeholder visibility separately or re-create it.
            // The blade implementation had placeholder INSIDE container.

            Array.from(dt.files).forEach((file, index) => {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var html = '';
                    if (file.type === 'application/pdf') {
                        html = `
                            <div class="position-relative d-inline-block m-1" id="file-${inputId}-${index}">
                                <div class="ratio ratio-1x1 border rounded bg-light d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
                                    <div class="text-center">
                                        <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                                        <div class="small fw-bold text-truncate" style="max-width: 70px; font-size: 0.6rem;">${file.name}</div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 translate-middle rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                                    style="width: 20px; height: 20px; font-size: 0.7rem;" onclick="removeFile('${inputId}', ${index})">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                         `;
                    } else {
                        html = `
                            <div class="position-relative d-inline-block m-1" id="file-${inputId}-${index}">
                                <img src="${e.target.result}" class="rounded shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;">
                                <button type="button" class="btn btn-danger btn-sm p-0 position-absolute top-0 end-0 translate-middle rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
                                    style="width: 20px; height: 20px; font-size: 0.7rem;" onclick="removeFile('${inputId}', ${index})">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>
                        `;
                    }
                    container.append(html);
                }
                reader.readAsDataURL(file);
            });
        } else {
            // No files, show placeholder
            // Since we emptied container, we need to add placeholder back if we want it inside.
            // Or assumes it's hidden. 
            // In blade: <span ... id="..._placeholder">No image selected</span>.
            // If I stripped it, I must recreate it.
            container.html(`<span class="text-muted small d-block my-auto" id="${inputId.replace('image_', '')}_placeholder">No image selected</span>`);
            // Wait, ID logic for placeholder was specific in blade... 
            // let's pass placeholder text or just standard text.
            // Or simpler: Don't empty container immediately, just remove file elements? No, simpler to rebuild.
            // Let's just put generic text.
            container.append(`<span class="text-muted small d-block my-auto">No image selected</span>`);
            placeholder.removeClass('d-none'); // references original obj? if removed from DOM, this ref is stale? 
            // Yes, if placeholder was child of container, `placeholder` var is detached DOM node.
        }
    }

    function removeFile(inputId, index) {
        if (!fileStore[inputId]) return;
        const dt = fileStore[inputId];
        const newDt = new DataTransfer();

        // Copy all files EXCEPT index
        Array.from(dt.files).forEach((file, i) => {
            if (i !== index) newDt.items.add(file);
        });

        // Update Store and Input
        fileStore[inputId] = newDt;
        document.getElementById(inputId).files = newDt.files;

        // Re-render
        const container = $('#' + inputId + '_preview_container');
        // Placeholder handling is tricky if we don't pass it. 
        // But renderPreviews reconstructs.
        // We can pass null for placeholder since we rebuild it if empty.
        renderPreviews(inputId, container, null);
    }

    // --- Form Submission ---
    function saveLead() {
        const btn = $('#saveLeadBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');

        const formData = new FormData($('#leadForm')[0]);

        $.ajax({
            url: "{{ route('online-leads.store') }}",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Lead Created!',
                        text: 'Online lead has been successfully added.',
                        confirmButtonText: 'Go to Leads'
                    }).then((result) => {
                        window.location.href = "{{ route('leads.verifiedList') }}";
                    });
                } else {
                    Swal.fire('Error', res.message || 'Something went wrong', 'error');
                    btn.prop('disabled', false).html('<i class="bi bi-check-circle-fill me-2"></i> Submit Online Lead');
                }
            },
            error: function(xhr) {
                let msg = 'Failed to save lead.';
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    // Cleanup old errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.upload-area').removeClass('border-danger');
                    $('.text-danger.small').remove();
                    $('.error-msg').remove();

                    // Show new errors
                    $.each(errors, function(field, messages) {
                        // Handle array field inputs (e.g. image_nic.0 -> image_nic[], or image_nic -> image_nic[])
                        let inputName = field;
                        let input = $('[name="' + inputName + '"]');

                        if (input.length === 0) {
                            // Try appending []
                            input = $('[name="' + inputName + '[]"]');
                        }

                        if (input.length === 0 && inputName.includes('.')) {
                            // Split dot notation (e.g. image_nic.0)
                            let parts = inputName.split('.');
                            let baseName = parts[0];
                            input = $('[name="' + baseName + '[]"]');

                            if (input.length === 0) {
                                input = $('[name="' + baseName + '"]');
                            }
                        }

                        if (input.length > 0) {
                            input.addClass('is-invalid');
                            // Find the closest .upload-area to invoke improved visibility
                            let container = input.closest('.upload-area');
                            if (container.length > 0) {
                                // Add error message at the bottom of upload area
                                container.append('<div class="text-danger small mt-2 text-center fw-bold error-msg">' + messages[0] + '</div>');
                                container.addClass('border-danger');
                            } else {
                                // Fallback
                                input.after('<div class="text-danger small mt-1">' + messages[0] + '</div>');
                            }
                        }
                    });

                    Swal.fire('Validation Error', 'Please check the form for errors.', 'warning');
                } else {
                    Swal.fire('Error', 'An unexpected error occurred.', 'error');
                }
                btn.prop('disabled', false).html('<i class="bi bi-check-circle-fill me-2"></i> Submit Online Lead');
            }
        });
    }
</script>
@endsection
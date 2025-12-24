@extends('layout.admin')

@section('content')
<div class="container-fluid pb-5">
    <!-- Page Header -->
    <div class="row align-items-center mb-4 mt-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-1 fw-bold fs-3 text-dark">Edit Lead</h4>
                    <ol class="breadcrumb m-0 small text-muted">
                        <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('leads.index') }}" class="text-decoration-none text-muted">Leads</a></li>
                        <li class="breadcrumb-item active text-primary">Edit Lead</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Styles (Reuse from Create) -->
    <style>
        .card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #fff;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

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

        .btn-modern {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-modern:active {
            transform: scale(0.98);
        }

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
    </style>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Loading Spinner Initially -->
            <div id="loadingSpinner" class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-2 text-muted">Loading Lead Details...</p>
            </div>

            <form id="editLeadForm" enctype="multipart/form-data" style="display: none;">
                @csrf
                @method('PUT')
                <input type="hidden" id="lead_id" name="lead_id" value="{{ $lead->id }}">
                <input type="hidden" name="latitude" id="lead_lat">
                <input type="hidden" name="longitude" id="lead_lng">

                {{-- SECTION 1: LEAD INFORMATION --}}
                <div class="card card-modern">
                    <div class="card-body p-4">
                        <div class="section-header">
                            <div class="section-icon bg-primary-subtle text-primary">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <h5 class="mb-0 fw-bold text-dark">Edit Lead Information</h5>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="bi bi-person"></i></span>
                                    <input type="text" name="full_name" id="full_name" class="form-control border-start-0 ps-0 @error('full_name') is-invalid @enderror" placeholder="Ex: John Doe">
                                </div>
                                @error('full_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="bi bi-telephone"></i></span>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control border-start-0 ps-0 @error('phone_number') is-invalid @enderror" placeholder="Ex: 0771234567">
                                </div>
                                @error('phone_number') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" id="email" class="form-control border-start-0 ps-0 @error('email') is-invalid @enderror" placeholder="name@example.com">
                                </div>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Loan Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control select2-search @error('type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="group">Group</option>
                                    <option value="individual">Individual</option>
                                    <option value="business">Business</option>
                                    <option value="leasing">Leasing</option>
                                </select>
                                @error('type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Business Category <span class="text-danger">*</span></label>
                                <select name="business_category_id" id="business_category_id" class="form-control select2-search @error('business_category_id') is-invalid @enderror">
                                    <option value="">Select Category</option>
                                    @foreach($businessCategories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('business_category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Loan Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted">Rs.</span>
                                    <input type="number" name="loan_amount" id="loan_amount" step="0.01" class="form-control border-start-0 ps-0 @error('loan_amount') is-invalid @enderror" placeholder="0.00">
                                </div>
                                @error('loan_amount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">Periods (Months) <span class="text-danger">*</span></label>
                                <input type="number" name="periods" id="periods" class="form-control @error('periods') is-invalid @enderror" placeholder="12" min="1">
                                @error('periods') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" placeholder="Full address"></textarea>
                                @error('address') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label">Location (Read-Only from Update)</label>
                                <div class="p-3 bg-light rounded-3 border d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white p-2 rounded border">
                                            <small class="text-muted d-block text-uppercase" style="font-size:0.65rem; font-weight:700;">Latitude</small>
                                            <span id="lead_lat_display" class="font-monospace text-dark fw-bold">--</span>
                                        </div>
                                        <div class="bg-white p-2 rounded border">
                                            <small class="text-muted d-block text-uppercase" style="font-size:0.65rem; font-weight:700;">Longitude</small>
                                            <span id="lead_lng_display" class="font-monospace text-dark fw-bold">--</span>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary btn-modern btn-sm" onclick="getLeadLocation()">
                                            <i class="bi bi-geo-alt-fill me-1"></i> Update GPS
                                        </button>
                                        <a href="#" id="viewMapBtn" target="_blank" class="btn btn-outline-info btn-modern btn-sm d-none">
                                            <i class="bi bi-map-fill me-1"></i> View Map
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2: DOCUMENT IMAGES --}}
                @if(isset($imageTypes) && count($imageTypes) > 0)
                <div class="card card-modern">
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
                            @endphp
                            <div class="col-md-6">
                                <div class="upload-area">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <label class="form-label mb-0">{{ $imageType['name'] }}</label>
                                        <div id="{{ $fieldName }}_location_status"></div>
                                    </div>

                                    <input type="file" name="{{ $fieldName }}[]" id="{{ $fieldName }}" class="d-none" accept="image/*" multiple
                                        onchange="handleImageUpload(this, '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')"
                                        data-image-type="{{ $imageType['name'] }}">
                                    <input type="hidden" id="{{ $fieldName }}_lat" name="{{ $fieldName }}_latitude">
                                    <input type="hidden" id="{{ $fieldName }}_lng" name="{{ $fieldName }}_longitude">

                                    <div class="mt-auto">
                                        <div class="d-flex flex-column flex-sm-row gap-2 mb-3">
                                            <button type="button" class="btn btn-outline-primary btn-modern flex-grow-1 py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="getSpecificLocation('{{ $fieldName }}_lat', '{{ $fieldName }}_lng', '{{ $fieldName }}_location_status')">
                                                <i class="bi bi-geo-alt me-1"></i> Get GPS
                                            </button>
                                            <button type="button" class="btn btn-outline-dark btn-modern flex-grow-1 py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="openWebcamModal('{{ $fieldName }}', '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')">
                                                <i class="bi bi-camera me-1"></i> Camera
                                            </button>
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
                                        {{-- Current Link logic might need adjustment for multiple images, simplified to just show previews above --}}
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
                <div class="card card-modern">
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
                            @endphp
                            <div class="col-md-6">
                                <div class="upload-area">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <label class="form-label mb-0">{{ $imageType['name'] }}</label>
                                        <div id="{{ $fieldName }}_location_status"></div>
                                    </div>

                                    <input type="file" name="{{ $fieldName }}[]" id="{{ $fieldName }}" class="d-none" accept="image/*" multiple
                                        onchange="handleImageUpload(this, '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')"
                                        data-image-type="{{ $imageType['name'] }}">

                                    {{-- Explicit Location Fields --}}
                                    <div class="row g-1 mb-2">
                                        <div class="col-6">
                                            <input type="text" id="{{ $fieldName }}_lat" name="{{ $fieldName }}_latitude" class="form-control form-control-sm bg-white" placeholder="Lat" readonly style="font-size:0.75rem; height: 30px;">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" id="{{ $fieldName }}_lng" name="{{ $fieldName }}_longitude" class="form-control form-control-sm bg-white" placeholder="Lng" readonly style="font-size:0.75rem; height: 30px;">
                                        </div>
                                    </div>

                                    <div class="mt-auto">
                                        <div class="d-flex flex-column flex-sm-row gap-2 mb-3">
                                            <button type="button" class="btn btn-outline-primary btn-modern flex-grow-1 py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="getSpecificLocation('{{ $fieldName }}_lat', '{{ $fieldName }}_lng', '{{ $fieldName }}_location_status')">
                                                <i class="bi bi-geo-alt me-1"></i> Get GPS
                                            </button>
                                            <button type="button" class="btn btn-outline-dark btn-modern flex-grow-1 py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="openWebcamModal('{{ $fieldName }}', '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')">
                                                <i class="bi bi-camera me-1"></i> Camera
                                            </button>
                                            <button type="button" class="btn btn-light btn-modern flex-grow-1 border text-dark py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="triggerFileSelect('{{ $fieldName }}')">
                                                <i class="bi bi-folder2 me-1"></i> Upload
                                            </button>
                                        </div>
                                        <div class="text-center" style="min-height: 100px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 8px;">
                                            <div id="{{ $fieldName }}_preview_container" class="d-flex flex-wrap justify-content-center gap-2">
                                                <span class="text-muted small d-block my-auto" id="{{ $fieldName }}_placeholder">No image selected</span>
                                            </div>
                                        </div>
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
                <div class="card card-modern">
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
                            @endphp
                            <div class="col-md-6">
                                <div class="upload-area">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <label class="form-label mb-0">{{ $imageType['name'] }}</label>
                                        <div id="{{ $fieldName }}_location_status"></div>
                                    </div>

                                    <input type="file" name="{{ $fieldName }}[]" id="{{ $fieldName }}" class="d-none" accept="image/*" multiple
                                        onchange="handleImageUpload(this, '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')"
                                        data-image-type="{{ $imageType['name'] }}">

                                    {{-- Explicit Location Fields --}}
                                    <div class="row g-1 mb-2">
                                        <div class="col-6">
                                            <input type="text" id="{{ $fieldName }}_lat" name="{{ $fieldName }}_latitude" class="form-control form-control-sm bg-white" placeholder="Lat" readonly style="font-size:0.75rem; height: 30px;">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" id="{{ $fieldName }}_lng" name="{{ $fieldName }}_longitude" class="form-control form-control-sm bg-white" placeholder="Lng" readonly style="font-size:0.75rem; height: 30px;">
                                        </div>
                                    </div>

                                    <div class="mt-auto">
                                        <div class="d-flex flex-column flex-sm-row gap-2 mb-3">
                                            <button type="button" class="btn btn-outline-primary btn-modern flex-grow-1 py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="getSpecificLocation('{{ $fieldName }}_lat', '{{ $fieldName }}_lng', '{{ $fieldName }}_location_status')">
                                                <i class="bi bi-geo-alt me-1"></i> Get GPS
                                            </button>
                                            <button type="button" class="btn btn-outline-dark btn-modern flex-grow-1 py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="openWebcamModal('{{ $fieldName }}', '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')">
                                                <i class="bi bi-camera me-1"></i> Camera
                                            </button>
                                            <button type="button" class="btn btn-light btn-modern flex-grow-1 border text-dark py-2 px-3 text-truncate" style="font-size:0.85rem;"
                                                onclick="triggerFileSelect('{{ $fieldName }}')">
                                                <i class="bi bi-folder2 me-1"></i> Upload
                                            </button>
                                        </div>
                                        <div class="text-center" style="min-height: 100px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 8px;">
                                            <div id="{{ $fieldName }}_preview_container" class="d-flex flex-wrap justify-content-center gap-2">
                                                <span class="text-muted small d-block my-auto" id="{{ $fieldName }}_placeholder">No image selected</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="d-grid mb-5">
                    <button type="button" class="btn btn-success btn-lg py-3 rounded-pill shadow fw-bold" onclick="updateLead()" id="updateLeadBtn">
                        <i class="bi bi-check-circle-fill me-2"></i> Update Lead Information
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Webcam Modal (Same as Create) -->
<div class="modal fade" id="webcamModal" tabindex="-1" aria-labelledby="webcamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title fw-bold" id="webcamModalLabel">
                    <i class="bi bi-camera-fill me-2 text-warning"></i>Capture Photo
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeWebcamModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 bg-black text-center position-relative">
                <video id="webcamVideo" autoplay playsinline style="width: 100%; max-height: 50vh; display: none; object-fit: cover;"></video>
                <canvas id="webcamCanvas" style="display: none;"></canvas>
                <div id="webcamPlaceholder" class="py-5 text-white">
                    <div class="spinner-border text-light mb-3" role="status"></div>
                    <p class="mb-0">Starting camera...</p>
                </div>
                <div id="capturedImagePreview" class="p-3" style="display: none;">
                    <img id="capturedImg" src="" alt="Captured" class="img-fluid rounded-3 shadow-sm" style="max-height: 50vh;">
                </div>
                <div id="webcamError" class="alert alert-danger m-3 rounded-3" style="display: none;"></div>
            </div>
            <div class="modal-footer bg-light border-0 py-3 justify-content-center gap-2">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" onclick="closeWebcamModal()">Cancel</button>
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" id="switchCameraBtn" onclick="switchCamera()" style="display: none;">
                    <i class="bi bi-arrow-repeat me-1"></i> Flip
                </button>
                <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold" id="retakeBtn" onclick="retakePhoto()" style="display: none;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Retake
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold" id="captureBtn" onclick="capturePhoto()">
                    Capture
                </button>
                <button type="button" class="btn btn-success rounded-pill px-5 fw-bold" id="usePhotoBtn" onclick="useCapturedPhoto()" style="display: none;">
                    <i class="bi bi-check-lg me-1"></i> Use Photo
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    const GOOGLE_MAPS_API_KEY = "{{ config('services.google_maps.key') }}";

    $(document).ready(function() {
        // Init Select2
        $('.select2-search').select2({
            width: '100%',
            placeholder: 'Select Option',
            allowClear: true
        });


        // Let's implement dynamic loading:
        $.get('/sl-locations/districts', function(data) {

        });

        loadLeadData();
    });


    function loadDistricts(selectedDistrict = null) {}

    function loadLeadData() {
        const leadId = "{{ $lead->id }}";
        $.ajax({
            url: "{{ route('leads.data', $lead->id) }}",
            type: "GET",
            success: function(res) {
                if (res.success) {
                    const lead = res.lead;

                    // Populate Fields
                    $('#full_name').val(lead.full_name);
                    $('#phone_number').val(lead.phone_number);
                    $('#email').val(lead.email);
                    $('#nic').val(lead.nic || '');
                    $('#loan_amount').val(lead.loan_amount || '');
                    $('#periods').val(lead.periods);
                    $('#address').val(lead.address);
                    $('#source').val(lead.source || 'online').trigger('change');
                    $('#route_id').val(lead.route_id).trigger('change');

                    // Select2
                    $('#type').val(lead.type).trigger('change');
                    $('#business_category_id').val(lead.business_category_id).trigger('change');

                    if (lead.district) {
                        $('#district').append(new Option(lead.district, lead.district, true, true)).trigger('change');
                    }
                    if (lead.city) {
                        $('#city').append(new Option(lead.city, lead.city, true, true)).trigger('change');
                    }

                    // Location
                    if (lead.latitude && lead.longitude) {
                        $('#lead_lat').val(lead.latitude);
                        $('#lead_lng').val(lead.longitude);
                        $('#lead_lat_display').text(parseFloat(lead.latitude).toFixed(6));
                        $('#lead_lng_display').text(parseFloat(lead.longitude).toFixed(6));
                        $('#viewMapBtn').attr('href', `https://www.google.com/maps/search/?api=1&query=${lead.latitude},${lead.longitude}`).removeClass('d-none');
                    }

                    // Populate Images
                    // Expected structure: res.images = { 'Type Name': [ { image_path: '...', latitude: ... } ] }
                    if (res.images) {
                        $.each(res.images, function(type, images) {
                            if (images && images.length > 0) {
                                const fieldName = 'image_' + type.toLowerCase().replace(/ /g, '_').replace(/[^a-z0-9_]/g, '_');

                                const container = document.getElementById(fieldName + '_preview_container');
                                const placeholder = document.getElementById(fieldName + '_placeholder');

                                if (container) {
                                    if (placeholder) placeholder.style.display = 'none';

                                    // Iterate ALL images for this type
                                    images.forEach(img => {
                                        const imgEl = document.createElement('img');
                                        imgEl.src = "/storage/" + img.image_path;
                                        imgEl.className = 'rounded shadow-sm';
                                        imgEl.style.maxHeight = '100px';
                                        imgEl.style.maxWidth = '100px';
                                        imgEl.style.objectFit = 'cover';
                                        imgEl.style.cursor = 'pointer';
                                        imgEl.onclick = () => window.open(imgEl.src, '_blank');

                                        container.appendChild(imgEl);
                                    });

                                    // Set hidden lat/lngs to existing values of the FIRST image (as a fallback/reference)
                                    // ideally we don't rely on hidden lat/lng for existing images, only new uploads
                                    $(`#${fieldName}_lat`).val(images[0].latitude);
                                    $(`#${fieldName}_lng`).val(images[0].longitude);
                                }
                            }
                        });
                    }

                    // Show Form
                    $('#loadingSpinner').hide();
                    $('#editLeadForm').fadeIn();
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to load lead data.', 'error');
            }
        });
    }

    // --- Reuse Webcam & Location Functions from Create ---
    // (Pasting the core functions for brevity, assume shared JS or copied)

    // --- Location Functions ---
    function getLeadLocation() {
        if (!navigator.geolocation) {
            Swal.fire('Error', 'Geolocation is not supported by this browser.', 'error');
            return;
        }

        let watchId = null;
        let bestPosition = null;
        let bestAccuracy = Infinity;
        const TARGET_ACCURACY = 10; // meters
        const TIMEOUT_MS = 10000; // 10 seconds

        Swal.fire({
            title: 'Acquiring GPS Signal',
            html: 'Improving accuracy...<br>Current Accuracy: <strong id="gps-acc-display" class="text-primary">--</strong> meters',
            allowOutsideClick: false,
            showCancelButton: true,
            cancelButtonText: 'Use Current Best',
            didOpen: () => {
                Swal.showLoading();
            }
        }).then((result) => {
            if (result.dismiss === Swal.DismissReason.cancel) {
                // User chose to stop early and use what we have
                if (watchId) navigator.geolocation.clearWatch(watchId);
                if (bestPosition) {
                    showLeadPosition(bestPosition);
                } else {
                    Swal.fire('Warning', 'No location locked yet. Please try again.', 'warning');
                }
            }
        });

        watchId = navigator.geolocation.watchPosition(
            (position) => {
                const accuracy = position.coords.accuracy;

                // Update UI
                const display = document.getElementById('gps-acc-display');
                if (display) display.textContent = Math.round(accuracy);

                // Track best
                if (accuracy < bestAccuracy) {
                    bestAccuracy = accuracy;
                    bestPosition = position;
                }

                // Threshold Check
                if (accuracy <= TARGET_ACCURACY) {
                    finishWithPosition(position);
                }
            },
            (error) => {
                console.warn("GPS Watch Error:", error);
                if (error.code === error.PERMISSION_DENIED) {
                    finishWithError(error);
                }
            }, {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            }
        );

        // Global Timeout
        setTimeout(() => {
            if (watchId) {
                finishWithPosition(bestPosition);
            }
        }, TIMEOUT_MS);

        function finishWithPosition(pos) {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            Swal.close();

            if (pos) {
                setTimeout(() => showLeadPosition(pos), 200);
            } else {
                Swal.fire('Location Error', 'Unable to get a usable GPS signal.', 'error');
            }
        }

        function finishWithError(err) {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            Swal.close();
            showLeadError(err);
        }
    }

    function showLeadPosition(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        $('#lead_lat').val(lat);
        $('#lead_lng').val(lng);
        $('#lead_lat_display').text(lat.toFixed(6));
        $('#lead_lng_display').text(lng.toFixed(6));
        $('#viewMapBtn').attr('href', `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`).removeClass('d-none');

        // Reverse Geocoding via Google Maps API
        const geocodeUrl = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${GOOGLE_MAPS_API_KEY}`;

        // Indicate loading
        $('textarea[name="address"]').attr('placeholder', 'Fetching address...');

        fetch(geocodeUrl)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'OK') {
                    if (data.results && data.results.length > 0) {
                        $('textarea[name="address"]').val(data.results[0].formatted_address);
                    } else if (data.plus_code) {
                        let compoundCode = data.plus_code.compound_code;
                        const parts = compoundCode.split(',');
                        if (parts.length > 1) {
                            compoundCode = parts.slice(0, -1).join(',').trim();
                        }
                        $('textarea[name="address"]').val(compoundCode);
                    }
                } else {
                    console.error('Geocode failed: ' + data.status);
                }
            })
            .catch(err => console.error('Error:', err))
            .finally(() => {
                $('textarea[name="address"]').attr('placeholder', 'Full address');
            });
    }

    function showLeadError() {
        $('#lead_lat_display').text('Error');
        $('#lead_lng_display').text('Error');
        Swal.fire('Location Error', 'Unable to retrieve location.', 'error');
    }

    function getSpecificLocation(latFieldId, lngFieldId, statusDivId) {
        // Implementation from create.blade.php
        const statusDiv = document.getElementById(statusDivId);
        statusDiv.innerHTML = '<small class="text-info">Locating...</small>';
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((pos) => {
                document.getElementById(latFieldId).value = pos.coords.latitude;
                document.getElementById(lngFieldId).value = pos.coords.longitude;
                statusDiv.innerHTML = '<small class="text-success fw-bold">Captured</small>';
            });
        }
    }

    // --- Update Function ---
    function updateLead() {
        const btn = $('#updateLeadBtn');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Updating...');

        const formData = new FormData($('#editLeadForm')[0]);
        // Laravel PUT method spoofing is handled by @method('PUT') which adds _method field

        $.ajax({
            url: "{{ route('leads.update', $lead->id) }}",
            method: "POST", // POST with _method=PUT
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire('Updated!', 'Lead updated successfully.', 'success').then(() => {
                        window.location.href = "{{ route('leads.verifiedList') }}";
                    });
                } else {
                    Swal.fire('Error', res.message || 'Update failed', 'error');
                    btn.prop('disabled', false).html('<i class="bi bi-check-circle-fill me-2"></i> Update Lead Information');
                }
            },
            error: function(xhr) {
                // Validation errors handling
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;

                    // Clear previous errors
                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback-custom').remove();

                    let firstErrorField = null;

                    $.each(errors, function(key, value) {
                        const field = $(`[name="${key}"]`);
                        if (field.length > 0) {
                            field.addClass('is-invalid');

                            const errorHtml = `<div class="invalid-feedback-custom text-danger small mt-1"><i class="bi bi-exclamation-circle me-1"></i>${value[0]}</div>`;

                            if (field.closest('.input-group').length) {
                                field.closest('.input-group').after(errorHtml);
                            } else if (field.hasClass('select2-hidden-accessible')) {
                                field.next('.select2-container').after(errorHtml);
                            } else if (field.closest('.upload-area').length) {
                                field.closest('.upload-area').append(errorHtml);
                            } else {
                                field.after(errorHtml);
                            }

                            if (!firstErrorField) firstErrorField = field;
                        }
                    });

                    if (firstErrorField) {
                        $('html, body').animate({
                            scrollTop: firstErrorField.offset().top - 200
                        }, 500);
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please check the highlighted fields.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Server Error', 'Failed to update lead.', 'error');
                }
                btn.prop('disabled', false).html('<i class="bi bi-check-circle-fill me-2"></i> Update Lead Information');
            }
        });
    }

    // --- Webcam Logic (Copied from Create) ---
    let webcamStream = null;
    let currentFieldId = null;
    let currentPreviewId = null;
    let currentLatFieldId = null;
    let currentLngFieldId = null;
    let facingMode = 'environment';

    function openWebcamModal(fieldId, previewId, latFieldId, lngFieldId) {
        currentFieldId = fieldId;
        currentPreviewId = previewId;
        currentLatFieldId = latFieldId;
        currentLngFieldId = lngFieldId;

        // Reset UI State
        document.getElementById('webcamVideo').style.display = 'none';
        document.getElementById('webcamPlaceholder').style.display = 'block';
        document.getElementById('capturedImagePreview').style.display = 'none';
        document.getElementById('webcamError').style.display = 'none';

        document.getElementById('captureBtn').style.display = 'inline-block';
        document.getElementById('usePhotoBtn').style.display = 'none';
        document.getElementById('retakeBtn').style.display = 'none';

        const modal = new bootstrap.Modal(document.getElementById('webcamModal'));
        modal.show();
        setTimeout(startWebcam, 500);
    }

    function closeWebcamModal() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(t => t.stop());
            webcamStream = null;
        }
        document.getElementById('webcamVideo').style.display = 'none';
        const modal = bootstrap.Modal.getInstance(document.getElementById('webcamModal'));
        if (modal) modal.hide();
    }
    async function startWebcam() {
        const video = document.getElementById('webcamVideo');
        const placeholder = document.getElementById('webcamPlaceholder');
        try {
            if (webcamStream) webcamStream.getTracks().forEach(t => t.stop());
            webcamStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: facingMode,
                    width: {
                        ideal: 1280
                    }
                },
                audio: false
            });
            video.srcObject = webcamStream;
            video.style.display = 'block';
            video.play();
            placeholder.style.display = 'none';
            document.getElementById('switchCameraBtn').style.display = 'inline-block';
        } catch (err) {
            placeholder.style.display = 'none';
            document.getElementById('webcamError').style.display = 'block';
            document.getElementById('webcamError').innerText = 'Camera access denied';
        }
    }

    function switchCamera() {
        facingMode = facingMode === 'user' ? 'environment' : 'user';
        startWebcam();
    }

    function capturePhoto() {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        document.getElementById('capturedImg').src = canvas.toDataURL('image/jpeg', 0.9);
        document.getElementById('capturedImagePreview').style.display = 'block';
        video.style.display = 'none';
        document.getElementById('captureBtn').style.display = 'none';
        document.getElementById('usePhotoBtn').style.display = 'inline-block';
        document.getElementById('retakeBtn').style.display = 'inline-block';
    }

    function retakePhoto() {
        document.getElementById('webcamVideo').style.display = 'block';
        document.getElementById('capturedImagePreview').style.display = 'none';
        document.getElementById('captureBtn').style.display = 'inline-block';
        document.getElementById('usePhotoBtn').style.display = 'none';
    }

    function useCapturedPhoto() {
        const canvas = document.getElementById('webcamCanvas');
        canvas.toBlob(function(blob) {
            const file = new File([blob], `cam_${Date.now()}.jpg`, {
                type: 'image/jpeg'
            });
            const dt = new DataTransfer();
            dt.items.add(file);
            document.getElementById(currentFieldId).files = dt.files;
            closeWebcamModal();
            // Manually trigger handle logic
            const p = document.getElementById(currentPreviewId);
            p.src = URL.createObjectURL(file);
            p.classList.remove('d-none');
            getSpecificLocation(currentLatFieldId, currentLngFieldId, currentFieldId + '_location_status');
        }, 'image/jpeg', 0.9);
    }

    function triggerFileSelect(id) {
        document.getElementById(id).click();
    }

    function handleImageUpload(input, previewIdUnused, latId, lngId) {
        // Deduced container ID
        const containerId = input.id + '_preview_container';
        const placeholderId = input.id + '_placeholder';
        const container = document.getElementById(containerId);
        const placeholder = document.getElementById(placeholderId);

        if (input.files && input.files.length > 0) {
            // NOTE: In edit mode, do we want to clear existing "DB" images when user selects new files?
            // Usually <input file> selection replaces the "new files to upload" list.
            // But visuals of existing images might need to stay?
            // "Append" logic in backend means we add new files to existing DB checks.
            // So visually, we should probably Keep DB images and just Add new previews?
            // BUT, input.files change triggers this.

            // Let's create a visual separation or just append to container?
            // If we just append, user might be confused which is new.
            // But for simplicity, let's append new previews. 
            // Warning: if user selects files again, we should probably clear *only new* previews. 
            // Hard to distinguish without extra markup.
            // Simplified approach: Clear container and re-render everything? No, we can't re-render DB images easily without fetching again.
            // Better approach: Create a specific 'new-previews' container inside main container?
            // Or just append. If user changes mind and selects different files, we'd have duplicates if we don't clear.

            // For now: We remove any element that has class 'new-upload-preview' before adding new ones.
            const existingNew = container.querySelectorAll('.new-upload-preview');
            existingNew.forEach(el => el.remove());

            if (placeholder) placeholder.style.display = 'none';

            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'rounded shadow-sm new-upload-preview'; // Add class to identify
                    img.style.maxHeight = '100px';
                    img.style.maxWidth = '100px';
                    img.style.objectFit = 'cover';
                    img.style.border = '2px solid #556ee6'; // Highlight new uploads
                    container.appendChild(img);
                }
                reader.readAsDataURL(file);
            });

            getSpecificLocation(latId, lngId, input.id + '_location_status');
        }
    }

    function useCapturedPhoto() {
        const canvas = document.getElementById('webcamCanvas');
        canvas.toBlob(function(blob) {
            const file = new File([blob], `cam_${Date.now()}.jpg`, {
                type: 'image/jpeg'
            });

            const input = document.getElementById(currentFieldId);
            const dataTransfer = new DataTransfer();

            if (input.files) {
                Array.from(input.files).forEach(f => dataTransfer.items.add(f));
            }
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;

            closeWebcamModal();
            // Trigger handle to show preview
            handleImageUpload(input, null, currentLatFieldId, currentLngFieldId);
        }, 'image/jpeg', 0.9);
    }
</script>
@endsection
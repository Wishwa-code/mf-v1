@extends('layout.admin')

@section('content')
<style>
    /* Modern Form Styling */
    .lead-form {
        background: #ffffff;
    }
    
    .lead-form .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-radius: 12px;
        overflow: hidden;
    }
    
    .lead-form .card-body {
        padding: 2rem;
    }
    
    .lead-form .form-label {
        font-weight: 500;
        color: #333;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .lead-form .form-label .text-danger {
        color: #dc3545;
        margin-left: 2px;
    }
    
    .lead-form .form-control,
    .lead-form .form-select {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        background-color: #fafafa;
        transition: all 0.2s ease;
        height: 48px;
    }
    
    .lead-form .form-control:focus,
    .lead-form .form-select:focus {
        background-color: #ffffff;
        border-color: #4a90e2;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
        outline: none;
    }
    
    .lead-form .form-control::placeholder,
    .lead-form textarea::placeholder {
        color: #999;
        font-size: 0.9rem;
    }
    
    .lead-form textarea.form-control {
        min-height: 100px;
        resize: vertical;
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
    }
    
    .lead-form .small.text-muted {
        font-size: 0.85rem;
        color: #666;
        margin-top: 0.25rem;
        display: block;
    }
    
    .lead-form .row {
        margin-bottom: 1rem;
    }
    
    .lead-form .row:last-child {
        margin-bottom: 0;
    }
    
    .lead-form hr {
        border: none;
        border-top: 1px solid #e8e8e8;
        margin: 2rem 0;
    }
    
    .lead-form h5 {
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
    }
    
    /* Invalid State */
    .lead-form .form-control.is-invalid,
    .lead-form .form-select.is-invalid {
        border-color: #dc3545;
        background-color: #fff5f5;
    }
    
    .lead-form .invalid-feedback {
        font-size: 0.875rem;
        color: #dc3545;
        margin-top: 0.25rem;
        display: block;
    }
    
    /* Image Upload Styling */
    .image-upload-wrapper {
        position: relative;
    }
    
    .image-upload-input {
        transition: all 0.3s ease;
    }
    
    .img-thumbnail {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        transition: transform 0.2s ease;
        background: #fafafa;
    }
    
    .img-thumbnail:hover {
        transform: scale(1.02);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    /* Buttons */
    .lead-form .btn {
        border-radius: 8px;
        padding: 0.65rem 1.5rem;
        font-weight: 500;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        border: none;
    }
    
    .lead-form .btn-success {
        background: #28a745;
        box-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
    }
    
    .lead-form .btn-success:hover {
        background: #218838;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }
    
    .lead-form .btn-outline-secondary {
        border: 1px solid #e0e0e0;
        color: #666;
        background: #ffffff;
    }
    
    .lead-form .btn-outline-secondary:hover {
        background: #f8f9fa;
        border-color: #d0d0d0;
    }
    
    .btn-camera-capture,
    .btn-gallery-select {
        flex: 1;
        font-weight: 500;
        transition: all 0.2s ease;
        min-width: 140px;
        border-radius: 8px;
        padding: 0.65rem 1rem;
    }
    
    .btn-camera-capture:hover,
    .btn-gallery-select:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    
    /* Header Section */
    .lead-form .card-header-section {
        border-bottom: 1px solid #e8e8e8;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .lead-form .badge {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
        font-weight: 500;
    }
    
    .lead-form h4 {
        font-weight: 600;
        color: #333;
        font-size: 1.5rem;
    }
    
    .lead-form .text-muted {
        color: #666 !important;
    }
    
    /* Tabs */
    .nav-tabs {
        border-bottom: 2px solid #e8e8e8;
        margin-bottom: 1.5rem;
    }
    
    .nav-tabs .nav-link {
        border: none;
        border-bottom: 3px solid transparent;
        color: #666;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        transition: all 0.2s ease;
        background: transparent;
    }
    
    .nav-tabs .nav-link:hover {
        color: #4a90e2;
        border-bottom-color: #e0e0e0;
    }
    
    .nav-tabs .nav-link.active {
        color: #4a90e2;
        border-bottom-color: #4a90e2;
        background: transparent;
    }
    
    /* Input Group */
    .lead-form .input-group-text {
        background: #f8f9fa;
        border: 1px solid #e0e0e0;
        border-right: none;
        border-radius: 8px 0 0 8px;
        color: #666;
    }
    
    .lead-form .input-group .form-control {
        border-left: none;
        border-radius: 0 8px 8px 0;
    }
    
    .lead-form .input-group:focus-within .input-group-text {
        border-color: #4a90e2;
    }
    
    /* Alert */
    .lead-form .alert {
        border-radius: 8px;
        border: none;
        padding: 1rem;
    }
    
    .lead-form .alert-info {
        background: #e7f3ff;
        color: #0066cc;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .lead-form .card-body {
            padding: 1.5rem;
        }
        
        .btn-camera-capture,
        .btn-gallery-select {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
            min-width: auto;
            flex: 1 1 100%;
        }
        
        .d-flex.gap-2.mb-2.flex-wrap {
            flex-direction: column;
        }
        
        .lead-form .form-control,
        .lead-form .form-select {
            height: 44px;
            font-size: 0.9rem;
        }

        /* Fix Nav Tabs on Mobile */
        .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
        }
        
        .nav-tabs .nav-link {
            white-space: nowrap;
        }
    }

    /* Choices.js Customization */
    .choices__inner {
        min-height: 48px;
        background-color: #fafafa;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
    }
    .choices__list--single {
        padding: 0;
    }
    .choices[data-type*="select-one"] .choices__inner {
        padding-bottom: 0;
    }
    .choices:focus-visible .choices__inner,
    .choices.is-focused .choices__inner {
        border-color: #4a90e2;
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
    }
</style>
<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 lead-form">
            <div class="card-header bg-white border-bottom pt-4 px-4 pb-0">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <div>
                        <h4 class="mb-1">Lead Management</h4>
                        <p class="text-muted small mb-0">Capture and manage lead details.</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-success" onclick="saveLead()" id="saveLeadBtn">
                            <i class="bi bi-check2-circle"></i> Save Lead
                        </button>
                    </div>
                </div>
                
                <ul class="nav nav-tabs card-header-tabs" id="leadMainTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="lead-form-tab" data-bs-toggle="tab" data-bs-target="#lead-form" type="button" role="tab">
                            Lead Form
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="lead-empty-tab" data-bs-toggle="tab" data-bs-target="#lead-empty" type="button" role="tab">
                            Other
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body p-4">
                <div class="tab-content">
                    {{-- TAB 1: FULL FORM --}}
                    <div class="tab-pane fade show active" id="lead-form" role="tabpanel">
                        <form id="leadForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="lead_id" name="lead_id">
                        <input type="hidden" name="latitude" id="lead_lat">
                        <input type="hidden" name="longitude" id="lead_lng">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name *</label>
                                <input type="text" name="full_name" class="form-control form-control-lg @error('full_name') is-invalid @enderror" placeholder="E.g. Alex Fernando" value="{{ old('full_name') }}">
                                <small class="text-muted">Use the customer's preferred name.</small>
                                @error('full_name')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="text" name="phone_number" class="form-control form-control-lg @error('phone_number') is-invalid @enderror" placeholder="07X XXX XXXX" value="{{ old('phone_number') }}">
                                <small class="text-muted">Primary contact number for follow-ups.</small>
                                @error('phone_number')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="name@email.com" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Loan Type *</label>
                                <select name="type" class="form-select form-control-lg select2-search @error('type') is-invalid @enderror">
                                    <option value="">Select Type</option>
                                    <option value="group" {{ old('type') == 'group' ? 'selected' : '' }}>Group</option>
                                    <option value="individual" {{ old('type') == 'individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="business" {{ old('type') == 'business' ? 'selected' : '' }}>Business</option>
                                    <option value="leasing" {{ old('type') == 'leasing' ? 'selected' : '' }}>Leasing</option>
                                </select>
                                <small class="text-muted">Select the lead type.</small>
                                @error('type')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Loan Amount *</label>
                                <input type="number" name="loan_amount" step="0.01" class="form-control form-control-lg @error('loan_amount') is-invalid @enderror" placeholder="E.g. 50000" value="{{ old('loan_amount') }}">
                                <small class="text-muted">Expected loan amount.</small>
                                @error('loan_amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Periods *</label>
                                <input type="text" name="periods" class="form-control form-control-lg @error('periods') is-invalid @enderror" placeholder="E.g. 12 months, 24 months" value="{{ old('periods') }}">
                                <small class="text-muted">Enter the loan period duration.</small>
                                @error('periods')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" placeholder="Street, City, District">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Location Coordinates</label>
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-compass"></i> Latitude</span>
                                            <input type="text" id="lead_lat_display" class="form-control" placeholder="Auto from GPS" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-compass"></i> Longitude</span>
                                            <input type="text" id="lead_lng_display" class="form-control" placeholder="Auto from GPS" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-4 d-flex gap-2">
                                        <button type="button" class="btn btn-outline-primary flex-grow-1" onclick="getLeadLocation()" style="height: 48px;">
                                            <i class="bi bi-geo-alt"></i> Get Location
                                        </button>
                                        <a href="#" id="viewMapBtn" target="_blank" class="btn btn-outline-info flex-grow-1" style="height: 48px; display: none; align-items: center; justify-content: center; text-decoration: none;">
                                            <i class="bi bi-map me-1"></i> View Map
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Notes</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="4" placeholder="Key pain points, interest level, next steps">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            {{-- Image Upload Fields (Dynamic based on app_settings) --}}
                            @if(isset($imageTypes) && count($imageTypes) > 0)
                            <div class="col-12">
                                <hr>
                                <h5>
                                    <i class="bi bi-images"></i> Document Images
                                </h5>
                                <div class="row g-3">
                                    @foreach($imageTypes as $imageType)
                                        @php
                                            $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                                            $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);
                                            $isRequired = isset($imageType['is_required']) && $imageType['is_required'];
                                        @endphp
                                        <div class="col-md-6">
                                            <label class="form-label">
                                                {{ $imageType['name'] }}
                                                @if($isRequired)
                                                    <span class="text-danger">*</span>
                                                @else
                                                    <span class="text-muted small">(Optional)</span>
                                                @endif
                                            </label>
                                            <div class="image-upload-wrapper">
                                                <input type="file" 
                                                       name="{{ $fieldName }}" 
                                                       id="{{ $fieldName }}"
                                                       class="d-none image-upload-input @error($fieldName) is-invalid @enderror" 
                                                       accept="image/*" 
                                                       capture="user"
                                                       onchange="handleImageUpload(this, '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')"
                                                       data-image-type="{{ $imageType['name'] }}">
                                                @error($fieldName)
                                                    <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                                                @enderror
                                                <input type="hidden" id="{{ $fieldName }}_lat" name="{{ $fieldName }}_latitude">
                                                <input type="hidden" id="{{ $fieldName }}_lng" name="{{ $fieldName }}_longitude">
                                                
                                                <div class="d-flex gap-2 mb-2 flex-wrap">
                                                    <button type="button" 
                                                            class="btn btn-primary btn-camera-capture" 
                                                            onclick="openWebcamModal('{{ $fieldName }}', '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')"
                                                            title="Capture from Webcam/Camera">
                                                        <i class="bi bi-camera-video"></i> Use Camera
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-info btn-camera-capture" 
                                                            onclick="triggerCamera('{{ $fieldName }}', 'environment')"
                                                            title="Capture from Rear Camera (Mobile)">
                                                        <i class="bi bi-camera"></i> Mobile Camera
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-outline-secondary btn-gallery-select" 
                                                            onclick="triggerFileSelect('{{ $fieldName }}')"
                                                            title="Select from Gallery">
                                                        <i class="bi bi-image"></i> Choose File
                                                    </button>
                                                </div>
                                                
                                                <div class="mt-2">
                                                    <img id="{{ $fieldName }}_preview" 
                                                         src="" 
                                                         alt="Preview" 
                                                         class="img-thumbnail d-none" 
                                                         style="max-height: 150px; width: auto;">
                                                </div>
                                                <div id="{{ $fieldName }}_location_status" class="mt-2"></div>
                                                <small class="text-muted d-block mt-1">
                                                    <i class="bi bi-info-circle"></i> Max 10MB. Supported: JPG, PNG, GIF, WebP. Location will be captured automatically.
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            
                            <div class="col-12 mt-3">
                                <div class="alert alert-info d-flex align-items-center mb-0">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <div>Tap "Get Location" to capture GPS. This will also allow you to view the position on Google Maps.</div>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>

                    {{-- TAB 2: EMPTY TAB (PLACEHOLDER) --}}
                    <div class="tab-pane fade" id="lead-empty" role="tabpanel">
                        <div class="text-center py-5">
                            <h5 class="mb-2">Empty Tab</h5>
                            <p class="text-muted mb-0">You can design this tab later for additional lead information or summary.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Webcam Capture Modal --}}
<div class="modal fade" id="webcamModal" tabindex="-1" aria-labelledby="webcamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="webcamModalLabel">
                    <i class="bi bi-camera-video"></i> Capture Photo from Webcam
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="closeWebcamModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="position-relative" style="background: #000; border-radius: 8px; overflow: hidden;">
                    <video id="webcamVideo" autoplay playsinline style="width: 100%; max-height: 480px; display: none;"></video>
                    <canvas id="webcamCanvas" style="display: none;"></canvas>
                    <div id="webcamPlaceholder" class="py-5 text-white">
                        <i class="bi bi-camera-video" style="font-size: 3rem;"></i>
                        <p class="mt-3">Initializing camera...</p>
                    </div>
                </div>
                <div id="capturedImagePreview" class="mt-3" style="display: none;">
                    <img id="capturedImg" src="" alt="Captured" class="img-thumbnail" style="max-width: 100%; max-height: 300px;">
                </div>
                <div id="webcamError" class="alert alert-danger mt-3" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeWebcamModal()">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <button type="button" class="btn btn-warning" id="switchCameraBtn" onclick="switchCamera()" style="display: none;">
                    <i class="bi bi-arrow-repeat"></i> Switch Camera
                </button>
                <button type="button" class="btn btn-info" id="retakeBtn" onclick="retakePhoto()" style="display: none;">
                    <i class="bi bi-arrow-counterclockwise"></i> Retake
                </button>
                <button type="button" class="btn btn-primary" id="captureBtn" onclick="capturePhoto()">
                    <i class="bi bi-camera"></i> Capture Photo
                </button>
                <button type="button" class="btn btn-success" id="usePhotoBtn" onclick="useCapturedPhoto()" style="display: none;">
                    <i class="bi bi-check-circle"></i> Use This Photo
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Choices.js JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Choices.js
        const element = document.querySelector('.select2-search');
        if(element){
            const choices = new Choices(element, {
                searchEnabled: true,
                itemSelectText: '',
                shouldSort: false,
                placeholder: true,
                placeholderValue: 'Select Type',
            });
        }
    });

// Global variables for webcam
const GOOGLE_MAPS_API_KEY = "{{ config('services.google_maps.key') }}";
let webcamStream = null;
let currentFieldId = null;
let currentPreviewId = null;
let currentLatFieldId = null;
let currentLngFieldId = null;
let facingMode = 'user'; // 'user' for front camera, 'environment' for rear

/* ================= OPEN WEBCAM MODAL ================= */
function openWebcamModal(fieldId, previewId, latFieldId, lngFieldId) {
    currentFieldId = fieldId;
    currentPreviewId = previewId;
    currentLatFieldId = latFieldId;
    currentLngFieldId = lngFieldId;
    
    // Reset modal state
    document.getElementById('capturedImagePreview').style.display = 'none';
    document.getElementById('captureBtn').style.display = 'inline-block';
    document.getElementById('usePhotoBtn').style.display = 'none';
    document.getElementById('retakeBtn').style.display = 'none';
    document.getElementById('webcamError').style.display = 'none';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('webcamModal'));
    modal.show();
    
    // Initialize webcam after modal is shown
    setTimeout(() => {
        startWebcam();
    }, 500);
}

/* ================= START WEBCAM ================= */
async function startWebcam() {
    const video = document.getElementById('webcamVideo');
    const placeholder = document.getElementById('webcamPlaceholder');
    const errorDiv = document.getElementById('webcamError');
    
    try {
        // Check if browser supports getUserMedia
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            throw new Error('Your browser does not support camera access. Please use a modern browser like Chrome, Firefox, or Edge.');
        }
        
        // Stop existing stream if any
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
        }
        
        // Request camera access
        webcamStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: facingMode,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            },
            audio: false
        });
        
        // Show video and hide placeholder
        video.srcObject = webcamStream;
        video.style.display = 'block';
        placeholder.style.display = 'none';
        errorDiv.style.display = 'none';
        
        // Show switch camera button if multiple cameras available
        checkCameraAvailability();
        
    } catch (error) {
        console.error('Error accessing webcam:', error);
        placeholder.style.display = 'none';
        errorDiv.style.display = 'block';
        
        let errorMessage = 'Unable to access camera. ';
        if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
            errorMessage += 'Please allow camera access in your browser settings.';
        } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
            errorMessage += 'No camera found. Please connect a camera and try again.';
        } else {
            errorMessage += error.message || 'Please check your camera connection.';
        }
        
        errorDiv.innerHTML = `<i class="bi bi-exclamation-triangle"></i> ${errorMessage}`;
    }
}

/* ================= CHECK CAMERA AVAILABILITY ================= */
async function checkCameraAvailability() {
    try {
        const devices = await navigator.mediaDevices.enumerateDevices();
        const videoDevices = devices.filter(device => device.kind === 'videoinput');
        
        // Show switch button if more than one camera
        if (videoDevices.length > 1) {
            document.getElementById('switchCameraBtn').style.display = 'inline-block';
        } else {
            document.getElementById('switchCameraBtn').style.display = 'none';
        }
    } catch (error) {
        console.error('Error checking cameras:', error);
    }
}

/* ================= SWITCH CAMERA ================= */
function switchCamera() {
    facingMode = facingMode === 'user' ? 'environment' : 'user';
    startWebcam();
}

/* ================= CAPTURE PHOTO ================= */
function capturePhoto() {
    const video = document.getElementById('webcamVideo');
    const canvas = document.getElementById('webcamCanvas');
    const preview = document.getElementById('capturedImagePreview');
    const capturedImg = document.getElementById('capturedImg');
    
    if (!webcamStream || !video.srcObject) {
        Swal.fire('Error', 'Camera not initialized. Please try again.', 'error');
        return;
    }
    
    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw current video frame to canvas
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Convert canvas to image
    const imageDataUrl = canvas.toDataURL('image/jpeg', 0.9);
    capturedImg.src = imageDataUrl;
    
    // Show preview and hide video
    preview.style.display = 'block';
    video.style.display = 'none';
    
    // Show/hide buttons
    document.getElementById('captureBtn').style.display = 'none';
    document.getElementById('usePhotoBtn').style.display = 'inline-block';
    document.getElementById('retakeBtn').style.display = 'inline-block';
    document.getElementById('switchCameraBtn').style.display = 'none';
}

/* ================= RETAKE PHOTO ================= */
function retakePhoto() {
    const video = document.getElementById('webcamVideo');
    const preview = document.getElementById('capturedImagePreview');
    
    // Show video and hide preview
    video.style.display = 'block';
    preview.style.display = 'none';
    
    // Show/hide buttons
    document.getElementById('captureBtn').style.display = 'inline-block';
    document.getElementById('usePhotoBtn').style.display = 'none';
    document.getElementById('retakeBtn').style.display = 'none';
    checkCameraAvailability();
}

/* ================= USE CAPTURED PHOTO ================= */
async function useCapturedPhoto() {
    const canvas = document.getElementById('webcamCanvas');
    const input = document.getElementById(currentFieldId);
    const preview = document.getElementById(currentPreviewId);
    const statusDiv = document.getElementById(currentFieldId + '_location_status');
    
    if (!canvas || !input) {
        Swal.fire('Error', 'Unable to process photo. Please try again.', 'error');
        closeWebcamModal();
        return;
    }
    
    // Convert canvas to blob
    canvas.toBlob(async function(blob) {
        if (!blob) {
            Swal.fire('Error', 'Unable to create image file.', 'error');
            return;
        }
        
        // Create a File object from blob
        const file = new File([blob], `captured_${Date.now()}.jpg`, { type: 'image/jpeg' });
        
        // Create a DataTransfer object to set the file
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        input.files = dataTransfer.files;
        
        // Close modal first
        closeWebcamModal();
        
        // Show loading status
        statusDiv.innerHTML = '<small class="text-info"><i class="bi bi-hourglass-split"></i> Capturing location...</small>';
        
        // Get location and validate
        if (!navigator.geolocation) {
            Swal.fire({
                icon: 'error',
                title: 'Geolocation Not Supported',
                text: 'Your browser does not support geolocation.',
                confirmButtonText: 'OK'
            });
            input.value = '';
            statusDiv.innerHTML = '';
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            position => {
                const imageLat = position.coords.latitude;
                const imageLng = position.coords.longitude;
                const imageTypeName = input.getAttribute('data-image-type');
                
                // Check if location matches lead location
                if (!checkImageLocation(imageLat, imageLng, imageTypeName)) {
                    input.value = '';
                    preview.classList.add('d-none');
                    document.getElementById(currentLatFieldId).value = '';
                    document.getElementById(currentLngFieldId).value = '';
                    statusDiv.innerHTML = '';
                    return;
                }
                
                // Location matches, store coordinates and show preview
                document.getElementById(currentLatFieldId).value = imageLat;
                document.getElementById(currentLngFieldId).value = imageLng;
                
                // Show success status
                statusDiv.innerHTML = '<small class="text-success"><i class="bi bi-check-circle"></i> Location verified</small>';
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            },
            error => {
                let errorMsg = 'Unable to get location. ';
                if (error.code === 1) {
                    errorMsg += 'Location permission is required.';
                } else if (error.code === 2) {
                    errorMsg += 'Location is unavailable.';
                } else {
                    errorMsg += 'Location request timed out.';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Location Error',
                    text: errorMsg,
                    confirmButtonText: 'OK'
                });
                
                input.value = '';
                preview.classList.add('d-none');
                document.getElementById(currentLatFieldId).value = '';
                document.getElementById(currentLngFieldId).value = '';
                statusDiv.innerHTML = '';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }, 'image/jpeg', 0.9);
}

/* ================= CLOSE WEBCAM MODAL ================= */
function closeWebcamModal() {
    // Stop webcam stream
    if (webcamStream) {
        webcamStream.getTracks().forEach(track => track.stop());
        webcamStream = null;
    }
    
    // Hide video
    const video = document.getElementById('webcamVideo');
    video.srcObject = null;
    video.style.display = 'none';
    
    // Reset modal
    document.getElementById('webcamPlaceholder').style.display = 'block';
    document.getElementById('capturedImagePreview').style.display = 'none';
    
    // Hide modal
    const modalElement = document.getElementById('webcamModal');
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) {
        modal.hide();
    }
}

/* ================= TRIGGER CAMERA CAPTURE (Mobile) ================= */
function triggerCamera(fieldId, captureType = 'user') {
    const input = document.getElementById(fieldId);
    if (input) {
        // Set capture attribute based on type
        // 'user' = front-facing camera/webcam (desktop/laptop)
        // 'environment' = rear-facing camera (mobile)
        input.setAttribute('capture', captureType);
        
        // Clear any previous value to ensure the change event fires
        input.value = '';
        
        // Trigger file input click
        input.click();
    }
}

/* ================= TRIGGER FILE SELECT (GALLERY) ================= */
function triggerFileSelect(fieldId) {
    const input = document.getElementById(fieldId);
    if (input) {
        // Remove capture attribute to allow gallery selection
        input.removeAttribute('capture');
        input.click();
    }
}

/* ================= LEAD LOCATION + OPEN MAP ================= */
function getLeadLocation() {
    if (!navigator.geolocation) {
        Swal.fire('Error', 'Geolocation is not supported by this browser.', 'error');
        return;
    }

    const btn = $('button[onclick="getLeadLocation()"]');
    const originalContent = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Fetching...');

    navigator.geolocation.getCurrentPosition(
        async pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            // Store raw GPS
            $('#lead_lat').val(lat);
            $('#lead_lng').val(lng);
            $('#lead_lat_display').val(lat);
            $('#lead_lng_display').val(lng);

            // Update View Map Button if it exists (Google Maps)
            const mapUrl = `https://www.google.com/maps/search/?api=1&query=${lat},${lng}`;
            $('#viewMapBtn').attr('href', mapUrl).css('display', 'flex');

            // Reverse geocode using Google Maps
            if (GOOGLE_MAPS_API_KEY) {
                try {
                    const response = await fetch(
                        `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${GOOGLE_MAPS_API_KEY}`
                    );

                    const data = await response.json();

                    if (data.status === 'OK' && data.results && data.results.length > 0) {
                        const address = data.results[0].formatted_address;
                        $('textarea[name="address"]').val(address);
                        Swal.fire({
                            icon: 'success',
                            title: 'Location Verified',
                            text: `Address found: ${address}`,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        console.warn('Google Maps API Error:', data);
                        Swal.fire('Location Captured', 'Coordinates saved, but address could not be resolved.', 'success');
                    }

                } catch (e) {
                    console.error('Google Maps error', e);
                    Swal.fire('Location Captured', 'Coordinates saved. (Map API error)', 'success');
                }
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Location Captured',
                    text: 'Coordinates saved successfully.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
            
            btn.prop('disabled', false).html(originalContent);
            
            // Optionally open map automatically
            window.open(mapUrl, '_blank');
        },
        err => {
            btn.prop('disabled', false).html(originalContent);
            let msg = 'Unable to get your location.';
            if (err.code === 1) msg = 'Location permission is required.';
            Swal.fire('Error', msg, 'error');
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

/* ================= CALCULATE DISTANCE BETWEEN TWO COORDINATES (Haversine formula) ================= */
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371e3; // Earth radius in meters
    const φ1 = lat1 * Math.PI/180;
    const φ2 = lat2 * Math.PI/180;
    const Δφ = (lat2-lat1) * Math.PI/180;
    const Δλ = (lon2-lon1) * Math.PI/180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c; // Distance in meters
}

/* ================= CHECK IMAGE LOCATION ================= */
function checkImageLocation(imageLat, imageLng, imageTypeName) {
    const leadLat = parseFloat($('#lead_lat').val());
    const leadLng = parseFloat($('#lead_lng').val());
    
    // If lead location is not captured yet
    if (!leadLat || !leadLng || isNaN(leadLat) || isNaN(leadLng)) {
        Swal.fire({
            icon: 'warning',
            title: 'Lead Location Required',
            text: 'Please capture the lead location first using "Get Location" button before uploading images.',
            confirmButtonText: 'OK'
        });
        return false;
    }
    
    // Calculate distance between lead location and image location
    const distance = calculateDistance(leadLat, leadLng, imageLat, imageLng);
    const distanceKm = (distance / 1000).toFixed(2); // Convert to kilometers
    
    // Allow distance tolerance of 100 meters (0.1 km) - you can adjust this
    const tolerance = 100; // meters
    
    if (distance > tolerance) {
        Swal.fire({
            icon: 'error',
            title: 'Location Mismatch!',
            html: `
                <p>The image location is different from the lead location.</p>
                <p><strong>Lead Location:</strong> ${leadLat.toFixed(6)}, ${leadLng.toFixed(6)}</p>
                <p><strong>Image Location:</strong> ${imageLat.toFixed(6)}, ${imageLng.toFixed(6)}</p>
                <p><strong>Distance:</strong> ${distanceKm} km</p>
                <p class="text-danger mt-2">Please capture the image at the lead location to proceed.</p>
            `,
            confirmButtonText: 'OK'
        });
        return false;
    }
    
    return true;
}

/* ================= HANDLE IMAGE UPLOAD WITH LOCATION CHECK ================= */
function handleImageUpload(input, previewId, latFieldId, lngFieldId) {
    const file = input.files[0];
    const statusDiv = document.getElementById(input.id + '_location_status');
    const imageTypeName = input.getAttribute('data-image-type');
    
    if (!file) {
        document.getElementById(previewId).classList.add('d-none');
        document.getElementById(latFieldId).value = '';
        document.getElementById(lngFieldId).value = '';
        statusDiv.innerHTML = '';
        return;
    }
    
    // Show loading status
    statusDiv.innerHTML = '<small class="text-info"><i class="bi bi-hourglass-split"></i> Capturing location...</small>';
    
    // Get current location
    if (!navigator.geolocation) {
        Swal.fire({
            icon: 'error',
            title: 'Geolocation Not Supported',
            text: 'Your browser does not support geolocation. Please enable it to upload images.',
            confirmButtonText: 'OK'
        });
        input.value = '';
        statusDiv.innerHTML = '';
        return;
    }
    
    navigator.geolocation.getCurrentPosition(
        position => {
            const imageLat = position.coords.latitude;
            const imageLng = position.coords.longitude;
            
            // Check if location matches lead location
            if (!checkImageLocation(imageLat, imageLng, imageTypeName)) {
                // Location doesn't match, clear the file input
                input.value = '';
                document.getElementById(previewId).classList.add('d-none');
                document.getElementById(latFieldId).value = '';
                document.getElementById(lngFieldId).value = '';
                statusDiv.innerHTML = '';
                return;
            }
            
            // Location matches, store coordinates and show preview
            document.getElementById(latFieldId).value = imageLat;
            document.getElementById(lngFieldId).value = imageLng;
            
            // Show success status
            statusDiv.innerHTML = '<small class="text-success"><i class="bi bi-check-circle"></i> Location verified</small>';
            
            // Preview the image
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
                document.getElementById(previewId).classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        },
        error => {
            let errorMsg = 'Unable to get location. ';
            if (error.code === 1) {
                errorMsg += 'Location permission is required. Please allow location access.';
            } else if (error.code === 2) {
                errorMsg += 'Location is unavailable.';
            } else {
                errorMsg += 'Location request timed out.';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Location Error',
                text: errorMsg,
                confirmButtonText: 'OK'
            });
            
            input.value = '';
            document.getElementById(previewId).classList.add('d-none');
            document.getElementById(latFieldId).value = '';
            document.getElementById(lngFieldId).value = '';
            statusDiv.innerHTML = '';
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

/* ================= PREVIEW IMAGE (Legacy function kept for compatibility) ================= */
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.classList.add('d-none');
    }
}

/* ================= SAVE LEAD ================= */
function saveLead() {
    $('#saveLeadBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

    // Clear previous validation errors
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();

    // Create FormData for file uploads
    const formData = new FormData($('#leadForm')[0]);

    $.ajax({
        url: "{{ route('leads.store') }}",
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(res) {
            if (res.success) {
                $('#lead_id').val(res.lead_id);
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: res.message || 'Lead saved successfully',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Optionally reset form or redirect
                    // window.location.reload();
                });
            } else {
                Swal.fire('Error', res.message || 'Unable to save lead', 'error');
            }
        },
        error: function(xhr) {
            // Handle Laravel validation errors
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                
                // Display errors under each field
                $.each(errors, function(field, messages) {
                    const errorMsg = Array.isArray(messages) ? messages[0] : messages;
                    
                    // Find input/select/textarea by name
                    let input = $(`[name="${field}"]`);
                    
                    // Handle file inputs and image fields differently
                    if (input.length === 0 || input.is('input[type="file"]')) {
                        // For file inputs, find the parent wrapper
                        const wrapper = $(`[name="${field}"]`).closest('.image-upload-wrapper');
                        if (wrapper.length) {
                            input = wrapper.find(`[name="${field}"]`);
                        }
                    }
                    
                    if (input.length) {
                        // Add invalid class to input
                        input.addClass('is-invalid');
                        
                        // Remove existing error message
                        input.parent().find('.invalid-feedback').remove();
                        
                        // Add error message below input
                        const errorDiv = $('<div class="invalid-feedback d-block text-danger mt-1"></div>');
                        errorDiv.text(errorMsg);
                        
                        // Insert after input or in appropriate location
                        if (input.is('input[type="file"]')) {
                            input.closest('.image-upload-wrapper').append(errorDiv);
                        } else {
                            input.after(errorDiv);
                        }
                    }
                });
                
                // Show alert with summary
                // const errorCount = Object.keys(errors).length;
                // Swal.fire({
                //     icon: 'error',
                //     title: 'Validation Error',
                //     html: `<p>Please fix the ${errorCount} error(s) in the form below.</p>`,
                //     confirmButtonText: 'OK'
                // });
                
                // Scroll to first error
                setTimeout(function() {
                    const firstError = $('.is-invalid').first();
                    if (firstError.length) {
                        $('html, body').animate({
                            scrollTop: firstError.offset().top - 150
                        }, 500);
                    }
                }, 300);
                
            } else {
                let errorMessage = 'Unable to save lead right now';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    confirmButtonText: 'OK'
                });
            }
        },
        complete: function() {
            $('#saveLeadBtn').prop('disabled', false).html('<i class="bi bi-check2-circle"></i> Save Lead');
        }
    });
}
</script>
@endsection

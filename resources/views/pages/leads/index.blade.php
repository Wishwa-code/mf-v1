@extends('layout.admin')

@section('content')
<style>
    .image-upload-wrapper {
        position: relative;
    }
    
    .image-upload-input {
        transition: all 0.3s ease;
        border: 2px dashed #dee2e6;
        padding: 12px;
        cursor: pointer;
    }
    
    .image-upload-input:hover {
        border-color: #0d6efd;
        background-color: #f8f9fa;
    }
    
    .image-upload-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .img-thumbnail {
        border: 2px solid #dee2e6;
        border-radius: 8px;
        transition: transform 0.2s ease;
    }
    
    .img-thumbnail:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .lead-form .card {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .lead-form .form-control-lg {
        border-radius: 8px;
    }
    
    .lead-form .form-control {
        border-radius: 6px;
    }
    
    .lead-form .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link {
        border-radius: 8px 8px 0 0;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: transparent;
    }
    
    .btn-camera-capture {
        flex: 1;
        font-weight: 500;
        transition: all 0.3s ease;
        min-width: 140px;
    }
    
    .btn-camera-capture:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    .btn-gallery-select {
        flex: 1;
        font-weight: 500;
        transition: all 0.3s ease;
        min-width: 140px;
    }
    
    .btn-gallery-select:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    
    @media (max-width: 768px) {
        .btn-camera-capture,
        .btn-gallery-select {
            font-size: 0.875rem;
            padding: 8px 12px;
            min-width: auto;
            flex: 1 1 100%;
        }
        
        .d-flex.gap-2.mb-2.flex-wrap {
            flex-direction: column;
        }
    }
</style>

{{-- MAIN TABS --}}
<ul class="nav nav-tabs" id="leadMainTabs" role="tablist">
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

<div class="tab-content mt-3">
    {{-- TAB 1: FULL FORM --}}
    <div class="tab-pane fade show active" id="lead-form" role="tabpanel">
        <form id="leadForm" enctype="multipart/form-data" class="lead-form">
        @csrf

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill px-3 py-2">Lead Capture</span>
                            <span class="badge bg-light text-dark border">Customer First</span>
                        </div>
                        <h4 class="mt-3 mb-1">Create a new lead</h4>
                        <p class="text-muted mb-0">Capture contact details and location in one simple form.</p>
                    </div>
                    <div class="text-end">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="getLeadLocation()">
                            <i class="bi bi-geo-alt"></i> Get Location
                        </button>
                        <button type="button" class="btn btn-success" onclick="saveLead()" id="saveLeadBtn">
                            <i class="bi bi-check2-circle"></i> Save Lead
                        </button>
                    </div>
                </div>

                <input type="hidden" id="lead_id" name="lead_id">
                <input type="hidden" name="latitude" id="lead_lat">
                <input type="hidden" name="longitude" id="lead_lng">

                <div class="row g-3 mt-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="full_name" class="form-control form-control-lg" placeholder="E.g. Alex Fernando" required>
                        <small class="text-muted">Use the customer's preferred name.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone_number" class="form-control form-control-lg" placeholder="+94 7X XXX XXXX" required>
                        <small class="text-muted">Primary contact number for follow-ups.</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="name@email.com">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="3" placeholder="Street, City, District"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Latitude</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-compass"></i></span>
                            <input type="text" id="lead_lat_display" class="form-control" placeholder="Auto from GPS" readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Longitude</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-compass"></i></span>
                            <input type="text" id="lead_lng_display" class="form-control" placeholder="Auto from GPS" readonly>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Key pain points, interest level, next steps"></textarea>
                    </div>
                    
                    {{-- Image Upload Fields (Dynamic based on app_settings) --}}
                    @if(isset($imageTypes) && count($imageTypes) > 0)
                    <div class="col-12">
                        <hr class="my-4">
                        <h5 class="mb-3">
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
                                               class="d-none image-upload-input" 
                                               accept="image/*" 
                                               capture="user"
                                               @if($isRequired) required @endif
                                               onchange="handleImageUpload(this, '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')"
                                               data-image-type="{{ $imageType['name'] }}">
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
                    
                    <div class="col-12">
                        <div class="alert alert-info d-flex align-items-center mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            <div>Tap "Get Location" to capture GPS, it will also open your position in the map.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>

    {{-- TAB 2: EMPTY TAB (PLACEHOLDER) --}}
    <div class="tab-pane fade" id="lead-empty" role="tabpanel">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <h5 class="mb-2">Empty Tab</h5>
                <p class="text-muted mb-0">You can design this tab later for additional lead information or summary.</p>
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

<script>
// Global variables for webcam
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
        alert('Geolocation is not supported by this browser.');
        return;
    }

    navigator.geolocation.getCurrentPosition(
        pos => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            $('#lead_lat').val(lat);
            $('#lead_lng').val(lng);
            $('#lead_lat_display').val(lat);
            $('#lead_lng_display').val(lng);

            // Open OpenStreetMap with current position
            const mapUrl = `https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}#map=18/${lat}/${lng}`;
            window.open(mapUrl, '_blank');
        },
        err => {
            if (err.code === 1) {
                alert('Location permission is required. Please allow it in your browser.');
            } else {
                alert('Unable to get your location. Please try again.');
            }
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
    // Validate form
    if (!$('#leadForm')[0].checkValidity()) {
        $('#leadForm')[0].reportValidity();
        return;
    }

    $('#saveLeadBtn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

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
                    // $('#leadForm')[0].reset();
                    // $('.img-thumbnail').addClass('d-none');
                });
            } else {
                Swal.fire('Error', res.message || 'Unable to save lead', 'error');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Unable to save lead right now';
            
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                // Laravel validation errors
                const errors = xhr.responseJSON.errors;
                const errorList = Object.values(errors).flat().join('<br>');
                errorMessage = errorList;
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: errorMessage
            });
        },
        complete: function() {
            $('#saveLeadBtn').prop('disabled', false).html('<i class="bi bi-check2-circle"></i> Save Lead');
        }
    });
}
</script>
@endsection

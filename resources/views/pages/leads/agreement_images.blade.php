@extends('layout.admin')

@section('content')
<style>
    /* Modern Form Styling (from user request) */
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
    
    .lead-form .small.text-muted {
        font-size: 0.85rem;
        color: #666;
        margin-top: 0.25rem;
        display: block;
    }
    
    .lead-form h5 {
        font-weight: 600;
        color: #333;
        margin-bottom: 1.5rem;
        font-size: 1.1rem;
    }
    
    /* Image Upload Styling */
    .image-upload-wrapper {
        position: relative;
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
    
    /* Responsive */
    @media (max-width: 768px) {
        .card-body { padding: 1.5rem !important; }
        .d-flex.gap-2.mb-2.flex-wrap { flex-direction: column; }
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
</style>
<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />

<div class="row mt-4">
    <div class="col-md-7">
        <div class="card shadow-sm border-0 lead-form">
            <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                <h4 class="mb-1">Upload Agreement Images</h4>
                <p class="text-muted small mb-0">Select a lead and upload agreement documents.</p>
            </div>

            <div class="card-body p-4">
                <form id="agreementForm" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="form-label">Select Lead *</label>
                        <select id="lead_select" name="lead_id" class="form-select form-control-lg select2-search">
                            <option value="">Search and select a lead...</option>
                            @foreach($leads as $lead)
                                <option value="{{ $lead->id }}">{{ $lead->full_name }} - {{ $lead->phone_number }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Search by name or phone number.</small>
                    </div>

                    <div id="image_upload_section" style="display: none;">
                        <hr class="my-4">
                        
                        @if(isset($imageTypes) && count($imageTypes) > 0)
                            <h5 class="mb-3"><i class="bi bi-images me-2"></i>Agreement Documents</h5>
                            <div class="row g-4">
                                @foreach($imageTypes as $imageType)
                                    @php
                                        $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                                        $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);
                                        $isRequired = isset($imageType['is_required']) && $imageType['is_required'];
                                    @endphp
                                    <div class="col-12">
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
                                                   onchange="handleImageUpload(this, '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')"
                                                   data-image-type="{{ $imageType['name'] }}">
                                            
                                            <input type="hidden" id="{{ $fieldName }}_lat" name="{{ $fieldName }}_latitude">
                                            <input type="hidden" id="{{ $fieldName }}_lng" name="{{ $fieldName }}_longitude">
                                            
                                            <div class="d-flex gap-2 mb-2 flex-wrap">
                                                <button type="button" 
                                                        class="btn btn-primary btn-camera-capture" 
                                                        onclick="openWebcamModal('{{ $fieldName }}', '{{ $fieldName }}_preview', '{{ $fieldName }}_lat', '{{ $fieldName }}_lng')"
                                                        title="Capture from Webcam">
                                                    <i class="bi bi-camera-video"></i> Use Camera
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-info btn-camera-capture" 
                                                        onclick="triggerCamera('{{ $fieldName }}', 'environment')"
                                                        title="Capture from Rear Camera">
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
                                                     style="max-height: 200px; width: auto;">
                                            </div>
                                            <div id="{{ $fieldName }}_location_status" class="mt-2"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <hr class="my-4">
                            <button type="button" class="btn btn-success w-100 py-2" onclick="saveAgreementImages()" id="btnSaveAgreement">
                                <i class="bi bi-check2-circle me-1"></i> Upload Images
                            </button>
                        @else
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i> No Agreement Image Types configured in Settings.
                            </div>
                        @endif
                    </div>
                    
                    <div id="select_lead_msg" class="text-center py-5 text-muted">
                        <i class="bi bi-person-check" style="font-size: 3rem;"></i>
                        <p class="mt-3">Please select a lead to proceed.</p>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <!-- Right Side: Lead Details -->
    <div class="col-md-5">
        <div class="card shadow-sm border-0 lead-form h-100">
            <div class="card-header bg-white border-bottom pt-4 px-4 pb-3">
                <h5 class="mb-0">Lead Details</h5>
            </div>
            <div class="card-body p-4" id="lead_details_container">
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-info-circle" style="font-size: 3rem;"></i>
                    <p class="mt-3">Select a lead to view details here.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Webcam Capture Modal (Reused) --}}
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
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Choices.js for Lead Selection
        const element = document.getElementById('lead_select');
        const choices = new Choices(element, {
            searchEnabled: true,
            itemSelectText: '',
            shouldSort: false,
            placeholder: true,
            placeholderValue: 'Search and select a lead...',
        });

        // Handle Change
        element.addEventListener('change', function(event) {
            const leadId = event.detail.value;
            if (leadId) {
                loadLeadDetails(leadId);
                $('#image_upload_section').slideDown();
                $('#select_lead_msg').slideUp();
            } else {
                $('#image_upload_section').slideUp();
                $('#select_lead_msg').slideDown();
                resetLeadDetails();
            }
        });
    });

    function loadLeadDetails(leadId) {
        $('#lead_details_container').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Loading details...</p></div>');
        
        $.ajax({
            url: "/api/leads/" + leadId + "/details",
            method: "GET",
            success: function(res) {
                if(res.success) {
                    const html = `
                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Full Name</label>
                            <div class="fs-5 text-dark fw-medium">${res.full_name || '-'}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Phone Number</label>
                            <div class="fs-5 text-dark">${res.phone_number || '-'}</div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted text-uppercase fw-bold">Address</label>
                            <div class="text-dark">${res.address || '-'}</div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <label class="small text-muted text-uppercase fw-bold">Latitude</label>
                                <div>${res.latitude || '-'}</div>
                            </div>
                            <div class="col-6">
                                <label class="small text-muted text-uppercase fw-bold">Longitude</label>
                                <div>${res.longitude || '-'}</div>
                            </div>
                        </div>
                        ${res.latitude ? `
                        <div class="mt-3">
                             <a href="https://www.google.com/maps/search/?api=1&query=${res.latitude},${res.longitude}" target="_blank" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-geo-alt me-1"></i> View on Map
                            </a>
                        </div>
                        ` : ''}
                    `;
                    $('#lead_details_container').html(html);
                }
            },
            error: function() {
                $('#lead_details_container').html('<div class="alert alert-danger">Failed to load lead details.</div>');
            }
        });
    }

    function resetLeadDetails() {
        $('#lead_details_container').html(`
            <div class="text-center py-5 text-muted">
                <i class="bi bi-info-circle" style="font-size: 3rem;"></i>
                <p class="mt-3">Select a lead to view details here.</p>
            </div>
        `);
    }

    // --- Webcam & Image Logic (Copied and adapted from create.blade.php) ---
    // Make sure to include the webcam functions (openWebcamModal, startWebcam, etc.)
    // For brevity in this turn, I will assume the same JS logic functions are needed.
    
    let webcamStream = null;
    let currentFieldId = null;
    let currentPreviewId = null;
    let currentLatFieldId = null;
    let currentLngFieldId = null;
    let facingMode = 'user'; 

    function openWebcamModal(fieldId, previewId, latFieldId, lngFieldId) {
        currentFieldId = fieldId;
        currentPreviewId = previewId;
        currentLatFieldId = latFieldId;
        currentLngFieldId = lngFieldId;
        
        document.getElementById('capturedImagePreview').style.display = 'none';
        document.getElementById('captureBtn').style.display = 'inline-block';
        document.getElementById('usePhotoBtn').style.display = 'none';
        
        const modal = new bootstrap.Modal(document.getElementById('webcamModal'));
        modal.show();
        
        setTimeout(() => startWebcam(), 500);
    }
    
    async function startWebcam() {
        const video = document.getElementById('webcamVideo');
        if (webcamStream) webcamStream.getTracks().forEach(track => track.stop());
        
        try {
            webcamStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode }, audio: false });
            video.srcObject = webcamStream;
            video.style.display = 'block';
            document.getElementById('webcamPlaceholder').style.display = 'none';
        } catch (err) {
            console.error(err);
            document.getElementById('webcamError').style.display = 'block';
            document.getElementById('webcamError').innerText = "Camera access denied or missing.";
        }
    }

    function capturePhoto() {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        
        document.getElementById('capturedImg').src = canvas.toDataURL('image/jpeg');
        document.getElementById('capturedImagePreview').style.display = 'block';
        video.style.display = 'none';
        
        document.getElementById('captureBtn').style.display = 'none';
        document.getElementById('usePhotoBtn').style.display = 'inline-block';
    }

    function closeWebcamModal() {
        if(webcamStream) webcamStream.getTracks().forEach(t => t.stop());
        webcamStream = null;
        const el = document.getElementById('webcamModal');
        const modal = bootstrap.Modal.getInstance(el);
        if(modal) modal.hide();
    }
    
    function switchCamera() {
        facingMode = facingMode === 'user' ? 'environment' : 'user';
        startWebcam();
    }
    
    function retakePhoto() {
        // Simple logic to show video again
        document.getElementById('webcamVideo').style.display = 'block';
        document.getElementById('capturedImagePreview').style.display = 'none';
        document.getElementById('captureBtn').style.display = 'inline-block';
        document.getElementById('usePhotoBtn').style.display = 'none';
    }

    async function useCapturedPhoto() {
        const canvas = document.getElementById('webcamCanvas');
        canvas.toBlob(blob => {
            const file = new File([blob], "captured_"+Date.now()+".jpg", {type: "image/jpeg"});
            const container = new DataTransfer();
            container.items.add(file);
            document.getElementById(currentFieldId).files = container.files;
            
            // Preview
            const reader = new FileReader();
            reader.onload = e => {
                 const img = document.getElementById(currentPreviewId);
                 img.src = e.target.result;
                 img.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
            
            closeWebcamModal();
            captureLocation(); // Auto capture location on photo use
        }, 'image/jpeg');
    }
    
    // File Input Trigger
    function triggerFileSelect(id) { document.getElementById(id).click(); }
    function triggerCamera(id, mode) { 
        const el = document.getElementById(id); 
        el.setAttribute('capture', mode); 
        el.click(); 
    }
    
    function handleImageUpload(input, previewId, latId, lngId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                img.src = e.target.result;
                img.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
            
            // Auto capture location
            currentLatFieldId = latId;
            currentLngFieldId = lngId;
            captureLocation(); 
        }
    }
    
    function captureLocation() {
        if (!navigator.geolocation) return;
        
        // Find visible status div
        let statusDiv = null;
        if(currentFieldId) statusDiv = document.getElementById(currentFieldId + '_location_status');
        
        navigator.geolocation.getCurrentPosition(pos => {
            if(currentLatFieldId) $(`#${currentLatFieldId}`).val(pos.coords.latitude);
            if(currentLngFieldId) $(`#${currentLngFieldId}`).val(pos.coords.longitude);
            if(statusDiv) statusDiv.innerHTML = '<small class="text-success"><i class="bi bi-check"></i> Location Captured</small>';
        }, err => {
            console.error(err);
            if(statusDiv) statusDiv.innerHTML = '<small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Location Failed</small>';
        });
    }

    function saveAgreementImages() {
        const btn = $('#btnSaveAgreement');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Uploading...');
        
        const formData = new FormData($('#agreementForm')[0]);
        
        $.ajax({
            url: "{{ route('leads.agreementImages.upload') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if(res.success) {
                    Swal.fire('Success', res.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function(xhr) {
                let msg = 'Upload failed.';
                if(xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire('Error', msg, 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Upload Images');
            }
        });
    }
</script>
@endsection

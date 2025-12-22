@extends('layout.admin')

@section('head')
<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<style>
    /* Modern Card Styling */
    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #fff;
        /* overflow: hidden; Removed to allow dropdown to expand */
        margin-bottom: 1.5rem;
    }

    .verified-header {
        background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
        color: white;
        padding: 2.5rem;
        border-radius: 16px;
        margin-bottom: 2rem;
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

    /* Upload Area */
    .upload-area {
        border: 2px dashed #e0e0e0;
        border-radius: 12px;
        padding: 1.5rem;
        background: #fff;
        transition: all 0.2s;
        text-align: center;
    }

    .upload-area:hover {
        border-color: #556ee6;
        background: #f8f9fa;
    }

    .upload-area.disabled {
        pointer-events: none;
        opacity: 0.6;
        background: #f1f1f1;
    }

    .btn-modern {
        border-radius: 10px;
        padding: 8px 16px;
        font-weight: 600;
        transition: all 0.3s;
    }
</style>
@endsection

@section('content')

<div class="row mt-4">
    <div class="col-12">
        <div class="verified-header text-center">
            <h2 class="fw-bold mb-2">Lead Field Verification</h2>
            <p class="mb-0 text-white-50">Select a lead, visit the location, and verify details.</p>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <!-- Step 1: Select Lead -->
        <div class="card card-modern">
            <div class="card-body p-4">
                <div class="section-header">
                    <div class="section-icon bg-primary-subtle text-primary">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="mb-0 fw-bold text-dark">Step 1: Select Lead</h5>
                        <small class="text-muted">Choose a pending lead to start verification</small>
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">Pending</span>
                </div>

                @if($leads->isEmpty())
                <div class="alert alert-success text-center border-0 bg-success-subtle text-success py-4">
                    <i class="bi bi-check-circle-fill fs-1 mb-2 d-block"></i>
                    <h5 class="fw-bold">All Caught Up!</h5>
                    <p class="mb-0">There are no pending leads requiring verification at this moment.</p>
                </div>
                @else
                <div class="mb-2">
                    <label class="form-label fw-bold text-dark">Search Customer</label>
                    <select class="form-control" id="lead_select">
                        <option value="">Select a lead to verify...</option>
                        @foreach($leads as $lead)
                        <option value="{{ $lead->id }}">{{ $lead->full_name }} - {{ $lead->phone_number }} ({{ $lead->address }})</option>
                        @endforeach
                    </select>
                    <div class="form-text mt-2"><i class="bi bi-search me-1"></i> Type name or phone number to search.</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Lead Details & Verification (Hidden initially) -->
        <div id="verification_section" style="display: none;">

            <div class="card card-modern border-0">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="section-icon bg-info-subtle text-info">
                            <i class="bi bi-info-circle-fill"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Lead Details</h5>
                    </div>

                    <input type="hidden" id="selected_lead_id">
                    <input type="hidden" id="lead_lat">
                    <input type="hidden" id="lead_lng">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Full Name</label>
                            <div class="fs-5 fw-bold text-dark" id="disp_name"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted text-uppercase fw-bold">Phone Number</label>
                            <div class="fs-5 text-dark" id="disp_phone"></div>
                        </div>
                        <div class="col-12">
                            <label class="small text-muted text-uppercase fw-bold">Address</label>
                            <div class="text-dark bg-light p-3 rounded-3" id="disp_address"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Location & Comment -->
            <div class="card card-modern">
                <div class="card-body p-4">
                    <div class="section-header">
                        <div class="section-icon bg-success-subtle text-success">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Step 2: Verification</h5>
                    </div>

                    <div class="text-center mb-4">
                        <div id="locationStatus" class="mt-3">
                            <div class="p-3 bg-light rounded border text-muted">
                                <span class="spinner-border spinner-border-sm me-2"></span> Waiting to detect location...
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-link text-decoration-none" onclick="detectUserLocation()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Refresh Location
                            </button>
                        </div>
                    </div>

                    <form id="verifyForm">
                        <input type="hidden" id="visited_latitude" name="visited_latitude">
                        <input type="hidden" id="visited_longitude" name="visited_longitude">

                        <div class="mb-4">
                            <label class="form-label fw-bold">Visit Comments</label>
                            <textarea class="form-control" id="visit_notes" name="visit_notes" rows="4"
                                placeholder="Verification disabled. Please detect location first..." disabled></textarea>
                            <div class="form-text">You must be within 300 meters of the lead to verify.</div>
                        </div>


                        <div class="mb-4">
                            <label class="form-label fw-bold mb-2">Verification Evidence</label>

                            <div class="upload-area disabled" id="verification_upload_area">
                                <div class="d-flex justify-content-center align-items-center mb-3">
                                    <div class="bg-light rounded-circle p-3 text-primary">
                                        <i class="bi bi-camera-fill fs-3"></i>
                                    </div>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Upload Photo</h6>
                                <p class="text-muted small mb-3">Take a photo or upload file</p>

                                <input class="d-none" type="file" id="verification_image" name="verification_image" accept="image/*" onchange="handleFileSelect(this)">

                                <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 mb-3">
                                    <button type="button" class="btn btn-outline-primary btn-modern flex-grow-1 flex-sm-grow-0 px-4" id="openWebcamBtn" disabled>
                                        <i class="bi bi-camera-video me-1"></i> Camera
                                    </button>
                                    <button type="button" class="btn btn-light btn-modern border flex-grow-1 flex-sm-grow-0 px-4" id="openFileBtn" onclick="$('#verification_image').click()" disabled>
                                        <i class="bi bi-folder2-open me-1"></i> Select File
                                    </button>
                                </div>

                                <div id="capturedImagePreview" class="d-none position-relative d-inline-block">
                                    <img src="" class="img-thumbnail rounded-3 shadow-sm border-0" style="max-height: 120px; object-fit: cover;">
                                    <button type="button" class="btn-close position-absolute top-0 end-0 bg-white shadow-sm p-1 m-1"
                                        aria-label="Remove" onclick="resetCapture()"></button>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="button" class="btn btn-primary btn-lg rounded-pill shadow-sm" id="submitVerificationBtn" disabled>
                                <i class="bi bi-check-circle-fill me-2"></i> Mark as Verified
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- Webcam Modal -->
<div class="modal fade" id="webcamModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-lg border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Take Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0 text-center position-relative">
                <div class="ratio ratio-4x3 bg-dark">
                    <video id="webcamVideo" autoplay playsinline class="object-fit-cover"></video>
                </div>
                <canvas id="webcamCanvas" class="d-none"></canvas>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4 pt-2">
                <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 shadow" id="captureBtn">
                    <i class="bi bi-circle-fill me-2 text-white"></i> Capture
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Choices.js
        const element = document.getElementById('lead_select');
        const choices = new Choices(element, {
            searchEnabled: true,
            itemSelectText: '',
            placeholder: true,
            placeholderValue: 'Type to search...'
        });

        // Handle Selection
        element.addEventListener('change', function(e) {
            const leadId = e.detail.value;
            if (!leadId) {
                $('#verification_section').slideUp();
                return;
            }

            // Fetch info
            $.ajax({
                url: "{{ url('/api/leads') }}/" + leadId + "/details",
                method: 'GET',
                success: function(res) {
                    if (res.success) {
                        $('#selected_lead_id').val(res.id);
                        $('#disp_name').text(res.full_name);
                        $('#disp_phone').text(res.phone_number);
                        $('#disp_address').text(res.address);

                        $('#lead_lat').val(res.latitude);
                        $('#lead_lng').val(res.longitude);

                        // Reset form
                        $('#locationStatus').html('');
                        // Reset form
                        $('#locationStatus').html('');
                        $('#visit_notes').val('').prop('disabled', true);
                        $('#locationStatus').html('');
                        $('#visit_notes').val('').prop('disabled', true);

                        $('#verification_upload_area').addClass('disabled');
                        $('#openWebcamBtn').prop('disabled', true);
                        $('#openFileBtn').prop('disabled', true);
                        resetCapture();

                        $('#submitVerificationBtn').prop('disabled', true);
                        $('#submitVerificationBtn').prop('disabled', true);
                        $('#visited_latitude').val('');
                        $('#visited_longitude').val('');

                        $('#verification_section').slideDown(400, function() {
                            // Auto-detect location once visible
                            detectUserLocation();
                        });
                    }
                }
            });
        });

        // Haversine
        function getDistanceFromLatLonInM(lat1, lon1, lat2, lon2) {
            var R = 6371;
            var dLat = deg2rad(lat2 - lat1);
            var dLon = deg2rad(lon2 - lon1);
            var a =
                Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            var d = R * c;
            return d * 1000;
        }

        function deg2rad(deg) {
            return deg * (Math.PI / 180)
        }

        // Location Check Function
        function detectUserLocation() {
            const status = $('#locationStatus');
            const targetLat = parseFloat($('#lead_lat').val());
            const targetLng = parseFloat($('#lead_lng').val());

            status.html('<div class="p-3 bg-light rounded border text-primary"><span class="spinner-border spinner-border-sm me-2"></span> Verifying Location Compliance...</div>');

            $('#visit_notes').prop('disabled', true).attr('placeholder', 'Verifying location compatibility...');
            $('#visit_notes').prop('disabled', true).attr('placeholder', 'Verifying location compatibility...');

            $('#verification_upload_area').addClass('disabled');
            $('#openWebcamBtn').prop('disabled', true);
            $('#openFileBtn').prop('disabled', true);

            $('#submitVerificationBtn').prop('disabled', true);
            $('#submitVerificationBtn').prop('disabled', true);

            if (!navigator.geolocation) {
                status.html('<div class="alert alert-danger">Geolocation not supported by your browser.</div>');
                return;
            }

            navigator.geolocation.getCurrentPosition(function(pos) {
                const curLat = pos.coords.latitude;
                const curLng = pos.coords.longitude;
                const accuracy = pos.coords.accuracy; // Accuracy in meters

                $('#visited_latitude').val(curLat);
                $('#visited_longitude').val(curLng);

                // Distance Logic
                let msg = '';
                let success = false;
                const ALLOWED_RADIUS = 300; // Increased to 300m

                if (targetLat && targetLng) {
                    const dist = getDistanceFromLatLonInM(targetLat, targetLng, curLat, curLng);

                    if (dist > ALLOWED_RADIUS) {
                        msg = `<div class="alert alert-danger border-2 border-danger mb-0 text-start">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-x-circle-fill fs-2 me-3 text-danger"></i>
                                    <div>
                                        <h5 class="alert-heading fw-bold mb-0">Out of Range</h5>
                                        <div class="small">Distance: <strong>${Math.round(dist)}m</strong> (Max ${ALLOWED_RADIUS}m)</div>
                                        <div class="small text-muted">GPS Accuracy: +/- ${Math.round(accuracy)}m</div>
                                    </div>
                                </div>
                                <p class="mb-0 small">You are too far from the registered location. Please move closer.</p>
                               </div>`;
                        success = false;
                    } else {
                        msg = `<div class="alert alert-success border-2 border-success mb-0 text-start">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill fs-2 me-3 text-success"></i>
                                    <div>
                                        <h5 class="alert-heading fw-bold mb-0">Location Verified</h5>
                                        <div class="small">Distance: <strong>${Math.round(dist)}m</strong> (Accuracy: +/- ${Math.round(accuracy)}m)</div>
                                    </div>
                                </div>
                               </div>`;
                        success = true;
                    }
                } else {
                    // No original location
                    msg = `<div class="alert alert-warning mb-0 text-start">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Warning:</strong> Original location missing. Verification allowed with caution.
                             <div class="small mt-1 text-muted">GPS Accuracy: +/- ${Math.round(accuracy)}m</div>
                           </div>`;
                    success = true;
                }

                status.html(msg);

                if (success) {
                    $('#visit_notes').prop('disabled', false).attr('placeholder', 'Enter details about the visit...').focus();

                    $('#verification_upload_area').removeClass('disabled');
                    $('#openWebcamBtn').prop('disabled', false);
                    $('#openFileBtn').prop('disabled', false);

                    $('#submitVerificationBtn').prop('disabled', false);
                } else {
                    $('#visit_notes').prop('disabled', true);

                    $('#verification_upload_area').addClass('disabled');
                    $('#openWebcamBtn').prop('disabled', true);
                    $('#openFileBtn').prop('disabled', true);

                    $('#submitVerificationBtn').prop('disabled', true);
                }

            }, function(err) {
                status.html(`<div class="alert alert-danger mb-0">Location Error: ${err.message}. <a href="javascript:void(0)" onclick="detectUserLocation()" class="fw-bold">Retry</a></div>`);
                enableHighAccuracy: true
            });
        }

        // Expose to window for inline onclick
        window.detectUserLocation = detectUserLocation;

        // Submit
        $('#submitVerificationBtn').on('click', function() {
            const leadId = $('#selected_lead_id').val();
            const notes = $('#visit_notes').val();
            const lat = $('#visited_latitude').val();
            const lng = $('#visited_longitude').val();

            if (!notes) {
                Swal.fire('Error', 'Please enter comments.', 'error');
                return;
            }


            let formData = new FormData();
            formData.append('_token', "{{ csrf_token() }}");
            formData.append('visit_notes', notes);
            formData.append('visited_latitude', lat);
            formData.append('visited_longitude', lng);

            const fileInput = document.getElementById('verification_image');
            if (fileInput.files.length > 0) {
                formData.append('verification_image', fileInput.files[0]);
            }

            $.ajax({
                url: "{{ url('/leads') }}/" + leadId + "/mark-visited",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#submitVerificationBtn').prop('disabled', true).text('Saving...');
                },
                success: function(res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Verified Successfully!',
                            text: 'Redirecting...',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                },
                error: function(err) {
                    Swal.fire('Error', 'Failed to save.', 'error');
                    $('#submitVerificationBtn').prop('disabled', false).text('Mark as Verified');
                }
            });
        });


        // Webcam Logic
        let stream = null;
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        const modal = new bootstrap.Modal(document.getElementById('webcamModal'));

        $('#openWebcamBtn').click(function() {
            modal.show();
            startCamera();
        });

        $('#webcamModal').on('hidden.bs.modal', function() {
            stopCamera();
        });

        function startCamera() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: "environment"
                        }
                    })
                    .then(function(s) {
                        stream = s;
                        video.srcObject = stream;
                        video.play();
                    })
                    .catch(function(err) {
                        Swal.fire('Camera Error', 'Could not access camera: ' + err.message, 'error');
                        modal.hide();
                    });
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
            }
        }

        $('#captureBtn').click(function() {
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);

            canvas.toBlob(function(blob) {
                const file = new File([blob], "capture_" + Date.now() + ".jpg", {
                    type: "image/jpeg"
                });

                // Set file to input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                document.getElementById('verification_image').files = dataTransfer.files;

                // Show preview
                $('#capturedImagePreview img').attr('src', URL.createObjectURL(blob));
                $('#capturedImagePreview').removeClass('d-none');

                modal.hide();
            }, 'image/jpeg', 0.8);
        });

        window.resetCapture = function() {
            document.getElementById('verification_image').value = "";
            $('#capturedImagePreview').addClass('d-none');
            $('#capturedImagePreview img').attr('src', '');
        }

        window.handleFileSelect = function(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#capturedImagePreview img').attr('src', e.target.result);
                    $('#capturedImagePreview').removeClass('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    });
</script>
@endsection
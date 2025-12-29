@extends('layout.admin')

@section('head')
<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid pb-5">
    <!-- Page Header -->
    <div class="row align-items-center mb-4 mt-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-1 fw-bold fs-3 text-dark">Agreement Signing</h4>
                    <ol class="breadcrumb m-0 small text-muted">
                        <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('leads.index') }}" class="text-decoration-none text-muted">Leads</a></li>
                        <li class="breadcrumb-item active text-primary">Agreement Sign</li>
                    </ol>
                </div>
                <div class="d-none d-md-block">
                    <!-- Optional: Add action buttons here if needed -->
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Styles for Modern Look -->
    <style>
        .card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            background: #fff;
            overflow: hidden;
        }

        /* Select2 Customization */
        .select2-container--default .select2-selection--single {
            height: 50px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
            transition: all 0.3s;
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
            font-size: 1rem;
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

        /* Upload Area */
        .upload-area {
            border: 2px dashed #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            background: #fff;
            transition: all 0.2s;
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

        /* Avatar */
        .avatar-lg-modern {
            width: 80px;
            height: 80px;
            font-size: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(85, 110, 230, 0.2);
        }

        /* Avatar */
        .avatar-lg-modern {
            width: 80px;
            height: 80px;
            font-size: 2rem;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(85, 110, 230, 0.2);
        }

        /* Table Styling */
        .table-modern thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #edf2f9;
            padding: 1rem 0.75rem;
        }

        .table-modern tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #edf2f9;
            color: #495057;
            font-size: 0.9rem;
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .table-modern tbody tr {
            transition: background-color 0.2s;
        }

        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>

    <!-- Agreements List Table -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-header bg-white border-bottom-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="mb-0 fw-bold text-dark">Pending Agreements</h5>

                    <div class="d-flex align-items-center gap-2">
                        <label class="small text-muted fw-bold text-nowrap">Filter by Creator:</label>
                        <select id="filter_created_by" class="form-select form-select-sm" style="width: 200px;">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->Full_Name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive p-3">
                        <table id="agreements-table" class="table table-modern table-borderless dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Lead Name</th>
                                    <th>Phone</th>
                                    <th>Route</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" id="action-section">
        <!-- Left Side: Selection and Upload -->
        <div class="col-lg-8">
            <div class="card card-modern h-100">
                <div class="card-header bg-white border-bottom-0 py-4 px-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar-sm bg-light rounded-circle d-flex align-items-center justify-content-center me-3 text-primary">
                            <i class="bi bi-file-earmark-text-fill fs-5"></i>
                        </div>
                        <h5 class="mb-0 fw-bold text-dark">Document Uploads</h5>
                    </div>
                </div>
                <div class="card-body px-4 pb-4">
                    <form id="agreementForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="agreement_target_lat" name="agreement_target_lat">
                        <input type="hidden" id="agreement_target_lng" name="agreement_target_lng">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted text-uppercase small mb-2">Select Customer</label>
                            <select id="lead_select_agreement" name="lead_id" class="form-control select2-agreement w-100">
                                <option value="">Search by name or phone...</option>
                                @foreach($leads as $lead)
                                <option value="{{ $lead->id }}">{{ $lead->full_name }} - {{ $lead->phone_number }}</option>
                                @endforeach
                            </select>

                            <!-- Mobile Only: Quick Lead Details Summary -->
                            <div id="mobile_lead_summary" class="d-md-none mt-3 p-3 bg-light rounded-3 border-0" style="display: none;">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-2">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark" id="mob_lead_name"></h6>
                                        <small class="text-muted" id="mob_lead_loc"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="image_upload_section_agreement" style="display: none;">
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-light text-primary px-3 py-2 rounded-pill fw-bold">Documents Required</span>
                                <div class="flex-grow-1 ms-3 border-bottom"></div>
                            </div>

                            @if(isset($agreementImageTypes) && count($agreementImageTypes) > 0)
                            <div class="row g-4">
                                @foreach($agreementImageTypes as $imageType)
                                @php
                                $fieldName = 'image_' . str_replace(' ', '_', strtolower($imageType['name']));
                                $fieldName = preg_replace('/[^a-z0-9_]/', '_', $fieldName);
                                $isRequired = isset($imageType['is_required']) && $imageType['is_required'];
                                @endphp
                                <div class="col-12">
                                    <div class="upload-area">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <label class="form-label fw-bold mb-0 text-dark fs-6 d-flex align-items-center">
                                                {{ $imageType['name'] }}
                                                @if($isRequired)
                                                <span class="badge bg-danger-subtle text-danger ms-2" style="font-size: 0.65em;">REQUIRED</span>
                                                @endif
                                            </label>
                                            <div id="{{ $fieldName }}_agreement_location_status"></div>
                                        </div>

                                        <input type="file"
                                            name="{{ $fieldName }}"
                                            id="{{ $fieldName }}_agreement"
                                            class="d-none"
                                            accept="image/*"
                                            api-field-name="{{ $fieldName }}"
                                            capture="user"
                                            onchange="handleImageUpload(this, '{{ $fieldName }}_agreement_preview', '{{ $fieldName }}_agreement_lat', '{{ $fieldName }}_agreement_lng')"
                                            data-image-type="{{ $imageType['name'] }}">

                                        <input type="hidden" id="{{ $fieldName }}_agreement_lat" name="{{ $fieldName }}_latitude">
                                        <input type="hidden" id="{{ $fieldName }}_agreement_lng" name="{{ $fieldName }}_longitude">

                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-primary btn-modern flex-grow-1"
                                                onclick="openWebcamModal('{{ $fieldName }}_agreement', '{{ $fieldName }}_agreement_preview', '{{ $fieldName }}_agreement_lat', '{{ $fieldName }}_agreement_lng')">
                                                <i class="bi bi-camera-video me-1"></i> Camera
                                            </button>
                                            <button type="button" class="btn btn-light btn-modern flex-grow-1 text-dark border"
                                                onclick="triggerFileSelect('{{ $fieldName }}_agreement')">
                                                <i class="bi bi-folder2-open me-1"></i> Gallery
                                            </button>
                                        </div>

                                        <div class="mt-3 text-center">
                                            <img id="{{ $fieldName }}_agreement_preview"
                                                src=""
                                                alt="Preview"
                                                class="img-fluid rounded-3 shadow-sm d-none"
                                                style="max-height: 250px; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <hr class="my-4 text-muted opacity-25">

                            <button type="button" class="btn btn-primary btn-lg w-100 py-3 rounded-3 shadow-lg fw-bold" onclick="saveAgreementImages()" id="btnSaveAgreement">
                                <i class="bi bi-cloud-arrow-up-fill me-2"></i> Upload & Save Documents
                            </button>
                            @else
                            <div class="alert alert-warning border-0 shadow-sm rounded-3">
                                <i class="bi bi-exclamation-circle-fill me-2"></i> No Agreement Image Types configured in Settings.
                            </div>
                            @endif
                        </div>

                        <div id="select_lead_msg_agreement" class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-cloud-upload text-muted opacity-25" style="font-size: 5rem;"></i>
                            </div>
                            <h5 class="text-muted">No Customer Selected</h5>
                            <p class="text-muted small">Search and select a customer above to begin uploading documents.</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Side: Lead Details -->
        <div class="col-lg-4">
            <div class="card card-modern h-100 position-sticky" style="top: 20px;">
                <div class="card-body p-4 text-center d-flex flex-column justify-content-center" id="agreement_lead_details_container">
                    <div class="py-5 text-muted opacity-50">
                        <i class="bi bi-person-badge" style="font-size: 4rem;"></i>
                        <p class="mt-3 fw-medium">Customer Details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Webcam Capture Modal --}}
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
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" onclick="closeWebcamModal()">
                    Cancel
                </button>
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold" id="switchCameraBtn" onclick="switchCamera()" style="display: none;">
                    <i class="bi bi-arrow-repeat me-1"></i> Flip
                </button>
                <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold" id="retakeBtn" onclick="retakePhoto()" style="display: none;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Retake
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold" id="captureBtn" onclick="capturePhoto()">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-circle-fill fs-6 me-2 animate-pulse"></i> Capture
                    </div>
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
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
    const GOOGLE_MAPS_API_KEY = "{{ config('services.google_maps.key') }}";

    /* ================= DISTANCE CALCULATION ================= */
    function calculateDistance(lat1, lon1, lat2, lon2) {
        const R = 6371e3; // Earth radius in meters
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;

        const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
            Math.cos(φ1) * Math.cos(φ2) *
            Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

        return R * c; // Distance in meters
    }

    $(document).ready(function() {
        // Initialize Select2
        $('.select2-agreement').select2({
            placeholder: 'Search and select a customer...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('body')
        });

        // DataTable Initialization
        const table = $('#agreements-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('leads.agreementData') }}",
                data: function(d) {
                    d.created_by = $('#filter_created_by').val();
                }
            },
            columns: [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'full_name',
                    name: 'full_name'
                },
                {
                    data: 'phone_number',
                    name: 'phone_number'
                },
                {
                    data: 'route.name',
                    name: 'route.name',
                    defaultContent: '-'
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        return `<span class="badge bg-soft-info text-info font-size-12">${data}</span>`;
                    }
                },
                {
                    data: 'created_by_name',
                    name: 'creator.Full_Name',
                    defaultContent: '-'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [0, 'desc']
            ],
            language: {
                searchPlaceholder: "Search...",
                search: ""
            },
            dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center'B><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [{
                    extend: 'csv',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'print',
                    className: 'btn btn-light btn-sm border'
                }
            ],
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control form-control-sm').css('margin-left', '10px');
                $('.dt-buttons').addClass('d-flex gap-1');
            }
        });

        // Filter Change
        $('#filter_created_by').change(function() {
            table.draw();
        });

        // Handle Select2 Change
        $('.select2-agreement').on('select2:select', function(e) {
            const leadId = e.params.data.id;
            if (leadId) {
                loadAgreementLeadDetails(leadId);
                $('#image_upload_section_agreement').slideDown(300);
                $('#select_lead_msg_agreement').slideUp(300);
            }
        });

        // Clear Selection
        $('.select2-agreement').on('select2:clear', function(e) {
            $('#image_upload_section_agreement').slideUp(300);
            $('#select_lead_msg_agreement').slideDown(300);
            resetAgreementLeadDetails();
        });
    });

    // Function called by "Verify" button in table
    window.selectLead = function(id) {
        // Set Select2 Value
        $('.select2-agreement').val(id).trigger('change');

        // Since triggering change might rely on actual selection event which Select2 manual trigger might miss for 'select2:select'
        // We ensure we load details manually if the event doesn't fire as expected or to be safe.
        // Actually .val().trigger('change') fires 'change' but not 'select2:select'. 
        // So we need to call the load function manually too.

        loadAgreementLeadDetails(id);
        $('#image_upload_section_agreement').slideDown(300);
        $('#select_lead_msg_agreement').slideUp(300);

        // Scroll to action section
        $('html, body').animate({
            scrollTop: $("#action-section").offset().top - 20
        }, 500);
    };

    // Agreement Images Functions
    function loadAgreementLeadDetails(leadId) {
        $('#agreement_lead_details_container').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-3 text-muted fw-medium">Retrieving details...</p></div>');

        $.ajax({
            url: "/api/leads/" + leadId + "/details",
            method: "GET",
            success: function(res) {
                if (res.success) {
                    // Populate hidden location fields for validation
                    $('#agreement_target_lat').val(res.latitude || '');
                    $('#agreement_target_lng').val(res.longitude || '');

                    // Show Mobile Summary
                    $('#mob_lead_name').text(res.full_name || 'Unknown');
                    $('#mob_lead_loc').text(res.latitude ? 'Location Registered' : 'No Location');
                    $('#mobile_lead_summary').slideDown();

                    const html = `
                        <div class="mb-4 text-center">
                            <div class="avatar-lg-modern bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3">
                                ${res.full_name ? res.full_name.charAt(0).toUpperCase() : '?'}
                            </div>
                            <h4 class="mb-1 fw-bold text-dark">${res.full_name || 'Unknown'}</h4>
                            <span class="badge bg-light text-dark border px-3 py-1 rounded-pill mt-2">ID: #${res.id}</span>
                        </div>
                        
                        <div class="text-start w-100">
                             <div class="mb-3 p-3 bg-light rounded-3">
                                <label class="small text-muted text-uppercase fw-bold mb-1" style="font-size:0.7rem;">Contact</label>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-telephone-fill me-2 text-primary"></i>
                                    <span class="fs-5 text-dark fw-bold">${res.phone_number || '-'}</span>
                                </div>
                            </div>
                            
                            <div class="mb-3 p-3 bg-light rounded-3">
                                <label class="small text-muted text-uppercase fw-bold mb-1" style="font-size:0.7rem;">Address</label>
                                <div class="d-flex align-items-start">
                                    <i class="bi bi-geo-alt-fill me-2 text-primary mt-1"></i>
                                    <span class="text-dark fw-medium" style="line-height:1.4;">${res.address || '-'}</span>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4 w-100 opacity-25">
                        
                        <div class="row g-2 w-100 mb-3">
                            <div class="col-6">
                                <div class="p-2 border rounded-3 bg-white text-center">
                                    <label class="small text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.65rem;">Latitude</label>
                                    <span class="font-monospace text-primary fw-bold small">${res.latitude || '-'}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 border rounded-3 bg-white text-center">
                                    <label class="small text-muted text-uppercase fw-bold d-block mb-1" style="font-size: 0.65rem;">Longitude</label>
                                    <span class="font-monospace text-primary fw-bold small">${res.longitude || '-'}</span>
                                </div>
                            </div>
                        </div>
                        
                        ${res.latitude ? `
                        <a href="https://www.google.com/maps/search/?api=1&query=${res.latitude},${res.longitude}" target="_blank" class="btn btn-outline-primary w-100 rounded-3 fw-bold">
                            <i class="bi bi-map-fill me-2"></i> Open in Maps
                        </a>
                        ` : ''}
                    `;
                    $('#agreement_lead_details_container').html(html);
                }
            },
            error: function() {
                $('#agreement_lead_details_container').html('<div class="alert alert-danger border-0 shadow-sm"><i class="bi bi-exclamation-circle-fill me-2"></i> Failed to load lead details.</div>');
            }
        });
    }

    function resetAgreementLeadDetails() {
        $('#agreement_target_lat').val('');
        $('#agreement_target_lng').val('');
        $('#mobile_lead_summary').slideUp();
        $('#agreement_lead_details_container').html(`
            <div class="py-5 text-muted opacity-50">
                <i class="bi bi-person-badge" style="font-size: 4rem;"></i>
                <p class="mt-3 fw-medium">Customer Details</p>
            </div>
        `);
    }

    // Check Location for Agreement Images
    function checkAgreementImageLocation(imageLat, imageLng, imageTypeName) {
        const leadLat = parseFloat($('#agreement_target_lat').val());
        const leadLng = parseFloat($('#agreement_target_lng').val());

        // If lead location is not captured yet
        if (!leadLat || !leadLng || isNaN(leadLat) || isNaN(leadLng)) {
            Swal.fire({
                icon: 'warning',
                title: 'No Location Found',
                text: 'This customer does not have a registered location. Please update their profile first.',
                confirmButtonText: 'Understood'
            });
            return false;
        }

        // Calculate distance via Haversine
        const distance = calculateDistance(leadLat, leadLng, imageLat, imageLng);
        const distanceKm = (distance / 1000).toFixed(2);

        // Tolerance: 100 meters
        const tolerance = 100;

        if (distance >= tolerance) {
            Swal.fire({
                icon: 'error',
                title: 'Location Mismatch',
                html: `
                    <div class="text-start">
                        <p class="mb-2">Your location is too far from the customer's registered site.</p>
                        <div class="alert alert-light border d-flex justify-content-between px-3 py-2 mb-2">
                             <div class="text-end border-end pe-3 w-50">
                                <small class="text-muted d-block uppercase" style="font-size:0.7rem;">Reg. Location</small>
                                <span class="fw-bold text-dark">${leadLat.toFixed(4)}, ${leadLng.toFixed(4)}</span>
                             </div>
                             <div class="text-start ps-3 w-50">
                                <small class="text-muted d-block uppercase" style="font-size:0.7rem;">Your Location</small>
                                <span class="fw-bold text-primary">${imageLat.toFixed(4)}, ${imageLng.toFixed(4)}</span>
                             </div>
                        </div>
                        <p class="mb-0 text-danger fw-bold text-center">Distance: ${distanceKm} km (Limit: ${tolerance}m)</p>
                    </div>
                `,
                confirmButtonText: 'Close'
            });
            return false;
        }

        return true;
    }

    function saveAgreementImages() {
        const btn = $('#btnSaveAgreement');
        // Validate Lead Selection
        const leadId = $('#lead_select_agreement').val();
        if (!leadId) {
            Swal.fire('Selection Required', 'Please search and select a customer first.', 'info');
            return;
        }

        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Uploading...');

        const formData = new FormData($('#agreementForm')[0]);

        $.ajax({
            url: "{{ route('leads.agreementImages.upload') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Upload Complete',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false,
                        backdrop: `rgba(0,0,0,0.4)`
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Upload Failed', res.message || 'Something went wrong.', 'error');
                }
            },
            error: function(xhr) {
                let msg = 'Upload failed.';
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                Swal.fire('Server Error', msg, 'error');
            },
            complete: function() {
                btn.prop('disabled', false).html('<i class="bi bi-cloud-arrow-up-fill me-2"></i> Upload & Save Documents');
            }
        });
    }

    /* ================= WEBCAM VARS & FUNCTIONS ================= */
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
        document.getElementById('retakeBtn').style.display = 'none';
        document.getElementById('webcamError').style.display = 'none';

        const modal = new bootstrap.Modal(document.getElementById('webcamModal'));
        modal.show();

        setTimeout(() => {
            startWebcam();
        }, 500);
    }

    function closeWebcamModal() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        const video = document.getElementById('webcamVideo');
        video.srcObject = null;
        video.style.display = 'none';

        const modalElement = document.getElementById('webcamModal');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) modal.hide();
    }

    async function startWebcam() {
        const video = document.getElementById('webcamVideo');
        const placeholder = document.getElementById('webcamPlaceholder');
        const errorDiv = document.getElementById('webcamError');

        try {
            if (webcamStream) webcamStream.getTracks().forEach(track => track.stop());

            webcamStream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: facingMode,
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    }
                },
                audio: false
            });

            video.srcObject = webcamStream;
            video.style.display = 'block';
            placeholder.style.display = 'none';
            errorDiv.style.display = 'none';

            // Check availability for switch button
            try {
                const devicez = await navigator.mediaDevices.enumerateDevices();
                const vids = devicez.filter(d => d.kind === 'videoinput');
                document.getElementById('switchCameraBtn').style.display = (vids.length > 1) ? 'inline-block' : 'none';
            } catch (e) {}

        } catch (error) {
            console.error('Error accessing webcam:', error);
            placeholder.style.display = 'none';
            errorDiv.style.display = 'block';
            errorDiv.innerHTML = `<i class="bi bi-exclamation-triangle-fill fs-1 d-block mb-3"></i> Camera Access Denied. <br>Please allow camera permissions.`;
        }
    }

    function switchCamera() {
        facingMode = facingMode === 'user' ? 'environment' : 'user';
        startWebcam();
    }

    function capturePhoto() {
        const video = document.getElementById('webcamVideo');
        const canvas = document.getElementById('webcamCanvas');
        const preview = document.getElementById('capturedImagePreview');
        const capturedImg = document.getElementById('capturedImg');

        if (!webcamStream || !video.srcObject) return;

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

        capturedImg.src = canvas.toDataURL('image/jpeg', 0.9);
        preview.style.display = 'block';
        video.style.display = 'none';

        document.getElementById('captureBtn').style.display = 'none';
        document.getElementById('usePhotoBtn').style.display = 'inline-block';
        document.getElementById('retakeBtn').style.display = 'inline-block';
        document.getElementById('switchCameraBtn').style.display = 'none';
    }

    function retakePhoto() {
        document.getElementById('webcamVideo').style.display = 'block';
        document.getElementById('capturedImagePreview').style.display = 'none';
        document.getElementById('captureBtn').style.display = 'inline-block';
        document.getElementById('usePhotoBtn').style.display = 'none';
        document.getElementById('retakeBtn').style.display = 'none';
        document.getElementById('switchCameraBtn').style.display = (facingMode ? 'inline-block' : 'none');
    }

    function useCapturedPhoto() {
        const canvas = document.getElementById('webcamCanvas');
        const input = document.getElementById(currentFieldId);
        const preview = document.getElementById(currentPreviewId);
        const statusDiv = document.getElementById(currentFieldId + '_location_status');

        canvas.toBlob(function(blob) {
            const file = new File([blob], `captured_${Date.now()}.jpg`, {
                type: 'image/jpeg'
            });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            input.files = dataTransfer.files;

            closeWebcamModal();
            handleImageUpload(input, currentPreviewId, currentLatFieldId, currentLngFieldId);
        }, 'image/jpeg', 0.9);
    }

    function handleImageUpload(input, previewId, latFieldId, lngFieldId) {
        const file = input.files[0];
        const statusDiv = document.getElementById(input.id + '_location_status');
        const imageTypeName = input.getAttribute('data-image-type');

        if (!file) return;

        statusDiv.innerHTML = '<small class="text-info fw-bold"><span class="spinner-grow spinner-grow-sm me-1"></span>Verifying Location...</small>';

        if (!navigator.geolocation) {
            statusDiv.innerHTML = '<small class="text-danger fw-bold">Geo-location not available.</small>';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            position => {
                const imageLat = position.coords.latitude;
                const imageLng = position.coords.longitude;

                // Validate against lead location
                if (!checkAgreementImageLocation(imageLat, imageLng, imageTypeName)) {
                    input.value = '';
                    statusDiv.innerHTML = '<small class="text-danger fw-bold"><i class="bi bi-x-circle-fill"></i> Location Mismatch</small>';
                    return;
                }

                document.getElementById(latFieldId).value = imageLat;
                document.getElementById(lngFieldId).value = imageLng;
                statusDiv.innerHTML = '<small class="text-success fw-bold"><i class="bi bi-check-circle-fill"></i> Location Verified</small>';

                const reader = new FileReader();
                reader.onload = function(e) {
                    const p = document.getElementById(previewId);
                    p.src = e.target.result;
                    p.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            },
            error => {
                statusDiv.innerHTML = '<small class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Location Error</small>';
            }, {
                enableHighAccuracy: true
            }
        );
    }

    function triggerFileSelect(fieldId) {
        document.getElementById(fieldId).removeAttribute('capture');
        document.getElementById(fieldId).click();
    }
</script>
@endsection
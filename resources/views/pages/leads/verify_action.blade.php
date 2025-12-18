@extends('layout.admin')

@section('head')
    <!-- Choices.js CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        .verified-header {
            background: linear-gradient(135deg, #4f46e5 0%, #8b5cf6 100%);
            color: white;
            padding: 2.5rem;
            border-radius: 16px;
            margin-bottom: 2rem;
        }
        .lead-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            /* overflow: hidden; Removed to allow dropdown to expand */
            transition: all 0.2s;
        }
        .lead-card-header {
            background-color: #f9fafb;
            padding: 1.25rem;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
        }
        .lead-card-body {
            padding: 1.5rem;
        }
        .steps-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .step-item {
            text-align: center;
            z-index: 2;
            background: white;
            padding: 0 10px;
        }
        .step-number {
            width: 40px;
            height: 40px;
            background: #e5e7eb;
            color: #6b7280;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 0.5rem;
        }
        .step-item.active .step-number {
            background: #4f46e5;
            color: white;
        }
        .step-line {
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e5e7eb;
            z-index: 1;
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
        <div class="card lead-card mb-4 shadow-sm">
            <div class="lead-card-header d-flex justify-content-between align-items-center">
                <span>Step 1: Select Lead to Verify</span>
                <span class="badge bg-primary rounded-pill">Pending</span>
            </div>
            <div class="card-body p-4">
                @if($leads->isEmpty())
                    <div class="alert alert-success text-center border-0 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-circle-fill fs-3 mb-2 d-block"></i>
                        <h5 class="fw-bold">All Caught Up!</h5>
                        <p class="mb-0">There are no pending leads requiring verification at this moment.</p>
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark">Search Customer</label>
                        <select class="form-control" id="lead_select">
                            <option value="">Select a lead to verify...</option>
                            @foreach($leads as $lead)
                                <option value="{{ $lead->id }}">{{ $lead->full_name }} ({{ $lead->address }})</option>
                            @endforeach
                        </select>
                        <div class="form-text mt-2"><i class="bi bi-search me-1"></i> Type name or phone number to search.</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Lead Details & Verification (Hidden initially) -->
        <div id="verification_section" style="display: none;">
            
            <div class="card lead-card mb-4 shadow-sm border-primary">
                <div class="lead-card-header bg-primary text-white">
                    <i class="bi bi-person-badge me-2"></i> Lead Information
                </div>
                <div class="card-body p-4">
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
                            <div class="text-dark" id="disp_address"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Location & Comment -->
            <div class="card lead-card shadow-lg">
                <div class="lead-card-header">
                    Step 2: Verification
                </div>
                <div class="card-body p-4">
                    
                    <div class="text-center mb-4">
                        <div id="locationStatus" class="mt-3">
                            <div class="p-3 bg-light rounded border text-muted">
                                <span class="spinner-border spinner-border-sm me-2"></span> Waiting to detect location...
                            </div>
                        </div>
                    </div>

                    <form id="verifyForm">
                         <input type="hidden" id="visited_latitude" name="visited_latitude">
                         <input type="hidden" id="visited_longitude" name="visited_longitude">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Visit Comments</label>
                            <textarea class="form-control" id="visit_notes" rows="4" 
                                placeholder="Verification disabled. Please detect location first..." disabled></textarea>
                            <div class="form-text">You must be within 5 meters of the lead to add comments.</div>
                        </div>

                        <div class="d-grid">
                            <button type="button" class="btn btn-success btn-lg" id="submitVerificationBtn" disabled>
                                <i class="bi bi-check-circle-fill me-2"></i> Mark as Verified
                            </button>
                        </div>
                    </form>

                </div>
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
            if(!leadId) {
                $('#verification_section').slideUp();
                return;
            }

            // Fetch info
            $.ajax({
                url: "{{ url('/api/leads') }}/" + leadId + "/details",
                method: 'GET',
                success: function(res) {
                    if(res.success) {
                        $('#selected_lead_id').val(res.id);
                        $('#disp_name').text(res.full_name);
                        $('#disp_phone').text(res.phone_number);
                        $('#disp_address').text(res.address);
                        
                        $('#lead_lat').val(res.latitude);
                        $('#lead_lng').val(res.longitude);

                        // Reset form
                        $('#locationStatus').html('');
                        $('#visit_notes').val('').prop('disabled', true);
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
            var dLat = deg2rad(lat2-lat1); 
            var dLon = deg2rad(lon2-lon1); 
            var a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) * 
                Math.sin(dLon/2) * Math.sin(dLon/2); 
            var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            var d = R * c; 
            return d * 1000; 
        }

        function deg2rad(deg) {
            return deg * (Math.PI/180)
        }

        // Location Check Function
        function detectUserLocation() {
            const status = $('#locationStatus');
            const targetLat = parseFloat($('#lead_lat').val());
            const targetLng = parseFloat($('#lead_lng').val());

            status.html('<div class="p-3 bg-light rounded border text-primary"><span class="spinner-border spinner-border-sm me-2"></span> Verifying Location Compliance...</div>');
            
            $('#visit_notes').prop('disabled', true).attr('placeholder', 'Verifying location compatibility...');
            $('#submitVerificationBtn').prop('disabled', true);

            if(!navigator.geolocation) {
                status.html('<div class="alert alert-danger">Geolocation not supported by your browser.</div>');
                return;
            }

            navigator.geolocation.getCurrentPosition(function(pos) {
                const curLat = pos.coords.latitude;
                const curLng = pos.coords.longitude;
                
                $('#visited_latitude').val(curLat);
                $('#visited_longitude').val(curLng);

                // Distance Logic
                let msg = '';
                let success = false;

                if (targetLat && targetLng) {
                    const dist = getDistanceFromLatLonInM(targetLat, targetLng, curLat, curLng);
                    if (dist > 5) {
                        msg = `<div class="alert alert-danger border-2 border-danger mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-x-circle-fill fs-2 me-3"></i>
                                    <div>
                                        <h5 class="alert-heading fw-bold mb-1">Out of Range</h5>
                                        <p class="mb-0">You are <strong>${Math.round(dist)}m</strong> away from the lead. Access denied.</p>
                                    </div>
                                </div>
                               </div>`;
                        success = false;
                    } else {
                        msg = `<div class="alert alert-success border-2 border-success mb-0">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill fs-2 me-3"></i>
                                    <div>
                                        <h5 class="alert-heading fw-bold mb-1">Location Verified</h5>
                                        <p class="mb-0">You are within range (${Math.round(dist)}m). Access granted.</p>
                                    </div>
                                </div>
                               </div>`;
                        success = true;
                    }
                } else {
                    // No original location
                    msg = `<div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Warning:</strong> Original location missing. Verification allowed with caution.
                           </div>`;
                    success = true; 
                }

                status.html(msg);
                
                if(success) {
                    $('#visit_notes').prop('disabled', false).attr('placeholder', 'Enter details about the visit...').focus();
                    $('#submitVerificationBtn').prop('disabled', false);
                } else {
                    $('#visit_notes').prop('disabled', true);
                    $('#submitVerificationBtn').prop('disabled', true);
                }

            }, function(err) {
                 status.html(`<div class="alert alert-danger mb-0">Location Error: ${err.message}. <a href="javascript:void(0)" onclick="detectUserLocation()" class="fw-bold">Retry</a></div>`);
            }, { enableHighAccuracy: true });
        }

        // Submit
        $('#submitVerificationBtn').on('click', function() {
            const leadId = $('#selected_lead_id').val();
            const notes = $('#visit_notes').val();
            const lat = $('#visited_latitude').val();
            const lng = $('#visited_longitude').val();

            if(!notes) {
                Swal.fire('Error', 'Please enter comments.', 'error');
                return;
            }

            $.ajax({
                url: "{{ url('/leads') }}/" + leadId + "/mark-visited",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    visit_notes: notes,
                    visited_latitude: lat,
                    visited_longitude: lng
                },
                beforeSend: function() {
                    $('#submitVerificationBtn').prop('disabled', true).text('Saving...');
                },
                success: function(res) {
                    if(res.success) {
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
    });
</script>
@endsection

@extends('layout.admin')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <style>
        #leadMap {
            width: 100%;
            height: 400px;
            min-height: 400px;
            border-radius: 10px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- Page header --}}
        <div class="row mb-3">
            <div class="col-md-8">
                <h4 class="page-title mb-1">
                    Lead Details
                </h4>
                <p class="text-muted mb-0">
                    Clean overview of the captured lead information and location before approval.
                </p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('leads.approvals') }}" class="btn btn-light me-2">
                    <i class="bi bi-arrow-left"></i> Back to Approvals
                </a>
                @if($lead->status === 'pending')
                    <button type="button"
                            id="approveLeadBtn"
                            class="btn btn-success">
                        <i class="bi bi-check2-circle"></i> Approve Lead
                    </button>
                @else
                    <span class="badge bg-success px-3 py-2">
                        {{ strtoupper($lead->status) }}
                    </span>
                @endif
            </div>
        </div>

        <div class="row">
            {{-- Left column – details --}}
            <div class="col-lg-7 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="mb-1">{{ $lead->full_name ?? '-' }}</h5>
                                <span class="badge bg-soft-primary text-primary text-uppercase">
                                    {{ $lead->type ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="text-end">
                                <div class="mb-1">
                                    @php
                                        $statusClass = match($lead->status) {
                                            'pending-approved' => 'bg-success',
                                            'agreement-signed' => 'bg-info',
                                            'loan-issued' => 'bg-primary',
                                            default => 'bg-warning text-dark',
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ strtoupper($lead->status) }}
                                    </span>
                                </div>
                                <small class="text-muted d-block">
                                    Created at:
                                    {{ optional($lead->created_at_lead)->format('Y-m-d H:i') ?? '-' }}
                                </small>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted text-uppercase fw-semibold small">Contact</h6>
                                <p class="mb-1">
                                    <i class="bi bi-telephone me-1"></i>
                                    {{ $lead->phone_number ?? '-' }}
                                </p>
                                <p class="mb-0">
                                    <i class="bi bi-envelope me-1"></i>
                                    {{ $lead->email ?? '-' }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted text-uppercase fw-semibold small">Loan Info</h6>
                                <p class="mb-1">
                                    <span class="text-muted">Type:</span>
                                    <strong>{{ $lead->type ?? '-' }}</strong>
                                </p>
                                <p class="mb-0">
                                    <span class="text-muted">Periods:</span>
                                    <strong>{{ $lead->periods ?? '-' }}</strong>
                                </p>
                            </div>

                            <div class="col-12 mb-3">
                                <h6 class="text-muted text-uppercase fw-semibold small">Address</h6>
                                <p class="mb-0">
                                    <i class="bi bi-geo-alt me-1"></i>
                                    {{ $lead->address ?? '-' }}
                                </p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted text-uppercase fw-semibold small">Captured Location</h6>
                                <p class="mb-1 mb-md-0">
                                    <span class="text-muted">Latitude:</span>
                                    <strong>{{ $lead->latitude ?? '-' }}</strong>
                                </p>
                                <p class="mb-0">
                                    <span class="text-muted">Longitude:</span>
                                    <strong>{{ $lead->longitude ?? '-' }}</strong>
                                </p>
                            </div>

                            <div class="col-md-6 mb-3">
                                <h6 class="text-muted text-uppercase fw-semibold small">Visit Status</h6>
                                <p class="mb-1">
                                    <span class="text-muted">Visited:</span>
                                    <strong>{{ $lead->is_visited ? 'Yes' : 'No' }}</strong>
                                </p>
                                @if($lead->is_visited)
                                    <p class="mb-1">
                                        <span class="text-muted">Visited Lat / Lng:</span>
                                        <strong>{{ $lead->visited_latitude ?? '-' }} / {{ $lead->visited_longitude ?? '-' }}</strong>
                                    </p>
                                @endif
                            </div>

                            <div class="col-12 mb-2">
                                <h6 class="text-muted text-uppercase fw-semibold small">Notes</h6>
                                <p class="mb-0">
                                    {{ $lead->notes ?: 'No additional notes provided.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Images --}}
                <div class="card shadow-sm border-0 mt-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 text-muted text-uppercase fw-semibold small">Attached Images</h6>
                            <small class="text-muted">
                                {{ $lead->images->count() }} file(s)
                            </small>
                        </div>

                        @if($lead->images->isEmpty())
                            <p class="text-muted mb-0">No images were uploaded for this lead.</p>
                        @else
                            <div class="row g-3">
                                @foreach($lead->images as $image)
                                    <div class="col-md-4 col-sm-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="ratio ratio-4x3">
                                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                                     class="card-img-top"
                                                     alt="{{ $image->image_type }}"
                                                     style="object-fit: cover;">
                                            </div>
                                            <div class="card-body p-2">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small fw-semibold">
                                                        {{ $image->image_type }}
                                                    </span>
                                                </div>
                                                @if($image->latitude && $image->longitude)
                                                    <small class="text-muted d-block mt-1">
                                                        <i class="bi bi-geo-alt me-1"></i>
                                                        {{ $image->latitude }}, {{ $image->longitude }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right column – map --}}
            <div class="col-lg-5 mb-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 text-muted text-uppercase fw-semibold small">Lead Location</h6>
                            @if($lead->latitude && $lead->longitude)
                                <div class="d-flex gap-2">
                                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $lead->latitude }},{{ $lead->longitude }}"
                                       target="_blank"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Get directions in Google Maps">
                                        <i class="bi bi-signpost-split"></i> Get Directions
                                    </a>
                                </div>
                            @endif
                        </div>

                        @if($lead->latitude && $lead->longitude)
                            <div id="leadMap" data-lat="{{ $lead->latitude }}" data-lng="{{ $lead->longitude }}"></div>
                            <p class="text-muted mb-0 small mt-2">
                                Map shows the captured GPS location. Click "Get Directions" to open navigation.
                            </p>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Location has not been captured for this lead yet.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id="lead_id" value="{{ $lead->id }}">
@endsection

@section('script')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        $(function () {
            // Initialize Leaflet map for lead location
            const mapContainer = document.getElementById('leadMap');
            if (mapContainer) {
                const lat = parseFloat(mapContainer.dataset.lat);
                const lng = parseFloat(mapContainer.dataset.lng);

                if (!isNaN(lat) && !isNaN(lng)) {
                    const map = L.map('leadMap').setView([lat, lng], 16);

                    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© OpenStreetMap'
                    }).addTo(map);

                    const marker = L.marker([lat, lng]).addTo(map);
                    marker.bindPopup(`
                        <b>{{ $lead->full_name ?? 'Lead Location' }}</b><br>
                        <small>Lat: ${lat}, Lng: ${lng}</small><br>
                        <a href="https://www.google.com/maps?q=${lat},${lng}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">📍 Google Map</a>
                    `).openPopup();

                    setTimeout(() => map.invalidateSize(), 500);
                }
            }

            // Approve button handler
            $('#approveLeadBtn').on('click', function () {
                const leadId = $('#lead_id').val();
                if (!leadId) {
                    Swal.fire('Error', 'Lead ID is missing.', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Approve this lead?',
                    text: 'Please ensure the details and map location are correct.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, approve',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (!result.isConfirmed) {
                        return;
                    }

                    $.ajax({
                        url: "{{ route('leads.approve', ['lead' => '__ID__']) }}".replace('__ID__', leadId),
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (res) {
                            if (res.success) {
                                Swal.fire('Approved', res.message || 'Lead approved successfully.', 'success')
                                    .then(() => {
                                        window.location.reload();
                                    });
                            } else {
                                Swal.fire('Error', res.message || 'Unable to approve lead.', 'error');
                            }
                        },
                        error: function (xhr) {
                            let msg = 'Unable to approve lead right now.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire('Error', msg, 'error');
                        }
                    });
                });
            });
        });
    </script>
@endsection





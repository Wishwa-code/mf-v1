@extends('layout.admin')

@section('head')
<script>
    (g => {
        var h, a, k, p = "The Google Maps JavaScript API",
            c = "google",
            l = "importLibrary",
            q = "__ib__",
            m = document,
            b = window;
        b = b[c] || (b[c] = {});
        var d = b.maps || (b.maps = {}),
            r = new Set,
            e = new URLSearchParams,
            u = () => h || (h = new Promise(async (f, n) => {
                await (a = m.createElement("script"));
                e.set("libraries", [...r] + "");
                for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
                e.set("callback", c + ".maps." + q);
                a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
                d[q] = f;
                a.onerror = () => h = n(Error(p + " could not load."));
                a.nonce = m.querySelector("script[nonce]")?.nonce || "";
                m.head.append(a)
            }));
        d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
    })({
        key: "{{ config('services.google_maps.key') }}",
        v: "beta"
    });
</script>
<style>
    .rounded-4 {
        border-radius: 1rem !important;
    }

    .shadow-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        transition: all .2s ease-in-out;
    }

    gmp-map {
        width: 100%;
        height: 300px;
        border-radius: 1rem;
        display: block;
    }

    .image-card img {
        transition: transform .3s ease;
    }

    .image-card:hover img {
        transform: scale(1.05);
    }

    .badge-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .badge-soft-success {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .badge-soft-warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: #856404;
    }

    .badge-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }

    .detail-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        font-size: 1rem;
        font-weight: 500;
        color: #212529;
    }

    @media (min-width: 992px) {
        .sticky-lg-top-custom {
            position: sticky;
            top: 1rem;
            z-index: 1020;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid mt-3 mt-lg-5 pb-5">
    {{-- Page header --}}
    <div class="card shadow-sm rounded-4 mb-4 border-0 sticky-lg-top-custom">
        <div class="card-body p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div>
                    <h4 class="fw-bold text-dark mb-1">
                        <i class="bi bi-person-badge me-2 text-primary"></i>Lead Details
                    </h4>
                    <p class="text-muted mb-0 small">
                        View and manage captured lead information and location.
                    </p>
                </div>
                <div class="mt-3 mt-md-0 d-flex gap-2 flex-wrap">
                    <a href="{{ route('leads.approvals') }}" class="btn btn-outline-secondary shadow-sm rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                    @if($lead->status === 'pending')
                    <button type="button" id="approveLeadBtn" class="btn btn-primary shadow-sm rounded-pill px-4">
                        <i class="bi bi-check2-circle me-1"></i> Approve Lead
                    </button>
                    @else
                    <span class="badge bg-success rounded-pill px-4 py-2 d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2"></i> {{ strtoupper($lead->status) }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left column – details --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-3 px-md-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">Information</h5>
                        @php
                        $statusClass = match($lead->status) {
                        'pending-approved' => 'badge-soft-success',
                        'agreement-signed' => 'badge-soft-info',
                        'loan-issued' => 'badge-soft-primary',
                        default => 'badge-soft-warning',
                        };
                        @endphp
                        <span class="badge {{ $statusClass }} rounded-pill px-3">
                            {{ strtoupper($lead->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body px-3 px-md-4 pb-4 pt-0">
                    <div class="p-3 bg-light rounded-3 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-white rounded-circle p-2 shadow-sm me-3 text-center" style="width: 48px; height: 48px;">
                                <i class="bi bi-person fs-4 text-primary"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">{{ $lead->full_name ?? '-' }}</h5>
                                <small class="text-muted">
                                    Created: {{ optional($lead->created_at_lead)->format('d M Y, h:i A') ?? '-' }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 h-100">
                                <div class="detail-label"><i class="bi bi-telephone me-1"></i> Phone</div>
                                <div class="detail-value">{{ $lead->phone_number ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 h-100">
                                <div class="detail-label"><i class="bi bi-envelope me-1"></i> Email</div>
                                <div class="detail-value">{{ $lead->email ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 h-100">
                                <div class="detail-label"><i class="bi bi-credit-card me-1"></i> Loan Type</div>
                                <div class="detail-value">{{ $lead->type ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 h-100">
                                <div class="detail-label"><i class="bi bi-calendar-check me-1"></i> Periods</div>
                                <div class="detail-value">{{ $lead->periods ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 h-100">
                                <div class="detail-label"><i class="bi bi-cash-stack me-1"></i> Loan Amount</div>
                                <div class="detail-value">{{ $lead->loan_amount ? number_format($lead->loan_amount, 2) : '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 h-100">
                                <div class="detail-label"><i class="bi bi-tag me-1"></i> Business Category</div>
                                <div class="detail-value">{{ $lead->businessCategory->name ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 border rounded-3 h-100">
                                <div class="detail-label"><i class="bi bi-geo-alt me-1"></i> Address</div>
                                <div class="detail-value">{{ $lead->address ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="fw-bold mb-3 text-dark border-bottom pb-2">Additional Details</h6>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-2">
                                <div class="detail-label">Captured Location (GPS)</div>
                                <div class="detail-value text-break monospace fs-6">
                                    {{ $lead->latitude ?? '-' }}, {{ $lead->longitude ?? '-' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <div class="detail-label">Visit Status</div>
                                <div class="detail-value">
                                    @if($lead->is_visited)
                                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> Visited</span>
                                    <div class="small text-muted mt-1">{{ $lead->visited_latitude ?? '-' }} / {{ $lead->visited_longitude ?? '-' }}</div>
                                    @if($lead->verification_image)
                                    <div class="mt-2">
                                        <a href="{{ asset('storage/' . $lead->verification_image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $lead->verification_image) }}" class="rounded shadow-sm border" style="width: 100px; height: 100px; object-fit: cover;" alt="Verification Image">
                                        </a>
                                        <div class="small text-muted mt-1">Evidence</div>
                                    </div>
                                    @endif
                                    @else
                                    <span class="text-muted"><i class="bi bi-circle me-1"></i> Not Visited</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded-3">
                                <div class="detail-label">Notes</div>
                                <p class="mb-0 text-dark fst-italic">
                                    "{{ $lead->notes ?: 'No additional notes provided.' }}"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Images --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 px-3 px-md-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">Attached Images</h5>
                    <span class="badge bg-secondary rounded-pill">{{ $lead->images->count() }}</span>
                </div>
                <div class="card-body px-3 px-md-4 pb-4 pt-0">
                    @if($lead->images->isEmpty())
                    <div class="text-center py-5 text-muted bg-light rounded-3">
                        <i class="bi bi-images fs-1 mb-2 d-block opacity-50"></i>
                        No images uploaded
                    </div>
                    @else
                    <div class="row g-3">
                        @foreach($lead->images as $image)
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 border-0 shadow-sm image-card overflow-hidden rounded-3">
                                <div class="ratio ratio-4x3 cursor-pointer bg-light">
                                    <img src="{{ asset('storage/' . $image->image_path) }}"
                                        class="w-100 h-100"
                                        alt="{{ $image->image_type }}"
                                        style="object-fit: cover;">
                                </div>
                                <div class="card-footer bg-white border-top-0 p-2">
                                    <div class="small fw-semibold text-truncate" title="{{ $image->image_type }}">
                                        {{ $image->image_type }}
                                    </div>
                                    @if($image->latitude && $image->longitude)
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">
                                        <i class="bi bi-geo-alt"></i> {{ number_format($image->latitude, 4) }}, {{ number_format($image->longitude, 4) }}
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

        {{-- Right column – map summary --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden sticky-lg-top-custom">
                <div class="card-header bg-white border-0 py-3 px-3 px-md-4 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">Location</h5>
                    @if($lead->latitude && $lead->longitude)
                    <a href="{{ route('leads.map', $lead->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-arrows-fullscreen me-1"></i> Expand Map
                    </a>
                    @endif
                </div>
                <div class="card-body p-0 position-relative">
                    @if($lead->latitude && $lead->longitude)
                    {{-- Static image or simple map container --}}
                    <gmp-map center="{{ $lead->latitude }},{{ $lead->longitude }}" zoom="15" map-id="DEMO_MAP_ID" style="height: 300px; width: 100%; border-radius: 1rem;">
                        <gmp-advanced-marker position="{{ $lead->latitude }},{{ $lead->longitude }}"></gmp-advanced-marker>
                    </gmp-map>

                    {{-- Overlay button --}}
                    <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 1000;">
                        <a href="{{ route('leads.map', $lead->id) }}" class="btn btn-light shadow-lg rounded-pill fw-bold border py-2 px-4 hover-scale">
                            <i class="bi bi-map me-2 text-primary"></i>View Full Map
                        </a>
                    </div>
                    @else
                    <div class="p-5 text-center text-muted bg-light">
                        <i class="bi bi-geo-alt-fill fs-1 mb-3 text-secondary d-block opacity-50"></i>
                        <p class="mb-0">Location coordinates not available.</p>
                    </div>
                    @endif
                </div>
                @if($lead->latitude && $lead->longitude)
                <div class="card-footer bg-white border-top-0 p-3 pt-0">
                    <div class="d-flex align-items-center small text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        <span>Click "Expand Map" to view detailed locations and verifications.</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<input type="hidden" id="lead_id" value="{{ $lead->id }}">
@endsection

@section('script')
<script>
    $(function() {
        (async () => {
            await google.maps.importLibrary("maps");
            await google.maps.importLibrary("marker");
        })();

        // Interaction for 'View Full Map' button hover effect
        $('.hover-scale').hover(
            function() {
                $(this).addClass('shadow-sm').css('transform', 'scale(1.05)');
            },
            function() {
                $(this).removeClass('shadow-sm').css('transform', 'scale(1)');
            }
        );


        // Approve button
        $('#approveLeadBtn').on('click', function() {
            const leadId = $('#lead_id').val();
            if (!leadId) return Swal.fire('Error', 'Lead ID is missing.', 'error');

            Swal.fire({
                title: 'Approve Lead?',
                text: 'Are you sure you want to approve this lead? This action cannot be undone.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Approve',
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('leads.approve', ['lead' => '__ID__']) }}".replace('__ID__', leadId),
                        method: 'POST',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        beforeSend: function() {
                            Swal.showLoading();
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    title: 'Approved!',
                                    text: res.message || 'Lead has been approved.',
                                    icon: 'success',
                                    confirmButtonColor: '#198754',
                                    customClass: {
                                        popup: 'rounded-4'
                                    }
                                }).then(() => window.location.reload());
                            } else {
                                Swal.fire('Error', res.message || 'Failed.', 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error', xhr.responseJSON?.message || 'Server error.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
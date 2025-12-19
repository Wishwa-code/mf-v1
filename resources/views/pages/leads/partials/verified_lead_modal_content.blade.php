<style>
    @media (max-width: 767.98px) {
        .border-end-md {
            border-right: 0 !important;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 1.5rem;
            padding-bottom: 1.5rem;
        }
    }

    @media (min-width: 768px) {
        .border-end-md {}

        gmp-map {
            width: 100%;
            height: 300px;
            border-radius: 12px;
            display: block;
        }
    }
</style>
<div class="row g-4">
    {{-- Left Column: Customer Details --}}
    <div class="col-md-5 border-end-md">
        <div class="text-center mb-4">
            <div class="avatar bg-light rounded-circle p-3 mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                <span class="fs-2 fw-bold text-primary">{{ substr($lead->full_name, 0, 1) }}</span>
            </div>
            <h5 class="fw-bold mb-1">{{ $lead->full_name }}</h5>
            <span class="badge {{ $lead->status == 'rejected' ? 'bg-danger' : 'bg-success' }} bg-opacity-10 text-{{ $lead->status == 'rejected' ? 'danger' : 'success' }} px-3 py-2 rounded-pill">
                {{ ucfirst($lead->status) }}
            </span>
        </div>

        <div class="px-2">
            <div class="mb-3">
                <label class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Contact Info</label>
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-telephone text-primary me-3 bg-primary bg-opacity-10 p-2 rounded-circle"></i>
                    <div>
                        <div class="small fw-semibold">Phone</div>
                        <div>{{ $lead->phone_number }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-2">
                    <i class="bi bi-envelope text-primary me-3 bg-primary bg-opacity-10 p-2 rounded-circle"></i>
                    <div>
                        <div class="small fw-semibold">Email</div>
                        <div class="text-break">{{ $lead->email ?? '-' }}</div>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <i class="bi bi-geo-alt text-primary me-3 bg-primary bg-opacity-10 p-2 rounded-circle"></i>
                    <div>
                        <div class="small fw-semibold">Address</div>
                        <div>{{ $lead->address }}</div>
                    </div>
                </div>
            </div>

            <hr class="my-4 text-muted opacity-25">

            <div class="mb-3">
                <label class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Loan Details</label>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3">
                            <small class="d-block text-muted" style="font-size: 0.7rem;">Type</small>
                            <span class="fw-semibold">{{ $lead->type ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3">
                            <small class="d-block text-muted" style="font-size: 0.7rem;">Amount</small>
                            <span class="fw-semibold">{{ $lead->loan_amount ? number_format($lead->loan_amount, 2) : '-' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3">
                            <small class="d-block text-muted" style="font-size: 0.7rem;">Periods</small>
                            <span class="fw-semibold">{{ $lead->periods ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3">
                            <small class="d-block text-muted" style="font-size: 0.7rem;">Business Cat.</small>
                            <span class="fw-semibold text-truncate d-block" title="{{ $lead->businessCategory->name ?? '-' }}">{{ $lead->businessCategory->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Additional Info</label>
                @if($lead->notes)
                <div class="p-2 bg-light rounded-3 mb-2">
                    <div class="small fw-semibold text-muted" style="font-size: 0.7rem;">Notes</div>
                    <p class="mb-0 small fst-italic">"{{ $lead->notes }}"</p>
                </div>
                @endif

                <div class="p-2 bg-light rounded-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-muted fw-semibold" style="font-size: 0.7rem;">Visit Status</span>
                        @if($lead->is_visited)
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill">Visited</span>
                        @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">Not Visited</span>
                        @endif
                    </div>
                    @if($lead->is_visited)
                    <div class="small text-muted mt-1" style="font-size: 0.65rem;">
                        {{ $lead->visited_latitude }}, {{ $lead->visited_longitude }}
                    </div>
                    @endif
                </div>
            </div>

            @if($lead->latitude && $lead->longitude)
            <div class="mt-4">
                <label class="small text-muted text-uppercase fw-bold mb-2" style="font-size: 0.7rem;">Captured Location</label>

                <a href="https://www.google.com/maps/search/?api=1&query={{ $lead->latitude }},{{ $lead->longitude }}" target="_blank" class="btn btn-outline-primary btn-sm w-100 mb-2">
                    <i class="bi bi-map-fill me-1"></i> View on Google Maps
                </a>



                <gmp-map center="{{ $lead->latitude }},{{ $lead->longitude }}" zoom="15" map-id="DEMO_MAP_ID">
                    <gmp-advanced-marker position="{{ $lead->latitude }},{{ $lead->longitude }}"></gmp-advanced-marker>
                </gmp-map>
            </div>
            @endif
        </div>
    </div>

    {{-- Right Column: Images Gallery --}}
    <div class="col-md-7">
        <h6 class="fw-bold mb-3 border-bottom pb-2">Docs & Images <span class="badge bg-secondary rounded-pill ms-1">{{ $lead->images->count() }}</span></h6>

        @if($lead->images->isEmpty())
        <div class="text-center py-5 bg-light rounded-3">
            <i class="bi bi-images text-muted opacity-50 mb-2" style="font-size: 2rem;"></i>
            <p class="text-muted small mb-0">No images uploaded.</p>
        </div>
        @else
        <div class="row g-3 pe-1">
            @foreach($lead->images as $img)
            <div class="col-6 col-md-6">
                <div class="card h-100 border-0 shadow-sm">
                    <a href="{{ Storage::url($img->image_path) }}" target="_blank" class="position-relative d-block">
                        <div class="ratio ratio-4x3 bg-light rounded-top">
                            <img src="{{ Storage::url($img->image_path) }}" class="card-img-top object-fit-cover" alt="{{ $img->image_type }}">
                        </div>
                        <div class="position-absolute bottom-0 start-0 w-100 p-1 bg-dark bg-opacity-50 text-white text-center small">
                            Click to View
                        </div>
                    </a>
                    <div class="card-body p-2 bg-light rounded-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-truncate fw-bold text-dark small" style="max-width: 120px;" title="{{ $img->image_type }}">
                                {{ $img->image_type }}
                            </div>
                            @if($img->latitude)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ $img->latitude }},{{ $img->longitude }}" target="_blank" class="text-primary" title="Image Location">
                                <i class="bi bi-geo-alt-fill"></i>
                            </a>
                            @endif
                        </div>
                        <small class="text-muted d-block mt-1" style="font-size: 0.65rem;">
                            {{ $img->created_at->format('d M, H:i') }}
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
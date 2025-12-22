<div class="container-fluid px-0">
    <!-- Header Grid -->
    <div class="row g-4 mb-4">
        <!-- Row 1 -->
        <div class="col-md-4">
            <label class="text-uppercase text-muted small fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Description</label>
            <div class="fs-6 text-dark fw-bold">{{ $activity->description ?: 'No description' }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-uppercase text-muted small fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Event Type</label>
            <div>
                @php
                $badgeClass = match($activity->action) {
                'created' => 'bg-success-subtle text-success',
                'updated' => 'bg-warning-subtle text-warning',
                'deleted' => 'bg-danger-subtle text-danger',
                default => 'bg-primary-subtle text-primary'
                };
                @endphp
                <span class="badge {{ $badgeClass }} px-3 py-2 rounded-1 text-uppercase border border-light">
                    {{ str_replace('_', ' ', $activity->action) }}
                </span>
            </div>
        </div>
        <div class="col-md-4">
            <label class="text-uppercase text-muted small fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Subject Type</label>
            <div class="fs-6 text-dark fw-bold">Lead</div>
        </div>

        <!-- Row 2 -->
        <div class="col-md-4">
            <label class="text-uppercase text-muted small fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Subject ID</label>
            <div class="fs-6 text-dark fw-bold">#{{ $activity->lead_id }}</div>
        </div>
        <div class="col-md-4">
            <label class="text-uppercase text-muted small fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Performed By</label>
            <div class="d-flex align-items-center">
                <i class="bi bi-person-circle me-2 text-muted"></i>
                <span class="fs-6 text-dark fw-bold">{{ optional($activity->user)->Full_Name ?: (optional($activity->user)->name ?: 'System') }}</span>
            </div>
        </div>
        <div class="col-md-4">
            <label class="text-uppercase text-muted small fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">Date & Time</label>
            <div class="d-flex align-items-center">
                <i class="bi bi-clock me-2 text-muted"></i>
                <span class="fs-6 text-dark fw-bold">{{ $activity->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>
    </div>

    <!-- Changes Section -->
    @if(isset($activity->properties['attributes']) && !empty($activity->properties['attributes']))
    <h6 class="fw-bold mb-3 mt-4 d-flex align-items-center text-dark">
        <i class="bi bi-pencil-square me-2"></i>
        {{ ($activity->action == 'created') ? 'Initial Attributes' : 'Changes Made' }}
    </h6>
    <div class="d-flex flex-column gap-3">
        @foreach($activity->properties['attributes'] as $field => $newValue)
        <div class="bg-white rounded-3 p-3 border-start border-4 border-success shadow-sm">
            <label class="d-block text-dark fw-bold mb-2 fs-6">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
            <div class="text-success fw-bold text-break bg-success-subtle px-3 py-2 rounded d-inline-block">
                {{ $newValue ?? 'NULL' }}
            </div>
        </div>
        @endforeach
    </div>
    @elseif(isset($activity->properties['changes']) && !empty($activity->properties['changes']))
    <!-- Fallback for old logs -->
    <h6 class="fw-bold mb-3 mt-4 d-flex align-items-center text-dark">
        <i class="bi bi-pencil-square me-2"></i>Changes Made
    </h6>
    <div class="d-flex flex-column gap-3">
        @foreach($activity->properties['changes'] as $field => $change)
        <div class="bg-white rounded-3 p-3 border-start border-4 border-success shadow-sm">
            <label class="d-block text-dark fw-bold mb-2 fs-6">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
            <div class="text-success fw-bold text-break bg-success-subtle px-3 py-2 rounded d-inline-block">
                {{ $change['new'] ?? '-' }}
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Images Section -->
    @if(isset($activity->properties['updated_images']) && !empty($activity->properties['updated_images']))
    <h6 class="fw-bold mb-3 mt-4 d-flex align-items-center text-dark">
        <i class="bi bi-images me-2"></i>Updated Images
    </h6>
    <div class="d-flex flex-wrap gap-3">
        @foreach($activity->properties['updated_images'] as $imageName)
        <div class="bg-white rounded p-3 d-flex align-items-center gap-3 border shadow-sm">
            <div class="bg-light rounded p-2 text-primary">
                <i class="bi bi-file-earmark-image fs-4"></i>
            </div>
            <span class="fw-bold text-dark">{{ $imageName }}</span>
        </div>
        @endforeach
    </div>
    @endif

    <!-- Visit Notes -->
    @if(isset($activity->properties['visit_notes']))
    <h6 class="fw-bold mb-3 mt-4 d-flex align-items-center text-dark">
        <i class="bi bi-sticky me-2"></i>Visit Notes
    </h6>
    <div class="bg-info-subtle bg-opacity-10 rounded p-3 text-dark border-start border-4 border-info shadow-sm fw-medium">
        {{ $activity->properties['visit_notes'] }}
    </div>
    @endif

    <!-- Location -->
    @if(isset($activity->properties['visited_latitude']) && isset($activity->properties['visited_longitude']))
    <h6 class="fw-bold mb-3 mt-4 d-flex align-items-center text-dark">
        <i class="bi bi-geo-alt me-2"></i>Visit Location
    </h6>
    <div class="bg-white rounded p-3 d-flex justify-content-between align-items-center border shadow-sm">
        <div>
            <span class="fw-bold text-dark">Lat:</span> <span class="text-muted">{{ $activity->properties['visited_latitude'] }}</span>
            <span class="mx-3 text-muted">|</span>
            <span class="fw-bold text-dark">Lng:</span> <span class="text-muted">{{ $activity->properties['visited_longitude'] }}</span>
        </div>
        <a href="https://www.google.com/maps?q={{ $activity->properties['visited_latitude'] }},{{ $activity->properties['visited_longitude'] }}" target="_blank" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold">
            <i class="bi bi-map me-1"></i> View on Map
        </a>
    </div>
    @endif
</div>
<div class="card p-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h5 class="text-primary mb-0"><i class="fas fa-list me-2"></i>Registration Summary</h5>
    </div>

    @if(isset($summary) && $summary)
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Registered Branch</label>
                <input type="text" class="form-control" value="{{ $summary->branch_name }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Route</label>
                <input type="text" class="form-control" value="{{ $summary->route_name }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Center</label>
                <input type="text" class="form-control" value="{{ $summary->center_name }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Group</label>
                <input type="text" class="form-control" value="{{ $summary->group_name }}" disabled>
            </div>
        </div>

        <hr class="my-4">

        <h5 class="mb-3 text-primary"><i class="fas fa-info-circle me-2"></i>Other Data</h5>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Registered On</label>
                <input type="text" class="form-control" value="{{ (isset($summary->registered_at) && $summary->registered_at) ? \Carbon\Carbon::parse($summary->registered_at)->format('Y-m-d h:i A') : '-' }}" disabled>
            </div>
            <div class="col-md-6">
                <label class="form-label">Customer Age with Company</label>
                <input type="text" class="form-control" value="{{ (isset($summary->registered_at) && $summary->registered_at) ? \Carbon\Carbon::parse($summary->registered_at)->diff(\Carbon\Carbon::now())->format('%y years %m months %d days') : '-' }}" disabled>
            </div>
        </div>
    @else
        <p class="text-muted mb-0">No summary details found for this customer.</p>
    @endif
</div>

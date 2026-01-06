@extends('layout.admin')

@section('content')
<!-- Filter Section -->
<div class="row m-1">
    <div class="card p-3 shadow-sm border-0 rounded-4">
        <form action="{{ route('activity-logs.index') }}" method="GET" class="d-flex flex-wrap gap-2 align-items-end">
            <div class="flex-grow-1">
                <label class="form-label text-muted small fw-bold">User Name</label>
                <input type="text" name="user_name" class="form-control border-0 bg-light rounded-3"
                    value="{{ request('user_name') }}" placeholder="Search by name...">
            </div>

            <div class="flex-grow-1">
                <label class="form-label text-muted small fw-bold">Date</label>
                <input type="date" name="date" class="form-control border-0 bg-light rounded-3"
                    value="{{ request('date') }}">
            </div>

            <div class="flex-grow-1">
                <label class="form-label text-muted small fw-bold">Event</label>
                <select name="event" class="form-select border-0 bg-light rounded-3">
                    <option value="">All Events</option>
                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    <option value="login" {{ request('event') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('event') == 'logout' ? 'selected' : '' }}>Logout</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary rounded-3 px-4">
                <i class="ri-filter-3-line me-1"></i> Filter
            </button>
            <a href="{{ route('activity-logs.index') }}" class="btn btn-light rounded-3">
                Reset
            </a>
        </form>
    </div>
</div>

<!-- Logs Table -->
<div class="row m-1 mt-3">
    <div class="card p-0 shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-3">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="ri-history-line me-2 text-primary"></i> Activity Logs
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 text-muted small text-uppercase">Timestamp</th>
                        <th class="text-muted small text-uppercase">User</th>
                        <th class="text-muted small text-uppercase">Action</th>
                        <th class="text-muted small text-uppercase">Subject</th>
                        <th class="text-end pe-4 text-muted small text-uppercase">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">{{ $activity->created_at->format('M d, Y') }}</span>
                                <small class="text-muted">{{ $activity->created_at->format('h:i A') }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar-sm bg-light-primary rounded-circle me-2 d-flex align-items-center justify-content-center text-primary fw-bold">
                                    {{ substr($activity->properties['causer_name'] ?? ($activity->causer->Full_Name ?? 'System'), 0, 1) }}
                                </div>
                                <span class="fw-medium text-dark">{{ $activity->properties['causer_name'] ?? ($activity->causer->Full_Name ?? 'System') }}</span>
                            </div>
                        </td>
                        <td>
                            @php
                            $evt = $activity->event ?? $activity->description;
                            $badgeClass = match($evt) {
                            'created' => 'bg-success-subtle text-success',
                            'updated' => 'bg-warning-subtle text-warning',
                            'deleted' => 'bg-danger-subtle text-danger',
                            'login' => 'bg-info-subtle text-info',
                            'logout' => 'bg-dark-subtle text-dark',
                            default => 'bg-secondary-subtle text-secondary'
                            };
                            @endphp
                            <span class="badge {{ $badgeClass }} rounded-pill px-3 py-2 text-uppercase" style="font-size: 0.75rem;">
                                {{ ucfirst($evt) }}
                            </span>
                        </td>
                        <td>
                            @if($activity->subject_type)
                            <span class="text-muted">{{ class_basename($activity->subject_type) }}</span>
                            <small class="text-secondary ms-1">#{{ $activity->subject_id }}</small>
                            @else
                            <span class="text-muted fst-italic">System</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-light rounded-3 text-primary view-details-btn"
                                data-id="{{ $activity->id }}">
                                View
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <div class="d-flex flex-column align-items-center">
                                <i class="ri-file-list-3-line fs-1 mb-2 opacity-50"></i>
                                <span>No activity logs found.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top p-3">
            {{ $activities->links() }}
        </div>
    </div>
</div>

<!-- Detail Modal (75% Width) -->
<div class="modal fade" id="activityModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 75%;">
        <div class="modal-content rounded-4 border-0 shadow-lg" style="width: 100%;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Activity Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="modalLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>

                <div id="modalContent" class="d-none">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">Old Values</h6>
                            <div class="bg-light p-3 rounded-3" style="max-height: 400px; overflow-y: auto;">
                                <div id="oldValues" class="text-secondary small">--</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">New Values</h6>
                            <div class="bg-light p-3 rounded-3" style="max-height: 400px; overflow-y: auto;">
                                <div id="newValues" class="text-secondary small">--</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        const modal = $('#activityModal');
        const modalLoading = $('#modalLoading');
        const modalContent = $('#modalContent');
        const oldValuesEl = $('#oldValues');
        const newValuesEl = $('#newValues');
        const modalTitle = $('#modalTitle');

        function formatValue(value) {
            if (value === null || value === undefined) return '<span class="text-muted">N/A</span>';
            if (value === true || value === 'true') return '<span class="badge bg-success-subtle text-success">Yes</span>';
            if (value === false || value === 'false') return '<span class="badge bg-danger-subtle text-danger">No</span>';
            if (typeof value === 'object') return '<pre class="m-0 small">' + JSON.stringify(value, null, 2) + '</pre>';
            return String(value);
        }

        function formatKey(key) {
            // Replace underscores with spaces and capitalize words
            return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        function generateTable(data) {
            if (!data || Object.keys(data).length === 0) {
                return '<div class="text-muted fst-italic py-2">No data available</div>';
            }

            let html = '<table class="table table-sm table-borderless mb-0"><tbody>';
            for (const [key, value] of Object.entries(data)) {
                html += `
                    <tr>
                        <td class="text-muted fw-bold" style="width: 40%; vertical-align: top;">${formatKey(key)}:</td>
                        <td class="text-dark" style="width: 60%;">${formatValue(value)}</td>
                    </tr>`;
            }
            html += '</tbody></table>';
            return html;
        }

        // Handle button click to open modal and fetch data
        $(document).on('click', '.view-details-btn', function() {
            const logId = $(this).data('id');

            // Show modal
            modal.modal('show');

            // Reset state
            modalLoading.removeClass('d-none');
            modalContent.addClass('d-none');
            oldValuesEl.html('--');
            newValuesEl.html('--');
            modalTitle.text('Activity Details');

            // AJAX Request
            $.ajax({
                url: `/settings/activity-logs/${logId}`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    modalLoading.addClass('d-none');
                    modalContent.removeClass('d-none');

                    const props = data.properties || {};
                    // Check if properties are nested (standard log) or flat (custom log)
                    const isStandard = props.hasOwnProperty('attributes') || props.hasOwnProperty('old');

                    let oldContent = '';
                    let newContent = '';

                    if (isStandard) {
                        const old = props.old || {};
                        const attributes = props.attributes || {};
                        oldContent = generateTable(old);
                        newContent = generateTable(attributes);
                    } else {
                        // For custom logs like login, show all properties in "New Values"
                        // If "old" exists in flat props, separate it (unlikely but safe)
                        newContent = generateTable(props);
                        oldContent = generateTable({});
                    }

                    oldValuesEl.html(oldContent);
                    newValuesEl.html(newContent);

                    const eventName = (data.event || data.description || 'Event').toUpperCase();
                    const subject = data.subject_type ? `${data.subject_type} #${data.subject_id}` : 'System Action';

                    modalTitle.text(`${eventName} - ${subject}`);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching log details:', error);
                    oldValuesEl.html('<div class="text-danger">Error loading data</div>');
                    newValuesEl.html('<div class="text-danger">Error loading data</div>');
                    modalLoading.addClass('d-none');
                    modalContent.removeClass('d-none');
                }
            });
        });
    });
</script>
@endpush
@endsection
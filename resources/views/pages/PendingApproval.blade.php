@extends('layout.admin')

@section('head')
{{-- DataTables CSS --}}
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">

<style>
    /* Modern Card Styling */
    .card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        border-radius: 1rem;
        overflow: hidden;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.5rem;
    }

    .page-title {
        font-weight: 700;
        color: #343a40;
        letter-spacing: -0.5px;
    }

    /* Table Styling */
    #pendingApprovalTable thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e9ecef;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    #pendingApprovalTable tbody td {
        vertical-align: middle;
        font-size: 0.9rem;
        color: #555;
        border-bottom: 1px solid #f0f0f0;
    }

    /* Badge Styling */
    .badge {
        font-weight: 500;
        padding: 0.5em 0.8em;
        border-radius: 0.5rem;
    }

    .badge.bg-primary {
        background-color: #eef2ff !important;
        color: #4f46e5 !important;
    }

    .badge.bg-info {
        background-color: #e0f2fe !important;
        color: #0ea5e9 !important;
    }

    .badge.bg-warning {
        background-color: #fffbeb !important;
        color: #d97706 !important;
    }

    /* Action Buttons */
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: all 0.2s;
        margin: 0 2px;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-view {
        background-color: #e0f2fe;
        color: #0ea5e9;
        border: none;
    }

    .btn-view:hover {
        background-color: #0ea5e9;
        color: #fff;
    }

    .btn-approve {
        background-color: #dcfce7;
        color: #16a34a;
        border: none;
    }

    .btn-approve:hover {
        background-color: #16a34a;
        color: #fff;
    }

    .btn-reject {
        background-color: #fee2e2;
        color: #dc2626;
        border: none;
    }

    .btn-reject:hover {
        background-color: #dc2626;
        color: #fff;
    }

    .btn-callback {
        background-color: #fffbeb;
        color: #d97706;
        border: none;
    }

    .btn-callback:hover {
        background-color: #d97706;
        color: #fff;
    }

    /* Expand Icon */
    .details-control {
        cursor: pointer;
        color: #6c757d;
        transition: color 0.2s;
    }

    .details-control:hover {
        color: #4f46e5;
    }

    tr.shown .details-control i {
        color: #4f46e5;
        transform: rotate(180deg);
    }

    .child-row-content {
        background-color: #f8fafc;
        border-radius: 0.5rem;
        padding: 1rem;
        margin: 0.5rem 0;
        border: 1px solid #e2e8f0;
    }

    /* Filter Section */
    .filter-section {
        background-color: #f8f9fa;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .form-label {
        font-weight: 500;
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .form-select,
    .form-control {
        border-radius: 0.5rem;
        border: 1px solid #dee2e6;
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }

    .form-select:focus,
    .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-12">
 {{dd($branches)}}
            {{-- Filter Section --}}
            <div class="card mb-4 filter-section border-0">
                <form method="GET" action="{{ route('approval.pending') }}">
                    <div class="row g-3 align-items-end">
                        @if(session('branch_id') == session('head_branch'))
                        <div class="col-md-4">
                            <label for="branch_id" class="form-label">Branch</label>
                            <select class="form-select" id="branch_id" name="branch_id">
                                <option value="">All Branches</option>
                                @foreach($branches as $branch)
                               
                                <option value="{{ $branch->branch_id }}"
                                    {{ $selectedBranch == $branch->branch_id ? 'selected' : '' }}>
                                    {{ $branch->Name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <div class="{{ session('branch_id') == session('head_branch') ? 'col-md-4' : 'col-md-8' }}">
                            @if(session('branch_id') != session('head_branch'))
                            <input type="hidden" name="branch_id" value="{{ session('branch_id') }}">
                            @endif
                            <label for="type" class="form-label">Request Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All Request Types</option>
                                @foreach($types as $category => $typesList)
                                <optgroup label="{{ $category }}">
                                    @foreach($typesList as $code => $name)
                                    <option value="{{ $code }}" {{ (string)$selectedType === (string)$code ? 'selected' : '' }}>
                                        {{ $code }} - {{ $name }}
                                    </option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary px-4" style="border-radius: 0.5rem; background-color: #4f46e5; border: none;">
                                    <i class="ri-filter-3-line me-1"></i> Filter
                                </button>
                                @if(!empty($selectedBranch) || !empty($selectedType))
                                <a href="{{ route('approval.pending') }}" class="btn btn-light px-4" style="border-radius: 0.5rem; color: #6c757d;">
                                    <i class="ri-refresh-line me-1"></i> Clear
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Main Content Card --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-white border-0 pb-0">
                    <div>
                        <h4 class="page-title mb-1">Pending Approvals</h4>
                        <p class="text-muted small mb-0">Manage and review pending requests requiring your attention.</p>
                    </div>
                    <span class="badge bg-warning text-dark fs-6 d-flex align-items-center gap-2">
                        <i class="ri-time-line"></i> {{ $pendingCount }} Pending
                    </span>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="pendingApprovalTable" class="table table-hover align-middle" style="width: 100%" data-is-head-office="{{ session('branch_id') == session('head_branch') ? 'true' : 'false' }}">
                            <thead>
                                <tr>

                                    <th>Branch</th>
                                    <th>Type</th>
                                    <th>Date Submitted</th>
                                    <th>Requested By</th>
                                    <th>Description</th>
                                    @if(session('branch_id') == session('head_branch'))
                                    <th class="text-center" style="min-width: 140px;">Actions</th>
                                    @else
                                    <th class="text-center">View</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                {{-- populated by yajra --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Details Modal (Generic) --}}
<div class="modal fade" id="detailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
            <div class="modal-header bg-light border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="detailsModalLabel">Request Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="detailsModalContent">
                <div class="d-flex justify-content-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- Action Modal --}}
<div class="modal fade" id="actionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 1rem;">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold" id="actionModalLabel">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <div class="text-center mb-4">
                    <div id="actionIconWrapper" class="mb-3">
                        <i id="modalIcon" style="font-size: 3rem;"></i>
                    </div>
                    <h4 id="modalTitle" class="mb-2"></h4>
                    <p id="modalMessage" class="text-muted"></p>
                </div>

                <div class="mb-3">
                    <label for="actionComment" class="form-label" id="commentLabel"></label>
                    <textarea class="form-control" id="actionComment" rows="3" style="resize: none;"></textarea>
                    <div class="invalid-feedback">
                        This field is required.
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn px-4" id="confirmBtn" onclick="confirmAction()">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/responsive.bootstrap5.min.js"></script>

<script>
    let currentRequestId = null;
    let currentAction = null;

    $(document).ready(function() {
        // Initialize Tooltips inside the table (event delegation handled by DT draw callback or specific init)
        $('body').tooltip({
            selector: '[data-bs-toggle="tooltip"]'
        });

        const isHeadOffice = $('#pendingApprovalTable').data('is-head-office');

        // Define columns
        let columns = [{
                data: 'branch_name',
                name: 'branch_name',
                render: function(data) {
                    return `<span class="badge bg-primary">${data}</span>`;
                }
            },
            {
                data: 'type_name',
                name: 'type_name',
                render: function(data, type, row) {
                    return `<span class="badge bg-info text-dark">${data || row.typeid}</span>`;
                }
            },
            {
                data: 'data_time',
                name: 'data_time',
                render: function(data) {
                    if (!data) return '';
                    const date = new Date(data);
                    // Format: DD MMM YYYY
                    const day = date.toLocaleString('default', {
                        day: '2-digit'
                    });
                    const month = date.toLocaleString('default', {
                        month: 'short'
                    });
                    const year = date.getFullYear();
                    const time = date.toLocaleString('default', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                    return `<div class="d-flex flex-column"><span class="fw-bold text-dark">${day} ${month} ${year}</span><small class="text-muted">${time}</small></div>`;
                }
            },
            {
                data: 'user_full_name',
                name: 'user_full_name',
                render: function(data) {
                    const initial = data ? data.charAt(0) : 'U';
                    return `<div class="d-flex align-items-center"><div class="avatar-xs me-2"><span class="avatar-title rounded-circle bg-soft-primary text-primary">${initial}</span></div><span class="fw-medium text-dark">${data || 'N/A'}</span></div>`;
                }
            },
            {
                data: 'description',
                name: 'description',
                visible: true,
                render: function(data, type, row) {
                    return `<span class="text-wrap">${data || 'N/A'}</span>`;
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false,
                className: 'text-center'
            }
        ];

        const table = $('#pendingApprovalTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('approval.pending') }}",
                data: function(d) {
                    d.branch_id = $('#branch_id').val();
                    d.type = $('#type').val();
                }
            },
            columns: columns,
            order: [
                [3, 'desc']
            ], // Sort by Date Submitted
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search requests...",
                paginate: {
                    next: '<i class="ri-arrow-right-s-line"></i>',
                    previous: '<i class="ri-arrow-left-s-line"></i>'
                }
            },
            dom: '<"d-flex justify-content-between align-items-center mb-3"f>t<"d-flex justify-content-between align-items-center mt-3"ip>'
        });

        // Clean up Filter Input
        $('.dataTables_filter input').addClass('form-control').css('width', '250px');

        // Filter Change Events
        $('#branch_id, #type').on('change', function() {
            table.draw();
        });


    });

    // ------------------------------------------------------------------
    // View Details Logic - Mapped to Routes
    // ------------------------------------------------------------------
    function viewDetails(id, type, typeId) {
        let url = '';

        // Map Type ID to Route
        // Note: Please ensure these routes exist in web.php and point to ApprovalController methods
        switch (parseInt(typeId)) {
            case 401:
                url = '{{ route("approval.loan_details", ":id") }}';
                break;
            case 402:
                url = '{{ route("approval.loan_rejection_details", ":id") }}';
                break;
            case 403:
                url = '{{ route("approval.loan_installment_modification_details", ":id") }}';
                break;
            case 201:
                url = '{{ route("approval.designation_details", ":id") }}';
                break;
            case 101:
                url = '{{ route("approval.user_creation_details", ":id") }}';
                break;
            case 102:
                url = '{{ route("approval.user_details_update_details", ":id") }}';
                break;
            case 103:
                url = '{{ route("approval.user_privilege_change_details", ":id") }}';
                break;
            case 301:
                url = '{{ route("approval.customer_creation_details", ":id") }}';
                break;
            case 302:
                url = '{{ route("approval.customer_update_details", ":id") }}';
                break;
            case 304:
                url = '{{ route("approval.customer_status_change_details", ":id") }}';
                break;
            case 305:
                url = '{{ route("approval.customer_document_delete_details", ":id") }}';
                break;
            case 603:
                url = '{{ route("approval.expense_delete_details", ":id") }}';
                break;
            default:
                // Try to guess based on type string if ID match fails
                console.warn('Unknown Type ID:', typeId);
                // attempt generic handling or alert
                if (url === '') {
                    Swal.fire({
                        icon: 'info',
                        title: 'Details',
                        text: 'No detailed view available for this request type.'
                    });
                    return;
                }
        }

        url = url.replace(':id', id);
        loadDetails(url, type);
    }

    function loadDetails(url, title) {
        // Set generic title
        $('#detailsModalLabel').text(title ? title + ' Details' : 'Request Details');

        // Show loading state
        $('#detailsModalContent').html(`
                <div class="d-flex flex-column align-items-center justify-content-center py-5">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <span class="text-muted">Fetching details...</span>
                </div>
            `);

        $('#detailsModal').modal('show');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#detailsModalContent').html(response.html);
                } else {
                    $('#detailsModalContent').html(`
                            <div class="alert alert-warning d-flex align-items-center">
                                <i class="ri-alert-line fs-1 me-3"></i>
                                <div>
                                    <h5 class="alert-heading">Notice</h5>
                                    <p class="mb-0">${response.message || 'Details not found.'}</p>
                                </div>
                            </div>
                        `);
                }
            },
            error: function(xhr) {
                $('#detailsModalContent').html(`
                         <div class="alert alert-danger d-flex align-items-center">
                            <i class="ri-error-warning-line fs-1 me-3"></i>
                            <div>
                                <h5 class="alert-heading">Error</h5>
                                <p class="mb-0">Failed to load details. Please try again later.</p>
                            </div>
                        </div>
                    `);
            }
        });
    }

    // ------------------------------------------------------------------
    // Action Logic (Approve, Reject, Callback)
    // ------------------------------------------------------------------
    function approveRequest(id) {
        setupActionModal(id, 'approve');
    }

    function rejectRequest(id) {
        setupActionModal(id, 'reject');
    }

    function callbackRequest(id) {
        setupActionModal(id, 'callback');
    }

    function setupActionModal(id, action) {
        currentRequestId = id;
        currentAction = action;
        const $comment = $('#actionComment');
        const $btn = $('#confirmBtn');
        const $icon = $('#modalIcon');
        const $wrapper = $('#actionIconWrapper');

        // Reset validation
        $comment.val('').removeClass('is-invalid');

        if (action === 'approve') {
            $('#modalTitle').text('Approve Request');
            $('#modalMessage').text('Are you sure you want to approve this request? This action cannot be undone.');
            $('#commentLabel').html('Comment <span class="text-muted small">(Optional)</span>');
            $comment.attr('placeholder', 'Add a note (optional)...');

            $wrapper.attr('class', 'mb-3 text-success');
            $icon.attr('class', 'ri-checkbox-circle-line');

            $btn.attr('class', 'btn btn-success px-4 text-white');
            $btn.text('Approve Request');

        } else if (action === 'reject') {
            $('#modalTitle').text('Reject Request');
            $('#modalMessage').text('Are you sure you want to reject this request? Please provide a reason.');
            $('#commentLabel').html('Rejection Reason <span class="text-danger">*</span>');
            $comment.attr('placeholder', 'Explain why this request is being rejected...');

            $wrapper.attr('class', 'mb-3 text-danger');
            $icon.attr('class', 'ri-close-circle-line');

            $btn.attr('class', 'btn btn-danger px-4 text-white');
            $btn.text('Reject Request');

        } else if (action === 'callback') {
            $('#modalTitle').text('Request Callback');
            $('#modalMessage').text('Return this request for clarification or changes?');
            $('#commentLabel').html('Callback Reason <span class="text-danger">*</span>');
            $comment.attr('placeholder', 'What needs to be changed?');

            $wrapper.attr('class', 'mb-3 text-warning');
            $icon.attr('class', 'ri-question-line');

            $btn.attr('class', 'btn btn-warning px-4 text-white');
            $btn.text('Send for Callback');
        }

        $('#actionModal').modal('show');
    }

    function confirmAction() {
        const comment = $('#actionComment').val().trim();

        // Validation for Reject/Callback
        if (currentAction !== 'approve' && comment === '') {
            $('#actionComment').addClass('is-invalid');
            return;
        }
        $('#actionComment').removeClass('is-invalid');

        let url, data;

        // Disable button
        const $btn = $('#confirmBtn');
        const originalText = $btn.text();
        $btn.prop('disabled', true).text('Processing...');

        if (currentAction === 'approve') {
            url = '{{ route("approval.approve") }}';
            data = {
                id: currentRequestId,
                comment: comment,
                _token: '{{ csrf_token() }}'
            };
        } else if (currentAction === 'reject') {
            url = '{{ route("approval.reject") }}';
            data = {
                id: currentRequestId,
                reason: comment,
                _token: '{{ csrf_token() }}'
            };
        } else if (currentAction === 'callback') {
            url = '{{ route("approval.callback") }}';
            data = {
                id: currentRequestId,
                comment: comment,
                _token: '{{ csrf_token() }}'
            };
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(response) {
                $('#actionModal').modal('hide');
                $btn.prop('disabled', false).text(originalText);

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message || 'Action completed successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Something went wrong.'
                    });
                }
            },
            error: function() {
                $('#actionModal').modal('hide');
                $btn.prop('disabled', false).text(originalText);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'Failed to process request. Please try again.'
                });
            }
        });
    }
</script>
@endsection
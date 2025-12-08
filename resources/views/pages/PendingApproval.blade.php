@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        thead {
            background-color: #d9edf7;
            color: #31708f;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .details-control {
            cursor: pointer;
            color: #007bff;
            font-size: 1.2rem;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .details-control:hover {
            color: #0056b3;
        }
        .child-row-content {
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 3px solid #007bff;
        }
        .child-row-content strong {
            color: #495057;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3 align-items-center">
                            <h4 class="page-title mb-0">Pending Approval</h4>
                            <span class="badge bg-warning text-dark fs-6">
                                {{ count($pendingApprovals) }} Pending
                            </span>
                        </div>

                        {{-- Filters --}}
                        <form method="GET" action="{{ route('approval.pending') }}" class="mb-4">
                            <div class="row mb-3">
                                @if(session('branch_id') == -1)
                                    <div class="col-md-6">
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

                                <div class="{{ session('branch_id') == -1 ? 'col-md-6' : 'col-md-12' }}">
                                    @if(session('branch_id') != -1)
                                        <input type="hidden" name="branch_id" value="{{ session('branch_id') }}">
                                    @endif
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select" id="type" name="type">
                                        <option value="">All Types</option>
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
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="ri-filter-line me-1"></i>Filter
                                    </button>
                                    @if(!empty($selectedBranch) || !empty($selectedType))
                                        <a href="{{ route('approval.pending') }}" class="btn btn-outline-secondary">
                                            <i class="ri-close-line me-1"></i>Clear
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>

                        @if(count($pendingApprovals) > 0)
                            <div class="table-responsive">
                                <table id="pendingApprovalTable" class="table table-striped table-bordered nowrap" style="width: 100%">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 30px;"></th>
                                        <th>Branch</th>
                                        <th>Type</th>
                                        <th>Date &amp; Time</th>
                                        <th>User</th>
                                        <th class="text-center">View</th>
                                        @if(session('branch_id') == -1)
                                            <th class="text-center">Actions</th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($pendingApprovals as $approval)
                                        <tr data-description="{{ htmlspecialchars($approval->description ?? '', ENT_QUOTES, 'UTF-8') }}">
                                            <td class="details-control text-center">
                                                <i class="ri-add-circle-line"></i>
                                            </td>
                                            <td>
                                                    <span class="badge bg-primary">
                                                        {{ $approval->branch_name ?? 'N/A' }}
                                                    </span>
                                            </td>
                                            <td>
                                                    <span class="badge bg-info text-dark">
                                                        {{ $approval->type ?? $approval->typeid }}
                                                    </span>
                                            </td>
                                            <td>
                                                <small>{{ date('d/m/Y', strtotime($approval->data_time)) }}</small><br>
                                                <small class="text-muted">{{ date('h:i A', strtotime($approval->data_time)) }}</small>
                                            </td>
                                            <td>
                                                    <span class="text-primary">
                                                        {{ $approval->user_full_name ?? 'N/A' }}
                                                    </span>
                                            </td>
                                            <td class="text-center">
                                                <button
                                                        class="btn btn-outline-info btn-sm"
                                                        onclick="viewDetails({{ $approval->id }}, @json($approval->type), {{ $approval->typeid }})"
                                                        title="View Details"
                                                >
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                            </td>
                                            @if(session('branch_id') == -1)
                                                <td class="text-center action-buttons">
                                                    <button
                                                            class="btn btn-success btn-sm me-1"
                                                            onclick="approveRequest({{ $approval->id }})"
                                                            title="Approve"
                                                    >
                                                        <i class="ri-check-line"></i> Approve
                                                    </button>
                                                    <button
                                                            class="btn btn-danger btn-sm me-1"
                                                            onclick="rejectRequest({{ $approval->id }})"
                                                            title="Reject"
                                                    >
                                                        <i class="ri-close-line"></i> Reject
                                                    </button>
                                                    <button
                                                            class="btn btn-warning btn-sm"
                                                            onclick="callbackRequest({{ $approval->id }})"
                                                            title="Callback"
                                                    >
                                                        <i class="ri-phone-line"></i> Callback
                                                    </button>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="ri-checkbox-circle-line" style="font-size: 4rem; color: #28a745;"></i>
                                </div>
                                <h5 class="text-muted mb-1">No Pending Approvals</h5>
                                <p class="text-muted mb-0">All requests have been processed.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Loan / Generic details modal (reused for many types) --}}
    <div class="modal fade" id="loanDetailsModal" tabindex="-1" aria-labelledby="loanDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="loanDetailsModalLabel">
                        <i class="ri-file-text-line me-2"></i>Loan Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="loanDetailsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Loading details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Designation details modal --}}
    <div class="modal fade" id="designationDetailsModal" tabindex="-1" aria-labelledby="designationDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="designationDetailsModalLabel">
                        <i class="ri-shield-user-line me-2"></i>Designation Change Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="designationDetailsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border text-warning" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3">Loading designation details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Common action modal (Approve / Reject / Callback) --}}
    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" id="modalHeader">
                    <h5 class="modal-title" id="actionModalLabel">
                        <i id="modalIcon" class="me-2"></i><span id="modalTitle"></span>
                    </h5>
                    <button type="button" class="btn-close" id="modalCloseBtn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="modalMessage"></p>
                    <div class="mb-3">
                        <label for="actionComment" class="form-label" id="commentLabel"></label>
                        <textarea class="form-control" id="actionComment" rows="3" placeholder=""></textarea>
                        <div class="invalid-feedback">
                            Please provide a comment.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn" id="confirmBtn" onclick="confirmAction()">
                        <i id="confirmIcon" class="me-1"></i><span id="confirmText"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        let currentRequestId = null;
        let currentAction    = null;

        $(document).ready(function () {
            const isHeadOffice       = {{ session('branch_id') == -1 ? 'true' : 'false' }};
            const nonSortableColumns = isHeadOffice ? [0, 5, 6] : [0, 5];

            const table = $('#pendingApprovalTable').DataTable({
                pageLength: 25,
                responsive: false,
                order: [[3, 'desc']],
                columnDefs: [
                    { orderable: false, targets: nonSortableColumns }
                ]
            });

            // Expand/collapse description
            $('#pendingApprovalTable tbody').on('click', 'td.details-control', function () {
                const tr   = $(this).closest('tr');
                const row  = table.row(tr);
                const icon = $(this).find('i');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('ri-subtract-line').addClass('ri-add-circle-line');
                } else {
                    const description = (tr.data('description') || '').toString();
                    const safeText    = description === '' ? '<em>No description</em>' : description;
                    row.child('<div class="child-row-content"><strong>Description:</strong> ' + safeText + '</div>').show();
                    tr.addClass('shown');
                    icon.removeClass('ri-add-circle-line').addClass('ri-subtract-line');
                }
            });
        });

        function viewDetails(id, type, typeId) {
            if (typeId === 401) {
                showLoanDetails(id);
            } else if (typeId === 402) {
                showLoanRejectionDetails(id);
            } else if (typeId === 403) {
                showLoanInstallmentModificationDetails(id);
            } else if (typeId === 201) {
                showDesignationDetails(id);
            } else if (typeId === 101) {
                showUserCreationDetails(id);
            } else if (typeId === 102) {
                showUserDetailsUpdateDetails(id);
            } else if (typeId === 103) {
                showUserPrivilegeChangeDetails(id);
            } else if (typeId === 301) {
                showCustomerCreationDetails(id);
            } else if (typeId === 302) {
                showCustomerDetailsUpdateDetails(id);
            } else if (typeId === 304) {
                showCustomerStatusChangeDetails(id);
            } else if (typeId === 305) {
                showCustomerDocumentDeleteDetails(id);
            } else if (typeId === 603) {
                showExpenseDeleteDetails(id);
            } else {
                alert(`View details for ${type || 'Unknown'} request #${id} (Type ID: ${typeId})`);
            }
        }

        // All the show* functions are same as your code, just left as-is:
        // showLoanDetails, showDesignationDetails, showLoanRejectionDetails,
        // showLoanInstallmentModificationDetails, showUserCreationDetails,
        // showUserDetailsUpdateDetails, showUserPrivilegeChangeDetails,
        // showCustomerCreationDetails, showCustomerDetailsUpdateDetails,
        // showCustomerStatusChangeDetails, showCustomerDocumentDeleteDetails,
        // showExpenseDeleteDetails. (No change needed – they already look good.)

        function approveRequest(id) {
            currentRequestId = id;
            currentAction    = 'approve';

            $('#modalHeader').attr('class', 'modal-header bg-success text-white');
            $('#modalCloseBtn').attr('class', 'btn-close btn-close-white');
            $('#modalIcon').attr('class', 'ri-check-line me-2');
            $('#modalTitle').text('Approve Request');
            $('#modalMessage').text('Are you sure you want to approve this request?');
            $('#commentLabel').html('Approval Comment <span class="text-muted">(Optional)</span>');
            $('#actionComment').attr('placeholder', 'Enter approval comment...').val('').removeClass('is-invalid');
            $('#confirmBtn').attr('class', 'btn btn-success');
            $('#confirmIcon').attr('class', 'ri-check-line me-1');
            $('#confirmText').text('Approve');

            $('#actionModal').modal('show');
        }

        function rejectRequest(id) {
            currentRequestId = id;
            currentAction    = 'reject';

            $('#modalHeader').attr('class', 'modal-header bg-danger text-white');
            $('#modalCloseBtn').attr('class', 'btn-close btn-close-white');
            $('#modalIcon').attr('class', 'ri-close-line me-2');
            $('#modalTitle').text('Reject Request');
            $('#modalMessage').text('Are you sure you want to reject this request?');
            $('#commentLabel').html('Rejection Reason <span class="text-danger">*</span>');
            $('#actionComment').attr('placeholder', 'Enter rejection reason...').val('').removeClass('is-invalid');
            $('#confirmBtn').attr('class', 'btn btn-danger');
            $('#confirmIcon').attr('class', 'ri-close-line me-1');
            $('#confirmText').text('Reject');

            $('#actionModal').modal('show');
        }

        function callbackRequest(id) {
            currentRequestId = id;
            currentAction    = 'callback';

            $('#modalHeader').attr('class', 'modal-header bg-warning text-dark');
            $('#modalCloseBtn').attr('class', 'btn-close');
            $('#modalIcon').attr('class', 'ri-phone-line me-2');
            $('#modalTitle').text('Callback Request');
            $('#modalMessage').text('Are you sure you want to send this request back for callback?');
            $('#commentLabel').html('Callback Comment <span class="text-danger">*</span>');
            $('#actionComment').attr('placeholder', 'Enter callback comment...').val('').removeClass('is-invalid');
            $('#confirmBtn').attr('class', 'btn btn-warning');
            $('#confirmIcon').attr('class', 'ri-phone-line me-1');
            $('#confirmText').text('Send Callback');

            $('#actionModal').modal('show');
        }

        function confirmAction() {
            const comment    = $('#actionComment').val().trim();
            const isRequired = (currentAction !== 'approve');

            if (isRequired && comment === '') {
                $('#actionComment').addClass('is-invalid');
                return;
            }
            $('#actionComment').removeClass('is-invalid');

            let url, data;

            if (currentAction === 'approve') {
                url  = '{{ route("approval.approve") }}';
                data = { id: currentRequestId, comment, _token: '{{ csrf_token() }}' };
            } else if (currentAction === 'reject') {
                url  = '{{ route("approval.reject") }}';
                data = { id: currentRequestId, reason: comment, _token: '{{ csrf_token() }}' };
            } else if (currentAction === 'callback') {
                url  = '{{ route("approval.callback") }}';
                data = { id: currentRequestId, comment, _token: '{{ csrf_token() }}' };
            } else {
                return;
            }

            $.ajax({
                url,
                method: 'POST',
                data,
                success: function (response) {
                    $('#actionModal').modal('hide');

                    if (response.success) {
                        showSuccessAlert(response.message || 'Action completed.');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showErrorAlert(response.message || 'Error occurred.');
                    }
                },
                error: function () {
                    $('#actionModal').modal('hide');
                    showErrorAlert('Error processing request. Please try again.');
                }
            });
        }

        function showSuccessAlert(message) {
            const html = `
                <div class="alert alert-success alert-dismissible fade show position-fixed"
                     style="top:20px; right:20px; z-index:9999;" role="alert">
                    <i class="ri-check-circle-line me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            $('body').append(html);
        }

        function showErrorAlert(message) {
            const html = `
                <div class="alert alert-danger alert-dismissible fade show position-fixed"
                     style="top:20px; right:20px; z-index:9999;" role="alert">
                    <i class="ri-error-warning-line me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`;
            $('body').append(html);
        }
    </script>
@endsection

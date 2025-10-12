@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    
    <style>
        /* Custom thead style */
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
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <h4 class="page-title">Pending Approval</h4>
                            <span class="badge bg-warning text-dark fs-6">{{ count($pendingApprovals) }} Pending</span>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="{{ route('approval.pending') }}" class="mb-4">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="branch_id" class="form-label">Branch</label>
                                    <select class="form-select" id="branch_id" name="branch_id" {{ $branch_access == 0 ? 'disabled' : '' }}>
                                        @if($branch_access == 1)
                                            <option value="">All Branches</option>
                                        @endif
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->branch_id }}"
                                                    {{ $selectedBranch == $branch->branch_id ? 'selected' : '' }}>
                                                {{ $branch->Name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($branch_access == 0)
                                        <input type="hidden" name="branch_id" value="{{ session('branch_id') }}">
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label for="type" class="form-label">Type</label>
                                    <select class="form-select" id="type" name="type">
                                        <option value="">All Types</option>
                                        @foreach($types as $category => $typesList)
                                            <optgroup label="{{ $category }}">
                                                @foreach($typesList as $code => $name)
                                                    <option value="{{ $code }}" {{ $selectedType == $code ? 'selected' : '' }}>
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
                                <table id="pendingApprovalTable" class="table table-striped table-bordered nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Branch</th>
                                            <th>Type</th>
                                            <th>Date & Time</th>
                                            <th>Description</th>
                                            <th>User</th>
                                            <th class="text-center">View</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($pendingApprovals as $approval)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ $approval->branch_name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info text-dark">{{ $approval->type }}</span>
                                                </td>
                                                <td>
                                                    <small>{{ date('d/m/Y', strtotime($approval->data_time)) }}</small><br>
                                                    <small class="text-muted">{{ date('h:i A', strtotime($approval->data_time)) }}</small>
                                                </td>
                                                <td>
                                                    <div style="max-width: 300px;">
                                                        <strong>{{ $approval->description }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-primary">
                                                        {{ $approval->user_full_name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-outline-info btn-sm" 
                                                            onclick="viewDetails({{ $approval->id }}, '{{ $approval->type }}', {{ $approval->typeid }})"
                                                            title="View Details">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                </td>
                                                <td class="text-center action-buttons">
                                                    <button class="btn btn-success btn-sm me-1" 
                                                            onclick="approveRequest({{ $approval->id }})"
                                                            title="Approve">
                                                        <i class="ri-check-line"></i> Approve
                                                    </button>
                                                    <button class="btn btn-danger btn-sm me-1" 
                                                            onclick="rejectRequest({{ $approval->id }})"
                                                            title="Reject">
                                                        <i class="ri-close-line"></i> Reject
                                                    </button>
                                                    <button class="btn btn-warning btn-sm" 
                                                            onclick="callbackRequest({{ $approval->id }})"
                                                            title="Callback">
                                                        <i class="ri-phone-line"></i> Callback
                                                    </button>
                                                </td>
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
                                <h5 class="text-muted">No Pending Approvals</h5>
                                <p class="text-muted">All requests have been processed.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loan Details Modal -->
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
                        <p class="mt-3">Loading loan details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal -->
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
        let currentAction = null;

        $(document).ready(function() {
            $('#pendingApprovalTable').DataTable({
                "pageLength": 25,
                "responsive": true,
                "order": [[ 2, "desc" ]], // Sort by date column
                "columnDefs": [
                    { "orderable": false, "targets": [5, 6] } // Disable sorting for View and Actions columns
                ]
            });
        });

        function viewDetails(id, type, typeId) {
            if (typeId == 401) {
                // Loan Approval - Show loan details modal
                showLoanDetails(id);
            } else {
                // Other types - show alert for now
                alert(`View details for ${type} request #${id} (Type ID: ${typeId})`);
                // TODO: Implement other type views
            }
        }

        function showLoanDetails(approvalId) {
            $('#loanDetailsModal').modal('show');
            $('#loanDetailsContent').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3">Loading loan details...</p>
                </div>
            `);
            
            $.ajax({
                url: `/approval/loan-details/${approvalId}`,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#loanDetailsContent').html(response.html);
                    } else {
                        $('#loanDetailsContent').html(`
                            <div class="alert alert-danger" role="alert">
                                <i class="ri-error-warning-line me-2"></i>Error: ${response.message}
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#loanDetailsContent').html(`
                        <div class="alert alert-danger" role="alert">
                            <i class="ri-error-warning-line me-2"></i>Error loading loan details. Please try again.
                        </div>
                    `);
                }
            });
        }

        function approveRequest(id) {
            currentRequestId = id;
            currentAction = 'approve';
            
            // Configure modal for approve
            $('#modalHeader').removeClass().addClass('modal-header bg-success text-white');
            $('#modalCloseBtn').removeClass().addClass('btn-close btn-close-white');
            $('#modalIcon').removeClass().addClass('ri-check-line me-2');
            $('#modalTitle').text('Approve Request');
            $('#modalMessage').text('Are you sure you want to approve this request?');
            $('#commentLabel').html('Approval Comment <span class="text-muted">(Optional)</span>');
            $('#actionComment').attr('placeholder', 'Enter approval comment...').val('').removeClass('is-invalid');
            $('#confirmBtn').removeClass().addClass('btn btn-success');
            $('#confirmIcon').removeClass().addClass('ri-check-line me-1');
            $('#confirmText').text('Approve');
            
            $('#actionModal').modal('show');
        }

        function rejectRequest(id) {
            currentRequestId = id;
            currentAction = 'reject';
            
            // Configure modal for reject
            $('#modalHeader').removeClass().addClass('modal-header bg-danger text-white');
            $('#modalCloseBtn').removeClass().addClass('btn-close btn-close-white');
            $('#modalIcon').removeClass().addClass('ri-close-line me-2');
            $('#modalTitle').text('Reject Request');
            $('#modalMessage').text('Are you sure you want to reject this request?');
            $('#commentLabel').html('Rejection Reason <span class="text-danger">*</span>');
            $('#actionComment').attr('placeholder', 'Enter rejection reason...').val('').removeClass('is-invalid');
            $('#confirmBtn').removeClass().addClass('btn btn-danger');
            $('#confirmIcon').removeClass().addClass('ri-close-line me-1');
            $('#confirmText').text('Reject');
            
            $('#actionModal').modal('show');
        }

        function callbackRequest(id) {
            currentRequestId = id;
            currentAction = 'callback';
            
            // Configure modal for callback
            $('#modalHeader').removeClass().addClass('modal-header bg-warning text-dark');
            $('#modalCloseBtn').removeClass().addClass('btn-close');
            $('#modalIcon').removeClass().addClass('ri-phone-line me-2');
            $('#modalTitle').text('Callback Request');
            $('#modalMessage').text('Are you sure you want to send this request back for callback?');
            $('#commentLabel').html('Callback Comment <span class="text-danger">*</span>');
            $('#actionComment').attr('placeholder', 'Enter callback comment...').val('').removeClass('is-invalid');
            $('#confirmBtn').removeClass().addClass('btn btn-warning');
            $('#confirmIcon').removeClass().addClass('ri-phone-line me-1');
            $('#confirmText').text('Send Callback');
            
            $('#actionModal').modal('show');
        }

        function confirmAction() {
            const comment = $('#actionComment').val().trim();
            const isRequired = currentAction !== 'approve';
            
            if (isRequired && comment === '') {
                $('#actionComment').addClass('is-invalid');
                return;
            }
            
            $('#actionComment').removeClass('is-invalid');
            
            let url, data;
            
            switch(currentAction) {
                case 'approve':
                    url = '{{ route("approval.approve") }}';
                    data = { id: currentRequestId, comment: comment, _token: '{{ csrf_token() }}' };
                    break;
                case 'reject':
                    url = '{{ route("approval.reject") }}';
                    data = { id: currentRequestId, reason: comment, _token: '{{ csrf_token() }}' };
                    break;
                case 'callback':
                    url = '{{ route("approval.callback") }}';
                    data = { id: currentRequestId, comment: comment, _token: '{{ csrf_token() }}' };
                    break;
                default:
                    return;
            }
            
            $.ajax({
                url: url,
                method: 'POST',
                data: data,
                success: function(response) {
                    $('#actionModal').modal('hide');
                    if (response.success) {
                        showSuccessAlert(response.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showErrorAlert('Error: ' + response.message);
                    }
                },
                error: function() {
                    $('#actionModal').modal('hide');
                    showErrorAlert('Error processing request. Please try again.');
                }
            });
        }

        function showSuccessAlert(message) {
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
                    <i class="ri-check-circle-line me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('body').append(alertHtml);
        }

        function showErrorAlert(message) {
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999;" role="alert">
                    <i class="ri-error-warning-line me-2"></i>${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('body').append(alertHtml);
        }
    </script>
@endsection

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

    /* Expand/Collapse icon styling */
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

    /* Child row styling */
    .child-row-content {
        padding: 15px;
        background-color: #f8f9fa;
        border-left: 3px solid #dc3545;
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
                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="page-title">Rejected Approval</h4>
                        <span class="badge bg-danger fs-6">{{ $rejectedCount }} Rejected</span>
                    </div>

                    <!-- Filters -->
                    <form method="GET" action="{{ route('approval.rejected') }}" class="mb-4">
                        <div class="row mb-3">
                            @if(session('branch_id') == -1)
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
                            <div class="{{ session('branch_id') == -1 ? 'col-md-4' : 'col-md-6' }}">
                                @if(session('branch_id') != -1)
                                <input type="hidden" name="branch_id" value="{{ session('branch_id') }}">
                                @endif
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
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ri-filter-line me-1"></i>Filter
                                </button>
                                @if(!empty($selectedBranch) || !empty($selectedType) || !empty($dateFrom) || !empty($dateTo))
                                <a href="{{ route('approval.rejected') }}" class="btn btn-outline-secondary">
                                    <i class="ri-close-line me-1"></i>Clear
                                </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table id="rejectedApprovalTable" class="table table-striped table-bordered nowrap" style="width:100%" data-is-head-office="{{ session('branch_id') == -1 ? 'true' : 'false' }}">
                            <thead>
                                <tr>

                                    <th>Branch</th>
                                    <th>Type</th>
                                    <th>Request Date</th>
                                    <th>Rejected Date</th>
                                    <th>User</th>
                                    <th>Rejected By</th>
                                    <th>Reason</th>
                                    <th>Description</th>
                                    <th>View</th>
                                    @if(session('branch_id') == -1)
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Populated by Yajra --}}
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

@endsection

@section('script')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function() {
        var isHeadOffice = $('#rejectedApprovalTable').data('is-head-office');

        var table = $('#rejectedApprovalTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('approval.rejected') }}",
                data: function(d) {
                    d.branch_id = $('#branch_id').val();
                    d.type = $('#type').val();
                    d.date_from = $('#date_from').val();
                    d.date_to = $('#date_to').val();
                }
            },
            columns: [{
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
                        return `<span class="badge bg-danger">${data || row.typeid}</span>`;
                    }
                },
                {
                    data: 'data_time',
                    name: 'data_time',
                    render: function(data) {
                        if (!data) return '';
                        const date = new Date(data);
                        return `<small>${date.toLocaleString()}</small>`;
                    }
                },
                {
                    data: 'approved_date_time',
                    name: 'approved_date_time',
                    render: function(data) {
                        if (!data) return 'N/A';
                        const date = new Date(data);
                        return `<small>${date.toLocaleString()}</small>`;
                    }
                },
                {
                    data: 'user_full_name',
                    name: 'user_full_name',
                    render: function(data) {
                        return `<span class="text-primary">${data || 'N/A'}</span>`;
                    }
                },
                {
                    data: 'rejected_by_full_name',
                    name: 'rejected_by_full_name',
                    render: function(data) {
                        return `<span class="text-danger">${data || 'N/A'}</span>`;
                    }
                },
                {
                    data: 'comment',
                    name: 'comment',
                    render: function(data) {
                        return `<div style="max-width: 200px;"><small class="text-muted">${data || 'No reason provided'}</small></div>`;
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
                },
                ...(isHeadOffice ? [{
                    data: 'typeid',
                    name: 'undo_action',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        if (data == 401) {
                            return `<button onclick="undoRejection(${row.id})" class="btn btn-sm btn-warning" title="Undo Rejection"><i class="ri-arrow-go-back-line"></i> Undo</button>`;
                        }
                        return '<span class="text-muted">-</span>';
                    }
                }] : [])
            ],
            order: [
                [4, "desc"]
            ], // Sort by rejected date column
            pageLength: 25,
            responsive: false,
        });

        // Clean up Filter Input
        $('.dataTables_filter input').addClass('form-control').css('width', '250px');

        // Filter Change Events
        $('#branch_id, #type, #date_from, #date_to').on('change', function() {
            table.draw();
        });


    });

    function undoRejection(approvalId) {
        Swal.fire({
            title: 'Undo Rejection?',
            text: "This will send the loan back to pending approval status.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, undo it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/approval/undo-rejection/${approvalId}`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Undone!',
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                // Refresh table instead of reload
                                $('#rejectedApprovalTable').DataTable().draw(false);
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Failed to undo rejection. Please try again.', 'error');
                    }
                });
            }
        });
    }

    function viewDetails(id, type, typeId) {
        let url = '';

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
                Swal.fire({
                    icon: 'info',
                    title: 'Details',
                    text: 'No specific details view available.'
                });
                return;
        }
        url = url.replace(':id', id);

        $('#detailsModalLabel').text(type ? type + ' Details' : 'Request Details');
        $('#detailsModalContent').html('<div class="d-flex justify-content-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');
        $('#detailsModal').modal('show');

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#detailsModalContent').html(response.html);
                } else {
                    $('#detailsModalContent').html('<div class="alert alert-warning">' + (response.message || 'Details not found') + '</div>');
                }
            },
            error: function() {
                $('#detailsModalContent').html('<div class="alert alert-danger">Failed to load details.</div>');
            }
        });
    }
</script>
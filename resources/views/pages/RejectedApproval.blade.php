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
                            <span class="badge bg-danger fs-6">{{ count($rejectedApprovals) }} Rejected</span>
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
                        
                        @if(count($rejectedApprovals) > 0)
                            <div class="table-responsive">
                                <table id="rejectedApprovalTable" class="table table-striped table-bordered nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 30px;"></th>
                                            <th>Branch</th>
                                            <th>Type</th>
                                            <th>Request Date</th>
                                            <th>Rejected Date</th>
                                            <th>User</th>
                                            <th>Rejected By</th>
                                            <th>Reason</th>
                                            @if(session('branch_id') == -1)
                                                <th>Action</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rejectedApprovals as $rejected)
                                            <tr data-description="{{ htmlspecialchars($rejected->description, ENT_QUOTES, 'UTF-8') }}">
                                                <td class="details-control text-center">
                                                    <i class="ri-add-circle-line"></i>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $rejected->branch_name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger">{{ $rejected->type }}</span>
                                                </td>
                                                <td>
                                                    <small>{{ date('d/m/Y h:i A', strtotime($rejected->data_time)) }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ $rejected->approved_date_time ? date('d/m/Y h:i A', strtotime($rejected->approved_date_time)) : 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <span class="text-primary">
                                                        {{ $rejected->user_full_name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-danger">
                                                        {{ $rejected->rejected_by_full_name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div style="max-width: 200px;">
                                                        <small class="text-muted">{{ $rejected->comment ?? 'No reason provided' }}</small>
                                                    </div>
                                                </td>
                                                @if(session('branch_id') == -1)
                                                    <td>
                                                        @if($rejected->typeid == 401)
                                                            <button onclick="undoRejection({{ $rejected->id }})" class="btn btn-sm btn-warning" title="Undo Rejection">
                                                                <i class="ri-arrow-go-back-line"></i> Undo
                                                            </button>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
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
                                    <i class="ri-close-circle-line" style="font-size: 4rem; color: #dc3545;"></i>
                                </div>
                                <h5 class="text-muted">No Rejected Requests</h5>
                                <p class="text-muted">No requests have been rejected.</p>
                            </div>
                        @endif
                    </div>
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
            var isHeadOffice = {{ session('branch_id') == -1 ? 'true' : 'false' }};
            var nonSortableColumns = isHeadOffice ? [0, 8] : [0]; // Adjust based on visible columns
            
            var table = $('#rejectedApprovalTable').DataTable({
                "pageLength": 25,
                "responsive": false,
                "order": [[ 4, "desc" ]], // Sort by rejected date column
                "columnDefs": [
                    { "orderable": false, "targets": nonSortableColumns } // Disable sorting for expand and action columns
                ]
            });
            
            // Add event listener for expand/collapse icon
            $('#rejectedApprovalTable tbody').on('click', 'td.details-control', function() {
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var icon = $(this).find('i');
                
                if (row.child.isShown()) {
                    // Close the row
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('ri-subtract-line').addClass('ri-add-circle-line');
                } else {
                    // Open the row
                    var description = tr.data('description');
                    row.child('<div class="child-row-content"><strong>Description:</strong> ' + description + '</div>').show();
                    tr.addClass('shown');
                    icon.removeClass('ri-add-circle-line').addClass('ri-subtract-line');
                }
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
                                    window.location.reload();
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
    </script>
@endsection

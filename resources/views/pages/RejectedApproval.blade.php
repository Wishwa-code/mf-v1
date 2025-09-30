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
                            <h4 class="page-title">Rejected Approval</h4>
                            <span class="badge bg-danger fs-6">{{ count($rejectedApprovals) }} Rejected</span>
                        </div>

                        <!-- Filters -->
                        <form method="GET" action="{{ route('approval.rejected') }}" class="mb-4">
                            <div class="row mb-3">
                                <div class="col-md-4">
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
                                <div class="col-md-4">
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
                                            <th>Branch</th>
                                            <th>Type</th>
                                            <th>Request Date</th>
                                            <th>Rejected Date</th>
                                            <th>Description</th>
                                            <th>User</th>
                                            <th>Rejected By</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rejectedApprovals as $rejected)
                                            <tr>
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
                                                    <div style="max-width: 250px;">
                                                        <strong>{{ $rejected->description }}</strong>
                                                    </div>
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
            $('#rejectedApprovalTable').DataTable({
                "pageLength": 25,
                "responsive": true,
                "order": [[ 3, "desc" ]], // Sort by rejected date column
            });
        });
    </script>
@endsection

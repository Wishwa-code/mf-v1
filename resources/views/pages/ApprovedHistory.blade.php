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
                            <h4 class="page-title">Approved History</h4>
                            <span class="badge bg-success fs-6">{{ count($approvedHistory) }} Approved</span>
                        </div>

                        <!-- Branch Filter -->
                        <form method="GET" action="{{ route('approval.approved') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="branch_id" class="form-label">Filter by Branch</label>
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
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="ri-filter-line me-1"></i>Filter
                                    </button>
                                    @if(!empty($selectedBranch))
                                        <a href="{{ route('approval.approved') }}" class="btn btn-outline-secondary">
                                            <i class="ri-close-line me-1"></i>Clear
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                        
                        @if(count($approvedHistory) > 0)
                            <div class="table-responsive">
                                <table id="approvedHistoryTable" class="table table-striped table-bordered nowrap" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>Branch</th>
                                            <th>Type</th>
                                            <th>Request Date</th>
                                            <th>Approved Date</th>
                                            <th>Description</th>
                                            <th>User</th>
                                            <th>Approved By</th>
                                            <th>Comment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($approvedHistory as $approved)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ $approved->branch_name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-success">{{ $approved->type }}</span>
                                                </td>
                                                <td>
                                                    <small>{{ date('d/m/Y h:i A', strtotime($approved->data_time)) }}</small>
                                                </td>
                                                <td>
                                                    <small>{{ $approved->approved_date_time ? date('d/m/Y h:i A', strtotime($approved->approved_date_time)) : 'N/A' }}</small>
                                                </td>
                                                <td>
                                                    <div style="max-width: 250px;">
                                                        <strong>{{ $approved->description }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-primary">
                                                        {{ $approved->user_full_name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-success">
                                                        {{ $approved->approved_by_full_name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div style="max-width: 200px;">
                                                        <small class="text-muted">{{ $approved->comment ?? 'No comment' }}</small>
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
                                    <i class="ri-history-line" style="font-size: 4rem; color: #28a745;"></i>
                                </div>
                                <h5 class="text-muted">No Approved Requests</h5>
                                <p class="text-muted">No requests have been approved yet.</p>
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
            $('#approvedHistoryTable').DataTable({
                "pageLength": 25,
                "responsive": true,
                "order": [[ 3, "desc" ]], // Sort by approved date column
            });
        });
    </script>
@endsection

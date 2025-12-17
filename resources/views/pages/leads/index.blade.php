@extends('layout.admin')

@section('content')
<div class="container-fluid">
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0" style="font-weight: 600; color: #333;">Leads Management</h4>
                    <a href="{{ route('leads.create') }}" class="btn btn-primary" style="border-radius: 8px; font-weight: 500;">
                        <i class="bi bi-plus-lg me-1"></i> Add New Lead
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table id="leads-table" class="table table-hover table-striped dt-responsive nowrap w-100" style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Periods</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $lead)
                                <tr>
                                    <td>{{ $lead->id }}</td>
                                    <td class="fw-medium">{{ $lead->full_name }}</td>
                                    <td>{{ $lead->phone_number }}</td>
                                    <td>
                                        <span class="badge bg-soft-info text-info">{{ ucfirst($lead->type) }}</span>
                                    </td>
                                    <td>{{ $lead->periods }}</td>
                                    <td>{{ Str::limit($lead->address, 30) }}</td>
                                    <td>
                                        @if($lead->status == 'pending')
                                            <span class="badge bg-soft-warning text-warning">Pending</span>
                                        @elseif($lead->status == 'approved')
                                            <span class="badge bg-soft-success text-success">Approved</span>
                                        @else
                                            <span class="badge bg-soft-secondary text-secondary">{{ ucfirst($lead->status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $lead->created_at_lead ? \Carbon\Carbon::parse($lead->created_at_lead)->format('Y-m-d H:i') : '-' }}</td>
                                    <td>
                                        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="border-radius: 6px;">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#leads-table').DataTable({
            responsive: true,
            order: [[ 0, "desc" ]],
            language: {
                searchPlaceholder: "Search records",
                search: "",
            },
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-light btn-sm'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-light btn-sm'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-light btn-sm'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-light btn-sm'
                },
                {
                    extend: 'print',
                    className: 'btn btn-light btn-sm'
                }
            ],
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control').css('margin-left','10px');
                $('.dt-buttons .btn').removeClass('btn-secondary').addClass('btn-light btn-sm border');
            }
        });
    });
</script>
@endsection

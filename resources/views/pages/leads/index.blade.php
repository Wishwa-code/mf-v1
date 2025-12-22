@extends('layout.admin')

@section('content')
<div class="container-fluid pb-5">
    <!-- Page Header -->
    <div class="row align-items-center mb-4 mt-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-1 fw-bold fs-3 text-dark">Leads Management</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small text-muted">
                            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Leads</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('leads.create') }}" class="btn btn-primary btn-modern shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> Add New Lead
                </a>
            </div>
        </div>
    </div>

    <!-- Modern Styles (Matching Agreements/Create) -->
    <style>
        .card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
            transition: all 0.2s;
        }

        .btn-modern {
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        /* Table Styling */
        .table-modern thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #edf2f9;
            padding: 1rem 0.75rem;
        }

        .table-modern tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #edf2f9;
            color: #495057;
            font-size: 0.9rem;
        }

        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }

        .table-modern tbody tr {
            transition: background-color 0.2s;
        }

        .table-modern tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge-modern {
            padding: 0.4em 0.8em;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.75rem;
        }
    </style>

    <div class="row">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-body p-0"> <!-- p-0 for flush table -->
                    <div class="table-responsive p-3">
                        <table id="leads-table" class="table table-modern table-borderless dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="ps-4">Action</th> <!-- Action First -->
                                    <th>ID</th>
                                    <th>Full Name</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Periods</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leads as $lead)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-sm btn-outline-success d-inline-flex align-items-center gap-1" style="border-radius: 6px;" title="Edit Lead">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </a>
                                            <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-sm btn-outline-primary d-inline-flex align-items-center gap-1" style="border-radius: 6px;" title="View Details">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                        </div>
                                    </td>
                                    <td><span class="fw-bold text-dark">#{{ $lead->id }}</span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary-subtle rounded-circle d-flex align-items-center justify-content-center me-2 text-primary fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ substr($lead->full_name, 0, 1) }}
                                            </div>
                                            <span class="fw-medium text-dark">{{ $lead->full_name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $lead->phone_number }}</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle badge-modern">{{ ucfirst($lead->type) }}</span>
                                    </td>
                                    <td>{{ $lead->periods }} <span class="text-muted small">Months</span></td>
                                    <td><span class="text-truncate d-block" style="max-width: 150px;" title="{{ $lead->address }}">{{ $lead->address }}</span></td>
                                    <td>
                                        @if($lead->status == 'pending')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle badge-modern">Pending</span>
                                        @elseif($lead->status == 'approved')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle badge-modern">Approved</span>
                                        @else
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle badge-modern">{{ ucfirst($lead->status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $lead->created_at_lead ? \Carbon\Carbon::parse($lead->created_at_lead)->format('Y-m-d') : '-' }} <small class="text-muted">{{ $lead->created_at_lead ? \Carbon\Carbon::parse($lead->created_at_lead)->format('H:i') : '' }}</small></td>
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
            order: [
                [1, "desc"]
            ], // Order by ID (index 1 now, since Action is 0)
            language: {
                searchPlaceholder: "Search leads...",
                search: "",
                lengthMenu: "Show _MENU_ entries"
            },
            dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center'B><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-light btn-sm border'
                },
                {
                    extend: 'print',
                    className: 'btn btn-light btn-sm border'
                }
            ],
            initComplete: function() {
                $('.dataTables_filter input').addClass('form-control form-control-sm').css('margin-left', '10px');
                $('.dt-buttons').addClass('d-flex gap-1');
            }
        });
    });
</script>
@endsection
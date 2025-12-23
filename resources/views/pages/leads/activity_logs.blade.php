@extends('layout.admin')

@section('head')
<!-- DataTables -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<!-- Flatpickr -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<!-- Filter Styling -->
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 12px !important;
        color: #495057;
    }
</style>
@endsection

@section('content')
<div class="container-fluid pb-5">
    <!-- Page Header -->
    <div class="row align-items-center mb-4 mt-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-1 fw-bold fs-3 text-dark">Lead Activity Logs</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small text-muted">
                            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('leads.verifiedList') }}" class="text-decoration-none text-muted">Leads</a></li>
                            <li class="breadcrumb-item active text-primary" aria-current="page">Activity Logs</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Styles -->
    <style>
        .card-modern {
            border: none;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            background: #fff;
            overflow: hidden;
            transition: all 0.2s;
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

        div.dataTables_wrapper div.dataTables_filter input {
            margin-left: 0.5em;
            display: inline-block;
            width: auto;
            min-height: calc(1.5em + .5rem + 2px);
            padding: .25rem .5rem;
            font-size: .875rem;
            border-radius: .2rem;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
        }
    </style>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-body">
                    <form id="filter-form" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label text-muted small fw-bold text-uppercase">Start Date</label>
                            <input type="text" class="form-control bg-light border-0 flatpickr-input" id="start_date" name="start_date" placeholder="Select Date">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label text-muted small fw-bold text-uppercase">End Date</label>
                            <input type="text" class="form-control bg-light border-0 flatpickr-input" id="end_date" name="end_date" placeholder="Select Date">
                        </div>
                        <div class="col-md-2">
                            <label for="action_filter" class="form-label text-muted small fw-bold text-uppercase">Action</label>
                            <select class="form-select bg-light border-0 select2-filter" id="action_filter" name="action">
                                <option value="all">All Actions</option>
                                @foreach($actions as $action)
                                <option value="{{ $action }}">{{ ucfirst(str_replace('_', ' ', $action)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="user_filter" class="form-label text-muted small fw-bold text-uppercase">User</label>
                            <select class="form-select bg-light border-0 select2-filter" id="user_filter" name="user_id">
                                <option value="all">All Users</option>
                                @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->Full_Name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="button" class="btn btn-primary w-100" id="btn-filter"><i class="bi bi-funnel-fill me-1"></i> Filter</button>
                            <button type="button" class="btn btn-light w-100 border" id="btn-reset"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(optional($privilege)->lead_activity_logs == 1)
    <div class="row">
        <div class="col-12">
            <div class="card card-modern">
                <div class="card-body p-0">
                    <div class="table-responsive p-3">
                        <table id="activity-logs-table" class="table table-modern table-borderless dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th class="ps-4">Action</th>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Lead</th>
                                    <th>Changes</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-danger mt-4">
        <i class="bi bi-lock-fill me-2"></i> You do not have permission to view activity logs.
    </div>
    @endif
</div>

<!-- Activity Details Modal -->
<div class="modal fade" id="activityDetailsModal" tabindex="-1" aria-labelledby="activityDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="activityDetailsModalLabel">Activity Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="activityDetailsModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $(function() {
        // Initialize Flatpickr
        $(".flatpickr-input").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: true
        });

        // Initialize Select2
        $('.select2-filter').select2({
            width: '100%',
            placeholder: "Select an option",
            allowClear: true
        });

        var table = $('#activity-logs-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('leads.activityLogsData') }}",
                data: function(d) {
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                    d.action = $('#action_filter').val();
                    d.user_id = $('#user_filter').val();
                }
            },
            columns: [{
                    data: 'action',
                    name: 'action',
                    className: 'ps-4'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'user_id',
                    name: 'user.name'
                },
                {
                    data: 'lead_id',
                    name: 'lead.full_name'
                },
                {
                    data: 'action_btn',
                    name: 'action_btn',
                    orderable: false,
                    searchable: false
                }
            ],
            order: [
                [1, 'desc'] // Order by Date
            ],
            language: {
                searchPlaceholder: "Search activities...",
                search: "",
                lengthMenu: "Show _MENU_ entries"
            },
            dom: "<'row mb-3'<'col-sm-12 col-md-6 d-flex align-items-center'B><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            initComplete: function() {
                $('.dt-buttons .btn').addClass('btn btn-light btn-sm border');
            }
        });

        $('#btn-filter').click(function() {
            table.draw();
        });

        $('#btn-reset').click(function() {
            $('#filter-form')[0].reset();
            table.draw();
        });
    });

    function viewActivityDetails(id) {
        const modalBody = document.getElementById('activityDetailsModalBody');
        modalBody.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading details...</p>
        </div>
    `;

        const modal = new bootstrap.Modal(document.getElementById('activityDetailsModal'));
        modal.show();

        $.ajax({
            url: "/leads/activity-logs/" + id + "/details",
            type: "GET",
            success: function(html) {
                modalBody.innerHTML = html;
            },
            error: function() {
                modalBody.innerHTML = `
                <div class="text-center py-5 text-danger">
                    <i class="bi bi-exclamation-circle fs-1 mb-2"></i>
                    <p>Failed to load activity details.</p>
                </div>
            `;
            }
        });
    }
</script>
@endsection
@extends('layout.admin')

@section('head')
    {{-- Select2 --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    {{-- DataTables --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .bg-purple { background-color: #1A2942 !important; color: #fff !important; }
        .bg-purple th { color: #fff !important; }
        .form-label { font-weight: 700; }
        .summary-card { border: 1px solid #eef1f5; box-shadow: 0 6px 18px rgba(0,0,0,0.05); border-radius: 12px; }
        table.dataTable tbody td { padding: 6px 10px; vertical-align: middle; }
        table.dataTable thead th { white-space: nowrap; }
    </style>
@endsection

@section('content')
    @php
        $selectedBranch = request('branch') ?? session('branch_id');
        $fromDateVal = $fromDate ?? request('from_date') ?? date('Y-m-d');
        $toDateVal   = $toDate   ?? request('to_date')   ?? date('Y-m-d');
    @endphp

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="page-title mb-0">Daily Collection Ratio</h4>
                    </div>

                    {{-- ✅ Summary --}}
                    <div class="row g-3 my-3">
                        <div class="col-md-4">
                            <div class="card summary-card">
                                <div class="card-body">
                                    <div class="fw-bold text-muted">Total Collection (Selected Date Range)</div>
                                    <div class="fs-3 fw-bold text-success">{{ number_format($totalCollection ?? 0, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card summary-card">
                                <div class="card-body">
                                    <div class="fw-bold text-muted">Collectable</div>
                                    <div class="fs-3 fw-bold text-primary">{{ number_format($totalCollectable ?? 0, 2) }}</div>
                                    <div class="small text-muted">Used for ratio denominator</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card summary-card">
                                <div class="card-body">
                                    <div class="fw-bold text-muted">Ratio (Total Collection ÷ Collectable)</div>
                                    <div class="fs-3 fw-bold text-danger">{{ number_format((($ratio ?? 0) * 100), 2) }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ Filters --}}
                    <form action="{{ route('report.dailycollectionratio') }}" method="get" class="row g-3 mb-4">
                        @csrf

                        {{-- Branch --}}
                        <div class="col-md-3">
                            <label class="form-label">Branch</label>
                            <select class="form-control select2" id="branch" name="branch"
                                    {{ session('branch_id') != -1 ? 'disabled' : '' }}>
                                <option value="">All</option>
                                @foreach($branch as $b)
                                    <option value="{{ $b->branch_id }}"
                                            {{ (string)$selectedBranch === (string)$b->branch_id ? 'selected' : '' }}>
                                        {{ $b->Name }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- If NOT HO => send fixed branch --}}
                            @if(session('branch_id') != -1)
                                <input type="hidden" name="branch" value="{{ session('branch_id') }}">
                            @endif
                        </div>

                        {{-- Center --}}
                        <div class="col-md-3">
                            <label class="form-label">Center</label>
                            <select id="centerFilter" name="center_id" class="form-control select2">
                                <option value="">All</option>
                                @foreach($centers as $c)
                                    <option value="{{ $c->idCenter }}"
                                            {{ request('center_id') == $c->idCenter ? 'selected' : '' }}>
                                        {{ $c->No }} - {{ $c->Name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Route --}}
                        <div class="col-md-3">
                            <label class="form-label">Route</label>
                            <select id="routeFilter" name="route_id" class="form-control select2">
                                <option value="">All</option>
                                @foreach($routes as $r)
                                    @php
                                        // ✅ FIX: avoid Undefined property errors
                                        $routeId   = $r->id_route ?? $r->idRoute ?? null;
                                        $routeName = $r->route_name ?? $r->Name ?? $r->name ?? '';
                                    @endphp
                                    <option value="{{ $routeId }}"
                                            {{ request('route_id') == $routeId ? 'selected' : '' }}>
                                        {{ $routeName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Group (optional) --}}
                        <div class="col-md-3">
                            <label class="form-label">Group</label>
                            <select id="groupFilter" name="group_name" class="form-control select2">
                                <option value="">All Groups</option>
                                @foreach($groups as $g)
                                    <option value="{{ $g->group_name }}"
                                            {{ request('group_name') == $g->group_name ? 'selected' : '' }}>
                                        {{ $g->group_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- From Date --}}
                        <div class="col-md-3">
                            <label class="form-label">From Date</label>
                            <input type="date" name="from_date" class="form-control" value="{{ $fromDateVal }}">
                        </div>

                        {{-- To Date --}}
                        <div class="col-md-3">
                            <label class="form-label">To Date</label>
                            <input type="date" name="to_date" class="form-control" value="{{ $toDateVal }}">
                        </div>

                        {{-- Search --}}
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-search"></i> Search
                            </button>
                        </div>
                    </form>

                    {{-- ✅ Table --}}
                    <table id="customerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                        <thead class="bg-purple">
                        <tr>
                            <th>No</th>
                            <th>Branch</th>
                            <th>Route</th>
                            <th>Center</th>
                            <th>Client Name</th>
                            <th>Loan Number</th>
                            <th>Phone</th>
                            <th>Installment Amount</th>

                            <th>Paid Amount (Range)</th>
                            <th>Arrears Balance</th>
                            <th>Arrears Reason (Loan Comment)</th>

                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($loan as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row->branch_name }}</td>
                                <td>{{ $row->route_name ?? '-' }}</td>
                                <td>{{ $row->center_name ?? '-' }}</td>
                                <td>{{ $row->First_Name }} {{ $row->Last_Name }}</td>
                                <td>{{ $row->Loan_No }}</td>
                                <td>{{ $row->Contact_No }}</td>
                                <td class="text-end">{{ number_format($row->Installment_Amount ?? 0, 2) }}</td>

                                <td class="text-end fw-bold text-success">{{ number_format($row->paid_amount ?? 0, 2) }}</td>
                                <td class="text-end">{{ number_format($row->arrears_balance ?? 0, 2) }}</td>

                                <td>
                                    {{ $row->arrears_reason ?? '' }}
                                </td>

                                <td class="text-center">
                                    <a href="/loanview/{{ $row->idCustomer_Loan }}" target="_blank" class="btn btn-warning btn-sm">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div> {{-- card-body --}}
            </div> {{-- card --}}
        </div>
    </div>
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    {{-- Select2 --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    {{-- DataTables --}}
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>

    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2();

            // ✅ DataTable with FULL export
            $('#customerTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                pageLength: 25,
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: ':visible',
                            modifier: { page: 'all', search: 'applied', order: 'applied' }
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn btn-danger',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible',
                            modifier: { page: 'all', search: 'applied', order: 'applied' }
                        }
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print',
                        className: 'btn btn-info',
                        exportOptions: {
                            columns: ':visible',
                            modifier: { page: 'all', search: 'applied', order: 'applied' }
                        }
                    }
                ]
            });

            // ✅ Branch change => load centers + routes + groups
            $('#branch').on('change', function () {
                let branchId = $(this).val();

                // reset
                if (branchId === '') {
                    $('#centerFilter').empty().append('<option value="">All</option>').trigger('change.select2');
                    $('#routeFilter').empty().append('<option value="">All</option>').trigger('change.select2');
                    $('#groupFilter').empty().append('<option value="">All Groups</option>').trigger('change.select2');
                    return;
                }

                $.ajax({
                    url: '{{ route("ajax.centers.routes") }}',
                    type: 'GET',
                    data: { branch_id: branchId },
                    success: function (res) {

                        // centers
                        let center = $('#centerFilter');
                        center.empty().append('<option value="">All</option>');
                        $.each(res.centers, function (i, c) {
                            center.append(`<option value="${c.idCenter}">${c.No} - ${c.Name}</option>`);
                        });

                        // routes
                        let route = $('#routeFilter');
                        route.empty().append('<option value="">All</option>');
                        $.each(res.routes, function (i, r) {
                            // be safe with keys
                            let rid = r.id_route ?? r.idRoute ?? '';
                            let rname = r.route_name ?? r.Name ?? r.name ?? '';
                            route.append(`<option value="${rid}">${rname}</option>`);
                        });

                        // groups
                        let group = $('#groupFilter');
                        group.empty().append('<option value="">All Groups</option>');
                        $.each(res.groups, function (i, g) {
                            group.append(`<option value="${g.group_name}">${g.group_name}</option>`);
                        }); 

                        center.trigger('change.select2');
                        route.trigger('change.select2');
                        group.trigger('change.select2');
                    }
                });
            });
        });
    </script>
@endsection

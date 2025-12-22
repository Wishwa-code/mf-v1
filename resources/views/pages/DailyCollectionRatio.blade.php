@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .bg-purple { background-color: #1A2942 !important; color: #fff !important; }
        .bg-purple th { color: #fff !important; }

        .form-label { font-weight: 700; }
        .card { border-radius: 12px; }
        .summary-card { border: 1px solid #eef1f5; box-shadow: 0 6px 18px rgba(0,0,0,0.04); }

        /* make table more compact */
        table.dataTable tbody td { padding: 6px 10px; vertical-align: middle; }
        table.dataTable thead th { white-space: nowrap; }
    </style>
@endsection

@section('content')
    @php
        $selectedBranch = request('branch') ?? session('branch_id');
    @endphp

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h4 class="page-title mb-0">Daily Collection Ratio (Today)</h4>
                    </div>

                    {{-- ✅ Summary Cards --}}
                    <div class="row g-3 my-3">
                        <div class="col-md-4">
                            <div class="card summary-card">
                                <div class="card-body">
                                    <div class="fw-bold text-muted">Total Collection (Today)</div>
                                    <div class="fs-3 fw-bold text-success">
                                        {{ number_format($totalCollection ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card summary-card">
                                <div class="card-body">
                                    <div class="fw-bold text-muted">Collectable (Today)</div>
                                    <div class="fs-3 fw-bold text-primary">
                                        {{ number_format($totalCollectable ?? 0, 2) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php
                            $totalRatioSum = 0;
                            $rowCount = 0;

//                            foreach ($loan as $row) {
//                                if (($row->Installment_Amount ?? 0) > 0) {
//                                    $rowRatio = ($row->paid_today / $row->Installment_Amount) * 100;
//                                    $rowRatio = min($rowRatio, 100); // cap at 100
//                                    $totalRatioSum += $rowRatio;
//                                    $rowCount++;
//                                }
//                            }

                            $averageRatio = $rowCount > 0 ? ($totalRatioSum / $rowCount) : 0;
                        @endphp

                        <div class="col-md-4">
                            <div class="card summary-card">
                                <div class="card-body">
                                    <div class="fw-bold text-muted">Ratio (Collection ÷ Collectable)</div>
                                    <div class="fs-3 fw-bold text-danger">
                                        {{ number_format($averageRatio, 2) }}%
                                    </div>
                                    <div class="small text-muted">
                                        Average of today’s collection ratios (capped at 100%)
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- ✅ Filters --}}
                    <div class="row mb-4">
                        <form action="{{ route('report.dailycollectionratio') }}" method="get" class="row g-3 w-100">
                            @csrf

                            {{-- Branch --}}
                            <div class="col-md-3">
                                <label class="form-label">Filter by Branch</label>
                                <select class="form-control select2" id="branch" name="branch" {{ session('branch_access') == 0 ? 'disabled' : '' }}>
                                    <option value="">All</option>
                                    @foreach($branch as $b)
                                        <option value="{{ $b->branch_id }}" {{ (string)$selectedBranch === (string)$b->branch_id ? 'selected' : '' }}>
                                            {{ $b->Name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(session('branch_access') == 0)
                                    <input type="hidden" name="branch" value="{{ session('branch_id') }}">
                                @endif
                            </div>

                            {{-- Center --}}
                            <div class="col-md-3">
                                <label class="form-label">Filter by Center</label>
                                <select id="centerFilter" name="center_id" class="form-control select2">
                                    <option value="">All</option>
                                    @foreach($centers as $c)
                                        <option value="{{ $c->idCenter }}" {{ request('center_id') == $c->idCenter ? 'selected' : '' }}>
                                            {{ $c->No }} - {{ $c->Name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Route --}}
                            <div class="col-md-3">
                                <label class="form-label">Filter by Route</label>
                                <select id="routeFilter" name="route_id" class="form-control select2">
                                    <option value="">All</option>
                                    @foreach($routes as $r)
                                        <option value="{{ $r->id_route }}" {{ request('route_id') == $r->id_route ? 'selected' : '' }}>
                                            {{ $r->Name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Group --}}
                            <div class="col-md-3" hidden>
                                <label class="form-label">Filter by Group</label>
                                <select id="groupFilter" name="group_name" class="form-control select2">
                                    <option value="">All Groups</option>
                                    @foreach($groups as $g)
                                        <option value="{{ $g->group_name }}" {{ request('group_name') == $g->group_name ? 'selected' : '' }}>
                                            {{ $g->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Search --}}
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ✅ Table --}}
                    <table id="customerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                        <thead class="bg-purple">
                        <tr>
                            <th>No</th>
                            <th>Branch</th>
                            <th>Route</th>
                            <th>Center</th>
                            <th>Group</th>

                            <th>Client Name</th>
                            <th>Loan Number</th>
                            <th>Customer No</th>
                            <th>NIC</th>
                            <th>Phone</th>

                            <th>Installment Amount</th>
{{--                            <th>Today Paid Amount</th>--}}
                            <th>Today Due Balance</th>
                            <th>Collection Ratio (%)</th>

{{--                            <th>Arrears Reason</th>--}}
{{--                            <th>Arrears Balance</th>--}}
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($loan as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $row->branch_name }}</td>
                                <td>{{ $row->route_name }}</td>
                                <td>{{ $row->center_no }} - {{ $row->center_name }}</td>
                                <td>{{ $row->group_name }}</td>

                                <td>{{ $row->First_Name }} {{ $row->Last_Name }}</td>
                                <td>{{ $row->Loan_No }}</td>
                                <td>{{ $row->cus_number }}</td>
                                <td>{{ $row->Nic }}</td>
                                <td>{{ $row->Contact_No }}</td>

                                <td class="text-end">{{ number_format($row->Installment_Amount ?? 0, 2) }}</td>
{{--                                <td class="text-end fw-bold text-success">{{ number_format($row->paid_today ?? 0, 2) }}</td>--}}
                                <td class="text-end fw-bold text-primary">{{ number_format($row->today_due_balance ?? 0, 2) }}</td>
                                @php
                                    $ratio = 0;
//                                    if (($row->Installment_Amount ?? 0) > 0) {
//                                        $ratio = ($row->paid_today / $row->Installment_Amount) * 100;
//                                        if ($ratio > 100) {
//                                            $ratio = 100;
//                                        }
//                                    }
                                @endphp

                                <td>{{ number_format($ratio, 2) }}</td>

{{--                                <td>--}}
{{--                                    @if(($row->arrears_balance ?? 0) > 0)--}}
{{--                                        <span class="badge bg-danger">Overdue</span>--}}
{{--                                        <span class="ms-1">{{ $row->arrears_reason }}</span>--}}
{{--                                    @else--}}
{{--                                        <span class="badge bg-success">OK</span>--}}
{{--                                        <span class="ms-1">-</span>--}}
{{--                                    @endif--}}
{{--                                </td>--}}

{{--                                <td class="text-end">{{ number_format($row->arrears_balance ?? 0, 2) }}</td>--}}

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

    <!-- Select2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2();

            $('#customerTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,

                pageLength: 20,                 // ✅ default rows per page
                lengthMenu: [10, 20, 50, 100],  // ✅ dropdown options (optional)

                buttons: [
                    { extend: 'copy',  text: '<i class="bi bi-clipboard"></i> Copy',  className: 'btn btn-secondary' },
                    { extend: 'csv',   text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV', className: 'btn btn-success' },
                    { extend: 'excel', text: '<i class="bi bi-file-earmark-excel"></i> Excel', className: 'btn btn-primary' },
                    { extend: 'pdf',   text: '<i class="bi bi-file-earmark-pdf"></i> PDF', className: 'btn btn-danger' },
                    { extend: 'print', text: '<i class="bi bi-printer"></i> Print', className: 'btn btn-info' },
                ]
            });

            // ✅ Branch change => load centers + routes + groups
            $('#branch').on('change', function () {
                let branchId = $(this).val();

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
                            route.append(`<option value="${r.id_route}">${r.Name}</option>`);
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

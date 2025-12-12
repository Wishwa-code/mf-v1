@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .style-tr > td {
            padding: 2px 15px;
        }
        .form-label {
            font-weight: bold;
        }
        .form-control {
            border-radius: 0.25rem;
        }
        .card-body {
            padding: 1.5rem;
        }
        .card {
            border-radius: 0.5rem;
        }
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
        }
        .bg-purple th {
            color: #e1e1e1 !important;
        }
        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }
    </style>
@endsection

@section('content')
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="page-title">Penalty Deduction Loans Report</h4>
                    </div>
                    <span style="color: #a19595">
                        "This report lists all loans where penalties have been deducted.
                        You can review the total penalty deducted per loan and use the view button
                        to see each deduction transaction in detail."
                    </span>
                    <br><br>

                    <!-- Filter Row -->
                    <div class="row mb-4">
                        <form action="{{ route('report.penaltyDeduction') }}" method="get" class="d-flex flex-wrap w-100">
                            @csrf

                            <!-- Loan Number Filter -->
                            <div class="col-md-3 mb-2 pe-2">
                                <label for="loan_no" class="form-label">Loan Number</label>
                                <input type="text"
                                       id="loan_no"
                                       name="loan_no"
                                       value="{{ old('loan_no', $loanNo ?? '') }}"
                                       class="form-control"
                                       placeholder="Enter Loan No">
                            </div>

                            <!-- Route Filter -->
                            <div class="col-md-3 mb-2 pe-2">
                                <label for="routeFilter" class="form-label">Route</label>
                                <select id="routeFilter" name="route_id" class="form-control select2">
                                    <option value="">All Routes</option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->id_route }}"
                                                {{ (isset($routeId) && $routeId == $route->id_route) ? 'selected' : '' }}>
                                            {{ $route->Name ?? $route->route_name ?? ('Route ' . $route->id_route) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Center Filter -->
                            <div class="col-md-3 mb-2 pe-2">
                                <label for="centerFilter" class="form-label">Center</label>
                                <select id="centerFilter" name="center_id" class="form-control select2">
                                    <option value="">All Centers</option>
                                    @foreach($centers as $center)
                                        <option value="{{ $center->idCenter }}"
                                                {{ (isset($centerId) && $centerId == $center->idCenter) ? 'selected' : '' }}>
                                            {{ $center->No }} - {{ $center->Name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Search Button -->
                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-danger w-100">Search</button>
                            </div>
                        </form>
                    </div>

                    <!-- DataTable -->
                    <table id="penaltyTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                        <thead class="bg-purple">
                        <tr>
                            <th>Loan No</th>
                            <th>Route</th>
                            <th>Center</th>
                            <th>Group</th>
                            <th>Customer Name</th>
                            <th>Customer No</th>
                            <th>NIC</th>
                            <th>Contact No</th>
                            <th>Product</th>
                            <th>Issue Date</th>
                            <th>Loan Amount</th>

                            <th>Total Penalty Deducted</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($loanPenalty as $index => $item)
                            <tr>
                                <td>{{ $item->Loan_No }}</td>
                                <td>{{ $item->route_name ?? '-' }}</td>
                                <td>{{ $item->center_name ?? '-' }}</td>
                                <td>{{ $item->group_name ?? '-' }}</td>
                                <td>{{ $item->First_Name }} {{ $item->Last_Name }}</td>
                                <td>{{ $item->cus_number }}</td>
                                <td>{{ $item->Nic }}</td>
                                <td>{{ $item->Contact_No }}</td>
                                <td>{{ $item->loan_name }}</td>
                                <td>{{ $item->Date_Time }}</td>
                                <td>{{ number_format($item->Amount, 2) }}</td>
                                <td>{{ number_format($item->total_penalty_deduct ?? 0, 2) }}</td>

                                @if($item->Status == 1)
                                    <td class="text-center"><span class="badge bg-primary">Completed</span></td>
                                @elseif($item->Status == 0)
                                    <td class="text-center"><span class="badge bg-danger">Ongoing</span></td>
                                @elseif($item->Status == -2)
                                    <td class="text-center"><span class="badge bg-warning">Deleted</span></td>
                                @else
                                    <td class="text-center"><span class="badge bg-secondary">Unknown</span></td>
                                @endif

                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-warning btn-sm view-penalty"
                                            data-loan-id="{{ $item->idCustomer_Loan }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->

    <!-- Penalty Deduction Details Modal -->
    <div class="modal fade" id="penaltyModal" tabindex="-1" aria-labelledby="penaltyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-purple">
                    <h5 class="modal-title" id="penaltyModalLabel">Penalty Deduction Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <div id="penalty-loan-header" class="mb-3">
                        <!-- loan basic info injected via JS -->
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="penaltyDetailsTable">
                            <thead class="bg-purple">
                            <tr>
                                <th>#</th>
                                <th>Date & Time</th>
                                <th>Deduct Amount</th>
                                <th>Deducted By</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>

                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Select2 JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- DataTables JavaScript -->
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

            $('#penaltyTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard"></i> Copy',
                        className: 'btn btn-secondary',
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                        className: 'btn btn-success',
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-primary',
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn btn-danger',
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print',
                        className: 'btn btn-info',
                    }
                ]
            });

            // View button click – load penalty details via AJAX
            $(document).on('click', '.view-penalty', function () {
                const loanId = $(this).data('loan-id');

                $.ajax({
                    url: '{{ url("/report/penalty-deduction/details") }}/' + loanId,
                    type: 'GET',
                    success: function (response) {
                        const loan = response.loan;
                        const logs = response.logs;

                        // Header info
                        let headerHtml = '';
                        if (loan) {
                            headerHtml = `
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Loan No:</strong> ${loan.Loan_No}<br>
                                        <strong>Customer:</strong> ${loan.First_Name} ${loan.Last_Name}<br>
                                        <strong>Customer No:</strong> ${loan.cus_number}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Product:</strong> ${loan.loan_name ?? ''}<br>
                                        <strong>Issue Date:</strong> ${loan.Date_Time}<br>
                                        <strong>Agreed Amount:</strong> ${parseFloat(loan.Amount).toFixed(2)}
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Outstanding:</strong> ${parseFloat(loan.Balance_Amount ?? 0).toFixed(2)}
                                    </div>
                                </div>
                            `;
                        }
                        $('#penalty-loan-header').html(headerHtml);

                        // Table rows
                        const tbody = $('#penaltyDetailsTable tbody');
                        tbody.empty();

                        if (logs && logs.length > 0) {
                            logs.forEach((log, index) => {
                                tbody.append(`
    <tr>
        <td>${index + 1}</td>
        <td>${log.Date_Time}</td>
        <td>${parseFloat(log.Amount ?? 0).toFixed(2)}</td>
        <td>${log.deducted_user ?? 'N/A'}</td>
    </tr>
`);


                            });
                        } else {
                            tbody.append('<tr><td colspan="13" class="text-center">No penalty deduction records found.</td></tr>');
                        }

                        // Show modal
                        const penaltyModal = new bootstrap.Modal(document.getElementById('penaltyModal'));
                        penaltyModal.show();
                    }
                });
            });

        });
    </script>
@endsection

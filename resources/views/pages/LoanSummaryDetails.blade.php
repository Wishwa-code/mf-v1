@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <!-- DataTable CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .style-tr>td {
            padding: 2px 15px;
        }
    </style>

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
            color: #e1e1e1 !important; /* Ensure white text color for th elements */
        }

        .bg-purple {
            background-color: #1A2942 !important; /* Purple color */
            color: white !important; /* White text color */
        }


    </style>
@endsection

@section('content')


    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="page-title">Loan Summary Report Overview</h4>
                    </div>
                    <span style="color: #a19595">"This report provides a detailed view of your loan portfolio, with options to filter by branch, center, and group. It includes key loan information such as customer details, loan amounts, outstanding balances, loan status, arrears, and payment history. Use this report to track loan performance, overdue amounts, and identify any risk areas for better portfolio management."</span>
                    <br><br>

                    <!-- Center Filter Dropdown -->
                    <div class="row mb-4">
                        <form action="{{ route('report.loansummary') }}" method="get" class="d-flex flex-wrap w-100">
                            @csrf
                            @php
                                $selectedBranch = request('branch') ?? session('branch_id');
                            @endphp

                                    <!-- Branch Filter -->
                            <div class="col-md-3 mb-2 pe-2">
                                <label for="branch" class="form-label">Filter by Branch</label>
                                <select class="form-control select2" id="branch" name="branch" {{ session('branch_access') == 0 ? 'disabled' : '' }}>
                                    <option value="">All</option>
                                    @foreach($branch as $item)
                                        <option value="{{ $item->branch_id }}" {{ $selectedBranch == $item->branch_id ? 'selected' : '' }}>
                                            {{ $item->Name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(session('branch_access') == 0)
                                    <input type="hidden" name="branch" value="{{ session('branch_id') }}">
                                @endif
                            </div>

                            <!-- Center Filter -->
                            <div class="col-md-3 mb-2 pe-2">
                                <label for="centerFilter" class="form-label">Filter by Center</label>
                                <select id="centerFilter" name="center_id" class="form-control select2">
                                    <option value="">All</option>
                                    @foreach($centers as $center)
                                        <option value="{{ $center->idCenter }}" {{ request('center_id') == $center->idCenter ? 'selected' : '' }}>
                                            {{ $center->No }} - {{ $center->Name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Group Filter -->
                            <div class="col-md-3 mb-2 pe-2">
                                <label for="groupFilter" class="form-label">Filter by Group</label>
                                <select id="groupFilter" name="group_name" class="form-control select2">
                                    <option value="">All Groups</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->group_name }}" {{ request('group_name') == $group->group_name ? 'selected' : '' }}>
                                            {{ $group->group_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Loan Status Filter -->
                            <div class="col-md-3 mb-2 pe-2">
                                <label for="loan_status" class="form-label">Loan Type</label>
                                <select id="loan_status" name="loan_status" class="form-control select2">
                                    <option value="">All Loans</option>
                                    <option value="1" {{ request('loan_status') == 'without_settlement' ? 'selected' : '' }}>
                                        Without Settlement
                                    </option>
                                </select>
                            </div>


                            <!-- Search Button -->
                            <div class="col-md-3 mb-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-danger w-100">Search</button>
                            </div>
                        </form>
                    </div>






                    <!-- DataTable -->
                    <table id="customerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                        <thead class="bg-purple">
                        <tr>
                            <th>No</th>
                            <th>Branch</th>
                            <th>Credit Officer</th>
                            <th>Center</th>
                            <th>Group</th>
                            <th>Customer Name</th>
                            <th>Customer No</th>
                            <th>Customer Nic</th>
                            <th>Contact No</th>
                            <th>Product</th>
                            <th>Loan Number</th>
                            <th>Loan Status</th>
                            <th>Issue Date</th>
                            <th>Loan Amount</th>
                            <th>Agreed Amount</th>
                            <th>Portfolio</th>
                            <th>Interest Balance</th>
                            <th>Panelty</th>
                            <th>Outstanding</th>
                            <th>Rental</th>
                            <th>Arrears</th>
                            <th>Total Over Paid</th>
                            <th>Age</th>
                            <th>Period Over Date</th>
                            <th>Period Over Days</th>
                            <th>Last Payment Date</th>
                            <th>Last Payment Amount</th>
                            <th>Last Due Date</th>
                            <th>Next Due Date</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($loan as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->branch_name }}</td>
                                <td>{{ $item->LendingOfficer }}</td>
                                <td>{{ $item->center_name }}</td>
                                <td>{{ $item->group_name }}</td>
                                <td>{{ $item->First_Name }} {{ $item->Last_Name }}</td>
                                <td>{{ $item->cus_number }}</td>
                                <td>{{ $item->Nic }}</td>
                                <td>{{ $item->Contact_No }}</td>
                                <td>{{ $item->loan_name }}</td>
                                <td>{{ $item->Loan_No }}</td>
                                @if($item->Status == "1")
                                    <td class="text-center"><span class="badge bg-primary">Completed</span></td>
                                @elseif($item->Status == "0")
                                    <td class="text-center"><span class="badge bg-danger">Ongoing</span></td>
                                @elseif($item->Status == "-2")
                                    <td class="text-center"><span class="badge bg-warning">Deleted</span></td>
                                @endif
                                <td>{{ $item->Date_Time }}</td>
                                <td>{{ number_format($item->Amount,2) }}</td>
                                <td>{{ number_format($item->Total_Loan_Amount,2) }}</td>
                                @php
                                    $arrease = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('Installment_Date', '<=', date('Y-m-d'))
                                        ->where('branch_id', session('branch_id'))
                                        ->sum('Total_Balance');
                                    $panelty = DB::table('installments')
                                            ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                            ->where('branch_id', session('branch_id'))
                                            ->sum('Panalty_Balance');
                                @endphp
                                <td>{{ number_format($item->capital_balance,2) }}</td>
                                <td>{{ number_format($item->installment_balance,2) }}</td>
                                <td>{{ number_format($panelty,2) }}</td>
                                <td>{{ number_format(($panelty ?? 0) + ($item->Balance_Amount ?? 0), 2) }}</td>
                                <td>{{ number_format($item->Installment_Amount,2) }}</td>
                                @php
                                    $arrease = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('Installment_Date', '<', date('Y-m-d'))
                                        ->where('branch_id', session('branch_id'))
                                        ->sum('Total_Balance');
                                @endphp
                                <td>{{ number_format($arrease, 2) }}</td>

                                @php
                                    $overpaid = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('Installment_Date', '>', date('Y-m-d'))
                                        ->where('branch_id', session('branch_id'))
                                        ->sum('Paid_Amount');
                                @endphp
                                <td>{{ number_format($overpaid, 2) }}</td>
                                @php
                                    $age = date_diff(new DateTime(date('Y-m-d')), new DateTime($item->Date_Time)); // Calculate the age in years
                                $age=$age->days;
                                @endphp
                                <td>{{ $age }} Days</td>
                                @php
                                    $maturityDate = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('branch_id', session('branch_id'))
                                        ->orderBy('Installment_Date', 'desc')
                                        ->value('Installment_Date'); // Get the last installment date directly
                                @endphp

                                <td>{{ $maturityDate }}</td>
                                @php
                                    $currentDate = date('Y-m-d'); // Today's date
                                    $dateDiff = date_diff(new DateTime($currentDate), new DateTime($maturityDate)); // Calculate the difference
                                    $diffDays = $dateDiff->days; // Get the total number of days
                                    $isPast = $dateDiff->invert; // Check if the maturity date is in the past (1 if true, 0 if false)

                                    // If the date difference is negative, make it negative
                                    $diffDays = $isPast ? -$diffDays : $diffDays;
                                @endphp
                                <td>{{ $diffDays }} Days</td>

                                @php
                                    $lastPayment = DB::table('customer_payments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('branch_id', session('branch_id'))
                                        ->orderBy('Date', 'desc')
                                        ->first(['Date', 'Amount']); // Get the last payment date and amount
                                @endphp

                                <td>{{ $lastPayment->Date ?? 'N/A' }}</td>
                                <td>{{ isset($lastPayment) ? number_format($lastPayment->Amount, 2) : 'N/A' }}</td>

                                @php
                                    $lastDueDate = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('Installment_Date', '<', date('Y-m-d'))
                                        ->where('branch_id', session('branch_id'))
                                        ->orderBy('Installment_Date', 'desc') // Order by date descending
                                        ->value('Installment_Date'); // Get the last due date
                                    $nextDueDate = DB::table('installments')
                                        ->where('Customer_Loan_idCustomer_Loan', $item->idCustomer_Loan)
                                        ->where('branch_id', session('branch_id'))
                                        ->where('Installment_Date', '>', date('Y-m-d'))
                                        ->orderBy('Installment_Date', 'asc') // Order by date descending
                                        ->value('Installment_Date'); // Get the last due date
                                @endphp

                                <td>{{ $lastDueDate ?? 'N/A' }}</td> <!-- Display the last due date or 'N/A' if not found -->
                                <td>{{ $nextDueDate ?? 'N/A' }}</td> <!-- Display the last due date or 'N/A' if not found -->
                                <td><a href="/loanview/{{$item->idCustomer_Loan}}" target="_blank" class="btn btn-warning"><i class="bi bi-eye"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->

@endsection

@section('script')
    <script src="../assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="../assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="../assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/group.js"></script>
    <script src="../JS/customer.js"></script>
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

            // Initialize Select2 Elements
            $('.select2').select2();

            // Initialize DataTable
            $('#customerTable').DataTable({
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
        });


    </script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();

            $('#branch').on('change', function () {
                let branchId = $(this).val();

                if (branchId !== '') {
                    $.ajax({
                        url: '{{ route("ajax.centers.groups") }}',
                        type: 'GET',
                        data: { branch_id: branchId },
                        success: function (response) {
                            // Clear and update centers
                            let centerDropdown = $('#centerFilter');
                            centerDropdown.empty().append('<option value="">All</option>');
                            $.each(response.centers, function (index, center) {
                                centerDropdown.append(`<option value="${center.idCenter}">${center.No} - ${center.Name}</option>`);
                            });

                            // Clear and update groups
                            let groupDropdown = $('#groupFilter');
                            groupDropdown.empty().append('<option value="">All Groups</option>');
                            $.each(response.groups, function (index, group) {
                                groupDropdown.append(`<option value="${group.group_name}">${group.group_name}</option>`);
                            });

                            // Refresh select2
                            centerDropdown.trigger('change.select2');
                            groupDropdown.trigger('change.select2');
                        }
                    });
                } else {
                    // If "All" is selected, you might want to clear the dependent dropdowns
                    $('#centerFilter').empty().append('<option value="">All</option>').trigger('change.select2');
                    $('#groupFilter').empty().append('<option value="">All Groups</option>').trigger('change.select2');
                }
            });
        });
    </script>

@endsection


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
        .table {
            width: 100%;
        }

        .card-body {
            padding: 0;
        }

        .card-body {
            padding: 0;
        }

        .table {
            width: 100%;
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

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Full Loan Detail Report Overview</h4>
            </div>
        </div>
        <span style="color: #a19595">"This report provides a comprehensive breakdown of all loans within a specified period. It includes detailed information about each loan, such as loan terms, repayment status, client details, and financial metrics. The report is highly customizable, allowing users to filter data by date range, collector, route, and loan status."</span>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <!-- Date Range Filter Form -->
            <form method="GET" action="{{ route('fullLoanDetailReport') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date_from">Date From</label>
                        <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_to">Date To</label>
                        <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="simpleinput" class="form-label">Collector</label>
                        <select class="form-control select2" id="collector_id" name="collector_id">
                            <option value="0" {{ $collector_id == 0 ? 'selected' : '' }}>All</option>
                            @foreach($collector as $item)
                                <option value="{{ $item->id }}" {{ $collector_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->Full_Name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="simpleinput" class="form-label">Route</label>
                        <select class="form-control select2" id="route_id" name="route_id">
                            <option value="0" {{ $route_id == 0 ? 'selected' : '' }}>All</option>
                            @foreach($route as $item)
                                <option value="{{ $item->id_route }}" {{ $route_id == $item->id_route ? 'selected' : '' }}>
                                    {{$item->root_code}}-{{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="loan_status" class="form-label">Loan Status</label>
                        <select class="form-control select2" id="loan_status" name="loan_status[]" multiple>
                            <option value="-1" {{ in_array(-1, (array) $loan_status) ? 'selected' : '' }}>Pending</option>
                            <option value="0" {{ in_array(0, (array) $loan_status) ? 'selected' : '' }}>Ongoing</option>
                            <option value="1" {{ in_array(1, (array) $loan_status) ? 'selected' : '' }}>Settled</option>
                            <option value="-2" {{ in_array(-2, (array) $loan_status) ? 'selected' : '' }}>Deleted</option>
                        </select>
                    </div>


                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- DataTable Wrapper -->
                    <div style="overflow-y: auto; height: auto">
                        <table id="customerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                            <thead class="sticky-top bg-purple">
                            <tr>
                                <th>Loan Number</th>
                                <th>Product Name</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Interest Rate</th>
                                <th>Penalty Rate</th>
                                <th>Installment Count</th>
                                <th>Interest Amount</th>
                                <th>Total Other Loan Charges</th>
                                <th>Total Loan Amount</th>
                                <th>Installment Amount</th>
                                <th>Collection Type</th>
                                <th>Total Paid Amount</th>
                                <th>Total Balance</th>
                                <th>Capital Balance</th>
                                <th>Loan Maturity Date</th>
                                <th>Loan Status</th>
                                <th>Customer No</th>
                                <th>Title</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Contact Number</th>
                                <th>Contact Number 2</th>
                                <th>NIC</th>
                                <th>Gender</th>
                                <th>DOB</th>
                                <th>Risk Level</th>
                                <th>Address</th>
                                <th>Address 2</th>
                                <th>Address 3</th>
                                <th>Permanent Address 1</th>
                                <th>Permanent Address 2</th>
                                <th>Permanent Address 3</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Landline</th>
                                <th>Note</th>
                                <th>Guardian Title</th>
                                <th>Guardian Name</th>
                                <th>Guardian Gender</th>
                                <th>Guardian Relation</th>
                                <th>Guardian Occupation</th>
                                <th>Guardian Contact</th>
                                <th>Guardian Address</th>
                                <th>Guardian NIC</th>
                                <th>Customer Photo</th>
                                <th>Customer Status</th>
                                <th>Civil Status</th>
                                <th>Job Position</th>
                                <th>Monthly Salary</th>
                                <th>Occupation Address 1</th>
                                <th>Occupation Address 2</th>
                                <th>Occupation Address 3</th>
                                <th>Occupation Contact No</th>
                                @for ($i = 1; $i <= 5; $i++)
                                    <th>Guarantor {{ $i }} Name</th>
                                    <th>Guarantor {{ $i }} Contact Number</th>
                                    <th>Guarantor {{ $i }} NIC</th>
                                    <th>Guarantor {{ $i }} Address</th>
                                @endfor
                                <th>Lending Officer</th>
                                <th>Collecting Officer</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($loan as $loanDetail)
                                <tr>
                                    <td>{{ $loanDetail->Loan_No }}</td>
                                    <td>{{ $loanDetail->Product_Name }}</td>
                                    <td>{{ $loanDetail->Date_Time }}</td>
                                    <td>{{ number_format($loanDetail->Amount, 2) }}</td>
                                    <td>{{ number_format($loanDetail->Interest_Rate, 2) }}</td>
                                    <td>{{ number_format($loanDetail->Panalty_Rate, 2) }}</td>
                                    <td>{{ $loanDetail->Installment_Count }}</td>
                                    <td>{{ number_format($loanDetail->Interest_Amount, 2) }}</td>
                                    <td>{{ number_format($loanDetail->Total_Other_Amount, 2) }}</td>
                                    <td>{{ number_format($loanDetail->Total_Loan_Amount, 2) }}</td>
                                    <td>{{ number_format($loanDetail->Installment_Amount, 2) }}</td>
                                    <td>{{ $loanDetail->Collection_Type }}</td>
                                    <td>{{ number_format($loanDetail->total_paid_amount, 2) }}</td>
                                    <td>{{ number_format($loanDetail->Balance_Amount, 2) }}</td>
                                    <td>{{ number_format($loanDetail->capital_balance, 2) }}</td>
                                    <td>{{ $loanDetail->Loan_Maturity_Date }}</td>
                                    <td>
                                        @if ($loanDetail->Status == 'o')
                                            Closed
                                        @else
                                            Active
                                        @endif
                                    </td>
                                    <td>{{ $loanDetail->Customer_No }}</td>
                                    <td>{{ $loanDetail->Title }}</td>
                                    <td>{{ $loanDetail->First_Name }}</td>
                                    <td>{{ $loanDetail->Last_Name }}</td>
                                    <td>{{ $loanDetail->Email }}</td>
                                    <td>{{ $loanDetail->Contact_No }}</td>
                                    <td>{{ $loanDetail->contact_number_2 }}</td>
                                    <td>{{ $loanDetail->Nic }}</td>
                                    <td>{{ $loanDetail->Gender }}</td>
                                    <td>{{ $loanDetail->Dob }}</td>
                                    <td>{{ $loanDetail->Customer_Risk_Level }}</td>
                                    <td>{{ $loanDetail->Address }}</td>
                                    <td>{{ $loanDetail->Address_02 }}</td>
                                    <td>{{ $loanDetail->Address_03 }}</td>
                                    <td>{{ $loanDetail->Per_Address_01 }}</td>
                                    <td>{{ $loanDetail->Per_Address_02 }}</td>
                                    <td>{{ $loanDetail->Per_Address_03 }}</td>
                                    <td>{{ $loanDetail->City }}</td>
                                    <td>{{ $loanDetail->State }}</td>
                                    <td>{{ $loanDetail->Landline }}</td>
                                    <td>{{ $loanDetail->Note }}</td>
                                    <td>{{ $loanDetail->Gua_title }}</td>
                                    <td>{{ $loanDetail->Gua_name }}</td>
                                    <td>{{ $loanDetail->Guardian_gender }}</td>
                                    <td>{{ $loanDetail->Gua_relation }}</td>
                                    <td>{{ $loanDetail->Gua_occu }}</td>
                                    <td>{{ $loanDetail->Gua_contact }}</td>
                                    <td>{{ $loanDetail->Gua_address }}</td>
                                    <td>{{ $loanDetail->Gua_nic }}</td>
                                    <td>{{ $loanDetail->Cus_phto }}</td>
                                    <td>{{ $loanDetail->Customer_Status }}</td>
                                    <td>{{ $loanDetail->civil_status }}</td>
                                    <td>{{ $loanDetail->occu_job_position }}</td>
                                    <td>{{ $loanDetail->occu_monthly_salary }}</td>
                                    <td>{{ $loanDetail->occu_address_01 }}</td>
                                    <td>{{ $loanDetail->occu_address_02 }}</td>
                                    <td>{{ $loanDetail->occu_address_03 }}</td>
                                    <td>{{ $loanDetail->occu_contact_no }}</td>
                                    @php
                                        $guarantors = $loanDetail->guarantors;
                                        $guarantorCount = count($guarantors);
                                    @endphp

                                    @for ($i = 0; $i < 5; $i++)
                                        @if ($i < $guarantorCount)
                                            <td>{{ $guarantors[$i]->First_Name }} {{ $guarantors[$i]->Last_Name }}</td>
                                            <td>{{ $guarantors[$i]->Contact_No }}</td>
                                            <td>{{ $guarantors[$i]->Nic }}</td>
                                            <td>{{ $guarantors[$i]->Address }}</td>
                                        @else
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>
                                        @endif
                                    @endfor
                                    <td>{{ $loanDetail->lending_officer_name }}</td>
                                    <td>{{ $loanDetail->collector_officer_name }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>
                    <div class="mt-3">
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td>{{ number_format($totalAmount, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Total Loan Amount:</strong></td>
                                <td>{{ number_format($totalLoanAmount, 2) }}</td>
                            </tr>
                        </table>
                    </div>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->




@endsection

@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Replace special characters in the company name for exporting the file name correctly
            var companyName = {!! json_encode(session('company_name')) !!}.replace(/[^\w\s]/gi, '');

            // Initialize DataTable with export and print buttons
            $('#customerTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: companyName + '_Loan_Detail_Report',

                    },
                    {
                        extend: 'pdfHtml5',
                        title: companyName + '_Loan_Detail_Report',
                        orientation: 'landscape',
                        pageSize: 'A4',

                    },
                    {
                        extend: 'print',
                        title: companyName + '_Loan_Detail_Report',

                    }
                ],
                responsive: true,
                fixedHeader: true, // Fix header on scroll
                order: [[2, 'asc']], // Default ordering by date column
                pageLength: 25 // Set default page length
            });

        });

    </script>

@endsection


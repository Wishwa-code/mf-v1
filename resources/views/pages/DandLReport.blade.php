
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


        tfoot tr {
            background-color: #f0f0f0;
            font-weight: bold;
            color: #333;
        }

        tfoot th {
            border-top: 2px solid #1A2942;
            padding: 8px;
            text-align: right;
        }

    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">

                <h4 class="page-title">D & L Report</h4>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <!-- Date Range Filter Form -->
            <form method="GET" action="{{ route('dandlreport') }}">
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
                        <label for="date_from">Center</label>
                        <select class="form-control select2" id="center_id" name="center_id">
                            <option value="0">All</option>
                            @foreach($center as $item)
                                <option value="{{ $item->idCenter }}" {{ $center_id == $item->idCenter ? 'selected' : '' }}>
                                    {{ $item->No }} - {{ $item->Name }}
                                </option>
                            @endforeach
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
                                <th>Date</th>
                                <th>Collection Due</th>
                                <th>Arrease</th>
                                <th>Collected Loan Amount</th>
                                <th>Collected Document Charges</th>
                                <th>Total Collected</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $totalAmount = 0; @endphp
                            @foreach($loan as $item)
                                <tr>
                                    <td>{{ $item->Installment_Date }}</td>
                                    <td>{{ number_format($item->Total_Amount, 2) }}</td>
                                    <td>{{ number_format($item->Total_Balance, 2) }}</td>
                                    <td>{{ number_format($item->Paid_Amount, 2) }}</td>
                                    <td>{{ number_format($item->Doc_Amount, 2) }}</td>
                                    <td>{{ number_format($item->Paid_Amount+$item->Doc_Amount, 2) }}</td>
                                </tr>
                                @php $totalAmount += ($item->Paid_Amount+$item->Doc_Amount); @endphp
                            @endforeach
                            </tbody>
                            <!-- Total Row -->
                            <tfoot>
                            <tr class="bg-light">
                                <th colspan="5" class="text-right">Total Amount</th>
                                <th>{{ number_format($totalAmount, 2) }}</th>
                            </tr>
                            </tfoot>
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
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        title: companyName + '_Loan_Detail_Report',
                        orientation: 'landscape',
                        pageSize: 'A4',
                        exportOptions: {
                            columns: ':visible'
                        }
                    },
                    {
                        extend: 'print',
                        title: companyName + '_Loan_Detail_Report',
                        exportOptions: {
                            columns: ':visible'
                        }
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


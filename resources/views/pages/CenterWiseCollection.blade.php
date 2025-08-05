
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
                <h4 class="page-title">Center Wise Collection Detail Overview</h4>
            </div>
        </div>
        <span style="color: #a19595">"The Center Wise Collection Detail provides a breakdown of collections made across different centers on a specific date. It includes detailed information on the loan payments, including payment types, amounts collected, and the associated users and company account details. This helps to track the collection activities of different centers, groups, and collectors."</span>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <!-- Date Range Filter Form -->
            <form method="GET" action="{{ route('center_collection.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="date_from">Select Date</label>
                        <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
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
                        <label for="simpleinput" class="form-label">Center</label>
                        <select class="form-control select2" id="center_id" name="center_id">
                            <option value="0" {{ $center_id == 0 ? 'selected' : '' }}>All</option>
                            @foreach($center as $item)
                                <option value="{{ $item->idCenter }}" {{ $center_id == $item->idCenter ? 'selected' : '' }}>
                                    {{$item->No}}-{{ $item->Name }}
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
                                <th>Center</th>
                                <th>Group</th>
                                <th>Loan Number</th>
                                <th>Customer Name</th>
                                <th>Customer No</th>
                                <th>Payment Type</th>
                                <th>Amount</th>
                                <th>Collected User</th>
                                <th>Company Acc Type</th>
                                <th>Company Acc Num</th>
                                <th>Transaction Time</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach($sorted_merge_query as $item)
                                    <tr>
                                        <td>{{ $item->center_name }}</td>
                                        <td>{{ $item->group_name }}</td>
                                        <td>{{ $item->loan_number }}</td>
                                        <td>{{ $item->f_name }} {{$item->l_name}}</td>
                                        <td>{{ $item->cus_number }}</td>
                                        <td>{{ $item->payment_type }}</td>
                                        <td>{{ number_format($item->amount,2) }}</td>
                                        <td>{{ $item->collected_user }}</td>
                                        <td>{{ $item->type }}</td>
                                        <td>{{ $item->bank_name }}-{{$item->bank_account_number}}</td>
                                        <td>{{ $item->date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>



    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2();

            // Get selected filter values
            var selectedDate = $('#date_from').val() || 'All Dates';
            var selectedCenter = $('#center_id option:selected').text() || 'All Centers';
            var selectedCollector = $('#collector_id option:selected').text() || 'All Collectors';

            // Replace special characters in the company name for exporting the file name correctly
            var companyName = {!! json_encode(session('company_name')) !!}.replace(/[^\w\s]/gi, '');

            // Initialize DataTable with export buttons
            $('#customerTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        title: companyName + '_Center Wise Collection Detail',
                        text: 'Export to Excel',
                        className: 'btn btn-success',
                        sheetName: 'Center Wise Collection',
                        messageTop: 'Date: ' + selectedDate + '\nCenter: ' + selectedCenter + '\nCollector: ' + selectedCollector,
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // Adjust indexes as per your table structure
                        }
                    },
                    {
                        extend: 'print',
                        title: 'Center Wise Collection Detail',
                        text: 'Print Report',
                        className: 'btn btn-primary',
                        autoPrint: true,
                        messageTop: '<p>Date: ' + selectedDate + '<br>Center: ' + selectedCenter + '<br>Collector: ' + selectedCollector + '</p>',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // Adjust indexes as per your table structure
                        }
                    }
                ],
                responsive: true,
                fixedHeader: true,
                order: [[2, 'asc']],
                pageLength: 25
            });
        });

    </script>



@endsection


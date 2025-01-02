@extends('layout.admin')

@section('head')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <style>
        .style-tr > td {
            padding: 1px 10px;

        }
        #messageContent {
            white-space: pre-wrap; /* Preserves whitespace and line breaks */
        }

        table.dataTable thead th, table.dataTable thead td {
            padding: 5px 5px; /* Set padding for table header */
            font-size: 14px; /* Set font size for table header */
        }

        table.dataTable tbody th, table.dataTable tbody td {
            padding: 5px 5px; /* Set padding for table body */
            font-size: 14px; /* Set font size for table body */
        }

        .modal-body {
            font-size: 14px; /* Set font size for modal body */
        }

        .modal-title {
            font-size: 18px; /* Set font size for modal title */
        }

        .btn {
            font-size: 14px; /* Set font size for buttons */
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
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">SMS History Report</h4>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- DataTable -->
                    <table id="customerTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                        <thead class="sticky-top bg-purple">
                        <tr>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Contact Number</th>
                            <th>Message Type</th>
                            <th>Message Content</th>
                            <th>Message Count</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($smsRecords as $sms)
                            @php
                                $message = $sms->type === 'OTP' ? preg_replace('/\d{6}/', '#########', $sms->message) : $sms->message;
                                $smsLength = strlen($message);
                                $smsCount = ceil($smsLength / 160);
                            @endphp
                            <tr>
                                <td>{{ $sms->date }} {{ $sms->time }}</td>
                                <td>{{ $sms->cus_name }} ({{ $sms->cus_id }})</td>
                                <td>{{ $sms->contact_no }}</td>
                                <td>{{ $sms->type }}</td>

                                <td>
                                    @if($sms->type === 'OTP')
                                        {{ Str::limit(preg_replace('/\d{6}/', '#########', $sms->message), 50) }}
                                    @else
                                        {{ Str::limit($sms->message, 50) }}
                                    @endif
                                </td>
                                <td>{{ $smsCount }} ( {{ $smsLength }} Characters )</td>
                                <td>
                                    <a href="/loanview/{{ $sms->idCustomer_Loan }}" data-toggle="modal" data-target="#messageModal" class="btn btn-warning" data-message="{{ $sms->type === 'OTP' ? preg_replace('/\d{6}/', '#########', $sms->message) : $sms->message }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->
    </div> <!-- end row -->

    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">SMS Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p id="messageContent"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
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

    <!-- Bootstrap JavaScript -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#customerTable').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ]
            });

            // Handle view message link click
            $('.btn-warning').on('click', function() {
                var message = $(this).data('message');
                $('#messageContent').text(message);
            });
        });
    </script>

@endsection

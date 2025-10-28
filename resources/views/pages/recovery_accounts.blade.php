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
        .card-body {
            padding: 1.5rem;
        }
        .card {
            border-radius: 0.5rem;
        }
        .bg-purple th {
            color: #e1e1e1 !important;
        }
        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;
        }
        .badge-status-active {
            background-color: #28a745;
        }
        .badge-status-closed {
            background-color: #6c757d;
        }
    </style>
@endsection

@section('content')

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <h4 class="page-title">Recovery Accounts</h4>
                    </div>

                    <!-- DataTable -->
                    <table id="recoveryTable" class="display nowrap table table-striped table-bordered" style="width:100%">
                        <thead class="bg-purple">
                        <tr>
                            <th>Customer No</th>
                            <th>Customer Name</th>
                            <th>NIC</th>
                            <th>Contact Number</th>
                            <th>Current Recovery Balance</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($recoveryAccounts as $acc)
                            <tr>
                                <td>{{ $acc->cus_number }}</td>
                                <td>{{ $acc->customer_name }}</td>
                                <td>{{ $acc->Nic }}</td>
                                <td>{{ $acc->Contact_No }}</td>
                                <td>{{ number_format($acc->current_balance,2) }}</td>
                                <td>
                                    @if($acc->status === 'Active')
                                        <span class="badge badge-status-active text-white px-2 py-1">Active</span>
                                    @else
                                        <span class="badge badge-status-closed text-white px-2 py-1">Closed</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button
                                            type="button"
                                            class="btn btn-warning btn-sm"
                                            onclick="openRecoveryLogModal({{ $acc->idRecovery_Account }}, '{{ $acc->customer_name }}', '{{ $acc->cus_number }}')"
                                    >
                                        <i class="bi bi-journal-text fs-5"></i>
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


    <!-- Recovery Log Modal -->
    <div class="modal fade" id="recovery-log-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h4 class="modal-title mb-0">Recovery Account Log</h4>
                        <small id="recoveryLogSubtitle" class="text-muted d-block" style="font-size: 0.8rem;"></small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive-sm">
                        <table class="table table-centered mb-0 table-striped table-bordered" id="recovery_log_table">
                            <thead class="table-dark">
                            <tr>
                                <th>Date / Time</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Balance After</th>
                                <th>Loan ID</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- will be injected by JS -->
                            </tbody>
                        </table>
                    </div>
                </div><!-- /.modal-body -->

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

@endsection


@section('script')
    <script src="../assets/vendor/daterangepicker/moment.min.js"></script>
    <script src="../assets/vendor/daterangepicker/daterangepicker.js"></script>
    <script src="../assets/js/pages/dashboard.js"></script>
    <script src="../JS/validate.js"></script>
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
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.pdfMake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {

            // Initialize Select2 for any .select2 (if you use it in the future here)
            $('.select2').select2();

            // prepare export file name like in your savings page
            var companyName = {!! json_encode($company->company_name) !!}.replace(/&/g, ' And ');

            $('#recoveryTable').DataTable({
                dom: 'Bfrtip',
                responsive: true,
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard"></i> Copy',
                        className: 'btn btn-secondary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-spreadsheet"></i> CSV',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                        className: 'btn btn-primary',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        filename: companyName
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i> Print',
                        className: 'btn btn-info',
                        exportOptions: {
                            columns: [0, 1, 2, 3, 4, 5]
                        },
                        filename: companyName
                    }
                ]
            });
        });

        // opens modal and loads log rows via AJAX
        function openRecoveryLogModal(recoveryAccountId, customerName, cusNumber) {
            // set the subtitle line under modal title
            $('#recoveryLogSubtitle').text(`Customer: ${customerName} (${cusNumber}) | Account ID: ${recoveryAccountId}`);

            // clear existing table rows
            $('#recovery_log_table tbody').empty();

            // ajax to fetch the log data
            $.ajax({
                type: "GET",
                url: "/recovery-account/logs/" + recoveryAccountId,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200 && data.logs) {
                        data.logs.forEach(function (log) {
                            let row = `
                                <tr>
                                    <td>${log.created_at ?? ''}</td>
                                    <td>${log.action_type ?? ''}</td>
                                    <td>${log.description ?? ''}</td>
                                    <td>${parseFloat(log.amount || 0).toFixed(2)}</td>
                                    <td>${parseFloat(log.balance_after || 0).toFixed(2)}</td>
                                    <td>${log.loan_no ?? ''}</td>
                                </tr>
                            `;
                            $('#recovery_log_table tbody').append(row);
                        });
                    } else {
                        // no logs / unexpected
                        $('#recovery_log_table tbody').append(`
                            <tr>
                                <td colspan="6" class="text-center text-muted">No log entries found.</td>
                            </tr>
                        `);
                    }
                },
                error: function (xhr, status, error) {
                    $('#recovery_log_table tbody').append(`
                        <tr>
                            <td colspan="6" class="text-center text-danger">Failed to load log data.</td>
                        </tr>
                    `);
                }
            });

            // finally show the modal
            $('#recovery-log-modal').modal('show');
        }
    </script>
@endsection

@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .total-pending-container {
            background-color: #1A2942;
            padding: 5px;
            margin-top: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            color: #ffffff !important;
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

        #overlay {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .bg-purple {
            background-color: purple;
            color: white;
        }

        .custom-hover:hover {
            background-color: darkorchid;
            color: white;
        }

        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }

            .printer-design, .printer-design * {
                visibility: visible;
            }

            .printer-design {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
                background: white;
            }

            .modal-content {
                width: 80mm;
                border: none;
            }

            .receipt {
                width: 100%;
                padding: 10px;
            }

            .printer-design button {
                display: none;
            }

            .form-control {
                height: calc(5.25rem + 2px);
            }
        }
    </style>

@endsection


@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Date / Center Wise Cash Flow Details Overview</h4>
                </div>
            </div>
            <span style="color: #a19595">"The Date / Center Wise Cash Flow Details report provides a snapshot of the cash flow transactions for each center over a specific date range. It allows you to track all cash inflows and outflows related to loans and customer payments."</span>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-lg-3">
                                <label for="center_details" class="form-label">Center</label>
                                <select class="form-control select2" id="center_details">
                                    <option value="0">All</option>
                                    @foreach($center as $item)
                                        <option value="{{ $item->idCenter }}">{{ $item->Name }}
                                            - {{ $item->Route }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="date_from" class="form-label">Date From</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                       value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-lg-3">
                                <label for="date_to" class="form-label">Date To</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                       value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-lg-3 d-flex align-items-end">
                                <button type="button" class="btn btn-danger" id="search_button" onclick="load_data();">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive-sm">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead class="bg-purple">
                                <tr>
                                    <th>Center No</th>
                                    <th>Group No</th>
                                    <th>Transaction Type</th>
                                    <th>Customer Number</th>
                                    <th>Loan Number</th>
                                    <th>Customer Name</th>
                                    <th>Date</th>
                                    <th>Cash In</th>
                                    <th>Cash Out</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-1 mb-1 p-2" hidden>
                            <div
                                class="col-lg-12 d-flex align-items-center justify-content-between total-pending-container">
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold">Total Collected Amount:</span>
                                    <span id="tot_Collected_amount" class="fw-bold ms-2">0.00</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold">Total Loan Amount:</span>
                                    <span id="tot_Issued_amount" class="fw-bold ms-2">0.00</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <span class="fw-bold">Total Other Charges:</span>
                                    <span id="tot_other_Charges" class="fw-bold ms-2">0.00</span>
                                </div>
{{--                                <button class="btn btn-primary" onclick="">Print Report</button>--}}
                            </div>

                        </div>

                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div>

@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript"
            src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#loan_table').DataTable({
                destroy: true, // Allows reinitialization
                order: [[6, 'asc'], [4, 'asc']], // Order by date (column 6) and loan number (column 4)
                paging: true,
                searching: true,
                responsive: true,
                pageLength: 6, // Set number of rows per page to 6
                dom: 'Bfrtip', // Add Buttons control to the DataTable
                buttons: [
                    {
                        extend: 'copy',
                        title: 'Date / Center wise cash flow details',
                        filename:  {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ') + " Cash Flow Detail Report"
                    },
                    {
                        extend: 'csv',
                        title: 'Date / Center wise cash flow details',
                        filename:  {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ') + " Cash Flow Detail Report"
                    },
                    {
                        extend: 'excel',
                        title: 'Date / Center wise cash flow details',
                        filename:  {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ') + " Cash Flow Detail Report"
                    },
                    {
                        extend: 'pdf',
                        title: 'Date / Center wise cash flow details',
                        filename:  {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ') + " Cash Flow Detail Report"
                    },
                    {
                        extend: 'print',
                        title: 'Date / Center wise cash flow details',
                        filename:  {!! json_encode(session('company_name')) !!}.replace(/&/g, ' And ') + " Cash Flow Detail Report"
                    }
                ],
                ajax: {
                    type: "POST",
                    url: "/cashflow/datewise",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: function(d) {
                        // Append additional data to the request
                        d.center_details = $("#center_details").val();
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                    },
                    dataSrc: function(json) {
                        // Calculate totals before returning data
                        calculateTotals(json.items);
                        return json.items;
                    }
                },
                columns: [
                    { data: 'center_name' },
                    { data: 'group_name' },
                    { data: 'transaction_type' },
                    { data: 'customer_number' },
                    { data: 'loan_number' },
                    { data: 'customer_name' },
                    { data: 'date' },
                    {
                        data: 'amount',
                        render: function(data, type, row) {
                            return row.transaction_type === 'Loan Issue' ? '0.00' : $.fn.dataTable.render.number(',', '.', 2).display(data);
                        }
                    },
                    {
                        data: 'amount',
                        render: function(data, type, row) {
                            return row.transaction_type === 'Loan Issue' ? $.fn.dataTable.render.number(',', '.', 2).display(data) : '0.00';
                        }
                    }
                ]
            });

            // Search button functionality
            $('#search_button').on('click', function() {
                table.ajax.reload(); // Reload data when search button is clicked
            });
        });

        // Function to calculate totals
        function calculateTotals(items) {
            let totalCollected = 0;
            let totalIssued = 0;
            let totalOtherCharges = 0;

            items.forEach(item => {
                if (item.transaction_type === "Customer Payment") {
                    totalCollected += parseFloat(item.amount);
                } else if (item.transaction_type === "Loan Issue") {
                    totalIssued += parseFloat(item.amount);
                } else if (item.transaction_type === "Other Charges") {
                    totalOtherCharges += parseFloat(item.amount);
                }
            });

            $("#tot_Collected_amount").text(totalCollected.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $("#tot_Issued_amount").text(totalIssued.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
            $("#tot_other_Charges").text(totalOtherCharges.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }));

        }

        function load_data() {
            let center_details = $("#center_details").val();
            let date_from = $("#date_from").val();
            let date_to = $("#date_to").val();

            $.ajax({
                type: "POST",
                url: "/cashflow/datewise",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    center_details: center_details,
                    date_from: date_from,
                    date_to: date_to
                },
                success: function (data) {
                    console.log(data);
                    let items = data.items;
                    let tableBody = $("#loan_table tbody");
                    tableBody.empty(); // Clear existing data

                    items.forEach(item => {
                        let row = `<tr>
                    <td>${item.center_name || ''}</td>
                    <td>${item.group_name || ''}</td>
                    <td>${item.transaction_type || ''}</td>
                    <td>${item.customer_number || ''}</td>
                    <td>${item.loan_number || ''}</td>
                    <td>${item.customer_name || ''}</td>
                    <td>${item.date || ''}</td>`;

                        if (item.transaction_type === 'Loan Issue') {
                            row += `
                        <td>0.00</td>
                        <td>${item.amount || '0.00'}</td>`;
                        } else {
                            row += `
                        <td>${item.amount || '0.00'}</td>
                        <td>0.00</td>`;
                        }

                        row += `</tr>`;
                        tableBody.append(row);
                    });

                    // Calculate totals
                    calculateTotals(items);
                },
                error: function (xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }


    </script>

@endsection


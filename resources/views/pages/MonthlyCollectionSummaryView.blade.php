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
    <style>
        .table-responsive {
            overflow-x: auto;
        }

        .table th, .table td {
            white-space: nowrap;
        }
    </style>

@endsection


@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Monthly Collection Summary</h4>
                </div>
            </div>
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
                                <label for="day" class="form-label">Day</label>
                                <select class="form-control" id="day" name="day">
                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="month" class="form-label">Month</label>
                                <select class="form-control" id="month" name="month">
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-lg-3">
                                <label for="year" class="form-label">Year</label>
                                <select class="form-control" id="year" name="year">
                                    @for ($i = date('Y') - 20; $i <= date('Y') + 20; $i++)
                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <br><br><br><br>
                            <div class="col-lg-3 d-flex align-items-end">
                                <button type="button" class="btn btn-danger" id="search_button">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                &nbsp;&nbsp;

                                <button type="button" class="btn btn-primary" id="print_button">
                                    <i class="bi bi-printer"></i> Print
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="result_table" class="table">
                                <thead>
                                <tr id="header_row" class="bg-purple">
                                    <th>Center No</th>
                                    <th>Group No</th>
                                    <th>Member No</th>
                                    <th>Member Name</th>
                                    <th>Loan Amount</th>
                                    <th>Installment</th>
                                    <th>Loan Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                        <div class="row mt-1 mb-1 p-2">
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
                            </div>
                        </div>

                    </div> <!-- end card body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div>

    <!-- Hidden print section -->
    <div id="printSection" style="display:none;">
        <table class="loan-collection-table">
            <thead>
            <tr>
                <th colspan="2" class="branch-center">CENTER</th>
                <th colspan="2" class="branch-center">002 - Thuduwegedara</th>
            </tr>
            <tr>
                <th>MEMBER NO</th>
                <th>MEMBER NAME</th>
                <th>LOAN AMOUNT</th>
                <th>INSTALLMENT</th>
                <th>LOAN BALANCE</th>
                <th>CAPITAL AMOUNT</th>
                <th>CAPITAL BALANCE</th>
                <th class="payment-date">DATE: 17 / 05 / 2024</th> <!-- should fill dates here -->
                <th>Paid Amount</th> <!-- should show paid amounts -->
            </tr>
            </thead>
            <tbody id="printTableBody">
            <!-- Dynamic rows will be inserted here -->
            </tbody>
        </table>
    </div>

@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function () {
            let table = $('#result_table').DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf'
                ],
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                lengthChange: true,
                pageLength: 10,
            });

            // Search button click event
            $('#search_button').click(function () {
                let center = $('#center_details').val();
                let day = $('#day').val();
                let month = $('#month').val();
                let year = $('#year').val();

                // Fetch and update the table data here based on the selected filters
                // For example, you can use an AJAX request to fetch the data and update the table

                // Example AJAX request (you need to implement the actual logic)
                $.ajax({
                    url: '/fetch-data', // Update with your actual endpoint
                    method: 'GET',
                    data: {
                        center: center,
                        day: day,
                        month: month,
                        year: year
                    },
                    success: function (response) {
                        // Update the table data
                        table.clear().rows.add(response.data).draw();

                        // Update the total amounts
                        $('#tot_Collected_amount').text(response.tot_Collected_amount);
                        $('#tot_Issued_amount').text(response.tot_Issued_amount);
                        $('#tot_other_Charges').text(response.tot_other_Charges);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching data:', error);
                    }
                });
            });

            // Print button click event
            $('#print_button').click(function () {
                generatePrintTable();
                window.print();
            });

            // Function to generate the print table
            function generatePrintTable() {
                let printTableBody = $('#printTableBody');
                printTableBody.empty(); // Clear existing content

                let data = table.rows().data().toArray(); // Get all data from the DataTable

                data.forEach(function (row) {
                    let newRow = $('<tr></tr>');
                    row.forEach(function (cell) {
                        newRow.append('<td>' + cell + '</td>');
                    });
                    printTableBody.append(newRow);
                });

                // Append totals if needed
                printTableBody.append(`
                    <tr>
                        <td colspan="6" class="group-total">GROUP TOTAL</td>
                        <td class="payment-amount">000</td>
                        <td class="payment-amount">000</td>
                        <td class="payment-amount">000</td>
                        <td class="payment-amount">000</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="page-collection total-label">Total Collection Amount</td>
                        <td class="payment-amount">300,000.00</td>
                        <td class="payment-amount">14,571.43</td>
                        <td class="payment-amount">262,500.00</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                `);
            }
        });
    </script>
@endsection

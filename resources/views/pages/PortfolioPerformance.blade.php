@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <style>
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

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Portfolio & Performance</h4>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <form>
                            @csrf
                            <div class="row g-2"> <!-- Use g-2 for reduced spacing -->
                                <!-- Date From -->
                                <div class="col-lg-3">
                                    <div class="mb-2">
                                        <label for="date_from" class="form-label">Date From</label>
                                        <input type="date" class="form-control" id="date_from" name="date_from">
                                    </div>
                                </div>
                                <!-- Date To -->
                                <div class="col-lg-3">
                                    <div class="mb-2">
                                        <label for="date_to" class="form-label">Date To</label>
                                        <input type="date" class="form-control" id="date_to" name="date_to">
                                    </div>
                                </div>

                                <!-- Branch Selection -->
                                <div class="col-lg-3">
                                    <div class="mb-2">
                                        <label for="branch" class="form-label">Branch</label>
                                        <select class="form-control select2" id="branch" name="branch">
                                            <option value="0">All</option>
                                            @foreach($branch as $item)
                                                <option value="{{$item->branch_id}}">{{ $item->Name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Route Selection (Dynamic) -->
                                <div class="col-lg-3">
                                    <div class="mb-2">
                                        <label for="route" class="form-label">Route</label>
                                        <select class="form-control select2" id="route" name="route">
                                            <option value="0">All</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Center Selection (Dynamic) -->
                                <div class="col-lg-3">
                                    <div class="mb-2">
                                        <label for="center_details" class="form-label">Center</label>
                                        <select class="form-control select2" id="center_details" name="center_details">
                                            <option value="0">All</option>
                                        </select>
                                    </div>
                                </div>


                                <!-- Search Button -->
                                <div class="col-lg-3 d-flex align-items-center">
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-search"></i> Search
                                    </button>
                                </div>


                            </div>
                        </form>



                        <hr>
                        <div class="mb-3">
                            <button id="exportExcel" class="btn btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Export to Excel
                            </button>

                        </div>


                        <div class="table-responsive">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead class="sticky-top bg-purple">
                                <tr>
                                    <th>Branch</th>
                                    <th>Route</th>
                                    <th>Center</th>
                                    <th>Total Disbursement</th>
                                    <th>Total Loan Amount</th>
                                    <th>Issued Loan Count</th>
                                    <th>New Clients</th>
                                    <th>Repeat Clients</th>
                                    <th>Schedule Repayments</th>
                                    <th>Collected Repayments</th>
                                    <th>Capital Received</th>
                                    <th>Interest Received</th>
                                    <th>Penalty Received</th>
                                    <th>Processing Fee Received</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->
                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->




        </div> <!-- container -->

    </div>
@endsection

@section('script')
    <!-- DataTables JavaScript -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- SheetJS for Excel Export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <!-- jsPDF Library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- jsPDF AutoTable Plugin -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>


    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Select an option",
                allowClear: true
            });

            // Load Routes and Centers when Branch is selected
            $("#branch").change(function() {
                let branch_id = $(this).val();

                if (branch_id == 0) {
                    $("#route").html('<option value="0">All</option>');
                    $("#center_details").html('<option value="0">All</option>');
                    return;
                }

                // Fetch Routes & Centers for selected Branch
                $.ajax({
                    type: "GET",
                    url: "/get-routes-centers",
                    data: { branch_id: branch_id },
                    success: function(response) {
                        let routes = response.routes;
                        let centers = response.centers;

                        // Populate Routes
                        let routeOptions = '<option value="0">All</option>';
                        routes.forEach(route => {
                            routeOptions += `<option value="${route.id_route}">${route.name} - ${route.root_code}</option>`;
                        });
                        $("#route").html(routeOptions);

                        // Populate Centers
                        let centerOptions = '<option value="0">All</option>';
                        centers.forEach(center => {
                            centerOptions += `<option value="${center.idCenter}">${center.No} - ${center.Name}</option>`;
                        });
                        $("#center_details").html(centerOptions);
                    }
                });
            });

            // Load table data on form submit
            $("form").submit(function(e) {
                e.preventDefault(); // Prevent form from refreshing

                let date_from = $("#date_from").val();
                let date_to = $("#date_to").val();
                let branch = $("#branch").val();
                let route = $("#route").val();
                let center_details = $("#center_details").val();

                $.ajax({
                    type: "GET",
                    url: "/get-portfolio-performance",
                    data: {
                        date_from: date_from,
                        date_to: date_to,
                        branch: branch,
                        route: route,
                        center_details: center_details
                    },
                    success: function(response) {
                        let table = $("#loan_table tbody");
                        table.empty(); // Clear previous data

                        // Initialize totals
                        let total_disbursement = 0;
                        let total_loan_amount = 0;
                        let total_issued_loans = 0;
                        let total_new_clients = 0;
                        let total_repeat_clients = 0;
                        let total_schedule_repayments = 0;
                        let total_collected_repayments = 0;
                        let total_capital_received = 0;
                        let total_interest_received = 0;
                        let total_penalty_received = 0;
                        let total_processing_fee = 0;

                        response.data.forEach(row => {
                            // Convert numeric values to float and sum them
                            total_disbursement += parseFloat(row.total_disbursement);
                            total_loan_amount += parseFloat(row.total_loan_amount);
                            total_issued_loans += parseInt(row.issued_loan_count);
                            total_new_clients += parseInt(row.new_clients);
                            total_repeat_clients += parseInt(row.repeat_clients);
                            total_schedule_repayments += parseFloat(row.schedule_repayments);
                            total_collected_repayments += parseFloat(row.collected_repayments);
                            total_capital_received += parseFloat(row.capital_received);
                            total_interest_received += parseFloat(row.interest_received);
                            total_penalty_received += parseFloat(row.penalty_received);
                            total_processing_fee += parseFloat(row.processing_fee_received);

                            // Add row data
                            let newRow = `<tr>
                    <td>${row.branch_name}</td>
                    <td>${row.route_name}</td>
                    <td>${row.center_name}</td>
                    <td>${row.total_disbursement.toFixed(2)}</td>
                    <td>${row.total_loan_amount.toFixed(2)}</td>
                    <td>${row.issued_loan_count}</td>
                    <td>${row.new_clients}</td>
                    <td>${row.repeat_clients}</td>
                    <td>${row.schedule_repayments.toFixed(2)}</td>
                    <td>${row.collected_repayments.toFixed(2)}</td>
                    <td>${row.capital_received.toFixed(2)}</td>
                    <td>${row.interest_received.toFixed(2)}</td>
                    <td>${row.penalty_received.toFixed(2)}</td>
                    <td>${row.processing_fee_received.toFixed(2)}</td>
                </tr>`;
                            table.append(newRow);
                        });

                        // Append Total Row at the bottom
                        let totalRow = `<tr style="font-weight:bold; background-color: #f8f9fa;">
                <td colspan="3" class="text-center">Total</td>
                <td>${total_disbursement.toFixed(2)}</td>
                <td>${total_loan_amount.toFixed(2)}</td>
                <td>${total_issued_loans}</td>
                <td>${total_new_clients}</td>
                <td>${total_repeat_clients}</td>
                <td>${total_schedule_repayments.toFixed(2)}</td>
                <td>${total_collected_repayments.toFixed(2)}</td>
                <td>${total_capital_received.toFixed(2)}</td>
                <td>${total_interest_received.toFixed(2)}</td>
                <td>${total_penalty_received.toFixed(2)}</td>
                <td>${total_processing_fee.toFixed(2)}</td>
            </tr>`;

                        table.append(totalRow); // Add totals at the bottom of the table
                    }
                });
            });

            // Export to Excel
            $("#exportExcel").click(function() {
                let table = document.getElementById("loan_table");
                let wb = XLSX.utils.table_to_book(table, { sheet: "Portfolio Report" });
                XLSX.writeFile(wb, "Portfolio_Performance.xlsx");
            });







        });

    </script>
@endsection


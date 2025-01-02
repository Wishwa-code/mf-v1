@extends('layout.admin')

@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        .table-container {
            max-height: 400px; /* Set your desired max height */
            overflow-y: auto; /* Enable vertical scrollbar */
            position: relative;
        }

        .table-container thead th {
            position: sticky;
            top: 0;
            background-color: #136917; /* Change this to your desired header color */
            color: white; /* Change this to your desired text color */
            z-index: 10;
        }

        .table-container tbody tr {
            background-color: #fff;
        }

        .table th, .table td {
            text-align: center; /* Center the text */
            vertical-align: middle; /* Center vertically */
        }

        .card-body {
            padding: 1rem; /* Added padding */
        }

        .card {
            margin-bottom: 20px; /* Added margin bottom for spacing */
        }



        .total-pending-container {
            background-color: #1A2942; /* Light background color */
            padding: 5px; /* Padding around the container */
            margin-top: 10px; /* Top margin */
            border: 1px solid #dee2e6; /* Border color */
            border-radius: 5px; /* Rounded corners */

        }

        .total-pending-details {
            display: flex; /* Flex layout */
            justify-content: space-between; /* Space between elements */
            align-items: center; /* Center vertically */
            margin: 8px; /* Top margin */
            font-weight: bold; /* Bold text */
            font-size: 18px; /* Larger font size */
            /*margin-bottom: 10px; !* Bottom margin *!*/
            color: #d3d3d3;
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

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Loan In Areas</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <!-- Tabs navigation -->
                        <ul class="nav nav-tabs" id="loanTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="tab1-tab" data-toggle="tab" href="#tab1" role="tab"
                                   aria-controls="tab1" aria-selected="true">All Pending</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab2-tab" data-toggle="tab" href="#tab2" role="tab"
                                   aria-controls="tab2" aria-selected="false">30 Days</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab3-tab" data-toggle="tab" href="#tab3" role="tab"
                                   aria-controls="tab3" aria-selected="false">60 Days</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="tab4-tab" data-toggle="tab" href="#tab4" role="tab"
                                   aria-controls="tab4" aria-selected="false">90 Days</a>
                            </li>
                        </ul>
                        <br>
                        <!-- Tabs content -->
                        <div class="tab-content" id="loanTabsContent">
                            @for ($i = 1; $i <= 4; $i++)
                                <div class="tab-pane fade {{ $i == 1 ? 'show active' : '' }}" id="tab{{ $i }}"
                                     role="tabpanel" aria-labelledby="tab{{ $i }}-tab">
                                    <div class="table-responsive custom-scrollbar">
                                        <table class="table table-bordered table-sm table-striped"
                                               id="loan_table{{ $i }}">
                                            <thead class="sticky-top bg-purple">
                                            <tr>
                                                <th>Loan No</th>
                                                <th>Center No</th>
                                                <th>Group No</th>
                                                <th>Member NIC</th>
                                                <th>Member Name</th>
                                                <th>Pending Installment</th>
                                                <th>Penalty Total</th>
                                                <th>Pending Total</th>
                                                <th>Late Days</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody class="custom-scrollbar" style="max-height: 400px;">
                                            <!-- Populate table rows dynamically here -->
                                            </tbody>
                                        </table>
                                    </div> <!-- end table-container-->

                                    <div class="total-pending-container">

                                        <div class="total-pending-details">
                                            <div>
                                                <span class="total-pending-label">Total Late Installments : </span>
                                                <span class="total-pending-value" id="total_installments{{ $i }}">0</span>
                                            </div>
                                            <div>
{{--                                                <span class="total-pending-label">Total Loans : </span>--}}
{{--                                                <span class="total-pending-value" id="total_loans{{ $i }}">0</span>--}}
                                            </div>
                                            <div>
                                                <span class="total-pending-label">Total Pending Amount : </span>
                                                <span class="total-pending-value" id="tot_amount{{ $i }}">0.00</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- end container-fluid -->

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>

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
        $(function () {
            load_payment_table(1);
            load_payment_table(2);
            load_payment_table(3);
            load_payment_table(4);
            let x = ["#payment_amount"];
            decimalFormat(x);
            // Initialize Select2 Elements
            $('.select2').select2()

            // Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
        });

        function load_payment_table(tabIndex) {
            let center_details = $("#center_details").val();
            let group = $("#group").val();
            let customer = $("#customer_id").val();
            let status = $("#status").val();

            $.ajax({
                type: "POST",
                url: "/late_payment_report",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    center_details: center_details,
                    group: group,
                    customer: customer,
                    status: status
                },
                success: function (data, textStatus, xhr) {
                    console.log(data);
                    if (xhr.status === 200) {
                        var tableBody = $('#loan_table' + tabIndex + ' tbody');
                        tableBody.empty(); // Clear any existing rows
                        let tot = 0.0;
                        let totalInstallments = 0;

                        // Helper function to filter unique loans by highest Late_Days
                        function getUniqueLoans(loans) {
                            const uniqueLoans = {};
                            loans.forEach(function (loan) {
                                // Use loan ID as key
                                if (!uniqueLoans[loan.idCustomer_Loan] ||
                                    uniqueLoans[loan.idCustomer_Loan].Late_Days < loan.Late_Days) {
                                    uniqueLoans[loan.idCustomer_Loan] = loan; // Keep the one with the highest Late_Days
                                }
                            });
                            return Object.values(uniqueLoans);
                        }

                        let filteredLoans = [];
                        if (tabIndex === 2) {
                            filteredLoans = getUniqueLoans(data.loan_30_days);
                        } else if (tabIndex === 3) {
                            filteredLoans = getUniqueLoans(data.loan_60_days);
                        } else if (tabIndex === 4) {
                            filteredLoans = getUniqueLoans(data.loan_90_days);
                        } else if (tabIndex === 1) {
                            filteredLoans = getUniqueLoans(data.All);
                        }

                        // Process filtered loans
                        filteredLoans.forEach(function (item) {
                            tot += parseFloat(item.Total_Balance);
                            totalInstallments++;

                            var row = `<tr>
                                <td style="text-align: left">${item.Loan_No}</td>
                                <td>${item.center_no}</td>
                                <td>${item.group_name}</td>
                                <td style="text-align: left">${item.NIC}</td>
                                <td style="text-align: left">${item.customer_name} ${item.customer_lastname}</td>
                                <td>${parseFloat(item.Installment_Balance).toFixed(2)}</td>
                                <td>${parseFloat(item.Panalty_Balance).toFixed(2)}</td>
                                <td>${parseFloat(item.Total_Balance).toFixed(2)}</td>
                                <td>${item.Late_Days}</td>
                                <td><a href="/loanview/${item.idCustomer_Loan}" target="_blank" class="btn btn-warning"><i class="bi bi-eye"></i></a></td>
                            </tr>`;

                            tableBody.append(row);
                        });

                        $("#tot_amount" + tabIndex).text(formatNumber(tot));
                        $("#total_installments" + tabIndex).text(totalInstallments);

                        // Initialize DataTable after loading the data
                        $('#loan_table' + tabIndex).DataTable({
                            "paging": true,
                            "searching": true,
                            "ordering": true,
                            "info": true,
                            "responsive": true,
                            "lengthChange": false, // Disable page length change
                            "autoWidth": false, // Disable auto width calculation

                            "language": {
                                "emptyTable": "No data available in table"
                            },
                            "buttons": [
                                {
                                    extend: 'excelHtml5',
                                    className: 'btn btn-primary',
                                    text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                                    exportOptions: {
                                        columns: ':visible'
                                    }
                                }
                            ],
                            "dom": 'Bfrtip',
                        });
                    }
                },
                error: function (xhr, textStatus, error) {
                    console.log(error);
                }
            });
        }


        function getUniqueLoans(loans) {
            const uniqueLoans = {}; // Create an empty object to store loans by their ID

            loans.forEach(function (loan) {
                // Check if the loan ID exists in the uniqueLoans object
                if (!uniqueLoans[loan.idCustomer_Loan] ||
                    uniqueLoans[loan.idCustomer_Loan].Late_Days < loan.Late_Days) {
                    // If it doesn't exist or if the current loan has higher Late_Days, update the object
                    uniqueLoans[loan.idCustomer_Loan] = loan;
                }
            });

            // Return the values of the uniqueLoans object as an array
            return Object.values(uniqueLoans);
        }

        function formatNumber(number) {
            return number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        }

    </script>
@endsection

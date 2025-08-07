@extends('layout.admin')

@section('head')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.0.1/css/buttons.dataTables.min.css">

    <style>
        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
        }
        .table-responsive-sm {
            /* Optional: Add some padding to the table container for better visuals */
            padding: 10px;
        }

        /*#loan_table {*/
        /*    !* Optional: Set a smaller font size if needed *!*/
        /*    font-size: 0.875rem; !* Example: 14px *!*/
        /*}*/

        #loan_table th, #loan_table td {
            /* Reduce padding to decrease row height */
            padding: 4px 8px;
            /* Set a smaller line height */
            line-height: 1.2;
        }
        .bulb-icon {
            color: #ffcc00; /* Example color for the bulb icon */
        }

        .fa-lightbulb {
            font-size: 1.5rem; /* Adjust size as needed */
        }

        .bg-purple th {
            color: #e1e1e1 !important;
        }

        .bg-purple {
            background-color: #1A2942 !important;
            color: white !important;

        }
        /*#loan_table tr, #loan_table th, #loan_table td {*/
        /*    margin: 0 !important;*/
        /*    padding: 10px !important; !* Adjust padding to your preference *!*/
        /*}*/

    </style>

    <style>
        .status-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 10px;
            background-color: #f4f4f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .status-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 1rem;
            color: #333;
        }
        .status-item i {
            font-size: 1.5rem;
        }
        .table-responsive-sm {
            overflow-x: auto;
        }

        table.dataTable {
            width: 100%;  /* Ensures table uses full width */
        }

        @media (max-width: 768px) {
            /* Adjust table column visibility or width as needed */
            #loan_table {
                font-size: 12px;  /* Smaller font for smaller screens */
            }
        }
        .table-scroll-container {
            max-height: 700px; /* Change height as needed */
            overflow-y: auto;
            overflow-x: hidden;
            border: 1px solid #ccc;
        }

        /* Keep the header sticky */
        #loan_table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: #1A2942; /* Match your bg-purple */
            color: white;
        }


    </style>
@endsection


@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Loan in Arrears Report Overview</h4>
                </div>
            </div>
            <span style="color: #a19595">"The Loan in Arrears Report provides insights into overdue installments for loans, categorized based on the number of installments in arrears. It helps you track customers who are behind on payments, enabling you to take timely actions for collection or resolution."</span>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="mb-2"> <!-- Reduced bottom margin -->
                                    <label for="route" class="form-label">Route</label>
                                    <select class="form-control select2" id="route">
                                        <option value="0">All</option>
                                        @foreach($route as $item)
                                            <option value="{{$item->id_route}}">{{ $item->name }} - {{ $item->Full_Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Center</label>
                                    <select class="form-control select2" id="center_details">
                                        <option value="0">All</option>
                                        @foreach($center as $item)
                                            <option value="{{$item->idCenter}}">{{ $item->Name }}-{{ $item->Route }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Group</label>
                                    <select class="form-control select2" id="group">
                                        <option value="0">All</option>
                                        @foreach($group as $item)
                                            <option value="{{$item->idCustomer_Group}}">{{ $item->Group_No }}-{{ $item->Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Customer</label>
                                    <select class="form-control select2" id="customer_id">
                                        <option value="0">All</option>
                                        @foreach($customers as $item)
                                            <option value="{{$item->idCustomer}}">{{ $item->First_Name }} {{$item->Last_Name}}-{{ $item->Nic }}-{{ $item->Contact_No }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Lending Officer</label>
                                    <select class="form-control select2" id="lending">
                                        <option value="0">All</option>
                                        @foreach($lending_officer as $item)
                                            <option value="{{ $item->id }}">{{ $item->Full_Name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="installment_filter" class="form-label">Pending Installments Filter</label>
                                    <select class="form-control select2" id="installment_filter">
                                        <option value="all">All</option>
                                        <option value="more_than_3">More than 3</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3" hidden>
                                <div class="mb-3">

                                    <select class="form-control select2" id="status">
                                        <option value="0">All</option>
                                        <option value="1">Today Collection</option>
                                        <option value="2" selected>Late Payments</option>
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="col-lg-3">
                                <div class="mb-3">

                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-danger" onclick="load_payment_table();"><i class="bi bi-search"></i> </button>


                        <hr>
                        <button onclick="downloadExcel()" class="btn btn-success mb-3">
                            <i class="fas fa-file-excel"></i> Download Excel
                        </button>
                        <div class="status-container">
                            <div class="status-item">
                                <i class="fas fa-lightbulb bulb-icon" style="color: #e1cf1e"></i>
                                <span>One Installment Arrease</span>
                            </div>
                            <div class="status-item">
                                <i class="fas fa-lightbulb bulb-icon" style="color: orange"></i>
                                <span>Two Installments Arrease</span>
                            </div>
                            <div class="status-item">
                                <i class="fas fa-lightbulb bulb-icon" style="color: #f35858"></i>
                                <span>More Than Two Installments Arrease</span>
                            </div>
                        </div>

                        <div class="table-scroll-container">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead class="sticky-top bg-purple">
                                <tr>
                                    <th>Loan No</th>
                                    <th>Center No</th>
                                    <th>Group No</th>
                                    <th>Leasing</th>
                                    <th>Member NIC</th>
                                    <th>Member Contact No</th>
                                    <th>Member Name</th>
                                    <th>Pending Installments</th>
                                    <th>Penalty Total</th>
                                    <th>Pending Total</th>
                                    <th>Loan Balance</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>


                        <div class="row mt-1 mb-1 p-2">
                            <div class="col-md-8 row">
                                <div class="col-sm-3">
                                    <div>
                                        <span class="fw-bold">Total Pending Amount</span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <span id="tot_amount">0.00</span>
                                    </div>

                                </div>
                            </div>



                        </div>

                    </div> <!-- end card-->
                </div> <!-- end col -->


            </div>
            <!-- end row -->




        </div> <!-- container -->

    </div>

    <div class="modal fade" id="issue-loan-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h4 class="modal-title" >gwegerg</h4> -->
                    <!-- <h4>Issue Loan</h4> -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div >

                        <div>
                            <div class="row mt-1 mb-2">
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Paid Amount(LKR)</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">

                                    <input type="hidden" id="cus_id"  class="form-control">
                                    <input type="hidden" id="ins_id"  class="form-control">
                                    <div>
                                        <input type="text" id="payment_amount" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="payment()">Pay</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>


    <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header pt-2">
                    <h4>Installment Log</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="table-responsive-sm">
                        <div class="table-scroll-container">
                            <table class="table table-centered mb-0" id="ins_table">
                                <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Penalty Total</th>
                                    <th>Installment Balance</th>
                                    <th>Total Balance</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>

                    </div> <!-- end table-responsive-->

                    <div class="row mt-1 mb-1 pt-3">

                        <div class="col-md-7 pl-3">
                            <div class="row mt-1 mb-5">
                                <div class="col-sm-5">
                                    <div>
                                        <span class="fw-bold">Penalty Total(LKR)</span>
                                    </div>
                                    <div>
                                        <span class="fw-bold">Installment Balance(LKR)</span>
                                    </div>

                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <span id="panelty_tot">0.00</span>
                                    </div>
                                    <div>
                                        <span id="ins_tot">0.00</span>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 pl-3 float-right">
                            <div class="row mt-1 mb-5">
                                <div class="col-sm-10">
                                    <div>
                                        <span class="fw-bold">Total Balance(LKR)</span>
                                    </div>


                                </div>
                                <div class="col-lg-2">
                                    <div>
                                        <span id="tot_balance">0.00</span>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


@endsection

@section('script')
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/js/all.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    {{--    <script src="../JS/today_payment.js"></script>--}}
    <script>
        $(function() {
            load_payment_table();
            let x = ["#payment_amount"];
            decimalFormat(x);
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })




        })

        $('#installment_filter').on('change', function () {
            load_payment_table();
        });

        function load_payment_table(page = 1) {
            let center_details = $("#center_details").val();
            let route = $("#route").val();
            let group = $("#group").val();
            let customer = $("#customer_id").val();
            let status = $("#status").val();
            let lending = $("#lending").val();
            let installmentFilter = $("#installment_filter").val();

            $.ajax({
                type: "POST",
                url: `/latePayment_load_check?page=${page}`,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: {
                    center_details: center_details,
                    route: route,
                    group: group,
                    customer: customer,
                    lending: lending,
                    status: status,
                    installment_filter: installmentFilter
                },
                success: function(data, textStatus, xhr) {
                    console.log(data);

                    const tbody = $('#loan_table tbody');
                    tbody.empty(); // Clear old rows

                    if (xhr.status === 200 && data.item.length > 0) {
                        let tot = 0.0;

                        let currentPage = data.current_page ?? page;
                        let lastPage = data.last_page ?? 1;

                        data.item.forEach(function(item) {
                            let totalBalance = parseFloat(item.Total_Balance);
                            tot += totalBalance;

                            let statusColor = "#000";
                            if (item.Installment_Count === 1) {
                                statusColor = "#e1cf1e";
                            } else if (item.Installment_Count === 2) {
                                statusColor = "orange";
                            } else if (item.Installment_Count > 2) {
                                statusColor = "#f35858";
                            }

                            let row = `
                        <tr>
                            <td>${item.Loan_No}</td>
                            <td>${item.center_no}</td>
                            <td>${item.group_name}</td>
                            <td>${item.Vehicle_No ?? '-'}</td>
                            <td>${item.NIC}</td>
                            <td>${item.Contact_No}</td>
                            <td>${item.customer_name} ${item.customer_lastname}</td>
                            <td>${item.Installment_Count}</td>
                            <td>${parseFloat(item.Panalty_Balance).toFixed(2)}</td>
                            <td>${parseFloat(item.Total_Balance).toFixed(2)}</td>
                            <td>${parseFloat(item.Balance_Amount).toFixed(2)}</td>
                            <td><i class="fas fa-lightbulb bulb-icon" style="color: ${statusColor}"></i></td>
                            <td><a href="/loanview/${item.idCustomer_Loan}" target="_blank" class="btn btn-warning"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    `;
                            tbody.append(row);
                        });

                        // Update total amount
                        $("#tot_amount").text(tot.toFixed(2));

                        // Custom Pagination
                        let paginationControls = '';
                        if (currentPage > 1) {
                            paginationControls += `
                        <button onclick="load_payment_table(${currentPage - 1})" class="btn btn-sm btn-outline-primary me-2">
                            <i class="bi bi-arrow-left-circle me-1"></i> Previous
                        </button>`;
                        }
                        if (currentPage < lastPage) {
                            paginationControls += `
                        <button onclick="load_payment_table(${currentPage + 1})" class="btn btn-sm btn-outline-primary">
                            Next <i class="bi bi-arrow-right-circle ms-1"></i>
                        </button>`;
                        }

                        $('#pagination').html(paginationControls);

                    } else {
                        tbody.append('<tr><td colspan="12" class="text-center">No records found</td></tr>');
                        $("#tot_amount").text("0.00");
                        $('#pagination').html('');
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }




        function formatNumber(num) {
            return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        function downloadExcel() {
            const wb = XLSX.utils.book_new();
            const ws_data = [];

            // Add header (excluding last 2)
            const headers = [];
            $('#loan_table thead th').each(function(index) {
                if (index < $('#loan_table thead th').length - 2) {
                    headers.push($(this).text().trim());
                }
            });
            ws_data.push(headers);

            // Add rows
            $('#loan_table tbody tr').each(function () {
                const row = [];
                $(this).find('td').each(function (index) {
                    if (index < $(this).parent().find('td').length - 2) {
                        row.push($(this).text().trim());
                    }
                });
                ws_data.push(row);
            });

            const ws = XLSX.utils.aoa_to_sheet(ws_data);
            XLSX.utils.book_append_sheet(wb, ws, "Loan Report");

            XLSX.writeFile(wb, 'Loan_in_Arrears_Report.xlsx');
        }



    </script>


@endsection


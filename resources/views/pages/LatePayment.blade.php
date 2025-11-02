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
            min-width: 1200px; /* add width to reduce column cutout */
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
            overflow-x: auto;
            border: 1px solid #ccc;
            position: relative;
        }

        /* Keep the header sticky */
        #loan_table thead th {
            position: sticky;
            top: 0;
            z-index: 2;
            background-color: #1A2942; /* Match your bg-purple */
            color: white;
        }
        #loan_table tfoot {
            position: sticky;
            bottom: 0;
            z-index: 3;
        }
        #loan_table tfoot td,
        #loan_table tfoot th {
            background-color: #1A2942;
            color: #e1e1e1;
        }
        #loading-spinner {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            z-index: 9999;
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 8px;
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
                                        <option value="maturity">More than 3 and maturity expired</option>
                                        <option value="maturity7">Maturity +7 days</option>
                                        <option value="maturity14">Maturity +14 days</option>
                                        <option value="maturity21">Maturity +21 days</option>
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
                            <div id="loading-spinner" style="display:none; text-align:center; padding:20px;">
                                <i class="fas fa-spinner fa-spin fa-2x"></i>
                                <p>Loading data, please wait...</p>
                            </div>

                            <table class="table table-centered mb-0" id="loan_table">

                                <thead class="sticky-top bg-purple">
                                <tr>
                                    <th>Loan No</th>
                                    <th>Disbursement Date</th>
                                    <th>Center Name</th>
                                    <th>Group No</th>
                                    <th>Leasing</th>
                                    <th>Member NIC</th>
                                    <th>Member Contact No</th>
                                    <th>Member Name</th>
                                    <th>Installment Amount</th>
                                    <th>Pending Installments</th>
                                    <th>Penalty Total</th>
                                    <th>Loan Balance</th>
                                    <th>Capital Balance</th>
                                    <th>Arrears</th>
                                    <th>Total Balance</th>
                                    <th>Last Payment Date</th>
                                    <th>Last Payment Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot class="bg-purple" style="font-weight: bold;">
                                <tr>
                                    <td colspan="8" style="text-align: right; padding-right: 10px;">TOTAL:</td>
                                    <td id="total_installment_amount">0.00</td>
                                    <td id="total_pending_installments">0</td>
                                    <td id="total_penalty">0.00</td>
                                    <td id="total_loan_balance">0.00</td>
                                    <td id="total_capital_balance">0.00</td>
                                    <td id="total_arrears">0.00</td>
                                    <td id="total_balance">0.00</td>
                                    <td></td>
                                    <td id="total_last_payment">0.00</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                </tfoot>
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

            // Show spinner and clear table
            $("#loading-spinner").show();
            $('#loan_table tbody').empty();

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
                        let totalInstallmentAmount = 0.0;
                        let totalPendingInstallments = 0;
                        let totalPenalty = 0.0;
                        let totalLoanBalance = 0.0;
                        let totalCapitalBalance = 0.0;
                        let totalArrears = 0.0;
                        let totalBalanceSum = 0.0;
                        let totalLastPayment = 0.0;

                        let currentPage = data.current_page ?? page;
                        let lastPage = data.last_page ?? 1;

                        data.item.forEach(function(item) {
                            const rowTotalBalance = parseFloat(item.Total_Balance || 0);
                            let Installment_Count = parseFloat(item.Installment_Count);
                            tot += rowTotalBalance;

                            // Accumulate totals
                            totalInstallmentAmount += parseFloat(item.Installment_Amount || 0);
                            totalPendingInstallments += parseInt(item.Installment_Count || 0);
                            totalPenalty += parseFloat(item.Panalty_Balance || 0);
                            totalLoanBalance += parseFloat(item.Balance_Amount || 0);
                            totalCapitalBalance += parseFloat(item.capital_balance || 0);
                            totalArrears += parseFloat(item.arrears || 0);
                            totalBalanceSum += (parseFloat(item.Balance_Amount || 0) + parseFloat(item.Panalty_Balance || 0));
                            totalLastPayment += parseFloat(item.Last_Payment_Amount || 0);

                            let statusColor = "#000";
                            if (Installment_Count === 1) {
                                statusColor = "#e1cf1e";
                            } else if (Installment_Count === 2) {
                                statusColor = "orange";
                            } else if (Installment_Count > 2) {
                                statusColor = "#f35858";
                            }

                            let row = `
                        <tr>
                            <td>${item.Loan_No}</td>
                            <td>${item.Date_Time ?? '-'}</td>
                            <td>${item.center_no}</td>
                            <td>${item.group_name}</td>
                            <td>${item.Vehicle_No ?? '-'}</td>
                            <td>${item.NIC}</td>
                            <td>${item.Contact_No}</td>
                            <td>${item.customer_name} ${item.customer_lastname}</td>
                            <td>${parseFloat(item.Installment_Amount).toFixed(2)}</td>
                            <td>${item.Installment_Count}</td>
                            <td>${parseFloat(item.Panalty_Balance).toFixed(2)}</td>
                            <td>${parseFloat(item.Balance_Amount).toFixed(2)}</td>
                            <td>${parseFloat(item.capital_balance).toFixed(2)}</td>
                            <td>${parseFloat(item.arrears ?? 0).toFixed(2)}</td>
<td>${(
                                (parseFloat(item.Balance_Amount) || 0) +
                                (parseFloat(item.Panalty_Balance) || 0)
                            ).toFixed(2)}</td>
                            <td>${item.Last_Payment_Date ?? '-'}</td>
                            <td>${item.Last_Payment_Amount > 0 ? parseFloat(item.Last_Payment_Amount).toFixed(2) : '-'}</td>
                            <td><i class="fas fa-lightbulb bulb-icon" style="color: ${statusColor}"></i></td>
                            <td><a href="/loanview/${item.idCustomer_Loan}" target="_blank" class="btn btn-warning"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    `;
                            tbody.append(row);
                        });

                        // Update footer totals
                        $('#total_installment_amount').text(totalInstallmentAmount.toFixed(2));
                        $('#total_pending_installments').text(totalPendingInstallments);
                        $('#total_penalty').text(totalPenalty.toFixed(2));
                        $('#total_loan_balance').text(totalLoanBalance.toFixed(2));
                        $('#total_capital_balance').text(totalCapitalBalance.toFixed(2));
                        $('#total_arrears').text(totalArrears.toFixed(2));
                        $('#total_balance').text(totalBalanceSum.toFixed(2));
                        $('#total_last_payment').text(totalLastPayment.toFixed(2));

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
                        tbody.append('<tr><td colspan="19" class="text-center">No records found</td></tr>');
                        $("#tot_amount").text("0.00");
                        
                        // Reset footer totals
                        $('#total_installment_amount').text('0.00');
                        $('#total_pending_installments').text('0');
                        $('#total_penalty').text('0.00');
                        $('#total_loan_balance').text('0.00');
                        $('#total_capital_balance').text('0.00');
                        $('#total_arrears').text('0.00');
                        $('#total_balance').text('0.00');
                        $('#total_last_payment').text('0.00');
                        
                        $('#pagination').html('');
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                },
                complete: function() {
                    // Always hide spinner after request finishes
                    $("#loading-spinner").hide();
                }
            });
        }




        function formatNumber(num) {
            return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function downloadExcel() {
            const wb = XLSX.utils.book_new();
            const ws_data = [];

            // 1) Collect headers (excluding last 2 columns)
            const headers = [];
            const $ths = $('#loan_table thead th');
            const colCount = $ths.length - 2; // drop Status + Action
            for (let i = 0; i < colCount; i++) {
                headers.push($($ths[i]).text().trim());
            }
            ws_data.push(headers);

            // 2) Which headers are numeric? (adjust names if yours differ)
            const NUMERIC_HEADERS = new Set([
                'Installment Amount',
                'Pending Installments',
                'Penalty Total',
                'Total Balance',
                'Loan Balance',
                'Capital Balance',
                'Arrears',
                'Last Payment Amount'
            ]);

            // Build a map of column index -> numeric (true/false)
            const numericColMap = {};
            headers.forEach((h, i) => {
                numericColMap[i] = NUMERIC_HEADERS.has(h);
            });

            // 3) Add body rows; coerce numeric cells to real numbers
            $('#loan_table tbody tr').each(function () {
                const row = [];
                const $tds = $(this).find('td');

                for (let i = 0; i < colCount; i++) {
                    let txt = $($tds[i]).text().trim();

                    if (numericColMap[i]) {
                        // Remove commas/spaces and convert; treat '-' or empty as blank
                        const cleaned = txt.replace(/,/g, '').replace(/\s+/g, '');
                        const num = parseFloat(cleaned);
                        if (!isNaN(num) && isFinite(num)) {
                            row.push(num); // push a Number, not a string
                        } else {
                            row.push(null); // keep cell empty if not a valid number
                        }
                    } else {
                        row.push(txt);
                    }
                }
                ws_data.push(row);
            });

            // 3.5) Add totals row from tfoot (accounting for colspan)
            const totalsRow = [];
            
            // Manually build totals row with proper column alignment
            // Columns: Loan No, Disbursement Date, Center Name, Group No, Leasing, Member NIC, Member Contact No, Member Name,
            //          Installment Amount, Pending Installments, Penalty Total, Loan Balance, Capital Balance, Arrears, Total Balance, Last Payment Date, Last Payment Amount
            
            // First 8 columns show "TOTAL:"
            totalsRow.push('TOTAL:');  // Loan No
            totalsRow.push('');         // Disbursement Date
            totalsRow.push('');         // Center Name
            totalsRow.push('');         // Group No
            totalsRow.push('');         // Leasing
            totalsRow.push('');         // Member NIC
            totalsRow.push('');         // Member Contact No
            totalsRow.push('');         // Member Name
            
            // Add the actual totals from footer
            totalsRow.push(parseFloat($('#total_installment_amount').text()) || 0);  // Installment Amount
            totalsRow.push(parseInt($('#total_pending_installments').text()) || 0);  // Pending Installments
            totalsRow.push(parseFloat($('#total_penalty').text()) || 0);             // Penalty Total
            totalsRow.push(parseFloat($('#total_loan_balance').text()) || 0);        // Loan Balance
            totalsRow.push(parseFloat($('#total_capital_balance').text()) || 0);     // Capital Balance
            totalsRow.push(parseFloat($('#total_arrears').text()) || 0);             // Arrears
            totalsRow.push(parseFloat($('#total_balance').text()) || 0);             // Total Balance
            totalsRow.push('');                                                      // Last Payment Date
            totalsRow.push(parseFloat($('#total_last_payment').text()) || 0);        // Last Payment Amount
            totalsRow.push('');                                                      // Status
            totalsRow.push('');                                                      // Action
            
            ws_data.push(totalsRow);

            // 4) Create sheet
            const ws = XLSX.utils.aoa_to_sheet(ws_data);

            // 5) Apply number format to numeric columns (two decimals)
            //    Also make sure those cells are typed as numeric.
            const range = XLSX.utils.decode_range(ws['!ref'] || 'A1');
            for (let C = 0; C < colCount; C++) {
                if (!numericColMap[C]) continue;

                for (let R = 1; R <= range.e.r; R++) { // skip header at R=0
                    const cellAddress = XLSX.utils.encode_cell({ r: R, c: C });
                    const cell = ws[cellAddress];
                    if (!cell) continue;
                    if (typeof cell.v === 'number') {
                        cell.t = 'n';          // explicitly numeric
                        cell.z = '0.00';       // 2 decimal places
                    } else if (cell.v == null || cell.v === '') {
                        // leave empty
                    } else {
                        // Try one more time to coerce (in case something slipped through)
                        const maybe = parseFloat(String(cell.v).replace(/,/g, ''));
                        if (!isNaN(maybe) && isFinite(maybe)) {
                            cell.v = maybe;
                            cell.t = 'n';
                            cell.z = '0.00';
                        }
                    }
                }
            }

            // 6) Optional: set column widths a bit wider for readability
            ws['!cols'] = headers.map(() => ({ wch: 16 }));

            // 7) Append and write
            XLSX.utils.book_append_sheet(wb, ws, 'Loan Report');
            XLSX.writeFile(wb, 'Loan_in_Arrears_Report.xlsx');
        }

    </script>


@endsection


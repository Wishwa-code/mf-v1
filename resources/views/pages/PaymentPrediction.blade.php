@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <style>


        .total-pending-container {
            background-color: #1A2942; /* Light background color */
            padding: 5px; /* Padding around the container */
            margin-top: 10px; /* Top margin */
            border: 1px solid #dee2e6; /* Border color */
            border-radius: 5px; /* Rounded corners */
            color: #ffffff !important;

        }

        thead {
            background-color: #d9edf7; /* Light blue color */
            color: #31708f; /* Darker blue text for contrast */
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


        .bg-purple th {
            color: #e1e1e1 !important; /* Ensure white text color for th elements */
        }

        .bg-purple {
            background-color: #1A2942 !important; /* Purple color */
            color: white !important; /* White text color */
        }

        .modal_2 {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal_2-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #ccc;
            width: 90%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .close_2 {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close_2:hover,
        .close_2:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .printer-design {
            text-align: center;
        }

        .receipt {
            font-family: 'Arial', sans-serif;
            text-align: left;
            margin: 0;
        }

        .receipt .header {
            text-align: center;
        }

        .receipt .logo {
            width: 80px;
            margin: 0 auto 10px;
        }

        .receipt h1, .receipt h2 {
            margin: 5px 0;
        }

        .receipt p {
            margin: 5px 0;
            line-height: 1.5;
        }

        .receipt .details p {
            margin: 3px 0;
        }

        .receipt .payment-info {
            margin: 10px 0;
        }

        .receipt .payment-info .item {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }

        .receipt .payment-info .description {
            font-weight: bold;
        }

        .receipt .payment-info .amount {
            text-align: right;
        }

        .receipt hr {
            border: 0;
            border-top: 1px dashed #ddd;
            margin: 10px 0;
        }

        .receipt .totals p {
            margin: 5px 0;
            font-weight: bold;
        }

        .receipt .signature {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            margin: 20px 0;
        }

        .receipt .signature-line {
            width: 100%;
            text-align: center;
            margin-bottom: 5px;
        }

        .receipt .thank-you {
            text-align: center;
            font-size: 18px;
            margin-top: 20px;
        }

        .bg-purple th {
            color: #e1e1e1 !important; /* Ensure white text color for th elements */
        }


        .bg-purple {
            background-color: #1A2942 !important; /* Purple color */
            color: white !important; /* White text color */
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
                width: 80mm; /* 80mm width for thermal printer */
                background: white;
            }

            .modal-content {
                width: 80mm; /* Ensures the modal content fits the thermal printer paper */
                border: none; /* Removes border during print */
            }

            .receipt {
                width: 100%;
                padding: 10px;
            }

            .printer-design button {
                display: none;
            }

            .form-control {
                height: calc(5.25rem + 2px); /* Adjust this value if needed */
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
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Payment Prediction Report</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body" style="width: 98%">

                        <div class="row">
                            <div class="card p-3 mb-3">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">Center</label>
                                        <select class="form-select select2" id="center_id">
                                            <option value="0">All Centers</option>
                                            @foreach($centers as $center)
                                                <option value="{{ $center->idCenter }}">{{ $center->No }} - {{ $center->Name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">From Date</label>
                                        <input type="date" id="from_date" class="form-control" min="{{ now()->toDateString() }}">
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">To Date</label>
                                        <input type="date" id="to_date" class="form-control" min="{{ now()->toDateString() }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Filter Type</label>
                                        <select class="form-select" id="filter_type">
                                            <option value="all">All</option>
                                            <option value="scheduled">Scheduled Installment Amount</option>
                                            <option value="arrears">Arrears Amount</option>
                                        </select>
                                    </div>


                                    <div class="col-md-2 d-grid">
                                        <button class="btn btn-primary" onclick="loadPredictionReport()">
                                            <i class="bi bi-graph-up"></i> Generate
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <hr>
                        <div class="table-responsive" style="max-height: 800px; overflow-y: auto;">
                            <table class="table table-bordered mb-0" id="loan_table">
                                <thead class="bg-purple" style="position: sticky; top: 0; z-index: 1;">
                                <tr>
                                    <th>Center Name</th>
                                    <th>Loan No</th>
                                    <th>Member No</th>
                                    <th>Loan Portfolio</th>
                                    <th>Installment Amount</th>
                                    <th>Loan Balance</th>
                                    <th>Scheduled Installment Amount</th>
                                    <th>Arrears Amount</th>
                                    <th>Penalty Amount</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody style="max-height: 400px; overflow-y: auto;"></tbody>
                                <tfoot>
                                <tr style="font-weight: bold; background: #f2f2f2;">
                                    <td colspan="4" class="text-end">Total</td>
                                    <td id="tot_installment">0.00</td>
                                    <td id="tot_balance">0.00</td>
                                    <td id="tot_target">0.00</td>
                                    <td id="tot_arrears">0.00</td>
                                    <td id="tot_penalty">0.00</td>
                                    <td></td>
                                </tr>
                                </tfoot>
                            </table>

                        </div>

                        <div class="text-end mt-2">
                            <button class="btn btn-success" onclick="exportTableToExcel('loan_table')">
                                <i class="bi bi-file-earmark-excel"></i> Export to Excel
                            </button>
                        </div>

                    </div> <!-- end card-->

                </div> <!-- end col -->


            </div>
            <!-- end row -->


        </div> <!-- container -->

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
    <script src="../JS/validate.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        function loadPredictionReport() {
            let fromDate = $('#from_date').val();
            let toDate = $('#to_date').val();
            let center = $('#center_id').val();
            let filterType = $('#filter_type').val();
            if (!fromDate || !toDate) {
                Swal.fire("Validation Error", "Please select both From and To dates.", "warning");
                return;
            }

            if (fromDate > toDate) {
                Swal.fire("Validation Error", "From Date cannot be after To Date.", "warning");
                return;
            }

            $.ajax({
                url: "/prediction-report/fetch",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
                },
                data: {
                    from_date: fromDate,
                    to_date: toDate,
                    center_id: center
                },
                success: function (res) {
                    let tbody = $('#loan_table tbody');
                    tbody.empty();

                    let tot_target = 0;
                    let tot_installment = 0;
                    let tot_arrears = 0;
                    let tot_penalty = 0;
                    let tot_balance = 0;

                    if (res.length === 0) {
                        tbody.append('<tr><td colspan="10" class="text-center">No data found</td></tr>');
                    } else {
                        res.forEach(item => {

                            const scheduled = parseFloat(item.total_balance || 0);
                            const arrears = parseFloat(item.arrears || 0);

                            if (filterType === 'scheduled' && scheduled <= 0) return;
                            if (filterType === 'arrears' && arrears <= 0) return;



                            tot_target += parseFloat(item.total_balance || 0);
                            tot_installment += parseFloat(item.Installment_Amount || 0);
                            tot_arrears += parseFloat(item.arrears || 0);
                            tot_penalty += parseFloat(item.penalty || 0);
                            tot_balance += parseFloat(item.loan_balance || 0);

                            tbody.append(`
                        <tr>
                            <td>${item.center_name ?? '-'}</td>
                            <td>${item.loan_no}</td>
                            <td>${item.customer_no}</td>
                            <td>${parseFloat(item.total_loan_amount).toFixed(2)}</td>
                            <td>${parseFloat(item.Installment_Amount).toFixed(2)}</td>
                            <td>${parseFloat(item.loan_balance).toFixed(2)}</td>
                            <td>${parseFloat(item.total_balance).toFixed(2)}</td>
                            <td>${parseFloat(item.arrears).toFixed(2)}</td>
                            <td>${parseFloat(item.penalty).toFixed(2)}</td>
                            <td>
                                <a href="/loanview/${item.idCustomer_Loan}" target="_blank" class="btn btn-warning btn-sm" title="View Loan">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    `);
                        });
                    }

                    // ✅ Always update total fields
                    $('#tot_balance').text(tot_balance.toFixed(2));
                    $('#tot_installment').text(tot_installment.toFixed(2));
                    $('#tot_arrears').text(tot_arrears.toFixed(2));
                    $('#tot_penalty').text(tot_penalty.toFixed(2));
                    $('#tot_target').text(tot_target.toFixed(2));
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    Swal.fire("Error", "Failed to fetch data", "error");
                }
            });
        }




        function exportTableToExcel(tableID, filename = '') {
            let downloadLink;
            let dataType = 'application/vnd.ms-excel';
            let tableSelect = document.getElementById(tableID);
            let tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

            filename = filename ? filename + '.xls' : 'payment_prediction.xls';


            downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);

            if (navigator.msSaveOrOpenBlob) {
                let blob = new Blob(['\ufeff', tableHTML], { type: dataType });
                navigator.msSaveOrOpenBlob(blob, filename);
            } else {
                downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
                downloadLink.download = filename;
                downloadLink.click();
            }
        }
    </script>


@endsection


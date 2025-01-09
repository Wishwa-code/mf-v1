@extends('layout.admin')

@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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
        background-color: rgba(0,0,0,0.5);
    }

    .modal_2-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #ccc;
        width: 90%;
        max-width: 400px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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
            <div class="col-6">
                <div class="page-title-box">
                    <h4 class="page-title">View Re-Payment</h4>
                </div>
            </div>
            <div class="col-6 d-flex justify-content-end align-items-center">
                <div class="mb-4">
                    Date From: &nbsp; &nbsp; <input type="date" class="form-control d-inline-block w-auto" id="select_date" onchange="load_payment_table();" name="select_date" value="{{ date('Y-m-d') }}">
                    Date To: &nbsp; &nbsp; <input type="date" class="form-control d-inline-block w-auto" id="select_date_to" onchange="load_payment_table();" name="select_date_to" value="{{ date('Y-m-d') }}">
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <!-- Center Selection -->
                            <div class="col-lg-3 mb-3">
                                <label for="center_details" class="form-label">Center</label>
                                <select class="form-control select2" id="center_details">
                                    <option value="0">All</option>
                                    @foreach($center as $item)
                                        <option value="{{ $item->idCenter }}">{{ $item->Name }} - {{ $item->Route }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Group Selection -->
                            <div class="col-lg-3 mb-3">
                                <label for="group" class="form-label">Group</label>
                                <select class="form-control select2" id="group">
                                    <option value="0">All</option>
                                    @foreach($group as $item)
                                        <option value="{{ $item->idCustomer_Group }}">{{ $item->Group_No }} - {{ $item->Name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Customer Selection -->
                            <div class="col-lg-3 mb-3">
                                <label for="customer_id" class="form-label">Customer</label>
                                <select class="form-control select2" id="customer_id">
                                    <option value="0">All</option>
                                    @foreach($customers as $item)
                                        <option value="{{ $item->idCustomer }}">{{ $item->First_Name }} {{ $item->Last_Name }} - {{ $item->Nic }} - {{ $item->Contact_No }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Agent Selection -->
                            <div class="col-lg-3 mb-3">
                                <label for="agent" class="form-label">Agent</label>
                                <select class="form-control select2" id="agent">
                                    <option value="0">All</option>
                                    @foreach($user as $item)
                                        <option value="{{ $item->id }}">{{ $item->Full_Name }} - {{ $item->email }} - {{ $item->TP }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Search Box -->
                            <div class="col-lg-3 mb-3">
                                <label for="simpleinput" class="form-label">Loan Number</label>
                                <select class="form-control select2" id="loan_number_search" onchange="load_payment_table()">
                                    <option value="0">All</option>
                                    @foreach($loan as $item)
                                        <option value="{{$item->idCustomer_Loan}}">{{ $item->Loan_No }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Search Button -->
                            <div class="col-lg-3 mb-3 d-flex align-items-end">
                                <button type="button" class="btn btn-danger" onclick="load_payment_table();">
                                    <i class="bi bi-search"></i> Search
                                </button>
                            </div>
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead  class="bg-purple">
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Center No</th>
                                    <th>Center Location</th>
                                    <th>Group No</th>
                                    <th>Customer Number</th>
                                    <th>Loan Number</th>
                                    <th>Customer Name</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                    <th>Amount</th>
                                    <th>Agent</th>
                                    <th>Comment</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Rows will be populated here by JavaScript -->
                                </tbody>
                            </table>

                        </div> <!-- end table-responsive-->

                        <div class="row mt-1 mb-1 p-2">
                            <div class="col-lg-12 d-flex align-items-center justify-content-between total-pending-container">
                                <div class="d-flex align-items-center">
                                    <div class="col-sm-12">
                                        <div>
                                            <span class="fw-bold">Total Collected Amount</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div>
                                            <span id="tot_amount"  class="fw-bold">0.00</span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-primary" onclick="printReceipt()">Print Report</button>
                                </div>
                            </div>
                        </div>


                    </div> <!-- end card-->

                </div> <!-- end col -->


            </div>
            <!-- end row -->




        </div> <!-- container -->

    </div>


    <div class="modal fade" id="addSlipModal" tabindex="-1" aria-labelledby="addSlipModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSlipModalLabel">Upload Slip</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="paymentId">
                    <div class="mb-3">
                        <label for="slipFile" class="form-label">Select Slip File</label>
                        <input type="file" id="slipFile" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="saveSlip()">Save</button>
                </div>
            </div>
        </div>
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
                            <div class="row mt-1 mb-2">
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Receipt</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <input type="file" id="file" class="form-control">
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




    <div id="overlay"></div>
    <div id="printerModal" class="modal_2">
        <div class="modal_2-content">
            <span class="close_2">&times;</span>
            <button onclick="printReceipt_view()" class="btn btn-danger">Print Receipt</button>
            <div class="printer-design">
                <div class="receipt">
                    <div class="header">
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" class="logo">
                        <h1>{{$company->company_name}}</h1>
                        <p>Contact Number: {{$company->contact_no}}</p>
                        <p> <span id="Inv_number">Receipt No. 001</span></p>
                    </div>
                    <hr>
                    <h2>Payment Receipt</h2>
                    <div class="details">
                        <p><strong>Customer Name:</strong> <span id="customer_name">John Doe</span></p>
                        <p><strong>Customer No:</strong> <span id="customer_number">001</span></p>
                        <p><strong>Loan Number:</strong> <span id="loan_number">001</span></p>
                        <p><strong>Payment Date:</strong> <span id="payment_date">001</span></p>
                        <p><strong>Payment Time:</strong> <span id="payment_time">001</span></p>
                    </div>
                    <hr>
                    <div class="payment-info">

                        <div class="item">
                            <span class="description">Loan Amount</span>
                            <span class="amount" id="loan_amount">10,000.00</span>
                        </div>
                        <div class="item">
                            <span class="description">Loan With Interest</span>
                            <span class="amount" id="full_loan_amount">10,000.00</span>
                        </div>
                        <div class="item">
                            <span class="description">Payed Amount</span>
                            <span class="amount"  id="payed_amount">1,000.00</span>
                        </div>

                    </div>
                    <hr>
                    <div class="payment-info">
                        <div class="item">
                            <span class="description">Balance Amount</span>
                            <b><span class="amount"  id="capital_balance">0.00</span></b>
                        </div>
                    </div>
                    <div class="payment-info balance">
                        <div class="item">
                            <span class="description">Penalty Balance</span>
                            <b><span class="amount"  id="panelty_balance">0.00</span></b>
                        </div>
                    </div>
                    <div class="payment-info balance">
                        <div class="item">
                            <span class="description">Total Balance</span>
                            <b><span class="amount"  id="tot_balance">9,000.00</span></b>
                        </div>
                    </div>
                    <div class="payment-info">
                        <div class="item">
                            <span class="description">Payment Type</span>
                            <b><span class="amount"  id="payment_type_view">-</span></b>
                        </div>
                    </div>
                    <div class="payment-info cheque-section" id="cheque_section" style="display:none; padding: 15px; border: 1px solid #ccc; border-radius: 8px;  background-color: #f9f9f9;">
                        <div class="item" style="display: flex; justify-content: space-between;">
                            <span class="description">Cheque No:</span>
                            <b><span class="amount" id="cheque_no">-</span></b>
                        </div>
                        <div class="item" style="display: flex; justify-content: space-between;">
                            <span class="description">Cheque Date:</span>
                            <b><span class="amount" id="cheque_date">-</span></b>
                        </div>
                        <div class="item" style="display: flex; justify-content: space-between;">
                            <span class="description">Name On Cheque:</span>
                            <b><span class="amount" id="cheque_name">-</span></b>
                        </div>
                        <div class="item" style="display: flex; justify-content: space-between;">
                            <span class="description">Cheque Type:</span>
                            <b><span class="amount" id="cheque_type">-</span></b>
                        </div>
                    </div>


                    <hr>
                    <div class="payment-info" id="loyalty_section">
                        <div class="item">
                            <span class="description">Loyalty Points</span>
                            <b><span class="amount"  id="loyalty_points">9,000.00</span></b>
                        </div>
                    </div>
                    <hr>
                    <br>
                    <div class="signature">
                        <p class="signature-line">________________________</p>
                        <span class="signature-line" id="signature">John Doe</span>
                    </div>
                    <hr>
                    <p class="thank-you">Thank you for your payment!</p>
                    <hr>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/re_payment.js?n=16"></script>
    <script>

        $(document).ready(function() {
            $('#customer_id').select2();
            $('#loyalty_section').hide();
            // Initialize or re-initialize DataTable
            $('#loan_table').DataTable({
                destroy: true,
                ordering: true,
                paging: false,
                searching: false,
                columnDefs: [{
                    orderable: false,
                    targets: -1 // Disable sorting on the last column (Action)
                }]
            });
        });



        // Function to print receipt (assuming it's defined elsewhere)
        function printReceipt_view() {
            window.print();
        }


        // Event listener for the close button within the modal
        document.querySelector('.close_2').addEventListener('click', function() {
            closeModal();
        });

        // Close the modal if the overlay is clicked
        document.getElementById('overlay').addEventListener('click', function(event) {
            if (event.target === this) {
                closeModal();
            }
        });

        // Optional: Close the modal on ESC key press
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });



        $(function() {

            let x = ["#payment_amount"];
            decimalFormat(x);
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

        })

        // Close the modal if the overlay is clicked
        document.getElementById('overlay').addEventListener('click', function() {
            closeModal();
        });

        function closeModal() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('printerModal').style.display = 'none';
        }

        function printReceipt() {
            // Gather selected values
            const center = document.getElementById('center_details').options[document.getElementById('center_details').selectedIndex].text;
            const group = document.getElementById('group').options[document.getElementById('group').selectedIndex].text;
            const customer = document.getElementById('customer_id').options[document.getElementById('customer_id').selectedIndex].text;
            const agent = document.getElementById('agent').options[document.getElementById('agent').selectedIndex].text;
            const date = document.getElementById('select_date').value;

            const totalPendingAmount = document.getElementById('tot_amount').innerText;

            // Gather table data (skip last column)
            let tableData = '';
            const tableRows = document.querySelectorAll('#loan_table tbody tr');
            tableRows.forEach(row => {
                const columns = row.querySelectorAll('td');
                tableData += `<tr>`;
                columns.forEach((col, index) => {
                    // Skip the last column (index starts from 0)
                    if (index < columns.length - 1) {
                        tableData += `<td>${col.innerHTML}</td>`;
                    }
                });
                tableData += `<td> </td>`;

                tableData += `</tr>`;
            });

            // Create a print-friendly format
            const printContent = `
<div style="text-align: center;">
    <h1>Repayment Collection Report</h1>
</div>
<div style="text-align: left;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 10%;"><strong>Date</strong></td>
            <td style="width: 90%;">${date}</td>
        </tr>
        <tr>
            <td style="width: 10%;"><strong>Customer</strong></td>
            <td style="width: 90%;">${customer}</td>
        </tr>
        <tr>
            <td style="width: 10%;"><strong>Center</strong></td>
            <td style="width: 90%;">${center}</td>
        </tr>
        <tr>
            <td style="width: 10%;"><strong>Group</strong></td>
            <td style="width: 90%;">${group}</td>
        </tr>
        <tr>
            <td style="width: 10%;"><strong>Agent</strong></td>
            <td style="width: 90%;">${agent}</td>
        </tr>
    </table>
    <br>
    <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; text-align: left;">
        <thead>
            <tr>
            <th style="text-align: center;">Center</th>
            <th style="text-align: center;">Group</th>
            <th style="text-align: center;">Customer Number</th>
            <th style="text-align: center;">Loan Number</th>
            <th style="text-align: center;">Customer Name</th>
            <th style="text-align: center;">Date</th>
            <th style="text-align: center;">Payment Method</th>
            <th style="text-align: center;">Amount</th>
            <th style="text-align: center;">Agent</th>
            <th style="text-align: center;">Status</th>
            <th style="text-align: center;">Comment</th>
            <th style="width: 10%; text-align: center;">Note</th>
            </tr>
        </thead>
        <tbody>
            ${tableData}
        </tbody>
    </table>
<div style="text-align: right; font-weight: bold;">
        <h3>Total Collected Amount: ${totalPendingAmount}</h3>
    </div>

</div>
`;

            const newWindow = window.open('', '', 'height=600,width=800');
            newWindow.document.write('<html><head><title>Print</title>');
            newWindow.document.write('</head><body>');
            newWindow.document.write(printContent);
            newWindow.document.write('</body></html>');
            newWindow.document.close();
            newWindow.print();
        }

    </script>


@endsection


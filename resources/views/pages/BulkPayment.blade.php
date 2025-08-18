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

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
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
        }
        .bg-purple th {
            color: #e1e1e1 !important; /* Ensure white text color for th elements */
        }


        .bg-purple {
            background-color: #1A2942 !important; /* Purple color */
            color: white !important; /* White text color */
        }


    </style>
    <style>
        .amount-column,
        #loan_table td.amount-column {
            width: 100px;
            text-align: right;
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
                    <h4 class="page-title">Add Bulk Payment</h4>
                </div>
            </div>
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
                                        <option value="0" selected>None</option>
                                        @foreach($center as $index => $item)
                                            <option value="{{ $item->idCenter }}">
                                                {{ $item->Name }}-{{ $item->Route }}
                                            </option>
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
                                    <label for="simpleinput" class="form-label">Status</label>
                                    <select class="form-control select2" id="status">
                                        <option value="-1">All</option>
                                        <option value="0">Pending</option>
                                        <option value="1">Today Collection</option>
                                        <option value="2">Late Payments</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Loan Number</label>
                                    <select class="form-control select2" id="loan_number_search">
                                        <option value="0">All</option>
                                        @foreach($loan as $item)
                                            <option value="{{$item->idCustomer_Loan}}">{{ $item->Loan_No }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <button type="button" class="btn btn-danger" onclick="load_payment_table();"><i class="bi bi-search"></i> </button>
                                </div>
                            </div>
                        </div>
                        <hr>
{{--                        <div class="container">--}}
                            <!-- Row for Totals -->
                            <div class="row align-items-center mt-3">
                                <!-- Total Today Installment -->
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold">Total Entered Amount:</span>
                                        <span id="tot_installment" class="ms-2 fs-5 fw-bold text-success">0.00</span>
                                    </div>
                                </div>

                                <!-- Total Entered Amount -->
                                <div class="col-md-6 text-end">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span class="fw-bold">Total Today Installment:</span>
                                        <span id="tot_amount" class="ms-2 fs-5 fw-bold text-primary">0.00</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bulk Payment Button -->
                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <input type="button" class="btn btn-warning" value="Bulk Payment" id="bulk_payment" onclick="automatePayments();">
                                    <input type="file" name="file" id="file" hidden>
                                </div>
                            </div>
                            <br>
                            <!-- Loan Table -->
                            <div class="row m-0 p-0">
                                <div class="card mb-3">
                                    <div class="card-body p-2">
                                        <h5 class="card-title mb-1">Selected Filters</h5>
                                        <div id="selected_filters_content" class="d-flex flex-wrap gap-2">
                                            <!-- Filters will be inserted here -->
                                        </div>
                                    </div>
                                </div>


                                <div class="col-12 p-0">
                                    <div class="table-responsive" style="width: 100%;">
                                        <table class="table table-striped table-bordered table-hover table-centered mb-0" id="loan_table" style="width: 100%;">
                                            <thead class="bg-light">
                                            <tr>
                                                <th scope="col">Loan No</th>
                                                <th scope="col">Member NIC</th>
                                                <th scope="col">Center</th>
                                                <th scope="col">Group</th>
                                                <th scope="col">Loan Amount</th>
                                                <th scope="col">Loan Balance</th>
                                                <th scope="col">Last Payment Amount</th>
                                                <th scope="col">Last Payment Date</th>
                                                <th scope="col">Today Installment</th>
                                                <th scope="col">Date</th>
                                                <th scope="col" class="amount-column">Amount</th>
                                                <th scope="col">Member Name</th>
                                                <th scope="col">Type</th>
                                            </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <!-- Pagination -->
                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <div id="pagination" class="d-flex justify-content-end"></div>
                                </div>
                            </div>
{{--                        </div>--}}


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
                    <h4 id="topic_2">Issue Loan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div >

                        <div>

                            <div class="row mt-1 mb-2" hidden>
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Payment Date</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <input type="date" id="payment_date" value="{{date('Y-m-d')}}" class="form-control">
                                    </div>
                                </div>
                            </div>

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


    <div class="modal fade" id="issue-loan-modal_2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 id="topic">Issue Loan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div >

                        <div>
                            <div class="row mt-1 mb-2">
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Total Pending Amount</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <span id="pending_amount" class="fw-bold"  style="color: red"></span>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Last Installment Date</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <span id="next_payment_date" class="fw-bold"></span>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Last Capital Payment Date</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <span id="last_payment_date" class="fw-bold"></span>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Days From Last Payment Date</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <span id="days_from_last_payment_date" class="fw-bold"></span>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-sm-6" hidden>

                                    <div>
                                        <span class="fw-bold">Interest Type</span>
                                    </div>

                                </div>
                                <div class="col-lg-5" hidden>
                                    <div>
                                        <span id="interest_type" class="fw-bold"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6" hidden>

                                    <div>
                                        <span class="fw-bold">Interest Rate</span>
                                    </div>

                                </div>
                                <div class="col-lg-5" hidden>
                                    <div>
                                        <span id="interest_rate" class="fw-bold"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6" hidden>

                                    <div>
                                        <span class="fw-bold">Rate For A Date</span>
                                    </div>

                                </div>
                                <div class="col-lg-5" hidden>
                                    <div>
                                        <span id="rate_date" class="fw-bold"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Installment Capital</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <span id="ins_capital" class="fw-bold"></span>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Installment Interest</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <span id="installment_interest" class="fw-bold"></span>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Installment Interest For Today</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <span id="installment_interest_today" class="fw-bold"></span>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Required Payment Before Capital Reduce</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <span id="required_payment_before_capital" class="fw-bold" style="color: red"></span>
                                    </div>
                                </div>
                                <br><br>
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Loan Capital Balance</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">
                                    <div>
                                        <span id="loan_capital_balance" class="fw-bold"></span>
                                    </div>
                                </div>



                                <br><br><br>
                                <hr>
                                <div class="col-sm-6" hidden>

                                    <div>
                                        <span class="fw-bold">Payment Date</span>
                                    </div>

                                </div>
                                <div class="col-lg-5" hidden>
                                    <div>
                                        <input type="date" id="payment_date_2" value="{{date('Y-m-d')}}" class="form-control">
                                    </div>
                                </div>
                                <br><br><br>
                                <div class="col-sm-6">

                                    <div>
                                        <span class="fw-bold">Paid Amount(LKR)</span>
                                    </div>

                                </div>
                                <div class="col-lg-5">

                                    <input type="hidden" id="cus_id_2"  class="form-control">
                                    <input type="hidden" id="ins_id"  class="form-control">
                                    <div>
                                        <input type="text" id="payment_amount_2" class="form-control">
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
                    <input type="hidden" id="reduce_balance_loan_id">
                    <div class="modal-footer d-flex justify-content-between align-items-center">
                        <a type="button" href="#" class="btn btn-primary" onclick="check_reduce()">Reduce Capital</a>
                        <div>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-success" onclick="payment_2()">Pay</button>
                        </div>
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




    <div id="overlay" class="overlay"></div>
    <div id="printerModal" class="modal_2">
        <div class="modal_2-content">
            <span class="close_2">&times;</span>
            <div class="printer-design">
                <div class="receipt">
                    <div class="header">
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" class="logo">
                        <h1>{{$company->company_name}}</h1>
                        <p>Contact Number: {{$company->contact_no}}</p>
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
                            <span class="description">Payed Amount</span>
                            <span class="amount"  id="payed_amount">1,000.00</span>
                        </div>
                    </div>
                    <hr>
                    <div class="payment-info">
                        <div class="item">
                            <span class="description">Balance Amount</span>
                            <b><span class="amount"  id="capital_balance">9,000.00</span></b>
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
            <button onclick="printReceipt()">Print Receipt</button>
        </div>
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
    <script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/bulk_payment.js?n=18"></script>
    <script>
        $('#loan_number_search').on('change', function () {
            load_payment_table();
        });

        function load_payment_reciept(id){
            // document.getElementById('issue-loan-modal').style.display = 'none';
            // document.getElementById('issue-loan-modal_2').style.display = 'none';
            openModal();
            $('.btn-success').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "/view_payment_load_reciept/" + id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    console.log(data);
                    let customer = data.customer;
                    let payment = data.payment;
                    let loan = data.loan;
                    let user = data.user;

                    if (xhr.status === 200) {
                        $("#customer_name").text(customer.First_Name+" "+customer.Last_Name);
                        $("#customer_number").text(customer.cus_number);
                        $("#loan_number").text(loan.Loan_No);
                        $("#payment_date").text(payment.Date);
                        $("#payment_time").text(payment.time);

                        $("#loan_amount").text(parseFloat(loan.Amount).toFixed(2));
                        $("#payed_amount").text(parseFloat(payment.Amount).toFixed(2));
                        $("#capital_balance").text(parseFloat(loan.Balance_Amount).toFixed(2));

                        $("#signature").text(user.Full_Name);
                    }
                }
            });
        }

        function openModal() {
            document.getElementById('overlay').style.display = 'block';
            document.getElementById('printerModal').style.display = 'block';
        }

        // Function to print the receipt
        function printReceipt() {
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

        function closeModal() {
            window.location.reload();
        }

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

        function check_reduce(){
            let pending_amount = parseFloat($("#pending_amount").text());
            let next_payment_date = $("#next_payment_date").text();
            let days_from_last_payment_date = $("#days_from_last_payment_date").text();
            let ins_capital = $("#ins_capital").text();
            let installment_interest = $("#installment_interest").text();
            let installment_interest_today = $("#installment_interest_today").text();
            let required_payment_before_capital = $("#required_payment_before_capital").text();
            let reduce_balance_loan_id = $("#reduce_balance_loan_id").val();

            if (pending_amount === 0) {
                $.ajax({
                    type: "POST",
                    url: "/reduce_capital",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        reduce_balance_loan_id: reduce_balance_loan_id,
                        pending_amount: pending_amount,
                        next_payment_date: next_payment_date,
                        days_from_last_payment_date: days_from_last_payment_date,
                        ins_capital: ins_capital,
                        installment_interest: installment_interest,
                        installment_interest_today: installment_interest_today,
                        required_payment_before_capital: required_payment_before_capital,
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            window.location.href = response.redirect_url;
                        } else {
                            Swal.fire("Error!", response.message, "error");
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire("Error!", "Something went wrong!", "error");
                    }
                });
            } else {
                Swal.fire("Error!", "You have to complete pending payments before reducing capital!", "error");
            }
        }

    </script>


@endsection


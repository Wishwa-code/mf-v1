@extends('layout.admin')

@section('head')
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


@endsection


@section('content')

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Date Wise Installment View</h4>
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
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Date From</label>
                                        <input type="date" class="form-control" id="select_date" name="select_date" value="{{date('Y-m-d')}}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Date To</label>
                                        <input type="date" class="form-control" id="select_date_to" name="select_date_to" value="{{date('Y-m-d')}}">
                                    </div>

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
                                    <button type="button" class="btn btn-danger" onclick="load_payment_table();"><i class="bi bi-search"></i> </button>
                                </div>
                            </div>
                        </div>
                        <hr>

                        <div class="table-responsive-sm">
                            <table class="table table-centered mb-0" id="loan_table">
                                <thead class="bg-purple">
                                <tr>
                                    <th>Date</th>
                                    <th>Center No</th>
                                    <th>Group No</th>
                                    <th>Customer Number</th>
                                    <th>Loan Number</th>
                                    <th>Customer Name</th>
                                    <th>Ins. Amount</th>
                                    <th>Total Balance</th>

                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div> <!-- end table-responsive-->

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



    <div id="overlay"></div>
    <div id="printerModal" class="modal_2">
        <div class="modal_2-content">
            <span class="close_2">&times;</span>
            <div class="printer-design">
                <div class="receipt">
                    <div class="header">
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" class="logo">
                        <h1>{{$company->company_name}}</h1>
                        <p>Contact Number: {{$company->contact_no}}</p>
{{--                        customer name--}}
{{--                        customer number--}}
{{--                        loan number--}}
{{--                        loan amount--}}


{{--                        paid amount--}}
{{--                        paid date and time--}}
{{--                        capital balance--}}

{{--                        Agent id--}}
{{--                        Agent Name--}}


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
                    <div class="payment-info">
                        <div class="item">
                            <span class="description">Payment Type</span>
                            <b><span class="amount"  id="payment_type_view">-</span></b>
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
    <script src="../JS/validate.js"></script>
    <script src="../JS/datewiseinstallment.js?n=5"></script>
    <script>



        // Function to print receipt (assuming it's defined elsewhere)
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

    </script>


@endsection


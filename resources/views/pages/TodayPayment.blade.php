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
            width: 80mm; /* Thermal printer width */
            height: auto;
            background: white;
            margin: 0;
            padding: 0;
        }
        .modal-content {
            width: 80mm;
            border: none;
        }
        .receipt {
            width: 100%;
            padding: 5px;
            font-size: 11px; /* smaller font size to fit */
        }
        .receipt * {
            page-break-inside: avoid; /* prevent page break inside */
        }
        @page {
            size: A4 portrait;
            margin: 10mm;
        }

        .printer-design button {
            display: none;
        }
    }

    .bg-purple th {
        color: #e1e1e1 !important;
    }

    .bg-purple {
        background-color: #1A2942 !important;
        color: white !important;
    }
    .table th{
        padding: 10px !important; /* Adjust the padding as needed */
    }

</style>

@endsection


@section('content')
    <div id="loadingSpinner" style="
    display: none;
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(2px);
    justify-content: center;
    align-items: center;
">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                    </div>
                    <h4 class="page-title">Add Re-Payment</h4>
                    <label style="color: red">Upload Excel</label>
                    <input type="file" id="uploadExcel" accept=".xlsx, .xls" class="form-control mb-2 w-50">

                    <!-- Upload button, aligned below the file input -->
                    <input type="button" onclick="upload_excel()" class="btn btn-success mt-2" value="Upload">
                </div>
                <div id="progressWrapper" style="display:none; margin-top:20px;">
                    <label>Uploading Excel...</label>
                    <div style="background-color: #e0e0e0; border-radius: 10px; overflow: hidden;">
                        <div id="uploadProgress" style="height: 20px; width: 0%; background-color: #4caf50;"></div>
                    </div>
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
                                        <option value="0">All</option>
                                        @foreach($center as $item)
                                            <option value="{{$item->idCenter}}">{{ $item->Name }}-{{ $item->Route }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3">
                                <div class="mb-3">
                                    <label for="group" class="form-label">Group</label>
                                    <select class="form-control select2" id="group">
                                        <option value="0">All</option>
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
                                    <select class="form-control select2" id="loan_number_search" onchange="load_payment_table()">
                                        <option value="0">All</option>
                                        @foreach($loan as $item)
                                            <option value="{{$item->idCustomer_Loan}}">{{ $item->Loan_No }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3" hidden>
                                <div class="mb-3">
                                    <label for="simpleinput" class="form-label">Member Name Type</label>
                                    <select class="form-control select2" id="mem_name_type" onchange="load_payment_table()">
                                        <option value="0" {{ config('app.settings.payment_member_name') == 'with_initial' ? 'selected' : '' }}>
                                            With Initial
                                        </option>
                                        <option value="1" {{ config('app.settings.payment_member_name') == 'full_name' ? 'selected' : '' }}>
                                            Full Name
                                        </option>
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
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover table-centered mb-0" id="loan_table" style="padding: 0!important;">
                                <thead>
                                <tr>
{{--                                    <th scope="col">Loan Id</th>--}}
                                    <th scope="col">Loan No</th>
{{--                                    <th scope="col">Center No</th>--}}
{{--                                    <th scope="col">Group No</th>--}}
                                    <th scope="col">Member Name</th>
                                    <th scope="col">Member No</th>

                                    <th scope="col">Loan Amount</th>
                                    <th scope="col">Due Amount</th>
{{--                                    <th scope="col">Total Loan Balance</th>--}}
{{--                                    <th scope="col">Balance Until Today</th>--}}
                                    <th scope="col">Today Installment</th>
{{--                                    <th scope="col">Arrease</th>--}}
{{--                                    <th scope="col">Total Paid Amount</th>--}}
                                    <th scope="col">Action</th>
                                    <th scope="col">Member NIC</th>
                                    <th scope="col">Type</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div id="pagination" class="d-flex justify-content-end mt-3 me-3"></div>
                        </div>

                        <div class="row mt-3 mb-3 p-3 bg-white rounded shadow-sm border">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="text-dark fw-bold" style="width: 180px;">
                                        Total Loan Balance
                                    </div>
                                    <div class="ms-2">
                                        <span id="tot_amount" class="text-dark fw-bold" >0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="text-dark fw-bold" style="width: 180px;">
                                        Balance Until Today
                                    </div>
                                    <div class="ms-2">
                                        <span id="balance_until" class="text-dark fw-bold">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="text-dark fw-bold" style="width: 180px;">
                                        Today's Installment
                                    </div>
                                    <div class="ms-2">
                                        <span id="today_installment" class="text-dark fw-bold">0.00</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="text-dark fw-bold" style="width: 180px;">
                                        Total Arrears
                                    </div>
                                    <div class="ms-2">
                                        <span id="total_arrease" class="text-dark fw-bold">0.00</span>
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
                    <h4 class="modal-title" id="topic_2">Issue Loan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="payment_date" class="form-label fw-bold">Total Arrease</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="today_arrese" class="form-control" readonly disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="payment_date" class="form-label fw-bold">Today Installment</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="today_installment_loan" class="form-control" readonly disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="payment_date" class="form-label fw-bold">Total Outstanding</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="total_outstanding" class="form-control" readonly disabled>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="payment_date" class="form-label fw-bold">Total Loan Balance</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="total_loan_balance" class="form-control" readonly disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="payment_date" class="form-label fw-bold">Savings Balance</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="total_savings_balance" class="form-control" readonly disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="payment_date" class="form-label fw-bold">Installment Amount</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="ins_amount" class="form-control" readonly disabled>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="tot_paid_amount" class="form-label fw-bold">Total Paid Amount</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="tot_paid_amount" class="form-control" readonly disabled>
                            </div>
                        </div>



                        <!-- Payment Date and Type Section -->
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="payment_date" class="form-label fw-bold">Payment Date</label>
                            </div>
                            <input type="hidden" id="loan_balance"  class="form-control">
                            <div class="col-sm-8">
                                <input type="date" id="payment_date"
                                       value="{{ date('Y-m-d') }}" class="form-control"
                                       inputmode="none" onkeydown="return false" onpaste="return false">

                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="payment_type" class="form-label fw-bold">Payment Type</label>
                            </div>
                            <div class="col-sm-8">
                                <select class="form-control" id="payment_type"></select>
                            </div>
                        </div>

                        <hr>
                        <!-- Bank Account Section -->
                        <div class="row mb-3" id="bank_account_section" style="display: none;">
                            <div class="col-sm-4">
                                <label for="bank_account_company" class="form-label fw-bold">Bank Account</label>
                            </div>
                            <div class="col-sm-12">
                                <select class="form-control" id="bank_account_company">
                                    @foreach($banks as $bank)
                                        @if($bank->Account_No!="Cash")
                                            <option value="{{ $bank->Idbank }}" data-account-no="{{ $bank->Account_No }}">{{ $bank->Account_No }} - {{ $bank->Account_Name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Cheque Details Section -->
                        <div class="row mb-3" id="cheque_issue_bank_section" style="display: none;">
                            <div class="col-sm-4">
                                <label for="cheque_issue_bank" class="form-label fw-bold">Cheque Issue Bank</label>
                            </div>
                            <div class="col-sm-12">
                                <select class="form-control" id="cheque_issue_bank">
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->Idbank }}">{{ $bank->Account_No }} - {{ $bank->Account_Name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3" id="cheque_details_section" style="display: none;">
                            <div class="col-sm-4">
                                <label for="name_on_cheque" class="form-label fw-bold">Name on the Cheque</label>
                            </div>
                            <div class="col-sm-12">
                                <input type="text" id="name_on_cheque" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3" id="cheque_number_section" style="display: none;">
                            <div class="col-sm-4">
                                <label for="chq_number" class="form-label fw-bold">Cheque Number</label>
                            </div>
                            <div class="col-sm-12">
                                <input type="text" id="chq_number" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3" id="cheque_date_section" style="display: none;">
                            <div class="col-sm-4">
                                <label for="chq_date" class="form-label fw-bold">Cheque Date</label>
                            </div>
                            <div class="col-sm-12">
                                <input type="date" id="chq_date" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3" id="cheque_type_section" style="display: none;">
                            <div class="col-sm-4">
                                <label for="chq_type" class="form-label fw-bold">Cheque Type</label>
                            </div>
                            <div class="col-sm-12">
                                <select class="form-control" id="chq_type">
                                    <option value="Crossed">Crossed</option>
                                    <option value="Bearer">Bearer</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <!-- Payment Amount and Receipt Section -->
                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="payment_amount" class="form-label fw-bold">Paid Amount (LKR)</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="hidden" id="cus_id" class="form-control">
                                <input type="hidden" id="ins_id" class="form-control">
                                <input type="text" id="payment_amount" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3" id="savings_section" style="display: none;">
                            <div class="col-sm-4">
                                <label for="saving_amount" class="form-label fw-bold">Savings Amount (LKR)</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="text" id="saving_amount" class="form-control">
                            </div>
                        </div>


                        <div class="row mb-3">
                            <div class="col-sm-4">
                                <label for="file" class="form-label fw-bold">Receipt</label>
                            </div>
                            <div class="col-sm-8">
                                <input type="file" id="file" class="form-control">
                                <button type="button" onclick="openGlobalCamera('#file')" class="btn btn-outline-secondary mt-1">📷</button>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" onclick="payment()">Pay</button>
                </div>
            </div>
        </div>
    </div>





    <div class="modal fade" id="issue-loan-modal_2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="topic">Issue Loan</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div>
                                    <span class="fw-bold">Total Outstanding Amount:</span>
                                    <span id="pending_amount" class="fw-bold" style="color: red;"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <span class="fw-bold">Loan Capital Balance:</span>
                                    <span id="loan_capital_balance" class="fw-bold"></span>
                                </div>
                            </div>

                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div>
                                    <span class="fw-bold">Last Capital Payment Date:</span>
                                    <span id="last_payment_date" class="fw-bold"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <span class="fw-bold">Installment Interest:</span>
                                    <span id="installment_interest" class="fw-bold"></span>
                                </div>

                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div hidden>
                                    <span class="fw-bold">Installment Capital:</span>
                                    <span id="ins_capital" class="fw-bold"></span>
                                </div>
                                <div class="col-md-8">
                                    <div>
                                        <span class="fw-bold">Last Installment Date:</span>
                                        <span id="next_payment_date" class="fw-bold"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div>

                                    <span class="fw-bold">Days From Last Payment Date:</span>
                                    <span id="days_from_last_payment_date" class="fw-bold"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div>
                                    <span class="fw-bold">Installment Interest For Today:</span>
                                    <span id="installment_interest_today" class="fw-bold"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div>
                                    <span class="fw-bold">Required Payment Before Capital Reduce:</span>
                                    <span id="required_payment_before_capital" class="fw-bold" style="color: red;"></span>
                                </div>
                            </div>
                        </div>




                        <input type="hidden" id="loan_balance_2"  class="form-control">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div>
                                    <label for="payment_date_2" class="form-label fw-bold">Payment Date:</label>
                                    <input type="date" id="payment_date_2"
                                           value="{{ date('Y-m-d') }}" class="form-control"
                                           inputmode="none" onkeydown="return false" onpaste="return false" >

                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="payment_type_2" class="form-label fw-bold">Payment Type:</label>
                                @if($collector==1)
                                    <select class="form-control" id="payment_type_2" onchange="togglePaymentSections_2(this.value)">
                                        <option value="Cash" >Cash</option>
                                        <option value="Bank Deposit">Bank Deposit</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Collector" selected>Collector</option>
                                    </select>
                                @elseif($cashier==1)
                                    <select class="form-control" id="payment_type_2" onchange="togglePaymentSections_2(this.value)">
                                        <option value="Cash">Cash</option>
                                        <option value="Bank Deposit">Bank Deposit</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Cashier" selected>Cashier</option>
                                    </select>
                                @else
                                    <select class="form-control" id="payment_type_2" onchange="togglePaymentSections_2(this.value)">
                                        <option value="Cash" selected>Cash</option>
                                        <option value="Bank Deposit">Bank Deposit</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                @endif
                            </div>
                        </div>

                        <hr>
                        <!-- Bank Account Section -->
                        <div class="row mb-3" id="bank_account_section_2" style="display: none;">
                            <div class="col-md-12">
                                <label for="bank_account_company_2" class="form-label fw-bold">Bank Account:</label>
                                <select class="form-control" id="bank_account_company_2">
                                    @foreach($banks as $bank)
                                        @if($bank->Account_No!="Cash")
                                            <option value="{{ $bank->Idbank }}" data-account-no="{{ $bank->Account_No }}">{{ $bank->Account_No }} - {{ $bank->Account_Name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Cheque Details Section -->
                        <div class="row mb-3" id="cheque_issue_bank_section_2" style="display: none;">
                            <div class="col-md-12">
                                <label for="cheque_issue_bank_2" class="form-label fw-bold">Cheque Issue Bank:</label>
                                <select class="form-control" id="cheque_issue_bank_2">
                                    @foreach($banks as $bank)
                                        <option value="{{ $bank->Idbank }}">{{ $bank->Account_No }} - {{ $bank->Account_Name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3" id="cheque_details_section_2" style="display: none;">
                            <div class="col-md-12">
                                <label for="name_on_cheque_2" class="form-label fw-bold">Name on the Cheque:</label>
                                <input type="text" id="name_on_cheque_2" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3" id="cheque_number_section_2" style="display: none;">
                            <div class="col-md-12">
                                <label for="chq_number_2" class="form-label fw-bold">Cheque Number:</label>
                                <input type="text" id="chq_number_2" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3" id="cheque_date_section_2" style="display: none;">
                            <div class="col-md-12">
                                <label for="chq_date_2" class="form-label fw-bold">Cheque Date:</label>
                                <input type="date" id="chq_date_2" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3" id="cheque_type_section_2" style="display: none;">
                            <div class="col-md-12">
                                <label for="chq_type_2" class="form-label fw-bold">Cheque Type:</label>
                                <select class="form-control" id="chq_type_2">
                                    <option value="Crossed">Crossed</option>
                                    <option value="Bearer">Bearer</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div>
                                    <span class="fw-bold">Paid Amount (LKR):</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" id="cus_id_2" class="form-control">
                                <input type="hidden" id="ins_id" class="form-control">
                                <input type="text" id="payment_amount_2" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div>
                                    <span class="fw-bold">Receipt:</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="file" id="file" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="me-auto">
                        <input type="hidden" id="reduce_balance_loan_id">
                        <a href="#" class="btn btn-primary" onclick="check_reduce()">Reduce Capital</a>
                    </div>
                    <div>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" onclick="payment_2()">Pay</button>
                    </div>
                </div>


            </div>
        </div>
    </div>



    <div class="modal fade" id="view_details_payment" tabindex="-1" role="dialog" aria-labelledby="viewDetailsPaymentLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content shadow-lg rounded-3">
                <div class="modal-header border-bottom-0">
                    <h4 class="modal-title" id="viewDetailsPaymentLabel">View Selected Loan Balance Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="fw-bold text-dark">Total Capital Balance</span>
                                    <div class="p-2 rounded bg-light border">
                                        <span id="total_capital_balance" class="fw-bold text-dark"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="fw-bold text-dark">Total Interest Balance</span>
                                    <div class="p-2 rounded bg-light border">
                                        <span id="total_interest_balance" class="fw-bold text-dark"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="fw-bold text-dark">Total Penalty Balance</span>
                                    <div class="p-2 rounded bg-light border">
                                        <span id="total_penalty_balance" class="fw-bold text-dark"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <span class="fw-bold text-dark">Total Saving Amount</span>
                                    <div class="p-2 rounded bg-light border">
                                        <span id="total_due_saving_amount" class="fw-bold text-dark"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <span class="fw-bold text-dark">Total Balance Amount</span>
                                    <div class="p-2 rounded bg-light border">
                                        <span id="total_balance_amount" class="fw-bold text-dark"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>





    <div class="modal fade" id="standard-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header pt-2">
                    <h4>Installment Log</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="table-responsive">
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


            <div class="printer-design">
                <div class="receipt">
                    <div class="header">
                        <img src="{{ asset('storage/' . $company->logo) }}" alt="Company Logo" class="logo">
                        <h1>{{$company->company_name}}</h1>
                        <p>Contact Number: {{$company->contact_no}}</p>
                        <p> <span id="Inv_number">INV001</span></p>
                    </div>
                    <hr>
                    <h2>Payment Receipt</h2>
                    <div class="details">
                        <p><strong>Customer Name:</strong> <span id="customer_name">John Doe</span></p>
                        <p><strong>Customer No:</strong> <span id="customer_number">001</span></p>
                        <p><strong>Loan Number:</strong> <span id="loan_number">001</span></p>
                        <p><strong>Payment Date:</strong> <span id="payment_date_view">001</span></p>
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
                            <span class="description" for="payed_amount">Payed Amount</span>
                            <span class="amount"  id="payed_amount">1,000.00</span>
                        </div>
                        <div class="item" id="saving_amount_container">

                        </div>
                    </div>
                    <hr>
                    <div class="payment-info">
                        <div class="item">
                            <span class="description">Balance Amount</span>
                            <b><span class="amount"  id="capital_balance">9,000.00</span></b>
                        </div>
                    </div>
                    <div class="payment-info balance">
                        <div class="item">
                            <span class="description">Penalty Balance</span>
                            <b><span class="amount"  id="panelty_balance">9,000.00</span></b>
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
                <span class="close_2">&times;</span>
                <button onclick="printReceipt_view()" class="btn btn-danger">Print Receipt</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="loanCommentModal" tabindex="-1" aria-labelledby="loanLogModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loanLogModalLabel">Loan Comment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="loan_id_comment">
                <div class="modal-body">
                    <div class="mt-3">
                        <h6>Add a Comment</h6>
                        <textarea id="commentText" class="form-control" rows="3" placeholder="Enter your comment here"></textarea>
                        <button class="btn btn-primary mt-2" id="submitComment">Add Loan Comment</button>
                    </div>
                    <br>
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-striped" id="loanCommentTable">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Description</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- AJAX loaded data will go here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Comment Section -->

                </div>
            </div>
        </div>
    </div>


@endsection

@section('script')

    {{-- Vendor / app scripts you already use --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="../JS/validate.js"></script>
    <script src="../JS/today_payment.js?n=29"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ids = ["payment_date", "payment_date_2"];
            const allowBackdate = window.APP_SETTINGS?.payment_backdate === "enabled";
            const today = new Date().toISOString().split("T")[0];

            let minDate = today;
            if (allowBackdate) {
                const lastYear = new Date();
                lastYear.setFullYear(lastYear.getFullYear() - 1);
                minDate = lastYear.toISOString().split("T")[0];
            }

            ids.forEach(id => {
                const el = document.getElementById(id);
                if (!el) return;

                el.setAttribute("max", today);
                el.setAttribute("min", minDate);

                // ✅ Always disable typing/pasting (regardless of backdate)
                el.addEventListener('keydown', e => e.preventDefault());
                el.addEventListener('keypress', e => e.preventDefault());
                el.addEventListener('keyup', e => e.preventDefault());
                el.addEventListener('paste', e => e.preventDefault());
                el.addEventListener('input', e => e.preventDefault());
                el.setAttribute("inputmode", "none"); // hides keyboard on mobile

                // ✅ Allow picker to open normally
                el.addEventListener('focus', () => {
                    if (typeof el.showPicker === "function") el.showPicker();
                });

                // ✅ Clamp to min/max if changed through picker
                el.addEventListener('change', () => {
                    if (!el.value) return;
                    if (el.value > today)   el.value = today;
                    if (el.value < minDate) el.value = minDate;
                });
            });
        });
    </script>



    <script>
        // collectorId from session
        const COLLECTOR_ID = {{ (int) session('userid') }};

        function selectCollectorBankByAccountNo(suffix) {
            const sel = document.getElementById(`bank_account_company${suffix}`);
            if (!sel) return;

            const wanted = String(COLLECTOR_ID);
            for (const opt of sel.options) {
                if (String(opt.dataset.accountNo) === wanted) {
                    sel.value = opt.value;
                    break;
                }
            }
        }

        function setBankSelectDisabled(suffix, disabled) {
            const sel = document.getElementById(`bank_account_company${suffix}`);
            if (sel) sel.disabled = !!disabled;
        }

        // Generic toggler used by both
        function togglePaymentSectionsGeneric(value, suffix) {
            const bankAccountSection        = document.getElementById(`bank_account_section${suffix}`);
            const chequeDetailsSection      = document.getElementById(`cheque_details_section${suffix}`);
            const chequeNumberSection       = document.getElementById(`cheque_number_section${suffix}`);
            const chequeDateSection         = document.getElementById(`cheque_date_section${suffix}`);
            const chequeTypeSection         = document.getElementById(`cheque_type_section${suffix}`);
            const chequeIssueBankSection    = document.getElementById(`cheque_issue_bank_section${suffix}`);

            [bankAccountSection, chequeDetailsSection, chequeNumberSection,
                chequeDateSection, chequeTypeSection, chequeIssueBankSection]
                .forEach(el => { if (el) el.style.display = 'none'; });

            // Reset disable state by default
            setBankSelectDisabled(suffix, false);

            if (value === 'Bank Deposit' || value === 'Collector' || value === 'Cashier') {
                if (bankAccountSection) bankAccountSection.style.display = 'block';

                if (value === 'Collector') {
                    selectCollectorBankByAccountNo(suffix);
                    setBankSelectDisabled(suffix, true);   // 🔒 disable when Collector
                }
            } else if (value === 'Cheque') {
                if (chequeDetailsSection)   chequeDetailsSection.style.display   = 'block';
                if (chequeNumberSection)    chequeNumberSection.style.display    = 'block';
                if (chequeDateSection)      chequeDateSection.style.display      = 'block';
                if (chequeTypeSection)      chequeTypeSection.style.display      = 'block';
                if (chequeIssueBankSection) chequeIssueBankSection.style.display = 'block';
            }
        }

        // Keep your original names
        function togglePaymentSections(value)   { togglePaymentSectionsGeneric(value, ""); }
        function togglePaymentSections_2(value) { togglePaymentSectionsGeneric(value, "_2"); }

        // If default is already Collector on load, auto-pick & disable
        document.addEventListener("DOMContentLoaded", function () {
            const s1 = document.getElementById("payment_type");
            const s2 = document.getElementById("payment_type_2");

            if (s1?.value === "Collector") {
                selectCollectorBankByAccountNo("");
                setBankSelectDisabled("", true);
            }
            if (s2?.value === "Collector") {
                selectCollectorBankByAccountNo("_2");
                setBankSelectDisabled("_2", true);
            }
        });
    </script>



    <script>
        /*** Build Payment Type <select>
         *  - If isCollector==1 => use APP_SETTINGS.collector_txn_modes
         *  - Else               => show ALL options
         ***/
        function buildPaymentTypeSelect(selectId, isCollector, isCashier, onChangeFnName) {
            const sel = document.getElementById(selectId);
            if (!sel) return;

            const ALL = ["Cash", "Bank Deposit", "Cheque", "Collector", "Cashier"];

            let allowed = [];

            if (Number(isCollector) === 1) {
                // Respect APP_SETTINGS when the logged user is a collector
                const modesRaw = window.APP_SETTINGS?.collector_txn_modes || "[]";
                let modes = [];
                try {
                    const parsed = JSON.parse(modesRaw);
                    modes = Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    modes = String(modesRaw).split(',').map(s => s.trim()).filter(Boolean);
                }

                // Map config booleans to concrete options
                if (modes.includes("cash_bank"))    allowed.push("Cash");
                if (modes.includes("bank_deposit")) allowed.push("Bank Deposit");
                if (modes.includes("cheques"))      allowed.push("Cheque");
                if (modes.includes("collector_account")) allowed.push("Collector");

                // If cashier role is present, allow Cashier explicitly
                if (Number(isCashier) === 1) allowed.push("Cashier");

                // Fallback: if config empty/bad, allow Cash at least
                if (!allowed.length) allowed = ["Cash"];
            } else {
                // NOT a collector → show ALL options, regardless of APP_SETTINGS
                allowed = ALL.slice();
            }

            // Fill options
            sel.innerHTML = "";
            allowed.forEach(type => {
                const opt = document.createElement("option");
                opt.value = type;
                opt.textContent = type;
                sel.appendChild(opt);
            });

            // Default selection:
            if (Number(isCollector) === 1) {
                // For collectors prefer "Collector" if allowed, else first option
                sel.value = allowed.includes("Collector") ? "Collector" : allowed[0];
            } else {
                // Non-collectors default to Cash
                sel.value = allowed.includes("Cash") ? "Cash" : allowed[0];
            }

            // Bind change + trigger once
            if (onChangeFnName && typeof window[onChangeFnName] === "function") {
                sel.onchange = function () { window[onChangeFnName](this.value); };
                window[onChangeFnName](sel.value);
            }
        }

        // Build both selects on DOM ready
        document.addEventListener("DOMContentLoaded", function() {
            buildPaymentTypeSelect("payment_type",   {{ (int) $collector }}, {{ (int) $cashier }}, "togglePaymentSections");
            buildPaymentTypeSelect("payment_type_2", {{ (int) $collector }}, {{ (int) $cashier }}, "togglePaymentSections_2");
        });
    </script>


    <script>
        /*** Client-side validation before Pay ***/
        function validatePaymentTypeBlock(suffix) {
            const type = document.getElementById(`payment_type${suffix}`)?.value || "Cash";

            // Bank/Collector/Cashier require a company bank account
            if (type === "Bank Deposit" || type === "Collector" || type === "Cashier") {
                const bankSel = document.getElementById(`bank_account_company${suffix}`);
                if (!bankSel || !bankSel.value) {
                    Swal.fire("Missing Bank Account", "Please select a bank account.", "warning");
                    return false;
                }
            }

            // Cheque requires all fields
            if (type === "Cheque") {
                const bank   = document.getElementById(`cheque_issue_bank${suffix}`);
                const nameOn = document.getElementById(`name_on_cheque${suffix}`);
                const no     = document.getElementById(`chq_number${suffix}`);
                const date   = document.getElementById(`chq_date${suffix}`);
                const chqT   = document.getElementById(`chq_type${suffix}`);

                if (!bank?.value)   { Swal.fire("Cheque", "Please select Cheque Issue Bank.", "warning"); return false; }
                if (!nameOn?.value) { Swal.fire("Cheque", "Please enter Name on the Cheque.", "warning"); return false; }
                if (!no?.value)     { Swal.fire("Cheque", "Please enter Cheque Number.", "warning"); return false; }
                if (!date?.value)   { Swal.fire("Cheque", "Please select Cheque Date.", "warning"); return false; }
                if (!chqT?.value)   { Swal.fire("Cheque", "Please select Cheque Type.", "warning"); return false; }
            }

            return true;
        }

        // Hook into your existing functions from today_payment.js (if present)
        (function hookPayments() {
            const makeHook = (fnName, suffix) => {
                const orig = window[fnName];
                window[fnName] = function() {
                    if (!validatePaymentTypeBlock(suffix)) return;
                    if (typeof orig === "function") {
                        return orig.apply(this, arguments);
                    } else {
                        console.warn(fnName + " is not defined yet.");
                    }
                };
            };
            makeHook('payment',  '');
            makeHook('payment_2','_2');
        })();
    </script>

    <script>
        /*** Comments, spinner, Select2, receipt printing, etc. ***/
        $(document).ready(function() {
            // Hide loyalty section by default
            $('#loyalty_section').hide();

            // Number formatting helper for amount fields
            decimalFormat(["#payment_amount"]);

            // Initialize Select2
            $('.select2').select2();
            $('.select2bs4').select2({ theme: 'bootstrap4' });

            // Submit loan comment
            $('#submitComment').click(function() {
                const comment = $('#commentText').val();
                const loanId  = $("#loan_id_comment").val();

                if (!comment) {
                    Swal.fire('Error', 'Comment cannot be empty', 'error');
                    return;
                }

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to submit this comment?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: "{{ route('loan.comment.store') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            comment: comment,
                            loan_id: loanId
                        },
                        success: function() {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Comment has been submitted!",
                            }).then(function () {
                                window.location.reload();
                            });
                        },
                        error: function() {
                            Swal.fire('Error', 'Something went wrong', 'error');
                        }
                    });
                });
            });

            // Load comments when modal opens
            $('#loanCommentModal').on('show.bs.modal', function() {
                const loanId = $("#loan_id_comment").val();
                $('#loanCommentTable tbody').empty();

                $.ajax({
                    url: "{{ route('loan.comment.fetch') }}",
                    type: "GET",
                    data: { loan_id: loanId },
                    success: function(response) {
                        if ((response.comments || []).length) {
                            response.comments.forEach(comment => {
                                $('#loanCommentTable tbody').append(`
                                    <tr>
                                        <td>${comment.date}</td>
                                        <td>${comment.time}</td>
                                        <td>${comment.comment}</td>
                                    </tr>
                                `);
                            });
                        } else {
                            $('#loanCommentTable tbody').append(`
                                <tr>
                                    <td colspan="3" class="text-center">No comments available</td>
                                </tr>
                            `);
                        }
                    },
                    error: function() {
                        console.log('Error fetching data');
                    }
                });
            });
        });

        /*** Receipt view / spinner helpers ***/
        function openModal() {
            $('#issue-loan-modal').modal('hide');
            $('#issue-loan-modal_2').modal('hide');
            $("#printerModal").fadeIn();
        }

        function closeModal() {
            // Refresh table page after close
            if (typeof currentLoadedPage !== 'undefined') {
                load_payment_table(currentLoadedPage);
            }
            $("#printerModal").fadeOut();
            $("#issue-loan-modal").fadeOut();
            $("#issue-loan-modal_2").fadeOut();
            $("#payment_amount").val("");
            $("#payment_amount_2").val("");
            $('.btn-success').prop('disabled', false);
        }

        document.querySelector('.close_2')?.addEventListener('click', closeModal);
        document.getElementById('overlay')?.addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        function printReceipt_view() {
            const modal = document.querySelector('#printerModal .printer-design');
            const printWindow = window.open('', '_blank', 'width=600,height=800');
            printWindow.document.open();

            let styles = '';
            Array.from(document.styleSheets).forEach(styleSheet => {
                try {
                    if (styleSheet.cssRules) {
                        Array.from(styleSheet.cssRules).forEach(rule => {
                            styles += rule.cssText;
                        });
                    }
                } catch (e) {}
            });

            const receiptHTML = modal.outerHTML;
            printWindow.document.write(`
                <html>
                <head>
                    <title>Print Receipt</title>
                    <style>
                        ${styles}
                        @page { size: 80mm auto; margin: 0; }
                        body { margin: 0; padding: 0; font-family: Arial, sans-serif; }
                        .printer-design { width: 80mm; margin: auto; padding: 10px; background: white; }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    ${receiptHTML}
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        /*** Load payment receipt (shows spinner, fills fields, shows cheque block if needed) ***/
        function load_payment_reciept(id) {
            openModal();
            $('.btn-success').prop('disabled', true);
            document.getElementById('loadingSpinner').style.display = 'flex';

            $.ajax({
                type: "POST",
                url: "/view_payment_load_reciept/" + id + "/1",
                headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                success: function (data, textStatus, xhr) {
                    const { customer, payment, loan, user, cheque_details: chq, saving_on, saving_amount } = data;

                    if (xhr.status === 200) {
                        if (data.point_check === "1") $('#loyalty_section').show(); else $('#loyalty_section').hide();

                        $("#Inv_number").text("Receipt No." + payment.idCustomer_Payments);
                        $("#customer_name").text((customer.First_Name || '') + " " + (customer.Last_Name || ''));
                        $("#customer_number").text(customer.cus_number || '');
                        $("#loyalty_points").text(parseFloat(data.points_to_add || 0).toFixed(2));
                        $("#loan_number").text(loan.Loan_No || '');
                        $("#payment_date_view").text(payment.Date || '');
                        $("#payment_time").text(payment.time || '');

                        const hasPenalty = parseFloat(data.panelty_balance || 0) > 0;
                        if (hasPenalty) {
                            $("#panelty_balance").text(data.panelty_balance);
                            $("#tot_balance").text(data.tot_balance);
                            $(".payment-info.balance").show();
                        } else {
                            $(".payment-info.balance").hide();
                        }

                        // Savings line
                        if (saving_on === "1") {
                            const sv = parseFloat(saving_amount || 0);
                            document.getElementById("saving_amount_container").style.display = "block";
                            document.getElementById("saving_amount_container").innerHTML = `
                                <table width="100%">
                                    <tr>
                                        <td><strong>Saving Amount</strong></td>
                                        <td align="right">${sv.toFixed(2)}</td>
                                    </tr>
                                </table>`;
                        } else {
                            document.getElementById("saving_amount_container").style.display = "none";
                        }

                        // Cheque section
                        if (payment.Payment_type === "Cheque") {
                            $('#cheque_section').show();
                            $("#cheque_no").text(chq?.Cheque_No || '-');
                            $("#cheque_date").text(chq?.Cheque_Date || '-');
                            $("#cheque_name").text(chq?.Name_On_The_Cheque || '-');
                            $("#cheque_type").text(chq?.Cheque_Type || '-');
                        } else {
                            $('#cheque_section').hide();
                        }

                        $("#loan_amount").text(parseFloat(loan.Amount || 0).toFixed(2));
                        $("#full_loan_amount").text(parseFloat(loan.Total_Loan_Amount || 0).toFixed(2));
                        const paid = parseFloat(payment.Amount || 0) - (saving_on === "1" ? parseFloat(saving_amount || 0) : 0);
                        $("#payed_amount").text(paid.toFixed(2));
                        $("#capital_balance").text(parseFloat(loan.Balance_Amount || 0).toFixed(2));

                        let paymentTypeLabel = payment.Payment_type || '-';
                        // If the logged-in user is cashier, show Cashier label
                        if ({{ (int) $cashier }} === 1) paymentTypeLabel = "Cashier";
                        $("#payment_type_view").text(paymentTypeLabel);

                        $("#signature").text(user.Full_Name || '');
                    }
                },
                complete: function () {
                    document.getElementById('loadingSpinner').style.display = 'none';
                },
                error: function () {
                    document.getElementById('loadingSpinner').style.display = 'none';
                    alert('Failed to load payment details.');
                }
            });
        }

        /*** Reduce capital action ***/
        function check_reduce() {
            const pending_amount = parseFloat($("#pending_amount").text() || 0);
            const next_payment_date = $("#next_payment_date").text();
            const days_from_last_payment_date = $("#days_from_last_payment_date").text();
            const ins_capital = $("#ins_capital").text();
            const installment_interest = $("#installment_interest").text();
            const installment_interest_today = $("#installment_interest_today").text();
            const required_payment_before_capital = $("#required_payment_before_capital").text();
            const reduce_balance_loan_id = $("#reduce_balance_loan_id").val();

            if (pending_amount === 0) {
                $.ajax({
                    type: "POST",
                    url: "/reduce_capital",
                    headers: { "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") },
                    data: {
                        reduce_balance_loan_id,
                        pending_amount,
                        next_payment_date,
                        days_from_last_payment_date,
                        ins_capital,
                        installment_interest,
                        installment_interest_today,
                        required_payment_before_capital,
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            window.location.href = response.redirect_url;
                        } else {
                            Swal.fire("Error!", response.message, "error");
                        }
                    },
                    error: function () {
                        Swal.fire("Error!", "Something went wrong!", "error");
                    }
                });
            } else {
                Swal.fire("Error!", "You have to complete pending payments before reducing capital!", "error");
            }
        }

        // Small guard for duplicate IDs (dev hint)
        document.addEventListener("DOMContentLoaded", function () {
            const dup = document.querySelectorAll('#ins_id');
            if (dup.length > 1) {
                console.warn('Duplicate id="ins_id" found in DOM. Consider renaming one (e.g., ins_id_2).');
            }
        });
        // Real-time cheque number validation
        let chequeValidationTimeout;
        $('#chq_number, #chq_number_2').on('input', function() {
            const chequeInput = $(this);
            const chequeNumber = chequeInput.val().trim();
            const feedbackId = chequeInput.attr('id') + '_feedback';
            
            // Remove existing feedback
            $('#' + feedbackId).remove();
            
            if (!chequeNumber) {
                return;
            }
            
            // Clear previous timeout
            clearTimeout(chequeValidationTimeout);
            
            // Add loading indicator
            chequeInput.after('<small id="' + feedbackId + '" class="text-muted">Checking...</small>');
            
            // Debounce validation
            chequeValidationTimeout = setTimeout(function() {
                $.ajax({
                    url: '/check-cheque-number',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: { chq_number: chequeNumber },
                    success: function(response) {
                        $('#' + feedbackId).remove();
                        if (response.exists) {
                            chequeInput.addClass('is-invalid');
                            chequeInput.after('<small id="' + feedbackId + '" class="text-danger"><i class="bi bi-x-circle"></i> Cheque number already exists</small>');
                        } else {
                            chequeInput.removeClass('is-invalid').addClass('is-valid');
                            chequeInput.after('<small id="' + feedbackId + '" class="text-success"><i class="bi bi-check-circle"></i> Available</small>');
                        }
                    },
                    error: function() {
                        $('#' + feedbackId).remove();
                    }
                });
            }, 500);
        });

        // When center is changed, load its groups dynamically
        $('#center_details').on('change', function () {
            const centerId = $(this).val();
            const $groupSelect = $('#group');

            $groupSelect.html('<option value="0">Loading...</option>'); // temporary

            if (centerId === "0" || !centerId) {
                $groupSelect.html('<option value="0">All</option>');
                $groupSelect.trigger('change');
                return;
            }

            $.ajax({
                url: '/get-groups-by-center/' + centerId,
                type: 'GET',
                success: function (data) {
                    $groupSelect.empty().append('<option value="0">All</option>');

                    if (data && data.length > 0) {
                        data.forEach(group => {
                            $groupSelect.append(
                                `<option value="${group.idCustomer_Group}">
                            ${group.Group_No} - ${group.Name}
                        </option>`
                            );
                        });
                    } else {
                        $groupSelect.append('<option value="0">No groups found</option>');
                    }

                    // Refresh Select2
                    $groupSelect.trigger('change');
                },
                error: function () {
                    $groupSelect.html('<option value="0">Error loading groups</option>');
                }
            });
        });

    </script>


@endsection


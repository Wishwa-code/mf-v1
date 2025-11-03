@php use Carbon\Carbon; @endphp
@extends('layout.admin')

@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <style>


        .custom-scrollbar {
            max-height: 400px; /* Adjust the height as needed */
            overflow-y: auto;

        }

        .card-header {
            background-color: #6c757d; /* Dark grey background color */
            color: #fff; /* White text color */
            padding-top: 0.5rem; /* Adjusted padding */
            padding-bottom: 0.5rem; /* Adjusted padding */
        }

        .custom-button-align {
            text-align: right;
            margin-top: 10px; /* Adjust margin as needed */
        }

        .card-body {
            padding: 1rem; /* Added padding */
        }

        .card {
            margin-bottom: 20px; /* Added margin bottom for spacing */
        }

        .modal-content {
            border: none;
            padding: 0;
            margin: 0;
        }

        .modal-body {
            padding: 0;
        }

        .card {
            margin: 0;
            border: none;
        }

        .card-header {
            padding: 0.75rem 1.25rem;
            background-color: #1A2942;
            color: #fff;
        }


        .bulb-icon {
            display: inline-block;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 50%;
            text-align: center;
            line-height: 1.5rem;
            font-size: 1.5rem;
        }

        .text-success {
            color: #1c8d36; /* Green text color */
        }

        .text-danger {
            color: #dc3545; /* Red text color */
        }

        .text-Normal {
            color: #ababab; /* Blue text color */
        }

        .highlight-card {
            background-color: #f8f9fa;
            border: 1px solid #1A2942;
        }

        .highlight-card .card-header {
            background-color: #1A2942;
            color: #fff;
        }

        .highlight-card .card-body {
            font-size: 1.2em; /* Increase font size */
        }

        .table th, .table td {
            text-align: center; /* Center the text */
            vertical-align: middle; /* Center vertically */
        }

    </style>
    <style>
        /* Ensures the table stays within the modal with scrollable content */
        .modal-body {
            padding: 1rem;
        }

        .table-responsive {
            max-height: 400px; /* Adjust this value to control the height */
            overflow-y: auto;  /* Vertical scroll for table if content exceeds height */
        }

        /* Adjust table row behavior for responsive layout */
        table th, table td {
            white-space: nowrap;
        }

        /* Optional: style for table striped rows and hover effects */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05); /* Customize striping color */
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.1); /* Customize hover color */
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

    </style>


    <style>
        .preview-box {
            width: 200px;
            height: 150px;
            overflow: hidden;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }

        .preview-box img,
        .preview-box iframe,
        .preview-box video {
            max-width: 100%;
            max-height: 100%;
        }

        /* Make the table area scrollable with a max-height so modal body doesn't overflow */
        #deTableWrap {
            max-height: 55vh; /* adjust based on preference */
            overflow: auto;
        }

        /* Sticky header - stays visible while scrolling the tbody */
        #doubleEntriesTable thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background: #fff; /* match table header bg (table-light) */
        }

        /* Sticky footer - keep totals visible at bottom of table area */
        #doubleEntriesTable tfoot th {
            position: sticky;
            bottom: 0;
            background: #fff;
            z-index: 4;
        }

        /* Improve numeric cell spacing and wrapping */
        #doubleEntriesTable td, #doubleEntriesTable th {
            white-space: nowrap;
            vertical-align: middle;
        }

        /* On very narrow screens allow wrapping for account names */
        @media (max-width: 420px) {
            #doubleEntriesTable td:first-child,
            #doubleEntriesTable th:first-child,
            #doubleEntriesTable td:last-child,
            #doubleEntriesTable th:last-child {
                white-space: normal;
            }
        }

    </style>
    <style>
        /* Custom XXL Modal */
        .modal-xxl {
            max-width: 98% !important;  /* nearly full-screen width */
        }
        #loanLogTable th, #loanLogTable td {
            white-space: nowrap;        /* prevent wrapping for better readability */
            font-size: 13px;            /* slightly smaller font for big tables */
        }
    </style>

@endsection

@section('content')
    <div class="custom-container py-4">
        <div class="row justify-content-center">
            <!-- Highlighted Loan Details Card -->
            <div class="col-lg-12 mb-4">
                @if($type==438217)
                    <input type="hidden" id="designation_user" value="{{session('designation')}}">
                    <div class="card shadow mb-3">
                        <div class="card-header">
                            Approval Section
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-sm table-striped" id="approval_table">
                                    <thead class="sticky-top bg-white">
                                    <tr>
                                        <th hidden>#</th>
                                        <th scope="col">Level</th>
                                        <th scope="col">Permissions</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Comment</th>
                                        <th scope="col">Approve</th>
                                        <th scope="col">Approved User</th>
                                        <th scope="col">Date Time</th>
                                        <th scope="col">Check List</th>
                                    </tr>
                                    </thead>
                                    <tbody class="custom-scrollbar" style="max-height: 400px;">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="modal fade" id="checklist-modal" tabindex="-1" aria-labelledby="checklistModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="checklistModalLabel">Checklist</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="checklist-container"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>




                <div class="card shadow highlight-card">
                    <div class="card-header">
                        Loan Summary
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Loan Stock</th>
                                    <th scope="col">Loan Portfolio</th>
                                    <th scope="col">Paid Loan Amount</th>
                                    <th scope="col">Paid Saving Amount</th>
                                    <th scope="col">Total Paid Amount</th>

                                    <th scope="col">Total Paid Penalty</th>
                                    <th scope="col">Total Penalty Balance</th>
                                    <th scope="col">Savings Balance</th>
                                    <th scope="col">Capital Balance</th>
                                    <th scope="col">Interest Balance</th>
                                    <th scope="col">Extra Charge Balance</th>
                                    <th scope="col">Total Outstanding</th>
                                    <th scope="col">Loan Maturity Date</th>
                                    <th>Loan Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ number_format($loan->Amount, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->Total_Loan_Amount, 2, '.', ',') }}</td>
                                    <td>{{ number_format(max($total_paid_amount - $savingBalanceSum, 0), 2, '.', ',') }}</td>
                                    <td>{{ number_format($savingBalanceSum, 2, '.', ',') }}</td>
                                    <td>{{ number_format($total_paid_amount, 2, '.', ',') }}</td>
                                    <td>{{ number_format($Panalty_Amount-$Panalty_BalanceSum, 2, '.', ',') }}</td>
                                    <td>{{ number_format($Panalty_BalanceSum, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan_saving_balance, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->capital_balance, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->installment_balance, 2, '.', ',') }}</td>
                                    <td>{{ number_format($extraChargelatestBalance, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan_balance, 2, '.', ',') }}</td>
                                    <td>{{ $installments->last()->Installment_Date }}</td>
                                    <td style="color:
    {{ $loan->Status == -1 ? 'orange' : ($loan->Status == 0 ? 'red' : 'green') }};">
                                        <strong>
                                            {{ $loan->Status == -1 ? 'Pending' : ($loan->Status == 0 ? 'Ongoing' : 'Settled') }}
                                        </strong>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="text-center">
                                <a href="{{ route('generate-loan-pdf', $loan->idCustomer_Loan) }}"
                                   class="btn btn-primary">
                                    Download Full Loan Detail PDF
                                </a>
                                <a href="#" class="btn btn-danger" id="viewLoanLog">
                                    Loan Log
                                </a>
                                <a href="#" class="btn btn-warning" id="viewLoanLog">
                                    Loan Comment
                                </a>
                                <a href="/kyc/{{$loan->Customer_idCustomer}}" class="btn btn-dark" id="KYC" target="_blank">
                                    KYC
                                </a>
                                <a href="javascript:void(0)" class="btn btn-outline-danger" id="btnDeleteLoan">
                                    Delete Loan Permanently
                                </a>

                                <a href="javascript:void(0)"
                                   class="btn btn-outline-dark btn-show-double-entries"
                                   data-loan-id="{{ $loan->idCustomer_Loan }}"
                                   data-loan-no="{{ $loan->Loan_No }}">
                                    Loan Double Entries
                                </a>




                            </div>

                        </div>
                    </div>
                </div>
            </div>



            <!-- Double Entries Modal -->
            <!-- Modal -->
            <div class="modal fade" id="doubleEntriesModal" tabindex="-1" role="dialog" aria-hidden="true">
                <!-- modal-fullscreen-sm-down makes modal fullscreen on small screens -->
                <div class="modal-dialog modal-lg modal-fullscreen-sm-down modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Loan Double Entries — <span id="deLoanNo"></span></h5>
                        </div>

                        <div class="modal-body">
                            <!-- error -->
                            <div id="deError" class="alert alert-danger d-none"></div>

                            <!-- Scrollable table area with sticky header/footer -->
                            <div class="table-responsive" id="deTableWrap">
                                <table class="table table-sm table-bordered table-striped align-middle mb-0" id="doubleEntriesTable">
                                    <thead class="table-light">
                                    <tr>
                                        <th class="text-start">Account Name</th>
                                        <th class="text-end">Debit Amount</th>
                                        <th class="text-end">Credit Amount</th>
                                        <th class="text-start">Contra Account</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <!-- filled by JS -->
                                    </tbody>
                                    <tfoot class="table-light">
                                    <tr>
                                        <th>Total</th>
                                        <th id="totalDebit" class="text-end">0.00</th>
                                        <th id="totalCredit" class="text-end">0.00</th>
                                        <th></th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>



                    </div>
                </div>
            </div>






            <div class="custom-container py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header">
                                Loan Details
                            </div>
                            <div class="card-body">
                                <div class="table-responsive custom-scrollbar">
                                    <table class="table table-bordered table-sm">
                                        <tbody>

                                        <tr>
                                            <td style="text-align: left"><strong>Loan Number</strong></td>
                                            <td style="text-align:left">
                                                {{ $loan->Loan_No }}
                                                @if ($exists)
                                                    <span class="badge bg-danger ms-2">Rescheduled Loan</span>
                                                @endif
                                            </td>

                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Product Name</strong></td>
                                            <td style="text-align: left">{{ $loan->Loan_Category_idLoan_Category }}
                                                - {{ $Loan_Category->Name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Created Date & Time</strong></td>
                                            <td style="text-align: left">{{ $loan->created_at }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Disbursement Date</strong></td>
                                            <td style="text-align: left">{{ $loan->Status != -1 ? $loan->Date_Time : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Amount</strong></td>
                                            <td style="text-align: left">{{ $loan->Amount }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Interest Rate</strong></td>
                                            <td style="text-align: left">{{ $loan->Interest_Rate }} % ({{ $loan->Interest_period }})</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Penalty Rate</strong></td>
                                            <td style="text-align: left">{{ $loan->Panalty_Rate }} %</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Installment Count</strong></td>
                                            <td style="text-align: left">{{ $ins_count }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Interest Amount</strong></td>
                                            <td style="text-align: left">{{ number_format($loan->Interest_Amount,2,'.',',') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Total Other Loan Charges</strong></td>
                                            <td style="text-align: left">{{ $loan->Total_Other_Amount }} &nbsp <a
                                                        href="#" data-toggle="modal"
                                                        data-target="#otherChargesModal"><i
                                                            class="fas fa-search fa-lg"></i></a></td>

                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Total Loan Amount</strong></td>
                                            <td style="text-align: left">{{ number_format($loan_Total_Amount,2,'.',',') }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Installment Amount</strong></td>
                                            <td style="text-align: left">{{ $loan->Installment_Amount }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Collection Type</strong></td>
                                            <td style="text-align: left">{{ $loan->Collection_Type }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Created User</strong></td>
                                            <td style="text-align: left">{{ $User->id }} - {{ $User->Full_Name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Capital Balance</strong></td>
                                            <td style="text-align: left">{{ $loan->capital_balance }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Interest Balance</strong></td>
                                            <td style="text-align: left">{{ $loan->installment_balance }}</td>
                                        </tr>

                                        <tr>
                                            <td style="text-align: left"><strong>Customer Bank Account</strong></td>
                                            <td style="text-align: left">{{ $Customer_Bank->bank_name ?? '-' }}
                                                - {{ $Customer_Bank->account_number ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Lending Officer ID</strong></td>
                                            <td style="text-align: left">{{ $Lending_Officer->id }}
                                                - {{ $Lending_Officer->Full_Name }}</td>
                                        </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header">
                                Customer Details
                            </div>
                            <div class="card-body">
                                <div class="table-responsive custom-scrollbar">
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                        <tr>
                                            <td style="text-align: left"><strong>Customer No</strong></td>
                                            <td style="text-align: left">{{ $customers->cus_number }} </td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Name</strong></td>
                                            <td style="text-align: left">{{ $customers->First_Name }} {{ $customers->Last_Name }}</td>
                                        </tr>

                                        <tr>
                                            <td style="text-align: left"><strong>Email</strong></td>
                                            <td style="text-align: left">{{ $customers->Email }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Contact Number</strong></td>
                                            <td style="text-align: left">{{ $customers->Contact_No }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>NIC</strong></td>
                                            <td style="text-align: left">{{ $customers->Nic }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Address</strong></td>
                                            <td style="text-align: left">{{ $customers->Address }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>City</strong></td>
                                            <td style="text-align: left">{{ $customers->City }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>State</strong></td>
                                            <td style="text-align: left">{{ $customers->State }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Civil Status</strong></td>
                                            <td style="text-align: left">{{ $customers->civil_status }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Occupation</strong></td>
                                            <td style="text-align: left">{{ $customers->occu_job_position }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Working Place Contact</strong></td>
                                            <td style="text-align: left">{{ $customers->occu_contact_no }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Working Place Address</strong></td>
                                            <td style="text-align: left">{{ $customers->occu_address_01 }},{{ $customers->occu_address_02 }},{{ $customers->occu_address_03 }}</td>
                                        </tr>

                                        </tbody>
                                    </table>
                                    <hr>
                                    <div class="table-responsive custom-scrollbar">
                                        <table class="table table-bordered table-sm">
                                            <tbody>
                                            <tr>
                                                <td style="text-align: left"><strong>Guardian Name</strong></td>
                                                <td style="text-align: left">{{ $customers->Gua_title }} {{ $customers->Gua_name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left"><strong>Relation to Customer</strong></td>
                                                <td style="text-align: left">{{ $customers->Gua_relation }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left"><strong>Guardian NIC</strong></td>
                                                <td style="text-align: left">{{ $customers->Gua_nic }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left"><strong>Guardian Address</strong></td>
                                                <td style="text-align: left">{{ $customers->Gua_address }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left"><strong>Guardian Occupation</strong></td>
                                                <td style="text-align: left">{{ $customers->Gua_occu }}</td>
                                            </tr>
                                            <tr>
                                                <td style="text-align: left"><strong>Guardian Contact</strong></td>
                                                <td style="text-align: left">{{ $customers->Gua_contact }}</td>
                                            </tr>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow">
                        <div class="card-header">
                            Installment Schedule
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-sm table-striped">
                                    <thead class="sticky-top bg-white">
                                    <tr>
                                        <th scope="col">Installment No</th>
                                        <th scope="col">Installment Date</th>
                                        @if($company->product_editable==1)
                                        @else
                                            <th scope="col">Penalty Date</th>
                                        @endif
                                        <th scope="col">Installment Amount</th>
                                        <th scope="col">Capital Amount</th>
                                        <th scope="col">Interest Amount</th>
                                        <th scope="col">Penalty Amount</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">Paid Amount</th>

                                        <th scope="col">Penalty Balance</th>
                                        <th scope="col">Interest Balance</th>
                                        <th scope="col">Capital Balance</th>
                                        <th scope="col">Savings Balance</th>
                                        <th scope="col">Total Balance</th>
                                        <th scope="col">Collection Date</th>
                                        <th scope="col">Difference</th>
                                        <th scope="col">Status</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="custom-scrollbar" style="max-height: 400px;">
                                    @foreach ($installments as $installment)
                                        @php
                                            $statusText = $installment->Status == 1 ? 'Paid' : 'Pending';
                                            $bulbColor = '';
                                            if ($installment->Status == 1) {
                                                $bulbColor = 'text-success'; // Green bulb color
                                            } elseif ($installment->Status == 0 && \Carbon\Carbon::parse($installment->Installment_Date)->isPast()) {
                                                $bulbColor = 'text-danger'; // Red bulb color
                                            } else {
                                                $bulbColor = 'text-Normal'; // Blue bulb color
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $installment->No }}</td>
                                            <td>{{ $installment->Installment_Date }}</td>
                                            @if($company->product_editable==1)
                                            @else
                                                <td>{{ $installment->Panelty_date }}</td>
                                            @endif

                                            <td>{{ number_format($installment->Installment_Amount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($installment->capital_amount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($installment->interest_amount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($installment->Panalty_Amount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($installment->Total_Amount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($installment->Paid_Amount, 2, '.', ',') }}</td>
                                            <td>{{ number_format($installment->Panalty_Balance, 2, '.', ',') }}</td>
                                            <td>{{ number_format($installment->Interest_Balance, 2, '.', ',') }}</td>
                                            <td>{{ number_format($installment->capital_balance, 2, '.', ',') }}</td>
                                            <td>{{ number_format($installment->Saving_balance, 2, '.', ',') }}</td>
                                            <td>{{ number_format($installment->Total_Balance, 2, '.', ',') }}</td>
                                            <td>{{ $installment->Collection_Date ?? '-' }}</td>
                                            <td>{{ $installment->Collection_Diff ?? '-' }}</td>
                                            <td>{{ $statusText }}</td>
                                            <td>
                                                <i class="fas fa-lightbulb bulb-icon {{ $bulbColor }}"></i>
                                                <a href="#" data-toggle="modal" data-target="#installmentLogModal{{ $installment->idInstallments }}">
                                                    <i class="fas fa-eye bulb-icon"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Summary Section -->
                    @if(isset($customerSummary) && $customerSummary)
                    <div class="card shadow mt-4">
                        <div class="card-header">
                            <i class="fas fa-id-card me-2"></i>Customer Summary
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Left Column: Customer Details -->
                                <div class="col-lg-6">
                                    <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Customer Information</h6>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <tbody>
                                                <tr>
                                                    <td style="text-align: left; width: 40%;"><strong>Route</strong></td>
                                                    <td style="text-align: left;">{{ $customerSummary->route_code ?? '-' }} - {{ $customerSummary->route_name ?? '-' }}</td>
                                                </tr>
                                                @if(isset($customerSummary->collection_type) && strtolower($customerSummary->collection_type) === 'fixed' && isset($customerSummary->collection_date))
                                                <tr>
                                                    <td style="text-align: left;"><strong>Collection Date</strong></td>
                                                    <td style="text-align: left;">{{ $customerSummary->collection_date }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td style="text-align: left;"><strong>Center</strong></td>
                                                    <td style="text-align: left;">{{ $customerSummary->center_no ?? '-' }} - {{ $customerSummary->center_name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align: left;"><strong>Group</strong></td>
                                                    <td style="text-align: left;">{{ $customerSummary->group_no ?? '-' }} - {{ $customerSummary->group_name ?? '-' }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Right Column: Group Members -->
                                <div class="col-lg-6">
                                    <h6 class="text-primary mb-3"><i class="fas fa-users me-2"></i>Group Members</h6>
                                    @if(isset($groupMembers) && $groupMembers->count() > 0)
                                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                            <table class="table table-bordered table-sm table-striped">
                                                <thead class="sticky-top bg-white">
                                                    <tr>
                                                        <th style="width: 120px;">Cus No</th>
                                                        <th>Name</th>
                                                        <th style="width: 80px;" class="text-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($groupMembers as $member)
                                                        <tr>
                                                            <td>{{ $member->cus_number }}</td>
                                                            <td>{{ $member->First_Name }} {{ $member->Last_Name }}</td>
                                                            <td class="text-center">
                                                                <a href="/kyc/{{ $member->idCustomer }}" class="btn btn-sm btn-outline-primary" target="_blank" title="View KYC">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>No other members in this group.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif


                </div>


                <br>

                <div class="row">
                    @foreach ($witnessDetails as $index => $witness)
                        <div class="col-md-6 witness-card">
                            <div class="card shadow">
                                <div class="card-header">
                                    {{ $witness['type'] === 'Customer' ? 'Cross Customer' : $witness['type'] }} {{ $index + 1 }}
                                    Details
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-sm">
                                        <tbody>
                                        <tr>
                                            <td><strong>First Name</strong></td>
                                            <td>{{ $witness['details']->First_Name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Name</strong></td>
                                            <td>{{ $witness['details']->Last_Name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Contact Number</strong></td>
                                            <td>{{ $witness['details']->Contact_No }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIC</strong></td>
                                            <td>{{ $witness['details']->Nic }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Address</strong></td>
                                            <td>{{ $witness['details']->Address }}</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($type==438217)
                    <br>
                    <div class="card shadow">
                        <div class="card-header">
                            Customer Document Details
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-sm table-striped">
                                    <thead class="sticky-top bg-white">
                                    <tr>
                                        <th scope="col">Name</th>
                                        <th scope="col">File</th>
                                        <th scope="col">Preview</th>
                                    </tr>
                                    </thead>
                                    <tbody class="custom-scrollbar" style="max-height: 400px;">
                                    @foreach ($customer_documents as $document)
                                        <tr>
                                            <td>{{ $document->Description }}</td>
                                            <td>
                                                @if (!empty($document->Path) && file_exists(storage_path('app/public/' . $document->Path)))
                                                    <a href="{{ asset('storage/' . $document->Path) }}" target="_blank">View File</a>
                                                @else
                                                    <span class="text-muted">No File</span>
                                                @endif
                                            </td>

                                            <td>
                                                <div class="preview-box">
                                                    @php
                                                        $ext = strtolower(pathinfo($document->Path, PATHINFO_EXTENSION));
                                                    @endphp

                                                    @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                                        <img src="{{ asset('storage/' . $document->Path) }}" alt="Image Preview">
                                                    @elseif ($ext === 'pdf')
                                                        <iframe src="{{ asset('storage/' . $document->Path) }}"></iframe>
                                                    @elseif (in_array($ext, ['mp4', 'webm']))
                                                        <video muted autoplay loop>
                                                            <source src="{{ asset('storage/' . $document->Path) }}" type="video/{{ $ext }}">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    @else
                                                        <span>No Preview</span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>


                    <br>
                @endif
                <br>
                <div class="card shadow">
                    <div class="card-header">
                        Uploaded Loan Document Details
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table table-bordered table-sm table-striped">
                                <thead class="sticky-top bg-white">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">File</th>
                                    <th scope="col">Preview</th>
                                </tr>
                                </thead>
                                <tbody class="custom-scrollbar" style="max-height: 400px;">
                                @foreach ($documents as $document)
                                    <tr>
                                        <td>{{ $document->Name }}</td>
                                        <td>
                                            @if (!empty($document->Path) && file_exists(storage_path('app/public/' . $document->Path)))
                                                <a href="{{ asset('storage/' . $document->Path) }}" target="_blank">View File</a>
                                            @else
                                                <span class="text-muted">No File</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="preview-box">
                                                @php
                                                    $ext = strtolower(pathinfo($document->Path, PATHINFO_EXTENSION));
                                                @endphp

                                                @if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
                                                    <img src="{{ asset('storage/' . $document->Path) }}" alt="Image Preview">
                                                @elseif ($ext === 'pdf')
                                                    <iframe src="{{ asset('storage/' . $document->Path) }}"></iframe>
                                                @elseif (in_array($ext, ['mp4', 'webm']))
                                                    <video muted autoplay loop>
                                                        <source src="{{ asset('storage/' . $document->Path) }}" type="video/{{ $ext }}">
                                                        Your browser does not support the video tag.
                                                    </video>
                                                @else
                                                    <span>No Preview</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <br>


                @if($type!=438217)
                    <div class="card shadow" id="np">
                        <div class="card-header" style="position: relative;">
                            Payment History
                            <button onclick="printTable()" class="btn btn-primary"
                                    style="position: absolute; right: 0; top: 50%; transform: translateY(-50%);">Download
                                Payment History PDF
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-sm table-striped">
                                    <thead class="sticky-top bg-white">
                                    <tr>
                                        <th scope="col">Date</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col" style="width: 40%">Comment</th>
                                        <th scope="col">Slip</th>
                                        <th scope="col">Payment Type</th>
                                        <th scope="col">User</th>
                                        <th scope="col">Slip</th>
                                        <th scope="col">Payment Slip</th>
                                    </tr>
                                    </thead>
                                    <tbody class="custom-scrollbar" style="max-height: 400px;">
                                    @foreach ($customer_payments as $customer_payment)
                                        <tr>
                                            <td>{{ $customer_payment->Date }} - {{ $customer_payment->time }}</td>
                                            <td>{{ $customer_payment->Description }}</td>
                                            <td>{{ number_format($customer_payment->Amount, 2, '.', ',') }}</td>
                                            <td>{{ $customer_payment->comment }}</td>
                                            <td>
                                                @if (!empty($customer_payment->Slip))
                                                    <a href="javascript:void(0);"
                                                       onclick="openSlip('{{ asset('storage/' . $customer_payment->Slip) }}')">View
                                                        Slip</a>
                                                @else
                                                    No Slip Available
                                                @endif
                                            </td>
                                            <td>{{ $customer_payment->Payment_type }}   </td>
                                            <td>{{ $customer_payment->Full_Name }} ( {{ $customer_payment->Designation }}
                                                - {{ $customer_payment->email }} )
                                            </td>
                                            <td>
                                                @if (!empty($customer_payment->Slip))
                                                    <button type="button" class="btn btn-success btn-sm" onclick="openSlip('{{ asset('storage/' . $customer_payment->Slip) }}')">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                @else
                                                    <button type="button" class="btn btn-success btn-sm disabled">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" onclick="payment_slip({{$customer_payment->idCustomer_Payments}})">
                                                    <i class="bi bi-printer"></i>
                                                </button>
                                            </td>
                                            @if($payment_delete_status == '1')
                                                <td>
                                                    <button class="reverse-payment-btn btn btn-outline-danger"
                                                            onclick="undo_payment({{ $customer_payment->idCustomer_Payments }})"
                                                            @if($customer_payment->status != 0) disabled title="Cannot reverse this payment" @endif>
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </td>
                                            @else
                                                <td></td>
                                            @endif



                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif


                <div class="modal fade" id="otherChargesModal" tabindex="-1" role="dialog"
                     aria-labelledby="otherChargesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="card shadow">
                                    <div class="card-header">
                                        Other Loan Charges
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive custom-scrollbar">
                                            <table class="table table-bordered table-sm table-striped">
                                                <thead class="sticky-top bg-white">
                                                <tr>
                                                    <th scope="col">Description</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody class="custom-scrollbar" style="max-height: 400px;">
                                                @foreach ($Other_Charges as $Other_Charge)
                                                    <tr>
                                                        <td>{{ $Other_Charge->Description }}</td>
                                                        <td>{{ $Other_Charge->Type }}</td>
                                                        <td>{{ number_format($Other_Charge->Amount, 2, '.', ',') }}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                            <div class="custom-button-align">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <br>

                <!-- Loan Log Modal -->
                <div class="modal fade" id="loanLogModal" tabindex="-1" aria-labelledby="loanLogModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xxl">

                    <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="loanLogModalLabel">Loan Log</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex justify-content-end mb-2">
                                    <button class="btn btn-success" onclick="downloadExcel()">Download Excel</button>
                                </div>
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-striped" id="loanLogTable">
                                        <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>ID</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Penalty Payment</th>
                                            <th>Interest Payment</th>
                                            <th>Capital Payment</th>
                                            <th>Savings Payment</th>
                                            <th>Extra Payment</th>
                                            <th>Recovery Payment</th>
                                            <th>Penalty Balance</th>
                                            <th>Interest Balance</th>
                                            <th>Capital Balance</th>
                                            <th>Savings Balance</th>
                                            <th>Extra Balance</th>
                                            <th>Recovery Balance</th>
                                            <th>Total Pending Balance</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Data will be loaded dynamically via AJAX -->
                                        </tbody>
                                    </table>

                                </div>
                            </div>
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
                            <div class="modal-body">
                                <div class="mt-3">
                                    <h6>Add a Comment</h6>
                                    <textarea id="commentText" class="form-control" rows="3" placeholder="Enter your comment here"></textarea>
                                    <button class="btn btn-primary mt-2" id="submitComment">Add Loan Comment</button>
                                </div>
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




                @foreach ($installments as $installment)
                    <div class="modal fade" id="installmentLogModal{{ $installment->idInstallments }}" tabindex="-1"
                         role="dialog"
                         aria-labelledby="installmentLogModalLabel{{ $installment->idInstallments }}"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"
                                        id="installmentLogModalLabel{{ $installment->idInstallments }}">Installment Log
                                        Details</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <table class="table table-bordered table-sm">
                                        <thead class="sticky-top bg-white">
                                        <tr>
                                            <th scope="col">Date</th>
                                            <th scope="col">Description</th>
                                            <th scope="col">Amount</th>
                                            <th scope="col">Penalty Balance</th>
                                            <th scope="col">Interest Balance</th>
                                            <th scope="col">Capital Balance</th>
                                            <th scope="col">Savings Balance</th>
                                            <th scope="col">Total Balance</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($installment_logs as $log)
                                            @if ($log->Installments_idInstallments == $installment->idInstallments)
                                                <tr>
                                                    <td>{{ $log->Date }}</td>
                                                    <td>{{ $log->Description }}</td>
                                                    <td>{{ number_format($log->Amount, 2, '.', ',') }}</td>
                                                    <td>{{ number_format($log->Panalty_Total, 2, '.', ',') }}</td>
                                                    <td>{{ number_format($log->Interest_Balance, 2, '.', ',') }}</td>
                                                    <td>{{ number_format($log->Capital_balance, 2, '.', ',') }}</td>
                                                    <td>{{ number_format($log->Saving_balance, 2, '.', ',') }}</td>
                                                    <td>{{ number_format($log->Total_Balance, 2, '.', ',') }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
@endforeach


                <div id="overlay"></div>
                <div id="printerModal" class="modal_2">
                    <div class="modal_2-content">
                        <span class="close_2">&times;</span>
{{--                        <button onclick="printReceipt_view()" class="btn btn-danger">Print Receipt</button>--}}
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

                <div class="modal fade" id="deleteLoanModal" tabindex="-1" role="dialog" aria-labelledby="deleteLoanModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <form id="deleteLoanForm">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteLoanModalLabel">Delete Loan Permanently</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>&times;</span></button>
                                </div>

                                <div class="modal-body">
                                    <p class="mb-2">Choose how to proceed:</p>

                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" id="modePassword" name="mode" class="custom-control-input" value="password" checked>
                                        <label class="custom-control-label" for="modePassword">Delete now with Admin Password</label>
                                    </div>

                                    <div id="passwordBlock" class="mb-3">
                                        <label for="admin_password" class="mb-1">Admin Password</label>
                                        <input type="password" name="admin_password" id="admin_password" class="form-control" placeholder="Enter admin password">
                                        <small class="text-muted">Immediately reverses & deletes on success.</small>
                                    </div>

                                    <div class="custom-control custom-radio mb-2">
                                        <input type="radio" id="modeApproval" name="mode" class="custom-control-input" value="approval">
                                        <label class="custom-control-label" for="modeApproval">Send for Approval</label>
                                    </div>

                                    <div id="approvalNote" class="d-none">
                                        <small class="text-muted">Creates a request; admin must approve before deletion.</small>
                                    </div>

                                    <input type="hidden" name="loan_id" value="{{ $loan->idCustomer_Loan }}">
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="btnProceedDelete">Proceed</button>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>



@endsection
@section('script')
    <script>
        $(document).ready(function () {
            load_approval_check({{$id}});
        })

        function load_approval_check(id) {
            $.ajax({
                type: "GET",
                url: "/load_loan_approval/" + id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {

                    console.log(data);

                    if (xhr.status === 200) {
                        let login_designation = data.login_designation;

                        // Clear the existing rows
                        $('#approval_table tbody').empty();

                        let allUsersHaveIds = true; // Flag for "Issue Loan" button

                        // Iterate over approval items
                        data.item.forEach(function (document, index) {
                            let levelDesignations = data.designation
                                .filter(designationObj => designationObj.level_id == document.level_id) // Match correct level
                                .map(designationObj => designationObj.designation_id) // Use correct designation field
                                .join(", ");

                            let approveButton;
                            if (login_designation === "Admin") {
                                approveButton = document.user_id == '0'
                                    ? `<input type="button" class="btn btn-primary" id="approve_btn_${document.level_id}" value="Approve" onclick="approve(${document.id}, '${index}')">`
                                    : `<input type="button" class="btn btn-primary" value="Approve" disabled>`;
                            } else {
                                let userDesignation = $("#designation_user").val().trim(); // Get logged-in user's designation and remove spaces

// Get designations for the current level only
                                let levelDesignationArray = data.designation
                                    .filter(designationObj => designationObj.level_id == document.level_id) // Only for this level
                                    .map(designationObj => designationObj.designation_id.trim()); // Remove extra spaces

// Check if logged-in user's designation matches this level's designation(s)
                                if (levelDesignationArray.includes(userDesignation) && document.user_id == '0') {
                                    approveButton = `<input type="button" class="btn btn-primary" id="approve_btn_${document.level_id}" value="Approve" onclick="approve(${document.id}, '${index}')">`;
                                } else {
                                    approveButton = `<input type="button" class="btn btn-primary" value="Approve" disabled>`;
                                }

                            }

                            let newRow = `<tr>
        <td hidden>${document.id}</td>
        <td>${document.level}</td>
        <td>${levelDesignations}</td>
        <td>${document.description}</td>
        <td><input type="text" class="form-control" value="${document.comment}" id="des_${index}"></td>
        <td>${approveButton}</td>
        <td>${document.user_id === 0 ? '-' : document.Full_Name}</td>
        <td>${document.date}</td>
        <td>
            <button class="btn btn-info btn-sm" onclick="toggleChecklist(${document.level_id},${id})">
                View Checklist (<span id="checklist_progress_${document.level_id}">0/0</span>)
            </button>
        </td>
    </tr>
    <tr id="checklist_row_${document.level_id}" style="display: none;">
        <td colspan="9">
            <div id="checklist_container_${document.level_id}" class="p-3 bg-light"></div>
        </td>
    </tr>`;

                            $('#approval_table tbody').append(newRow);
                            loadChecklistProgress(document.level_id, id);
                        });

                        // Enable or disable "Issue Loan" button
                        $('#issue_loan_btn').prop('disabled', !allUsersHaveIds);
                    } else {
                        Swal.fire("Error!", "Failed to load data!", "error");
                    }
                },
                error: function (xhr) {
                    console.log("Error:", xhr.responseText);
                },
            });
        }



        function approve(id, index, el) {
            // Read & validate comment
            var comment = String($("#des_" + index).val() || "").trim();
            if (!comment) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Please enter a comment!",
                });
                return;
            }

            // Try to infer loan_id
            // 1) common hidden inputs you already have on the page
            var loan_id ={{$loan->idCustomer_Loan}};

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to approve this Loan?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, approve it!",
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    type: "POST",
                    url: "/approve_loan",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        id: id,
                        loan_id: loan_id,     // <-- include loan_id for the backend "all approved" check
                        comment: comment,
                    },
                    success: function (res, _textStatus, xhr) {
                        if (xhr.status !== 200) {
                            Swal.fire("Error!", "Failed to update!", "error");
                            return;
                        }

                        // If backend says to redirect (all approvals done), go to /loan_disbursement
                        if (res && res.redirect && res.url) {
                            Swal.fire({
                                icon: "success",
                                title: "Approved!",
                                text: "All approvals completed. Redirecting...",
                                timer: 1200,
                                showConfirmButton: false,
                            }).then(() => {
                                window.location.href = res.url;
                            });
                            return;
                        }

                        // Otherwise keep your original behavior (just refresh the current page)
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Successfully Updated!",
                            timer: 1000,
                            showConfirmButton: false,
                        }).then(function () {
                            window.location.reload();
                        });
                    },
                    error: function (_xhr, _textStatus, errorThrown) {
                        console.error("Error:", errorThrown);
                        Swal.fire("Error!", "Something went wrong!", "error");
                    },
                });
            });
        }


        function loadChecklistProgress(levelId,loan_id) {
            $.ajax({
                type: "GET",
                url: `/load_checklist/${levelId}/${loan_id}`,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data) {

                    if (data.success) {
                        const total = data.checklist.length;
                        const completed = data.checklist.filter(item => parseInt(item.status) === 1).length;

                        // Handle cases with no checklist items
                        const progressText = total > 0 ? `${completed}/${total}` : `0/0`;

                        // Update progress as a fraction (e.g., 1/3)
                        $(`#checklist_progress_${levelId}`).text(progressText);

                    } else {
                        Swal.fire("Error!", "Failed to load checklist progress!", "error");
                    }
                },
                error: function (xhr) {
                    console.log("Error:", xhr.responseText);
                },
            });
        }


        function toggleChecklist(levelId,loan_id) {
            const row = $(`#checklist_row_${levelId}`);
            if (row.is(':visible')) {
                row.hide();
            } else {
                loadChecklist(levelId,loan_id);
                row.show();
            }
        }

        function loadChecklist(levelId, loan_id) {
            $.ajax({
                type: "GET",
                url: `/load_checklist/${levelId}/${loan_id}`,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data) {
                    if (data.success) {
                        let checklistHtml = `<ul class="list-group">`;
                        data.checklist.forEach(item => {
                            const isMarked = parseInt(item.status) === 1;
                            const rowStyle = isMarked
                                ? "background-color:#b3e5af; color: Green; height: 40px;" // Green background, white text, reduced height
                                : "background-color:white; color: Gray; height: 40px;"; // Red background, white text, reduced height
                            const buttonLabel = isMarked ? "Remove Checked" : "Checked";
                            const buttonStyle = isMarked
                                ? "background-color: #f8f9fa; color:red;" // Light background with green text
                                : "background-color: #f8f9fa; color: green;"; // Light background with red text

                            checklistHtml += `
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="${rowStyle}">
                            ${item.description}
                            <button class="btn btn-sm" style="${buttonStyle}" onclick="markChecklistItem(${item.id}, ${levelId}, ${item.status}, ${loan_id})">
                                ${buttonLabel}
                            </button>
                        </li>`;
                        });
                        checklistHtml += `</ul>`;
                        $(`#checklist_container_${levelId}`).html(checklistHtml);
                    } else {
                        Swal.fire("Error!", "Failed to load checklist!", "error");
                    }
                },
                error: function (xhr) {
                    console.log("Error:", xhr.responseText);
                },
            });
        }




        function markChecklistItem(itemId, levelId, currentStatus,loan_id) {
            const newStatus = currentStatus === 1 ? 0 : 1; // Toggle status (1 -> 0, 0 -> 1)
            const action = newStatus === 1 ? "mark this item as completed" : "remove the mark";

            Swal.fire({
                title: "Are you sure?",
                text: `Do you want to ${action}?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, proceed!",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed to toggle the checklist item status
                    $.ajax({
                        type: "POST",
                        url: `/update_checklist/${itemId}`,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: { status: newStatus },
                        success: function (data) {
                            if (data.success) {
                                // Reload the checklist to reflect changes
                                loadChecklist(levelId,loan_id);
                                // Refresh checklist progress after updating the database
                                loadChecklistProgress(levelId,loan_id);

                                // Show success notification
                                Swal.fire(
                                    newStatus === 1 ? "Marked!" : "Unmarked!",
                                    `The checklist item has been ${newStatus === 1 ? "marked as completed" : "unmarked"}.`,
                                    "success"
                                );
                            } else {
                                Swal.fire("Error!", "Failed to update checklist item!", "error");
                            }
                        },
                        error: function (xhr) {
                            console.log("Error:", xhr.responseText);
                            Swal.fire("Error!", "An unexpected error occurred!", "error");
                        },
                    });
                }
            });
        }

    </script>

                    <script>
                        function printTable() {
                            // Get the table element
                            var table = document.querySelector('#np .table');


                            // Customer details (Replace placeholder with dynamic values if needed)
                            var customerDetails = `
            <div ">
                <h3 style="font-weight: bold; text-align: center;  background-color: #f0f0f0; color: black;padding: 10px; margin-bottom: 20px;">Loan And Payment Details</h3>
                <p><strong>Customer Name:</strong> {{ $customers->First_Name }} {{ $customers->Last_Name }}</p>
                <p><strong>Customer No:</strong> {{ $customers->cus_number }}</p>
                <p><strong>Contact No:</strong> {{ $customers->Contact_No }}</p>
                <p><strong>Loan No:</strong> {{ $loan->Loan_No }}</p>

                <br>
                 <table class="table table-bordered table-sm">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Loan Amount</th>
                                    <th scope="col">Total Loan Amount</th>
                                    <th scope="col">Total Paid Amount</th>
                                    <th scope="col">Total Balance</th>
                                    <th scope="col">Capital Balance</th>
                                    <th scope="col">Loan Maturity Date</th>
                                    <th>Loan Status</th>

                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{ number_format($loan->Amount, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->Total_Loan_Amount, 2, '.', ',') }}</td>
                                    <td>{{ number_format($total_paid_amount, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->Balance_Amount, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->capital_balance, 2, '.', ',') }}</td>
                                    <td>{{ $installments->isNotEmpty() ? $installments->last()->Installment_Date : '-' }}</td>
                                    <td style="color: {{ $loan->Balance_Amount > 0 ? 'red' : 'green' }};">
                                        <Strong>
                                            {{ $loan->Balance_Amount > 0 ? 'Ongoing' : 'Settled' }}
                            </Strong></td>

                    </tr>
                    </tbody>
                </table>
                <br> <br>
                 <h3 style="font-weight: bold; text-align: center; background-color: #f0f0f0; color: black; padding: 10px; margin-bottom: 20px;">Payment Details</h3>

                 <div class="card-body">
                            <div class="table-responsive custom-scrollbar">
                                <table class="table table-bordered table-sm table-striped">
                                    <thead class="sticky-top bg-white">
                                    <tr>
                                        <th scope="col">Date</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col" style="width: 40%">Comment</th>
                                         <th scope="col">Payment Type</th>
                                         <th scope="col">User</th>

                                    </tr>
                                    </thead>
                                    <tbody class="custom-scrollbar" style="max-height: 400px;">
@foreach ($customer_payments as $customer_payment)
                            <tr>
                                <td>{{ $customer_payment->Date }} - {{ $customer_payment->time }}</td>
                                        <td>{{ $customer_payment->Description }}</td>
                                        <td>{{ number_format($customer_payment->Amount, 2, '.', ',') }}</td>
                                        <td>{{ $customer_payment->comment }}</td>
                                         <td>{{ $customer_payment->Payment_type }}   </td>
 <td>{{ $customer_payment->Full_Name }} ( {{ $customer_payment->Designation }} - {{ $customer_payment->email }} )</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
    </div>
`;

                            // Create a new window for printing
                            var printWindow = window.open('', '', 'height=600,width=800');
                            printWindow.document.write('<html><head><title>Payment History</title>');
                            printWindow.document.write('<style>');
                            printWindow.document.write('table { width: 100%; border-collapse: collapse; }');
                            printWindow.document.write('table, th, td { border: 1px solid black; padding: 10px; text-align: left; }');
                            printWindow.document.write('h3, h4 { margin: 0; padding: 5px 0; }');
                            printWindow.document.write('</style>');
                            printWindow.document.write('</head><body>');
                            printWindow.document.write(customerDetails);
                            printWindow.document.write('</body></html>');
                            printWindow.document.close();
                            printWindow.print();
                        }
                    </script>






                    <!-- Bootstrap JS and jQuery (if not included already) -->
                    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
                    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
                    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                    <script>
                        function openSlip(slipPath) {
                            window.open(slipPath, '_blank');
                        }

                        function payment_slip(id) {
                            openModal();
                            load_payment_reciept(id);
                        }

                        function openModal() {
                            document.getElementById('overlay').style.display = 'block';
                            document.getElementById('printerModal').style.display = 'block';
                        }

                        function closeModal() {
                            document.getElementById('overlay').style.display = 'none';
                            document.getElementById('printerModal').style.display = 'none';
                        }

                        // Close modal when clicking outside
                        document.getElementById('overlay').addEventListener('click', closeModal);



                        function load_payment_reciept(id) {
                            $.ajax({
                                type: "POST",
                                url: "/view_payment_load_reciept/" + id + "/0",
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                },
                                success: function (data, textStatus, xhr) {
                                    console.log(data);
                                    let customer = data.customer;
                                    let payment = data.payment;
                                    let loan = data.loan;
                                    let user = data.user;
                                    let chequeDetails = data.cheque_details;

                                    if (xhr.status === 200) {
                                        if (data.point_check === "1") {

                                            $('#loyalty_section').show();
                                        } else {

                                            $('#loyalty_section').hide();
                                        }
                                        $("#Inv_number").text("Receipt No. :"+payment.idCustomer_Payments);
                                        $("#customer_name").text(customer.First_Name + " " + customer.Last_Name);
                                        $("#customer_number").text(customer.cus_number);
                                        $("#loyalty_points").text(parseFloat(data.points_to_add).toFixed(2));
                                        $("#loan_number").text(loan.Loan_No);
                                        $("#payment_date").text(payment.Date);
                                        $("#payment_time").text(payment.time);
                                        $("#payment_type_view").text(payment.Payment_type);

                                        if (parseFloat(data.panelty_balance) > 0) {
                                            $("#panelty_balance").text(parseFloat(data.panelty_balance).toFixed(2));
                                            $("#tot_balance").text(parseFloat(data.tot_balance).toFixed(2));

                                            // Show the relevant sections if they are hidden
                                            $(".payment-info.balance").show();
                                        } else {
                                            // Optionally hide the sections if no penalty balance exists
                                            $(".payment-info.balance").hide();
                                        }
                                        if (payment.Payment_type === "Cheque") {
                                            console.log(chequeDetails.Cheque_No
                                            );
                                            // Show the cheque section
                                            $('#cheque_section').show();

                                            // // Populate the cheque details
                                            $("#cheque_no").text(chequeDetails.Cheque_No);
                                            $("#cheque_date").text(chequeDetails.Cheque_Date);
                                            $("#cheque_name").text(chequeDetails.Name_On_The_Cheque);
                                            $("#cheque_type").text(chequeDetails.Cheque_Type);
                                        } else {
                                            $('#cheque_section').hide(); // Hide the cheque section if payment type is not "Cheque"
                                        }


                                        $("#loan_amount").text(parseFloat(loan.Amount).toFixed(2));
                                        $("#full_loan_amount").text(parseFloat(loan.Total_Loan_Amount).toFixed(2));
                                        $("#payed_amount").text(parseFloat(payment.Amount).toFixed(2));
                                        $("#capital_balance").text(parseFloat(loan.Balance_Amount).toFixed(2));


                                        $("#signature").text(user.Full_Name);
                                    }
                                }
                            });
                        }

                        $(document).ready(function() {
                            $('#submitComment').click(function() {
                                let comment = $('#commentText').val();
                                let loanId = '{{ $loan->idCustomer_Loan }}'; // Loan ID from Blade

                                if (comment === '') {
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
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: "{{ route('loan.comment.store') }}", // Your route here
                                            type: "POST",
                                            data: {
                                                _token: '{{ csrf_token() }}',
                                                comment: comment,
                                                loan_id: loanId
                                            },
                                            success: function(response) {
                                                Swal.fire({
                                                    position: "center",
                                                    icon: "success",
                                                    title: "Comment has been submitted!",
                                                }).then(function () {
                                                    window.location.reload();
                                                });
                                            },
                                            error: function(xhr) {
                                                Swal.fire('Error', 'Something went wrong', 'error');
                                            }
                                        });
                                    }
                                });
                            });



                            // Handle the Loan Log button click
                            var table = $('#loanLogTable').DataTable({
                                "columnDefs": [
                                    { "orderable": true, "targets": 0 },  // Enable sorting only for the first column
                                    { "orderable": false, "targets": "_all" }  // Disable sorting for all other columns
                                ]
                            });





                            $('.btn-danger').click(function(e) {
                                e.preventDefault();

                                // Assuming loanId is available in the view or fetched dynamically
                                var loanId = "{{ $loan->idCustomer_Loan }}"; // Replace with dynamic data

                                // Send AJAX request to fetch loan logs
                                $.ajax({
                                    url: "{{ route('loan-log.fetch', ':loanId') }}".replace(':loanId', loanId),
                                    method: 'GET',
                                    success: function(response) {
                                        // Clear the table body
                                        table.clear().draw();

                                        // Loop through response and add rows dynamically
                                        $.each(response, function(index, log) {
                                            table.row.add([

                                                log.Date_Time,
                                                log.Type_ID,
                                                log.Description,
                                                parseFloat(log.Amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Panelty_Payment).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Interest_Payment).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Capital_Payment).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Savings_Payment).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Extra_Payment).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Recovery_Amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Panelty_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Interest_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Capital_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Saving_Account_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Extra_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Recovery_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
                                                parseFloat(log.Total_Pending_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
                                            ]).draw(false);
                                        });

                                        // Show the modal
                                        $('#loanLogModal').modal('show');
                                    },
                                    error: function(xhr) {
                                        console.log('Error:', xhr);
                                    }
                                });
                            });


                            // Trigger the modal open event
                            $('#loanCommentModal').on('show.bs.modal', function() {
                                let loanId = '{{ $loan->idCustomer_Loan }}'; // Loan ID from Blade

                                // Clear previous table content
                                $('#loanCommentTable tbody').empty();

                                // Make an AJAX request to fetch comments
                                $.ajax({
                                    url: "{{ route('loan.comment.fetch') }}", // Route to fetch comments
                                    type: "GET",
                                    data: {
                                        loan_id: loanId
                                    },
                                    success: function(response) {
                                        if (response.comments.length > 0) {
                                            // Append fetched comments to the table
                                            $.each(response.comments, function(index, comment) {
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
                                    error: function(xhr) {
                                        console.log('Error fetching data');
                                    }
                                });
                            });


                            // Handle the Loan Log button click
                            $('.btn-warning').click(function(e) {
                                $('#loanCommentModal').modal('show');
                            });


                        });


                        function undo_payment(id) {

                            $('#loanLogModal').modal('hide');
                            Swal.fire({
                                title: "Are you sure?",
                                text: "Do you want to undo this payment?",
                                icon: "warning",
                                input: "textarea", // Adds a text box for the reason
                                inputPlaceholder: "Enter your reason here...",
                                showCancelButton: true,
                                confirmButtonColor: "#3085d6",
                                cancelButtonColor: "#d33",
                                confirmButtonText: "Yes, Undo it!",
                                preConfirm: (reason) => {
                                    if (!reason) {
                                        Swal.showValidationMessage("Reason is required!");
                                    }
                                    return reason;
                                },
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    const reason = result.value; // Get the entered reason

                                    $.ajax({
                                        type: "POST", // Use POST for sending data securely
                                        url: "/undoPayment/" + id,
                                        headers: {
                                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                        },
                                        data: {reason: reason}, // Pass the reason to the backend
                                        success: function (data, textStatus, xhr) {
                                            if (xhr.status === 200) {
                                                Swal.fire({
                                                    position: "center",
                                                    icon: "success",
                                                    title: "Payment removed successfully!",
                                                }).then(function () {
                                                    window.location.reload();
                                                });
                                            } else {
                                                Swal.fire("Error!", "Failed to undo payment!", "error");
                                            }
                                        },
                                        error: function (xhr, textStatus, errorThrown) {
                                            Swal.fire("Error!", "An error occurred. Please try again.", "error");
                                            console.log("Error:", errorThrown);
                                        },
                                    });
                                }
                            });
                        }

                        function downloadExcel() {
                            // Get the table element
                            const table = document.getElementById("loanLogTable");

                            // Convert the table to a SheetJS worksheet
                            const worksheet = XLSX.utils.table_to_sheet(table);

                            // Create a new workbook and append the worksheet
                            const workbook = XLSX.utils.book_new();
                            XLSX.utils.book_append_sheet(workbook, worksheet, "Loan Log");

                            // Export the workbook as an Excel file
                            XLSX.writeFile(workbook, "LoanLog.xlsx");
                        }

                    </script>

                    <script>
                        $(document).ready(function() {

                            function formatCurrency(n) {
                                return Number(parseFloat(n || 0).toFixed(2)).toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            }

                            $(document).on('click', '.btn-show-double-entries', function(e) {
                                e.preventDefault();

                                var loanNo = $(this).data('loan-no') || '';
                                var loanId = $(this).data('loan-id'); // now taken from button attribute

                                if (!loanId) {
                                    alert('Loan ID missing.');
                                    return;
                                }

                                $('#deLoanNo').text(loanNo);
                                $('#doubleEntriesTable tbody').html('');
                                $('#totalDebit').text('0.00');
                                $('#totalCredit').text('0.00');
                                $('#deError').addClass('d-none').text('');

                                // show modal
                                var modalEl = document.getElementById('doubleEntriesModal');
                                var modal = new bootstrap.Modal(modalEl, { keyboard: true });
                                modal.show();

                                // fetch entries
                                $.ajax({
                                    url: '/double-entries/' + loanId,
                                    method: 'GET',
                                    dataType: 'json',
                                    success: function(res) {
                                        if (!res.data || res.data.length === 0) {
                                            $('#doubleEntriesTable tbody').html('<tr><td colspan="4" class="text-center">No entries found for this loan.</td></tr>');
                                            return;
                                        }

                                        var rows = res.data;
                                        var html = '';
                                        var totalDebit = 0;
                                        var totalCredit = 0;

                                        rows.forEach(function(r) {
                                            // guard number fields
                                            var debit = parseFloat(r.debit || 0);
                                            var credit = parseFloat(r.credit || 0);

                                            html += '<tr>';
                                            html += '<td class="text-start">' + (r.account_name || '') + '</td>';
                                            html += '<td class="text-end">' + formatCurrency(debit) + '</td>';
                                            html += '<td class="text-end">' + formatCurrency(credit) + '</td>';
                                            html += '<td class="text-start">' + (r.contra_account || '') + '</td>';
                                            html += '</tr>';

                                            totalDebit += debit;
                                            totalCredit += credit;
                                        });

                                        $('#doubleEntriesTable tbody').html(html);
                                        $('#totalDebit').text(formatCurrency(totalDebit));
                                        $('#totalCredit').text(formatCurrency(totalCredit));
                                    },
                                    error: function(xhr) {
                                        var msg = 'Failed to load entries.';
                                        if (xhr && xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
                                        $('#deError').removeClass('d-none').text(msg);
                                        $('#doubleEntriesTable tbody').html('<tr><td colspan="4" class="text-center">Error loading data</td></tr>');
                                    }
                                });
                            });

                        });
                    </script>


                    <script>
                        $(function() {
                            $('#btnDeleteLoan').on('click', function() {
                                $('#deleteLoanModal').modal('show');
                            });

                            $('input[name="mode"]').on('change', function() {
                                const mode = $(this).val();
                                if (mode === 'password') {
                                    $('#passwordBlock').removeClass('d-none');
                                    $('#approvalNote').addClass('d-none');
                                } else {
                                    $('#passwordBlock').addClass('d-none');
                                    $('#approvalNote').removeClass('d-none');
                                }
                            });

                            $('#btnProceedDelete').on('click', function(e) {
                                e.preventDefault();
                                const form = $('#deleteLoanForm');
                                const data = form.serialize();

                                if (window.Swal) {
                                    Swal.fire({
                                        title: 'Are you sure?',
                                        text: 'This will permanently delete the loan after reversal & archiving.',
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Yes, proceed',
                                        cancelButtonText: 'Cancel'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            doAjax(data);
                                        }
                                    });
                                } else if (confirm('This will permanently delete the loan after reversal & archiving. Continue?')) {
                                    doAjax(data);
                                }
                            });

                            function doAjax(payload) {
                                $.ajax({
                                    url: "{{ route('loan.destroy_loan') }}",
                                    method: "POST",
                                    data: payload,
                                    success: function(res) {
                                        if (res.need_approval) {
                                            Swal && Swal.fire('Request Sent', 'Approval request was created successfully.', 'info');
                                            $('#deleteLoanModal').modal('hide');
                                            return;
                                        }

                                        const msg = res.message || 'Loan deleted successfully.';

                                        if (window.Swal) {
                                            Swal.fire('Done', msg, 'success').then(() => {
                                                window.location.href = "/payment_step_1"; // ✅ redirect here
                                            });
                                        } else {
                                            alert(msg);
                                            window.location.href = "/payment_step_1"; // ✅ fallback redirect
                                        }
                                    },
                                    error: function(xhr) {
                                        let msg = 'Operation failed';
                                        if (xhr.responseJSON && xhr.responseJSON.error) msg = xhr.responseJSON.error;
                                        if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                                        Swal && Swal.fire('Error', msg, 'error');
                                    }
                                });
                            }

                        });

                    </script>




@endsection




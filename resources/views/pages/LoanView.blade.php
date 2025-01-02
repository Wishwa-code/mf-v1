@php use Carbon\Carbon; @endphp
@extends('layout.admin')

@section('head')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css"
          href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">
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
    </style>

@endsection

@section('content')

    <div class="custom-container py-4">

        <div class="row justify-content-center">



            <!-- Highlighted Loan Details Card -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow highlight-card">
                    <div class="card-header">
                        Loan Summary
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Loan Amount</th>
                                    <th scope="col">Total Loan Amount</th>
                                    <th scope="col">Total Paid Amount</th>
                                    <th scope="col">Saving Amount</th>
                                    <th scope="col">Saving Balance</th>
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
                                    <td>{{ number_format($Saving_amountSum, 2, '.', ',') }}</td>
                                    <td>{{ number_format($savingBalanceSum, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->Balance_Amount, 2, '.', ',') }}</td>
                                    <td>{{ number_format($loan->capital_balance, 2, '.', ',') }}</td>
                                    <td>{{ $installments->last()->Installment_Date }}</td>
                                    <td style="color:
    {{ $loan->Status == -1 ? 'orange' : ($loan->Status == 0 ? 'red' : 'green') }};">
                                        <strong>
                                            {{ $loan->Status == -1 ? 'Pending Loan' : ($loan->Status == 0 ? 'Ongoing Loan' : 'Settled') }}
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
                                            <td style="text-align: left">{{ $loan->Loan_No }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Product Name</strong></td>
                                            <td style="text-align: left">{{ $loan->Loan_Category_idLoan_Category }}
                                                - {{ $Loan_Category->Name }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Date & Time</strong></td>
                                            <td style="text-align: left">{{ $loan->Date_Time }}</td>
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
                                            <td style="text-align: left">{{ $loan->Installment_Count }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left"><strong>Interest Amount</strong></td>
                                            <td style="text-align: left">{{ $loan->Interest_Amount }}</td>
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
                                            <td style="text-align: left">{{ $loan->Total_Loan_Amount }}</td>
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

                <br>
                <div class="card shadow">
                    <div class="card-header">
                        Uploaded Document Details
                    </div>
                    <div class="card-body">
                        <div class="table-responsive custom-scrollbar">
                            <table class="table table-bordered table-sm table-striped">
                                <thead class="sticky-top bg-white">
                                <tr>

                                    <th scope="col">Name</th>
                                    <th scope="col">File</th>
                                </tr>
                                </thead>
                                <tbody class="custom-scrollbar" style="max-height: 400px;">
                                @foreach ($documents as $document)
                                    <tr>

                                        <td>{{ $document->Name }}</td>
                                        <td>
                                            <a href="{{ asset('storage/' . $document->Path) }}" target="_blank">View
                                                File</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <br>
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

                                        @if($payment_delete_status===1)
                                            <td>
                                                <button class="reverse-payment-btn btn btn-outline-danger"
                                                        onclick="undo_payment({{ $customer_payment->idCustomer_Payments }})">
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
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="loanLogModalLabel">Loan Log</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                    <table class="table table-striped" id="loanLogTable">
                                        <thead>
                                        <tr>
                                            <th>Date/Time</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Penalty Payment</th>
                                            <th>Interest Payment</th>
                                            <th>Capital Payment</th>
                                            <th>Penalty Balance</th>
                                            <th>Interest Balance</th>
                                            <th>Capital Balance</th>
                                            <th>Total Pending Balance</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- AJAX loaded data will go here -->
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





                @endsection
                @section('script')


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
                                    <td>{{ $installments->last()->Installment_Date }}</td>
                                    <td style="color: {{ $loan->Balance_Amount > 0 ? 'red' : 'green' }};">
                                        <Strong>
                                            {{ $loan->Balance_Amount > 0 ? 'Ongoing Loan' : 'Settled' }}
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
                    <script>

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
                                        $('#loanLogTable tbody').empty();

                                        $.each(response, function(index, log) {
                                            $('#loanLogTable tbody').append(
                                                '<tr>' +
                                                '<td>' + log.Date_Time + '</td>' +
                                                '<td>' + log.Description + '</td>' +
                                                '<td>' + parseFloat(log.Amount).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                                                '<td>' + parseFloat(log.Panelty_Payment).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                                                '<td>' + parseFloat(log.Interest_Payment).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                                                '<td>' + parseFloat(log.Capital_Payment).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                                                '<td>' + parseFloat(log.Panelty_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                                                '<td>' + parseFloat(log.Interest_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                                                '<td>' + parseFloat(log.Capital_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                                                '<td>' + parseFloat(log.Total_Pending_Balance).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</td>' +
                                                '</tr>'
                                            );
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
                    </script>

@endsection




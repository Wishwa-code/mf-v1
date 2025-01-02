@php use Carbon\Carbon; @endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ffffff;
            margin: 25px 10px 00px 10px;
        }

        .page-header, .page-footer, .page-header2 {

            position: fixed;
            color: #7e7e7e;
            font-size: 8px;
        }

        .page-header {
            width: 50%;
            top: 0;
            text-align: left;
        }

        .page-header2 {
            width: 100%;
            top: 0;
            text-align: right;
        }

        .page-footer {
            width: 100%;
            bottom: 0;
            text-align: center;
        }

        .page-number:before {
            content: counter(page);
        }


        h1, h2 {
            text-align: center;
            color: #333;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            padding: 0.25rem 1.25rem;
            background-color: #1A2942;
            color: #fff;
            border-radius: 8px 8px 0 0;
            font-size: 1rem;
            text-align: center;
        }

        .card-header2 {
            padding: 0.25rem 1.25rem;
            background-color: #949494;
            color: #fff;

            font-size: 0.8rem;
            text-align: center;
        }

        .card-body {
            padding: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        th, td {
            padding-top: 10px;
            padding-bottom: 10px;
            padding-left: 2px;
            padding-right: 2px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 0.8rem;
        }

        th {
            background-color: #ffffff;
            color: #000000;
        }

        @media (max-width: 600px) {
            th, td {
                padding: 6px;
            }

            h1, h2 {
                font-size: 1.5rem;
            }
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>

<div class="page-header">
    <strong>Loan Number: {{ $loan->Loan_No }}</strong>

</div>
<div class="page-header2">

    <strong>{{session('company_name')}}</strong>
    <hr>
</div>

<div class="page-footer">
    <hr>
    Page <span class="page-number"></span> : Full Loan Detail Report : {{ $loan->Loan_No }}
</div>


<h2>Full Loan Detail Report</h2>
<br>
<div class="card">
    <div class="card-header">
        Loan Summary
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table>
                <thead>
                <tr style="align-content: center">
                    <th>Loan Amount</th>
                    <th>Total Loan Amount</th>
                    <th>Total Paid Amount</th>
                    <th>Total Balance</th>
                    <th>Capital Balance</th>
                    <th>Loan Maturity Date</th>
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
                            {{ $loan->Balance_Amount > 0 ? 'Ongoing Loan' : 'Closed' }}
                        </Strong></td>

                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<br><br>

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
                            <td style="text-align: left; width: 30%"><strong>Loan Number</strong></td>
                            <td style="text-align: left; width:70%">{{ $loan->Loan_No }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>Product Name</strong></td>
                            <td style="text-align: left">{{ $Loan_Category->Name }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>Date</strong></td>
                            <td style="text-align: left">{{ $loan->Date_Time }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>Amount</strong></td>
                            <td style="text-align: left">{{ $loan->Amount }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>Interest Rate</strong></td>
                            <td style="text-align: left">{{ $loan->Interest_Rate }} %</td>
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
                            <td style="text-align: left">{{ $loan->Total_Other_Amount }}</td>

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
                            <td style="text-align: left">{{ $User->Full_Name }}</td>
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
                            <td style="text-align: left"><strong>Lending Officer</strong></td>
                            <td style="text-align: left">{{ $Lending_Officer->id }}
                                - {{ $Lending_Officer->Full_Name }}</td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="page-break"></div> <!-- Page break here -->


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
                            <td style="text-align: left; width: 30%"><strong>Customer No</strong></td>
                            <td style="text-align: left; width: 70%">{{ $customers->cus_number }} </td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>Name</strong></td>
                            <td style="text-align: left">{{ $customers->Title }}
                                .{{ $customers->First_Name }} {{ $customers->Last_Name }}</td>
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
                            <td style="text-align: left"><strong>Lan Number</strong></td>
                            <td style="text-align: left">{{ $customers->Landline }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>NIC</strong></td>
                            <td style="text-align: left">{{ $customers->Nic }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>Gender</strong></td>
                            <td style="text-align: left">{{ $customers->Gender }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>Date Of Birth</strong></td>
                            <td style="text-align: left">{{ $customers->Dob }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>Current Address</strong></td>
                            <td style="text-align: left">{{ $customers->Address }},{{ $customers->Address_02 }}
                                ,{{ $customers->Address_03 }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>Permanent Address</strong></td>
                            <td style="text-align: left">{{ $customers->Per_Address_01 }}
                                ,{{ $customers->Per_Address_02 }},{{ $customers->Per_Address_03 }}</td>
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
                            <td style="text-align: left"><strong>Customer Note</strong></td>
                            <td style="text-align: left">{{ $customers->Note }}</td>
                        </tr>
                        <tr>
                            <td style="text-align: left"><strong>Risk Level</strong></td>
                            <td style="text-align: left">
                                @php
                                    $riskLevels = [
                                        1 => 'Low',
                                        2 => 'Normal',
                                        3 => 'High',
                                        4 => 'Warning'
                                    ];
                                @endphp
                                {{ $riskLevels[$customers->Customer_Risk_Level] ?? 'Unknown' }}
                            </td>
                        </tr>

                        </tbody>
                    </table>
                    <br>
                    <br>
                    <div class="table-responsive custom-scrollbar">
                        <table class="table table-bordered table-sm">
                            <tbody>
                            <tr>
                                <td style="text-align: left; width: 30%"><strong>Guardian Name</strong></td>
                                <td style="text-align: left; width: 70%">{{ $customers->Gua_title }} {{ $customers->Gua_name }}</td>
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

    <div class="page-break"></div> <!-- Page break here -->


    @if (count($witnessDetails) > 0)
        <div class="card-header">
            Guarantee / Cross Customer Details
        </div>

        @foreach ($witnessDetails as $index => $witness)
            <div class="col-md-6 witness-card">
                <div class="card shadow">
                    <div class="card-header2">
                        {{ $witness['type'] === 'Customer' ? 'Cross Customer' : $witness['type'] }} {{ $index + 1 }} Details
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-sm">
                            <tbody>
                            <tr>
                                <td style="text-align: left; width: 30%"><strong>First Name</strong></td>
                                <td style="text-align: left; width: 70%">{{ $witness['details']->First_Name }}</td>
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
    @endif

    <div class="col-md-6 card shadow">
        <div class="card-header">
            Installment Schedule
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-xs table-striped" style="width: 103%;">
                    <thead class="sticky-top bg-white">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Date</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Capital Amount</th>
                        <th scope="col">Interest Amount</th>
                        <th scope="col">Penalty Amount</th>
                        <th scope="col">Total Amount</th>
                        <th scope="col">Paid Amount</th>
                        <th scope="col">Penalty Balance</th>
{{--                        <th scope="col">Installment Balance</th>--}}
                        <th scope="col">Total Balance</th>
                        <th scope="col">Status</th>
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
                            <td>{{ number_format($installment->Installment_Amount, 2, '.', ',') }}</td>
                            <td>{{ number_format($installment->capital_amount, 2, '.', ',') }}</td>
                            <td>{{ number_format($installment->interest_amount, 2, '.', ',') }}</td>
                            <td>{{ number_format($installment->Panalty_Amount, 2, '.', ',') }}</td>
                            <td>{{ number_format($installment->Total_Amount, 2, '.', ',') }}</td>
                            <td>{{ number_format($installment->Paid_Amount, 2, '.', ',') }}</td>
                            <td>{{ number_format($installment->Panalty_Balance, 2, '.', ',') }}</td>
{{--                            <td>{{ number_format($installment->Installment_Balance, 2, '.', ',') }}</td>--}}
                            <td>{{ number_format($installment->Total_Balance, 2, '.', ',') }}</td>
                            <td>{{ $statusText }}</td>

                            <!-- Colored bulb icon -->
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <br>
{{--    <div class="card shadow">--}}
{{--        <div class="card-header">--}}
{{--            Uploaded Document Details--}}
{{--        </div>--}}
{{--        <div class="card-body">--}}
{{--            <div class="table-responsive custom-scrollbar">--}}
{{--                <table class="table table-bordered table-sm table-striped">--}}
{{--                    <thead class="sticky-top bg-white">--}}
{{--                    <tr>--}}

{{--                        <th scope="col">Name</th>--}}
{{--                        <th scope="col">File</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody class="custom-scrollbar" style="max-height: 400px;">--}}
{{--                    @foreach ($documents as $document)--}}
{{--                        <tr>--}}

{{--                            <td>{{ $document->Name }}</td>--}}
{{--                            <td>--}}
{{--                                <a href="{{ asset('storage/' . $document->Path) }}" target="_blank">View File</a>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

    <div class="page-break"></div> <!-- Page break here -->
    <br>
    <div class="card shadow">
        <div class="card-header">
            Payment History
        </div>
        <div class="card-body">
            <div class="table-responsive custom-scrollbar">
                <table class="table table-bordered table-sm table-striped">
                    <thead class="sticky-top bg-white">
                    <tr>
                        {{--                            <th scope="col">Payment ID</th>--}}
                        <th scope="col">Date</th>
                        <th scope="col">Description</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Comment</th>
{{--                        <th scope="col">Slip</th>--}}
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
{{--                            <td>{{ $customer_payment->Slip }}</td>--}}
                            <td>{{ $customer_payment->Full_Name }} ( {{ $customer_payment->Designation }} - {{ $customer_payment->email }} )</td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div>
</body>
</html>

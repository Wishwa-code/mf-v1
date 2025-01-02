@extends('layout.admin')

@section('head')

    <style>
        .style-tr>td {
            padding: 2px 15px;
        }
    </style>
    <style>
        .form-check-input {
            transform: scale(1.2);
            margin-left: 0.5rem;
        }

        .fw-bold {
            font-weight: 900 !important;
        }

        .fw-size {
            font-size: 17px;
            color: #7d2f23;
        }

        .text-left {
            text-align: left;
        }

        .align-items-center {
            display: flex;
            align-items: center;
        }

        thead {
            background-color: #dedede; /* Light blue color */
            color: #6c4b4b; /* Darker blue text for contrast */
            /*margin-top:0px;*/
        }
        thead th {
            padding: 5px !important;

        }
        tbody td {
            padding: 7px !important;
        }


        .section-break {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #c9c9c9; /* Background color for the title */
            color: #726262; /* Text color for the title */
            padding: 5px; /* Padding for the title */
            font-weight: bold;
            font-size: 16px;
            border-radius: 5px 5px 0 0; /* Rounded corners at the top */
        }

        .section-title:after {
            content: '';
            display: block;
            height: 0px; /* Height of the colored line */
            background-color: #8f8f8f; /* Color of the line */
            border-radius: 0 0 5px 5px; /* Rounded corners at the bottom */
            margin-top: 5px;
        }

        .btn-10 {
            width: 20%;
            margin: 10px;
            font-size: 15px;
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

    </style>



@endsection

@section('content')
    <div>

        <!-- start page title -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h3 class="my-4">Reduce Capital (Loan No : {{$loan->Loan_No}})</h3>

                                </div>

                                <div class="col-lg-12">

                                    <div class="mb-3" hidden>
                                        <label for="simpleinput" class="form-label">Type</label>
                                        <select class="form-control"  id="type" onchange="change_type(this.value)">
                                            <option value="0">Individual</option>
                                            <option value="1">Group</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="center_feild" hidden>
                                        <label for="simpleinput" class="form-label">Center</label>
                                        <select class="form-control"  id="center" onchange="change_group(this.value)">
                                            <option id="0">Select</option>
                                            @foreach($center as $item)
                                                <option value="{{$item->idCenter}}">{{ $item->Name }}-{{$item->Contact_no}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3" id="group_feild" hidden>
                                        <label for="simpleinput" class="form-label">Group</label>
                                        <select class="form-control"  id="group" onchange="load_customer(this.value)">

                                        </select>
                                    </div>




                                    <div class="mb-3" id="customer_field">
                                        <label for="simpleinput" class="form-label">Customer</label>
                                        <select class="form-control" id="customer" disabled>
                                            <option id="0">Select</option>
                                            @foreach($customers as $item)
                                                <option value="{{$item->idCustomer}}" {{ $item->idCustomer == $loan->Customer_idCustomer ? 'selected' : '' }}>
                                                    {{ $item->First_Name }} {{ $item->Last_Name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    <div class="mb-3" id="package_feild">
                                        <label for="simpleinput" class="form-label">Select Package</label>
                                        <select class="form-control"  id="package_details" disabled>
                                            <option id="0">Select</option>
                                            @foreach($product as $item)
                                                <option value="{{$item->idLoan_Category}}" {{ $item->idLoan_Category == $loan->Loan_Category_idLoan_Category ? 'selected' : '' }}>
                                                    {{$item->Name}}-{{$item->Loan_amount}}-{{$item->Interest_period}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3" id="package_feild">
                                        <label for="interest" class="form-label">Default Loan Interest (%)<span class="required-asterisk">*</span></label>
                                        <input type="text" id="loan_interest" class="form-control" onkeyup="calculateInterest()" disabled>
                                    </div>


{{--                                    <hr>--}}

                                    <div class="container" id="product_details">

                                        <div class="row mb-3 section-break" hidden>
                                            <div class="col-12">
                                                <div class="section-title">
                                                    Product Details
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="row mb-3" hidden>
                                                <div class="col-md-6" >
                                                    <div class="mb-3">
                                                        <label for="interest_method" class="form-label">Interest Method<span class="required-asterisk">*</span></label>
                                                        <select class="form-select" id="interest_method" disabled>
                                                            <option value="Flat Rate">Flat Rate</option>
                                                            <option value="Reducing Balance - Equal Installments">Reducing Balance - Equal Installments</option>
                                                            <option value="Reducing Balance - Equal Capital">Reducing Balance - Equal Capital</option>
                                                            <option value="Interest Only">Interest Only</option>
                                                            <option value="Draft">Draft</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="loan_amount" class="form-label">Loan Amount<span class="required-asterisk">*</span></label>
                                                        <input type="text" id="loan_amount" class="form-control" value="{{$loan->Amount}}" onkeyup="calculateInterest()" disabled>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3" hidden>
                                                <div class="col-md-3">

                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-3">
                                                        <label for="interest_period" class="form-label">Default Loan Interest Period<span class="required-asterisk">*</span></label>
                                                        <select class="form-select" id="interest_period" onchange="calculateInterest()" disabled>
                                                            <option value="Daily">Per Day</option>
                                                            <option value="Weekly">Per Week</option>
                                                            <option value="Per Month">Per Month</option>
                                                            <option value="Per Year">Per Year</option>
                                                            <option value="Per Loan">Per Loan</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label for="period_count" class="form-label">Loan Period<span class="required-asterisk">*</span></label>
                                                        <input type="number" id="interest_period_count" class="form-control" onkeyup="calculateInterest()" disabled>
                                                    </div>

                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label for="period_count" class="form-label" hidden>Type</label>
                                                        <select class="form-select" id="duration_period" onchange="calculateInterest()" disabled hidden>
                                                            <option value="Days">Days</option>
                                                            <option value="Weeks">Weeks</option>
                                                            <option value="Months">Months</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-md-3">
                                                        <div class="mb-3">
                                                            <label for="witnessCount" class="form-label">Guarantee Count<span class="required-asterisk">*</span></label>
                                                            <input type="number" id="guarantee_count" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row mb-3 section-break" hidden>
                                                <div class="col-12">
                                                    <div class="section-title">
                                                        Loan duration and Repayments
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3" hidden>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label for="loan_duration" class="form-label">Loan Duration<span class="required-asterisk">*</span></label>
                                                        <input type="number" id="loan_period" class="form-control" value="{{$loan->Installment_Count}}" onkeyup="calculateInterest()" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" hidden>
                                                    <div class="mb-3">
                                                        <label for="loan_duration" class="form-label">Type<span class="required-asterisk">*</span></label>
                                                        <select class="form-select" id="duration_period" disabled>
                                                            <option value="Days">Days</option>
                                                            <option value="Weeks">Weeks</option>
                                                            <option value="Months">Months</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">

                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="collection_type" class="form-label">Repayment Type<span class="required-asterisk">*</span></label>
                                                        <select class="form-select" id="repayment_type" onchange="calculateInterest()" disabled>
                                                            <option value="Daily">Daily</option>
                                                            <option value="Weekly">Weekly</option>
                                                            <option value="First Of The Month">First Of The Month</option>
                                                            <option value="End Of The Month">End Of The Month</option>
                                                            <option value="Twice A Month">Twice A Month</option>
                                                            <option value="On A Selected Date">On A Selected Date</option>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="row mb-3 section-break" hidden>
                                                <div class="col-12">
                                                    <div class="section-title">
                                                        Penalty Details
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3" hidden>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="panelty_rate" class="form-label">Penalty Percentage (%)<span class="required-asterisk">*</span></label>
                                                        <input type="text" id="penalty_percentage" class="form-control" onkeyup="calculateInterest()" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="penalty_period" class="form-label">Penalty Period<span class="required-asterisk">*</span></label>
                                                        <select class="form-select" id="penalty_period" onchange="calculateInterest()" disabled>
                                                            <option value="Daily">Per Day</option>
                                                            <option value="Weekly">Per Week</option>
                                                            <option value="Per Month">Per Month</option>
                                                            <option value="Per Installment">Per Installment</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3" hidden>

                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label for="panelty_rate_date" class="form-label">Penalty Start After<span class="required-asterisk">*</span></label>
                                                        <input type="number" id="penalty_date" class="form-control" onkeyup="calculateInterest()" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="mb-3">
                                                        <label for="loan_duration" class="form-label">Default Loan Duration<span class="required-asterisk">*</span></label>
                                                        <select class="form-select" id="duration_period" disabled>
                                                            <option value="Days">Days</option>
                                                            <option value="Weeks">Weeks</option>
                                                            <option value="Months">Months</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="mt-4" hidden>
                                                <div class="row mb-3 section-break">
                                                    <div class="col-12">
                                                        <div class="section-title">
                                                            Loan Charge
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div>
                                                        <div class="table-responsive-sm border border-1">
                                                            <table class="table table-centered mb-0" id="loan_charge_table">
                                                                <thead>
                                                                <tr>
                                                                    <th>Description</th>
                                                                    <th style="float: right;">Amount</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div> <!-- end table-responsive-->
                                                    </div> <!-- end card-body-->
                                                </div> <!-- end card-->

                                                <div class="row mt-4 mb-3">
                                                    <div class="col-12">
                                                        <!-- Total Loan Charges with value -->
                                                        <div class="row mb-3">
                                                            <div class="col-6 fw-bold">Total Loan Charges</div>
                                                            <div class="col-6 fw-bold text-left" id="total_loan_charge">0.00</div>
                                                        </div>
                                                        <!-- Loan Charges Balance checkbox -->
                                                        <div class="row mb-3">
                                                            <div class="col-12 fw-bold d-flex align-items-center">
                                                                <div class="col-6 fw-bold">Add Total Other Chargers To The Loan Amount</div>
                                                                <input type="checkbox" class="form-check-input ms-2" id="loanChargesBalance" onchange="checkLoanChargesBalance()">
                                                            </div>
                                                        </div>
                                                        <!-- Total Loan Amount -->
                                                        <div class="row mb-3">
                                                            <div class="col-6 fw-bold fw-size ">Capital Amount</div>
                                                            <div class="col-6 text-left fw-bold fw-size " id="total_capital_amount">0.00</div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-6 fw-bold fw-size ">Interest Amount</div>
                                                            <div class="col-6 text-left fw-bold fw-size " id="total_interest_amount">0.00</div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-6 fw-bold fw-size ">Total Loan Amount</div>
                                                            <div class="col-6 text-left fw-bold fw-size " id="total_loan_amount">0.00</div>
                                                        </div>
                                                        <!-- Installment Amount -->
                                                        <div class="row mb-3">
                                                            <div class="col-6 fw-bold fw-size ">Installment Amount</div>
                                                            <div class="col-6 text-left fw-bold fw-size " id="new_interest_amount">0.00</div>
                                                        </div>
                                                        <!-- Add more inputs here -->
                                                    </div>
                                                </div>




                                            </div>

                                            <hr>
                                            <div class="row mt-4 mb-4" hidden>
                                                <div class="col-lg-2">
                                                    <div class="mt-2 mb-2">
                                                        <span class="fw-bold">Collection Date</span>
                                                    </div>
                                                    <div class="mt-2 mb-2">
                                                        <span class="fw-bold">Penalty Date</span>
                                                    </div>
                                                    <div class="mt-4 mb-2">
                                                        <span class="fw-bold">First Installment Date</span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mt-1 mb-2 text-end" id="daily">
                                                        <select class="form-control" id="daily_txt" disabled>
                                                            <option value="1" selected>Daily</option>
                                                        </select>

                                                    </div>
                                                    <div class="mt-1 mb-2 text-end" id="weekly">
                                                        <select class="form-control" id="weekly_txt">
                                                            <option value="1" selected>Monday</option>
                                                            <option value="2">Tuesday</option>
                                                            <option value="3">Wednesday</option>
                                                            <option value="4">Thursday</option>
                                                            <option value="5">Friday</option>
                                                            <option value="6">Saturday</option>
                                                            <option value="7">Sunday</option>
                                                        </select>

                                                    </div>
                                                    <div class="mt-1 mb-2" id="first_of_the_month">
                                                        <label class="form-label">First Of The Month</label>
                                                    </div>
                                                    <div class="mt-1 mb-2" id="end_of_the_month">
                                                        <label class="form-label">End Of The Month</label>
                                                    </div>
                                                    <div class="mt-1 mb-2 text-end" id="twice_a_month">
                                                        <select class="form-control" id="twice_a_month_txt"
                                                                onchange="change_date(this.value)">
                                                            <option value="1" selected>First Of Month And 15th</option>
                                                            <option value="2">15th And End Of Month</option>
                                                        </select>
                                                    </div>
                                                    <div class="mt-1 mb-2 text-end" id="on_a_selected_date">
                                                        <select class="form-control" id="on_a_selected_date_txt"
                                                                onchange="check_date(this.value)">
                                                            <option value="1" selected>1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                            <option value="6">6</option>
                                                            <option value="7">7</option>
                                                            <option value="8">8</option>
                                                            <option value="9">9</option>
                                                            <option value="10">10</option>
                                                            <option value="11">11</option>
                                                            <option value="12">12</option>
                                                            <option value="13">13</option>
                                                            <option value="14">14</option>
                                                            <option value="15">15</option>
                                                            <option value="16">16</option>
                                                            <option value="17">17</option>
                                                            <option value="18">18</option>
                                                            <option value="19">19</option>
                                                            <option value="20">20</option>
                                                            <option value="21">21</option>
                                                            <option value="22">22</option>
                                                            <option value="23">23</option>
                                                            <option value="24">24</option>
                                                            <option value="25">25</option>
                                                            <option value="26">26</option>
                                                            <option value="27">27</option>
                                                            <option value="28">28</option>
                                                        </select>


                                                    </div>
                                                    <div class="mt-2 mb-2">
                                                        <label class="form-label" id="panelty_date"></label>
                                                        <label class="form-label" id="panelty_date_2" hidden></label>

                                                    </div>
                                                    <div class="mt-2 mb-2">
                                                        <input type="date" class="form-control date"
                                                               id="installment_date_txt">
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="container">
                                                <div class="row justify-content-center">
                                                    <div class="col-lg-5">
                                                        <div class="row mb-3">
                                                            <div class="col-sm-6">
                                                                <span class="fw-bold">Loan Capital Balance</span>
                                                            </div>
                                                            <div class="col-sm-6 text-start">
                                                                <b><span id="loan_capital_balance" class="fw-bold">{{$loan->capital_balance}}</span></b>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-sm-6">
                                                                <span class="fw-bold">Last Installment Date</span>
                                                            </div>
                                                            <div class="col-sm-6 text-start">
                                                                <b><span id="next_payment_date" class="fw-bold">{{$reduce_capital_data['next_payment_date']}}</span></b>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-sm-6">
                                                                <span class="fw-bold">Days From Last Payment Date</span>
                                                            </div>
                                                            <div class="col-sm-6 text-start">
                                                                <b><span id="days_from_last_payment_date" class="fw-bold">{{$reduce_capital_data['days_from_last_payment_date']}}</span></b>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-sm-6">
                                                                <span class="fw-bold">Installment Capital</span>
                                                            </div>
                                                            <div class="col-sm-6 text-start">
                                                                <b><span id="ins_capital" class="fw-bold">{{$reduce_capital_data['ins_capital']}}</span></b>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-5">
                                                        <div class="row mb-3">
                                                            <div class="col-sm-6">
                                                                <span class="fw-bold">Installment Interest</span>
                                                            </div>
                                                            <div class="col-sm-6 text-start">
                                                                <b><span id="installment_interest" class="fw-bold">{{$reduce_capital_data['installment_interest']}}</span></b>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-sm-6">
                                                                <span class="fw-bold">Installment Interest For Today</span>
                                                            </div>
                                                            <div class="col-sm-6 text-start">
                                                                <b><span id="installment_interest_today" class="fw-bold">{{$reduce_capital_data['installment_interest_today']}}</span></b>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-sm-6">
                                                                <span class="fw-bold">Required Payment Before Capital Reduce</span>
                                                            </div>
                                                            <div class="col-sm-6 text-start">
                                                                <b><span id="required_payment_before_capital" class="fw-bold text-danger">{{$reduce_capital_data['required_payment_before_capital']}}</span></b>
                                                            </div>
                                                        </div>

                                                        <div class="row mb-3">
                                                            <div class="col-sm-6">
                                                                <span class="fw-bold">Interest Period</span>
                                                            </div>
                                                            <div class="col-sm-6 text-start">
                                                                <b><span id="Interest_period_view" class="fw-bold">{{$loan->Interest_period}}</span></b>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

<br>
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="mb-3">
                                                        <label for="simpleinput" class="form-label">Payment Amount<span class="required-asterisk">*</span></label>
                                                        <input type="text" id="payment_amount" class="form-control">
                                                    </div>
                                                </div>
                                            </div>

                                            <button class="btn btn-primary btn-10" onclick="addInstallmentDates()">Generate Installments</button>

                                            <div class="row mb-3 section-break">
                                                <div class="col-12">
                                                    <div class="section-title">
                                                        Installments
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <div>
                                                    <div class="table-responsive-sm border border-1">
                                                        <table class="table table-centered mb-0" id="installment_table">
                                                            <thead>
                                                            <tr>
                                                                <th>No</th>
                                                                <th>Installment Date</th>
                                                                <th class="text-end">Installment Amount</th>
                                                                <th class="text-end">Capital Amount</th>
                                                                <th class="text-end">Interest Amount</th>
                                                                <th class="text-end">Penalty Date</th>
                                                                <th class="text-end">Penalty Amount</th>
                                                                <th class="text-end">Saving Amount</th>
                                                                <th class="text-end">Total Amount</th>
                                                                <th class="text-end">Paid Amount</th>
                                                                <th class="text-end">Penalty Balance</th>
                                                                <th class="text-end">Installment Balance</th>
                                                                <th class="text-end">Savings Balance</th>
                                                                <th class="text-end">Total Balance</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <tbody>
                                                            @foreach($installments as $item)
                                                                <tr data-installment-id="{{ $item->idInstallments }}"
                                                                    data-installment-date="{{ $item->Installment_Date }}"
                                                                    data-installment-amount="{{ $item->Installment_Amount }}"
                                                                    data-capital-amount="{{ $item->capital_amount }}"
                                                                    data-interest-amount="{{ $item->interest_amount }}"
                                                                    data-penalty-date="{{ $item->Panelty_date }}"
                                                                    data-penalty-amount="{{ $item->Panalty_Amount }}"
                                                                    data-saving-amount="{{ $item->Saving_amount }}"
                                                                    data-total-amount="{{ $item->Total_Amount }}"
                                                                    data-paid-amount="{{ $item->Paid_Amount }}"
                                                                    data-penalty-balance="{{ $item->Panalty_Balance }}"
                                                                    data-interest-balance="{{ $item->Interest_Balance}}"
                                                                    data-capital-balance="{{ $item->capital_balance}}"
                                                                    data-installment-balance="{{ $item->capital_amount}}"
                                                                    data-total-balance="{{ $item->Total_Balance }}"
                                                                >
                                                                    <td>{{ $item->No }}</td>
                                                                    <td>{{ $item->Installment_Date }}</td>
                                                                    <td class="text-end">{{ number_format($item->Installment_Amount, 2, '.', '') }}</td>
                                                                    <td class="text-end">{{ number_format($item->capital_amount, 2, '.', '') }}</td>
                                                                    <td class="text-end">{{ number_format($item->interest_amount, 2, '.', '') }}</td>
                                                                    <td class="text-end">{{ $item->Panelty_date }}</td>
                                                                    <td class="text-end">{{ number_format($item->Panalty_Amount, 2, '.', '') }}</td>
                                                                    <td class="text-end">{{ number_format($item->Saving_amount, 2, '.', '') }}</td>
                                                                    <td class="text-end">{{ number_format($item->Total_Amount, 2, '.', '') }}</td>
                                                                    <td class="text-end">{{ number_format($item->Paid_Amount, 2, '.', '') }}</td>
                                                                    <td class="text-end">{{ number_format($item->Panalty_Balance, 2, '.', '') }}</td>
                                                                    <td class="text-end">{{ number_format($item->Interest_Balance, 2, '.', '') }}</td>
                                                                    <td class="text-end">{{ number_format($item->capital_balance, 2, '.', '') }}</td>
                                                                    <td class="text-end">{{ number_format($item->Total_Balance, 2, '.', '') }}</td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>

                                                        </tbody>
                                                        </table>

                                                    </div> <!-- end table-responsive-->
                                                </div>
                                                <br>
                                                <input type="button" style="float: right" class="btn btn-danger" onclick="update_ins()" value="Process">
                                            </div>

                                            <div class="mt-4" hidden>

                                                <div class="row mb-3 section-break">
                                                    <div class="col-12">
                                                        <div class="section-title">
                                                            Required Document
                                                        </div>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div>
                                                        <div class="table-responsive-sm border border-1">
                                                            <table class="table table-centered mb-0" id="document_show_table">
                                                                <thead>
                                                                <tr>
                                                                    <th>Description</th>
                                                                    <th>Document Name</th>
                                                                    <th style="width: 200px;">Action</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                </tbody>
                                                            </table>
                                                        </div> <!-- end table-responsive-->
                                                    </div> <!-- end card-body-->
                                                </div> <!-- end card-->

                                            </div>


                                            <div class="row mb-3 section-break" hidden>
                                                <div class="col-12">
                                                    <div class="section-title">
                                                        Guarantee Details
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="card-body mt-4" hidden>
                                                <ul class="nav nav-tabs mb-3">

                                                    <li class="nav-item">
                                                        <a href="#home" data-bs-toggle="tab" aria-expanded="true"
                                                           class="nav-link active">
                                                            Witness 01
                                                        </a>
                                                    </li>

                                                </ul>

                                                <div class="tab-content">
                                                    <div class="tab-pane show active" id="home">

                                                        <div class="row">
                                                        </div>
                                                    </div>


                                                </div>
                                            </div> <!-- end card-body -->

                                        </div> <!-- end row -->
                                    </div>



                                </div>

                                <!-- HTML -->

                                <div class="mb-4" hidden>
                                    <label for="simpleinput" id="interest_amount_txt" class="form-label">Total Interest Amount</label>
                                    <input type="text" id="interest_amount" class="form-control" disabled>

                                </div>


                            </div> <!-- end card-->
                        </div> <!-- end col -->

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="overlay" class="overlay"></div>
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
    <script src="../JS/validate.js"></script>
    <script src="../JS/issueloan.js"></script>
    <script>

        var witness_load=[];
        document.addEventListener("DOMContentLoaded", function() {
            generateWitnessTabs({{$witnessCount}});
            witness_load=@json($witnessDetails)


            var customerSelect = document.getElementById("customer");
            if (customerSelect.value !== "0") {
                $("#package_feild").slideDown();
            }
            var package_details = document.getElementById("package_details");
            var valu = package_details.value;
            if (package_details.value !== "0") {
                load_package_details(valu);
            }

        });

        $(function() {
            $("#createLoanButton").addClass("disabled").on("click", function(event) {
                event.preventDefault();
            });


            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })
            load_individual_customer();


        })

        function load_package_details(id){
            $("#product_details").slideDown();
            load_product_details(id);
            view_doc(id);

            $.each(witness_load, function(index, witness) {
                index++;
                console.log(index);
                $('#first_name' + index).val(witness.details.First_Name);
                $('#last_name' + index).val(witness.details.Last_Name);
                $('#nic' + index).val(witness.details.Nic);
                $('#contact' + index).val(witness.details.Contact_No);
                $('#address' + index).val(witness.details.Address);
            });
        }


        function load_product_details(id) {
            $.ajax({
                type: "GET",
                url: "/load_product_details/" + id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(data, textStatus, xhr) {
                    console.log(data);

                    if (data && data.product_details && data.product_details.length > 0) {
                        var product = data.product_details[0];


                        $('#interest_method').val(product.Interest_method);
                        $('#interest_period').text(product.Interest_period);
                        $('#loan_interest').val(product.Loan_interest);
                        $('#interest_period_count').val(product.Interest_Period_Count);
                        $('#duration_period').val(product.Duration_period);
                        // $('#loan_period').val(product.Loan_period);
                        $('#repayment_type').val(product.Repayment_type);
                        $('#penalty_period').val(product.Panelty_period);
                        $('#penalty_percentage').val(product.Panelty_pecentage);
                        $('#penalty_date').val(product.Panelty_date);
                        $('#guarantee_count').val(product.Guarantee_count);
                        // generateWitnessTabs(product.Guarantee_count);
                        $('#witness1').addClass('active show');
                        $('a[href="#witness1"]').tab('show');
                        changeCategory();
                        calculateInterest();
                    } else {
                        console.log("No product details found");
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }



        function change_type(type){
            $("#package_feild").slideUp();
            $("#product_details").slideUp();
            if(type==="0"){
                $("#center_feild").slideUp();
                $("#group_feild").slideUp();
                $('#customer_details').empty();

                // Append a default "Select" option
                var defaultOption = $('<option></option>')
                    .attr('value', "0")
                    .text("Select");
                $('#customer_details').append(defaultOption);
                load_individual_customer();

            }else{
                $("#customer_feild").slideDown();
                $("#center_feild").slideDown();
                $("#group_feild").slideDown();
                $('#customer_details').empty();
                var defaultOption = $('<option></option>')
                    .attr('value', "0")
                    .text("Select");
                $('#customer_details').append(defaultOption);
            }
        }






        function load_customer(id) {
            $.ajax({
                type: "GET",
                url: "/load_customers/" + id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(data, textStatus, xhr) {
                    var customerData = data.item;

                    // Clear existing options in the select element
                    $('#customer_details').empty();

                    // Append a default "Select" option
                    var defaultOption = $('<option></option>')
                        .attr('value', "0")
                        .text("Select");
                    $('#customer_details').append(defaultOption);

                    // Check if customerData is not empty and is an array
                    if (Array.isArray(customerData) && customerData.length > 0) {
                        // Iterate through the data and create new options
                        customerData.forEach(function(customer) {
                            var option = $('<option></option>')
                                .attr('value', customer.idCustomer)
                                .text(customer.First_Name + " " + customer.Last_Name + " - " + customer.Contact_No + " - " + customer.Nic); // Adjust according to your customer data structure
                            $('#customer_details').append(option);
                        });
                    } else {
                        // If no data, append a default "No customers available" option
                        var noDataOption = $('<option></option>')
                            .attr('value', '')
                            .text('No customers available');
                        $('#customer_details').append(noDataOption);
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }


        function load_individual_customer() {
            $.ajax({
                type: "GET",
                url: "/load_individual_customer",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(data, textStatus, xhr) {
                    var customerData = data.item;

                    // Clear existing options in the select element
                    $('#customer_details').empty();

                    // Append a default "Select" option
                    var defaultOption = $('<option></option>')
                        .attr('value', "0")
                        .text("Select");
                    $('#customer_details').append(defaultOption);

                    // Check if customerData is not empty and is an array
                    if (Array.isArray(customerData) && customerData.length > 0) {
                        // Iterate through the data and create new options
                        customerData.forEach(function(customer) {
                            var option = $('<option></option>')
                                .attr('value', customer.idCustomer)
                                .text(customer.First_Name + " " + customer.Last_Name + " - " + customer.Contact_No + " - " + customer.Nic); // Adjust according to your customer data structure
                            $('#customer_details').append(option);
                        });
                    } else {
                        $('#customer_details').empty();
                        // If no data, append a default "No customers available" option
                        var noDataOption = $('<option></option>')
                            .attr('value', '')
                            .text('No customers available');
                        $('#customer_details').append(noDataOption);
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }

        function view_doc(id){

            $.ajax({
                type: "GET",
                url: "/loancategory/cost/"+id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {

                    if (xhr.status === 200) {
                        // Process other charges
                        var otherChargesTable = $('#loan_charge_table tbody');
                        otherChargesTable.empty(); // Clear existing rows
                        var totalAmount = 0; // Initialize total amount
                        data.other_charges.forEach(function(charge) {
                            var row = $('<tr></tr>');
                            row.append('<td>' + charge.Description + '</td>');
                            row.append('<td class="text-end">' + charge.Amount + '</td>');
                            otherChargesTable.append(row);
                            totalAmount += parseFloat(charge.Amount);
                        });


                        $('#total_loan_charge').text(totalAmount.toFixed(2));

                        var documentsTable = $('#document_show_table tbody');
                        documentsTable.empty(); // Clear existing rows
                        console.log(data.required_documents);
                        data.required_documents.forEach(function(document) {
                            var row = $('<tr></tr>');
                            row.append('<td>' + document.Name + '</td>');
                            row.append('<td><input type="file" class="form-control file-upload" data-document-id="' + document.idRequired_Documents + '"></td>');
                            row.append('<td><button class="btn btn-danger"><i class="bi bi-trash"></i></button></td>');
                            documentsTable.append(row);
                        });

                    }
                },

                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });

            $('#document_show_table').on('click', '.btn-danger', function() {

                $(this).closest('tr').remove(); // Remove the closest <tr> (row) to the clicked button
            });
        }
        function generateWitnessTabs(witnessCount) {
            let tabsContainer = $(".nav-tabs");
            let tabsContent = $(".tab-content");

            tabsContainer.empty();
            tabsContent.empty();

            for (let i = 1; i <= witnessCount; i++) {
                // Create tab link
                let tabLink = $('<a></a>')
                    .attr("href", "#witness" + i)
                    .attr("data-bs-toggle", "tab")
                    .addClass("nav-link")
                    .text("Guarantor " + i);

                // Set active class for the first witness tab
                if (i === 1) {
                    tabLink.addClass("active");
                }

                let tabItem = $('<li></li>').addClass("nav-item").append(tabLink);
                tabsContainer.append(tabItem);

                // Create tab content
                let tabPane = $('<div></div>')
                    .addClass("tab-pane")
                    .attr("id", "witness" + i);

                let rowDiv = $('<div></div>').addClass("row");

                // Left column
                let leftColDiv = $('<div></div>').addClass("col-lg-6");


                let typeSelect = $('<select>')
                    .addClass("form-control mb-3")
                    .attr("id", "type_" + i)
                    .on('change', function() {
                        set_customers(this.value,i);
                });


                // Add options to the select element
                let typeoptions = [
                    { value: '-1', text: 'Select' },
                    { value: '0', text: 'Cross Customer' },
                    { value: '1', text: 'Guarantor' },
                ];

                typeoptions.forEach(function(option) {
                    typeSelect.append($('<option></option>')
                        .attr('value', option.value)
                        .text(option.text));
                });


                // leftColDiv.append($('<label></label>').addClass("form-label").text("Guarantor Type"));
                // leftColDiv.append(typeSelect);


                let First_name = $('<input>')
                    .addClass("form-control mb-3")
                    .attr("type", "text")
                    .attr("id", "first_name" + i) // Set ID dynamically
                    .attr("placeholder", "First Name")
                    .prop("disabled", true);


                leftColDiv.append($('<label></label>').addClass("form-label").text("First Name"));
                leftColDiv.append(First_name);


                let Nic = $('<input>')
                    .addClass("form-control mb-3")
                    .attr("type", "text")
                    .attr("id", "nic" + i) // Set ID dynamically
                    .attr("placeholder", "NIC").prop("disabled", true);


                leftColDiv.append($('<label></label>').addClass("form-label").text("Nic"));
                leftColDiv.append(Nic);

                let Contact_no = $('<input>')
                    .addClass("form-control mb-3")
                    .attr("type", "text")
                    .attr("id", "contact" + i) // Set ID dynamically
                    .attr("placeholder", "Contact Number").prop("disabled", true);


                leftColDiv.append($('<label></label>').addClass("form-label").text("Contact Number"));
                leftColDiv.append(Contact_no);



                // Right column
                let rightColDiv = $('<div></div>').addClass("col-lg-6");

                let customerSelect = $('<select>')
                    .addClass("form-control mb-3")
                    .attr("id", "customer_" + i)
                    .on('change', function() {
                        getCusDetails(this.value,i);
                });

                customerSelect.append($('<option></option>')
                    .attr('value', "0")
                    .text("Select Customer"));


                // rightColDiv.append($('<label></label>').addClass("form-label").text("Guarantor"));
                // rightColDiv.append(customerSelect);

                let Last_name = $('<input>')
                    .addClass("form-control mb-3")
                    .attr("type", "text")
                    .attr("id", "last_name" + i) // Set ID dynamically
                    .attr("placeholder", "Last Name").prop("disabled", true);


                rightColDiv.append($('<label></label>').addClass("form-label").text("Last Name"));
                rightColDiv.append(Last_name);

                let Address = $('<input>')
                    .addClass("form-control mb-3")
                    .attr("type", "text")
                    .attr("id", "address" + i) // Set ID dynamically
                    .attr("placeholder", "Address").prop("disabled", true);

                rightColDiv.append($('<label></label>').addClass("form-label").text("Address"));
                rightColDiv.append(Address);

                // Append left and right columns to row
                rowDiv.append(leftColDiv);
                rowDiv.append(rightColDiv);

                // Append row to tab content
                tabPane.append(rowDiv);

                // Save button
                let buttonDiv = $('<div></div>').addClass("d-flex justify-content-end mt-3");

                tabPane.append(buttonDiv);

                tabsContent.append(tabPane);
            }
        }




        function set_customers(id,number){

            $.ajax({
                type: "GET",
                url: "/guarantor/load/"+id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {

                        $('#customer_'+number).empty();

                        // Add a default option
                        $('#customer_'+number).append($('<option>', {
                            value: '0',
                            text: 'Select Customer'
                        }));

                        // Add customer options
                        data.customer.forEach(function (customer) {
                            $('#customer_'+number).append($('<option>', {
                                value: customer.idCustomer,
                                text: customer.First_Name + ' ' + customer.Last_Name + ' - ' + customer.Contact_No + ' - ' + customer.Nic
                            }));
                        });
                    }
                },

                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });

        }

        function getCusDetails(id,number){
            let activeTab = $('.nav-tabs .nav-link.active');
            let activeTabHref = activeTab.attr('href');
            let activeTabIndex = activeTabHref.split('#witness')[1]; // Split to get the number


            let type=$("#type_"+activeTabIndex+"").val();

            $.ajax({
                type: "GET",
                url: "/guarantor/load/details/"+id+"/"+type,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        setCustomerData(data.customer,type,number);

                    }
                },

                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });

            function setCustomerData(data,type,number) {
                if (data.length > 0) {
                    let customer = data[0];

                }
            }

        }


        function changeCategory(){
            let Collection_Type=$('#repayment_type').val();
            let penalty_date=$('#penalty_date').val();
            $("#load_div").slideDown();

            $('#panelty_date').text("Installment Date + " + penalty_date + " Days");
            $('#panelty_date_2').text(penalty_date);
            if(Collection_Type==="Daily"){
                $("#weekly").hide();
                $("#first_of_the_month").hide();
                $("#end_of_the_month").hide();
                $("#twice_a_month").hide();
                $("#on_a_selected_date").hide();
                $("#daily").show();
            }else if (Collection_Type==="Weekly"){
                $("#weekly").show();
                $("#first_of_the_month").hide();
                $("#end_of_the_month").hide();
                $("#twice_a_month").hide();
                $("#on_a_selected_date").hide();
                $("#daily").hide();


                $("#panelty_amount_txt").text("Penalty Rate Per Full Amount (%)");
                $("#interest_txt").text("Interest Rate Per Full Amount (%)");


            }else if (Collection_Type==="First Of The Month"){
                $("#weekly").hide();
                $("#first_of_the_month").show();
                $("#end_of_the_month").hide();
                $("#twice_a_month").hide();
                $("#on_a_selected_date").hide();
                $("#daily").hide();
            }else if (Collection_Type==="End Of The Month"){
                $("#weekly").hide();
                $("#first_of_the_month").hide();
                $("#end_of_the_month").show();
                $("#twice_a_month").hide();
                $("#on_a_selected_date").hide();
                $("#daily").hide();
            }else if (Collection_Type==="Twice A Month"){
                $("#weekly").hide();
                $("#first_of_the_month").hide();
                $("#end_of_the_month").hide();
                $("#twice_a_month").show();
                $("#on_a_selected_date").hide();
                $("#daily").hide();
                var currentDate = new Date();
                currentDate.setDate(1);
                $("#installment_date_txt").val(currentDate.toISOString().split('T')[0]);
            }else if (Collection_Type==="On A Selected Date"){
                $("#weekly").hide();
                $("#first_of_the_month").hide();
                $("#end_of_the_month").hide();
                $("#twice_a_month").hide();
                $("#daily").hide();
                $("#on_a_selected_date").show();
                check_date(1);
            }

            // var tableBody = $('#installment_table tbody');

            // Clear existing rows
            // tableBody.empty();
            installment=[];
        }

        function check_date(on_a_selected_date_txt){
            var selectedValue = parseInt(on_a_selected_date_txt);
            selectedValue++;
            // Get current date
            var currentNewDate = new Date();
            // Calculate next month's date
            var nextMonthDate = new Date(currentNewDate.getFullYear(), currentNewDate.getMonth() + 1, selectedValue);
            $('#installment_date_txt').val(nextMonthDate.toISOString().slice(0, 10));
        }



        function calculateInterest(){

            let interest_period = $("#interest_period").text();
            let interest_method = $("#interest_method").val();
            let duration_period = $("#duration_period").val();
            let penalty_period = $("#penalty_period").val();
            let loan_period = $("#loan_period").val();
            let interest_period_count = $("#interest_period_count").val();

            let type = $("#repayment_type").val();
            let loan_amount = parseFloat($("#loan_amount").val());
            let interest = parseFloat($("#loan_interest").val());

            let interest_amount=(loan_amount*interest/100)*interest_period_count;

            $("#interest_amount").val(interest_amount.toFixed(2));
            // $("#total_loan_amount").text((parseFloat(loan_amount)+parseFloat(interest_amount)).toFixed(2));
            let total_loan_amount=(parseFloat(loan_amount)+parseFloat(interest_amount)).toFixed(2);
            console.log(total_loan_amount);
            let installment_amount=total_loan_amount/loan_period;
            console.log(interest_period);

        }
        // JavaScript
        function checkLoanChargesBalance() {
            let loanChargesBalanceCheckbox = document.getElementById("loanChargesBalance");
            let total_loan_amount=$("#total_loan_amount").text();
            let total_loan_charge=$("#total_loan_charge").text();
            let loan_period=$("#loan_period").val();
            if (loanChargesBalanceCheckbox.checked) {
                total_loan_amount=parseFloat(total_loan_amount);
                total_loan_charge=parseFloat(total_loan_charge);
                loan_period=parseInt(loan_period);

                let tot=total_loan_amount+total_loan_charge;

                let  installment=tot/loan_period;

                $("#total_loan_amount").text(tot.toFixed(2));
                $("#new_interest_amount").text(installment.toFixed(2));
            }else{
                total_loan_amount=parseFloat(total_loan_amount);
                total_loan_charge=parseFloat(total_loan_charge);
                loan_period=parseInt(loan_period);

                let tot=total_loan_amount-total_loan_charge;

                let  installment=tot/loan_period;

                $("#total_loan_amount").text(tot.toFixed(2));
                $("#new_interest_amount").text(installment.toFixed(2));
            }
        }

        function addInstallmentDates() {
            let payment_amount_show = $("#payment_amount").val();
            let payment_amount = parseFloat($("#payment_amount").val());
            let required_payment_before_capital = parseFloat($("#required_payment_before_capital").text());


            let loan_capital_balance=parseFloat($("#loan_capital_balance").text());
            let tot_am=required_payment_before_capital+loan_capital_balance;
            if(tot_am<payment_amount){
                Swal.fire("Error!", "You have to pay only Rs."+tot_am.toFixed(2)+" for close the loan.You can't pay more than that !", "error");
            } else if (payment_amount_show === "") {
                Swal.fire("Error!", "Please enter payment amount !", "error");
            } else if (required_payment_before_capital > 0) {
                Swal.fire("Error!", "Please Pay Required Payment Before Capital Reduce !", "error");
            } else {
                // Gather installment data
                let installments = [];
                $("#installment_table tbody tr").each(function () {
                    let installmentDate = $(this).data("installment-date");
                    let installmentBalance = parseFloat($(this).data("installment-balance"));
                    let totalBalance = parseFloat($(this).data("total-balance"));

                    installments.push({
                        date: installmentDate,
                        installmentBalance: installmentBalance,
                        totalBalance: totalBalance,
                        row: $(this) // Store reference to the row element
                    });
                });

                console.log(installments);

                // Filter installments where Installment_Balance is not 0.00
                let filteredInstallments = installments.filter(function (installment) {
                    return installment.totalBalance !== 0.00;
                });

                // Find the earliest date from the filtered installments
                if (filteredInstallments.length > 0) {
                    let earliestInstallment = filteredInstallments.reduce(function (prev, current) {
                        return (new Date(prev.date) < new Date(current.date)) ? prev : current;
                    });

                    // Calculate the difference between the earliest installment date and today
                    let today = new Date();
                    let earliestDate = new Date(earliestInstallment.date);
                    let timeDifference = today - earliestDate;
                    let dayDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));

                    console.log("Earliest Installment Date:", earliestInstallment.date);
                    console.log("Days Difference from Today:", dayDifference * -1);

                    let days = dayDifference * -1;

                    console.log(days);

                    // Filter installments where Total_Balance is not 0.00 and get the count
                    let totalBalanceNonZeroCount = installments.filter(function (installment) {
                        return installment.totalBalance !== 0.00;
                    }).length;

                    console.log("Total Balance Non-Zero Count:", totalBalanceNonZeroCount);

                    let loan_interest = parseFloat($("#loan_interest").val());
                    let loan_capital_balance = parseFloat($("#loan_capital_balance").text());
                    let reducing_amount = payment_amount - required_payment_before_capital;

                    let new_loan_capital_balance = loan_capital_balance - reducing_amount;

                    let interest_per_installment = (new_loan_capital_balance / 100) * loan_interest;
                    let new_interest = interest_per_installment * totalBalanceNonZeroCount;

                    let new_loan_total = new_loan_capital_balance + new_interest;

                    let installment_capital = new_loan_capital_balance / totalBalanceNonZeroCount;

                    let Interest_period = $("#Interest_period_view").text();
                    console.log(Interest_period);
                    let rate_per_day = 0.00;

                    let interest_method=$("#interest_method").val();
                    if(interest_method==="Draft"){
                        installment_capital=0.00;
                    }

                    if (Interest_period === "Daily") {
                        rate_per_day = interest_per_installment;
                    } else if (Interest_period === "Weekly") {
                        rate_per_day = interest_per_installment / 7;
                    } else if (Interest_period === "Per Month") {
                        rate_per_day = interest_per_installment / 30;
                    } else if (Interest_period === "Per Year") {
                        rate_per_day = interest_per_installment / 365;
                    }
                    let interest_for_balance_days = rate_per_day * days;

                    let ins_amount = interest_per_installment + installment_capital;

                    // Update rows in the installment table starting from indexToUpdate
                    let indexToUpdate = installments.indexOf(earliestInstallment);

                    let count=0;
                    if(interest_method === "Reducing Balance") {
                        for (let i = indexToUpdate; i < installments.length; i++) {

                            // ins_amount=parseFloat($("#installment_amount").val());
                            // let loan_period=parseFloat($("#loan_period").val());
                            // let total_interest_amount=parseFloat($("#total_interest_amount").text());
                            // interest_amount=0;
                            // interest_amount=(total_interest_amount/((loan_period*(1+loan_period))/2)*((loan_period+1)-count));
                            // capital_amount=ins_amount-interest_amount;


                            let paid = parseFloat(installments[i].row.find('td').eq(9).text()); // Convert text to a float
                            installments[i].row.find('td').eq(2).text(interest_per_installment.toFixed(2));
                            installments[i].row.find('td').eq(3).text(installment_capital.toFixed(2));
                            installments[i].row.find('td').eq(4).text(interest_per_installment.toFixed(2));
                            installments[i].row.find('td').eq(8).text((ins_amount).toFixed(2)); // Correct the calculation
                            installments[i].row.find('td').eq(11).text((ins_amount - paid).toFixed(2)); // Correct the calculation
                            installments[i].row.find('td').eq(13).text((ins_amount - paid).toFixed(2)); // Correct the calculation
                        }
                    }else{
                        for (let i = indexToUpdate; i < installments.length; i++) {
                            let paid = parseFloat(installments[i].row.find('td').eq(9).text()); // Convert text to a float
                            installments[i].row.find('td').eq(2).text(interest_per_installment.toFixed(2));
                            installments[i].row.find('td').eq(3).text(installment_capital.toFixed(2));
                            installments[i].row.find('td').eq(4).text(interest_per_installment.toFixed(2));
                            installments[i].row.find('td').eq(8).text((ins_amount).toFixed(2)); // Correct the calculation
                            installments[i].row.find('td').eq(11).text((ins_amount - paid).toFixed(2)); // Correct the calculation
                            installments[i].row.find('td').eq(13).text((ins_amount - paid).toFixed(2)); // Correct the calculation
                        }
                    }




                    // Perform other necessary actions with the earliest installment and date difference
                } else {
                    console.log("No installments with non-zero balance found.");
                }
            }
        }



        function update_ins() {
            addInstallmentDates();
            let payment_amount = $("#payment_amount").val();
            let required_payment_before_capital = parseFloat($("#required_payment_before_capital").text());
            let loan_capital_balance = parseFloat($("#loan_capital_balance").text());
            let reducing_amount = payment_amount - required_payment_before_capital;

            let new_loan_capital_balance = loan_capital_balance - reducing_amount;
            if(payment_amount===""){
                Swal.fire("Error!", "Please enter payment amount !", "error");
            }else{
                let installments = [];

                // Iterate through each row in the table
                $("#installment_table tbody tr").each(function() {
                    let id = $(this).data("installment-id");
                    let installmentDate = $(this).data("installment-date");
                    let installmentAmount = parseFloat($(this).find('td').eq(2).text()); // Assuming column index 2
                    let capitalAmount = parseFloat($(this).find('td').eq(3).text()); // Assuming column index 3
                    let interestAmount = parseFloat($(this).find('td').eq(4).text()); // Assuming column index 4
                    let penaltyDate = $(this).find('td').eq(5).text(); // Assuming column index 5
                    let penaltyAmount = parseFloat($(this).find('td').eq(6).text()); // Assuming column index 6
                    let savingAmount = parseFloat($(this).find('td').eq(7).text()); // Assuming column index 7
                    let totalAmount = parseFloat($(this).find('td').eq(8).text()); // Assuming column index 8
                    let paidAmount = parseFloat($(this).find('td').eq(9).text()); // Assuming column index 9
                    let penaltyBalance = parseFloat($(this).find('td').eq(10).text()); // Assuming column index 10
                    let installmentBalance = parseFloat($(this).find('td').eq(11).text()); // Assuming column index 10
                    let savingBalance = parseFloat($(this).find('td').eq(12).text()); // Assuming column index 10
                    let totalBalance = parseFloat($(this).find('td').eq(13).text()); // Assuming column index 11

                    let rowData = {
                        id: id,
                        installmentDate: installmentDate,
                        installmentAmount: installmentAmount,
                        capitalAmount: capitalAmount,
                        interestAmount: interestAmount,
                        penaltyDate: penaltyDate,
                        penaltyAmount: penaltyAmount,
                        savingAmount: savingAmount,
                        totalAmount: totalAmount,
                        paidAmount: paidAmount,
                        penaltyBalance: penaltyBalance,
                        savingBalance: savingBalance,
                        installmentBalance: installmentBalance,
                        totalBalance: totalBalance
                    };

                    installments.push(rowData);
                });

                Swal.fire({
                    title: "Are you sure?",
                    text: "Do you want to reduce the capital ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Reduce it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Perform AJAX request to update database via Laravel controller
                        $.ajax({
                            type: "POST",
                            url: "/update_reduce_balance", // Laravel named route
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                installments: installments,
                                payment_amount: payment_amount,
                                new_loan_capital_balance:new_loan_capital_balance
                            },
                            success: function(response) {
                                let payment_id = response.payment_id;
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Capital reduced successfully !",
                                }).then(function () {
                                    load_payment_reciept(payment_id);
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error("Error updating installments:", error);

                            }
                        });
                    }
                });
            }





        }

        // payment_amount_2
        function load_payment_reciept(id){
            // document.getElementById('issue-loan-modal').style.display = 'none';
            // document.getElementById('issue-loan-modal_2').style.display = 'none';
            openModal();
            $('.btn-success').prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "/view_payment_load_reciept/" + id+"/1",
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
                        if (data.point_check==="1"){

                            $('#loyalty_section').show();
                        }else{

                            $('#loyalty_section').hide();
                        }
                        $("#Inv_number").text("Receipt No."+payment.idCustomer_Payments);
                        $("#customer_name").text(customer.First_Name+" "+customer.Last_Name);
                        $("#customer_number").text(customer.cus_number);
                        $("#loyalty_points").text(parseFloat(data.points_to_add).toFixed(2));
                        $("#loan_number").text(loan.Loan_No);
                        $("#payment_date_view").text(payment.Date);

                        if (payment.Payment_type === "Cheque") {

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


                        $("#payment_time").text(payment.time);

                        $("#loan_amount").text(parseFloat(loan.Amount).toFixed(2));
                        $("#full_loan_amount").text(parseFloat(loan.Total_Loan_Amount).toFixed(2));
                        $("#payed_amount").text(parseFloat(payment.Amount).toFixed(2));
                        $("#capital_balance").text(parseFloat(loan.Balance_Amount).toFixed(2));
                        $("#payment_type_view").text(payment.Payment_type);

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

        function closeModal() {
            window.location.reload();
        }

    </script>
@endsection

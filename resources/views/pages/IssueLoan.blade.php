@extends('layout.admin')

@section('head')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
                                    <h3 class="my-4">Create Loan</h3>

                                </div>
                                <input type="hidden" id="saturday_sunday" class="form-control" value="{{$company->saturday_sunday}}">
                                <div class="col-lg-12">
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Issue Date</label>
                                        <input type="date" id="issue_date" class="form-control" value="{{date('Y-m-d')}}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Type</label>
                                        <select class="form-control"  id="type" onchange="change_type(this.value)">
                                            <option value="0">Individual</option>
                                            <option value="1">Group</option>
                                        </select>
                                    </div>

                                    <div class="mb-3" id="center_feild">
                                        <label for="simpleinput" class="form-label">Center</label>
                                        <select class="form-control"  id="center" onchange="change_group(this.value)">
                                            <option id="0">Select</option>
                                            @foreach($center as $item)
                                                <option value="{{$item->idCenter}}">{{ $item->Name }}-{{$item->Contact_no}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3" id="group_feild">
                                        <label for="simpleinput" class="form-label">Group</label>
                                        <select class="form-control"  id="group" onchange="load_customer(this.value)">

                                        </select>
                                    </div>


                                    <div class="mb-3" id="customer_feild">
                                        <label for="simpleinput" class="form-label">Select Customer</label>
                                        <select class="form-control"  id="customer_details" onchange="select_package()">

                                        </select>
                                    </div>

                                    <div class="mb-3" id="customer_feild">
                                        <label for="simpleinput" class="form-label">Loan Number</label>
                                        <input type="text" id="type_loan_number" class="form-control">
                                    </div>

                                    <div class="mb-3" id="customer_bank_feild">
                                        <label for="simpleinput" class="form-label">Customer Bank Account</label>
                                        <select class="form-control" id="bank_acc">
                                        </select>
                                        <br>
                                        <button type="button" class="btn btn-success"   data-bs-toggle="modal" data-bs-target="#bank-modal" onclick="setbankid()">
                                            Add Bank Account
                                        </button>
                                    </div>


                                    <div class="mb-3" id="package_feild">
                                        <label for="simpleinput" class="form-label">Select Package</label>
                                        <select class="form-control"  id="package_details" onchange="load_package_details(this.value)">
                                            <option id="0">Select</option>
                                            @foreach($product as $item)
                                                <option value="{{$item->idLoan_Category}}">{{$item->Name}}-{{$item->Loan_amount}}-{{$item->Interest_period}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3" id="leasing_feild">
                                        <label for="simpleinput" class="form-label">Select Type</label>
                                        <select class="form-control"  id="lease_type" onchange="check_leasing(this.value)">
                                            <option id="0">Cash</option>
                                            <option id="1">Leasing</option>

                                        </select>
                                    </div>


                                    <div class="mb-3" id="leasing_feild_vehicle">
                                        <label for="simpleinput" class="form-label">Vehicle Number</label>
                                        <input type="text" id="vehicle_num" class="form-control">
                                    </div>

                                    <hr>

                                    <div class="container" id="product_details">

                                        <div class="row mb-3 section-break">
                                            <div class="col-12">
                                                <div class="section-title">
                                                    Product Details
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            @if($company->product_editable==1)
                                                <div id="normal_loan">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6" >
                                                            <div class="mb-3">
                                                                <label for="interest_method" class="form-label">Interest Method<span class="required-asterisk">*</span></label>
                                                                <select class="form-select" id="interest_method" disabled>
                                                                    <option value="Flat Rate">Flat Rate</option>
                                                                    <option value="Reducing Balance - Equal Installments">Reducing Balance - Equal Installments</option>
                                                                    <option value="Reducing Balance - Equal Capital">Reducing Balance - Equal Capital</option>
                                                                    <option value="Interest Only">Interest Only</option>
                                                                    <option value="Draft">Draft</option>
                                                                    <option value="Reducing Balance">Reducing Balance</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="loan_amount" class="form-label">Loan Amount<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="loan_amount" class="form-control" onkeyup="calculateInterest()">
                                                                <input type="hidden" id="loan_amount_from" class="form-control">
                                                                <input type="hidden" id="loan_amount_to" class="form-control">
                                                                <p id="loan_display" style="color: blue; margin-top: 5px;"></p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label for="interest" class="form-label">Default Loan Interest (%)<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="loan_interest" class="form-control" onkeyup="calculateInterest()">
                                                                <input type="hidden" id="loan_interest_from" class="form-control">
                                                                <input type="hidden" id="loan_interest_to" class="form-control">
                                                                <p id="interest_display" style="color: blue; margin-top: 5px;"></p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3" >
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
                                                        <div class="col-md-2" hidden>
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
                                                </div>


                                                <div id="reducingBalanceFields" style="display: none;">
                                                    <div class="row mb-3">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="installment_amount" class="form-label">Installment Amount<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="installment_amount" class="form-control" onkeyup="change_ins_values()">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="months" class="form-label">Months<span class="required-asterisk">*</span></label>
                                                                <select class="form-control" id="months" onchange="change_ins_values()">
                                                                    <option value="1">1</option>
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
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="collection" class="form-label">Collection<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="collection" class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="maximum_loan" class="form-label">Maximum Loan (5% Interest)<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="maximum_loan" class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="offer_decided" class="form-label">Offer Decided<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="offer_decided" class="form-control" onkeyup="change_ins_values()">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="given_interest_rate" class="form-label">Given Interest Rate<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="given_interest_rate" class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                                <div class="row mb-3 section-break">
                                                    <div class="col-12">
                                                        <div class="section-title">
                                                            Loan duration and Repayments
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="loan_duration" class="form-label">Loan Duration<span class="required-asterisk">*</span></label>
                                                            <input type="number" id="loan_period" class="form-control" onkeyup="calculateInterest()">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="loan_duration" class="form-label">Type<span class="required-asterisk">*</span></label>
                                                            <select class="form-select" id="duration_period" onchange="change_loan_duration()">
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
                                                            <select class="form-select" id="repayment_type" onchange="calculateInterest()">
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
                                                <div class="row mb-3 section-break">
                                                    <div class="col-12">
                                                        <div class="section-title">
                                                            Penalty Details
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="panelty_rate" class="form-label">Penalty Percentage (%)<span class="required-asterisk">*</span></label>
                                                            <input type="text" id="penalty_percentage" class="form-control" onkeyup="calculateInterest()">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="penalty_period" class="form-label">Penalty Period<span class="required-asterisk">*</span></label>
                                                            <select class="form-select" id="penalty_period" onchange="calculateInterest()">
                                                                <option value="Daily">Per Day</option>
                                                                <option value="Weekly">Per Week</option>
                                                                <option value="Per Month">Per Month</option>
                                                                <option value="Per Installment">Per Installment</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">

                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="panelty_rate_date" class="form-label">Penalty Start After<span class="required-asterisk">*</span></label>
                                                            <input type="text" id="penalty_date" class="form-control"  oninput="validateNumberInput(this)" onkeyup="calculateInterest()">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2" hidden>
                                                        <div class="mb-3">
                                                            <label for="loan_duration" class="form-label">Default Loan Duration<span class="required-asterisk">*</span></label>
                                                            <select class="form-select" id="panelty_duration_period">
                                                                <option value="Days">Days</option>
                                                                <option value="Weeks">Weeks</option>
                                                                <option value="Months">Months</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                            @else
                                                <div id="normal_loan">
                                                    <div class="row mb-3">
                                                        <div class="col-md-6" >
                                                            <div class="mb-3">
                                                                <label for="interest_method" class="form-label">Interest Method<span class="required-asterisk">*</span></label>
                                                                <select class="form-select" id="interest_method" disabled>
                                                                    <option value="Flat Rate">Flat Rate</option>
                                                                    <option value="Reducing Balance - Equal Installments">Reducing Balance - Equal Installments</option>
                                                                    <option value="Reducing Balance - Equal Capital">Reducing Balance - Equal Capital</option>
                                                                    <option value="Interest Only">Interest Only</option>
                                                                    <option value="Draft">Draft</option>
                                                                    <option value="Reducing Balance">Reducing Balance</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label for="loan_amount" class="form-label">Loan Amount<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="loan_amount" class="form-control" onkeyup="calculateInterest()">
                                                                <input type="hidden" id="loan_amount_from" class="form-control" >
                                                                <input type="hidden" id="loan_amount_to" class="form-control">
                                                                <p id="loan_display" style="color: blue; margin-top: 5px;"></p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label for="interest" class="form-label">Default Loan Interest (%)<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="loan_interest" class="form-control" onkeyup="calculateInterest()">
                                                                <input type="hidden" id="loan_interest_from" class="form-control">
                                                                <input type="hidden" id="loan_interest_to" class="form-control">
                                                                <p id="interest_display" style="color: blue; margin-top: 5px;"></p>
                                                            </div>
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
                                                                <input type="number" id="interest_period_count" class="form-control" onkeyup="calculateInterest()">
                                                            </div>

                                                        </div>
                                                        <div class="col-md-2">
                                                            <div class="mb-3">
                                                                <label for="period_count" class="form-label" >Type</label>
                                                                <select class="form-select" id="duration_period" onchange="calculateInterest()"  >
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
                                                </div>
                                                <div id="reducingBalanceFields" style="display: none;">
                                                    <div class="row mb-3">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="installment_amount" class="form-label">Installment Amount<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="installment_amount" class="form-control" onkeyup="change_ins_values()">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="months" class="form-label">Months<span class="required-asterisk">*</span></label>
                                                                <select class="form-control" id="months" onchange="change_ins_values()">
                                                                    <option value="1">1</option>
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
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="collection" class="form-label">Collection<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="collection" class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mb-3">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="maximum_loan" class="form-label">Maximum Loan (5% Interest)<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="maximum_loan" class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="offer_decided" class="form-label">Offer Decided<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="offer_decided" class="form-control" onkeyup="change_ins_values()">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="given_interest_rate" class="form-label">Given Interest Rate<span class="required-asterisk">*</span></label>
                                                                <input type="text" id="given_interest_rate" class="form-control" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3 section-break">
                                                    <div class="col-12">
                                                        <div class="section-title">
                                                            Repayment Cycle
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-5">
                                                    <div class="col-md-2">
                                                        <div class="mb-3">
                                                            <label for="loan_duration" class="form-label">Repayment Duration<span class="required-asterisk">*</span></label>
                                                            <input type="number" id="loan_period" class="form-control" onkeyup="calculateInterest()">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3" >
                                                        <div class="mb-7">
                                                            <label for="loan_duration" class="form-label">Repayment Duration Type<span class="required-asterisk">*</span></label>
                                                            <select class="form-select" id="repayment_duration_period"  onchange="repayment_type(this.value)" disabled>
                                                                <option value="Days">Days</option>
                                                                <option value="Weeks">Weeks</option>
                                                                <option value="Months">Months</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-1">

                                                    </div>

                                                    <div class="col-md-6" >
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
                                                <div class="row mb-3 section-break">
                                                    <div class="col-12">
                                                        <div class="section-title">
                                                            Penalty Details
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
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

                                                <div class="row mb-3">

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
                                            @endif






                                            <div class="mt-4">
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
                                                                    <th>Type</th>
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
                                                                <div class="col-6 fw-bold">Total Other Charges In Loan</div>
                                                                <div class="btn-group ms-2" role="group" aria-label="Checkbox group">
{{--                                                                    <select class="form-select" id="loan_charge_dropdown" onchange="checkAnotherCheckbox(this.value)">--}}
{{--                                                                        <option value="1">Add Loan Charges to the Capital</option>--}}
{{--                                                                        <option value="2">Deduct Other Charges from Capital</option>--}}
{{--                                                                        <option value="3" selected>Loan Charges Separate from Loan</option>--}}
{{--                                                                    </select>--}}
                                                                    <input type="checkbox" class="form-check-input" id="loanChargesBalance" onchange="checkLoanChargesBalance()">
                                                                    <label class="form-check-label ms-2" for="loanChargesBalance">Add Loan Charges to the Capital</label>

                                                                    <input type="checkbox" class="form-check-input ms-3" id="deductCharges" onchange="checkAnotherCheckbox('deductCharges')">
                                                                    <label class="form-check-label ms-2" for="deductCharges">Deduct Other Charges from Capital</label>

                                                                    <input type="checkbox" class="form-check-input ms-3" id="separateCharges" onchange="checkAnotherCheckbox('separateCharges')" checked>
                                                                    <label class="form-check-label ms-2" for="separateCharges">Loan Charges Separate from Loan</label>
                                                                </div>
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
                                                        <div class="row mb-3">
                                                            <div class="col-6 fw-bold fw-size ">Issued Amount</div>
                                                            <div class="col-6 text-left fw-bold fw-size " id="new_issued_amount">0.00</div>
                                                        </div>
                                                    </div>
                                                </div>




                                            </div>
                                            <div id="saving_section">
                                                <div class="row mb-3 section-break">
                                                    <div class="col-12">
                                                        <div class="section-title">
                                                            Saving Account
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mb-3" hidden>
                                                    <div class="col-6 fw-bold fw-size ">Enable Savings Account Process</div>
                                                    <div class="col-6 text-left fw-bold fw-size " id="enable_saving">Yes</div>
                                                </div>
                                                <div class="row mb-3" hidden>
                                                    <div class="col-6 fw-bold fw-size ">Enable Savings Account Process 02</div>
                                                    <div class="col-6 text-left fw-bold fw-size " id="saving_payment_active">0.00</div>
                                                </div>
                                                <div class="row mb-3">
                                                    <div class="col-6 fw-bold fw-size ">Saving Account Amount Type</div>
                                                    <div class="col-6 text-left fw-bold fw-size " id="saving_account_amount_type">0.00</div>
                                                    <div class="col-6 text-left fw-bold fw-size " id="saving_account_amount_type_2" hidden>0.00</div>
                                                    <div class="col-6 text-left fw-bold fw-size " id="saving_account_amount_type_3" hidden>0.00</div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-6 fw-bold fw-size ">Amount</div>
                                                    <div class="col-6 text-left fw-bold fw-size " id="saving_amount">0.00</div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-6 fw-bold fw-size ">Account Type</div>
                                                    <div class="col-6 text-left fw-bold fw-size " >Saving</div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-6 fw-bold fw-size ">Opening Balance</div>
                                                    <div class="col-6 text-left fw-bold fw-size ">0.00</div>
                                                </div>

                                            </div>





                                            <hr>
                                            <div class="row mt-4 mb-4">
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

                                                            <tbody>
                                                            <tr>

                                                            </tr>

                                                            </tbody>
                                                        </table>
                                                    </div> <!-- end table-responsive-->
                                                </div> <!-- end card-body-->
                                            </div>


                                            <div class="mt-4">

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
                                                                    <th hidden>Checked</th>
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


                                            <div class="row mb-3 section-break">
                                                <div class="col-12">
                                                    <div class="section-title">
                                                        Guarantee Details
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="card-body mt-4">
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

                                            <div class="row mb-3 section-break">
                                                <div class="col-12">
                                                    <div class="section-title">
                                                        Officer Details
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="mb-3" id="lending_officer_feild">
                                                <label for="simpleinput" class="form-label">Select Lending Officer</label>
                                                <select class="form-control"  id="lending_officer">
                                                    @foreach($lending_officer as $item)
                                                        <option value="{{$item->id}}">{{$item->Full_Name}}-{{$item->Designation}}-{{$item->TP}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3" id="lending_officer_feild">
                                                <label for="simpleinput" class="form-label">Select Collecting officer</label>
                                                <select class="form-control"  id="collector_officer">
                                                    @foreach($collector as $item)
                                                        <option value="{{$item->id}}">{{$item->Full_Name}}-{{$item->Designation}}-{{$item->TP}}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                                <div class="mb-3" id="lending_officer_feild">
                                                    <label for="simpleinput" class="form-label">Select Broker</label>
                                                    <select class="form-control"  id="loan_broker">
                                                        @foreach($collector as $item)
                                                            <option value="{{$item->id}}">{{$item->Full_Name}}-{{$item->Designation}}-{{$item->TP}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3" id="lending_officer_feild">
                                                    <label for="simpleinput" class="form-label">Broker Commission(%)</label>
                                                    <input type="number" class="form-control" value="0" id="loan_broker_commission">
                                                </div>

                                            <div class="mb-3" id="lending_officer_feild">
                                                <label for="simpleinput" class="form-label">User</label>
                                                <input type="text" class="form-control" value="{{session('Full_Name')}}" readonly>
                                            </div>
                                        </div> <!-- end row -->
                                    </div>



                                </div>

                                <!-- HTML -->
                                <div class="mt-4" id="button_feild">
                                    <a href="#" class="btn btn-success" id="createLoanButton" style="float: right" onclick="save_loan()">Issue New Loan
                                        <i class="bi bi-arrow-right"></i></a>
                                </div>
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


    <div class="modal fade" id="bank-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h4 class="modal-title" >gwegerg</h4> -->
                    <h4>Bank Details</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" id="bank_name" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_name" class="form-label">Account Name</label>
                                    <input type="text" id="account_name" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="account_number" class="form-label">Account Number</label>
                                    <input type="text" id="account_number" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="branch" class="form-label">Branch</label>
                                    <input type="text" id="branch" class="form-control">
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success" id="addBankBtn" onclick="save_bank_details()">Add Bank Account</button>
                    </div>
                    <div class="table-responsive-sm">
                        <div class="table-responsive-sm">
                            <table class="table table-centered mb-0" id="bank_table">
                                <thead>
                                <tr>
                                    <th>Bank Name</th>
                                    <th>Account Name</th>
                                    <th>Account Number</th>
                                    <th>Branch</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                    </div><!-- /.modal-content -->
                </div><!-- /.modal-dialog -->
            </div><!-- /.modal -->
        </div>
    </div>

@endsection

@section('script')
    <script src="../JS/validate.js"></script>
    <script src="../JS/issueloan.js?n=15"></script>
    <script>

        $(document).ready(function() {
            let x = ["#installment_amount","#offer_decided"];
            decimalFormat(x);

            fetchHolidays();

        });


        function repayment_type(id) {
            var select = document.getElementById("repayment_type");

            // Clear the existing options
            select.innerHTML = '';

            // Conditionally add options based on the provided `id`
            if (id === "Days") {
                // Add only 'Daily'
                var option = new Option("Daily", "Daily");
                select.add(option);
            } else if (id === "Weeks") {
                // Add 'Weekly' and 'Twice A Month'
                var weekOptions = [
                    { text: "Weekly", value: "Weekly" },
                    { text: "Twice A Month", value: "Twice A Month" }
                ];

                weekOptions.forEach(function(optionData) {
                    var option = new Option(optionData.text, optionData.value);
                    select.add(option);
                });
            } else if (id === "Months") {
                // Add month-related options
                var monthOptions = [
                    { text: "First Of The Month", value: "First Of The Month" },
                    { text: "End Of The Month", value: "End Of The Month" },
                    { text: "Twice A Month", value: "Twice A Month" },
                    { text: "On A Selected Date", value: "On A Selected Date" }
                ];

                monthOptions.forEach(function(optionData) {
                    var option = new Option(optionData.text, optionData.value);
                    select.add(option);
                });
            }
        }


        function setbankid(){
            let customer_details=$('#customer_details').val();
            load_bank(customer_details);
        }


        function load_bank(id) {
            $.ajax({
                type: "GET",
                url: "/customers/bank/" + id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        // Clear the table body
                        $('#bank_table tbody').empty();

                        // Iterate over each item in the response and append it to the table
                        data.item.forEach(function(bankDetail) {
                            var newRow = `
                        <tr>
                            <td>${bankDetail.bank_name}</td>
                            <td>${bankDetail.account_name}</td>
                            <td>${bankDetail.account_number}</td>
                            <td>${bankDetail.branch}</td>
                            <td>
                                <button type="button" class="btn btn-danger remove-btn" onclick="removeBank(${bankDetail.id})">Remove</button>
                            </td>
                        </tr>
                    `;
                            $('#bank_table tbody').append(newRow);
                        });
                    } else {
                        Swal.fire("Error!", "Failed to load data!", "error");
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire("Error!", "Failed to load data!", "error");
                }
            });
        }


        // Function to remove bank detail row
        function removeBank(bankId) {
            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to remove the Bank Details ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Remove it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/customers/bank/remove/" + bankId,
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (data, textStatus, xhr) {
                            if (xhr.status === 200) {
                                // Remove the row from the table
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Bank detail removed successfully!",
                                }).then(function () {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire("Error!", "Failed to remove bank detail!", "error");
                            }
                        },
                        error: function (xhr, status, error) {
                            Swal.fire("Error!", "Failed to remove bank detail!", "error");
                        }
                    });
                }
            });

        }

        function save_bank_details() {


            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to save the Bank Details ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Save it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();

                    var id = $('#customer_details').val();



                    // Get the values from the input fields
                    var bankName = $('#bank_name').val();
                    var accountName = $('#account_name').val();
                    var accountNumber = $('#account_number').val();
                    var branch = $('#branch').val();

                    // Append the values to the FormData object
                    formData.append('bankName', bankName);
                    formData.append('accountName', accountName);
                    formData.append('accountNumber', accountNumber);
                    formData.append('branch', branch);
                    formData.append('id', id); // Append the general ID


                    // Send the form data via AJAX
                    $.ajax({
                        url: "/save-bank-details-single",
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully saved!",
                            }).then(function () {
                                window.location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle error
                            console.error(error);
                        }
                    });
                }
            });



        }




        $(function() {
            $('#customer_details').select2();
            $('#package_details').select2();
            $("#createLoanButton").addClass("disabled").on("click", function(event) {
                event.preventDefault();
            });
            $("#center_feild").slideUp();
            $("#customer_bank_feild").slideUp();
            $("#group_feild").slideUp();
            $("#package_feild").slideUp();
            $("#product_details").slideUp();
            $("#leasing_feild").slideUp();
            $("#leasing_feild_vehicle").slideUp();
            $("#lending_officer_feild").slideUp();

            // //Initialize Select2 Elements
            // $('.select2').select2()
            //
            // //Initialize Select2 Elements
            // $('.select2bs4').select2({
            //     theme: 'bootstrap4'
            // })
            load_individual_customer();

        })

        function load_package_details(id){
            if (id==="0"){
                $("#product_details").slideUp();
            }else{

                $("#product_details").slideDown();
                $("#lending_officer_feild").slideDown();
                load_product_details(id);
                view_doc(id);

            }
        }


        function load_doc_charge(){
            let id=$("#package_details").val();
            $.ajax({
                type: "GET",
                url: "/loancategory/cost/" + id,
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
                            let charge_type = charge.charge_type;
                            let amount = parseFloat(charge.Amount);

                            // Check if the amount is valid
                            if (!isNaN(amount)) {
                                amount = amount.toFixed(2); // Format as 2 decimal places

                                if(charge_type === "Percentage") {
                                    let loan_amount = parseFloat($("#loan_amount").val());

                                    // Check if loan_amount is valid before performing calculations
                                    if (!isNaN(loan_amount)) {
                                        let new_loan_amount = loan_amount * (amount / 100);
                                        amount = new_loan_amount.toFixed(2); // Update the amount with the percentage value
                                        charge_type = charge_type + " (" + charge.Amount + "%)";
                                        console.log(amount);
                                    } else {
                                        console.warn("Invalid loan amount");
                                        amount = '0.00'; // Default value if loan amount is invalid
                                    }
                                }

                                // Create a new table row with valid data
                                var row = $('<tr></tr>');
                                row.append('<td>' + charge.Description + '</td>');
                                row.append('<td>' + charge_type + '</td>');
                                row.append('<td class="text-end">' + amount + '</td>');
                                otherChargesTable.append(row);
                                totalAmount += parseFloat(amount); // Add to total if valid
                            } else {
                                console.warn("Invalid charge amount for:", charge);
                            }
                        });

                        $('#total_loan_charge').text(totalAmount.toFixed(2));
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
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

                        $('#loan_amount').val(product.Loan_amount);
                        $('#loan_amount_from').val(product.Loan_amount);
                        $('#loan_amount_to').val(product.Loan_amount_to);
                        $("#loan_display").text("Minimum Amount "+parseFloat(product.Loan_amount).toFixed(2) +" - Maximum Amount "+parseFloat(product.Loan_amount_to).toFixed(2)).css("color", "red");
                        $('#interest_method').val(product.Interest_method);
                        $('#interest_period').val(product.Interest_period);
                        $('#loan_interest').val(product.Loan_interest);
                        $('#loan_interest_from').val(product.Loan_interest);
                        $('#loan_interest_to').val(product.Loan_interest_to);
                        $("#interest_display").text("Minimum Interest "+product.Loan_interest +"% - Maximum Interest "+product.Loan_interest_to+"%").css("color", "red");
                        $('#interest_period_count').val(product.Interest_Period_Count);
                        $('#duration_period').val(product.default_loan_duration_period);
                        console.log(product.default_loan_duration_period);
                        $('#repayment_duration_period').val(product.Duration_period);
                        let duration_period=$("#duration_period").val();
                        console.log(product.Duration_period);
                        repayment_type(product.Duration_period);
                        console.log($('#duration_period').val());
                        console.log(product.Duration_period);
                        $('#loan_period').val(product.Loan_period);
                        $('#repayment_type').val(product.Repayment_type);
                        $('#penalty_period').val(product.Panelty_period);
                        $('#penalty_percentage').val(product.Panelty_pecentage);
                        $('#penalty_date').val(product.Panelty_date);
                        $('#guarantee_count').val(product.Guarantee_count);


                        toggleFields();


                        let enable_saving_process = product.enable_saving_process;
                        $("#enable_saving").text(product.enable_saving_process);
                        $("#saving_payment_active").text(product.saving_payment);
                        if (enable_saving_process === "Yes") {
                            document.getElementById('saving_section').style.display = 'block';

                            if (product.saving_amount_type === "percentage") {

                                $("#saving_account_amount_type").text("Percentage"+" "+product.saving_amount+"%");
                                $("#saving_account_amount_type_2").text(product.saving_amount);
                                $("#saving_account_amount_type_3").text("Percentage");
                                let loan_amount=$("#loan_amount").val();
                                let amount=(loan_amount*product.saving_amount)/100;
                                $("#saving_amount").text(amount.toFixed(2));
                            } else {
                                $("#saving_account_amount_type").text("Amount");
                                $("#saving_account_amount_type_3").text("Amount");
                                $("#saving_amount").text(product.saving_amount);
                            }
                        } else {
                            document.getElementById('saving_section').style.display = 'none';
                        }





                        generateWitnessTabs(product.Guarantee_count);
                        $('#witness1').addClass('active show');
                        $('a[href="#witness1"]').tab('show');
                        changeCategory();
                        calculateInterest();
                        updateIssuedAmount();
                    } else {
                        console.log("No product details found");
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        }


        function issue_request() {
            let id=$("#customer_details").val();


            $.ajax({
                type: "GET",
                url: "/customers/bank/" + id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {

                    console.log(data);
                    // Clear the current options in the Select2 dropdown
                    $('#bank_acc').empty();

                    // Parse the response data if necessary

                    $('#bank_acc').append(new Option("Cash", 0));
                    // Populate the Select2 dropdown with the groups
                    $.each(data.item, function (index, group) {
                        $('#bank_acc').append(new Option(group.bank_name+" - "+group.account_number+" - "+group.account_name+" - "+group.branch+"", group.id));
                    });

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
                $("#customer_bank_feild").slideDown();
                $("#group_feild").slideDown();
                $('#customer_details').empty();
                var defaultOption = $('<option></option>')
                    .attr('value', "0")
                    .text("Select");
                $('#customer_details').append(defaultOption);
            }
        }


        function select_package(){


            let group=$("#group").val();
            let customer_details=$("#customer_details").val();

            $("#package_feild").slideDown();
            $("#leasing_feild").slideDown();
            $("#customer_bank_feild").slideDown();
            issue_request();
            // if(group==="1" && customer_details==="1"){
            //     $("#package_feild").slideDown();
            // }else{
            //
            // }


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
                    console.log(customerData);
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
                                .text("("+customer.cus_number + ")" + customer.First_Name + " " + customer.Last_Name + " - " + customer.Contact_No + " - " + customer.Nic); // Adjust according to your customer data structure
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


        function check_leasing(val){

            if(val==="Cash"){
                $("#leasing_feild_vehicle").slideUp();
            }else{
                $("#leasing_feild_vehicle").slideDown();
            }
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
                            var option = $('<option></option>') // Create a new <option> element
                                .attr('value', customer.idCustomer) // Set the value attribute of the option to customer.idCustomer
                                .text("(" + customer.cus_number + ") " + customer.First_Name + " " + customer.Last_Name + " - " + customer.Contact_No + " - " + customer.Nic);
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
                url: "/loancategory/cost/" + id,
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
                            let charge_type = charge.charge_type;
                            let amount = parseFloat(charge.Amount);

                            // Check if the amount is valid
                            if (!isNaN(amount)) {
                                amount = amount.toFixed(2); // Format as 2 decimal places

                                if(charge_type === "Percentage") {
                                    let loan_amount = parseFloat($("#loan_amount").val());

                                    // Check if loan_amount is valid before performing calculations
                                    if (!isNaN(loan_amount)) {
                                        let new_loan_amount = loan_amount * (amount / 100);
                                        amount = new_loan_amount.toFixed(2); // Update the amount with the percentage value
                                        charge_type = charge_type + " (" + charge.Amount + "%)";
                                        console.log(amount);
                                    } else {
                                        console.warn("Invalid loan amount");
                                        amount = '0.00'; // Default value if loan amount is invalid
                                    }
                                }

                                // Create a new table row with valid data
                                var row = $('<tr></tr>');
                                row.append('<td>' + charge.Description + '</td>');
                                row.append('<td>' + charge_type + '</td>');
                                row.append('<td class="text-end">' + amount + '</td>');
                                otherChargesTable.append(row);
                                totalAmount += parseFloat(amount); // Add to total if valid
                            } else {
                                console.warn("Invalid charge amount for:", charge);
                            }
                        });

                        $('#total_loan_charge').text(totalAmount.toFixed(2));

                        // Process required documents
                        var documentsTable = $('#document_show_table tbody');
                        documentsTable.empty(); // Clear existing rows
                        console.log(data.required_documents);
                        data.required_documents.forEach(function(document, index) {
                            var row = $('<tr></tr>');
                            row.append('<td>' + document.Name + '</td>');
                            row.append('<td><input type="file" class="form-control file-upload" data-document-id="' + document.idRequired_Documents + '"></td>');
                            row.append('<td hidden><input type="checkbox" id="check' + (index + 1) + '" class="form-check-input" checked></td>'); // Set checkbox ID dynamically
                            row.append('<td><button class="btn btn-danger"><i class="bi bi-trash"></i></button></td>');
                            documentsTable.append(row);
                        });
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });

            // Handle document removal
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


                leftColDiv.append($('<label></label>').addClass("form-label").text("Guarantor Type"));
                leftColDiv.append(typeSelect);


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


                rightColDiv.append($('<label></label>').addClass("form-label").text("Guarantor"));
                rightColDiv.append(customerSelect);

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

        function set_customers(id, number) {
            let customer_details = $("#customer_details").val();
            $.ajax({
                type: "GET",
                url: "/guarantor/load/" + id + "/" + customer_details,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(data, textStatus, xhr) {
                    if (xhr.status === 200) {
                        $('#customer_' + number).empty();

                        // Add a default option
                        $('#customer_' + number).append($('<option>', {
                            value: '0',
                            text: 'Select Customer'
                        }));

                        // Add customer options
                        data.customer.forEach(function(customer) {
                            $('#customer_' + number).append($('<option>', {
                                value: customer.idCustomer,
                                text: customer.First_Name + ' ' + customer.Last_Name + ' - ' + customer.Contact_No + ' - ' + customer.Nic
                            }));
                        });

                        // Initialize Select2 on the dropdown
                        $('#customer_' + number).select2({
                            placeholder: "Select Customer", // Set placeholder
                            allowClear: true // Allow clearing the selection
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
            $('#fisrt_name'+number).val("");
            $('#last_name'+number).val("");
            $('#nic'+number).val("");
            $('#contact'+number).val("");
            $('#address'+number).val("");
            $.ajax({
                type: "GET",
                url: "/guarantor/load/details/"+id+"/"+type+"/",
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
                    $('#first_name'+number).val(customer.First_Name);
                    $('#last_name'+number).val(customer.Last_Name);
                    $('#nic'+number).val(customer.Nic);
                    $('#contact'+number).val(customer.Contact_No);
                    $('#address'+number).val(customer.Address);
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

            var tableBody = $('#installment_table tbody');

            // Clear existing rows
            tableBody.empty();
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


        let holidays = []; // Global variable for holidays

        // Function to fetch holidays
        function fetchHolidays() {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '/get-holidays',
                    type: 'GET',
                    dataType: 'json',
                    success: function (response) {
                        holidays = response.map(date => new Date(date).toISOString().split('T')[0]); // Ensure consistent format
                        console.log('Holidays fetched successfully:', holidays);
                        resolve(); // Resolve the Promise once holidays are fetched
                    },
                    error: function (error) {
                        console.error('Failed to fetch holidays:', error);
                        reject(error); // Reject the Promise on error
                    }
                });
            });
        }

        // Function to calculate dates (returns a Promise)
        function calculateDates(installmentCount) {
            return new Promise(async (resolve) => {
                const installmentDates = [];
                let i = 0;
                let currentDate = new Date();

                while (i < installmentCount) {
                    currentDate.setDate(currentDate.getDate() + 1);
                    const formattedDate = new Date(currentDate).toISOString().split('T')[0];

                    if (!holidays.includes(formattedDate)) {
                        installmentDates.push(formattedDate);
                        i++;
                    }
                }

                console.log('Final Installment Dates:', installmentDates);
                resolve(installmentDates); // Resolve the Promise with the installment dates
            });
        }





        function addInstallmentDates() {

            let startDate = $("#installment_date_txt").val();
            let total_loan_charge = $("#total_loan_charge").text();
            let total_loan_amount = parseFloat($("#total_loan_amount").text());
            let loan_amount = parseFloat($("#loan_amount").val());
            let loan_amount_ins = parseFloat($("#loan_amount").val());
            let installmentCount = $("#loan_period").val();
            let saturday_sunday = $("#saturday_sunday").val();
            let interest_method = $("#interest_method").val();
            let capital_amount = 0.0;
            let interest_amount = 0.0;
            let tot_amount = 0.0;
            let installmentAmount = 0.0;  // Initialize installmentAmount
            let interest = $("#loan_interest").val();



            const loanAmountFrom = parseFloat($("#loan_amount_from").val());
            const loanAmountTo = parseFloat($("#loan_amount_to").val());

            // Validate if loan_amount_from and loan_amount_to are numeric
            if (isNaN(loanAmountFrom) || isNaN(loanAmountTo)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please enter valid numeric values for Minimum Loan Amount to Maximum Loan Amount',
                });
                return; // Stop the function if validation fails
            }

            // Validate if loan_amount is a valid number
            loan_amount = parseFloat(loan_amount);
            if (isNaN(loan_amount)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please enter a valid Loan Amount.',
                });
                return; // Stop the function if validation fails
            }

            // Check if loan_amount is within the range
            if (loan_amount < loanAmountFrom) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: `Loan Amount must be greater than or equal to ${loanAmountFrom}`,
                });
                return; // Stop the function if validation fails
            } else if (loan_amount > loanAmountTo) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: `Loan Amount must be less than or equal to ${loanAmountTo}`,
                });
                return; // Stop the function if validation fails
            }

            const loan_interest_from = parseFloat($("#loan_interest_from").val());
            const loan_interest_to = parseFloat($("#loan_interest_to").val());

            // Validate if loan_interest_from and loan_interest_to are numeric
            if (isNaN(loan_interest_from) || isNaN(loan_interest_to)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please enter valid numeric values for Minimum Interest to Maximum Interest',
                });
                return; // Stop the function if validation fails
            }

            // Validate if interest is a valid number
            interest = parseFloat(interest);
            if (isNaN(interest)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please enter a valid Loan Interest.',
                });
                return; // Stop the function if validation fails
            }

            // Check if interest is within the range
            if (interest < loan_interest_from) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: `Interest must be greater than or equal to ${loan_interest_from}`,
                });
                return; // Stop the function if validation fails
            } else if (interest > loan_interest_to) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: `Interest must be less than or equal to ${loan_interest_to}`,
                });
                return; // Stop the function if validation fails
            }





            let loanChargesBalanceCheckbox = document.getElementById("loanChargesBalance");

            if (loanChargesBalanceCheckbox.checked) {
                loan_amount = loan_amount + parseFloat(total_loan_charge);
                tot_amount = total_loan_amount - loan_amount_ins;
                capital_amount = loan_amount / installmentCount;
                interest_amount = tot_amount / installmentCount;
            } else {
                capital_amount = loan_amount / installmentCount;
                tot_amount = total_loan_amount - loan_amount_ins;
                interest_amount = tot_amount / installmentCount;
            }



            capital_amount = capital_amount.toFixed(2);
            interest_amount = interest_amount.toFixed(2);

            let panelty_amount = $("#penalty_percentage").val();
            installmentAmount = parseFloat($("#new_interest_amount").text());
            let panelty_date_2 = parseFloat($("#panelty_date_2").text());


            if (interest_method === "Draft") {
                installmentAmount = interest_amount;
                installmentAmount = parseFloat(installmentAmount);
                capital_amount = 0.00;
            }


            let loan_type = $("#repayment_type").val();

            if (startDate.trim() === "" || isNaN(new Date(startDate))) {
                Swal.fire("Error!", "Please enter a valid installment date !", "error");
            } else if (isNaN(parseInt(installmentCount)) || parseInt(installmentCount) <= 0) {
                Swal.fire("Error!", "Please enter a valid installment count greater than zero !", "error")
            } else if (isNaN(installmentAmount) || installmentAmount <= 0) {
                Swal.fire("Error!", "Please calculate the installment amount first !", "error")
            } else if (panelty_amount === "") {
                Swal.fire("Error!", "Please calculate the penalty rate !", "error")
            } else {





                let saving=$("#enable_saving").text();
                let saving_payment_active=$("#saving_payment_active").text();



                let saving_amount_value=0.00;
                if (saving==="Yes"){
                    let saving_amount=$("#saving_amount").text();
                    saving_amount_value=saving_amount;
                    if(saving_payment_active==="1"){
                        saving_amount_value=0.00;
                    }
                }




                $('#installment_table thead').empty();
                // if (saving==="Yes"){

                    // Define the table header content
                    var theadContent = `
        <thead>
            <tr>
                <th>No</th>
                <th>Installment Date</th>
                <th class="text-end">Installment Amount</th>
                <th class="text-end">Capital Amount</th>
                <th class="text-end">Interest Amount</th>
                <th class="text-end" style="width: 100px;">Penalty Date</th>
                <th class="text-end">Penalty Amount</th>
                <th class="text-end">Saving Amount</th>
                <th class="text-end">Total Amount</th>
                <th class="text-end">Paid Amount</th>
                <th class="text-end">Penalty Balance</th>
                <th class="text-end">Installment Balance</th>
                <th class="text-end">Savings Balance</th>
                <th class="text-end">Total Balance</th>
                <th>Status</th>
            </tr>
        </thead>
    `;

                    // Append the header content to the table
                    $('#installment_table').prepend(theadContent);




                    if(loan_type==="Daily"){
                        var selected_date = new Date($('#installment_date_txt').val());
                        let on_a_selected_date_txt=$('#installment_date_txt').val();

                        if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                            Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                        }else{
                            var installmentDates = [];
                            var currentDate = new Date(on_a_selected_date_txt);

                            // Function to format date as YYYY-MM-DD
                            function formatDate(date) {
                                var year = date.getFullYear();
                                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                                var day = date.getDate().toString().padStart(2, '0');
                                return year + '-' + month + '-' + day;
                            }

                            // Ensure holidays array contains consistently formatted dates
                            holidays = holidays.map(date => new Date(date).toISOString().split('T')[0]);

                            for (var i = 0; i < installmentCount;) {
                                // Add one day to the current date
                                currentDate.setDate(currentDate.getDate() + 1);

                                // Format the current date as YYYY-MM-DD
                                var formattedDate = new Date(currentDate).toISOString().split('T')[0]; // Ensure consistent format

                                // Log both for debugging
                                console.log('Checking date:', formattedDate);
                                console.log('Holidays:', holidays);

                                // Check if the current date is NOT in the holidays array
                                if (!holidays.includes(formattedDate)) {
                                    console.log('Date added:', formattedDate); // Log added dates
                                    installmentDates.push(formattedDate);
                                    i++; // Increment only for valid dates (non-holidays)
                                } else {
                                    console.log('Skipped holiday:', formattedDate); // Log skipped dates
                                }
                            }
                            // Usage





                            // Get the table body
                            var tableBody = $('#installment_table tbody');

                            // Clear existing rows
                            tableBody.empty();
                            let saving_amount_show=installmentAmount+parseFloat(saving_amount_value);
                            installmentAmount=installmentAmount.toFixed(2);

                            let count=1;

                            installmentDates.forEach(function(date) {
                                let currentDate = new Date(date);

                                currentDate.setDate(currentDate.getDate() + panelty_date_2);

                                let panelty_date = currentDate.toISOString().slice(0, 10);

                                if(interest_method === "Reducing Balance") {
                                    installmentAmount = parseFloat($("#installment_amount").val());
                                    let loan_period = parseFloat($("#loan_period").val());
                                    let total_interest_amount = parseFloat($("#total_interest_amount").text());

// Ensure variables are valid and not NaN
                                    if (!isNaN(installmentAmount) && !isNaN(loan_period) && !isNaN(total_interest_amount)) {
                                        interest_amount = (total_interest_amount / ((loan_period * (1 + loan_period)) / 2)) * ((loan_period + 1) - count);
                                        let capital_amount = installmentAmount - interest_amount;

                                        // Ensure saving_amount_value and saving_amount_show are valid
                                        saving_amount_value = parseFloat(saving_amount_value) || 0;
                                        saving_amount_show = parseFloat(saving_amount_show) || 0;

                                        var row = '<tr>' +
                                            '<td>' + count + '</td>' +
                                            '<td>' + date + '</td>' +
                                            '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + capital_amount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + interest_amount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + panelty_date + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                            '<td class="text-center">' +
                                            '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                            '</td>' +
                                            '</tr>';

                                        tableBody.append(row);
                                    } else {
                                        console.error("Invalid input: Check installment amount, loan period, or total interest amount.");
                                    }

                                }else{

                                    // Create the row
                                    var row = '<tr>' +
                                        '<td>' + count + '</td>' +
                                        '<td>' + date + '</td>' +
                                        '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(capital_amount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(interest_amount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + panelty_date + '</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                        '<td class="text-center">' +
                                        '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                        '</td>' +
                                        '</tr>';

                                    tableBody.append(row);
                                }



                                var span = tableBody.children('tr:last-child').find('span');

                                var installmentDate = new Date(date);
                                var currentTime = new Date();

                                if (installmentDate > currentTime) {
                                    span.addClass('bg-warning text-danger').text('-');
                                } else {
                                    span.addClass('bg-danger text-warning').text('-');
                                }
                                count++;
                            });
                        }
                    }else if(loan_type==="Weekly"){

                        var selected_date = new Date($('#installment_date_txt').val());
                        var weekly_txt = $('#weekly_txt').val();



                        // Check if it's the first day of the month
                        if (selected_date.getDay() == weekly_txt) {

                            let on_a_selected_date_txt=$('#installment_date_txt').val();

                            if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                                Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                            }else{

                                var installmentDates = [];
                                var currentDate = new Date(on_a_selected_date_txt);

                                // Function to format date as YYYY-MM-DD
                                function formatDate(date) {
                                    var year = date.getFullYear();
                                    var month = (date.getMonth() + 1).toString().padStart(2, '0');
                                    var day = date.getDate().toString().padStart(2, '0');
                                    return year + '-' + month + '-' + day;
                                }

                                // Loop through each installment count
                                for (var i = 0; i < installmentCount; i++) {
                                    // Add one month to the current date
                                    // Format the date

                                    var formattedDate = formatDate(currentDate);
                                    installmentDates.push(formattedDate);
                                    currentDate.setDate(currentDate.getDate()+7)

                                }



                                // Get the table body
                                var tableBody = $('#installment_table tbody');

                                // Clear existing rows
                                tableBody.empty();
                                let saving_amount_show=installmentAmount+parseFloat(saving_amount_value);
                                installmentAmount=installmentAmount.toFixed(2);




                                let count=1;
                                installmentDates.forEach(function(date) {

                                    let currentDate = new Date(date);

                                    currentDate.setDate(currentDate.getDate() + panelty_date_2);

                                    let panelty_date = currentDate.toISOString().slice(0, 10);

                                    if(interest_method === "Reducing Balance") {
                                        installmentAmount = parseFloat($("#installment_amount").val());
                                        let loan_period = parseFloat($("#loan_period").val());
                                        let total_interest_amount = parseFloat($("#total_interest_amount").text());

// Ensure variables are valid and not NaN
                                        if (!isNaN(installmentAmount) && !isNaN(loan_period) && !isNaN(total_interest_amount)) {
                                            interest_amount = (total_interest_amount / ((loan_period * (1 + loan_period)) / 2)) * ((loan_period + 1) - count);
                                            let capital_amount = installmentAmount - interest_amount;

                                            // Ensure saving_amount_value and saving_amount_show are valid
                                            saving_amount_value = parseFloat(saving_amount_value) || 0;
                                            saving_amount_show = parseFloat(saving_amount_show) || 0;

                                            var row = '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + date + '</td>' +
                                                '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + capital_amount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + interest_amount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + panelty_date + '</td>' +
                                                '<td class="text-end">0.00</td>' +
                                                '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                                '<td class="text-end">0.00</td>' +
                                                '<td class="text-end">0.00</td>' +
                                                '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                                '<td class="text-center">' +
                                                '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                                '</td>' +
                                                '</tr>';

                                            tableBody.append(row);
                                        } else {
                                            console.error("Invalid input: Check installment amount, loan period, or total interest amount.");
                                        }

                                    }else{

                                        var row = '<tr>' +
                                            '<td>' + count + '</td>' +
                                            '<td>' + date + '</td>' +
                                            '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(capital_amount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(interest_amount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + panelty_date + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                            '<td class="text-center">' +
                                            '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                            '</td>' +
                                            '</tr>';

                                        tableBody.append(row);
                                    }

                                    var span = tableBody.children('tr:last-child').find('span');

                                    var installmentDate = new Date(date);
                                    var currentTime = new Date();

                                    if (installmentDate > currentTime) {
                                        span.addClass('bg-danger text-danger').text('-');
                                    } else {
                                        span.addClass('bg-warning text-warning').text('-');
                                    }
                                    count++;
                                });
                            }
                        } else {
                            Swal.fire("Error!", "Selected date is not a equal to selected day !", "error")
                        }



                    }else if (loan_type==="First Of The Month"){
                        var selected_date = new Date($('#installment_date_txt').val());

                        // Check if it's the first day of the month
                        if (selected_date.getDate() === 1) {

                            let on_a_selected_date_txt=$('#installment_date_txt').val();

                            if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                                Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                            }else{
                                var installmentDates = [];
                                var currentDate = new Date(on_a_selected_date_txt);

                                // Function to format date as YYYY-MM-DD
                                function formatDate(date) {
                                    var year = date.getFullYear();
                                    var month = (date.getMonth() + 1).toString().padStart(2, '0');
                                    var day = date.getDate().toString().padStart(2, '0');
                                    return year + '-' + month + '-' + day;
                                }

                                // Loop through each installment count
                                for (var i = 0; i < installmentCount; i++) {
                                    // Add one month to the current date
                                    // Format the date

                                    var formattedDate = formatDate(currentDate);
                                    installmentDates.push(formattedDate);
                                    currentDate.setMonth(currentDate.getMonth() + 1);
                                }

                                // Get the table body
                                var tableBody = $('#installment_table tbody');

                                // Clear existing rows
                                tableBody.empty();
                                let saving_amount_show=installmentAmount+parseFloat(saving_amount_value);
                                installmentAmount=installmentAmount.toFixed(2);
                                let count=1;
                                installmentDates.forEach(function(date) {
                                    let currentDate = new Date(date);

                                    currentDate.setDate(currentDate.getDate() + panelty_date_2);

                                    let panelty_date = currentDate.toISOString().slice(0, 10);

                                    if(interest_method === "Reducing Balance") {
                                        installmentAmount = parseFloat($("#installment_amount").val());
                                        let loan_period = parseFloat($("#loan_period").val());
                                        let total_interest_amount = parseFloat($("#total_interest_amount").text());

// Ensure variables are valid and not NaN
                                        if (!isNaN(installmentAmount) && !isNaN(loan_period) && !isNaN(total_interest_amount)) {
                                            interest_amount = (total_interest_amount / ((loan_period * (1 + loan_period)) / 2)) * ((loan_period + 1) - count);
                                            let capital_amount = installmentAmount - interest_amount;

                                            // Ensure saving_amount_value and saving_amount_show are valid
                                            saving_amount_value = parseFloat(saving_amount_value) || 0;
                                            saving_amount_show = parseFloat(saving_amount_show) || 0;

                                            var row = '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + date + '</td>' +
                                                '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + capital_amount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + interest_amount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + panelty_date + '</td>' +
                                                '<td class="text-end">0.00</td>' +
                                                '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                                '<td class="text-end">0.00</td>' +
                                                '<td class="text-end">0.00</td>' +
                                                '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                                '<td class="text-center">' +
                                                '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                                '</td>' +
                                                '</tr>';

                                            tableBody.append(row);
                                        } else {
                                            console.error("Invalid input: Check installment amount, loan period, or total interest amount.");
                                        }

                                    }else{
                                        var row = '<tr>' +
                                            '<td>' + count + '</td>' +
                                            '<td>' + date + '</td>' +
                                            '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(capital_amount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(interest_amount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + panelty_date + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                            '<td class="text-center">' +
                                            '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                            '</td>' +
                                            '</tr>';

                                        tableBody.append(row);
                                    }

                                    var span = tableBody.children('tr:last-child').find('span');

                                    var installmentDate = new Date(date);
                                    var currentTime = new Date();

                                    if (installmentDate > currentTime) {
                                        span.addClass('bg-danger text-danger').text('-');
                                    } else {
                                        span.addClass('bg-warning text-warning').text('-');
                                    }
                                    count++;
                                });
                            }
                        } else {
                            Swal.fire("Error!", "Selected date is not the first day of the month !", "error")
                        }
                    }else if (loan_type==="End Of The Month"){
                        var on_a_selected_date_txt = $('#installment_date_txt').val();
                        var selected_date = new Date(on_a_selected_date_txt);

// Get the last day of the month for the selected date
                        var lastDayOfMonth = new Date(selected_date.getFullYear(), selected_date.getMonth() + 1, 0);

// Check if it's the last day of the month
                        if (selected_date.getDate() === lastDayOfMonth.getDate()) {
                            if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                                Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                            } else {
                                var installmentDates = [];
                                var currentDate = new Date(on_a_selected_date_txt);

                                // Function to format date as YYYY-MM-DD
                                function formatDate(date) {
                                    var year = date.getFullYear();
                                    var month = (date.getMonth() + 1).toString().padStart(2, '0');
                                    var day = date.getDate().toString().padStart(2, '0');
                                    return year + '-' + month + '-' + day;
                                }

                                // Loop through each installment count
                                for (var i = 0; i < installmentCount; i++) {

                                    var selectedDate = new Date($('#installment_date_txt').val());

// Get the last day of the selected month
                                    var lastDayOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + 1+i, 0);


                                    // Get the last day of the current month
                                    // var nextMonthDate = new Date(currentDate);
                                    // nextMonthDate.setMonth(nextMonthDate.getMonth() + 1);
                                    // nextMonthDate.setDate(0); // Set to last day of current month

                                    // Format the date
                                    var formattedDate = formatDate(lastDayOfMonth);
                                    installmentDates.push(formattedDate);

                                    // Move to the first day of the next month
                                    // currentDate.setMonth(currentDate.getMonth() + 1);
                                    // currentDate.setDate(1);
                                }

                                // Get the table body
                                var tableBody = $('#installment_table tbody');

                                // Clear existing rows
                                tableBody.empty();
                                let saving_amount_show=installmentAmount+parseFloat(saving_amount_value);
                                installmentAmount = installmentAmount.toFixed(2);
                                let count=1;
                                installmentDates.forEach(function(date) {

                                    let currentDate = new Date(date);

                                    currentDate.setDate(currentDate.getDate() + panelty_date_2);

                                    let panelty_date = currentDate.toISOString().slice(0, 10);

                                    if(interest_method === "Reducing Balance") {
                                        installmentAmount = parseFloat($("#installment_amount").val());
                                        let loan_period = parseFloat($("#loan_period").val());
                                        let total_interest_amount = parseFloat($("#total_interest_amount").text());

// Ensure variables are valid and not NaN
                                        if (!isNaN(installmentAmount) && !isNaN(loan_period) && !isNaN(total_interest_amount)) {
                                            interest_amount = (total_interest_amount / ((loan_period * (1 + loan_period)) / 2)) * ((loan_period + 1) - count);
                                            let capital_amount = installmentAmount - interest_amount;

                                            // Ensure saving_amount_value and saving_amount_show are valid
                                            saving_amount_value = parseFloat(saving_amount_value) || 0;
                                            saving_amount_show = parseFloat(saving_amount_show) || 0;

                                            var row = '<tr>' +
                                                '<td>' + count + '</td>' +
                                                '<td>' + date + '</td>' +
                                                '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + capital_amount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + interest_amount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + panelty_date + '</td>' +
                                                '<td class="text-end">0.00</td>' +
                                                '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                                '<td class="text-end">0.00</td>' +
                                                '<td class="text-end">0.00</td>' +
                                                '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                                '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                                '<td class="text-center">' +
                                                '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                                '</td>' +
                                                '</tr>';

                                            tableBody.append(row);
                                        } else {
                                            console.error("Invalid input: Check installment amount, loan period, or total interest amount.");
                                        }

                                    }else{
                                        var row = '<tr>' +
                                            '<td>' + count + '</td>' +
                                            '<td>' + date + '</td>' +
                                            '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(capital_amount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(interest_amount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + panelty_date + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                            '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                            '<td class="text-center">' +
                                            '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                            '</td>' +
                                            '</tr>';

                                        tableBody.append(row);
                                    }



                                    var span = tableBody.children('tr:last-child').find('span');

                                    var installmentDate = new Date(date);
                                    var currentTime = new Date();

                                    if (installmentDate > currentTime) {
                                        span.addClass('bg-danger text-danger').text('-');
                                    } else {
                                        span.addClass('bg-warning text-warning').text('-');
                                    }

                                    count++;
                                });
                            }
                        } else {
                            Swal.fire("Error!", "Selected date is not the last day of the month !", "error")
                        }


                    }else if (loan_type==="Twice A Month"){
                        let twice_a_month_txt=$('#twice_a_month_txt').val();
                        if (twice_a_month_txt=="2"){
                            var installmentDates = [];
                            var currentDate = new Date(on_a_selected_date_txt);

                            // Function to format date as YYYY-MM-DD
                            function formatDate(date) {
                                var year = date.getFullYear();
                                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                                var day = date.getDate().toString().padStart(2, '0');
                                return year + '-' + month + '-' + day;
                            }

                            // Loop through each installment count
                            for (var i = 0; i < (installmentCount/2); i++) {

                                var selectedDate = new Date($('#installment_date_txt').val());

// Get the last day of the selected month


                                var lastDayOfMonth2 = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 15);
                                var lastDayOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i+1, 0);


                                var formattedDate = formatDate(lastDayOfMonth2);
                                installmentDates.push(formattedDate);
                                var formattedDate2 = formatDate(lastDayOfMonth);
                                installmentDates.push(formattedDate2);

                                // Move to the first day of the next month
                                // currentDate.setMonth(currentDate.getMonth() + 1);
                                // currentDate.setDate(1);
                            }

                            // Get the table body
                            var tableBody = $('#installment_table tbody');

                            // Clear existing rows
                            tableBody.empty();
                            let saving_amount_show=installmentAmount+parseFloat(saving_amount_value);
                            installmentAmount = installmentAmount.toFixed(2);
                            let count=1;
                            installmentDates.forEach(function(date) {
                                let currentDate = new Date(date);

                                currentDate.setDate(currentDate.getDate() + panelty_date_2);

                                let panelty_date = currentDate.toISOString().slice(0, 10);


                                if(interest_method === "Reducing Balance") {
                                    installmentAmount = parseFloat($("#installment_amount").val());
                                    let loan_period = parseFloat($("#loan_period").val());
                                    let total_interest_amount = parseFloat($("#total_interest_amount").text());

// Ensure variables are valid and not NaN
                                    if (!isNaN(installmentAmount) && !isNaN(loan_period) && !isNaN(total_interest_amount)) {
                                        interest_amount = (total_interest_amount / ((loan_period * (1 + loan_period)) / 2)) * ((loan_period + 1) - count);
                                        let capital_amount = installmentAmount - interest_amount;

                                        // Ensure saving_amount_value and saving_amount_show are valid
                                        saving_amount_value = parseFloat(saving_amount_value) || 0;
                                        saving_amount_show = parseFloat(saving_amount_show) || 0;

                                        var row = '<tr>' +
                                            '<td>' + count + '</td>' +
                                            '<td>' + date + '</td>' +
                                            '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + capital_amount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + interest_amount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + panelty_date + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                            '<td class="text-center">' +
                                            '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                            '</td>' +
                                            '</tr>';

                                        tableBody.append(row);
                                    } else {
                                        console.error("Invalid input: Check installment amount, loan period, or total interest amount.");
                                    }

                                }else{
                                    var row = '<tr>' +
                                        '<td>' + count + '</td>' +
                                        '<td>' + date + '</td>' +
                                        '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(capital_amount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(interest_amount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + panelty_date + '</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                        '<td class="text-center">' +
                                        '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                        '</td>' +
                                        '</tr>';

                                    tableBody.append(row);
                                }



                                var span = tableBody.children('tr:last-child').find('span');

                                var installmentDate = new Date(date);
                                var currentTime = new Date();

                                if (installmentDate > currentTime) {
                                    span.addClass('bg-danger text-danger').text('-');
                                } else {
                                    span.addClass('bg-warning text-warning').text('-');
                                }
                                count++;
                            });





                        }else{
                            var installmentDates = [];
                            var currentDate = new Date(on_a_selected_date_txt);

                            // Function to format date as YYYY-MM-DD
                            function formatDate(date) {
                                var year = date.getFullYear();
                                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                                var day = date.getDate().toString().padStart(2, '0');
                                return year + '-' + month + '-' + day;
                            }

                            // Loop through each installment count
                            for (var i = 0; i < (installmentCount/2); i++) {

                                var selectedDate = new Date($('#installment_date_txt').val());

// Get the last day of the selected month
                                var lastDayOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 1);

                                var lastDayOfMonth2 = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 15);


                                // Get the last day of the current month
                                // var nextMonthDate = new Date(currentDate);
                                // nextMonthDate.setMonth(nextMonthDate.getMonth() + 1);
                                // nextMonthDate.setDate(0); // Set to last day of current month
                                // Format the date
                                var formattedDate = formatDate(lastDayOfMonth);
                                installmentDates.push(formattedDate);
                                var formattedDate2 = formatDate(lastDayOfMonth2);
                                installmentDates.push(formattedDate2);

                                // Move to the first day of the next month
                                // currentDate.setMonth(currentDate.getMonth() + 1);
                                // currentDate.setDate(1);
                            }

                            // Get the table body
                            var tableBody = $('#installment_table tbody');

                            // Clear existing rows
                            tableBody.empty();
                            let saving_amount_show=installmentAmount+parseFloat(saving_amount_value);
                            installmentAmount = installmentAmount.toFixed(2);
                            let count=1;
                            installmentDates.forEach(function(date) {
                                let currentDate = new Date(date);

                                currentDate.setDate(currentDate.getDate() + panelty_date_2);

                                let panelty_date = currentDate.toISOString().slice(0, 10);


                                if(interest_method === "Reducing Balance") {
                                    installmentAmount = parseFloat($("#installment_amount").val());
                                    let loan_period = parseFloat($("#loan_period").val());
                                    let total_interest_amount = parseFloat($("#total_interest_amount").text());

// Ensure variables are valid and not NaN
                                    if (!isNaN(installmentAmount) && !isNaN(loan_period) && !isNaN(total_interest_amount)) {
                                        interest_amount = (total_interest_amount / ((loan_period * (1 + loan_period)) / 2)) * ((loan_period + 1) - count);
                                        let capital_amount = installmentAmount - interest_amount;

                                        // Ensure saving_amount_value and saving_amount_show are valid
                                        saving_amount_value = parseFloat(saving_amount_value) || 0;
                                        saving_amount_show = parseFloat(saving_amount_show) || 0;

                                        var row = '<tr>' +
                                            '<td>' + count + '</td>' +
                                            '<td>' + date + '</td>' +
                                            '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + capital_amount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + interest_amount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + panelty_date + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                            '<td class="text-center">' +
                                            '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                            '</td>' +
                                            '</tr>';

                                        tableBody.append(row);
                                    } else {
                                        console.error("Invalid input: Check installment amount, loan period, or total interest amount.");
                                    }

                                }else{
                                    var row = '<tr>' +
                                        '<td>' + count + '</td>' +
                                        '<td>' + date + '</td>' +
                                        '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(capital_amount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(interest_amount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + panelty_date + '</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                        '<td class="text-center">' +
                                        '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                        '</td>' +
                                        '</tr>';

                                    tableBody.append(row);
                                }



                                var span = tableBody.children('tr:last-child').find('span');

                                var installmentDate = new Date(date);
                                var currentTime = new Date();

                                if (installmentDate > currentTime) {
                                    span.addClass('bg-danger text-danger').text('-');
                                } else {
                                    span.addClass('bg-warning text-warning').text('-');
                                }
                                count++;
                            });
                        }

                    }else if (loan_type==="On A Selected Date"){

                        let on_a_selected_date_txt = $("#installment_date_txt").val();

                        if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                            Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                        }else{
                            var installmentDates = [];
                            var currentDate = new Date(on_a_selected_date_txt);

                            // Function to format date as YYYY-MM-DD
                            function formatDate(date) {
                                var year = date.getFullYear();
                                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                                var day = date.getDate().toString().padStart(2, '0');
                                return year + '-' + month + '-' + day;
                            }

                            // Loop through each installment count
                            for (var i = 0; i < installmentCount; i++) {
                                // Add one month to the current date
                                // Format the date

                                var formattedDate = formatDate(currentDate);
                                installmentDates.push(formattedDate);
                                currentDate.setMonth(currentDate.getMonth() + 1);
                            }

                            // Get the table body
                            var tableBody = $('#installment_table tbody');

                            // Clear existing rows
                            tableBody.empty();
                            let saving_amount_show=installmentAmount+parseFloat(saving_amount_value);
                            installmentAmount=installmentAmount.toFixed(2);
                            let count=1;
                            installmentDates.forEach(function(date) {

                                let currentDate = new Date(date);

                                currentDate.setDate(currentDate.getDate() + panelty_date_2);

                                let panelty_date = currentDate.toISOString().slice(0, 10);

                                if(interest_method === "Reducing Balance") {
                                    installmentAmount = parseFloat($("#installment_amount").val());
                                    let loan_period = parseFloat($("#loan_period").val());
                                    let total_interest_amount = parseFloat($("#total_interest_amount").text());

// Ensure variables are valid and not NaN
                                    if (!isNaN(installmentAmount) && !isNaN(loan_period) && !isNaN(total_interest_amount)) {
                                        interest_amount = (total_interest_amount / ((loan_period * (1 + loan_period)) / 2)) * ((loan_period + 1) - count);
                                        let capital_amount = installmentAmount - interest_amount;

                                        // Ensure saving_amount_value and saving_amount_show are valid
                                        saving_amount_value = parseFloat(saving_amount_value) || 0;
                                        saving_amount_show = parseFloat(saving_amount_show) || 0;

                                        var row = '<tr>' +
                                            '<td>' + count + '</td>' +
                                            '<td>' + date + '</td>' +
                                            '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + capital_amount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + interest_amount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + panelty_date + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">0.00</td>' +
                                            '<td class="text-end">' + installmentAmount.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_value.toFixed(2) + '</td>' +
                                            '<td class="text-end">' + saving_amount_show.toFixed(2) + '</td>' +
                                            '<td class="text-center">' +
                                            '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                            '</td>' +
                                            '</tr>';

                                        tableBody.append(row);
                                    } else {
                                        console.error("Invalid input: Check installment amount, loan period, or total interest amount.");
                                    }

                                }else{
                                    var row = '<tr>' +
                                        '<td>' + count + '</td>' +
                                        '<td>' + date + '</td>' +
                                        '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(capital_amount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(interest_amount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + panelty_date + '</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">0.00</td>' +
                                        '<td class="text-end">' + parseFloat(installmentAmount).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>' +
                                        '<td class="text-end">' + parseFloat(saving_amount_show).toFixed(2) + '</td>' +
                                        '<td class="text-center">' +
                                        '<span class="px-1" style="background-color: #ff0000; border-radius: 10px; color: #ff0000;">-</span>' +
                                        '</td>' +
                                        '</tr>';

                                    tableBody.append(row);
                                }

                                var span = tableBody.children('tr:last-child').find('span');

                                var installmentDate = new Date(date);
                                var currentTime = new Date();

                                if (installmentDate > currentTime) {
                                    span.addClass('bg-danger text-danger').text('-');
                                } else {
                                    span.addClass('bg-warning text-warning').text('-');
                                }
                                count++;
                            });
                        }
                    }

                // }
//                 else{
//
//                     // Define the table header content
//                     var theadContent = `
//         <thead>
//             <tr>
//                 <th>No</th>
//                 <th>Installment Date</th>
//                 <th class="text-end">Installment Amount</th>
//                 <th class="text-end">Capital Amount</th>
//                 <th class="text-end">Interest Amount</th>
//                 <th class="text-end">Penalty Date</th>
//                 <th class="text-end">Penalty Amount</th>
//                 <th class="text-end">Total Amount</th>
//                 <th class="text-end">Paid Amount</th>
//                 <th class="text-end">Penalty Balance</th>
//                 <th class="text-end">Installment Balance</th>
//                 <th class="text-end">Total Balance</th>
//                 <th>Status</th>
//             </tr>
//         </thead>
//     `;
//
//                     // Append the header content to the table
//                     $('#installment_table').prepend(theadContent);
//
//
//
//                     if(loan_type==="Daily"){
//                         var selected_date = new Date($('#installment_date_txt').val());
//                         let on_a_selected_date_txt=$('#installment_date_txt').val();
//
//                         if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
//                             Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
//                         }else{
//                             var installmentDates = [];
//                             var currentDate = new Date(on_a_selected_date_txt);
//
//                             // Function to format date as YYYY-MM-DD
//                             function formatDate(date) {
//                                 var year = date.getFullYear();
//                                 var month = (date.getMonth() + 1).toString().padStart(2, '0');
//                                 var day = date.getDate().toString().padStart(2, '0');
//                                 return year + '-' + month + '-' + day;
//                             }
//
//                             // Loop through each installment count
//                             for (var i = 0; i < installmentCount;) {
//                                 // Add one day to the current date
//                                 currentDate.setDate(currentDate.getDate() + 1);
//
//                                 // Check if the current date is Saturday or Sunday
//                                 var dayOfWeek = currentDate.getDay();
//
//                                 if(saturday_sunday==='1'){
//                                     if (dayOfWeek !== 0 && dayOfWeek !== 6) { // 0 is Sunday, 6 is Saturday
//                                         // Format the date
//                                         var formattedDate = formatDate(currentDate);
//                                         installmentDates.push(formattedDate);
//                                         i++; // Only increment i if it's a valid date
//                                     }
//
//                                 }else {
//
//
//                                     var formattedDate = formatDate(currentDate);
//                                     installmentDates.push(formattedDate);
//                                     i++;
//
//
//
//                                 }
//
//                             }
//                             // Get the table body
//                             var tableBody = $('#installment_table tbody');
//
//                             // Clear existing rows
//                             tableBody.empty();
//                             installmentAmount=installmentAmount.toFixed(2);
//                             let count=1;
//                             installmentDates.forEach(function(date) {
//                                 let currentDate = new Date(date);
//
//                                 currentDate.setDate(currentDate.getDate() + panelty_date_2);
//
//                                 let panelty_date = currentDate.toISOString().slice(0, 10);
//
//
//                                 var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+capital_amount+'</td><td class="text-end">'+interest_amount+'</td><td  class="text-end">' + panelty_date + '</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+installmentAmount+'</td><td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td></tr>';
//                                 tableBody.append(row);
//                                 var span = tableBody.children('tr:last-child').find('span');
//
//                                 var installmentDate = new Date(date);
//                                 var currentTime = new Date();
//
//                                 if (installmentDate > currentTime) {
//                                     span.addClass('bg-danger text-danger').text('-');
//                                 } else {
//                                     span.addClass('bg-warning text-warning').text('-');
//                                 }
//                                 count++;
//                             });
//                         }
//                     }else if(loan_type==="Weekly"){
//
//                         var selected_date = new Date($('#installment_date_txt').val());
//                         var weekly_txt = $('#weekly_txt').val();
//
//                         console.log(selected_date,weekly_txt);
//
//                         // Check if it's the first day of the month
//                         if (selected_date.getDay() == weekly_txt) {
//
//                             let on_a_selected_date_txt=$('#installment_date_txt').val();
//
//                             if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
//                                 Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
//                             }else{
//                                 var installmentDates = [];
//                                 var currentDate = new Date(on_a_selected_date_txt);
//
//                                 // Function to format date as YYYY-MM-DD
//                                 function formatDate(date) {
//                                     var year = date.getFullYear();
//                                     var month = (date.getMonth() + 1).toString().padStart(2, '0');
//                                     var day = date.getDate().toString().padStart(2, '0');
//                                     return year + '-' + month + '-' + day;
//                                 }
//
//                                 // Loop through each installment count
//                                 for (var i = 0; i < installmentCount; i++) {
//                                     // Add one month to the current date
//                                     // Format the date
//
//                                     var formattedDate = formatDate(currentDate);
//                                     installmentDates.push(formattedDate);
//                                     currentDate.setDate(currentDate.getDate()+7)
//
//                                 }
//
//                                 // Get the table body
//                                 var tableBody = $('#installment_table tbody');
//
//                                 // Clear existing rows
//                                 tableBody.empty();
//                                 installmentAmount=installmentAmount.toFixed(2);
//                                 let count=1;
//                                 installmentDates.forEach(function(date) {
//                                     let currentDate = new Date(date);
//
//                                     currentDate.setDate(currentDate.getDate() + panelty_date_2);
//
//                                     let panelty_date = currentDate.toISOString().slice(0, 10);
//
//
//                                     var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+capital_amount+'</td><td class="text-end">'+interest_amount+'</td><td  class="text-end">' + panelty_date + '</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+installmentAmount+'</td><td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td></tr>';
//                                     tableBody.append(row);
//                                     var span = tableBody.children('tr:last-child').find('span');
//
//                                     var installmentDate = new Date(date);
//                                     var currentTime = new Date();
//
//                                     if (installmentDate > currentTime) {
//                                         span.addClass('bg-danger text-danger').text('-');
//                                     } else {
//                                         span.addClass('bg-warning text-warning').text('-');
//                                     }
//                                     count++;
//                                 });
//                             }
//                         } else {
//                             Swal.fire("Error!", "Selected date is not a equal to selected day !", "error")
//                         }
//
//
//
//                     }else if (loan_type==="First Of The Month"){
//                         var selected_date = new Date($('#installment_date_txt').val());
//
//                         // Check if it's the first day of the month
//                         if (selected_date.getDate() === 1) {
//
//                             let on_a_selected_date_txt=$('#installment_date_txt').val();
//
//                             if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
//                                 Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
//                             }else{
//                                 var installmentDates = [];
//                                 var currentDate = new Date(on_a_selected_date_txt);
//
//                                 // Function to format date as YYYY-MM-DD
//                                 function formatDate(date) {
//                                     var year = date.getFullYear();
//                                     var month = (date.getMonth() + 1).toString().padStart(2, '0');
//                                     var day = date.getDate().toString().padStart(2, '0');
//                                     return year + '-' + month + '-' + day;
//                                 }
//
//                                 // Loop through each installment count
//                                 for (var i = 0; i < installmentCount; i++) {
//                                     // Add one month to the current date
//                                     // Format the date
//
//                                     var formattedDate = formatDate(currentDate);
//                                     installmentDates.push(formattedDate);
//                                     currentDate.setMonth(currentDate.getMonth() + 1);
//                                 }
//
//                                 // Get the table body
//                                 var tableBody = $('#installment_table tbody');
//
//                                 // Clear existing rows
//                                 tableBody.empty();
//                                 installmentAmount=installmentAmount.toFixed(2);
//                                 let count=1;
//                                 installmentDates.forEach(function(date) {
//                                     let currentDate = new Date(date);
//
//                                     currentDate.setDate(currentDate.getDate() + panelty_date_2);
//
//                                     let panelty_date = currentDate.toISOString().slice(0, 10);
//
//
//                                     var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+capital_amount+'</td><td class="text-end">'+interest_amount+'</td><td  class="text-end">' + panelty_date + '</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+installmentAmount+'</td><td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td></tr>';
//                                     tableBody.append(row);
//                                     var span = tableBody.children('tr:last-child').find('span');
//
//                                     var installmentDate = new Date(date);
//                                     var currentTime = new Date();
//
//                                     if (installmentDate > currentTime) {
//                                         span.addClass('bg-danger text-danger').text('-');
//                                     } else {
//                                         span.addClass('bg-warning text-warning').text('-');
//                                     }
//                                     count++;
//                                 });
//                             }
//                         } else {
//                             Swal.fire("Error!", "Selected date is not the first day of the month !", "error")
//                         }
//                     }else if (loan_type==="End Of The Month"){
//                         var on_a_selected_date_txt = $('#installment_date_txt').val();
//                         var selected_date = new Date(on_a_selected_date_txt);
//
// // Get the last day of the month for the selected date
//                         var lastDayOfMonth = new Date(selected_date.getFullYear(), selected_date.getMonth() + 1, 0);
//
// // Check if it's the last day of the month
//                         if (selected_date.getDate() === lastDayOfMonth.getDate()) {
//                             if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
//                                 Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
//                             } else {
//                                 var installmentDates = [];
//                                 var currentDate = new Date(on_a_selected_date_txt);
//
//                                 // Function to format date as YYYY-MM-DD
//                                 function formatDate(date) {
//                                     var year = date.getFullYear();
//                                     var month = (date.getMonth() + 1).toString().padStart(2, '0');
//                                     var day = date.getDate().toString().padStart(2, '0');
//                                     return year + '-' + month + '-' + day;
//                                 }
//
//                                 // Loop through each installment count
//                                 for (var i = 0; i < installmentCount; i++) {
//
//                                     var selectedDate = new Date($('#installment_date_txt').val());
//
// // Get the last day of the selected month
//                                     var lastDayOfMonth = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + 1+i, 0);
//
//
//                                     // Get the last day of the current month
//                                     // var nextMonthDate = new Date(currentDate);
//                                     // nextMonthDate.setMonth(nextMonthDate.getMonth() + 1);
//                                     // nextMonthDate.setDate(0); // Set to last day of current month
//
//                                     // Format the date
//                                     var formattedDate = formatDate(lastDayOfMonth);
//                                     installmentDates.push(formattedDate);
//
//                                     // Move to the first day of the next month
//                                     // currentDate.setMonth(currentDate.getMonth() + 1);
//                                     // currentDate.setDate(1);
//                                 }
//
//                                 // Get the table body
//                                 var tableBody = $('#installment_table tbody');
//
//                                 // Clear existing rows
//                                 tableBody.empty();
//                                 installmentAmount = installmentAmount.toFixed(2);
//                                 let count=1;
//                                 installmentDates.forEach(function(date) {
//
//                                     let currentDate = new Date(date);
//
//                                     currentDate.setDate(currentDate.getDate() + panelty_date_2);
//
//                                     let panelty_date = currentDate.toISOString().slice(0, 10);
//
//                                     var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+capital_amount+'</td><td class="text-end">'+interest_amount+'</td><td class="text-end">'+panelty_date+'</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+installmentAmount+'</td><td class="text-center"><span class="px-1" style="border-radius: 10px; color: #000000;">-</span></td></tr>';
//
//                                     tableBody.append(row);
//
//                                     var span = tableBody.children('tr:last-child').find('span');
//
//                                     var installmentDate = new Date(date);
//                                     var currentTime = new Date();
//
//                                     if (installmentDate > currentTime) {
//                                         span.addClass('bg-danger text-danger').text('-');
//                                     } else {
//                                         span.addClass('bg-warning text-warning').text('-');
//                                     }
//
//                                     count++;
//                                 });
//                             }
//                         } else {
//                             Swal.fire("Error!", "Selected date is not the last day of the month !", "error")
//                         }
//
//
//                     }else if (loan_type === "Twice A Month") {
//                         let twice_a_month_txt = $('#twice_a_month_txt').val();
//                         let installmentDates = [];
//                         let selectedDate = new Date($('#installment_date_txt').val());
//                         let on_a_selected_date_txt = new Date($('#on_a_selected_date_txt').val());
//
//                         // Function to format date as YYYY-MM-DD
//                         function formatDate(date) {
//                             var year = date.getFullYear();
//                             var month = (date.getMonth() + 1).toString().padStart(2, '0');
//                             var day = date.getDate().toString().padStart(2, '0');
//                             return year + '-' + month + '-' + day;
//                         }
//
//                         // Ensure the first installment date is after or on the selected date
//                         if (selectedDate < on_a_selected_date_txt) {
//                             selectedDate = new Date(on_a_selected_date_txt);
//                         }
//                         let day = selectedDate.getDate();
//
//                         // Calculate the installment dates
//                         for (var i = 0; i < installmentCount; i++) {
//                             let firstInstallmentDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 15);
//                             let secondInstallmentDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i + 1, 0);
//
//                             if (twice_a_month_txt == "2") {
//                                 if (i === 0) {
//                                     // Check if the current day is 15, or after 15, or before 15
//                                     if (day === 15) {
//                                         installmentDates.push(formatDate(firstInstallmentDate));
//                                         installmentDates.push(formatDate(secondInstallmentDate));
//                                     } else if (day > 15 && day <= secondInstallmentDate.getDate()) {
//                                         installmentDates.push(formatDate(secondInstallmentDate));
//                                     } else {
//                                         installmentDates.push(formatDate(firstInstallmentDate));
//                                         installmentDates.push(formatDate(secondInstallmentDate));
//                                     }
//                                 } else {
//                                     // For subsequent installments
//                                     installmentDates.push(formatDate(firstInstallmentDate));
//                                     installmentDates.push(formatDate(secondInstallmentDate));
//                                 }
//                             } else {
//                                 let altFirstInstallmentDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 1);
//                                 let altSecondInstallmentDate = new Date(selectedDate.getFullYear(), selectedDate.getMonth() + i, 15);
//                                 if (i === 0) {
//                                     if (day === 1) {
//                                         installmentDates.push(formatDate(altFirstInstallmentDate));
//                                         installmentDates.push(formatDate(altSecondInstallmentDate));
//                                     } else if (day > 1 && day <= altSecondInstallmentDate.getDate()) {
//                                         installmentDates.push(formatDate(altSecondInstallmentDate));
//                                     }
//                                 } else {
//                                     installmentDates.push(formatDate(altFirstInstallmentDate));
//                                     installmentDates.push(formatDate(altSecondInstallmentDate));
//                                 }
//                             }
//                             if (installmentCount<=installmentDates.length){
//                                 if (installmentCount<installmentDates.length){
//                                     installmentDates.pop();
//                                 }
//                                 break;
//                             }
//                         }
//
//                         // Get the table body
//                         var tableBody = $('#installment_table tbody');
//
//                         // Clear existing rows
//                         tableBody.empty();
//                         installmentAmount = installmentAmount.toFixed(2);
//                         let count = 1;
//
//                         installmentDates.forEach(function (date) {
//                             let currentDate = new Date(date);
//
//                             currentDate.setDate(currentDate.getDate() + panelty_date_2);
//
//                             let panelty_date = currentDate.toISOString().slice(0, 10);
//                             var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">' + installmentAmount + '</td><td class="text-end">' + capital_amount + '</td><td class="text-end">' + interest_amount + '</td><td class="text-end">' + panelty_date + '</td><td class="text-end">0.00</td><td class="text-end">' + installmentAmount + '</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">' + installmentAmount + '</td><td class="text-end">' + installmentAmount + '</td><td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td></tr>';
//                             tableBody.append(row);
//
//                             var span = tableBody.children('tr:last-child').find('span');
//
//                             var installmentDate = new Date(date);
//                             var currentTime = new Date();
//
//                             if (installmentDate > currentTime) {
//                                 span.addClass('bg-danger text-danger').text('-');
//                             } else {
//                                 span.addClass('bg-warning text-warning').text('-');
//                             }
//                             count++;
//                         });
//                     }
//
//                     else if (loan_type==="On A Selected Date"){
//
//                         let on_a_selected_date_txt = $("#installment_date_txt").val();
//
//                         if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
//                             Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
//                         }else{
//                             var installmentDates = [];
//                             var currentDate = new Date(on_a_selected_date_txt);
//
//                             // Function to format date as YYYY-MM-DD
//                             function formatDate(date) {
//                                 var year = date.getFullYear();
//                                 var month = (date.getMonth() + 1).toString().padStart(2, '0');
//                                 var day = date.getDate().toString().padStart(2, '0');
//                                 return year + '-' + month + '-' + day;
//                             }
//
//                             // Loop through each installment count
//                             for (var i = 0; i < installmentCount; i++) {
//                                 // Add one month to the current date
//                                 // Format the date
//
//                                 var formattedDate = formatDate(currentDate);
//                                 installmentDates.push(formattedDate);
//                                 currentDate.setMonth(currentDate.getMonth() + 1);
//                             }
//
//                             // Get the table body
//                             var tableBody = $('#installment_table tbody');
//
//                             // Clear existing rows
//                             tableBody.empty();
//                             installmentAmount=installmentAmount.toFixed(2);
//                             let count=1;
//                             installmentDates.forEach(function(date) {
//
//                                 let currentDate = new Date(date);
//
//                                 currentDate.setDate(currentDate.getDate() + panelty_date_2);
//
//                                 let panelty_date = currentDate.toISOString().slice(0, 10);
//
//                                 var row = '<tr><td>' + count + '</td><td>' + date + '</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+capital_amount+'</td><td class="text-end">'+interest_amount+'</td><td class="text-end">'+panelty_date+'</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">0.00</td><td class="text-end">0.00</td><td class="text-end">'+installmentAmount+'</td><td class="text-end">'+installmentAmount+'</td><td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td></tr>';
//                                 tableBody.append(row);
//
//                                 var span = tableBody.children('tr:last-child').find('span');
//
//                                 var installmentDate = new Date(date);
//                                 var currentTime = new Date();
//
//                                 if (installmentDate > currentTime) {
//                                     span.addClass('bg-danger text-danger').text('-');
//                                 } else {
//                                     span.addClass('bg-warning text-warning').text('-');
//                                 }
//                                 count++;
//                             });
//                         }
//                     }
//                 }
            }
            $("#createLoanButton").removeClass("disabled").off("click.disable").on("click", function() {
                save_loan();
            });

        }


        @if($company->product_editable==1)
            function calculateInterest(){
                load_doc_charge();
                changeCategory();
                let interest_period = $("#interest_period").val();
                let interest_method = $("#interest_method").val();
                let duration_period = $("#duration_period").val();
                let penalty_period = $("#penalty_period").val();
                let loan_period = $("#loan_period").val();
                $("#interest_period_count").val(loan_period);
                let interest_period_count = $("#interest_period_count").val();

                let type = $("#repayment_type").val();
                let loan_amount = parseFloat($("#loan_amount").val());
                let interest = parseFloat($("#loan_interest").val());

                let interest_amount=(loan_amount*interest/100)*interest_period_count;
                if(interest_period=="Per Loan"){
                     interest_amount=(loan_amount*interest/100);
                }

                $("#interest_amount").val(interest_amount.toFixed(2));
                $("#total_loan_amount").text((parseFloat(loan_amount)+parseFloat(interest_amount)).toFixed(2));
                let total_loan_amount=(parseFloat(loan_amount)+parseFloat(interest_amount)).toFixed(2);
                console.log(total_loan_amount);
                let installment_amount=total_loan_amount/loan_period;
                console.log(interest_period);
                $("#new_interest_amount").text(installment_amount.toFixed(2));








                let total_loan_charge_2 = $("#total_loan_charge").text();
                let loan_amount_2 = parseFloat($("#loan_amount").val());
                let capital_amount_2=0.0;
                let loanChargesBalanceCheckbox_2 = document.getElementById("loanChargesBalance");
                if (loanChargesBalanceCheckbox_2.checked) {
                    loan_amount_2=loan_amount_2+total_loan_charge_2;
                }

                capital_amount_2=loan_amount_2.toFixed(2);
                interest_amount=interest_amount.toFixed(2);

                if (interest_method==="Draft"){
                    capital_amount_2=0.0;
                }

                $("#total_capital_amount").text(capital_amount_2);
                $("#total_interest_amount").text(interest_amount);
                saving_cal();

                checkAnotherCheckbox('separateCharges');
            }
        @else
            function calculateInterest(){
                load_doc_charge();
                let interest_period = $("#interest_period").val();
                let interest_method = $("#interest_method").val();
                let duration_period = $("#duration_period").val();
                let penalty_period = $("#penalty_period").val();
                let loan_period = $("#loan_period").val();
                let interest_period_count = $("#interest_period_count").val();

                let type = $("#repayment_type").val();
                let loan_amount = parseFloat($("#loan_amount").val());
                let interest = parseFloat($("#loan_interest").val());
                let interest_amount=0;
                if(interest_period==="Daily"){
                    if(duration_period==="Days"){
                        interest_amount=(loan_amount*interest/100)*interest_period_count;
                    }else if(duration_period==="Weeks"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count*7);
                    }else if(duration_period==="Months"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count*30);
                    }
                }else if(interest_period==="Weekly"){
                    if(duration_period==="Days"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count/7);
                    }else if(duration_period==="Weeks"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count);
                    }else if(duration_period==="Months"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count*4);
                    }
                }else if(interest_period==="Per Month"){
                    if(duration_period==="Days"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count/30);
                    }else if(duration_period==="Weeks"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count/4);
                    }else if(duration_period==="Months"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count);
                    }
                }else if(interest_period==="Per Year"){
                    if(duration_period==="Days"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count/365);
                    }else if(duration_period==="Weeks"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count/52);
                    }else if(duration_period==="Months"){
                        interest_amount=(loan_amount*interest/100)*(interest_period_count/12);
                    }
                }else if(interest_period==="Per Loan"){
                    interest_amount=(loan_amount*interest/100);
                }
                $("#interest_amount").val(interest_amount.toFixed(2));
                $("#total_loan_amount").text((parseFloat(loan_amount)+parseFloat(interest_amount)).toFixed(2));
                let total_loan_amount=(parseFloat(loan_amount)+parseFloat(interest_amount)).toFixed(2);

                if (interest_method==="Draft"){
                    total_loan_amount=parseFloat(interest_amount);
                }

                let installment_amount=total_loan_amount/loan_period;
                $("#new_interest_amount").text(installment_amount.toFixed(2));








                let total_loan_charge_2 = $("#total_loan_charge").text();
                let loan_amount_2 = parseFloat($("#loan_amount").val());
                let capital_amount_2=0.0;
                let loanChargesBalanceCheckbox_2 = document.getElementById("loanChargesBalance");
                if (loanChargesBalanceCheckbox_2.checked) {
                    loan_amount_2=loan_amount_2+total_loan_charge_2;
                }

                capital_amount_2=loan_amount_2.toFixed(2);
                interest_amount=interest_amount.toFixed(2);


                $("#total_capital_amount").text(capital_amount_2);
                $("#total_interest_amount").text(interest_amount);
                saving_cal();
                // if (type==="Weekly"){
                //     let loan_amount = parseFloat($("#loan_amount").val());
                //     let interest = parseFloat($("#loan_interest").val());
                //     let installment_count = parseFloat($("#loan_period").val());
                //     if (isNaN(loan_amount) || isNaN(interest) || isNaN(installment_count)) {
                //         return;
                //     }
                //     let new_installment_count=0.0;
                //     let interest_amount = ((loan_amount * interest) / 100);
                //     $("#interest_amount").val(interest_amount.toFixed(2));
                //     let total = loan_amount + interest_amount;
                //     $("#total_loan_amount").text(total.toFixed(2));
                //     new_installment_count=total/installment_count;
                //     $("#new_interest_amount").text(new_installment_count.toFixed(2));
                //
                // }else{
                //     let loan_amount = parseFloat($("#loan_amount").val());
                //     let interest = parseFloat($("#loan_interest").val());
                //     let total_loan_charge = parseFloat($("#total_loan_charge").text());
                //     let installment_count = parseFloat($("#ins_count").val());
                //
                //     if (isNaN(total_loan_charge)) {
                //         total_loan_charge=0.00;
                //     }
                //     let new_installment_count=0.0;
                //     if (!isNaN(loan_amount) && !isNaN(interest) && !isNaN(installment_count)) {
                //         let interest_amount = ((loan_amount * interest) / 100)*installment_count;
                //         $("#interest_amount").val(interest_amount.toFixed(2));
                //
                //         let total = loan_amount + interest_amount + total_loan_charge;
                //         $("#total_loan_amount").text(total.toFixed(2));
                //         new_installment_count=total/installment_count;
                //         $("#new_interest_amount").text(new_installment_count.toFixed(2));
                //     }
                // }
                checkAnotherCheckbox('separateCharges');
            }
        @endif


function saving_cal(){
    let saving_account_amount_type_3 = $("#saving_account_amount_type_3").text();
    console.log(saving_account_amount_type_3);
    if(saving_account_amount_type_3==="Percentage"){
        let loan_amount=$("#loan_amount").val();
        let percentage=$("#saving_account_amount_type_2").text();
        let amount=(loan_amount*percentage)/100;
        $("#saving_amount").text(amount.toFixed(2));
    }
}




        @if($company->product_editable==1)
            function change_loan_duration(){
                let value=$("#duration_period").val();
                if(value==="Days"){
                    $("#interest_period").val("Daily");
                }else if(value==="Weeks"){
                    $("#interest_period").val("Weekly");
                }else if(value==="Months"){
                    $("#interest_period").val("Per Month");
                }

                var repaymentType = document.getElementById('repayment_type');

                // Clear existing options
                repaymentType.innerHTML = '';

                if (value === 'Days') {
                    // Add only Daily option
                    var option = document.createElement('option');
                    option.value = 'Daily';
                    option.text = 'Daily';
                    repaymentType.add(option);
                } else  if(value === 'Weeks') {
                    var option = document.createElement('option');
                    option.value = 'Weekly';
                    option.text = 'Weekly';
                    repaymentType.add(option);
                }else{
                    // Add all other options
                    var options = [
                        { value: 'First Of The Month', text: 'First Of The Month' },
                        { value: 'End Of The Month', text: 'End Of The Month' },
                        { value: 'Twice A Month', text: 'Twice A Month' },
                        { value: 'On A Selected Date', text: 'On A Selected Date' }
                    ];

                    options.forEach(function(opt) {
                        var option = document.createElement('option');
                        option.value = opt.value;
                        option.text = opt.text;
                        repaymentType.add(option);
                    });
                }
                changeCategory();
            }
        @endif

        let lastChecked = '';

        function checkLoanChargesBalance() {
            const loanChargesBalanceCheckbox = document.getElementById("loanChargesBalance");
            const deductChargesCheckbox = document.getElementById("deductCharges");
            const separateChargesCheckbox = document.getElementById("separateCharges");
            updateIssuedAmount();
            if (loanChargesBalanceCheckbox.checked) {
                deductChargesCheckbox.checked = false;
                separateChargesCheckbox.checked = false;
                performCalculations('loanChargesBalance');
            } else {
                separateChargesCheckbox.checked = true;
                performReversal('loanChargesBalance');
            }
        }

        function checkAnotherCheckbox(checkboxId) {
            const loanChargesBalanceCheckbox = document.getElementById("loanChargesBalance");
            const deductChargesCheckbox = document.getElementById("deductCharges");
            const separateChargesCheckbox = document.getElementById("separateCharges");
            const checkbox = document.getElementById(checkboxId);
            updateIssuedAmount();
            if (checkbox.checked) {
                loanChargesBalanceCheckbox.checked = false;
                deductChargesCheckbox.checked = (checkboxId === 'deductCharges');
                separateChargesCheckbox.checked = (checkboxId === 'separateCharges');

                if (checkboxId === 'deductCharges') {
                    performReversal('loanChargesBalance');
                }
            } else {
                separateChargesCheckbox.checked = true;
                performReversal('loanChargesBalance');
            }


        }

        function performCalculations(checkboxId) {
            let total_loan_amount = parseFloat($("#total_loan_amount").text());
            let total_loan_charge = parseFloat($("#total_loan_charge").text());
            let total_capital_amount = parseFloat($("#total_capital_amount").text());
            let loan_period = parseInt($("#loan_period").val());

            let total_interest_amount = parseFloat($("#total_interest_amount").text());
            let interest_method = $("#interest_method").val();

            if (checkboxId === 'loanChargesBalance') {
                let tot = total_loan_amount + total_loan_charge;
                let tot_1 = total_loan_amount - total_loan_charge;
                if (interest_method==="Draft"){
                    tot=total_interest_amount;
                }
                let installment = tot / loan_period;

                $("#total_loan_amount").text(tot_1.toFixed(2));
                $("#new_interest_amount").text(installment.toFixed(2));

                let tot_total_capital_amount = total_capital_amount + total_loan_charge;
                $("#total_capital_amount").text(tot_total_capital_amount.toFixed(2));
            }

            lastChecked = checkboxId;
        }

        function performReversal(checkboxId) {

            let interest_method = $("#interest_method").val();
            if(interest_method!=="Reducing Balance"){
                if (lastChecked !== 'loanChargesBalance') return;

                let total_loan_amount = parseFloat($("#total_loan_amount").text());
                let total_loan_charge = parseFloat($("#total_loan_charge").text());
                let total_capital_amount = parseFloat($("#total_capital_amount").text());
                let loan_period = parseInt($("#loan_period").val());


                let total_interest_amount = parseFloat($("#total_interest_amount").text());
                let interest_method = $("#interest_method").val();

                if (checkboxId === 'loanChargesBalance') {
                    let tot = total_loan_amount - total_loan_charge;
                    let tot_1 = total_loan_amount - total_loan_charge;
                    if (interest_method==="Draft"){
                        tot=total_interest_amount;
                    }
                    let installment = tot / loan_period;




                    $("#total_loan_amount").text(tot_1.toFixed(2));
                    $("#new_interest_amount").text(installment.toFixed(2));

                    let tot_total_capital_amount = total_capital_amount - total_loan_charge;
                    $("#total_capital_amount").text(tot_total_capital_amount.toFixed(2));
                }

                lastChecked = '';
            }
        }

        function updateIssuedAmount() {
            // Get the values using jQuery .val()
            const totalLoanCharge = parseFloat($('#total_loan_charge').text()) || 0;
            let issuedAmount = parseFloat($('#loan_amount').val()) || 0;
            console.log(issuedAmount);
            console.log(totalLoanCharge);
            // Check if the relevant checkboxes are checked
            if ($('#loanChargesBalance').is(':checked')) {
                issuedAmount += totalLoanCharge;
            }
            if ($('#deductCharges').is(':checked')) {
                issuedAmount -= totalLoanCharge;
            }
            // If 'Loan Charges Separate from Loan' is checked, no adjustment needed

            // Update the issued amount in the DOM
            $('#new_issued_amount').text(issuedAmount.toFixed(2));
        }


        function toggleFields() {
            const interestMethodSelect = document.getElementById('interest_method').value; // Get the selected value

            if (interestMethodSelect === "Reducing Balance") {
                $("#normal_loan").hide();
                $("#reducingBalanceFields").show();
                $("#loan_period").prop('disabled', true); // Disables the field
            } else {
                $("#reducingBalanceFields").hide();
                $("#normal_loan").show();
                $("#loan_period").prop('disabled', false); // Disables the field
            }
        }

        function change_ins_values(){
            let installment_amount=$('#installment_amount').val();
            let months=$('#months').val();
            let maximum_loan=$('#maximum_loan').val();
            let offer_decided=$('#offer_decided').val();
            let given_interest_rate=$('#given_interest_rate').val();
            let loan_interest=$('#loan_interest').val();

            let repayment_type=$('#repayment_type').val();

            if(installment_amount=="" || installment_amount==0){
                $('#maximum_loan').val("0.00");
                $('#offer_decided').val("0.00");
                $('#given_interest_rate').val("0");
                $('#collection').val("0.00");
            }else{


                let ins_count=0;
                if (repayment_type==="Daily"){
                    ins_count=months*20;
                }else if (repayment_type==="Weekly"){
                    ins_count=months*4;
                }else if (repayment_type==="Twice A Month"){
                    ins_count=months*2;
                }else{
                    ins_count=months*1;
                }
                let collection=ins_count*parseFloat(installment_amount).toFixed(2);
                let interest_amount=0;
                interest_amount=collection/(ins_count*100+(loan_interest*100*months))*(loan_interest*100*months);

                let max_loan=collection-interest_amount;
                $('#maximum_loan').val(max_loan.toFixed(2));
                $('#loan_period').val(ins_count);
                $('#collection').val(collection.toFixed(2));


                let give_interest=(((collection-offer_decided)/offer_decided))/loan_interest*100;
                if(offer_decided>0){
                    $('#given_interest_rate').val(give_interest.toFixed(2));
                }


                if (max_loan < offer_decided) {
                    document.getElementById("offer_decided").style.color = "red";
                } else {
                    document.getElementById("offer_decided").style.color = ""; // Reset to default color
                }
                let total_interest_amount=collection-offer_decided;
                $('#total_capital_amount').text(parseFloat(offer_decided).toFixed(2));
                $('#total_interest_amount').text(parseFloat(total_interest_amount).toFixed(2));
                $('#interest_amount').val(parseFloat(total_interest_amount).toFixed(2));
                $('#total_loan_amount').text(parseFloat(collection).toFixed(2));
                $('#new_interest_amount').text(parseFloat(installment_amount).toFixed(2));
                $('#loan_amount').val(offer_decided);
                checkAnotherCheckbox('separateCharges');
                // $('#new_issued_amount').val();
            }
        }

    </script>
@endsection






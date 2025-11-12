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

                                    <div class="mb-3" id="customer_feild" hidden>
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

                                    <div class="mb-3" id="leasing_feild" hidden>
                                        <label for="simpleinput" class="form-label">Select Type</label>
                                        <select class="form-control"  id="lease_type" onchange="check_leasing(this.value)">
                                            <option id="0" selected>Cash</option>
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
                                                                <select class="form-select" id="interest_period" onchange="calculateInterest()">
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
                                                                    <input type="number" id="guarantee_count" class="form-control">
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
                                                            <select class="form-select" id="repayment_duration_period"  onchange="repayment_type(this.value)">
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
                                                            <select class="form-select" id="repayment_type" onchange="calculateInterest()" >
                                                                <option value="Daily">Daily</option>
                                                                <option value="Weekly">Weekly</option>
                                                                <option value="First Of The Month">First Of The Month</option>
                                                                <option value="End Of The Month">End Of The Month</option>
                                                                <option value="Twice A Month">Twice A Month</option>
                                                                <option value="On A Selected Date">On A Selected Date</option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="collection_date_type" class="form-label">Collection Date Type<span
                                                                        class="required-asterisk">*</span></label>
                                                            <select class="form-select" id="collection_date_type" disabled>
                                                                <option value="same_as_installment" selected>Same As Installment Due</option>
                                                                <option value="according_to_route">According to Route</option>
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
                                                            Loan Charge (On Loan Disbursement)
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
                                                            <div class="col-6 fw-bold"  style="color: red">Total Loan Charges (On Loan Disbursement)</div>
                                                            <div class="col-6 fw-bold text-left" id="total_loan_charge"  style="color: red">0.00</div>
                                                        </div>


                                                        <div class="row mb-3 section-break">
                                                            <div class="col-12">
                                                                <div class="section-title">
                                                                    Loan Charge (As First Installment)
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <div>
                                                                <div class="table-responsive-sm border border-1">
                                                                    <table class="table table-centered mb-0" id="first_installment_table">
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
                                                        <br><br>
                                                        <div class="row mb-3">
                                                            <div class="col-6 fw-bold" style="color: red">Total Loan Charges (As First Installment)</div>
                                                            <div class="col-6 fw-bold text-left" id="total_loan_charge_first_installment" style="color: red">0.00</div>
                                                        </div>

                                                        <!-- Loan Charges Balance checkbox -->
                                                        <div class="row mb-3">
                                                            <div class="col-12 fw-bold d-flex align-items-center">
                                                                <div class="col-6 fw-bold">Total Other Charges In Loan</div>
                                                                <div class="btn-group ms-2" role="group" aria-label="Checkbox group">
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
                                                        <div class="row mb-3" id="total_interest_amount_show">
                                                            <div class="col-6 fw-bold fw-size ">Interest Amount</div>
                                                            <div class="col-6 text-left fw-bold fw-size " id="total_interest_amount">0.00</div>
                                                        </div>

                                                        <div class="row mb-3"  id="total_loan_amount_show">
                                                            <div class="col-6 fw-bold fw-size ">Total Loan Amount</div>
                                                            <div class="col-6 text-left fw-bold fw-size " id="total_loan_amount">0.00</div>
                                                        </div>
                                                        <!-- Installment Amount -->
                                                        <div class="row mb-3"  id="new_interest_amount_show">
                                                            <div class="col-6 fw-bold fw-size ">Installment Amount</div>
                                                            <div class="col-6 text-left fw-bold fw-size " id="new_interest_amount">0.00</div>
                                                        </div>
                                                        <!-- Add more inputs here -->
                                                        <div class="row mb-3"  id="new_issued_amount_show">
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
                                            <div class="row mt-4 mb-4" id="Collection_section">
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
                                                            <option value="0">Sunday</option>
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
                                                            <option value="29">29</option>
                                                            <option value="30">30</option>
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

                                                <div class="mb-3" id="lending_officer_feild" hidden>
                                                    <label for="simpleinput" class="form-label">Select Broker</label>
                                                    <select class="form-control"  id="loan_broker">
                                                        @foreach($collector as $item)
                                                            <option value="{{$item->id}}">{{$item->Full_Name}}-{{$item->Designation}}-{{$item->TP}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3" id="lending_officer_feild" hidden>
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
    <script src="../JS/issueloan.js?n=16"></script>
    <script>
        var route_collection_type="";
        var route_collection_date="";
        var collection_date_type_global="";
        var canEditProductDetails = true; // Global flag for product details editing
        
        $(document).ready(function() {
            let x = ["#installment_amount","#offer_decided"];
            decimalFormat(x);

            fetchHolidays();
            
            // Check if product details can be edited
            $.get("/settings/all", function(settings) {
                if (settings.items.change_product_details === 'not_editable') {
                    canEditProductDetails = false;
                    
                    // Show informational message
                    $('.card-body h3:first').after(
                        '<div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">' +
                        '<i class="mdi mdi-alert-outline me-2"></i>' +
                        '<strong>Notice:</strong> Product details cannot be modified. You must use the default values from the selected loan product.' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                        '</div>'
                    );
                }
            });
            initCollectionDayOptions();
        });


        function initCollectionDayOptions() {
            $.ajax({
                type: "GET",
                url: "/settings/all",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data) {
                    const items = data.items || {};
                    let allowedDays = [];

                    if (items.collection_days) {
                        try {
                            allowedDays = JSON.parse(items.collection_days);
                            if (!Array.isArray(allowedDays)) {
                                allowedDays = [];
                            }
                        } catch (e) {
                            // fallback if stored comma-separated
                            allowedDays = String(items.collection_days)
                                .split(',')
                                .map(v => v.trim())
                                .filter(v => v !== '')
                                .map(v => parseInt(v));
                        }
                    }

                    // map day numbers to labels
                    const dayMap = {
                        1: 'Monday',
                        2: 'Tuesday',
                        3: 'Wednesday',
                        4: 'Thursday',
                        5: 'Friday',
                        6: 'Saturday',
                        0: 'Sunday'
                    };

                    // If admin removed everything by mistake, fallback to all 7
                    if (allowedDays.length === 0) {
                        allowedDays = [1,2,3,4,5,6,0];
                    }

                    // rebuild dropdown
                    const $select = $('#weekly_txt');
                    $select.empty();

                    allowedDays.forEach(function(dayVal, idx){
                        const label = dayMap[dayVal] ?? ('Day ' + dayVal);
                        $select.append(
                            $('<option>', {
                                value: dayVal,
                                text: label,
                                selected: idx === 0 // first one auto selected
                            })
                        );
                    });
                },
                error: function (xhr) {
                    console.error("Failed to load settings for collection days", xhr);
                    // fallback: do nothing, keep whatever HTML was there
                }
            });
        }


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
            $('#group').select2();
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


        function load_doc_charge() {
            let id = $("#package_details").val();

            $.ajax({
                type: "GET",
                url: "/loancategory/cost/" + id,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data, textStatus, xhr) {
                    if (xhr.status !== 200) return;

                    // Cache DOM
                    const $loanTblBody    = $('#loan_charge_table tbody');
                    const $firstTblBody   = $('#first_installment_table tbody'); // <-- ensure this table exists in your blade
                    const $loanTotalEl    = $('#total_loan_charge');
                    const $firstTotalEl   = $('#total_loan_charge_first_installment');

                    // Clear tables
                    $loanTblBody.empty();
                    $firstTblBody.empty();

                    // Totals
                    let totalLoanCharges = 0;
                    let totalFirstInst   = 0;

                    // Helpers
                    const n2 = (v) => (isNaN(v) ? '0.00' : Number(v).toFixed(2));
                    const readLoanAmount = () => {
                        const v = parseFloat($("#loan_amount").val());
                        return isNaN(v) ? null : v;
                    };

                    (data.other_charges || []).forEach(function (charge) {
                        let { Description, Amount, charge_type, deduction_type } = charge;

                        // base numeric amount
                        let numericAmount = parseFloat(Amount);
                        if (isNaN(numericAmount)) {
                            console.warn("Invalid charge amount for:", charge);
                            return; // skip
                        }

                        let displayType = charge_type;
                        let finalAmount = numericAmount;

                        // Handle percentage against loan amount
                        if (charge_type === "Percentage") {
                            const loanAmt = readLoanAmount();
                            if (loanAmt === null) {
                                console.warn("Invalid loan amount; percentage charge set to 0.00");
                                finalAmount = 0;
                            } else {
                                finalAmount = loanAmt * (numericAmount / 100);
                                displayType = `Percentage (${n2(numericAmount)}%)`;
                            }
                        }

                        const amountText = n2(finalAmount);

                        // Build row
                        const $row = $('<tr></tr>');
                        $row.append('<td>' + (Description ?? '') + '</td>');
                        $row.append('<td>' + displayType + '</td>');
                        $row.append('<td class="text-end">' + amountText + '</td>');

                        // Route to correct table by deduction_type
                        if (deduction_type === "On Loan Disbursement") {
                            $loanTblBody.append($row);
                            totalLoanCharges += finalAmount;
                        } else {
                            // default to "As First Installment"
                            $firstTblBody.append($row);
                            totalFirstInst += finalAmount;
                        }
                    });

                    // Update totals
                    $loanTotalEl.text(n2(totalLoanCharges));
                    $firstTotalEl.text(n2(totalFirstInst));
                },
                error: function (xhr, textStatus, errorThrown) {
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
                        $('#collection_date_type').val(product.collection_date_type || 'same_as_installment').trigger('change');
                        collection_date_type_global=product.collection_date_type;

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
                        
                        // Check if product details can be edited
                        if (!canEditProductDetails) {
                            // Disable ALL product-related input fields
                            $('#loan_amount').prop('disabled', true).prop('readonly', true);
                            $('#loan_interest').prop('disabled', true).prop('readonly', true);
                            $('#interest_period_count').prop('disabled', true).prop('readonly', true); // Loan Period
                            $('#loan_period').prop('disabled', true).prop('readonly', true); // Repayment Duration
                            $('#guarantee_count').prop('disabled', true).prop('readonly', true);
                            $('#penalty_percentage').prop('disabled', true).prop('readonly', true);
                            $('#penalty_date').prop('disabled', true).prop('readonly', true);
                            $('#installment_amount').prop('disabled', true).prop('readonly', true);
                            $('#offer_decided').prop('disabled', true).prop('readonly', true);
                            
                            // Disable ALL product-related select/dropdown fields
                            $('#interest_method').prop('disabled', true);
                            $('#interest_period').prop('disabled', true);
                            $('#duration_period').prop('disabled', true);
                            $('#repayment_duration_period').prop('disabled', true);
                            $('#repayment_type').prop('disabled', true);
                            $('#penalty_period').prop('disabled', true);
                            $('#months').prop('disabled', true);
                            $('#collection_date_type').prop('disabled', true);
                            
                            // Add visual styling to ALL disabled text inputs
                            $('#loan_amount, #loan_interest, #interest_period_count, #loan_period, #guarantee_count, #penalty_percentage, #penalty_date, #installment_amount, #offer_decided').css({
                                'background-color': '#f8f9fa',
                                'cursor': 'not-allowed',
                                'border': '1px solid #dee2e6'
                            });
                            
                            // Add visual styling to ALL disabled dropdowns
                            $('#interest_method, #interest_period, #duration_period, #repayment_duration_period, #repayment_type, #penalty_period, #months, #collection_date_type').css({
                                'background-color': '#f8f9fa',
                                'cursor': 'not-allowed',
                                'pointer-events': 'none'
                            });
                        }
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

            let customer_details=$('#customer_details').val();
            $.ajax({
                type: "GET",
                url: "/load_customer_route/" + customer_details,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(data) {
                    route_collection_type = data.collection_type;
                    const type = (data.collection_type || '').toLowerCase();
                    route_collection_date = (type === 'fixed') ? (data.collection_date || '') : '';

                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });

            $("#package_feild").slideDown();
            $("#leasing_feild").slideDown();
            $("#customer_bank_feild").slideDown();
            issue_request();
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
                            var uniqueId = 'docInput_' + Date.now() + '_' + index;

                            var row = $('<tr></tr>');

                            row.append('<td>' + document.Name + '</td>');

                            row.append(`
        <td>
            <input type="file" id="${uniqueId}" class="form-control file-upload" data-document-id="${document.idRequired_Documents}">
            <button type="button" class="btn btn-outline-secondary mt-1" onclick="openGlobalCamera('#${uniqueId}')">📷</button>
        </td>
    `);

                            row.append(`<td hidden><input type="checkbox" id="check${index + 1}" class="form-check-input" checked></td>`);

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
                    $('#address'+number).val(customer.Address_01+","+customer.Address_02+","+customer.Address_03);
                }
            }

        }


        function hideAllSchedules() {
            $("#weekly,#first_of_the_month,#end_of_the_month,#twice_a_month,#on_a_selected_date,#daily").hide();
        }

        function changeCategory(){
            // ===== Helpers defined at top (inside same function) =====
            function hideAllSchedules() {
                $("#weekly,#first_of_the_month,#end_of_the_month,#twice_a_month,#on_a_selected_date,#daily").hide();
            }
            const WEEKDAY = { Sunday:0, Monday:1, Tuesday:2, Wednesday:3, Thursday:4, Friday:5, Saturday:6 };
            const NTH_MAP = { first:1, second:2, third:3, fourth:4 };

            function parseISO(d){ return new Date(d); }
            function fmtISO(d){ return new Date(d.getTime() - d.getTimezoneOffset()*60000).toISOString().slice(0,10); }

            function addMonthsSafe(d, months){
                const nd = new Date(d);
                const day = nd.getDate();
                nd.setDate(1);
                nd.setMonth(nd.getMonth() + months);
                const last = new Date(nd.getFullYear(), nd.getMonth()+1, 0).getDate();
                nd.setDate(Math.min(day, last));
                return nd;
            }

            function getNthWeekday(year, month /*0-based*/, weekday /*0=Sun..6*/, nth /*1..4*/){
                const first = new Date(year, month, 1);
                const shift = (weekday - first.getDay() + 7) % 7;
                const day = 1 + shift + (nth - 1) * 7;
                return new Date(year, month, day);
            }

            // For "First Week Monday" etc: choose the nth weekday in the SAME month.
            // If that date is > dueDate, pick the PREVIOUS month's nth weekday.
            function nthWeekCollectionOnOrBeforeDue(dueDate, nthWord, weekdayName){
                const nth = NTH_MAP[(nthWord || '').toLowerCase()] || 1;
                const wd  = WEEKDAY[weekdayName];
                const y   = dueDate.getFullYear();
                const m   = dueDate.getMonth();
                let candidate = getNthWeekday(y, m, wd, nth);
                if (candidate > dueDate) {
                    // previous month
                    const pm = (m - 1 + 12) % 12;
                    const py = y - (m === 0 ? 1 : 0);
                    candidate = getNthWeekday(py, pm, wd, nth);
                }
                return candidate;
            }

            // For plain weekday ("Monday" etc): pick the last occurrence on/before dueDate
            function weekdayOnOrBefore(dueDate, weekdayName){
                const wd = WEEKDAY[weekdayName];
                const diff = (dueDate.getDay() - wd + 7) % 7; // 0..6 days to go back
                const d = new Date(dueDate);
                d.setDate(d.getDate() - diff);
                return d;
            }

            function computeCollectionOnOrBeforeDue(dueISO, routeTxt){
                const due = parseISO(dueISO);
                const txt = String(routeTxt || '').trim();

                // Plain weekday
                if (WEEKDAY.hasOwnProperty(txt)) {
                    return fmtISO(weekdayOnOrBefore(due, txt));
                }

                // "Second Week Monday" etc
                const parts = txt.split(/\s+/); // e.g., ["Second","Week","Monday"]
                if (parts.length >= 3) {
                    const nthWord = parts[0];
                    const weekdayName = parts[2];
                    if (WEEKDAY.hasOwnProperty(weekdayName)) {
                        const d = nthWeekCollectionOnOrBeforeDue(due, nthWord, weekdayName);
                        return fmtISO(d);
                    }
                }

                // Fallback: due date itself
                return fmtISO(due);
            }

            // ===== Your existing code starts here =====
            let Collection_Type      = $('#repayment_type').val();
            let penalty_date         = $('#penalty_date').val();
            let collection_date_type = collection_date_type_global;

            $("#load_div").slideDown();
            $('#panelty_date').text("Installment Date + " + penalty_date + " Days");
            $('#panelty_date_2').text(penalty_date);

            // 🔒 route-based schedule? hide EVERYTHING including the section and show summary
            const isRouteLocked =
                (String(route_collection_type || '').toLowerCase() === 'fixed') &&
                (collection_date_type === 'according_to_route');

            if (isRouteLocked) {
                hideAllSchedules();
                $("#Collection_section").hide();
                $('#repayment_type').prop('disabled', true);

                // === Compute and show the 3 values ===
                let issue_date = $('#issue_date').val(); // expected YYYY-MM-DD
                if (!issue_date || isNaN(parseISO(issue_date))) {
                    // If missing/invalid, just ensure any old summary is removed and bail (or you can alert)
                    $('#RouteLockedSummary').remove();
                } else {
                    // First Due Date = issue_date + 1 month
                    const firstDue = addMonthsSafe(parseISO(issue_date), 1);
                    const firstDueISO = fmtISO(firstDue);

                    // First Collection Date based on route_collection_date (on/before due)
                    const firstCollectionISO = computeCollectionOnOrBeforeDue(firstDueISO, route_collection_date);

                    // Difference (days); label earlier/later/same
                    const dueDateObj = parseISO(firstDueISO);
                    const colDateObj = parseISO(firstCollectionISO);
                    const diffMs = dueDateObj - colDateObj;
                    const diffDays = Math.round(diffMs / (24*60*60*1000));
                    const diffLabel = (diffDays === 0)
                        ? 'same day'
                        : (diffDays > 0 ? `${diffDays} day(s) earlier` : `${Math.abs(diffDays)} day(s) later`);

                    // Render / update a small summary box right above the hidden section
                    if (!$('#RouteLockedSummary').length) {
                        $('#Collection_section').before(
                            `<div id="RouteLockedSummary" class="alert alert-info mt-2">
             <div><strong>First Due Date:</strong> <span id="first_due_date_txt"></span></div>
             <div><strong>First Collection Date:</strong> <span id="first_collection_date_txt"></span></div>
             <div><strong>Difference:</strong> <span id="due_collect_diff_txt"></span></div>
             <small class="text-muted">Repayments follow the customer’s route schedule${route_collection_date ? ' ('+route_collection_date+')' : ''}.</small>
           </div>`
                        );
                    }
                    $('#first_due_date_txt').text(firstDueISO);
                    $('#first_collection_date_txt').text(firstCollectionISO);
                    $('#due_collect_diff_txt').text(diffLabel);
                }

                if (!$('#routeLockMsg').length) {
                    $('#repayment_type').closest('.mb-3').append(
                        '<small id="routeLockMsg" class="text-info d-block mt-1">Repayments follow the customer’s route schedule'
                        + (route_collection_date ? ' (' + route_collection_date + ')' : '')
                        + '.</small>'
                    );
                }

                // clear table if you generate rows
                $('#installment_table tbody').empty();
                installment = [];
                return; // stop here so nothing else is shown
            } else {
                // unlock + SHOW the section again when not route-locked
                $('#repayment_type').prop('disabled', false);
                $('#routeLockMsg').remove();
                $('#RouteLockedSummary').remove(); // remove summary if present
                $("#Collection_section").show();
            }

            // --- your existing UI switching ---
            hideAllSchedules();
            if (Collection_Type === "Daily"){
                $("#daily").show();
            } else if (Collection_Type === "Weekly"){
                $("#weekly").show();
                $("#panelty_amount_txt").text("Penalty Rate Per Full Amount (%)");
                $("#interest_txt").text("Interest Rate Per Full Amount (%)");
            } else if (Collection_Type === "First Of The Month"){
                $("#first_of_the_month").show();
            } else if (Collection_Type === "End Of The Month"){
                $("#end_of_the_month").show();
            } else if (Collection_Type === "Twice A Month"){
                $("#twice_a_month").show();
                var currentDate = new Date(); currentDate.setDate(1);
                $("#installment_date_txt").val(currentDate.toISOString().split('T')[0]);
            } else if (Collection_Type === "On A Selected Date"){
                $("#on_a_selected_date").show();
                check_date(1);
            }

            // reset table on change
            $('#installment_table tbody').empty();
            installment = [];
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

            let interest_period = $("#interest_period").val();
            let duration_period = $("#duration_period").val();
            let interest_period_count = $("#interest_period_count").val();


            const loanAmountFrom = parseFloat($("#loan_amount_from").val());
            const loanAmountTo = parseFloat($("#loan_amount_to").val());




            /***** tiny helpers (safe to keep even if already defined) *****/
            function parseYMD(s){ if(!s) return new Date(''); const [y,m,d]=s.split('-').map(Number); return new Date(y,(m||1)-1,d||1); }
            function pad2(n){ return String(n).padStart(2,'0'); }
            function fmtISO(d){ return `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`; }
            function addMonthsKeepDOM(date, n){
                const d=new Date(date), dom=d.getDate(); d.setMonth(d.getMonth()+n,1);
                const end=new Date(d.getFullYear(), d.getMonth()+1, 0).getDate();
                d.setDate(Math.min(dom,end)); return d;
            }
            function diffDays(aISO,bISO){
                const a=parseYMD(aISO), b=parseYMD(bISO);
                if(isNaN(a)||isNaN(b)) return 0;
                const MS=24*60*60*1000; return Math.round((b-a)/MS);
            }

            /***** route-collection helpers *****/
            const WEEKDAY = { Sunday:0, Monday:1, Tuesday:2, Wednesday:3, Thursday:4, Friday:5, Saturday:6 };
            const NTH_MAP = { first:1, second:2, third:3, fourth:4 };
            function getNthWeekday(y,m,wd,n){ const f=new Date(y,m,1); const s=(wd-f.getDay()+7)%7; return new Date(y,m,1+s+(n-1)*7); }
            function nthWeekOnOrBefore(due, nthWord, wdName){
                const nth=NTH_MAP[(nthWord||'').toLowerCase()]||1, wd=WEEKDAY[wdName];
                const y=due.getFullYear(), m=due.getMonth(); let c=getNthWeekday(y,m,wd,nth);
                if(c>due){ const pm=(m+11)%12, py=y-(m===0?1:0); c=getNthWeekday(py,pm,wd,nth); }
                return fmtISO(c);
            }
            function weekdayOnOrBefore(due, wdName){ const wd=WEEKDAY[wdName]; const diff=(due.getDay()-wd+7)%7; const d=new Date(due); d.setDate(d.getDate()-diff); return fmtISO(d); }
            function collectionDateForRoute(dueISO, offsetDays=5){
                const txt=String(route_collection_date||'').trim();
                let due=parseYMD(dueISO); if(isNaN(due.getTime())) return dueISO;
                if(Number.isFinite(offsetDays)&&offsetDays!==0){ const d=new Date(due); d.setDate(d.getDate()+offsetDays); due=d; }
                if(WEEKDAY.hasOwnProperty(txt)) return weekdayOnOrBefore(due, txt);
                const parts=txt.split(/\s+/);
                if(parts.length>=3 && WEEKDAY.hasOwnProperty(parts[2])) return nthWeekOnOrBefore(due, parts[0], parts[2]);
                return fmtISO(due);
            }



            /***** MAIN BLOCK *****/
            if (route_collection_type === "fixed") {
                if (collection_date_type_global === "according_to_route") {

                    // ---- header with Collection Date + Difference ----
                    $('#installment_table thead').empty();
                    const theadContent = `
      <thead>
        <tr>
          <th>No</th>
          <th>Due Date</th>
          <th class="text-end">Installment Amount</th>
          <th class="text-end">Capital Amount</th>
          <th class="text-end">Interest Amount</th>
          <th class="text-end" style="width:100px;">Penalty Date</th>
          <th class="text-end">Penalty Amount</th>
          <th class="text-end">Saving Amount</th>
          <th class="text-end">Total Amount</th>
          <th class="text-end">Paid Amount</th>
          <th class="text-end">Penalty Balance</th>
          <th class="text-end">Installment Balance</th>
          <th class="text-end">Savings Balance</th>
          <th class="text-end">Total Balance</th>
          <th class="text-end">Collection Date</th>
          <th class="text-end">Difference</th>
          <th>Status</th>
        </tr>
      </thead>`;
                    $('#installment_table').prepend(theadContent);

                    // ---- inputs / dates ----
                    const firstDueISO = ($('#first_due_date_txt').text() || '').trim();
                    const firstDue = parseYMD(firstDueISO);
                    const instCount = parseInt(installmentCount, 10) || 0;
                    if (!firstDueISO || isNaN(firstDue.getTime())) { Swal.fire("Error!", "First Due Date is invalid.", "error"); return; }

                    // build monthly due dates
                    const installmentDates = [];
                    for (let i = 0; i < instCount; i++) installmentDates.push(fmtISO(addMonthsKeepDOM(firstDue, i)));

                    // ---- amounts (as in your code) ----
                    const loan_interest_from  = parseFloat($("#loan_interest_from").val()) || 0;
                    const interestRate = (typeof interest !== 'undefined' && !isNaN(parseFloat(interest))) ? parseFloat(interest) : loan_interest_from;

                    const duration_period       = $("#duration_period").val(); // Days | Weeks | Months
                    const interest_period_count = parseFloat($("#period_count").val()) || 0;

                    let interest_amt = 0;
                    if (duration_period === "Days")   interest_amt = (interestRate / 30) * interest_period_count;
                    if (duration_period === "Weeks")  interest_amt = (interestRate / 30) * 7 * interest_period_count;
                    if (duration_period === "Months") interest_amt =  interestRate *       interest_period_count;

                    const loanChargesBalanceCheckbox = document.getElementById("loanChargesBalance");
                    if (loanChargesBalanceCheckbox && loanChargesBalanceCheckbox.checked) {
                        loan_amount = (parseFloat(loan_amount)||0) + parseFloat(total_loan_charge || 0);
                    }

                    let tot_amount      = (parseFloat(total_loan_amount) || 0) - (parseFloat(loan_amount_ins) || 0);
                    let capital_amount  = (parseFloat(loan_amount) || 0) / installmentDates.length;
                    let interest_amount = tot_amount / installmentDates.length;
                    capital_amount  = parseFloat(capital_amount.toFixed(2));
                    interest_amount = parseFloat(interest_amount.toFixed(2));

                    let installmentAmount = parseFloat($("#new_interest_amount").text()) || 0;
                    let panelty_date_2    = parseFloat($("#panelty_date_2").text()) || 0;

                    if (interest_method === "Draft") {
                        installmentAmount = parseFloat(interest_amount);
                        capital_amount = 0;
                    }

                    // ---- render ----
                    const tableBody = $('#installment_table tbody');
                    tableBody.empty();

                    let count = 1;

                    // ========== ROW #1: OTHER CHARGES (if any) ==========
                    const otherCharge = parseFloat(total_loan_charge || loan_amount_ins || 0);
                    if (otherCharge > 0) {
                        const otherDue = firstDueISO; // or use grant date if you prefer
                        const penBase  = parseYMD(otherDue);
                        if (!isNaN(penBase.getTime())) penBase.setDate(penBase.getDate() + (Number.isFinite(panelty_date_2) ? panelty_date_2 : 0));
                        const penaltyDate  = isNaN(penBase) ? otherDue : fmtISO(penBase);
                        const collectionISO = collectionDateForRoute(otherDue);
                        const diff = diffDays(otherDue, collectionISO);

                        tableBody.append(`
        <tr>
          <td>${count}</td>
          <td>${otherDue}</td>
          <td class="text-end">${otherCharge.toFixed(2)}</td>
          <td class="text-end">${otherCharge.toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${penaltyDate}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${otherCharge.toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${otherCharge.toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${otherCharge.toFixed(2)}</td>
          <td class="text-end">${collectionISO}</td>
          <td class="text-end">${diff}</td>
          <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
        </tr>
      `);
                        count += 1;
                    }
                    // =====================================================

                    // ===== remaining rows =====
                    if (interest_method === "Reducing Balance") {
                        const total_interest_percent = parseFloat(interest) || 0;
                        const n = installmentDates.length;
                        const r = (total_interest_percent / 100) / n;  // same split style you had
                        let EMI = (parseFloat(loan_amount)||0) * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
                        EMI = isFinite(EMI) ? parseFloat(EMI.toFixed(2)) : 0;

                        let principal_balance = parseFloat(loan_amount)||0;

                        installmentDates.forEach((dueISO) => {
                            const penBase = parseYMD(dueISO);
                            if (isNaN(penBase.getTime())) return;
                            penBase.setDate(penBase.getDate() + (Number.isFinite(panelty_date_2) ? panelty_date_2 : 0));
                            const panelty_date = fmtISO(penBase);

                            let interest_amt_rb = parseFloat((principal_balance * r).toFixed(2));
                            let capital_amt_rb  = parseFloat((EMI - interest_amt_rb).toFixed(2));

                            // last row cleanup
                            const emittedInstallments = count - (otherCharge > 0 ? 2 : 1); // zero-based inside schedule
                            if (emittedInstallments + 1 === n) {
                                capital_amt_rb = parseFloat(principal_balance.toFixed(2));
                                EMI = parseFloat((capital_amt_rb + interest_amt_rb).toFixed(2));
                            }
                            principal_balance = parseFloat((principal_balance - capital_amt_rb).toFixed(2));

                            const collectionISO = collectionDateForRoute(dueISO);
                            const diff = diffDays(dueISO, collectionISO);

                            tableBody.append(
                                '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + dueISO + '</td>' +
                                '<td class="text-end">' + EMI.toFixed(2) + '</td>' +
                                '<td class="text-end">' + capital_amt_rb.toFixed(2) + '</td>' +
                                '<td class="text-end">' + interest_amt_rb.toFixed(2) + '</td>' +
                                '<td class="text-end">' + panelty_date + '</td>' +
                                '<td class="text-end">0.00</td>' +
                                '<td class="text-end">0.00</td>' +
                                '<td class="text-end">' + EMI.toFixed(2) + '</td>' +
                                '<td class="text-end">0.00</td>' +
                                '<td class="text-end">0.00</td>' +
                                '<td class="text-end">' + EMI.toFixed(2) + '</td>' +
                                '<td class="text-end">0.00</td>' +
                                '<td class="text-end">' + EMI.toFixed(2) + '</td>' +
                                '<td class="text-end">' + collectionISO + '</td>' +
                                '<td class="text-end">' + diff + '</td>' +
                                '<td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>' +
                                '</tr>'
                            );
                            count += 1;
                        });

                    } else {
                        // FLAT / DRAFT
                        const capEach  = parseFloat(capital_amount);
                        const intEach  = parseFloat(interest_amount);
                        installmentDates.forEach((dueISO) => {
                            const penBase = parseYMD(dueISO);
                            if (isNaN(penBase.getTime())) return;
                            penBase.setDate(penBase.getDate() + (Number.isFinite(panelty_date_2) ? panelty_date_2 : 0));
                            const panelty_date = fmtISO(penBase);

                            const instAmt = (interest_method === "Draft")
                                ? parseFloat(intEach)
                                : parseFloat( (isNaN(installmentAmount)||installmentAmount===0) ? (capEach + intEach) : installmentAmount );

                            const capAmt = (interest_method === "Draft") ? 0 : capEach;
                            const intAmt = (interest_method === "Draft") ? intEach : (instAmt - capAmt);

                            const collectionISO = collectionDateForRoute(dueISO);
                            const diff = diffDays(dueISO, collectionISO);

                            tableBody.append(
                                '<tr>' +
                                '<td>' + count + '</td>' +
                                '<td>' + dueISO + '</td>' +
                                '<td class="text-end">' + instAmt.toFixed(2) + '</td>' +
                                '<td class="text-end">' + capAmt.toFixed(2) + '</td>' +
                                '<td class="text-end">' + intAmt.toFixed(2) + '</td>' +
                                '<td class="text-end">' + panelty_date + '</td>' +
                                '<td class="text-end">0.00</td>' +
                                '<td class="text-end">0.00</td>' +
                                '<td class="text-end">' + instAmt.toFixed(2) + '</td>' +
                                '<td class="text-end">0.00</td>' +
                                '<td class="text-end">0.00</td>' +
                                '<td class="text-end">' + instAmt.toFixed(2) + '</td>' +
                                '<td class="text-end">0.00</td>' +
                                '<td class="text-end">' + instAmt.toFixed(2) + '</td>' +
                                '<td class="text-end">' + collectionISO + '</td>' +
                                '<td class="text-end">' + diff + '</td>' +
                                '<td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>' +
                                '</tr>'
                            );
                            count += 1;
                        });
                    }

                } else {
                    // your original fallback
                    load_ins_data();
                }
            } else {
                // your original fallback
                load_ins_data();
            }







            function load_ins_data(){
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


               if(interest_method === "Reducing Balance") {
                   let interest_amt = 0;

                   let interestRate = parseFloat(interest); // input interest value
                   let principal = parseFloat(loan_amount); // loan amount

                   if (interest_period === "Daily") {
                       if (duration_period === "Days") {
                           interest_amt = interestRate * interest_period_count;
                       } else if (duration_period === "Weeks") {
                           interest_amt = interestRate * interest_period_count * 7;
                       } else if (duration_period === "Months") {
                           interest_amt = interestRate * interest_period_count * 30;
                       }

                   } else if (interest_period === "Weekly") {
                       if (duration_period === "Days") {
                           interest_amt = (interestRate / 7) * interest_period_count;
                       } else if (duration_period === "Weeks") {
                           interest_amt = interestRate * interest_period_count;
                       } else if (duration_period === "Months") {
                           interest_amt = (interestRate / 7) * 30 * interest_period_count;
                       }

                   } else if (interest_period === "Per Month") {
                       if (duration_period === "Days") {
                           interest_amt = (interestRate / 30) * interest_period_count;
                       } else if (duration_period === "Weeks") {
                           interest_amt = (interestRate / 30) * 7 * interest_period_count;
                       } else if (duration_period === "Months") {
                           interest_amt = interestRate * interest_period_count;
                       }

                   } else if (interest_period === "Per Year") {
                       if (duration_period === "Days") {
                           interest_amt = (interestRate / 365) * interest_period_count;
                       } else if (duration_period === "Weeks") {
                           interest_amt = (interestRate / 365) * 7 * interest_period_count;
                       } else if (duration_period === "Months") {
                           interest_amt = (interestRate / 12) * interest_period_count;
                       }

                   } else if (interest_period === "Per Loan") {
                       interest_amt = (principal * interestRate / 100);
                   }

                   interest = parseFloat(interest_amt.toFixed(2)); // Final assignment


               }else{
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

                   let is_according_to_route=0;
                   // alert(route_collection_type);
                   // alert(route_collection_date);

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

                   let firstInstCharge = parseFloat($('#total_loan_charge_first_installment').text()) || 0;


                   if (loan_type === "Daily") {
                       const on_a_selected_date_txt = $('#installment_date_txt').val();

                       if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                           Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                           return;
                       }

                       // ---------- helpers ----------
                       function fmt(d) {
                           const y = d.getFullYear();
                           const m = String(d.getMonth() + 1).padStart(2, '0');
                           const day = String(d.getDate()).padStart(2, '0');
                           return `${y}-${m}-${day}`;
                       }

                       // normalize holidays to YYYY-MM-DD strings
                       holidays = (holidays || []).map(h => new Date(h).toISOString().split('T')[0]);

                       // ---------- build dates (skip holidays) ----------
                       const hasOC = Number(firstInstCharge) > 0;
                       const nInst = parseInt(installmentCount, 10) || 0;          // number of installment rows (excludes OC)
                       const needDates = nInst + (hasOC ? 1 : 0);                  // total rows we will render
                       const installmentDates = [];

                       let cur = new Date(on_a_selected_date_txt);
                       while (installmentDates.length < needDates) {
                           const dstr = fmt(cur);
                           if (!holidays.includes(dstr)) {
                               installmentDates.push(dstr);
                           }
                           cur.setDate(cur.getDate() + 1);
                       }

                       // ---------- table prep ----------
                       const tableBody = $('#installment_table tbody');
                       tableBody.empty();

                       let saving_amount_show = parseFloat(installmentAmount) + parseFloat(saving_amount_value);
                       installmentAmount = Number(installmentAmount).toFixed(2);

                       // RB math (follow your style)
                       const total_interest_percent = parseFloat(interest) || 0;
                       const r = (total_interest_percent / 100) / (nInst > 0 ? nInst : 1);
                       let EMI = (parseFloat(loan_amount) || 0) * r * Math.pow(1 + r, nInst) / (Math.pow(1 + r, nInst) - 1);
                       EMI = isFinite(EMI) ? parseFloat(EMI.toFixed(2)) : 0;
                       let principal_balance = parseFloat(loan_amount) || 0;

                       let count = 1;

                       // ---------- Row #1: Other Charges, same date as first installment date (no backdate) ----------
                       if (hasOC) {
                           const ocDate = installmentDates[0];
                           const pd = new Date(ocDate);
                           pd.setDate(pd.getDate() + (Number(panelty_date_2) || 0));
                           const panelty_date = pd.toISOString().slice(0, 10);

                           const ocRow = `
      <tr>
        <td>${count}</td>
        <td>${ocDate}</td>
        <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
        <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
        <td class="text-end">0.00</td>
        <td class="text-end">${panelty_date}</td>
        <td class="text-end">0.00</td>
        <td class="text-end">0.00</td>
        <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
        <td class="text-end">0.00</td>
        <td class="text-end">0.00</td>
        <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
        <td class="text-end">0.00</td>
        <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
        <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
      </tr>`;
                           tableBody.append(ocRow);

                           const spanOC = tableBody.children('tr:last-child').find('span');
                           (new Date(ocDate) > new Date())
                               ? spanOC.addClass('bg-danger text-danger').text('-')
                               : spanOC.addClass('bg-warning text-warning').text('-');

                           count += 1;
                           firstInstCharge = 0; // consume once
                       }

                       // ---------- Installment rows (start after OC date if present) ----------
                       const startIdx = hasOC ? 1 : 0; // skip the first date that OC used
                       installmentDates.slice(startIdx).forEach((date, idx) => {
                           const pd = new Date(date);
                           pd.setDate(pd.getDate() + (Number(panelty_date_2) || 0));
                           const panelty_date = pd.toISOString().slice(0, 10);

                           let row = "";

                           if (interest_method === "Reducing Balance") {
                               const interest_amt = parseFloat((principal_balance * r).toFixed(2));
                               let capital_amt = parseFloat((EMI - interest_amt).toFixed(2));

                               // last RB row cleanup: idx goes 0..(nInst-1)
                               if (idx === nInst - 1) {
                                   capital_amt = parseFloat(principal_balance.toFixed(2));
                                   EMI = parseFloat((capital_amt + interest_amt).toFixed(2));
                               }
                               principal_balance = parseFloat((principal_balance - capital_amt).toFixed(2));

                               const saving_show = EMI + parseFloat(saving_amount_value);

                               row = `
        <tr>
          <td>${count}</td>
          <td>${date}</td>
          <td class="text-end">${EMI.toFixed(2)}</td>
          <td class="text-end">${capital_amt.toFixed(2)}</td>
          <td class="text-end">${interest_amt.toFixed(2)}</td>
          <td class="text-end">${panelty_date}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
          <td class="text-end">${saving_show.toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${EMI.toFixed(2)}</td>
          <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
          <td class="text-end">${saving_show.toFixed(2)}</td>
          <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
        </tr>`;
                           } else {
                               // Flat / Draft
                               const instAmt = parseFloat(installmentAmount);
                               const capAmt  = parseFloat(capital_amount);
                               const intAmt  = parseFloat(interest_amount);
                               const saving_show = instAmt + parseFloat(saving_amount_value);

                               row = `
        <tr>
          <td>${count}</td>
          <td>${date}</td>
          <td class="text-end">${instAmt.toFixed(2)}</td>
          <td class="text-end">${capAmt.toFixed(2)}</td>
          <td class="text-end">${intAmt.toFixed(2)}</td>
          <td class="text-end">${panelty_date}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
          <td class="text-end">${saving_show.toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${instAmt.toFixed(2)}</td>
          <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
          <td class="text-end">${saving_show.toFixed(2)}</td>
          <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
        </tr>`;
                           }

                           tableBody.append(row);

                           const span = tableBody.children('tr:last-child').find('span');
                           (new Date(date) > new Date())
                               ? span.addClass('bg-danger text-danger').text('-')
                               : span.addClass('bg-warning text-warning').text('-');

                           count += 1; // exactly once per row
                       });
                   }else if (loan_type === "Weekly") {

                       const selected_date = new Date($('#installment_date_txt').val());
                       const weekly_txt = $('#weekly_txt').val();

                       if (selected_date.getDay() == weekly_txt) {

                           const on_a_selected_date_txt = $('#installment_date_txt').val();
                           if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                               Swal.fire("Error!", "Please enter a valid Collection Date!", "error");
                               return;
                           }

                           // ---------- Build weekly due dates ----------
                           const hasOC = Number(firstInstCharge) > 0;
                           const totalDates = (parseInt(installmentCount, 10) || 0) + (hasOC ? 1 : 0);

                           const installmentDates = [];
                           let cur = new Date(on_a_selected_date_txt);

                           function fmt(date) {
                               const y = date.getFullYear();
                               const m = String(date.getMonth() + 1).padStart(2, "0");
                               const d = String(date.getDate()).padStart(2, "0");
                               return `${y}-${m}-${d}`;
                           }

                           for (let i = 0; i < totalDates; i++) {
                               installmentDates.push(fmt(cur));
                               cur.setDate(cur.getDate() + 7);
                           }

                           // ---------- Table setup ----------
                           const tableBody = $('#installment_table tbody');
                           tableBody.empty();

                           let saving_amount_show = parseFloat(installmentAmount) + parseFloat(saving_amount_value);
                           installmentAmount = Number(installmentAmount).toFixed(2);

                           // Reducing Balance parameters (same as yours)
                           const total_interest_percent = parseFloat(interest) || 0;
                           const n = installmentCount; // number of installment rows (NOT counting OC)
                           const r = (total_interest_percent / 100) / (n > 0 ? n : 1);
                           let EMI = (parseFloat(loan_amount) || 0) * r * Math.pow(1 + r, n) / (Math.pow(1 + r, n) - 1);
                           EMI = isFinite(EMI) ? parseFloat(EMI.toFixed(2)) : 0;
                           let principal_balance = parseFloat(loan_amount) || 0;

                           let count = 1;

                           // ---------- Other Charges row (same date as first installment, no backdate) ----------
                           if (hasOC) {
                               const ocDate = installmentDates[0];
                               const pd = new Date(ocDate);
                               pd.setDate(pd.getDate() + (Number(panelty_date_2) || 0));
                               const panelty_date = pd.toISOString().slice(0, 10);

                               tableBody.append(`
        <tr>
          <td>${count}</td>
          <td>${ocDate}</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${panelty_date}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
        </tr>
      `);

                               const spanOC = tableBody.children('tr:last-child').find('span');
                               (new Date(ocDate) > new Date())
                                   ? spanOC.addClass('bg-danger text-danger').text('-')
                                   : spanOC.addClass('bg-warning text-warning').text('-');

                               count++;
                               firstInstCharge = 0; // consume once
                           }

                           // ---------- Installments (start after OC index if present) ----------
                           const startIdx = hasOC ? 1 : 0;
                           installmentDates.slice(startIdx).forEach((date, idx) => {
                               // penalty date
                               const pd = new Date(date);
                               pd.setDate(pd.getDate() + (Number(panelty_date_2) || 0));
                               const panelty_date = pd.toISOString().slice(0, 10);

                               let rowHTML = "";

                               if (interest_method === "Reducing Balance") {
                                   const interest_amt = parseFloat((principal_balance * r).toFixed(2));
                                   let capital_amt = parseFloat((EMI - interest_amt).toFixed(2));

                                   // last installment cleanup (idx counts 0..n-1)
                                   if (idx === n - 1) {
                                       capital_amt = parseFloat(principal_balance.toFixed(2));
                                       EMI = parseFloat((capital_amt + interest_amt).toFixed(2));
                                   }
                                   principal_balance = parseFloat((principal_balance - capital_amt).toFixed(2));

                                   const saving_show = EMI + parseFloat(saving_amount_value);

                                   rowHTML = `
          <tr>
            <td>${count}</td>
            <td>${date}</td>
            <td class="text-end">${EMI.toFixed(2)}</td>
            <td class="text-end">${capital_amt.toFixed(2)}</td>
            <td class="text-end">${interest_amt.toFixed(2)}</td>
            <td class="text-end">${panelty_date}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${EMI.toFixed(2)}</td>
            <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
          </tr>`;
                               } else {
                                   const instAmt = parseFloat(installmentAmount);
                                   const capAmt  = parseFloat(capital_amount);
                                   const intAmt  = parseFloat(interest_amount);
                                   const saving_show = instAmt + parseFloat(saving_amount_value);

                                   rowHTML = `
          <tr>
            <td>${count}</td>
            <td>${date}</td>
            <td class="text-end">${instAmt.toFixed(2)}</td>
            <td class="text-end">${capAmt.toFixed(2)}</td>
            <td class="text-end">${intAmt.toFixed(2)}</td>
            <td class="text-end">${panelty_date}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${instAmt.toFixed(2)}</td>
            <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
          </tr>`;
                               }

                               tableBody.append(rowHTML);

                               const span = tableBody.children('tr:last-child').find('span');
                               (new Date(date) > new Date())
                                   ? span.addClass('bg-danger text-danger').text('-')
                                   : span.addClass('bg-warning text-warning').text('-');

                               count++;
                           });

                       } else {
                           Swal.fire("Error!", "Selected date is not equal to selected weekday!", "error");
                       }
                   }else if (loan_type === "First Of The Month") {
                       const selected_date = new Date($('#installment_date_txt').val());

                       // must be the 1st of the month
                       if (selected_date.getDate() === 1) {
                           const on_a_selected_date_txt = $('#installment_date_txt').val();

                           if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                               Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                               return;
                           }

                           // --------- helpers ----------
                           function formatDate(date) {
                               const y = date.getFullYear();
                               const m = String(date.getMonth() + 1).padStart(2, '0');
                               const d = String(date.getDate()).padStart(2, '0');
                               return `${y}-${m}-${d}`;
                           }

                           // --------- build monthly dates (include OC slot if needed) ----------
                           const hasOC = Number(firstInstCharge) > 0;
                           const nInst = parseInt(installmentCount, 10) || 0;        // number of installment rows (excludes OC)
                           const needDates = nInst + (hasOC ? 1 : 0);                // total rows we will render

                           const installmentDates = [];
                           let currentDate = new Date(on_a_selected_date_txt);       // this is the 1st of a month
                           for (let i = 0; i < needDates; i++) {
                               // always push the 1st of month
                               currentDate.setDate(1);
                               installmentDates.push(formatDate(currentDate));
                               currentDate.setMonth(currentDate.getMonth() + 1);
                           }

                           // --------- table prep ----------
                           const tableBody = $('#installment_table tbody');
                           tableBody.empty();

                           let saving_amount_show = parseFloat(installmentAmount) + parseFloat(saving_amount_value);
                           installmentAmount = Number(installmentAmount).toFixed(2);

                           // Reducing Balance params (same style you use)
                           const total_interest_percent = parseFloat(interest) || 0;
                           const r = (total_interest_percent / 100) / (nInst > 0 ? nInst : 1);
                           let EMI = (parseFloat(loan_amount) || 0) * r * Math.pow(1 + r, nInst) / (Math.pow(1 + r, nInst) - 1);
                           EMI = isFinite(EMI) ? parseFloat(EMI.toFixed(2)) : 0;
                           let principal_balance = parseFloat(loan_amount) || 0;

                           let count = 1;

                           // --------- Row #1: Other Charges (same date as first installment; installments start next month) ----------
                           if (hasOC) {
                               const ocDate = installmentDates[0];
                               const pd = new Date(ocDate);
                               pd.setDate(pd.getDate() + (Number(panelty_date_2) || 0));
                               const panelty_date = pd.toISOString().slice(0, 10);

                               const ocRow = `
        <tr>
          <td>${count}</td>
          <td>${ocDate}</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${panelty_date}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
        </tr>`;
                               tableBody.append(ocRow);

                               const spanOC = tableBody.children('tr:last-child').find('span');
                               (new Date(ocDate) > new Date())
                                   ? spanOC.addClass('bg-danger text-danger').text('-')
                                   : spanOC.addClass('bg-warning text-warning').text('-');

                               count += 1;
                               firstInstCharge = 0; // consume it once
                           }

                           // --------- Installment rows (start after OC date if present) ----------
                           const startIdx = hasOC ? 1 : 0; // skip the first date used by OC
                           installmentDates.slice(startIdx).forEach((date, idx) => {
                               const pd = new Date(date);
                               pd.setDate(pd.getDate() + (Number(panelty_date_2) || 0));
                               const panelty_date = pd.toISOString().slice(0, 10);

                               let rowHTML = "";

                               if (interest_method === "Reducing Balance") {
                                   const interest_amt = parseFloat((principal_balance * r).toFixed(2));
                                   let capital_amt = parseFloat((EMI - interest_amt).toFixed(2));

                                   // last installment cleanup: idx is 0..(nInst-1)
                                   if (idx === nInst - 1) {
                                       capital_amt = parseFloat(principal_balance.toFixed(2));
                                       EMI = parseFloat((capital_amt + interest_amt).toFixed(2));
                                   }
                                   principal_balance = parseFloat((principal_balance - capital_amt).toFixed(2));

                                   const saving_show = EMI + parseFloat(saving_amount_value);

                                   rowHTML = `
          <tr>
            <td>${count}</td>
            <td>${date}</td>
            <td class="text-end">${EMI.toFixed(2)}</td>
            <td class="text-end">${capital_amt.toFixed(2)}</td>
            <td class="text-end">${interest_amt.toFixed(2)}</td>
            <td class="text-end">${panelty_date}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${EMI.toFixed(2)}</td>
            <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
          </tr>`;
                               } else {
                                   // Flat / Draft
                                   const instAmt = parseFloat(installmentAmount);
                                   const capAmt  = parseFloat(capital_amount);
                                   const intAmt  = parseFloat(interest_amount);
                                   const saving_show = instAmt + parseFloat(saving_amount_value);

                                   rowHTML = `
          <tr>
            <td>${count}</td>
            <td>${date}</td>
            <td class="text-end">${instAmt.toFixed(2)}</td>
            <td class="text-end">${capAmt.toFixed(2)}</td>
            <td class="text-end">${intAmt.toFixed(2)}</td>
            <td class="text-end">${panelty_date}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${instAmt.toFixed(2)}</td>
            <td class="text-end">${parseFloat(saving_amount_value).toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
          </tr>`;
                               }

                               tableBody.append(rowHTML);

                               const span = tableBody.children('tr:last-child').find('span');
                               (new Date(date) > new Date())
                                   ? span.addClass('bg-danger text-danger').text('-')
                                   : span.addClass('bg-warning text-warning').text('-');

                               count += 1; // exactly once per row
                           });

                       } else {
                           Swal.fire("Error!", "Selected date is not the first day of the month !", "error");
                       }
                   }else if (loan_type === "End Of The Month") {
                       var on_a_selected_date_txt = $('#installment_date_txt').val();
                       var selected_date = new Date(on_a_selected_date_txt);

                       // last day of selected month?
                       var lastDayOfMonth = new Date(selected_date.getFullYear(), selected_date.getMonth() + 1, 0);
                       if (selected_date.getDate() === lastDayOfMonth.getDate()) {

                           if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                               Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                               return;
                           }

                           // ---------- helpers ----------
                           function formatDate(date) {
                               var y = date.getFullYear();
                               var m = (date.getMonth() + 1).toString().padStart(2, '0');
                               var d = date.getDate().toString().padStart(2, '0');
                               return y + '-' + m + '-' + d;
                           }
                           function lastDayOf(y, m /* 0-based */) {
                               return new Date(y, m + 1, 0);
                           }

                           // ---------- build month-end dates ----------
                           var hasOC = Number(firstInstCharge) > 0;
                           var nInst = parseInt(installmentCount, 10) || 0;          // installments count (excluding OC)
                           var needDates = nInst + (hasOC ? 1 : 0);                  // total rows to render

                           var base = new Date(on_a_selected_date_txt);              // this is a month-end date
                           var y0 = base.getFullYear(), m0 = base.getMonth();

                           var installmentDates = [];
                           for (var i = 0; i < needDates; i++) {
                               var d = lastDayOf(y0, m0 + i);                          // month-end for base + i months
                               installmentDates.push(formatDate(d));
                           }

                           // ---------- table prep ----------
                           var tableBody = $('#installment_table tbody');
                           tableBody.empty();

                           var saving_amount_show = parseFloat(installmentAmount) + parseFloat(saving_amount_value);
                           installmentAmount = Number(installmentAmount).toFixed(2);

                           // Reducing Balance (same style as your code)
                           var total_interest_percent = parseFloat(interest) || 0;
                           var r = (total_interest_percent / 100) / (nInst > 0 ? nInst : 1);
                           var EMI = (parseFloat(loan_amount) || 0) * r * Math.pow(1 + r, nInst) / (Math.pow(1 + r, nInst) - 1);
                           EMI = isFinite(EMI) ? parseFloat(EMI.toFixed(2)) : 0;
                           var principal_balance = parseFloat(loan_amount) || 0;

                           var count = 1;

                           // ---------- Row #1: Other Charges (same date as first month-end; installments start next month-end) ----------
                           if (hasOC) {
                               var ocDate = installmentDates[0];
                               var pdOC = new Date(ocDate);
                               pdOC.setDate(pdOC.getDate() + (Number(panelty_date_2) || 0));
                               var panelty_date_oc = pdOC.toISOString().slice(0, 10);

                               var ocRow = ''
                                   + '<tr>'
                                   +   '<td>' + count + '</td>'
                                   +   '<td>' + ocDate + '</td>'
                                   +   '<td class="text-end">' + Number(firstInstCharge).toFixed(2) + '</td>'
                                   +   '<td class="text-end">' + Number(firstInstCharge).toFixed(2) + '</td>'
                                   +   '<td class="text-end">0.00</td>'
                                   +   '<td class="text-end">' + panelty_date_oc + '</td>'
                                   +   '<td class="text-end">0.00</td>'
                                   +   '<td class="text-end">0.00</td>'
                                   +   '<td class="text-end">' + Number(firstInstCharge).toFixed(2) + '</td>'
                                   +   '<td class="text-end">0.00</td>'
                                   +   '<td class="text-end">0.00</td>'
                                   +   '<td class="text-end">' + Number(firstInstCharge).toFixed(2) + '</td>'
                                   +   '<td class="text-end">0.00</td>'
                                   +   '<td class="text-end">' + Number(firstInstCharge).toFixed(2) + '</td>'
                                   +   '<td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>'
                                   + '</tr>';

                               tableBody.append(ocRow);

                               var spanOC = tableBody.children('tr:last-child').find('span');
                               (new Date(ocDate) > new Date())
                                   ? spanOC.addClass('bg-danger text-danger').text('-')
                                   : spanOC.addClass('bg-warning text-warning').text('-');

                               count += 1;
                               firstInstCharge = 0; // consume once
                           }

                           // ---------- Installment rows (start after OC date if present) ----------
                           var startIdx = hasOC ? 1 : 0;
                           installmentDates.slice(startIdx).forEach(function(date, idx) {
                               var pd = new Date(date);
                               pd.setDate(pd.getDate() + (Number(panelty_date_2) || 0));
                               var panelty_date = pd.toISOString().slice(0, 10);

                               var rowHTML = '';

                               if (interest_method === "Reducing Balance") {
                                   var interest_amt = parseFloat((principal_balance * r).toFixed(2));
                                   var capital_amt  = parseFloat((EMI - interest_amt).toFixed(2));

                                   // last installment cleanup: idx is 0..(nInst-1)
                                   if (idx === nInst - 1) {
                                       capital_amt = parseFloat(principal_balance.toFixed(2));
                                       EMI = parseFloat((capital_amt + interest_amt).toFixed(2));
                                   }
                                   principal_balance = parseFloat((principal_balance - capital_amt).toFixed(2));

                                   var saving_show = EMI + parseFloat(saving_amount_value);

                                   rowHTML = ''
                                       + '<tr>'
                                       +   '<td>' + count + '</td>'
                                       +   '<td>' + date + '</td>'
                                       +   '<td class="text-end">' + EMI.toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + capital_amt.toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + interest_amt.toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + panelty_date + '</td>'
                                       +   '<td class="text-end">0.00</td>'
                                       +   '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + saving_show.toFixed(2) + '</td>'
                                       +   '<td class="text-end">0.00</td>'
                                       +   '<td class="text-end">0.00</td>'
                                       +   '<td class="text-end">' + EMI.toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + saving_show.toFixed(2) + '</td>'
                                       +   '<td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>'
                                       + '</tr>';

                               } else {
                                   // Flat / Draft
                                   var instAmt = parseFloat(installmentAmount);
                                   var capAmt  = parseFloat(capital_amount);
                                   var intAmt  = parseFloat(interest_amount);
                                   var saving_show = instAmt + parseFloat(saving_amount_value);

                                   rowHTML = ''
                                       + '<tr>'
                                       +   '<td>' + count + '</td>'
                                       +   '<td>' + date + '</td>'
                                       +   '<td class="text-end">' + instAmt.toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + capAmt.toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + intAmt.toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + panelty_date + '</td>'
                                       +   '<td class="text-end">0.00</td>'
                                       +   '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + saving_show.toFixed(2) + '</td>'
                                       +   '<td class="text-end">0.00</td>'
                                       +   '<td class="text-end">0.00</td>'
                                       +   '<td class="text-end">' + instAmt.toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + parseFloat(saving_amount_value).toFixed(2) + '</td>'
                                       +   '<td class="text-end">' + saving_show.toFixed(2) + '</td>'
                                       +   '<td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>'
                                       + '</tr>';
                               }

                               tableBody.append(rowHTML);

                               var span = tableBody.children('tr:last-child').find('span');
                               (new Date(date) > new Date())
                                   ? span.addClass('bg-danger text-danger').text('-')
                                   : span.addClass('bg-warning text-warning').text('-');

                               count += 1; // exactly once per row
                           });

                       } else {
                           Swal.fire("Error!", "Selected date is not the last day of the month !", "error");
                       }
                   } else if (loan_type === "Twice A Month") {
                       let twice_a_month_txt = $('#twice_a_month_txt').val();

                       // ---- helpers ----
                       function formatDate(date) {
                           var y = date.getFullYear();
                           var m = String(date.getMonth() + 1).padStart(2, '0');
                           var d = String(date.getDate()).padStart(2, '0');
                           return y + '-' + m + '-' + d;
                       }
                       function lastDayOf(y, m /* 0-based */) {
                           return new Date(y, m + 1, 0);
                       }

                       const on_a_selected_date_txt = $('#installment_date_txt').val();
                       const selectedDate = new Date(on_a_selected_date_txt);
                       if (on_a_selected_date_txt.trim() === "" || isNaN(selectedDate)) {
                           Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                           return;
                       }

                       // ---- config & dates to generate ----
                       const hasOC = Number(firstInstCharge) > 0;         // OC row?
                       const nInst = parseInt(installmentCount, 10) || 0; // installments count (excluding OC)
                       const needDates = nInst + (hasOC ? 1 : 0);         // total rows we will render

                       const y0 = selectedDate.getFullYear();
                       const m0 = selectedDate.getMonth();
                       const installmentDates = [];

                       if (twice_a_month_txt == "2") {
                           // 15th then month-end (for each month)
                           for (let i = 0; installmentDates.length < needDates; i++) {
                               const fifteenth = new Date(y0, m0 + i, 15);
                               installmentDates.push(formatDate(fifteenth));
                               if (installmentDates.length >= needDates) break;
                               const monthEnd = lastDayOf(y0, m0 + i);
                               installmentDates.push(formatDate(monthEnd));
                           }
                       } else {
                           // 1st then 15th (for each month)
                           for (let i = 0; installmentDates.length < needDates; i++) {
                               const first = new Date(y0, m0 + i, 1);
                               installmentDates.push(formatDate(first));
                               if (installmentDates.length >= needDates) break;
                               const fifteenth = new Date(y0, m0 + i, 15);
                               installmentDates.push(formatDate(fifteenth));
                           }
                       }

                       // ---- table prep ----
                       const tableBody = $('#installment_table tbody');
                       tableBody.empty();

                       // EMI / RB params (OC not counted)
                       const total_interest_percent = parseFloat(interest) || 0;
                       const r = (total_interest_percent / 100) / (nInst > 0 ? nInst : 1);
                       let EMI = (parseFloat(loan_amount) || 0) * r * Math.pow(1 + r, nInst) / (Math.pow(1 + r, nInst) - 1);
                       EMI = isFinite(EMI) ? parseFloat(EMI.toFixed(2)) : 0;
                       let principal_balance = parseFloat(loan_amount) || 0;

                       // ensure numeric strings become numbers
                       const capEach  = parseFloat(capital_amount);
                       const intEach  = parseFloat(interest_amount);
                       const instAmtFlat = parseFloat(installmentAmount);
                       const savingVal = parseFloat(saving_amount_value);

                       let count = 1;

                       // ---- Row #1: Other Charges (on the first generated date), once ----
                       if (hasOC) {
                           const ocDate = installmentDates[0];
                           const pdOC = new Date(ocDate);
                           pdOC.setDate(pdOC.getDate() + (Number(panelty_date_2) || 0));
                           const panelty_date_oc = pdOC.toISOString().slice(0, 10);

                           const ocRow =
                               '<tr>' +
                               '<td>' + count + '</td>' +
                               '<td>' + ocDate + '</td>' +
                               '<td class="text-end">' + Number(firstInstCharge).toFixed(2) + '</td>' +
                               '<td class="text-end">' + Number(firstInstCharge).toFixed(2) + '</td>' +
                               '<td class="text-end">0.00</td>' +
                               '<td class="text-end">' + panelty_date_oc + '</td>' +
                               '<td class="text-end">0.00</td>' +
                               '<td class="text-end">0.00</td>' +
                               '<td class="text-end">' + Number(firstInstCharge).toFixed(2) + '</td>' +
                               '<td class="text-end">0.00</td>' +
                               '<td class="text-end">0.00</td>' +
                               '<td class="text-end">' + Number(firstInstCharge).toFixed(2) + '</td>' +
                               '<td class="text-end">0.00</td>' +
                               '<td class="text-end">' + Number(firstInstCharge).toFixed(2) + '</td>' +
                               '<td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>' +
                               '</tr>';

                           tableBody.append(ocRow);

                           // status marker
                           const spanOC = tableBody.children('tr:last-child').find('span');
                           (new Date(ocDate) > new Date())
                               ? spanOC.addClass('bg-danger text-danger').text('-')
                               : spanOC.addClass('bg-warning text-warning').text('-');

                           count += 1;
                           firstInstCharge = 0; // consume once
                       }

                       // ---- Installment rows (start after OC date if present) ----
                       const startIdx = hasOC ? 1 : 0;

                       installmentDates.slice(startIdx).forEach(function(date, idx) {
                           const pd = new Date(date);
                           pd.setDate(pd.getDate() + (Number(panelty_date_2) || 0));
                           const panelty_date = pd.toISOString().slice(0, 10);

                           let rowHTML = '';

                           if (interest_method === "Reducing Balance") {
                               const interest_amt = parseFloat((principal_balance * r).toFixed(2));
                               let capital_amt  = parseFloat((EMI - interest_amt).toFixed(2));

                               // last installment cleanup (idx: 0..nInst-1)
                               if (idx === nInst - 1) {
                                   capital_amt = parseFloat(principal_balance.toFixed(2));
                                   EMI = parseFloat((capital_amt + interest_amt).toFixed(2));
                               }
                               principal_balance = parseFloat((principal_balance - capital_amt).toFixed(2));

                               const saving_show = EMI + savingVal;

                               rowHTML =
                                   '<tr>' +
                                   '<td>' + count + '</td>' +
                                   '<td>' + date + '</td>' +
                                   '<td class="text-end">' + EMI.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + capital_amt.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + interest_amt.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + panelty_date + '</td>' +
                                   '<td class="text-end">0.00</td>' +
                                   '<td class="text-end">' + savingVal.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + saving_show.toFixed(2) + '</td>' +
                                   '<td class="text-end">0.00</td>' +
                                   '<td class="text-end">0.00</td>' +
                                   '<td class="text-end">' + EMI.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + savingVal.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + saving_show.toFixed(2) + '</td>' +
                                   '<td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>' +
                                   '</tr>';

                           } else {
                               // Flat / Draft
                               const instAmt = instAmtFlat;
                               const capAmt  = isNaN(capEach) ? 0 : capEach;
                               const intAmt  = isNaN(intEach) ? 0 : intEach;
                               const saving_show = instAmt + savingVal;

                               rowHTML =
                                   '<tr>' +
                                   '<td>' + count + '</td>' +
                                   '<td>' + date + '</td>' +
                                   '<td class="text-end">' + instAmt.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + capAmt.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + intAmt.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + panelty_date + '</td>' +
                                   '<td class="text-end">0.00</td>' +
                                   '<td class="text-end">' + savingVal.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + (saving_show).toFixed(2) + '</td>' +
                                   '<td class="text-end">0.00</td>' +
                                   '<td class="text-end">0.00</td>' +
                                   '<td class="text-end">' + instAmt.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + savingVal.toFixed(2) + '</td>' +
                                   '<td class="text-end">' + (saving_show).toFixed(2) + '</td>' +
                                   '<td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>' +
                                   '</tr>';
                           }

                           tableBody.append(rowHTML);

                           // status marker
                           const span = tableBody.children('tr:last-child').find('span');
                           (new Date(date) > new Date())
                               ? span.addClass('bg-danger text-danger').text('-')
                               : span.addClass('bg-warning text-warning').text('-');

                           count += 1; // exactly once per row
                       });
                   }else if (loan_type === "On A Selected Date") {

                       let on_a_selected_date_txt = $("#installment_date_txt").val();

                       if (on_a_selected_date_txt.trim() === "" || isNaN(new Date(on_a_selected_date_txt))) {
                           Swal.fire("Error!", "Please enter a valid Collection Date !", "error");
                       } else {
                           // ---------- helpers ----------
                           function formatDate(date) {
                               const y = date.getFullYear();
                               const m = String(date.getMonth() + 1).padStart(2, '0');
                               const d = String(date.getDate()).padStart(2, '0');
                               return `${y}-${m}-${d}`;
                           }
                           function daysInMonth(y, m /*0-based*/) {
                               return new Date(y, m + 1, 0).getDate();
                           }
                           // add months but keep the selected DOM; if invalid (e.g., 31 in Feb), clamp to month's last day
                           function addMonthsKeepDOM(d, add) {
                               const y = d.getFullYear();
                               const m = d.getMonth();
                               const dom = d.getDate();
                               const targetM = m + add;
                               const targetY = y + Math.floor(targetM / 12);
                               const normM = (targetM % 12 + 12) % 12;
                               const dim = daysInMonth(targetY, normM);
                               const day = Math.min(dom, dim);
                               return new Date(targetY, normM, day);
                           }

                           // ---------- build dates ----------
                           const base = new Date(on_a_selected_date_txt);          // selected date
                           const hasOC = Number(firstInstCharge) > 0;
                           const nInst = parseInt(installmentCount, 10) || 0;      // installments count (excluding OC)
                           const needDates = nInst + (hasOC ? 1 : 0);              // total rows we will render

                           const installmentDates = [];
                           for (let i = 0; i < needDates; i++) {
                               const d = addMonthsKeepDOM(base, i);                  // monthly on same DOM, with month-end clamp
                               installmentDates.push(formatDate(d));
                           }

                           // ---------- table prep ----------
                           const tableBody = $('#installment_table tbody');
                           tableBody.empty();

                           // RB params (do NOT count OC)
                           const total_interest_percent = parseFloat(interest) || 0;
                           const r = (total_interest_percent / 100) / (nInst > 0 ? nInst : 1);
                           let EMI = (parseFloat(loan_amount) || 0) * r * Math.pow(1 + r, nInst) / (Math.pow(1 + r, nInst) - 1);
                           EMI = isFinite(EMI) ? parseFloat(EMI.toFixed(2)) : 0;
                           let principal_balance = parseFloat(loan_amount) || 0;

                           // numeric coercions for flat/draft
                           const instAmtFlat = parseFloat(installmentAmount);
                           const capEach     = parseFloat(capital_amount);
                           const intEach     = parseFloat(interest_amount);
                           const savingVal   = parseFloat(saving_amount_value);

                           let count = 1;

                           // ---------- Row #1: Other Charges (same as selected date), once ----------
                           if (hasOC) {
                               const ocDate = installmentDates[0];
                               const pd = new Date(ocDate);
                               pd.setDate(pd.getDate() + (Number(panelty_date_2) || 0));
                               const panelty_date = pd.toISOString().slice(0, 10);

                               const ocRow = `
        <tr>
          <td>${count}</td>
          <td>${ocDate}</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${panelty_date}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-end">0.00</td>
          <td class="text-end">${Number(firstInstCharge).toFixed(2)}</td>
          <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
        </tr>`;
                               tableBody.append(ocRow);

                               const spanOC = tableBody.children('tr:last-child').find('span');
                               (new Date(ocDate) > new Date())
                                   ? spanOC.addClass('bg-danger text-danger').text('-')
                                   : spanOC.addClass('bg-warning text-warning').text('-');

                               count += 1;
                               firstInstCharge = 0; // consume once
                           }

                           // ---------- Installment rows (start after OC date if present) ----------
                           const startIdx = hasOC ? 1 : 0;
                           installmentDates.slice(startIdx).forEach((date, idx) => {
                               const pd = new Date(date);
                               pd.setDate(pd.getDate() + (Number(panelty_date_2) || 0));
                               const panelty_date = pd.toISOString().slice(0, 10);

                               let rowHTML = '';

                               if (interest_method === "Reducing Balance") {
                                   const interest_amt = parseFloat((principal_balance * r).toFixed(2));
                                   let capital_amt    = parseFloat((EMI - interest_amt).toFixed(2));

                                   // last installment cleanup: idx is 0..(nInst-1)
                                   if (idx === nInst - 1) {
                                       capital_amt = parseFloat(principal_balance.toFixed(2));
                                       EMI = parseFloat((capital_amt + interest_amt).toFixed(2));
                                   }
                                   principal_balance = parseFloat((principal_balance - capital_amt).toFixed(2));

                                   const saving_show = EMI + savingVal;

                                   rowHTML = `
          <tr>
            <td>${count}</td>
            <td>${date}</td>
            <td class="text-end">${EMI.toFixed(2)}</td>
            <td class="text-end">${capital_amt.toFixed(2)}</td>
            <td class="text-end">${interest_amt.toFixed(2)}</td>
            <td class="text-end">${panelty_date}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${savingVal.toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${EMI.toFixed(2)}</td>
            <td class="text-end">${savingVal.toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
          </tr>`;
                               } else {
                                   // Flat / Draft
                                   const instAmt     = isNaN(instAmtFlat) ? 0 : instAmtFlat;
                                   const capAmt      = isNaN(capEach) ? 0 : capEach;
                                   const intAmt      = isNaN(intEach) ? 0 : intEach;
                                   const saving_show = instAmt + savingVal;

                                   rowHTML = `
          <tr>
            <td>${count}</td>
            <td>${date}</td>
            <td class="text-end">${instAmt.toFixed(2)}</td>
            <td class="text-end">${capAmt.toFixed(2)}</td>
            <td class="text-end">${intAmt.toFixed(2)}</td>
            <td class="text-end">${panelty_date}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${savingVal.toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-end">0.00</td>
            <td class="text-end">0.00</td>
            <td class="text-end">${instAmt.toFixed(2)}</td>
            <td class="text-end">${savingVal.toFixed(2)}</td>
            <td class="text-end">${saving_show.toFixed(2)}</td>
            <td class="text-center"><span class="px-1" style="background-color:#ff0000;border-radius:10px;color:#ff0000;">-</span></td>
          </tr>`;
                               }

                               tableBody.append(rowHTML);

                               const span = tableBody.children('tr:last-child').find('span');
                               (new Date(date) > new Date())
                                   ? span.addClass('bg-danger text-danger').text('-')
                                   : span.addClass('bg-warning text-warning').text('-');

                               count += 1; // exactly once per row
                           });
                       }
                   }


               }
           }



            $("#createLoanButton").removeClass("disabled").off("click.disable").on("click", function() {
                save_loan();
            });

        }
        function resetInstallmentSection() {
            $('#installment_table tbody').empty();
            $('#createLoanButton').prop('disabled', false);
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
                }else if(interest_method==="Reducing Balance"){
                    $('#total_interest_amount_show').hide();
                    $('#total_loan_amount_show').hide();
                    $('#new_interest_amount_show').hide();
                    $('#new_issued_amount_show').hide();
                }else{
                    $('#total_interest_amount_show').show();
                    $('#total_loan_amount_show').show();
                    $('#new_interest_amount_show').show();
                    $('#new_issued_amount_show').show();
                }

                $("#total_capital_amount").text(capital_amount_2);
                $("#total_interest_amount").text(interest_amount);
                saving_cal();

                checkAnotherCheckbox('separateCharges');
                resetInstallmentSection();
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
                }else if(interest_method==="Reducing Balance"){
                    $('#total_interest_amount_show').hide();
                    $('#total_loan_amount_show').hide();
                    $('#new_interest_amount_show').hide();
                    $('#new_issued_amount_show').hide();
                }else{
                    $('#total_interest_amount_show').show();
                    $('#total_loan_amount_show').show();
                    $('#new_interest_amount_show').show();
                    $('#new_issued_amount_show').show();
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
                checkAnotherCheckbox('separateCharges');
                resetInstallmentSection();
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
        let currentLoanChargeMode = 'separate'; // possible values: 'add', 'deduct', 'separate'


        function checkLoanChargesBalance() {
            const loanChargesBalanceCheckbox = document.getElementById("loanChargesBalance");
            const deductChargesCheckbox = document.getElementById("deductCharges");
            const separateChargesCheckbox = document.getElementById("separateCharges");

            if (loanChargesBalanceCheckbox.checked && currentLoanChargeMode !== 'add') {
                deductChargesCheckbox.checked = false;
                separateChargesCheckbox.checked = false;
                performCalculations('loanChargesBalance');
                currentLoanChargeMode = 'add';
            } else if (!loanChargesBalanceCheckbox.checked && currentLoanChargeMode !== 'separate') {
                separateChargesCheckbox.checked = true;
                performReversal('loanChargesBalance');
                currentLoanChargeMode = 'separate';
            }

            updateIssuedAmount();
        }


        function checkAnotherCheckbox(checkboxId) {
            const loanChargesBalanceCheckbox = document.getElementById("loanChargesBalance");
            const deductChargesCheckbox = document.getElementById("deductCharges");
            const separateChargesCheckbox = document.getElementById("separateCharges");
            const checkbox = document.getElementById(checkboxId);

            if (checkbox.checked) {
                loanChargesBalanceCheckbox.checked = false;
                deductChargesCheckbox.checked = (checkboxId === 'deductCharges');
                separateChargesCheckbox.checked = (checkboxId === 'separateCharges');

                if (checkboxId === 'deductCharges' && currentLoanChargeMode !== 'deduct') {
                    performReversal('loanChargesBalance');
                    currentLoanChargeMode = 'deduct';
                }

                if (checkboxId === 'separateCharges' && currentLoanChargeMode !== 'separate') {
                    performReversal('loanChargesBalance');
                    currentLoanChargeMode = 'separate';
                }
            }

            updateIssuedAmount();
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

                if (interest_method === "Draft") {
                    tot = total_interest_amount;
                }

                let installment = tot / loan_period;

                $("#total_loan_amount").text(tot_1.toFixed(2));
                $("#new_interest_amount").text(installment.toFixed(2));

                let new_capital = total_capital_amount + total_loan_charge;
                $("#total_capital_amount").text(new_capital.toFixed(2));
            }

            lastChecked = checkboxId;
        }

        function performReversal(checkboxId) {
            let interest_method = $("#interest_method").val();
            if (interest_method !== "Reducing Balance") {
                if (lastChecked !== 'loanChargesBalance') return;

                let total_loan_amount = parseFloat($("#total_loan_amount").text());
                let total_loan_charge = parseFloat($("#total_loan_charge").text());
                let total_capital_amount = parseFloat($("#total_capital_amount").text());
                let loan_period = parseInt($("#loan_period").val());
                let total_interest_amount = parseFloat($("#total_interest_amount").text());

                if (checkboxId === 'loanChargesBalance') {
                    let tot = total_loan_amount - total_loan_charge;
                    if (interest_method === "Draft") {
                        tot = total_interest_amount;
                    }

                    let installment = tot / loan_period;

                    $("#total_loan_amount").text((total_loan_amount + total_loan_charge).toFixed(2));
                    $("#new_interest_amount").text(installment.toFixed(2));

                    let new_capital = total_capital_amount - total_loan_charge;
                    $("#total_capital_amount").text(new_capital.toFixed(2));
                }

                lastChecked = '';
            }
        }

        function updateIssuedAmount() {
            const totalLoanCharge = parseFloat($('#total_loan_charge').text()) || 0;
            let issuedAmount = parseFloat($('#loan_amount').val()) || 0;

            if ($('#loanChargesBalance').is(':checked')) {
                issuedAmount += totalLoanCharge;
            }
            if ($('#deductCharges').is(':checked')) {
                issuedAmount -= totalLoanCharge;
            }

            $('#new_issued_amount').text(issuedAmount.toFixed(2));
        }



        function toggleFields() {
            const interestMethodSelect = document.getElementById('interest_method').value; // Get the selected value

            // if (interestMethodSelect === "Reducing Balance") {
            //     $("#normal_loan").hide();
            //     $("#reducingBalanceFields").show();
            //     $("#loan_period").prop('disabled', true); // Disables the field
            // } else {
            //     $("#reducingBalanceFields").hide();
            //     $("#normal_loan").show();
            //     $("#loan_period").prop('disabled', false); // Disables the field
            // }
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






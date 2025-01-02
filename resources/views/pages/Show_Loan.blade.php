@extends('layout.admin')

@section('head')
    <style>
        .style-tr>td {
            padding: 2px 10px;
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
                                <div>
                                    <h4>Loan Details</h4>


                                    <div class="d-flex justify-content-between flex-wrap mb-3 mt-4">
                                        <div class="col-md-8 row col-sm-12 mb-3">
                                            <div class="col-md-6 col-sm-12">
                                                <label for="simpleinput" class="form-label">Select Loan Category</label>
                                                <select class="form-control select2" data-toggle="select2" id="loan_cate_id"
                                                        disabled>
                                                    @foreach ($category as $item)
                                                        <option value="{{ $item->idLoan_Category }}">{{ $item->Name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="row mt-3 mb-2">
                                                <div class="col-sm-6 col-md-6">
                                                    <div class="row col-md-12">
                                                        <span class=" col-6 fw-bold">Loan Type</span>
                                                        <span class="col-6" id="only_interest">-</span>
                                                    </div>
                                                    <div class="row col-md-12">
                                                        <span class="col-6 fw-bold">Witness Count</span>
                                                        <span class="col-6" id="witness">-</span>
                                                    </div>
                                                    <div class="row col-md-12">
                                                        <span class="col-6 fw-bold">Collection Type</span>
                                                        <span class="col-6" id="collection_type">-</span>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <input type="hidden" id="customer_id" value="{{ $customers->idCustomer }}">
                                        <div class="col-md-4 col-sm-12 mb-3">
                                            <table class='table table-bordered' style="border-color:#CBC9C8; width: 100%;">
                                                <tr class='style-tr'>
                                                    <td class="col-md-5 fw-bold">
                                                        Customer
                                                    </td>
                                                    <td>
                                                        {{ $customers->Full_Name }}
                                                    </td>
                                                </tr>
                                                <tr class='style-tr'>
                                                    <td class="col-md-5 fw-bold">Contact No
                                                    </td>
                                                    <td>
                                                        {{ $customers->Contact_No }}
                                                    </td>
                                                </tr>
                                                <tr class='style-tr'>
                                                    <td class="col-md-5 fw-bold">Risk Level
                                                    </td>
                                                    <td>
                                                        {{ $customers->Customer_Risk_Level }}
                                                    </td>
                                                </tr>
                                                <tr class='style-tr'>
                                                    <td class="col-md-5 fw-bold">Note
                                                    </td>
                                                    <td>
                                                        {{ $customers->Note !== null ? $customers->Note : '-' }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>



                                    <hr class="border-top border-2">

                                    <div class="row mb-2">
                                        <div class="col-lg-6">

                                            <div class="mb-3" id="leasing_type">
                                                <label for="simpleinput" class="form-label">Select Leasing Type</label>
                                                <select class="form-control select2" data-toggle="select2" id="leasing_type"
                                                        onchange="change_type(this.value)">
                                                    <option value="Vehicle">Vehicle</option>
                                                    <option value="Land">Land</option>
                                                    <option value="Gold">Gold</option>
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Start Date</label>
                                                <input type="date" class="form-control date" id="startdate"
                                                       value="{{ $getloan->Date_Time }}" disabled>
                                            </div>

                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Interest Rate</label>
                                                <input type="text" id="interest" class="form-control"
                                                       value="{{ $getloan->Interest_Rate }}" onkeyup="calculateInterest()"
                                                       disabled>
                                            </div>

                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Installment Count</label>
                                                <input type="number" id="ins_count" class="form-control"
                                                       value="{{ $getloan->Installment_Count }}" onkeyup="calculateInterest()"
                                                       disabled>
                                            </div>


                                        </div> <!-- end col -->
                                        <div class="col-lg-6 mb-3 ">

                                            <div class="mb-3" id="registration_no">
                                                <label for="simpleinput" id="leasing_type_name"
                                                       class="form-label">Registration Number</label>
                                                <input type="text" id="loan_number" class="form-control">
                                            </div>

                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Loan Amount</label>
                                                <input type="text" id="loan_amount" class="form-control"
                                                       value="{{ $getloan->Amount }}" onkeyup="calculateInterest()" disabled>
                                            </div>

                                            <div class="mb-3">
                                                <label for="simpleinput" class="form-label">Panalty Rate</label>
                                                <input type="text" id="panelty_amount" class="form-control"
                                                       value="{{ $getloan->Panalty_Rate }}" disabled>
                                            </div>

                                            <div class="mb-4">
                                                <label for="simpleinput" class="form-label">Interest Amount</label>
                                                <input type="text" id="interest_amount" class="form-control"
                                                       value="{{ $getloan->Interest_Amount }}" disabled>
                                            </div>

                                        </div> <!-- end col -->
                                    </div>

                                    <hr class="border-top border-2">

                                    <div class="mt-4">
                                        <p class="fw-bold mb-3">Loan Charges</p>

                                        <div>
                                            <div>
                                                <div class="table-responsive-sm border border-1">
                                                    <table class="table table-centered mb-0" id="loan_charge_table">
                                                        <thead style="background-color: #b01e1e">
                                                        <tr>
                                                            <th style="color: white">Description</th>
                                                            <th class="text-end" style="color: white">Amount (LKR)
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>

                                                        </tbody>
                                                    </table>
                                                </div> <!-- end table-responsive-->
                                            </div> <!-- end card-body-->
                                        </div> <!-- end card-->

                                        <div class="row mt-4 mb-3">
                                            <div class="col-md-6 col-sm-12 row">
                                                <div class="mt-2 mb-3 row col-md-12">
                                                    <span class="col-6 fw-bold">Total Loan Charges</span>
                                                    <span class="col-6 fw-bold text-left"
                                                          id="total_loan_charge">0.00</span>
                                                </div>
                                                <div class="mt-2 mb-3 row col-md-12">
                                                    <span class="col-6 fw-bold">Loan Charges Balance</span>
                                                    <input type="text" id="loan_charge_balance"
                                                           style="text-align: right; width:50% !important;" value="0.00"
                                                           class="form-control col-6"
                                                           value="{{ $getloan->Other_Amount_Balance }}" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-sm-12 row">

                                                <div class="mt-2 mb-3  col-md-12">
                                                    <span class="col-md-8 fw-bold">Total Loan Amount</span>
                                                    <span style="margin-left: 2rem;" class="col-md-2 ml-3 "
                                                          id="total_loan_amount">0.00</span>
                                                </div>
                                                <div class="mt-2 mb-3  col-md-12">
                                                    <span class="col-md-8 fw-bold">Installment Amount</span>
                                                    <span style="margin-left: 2rem;" class="col-md-2 ml-3 "
                                                          id="new_interest_amount">0.00</span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <hr class="border-top border-2">


                                    <div class="row mt-4 mb-4" hidden>
                                        <div class="col-lg-2">
                                            <div class="mt-2 mb-4">
                                                <span class="fw-bold">Collection Date</span>
                                            </div>
                                            <div class="mt-2 mb-4">
                                                <span class="fw-bold">Penalty Date</span>
                                            </div>
                                            <div class="mt-2 mb-4">
                                                <span class="fw-bold">First Installment Date</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="mt-1 mb-3 text-end" id="weekly">
                                                <select class="form-control" id="weekly_txt">
                                                    <option selected>Monday</option>
                                                    <option>Tuesday</option>
                                                    <option>Wednesday</option>
                                                    <option>Thursday</option>
                                                    <option>Friday</option>
                                                    <option>Saturday</option>
                                                    <option>Sunday</option>
                                                </select>

                                            </div>
                                            <div class="mt-1 mb-3" id="first_of_the_month">
                                                <label class="form-label">First Of The Month</label>
                                            </div>
                                            <div class="mt-1 mb-3" id="end_of_the_month">
                                                <label class="form-label">End Of The Month</label>
                                            </div>
                                            <div class="mt-1 mb-3 text-end" id="twice_a_month">
                                                <select class="form-control" id="twice_a_month_txt"
                                                        onchange="change_date(this.value)">
                                                    <option value="1" selected>First Of Month And 15th</option>
                                                    <option value="2">15th And End Of Month</option>
                                                </select>
                                            </div>
                                            <div class="mt-1 mb-3 text-end" id="on_a_selected_date">
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
                                            <div class="mt-2 mb-3">
                                                <label class="form-label" id="panelty_date"></label>
                                                <label class="form-label" id="panelty_date_2" hidden></label>

                                            </div>
                                            <div class="mt-2 mb-3">
                                                <input type="date" class="form-control date" id="installment_date_txt"
                                                       value="{{ $getloan->Collection_Date }}">
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-success" onclick="addInstallmentDates()" hidden>Generate
                                        Installments</button>

                                    <div class="alert alert-purple text-center mt-3 mb-1"
                                         style="background-color: white; color: #192d50; border border-2 solid #192d50;">
                                        <h5 style="margin-top: -3px; margin-bottom: -3px;">Installments</h5>
                                    </div>



                                    <div class="row mt-4 mb-1" hidden>
                                        <div class="col-sm-2">
                                            <div>
                                                <span class="fw-bold">Loan No</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div>
                                                <span id="loan_number_txt">: NJG12345</span>
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
                                                        <th class="text-end">Panalty Amount</th>
                                                        <th class="text-end">Total Amount</th>
                                                        <th class="text-end">Paid Amount</th>
                                                        <th class="text-end">Panalty Balance</th>
                                                        <th class="text-end">Installment Balance</th>
                                                        <th class="text-end">Total Balance</th>
                                                        <th>Status</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    $count = 1;
                                                    ?>
                                                    @foreach ($installments as $item)
                                                        <tr>
                                                            <td>{{ $count }}</td>
                                                            <td>{{ $item->Installment_Date }}</td>
                                                            <td>{{ $item->Installment_Amount }}</td>
                                                            <td>{{ $item->Panalty_Amount }}</td>
                                                            <td>{{ $item->Total_Amount }}</td>
                                                            <td>{{ $item->Paid_Amount }}</td>
                                                            <td>{{ $item->Panalty_Balance }}</td>
                                                            <td>{{ $item->Installment_Balance }}</td>
                                                            <td>{{ $item->Total_Balance }}</td>
                                                            @if ($item->Status === '0')
                                                                @php
                                                                    $currentTime = now()->timezone('Asia/Colombo');
                                                                    $installmentDate = \Carbon\Carbon::parse($item->Installment_Date, 'Asia/Colombo');
                                                                @endphp
                                                                @if ($item->Total_Balance > 0 && $installmentDate->lessThan($currentTime))
                                                                    <td class="text-center"><span class="px-1" style="background-color: #ff0000;border-radius: 10px; color: #ff0000;">-</span></td>
                                                                @else
                                                                    <td class="text-center"><span class="px-1" style="background-color: #ffdd00;border-radius: 10px; color: #ffdd00;">-</span></td>
                                                                @endif
                                                            @else
                                                                <td class="text-center"><span class="px-1" style="background-color: #1bff00;border-radius: 10px; color: #00ff27;">-</span></td>
                                                            @endif

                                                        </tr>
                                                            <?php $count++; ?>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div> <!-- end table-responsive-->
                                        </div> <!-- end card-body-->
                                    </div> <!-- end card-->



                                    <div class="alert alert-purple text-center mt-5 mb-1"
                                         style="background-color: white; color: #192d50; border border-2 solid #192d50;">
                                        <h5 style="margin-top: -3px;margin-bottom: -3px;"> Witness Details</h5>
                                    </div>



                                    <div class="card-body mt-4">
                                        <ul class="nav nav-tabs mb-3" id="witnessTabs">
                                            @foreach ($witnesses as $key => $witness)
                                                <li class="nav-item">
                                                    <a href="#witness{{ $key }}" data-bs-toggle="tab"
                                                       aria-expanded="true"
                                                       class="nav-link{{ $key === 0 ? ' active' : '' }}">
                                                        Witness {{ $key + 1 }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>

                                        <div class="tab-content">
                                            @foreach ($witnesses as $key => $witness)
                                                <div class="tab-pane{{ $key === 0 ? ' show active' : '' }}"
                                                     id="witness{{ $key }}">

                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="title{{ $key }}"
                                                                       class="form-label">Title</label>
                                                                <select class="form-select" id="title{{ $key }}"
                                                                        disabled>
                                                                    @if ($witness->Title === 'Mr')
                                                                        <option selected>Mr</option>
                                                                        <option>Mrs</option>
                                                                    @else
                                                                        <option>Mr</option>
                                                                        <option selected>Mrs</option>
                                                                    @endif
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="fullName{{ $key }}"
                                                                       class="form-label">Full Name</label>
                                                                <input type="text" id="fullName{{ $key }}"
                                                                       class="form-control"
                                                                       value="{{ $witness->Full_Name }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="address{{ $key }}"
                                                                       class="form-label">Address</label>
                                                                <input type="text" id="address{{ $key }}"
                                                                       class="form-control" value="{{ $witness->Address }}"
                                                                       disabled>
                                                            </div>
                                                        </div> <!-- end col -->
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="firstName{{ $key }}"
                                                                       class="form-label">First Name</label>
                                                                <input type="text" id="firstName{{ $key }}"
                                                                       class="form-control"
                                                                       value="{{ $witness->First_Name }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="nic{{ $key }}"
                                                                       class="form-label">NIC</label>
                                                                <input type="text" id="nic{{ $key }}"
                                                                       class="form-control" value="{{ $witness->NIC }}"
                                                                       disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label for="contactNo{{ $key }}"
                                                                       class="form-label">Contact No</label>
                                                                <input type="text" id="contactNo{{ $key }}"
                                                                       class="form-control"
                                                                       value="{{ $witness->Contact_No }}" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>


                                    </div>


                                    <div class="alert alert-purple text-center mt-5 mb-1"
                                         style="background-color: white; color: #192d50; border border-2 solid #192d50;">
                                        <h5 style="margin-top: -3px;margin-bottom: -3px;"> Required Document</h5>
                                    </div>


                                    <div class="card mt-5">
                                        <div>
                                            <div class="table-responsive-sm border border-1">
                                                <table class="table table-centered mb-0" id="document_show_table">
                                                    <thead>
                                                    <tr>
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

                            </div> <!-- end card-->
                        </div> <!-- end col -->


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('../JS/validate.js') }}"></script>
    <script src="{{ asset('../JS/show_loan.js') }}"></script>
@endsection

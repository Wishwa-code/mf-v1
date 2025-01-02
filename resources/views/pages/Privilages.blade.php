@extends('layout.admin')

@section('head')
    <style>
        .main-topic {
            background-color: #ffffff; /* Light gray background for main topics */
        }

        .sub-topic {
            padding-left: 20px; /* Indent subtopics */
            color: #6c757d; /* Gray color for subtopics */
        }

        .sub-topic i {
            color: #ffc107; /* Star color for subtopic icon */
        }
    </style>
@endsection


@section('content')
    <div>

        <!-- start page title -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="row">
                    <div class="container-fluid" style="max-width: 100%; padding-left: 25px; padding-right: 25px; ">
                        <form id="updatePermissionForm">

                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">User</label>
                                <select class="form-control select2bs4" id="userid" name="userid" onchange="load_to_table(this.value)" style="width: 100%;">
                                    <option value="0">Select User</option>
                                    <?php
                                    $branch = session('branch_id'); // Retrieve the branch_id from the session
                                    $user_details = DB::select("SELECT * FROM user WHERE Status='1' AND branch_id = ?", [$branch]);

                                    foreach ($user_details as $item) {
                                        echo '<option value="' . $item->id . '">' . $item->Full_Name . '</option>';
                                    }
                                    ?>

                                </select>
                            </div>
                            <div class="d-flex align-items-center">
                                {{--                                <div class="mr-3">--}}
                                {{--                                    <div class="d-flex align-items-center">--}}
                                {{--                                        <i class="ti ti-folder h3 text-primary"></i>--}}
                                {{--                                        <span class="h5 font-weight-bold ml-2">Full Permission</span>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                                <div class="form-check form-switch mb-0">
                                    <label class="form-check-label mr-2" for="full_permission">Full Permission</label>
                                    <input class="form-check-input access_module main-checkbox" id="full_permission" type="checkbox">
                                </div>

                            </div>




                            <br>


                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                    <tr>
                                        <th class="fw-bolder">Module Permission</th>
                                        <th class="fw-bolder text-center">Access</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Dashboard</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="dashboard" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Customer Main Topic -->
                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Customer</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="1" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Customer Subtopics -->
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Add Customer</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="2" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">View Customer</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="3" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Center Main Topic -->
                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Loan Center</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="4" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Center Subtopics -->
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Create Center</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="5" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">View Center</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="6" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Create Group</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="7" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">View Group</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="8" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Add Customer To Group</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="9" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Loan Category Main Topic -->
                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Guarantee</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="10" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Loan Category Subtopics -->
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Add Guarantee</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="11" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">View Guarantee</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="12" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Product/Loan</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="13" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Loan Category Subtopics -->
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Add Product</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="14" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">View Product</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="15" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Issue Loan</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="16" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Pending Loan</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="17" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Current Loan</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="18" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Loan In Arrease</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="19" type="checkbox">
                                        </td>
                                    </tr>


                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Payment Details</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="20" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Loan Category Subtopics -->
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Add Repayment</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="21" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Add Bulk Repayment</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="51" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">View Repayment</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="22" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Pending Approval Repayments</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="23" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Approved Repayments</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="24" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Agent Collection</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="25" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Account Center</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="52" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Loan Category Subtopics -->
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Bank Account</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="53" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Cheque Details</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="54" type="checkbox">
                                        </td>
                                    </tr>





                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Account Center</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="account_department" type="checkbox">
                                        </td>
                                    </tr>






                                    <tr class="main-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Loan Calculator</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="26" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="main-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Calender</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="27" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Expenses</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="28" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Loan Category Subtopics -->
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Add Expenses</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="29" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">View Expenses</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="30" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Other Income</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="31" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Loan Category Subtopics -->
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Add Income</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="32" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">View Income</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="33" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">User</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="34" type="checkbox">
                                        </td>
                                    </tr>
                                    <!-- Loan Category Subtopics -->
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Create User</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="35" type="checkbox">
                                        </td>
                                    </tr>
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">User Privilege</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="36" type="checkbox">
                                        </td>
                                    </tr>



                                    <tr class="main-topic">
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-folder h3 text-primary mr-2"></i>
                                                <span class="h5 font-weight-bold">Report</span>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input access_module" id="37" type="checkbox">
                                        </td>
                                    </tr>


                                    <!-- Loan Category Subtopics -->
                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Full Loan Detail Report</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="AllLoanDetailReport" type="checkbox">
                                        </td>
                                    </tr>


                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Arrease Details</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="late_payment_report" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Date wise cash flow details</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="ViewDateWiseCashFlow" type="checkbox">
                                        </td>
                                    </tr>


                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Monthly Collection Summary details</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="MonthlyCollectionSummary" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">All Customer Details</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="customerreport_details" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Loan Details</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="loanreport" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Guardian Details</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="borrowerreport" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Agent Wise Repayment Collection</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="repaymentreport" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Customer Wise Repayments</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="customerrepaymentreport" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Deduction Report</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="deduct_report" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">CashBook Report</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="cashbook" type="checkbox">
                                        </td>
                                    </tr>


                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">PAR (Portfolio at Risk)</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="par" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Profit And Lost</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="profit" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">GL Report</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="gl_report" type="checkbox">
                                        </td>
                                    </tr>


                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Cash Flow Statement</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="cash_flow" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">Statement Of Financial Position</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="statement" type="checkbox">
                                        </td>
                                    </tr>

                                    <tr class="sub-topic">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ti ti-star h5 mr-2"></i>
                                                <span class="h6">SMS History Report</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <input class="form-check-input access_module" id="sms_history" type="checkbox">
                                        </td>
                                    </tr>




                                    </tbody>
                                </table>
                            </div>





                            <div class="text-center mt-4">
                                <button class="btn btn-primary btn-lg" type="button" onclick="savePrivileges(event)">Update Privileges</button>
                            </div>
                        </form>
                        <br>


                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')

    <script src="../JS/validate.js"></script>
    <script src="../JS/privilages.js?n=5"></script>
    <script>
        $(document).ready(function() {
            // Handle main checkbox change event
            $('.main-checkbox').change(function() {
                var isChecked = $(this).prop('checked');
                // Find all checkboxes with class .access_module and toggle their state
                $('.access_module').prop('checked', isChecked);
            });

            // Handle sub checkbox change events (if needed)
            $('.access_module:not(.main-checkbox)').change(function() {
                var isChecked = $(this).prop('checked');
                // Optionally, update main checkbox state based on sub checkbox changes
                updateMainCheckboxState();
            });

            // Function to update main checkbox state based on sub checkboxes
            function updateMainCheckboxState() {
                var allChecked = $('.access_module:not(.main-checkbox)').length === $('.access_module:not(.main-checkbox):checked').length;
                $('.main-checkbox').prop('checked', allChecked);
            }
        });
    </script>


@endsection







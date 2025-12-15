@extends('layout.admin')

@section('head')
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .main-topic {
            background-color: #ffffff;
            /* Light gray background for main topics */
        }


        .profile-card .card-header {
            background: #007bff;
            color: white;
            text-align: center;
        }

        .profile-card .profile-image {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: -50px;
        }

        .profile-card .profile-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 5px solid white;
        }
    </style>
@endsection

@section('content')
    <div class="row mt-3">
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="fw-bolder">Shortcut Permission</th>
                                    <th class="fw-bolder text-center">Access</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Customer Main Topic -->
                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Add Customer</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="1" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Customer Subtopics -->

                                <!-- Center Main Topic -->
                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">View Customer</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="2" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Center Subtopics -->

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Assign Customers to group</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="3" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Loan Category Subtopics -->
                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">View Products</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="4" type="checkbox">
                                    </td>
                                </tr>

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Pending Loans</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="5" type="checkbox">
                                    </td>
                                </tr>




                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Current Loans</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="6" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Loan Category Subtopics -->


                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Loan In arrears</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="7" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Loan Category Subtopics -->

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Add Repayment</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="8" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Loan Category Subtopics -->




                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Repayment details</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="9" type="checkbox">
                                    </td>
                                </tr>
                                <!-- Loan Category Subtopics -->

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Collector wise collections</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="10" type="checkbox">
                                    </td>
                                </tr>


                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Loan Calculator</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="11" type="checkbox">
                                    </td>
                                </tr>

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Add Expenses</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="12" type="checkbox">
                                    </td>
                                </tr>

                                <tr class="main-topic">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-folder h3 text-primary mr-2"></i>
                                            <span class="h5 font-weight-bold">Add Income</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <input class="form-check-input access_module" id="13" type="checkbox">
                                    </td>
                                </tr>





                            </tbody>
                        </table>

                        <input type="button" class="btn btn-danger" value="Update Shortcut"
                            onclick="saveShortcut(event)">
                    </div>
                </div>
            </div>

            <!-- Loan Creation Restrictions Card -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="mb-3">Loan Creation Restrictions</h5>
                    <hr>
                    <small class="text-muted d-block mb-3">Configure restrictions and requirements for loan creation
                        process.</small>

                    <!-- Document Upload Restriction -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Document Upload Restriction</label>
                        <div class="d-flex gap-2">
                            <select id="document_upload_restriction" class="form-select" style="max-width: 300px;">
                                <option value="" disabled selected>-- Select Option --</option>
                                <option value="required">Required</option>
                                <option value="not_required">Not Required</option>
                            </select>
                        </div>
                        <small class="text-muted">Specify whether document uploads are mandatory during loan
                            creation.</small>
                    </div>

                    <hr>
                    <!-- Guarantees Restriction -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Guarantees</label>
                        <div class="d-flex gap-2">
                            <select id="guarantees_restriction" class="form-select" style="max-width: 300px;">
                                <option value="" disabled selected>-- Select Option --</option>
                                <option value="required">Required</option>
                                <option value="not_required">Not Required</option>
                            </select>
                        </div>
                        <small class="text-muted">Specify whether guarantees are mandatory during loan
                            creation.</small>
                    </div>

                    <hr>
                    <!-- Change Product Details -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">Change Product Details</label>
                        <div class="d-flex gap-2">
                            <select id="change_product_details" class="form-select" style="max-width: 300px;">
                                <option value="" disabled selected>-- Select Option --</option>
                                <option value="editable">Editable</option>
                                <option value="not_editable">Not Editable</option>
                            </select>
                        </div>
                        <small class="text-muted">Control whether product details can be modified after loan
                            creation.</small>
                    </div>

                    <hr>
                    <!-- First Installment Date Restrictions -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">First Installment Date - Maximum Days After Issue
                            Date</label>
                        <small class="text-muted d-block mb-2">Set how many days after the Issue Date the first
                            installment date can be set for each loan type.</small>

                        <!-- Daily Loans -->
                        <div class="mb-3">
                            <label class="form-label">Daily Loans (max days)</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" id="first_installment_daily" class="form-control" min="0"
                                    max="365" style="max-width: 150px;" placeholder="Days">
                                <span class="text-muted">days</span>
                            </div>
                        </div>

                        <!-- Weekly Loans -->
                        <div class="mb-3">
                            <label class="form-label">Weekly Loans (max days)</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" id="first_installment_weekly" class="form-control" min="0"
                                    max="365" style="max-width: 150px;" placeholder="Days">
                                <span class="text-muted">days</span>
                            </div>
                        </div>

                        <!-- Monthly Loans -->
                        <div class="mb-3">
                            <label class="form-label">Monthly Loans (max days)</label>
                            <div class="d-flex gap-2 align-items-center">
                                <input type="number" id="first_installment_monthly" class="form-control" min="0"
                                    max="365" style="max-width: 150px;" placeholder="Days">
                                <span class="text-muted">days</span>
                            </div>
                        </div>
                    </div>

                    <button id="btnUpdateLoanRestrictions" class="btn btn-primary">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Update Restrictions
                    </button>
                </div>
            </div>
        </div>

      
    </div>
    <!-- Head Office Approval Section -->
    <div class="row mt-3" >
        <div class="col-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Head Office Approval</h5>
                    <hr>
                    <small class="text-muted d-block mb-3">Configure which approval types require head office
                        approval.</small>

                    <!-- User Management -->
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">User Management</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>User Creation</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_101"
                                        data-type="101">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>User Details Update</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_102"
                                        data-type="102">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>User Privilege Change</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_103"
                                        data-type="103">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>User Designation Change</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_104"
                                        data-type="104">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Designation Management -->
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">Designation Management</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Designation Privileges Update</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_201"
                                        data-type="201">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Management -->
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">Customer Management</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Customer Creation</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_301"
                                        data-type="301">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Customer Details Update</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_302"
                                        data-type="302">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Customer Status Change</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_303"
                                        data-type="303">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Customer Blacklist</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_304"
                                        data-type="304">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Customer Document Update</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_305"
                                        data-type="305">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Management -->
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">Loan Management</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Loan Approval</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_401"
                                        data-type="401">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Loan Rejection</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_402"
                                        data-type="402">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Loan Modification</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_403"
                                        data-type="403">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Transactions -->
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">Financial Transactions</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Bank Account Transfer</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_501"
                                        data-type="501">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Payment Undo</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_502"
                                        data-type="502">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Payment Reversal</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_503"
                                        data-type="503">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses -->
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">Expenses</h6>
                        <div class="list-group">
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Expense Creation</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_601"
                                        data-type="601">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Expense Approval</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_602"
                                        data-type="602">
                                </div>
                            </div>
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Expense Modification</span>
                                <div class="form-check form-switch">
                                    <input class="form-check-input approval-toggle" type="checkbox" id="approval_603"
                                        data-type="603">
                                </div>
                            </div>
                        </div>
                    </div>

                    <button id="btnSaveAllApprovals" class="btn btn-primary mt-2">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save All Approval Settings
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <!-- jQuery and Bootstrap 5 JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="../JS/profile.js"></script>
    <!-- Custom JS -->
    <script>
        $(document).ready(function() {
            load_data_shortcut();
            // you already call load_data_shortcut(); keep it
            load_settings();

            $('#btnUpdatePaymentMemberName').on('click', function(e) {
                e.preventDefault();
                const value = $('#payment_member_name').val(); // 'full_name' or 'with_initial'
                save_setting('payment_member_name', value);
            });

            $('#btnUpdateLoanPolicy').on('click', function(e) {
                e.preventDefault();
                const value = $('input[name="loan_policy"]:checked').val(); // 'strict' or 'flexible'

                if (!value) {
                    Swal.fire("Warning", "Please select a Loan Disbursement Policy before updating.",
                        "warning");
                    return;
                }

                save_setting('loan_disbursement_policy', value);
            });

            $('#btnUpdatePaymentBackdate').on('click', function(e) {
                e.preventDefault();
                const value = $('#payment_backdate').val(); // 'enabled' | 'disabled'
                save_setting('payment_backdate', value);
            });

            $('#btnUpdateLoanOrder').on('click', function(e) {
                e.preventDefault();
                const value = $('#loan_order').val(); // 'create_date' | 'loan_number' | 'issue_date'
                save_setting('loan_order', value);
            });

            $('#btnUpdateDueSkipType').on('click', function(e) {
                e.preventDefault();
                const value = $('#due_skip_type').val(); // 'skip_installment' | 'skip_day'

                if (!value) {
                    Swal.fire("Warning", "Please select a Due Skip Type.", "warning");
                    return;
                }

                save_setting('due_skip_type', value);
            });


            $('#btnUpdateMaxLoans').on('click', function(e) {
                e.preventDefault();
                const value = $('#max_allowed_loans').val();
                if (!value || value < 1 || value > 50) {
                    Swal.fire("Warning", "Max allowed loans must be between 1 and 50.", "warning");
                    return;
                }
                save_setting('max_allowed_loans', value);
            });

            // Hidden: empty_row_count moved to Repayment Sheet 09 page
            // $('#btnUpdateEmptyRowCount').on('click', function (e) {
            //     e.preventDefault();
            //     const value = $('#empty_row_count').val();
            //     if (!value || value < 0 || value > 100) {
            //         Swal.fire("Warning", "Empty row count must be between 0 and 100.", "warning");
            //         return;
            //     }
            //     save_setting('empty_row_count', value);
            // });

            // Document Types Management
            $('#btnAddDocumentType').on('click', function(e) {
                e.preventDefault();
                addDocumentType();
            });

            $('#new_document_type').on('keypress', function(e) {
                if (e.which === 13) { // Enter key
                    e.preventDefault();
                    addDocumentType();
                }
            });

            $('#btnUpdateDocumentTypes').on('click', function(e) {
                e.preventDefault();
                saveDocumentTypes();
            });

            $('#btnUpdateCollectorModes').on('click', function(e) {
                e.preventDefault();
                const selected = $('.collector-mode:checked').map(function() {
                    return $(this).val();
                }).get();

                // Optional: prevent empty selection
                if (selected.length === 0) {
                    Swal.fire("Warning", "Select at least one mode.", "warning");
                    return;
                }

                // Save as JSON string
                save_setting('collector_txn_modes', JSON.stringify(selected));
            });

            $('#btnUpdateRecoveryAccount').on('click', function(e) {
                e.preventDefault();
                const value = $('#recovery_account_status').val(); // 'active' | 'inactive'
                save_setting('recovery_account_status', value);
            });

            // Collection Days update
            $('#btnUpdateCollectionDays').on('click', function(e) {
                e.preventDefault();

                const days = getSelectedCollectionDays(); // e.g. [1,2,3,4,5]

                if (days.length === 0) {
                    Swal.fire("Warning", "Select at least one collection day.", "warning");
                    return;
                }

                Swal.fire({
                    title: "Are you sure?",
                    text: "Update allowed collection days?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Update",
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        type: "POST",
                        url: "/settings/upsert",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: {
                            key: 'collection_days',
                            value: JSON.stringify(days) // store as JSON array
                        },
                        success: function() {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Collection days updated!",
                                timer: 1400,
                                showConfirmButton: false
                            });
                        },
                        error: function(xhr) {
                            Swal.fire("Error", xhr.responseJSON?.message ||
                                "Failed to update collection days", "error");
                        }
                    });
                });
            });


            // Read which weekdays are allowed
            const getSelectedCollectionDays = () => {
                const days = [];
                $('.collection-day:checked').each(function() {
                    days.push(parseInt($(this).val())); // [1,2,5,...]
                });
                return days;
            };



            // Head Office Approval - Save All
            $('#btnSaveAllApprovals').on('click', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: "Are you sure?",
                    text: "Update all head office approval settings?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Update All",
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    const settings = [];
                    $('.approval-toggle').each(function() {
                        const type = $(this).data('type');
                        const isChecked = $(this).is(':checked');
                        const value = isChecked ? 'required' : 'not_required';
                        settings.push({
                            key: 'headoffice_approval_' + type,
                            value: value
                        });
                    });

                    let completedCount = 0;
                    let hasError = false;

                    settings.forEach(setting => {
                        $.ajax({
                            type: "POST",
                            url: "/settings/upsert",
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                    "content"),
                            },
                            data: setting,
                            success: function() {
                                completedCount++;
                                if (completedCount === settings.length && !
                                    hasError) {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "All approval settings updated!",
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                if (!hasError) {
                                    hasError = true;
                                    Swal.fire("Error", xhr.responseJSON
                                        ?.message ||
                                        "Failed to update approval settings",
                                        "error");
                                }
                            }
                        });
                    });
                });
            });

            // Loan Creation Restrictions
            $('#btnUpdateLoanRestrictions').on('click', function(e) {
                e.preventDefault();

                const documentUpload = $('#document_upload_restriction').val();
                const guarantees = $('#guarantees_restriction').val();
                const changeProduct = $('#change_product_details').val();
                const dailyDays = $('#first_installment_daily').val();
                const weeklyDays = $('#first_installment_weekly').val();
                const monthlyDays = $('#first_installment_monthly').val();

                // Validate numeric fields
                if (!dailyDays || dailyDays < 0 || dailyDays > 365) {
                    Swal.fire("Warning", "Daily loans max days must be between 0 and 365.", "warning");
                    return;
                }
                if (!weeklyDays || weeklyDays < 0 || weeklyDays > 365) {
                    Swal.fire("Warning", "Weekly loans max days must be between 0 and 365.", "warning");
                    return;
                }
                if (!monthlyDays || monthlyDays < 0 || monthlyDays > 365) {
                    Swal.fire("Warning", "Monthly loans max days must be between 0 and 365.", "warning");
                    return;
                }

                // Save all settings
                saveLoanRestrictions(documentUpload, guarantees, changeProduct, dailyDays, weeklyDays,
                    monthlyDays);
            });

        });

        const getSelectedCheckboxValues = () => {
            const checkboxValues = {};
            $('input.access_module:checked').each(function() {
                const id = $(this).attr('id');
                let key;
                switch (id) {
                    case '1':
                        key = 'Add_Customer';
                        break;
                    case '2':
                        key = 'View_Customer';
                        break;
                    case '3':
                        key = 'Assign_Customers_to_group';
                        break;
                    case '4':
                        key = 'View_Products';
                        break;
                    case '5':
                        key = 'Pending_Loans';
                        break;
                    case '6':
                        key = 'Current_Loans';
                        break;
                    case '7':
                        key = 'Loan_In_arrears';
                        break;
                    case '8':
                        key = 'Add_Repayment';
                        break;
                    case '9':
                        key = 'Repayment_details';
                        break;
                    case '10':
                        key = 'Collector_wise_collections';
                        break;
                    case '11':
                        key = 'Loan_Calculator';
                        break;
                    case '12':
                        key = 'Add_Expenses';
                        break;
                    case '13':
                        key = 'Add_Income';
                        break;
                    default:
                        key = `Checkbox_${id}`;
                }
                checkboxValues[key] = 1;
            });
            return checkboxValues;
        }

        const saveShortcut = (e) => {
            e.preventDefault();

            const selectedCheckboxValues = getSelectedCheckboxValues();

            Swal.fire({
                title: "Are you sure?",
                text: "Do you want to update these shortcuts?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update it!",
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "POST",
                        url: "/shortcuts",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: {
                            checkboxValues: selectedCheckboxValues
                        }, // Use ES6 shorthand
                        success: function(data) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Successfully updated shortcuts!",
                            }).then(function() {
                                load_data_shortcut();
                            });
                        },
                    });
                }
            });
        };

        const load_data_shortcut = () => {
            $.ajax({
                type: "GET",
                url: "/shortcuts/all",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(data) {
                    console.log(data);
                    const items = data.items;
                    items.forEach(item => {
                        switch (item.name) {
                            case 'Add_Customer':
                                $('#1').prop('checked', true);
                                break;
                            case 'View_Customer':
                                $('#2').prop('checked', true);
                                break;
                            case 'Assign_Customers_to_group':
                                $('#3').prop('checked', true);
                                break;
                            case 'View_Products':
                                $('#4').prop('checked', true);
                                break;
                            case 'Pending_Loans':
                                $('#5').prop('checked', true);
                                break;
                            case 'Current_Loans':
                                $('#6').prop('checked', true);
                                break;
                            case 'Loan_In_arrears':
                                $('#7').prop('checked', true);
                                break;
                            case 'Add_Repayment':
                                $('#8').prop('checked', true);
                                break;
                            case 'Repayment_details':
                                $('#9').prop('checked', true);
                                break;
                            case 'Collector_wise_collections':
                                $('#10').prop('checked', true);
                                break;
                            case 'Loan_Calculator':
                                $('#11').prop('checked', true);
                                break;
                            case 'Add_Expenses':
                                $('#12').prop('checked', true);
                                break;
                            case 'Add_Income':
                                $('#13').prop('checked', true);
                                break;
                        }
                    });
                },
                error: function(error) {
                    console.log('Error:', error);
                }
            });
        };

        // ========== SETTINGS ==========

        // Load settings into UI
        const load_settings = () => {
            $.ajax({
                type: "GET",
                url: "/settings/all",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function(data) {
                    const items = data.items || {};

                    // Recovery Account Access
                    if (items.recovery_account_status) {
                        $('#recovery_account_status').val(items
                            .recovery_account_status); // 'active' | 'inactive'
                    }

                    // Due Skip Type
                    if (items.due_skip_type) {
                        $('#due_skip_type').val(items.due_skip_type); // 'skip_installment' | 'skip_day'
                    }

                    // Payment Member Name
                    if (items.payment_member_name) {
                        $('#payment_member_name').val(items.payment_member_name);
                    }

                    // Loan Disbursement Policy
                    if (items.loan_disbursement_policy) {
                        $(`input[name="loan_policy"][value="${items.loan_disbursement_policy}"]`).prop(
                            'checked', true);
                    }

                    // NEW: Payment Backdate
                    if (items.payment_backdate) {
                        $('#payment_backdate').val(items.payment_backdate); // 'enabled' | 'disabled'
                    }

                    // NEW: Loan Number Order
                    if (items.loan_order) {
                        $('#loan_order').val(items
                            .loan_order); // 'create_date' | 'loan_number' | 'issue_date'
                    }

                    // Max Allowed Loans
                    if (items.max_allowed_loans) {
                        $('#max_allowed_loans').val(items.max_allowed_loans);
                    }

                    // Collection Days (allowed weekdays)
                    if (items.collection_days) {
                        let allowedDays = [];
                        try {
                            allowedDays = JSON.parse(items.collection_days);
                            if (!Array.isArray(allowedDays)) {
                                allowedDays = [];
                            }
                        } catch (e) {
                            // fallback if somehow stored comma-separated
                            allowedDays = String(items.collection_days)
                                .split(',')
                                .map(v => v.trim())
                                .filter(v => v !== '')
                                .map(v => parseInt(v));
                        }

                        // uncheck all first
                        $('.collection-day').prop('checked', false);

                        // check allowed
                        allowedDays.forEach(d => {
                            $(`.collection-day[value="${d}"]`).prop('checked', true);
                        });
                    }

                    // Document Types
                    if (items.document_types) {
                        try {
                            const documentTypes = JSON.parse(items.document_types);
                            loadDocumentTypesList(documentTypes);
                        } catch (e) {
                            console.error('Error parsing document types:', e);
                            loadDocumentTypesList(getDefaultDocumentTypes());
                        }
                    } else {
                        loadDocumentTypesList(getDefaultDocumentTypes());
                    }

                    // Collector Transaction Modes
                    if (items.collector_txn_modes) {
                        let modes = [];
                        try {
                            // expected to be a JSON array
                            modes = JSON.parse(items.collector_txn_modes);
                            if (!Array.isArray(modes)) modes = [];
                        } catch (e) {
                            // fallback if stored as comma-separated
                            modes = String(items.collector_txn_modes).split(',').map(s => s.trim()).filter(
                                Boolean);
                        }

                        // Uncheck all first, then check the ones present
                        $('.collector-mode').prop('checked', false);
                        modes.forEach(v => $(`.collector-mode[value="${v}"]`).prop('checked', true));
                    }

                    // Loan Creation Restrictions
                    if (items.document_upload_restriction) {
                        $('#document_upload_restriction').val(items.document_upload_restriction);
                    }
                    if (items.guarantees_restriction) {
                        $('#guarantees_restriction').val(items.guarantees_restriction);
                    }
                    if (items.change_product_details) {
                        $('#change_product_details').val(items.change_product_details);
                    }
                    if (items.first_installment_daily) {
                        $('#first_installment_daily').val(items.first_installment_daily);
                    }
                    if (items.first_installment_weekly) {
                        $('#first_installment_weekly').val(items.first_installment_weekly);
                    }
                    if (items.first_installment_monthly) {
                        $('#first_installment_monthly').val(items.first_installment_monthly);
                    }

                    // Head Office Approval toggles
                    $('.approval-toggle').each(function() {
                        const type = $(this).data('type');
                        const key = 'headoffice_approval_' + type;
                        if (items[key]) {
                            $(this).prop('checked', items[key] === 'required');
                        }
                    });
                },
                error: function(xhr) {
                    console.error('Settings load error:', xhr.responseText || xhr.statusText);
                }
            });
        };

        // Save a single setting with confirmation
        const save_setting = (key, value) => {
            Swal.fire({
                title: "Are you sure?",
                text: "Update this setting?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update",
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    type: "POST",
                    url: "/settings/upsert",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        key,
                        value
                    },
                    success: function() {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Setting updated!",
                            timer: 1400,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error", xhr.responseJSON?.message || "Failed to update setting",
                            "error");
                    }
                });
            });
        };

        // Save all loan restriction settings at once
        const saveLoanRestrictions = (documentUpload, guarantees, changeProduct, dailyDays, weeklyDays, monthlyDays) => {
            Swal.fire({
                title: "Are you sure?",
                text: "Update all loan creation restrictions?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update All",
            }).then((result) => {
                if (!result.isConfirmed) return;

                // Prepare all settings to save
                const settings = [{
                        key: 'document_upload_restriction',
                        value: documentUpload
                    },
                    {
                        key: 'guarantees_restriction',
                        value: guarantees
                    },
                    {
                        key: 'change_product_details',
                        value: changeProduct
                    },
                    {
                        key: 'first_installment_daily',
                        value: dailyDays
                    },
                    {
                        key: 'first_installment_weekly',
                        value: weeklyDays
                    },
                    {
                        key: 'first_installment_monthly',
                        value: monthlyDays
                    },
                ];

                let completedCount = 0;
                let hasError = false;

                // Save each setting
                settings.forEach(setting => {
                    $.ajax({
                        type: "POST",
                        url: "/settings/upsert",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        data: setting,
                        success: function() {
                            completedCount++;
                            if (completedCount === settings.length && !hasError) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "All loan restrictions updated!",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        },
                        error: function(xhr) {
                            if (!hasError) {
                                hasError = true;
                                Swal.fire("Error", xhr.responseJSON?.message ||
                                    "Failed to update loan restrictions", "error");
                            }
                        }
                    });
                });
            });
        };

        // ========== DOCUMENT TYPES MANAGEMENT ==========

        // Get default document types
        const getDefaultDocumentTypes = () => {
            return [
                'NIC Copy',
                'Income Certificate'
            ];
        };

        // Load document types list in UI
        const loadDocumentTypesList = (documentTypes) => {
            const container = $('#document_types_list');
            container.empty();

            if (!documentTypes || documentTypes.length === 0) {
                container.html('<p class="text-muted">No document types added yet.</p>');
                return;
            }

            documentTypes.forEach((type, index) => {
                const item = $(`
                    <div class="d-flex align-items-center mb-2 document-type-item" data-index="${index}">
                        <div class="badge bg-light text-dark me-2 flex-grow-1 text-start py-2 px-3">
                            ${type}
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-doc-type" data-index="${index}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                `);
                container.append(item);
            });

            // Attach remove handlers
            $('.remove-doc-type').on('click', function() {
                const index = $(this).data('index');
                removeDocumentType(index);
            });
        };

        // Add new document type
        const addDocumentType = () => {
            const newType = $('#new_document_type').val().trim();

            if (!newType) {
                Swal.fire("Warning", "Please enter a document type.", "warning");
                return;
            }

            // Get current types
            const currentTypes = getCurrentDocumentTypes();

            // Check for duplicates (case insensitive)
            if (currentTypes.some(type => type.toLowerCase() === newType.toLowerCase())) {
                Swal.fire("Warning", "This document type already exists.", "warning");
                return;
            }

            // Add new type
            currentTypes.push(newType);
            loadDocumentTypesList(currentTypes);

            // Clear input
            $('#new_document_type').val('');
        };

        // Remove document type
        const removeDocumentType = (index) => {
            const currentTypes = getCurrentDocumentTypes();
            currentTypes.splice(index, 1);
            loadDocumentTypesList(currentTypes);
        };

        // Get current document types from UI
        const getCurrentDocumentTypes = () => {
            const types = [];
            $('.document-type-item').each(function() {
                const type = $(this).find('.badge').text().trim();
                if (type) {
                    types.push(type);
                }
            });
            return types;
        };

        // Save document types to database
        const saveDocumentTypes = () => {
            const documentTypes = getCurrentDocumentTypes();

            if (documentTypes.length === 0) {
                Swal.fire("Warning", "Please add at least one document type.", "warning");
                return;
            }

            const jsonValue = JSON.stringify(documentTypes);

            Swal.fire({
                title: "Are you sure?",
                text: "Update document types?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, Update",
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    type: "POST",
                    url: "/settings/upsert",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: {
                        key: 'document_types',
                        value: jsonValue
                    },
                    success: function() {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Document types updated!",
                            timer: 1400,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire("Error", xhr.responseJSON?.message ||
                            "Failed to update document types", "error");
                    }
                });
            });
        };
    </script>
@endsection

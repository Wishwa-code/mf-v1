@extends('layout.admin')

@section('head')
    <style>
        .main-topic {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .sub-topic {
            background-color: #ffffff;
            padding-left: 40px;
        }

        .sub-topic i {
            color: #ffc107;
        }

        .sub-topic td:first-child {
            display: flex;
            align-items: center;
        }

        .permission-table th, .permission-table td {
            vertical-align: middle;
        }

        .h6 {
            margin: 0;
        }

        .permission-wrapper {
            background: #ffffff;
            border-radius: 0.5rem;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            width: 100%;
        }

        .permission-container {
            max-width: 100%;
            padding: 1rem 2rem;
        }
    </style>
@endsection

@section('content')
    <div class="permission-container">
        <div class="permission-wrapper">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Designation Privileges</h2>
                <div>
                    <button type="button" class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#create-modal">Create Designation</button>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#view-designations-modal">View All Designations</button>
                </div>
            </div>
            <form id="updatePermissionForm">
                <div class="mb-4">
                    <label for="userid" class="form-label">Select Designation</label>
                    <select class="form-control select2bs4" id="userid" name="userid" onchange="load_to_table(this.value); showDesignationOverview(this)">
                        <option value="0">-- Select Designation --</option>
                        @php
                            $branch = session('branch_id');
                            $designation_details = DB::select("SELECT * FROM designation WHERE branch_id = ?", [$branch]);
                        @endphp
                        @foreach ($designation_details as $designation)
                            <option value="{{ $designation->idDesignation }}" 
                                    data-name="{{ $designation->name }}" 
                                    data-max-create="{{ number_format($designation->max_create_amount, 2) }}" 
                                    data-max-approve="{{ number_format($designation->max_issue_amount, 2) }}">
                                {{ $designation->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input main-checkbox" id="full_permission" type="checkbox">
                    <label class="form-check-label ms-2" for="full_permission">Grant Full Access</label>
                </div>
                <label class="form-check-label ms-2" for="full_permission" style="color: red">
                    Want to update sub-topic access? Just click on the related topic checkbox.
                </label>
                <br><br>
                
                <!-- Current Designation Overview Table -->
                <div class="table-responsive mb-4" id="designation-overview-container" style="display: none;">
                    <table class="table table-bordered table-hover">
                        <thead class="table-secondary">
                        <tr>
                            <th class="text-center">Designation</th>
                            <th class="text-center">Max for Create Loan</th>
                            <th class="text-center">Max for Approve Loan</th>
                            <th class="text-center">Change Status</th>
                        </tr>
                        </thead>
                        <tbody id="designation-overview">
                            <!-- Content will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover permission-table w-100">
                        <thead class="table-dark">
                        <tr>
                            <th style="width: 90%;">Module Permission</th>
                            <th class="text-center" style="width: 10%;">Access</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $mainPermissions = [
                                'Customer' => [
                                    'add_customer', 'view_customer', 'view_blacklist_customer',
                                    'customer_saving_acc', 'kyc', 'insurance'
                                ],
                                'Loan Center' => [
                                    'create_route', 'create_center', 'view_center',
                                    'create_group', 'view_group', 'add_customer_to_group'
                                ],
                                'Guarantee' => ['add_guarantee', 'view_guarantee'],
                                'Product' => [
                                    'add_product', 'view_product', 'create_loan',
                                    'change_collector', 'pending_loan', 'loan_disbursement',
                                    'current_loans', 'settled_loans'
                                ],
                                'Payment Details' => [
                                    'add_repayment', 'bulk_repayment', 'loan_settlement',
                                    'loan_reschedule', 'view_payment', 'collector_wise_collection'
                                ],
                                'Account Center' => [
                                    'bank_cash_account', 'internal_bank_transfer',
                                    'collector_account', 'cheque_details'
                                ],
                                'Account Department' => [
                                    'add_asset', 'asset_management', 'bank_reconciliation',
                                    'manual_journal', 'chart_of_account'
                                ],
                                'Loan Calculator' => ['loan_calculator'],
                                'Calender' => ['calendar'],
                                'Expenses' => ['add_expenses', 'view_expenses'],
                                'User' => ['create_user', 'user_privileges'],
                                'Reports' => [
                                    'main_reports_dashboard', 'loan_disbursement_performance', 'payment_detail_report','prediction_report',
                                    'full_loan_detail', 'loan_summary', 'par_monthly', 'par_weekly', 'loan_status',
                                    'cashflow_accumulated', 'cashflow_monthly', 'profit_loss', 'balance_sheet', 'trial_balance',
                                    'daily_collection_sheet', 'center_collection_detail', 'center_collection_summary', 'route_collections',
                                    'repayment_sheet_01', 'repayment_sheet_02', 'repayment_sheet_03', 'repayment_sheet_04','repayment_sheet_05','repayment_sheet_06','repayment_sheet_07','repayment_sheet_08',
                                    'other_charges_report', 'center_dashboard', 'repayment_summary', 'savings_report',
                                    'arrears_report', 'arrears_overview', 'datewise_cashflow', 'loan_detail_report',
                                    'collector_report', 'sms_history', 'customer_detail_report',
                                    'officer_customer_detail', 'guardian_detail_report'
                                ]
                            ];

                            $settingsPermissions = [
                                'Settings Privilege' => [
                                    'my_account', 'settings', 'sms_format', 'document_format',
                                    'company_holidays', 'branches', 'cashier_start', 'cashier_close'
                                ]
                            ];

                            $deletePermissions = [
                                'Access' => ['dashboard','payment_delete','current_loan_delete','loan_extra_charges','current_loan_agreement','branch_access','collector_access','cashier_access']
                            ];
                        @endphp

                        @foreach ($mainPermissions as $main => $subs)
                            <tr class="main-topic">
                                <td class="align-middle" colspan="2">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <i class="ti ti-folder h4 text-primary me-2"></i>
                                            <span class="h6">{{ $main }}</span>
                                        </div>
                                        <input type="checkbox" class="form-check-input access_module" data-key="{{ strtolower(str_replace(' ', '_', $main)) }}">
                                    </div>
                                </td>
                            </tr>
                        <tbody class="sub-topic-wrapper" data-wrapper="{{ strtolower(str_replace(' ', '_', $main)) }}" style="display: none;">
                        @foreach ($subs as $sub)
                            <tr class="sub-topic">
                                <td>
                                    <i class="ti ti-star me-2"></i>
                                    <span class="h6">{{ ucwords(str_replace('_', ' ', $sub)) }}</span>
                                </td>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input access_module" data-key="{{ $sub }}">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        @endforeach

                        </tbody>
                    </table>
                </div>

                <hr class="my-4">
                <h4 class="mb-3">Settings Privilege</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                        @foreach ($settingsPermissions['Settings Privilege'] as $setting)
                            <tr>
                                <td class="ps-4">{{ ucwords(str_replace('_', ' ', $setting)) }}</td>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input access_module" data-key="{{ $setting }}">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <hr class="my-4">
                <h4 class="mb-3">Access</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                        @foreach ($deletePermissions['Access'] as $delete)
                            <tr>
                                <td class="ps-4">{{ ucwords(str_replace('_', ' ', $delete)) }}</td>
                                <td class="text-center">
                                    <input type="checkbox" class="form-check-input access_module" data-key="{{ $delete }}">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-center mt-4">
                    <div class="form-check d-inline-flex align-items-center me-3">
                        <input class="form-check-input" type="checkbox" id="propagate-users">
                        <label class="form-check-label ms-2" for="propagate-users">Also update existing users in this designation</label>
                    </div>
                    <button class="btn btn-primary btn-lg px-5" type="button" onclick="savePrivileges(event)">Update Designation Privileges</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Designation Modal -->
    <div class="modal fade" id="create-modal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>Create New Designation</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="new_designation" class="form-label">Designation<span class="required-asterisk">*</span></label>
                                        <input type="text" id="new_designation" class="form-control" placeholder="Enter designation name">
                                    </div>
                                    <div class="col-md-6 mb-3" hidden>
                                        <label for="new_desi_level" class="form-label">Designation Level<span class="required-asterisk">*</span></label>
                                        <select class="form-control" id="new_desi_level">
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                            <option>6</option>
                                            <option>7</option>
                                            <option>8</option>
                                            <option>9</option>
                                            <option>10</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row" hidden>
                                    <div class="col-md-6 mb-3">
                                        <label for="new_loan_create" class="form-label">Loan Create (Issue Loan)<span class="required-asterisk">*</span></label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="new_loan_create">
                                            <label class="form-check-label" for="new_loan_create">Allow</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="new_loan_approve" class="form-label">Loan Approve<span class="required-asterisk">*</span></label>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="new_loan_approve">
                                            <label class="form-check-label" for="new_loan_approve">Allow</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="new_max_amount_create" class="form-label">Maximum Amount for Create Loan<span class="required-asterisk">*</span></label>
                                        <input type="text" id="new_max_amount_create" class="form-control" placeholder="0.00">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="new_max_amount_approve" class="form-label">Maximum Amount for Approve Loan<span class="required-asterisk">*</span></label>
                                        <input type="text" id="new_max_amount_approve" class="form-control" placeholder="0.00">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" onclick="createNewDesignation()">Create Designation</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View All Designations Modal -->
    <div class="modal fade" id="view-designations-modal" tabindex="-1" role="dialog" aria-labelledby="viewDesignationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h4>View All Designations</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card card-shadow-new border-primary">
                        <div class="card-body less-padding">
                            <div class="table-responsive">
                                <table id="designationViewTable" class="table table-bordered dash-table dash-table-d table-hover">
                                    <thead>
                                    <tr>
                                        <th class="text-center">Designation</th>
                                        <th class="text-center">Max for Create Loan</th>
                                        <th class="text-center">Max for Approve Loan</th>
                                        <th class="text-center">Delete</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $branch = session('branch_id');
                                        $designation_list = DB::select("SELECT * FROM designation WHERE branch_id = ?", [$branch]);
                                    @endphp
                                    @foreach ($designation_list as $item)
                                        <tr>
                                            <td class="align-middle text-center">{{ $item->name }}</td>
                                            <td class="align-middle text-center">{{ number_format($item->max_create_amount, 2) }}</td>
                                            <td class="align-middle text-center">{{ number_format($item->max_issue_amount, 2) }}</td>
                                            <td class="align-middle text-center">
                                                <button type="button" class="btn btn-danger btn-sm btn-delete-designation" data-id="{{ $item->idDesignation }}" data-name="{{ $item->name }}">Delete</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="../JS/validate.js"></script>
    <!-- Page specific privileges handling (designation JSON) -->
    <script src="../JS/designation_privileges.js?n=1"></script>
    <script>
        $(document).ready(function () {
            // Toggle all when "Full Access" is checked
            $('.main-checkbox').change(function () {
                var isChecked = $(this).prop('checked');
                $('.access_module').prop('checked', isChecked);
                $('.sub-topic-wrapper').toggle(isChecked);
            });

            // Toggle each sub-topic group when main topic is checked (only for user interactions, not programmatic)
            $('.access_module').on('change', function (e) {
                // Skip if this change was triggered programmatically during load
                if (e.originalEvent === undefined) return;
                
                var key = $(this).data('key');
                var $target = $(`.sub-topic-wrapper[data-wrapper="${key}"]`);
                if ($target.length) {
                    if ($(this).is(':checked')) {
                        $target.slideDown();
                    } else {
                        $target.slideUp();
                        // Also uncheck all sub-permissions when main permission is unchecked
                        $target.find('.access_module').prop('checked', false);
                    }
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const collectorCheckbox = document.querySelector('input[data-key="collector_access"]');
            const cashierCheckbox   = document.querySelector('input[data-key="cashier_access"]');

            if (collectorCheckbox && cashierCheckbox) {
                collectorCheckbox.addEventListener("change", function () {
                    if (this.checked) {
                        cashierCheckbox.checked = false;
                    }
                });

                cashierCheckbox.addEventListener("change", function () {
                    if (this.checked) {
                        collectorCheckbox.checked = false;
                    }
                });
            }
        });
    </script>

    <script>
        $(document).ready(function() {
            let x = ["#new_max_amount_create","#new_max_amount_approve"];
            if (typeof decimalFormat === 'function') {
                decimalFormat(x);
            }

            // Handle designation deletion
            $('.btn-delete-designation').click(function() {
                var designationId = $(this).data('id');
                var designationName = $(this).data('name');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you want to delete "${designationName}" designation? This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: 'POST',
                            url: '/designation/delete',
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            data: {
                                id: designationId
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Successfully deleted!",
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                } else {
                                    Swal.fire('Error', response.message || 'Failed to delete designation', 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error deleting designation:', error);
                                let errorMessage = 'Something went wrong!';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', errorMessage, 'error');
                            }
                        });
                    }
                });
            });

            $('.btn-update').click(function() {
                var itemId = $(this).data('item-id');
                var designation = $(`#designationTable input[data-item-id="${itemId}"]`).val();
                var desiLevel = $(`#designationTable select[data-item-id="${itemId}"]`).val();
                var loanCreate = $(`#loan_create_${itemId}`).prop('checked') ? 1 : 0;
                var loanApprove = $(`#loan_approve_${itemId}`).prop('checked') ? 1 : 0;
                var maxCreateAmount = $(`#designationTable .max-create-amount[data-item-id="${itemId}"]`).val();
                var maxIssueAmount = $(`#designationTable .max-issue-amount[data-item-id="${itemId}"]`).val();

                if(itemId==="" || designation==="" || desiLevel==="" || maxCreateAmount==="" || maxIssueAmount===""){
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please fill all required fields!'
                    })
                }else{
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to save this designation?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Update it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                method: 'POST',
                                url: '/update-designation',
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                                },
                                data: {
                                    id: itemId,
                                    designation: designation,
                                    desiLevel: desiLevel,
                                    loanCreate: loanCreate,
                                    loanApprove: loanApprove,
                                    maxCreateAmount: maxCreateAmount,
                                    maxIssueAmount: maxIssueAmount
                                },
                                success: function(response) {
                                    Swal.fire({
                                        position: "center",
                                        icon: "success",
                                        title: "Successfully updated!",
                                    }).then(function () {
                                        window.location.reload();
                                    });
                                },
                                error: function(xhr, status, error) {
                                    console.error('Error updating data:', error);
                                    Swal.fire('Error', 'Something went wrong!', 'error');
                                }
                            });
                        }
                    });
                }
            });
        });

        // Function to create new designation
        function createNewDesignation() {
            var designation = $('#new_designation').val();
            var desiLevel = $('#new_desi_level').val();
            var loanCreate = $('#new_loan_create').prop('checked') ? 1 : 0;
            var loanApprove = $('#new_loan_approve').prop('checked') ? 1 : 0;
            var maxCreateAmount = $('#new_max_amount_create').val();
            var maxIssueAmount = $('#new_max_amount_approve').val();

            if(designation==="" || maxCreateAmount==="" || maxIssueAmount===""){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please fill all required fields!'
                })
            } else {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to create this designation?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Create it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: 'POST',
                            url: '/user/designation',
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            data: {
                                designation: designation,
                                desi_level: desiLevel,
                                loan_create: loanCreate,
                                loan_approve: loanApprove,
                                max_amount_create: maxCreateAmount,
                                max_amount_approve: maxIssueAmount
                            },
                            success: function(response) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully created!",
                                }).then(function () {
                                    $('#create-modal').modal('hide');
                                    clearCreateForm();
                                    window.location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Error creating designation:', error);
                                Swal.fire('Error', 'Something went wrong!', 'error');
                            }
                        });
                    }
                });
            }
        }

        // Function to clear create form
        function clearCreateForm() {
            $('#new_designation').val('');
            $('#new_desi_level').val('1');
            $('#new_loan_create').prop('checked', false);
            $('#new_loan_approve').prop('checked', false);
            $('#new_max_amount_create').val('');
            $('#new_max_amount_approve').val('');
        }

        // Clear form when modal is closed
        $('#create-modal').on('hidden.bs.modal', function () {
            clearCreateForm();
        });

        // Function to show designation overview
        function showDesignationOverview(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const overviewContainer = document.getElementById('designation-overview-container');
            const overviewBody = document.getElementById('designation-overview');
            
            if (selectElement.value == "0") {
                overviewContainer.style.display = 'none';
            } else {
                const designationId = selectElement.value;
                const name = selectedOption.getAttribute('data-name');
                const maxCreate = selectedOption.getAttribute('data-max-create').replace(/,/g, '');
                const maxApprove = selectedOption.getAttribute('data-max-approve').replace(/,/g, '');
                
                overviewBody.innerHTML = `
                    <tr>
                        <td class="text-center">
                            <input type="text" class="form-control text-center" 
                                   id="overview-name" value="${name}" data-id="${designationId}">
                        </td>
                        <td class="text-center">
                            <input type="text" class="form-control text-center" 
                                   id="overview-max-create" value="${maxCreate}" data-id="${designationId}">
                        </td>
                        <td class="text-center">
                            <input type="text" class="form-control text-center" 
                                   id="overview-max-approve" value="${maxApprove}" data-id="${designationId}">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-primary btn-sm" 
                                    onclick="updateDesignationFromOverview(${designationId})">Update</button>
                        </td>
                    </tr>
                `;
                
                // Apply decimal formatting to the amount fields
                if (typeof decimalFormat === 'function') {
                    decimalFormat(['#overview-max-create', '#overview-max-approve']);
                }
                
                overviewContainer.style.display = 'block';
            }
        }

        // Function to update designation from overview table
        function updateDesignationFromOverview(designationId) {
            var designation = $('#overview-name').val();
            var maxCreateAmount = $('#overview-max-create').val();
            var maxIssueAmount = $('#overview-max-approve').val();

            if(designation==="" || maxCreateAmount==="" || maxIssueAmount===""){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please fill all required fields!'
                })
            } else {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to update this designation?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Update it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            method: 'POST',
                            url: '/update-designation',
                            headers: {
                                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                            },
                            data: {
                                id: designationId,
                                designation: designation,
                                desiLevel: 1, // Default value
                                loanCreate: 1, // Default value
                                loanApprove: 1, // Default value
                                maxCreateAmount: maxCreateAmount,
                                maxIssueAmount: maxIssueAmount
                            },
                            success: function(response) {
                                Swal.fire({
                                    position: "center",
                                    icon: "success",
                                    title: "Successfully updated!",
                                }).then(function () {
                                    // Update the dropdown option data
                                    const option = $(`#userid option[value="${designationId}"]`);
                                    option.attr('data-name', designation);
                                    option.attr('data-max-create', parseFloat(maxCreateAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                                    option.attr('data-max-approve', parseFloat(maxIssueAmount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                                    option.text(designation);
                                    
                                    // Refresh the overview display
                                    showDesignationOverview(document.getElementById('userid'));
                                });
                            },
                            error: function(xhr, status, error) {
                                console.error('Error updating data:', error);
                                Swal.fire('Error', 'Something went wrong!', 'error');
                            }
                        });
                    }
                });
            }
        }
    </script>

@endsection

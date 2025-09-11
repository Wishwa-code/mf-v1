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
            <h2 class="mb-4">Designation Privileges</h2>
            <form id="updatePermissionForm">
                <div class="mb-4">
                    <label for="userid" class="form-label">Select Designation</label>
                    <select class="form-control select2bs4" id="userid" name="userid" onchange="load_to_table(this.value)">
                        <option value="0">-- Select User --</option>
                        @php
                            $branch = session('branch_id');
                            $user_details = DB::select("SELECT * FROM user WHERE Status='1' AND branch_id = ?", [$branch]);
                        @endphp
                        @foreach ($user_details as $user)
                            <option value="{{ $user->id }}">{{ $user->Full_Name }}</option>
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
                    <button class="btn btn-primary btn-lg px-5" type="button" onclick="savePrivileges(event)">Update Designation Privileges</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script src="../JS/validate.js"></script>
    <script src="../JS/privilages.js?n=50"></script>
    <script>
        $(document).ready(function () {
            // Toggle all when "Full Access" is checked
            $('.main-checkbox').change(function () {
                var isChecked = $(this).prop('checked');
                $('.access_module').prop('checked', isChecked);
                $('.sub-topic-wrapper').toggle(isChecked);
            });

            // Toggle each sub-topic group when main topic is checked
            $('.access_module').change(function () {
                var key = $(this).data('key');
                var $target = $(`.sub-topic-wrapper[data-wrapper="${key}"]`);
                if ($target.length) {
                    if ($(this).is(':checked')) {
                        $target.slideDown();
                    } else {
                        $target.slideUp();
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

@endsection

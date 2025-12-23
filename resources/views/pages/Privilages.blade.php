@extends('layout.admin')

@section('head')
<style>
    .privilege-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .privilege-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }

    /* Custom Card Header to check responsiveness */
    .card-header-custom {
        background: transparent;
        border-bottom: 1px solid #f1f5f9;
        padding: 1rem 1.25rem;
        /* Reduced padding */
        min-height: 60px;
        /* Reduced height */
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .module-icon {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        margin-right: 12px;
        transition: all 0.3s ease;
    }

    .privilege-card:hover .module-icon {
        background: #0d6efd;
        color: #ffffff;
    }

    .sub-topic-wrapper {
        display: none;
    }

    .sub-permission-item {
        padding: 8px 12px;
        border-radius: 6px;
        transition: background 0.2s;
        margin-bottom: 4px;
    }

    .sub-permission-item:hover {
        background: #f8fafc;
    }

    .page-header {
        background: transparent;
        margin-bottom: 2rem;
    }

    .user-selector-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Checkbox Styling */
    .form-check-input {
        cursor: pointer;
        width: 1.1em;
        height: 1.1em;
    }

    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .section-divider {
        position: relative;
        text-align: center;
        margin: 3rem 0;
    }

    .section-divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 1px;
        background: #e2e8f0;
        z-index: 1;
    }

    .section-divider span {
        position: relative;
        z-index: 2;
        background: #f1f5f9;
        /* Matches background */
        padding: 0 1rem;
        color: #64748b;
        font-weight: 500;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .last-border-0:last-child {
        border-bottom: 0 !important;
    }

    /* Floating Save Button */
    .floating-action-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-top: 1px solid #e2e8f0;
        padding: 1rem;
        z-index: 1050;
        box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.05);
        transform: translateY(100%);
        transition: transform 0.3s ease;
    }

    @media (min-width: 768px) {
        .floating-action-bar {
            left: 260px;
            /* Adjust based on sidebar width */
        }
    }

    .floating-action-bar.visible {
        transform: translateY(0);
    }

    /* Select2 Customization matching Lead Create */
    .select2-container--default .select2-selection--single {
        height: 50px;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        display: flex;
        align-items: center;
        background-color: #f8f9fa;
    }

    .select2-container--default .select2-selection--single:focus-within {
        border-color: #556ee6;
        box-shadow: 0 0 0 0.25rem rgba(85, 110, 230, 0.1);
        background-color: #fff;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 50px;
        right: 10px;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 15px;
        font-size: 0.95rem;
        color: #495057;
        font-weight: 500;
    }

    .select2-dropdown {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        margin-top: 8px;
    }

    .select2-search__field {
        border-radius: 8px !important;
        padding: 8px 12px !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-dark mb-1">Privilege Management</h2>
            <p class="text-muted mb-0">Manage user access and system permissions</p>
        </div>
    </div>

    <form id="updatePermissionForm" class="mb-5 pb-5">

        <!-- User Selection Card -->
        <div class="user-selector-card p-4 mb-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-5">
                    <label for="userid" class="form-label fw-bold small text-uppercase text-secondary mb-2">Select Team Member</label>
                    <select class="form-control select2bs4 shadow-none" id="userid" name="userid" onchange="load_to_table(this.value)" style="width: 100%;">
                        <option value="0">-- Search & Select User --</option>
                        @php
                        $branch = session('branch_id');
                        $user_details = DB::select("SELECT * FROM user WHERE Status='1' AND branch_id = ?", [$branch]);
                        @endphp
                        @foreach ($user_details as $user)
                        <option value="{{ $user->id }}">{{ $user->Full_Name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-7">
                    <div class="d-flex align-items-center justify-content-lg-end bg-light p-3 rounded-3 border border-light-subtle">
                        <div class="me-4">
                            <span class="d-block fw-bold text-dark">Grant Full Access</span>
                            <span class="d-block text-muted small">Overrides all specific permissions</span>
                        </div>
                        <div class="form-check form-switch ps-0">
                            <input class="form-check-input ms-0 main-checkbox" id="full_permission" type="checkbox" style="width: 3em; height: 1.5em; margin-left: 0;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permission Config -->
        @php
        $mainPermissions = [
        'Customer' => [
        'add_customer', 'view_customer', 'view_blacklist_customer',
        'customer_saving_acc', 'kyc', 'insurance'
        ],
        'Customer Leads' => [
        'view_leads', 'create_lead', 'view_lead_map',
        'verify_lead', 'lead_agreement',
        'lead_activity_logs', 'lead_approvals','bussiness_categories'
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

        $icons = [
        'Customer' => 'user',
        'Customer Leads' => 'users',
        'Loan Center' => 'building-bank',
        'Guarantee' => 'shield-check',
        'Product' => 'package',
        'Payment Details' => 'cash',
        'Account Center' => 'wallet',
        'Account Department' => 'file-invoice',
        'Loan Calculator' => 'calculator',
        'Calender' => 'calendar',
        'Expenses' => 'receipt',
        'User' => 'user-cog',
        'Reports' => 'chart-bar'
        ];
        @endphp

        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mb-5">
            @foreach ($mainPermissions as $main => $subs)
            @php $key = strtolower(str_replace(' ', '_', $main)); @endphp
            <div class="col">
                <div class="card privilege-card">
                    <div class="card-header-custom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center text-truncate">
                            <div class="module-icon flex-shrink-0">
                                <i class="ti ti-{{ $icons[$main] ?? 'folder' }} fs-5"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark text-truncate" title="{{ $main }}">{{ $main }}</h6>
                        </div>
                        <div class="form-check m-0">
                            <input type="checkbox" class="form-check-input access_module" data-key="{{ $key }}">
                        </div>
                    </div>
                    <!-- Helper Text shown when collapsed -->
                    <div class="p-3 text-center text-muted small bg-light bg-opacity-50" onclick="$(this).prev().find('.access_module').click()" style="cursor: pointer;">
                        Click input to reveal {{ count($subs) }} permissions
                    </div>

                    <!-- Content Wrapper -->
                    <div class="card-body sub-topic-wrapper pt-0" data-wrapper="{{ $key }}">
                        <hr class="mt-0 mb-3 border-light">
                        <div class="d-flex flex-column gap-1">
                            @foreach ($subs as $sub)
                            <div class="sub-permission-item d-flex align-items-center justify-content-between">
                                <label class="form-check-label text-secondary mb-0 w-100 cursor-pointer small" for="chk_{{ $sub }}">
                                    {{ ucwords(str_replace('_', ' ', $sub)) }}
                                </label>
                                <input type="checkbox" id="chk_{{ $sub }}" class="form-check-input access_module" data-key="{{ $sub }}">
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="section-divider">
            <span>Advanced Settings</span>
        </div>

        <div class="row g-4">
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="card privilege-card h-100">
                    <div class="card-header-custom bg-light">
                        <h6 class="fw-bold mb-0 text-dark">Settings Privilege</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($settingsPermissions['Settings Privilege'] as $setting)
                        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between last-border-0 hover-bg-light">
                            <span class="text-secondary small fw-medium">{{ ucwords(str_replace('_', ' ', $setting)) }}</span>
                            <input type="checkbox" class="form-check-input access_module" data-key="{{ $setting }}">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="card privilege-card h-100">
                    <div class="card-header-custom bg-danger bg-opacity-10 border-danger border-opacity-10">
                        <h6 class="fw-bold mb-0 text-danger">Critical Access Control</h6>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($deletePermissions['Access'] as $delete)
                        <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between last-border-0 hover-bg-light">
                            <span class="text-danger small fw-medium">{{ ucwords(str_replace('_', ' ', $delete)) }}</span>
                            <input type="checkbox" class="form-check-input access_module" data-key="{{ $delete }}">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Floating Save Bar -->


    </form>
    <div class="floating-action-bar visible d-flex justify-content-between align-items-center">
        <div class="text-muted small ps-3">
            <i class="ti ti-info-circle me-1"></i> Changes will apply effectively immediately after save.
        </div>
        <button class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" type="button" onclick="savePrivileges(event)">
            <i class="ti ti-device-floppy me-2"></i>Update Privileges
        </button>
    </div>
</div>
@endsection

@section('script')
<script src="../JS/validate.js"></script>
<script src="../JS/privilages.js?n=50"></script>
<script>
    $(document).ready(function() {
        // Enhanced Toggle for Card Layout
        $('.access_module').change(function() {
            var key = $(this).data('key');
            var $target = $(`.sub-topic-wrapper[data-wrapper="${key}"]`);

            // Also toggle the helper text
            var $helper = $(this).closest('.card-header-custom').next('div');

            if ($target.length) {
                if ($(this).is(':checked')) {
                    $target.slideDown(300);
                    if ($helper.length) $helper.slideUp(200);
                } else {
                    $target.slideUp(300);
                    if ($helper.length) $helper.slideDown(200);
                }
            }
        });

        // Initialize select2
        $('.select2bs4').select2({
            width: '100%',
            dropdownParent: $('.user-selector-card') // Ensure dropdown is attached correctly if needed, generally optional but good practice
        });

        // Full Access Toggle
        $('.main-checkbox').change(function() {
            var isChecked = $(this).prop('checked');
            $('.access_module').prop('checked', isChecked);

            // Manually trigger change to animate
            // We use .each to avoid a massive concurrent animation if possible, or just force show
            if (isChecked) {
                $('.sub-topic-wrapper').slideDown();
                $('.card-header-custom').next('div').slideUp(); // hide helpers
            } else {
                $('.sub-topic-wrapper').slideUp();
                $('.card-header-custom').next('div').slideDown(); // show helpers
            }
        });
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const collectorCheckbox = document.querySelector('input[data-key="collector_access"]');
        const cashierCheckbox = document.querySelector('input[data-key="cashier_access"]');

        if (collectorCheckbox && cashierCheckbox) {
            collectorCheckbox.addEventListener("change", function() {
                if (this.checked) {
                    cashierCheckbox.checked = false;
                }
            });

            cashierCheckbox.addEventListener("change", function() {
                if (this.checked) {
                    collectorCheckbox.checked = false;
                }
            });
        }
    });
</script>
@endsection
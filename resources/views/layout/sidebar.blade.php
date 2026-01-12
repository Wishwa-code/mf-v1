<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="/" class="logo logo-light">
        @if ($logo)
        <img src="{{ $logo }}" class="logo-img rounded-logo">
        @else
        <img src="https://accountcenter.asipbook.com/asipiya.svg" class="logo-img rounded-logo">
        @endif
    </a>

    <!-- Brand Logo Dark -->
    <a href="/" class="logo logo-dark">
        @if ($logo)
        <img src="{{ $logo }}" class="logo-img rounded-logo">
        @else
        <img src="https://accountcenter.asipbook.com/asipiya.svg" class="logo-img rounded-logo">
        @endif
    </a>

    <!-- Mobile Close Button -->
    <a href="javascript:void(0);" class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </a>
    <!-- Sidebar -left -->

    <div class="h-100" id="leftside-menu-container" data-simplebar>

        <!--- Sidemenu -->
        <ul class="side-nav">

            <li class="side-nav-title" style="text-color: red">{{ user_data()['company']['Company_Name'] ?? '' }}</li>

            @php $isHeadOffice = session('head_branch') == session('branch_id'); @endphp

            @if ($isHeadOffice)
            {{-- Head Office restricted menu: Dashboard, View Customer, KYC, View Center --}}

            @hasPrivilege('DASHBOARD')
            <li class="side-nav-item mt-2">
                <a href="/" class="side-nav-link" data-tooltip="Dashboard">
                    <i class="ri-dashboard-3-line"></i>
                    <span> Dashboard </span>
                </a>
            </li>
            @endhasPrivilege

            @hasPrivilege('CUSTOMER')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#customer" aria-expanded="false" aria-controls="customer"
                    class="side-nav-link" data-tooltip="Customer">
                    <i class="ri-group-2-line"></i>
                    <span> Customer </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="customer">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('VIEW_CUSTOMER')
                        <li class="mt-2"><a href="/showcustomers">View Customer</a></li>
                        @endhasPrivilege
                        @hasPrivilege('KYC')
                        <li class="mt-2"><a href="/kyc">KYC</a></li>
                        @endhasPrivilege
                    </ul>
                </div>
            </li>
            @endhasPrivilege

            @hasPrivilege('LOAN_CENTER')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#center" aria-expanded="false" aria-controls="center"
                    class="side-nav-link" data-tooltip="Loan Center">
                    <i class="bi bi-building"></i>
                    <span> Loan Center </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="center">
                    <ul class="side-nav-second-level">
                        <li class="mt-2"><a href="/viewcenter">View Center</a></li>
                    </ul>
                </div>
            </li>
            @endhasPrivilege

            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#approval" aria-expanded="false" aria-controls="approval"
                    class="side-nav-link" data-tooltip="Approval Requests">
                    <i class="ri-check-double-line"></i>
                    <span> Approvals </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="approval">
                    <ul class="side-nav-second-level">
                        <li class="mt-2"><a href="/pending_approval">Pending Approvals</a></li>
                        <li class="mt-2"><a href="/approved_history">Approval History</a></li>
                        <li class="mt-2"><a href="/rejected_approval">Rejected History</a></li>
                    </ul>
                </div>
            </li>

            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#payment_voucher" aria-expanded="false"
                    aria-controls="payment_voucher" class="side-nav-link" data-tooltip="Payment Voucher Module">
                    <i class="ri-file-list-3-line"></i>
                    <span> Payment Voucher Module </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="payment_voucher">
                    <ul class="side-nav-second-level">
                        <li class="mt-2">
                            <a href="/VoucherDashboard">Voucher Dashboard</a>
                        </li>
                        <li class="mt-2">
                            <a href="/PendingVouchers">Pending Vouchers</a>
                        </li>
                        <li class="mt-2">
                            <a href="/SupplierRegistration">Supplier Registration</a>
                        </li>
                    </ul>
                </div>
            </li>

            @hasPrivilege('EXPENSES')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#expences" aria-expanded="false" aria-controls="expences"
                    class="side-nav-link" data-tooltip="Expenses">
                    <i class="ri-briefcase-line"></i>
                    <span> Expenses </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="expences">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_EXPENSES')
                        <li class="mt-2">
                            <a href="/expenses">Add Expenses</a>
                        </li>
                        @endhasPrivilege
                    </ul>
                </div>
            </li>
            @endhasPrivilege
            @else

            @hasPrivilege('DASHBOARD')
            <li class="side-nav-item mt-2">
                <a href="/" class="side-nav-link" data-tooltip="Dashboard">
                    <i class="ri-dashboard-3-line"></i>
                    <span> Dashboard </span>
                </a>
            </li>
            @endhasPrivilege

            {{-- @hasPrivilege('CUSTOMER') --}}
            {{-- CUSTOMER LEADS SECTION --}}
            @hasPrivilege('CUSTOMER_LEADS')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#lead" aria-expanded="false"
                    aria-controls="sidebarPagesAuth" class="side-nav-link " data-tooltip="Lead">
                    <i class="ri-article-fill"></i>
                    <span> Lead </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="lead">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('BUSSINESS_CATEGORIES')
                        <li class="mt-2">
                            <a href="{{ route('business-categories.index') }}">
                                Business Categories
                            </a>
                        </li>
                        @endhasPrivilege

                        @hasPrivilege('CREATE_LEAD')
                        <li class="mt-2">
                            <a href="{{ route('leads.index') }}">Lead Create</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CREATE_ONLINE_LEAD')
                        <li class="mt-2">
                            <a href="{{ route('online-leads.create') }}">Create Online Lead</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LEAD_APPROVALS')
                        <li class="mt-2">
                            <a href="{{ route('leads.approvals') }}">Lead Approvals</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VERIFY_LEAD')
                        <li class="mt-2">
                            <a href="{{ route('leads.verifyAction') }}">Lead Verification</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LEAD_AGREEMENT')
                        <li class="mt-2">
                            <a href="{{ route('leads.agreement') }}">Agreement Sign</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_LEAD_MAP')
                        <li class="mt-2">
                            <a href="{{ route('leads.globalMap') }}">All Leads Map</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LEAD_ACTIVITY_LOGS')
                        <li class="mt-2">
                            <a href="{{ route('leads.activityLogs') }}">Activity Logs</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_LEADS')
                        <li class="mt-2">
                            <a href="{{ route('leads.verifiedList') }}">Lead List</a>
                        </li>
                        @endhasPrivilege

                    </ul>
                </div>
            </li>
            @endhasPrivilege


            @hasPrivilege('CUSTOMER')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#customer" aria-expanded="false"
                    aria-controls="sidebarPagesAuth" class="side-nav-link" data-tooltip="Customer">
                    <i class="ri-group-2-line"></i>
                    <span> Customer </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="customer">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_CUSTOMER')
                        <li class="mt-2">
                            <a href="/customers">Add Customer</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_CUSTOMER')
                        <li class="mt-2">
                            <a href="/showcustomers">View Customer</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_BLACKLIST_CUSTOMER')
                        <li class="mt-2">
                            <a href="/showblacklistcustomers">View Blacklist Customer</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CUSTOMER_SAVING_ACC')
                        <li class="mt-2">
                            <a href="/showcustomerssaving">Customer Saving Acc.</a>
                        </li>
                        <li class="mt-2">
                            <a href="/showcustomersrecovery">Customer Recovery Acc.</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('KYC')
                        <li class="mt-2">
                            <a href="/kyc">KYC</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('INSURANCE')
                        <li class="mt-2">
                            <a href="/insurance">Insurance</a>
                        </li>
                        @endhasPrivilege

                        <li class="mt-2">
                            <a href="{{ route('customers.map') }}">
                                <span> Customer Map </span>
                            </a>
                        </li>
                    </ul>
                </div>

            </li>
            @endhasPrivilege

            @hasPrivilege('LOAN_CENTER')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#center" aria-expanded="false" aria-controls="center"
                    class="side-nav-link" data-tooltip="Loan Center">
                    <i class="bi bi-building"></i>
                    <span> Loan Center </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="center">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('CREATE_ROUTE')
                        <li class="mt-2">
                            <a href="/viewroutes">Create Route</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CREATE_CENTER')
                        <li class="mt-2">
                            <a href="/center">Create Center</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_CENTER')
                        <li class="mt-2">
                            <a href="/viewcenter">View Center</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CREATE_GROUP')
                        <li class="mt-2">
                            <a href="/customergroup">Create Group</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_GROUP')
                        <li class="mt-2">
                            <a href="/viewgroups">View Group</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('ADD_CUSTOMER_TO_GROUP')
                        <li class="mt-2">
                            <a href="/customergroupassign">Add Customers To Group</a>
                        </li>
                        @endhasPrivilege
                    </ul>
                </div>
            </li>
            @endhasPrivilege

            @hasPrivilege('GUARANTEE')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#Guarantee" aria-expanded="false"
                    aria-controls="sidebarPagesAuth" class="side-nav-link" data-tooltip="Guarantee">
                    <i class="ri-user-2-fill"></i>
                    <span> Guarantee </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="Guarantee">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_GUARANTEE')
                        <li class="mt-2">
                            <a href="/guardian">Add Guarantee</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_GUARANTEE')
                        <li class="mt-2">
                            <a href="/showguardian">View Guarantee</a>
                        </li>
                        @endhasPrivilege
                    </ul>
                </div>

            </li>
            @endhasPrivilege

            @hasPrivilege('PRODUCT')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#sidebarPages" aria-expanded="false"
                    aria-controls="sidebarPages" class="side-nav-link" data-tooltip="Product / Loan">
                    <i class="ri-pages-line"></i>
                    <span> Product / Loan </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarPages">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_PRODUCT')
                        <li class="mt-2">
                            <a href="/product">Add Product</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_PRODUCT')
                        <li class="mt-2">
                            <a href="/viewproduct">View Product</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CREATE_LOAN')
                        <li class="mt-2">
                            <a href="/loan">Create Loans</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CHANGE_COLLECTOR')
                        <li class="mt-2">
                            <a href="/changeCollector">Change Collector In Loan</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('PENDING_LOAN')
                        <li class="mt-2">
                            <a href="/pendingloan">Pending Loans</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LOAN_DISBURSEMENT')
                        <li class="mt-2">
                            <a href="/loan_disbursement">Loans Disbursement</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CURRENT_LOANS')
                        <li class="mt-2">
                            <a href="/payment_step_1">Current Loans</a>
                        </li>

                        <li class="mt-2">
                            <a href="/penalty-deduction">Panelty Deduction</a>
                        </li>
                        @endhasPrivilege


                        @hasPrivilege('SETTLED_LOANS')
                        <li class="mt-2">
                            <a href="/showsettleloan">Settled Loans</a>
                        </li>
                        @endhasPrivilege
                    </ul>
                </div>
            </li>
            @endhasPrivilege

            @hasPrivilege('PAYMENT_DETAILS')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#payment" aria-expanded="false" aria-controls="center"
                    class="side-nav-link" data-tooltip="Payment Details">
                    <i class="bi bi-currency-dollar"></i>
                    <span> Payment Details </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="payment">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_REPAYMENT')
                        <li class="mt-2">
                            <a href="/payment">Add Repayment</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('BULK_REPAYMENT')
                        <li class="mt-2">
                            <a href="/bulk_repayment">Bulk Repayment</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LOAN_SETTLEMENT')
                        <li class="mt-2">
                            <a href="/loan_settlement">Loan Settlement</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LOAN_RESCHEDULE')
                        <li class="mt-2">
                            <a href="/loan_reschedule">Loan Reschedule</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_PAYMENT')
                        <li class="mt-2">
                            <a href="/viewpayment">View Repayment</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('COLLECTOR_WISE_COLLECTION')
                        <li class="mt-2">
                            <a href="/collection">Collector Wise Collection</a>
                        </li>
                        @endhasPrivilege
                    </ul>
                </div>
            </li>
            @endhasPrivilege

            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#payment_voucher" aria-expanded="false"
                    aria-controls="payment_voucher" class="side-nav-link" data-tooltip="Payment Voucher">
                    <i class="ri-file-list-3-line"></i>
                    <span> Payment Voucher </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="payment_voucher">
                    <ul class="side-nav-second-level">
                        <li class="mt-2">
                            <a href="/VoucherDashboard">Voucher Dashboard</a>
                        </li>
                        <li class="mt-2">
                            <a href="/PaymentVoucher">Create Voucher</a>
                        </li>
                        <li class="mt-2">
                            <a href="/PendingVouchers">Pending Vouchers</a>
                        </li>
                        <li class="mt-2">
                            <a href="/ApprovedPayments">Voucher Payments</a>
                        </li>
                        <li class="mt-2">
                            <a href="/SupplierRegistration">Supplier Registration</a>
                        </li>
                    </ul>
                </div>
            </li>

            @hasPrivilege('LOAN_CALCULATOR')
            <li class="side-nav-item mt-2">
                <a href="/calculator" class="side-nav-link" data-tooltip="Loan Calculator">
                    <i class="ri-dashboard-3-line"></i>
                    <span> Loan Calculator </span>
                </a>
            </li>
            @endhasPrivilege

            @hasPrivilege('CALENDAR')
            <li class="side-nav-item mt-2">
                <a href="/calender" class="side-nav-link" data-tooltip="Calender">
                    <i class="ri-dashboard-3-line"></i>
                    <span> Calender </span>
                </a>
            </li>
            @endhasPrivilege

            @hasPrivilege('EXPENSES')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#expences" aria-expanded="false" aria-controls="expences"
                    class="side-nav-link" data-tooltip="Expenses">
                    <i class="ri-briefcase-line"></i>
                    <span> Expenses </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="expences">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_EXPENSES')
                        <li class="mt-2">
                            <a href="/expenses">Add Expenses</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_EXPENSES')
                        <li class="mt-2">
                            <a href="/view_expenses">View Expenses</a>
                        </li>
                        @endhasPrivilege
                    </ul>
                </div>
            </li>
            @endhasPrivilege

            @hasPrivilege('REPORTS')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#reports_section" aria-expanded="false"
                    class="side-nav-link" data-tooltip="Reports">
                    <i class="ri-file-paper-2-fill"></i>
                    <span> Reports </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="reports_section">
                    <ul class="side-nav-second-level">
                        <li class="side-nav-item mt-2">
                            <a data-bs-toggle="collapse" href="#main_report" aria-expanded="false"
                                class="side-nav-link">
                                <span> Main Reports </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="main_report">
                                <ul class="side-nav-third-level">
                                    @hasPrivilege('MAIN_REPORTS_DASHBOARD')
                                    <li class="mt-2">
                                        <a href="/portfolio_performance">Portfolio & Performance -
                                            Dashboard</a>
                                    </li>
                                    @endhasPrivilege
                                    @hasPrivilege('LOAN_DISBURSEMENT_PERFORMANCE')
                                    <li class="mt-2">
                                        <a href="/loan-report">Loan Disbursement Performance -
                                            Dashboard</a>
                                    </li>
                                    @endhasPrivilege
                                    @hasPrivilege('PAYMENT_DETAIL_REPORT')
                                    <li class="mt-2">
                                        <a href="/PaymentFullDetailsReport">Payment Details Report</a>
                                    </li>
                                    <li class="mt-2">
                                        <a href="/prediction_report">Payment Prediction</a>
                                    </li>
                                    @endhasPrivilege
                                    @hasPrivilege('FULL_LOAN_DETAIL')
                                    <li class="mt-2"><a href="/AllLoanDetailReport">Full Loan Detail Report</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('LOAN_SUMMARY')
                                    <li class="mt-2"><a href="/loansummaryreport">Loan Summary Report</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('PAR_MONTHLY')
                                    <li class="mt-2"><a href="/par">PAR (Monthly)</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('PAR_WEEKLY')
                                    <li class="mt-2"><a href="/par_weekly">PAR (Weekly)</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('LOAN_STATUS')
                                    <li class="mt-2">
                                        <a href="/loanStatus">Loan Status</a>
                                    </li>
                                    @endhasPrivilege

                                    <li class="mt-2">
                                        <a href="/depletion">Depletion Report Executive Summary</a>
                                    </li>
                                    <li class="mt-2">
                                        <a href="/investment">Investment Report Executive Summary</a>
                                    </li>

                                    <li class="mt-2">
                                        <a href="/arrears">Arrears Report Executive Summary</a>
                                    </li>

                                    <li class="mt-2">
                                        <a href="/report/penalty-deduction">Penalty Deduction Report</a>
                                    </li>

                                    <li class="mt-2">
                                        <a href="/reports/commission">Commission Report</a>
                                    </li>


                                </ul>
                            </div>
                        </li>

                        <li class="side-nav-item mt-2">
                            <a data-bs-toggle="collapse" href="#acc_report" aria-expanded="false"
                                class="side-nav-link">
                                <span> Account Reports </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="acc_report">
                                <ul class="side-nav-third-level">
                                    @hasPrivilege('CASHFLOW_ACCUMULATED')
                                    <li class="mt-2"><a href="/CashFlow">CashFlow Accumulated</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('CASHFLOW_MONTHLY')
                                    <li class="mt-2"><a href="/CashFlowMonthly">CashFlow Monthly</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('PROFIT_LOSS')
                                    <li class="mt-2"><a href="/ProfitLoss">Profit & Loss</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('BALANCE_SHEET')
                                    <li class="mt-2"><a href="/BalanceSheet">Balance Sheet</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('TRIAL_BALANCE')
                                    <li class="mt-2"><a href="/trialBalanceAccounting">Trial Balance</a></li>
                                    @endhasPrivilege
                                </ul>
                            </div>
                        </li>

                        <li class="side-nav-item mt-2">
                            <a data-bs-toggle="collapse" href="#collection_report" aria-expanded="false"
                                class="side-nav-link">
                                <span> Collection Reports </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="collection_report">
                                <ul class="side-nav-third-level">
                                    @hasPrivilege('DAILY_COLLECTION_SHEET')
                                    <li class="mt-2"><a href="/daily_repayment_sheet">Daily Collection Sheet</a></li>
                                    <li class="mt-2"><a href="/daily_repayment_sheet_lasantha">Daily Collection Sheet L</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('CENTER_COLLECTION_DETAIL')
                                    <li class="mt-2"><a href="/center_collection">Center Collection Detail</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('CENTER_COLLECTION_SUMMARY')
                                    <li class="mt-2"><a href="/center_collection_summary">Center Collection
                                            Summary</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('ROUTE_COLLECTIONS')
                                    <li class="mt-2"><a href="/root_wise_collection">Route Collections</a></li>
                                    @endhasPrivilege
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </li>
            @endhasPrivilege
            @endif


            <li class="side-nav-item mt-2">
                <a href="{{ route('activity-logs.index') }}" class="side-nav-link" data-tooltip="Activity Logs">
                    <i class="ri-history-line"></i>
                    <span> Activity Logs </span>
                </a>
            </li>

            <li class="side-nav-item">
                <a href="/logout" class="side-nav-link" data-tooltip="Logout">
                    <i class="ri-logout-box-line"></i>
                    <span> Logout </span>
                </a>
            </li>
        </ul>

        <!--- End Sidemenu -->
        <div class="clearfix"></div>
    </div>
</div>
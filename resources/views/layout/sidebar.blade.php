@include('layout.partials.sidebar-styles')
<!-- Sidebar -left -->
<div class="h-100" id="leftside-menu-container" data-simplebar>
    <!--- Sidemenu -->
    <ul class="side-nav">

        <li class="side-nav-title" style="text-color: red">{{ session('user_data')['company']['Company_Name'] }}</li>

        @php $isHeadOffice = session('head_branch') == session('branch_id'); @endphp

        @if ($isHeadOffice)
        {{-- Head Office restricted menu: Dashboard, View Customer, KYC, View Center --}}

        @hasPrivilege('DASHBOARD')
        <li class="side-nav-item">
            <a href="/" class="side-nav-link">
                <i class="ri-dashboard-3-line"></i>
                <span> Dashboard </span>
            </a>
        </li>
        @endhasPrivilege

        @hasPrivilege('CUSTOMER')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#customer" aria-expanded="false" aria-controls="customer"
                class="side-nav-link">
                <i class="ri-group-2-line"></i>
                <span> Customer </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="customer">
                <ul class="side-nav-second-level">
                    @hasPrivilege('VIEW_CUSTOMER')
                    <li><a href="/showcustomers">View Customer</a></li>
                    @endhasPrivilege
                    @hasPrivilege('KYC')
                    <li><a href="/kyc">KYC</a></li>
                    @endhasPrivilege
                </ul>
            </div>
        </li>
        @endhasPrivilege

        @hasPrivilege('LOAN_CENTER')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#center" aria-expanded="false" aria-controls="center"
                class="side-nav-link">
                <i class="bi bi-building"></i>
                <span> Loan Center </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="center">
                <ul class="side-nav-second-level">
                    <li><a href="/viewcenter">View Center</a></li>
                </ul>
            </div>
        </li>
        @endhasPrivilege

        <!-- 
        @hasPrivilege('ACCOUNT_CENTER')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#account" aria-expanded="false" aria-controls="center"
                class="side-nav-link">
                <i class="bi bi-universal-access"></i>
                <span> Account Center </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="account">
                <ul class="side-nav-second-level">
                    @hasPrivilege('BANK_CASH_ACCOUNT')
                    <li>
                        <a href="/bank_account">Bank/Cash Account</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('INTERNAL_BANK_TRANSFER')
                    <li>
                        <a href="/InnerBankTransfer">Internal Account Transfer</a>
                    </li>
                    @endhasPrivilege
                </ul>
            </div>
        </li>
        @endhasPrivilege

        @hasPrivilege('ACCOUNT_DEPARTMENT_1')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#accountmanagement" aria-expanded="false"
                aria-controls="center" class="side-nav-link">
                <i class="bi bi-bank"></i>
                <span> Account Department </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="accountmanagement">
                <ul class="side-nav-second-level">
                    @hasPrivilege('MANUAL_JOURNAL')
                    <li>
                        <a href="/ManualJournal">Manual Journal</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('CHART_OF_ACCOUNT')
                    <li>
                        <a href="/ChartOfAccount">Chart Of Account</a>
                    </li>
                    @endhasPrivilege
                </ul>
            </div>
        </li>
        @endhasPrivilege -->

        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#payment_voucher" aria-expanded="false"
                aria-controls="payment_voucher" class="side-nav-link">
                <i class="ri-file-list-3-line"></i>
                <span> Payment Voucher Module </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="payment_voucher">
                <ul class="side-nav-second-level">
                    <li>
                        <a href="/VoucherDashboard">Voucher Dashboard</a>
                    </li>
                    <li>
                        <a href="/PendingVouchers">Pending Vouchers</a>
                    </li>
                    <li>
                        <a href="/SupplierRegistration">Supplier Registration</a>
                    </li>
                </ul>
            </div>
        </li>

        @hasPrivilege('EXPENSES')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#expences" aria-expanded="false" aria-controls="expences"
                class="side-nav-link">
                <i class="ri-briefcase-line"></i>
                <span> Expenses </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="expences">
                <ul class="side-nav-second-level">
                    @hasPrivilege('ADD_EXPENSES')
                    <li>
                        <a href="/expenses">Add Expenses</a>
                    </li>
                    @endhasPrivilege
                </ul>
            </div>
        </li>
        @endhasPrivilege
        @else

        @hasPrivilege('DASHBOARD')
        <li class="side-nav-item">
            <a href="/" class="side-nav-link">
                <i class="ri-dashboard-3-line"></i>
                <span> Dashboard </span>
            </a>
        </li>
        @endhasPrivilege

        {{-- @hasPrivilege('CUSTOMER') --}}
        {{-- CUSTOMER LEADS SECTION --}}
        @hasPrivilege('CUSTOMER_LEADS')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#lead" aria-expanded="false"
                aria-controls="sidebarPagesAuth" class="side-nav-link">
                <i class="ri-article-fill"></i>
                <span> Lead </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="lead">
                <ul class="side-nav-second-level">
                    @hasPrivilege('BUSSINESS_CATEGORIES')
                    <li>
                        <a href="{{ route('business-categories.index') }}">
                            Business Categories
                        </a>
                    </li>
                    @endhasPrivilege

                    @hasPrivilege('CREATE_LEAD')
                    <li>
                        <a href="{{ route('leads.index') }}">Lead Create</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('CREATE_ONLINE_LEAD')
                    <li>
                        <a href="{{ route('online-leads.create') }}">Create Online Lead</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('LEAD_APPROVALS')
                    <li>
                        <a href="{{ route('leads.approvals') }}">Lead Approvals</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VERIFY_LEAD')
                    <li>
                        <a href="{{ route('leads.verifyAction') }}">Lead Verification</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('LEAD_AGREEMENT')
                    <li>
                        <a href="{{ route('leads.agreement') }}">Agreement Sign</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VIEW_LEAD_MAP')
                    <li>
                        <a href="{{ route('leads.globalMap') }}">All Leads Map</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('LEAD_ACTIVITY_LOGS')
                    <li>
                        <a href="{{ route('leads.activityLogs') }}">Activity Logs</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VIEW_LEADS')
                    <li>
                        <a href="{{ route('leads.verifiedList') }}">Lead List</a>
                    </li>
                    @endhasPrivilege

                </ul>
            </div>
        </li>
        @endhasPrivilege


        @hasPrivilege('CUSTOMER')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#customer" aria-expanded="false"
                aria-controls="sidebarPagesAuth" class="side-nav-link">
                <i class="ri-group-2-line"></i>
                <span> Customer </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="customer">
                <ul class="side-nav-second-level">
                    @hasPrivilege('ADD_CUSTOMER')
                    <li>
                        <a href="/customers">Add Customer</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VIEW_CUSTOMER')
                    <li>
                        <a href="/showcustomers">View Customer</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VIEW_BLACKLIST_CUSTOMER')
                    <li>
                        <a href="/showblacklistcustomers">View Blacklist Customer</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('CUSTOMER_SAVING_ACC')
                    <li>
                        <a href="/showcustomerssaving">Customer Saving Acc.</a>
                    </li>
                    <li>
                        <a href="/showcustomersrecovery">Customer Recovery Acc.</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('KYC')
                    <li>
                        <a href="/kyc">KYC</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('INSURANCE')
                    <li>
                        <a href="/insurance">Insurance</a>
                    </li>
                    @endhasPrivilege

                    <li>
                        <a href="{{ route('customers.map') }}">
                            <span> Customer Map </span>
                        </a>
                    </li>
                </ul>
            </div>

        </li>
        @endhasPrivilege

        @hasPrivilege('LOAN_CENTER')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#center" aria-expanded="false" aria-controls="center"
                class="side-nav-link">
                <i class="bi bi-building"></i>
                <span> Loan Center </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="center">
                <ul class="side-nav-second-level">
                    @hasPrivilege('CREATE_ROUTE')
                    <li>
                        <a href="/viewroutes">Create Route</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('CREATE_CENTER')
                    <li>
                        <a href="/center">Create Center</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VIEW_CENTER')
                    <li>
                        <a href="/viewcenter">View Center</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('CREATE_GROUP')
                    <li>
                        <a href="/customergroup">Create Group</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VIEW_GROUP')
                    <li>
                        <a href="/viewgroups">View Group</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('ADD_CUSTOMER_TO_GROUP')
                    <li>
                        <a href="/customergroupassign">Add Customers To Group</a>
                    </li>
                    @endhasPrivilege
                </ul>
            </div>
        </li>
        @endhasPrivilege

        @hasPrivilege('GUARANTEE')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#Guarantee" aria-expanded="false"
                aria-controls="sidebarPagesAuth" class="side-nav-link">
                <i class="ri-user-2-fill"></i>
                <span> Guarantee </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="Guarantee">
                <ul class="side-nav-second-level">
                    @hasPrivilege('ADD_GUARANTEE')
                    <li>
                        <a href="/guardian">Add Guarantee</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VIEW_GUARANTEE')
                    <li>
                        <a href="/showguardian">View Guarantee</a>
                    </li>
                    @endhasPrivilege
                </ul>
            </div>

        </li>
        @endhasPrivilege

        @hasPrivilege('PRODUCT')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#sidebarPages" aria-expanded="false"
                aria-controls="sidebarPages" class="side-nav-link">
                <i class="ri-pages-line"></i>
                <span> Product / Loan </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebarPages">
                <ul class="side-nav-second-level">
                    @hasPrivilege('ADD_PRODUCT')
                    <li>
                        <a href="/product">Add Product</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VIEW_PRODUCT')
                    <li>
                        <a href="/viewproduct">View Product</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('CREATE_LOAN')
                    <li>
                        <a href="/loan">Create Loans</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('CHANGE_COLLECTOR')
                    <li>
                        <a href="/changeCollector">Change Collector In Loan</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('PENDING_LOAN')
                    <li>
                        <a href="/pendingloan">Pending Loans</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('LOAN_DISBURSEMENT')
                    <li>
                        <a href="/loan_disbursement">Loans Disbursement</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('CURRENT_LOANS')
                    <li>
                        <a href="/payment_step_1">Current Loans</a>
                    </li>

                    <li>
                        <a href="/penalty-deduction">Panelty Deduction</a>
                    </li>
                    @endhasPrivilege


                    @hasPrivilege('SETTLED_LOANS')
                    <li>
                        <a href="/showsettleloan">Settled Loans</a>
                    </li>
                    @endhasPrivilege
                </ul>
            </div>
        </li>
        @endhasPrivilege

        @hasPrivilege('PAYMENT_DETAILS')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#payment" aria-expanded="false" aria-controls="center"
                class="side-nav-link">
                <i class="bi bi-currency-dollar"></i>
                <span> Payment Details </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="payment">
                <ul class="side-nav-second-level">
                    @hasPrivilege('ADD_REPAYMENT')
                    <li>
                        <a href="/payment">Add Repayment</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('BULK_REPAYMENT')
                    <li>
                        <a href="/bulk_repayment">Bulk Repayment</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('LOAN_SETTLEMENT')
                    <li>
                        <a href="/loan_settlement">Loan Settlement</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('LOAN_RESCHEDULE')
                    <li>
                        <a href="/loan_reschedule">Loan Reschedule</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VIEW_PAYMENT')
                    <li>
                        <a href="/viewpayment">View Repayment</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('COLLECTOR_WISE_COLLECTION')
                    <li>
                        <a href="/collection">Collector Wise Collection</a>
                    </li>
                    @endhasPrivilege
                </ul>
            </div>
        </li>
        @endhasPrivilege

        <!-- @hasPrivilege('ACCOUNT_CENTER')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#account" aria-expanded="false" aria-controls="center"
                class="side-nav-link">
                <i class="bi bi-universal-access"></i>
                <span> Account Center </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="account">
                <ul class="side-nav-second-level">
                    @hasPrivilege('BANK_CASH_ACCOUNT')
                    <li>
                        <a href="/bank_account">Bank/Cash Account</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('INTERNAL_BANK_TRANSFER')
                    <li>
                        <a href="/InnerBankTransfer">Internal Account Transfer</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('COLLECTOR_ACCOUNT')
                    <li>
                        <a href="/collector_index">Collector Account</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('CHEQUE_DETAILS')
                    <li>
                        <a href="/chq">Cheque Details</a>
                    </li>
                    @endhasPrivilege
                </ul>
            </div>
        </li>
        @endhasPrivilege

        @hasPrivilege('ACCOUNT_DEPARTMENT_1')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#accountmanagement" aria-expanded="false"
                aria-controls="center" class="side-nav-link">
                <i class="bi bi-bank"></i>
                <span> Account Department </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="accountmanagement">
                <ul class="side-nav-second-level">
                    @hasPrivilege('ADD_ASSET')
                    <li>
                        <a href="/AddAssetManagement">Add Asset Management</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('ASSET_MANAGEMENT')
                    <li>
                        <a href="/AssetManagement">Asset Management</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('BANK_RECONCILIATION')
                    <li>
                        <a href="/BankReconciliation">Bank Reconciliation</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('MANUAL_JOURNAL')
                    <li>
                        <a href="/ManualJournal">Manual Journal</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('CHART_OF_ACCOUNT')
                    <li>
                        <a href="/ChartOfAccount">Chart Of Account</a>
                    </li>
                    @endhasPrivilege
                </ul>
            </div>
        </li>
        @endhasPrivilege -->

        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#payment_voucher" aria-expanded="false"
                aria-controls="payment_voucher" class="side-nav-link">
                <i class="ri-file-list-3-line"></i>
                <span> Payment Voucher </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="payment_voucher">
                <ul class="side-nav-second-level">
                    <li>
                        <a href="/VoucherDashboard">Voucher Dashboard</a>
                    </li>
                    <li>
                        <a href="/PaymentVoucher">Create Voucher</a>
                    </li>
                    <li>
                        <a href="/PendingVouchers">Pending Vouchers</a>
                    </li>
                    <li>
                        <a href="/ApprovedPayments">Voucher Payments</a>
                    </li>
                    <li>
                        <a href="/SupplierRegistration">Supplier Registration</a>
                    </li>
                </ul>
            </div>
        </li>

        @hasPrivilege('LOAN_CALCULATOR')
        <li class="side-nav-item">
            <a href="/calculator" class="side-nav-link">
                <i class="ri-dashboard-3-line"></i>
                <span> Loan Calculator </span>
            </a>
        </li>
        @endhasPrivilege

        @hasPrivilege('CALENDAR')
        <li class="side-nav-item">
            <a href="/calender" class="side-nav-link">
                <i class="ri-dashboard-3-line"></i>
                <span> Calender </span>
            </a>
        </li>
        @endhasPrivilege

        @hasPrivilege('EXPENSES')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#expences" aria-expanded="false" aria-controls="expences"
                class="side-nav-link">
                <i class="ri-briefcase-line"></i>
                <span> Expenses </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="expences">
                <ul class="side-nav-second-level">
                    @hasPrivilege('ADD_EXPENSES')
                    <li>
                        <a href="/expenses">Add Expenses</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('VIEW_EXPENSES')
                    <li>
                        <a href="/view_expenses">View Expenses</a>
                    </li>
                    @endhasPrivilege
                </ul>
            </div>
        </li>
        @endhasPrivilege

        <!-- @hasPrivilege('USER')

        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#user" aria-expanded="false" aria-controls="user"
                class="side-nav-link collapsed">
                <i class="ri-user-3-fill"></i>
                <span> User </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="user" style="">
                <ul class="side-nav-second-level">
                    @hasPrivilege('CREATE_USER')
                    <li>
                        <a href="/user" class="active">Manage User</a>
                    </li>
                    @endhasPrivilege
                    @hasPrivilege('USER_PRIVILEGES')
                    <li>
                        <a href="/privileges">User Privileges</a>
                    </li>
                    @endhasPrivilege
                    <li>
                        <a href="/designation-privileges">Designation Privileges</a>
                    </li>
                </ul>
            </div>
        </li>
        @endhasPrivilege -->

        @hasPrivilege('REPORTS')
        <li class="side-nav-item">
            <a data-bs-toggle="collapse" href="#reports_section" aria-expanded="false"
                class="side-nav-link">
                <i class="ri-file-paper-2-fill"></i>
                <span> Reports </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="reports_section">
                <ul class="side-nav-second-level">
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#main_report" aria-expanded="false"
                            class="side-nav-link">
                            <span> Main Reports </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="main_report">
                            <ul class="side-nav-third-level">
                                @hasPrivilege('MAIN_REPORTS_DASHBOARD')
                                <li>
                                    <a href="/portfolio_performance">Portfolio & Performance -
                                        Dashboard</a>
                                </li>
                                @endhasPrivilege
                                @hasPrivilege('LOAN_DISBURSEMENT_PERFORMANCE')
                                <li>
                                    <a href="/loan-report">Loan Disbursement Performance -
                                        Dashboard</a>
                                </li>
                                @endhasPrivilege
                                @hasPrivilege('PAYMENT_DETAIL_REPORT')
                                <li>
                                    <a href="/PaymentFullDetailsReport">Payment Details Report</a>
                                </li>
                                <li>
                                    <a href="/prediction_report">Payment Prediction</a>
                                </li>
                                @endhasPrivilege
                                @hasPrivilege('FULL_LOAN_DETAIL')
                                <li><a href="/AllLoanDetailReport">Full Loan Detail Report</a></li>
                                @endhasPrivilege
                                @hasPrivilege('LOAN_SUMMARY')
                                <li><a href="/loansummaryreport">Loan Summary Report</a></li>
                                @endhasPrivilege
                                @hasPrivilege('PAR_MONTHLY')
                                <li><a href="/par">PAR (Monthly)</a></li>
                                @endhasPrivilege
                                @hasPrivilege('PAR_WEEKLY')
                                <li><a href="/par_weekly">PAR (Weekly)</a></li>
                                @endhasPrivilege
                                @hasPrivilege('LOAN_STATUS')
                                <li>
                                    <a href="/loanStatus">Loan Status</a>
                                </li>
                                @endhasPrivilege

                                <li>
                                    <a href="/depletion">Depletion Report Executive Summary</a>
                                </li>
                                <li>
                                    <a href="/investment">Investment Report Executive Summary</a>
                                </li>

                                <li>
                                    <a href="/arrears">Arrears Report Executive Summary</a>
                                </li>

                                <li>
                                    <a href="/report/penalty-deduction">Penalty Deduction Report</a>
                                </li>

                                <li>
                                    <a href="/reports/commission">Commission Report</a>
                                </li>


                            </ul>
                        </div>
                    </li>

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#acc_report" aria-expanded="false"
                            class="side-nav-link">
                            <span> Account Reports </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="acc_report">
                            <ul class="side-nav-third-level">
                                @hasPrivilege('CASHFLOW_ACCUMULATED')
                                <li><a href="/CashFlow">CashFlow Accumulated</a></li>
                                @endhasPrivilege
                                @hasPrivilege('CASHFLOW_MONTHLY')
                                <li><a href="/CashFlowMonthly">CashFlow Monthly</a></li>
                                @endhasPrivilege
                                @hasPrivilege('PROFIT_LOSS')
                                <li><a href="/ProfitLoss">Profit & Loss</a></li>
                                @endhasPrivilege
                                @hasPrivilege('BALANCE_SHEET')
                                <li><a href="/BalanceSheet">Balance Sheet</a></li>
                                @endhasPrivilege
                                @hasPrivilege('TRIAL_BALANCE')
                                <li><a href="/trialBalanceAccounting">Trial Balance</a></li>
                                @endhasPrivilege
                            </ul>
                        </div>
                    </li>


                    <!-- <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#expensess_reports" aria-expanded="false"
                            class="side-nav-link">
                            <span> Expenses Reports </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="expensess_reports">
                            <ul class="side-nav-third-level">
                                <li>
                                    <a href="/report">Expenses Report</a>
                                </li>
                                <li>
                                    <a href="/income">Income Report</a>
                                </li>
                            </ul>
                        </div>
                    </li> -->

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#collection_report" aria-expanded="false"
                            class="side-nav-link">
                            <span> Collection Reports </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="collection_report">
                            <ul class="side-nav-third-level">
                                @hasPrivilege('DAILY_COLLECTION_SHEET')
                                <li><a href="/daily_repayment_sheet">Daily Collection Sheet</a></li>
                                <li><a href="/daily_repayment_sheet_lasantha">Daily Collection Sheet L</a></li>
                                @endhasPrivilege
                                @hasPrivilege('CENTER_COLLECTION_DETAIL')
                                <li><a href="/center_collection">Center Collection Detail</a></li>
                                @endhasPrivilege
                                @hasPrivilege('CENTER_COLLECTION_SUMMARY')
                                <li><a href="/center_collection_summary">Center Collection
                                        Summary</a></li>
                                @endhasPrivilege
                                @hasPrivilege('ROUTE_COLLECTIONS')
                                <li><a href="/root_wise_collection">Route Collections</a></li>
                                @endhasPrivilege
                            </ul>
                        </div>
                    </li>
                </ul>
            </div>
        </li>
        @endhasPrivilege
        @endif


        <li class="side-nav-item">
            <a href="/logout" class="side-nav-link">
                <i class="ri-logout-box-line"></i>
                <span> Logout </span>
            </a>
        </li>
    </ul>

    <!--- End Sidemenu -->
    <div class="clearfix"></div>
</div>
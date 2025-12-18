<!-- Sidebar -left -->
<div class="h-100" id="leftside-menu-container" data-simplebar>
    <!--- Sidemenu -->
    <ul class="side-nav">
        <?php
        $query = "SELECT * FROM company where branch_id='" . session('branch_id') . "'";
        $check = DB::select($query);
        ?>

        @foreach ($check as $item)
            <li class="side-nav-title" style="color: red">{{ $item->company_name }}</li>
        @endforeach


        @if ($privilege)
            @php $isHeadOffice = session('branch_id') == -1; @endphp
            
            @if ($isHeadOffice)
                {{-- Head Office restricted menu: Dashboard, View Customer, KYC, View Center --}}
                @if (optional($privilege)->dashboard == 1)
                    <li class="side-nav-item">
                        <a href="/" class="side-nav-link">
                            <i class="ri-dashboard-3-line"></i>
                            <span> Dashboard </span>
                        </a>
                    </li>
                @endif

               
                <li class="side-nav-item">
                    <a href="{{ route('business-categories.index') }}" class="side-nav-link">
                        <i class="ri-layout-grid-fill"></i>
                        <span> Business Categories </span>
                    </a>
                </li>

                @if (optional($privilege)->customer == 1 && (optional($privilege)->view_customer == 1 || optional($privilege)->kyc == 1))
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#customer" aria-expanded="false" aria-controls="customer"
                            class="side-nav-link">
                            <i class="ri-group-2-line"></i>
                            <span> Customer </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="customer">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->view_customer == 1)
                                    <li><a href="/showcustomers">View Customer</a></li>
                                @endif
                                @if (optional($privilege)->kyc == 1)
                                    <li><a href="/kyc">KYC</a></li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if (optional($privilege)->loan_center == 1 && optional($privilege)->view_center == 1)
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
                @endif

                @if (optional($privilege)->account_center == 1)
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#account" aria-expanded="false" aria-controls="center"
                            class="side-nav-link">
                            <i class="bi bi-universal-access"></i>
                            <span> Account Center </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="account">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->bank_cash_account == 1)
                                    <li>
                                        <a href="/bank_account">Bank/Cash Account</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->internal_bank_transfer == 1)
                                    <li>
                                        <a href="/InnerBankTransfer">Internal Account Transfer</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if (optional($privilege)->account_department == 1)
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#accountmanagement" aria-expanded="false"
                            aria-controls="center" class="side-nav-link">
                            <i class="bi bi-bank"></i>
                            <span> Account Department </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="accountmanagement">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->manual_journal == 1)
                                    <li>
                                        <a href="/ManualJournal">Manual Journal</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->chart_of_account == 1)
                                    <li>
                                        <a href="/ChartOfAccount">Chart Of Account</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

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

                @if (optional($privilege)->expenses == 1)
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#expences" aria-expanded="false" aria-controls="expences"
                            class="side-nav-link">
                            <i class="ri-briefcase-line"></i>
                            <span> Expenses </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="expences">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->add_expenses == 1)
                                    <li>
                                        <a href="/expenses">Add Expenses</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif
            @else
                @if (optional($privilege)->dashboard == 1)
                    <li class="side-nav-item">
                        <a href="/" class="side-nav-link">
                            <i class="ri-dashboard-3-line"></i>
                            <span> Dashboard </span>
                        </a>
                    </li>
                @endif

                {{-- @if (optional($privilege)->customer == 1) --}}
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#lead" aria-expanded="false"
                            aria-controls="sidebarPagesAuth" class="side-nav-link">
                           <i class="ri-article-fill"></i>
                            <span> Lead </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="lead">
                            <ul class="side-nav-second-level">
                                {{-- @if (optional($privilege)->add_customer == 1) --}}
                                    <li>
                                        <a href="{{ route('leads.index') }}">Lead Create/Agreement</a>
                                    </li>
                                {{-- @endif --}}
                                <li>
                                    <a href="{{ route('leads.approvals') }}">Lead Approvals</a>
                                </li>
                                <li>
                                    <a href="{{ route('leads.verifyAction') }}">Lead Verification</a>
                                </li>
                                <li>
                                    <a href="{{ route('leads.verifiedList') }}">Verified Lead History</a>
                                </li>
                            </ul>
                        </div>

                    </li>

                    <li class="side-nav-item">
                        <a href="{{ route('business-categories.index') }}" class="side-nav-link">
                            <i class="ri-layout-grid-fill"></i>
                            <span> Business Categories </span>
                        </a>
                    </li>
                {{-- @endif --}}

                @if (optional($privilege)->customer == 1)
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#customer" aria-expanded="false"
                            aria-controls="sidebarPagesAuth" class="side-nav-link">
                            <i class="ri-group-2-line"></i>
                            <span> Customer </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="customer">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->add_customer == 1)
                                    <li>
                                        <a href="/customers">Add Customer</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->view_customer == 1)
                                    <li>
                                        <a href="/showcustomers">View Customer</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->view_blacklist_customer == 1)
                                    <li>
                                        <a href="/showblacklistcustomers">View Blacklist Customer</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->customer_saving_acc == 1)
                                    <li>
                                        <a href="/showcustomerssaving">Customer Saving Acc.</a>
                                    </li>
                                    <li>
                                        <a href="/showcustomersrecovery">Customer Recovery Acc.</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->kyc == 1)
                                    <li>
                                        <a href="/kyc">KYC</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->insurance == 1)
                                    <li>
                                        <a href="/insurance">Insurance</a>
                                    </li>
                                @endif

                                <li>
                                    <a href="{{ route('customers.map') }}">
                                        <span> Customer Map </span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                    </li>
                @endif

                @if (optional($privilege)->loan_center == 1)
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#center" aria-expanded="false" aria-controls="center"
                            class="side-nav-link">
                            <i class="bi bi-building"></i>
                            <span> Loan Center </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="center">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->create_route == 1)
                                    <li>
                                        <a href="/viewroutes">Create Route</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->create_center == 1)
                                    <li>
                                        <a href="/center">Create Center</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->view_center == 1)
                                    <li>
                                        <a href="/viewcenter">View Center</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->create_group == 1)
                                    <li>
                                        <a href="/customergroup">Create Group</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->view_group == 1)
                                    <li>
                                        <a href="/viewgroups">View Group</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->add_customer_to_group == 1)
                                    <li>
                                        <a href="/customergroupassign">Add Customers To Group</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if (optional($privilege)->guarantee == 1)
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#Guarantee" aria-expanded="false"
                            aria-controls="sidebarPagesAuth" class="side-nav-link">
                            <i class="ri-user-2-fill"></i>
                            <span> Guarantee </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="Guarantee">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->add_guarantee == 1)
                                    <li>
                                        <a href="/guardian">Add Guarantee</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->view_guarantee == 1)
                                    <li>
                                        <a href="/showguardian">View Guarantee</a>
                                    </li>
                                @endif
                            </ul>
                        </div>

                    </li>
                @endif

                @if (optional($privilege)->product == 1)
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#sidebarPages" aria-expanded="false"
                            aria-controls="sidebarPages" class="side-nav-link">
                            <i class="ri-pages-line"></i>
                            <span> Product / Loan </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="sidebarPages">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->add_product == 1)
                                    <li>
                                        <a href="/product">Add Product</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->view_product == 1)
                                    <li>
                                        <a href="/viewproduct">View Product</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->create_loan == 1)
                                    <li>
                                        <a href="/loan">Create Loans</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->change_collector == 1)
                                    <li>
                                        <a href="/changeCollector">Change Collector In Loan</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->pending_loan == 1)
                                    <li>
                                        <a href="/pendingloan">Pending Loans</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->loan_disbursement == 1)
                                    <li>
                                        <a href="/loan_disbursement">Loans Disbursement</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->current_loans == 1)
                                    <li>
                                        <a href="/payment_step_1">Current Loans</a>
                                    </li>

                                    <li>
                                        <a href="/penalty-deduction">Panelty Deduction</a>
                                    </li>
                                @endif


                                @if (optional($privilege)->settled_loans == 1)
                                    <li>
                                        <a href="/showsettleloan">Settled Loans</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif


                
                @if (optional($privilege)->payment_details == 1)
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#payment" aria-expanded="false" aria-controls="center"
                            class="side-nav-link">
                            <i class="bi bi-currency-dollar"></i>
                            <span> Payment Details </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="payment">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->add_repayment == 1)
                                    <li>
                                        <a href="/payment">Add Repayment</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->bulk_repayment == 1)
                                    <li>
                                        <a href="/bulk_repayment">Bulk Repayment</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->loan_settlement == 1)
                                    <li>
                                        <a href="/loan_settlement">Loan Settlement</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->loan_reschedule == 1)
                                    <li>
                                        <a href="/loan_reschedule">Loan Reschedule</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->view_payment == 1)
                                    <li>
                                        <a href="/viewpayment">View Repayment</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->collector_wise_collection == 1)
                                    <li>
                                        <a href="/collection">Collector Wise Collection</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if (optional($privilege)->account_center == 1)

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#account" aria-expanded="false" aria-controls="center"
                            class="side-nav-link">
                            <i class="bi bi-universal-access"></i>
                            <span> Account Center </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="account">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->bank_cash_account == 1)
                                    <li>
                                        <a href="/bank_account">Bank/Cash Account</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->internal_bank_transfer == 1)
                                    <li>
                                        <a href="/InnerBankTransfer">Internal Account Transfer</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->collector_account == 1)
                                    <li>
                                        <a href="/collector_index">Collector Account</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->cheque_details == 1)
                                    <li>
                                        <a href="/chq">Cheque Details</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if (optional($privilege)->account_department == 1)
                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#accountmanagement" aria-expanded="false"
                            aria-controls="center" class="side-nav-link">
                            <i class="bi bi-bank"></i>
                            <span> Account Department </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="accountmanagement">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->add_asset == 1)
                                    <li>
                                        <a href="/AddAssetManagement">Add Asset Management</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->asset_management == 1)
                                    <li>
                                        <a href="/AssetManagement">Asset Management</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->bank_reconciliation == 1)
                                    <li>
                                        <a href="/BankReconciliation">Bank Reconciliation</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->manual_journal == 1)
                                    <li>
                                        <a href="/ManualJournal">Manual Journal</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->chart_of_account == 1)
                                    <li>
                                        <a href="/ChartOfAccount">Chart Of Account</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

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

                @if (optional($privilege)->loan_calculator == 1)
                    <li class="side-nav-item">
                        <a href="/calculator" class="side-nav-link">
                            <i class="ri-dashboard-3-line"></i>
                            <span> Loan Calculator </span>
                        </a>
                    </li>
                @endif

                @if (optional($privilege)->calendar == 1)
                    <li class="side-nav-item">
                        <a href="/calender" class="side-nav-link">
                            <i class="ri-dashboard-3-line"></i>
                            <span> Calender </span>
                        </a>
                    </li>
                @endif

                @if (optional($privilege)->expenses == 1)

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#expences" aria-expanded="false" aria-controls="expences"
                            class="side-nav-link">
                            <i class="ri-briefcase-line"></i>
                            <span> Expenses </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="expences">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->add_expenses == 1)
                                    <li>
                                        <a href="/expenses">Add Expenses</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->view_expenses == 1)
                                    <li>
                                        <a href="/view_expenses">View Expenses</a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </li>
                @endif

                @if (optional($privilege)->user == 1)

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#user" aria-expanded="false" aria-controls="user"
                            class="side-nav-link collapsed">
                            <i class="ri-user-3-fill"></i>
                            <span> User </span>
                            <span class="menu-arrow"></span>
                        </a>
                        <div class="collapse" id="user" style="">
                            <ul class="side-nav-second-level">
                                @if (optional($privilege)->create_user == 1)
                                    <li>
                                        <a href="/user" class="active">Manage User</a>
                                    </li>
                                @endif
                                @if (optional($privilege)->user_privileges == 1)
                                    <li>
                                        <a href="/privileges">User Privileges</a>
                                    </li>
                                @endif
                                <li>
                                    <a href="/designation-privileges">Designation Privileges</a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endif

                @if (optional($privilege)->reports == 1)
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
                                            @if (optional($privilege)->main_reports_dashboard == 1)
                                                <li>
                                                    <a href="/portfolio_performance">Portfolio & Performance -
                                                        Dashboard</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->loan_disbursement_performance == 1)
                                                <li>
                                                    <a href="/loan-report">Loan Disbursement Performance -
                                                        Dashboard</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->payment_detail_report == 1)
                                                <li>
                                                    <a href="/PaymentFullDetailsReport">Payment Details Report</a>
                                                </li>
                                                <li>
                                                    <a href="/prediction_report">Payment Prediction</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->full_loan_detail == 1)
                                                <li><a href="/AllLoanDetailReport">Full Loan Detail Report</a></li>
                                            @endif
                                            @if (optional($privilege)->loan_summary == 1)
                                                <li><a href="/loansummaryreport">Loan Summary Report</a></li>
                                            @endif
                                            @if (optional($privilege)->par_monthly == 1)
                                                <li><a href="/par">PAR (Monthly)</a></li>
                                            @endif
                                            @if (optional($privilege)->par_weekly == 1)
                                                <li><a href="/par_weekly">PAR (Weekly)</a></li>
                                            @endif
                                            @if (optional($privilege)->loan_status == 1)
                                                <li>
                                                    <a href="/loanStatus">Loan Status</a>
                                                </li>
                                            @endif

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


                                        </ul>
                                    </div>
                                </li>

                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#acc_report" aria-expanded="false"
                                        class="side-nav-link">
                                        <span> Accounting Reports </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse" id="acc_report">
                                        <ul class="side-nav-third-level">
                                            @if (optional($privilege)->cashflow_accumulated == 1)
                                                <li>
                                                    <a href="/CashFlow">CashFlow Accumulated</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->cashflow_monthly == 1)
                                                <li>
                                                    <a href="/CashFlowMonthly">CashFlow Monthly</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->profit_loss == 1)
                                                <li>
                                                    <a href="/ProfitLoss">Profit & Loss (P&L)</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->balance_sheet == 1)
                                                <li>
                                                    <a href="/BalanceSheet">Balance Sheet</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->trial_balance == 1)
                                                <li>
                                                    <a href="/trialBalanceAccounting">Trial Balance</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>

                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#payment_report" aria-expanded="false"
                                        class="side-nav-link">
                                        <span> Payment Reports </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse" id="payment_report">
                                        <ul class="side-nav-third-level">
                                            @if (optional($privilege)->daily_collection_sheet == 1)
                                                <li>
                                                    <a href="/daily">Daily Collection Sheet</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->center_collection_detail == 1)
                                                <li>
                                                    <a href="/center_collection">Center Wise collection Detail</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->center_collection_summary == 1)
                                                <li>
                                                    <a href="/center_collection_summary">Center Wise collection
                                                        Summary</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->route_collections == 1)
                                                <li>
                                                    <a href="/root_wise_collection">Route Wise Daily Collection</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>
                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#repayment_report" aria-expanded="false"
                                        class="side-nav-link">
                                        <span> Re Payment Reports </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse" id="repayment_report">
                                        <ul class="side-nav-third-level">
                                            @if (optional($privilege)->repayment_sheet_01 == 1)
                                                <li>
                                                    <a href="/daily_repayment_sheet">Repayment Sheet 01</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->repayment_sheet_02 == 1)
                                                <li>
                                                    <a href="/RightWayDailyRepayment">Repayment Sheet 02</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->repayment_sheet_03 == 1)
                                                <li>
                                                    <a href="/daily_repayment_sheet_hm">Repayment Sheet 03</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->repayment_sheet_04 == 1)
                                                <li>
                                                    <a href="/daily_repayment_sheet_lasantha">Repayment Sheet 04</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->repayment_sheet_05 == 1)
                                                <li>
                                                    <a href="/daily_repayment_sheet_finwin">Repayment Sheet 05</a>
                                                </li>
                                            @endif

                                            @if (optional($privilege)->repayment_sheet_06 == 1)
                                                <li>
                                                    <a href="/GreenLankaTrustRepayment">Repayment Sheet 06</a>
                                                </li>
                                            @endif

                                            @if (optional($privilege)->repayment_sheet_07 == 1)
                                                <li>
                                                    <a href="/DandDRepayment">Repayment Sheet 07</a>
                                                </li>
                                            @endif

                                            @if (optional($privilege)->repayment_sheet_08 == 1)
                                                <li>
                                                    <a href="/dailyreport">Repayment Sheet 08</a>
                                                </li>
                                            @endif

                                            {{-- TEMPORARY: Show Repayment Sheet 09 without permission check for testing --}}
                                            {{-- @if (optional($privilege)->repayment_sheet_09 == 1) --}}
                                            <li>
                                                <a href="/repaymntseet9">Repayment Sheet 09</a>
                                            </li>
                                            {{-- @endif --}}

                                            {{-- Repayment Sheet 10 --}}
                                            {{-- @if (optional($privilege)->repayment_sheet_10 == 1) --}}
                                            <li>
                                                <a href="/RepaymentSheet10">Repayment Sheet 10</a>
                                            </li>
                                            {{-- @endif --}}
                                        </ul>
                                    </div>
                                </li>
                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#sub_report" aria-expanded="false"
                                        class="side-nav-link">
                                        <span> Sub Reports </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse" id="sub_report">
                                        <ul class="side-nav-third-level">
                                            @if (optional($privilege)->other_charges_report == 1)
                                                <li><a href="/LoanChargers">Loan Chargers Report</a></li>
                                            @endif
                                            @if (optional($privilege)->center_dashboard == 1)
                                                <li><a href="/dandlreport">Center Collection Dashboard</a></li>
                                            @endif
                                            @if (optional($privilege)->repayment_summary == 1)
                                                <li><a href="/monthlyprofit">Loan Repayment Summary Report</a></li>
                                            @endif
                                            @if (optional($privilege)->savings_report == 1)
                                                <li><a href="/savings_report">Savings Report</a></li>
                                            @endif
                                            @if (optional($privilege)->arrears_report == 1)
                                                <li><a href="/latePayment">Loan In Areas</a></li>
                                            @endif
                                            @if (optional($privilege)->arrears_overview == 1)
                                                <li><a href="/late_payment_report">Arrease Details</a></li>
                                            @endif
                                            @if (optional($privilege)->datewise_cashflow == 1)
                                                <li><a href="/ViewDateWiseCashFlow">Date Wise Cash Flow Details</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->loan_detail_report == 1)
                                                <li><a href="/loanreport">Loan Details</a></li>
                                            @endif
                                            @if (optional($privilege)->collector_report == 1)
                                                <li><a href="/repaymentreport">Collector Wise Repayment Collection</a>
                                                </li>
                                            @endif
                                            @if (optional($privilege)->sms_history == 1)
                                                <li><a href="/sms_history">SMS History Report</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </li>

                                <li class="side-nav-item">
                                    <a data-bs-toggle="collapse" href="#people_report" aria-expanded="false"
                                        class="side-nav-link">
                                        <span> People Reports </span>
                                        <span class="menu-arrow"></span>
                                    </a>
                                    <div class="collapse" id="people_report">
                                        <ul class="side-nav-third-level">
                                            @if (optional($privilege)->customer_detail_report == 1)
                                                <li><a href="/customerreport_details">All Customer Details</a></li>
                                            @endif
                                            @if (optional($privilege)->officer_customer_detail == 1)
                                                <li><a href="/customerreport_details_recover_officer">Recover Officer
                                                        Wise Customers</a></li>
                                            @endif
                                            @if (optional($privilege)->guardian_detail_report == 1)
                                                <li><a href="/borrowerreport">Guardian Details</a></li>
                                            @endif
                                        </ul>


                                    </div>
                                </li>


                            </ul>
                        </div>
                    </li>
                @endif

            @endif {{-- end isHeadOffice condition --}}

            {{-- Approval menu - Available to all users --}}
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#approval" aria-expanded="false" aria-controls="approval"
                    class="side-nav-link">
                    <i class="ri-checkbox-circle-line"></i>
                    <span> Approval </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="approval">
                    <ul class="side-nav-second-level">
                        <li><a href="/pending_approval">Pending Approval</a></li>
                        <li><a href="/approved_history">Approved History</a></li>
                        <li><a href="/rejected_approval">Rejected Approval</a></li>
                        <li>
                            <a href="loan_delete_requests">Delete Loans Approval</a>
                        </li>
                    </ul>
                </div>
            </li>

        @endif
    </ul>

    <div class="clearfix"></div>
</div>

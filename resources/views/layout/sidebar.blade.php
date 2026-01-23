<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <!-- Brand Logo Area -->
    <div class="logo-box">
        <a href="/" class="logo-link">
            <!-- Logo Image -->
            @if ($logo)
            <img src="{{ $logo }}" class="logo-img" alt="Logo">
            @else
            <img src="https://accountcenter.asipiya.com/asipiya.svg" class="logo-img" alt="Logo">
            @endif

            <!-- Logo Text (Company Name) -->
            <span class="logo-text">{{ user_data()['company']['Company_Name'] ?? 'Asipiya' }}</span>
        </a>
    </div>

    <!-- Mobile Close Button -->
    <a href="javascript:void(0);" class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </a>
    <!-- Sidebar -left -->

    <div class="h-100" id="leftside-menu-container" data-simplebar>

        <!--- Sidemenu -->
        <ul class="side-nav">

            <li class="side-nav-title" style="color: red">{{ user_data()['company']['Company_Name'] ?? '' }}</li>

            @php $isHeadOffice = session('head_branch') == session('branch_id'); @endphp

            @if ($isHeadOffice)
            {{-- Head Office restricted menu: Dashboard, View Customer, KYC, View Center --}}

            @hasPrivilege('DASHBOARD')
            @hasPrivilege('DASHBOARD')
            <li class="side-nav-item mt-2">
                <a href="/" class="side-nav-link {{ Request::is('/') ? 'active' : '' }}" data-tooltip="Dashboard">
                    <i class="ri-dashboard-3-line"></i>
                    <span> Dashboard </span>
                </a>
            </li>
            @endhasPrivilege
            @endhasPrivilege

            @hasPrivilege('CUSTOMER')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#customer" aria-expanded="false" aria-controls="customer"
                    class="side-nav-link" data-tooltip="Customer">
                    <i class="ri-group-2-line"></i>
                    <span> Customer </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ Request::is('showcustomers*', 'kyc*') ? 'show' : '' }}" id="customer">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('VIEW_CUSTOMER')
                        <li class="mt-2"><a href="/showcustomers" class="{{ Request::is('showcustomers*') ? 'active' : '' }}">View Customer</a></li>
                        @endhasPrivilege
                        @hasPrivilege('KYC')
                        <li class="mt-2"><a href="/kyc" class="{{ Request::is('kyc*') ? 'active' : '' }}">KYC</a></li>
                        @endhasPrivilege
                    </ul>
                </div>
            </li>
            @endhasPrivilege

            @hasPrivilege('LOAN_CENTER')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#center" aria-expanded="false" aria-controls="center"
                    class="side-nav-link" data-tooltip="Loan Center">
                    <i class="ri-building-4-line"></i>
                    <span> Loan Center </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ Request::is('viewcenter*') ? 'show' : '' }}" id="center">
                    <ul class="side-nav-second-level">
                        <li class="mt-2"><a href="/viewcenter" class="{{ Request::is('viewcenter*') ? 'active' : '' }}">View Center</a></li>
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
                <div class="collapse {{ Request::is('pending_approval*', 'approved_history*', 'rejected_approval*') ? 'show' : '' }}" id="approval">
                    <ul class="side-nav-second-level">
                        <li class="mt-2"><a href="/pending_approval" class="{{ Request::is('pending_approval*') ? 'active' : '' }}">Pending Approvals</a></li>
                        <li class="mt-2"><a href="/approved_history" class="{{ Request::is('approved_history*') ? 'active' : '' }}">Approval History</a></li>
                        <li class="mt-2"><a href="/rejected_approval" class="{{ Request::is('rejected_approval*') ? 'active' : '' }}">Rejected History</a></li>
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
                <div class="collapse {{ Request::is('VoucherDashboard*', 'PendingVouchers*', 'SupplierRegistration*') ? 'show' : '' }}" id="payment_voucher">
                    <ul class="side-nav-second-level">
                        <li class="mt-2">
                            <a href="/VoucherDashboard" class="{{ Request::is('VoucherDashboard*') ? 'active' : '' }}">Voucher Dashboard</a>
                        </li>
                        <li class="mt-2">
                            <a href="/PendingVouchers" class="{{ Request::is('PendingVouchers*') ? 'active' : '' }}">Pending Vouchers</a>
                        </li>
                        <li class="mt-2">
                            <a href="/SupplierRegistration" class="{{ Request::is('SupplierRegistration*') ? 'active' : '' }}">Supplier Registration</a>
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
                <div class="collapse {{ Request::is('expenses*') ? 'show' : '' }}" id="expences">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_EXPENSES')
                        <li class="mt-2">
                            <a href="/expenses" class="{{ Request::is('expenses*') ? 'active' : '' }}">Add Expenses</a>
                        </li>
                        @endhasPrivilege
                    </ul>
                </div>
            </li>
            @endhasPrivilege
            @else

            @hasPrivilege('DASHBOARD')
            <li class="side-nav-item mt-2">
                <a href="/" class="side-nav-link {{ Request::is('/') ? 'active' : '' }}" data-tooltip="Dashboard">
                    <i class="ri-dashboard-3-line"></i>
                    <span> Dashboard </span>
                </a>
            </li>
            @endhasPrivilege

            @hasPrivilege('PRODUCT')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#sidebarPages" aria-expanded="false"
                    aria-controls="sidebarPages" class="side-nav-link" data-tooltip="Product">
                    <i class="ri-box-3-line"></i>
                    <span> Product </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ Request::is('product*', 'viewproduct*') ? 'show' : '' }}" id="sidebarPages">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_PRODUCT')
                        <li class="mt-2">
                            <a href="/product" class="{{ Request::is('product*') ? 'active' : '' }}">Add Product</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_PRODUCT')
                        <li class="mt-2">
                            <a href="/viewproduct" class="{{ Request::is('viewproduct*') ? 'active' : '' }}">View Product</a>
                        </li>
                        @endhasPrivilege
                    </ul>
                </div>
            </li>
            @endhasPrivilege

            @hasPrivilege('LOAN')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#sidebarPages" aria-expanded="false"
                    aria-controls="sidebarPages" class="side-nav-link" data-tooltip="Loan">
                    <i class="ri-money-dollar-circle-line"></i>
                    <span> Loan </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ Request::is('loan*', 'changeCollector*', 'pendingloan*', 'loan_disbursement*', 'payment_step_1*', 'penalty-deduction*', 'showsettleloan*') ? 'show' : '' }}" id="sidebarPages">
                    <ul class="side-nav-second-level">

                        @hasPrivilege('CREATE_LOAN')
                        <li class="mt-2">
                            <a href="/loan" class="{{ Request::is('loan*') && !Request::is('loan_disbursement*') && !Request::is('loan_settlement*') && !Request::is('loan_reschedule*') ? 'active' : '' }}">Create Loans</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CHANGE_COLLECTOR')
                        <li class="mt-2">
                            <a href="/changeCollector" class="{{ Request::is('changeCollector*') ? 'active' : '' }}">Change Collector In Loan</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('PENDING_LOAN')
                        <li class="mt-2">
                            <a href="/pendingloan" class="{{ Request::is('pendingloan*') ? 'active' : '' }}">Pending Loans</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LOAN_DISBURSEMENT')
                        <li class="mt-2">
                            <a href="/loan_disbursement" class="{{ Request::is('loan_disbursement*') ? 'active' : '' }}">Loans Disbursement</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CURRENT_LOANS')
                        <li class="mt-2">
                            <a href="/payment_step_1" class="{{ Request::is('payment_step_1*') ? 'active' : '' }}">Current Loans</a>
                        </li>

                        <li class="mt-2">
                            <a href="/penalty-deduction" class="{{ Request::is('penalty-deduction*') ? 'active' : '' }}">Panelty Deduction</a>
                        </li>
                        @endhasPrivilege


                        @hasPrivilege('SETTLED_LOANS')
                        <li class="mt-2">
                            <a href="/showsettleloan" class="{{ Request::is('showsettleloan*') ? 'active' : '' }}">Settled Loans</a>
                        </li>
                        @endhasPrivilege
                    </ul>
                </div>
            </li>
            @endhasPrivilege

            {{-- CUSTOMER LEADS SECTION --}}
            @hasPrivilege('CUSTOMER_LEADS')
            <li class="side-nav-item mt-2">
                <a data-bs-toggle="collapse" href="#lead" aria-expanded="false"
                    aria-controls="sidebarPagesAuth" class="side-nav-link " data-tooltip="Lead">
                    <i class="ri-article-line"></i>
                    <span> Lead </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ Request::routeIs('business-categories*', 'leads*', 'online-leads*') ? 'show' : '' }}" id="lead">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('BUSSINESS_CATEGORIES')
                        <li class="mt-2">
                            <a href="{{ route('business-categories.index') }}" class="{{ Request::routeIs('business-categories.index') ? 'active' : '' }}">
                                Business Categories
                            </a>
                        </li>
                        @endhasPrivilege

                        @hasPrivilege('CREATE_LEAD')
                        <li class="mt-2">
                            <a href="{{ route('leads.index') }}" class="{{ Request::routeIs('leads.index') ? 'active' : '' }}">Lead Create</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CREATE_ONLINE_LEAD')
                        <li class="mt-2">
                            <a href="{{ route('online-leads.create') }}" class="{{ Request::routeIs('online-leads.create') ? 'active' : '' }}">Create Online Lead</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LEAD_APPROVALS')
                        <li class="mt-2">
                            <a href="{{ route('leads.approvals') }}" class="{{ Request::routeIs('leads.approvals') ? 'active' : '' }}">Lead Approvals</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VERIFY_LEAD')
                        <li class="mt-2">
                            <a href="{{ route('leads.verifyAction') }}" class="{{ Request::routeIs('leads.verifyAction') ? 'active' : '' }}">Lead Verification</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LEAD_AGREEMENT')
                        <li class="mt-2">
                            <a href="{{ route('leads.agreement') }}" class="{{ Request::routeIs('leads.agreement') ? 'active' : '' }}">Agreement Sign</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_LEAD_MAP')
                        <li class="mt-2">
                            <a href="{{ route('leads.globalMap') }}" class="{{ Request::routeIs('leads.globalMap') ? 'active' : '' }}">All Leads Map</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LEAD_ACTIVITY_LOGS')
                        <li class="mt-2">
                            <a href="{{ route('leads.activityLogs') }}" class="{{ Request::routeIs('leads.activityLogs') ? 'active' : '' }}">Activity Logs</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_LEADS')
                        <li class="mt-2">
                            <a href="{{ route('leads.verifiedList') }}" class="{{ Request::routeIs('leads.verifiedList') ? 'active' : '' }}">Lead List</a>
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
                <div class="collapse {{ Request::is('customers*', 'showcustomers*', 'showblacklistcustomers*', 'showcustomerssaving*', 'showcustomersrecovery*', 'kyc*', 'insurance*') || Request::routeIs('customers.map') ? 'show' : '' }}" id="customer">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_CUSTOMER')
                        <li class="mt-2">
                            <a href="/customers" class="{{ Request::is('customers*') && !Request::routeIs('customers.map') ? 'active' : '' }}">Add Customer</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_CUSTOMER')
                        <li class="mt-2">
                            <a href="/showcustomers" class="{{ Request::is('showcustomers*') ? 'active' : '' }}">View Customer</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_BLACKLIST_CUSTOMER')
                        <li class="mt-2">
                            <a href="/showblacklistcustomers" class="{{ Request::is('showblacklistcustomers*') ? 'active' : '' }}">View Blacklist Customer</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CUSTOMER_SAVING_ACC')
                        <li class="mt-2">
                            <a href="/showcustomerssaving" class="{{ Request::is('showcustomerssaving*') ? 'active' : '' }}">Customer Saving Acc.</a>
                        </li>
                        <li class="mt-2">
                            <a href="/showcustomersrecovery" class="{{ Request::is('showcustomersrecovery*') ? 'active' : '' }}">Customer Recovery Acc.</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('KYC')
                        <li class="mt-2">
                            <a href="/kyc" class="{{ Request::is('kyc*') ? 'active' : '' }}">KYC</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('INSURANCE')
                        <li class="mt-2">
                            <a href="/insurance" class="{{ Request::is('insurance*') ? 'active' : '' }}">Insurance</a>
                        </li>
                        @endhasPrivilege

                        <li class="mt-2">
                            <a href="{{ route('customers.map') }}" class="{{ Request::routeIs('customers.map') ? 'active' : '' }}">
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
                    <i class="ri-building-4-line"></i>
                    <span> Loan Center </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ Request::is('viewroutes*', 'center*', 'viewcenter*', 'customergroup*', 'viewgroups*', 'customergroupassign*') ? 'show' : '' }}" id="center">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('CREATE_ROUTE')
                        <li class="mt-2">
                            <a href="/viewroutes" class="{{ Request::is('viewroutes*') ? 'active' : '' }}">Create Route</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CREATE_CENTER')
                        <li class="mt-2">
                            <a href="/center" class="{{ Request::is('center*') ? 'active' : '' }}">Create Center</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_CENTER')
                        <li class="mt-2">
                            <a href="/viewcenter" class="{{ Request::is('viewcenter*') ? 'active' : '' }}">View Center</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('CREATE_GROUP')
                        <li class="mt-2">
                            <a href="/customergroup" class="{{ Request::is('customergroup*') && !Request::is('customergroupassign*') ? 'active' : '' }}">Create Group</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_GROUP')
                        <li class="mt-2">
                            <a href="/viewgroups" class="{{ Request::is('viewgroups*') ? 'active' : '' }}">View Group</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('ADD_CUSTOMER_TO_GROUP')
                        <li class="mt-2">
                            <a href="/customergroupassign" class="{{ Request::is('customergroupassign*') ? 'active' : '' }}">Add Customers To Group</a>
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
                    <i class="ri-shield-user-line"></i>
                    <span> Guarantee </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ Request::is('guardian*', 'showguardian*') ? 'show' : '' }}" id="Guarantee">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_GUARANTEE')
                        <li class="mt-2">
                            <a href="/guardian" class="{{ Request::is('guardian*') ? 'active' : '' }}">Add Guarantee</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_GUARANTEE')
                        <li class="mt-2">
                            <a href="/showguardian" class="{{ Request::is('showguardian*') ? 'active' : '' }}">View Guarantee</a>
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
                    <i class="ri-wallet-3-line"></i>
                    <span> Payment Details </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ Request::is('payment*', 'bulk_repayment*', 'loan_settlement*', 'loan_reschedule*', 'viewpayment*', 'collection*') && !Request::is('payment_step_1*') ? 'show' : '' }}" id="payment">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_REPAYMENT')
                        <li class="mt-2">
                            <a href="/payment" class="{{ Request::is('payment*') && !Request::is('payment_step_1*') ? 'active' : '' }}">Add Repayment</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('BULK_REPAYMENT')
                        <li class="mt-2">
                            <a href="/bulk_repayment" class="{{ Request::is('bulk_repayment*') ? 'active' : '' }}">Bulk Repayment</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LOAN_SETTLEMENT')
                        <li class="mt-2">
                            <a href="/loan_settlement" class="{{ Request::is('loan_settlement*') ? 'active' : '' }}">Loan Settlement</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('LOAN_RESCHEDULE')
                        <li class="mt-2">
                            <a href="/loan_reschedule" class="{{ Request::is('loan_reschedule*') ? 'active' : '' }}">Loan Reschedule</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_PAYMENT')
                        <li class="mt-2">
                            <a href="/viewpayment" class="{{ Request::is('viewpayment*') ? 'active' : '' }}">View Repayment</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('COLLECTOR_WISE_COLLECTION')
                        <li class="mt-2">
                            <a href="/collection" class="{{ Request::is('collection*') ? 'active' : '' }}">Collector Wise Collection</a>
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
                <div class="collapse {{ Request::is('VoucherDashboard*', 'PaymentVoucher*', 'PendingVouchers*', 'ApprovedPayments*', 'SupplierRegistration*') ? 'show' : '' }}" id="payment_voucher">
                    <ul class="side-nav-second-level">
                        <li class="mt-2">
                            <a href="/VoucherDashboard" class="{{ Request::is('VoucherDashboard*') ? 'active' : '' }}">Voucher Dashboard</a>
                        </li>
                        <li class="mt-2">
                            <a href="/PaymentVoucher" class="{{ Request::is('PaymentVoucher*') ? 'active' : '' }}">Create Voucher</a>
                        </li>
                        <li class="mt-2">
                            <a href="/PendingVouchers" class="{{ Request::is('PendingVouchers*') ? 'active' : '' }}">Pending Vouchers</a>
                        </li>
                        <li class="mt-2">
                            <a href="/ApprovedPayments" class="{{ Request::is('ApprovedPayments*') ? 'active' : '' }}">Voucher Payments</a>
                        </li>
                        <li class="mt-2">
                            <a href="/SupplierRegistration" class="{{ Request::is('SupplierRegistration*') ? 'active' : '' }}">Supplier Registration</a>
                        </li>
                    </ul>
                </div>
            </li>

            @hasPrivilege('LOAN_CALCULATOR')
            <li class="side-nav-item mt-2">
                <a href="/calculator" class="side-nav-link {{ Request::is('calculator*') ? 'active' : '' }}" data-tooltip="Loan Calculator">
                    <i class="ri-calculator-line"></i>
                    <span> Loan Calculator </span>
                </a>
            </li>
            @endhasPrivilege

            @hasPrivilege('CALENDAR')
            <li class="side-nav-item mt-2">
                <a href="/calender" class="side-nav-link {{ Request::is('calender*') ? 'active' : '' }}" data-tooltip="Calender">
                    <i class="ri-calendar-line"></i>
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
                <div class="collapse {{ Request::is('expenses*', 'view_expenses*') ? 'show' : '' }}" id="expences">
                    <ul class="side-nav-second-level">
                        @hasPrivilege('ADD_EXPENSES')
                        <li class="mt-2">
                            <a href="/expenses" class="{{ Request::is('expenses*') ? 'active' : '' }}">Add Expenses</a>
                        </li>
                        @endhasPrivilege
                        @hasPrivilege('VIEW_EXPENSES')
                        <li class="mt-2">
                            <a href="/view_expenses" class="{{ Request::is('view_expenses*') ? 'active' : '' }}">View Expenses</a>
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
                    <i class="ri-pie-chart-2-line"></i>
                    <span> Reports </span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse {{ Request::is('portfolio_performance*', 'loan-report*', 'PaymentFullDetailsReport*', 'prediction_report*', 'AllLoanDetailReport*', 'loansummaryreport*', 'par*', 'par_weekly*', 'loanStatus*', 'depletion*', 'investment*', 'arrears*', 'report/penalty-deduction*', 'reports/commission*', 'CashFlow*', 'CashFlowMonthly*', 'ProfitLoss*', 'BalanceSheet*', 'trialBalanceAccounting*', 'daily_repayment_sheet*', 'center_collection*', 'center_collection_summary*', 'root_wise_collection*') ? 'show' : '' }}" id="reports_section">
                    <ul class="side-nav-second-level">
                        <li class="side-nav-item mt-2">
                            <a data-bs-toggle="collapse" href="#main_report" aria-expanded="false"
                                class="side-nav-link">
                                <span> Main Reports </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse {{ Request::is('portfolio_performance*', 'loan-report*', 'PaymentFullDetailsReport*', 'prediction_report*', 'AllLoanDetailReport*', 'loansummaryreport*', 'par*', 'par_weekly*', 'loanStatus*', 'depletion*', 'investment*', 'arrears*', 'report/penalty-deduction*', 'reports/commission*') ? 'show' : '' }}" id="main_report">
                                <ul class="side-nav-third-level">
                                    @hasPrivilege('MAIN_REPORTS_DASHBOARD')
                                    <li class="mt-2">
                                        <a href="/portfolio_performance" class="{{ Request::is('portfolio_performance*') ? 'active' : '' }}">Portfolio & Performance -
                                            Dashboard</a>
                                    </li>
                                    @endhasPrivilege
                                    @hasPrivilege('LOAN_DISBURSEMENT_PERFORMANCE')
                                    <li class="mt-2">
                                        <a href="/loan-report" class="{{ Request::is('loan-report*') ? 'active' : '' }}">Loan Disbursement Performance -
                                            Dashboard</a>
                                    </li>
                                    @endhasPrivilege
                                    @hasPrivilege('PAYMENT_DETAIL_REPORT')
                                    <li class="mt-2">
                                        <a href="/PaymentFullDetailsReport" class="{{ Request::is('PaymentFullDetailsReport*') ? 'active' : '' }}">Payment Details Report</a>
                                    </li>
                                    <li class="mt-2">
                                        <a href="/prediction_report" class="{{ Request::is('prediction_report*') ? 'active' : '' }}">Payment Prediction</a>
                                    </li>
                                    @endhasPrivilege
                                    @hasPrivilege('FULL_LOAN_DETAIL')
                                    <li class="mt-2"><a href="/AllLoanDetailReport" class="{{ Request::is('AllLoanDetailReport*') ? 'active' : '' }}">Full Loan Detail Report</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('LOAN_SUMMARY')
                                    <li class="mt-2"><a href="/loansummaryreport" class="{{ Request::is('loansummaryreport*') ? 'active' : '' }}">Loan Summary Report</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('PAR_MONTHLY')
                                    <li class="mt-2"><a href="/par" class="{{ Request::is('par*') && !Request::is('par_weekly*') ? 'active' : '' }}">PAR (Monthly)</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('PAR_WEEKLY')
                                    <li class="mt-2"><a href="/par_weekly" class="{{ Request::is('par_weekly*') ? 'active' : '' }}">PAR (Weekly)</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('LOAN_STATUS')
                                    <li class="mt-2">
                                        <a href="/loanStatus" class="{{ Request::is('loanStatus*') ? 'active' : '' }}">Loan Status</a>
                                    </li>
                                    @endhasPrivilege

                                    <li class="mt-2">
                                        <a href="/depletion" class="{{ Request::is('depletion*') ? 'active' : '' }}">Depletion Report Executive Summary</a>
                                    </li>
                                    <li class="mt-2">
                                        <a href="/investment" class="{{ Request::is('investment*') ? 'active' : '' }}">Investment Report Executive Summary</a>
                                    </li>

                                    <li class="mt-2">
                                        <a href="/arrears" class="{{ Request::is('arrears*') ? 'active' : '' }}">Arrears Report Executive Summary</a>
                                    </li>

                                    <li class="mt-2">
                                        <a href="/report/penalty-deduction" class="{{ Request::is('report/penalty-deduction*') ? 'active' : '' }}">Penalty Deduction Report</a>
                                    </li>

                                    <li class="mt-2">
                                        <a href="/reports/commission" class="{{ Request::is('reports/commission*') ? 'active' : '' }}">Commission Report</a>
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
                            <div class="collapse {{ Request::is('CashFlow*', 'CashFlowMonthly*', 'ProfitLoss*', 'BalanceSheet*', 'trialBalanceAccounting*') ? 'show' : '' }}" id="acc_report">
                                <ul class="side-nav-third-level">
                                    @hasPrivilege('CASHFLOW_ACCUMULATED')
                                    <li class="mt-2"><a href="/CashFlow" class="{{ Request::is('CashFlow*') && !Request::is('CashFlowMonthly*') ? 'active' : '' }}">CashFlow Accumulated</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('CASHFLOW_MONTHLY')
                                    <li class="mt-2"><a href="/CashFlowMonthly" class="{{ Request::is('CashFlowMonthly*') ? 'active' : '' }}">CashFlow Monthly</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('PROFIT_LOSS')
                                    <li class="mt-2"><a href="/ProfitLoss" class="{{ Request::is('ProfitLoss*') ? 'active' : '' }}">Profit & Loss</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('BALANCE_SHEET')
                                    <li class="mt-2"><a href="/BalanceSheet" class="{{ Request::is('BalanceSheet*') ? 'active' : '' }}">Balance Sheet</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('TRIAL_BALANCE')
                                    <li class="mt-2"><a href="/trialBalanceAccounting" class="{{ Request::is('trialBalanceAccounting*') ? 'active' : '' }}">Trial Balance</a></li>
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
                            <div class="collapse {{ Request::is('daily_repayment_sheet*', 'center_collection*', 'center_collection_summary*', 'root_wise_collection*') ? 'show' : '' }}" id="collection_report">
                                <ul class="side-nav-third-level">
                                    @hasPrivilege('DAILY_COLLECTION_SHEET')
                                    <li class="mt-2"><a href="/daily_repayment_sheet" class="{{ Request::is('daily_repayment_sheet*') && !Request::is('daily_repayment_sheet_lasantha*') ? 'active' : '' }}">Daily Collection Sheet</a></li>
                                    <li class="mt-2"><a href="/daily_repayment_sheet_lasantha" class="{{ Request::is('daily_repayment_sheet_lasantha*') ? 'active' : '' }}">Daily Collection Sheet L</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('CENTER_COLLECTION_DETAIL')
                                    <li class="mt-2"><a href="/center_collection" class="{{ Request::is('center_collection*') && !Request::is('center_collection_summary*') ? 'active' : '' }}">Center Collection Detail</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('CENTER_COLLECTION_SUMMARY')
                                    <li class="mt-2"><a href="/center_collection_summary" class="{{ Request::is('center_collection_summary*') ? 'active' : '' }}">Center Collection
                                            Summary</a></li>
                                    @endhasPrivilege
                                    @hasPrivilege('ROUTE_COLLECTIONS')
                                    <li class="mt-2"><a href="/root_wise_collection" class="{{ Request::is('root_wise_collection*') ? 'active' : '' }}">Route Collections</a></li>
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
                <a href="{{ route('activity-logs.index') }}" class="side-nav-link {{ Request::routeIs('activity-logs.index') ? 'active' : '' }}" data-tooltip="Activity Logs">
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
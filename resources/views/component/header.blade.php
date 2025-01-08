<style>
    /* Adjustments for the Date and Time Display */
    .date-time {
        text-align: center;
    }

    .date-time h2 {
        margin: 0;
        font-size: 1.2rem; /* Adjust as needed */
        font-weight: 500;
    }

    #date {
        color: #a9a9a9; /* Adjust the color as needed */
    }

    #day {
        color: #57668a; /* Adjust the color as needed */
    }

    #time {
        font-size: 1.8rem; /* Increase the font size for the time */
    }

    /* Ensure alignment with other elements */
    .navbar-custom .topbar {
        padding: 0.5rem 2rem; /* Adjust padding as needed */
    }

    .navbar-custom .topbar-menu {
        margin-right: 1rem; /* Adjust margin as needed */
    }

    .logo-img {
        width: 60px; /* Adjust the width as necessary */
        height: auto; /* Maintains the aspect ratio */
        display: inline-block; /* Ensures it displays correctly with text */
        vertical-align: middle; /* Aligns the image vertically with text */
    }
</style>
<style>
    .logo-light, .logo-dark {
        display: flex;
        align-items: center;
        padding: 10px;
        text-decoration: none;
    }

    .logo-img {
        width: 70px; /* Adjust the size as needed */
        height: 70px; /* Adjust the size as needed */
        margin-right: 10px;
    }

    .rounded-logo {
        border-radius: 50%; /* Makes the image round */
        border: 2px solid #ddd; /* Optional: adds a border */
    }

    .logo-lg, .logo-sm {
        font-size: 10px; /* Adjust size as needed */
        font-weight: bold;
        color: #fff; /* Adjust color for better visibility */
        margin: 0;
    }

    .logo-lg {
        font-size: 15px;
    }

    .logo-sm {
        font-size: 15px;
    }

</style>


<div class="navbar-custom">
    <div class="topbar container-fluid">
        <div class="d-flex align-items-center gap-1">


            <!-- Sidebar Menu Toggle Button -->
            <button class="button-toggle-menu">
                <i class="ri-menu-line"></i>
            </button>

            <!-- Horizontal Menu Toggle Button -->
            <button class="navbar-toggle" data-bs-toggle="collapse" data-bs-target="#topnav-menu-content">
                <div class="lines">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

        </div>


                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        function updateTime() {
                            var now = new Date();
                            var hours = String(now.getHours()).padStart(2, '0');
                            var minutes = String(now.getMinutes()).padStart(2, '0');
                            var seconds = String(now.getSeconds()).padStart(2, '0');
                            var day = String(now.getDate()).padStart(2, '0');
                            var month = String(now.getMonth() + 1).padStart(2, '0'); // Months are zero-based
                            var year = now.getFullYear();

                            var formattedTime = hours + ':' + minutes + ':' + seconds;
                            var formattedDate = year + '/' + month + '/' + day;

                            document.getElementById('time').innerText = formattedTime;
                            document.getElementById('date').innerText = formattedDate;
                        }

                        updateTime(); // Initial call to display the time immediately
                        setInterval(updateTime, 1000); // Update every second
                    });
                </script>
                <div class="date-time">
                    <h2 id="date">{{ date('Y/m/d') }}</h2>
                    <h2 id="day">{{ date('l') }}</h2>
                </div>
        <?php
            $query = "SELECT * FROM branch where status=1";
            $branch = DB::select($query);
        ?>
        <div class="date-time">
            @if(session('branch_access')===1)
                <select class="form-control branch-select">
                    @foreach($branch as $item)
                        <option value="{{$item->branch_id}}" {{ session('branch_id') == $item->branch_id ? 'selected' : '' }}>
                            {{$item->Name}} Branch
                        </option>
                    @endforeach
                </select>
            @else
                <h2 id="date">{{ session('branch_name').' Branch' }}</h2>
            @endif
        </div>
        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="d-none d-sm-inline-block">
                <div class="nav-link" id="light-dark-mode">
                    <i class="ri-moon-line fs-22"></i>
                </div>
            </li>
            <?php
            $query = "SELECT * FROM company";
            $company = DB::select($query);
            ?>
            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#" role="button"
                   aria-haspopup="false" aria-expanded="false">
                                <span class="account-user-avatar">
                                    @foreach($company as $item)
                                        @php
                                            $logoPath = 'storage/' . $item->logo;
                                        @endphp
                                        @if ($item->logo && file_exists(public_path($logoPath)))
                                            <img src="{{asset($logoPath)}}" alt="user-image" width="32"
                                                 class="rounded-circle">
                                        @else
                                            <img src="{{asset('assets/images/users/avatar-1.jpg')}}" alt="user-image"
                                                 width="32" class="rounded-circle">
                                        @endif
                                    @endforeach

                                </span>
                    <span class="d-lg-block d-none">
                                    <h5 class="my-0 fw-normal">{{session('Full_Name')}} <i
                                            class="ri-arrow-down-s-line d-none d-sm-inline-block align-middle"></i></h5>
                                </span>
                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">

                    <!-- item-->
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>

                    <!-- item-->
                    <a href="/company" class="dropdown-item">
                        <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                        <span>My Account</span>
                    </a>

                    <!-- item-->
                    <a href="/setting" class="dropdown-item">
                        <i class="ri-settings-4-line fs-18 align-middle me-1"></i>
                        <span>Settings</span>
                    </a>

                    <?php

                    $query = "SELECT * FROM company";
                    $user_details = DB::select($query);
                    ?>

                    @foreach($user_details as $item)
                        @if($item->mask != null)
                            <!-- item-->
                            <a href="/sms" class="dropdown-item">
                                <i class="ri-mail-line fs-18 align-middle me-1"></i>
                                <span>SMS Format</span>
                            </a>
                        @else
                            <a href="#" class="dropdown-item">
                                <i class="ri-mail-line fs-18 align-middle me-1"></i>
                                <span>SMS Format</span>
                            </a>
                        @endif
                    @endforeach


                    <a href="/agreement" class="dropdown-item">
                        <i class="ri-file-paper-2-fill fs-18 align-middle me-1"></i>
                        <span>Document Format</span>
                    </a>

                    <a href="/holidays" class="dropdown-item">
                        <i class="ri-moon-clear-line fs-18 align-middle me-1"></i>
                        <span>Company Holidays</span>
                    </a>
                    @if(session('branch_access')===1)
                        <a href="/branch" class="dropdown-item">
                            <i class="ri-building-2-fill fs-18 align-middle me-1"></i>
                            <span>Branches</span>
                        </a>
                    @endif


                    <!-- item-->
                    <a href="#" class="dropdown-item">
                        <i class="ri-customer-service-2-line fs-18 align-middle me-1"></i>
                        <span>Support</span>
                    </a>


                    <!-- item-->
                    <a href="/logout" class="dropdown-item">
                        <i class="ri-logout-box-line fs-18 align-middle me-1"></i>
                        <span>Logout</span>
                    </a>

                </div>

            </li>
        </ul>

    </div>
</div>

<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="/" class="logo logo-light">
        @foreach($company as $item)
            @php
                $logoPath = 'storage/' . $item->logo;
            @endphp
                        @if ($item->logo && file_exists(public_path($logoPath)))
                            <img src="{{ asset($logoPath) }}" class="logo-img rounded-logo">
                        @else
                            <img src="https://via.placeholder.com/150" class="logo-img rounded-logo">
                        @endif
{{--            <span class="logo-lg">{{ $item->company_name }}</span>--}}
{{--            <span class="logo-sm">{{ $item->company_name }}</span>--}}
        @endforeach
    </a>

    <!-- Brand Logo Dark -->
    <a href="/" class="logo logo-dark">
        @foreach($company as $item)
            @php
                $logoPath = 'storage/' . $item->logo;
            @endphp
                        @if ($item->logo && file_exists(public_path($logoPath)))
                            <img src="{{ asset($logoPath) }}" class="logo-img rounded-logo">
                        @else
                            <img src="https://via.placeholder.com/150" class="logo-img rounded-logo">
                        @endif
{{--            <span class="logo-lg">{{ $item->company_name }}</span>--}}
{{--            <span class="logo-sm">{{ $item->company_name }}</span>--}}
        @endforeach
    </a>


    <?php
    $user_id = session('userid');
    $query = "SELECT * FROM user WHERE id = '$user_id'";
    $user_details = DB::select($query);
    ?>






        <!-- Sidebar -left -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!--- Sidemenu -->
        <ul class="side-nav">
            <li class="side-nav-title" style="color: red">{{$item->company_name}}</li>



            @if(count($user_details) > 0)
                @foreach($user_details as $item)
                    @if($item->dashboard == 1)
                        <li class="side-nav-item">
                            <a href="/" class="side-nav-link">
                                <i class="ri-dashboard-3-line"></i>
                                <span> Dashboard </span>
                            </a>
                        </li>
                    @endif

                    @if($item->customer == 1)
{{--                        <li class="side-nav-title">Customer Section</li>--}}
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#customer" aria-expanded="false"
                               aria-controls="sidebarPagesAuth" class="side-nav-link">
                                <i class="ri-group-2-line"></i>
                                <span> Customer </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="customer">
                                <ul class="side-nav-second-level">
                                    @if($item->add_customer == 1)
                                        <li>
                                            <a href="/customers">Add Customer</a>
                                        </li>
                                    @else
                                    @endif

                                    @if($item->view_customer == 1)
                                        <li>
                                            <a href="/showcustomers">View Customer</a>
                                        </li>

                                            <li>
                                                <a href="/showblacklistcustomers">View Blacklist Customer</a>
                                            </li>

                                       <li>
                                           <a href="/showcustomerssaving">Customer Saving Acc.</a>
                                       </li>
                                    @else
                                    @endif


                                </ul>
                            </div>

                        </li>
                    @else
                    @endif



                    @if($item->loan_center == 1)
{{--                        <li class="side-nav-title">Center Section</li>--}}
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#center" aria-expanded="false" aria-controls="center"
                               class="side-nav-link">
                                <i class="bi bi-building"></i>
                                <span> Loan Center </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="center">
                                <ul class="side-nav-second-level">

                                    @if($item->create_loan_center == 1)

                                        <li>
                                            <a href="/viewroutes">Create Route</a>
                                        </li>
                                        <li>
                                            <a href="/center">Create Center</a>
                                        </li>
                                    @else
                                    @endif

                                    @if($item->view_center == 1)
                                        <li>
                                            <a href="/viewcenter">View Center</a>
                                        </li>
                                    @else
                                    @endif

                                    @if($item->create_group == 1)
                                        <li>
                                            <a href="/customergroup">Create Group</a>
                                        </li>
                                    @else
                                    @endif


                                    @if($item->view_group == 1)
                                        <li>
                                            <a href="/viewgroups">View Group</a>
                                        </li>
                                    @else
                                    @endif
                                    @if($item->assign_customer_to_group == 1)
                                        <li>
                                            <a href="/customergroupassign">Add Customers To Group</a>
                                        </li>
                                    @else
                                    @endif

                                </ul>
                            </div>
                        </li>
                    @else
                    @endif


                    @if($item->guarantee == 1)
{{--                        <li class="side-nav-title">Guarantee Section</li>--}}
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#Guarantee" aria-expanded="false"
                               aria-controls="sidebarPagesAuth" class="side-nav-link">
                                <i class="ri-user-2-fill"></i>
                                <span> Guarantee </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="Guarantee">
                                <ul class="side-nav-second-level">
                                    @if($item->add_guarantee == 1)
                                        <li>
                                            <a href="/guardian">Add Guarantee</a>
                                        </li>
                                    @else
                                    @endif

                                    @if($item->view_guarantee == 1)
                                        <li>
                                            <a href="/showguardian">View Guarantee</a>
                                        </li>
                                    @else
                                    @endif


                                </ul>
                            </div>

                        </li>
                    @else
                    @endif



                    @if($item->product == 1)
{{--                        <li class="side-nav-title">Product Section</li>--}}
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#sidebarPages" aria-expanded="false"
                               aria-controls="sidebarPages" class="side-nav-link">
                                <i class="ri-pages-line"></i>
                                <span> Product / Loan  </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarPages">
                                <ul class="side-nav-second-level">
                                    @if($item->add_product == 1)
                                        <li>
                                            <a href="/product">Add Product</a>
                                        </li>
                                    @else
                                    @endif

                                    @if($item->view_product == 1)
                                        <li>
                                            <a href="/viewproduct">View Product</a>
                                        </li>
                                    @else
                                    @endif
                                    @if($item->issue_loan == 1)
                                        <li>
                                            <a href="/loan">Issue Loans</a>
                                        </li>
                                            <li>
                                                <a href="/changeCollector">Change Collector In Loan</a>
                                            </li>
                                    @else
                                    @endif
                                    @if($item->pending_loan == 1)
                                        <li>
                                            <a href="/pendingloan">Pending Loans</a>
                                        </li>
                                    @else
                                    @endif
                                    @if($item->current_loan == 1)
                                        <li>
                                            <a href="/payment_step_1">Current Loans</a>
                                        </li>
                                    @else
                                    @endif



                                </ul>
                            </div>
                        </li>
                    @else
                    @endif



                    @if($item->payment == 1)
{{--                        <li class="side-nav-title">Payment Section</li>--}}
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#payment" aria-expanded="false" aria-controls="center"
                               class="side-nav-link">
                                <i class="bi bi-currency-dollar"></i>
                                <span> Payment Details </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="payment">
                                <ul class="side-nav-second-level">
                                    @if($item->add_re_payment == 1)
                                        <li>
                                            <a href="/payment">Add Repayment</a>
                                        </li>
                                    @else
                                    @endif
                                        @if($item->add_re_payment == 1)
                                            <li>
                                                <a href="/loan_settlement">Loan Settlement</a>
                                            </li>
                                            <li>
                                                <a href="/showsettleloan">Settled Loans</a>
                                            </li>
                                        @else
                                    @endif
                                    @if($item->daily_payment == 1)
                                        <li>
                                            <a href="/daily">Daily Collection</a>
                                        </li>
                                    @else
                                    @endif
                                    @if($item->add_bulk_re_payment == 1)
                                        <li>
                                            <a href="/bulk_repayment">Bulk Repayment</a>
                                        </li>
                                    @else
                                    @endif

                                    @if($item->view_repayment == 1)
                                        <li>
                                            <a href="/viewpayment">View Repayment</a>
                                        </li>
                                            <li>
                                                <a href="/daily_repayment_sheet">Monthly Repayment Sheet</a>
                                            </li>
                                            <li>
                                                <a href="/daily_repayment_sheet_lasantha">Monthly Repayment Sheet Format</a>
                                            </li>
                                    @else
                                    @endif
                                    @if($item->pending_approval_repayment == 1)
                                        <li>
                                            <a href="/pending_collection">Pending Approval Repayments</a>
                                        </li>
                                    @else
                                    @endif
                                    @if($item->approval_repayment == 1)
                                        <li>
                                            <a href="/approved_collection">Approved Repayments</a>
                                        </li>
                                    @else
                                    @endif
                                    @if($item->agent_collection == 1)
                                        <li>
                                            <a href="/collection">Agent Collection</a>
                                        </li>
                                    @else
                                    @endif
                                    @if($item->view_repayment == 1)
                                        <li>
                                            <a href="/date_wise_installment">Date Wise Installment</a>
                                        </li>
                                    @else
                                    @endif


                                </ul>
                            </div>
                        </li>
                    @else
                    @endif


                        @if($item->account == 1)
                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#account" aria-expanded="false" aria-controls="center" class="side-nav-link">
                                    <i class="bi bi-universal-access"></i>
                                    <span> Account Center </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="account">
                                    <ul class="side-nav-second-level">
                                        @if($item->bank_details == 1)
                                            <li>
                                                <a href="/bank_account">Bank Account</a>
                                            </li>
                                            <li>
                                                <a href="/InnerBankTransfer">Internal Account Transfer</a>
                                            </li>
                                        @else
                                        @endif
                                        @if($item->chq_details == 1)
                                            <li>
                                                <a href="/chq">Cheque Details</a>
                                            </li>
                                        @else
                                        @endif
                                            @if($item->bank_details == 1)
                                                <li>
                                                    <a href="/collector_index">Collector Account</a>
                                                </li>
                                            @else
                                            @endif
                                    </ul>
                                </div>
                            </li>
                        @else
                        @endif





                        @if($item->account == 1)
                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#accountmanagement" aria-expanded="false" aria-controls="center" class="side-nav-link">
                                    <i class="bi bi-bank"></i>
                                    <span> Account Department </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="accountmanagement">
                                    <ul class="side-nav-second-level">
                                        <li>
                                            <a href="/AddAssetManagement">Add Asset Management</a>
                                        </li>
                                        <li>
                                            <a href="/AssetManagement">Asset Management</a>
                                        </li>

{{--                                        <li>--}}
{{--                                            <a href="/AddManualJournal">Asset Manual Journal</a>--}}
{{--                                        </li>--}}
                                        <li>
                                            <a href="/CashFlow">CashFlow Accumulated</a>
                                        </li>
                                        <li>
                                            <a href="/CashFlowMonthly">CashFlow Monthly</a>
                                        </li>
                                        <li>
                                            <a href="/ProfitLoss">Profit & Loss (P&L)</a>
                                        </li>
                                        <li>
                                            <a href="/BankReconciliation">Bank Reconciliation</a>
                                        </li>
                                        <li>
                                            <a href="/loanStatus">Loan Status</a>
                                        </li>

                                        <li>
                                            <a href="/BalanceSheet">Balance Sheet</a>
                                        </li>
                                        <li>
                                            <a href="/trialBalanceAccounting">Trial Balance</a>
                                        </li>

                                        <li>
                                            <a href="/ChartOfAccount">Chart Of Account</a>
                                        </li>
                                        <li>
                                            <a href="/ManualJournal">Manual Journal</a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @else
                        @endif





                    @if($item->loan_calculator == 1)
{{--                        <li class="side-nav-title">Calculator Section</li>--}}
                        <li class="side-nav-item">
                            <a href="/calculator" class="side-nav-link">
                                <i class="ri-dashboard-3-line"></i>
                                <span> Loan Calculator </span>
                            </a>
                        </li>
                    @else
                    @endif

                    @if($item->calender == 1)
{{--                        <li class="side-nav-title">Calendar Section</li>--}}
                        <li class="side-nav-item">
                            <a href="/calender" class="side-nav-link">
                                <i class="ri-dashboard-3-line"></i>
                                <span> Calender </span>
                            </a>
                        </li>
                    @else
                    @endif


                    @if($item->expenses == 1)
{{--                        <li class="side-nav-title">Expenses Section</li>--}}
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#expences" aria-expanded="false" aria-controls="expences"
                               class="side-nav-link">
                                <i class="ri-briefcase-line"></i>
                                <span> Expenses </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="expences">
                                <ul class="side-nav-second-level">
                                    @if($item->add_expenses == 1)
                                        <li>
                                            <a href="/expenses">Add Expenses</a>
                                        </li>
                                    @else
                                    @endif
                                    @if($item->view_expenses == 1)
                                        <li>
                                            <a href="/view_expenses">View Expenses</a>
                                        </li>
                                    @else
                                    @endif


                                </ul>
                            </div>
                        </li>
                    @else
                    @endif


                    @if($item->income == 1)
{{--                        <li class="side-nav-title">Income Section</li>--}}
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#income" aria-expanded="false" aria-controls="income"
                               class="side-nav-link">
                                <i class="ri-briefcase-line"></i>
                                <span> Other Income </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="income">
                                <ul class="side-nav-second-level">
                                    @if($item->add_income == 1)
                                        <li>
                                            <a href="/income">Add Income</a>
                                        </li>
                                    @else
                                    @endif

                                    @if($item->view_income == 1)
                                        <li>
                                            <a href="/view_income">View Income</a>
                                        </li>
                                    @else
                                    @endif


                                </ul>
                            </div>
                        </li>
                    @else
                    @endif


                    @if($item->user == 1)
{{--                        <li class="side-nav-title">User Account Section</li>--}}
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#user" aria-expanded="false" aria-controls="user"
                               class="side-nav-link collapsed">
                                <i class="ri-user-3-fill"></i>
                                <span> User </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="user" style="">
                                <ul class="side-nav-second-level">
                                    @if($item->create_user == 1)
                                        <li>
                                            <a href="/user" class="active">Create User</a>
                                        </li>
                                    @else
                                    @endif
                                    @if($item->user_privilage == 1)
                                        <li>
                                            <a href="/privileges">User Privileges</a>
                                        </li>
                                    @else
                                    @endif


                                </ul>
                            </div>
                        </li>
                    @else
                    @endif


                    @if($item->report == 1)
{{--                        <li class="side-nav-title">Report Section</li>--}}

                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#report" aria-expanded="false" aria-controls="expences"
                               class="side-nav-link">
                                <i class="ri-file-paper-2-fill"></i>
                                <span> Report </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="report">
                                <ul class="side-nav-second-level">
                                    @if($item->report_1 == 1)
                                        <li>
                                            <a href="/AllLoanDetailReport">Full Loan Detail Report</a>
                                        </li>
                                        <li>
                                            <a href="/loansummaryreport">Loan Summary Report</a>
                                        </li>
                                        <li>
                                            <a href="/LoanChargers">Loan Chargers Report</a>
                                        </li>
                                        <li>
                                            <a href="/dandlreport">D & L Report</a>
                                        </li>
                                        <li>
                                            <a href="/monthlyprofit">Loan Repayment Summary Report</a>
                                        </li>
                                        <li>
                                            <a href="/savings_report">Savings Report</a>
                                        </li>
                                        <li>
                                            <a href="/trialBalance">Trial Balance</a>
                                        </li>
                                    @else

                                    @endif
                                        @if($item->loan_in_arrease == 1)
                                            <li>
                                                <a href="/latePayment">Loan In Areas</a>
                                            </li>
                                        @else
                                        @endif
                                    @if($item->report_15 == 1)
                                        <li>
                                            <a href="/late_payment_report">Arrease Details</a>
                                        </li>
                                    @else

                                    @endif
                                    @if($item->report_16 == 1)
                                        <li>
                                            <a href="/ViewDateWiseCashFlow">Date wise cash flow details</a>
                                        </li>
                                    @else
                                    @endif

                                    @if($item->report_17 == 1)
                                                <li>
                                                    <a href="/MonthlyCollectionSummary">Monthly Collection Summary details</a>
                                                </li>
                                    @else
                                    @endif

                                    @if($item->report_2 == 1)
                                        <li>
                                            <a href="/customerreport_details">All Customer Details</a>
                                        </li>
                                            <li>
                                                <a href="/customerreport_details_recover_officer">Recover Officer Wise Customers</a>
                                            </li>
                                    @else
                                    @endif

                                    @if($item->report_3 == 1)
                                        <li>
                                            <a href="/loanreport">Loan Details</a>
                                        </li>
                                    @else
                                    @endif

                                    @if($item->report_4 == 1)
                                        <li>
                                            <a href="/borrowerreport">Guardian Details</a>
                                        </li>
                                    @else
                                    @endif

                                    @if($item->report_5 == 1)
                                        <li>
                                            <a href="/repaymentreport">Agent Wise Repayment Collection</a>
                                        </li>
                                    @else
                                    @endif


                                    @if($item->report_6 == 1)
{{--                                        <li>--}}
{{--                                            <a href="/customerrepaymentreport">Customer Wise Repayments</a>--}}
{{--                                        </li>--}}
                                    @else
                                    @endif


                                    @if($item->report_7 == 1)
                                        <li>
                                            <a href="/deduct_report">Deduction Report</a>
                                        </li>
                                    @else
                                    @endif


                                    @if($item->report_8 == 1)
                                        <li>
                                            <a href="/cashbook">CashBook Report</a>
                                        </li>

                                    @else
                                    @endif

                                    @if($item->report_9 == 1)
                                        <li>
                                            <a href="/par">PAR (Monthly)</a>
                                        </li>
                                            <li>
                                                <a href="/par_weekly">PAR (Weekly)</a>
                                            </li>

                                    @else
                                    @endif

                                    @if($item->report_10 == 1)
{{--                                        <li>--}}
{{--                                            <a href="#">Profit And Lost</a>--}}
{{--                                        </li>--}}

                                    @else
                                    @endif


                                    @if($item->report_11 == 1)
{{--                                        <li>--}}
{{--                                            <a href="gl_report">GL Report</a>--}}
{{--                                        </li>--}}

                                    @else
                                    @endif


                                    @if($item->report_12 == 1)
{{--                                        <li>--}}
{{--                                            <a href="#">Cash Flow Statement</a>--}}
{{--                                        </li>--}}

                                    @else
                                    @endif

                                    @if($item->report_13 == 1)
{{--                                        <li>--}}
{{--                                            <a href="#">Statement Of Financial Position</a>--}}
{{--                                        </li>--}}

                                    @else
                                    @endif @if($item->report_14 == 1)
                                        <li>
                                            <a href="/sms_history">SMS History Report</a>
                                        </li>

                                    @else
                                    @endif


                                </ul>
                            </div>
                        </li>
                    @else
                    @endif

                @endforeach
            @else
            @endif

        </ul>

        <div class="clearfix"></div>
    </div>
</div>



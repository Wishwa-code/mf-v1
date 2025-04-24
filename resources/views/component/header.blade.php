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

    <?php
        $query = "SELECT * FROM company where branch_id='" . session('branch_id') . "'";
        $banner = DB::select($query);
        ?>
        @foreach($banner as $item)
            @if($item->banner_status==1)
                .installment-banner {
                    background-color: #ff9800; /* Orange background */
                    color: #ffffff; /* White text */
                    text-align: center;
                    font-size: 16px;
                    font-weight: bold;
                    padding: 10px;
                    position: fixed; /* Ensures it's always on top */
                    width: 100%;
                    top: 0;
                    left: 0;
                    z-index: 1050; /* Higher than navbar */
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .navbar-custom {
                    margin-top: 40px; /* Push navbar down so it's not hidden under the banner */
                }

                .installment-banner p {
                    margin: 0;
                    flex: 1;
                }

                .installment-banner a {
                    color: #fff;
                    text-decoration: underline;
                }

                .close-banner {
                    background: none;
                    border: none;
                    color: white;
                    font-size: 18px;
                    cursor: pointer;
                    padding: 0 15px;
                }
             @endif
        @endforeach




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
    .enhanced-select {
        appearance: none; /* Remove default browser styles */
        background-color: #f3f3f3;
        border: 1px solid #ccc;
        border-radius: 8px;
        padding: 10px 15px;
        font-size: 16px;
        color: #333;
        cursor: pointer;
        width: 100%; /* Adjust width as per your requirement */
    }

    .enhanced-select:focus {
        border-color: #4caf50; /* Highlight border color */
        outline: none;
        box-shadow: 0px 0px 5px rgba(76, 175, 80, 0.5);
    }

    .enhanced-select option {
        padding: 10px; /* Add spacing for options */
    }

</style>

<div class="modal fade" id="cashierStartModal" tabindex="-1" aria-labelledby="cashierStartLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cashierStartLabel">Cashier Start Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="cashierForm">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" required>
                    </div>
                    <button type="button" class="btn btn-secondary" id="addToTable">Add to Table</button>
                </form>

                <!-- Table inside Modal -->
                <!-- Table inside Modal -->
                <h5 class="mt-3">Added Entries</h5>
                <table class="table mt-2">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Amount</th>
                        <th>Quantity</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="modalTableBody">
                    <!-- Entries will be added here -->
                    </tbody>
                </table>

                <!-- Grand Total Row -->
                <h5 class="mt-3">Grand Total: <span id="grandTotal">0</span></h5>


                <button type="button" class="btn btn-primary" id="saveEntries">Save</button>

            </div>
            <button type="button" class="btn btn-success mt-3" id="printDayStartReport">Print Day Start</button>
            <br>
        </div>
    </div>
</div>

<!-- Day End Modal -->
<div class="modal fade" id="dayEndModal" tabindex="-1" aria-labelledby="dayEndLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dayEndLabel">Day End Summary</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dayEndForm">
                    <!-- Starting Cash -->
                    <div class="mb-3">
                        <label class="form-label"><strong>Starting Cash (Plot Amount)</strong></label>
                        <input type="number" class="form-control" id="plotAmount" readonly>
                    </div>

                    <hr> <!-- Horizontal Line for Separation -->

                    <!-- Incomes Section -->
                    <u><h5 class="mt-3"><strong>Incomes</strong></h5></u>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Payment Amounts</label>
                            <input type="number" class="form-control" id="paymentAmounts" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Deposit</label>
                            <input type="number" class="form-control" id="deposit" readonly>
                        </div>
                    </div>
                    <h5 class="mt-3"><strong>Other Incomes</strong></h5>
                    <!-- Incomes Table -->
                    <table class="table table-bordered mt-3">
                        <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Source</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody id="incomeTableBody">
                        <!-- Income entries will be added dynamically -->
                        </tbody>
                    </table>
                    <h5 class="mt-3"><strong>Total Income: <span id="totalIncome">0.00</span></strong></h5>

                    <hr> <!-- Horizontal Line for Separation -->

                    <!-- Expenses (Pawning) Section -->
                    <u><h5 class="mt-3"><strong>Expenses</strong></h5></u>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Pawning Amount</label>
                            <input type="number" class="form-control" id="pawningAmount" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Withdrawal</label>
                            <input type="number" class="form-control" id="withdrawal" readonly>
                        </div>
                    </div>

                    <h5 class="mt-3"><strong>Other Expenses</strong></h5>
                    <!-- Expenses Table -->
                    <table class="table table-bordered mt-3">
                        <thead class="table-danger">
                        <tr>
                            <th>#</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody id="expensesTableBody">
                        <!-- Expenses entries will be added dynamically -->
                        </tbody>
                    </table>
                    <h5 class="mt-3"><strong>Total Expenses: <span id="totalExpenses">0.00</span></strong></h5>

                    <hr> <!-- Horizontal Line for Separation -->
                    <!-- Balance Calculation Section -->
                    <hr> <!-- Separator -->
                    <u><h5 class="mt-3"><strong>Balance Calculation</strong></h5></u>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label"><strong>Balance Amount</strong></label>
                            <input type="number" class="form-control" id="balanceAmount" readonly>
                        </div>
                    </div>
                    <hr>

                    <hr> <!-- Horizontal Line for Separation -->
                    <!-- Cash Drawer Balance Section -->
                    <h5 class="mt-3"><strong>Cash Drawer Balance</strong></h5>
                    <form id="cashDrawerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Denomination</label>
                                <input type="number" class="form-control" id="cashAmount" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" id="cashQuantity" required>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary mt-3" id="addCashToTable">Add to Table</button>
                    </form>

                    <!-- Cash Drawer Balance Table -->
                    <table class="table table-bordered mt-3">
                        <thead class="table-success">
                        <tr>
                            <th>#</th>
                            <th>Denomination</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody id="cashDrawerTableBody">
                        <!-- Entries will be added dynamically -->
                        </tbody>
                    </table>

                    <!-- Total Cash Drawer Balance -->
                    <h5 class="mt-3"><strong>Total Cash Drawer Balance: <span id="totalCashDrawer">0.00</span></strong></h5>

                    <!-- Balance Difference Display -->
                    <h5 class="mt-3 text-end"><strong>Balance Difference: <span id="balanceDifference" class="text-danger">0.00</span></strong></h5>

                    <div class="d-flex justify-content-between mt-4">
                        <!-- Save Button -->
                        <button type="button" class="btn btn-primary" id="saveDayEnd">Save Day End</button>
                        <button type="button" class="btn btn-success" id="printDayEndReport">Print Report</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$query = "SELECT * FROM company where branch_id='" . session('branch_id') . "'";
$banner = DB::select($query);
?>
@foreach($banner as $item)
    @if($item->banner_status==1)
        <div class="installment-banner">
            <p>📢 <strong>Reminder:</strong> {{$item->banner}}</p>
            <button class="close-banner" onclick="closeBanner()">✖</button>
        </div>

    @endif
@endforeach

<script>
    function closeBanner() {
        document.querySelector('.installment-banner').style.display = 'none';
    }
</script>



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
                <select class="form-control branch-select enhanced-select">
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
            $query = "SELECT * FROM company where branch_id='" . session('branch_id') . "'";
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
                        $query = "SELECT * FROM company where branch_id='" . session('branch_id') . "'";
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

                    <hr>

                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Cashier Section</h6>
                    </div>

                    <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#cashierStartModal">
                        <i class="ri-money-dollar-box-line font-size-17 align-middle me-1"></i> Cashier Start
                    </a>
                    <a class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#dayEndModal">
                        <i class="mdi mdi-lock-open-outline font-size-17 align-middle me-1"></i> Cashier Close
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
            <?php
            $query = "SELECT * FROM company where branch_id='" . session('branch_id') . "'";
            $check = DB::select($query);
            ?>


            @foreach($check as $item)
                <li class="side-nav-title" style="color: red">{{$item->company_name}}</li>
            @endforeach




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
                                            <li>
                                                <a href="/kyc">KYC</a>
                                            </li>
                                            <li>
                                                <a href="/insurance">Insurance</a>
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
                                            <a href="/loan">Create Loans</a>
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
                                            <li>
                                                <a href="/loan_disbursement">Loans Disbursement</a>
                                            </li>
                                    @else
                                    @endif
                                    @if($item->current_loan == 1)
                                        <li>
                                            <a href="/payment_step_1">Current Loans</a>
                                        </li>
                                            <li>
                                                <a href="/showsettleloan">Settled Loans</a>
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
                                        @if($item->add_bulk_re_payment == 1)
                                            <li>
                                                <a href="/bulk_repayment">Bulk Repayment</a>
                                            </li>
                                        @else
                                        @endif
                                        @if($item->add_re_payment == 1)
                                            <li>
                                                <a href="/loan_settlement">Loan Settlement</a>
                                            </li>

{{--                                            <li>--}}
{{--                                                <a href="/loan_reschedule">Loan Reschedule</a>--}}
{{--                                            </li>--}}
                                        @else
                                    @endif



                                    @if($item->view_repayment == 1)
                                        <li>
                                            <a href="/viewpayment">View Repayment</a>
                                        </li>
                                    @else
                                    @endif
{{--                                    @if($item->approval_repayment == 1)--}}
{{--                                        <li>--}}
{{--                                            <a href="/approved_collection">Approved Repayments</a>--}}
{{--                                        </li>--}}
{{--                                    @else--}}
{{--                                    @endif--}}
                                    @if($item->agent_collection == 1)
                                        <li>
                                            <a href="/collection">Collector Wise Collection</a>
                                        </li>
                                    @else
                                    @endif
{{--                                    @if($item->view_repayment == 1)--}}
{{--                                        <li>--}}
{{--                                            <a href="/date_wise_installment">Date Wise Installment</a>--}}
{{--                                        </li>--}}
{{--                                    @else--}}
{{--                                    @endif--}}


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
                                                <a href="/bank_account">Bank/Cash Account</a>
                                            </li>

                                            <li>
                                                <a href="/InnerBankTransfer">Internal Account Transfer</a>
                                            </li>
                                        @else
                                        @endif
                                            @if($item->bank_details == 1)
                                                <li>
                                                    <a href="/collector_index">Collector Account</a>
                                                </li>
                                            @else
                                            @endif
                                        @if($item->chq_details == 1)
                                            <li>
                                                <a href="/chq">Cheque Details</a>
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
                                        <li>
                                            <a href="/BankReconciliation">Bank Reconciliation</a>
                                        </li>
                                        <li>
                                            <a href="/ManualJournal">Manual Journal</a>
                                        </li>
                                        <li>
                                            <a href="/ChartOfAccount">Chart Of Account</a>
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


{{--                    @if($item->income == 1)--}}
{{--                        <li class="side-nav-title">Income Section</li>--}}
{{--                        <li class="side-nav-item">--}}
{{--                            <a data-bs-toggle="collapse" href="#income" aria-expanded="false" aria-controls="income"--}}
{{--                               class="side-nav-link">--}}
{{--                                <i class="ri-briefcase-line"></i>--}}
{{--                                <span> Other Income </span>--}}
{{--                                <span class="menu-arrow"></span>--}}
{{--                            </a>--}}
{{--                            <div class="collapse" id="income">--}}
{{--                                <ul class="side-nav-second-level">--}}
{{--                                    @if($item->add_income == 1)--}}
{{--                                        <li>--}}
{{--                                            <a href="/income">Add Income</a>--}}
{{--                                        </li>--}}
{{--                                    @else--}}
{{--                                    @endif--}}

{{--                                    @if($item->view_income == 1)--}}
{{--                                        <li>--}}
{{--                                            <a href="/view_income">View Income</a>--}}
{{--                                        </li>--}}
{{--                                    @else--}}
{{--                                    @endif--}}


{{--                                </ul>--}}
{{--                            </div>--}}
{{--                        </li>--}}
{{--                    @else--}}
{{--                    @endif--}}


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
                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#reports_section" aria-expanded="false" class="side-nav-link">
                                    <i class="ri-file-paper-2-fill"></i>
                                    <span> Reports </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="reports_section">
                                    <ul class="side-nav-second-level">

                                        {{-- Main Reports --}}
                                        <li class="side-nav-item">
                                            <a data-bs-toggle="collapse" href="#main_report" aria-expanded="false" class="side-nav-link">
                                                <span> Main Reports </span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse" id="main_report">
                                                <ul class="side-nav-third-level">
                                                    <li>
                                                        <a href="/portfolio_performance">Portfolio & Performance - Dashboard</a>
                                                    </li>
                                                    <li>
                                                        <a href="/loan-report">Loan Disbursement Performance - Dashboard</a>
                                                    </li>
                                                    <li>
                                                        <a href="/PaymentFullDetailsReport">Payment Details Report</a>
                                                    </li>
                                                    <li><a href="/AllLoanDetailReport">Full Loan Detail Report</a></li>
                                                    <li><a href="/loansummaryreport">Loan Summary Report</a></li>
                                                    @if($item->report_9 == 1)
                                                        <li><a href="/par">PAR (Monthly)</a></li>
                                                        <li><a href="/par_weekly">PAR (Weekly)</a></li>
                                                    @endif
                                                    <li>
                                                        <a href="/loanStatus">Loan Status</a>
                                                    </li>

                                                </ul>
                                            </div>
                                        </li>

                                        <li class="side-nav-item">
                                            <a data-bs-toggle="collapse" href="#acc_report" aria-expanded="false" class="side-nav-link">
                                                <span> Accounting Reports </span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse" id="acc_report">
                                                <ul class="side-nav-third-level">
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
                                                        <a href="/BalanceSheet">Balance Sheet</a>
                                                    </li>
                                                    <li>
                                                        <a href="/trialBalanceAccounting">Trial Balance</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>

                                        <li class="side-nav-item">
                                            <a data-bs-toggle="collapse" href="#payment_report" aria-expanded="false" class="side-nav-link">
                                                <span> Payment Reports </span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse" id="payment_report">
                                                <ul class="side-nav-third-level">
                                                    <li>
                                                        <a href="/daily">Daily Collection Sheet</a>
                                                    </li>
                                                    <li>
                                                        <a href="/center_collection">Center Wise collection Detail</a>
                                                    </li>
                                                    <li>
                                                        <a href="/center_collection_summary">Center Wise collection Summary</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="side-nav-item">
                                            <a data-bs-toggle="collapse" href="#repayment_report" aria-expanded="false" class="side-nav-link">
                                                <span> Re Payment Reports </span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse" id="repayment_report">
                                                <ul class="side-nav-third-level">
                                                    <li>
                                                        <a href="/daily_repayment_sheet">Repayment Sheet 01</a>
                                                    </li>
                                                    <li>
                                                        <a href="/RightWayDailyRepayment">Repayment Sheet 02</a>
                                                    </li>
                                                    <li>
                                                        <a href="/daily_repayment_sheet_hm">Repayment Sheet 03</a>
                                                    </li>
                                                    <li>
                                                        <a href="/daily_repayment_sheet_lasantha">Repayment Sheet 04</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </li>
                                        <li class="side-nav-item">
                                            <a data-bs-toggle="collapse" href="#sub_report" aria-expanded="false" class="side-nav-link">
                                                <span> Sub Reports </span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse" id="sub_report">
                                                <ul class="side-nav-third-level">
                                                    @if($item->report_1 == 1)
                                                        <li><a href="/LoanChargers">Loan Chargers Report</a></li>
                                                        <li><a href="/dandlreport">Center Collection Dashboard</a></li>
                                                        <li><a href="/monthlyprofit">Loan Repayment Summary Report</a></li>
                                                        <li><a href="/savings_report">Savings Report</a></li>

                                                    @endif

                                                    @if($item->loan_in_arrease == 1)
                                                        <li><a href="/latePayment">Loan In Areas</a></li>
                                                    @endif

                                                    @if($item->report_15 == 1)
                                                        <li><a href="/late_payment_report">Arrease Details</a></li>
                                                    @endif

                                                    @if($item->report_16 == 1)
                                                        <li><a href="/ViewDateWiseCashFlow">Date Wise Cash Flow Details</a></li>
                                                    @endif

{{--                                                    @if($item->report_17 == 1)--}}
{{--                                                        <li><a href="/MonthlyCollectionSummary">Monthly Collection Summary Details</a></li>--}}
{{--                                                    @endif--}}



                                                    @if($item->report_3 == 1)
                                                        <li><a href="/loanreport">Loan Details</a></li>
                                                    @endif



                                                    @if($item->report_5 == 1)
                                                        <li><a href="/repaymentreport">Collector Wise Repayment Collection</a></li>
                                                    @endif

                                                    {{--                                                    @if($item->report_7 == 1)--}}
                                                    {{--                                                        <li><a href="/deduct_report">Deduction Report</a></li>--}}
                                                    {{--                                                    @endif--}}



                                                    @if($item->report_14 == 1)
                                                        <li><a href="/sms_history">SMS History Report</a></li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </li>

                                        <li class="side-nav-item">
                                            <a data-bs-toggle="collapse" href="#people_report" aria-expanded="false" class="side-nav-link">
                                                <span> People Reports </span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse" id="people_report">
                                                <ul class="side-nav-third-level">
                                                    @if($item->report_2 == 1)
                                                        <li><a href="/customerreport_details">All Customer Details</a></li>
                                                        <li><a href="/customerreport_details_recover_officer">Recover Officer Wise Customers</a></li>
                                                    @endif
                                                        @if($item->report_4 == 1)
                                                            <li><a href="/borrowerreport">Guardian Details</a></li>
                                                        @endif
{{--                                                        <li><a href="#">User Logs</a></li>--}}
                                                </ul>


                                            </div>
                                        </li>

                                        {{-- Sub Reports --}}


                                    </ul>
                                </div>
                            </li>
                        @endif


                @endforeach
            @else
            @endif

        </ul>

        <div class="clearfix"></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script>
    $(document).ready(function () {
        // Function to update the Grand Total
        function updateGrandTotal() {
            let grandTotal = 0;
            $("#modalTableBody tr").each(function () {
                let total = parseFloat($(this).find(".total-amount").text()) || 0;
                grandTotal += total;
            });
            $("#grandTotal").text(grandTotal.toFixed(2));
        }
        // Load today's saved data when modal opens
        $("#cashierStartModal").on('show.bs.modal', function () {
            {{--$.ajax({--}}
            {{--    url: "{{ route('cashier.getTodayData') }}",--}}
            {{--    method: "GET",--}}
            {{--    success: function (response) {--}}
            {{--        if (response.status === 'success') {--}}
            {{--            let tableBody = $("#modalTableBody");--}}
            {{--            tableBody.empty(); // Clear existing entries--}}

            {{--            response.entries.forEach((entry, index) => {--}}
            {{--                tableBody.append(`--}}
            {{--                <tr data-amount="${entry.money}">--}}
            {{--                    <td>${index + 1}</td>--}}
            {{--                    <td class="amount">${entry.money}</td>--}}
            {{--                    <td class="quantity">${entry.qty}</td>--}}
            {{--                    <td class="total-amount">${entry.amount}</td>--}}
            {{--                    <td><button class="btn btn-danger btn-sm remove-entry">Remove</button></td>--}}
            {{--                </tr>--}}
            {{--            `);--}}
            {{--            });--}}

            {{--            $("#grandTotal").text(response.grandTotal);--}}
            {{--        }--}}
            {{--    },--}}
            {{--    error: function () {--}}
            {{--        Swal.fire({--}}
            {{--            icon: 'error',--}}
            {{--            title: 'Error',--}}
            {{--            text: 'Failed to load saved data.',--}}
            {{--        });--}}
            {{--    }--}}
            {{--});--}}
        });

        // Add to table with validation
        $("#addToTable").click(function () {
            let amount = parseFloat($("#amount").val());
            let quantity = parseInt($("#quantity").val());

            if (!amount || !quantity || amount <= 0 || quantity <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Input',
                    text: 'Please enter valid amount and quantity!',
                });
                return;
            }

            let totalAmount = amount * quantity;
            let existingRow = $("#modalTableBody").find(`tr[data-amount='${amount}']`);

            if (existingRow.length > 0) {
                let existingQuantity = parseInt(existingRow.find(".quantity").text());
                let newQuantity = existingQuantity + quantity;
                let newTotal = amount * newQuantity;

                existingRow.find(".quantity").text(newQuantity);
                existingRow.find(".total-amount").text(newTotal.toFixed(2));

                Swal.fire({
                    icon: 'info',
                    title: 'Updated Entry',
                    text: `Quantity updated for Amount: ${amount}`,
                });

            } else {
                let rowCount = $("#modalTableBody tr").length + 1;
                $("#modalTableBody").append(`
                <tr data-amount="${amount}">
                    <td>${rowCount}</td>
                    <td class="amount">${amount}</td>
                    <td class="quantity">${quantity}</td>
                    <td class="total-amount">${totalAmount.toFixed(2)}</td>
                    <td><button class="btn btn-danger btn-sm remove-entry">Remove</button></td>
                </tr>
            `);

                Swal.fire({
                    icon: 'success',
                    title: 'Added Successfully',
                    text: `Amount: ${amount}, Quantity: ${quantity}, Total: ${totalAmount.toFixed(2)}`,
                });
            }

            updateGrandTotal();
            $("#amount").val('');
            $("#quantity").val('');
        });
    });
</script>


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

    /* Modern Branch Switcher Styles */
    .modern-branch-switcher {
        position: relative;
        display: inline-block;
    }
    
    .modern-dropdown-toggle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        padding: 12px 20px;
        color: white;
        font-weight: 500;
        font-size: 14px;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-width: 180px;
        position: relative;
        overflow: hidden;
    }
    
    .modern-dropdown-toggle::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
        transition: left 1.2s ease-in-out;
    }
    
    .modern-dropdown-toggle:hover::before {
        left: 100%;
    }
    
    .modern-dropdown-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #5a6fd8 0%, #6b4190 100%);
    }
    
    .modern-dropdown-toggle:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
    }
    
    .modern-dropdown-toggle .dropdown-arrow {
        transition: transform 0.3s ease;
        margin-left: auto;
    }
    
    .modern-dropdown-toggle[aria-expanded="true"] .dropdown-arrow {
        transform: rotate(180deg);
    }
    
    .modern-dropdown-toggle[aria-expanded="true"] {
        background: linear-gradient(135deg, #5a6fd8 0%, #6b4190 100%) !important;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    .modern-dropdown-toggle[aria-expanded="true"] .branch-text {
        color: #ffffff !important;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        font-weight: 700;
    }
    
    .modern-dropdown-menu {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        padding: 8px;
        margin-top: 8px;
        background: white;
        backdrop-filter: blur(10px);
        min-width: 180px;
    }
    
    .modern-dropdown-item {
        border-radius: 8px;
        padding: 12px 16px;
        margin: 2px 0;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
        color: #4a5568;
        text-decoration: none;
        font-weight: 500;
        position: relative;
        overflow: hidden;
    }
    
    .modern-dropdown-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 0;
        height: 100%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        transition: width 0.3s ease;
        z-index: -1;
    }
    
    .modern-dropdown-item:hover::before {
        width: 100%;
    }
    
    .modern-dropdown-item:hover {
        color: white;
        transform: translateX(4px);
        background: transparent;
    }
    
    .modern-dropdown-item i {
        transition: all 0.2s ease;
    }
    
    .modern-dropdown-item:hover i {
        color: white;
    }
    
    .branch-text {
        font-weight: 700;
        letter-spacing: 0.5px;
        color: #ffffff;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        font-size: 14px;
        display: inline-block;
        min-width: 100px;
        text-align: left;
    }
    
    .modern-dropdown-toggle i {
        color: #ffffff;
        opacity: 0.9;
    }
    
    .modern-dropdown-toggle:hover i,
    .modern-dropdown-toggle[aria-expanded="true"] i {
        opacity: 1;
        color: #ffffff;
    }

</style>

<div class="modal fade" id="cashierStartModal" tabindex="-1" aria-labelledby="cashierStartLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cashierStartLabel">Cashier Start Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="cashierForm">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount</label>
                        <select class="form-control enhanced-select" id="amount" required>
                            <option value="">Select Amount</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="500">500</option>
                            <option value="1000">1000</option>
                            <option value="5000">5000</option>
                        </select>

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

                    <hr>
                    <h5 class="mt-3"><strong>Cash In</strong></h5>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-bordered">
                            <thead class="table-success sticky-top" style="top: 0; z-index: 1;">
                            <tr>
                                <th>#</th>
                                <th>Date Time</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody id="cashInTableBody">
                            <!-- Cash In entries will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <h5 class="mt-3"><strong>Total Income: <span id="totalIncome">0.00</span></strong></h5>


                    <hr>
                    <h5 class="mt-3"><strong>Cash Out</strong></h5>
                    <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                        <table class="table table-bordered">
                            <thead class="table-danger sticky-top" style="top: 0; z-index: 1;">
                            <tr>
                                <th>#</th>
                                <th>Date Time</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                            </tr>
                            </thead>
                            <tbody id="cashOutTableBody">
                            <!-- Cash Out entries will be loaded here -->
                            </tbody>
                        </table>
                    </div>
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
                    <u><h5 class="mt-3"><strong>Cash Drawer Balance</strong></h5></u>
                    <form id="cashDrawerForm">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Select Denomination</label>
                                <select id="cashAmount" class="form-control enhanced-select" required>
                                    <option value="">Select Denomination</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="5000">5000</option>
                                </select>
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

@php

    if (!\Illuminate\Support\Facades\Schema::hasTable('user_privileges_has_user')) {
        \Illuminate\Support\Facades\Schema::create('user_privileges_has_user', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('permission_key');
            $table->tinyInteger('value')->default(0);
        });
    }

    // ✅ Now whether the table was just created or already exists, check if the user has permissions
    $userId = (int)session('userid');
    $hasPermissions = DB::table('user_privileges_has_user')
        ->where('user_id', $userId)
        ->exists();

    if (!$hasPermissions) {
        // Permissions arrays
        $mainPermissions = [
            'Customer' => ['customer','add_customer', 'view_customer', 'view_blacklist_customer', 'customer_saving_acc', 'kyc', 'insurance'],
            'Loan Center' => ['loan_center','create_route', 'create_center', 'view_center', 'create_group', 'view_group', 'add_customer_to_group'],
            'Guarantee' => ['guarantee','add_guarantee', 'view_guarantee'],
            'Product' => ['product','add_product', 'view_product', 'create_loan', 'change_collector', 'pending_loan', 'loan_disbursement', 'current_loans', 'settled_loans'],
            'Payment Details' => ['payment_details','add_repayment', 'bulk_repayment', 'loan_settlement', 'loan_reschedule', 'view_payment', 'collector_wise_collection'],
            'Account Center' => ['account_center','bank_cash_account', 'internal_bank_transfer', 'collector_account', 'cheque_details'],
            'Account Department' => ['account_department','add_asset', 'asset_management', 'bank_reconciliation', 'manual_journal', 'chart_of_account'],
            'Loan Calculator' => ['loan_calculator'],
            'Calender' => ['calender','calendar'],
            'Expenses' => ['expenses','add_expenses', 'view_expenses'],
            'User' => ['user','create_user', 'user_privileges'],
            'Reports' => [
                'reports','main_reports_dashboard','prediction_report', 'loan_disbursement_performance', 'payment_detail_report', 'full_loan_detail', 'loan_summary',
                'par_monthly', 'par_weekly', 'loan_status', 'cashflow_accumulated', 'cashflow_monthly', 'profit_loss', 'balance_sheet',
                'trial_balance', 'daily_collection_sheet', 'center_collection_detail', 'center_collection_summary', 'route_collections',
                'repayment_sheet_01', 'repayment_sheet_02', 'repayment_sheet_03', 'repayment_sheet_04', 'repayment_sheet_05','repayment_sheet_06','repayment_sheet_07','repayment_sheet_08','repayment_sheet_09', 'other_charges_report',
                'center_dashboard', 'repayment_summary', 'savings_report', 'arrears_report', 'arrears_overview', 'datewise_cashflow',
                'loan_detail_report', 'collector_report', 'sms_history', 'customer_detail_report', 'officer_customer_detail', 'guardian_detail_report'
            ]
        ];

        $settingsPermissions = [
            'Settings Privilege' => ['my_account', 'settings', 'sms_format', 'document_format', 'company_holidays', 'branches', 'cashier_start', 'cashier_close']
        ];

        $deletePermissions = [
            'Access' => ['dashboard','payment_delete','current_loan_delete','loan_extra_charges','current_loan_agreement','branch_access']
        ];

        $allPermissions = array_merge(
            ...array_values($mainPermissions),
            ...array_values($settingsPermissions),
            ...array_values($deletePermissions)
        );

        $insertData = [];
        foreach ($allPermissions as $perm) {
            $insertData[] = [
                'user_id' => $userId,
                'permission_key' => $perm,
                'value' => 1
            ];
        }

        DB::table('user_privileges_has_user')->insert($insertData);
    }



        if (!session('userid')) {
            echo "<script>window.location.href = '".route('login')."'</script>";
            exit;
        }

        $user_id = session('userid');
        $permissions = DB::table('user_privileges_has_user')
            ->where('user_id', $user_id)
            ->pluck('value', 'permission_key')
            ->toArray();

        $privilege = new \stdClass();
        foreach ($permissions as $key => $value) {
            $privilege->$key = $value;
        }
@endphp


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
            // All active branches (used for super users)
            $query = "SELECT * FROM branch where status=1";
            $branch = DB::select($query);

            // Allowed branches for the current user (used for non-super users)
            $allowedBranches = collect([]);
            if (session('branch_access') !== 1) {
                $userId = session('userid');
                if ($userId) {
                    $allowedBranches = \Illuminate\Support\Facades\DB::table('user_has_branches')
                        ->join('branch', 'user_has_branches.branch_id', '=', 'branch.branch_id')
                        ->where('user_has_branches.user_id', $userId)
                        ->where('branch.status', 1)
                        ->select('branch.branch_id', 'branch.Name')
                        ->get();
                }
            }
        ?>
        <div class="date-time">
            @if(session('branch_access')===1)
                <div class="modern-branch-switcher">
                    <div class="dropdown">
                        <button type="button" class="btn modern-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="ri-building-2-line me-2"></i>
                            <span class="branch-text">
                                @foreach($branch as $item)
                                    @if(session('branch_id') == $item->branch_id)
                                        {{$item->Name}} Branch
                                    @endif
                                @endforeach
                            </span>
                            <i class="ri-arrow-down-s-line ms-2 dropdown-arrow"></i>
                        </button>
                        <ul class="dropdown-menu modern-dropdown-menu">
                            @foreach($branch as $item)
                                <li>
                                    <a class="dropdown-item modern-dropdown-item branch-option"
                                       href="#"
                                       data-branch-id="{{ $item->branch_id }}"
                                       data-branch-name="{{ $item->Name }} Branch">
                                        <i class="ri-building-2-line me-2"></i>
                                        {{ $item->Name }} Branch
                                        @if(session('branch_id') == $item->branch_id)
                                            <i class="ri-check-line ms-auto text-success"></i>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                    </div>
                </div>
            @else
                @if(isset($allowedBranches) && $allowedBranches->count() > 1)
                    <div class="modern-branch-switcher">
                        <div class="dropdown">
                            <button type="button" class="btn modern-dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-building-2-line me-2"></i>
                                <span class="branch-text">
                                    @php $currentName = optional($allowedBranches->firstWhere('branch_id', session('branch_id')))->Name; @endphp
                                    {{ ($currentName ?? session('branch_name')).' Branch' }}
                                </span>
                                <i class="ri-arrow-down-s-line ms-2 dropdown-arrow"></i>
                            </button>
                            <ul class="dropdown-menu modern-dropdown-menu">
                                @foreach($allowedBranches as $item)
                                    <li>
                                        <a class="dropdown-item modern-dropdown-item branch-option" href="#"
                                           data-branch-id="{{ $item->branch_id }}"
                                           data-branch-name="{{ $item->Name }} Branch">
                                            <i class="ri-building-2-line me-2"></i>
                                            {{ $item->Name }} Branch
                                            @if(session('branch_id') == $item->branch_id)
                                                <i class="ri-check-line ms-auto text-success"></i>
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @else
                    <h2 id="date">{{ session('branch_name').' Branch' }}</h2>
                @endif
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
                    @if($privilege)
                        @if(optional($privilege)->my_account == 1)
                            <a href="/company" class="dropdown-item">
                                <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                                <span>My Account</span>
                            </a>
                        @endif

                            @if(optional($privilege)->settings == 1)
                                <a href="/setting" class="dropdown-item">
                                    <i class="ri-settings-4-line fs-18 align-middle me-1"></i>
                                    <span>Settings</span>
                                </a>
                            @endif

                            @if(optional($privilege)->sms_format == 1)


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

                            @endif

                            @if(optional($privilege)->document_format == 1)
                                <a href="/agreement" class="dropdown-item">
                                    <i class="ri-file-paper-2-fill fs-18 align-middle me-1"></i>
                                    <span>Document Format</span>
                                </a>
                            @endif

                            @if(optional($privilege)->company_holidays == 1)
                                <a href="/holidays" class="dropdown-item">
                                    <i class="ri-moon-clear-line fs-18 align-middle me-1"></i>
                                    <span>Company Holidays</span>
                                </a>
                            @endif

                            @if(optional($privilege)->branches == 1)
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

                            <?php
                                $user_id = session('userid');
                                $cashier = DB::table('user')
                                    ->where('id', $user_id)
                                    ->where('cashier','=','1')
                                    ->first();
                            ?>
                            @if($cashier)
                                <hr>
                                <div class=" dropdown-header noti-title">
                                    <h6 class="text-overflow m-0">Cashier Section</h6>
                                </div>
                                @if(optional($privilege)->cashier_start == 1)
                                    <a class="dropdown-item d-flex align-items-center" href="#" data-bs-toggle="modal" data-bs-target="#cashierStartModal">
                                        <i class="ri-money-dollar-box-line font-size-17 align-middle me-1"></i> Cashier Start
                                    </a>
                                @endif

                                @if(optional($privilege)->cashier_close == 1)
                                    <a class="dropdown-item" href="#"  data-bs-toggle="modal" data-bs-target="#dayEndModal">
                                        <i class="mdi mdi-lock-open-outline font-size-17 align-middle me-1"></i> Cashier Close
                                    </a>
                                @endif
                            @endif


                    @else
                        <script>
                            window.location.href = "{{ route('login') }}"
                        </script>
                    @endif
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


            @if($privilege)
                @php $isHeadOffice = session('branch_id') == -1; @endphp
                @if($isHeadOffice)
                    {{-- Head Office restricted menu: Dashboard, View Customer, KYC, View Center --}}
                    @if(optional($privilege)->dashboard == 1)
                        <li class="side-nav-item">
                            <a href="/" class="side-nav-link">
                                <i class="ri-dashboard-3-line"></i>
                                <span> Dashboard </span>
                            </a>
                        </li>
                    @endif
                    
                    @if(optional($privilege)->customer == 1 && (optional($privilege)->view_customer == 1 || optional($privilege)->kyc == 1))
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#customer" aria-expanded="false"
                               aria-controls="customer" class="side-nav-link">
                                <i class="ri-group-2-line"></i>
                                <span> Customer </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="customer">
                                <ul class="side-nav-second-level">
                                    @if(optional($privilege)->view_customer == 1)
                                        <li><a href="/showcustomers">View Customer</a></li>
                                    @endif
                                    @if(optional($privilege)->kyc == 1)
                                        <li><a href="/kyc">KYC</a></li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if(optional($privilege)->loan_center == 1 && optional($privilege)->view_center == 1)
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

                    @if(optional($privilege)->account_center == 1)
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#account" aria-expanded="false" aria-controls="center" class="side-nav-link">
                                <i class="bi bi-universal-access"></i>
                                <span> Account Center </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="account">
                                <ul class="side-nav-second-level">
                                    @if(optional($privilege)->bank_cash_account == 1)
                                        <li>
                                            <a href="/bank_account">Bank/Cash Account</a>
                                        </li>
                                    @endif
                                    @if(optional($privilege)->internal_bank_transfer == 1)
                                        <li>
                                            <a href="/InnerBankTransfer">Internal Account Transfer</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif

                    @if(optional($privilege)->account_department == 1)
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#accountmanagement" aria-expanded="false" aria-controls="center" class="side-nav-link">
                                <i class="bi bi-bank"></i>
                                <span> Account Department </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="accountmanagement">
                                <ul class="side-nav-second-level">
                                    @if(optional($privilege)->manual_journal == 1)
                                        <li>
                                            <a href="/ManualJournal">Manual Journal</a>
                                        </li>
                                    @endif
                                    @if(optional($privilege)->chart_of_account == 1)
                                        <li>
                                            <a href="/ChartOfAccount">Chart Of Account</a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </li>
                    @endif

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#payment_voucher" aria-expanded="false" aria-controls="payment_voucher" class="side-nav-link">
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

                    @if(optional($privilege)->expenses == 1)
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#expences" aria-expanded="false" aria-controls="expences"
                               class="side-nav-link">
                                <i class="ri-briefcase-line"></i>
                                <span> Expenses </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="expences">
                                <ul class="side-nav-second-level">
                                    @if(optional($privilege)->add_expenses == 1)
                                        <li>
                                            <a href="/expenses">Add Expenses</a>
                                        </li>
                                    @endif
{{--                                    @if(optional($privilege)->view_expenses == 1)
                                        <li>
                                            <a href="/view_expenses">View Expenses</a>
                                        </li>
                                    @endif--}}
                                </ul>
                            </div>
                        </li>
                    @endif
                @else
                @if(optional($privilege)->dashboard == 1)
                    <li class="side-nav-item">
                        <a href="/" class="side-nav-link">
                            <i class="ri-dashboard-3-line"></i>
                            <span> Dashboard </span>
                        </a>
                    </li>
                @endif
                @if(optional($privilege)->customer == 1)
                        <li class="side-nav-item">
                            <a data-bs-toggle="collapse" href="#customer" aria-expanded="false"
                               aria-controls="sidebarPagesAuth" class="side-nav-link">
                                <i class="ri-group-2-line"></i>
                                <span> Customer </span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="customer">
                                <ul class="side-nav-second-level">
                                    @if(optional($privilege)->add_customer == 1)
                                        <li>
                                            <a href="/customers">Add Customer</a>
                                        </li>
                                    @endif
                                    @if(optional($privilege)->view_customer == 1)
                                        <li>
                                            <a href="/showcustomers">View Customer</a>
                                        </li>
                                    @endif
                                    @if(optional($privilege)->view_blacklist_customer == 1)
                                        <li>
                                            <a href="/showblacklistcustomers">View Blacklist Customer</a>
                                        </li>
                                    @endif
                                    @if(optional($privilege)->customer_saving_acc == 1)
                                        <li>
                                            <a href="/showcustomerssaving">Customer Saving Acc.</a>
                                        </li>
                                            <li>
                                                <a href="/showcustomersrecovery">Customer Recovery Acc.</a>
                                            </li>
                                    @endif
                                    @if(optional($privilege)->kyc == 1)
                                        <li>
                                            <a href="/kyc">KYC</a>
                                        </li>
                                    @endif
                                    @if(optional($privilege)->insurance == 1)
                                        <li>
                                            <a href="/insurance">Insurance</a>
                                        </li>
                                    @endif

                                </ul>
                            </div>

                        </li>
                @endif

                    @if(optional($privilege)->loan_center == 1)
                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#center" aria-expanded="false" aria-controls="center"
                                   class="side-nav-link">
                                    <i class="bi bi-building"></i>
                                    <span> Loan Center </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="center">
                                    <ul class="side-nav-second-level">
                                        @if(optional($privilege)->create_route == 1)
                                            <li>
                                                <a href="/viewroutes">Create Route</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->create_center == 1)
                                            <li>
                                                <a href="/center">Create Center</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->view_center == 1)
                                            <li>
                                                <a href="/viewcenter">View Center</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->create_group == 1)
                                            <li>
                                                <a href="/customergroup">Create Group</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->view_group == 1)
                                            <li>
                                                <a href="/viewgroups">View Group</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->add_customer_to_group == 1)
                                            <li>
                                                <a href="/customergroupassign">Add Customers To Group</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                    @endif

                    @if(optional($privilege)->guarantee == 1)
                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#Guarantee" aria-expanded="false"
                                   aria-controls="sidebarPagesAuth" class="side-nav-link">
                                    <i class="ri-user-2-fill"></i>
                                    <span> Guarantee </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="Guarantee">
                                    <ul class="side-nav-second-level">
                                        @if(optional($privilege)->add_guarantee == 1)
                                            <li>
                                                <a href="/guardian">Add Guarantee</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->view_guarantee == 1)
                                            <li>
                                                <a href="/showguardian">View Guarantee</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                            </li>
                    @endif

                    @if(optional($privilege)->product == 1)
                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#sidebarPages" aria-expanded="false"
                                   aria-controls="sidebarPages" class="side-nav-link">
                                    <i class="ri-pages-line"></i>
                                    <span> Product / Loan  </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="sidebarPages">
                                    <ul class="side-nav-second-level">
                                        @if(optional($privilege)->add_product == 1)
                                            <li>
                                                <a href="/product">Add Product</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->view_product == 1)
                                            <li>
                                                <a href="/viewproduct">View Product</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->create_loan == 1)
                                            <li>
                                                <a href="/loan">Create Loans</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->change_collector == 1)
                                            <li>
                                                <a href="/changeCollector">Change Collector In Loan</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->pending_loan == 1)
                                            <li>
                                                <a href="/pendingloan">Pending Loans</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->loan_disbursement == 1)
                                            <li>
                                                <a href="/loan_disbursement">Loans Disbursement</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->current_loans == 1)
                                            <li>
                                                <a href="/payment_step_1">Current Loans</a>
                                            </li>

                                            <li>
                                                <a href="/penalty-deduction">Panelty Deduction</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->current_loan_delete == 1)
                                                <li>
                                                    <a href="loan_delete_requests">Delete Loans Approval</a>
                                                </li>
                                            @endif

                                        @if(optional($privilege)->settled_loans == 1)
                                            <li>
                                                <a href="/showsettleloan">Settled Loans</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                    @endif


                    @if(optional($privilege)->payment_details == 1)
                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#payment" aria-expanded="false" aria-controls="center"
                                   class="side-nav-link">
                                    <i class="bi bi-currency-dollar"></i>
                                    <span> Payment Details </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="payment">
                                    <ul class="side-nav-second-level">
                                        @if(optional($privilege)->add_repayment == 1)
                                            <li>
                                                <a href="/payment">Add Repayment</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->bulk_repayment == 1)
                                            <li>
                                                <a href="/bulk_repayment">Bulk Repayment</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->loan_settlement == 1)
                                            <li>
                                                <a href="/loan_settlement">Loan Settlement</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->loan_reschedule == 1)
                                            <li>
                                                <a href="/loan_reschedule">Loan Reschedule</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->view_payment == 1)
                                            <li>
                                                <a href="/viewpayment">View Repayment</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->collector_wise_collection == 1)
                                            <li>
                                                <a href="/collection">Collector Wise Collection</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                    @endif

                    @if(optional($privilege)->account_center == 1)

                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#account" aria-expanded="false" aria-controls="center" class="side-nav-link">
                                    <i class="bi bi-universal-access"></i>
                                    <span> Account Center </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="account">
                                    <ul class="side-nav-second-level">
                                        @if(optional($privilege)->bank_cash_account == 1)
                                            <li>
                                                <a href="/bank_account">Bank/Cash Account</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->internal_bank_transfer == 1)
                                            <li>
                                                <a href="/InnerBankTransfer">Internal Account Transfer</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->collector_account == 1)
                                            <li>
                                                <a href="/collector_index">Collector Account</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->cheque_details == 1)
                                            <li>
                                                <a href="/chq">Cheque Details</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                    @endif


                    @if(optional($privilege)->account_department == 1)
                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#accountmanagement" aria-expanded="false" aria-controls="center" class="side-nav-link">
                                    <i class="bi bi-bank"></i>
                                    <span> Account Department </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="accountmanagement">
                                    <ul class="side-nav-second-level">
                                        @if(optional($privilege)->add_asset == 1)
                                            <li>
                                                <a href="/AddAssetManagement">Add Asset Management</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->asset_management == 1)
                                            <li>
                                                <a href="/AssetManagement">Asset Management</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->bank_reconciliation == 1)
                                            <li>
                                                <a href="/BankReconciliation">Bank Reconciliation</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->manual_journal == 1)
                                            <li>
                                                <a href="/ManualJournal">Manual Journal</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->chart_of_account == 1)
                                            <li>
                                                <a href="/ChartOfAccount">Chart Of Account</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                    @endif

                    <li class="side-nav-item">
                        <a data-bs-toggle="collapse" href="#payment_voucher" aria-expanded="false" aria-controls="payment_voucher" class="side-nav-link">
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

                    @if(optional($privilege)->loan_calculator == 1)
                        <li class="side-nav-item">
                            <a href="/calculator" class="side-nav-link">
                                <i class="ri-dashboard-3-line"></i>
                                <span> Loan Calculator </span>
                            </a>
                        </li>
                    @endif

                    @if(optional($privilege)->calendar == 1)
                        <li class="side-nav-item">
                            <a href="/calender" class="side-nav-link">
                                <i class="ri-dashboard-3-line"></i>
                                <span> Calender </span>
                            </a>
                        </li>
                    @endif

                    @if(optional($privilege)->expenses == 1)

                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#expences" aria-expanded="false" aria-controls="expences"
                                   class="side-nav-link">
                                    <i class="ri-briefcase-line"></i>
                                    <span> Expenses </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="expences">
                                    <ul class="side-nav-second-level">
                                        @if(optional($privilege)->add_expenses == 1)
                                            <li>
                                                <a href="/expenses">Add Expenses</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->view_expenses == 1)
                                            <li>
                                                <a href="/view_expenses">View Expenses</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                    @endif

                    @if(optional($privilege)->user == 1)

                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#user" aria-expanded="false" aria-controls="user"
                                   class="side-nav-link collapsed">
                                    <i class="ri-user-3-fill"></i>
                                    <span> User </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="user" style="">
                                    <ul class="side-nav-second-level">
                                        @if(optional($privilege)->create_user == 1)
                                            <li>
                                                <a href="/user" class="active">Manage User</a>
                                            </li>
                                        @endif
                                        @if(optional($privilege)->user_privileges == 1)
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


                    @if(optional($privilege)->reports == 1)
                            <li class="side-nav-item">
                                <a data-bs-toggle="collapse" href="#reports_section" aria-expanded="false" class="side-nav-link">
                                    <i class="ri-file-paper-2-fill"></i>
                                    <span> Reports </span>
                                    <span class="menu-arrow"></span>
                                </a>
                                <div class="collapse" id="reports_section">
                                    <ul class="side-nav-second-level">
                                        <li class="side-nav-item">
                                            <a data-bs-toggle="collapse" href="#main_report" aria-expanded="false" class="side-nav-link">
                                                <span> Main Reports </span>
                                                <span class="menu-arrow"></span>
                                            </a>
                                            <div class="collapse" id="main_report">
                                                <ul class="side-nav-third-level">
                                                    @if(optional($privilege)->main_reports_dashboard == 1)
                                                        <li>
                                                            <a href="/portfolio_performance">Portfolio & Performance - Dashboard</a>
                                                        </li>
                                                    @endif
                                                    @if(optional($privilege)->loan_disbursement_performance == 1)
                                                            <li>
                                                                <a href="/loan-report">Loan Disbursement Performance - Dashboard</a>
                                                            </li>
                                                    @endif
                                                    @if(optional($privilege)->payment_detail_report == 1)
                                                            <li>
                                                                <a href="/PaymentFullDetailsReport">Payment Details Report</a>
                                                            </li>
                                                            <li>
                                                                <a href="/prediction_report">Payment Prediction</a>
                                                            </li>
                                                    @endif
                                                    @if(optional($privilege)->full_loan_detail == 1)
                                                            <li><a href="/AllLoanDetailReport">Full Loan Detail Report</a></li>
                                                    @endif
                                                    @if(optional($privilege)->loan_summary == 1)
                                                            <li><a href="/loansummaryreport">Loan Summary Report</a></li>
                                                    @endif
                                                    @if(optional($privilege)->par_monthly == 1)
                                                            <li><a href="/par">PAR (Monthly)</a></li>
                                                    @endif
                                                    @if(optional($privilege)->par_weekly == 1)
                                                            <li><a href="/par_weekly">PAR (Weekly)</a></li>
                                                    @endif
                                                    @if(optional($privilege)->loan_status == 1)
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
                                                    @if(optional($privilege)->cashflow_accumulated == 1)
                                                        <li>
                                                            <a href="/CashFlow">CashFlow Accumulated</a>
                                                        </li>
                                                    @endif
                                                    @if(optional($privilege)->cashflow_monthly == 1)
                                                            <li>
                                                                <a href="/CashFlowMonthly">CashFlow Monthly</a>
                                                            </li>
                                                    @endif
                                                    @if(optional($privilege)->profit_loss == 1)
                                                            <li>
                                                                <a href="/ProfitLoss">Profit & Loss (P&L)</a>
                                                            </li>
                                                    @endif
                                                    @if(optional($privilege)->balance_sheet == 1)
                                                            <li>
                                                                <a href="/BalanceSheet">Balance Sheet</a>
                                                            </li>
                                                    @endif
                                                    @if(optional($privilege)->trial_balance == 1)
                                                            <li>
                                                                <a href="/trialBalanceAccounting">Trial Balance</a>
                                                            </li>
                                                    @endif
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
                                                    @if(optional($privilege)->daily_collection_sheet == 1)
                                                        <li>
                                                            <a href="/daily">Daily Collection Sheet</a>
                                                        </li>
                                                    @endif
                                                    @if(optional($privilege)->center_collection_detail == 1)
                                                            <li>
                                                                <a href="/center_collection">Center Wise collection Detail</a>
                                                            </li>
                                                    @endif
                                                    @if(optional($privilege)->center_collection_summary == 1)
                                                            <li>
                                                                <a href="/center_collection_summary">Center Wise collection Summary</a>
                                                            </li>
                                                    @endif
                                                    @if(optional($privilege)->route_collections == 1)
                                                            <li>
                                                                <a href="/root_wise_collection">Route Wise Daily Collection</a>
                                                            </li>
                                                    @endif
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
                                                    @if(optional($privilege)->repayment_sheet_01 == 1)
                                                        <li>
                                                            <a href="/daily_repayment_sheet">Repayment Sheet 01</a>
                                                        </li>
                                                    @endif
                                                    @if(optional($privilege)->repayment_sheet_02 == 1)
                                                            <li>
                                                                <a href="/RightWayDailyRepayment">Repayment Sheet 02</a>
                                                            </li>
                                                    @endif
                                                    @if(optional($privilege)->repayment_sheet_03 == 1)
                                                            <li>
                                                                <a href="/daily_repayment_sheet_hm">Repayment Sheet 03</a>
                                                            </li>
                                                    @endif
                                                    @if(optional($privilege)->repayment_sheet_04 == 1)
                                                            <li>
                                                                <a href="/daily_repayment_sheet_lasantha">Repayment Sheet 04</a>
                                                            </li>
                                                    @endif
                                                        @if(optional($privilege)->repayment_sheet_05 == 1)
                                                            <li>
                                                                <a href="/daily_repayment_sheet_finwin">Repayment Sheet 05</a>
                                                            </li>
                                                        @endif

                                                        @if(optional($privilege)->repayment_sheet_06 == 1)
                                                            <li>
                                                                <a href="/GreenLankaTrustRepayment">Repayment Sheet 06</a>
                                                            </li>
                                                        @endif

                                                        @if(optional($privilege)->repayment_sheet_07 == 1)
                                                            <li>
                                                                <a href="/DandDRepayment">Repayment Sheet 07</a>
                                                            </li>
                                                        @endif

                                                        @if(optional($privilege)->repayment_sheet_08== 1)
                                                            <li>
                                                                <a href="/dailyreport">Repayment Sheet 08</a>
                                                            </li>
                                                        @endif

                                                        {{-- TEMPORARY: Show Repayment Sheet 09 without permission check for testing --}}
                                                        {{-- @if(optional($privilege)->repayment_sheet_09== 1) --}}
                                                            <li>
                                                                <a href="/repaymntseet9">Repayment Sheet 09</a>
                                                            </li>
                                                        {{-- @endif --}}

                                                        {{-- Repayment Sheet 10 --}}
                                                        {{-- @if(optional($privilege)->repayment_sheet_10== 1) --}}
                                                            <li>
                                                                <a href="/RepaymentSheet10">Repayment Sheet 10</a>
                                                            </li>
                                                        {{-- @endif --}}
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
                                                    @if(optional($privilege)->other_charges_report == 1)
                                                        <li><a href="/LoanChargers">Loan Chargers Report</a></li>
                                                    @endif
                                                    @if(optional($privilege)->center_dashboard == 1)
                                                            <li><a href="/dandlreport">Center Collection Dashboard</a></li>
                                                    @endif
                                                    @if(optional($privilege)->repayment_summary == 1)
                                                            <li><a href="/monthlyprofit">Loan Repayment Summary Report</a></li>
                                                    @endif
                                                    @if(optional($privilege)->savings_report == 1)
                                                            <li><a href="/savings_report">Savings Report</a></li>
                                                    @endif
                                                    @if(optional($privilege)->arrears_report == 1)
                                                            <li><a href="/latePayment">Loan In Areas</a></li>
                                                    @endif
                                                    @if(optional($privilege)->arrears_overview == 1)
                                                            <li><a href="/late_payment_report">Arrease Details</a></li>
                                                    @endif
                                                    @if(optional($privilege)->datewise_cashflow == 1)
                                                            <li><a href="/ViewDateWiseCashFlow">Date Wise Cash Flow Details</a></li>
                                                    @endif
                                                    @if(optional($privilege)->loan_detail_report == 1)
                                                            <li><a href="/loanreport">Loan Details</a></li>
                                                    @endif
                                                    @if(optional($privilege)->collector_report == 1)
                                                            <li><a href="/repaymentreport">Collector Wise Repayment Collection</a></li>
                                                    @endif
                                                    @if(optional($privilege)->sms_history == 1)
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
                                                        @if(optional($privilege)->customer_detail_report == 1)
                                                            <li><a href="/customerreport_details">All Customer Details</a></li>
                                                        @endif
                                                        @if(optional($privilege)->officer_customer_detail == 1)
                                                            <li><a href="/customerreport_details_recover_officer">Recover Officer Wise Customers</a></li>
                                                        @endif
                                                        @if(optional($privilege)->guardian_detail_report == 1)
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
                        </ul>
                    </div>
                </li>

            @endif
        </ul>

        <div class="clearfix"></div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    // Handle branch switching for both super and non-super users
    $(document).on('click', '.branch-option', function (e) {
        e.preventDefault();
        const branchId = $(this).data('branch-id');
        const branchName = $(this).data('branch-name');

        $.ajax({
            url: '/update-branch',
            method: 'POST',
            data: {
                branch_id: branchId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                // Update UI and reload to apply scoping
                $('.branch-text').text(branchName);
                window.location.reload();
            },
            error: function (xhr) {
                const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unauthorized or error updating branch';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
</script>

<script>
    // Function to update the Grand Total
    function updateGrandTotal() {
        let grandTotal = 0;
        $("#modalTableBody tr").each(function () {
            let total = parseFloat($(this).find(".total-amount").text()) || 0;
            grandTotal += total;
        });
        $("#grandTotal").text(grandTotal.toFixed(2));
    }

    // Add to Table Button Click
    $("#addToTable").click(function () {
        let amount = parseFloat($("#amount").val());
        let quantity = parseInt($("#quantity").val());

        if (!amount || !quantity || amount <= 0 || quantity <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Input',
                text: 'Please select a valid amount and quantity.',
            });
            return;
        }

        let totalAmount = amount * quantity;
        let existingRow = $("#modalTableBody").find(`tr[data-amount='${amount}']`);

        if (existingRow.length > 0) {
            // Update existing row
            let existingQuantity = parseInt(existingRow.find(".quantity").text());
            let newQuantity = existingQuantity + quantity;
            let newTotal = amount * newQuantity;

            existingRow.find(".quantity").text(newQuantity);
            existingRow.find(".total-amount").text(newTotal.toFixed(2));

            Swal.fire({
                icon: 'info',
                title: 'Entry Updated',
                text: `Updated quantity for amount ${amount}`,
            });
        } else {
            // Add new row
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
                title: 'Entry Added',
                text: `Amount: ${amount}, Quantity: ${quantity}`,
            });
        }

        updateGrandTotal();
        $("#amount").val('');
        $("#quantity").val('');
    });

    // Handle Remove Entry button
    $(document).on('click', '.remove-entry', function () {
        $(this).closest('tr').remove();
        updateGrandTotal();
    });
    $(document).ready(function () {
        $("#cashierStartModal").on('show.bs.modal', function () {
            loadSavedPlotEntries();
        });

        $("#dayEndModal").on('show.bs.modal', function () {
            $.ajax({
                url: '/cashier/day-end-data',
                method: 'GET',
                success: function (response) {
                    $("#plotAmount").val(response.startingCash.toFixed(2));


                    // Inside AJAX success of #dayEndModal
                    let cashInBody = $("#cashInTableBody").empty();
                    response.cashIn.forEach((item, i) => {
                        cashInBody.append(`
        <tr>
            <td>${i + 1}</td>
            <td>${item.Date_Time}</td>
            <td>${item.Type}</td>
            <td>${item.Description}</td>
            <td>${parseFloat(item.Debit).toFixed(2)}</td>
        </tr>
    `);
                    });

                    let cashOutBody = $("#cashOutTableBody").empty();
                    response.cashOut.forEach((item, i) => {
                        cashOutBody.append(`
        <tr>
            <td>${i + 1}</td>
            <td>${item.Date_Time}</td>
            <td>${item.Type}</td>
            <td>${item.Description}</td>
            <td>${parseFloat(item.Credit).toFixed(2)}</td>
        </tr>
    `);
                    });






                    $("#totalIncome").text(response.totalIncome.toFixed(2));
                    $("#totalExpenses").text(response.totalExpenses.toFixed(2));
                    $("#balanceAmount").val(response.balanceAmount.toFixed(2));


                    updateCashDrawerTotals(); // Reset drawer totals if needed
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error loading data',
                        text: 'Could not fetch day end data.'
                    });
                }
            });
        });
        $("#dayEndModal").on('show.bs.modal', function () {
            $.ajax({
                url: '/cashier/get-saved-day-end',
                method: 'GET',
                success: function (res) {

                    // Set summary fields
                    $("#plotAmount").val(parseFloat(res.startingCash).toFixed(2));
                    $("#totalIncome").text(parseFloat(res.totalIncome).toFixed(2));
                    $("#totalExpenses").text(parseFloat(res.totalExpenses).toFixed(2));
                    $("#balanceAmount").val(parseFloat(res.balanceAmount).toFixed(2));

                    // Inside success callback for /cashier/get-saved-day-end
                    let cashInBody = $("#cashInTableBody").empty();
                    res.cashIn.forEach((item, i) => {
                        cashInBody.append(`
        <tr>
            <td>${i + 1}</td>
            <td>${item.Date_Time}</td>
            <td>${item.Type}</td>
            <td>${item.Description}</td>
            <td>${parseFloat(item.Debit).toFixed(2)}</td>
        </tr>
    `);
                    });

                    let cashOutBody = $("#cashOutTableBody").empty();
                    res.cashOut.forEach((item, i) => {
                        cashOutBody.append(`
        <tr>
            <td>${i + 1}</td>
            <td>${item.Date_Time}</td>
            <td>${item.Type}</td>
            <td>${item.Description}</td>
            <td>${parseFloat(item.Credit).toFixed(2)}</td>
        </tr>
    `);
                    });


                    // Cash drawer entries
                    $("#cashDrawerTableBody").empty();
                    res.cashDrawerEntries.forEach((entry, index) => {
                        $("#cashDrawerTableBody").append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td class="cash-amt">${parseFloat(entry.denomination)}</td>
                        <td class="cash-qty">${parseInt(entry.quantity)}</td>
                        <td class="cash-total">${parseFloat(entry.total_amount).toFixed(2)}</td>
                        <td></td> <!-- Empty cell (no Remove button) -->
                    </tr>
                `);
                    });

                    // Totals
                    $("#totalCashDrawer").text(parseFloat(res.savedData.cash_drawer_total).toFixed(2));
                    $("#balanceDifference").text(parseFloat(res.savedData.balance_difference).toFixed(2));

                    // Disable inputs if already saved
                    if (res.dayEndExists) {
                        $("#cashDrawerForm :input").prop("disabled", true);
                        $("#saveDayEnd").prop("disabled", true);
                        $("#addCashToTable").prop("disabled", true);
                    } else {
                        $("#cashDrawerForm :input").prop("disabled", false);
                        $("#saveDayEnd").prop("disabled", false);
                        $("#addCashToTable").prop("disabled", false);
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load Day End data.'
                    });
                }
            });
        });

    });



    $("#saveEntries").click(function () {
        let entries = [];

        $("#modalTableBody tr").each(function () {
            let amount = parseFloat($(this).find(".amount").text());
            let quantity = parseInt($(this).find(".quantity").text());
            let total = parseFloat($(this).find(".total-amount").text());

            entries.push({
                amount: amount,         // backend expects this as the denomination
                quantity: quantity,     // qty
                totalAmount: total      // calculated amount = denomination * quantity
            });
        });

        if (entries.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Data',
                text: 'Please add at least one entry before saving.',
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to save the entries.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, save it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/save-cashier-data',
                    method: 'POST',
                    data: {
                        entries: entries,
                        grandTotal: parseFloat($("#grandTotal").text()),
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message
                        });
                        loadSavedPlotEntries();
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'An error occurred while saving.'
                        });
                    }
                });
            }
        });
    });
    function loadSavedPlotEntries() {
        $.ajax({
            url: '/get-today-cashier-data',
            method: 'GET',
            success: function (response) {
                let tableBody = $("#modalTableBody");
                tableBody.empty();

                let grandTotal = 0;

                response.entries.forEach((entry, index) => {
                    let amount = parseFloat(entry.money);
                    let qty = parseInt(entry.qty);
                    let total = parseFloat(entry.amount);

                    grandTotal += total;

                    tableBody.append(`
                    <tr data-amount="${amount}">
                        <td>${index + 1}</td>
                        <td class="amount">${amount}</td>
                        <td class="quantity">${qty}</td>
                        <td class="total-amount">${total.toFixed(2)}</td>
                        <td><button class="btn btn-danger btn-sm remove-entry">Remove</button></td>
                    </tr>
                `);
                });

                $("#grandTotal").text(grandTotal.toFixed(2));
                // Disable save/add buttons if finalized
                if (response.isFinalized) {
                    $("#saveEntries").prop("disabled", true);
                    $("#addToTable").prop("disabled", true);
                    $("#amount, #quantity").prop("disabled", true);
                    $("#modalTableBody .remove-entry").prop("disabled", true); // ✅ Disable buttons instead of removing
                } else {
                    $("#saveEntries").prop("disabled", false);
                    $("#addToTable").prop("disabled", false);
                    $("#amount, #quantity").prop("disabled", false);
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not load saved entries.'
                });
            }
        });
    }


    $("#printDayStartReport").click(function () {
        let tableClone = $("#modalTableBody").closest("table").clone();

        // Remove the last column (Action) in both header and body
        tableClone.find("thead tr th:last-child").remove();
        tableClone.find("tbody tr").each(function () {
            $(this).find("td:last-child").remove();
        });

        // Get current date and time
        const now = new Date();
        const dateStr = now.toLocaleDateString();
        const timeStr = now.toLocaleTimeString();

        // Get user name from Laravel session
        const userName = `{{ session('Full_Name') }}`;
        const branchName = `{{ session('branch_name') }}`;

        let printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Day Start Report</title>');
        printWindow.document.write('<style>table { width: 100%; border-collapse: collapse; } th, td { padding: 8px; border: 1px solid #ccc; }</style>');
        printWindow.document.write('</head><body>');
        printWindow.document.write('<h3>Day Start Report</h3>');
        printWindow.document.write(`<p><strong>User:</strong> ${userName}</p>`);
        printWindow.document.write(`<p><strong>Branch:</strong> ${branchName}</p>`);
        printWindow.document.write(`<p><strong>Date:</strong> ${dateStr} <strong>Time:</strong> ${timeStr}</p>`);
        printWindow.document.write(tableClone.prop('outerHTML'));
        printWindow.document.write('<h4>Total: ' + $("#grandTotal").text() + '</h4>');
        printWindow.document.write('</body></html>');

        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    });



    $("#addCashToTable").click(function () {
        let amount = parseFloat($("#cashAmount").val());
        let quantity = parseInt($("#cashQuantity").val());

        if (!amount || !quantity || amount <= 0 || quantity <= 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Input',
                text: 'Please enter valid denomination and quantity.',
            });
            return;
        }

        let total = amount * quantity;
        let existingRow = $("#cashDrawerTableBody").find(`tr[data-amount='${amount}']`);

        if (existingRow.length > 0) {
            let existingQty = parseInt(existingRow.find(".cash-qty").text());
            let newQty = existingQty + quantity;
            let newTotal = amount * newQty;

            existingRow.find(".cash-qty").text(newQty);
            existingRow.find(".cash-total").text(newTotal.toFixed(2));
        } else {
            let rowCount = $("#cashDrawerTableBody tr").length + 1;

            $("#cashDrawerTableBody").append(`
            <tr data-amount="${amount}">
                <td>${rowCount}</td>
                <td class="cash-amt">${amount}</td>
                <td class="cash-qty">${quantity}</td>
                <td class="cash-total">${total.toFixed(2)}</td>
                <td><button class="btn btn-danger btn-sm remove-cash-row">Remove</button></td>
            </tr>
        `);
        }

        updateCashDrawerTotals();

        $("#cashAmount").val('');
        $("#cashQuantity").val('');
    });

    function updateCashDrawerTotals() {
        let total = 0;
        $("#cashDrawerTableBody tr").each(function () {
            let rowTotal = parseFloat($(this).find(".cash-total").text()) || 0;
            total += rowTotal;
        });
        $("#totalCashDrawer").text(total.toFixed(2));

        let balanceAmount = parseFloat($("#balanceAmount").val()) || 0;
        let difference = balanceAmount - total;
        $("#balanceDifference").text(difference.toFixed(2));
    }


    $(document).on('click', '.remove-cash-row', function () {
        $(this).closest('tr').remove();
        updateCashDrawerTotals();
    });

    $("#saveDayEnd").click(function () {
        let startingCash = parseFloat($("#plotAmount").val());
        let totalIncome = parseFloat($("#totalIncome").text());
        let totalExpense = parseFloat($("#totalExpenses").text());
        let balanceAmount = parseFloat($("#balanceAmount").val());
        let cashDrawerTotal = parseFloat($("#totalCashDrawer").text());
        let balanceDifference = parseFloat($("#balanceDifference").text());


        if (balanceDifference != 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Unbalanced Cash Drawer',
                html: `Balance Difference must be <b>0.00</b> to save the Day End.<br><br>
                   Current Difference: <strong style="color:red;">${balanceDifference.toFixed(2)}</strong>`,
            });
            return; // ❌ Stop further execution
        }


        let drawerEntries = [];

        $("#cashDrawerTableBody tr").each(function () {
            drawerEntries.push({
                denomination: parseFloat($(this).find(".cash-amt").text()),
                quantity: parseInt($(this).find(".cash-qty").text()),
                total_amount: parseFloat($(this).find(".cash-total").text())
            });
        });


        $.ajax({
            url: '/cashier/bank-list',
            method: 'GET',
            success: function (banks) {
                let selectOptions = banks.map(bank =>
                    `<option value="${bank.Idbank}">${bank.Bank_Name} - ${bank.Account_Name}</option>`
                ).join('');

                Swal.fire({
                    title: 'Select Bank to Deposit',
                    html: `
                <label>Select a bank account to deposit:</label>
                <select id="swal-bank-select" class="form-control mt-2">
                    <option value="">-- Select Bank --</option>
                    ${selectOptions}
                </select>
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Save Day End',
                    preConfirm: () => {
                        const selectedBankId = $('#swal-bank-select').val();
                        if (!selectedBankId) {
                            Swal.showValidationMessage('Please select a bank account.');
                        }
                        return selectedBankId;
                    }
                }).then(result => {
                    if (result.isConfirmed) {
                        let selectedBankId = result.value;

                        $.ajax({
                            url: '/cashier/save-day-end',
                            method: 'POST',
                            data: {
                                starting_cash: startingCash,
                                total_income: totalIncome,
                                total_expense: totalExpense,
                                balance_amount: balanceAmount,
                                cash_drawer_total: cashDrawerTotal,
                                balance_difference: balanceDifference,
                                selected_bank_id: selectedBankId,
                                cash_drawer_entries: drawerEntries,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function (res) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Saved',
                                    text: res.message
                                });
                                $("#saveDayEnd").prop('disabled', true);
                            },
                            error: function (xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Failed to save day end.'
                                });
                            }
                        });
                    }
                });
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not load bank list.'
                });
            }
        });

    });


    $("#printDayEndReport").click(function () {
        const printWindow = window.open('', '', 'height=700,width=900');
        const incomeRows = $("#cashInTableBody").html();
        const expenseRows = $("#cashOutTableBody").html();
        const drawerRows = $("#cashDrawerTableBody").html();

        const html = `
        <html>
        <head>
            <title>Day End Summary</title>
            <style>
                body { font-family: Arial; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { border: 1px solid #000; padding: 6px; text-align: left; }
                h2, h4 { margin: 10px 0; }
            </style>
        </head>
        <body>
            <h2>📋 Day End Summary - ${new Date().toLocaleDateString()}</h2>
            <h4>Branch: {{ session('branch_name') }}</h4>

            <h4>Starting Cash: ${$("#plotAmount").val()}</h4>
            <h4>Total Income: ${$("#totalIncome").text()}</h4>
            <h4>Total Expenses: ${$("#totalExpenses").text()}</h4>
            <h4>Balance Amount: ${$("#balanceAmount").val()}</h4>

            <h3>Other Incomes</h3>
            <table>
                <thead><tr><th>#</th><th>Date</th><th>Type</th><th>Description</th><th>Amount</th></tr></thead>
                <tbody>${incomeRows}</tbody>
            </table>

            <h3>Other Expenses</h3>
            <table>
                <thead><tr><th>#</th><th>Date</th><th>Type</th><th>Description</th><th>Amount</th></tr></thead>
                <tbody>${expenseRows}</tbody>
            </table>

            <h3>Cash Drawer</h3>
            <table>
                <thead><tr><th>#</th><th>Denomination</th><th>Qty</th><th>Total</th><th></th></tr></thead>
                <tbody>${drawerRows.replace(/<td>.*Remove.*<\/td>/g, '')}</tbody>
            </table>

            <h4>Total Cash Drawer Balance: ${$("#totalCashDrawer").text()}</h4>
            <h4 style="color: red;">Balance Difference: ${$("#balanceDifference").text()}</h4>
        </body>
        </html>
    `;

        printWindow.document.write(html);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    });



</script>


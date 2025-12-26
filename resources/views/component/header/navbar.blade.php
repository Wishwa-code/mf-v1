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
            document.addEventListener("DOMContentLoaded", function() {
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

                    var timeElement = document.getElementById('time');
                    if (timeElement) {
                        timeElement.innerText = formattedTime;
                    }
                    var dateElement = document.getElementById('date');
                    if (dateElement) {
                        dateElement.innerText = formattedDate;
                    }
                }

                updateTime();
                setInterval(updateTime, 1000);
            });
        </script>
        <div class="date-time d-none d-lg-block">
            <!-- <h2 id="time" style="margin-right: 15px;"></h2> -->
            <h2 id="date">{{ date('Y/m/d') }}</h2>
            <h2 id="day">{{ date('l') }}</h2>
        </div>
        <?php
        // All active branches (used for super users)

        use Illuminate\Support\Facades\DB;

        $query = 'SELECT * FROM branch where status=1';
        $branch = DB::select($query);

        // Allowed branches for the current user (used for non-super users)
        $allowedBranches = collect([]);
        if (session('branch_access') !== 1) {
            $userId = session('userid');
            if ($userId) {
                $allowedBranches = \Illuminate\Support\Facades\DB::table('user_has_branches')->join('branch', 'user_has_branches.branch_id', '=', 'branch.branch_id')->where('user_has_branches.user_id', $userId)->where('branch.status', 1)->select('branch.branch_id', 'branch.Name')->get();
            }
        }
        ?>
        <style>
            .navbar-custom,
            .topbar {
                overflow: visible !important;
            }

            .modern-branch-switcher .modern-dropdown-toggle {
                background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
                border: none;
                color: white;
                border-radius: 12px;
                padding: 10px 20px;
                font-weight: 600;
                box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2);
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .modern-branch-switcher .modern-dropdown-toggle:hover,
            .modern-branch-switcher .modern-dropdown-toggle:focus {
                background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
                transform: translateY(-1px);
                box-shadow: 0 8px 12px -1px rgba(99, 102, 241, 0.3);
            }

            .modern-branch-switcher .modern-dropdown-menu {
                border: 1px solid rgba(226, 232, 240, 0.8);
                border-radius: 16px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                padding: 8px;
                margin-top: 10px;
                backdrop-filter: blur(10px);
                background: rgba(255, 255, 255, 0.95);
                min-width: 240px;
                /* Scrollbar styling */
                scroll-behavior: smooth;
            }

            .modern-branch-switcher .modern-dropdown-menu::-webkit-scrollbar {
                width: 6px;
            }

            .modern-branch-switcher .modern-dropdown-menu::-webkit-scrollbar-track {
                background: rgba(0, 0, 0, 0.05);
                border-radius: 4px;
            }

            .modern-branch-switcher .modern-dropdown-menu::-webkit-scrollbar-thumb {
                background: rgba(99, 102, 241, 0.5);
                border-radius: 4px;
            }

            .modern-branch-switcher .modern-dropdown-item {
                border-radius: 10px;
                padding: 10px 16px;
                font-weight: 500;
                color: #475569;
                transition: all 0.2s;
                position: relative;
            }

            .modern-branch-switcher .modern-dropdown-item:hover {
                background: linear-gradient(to right, #eef2ff, #f5f3ff);
                color: #4f46e5;
                transform: translateX(4px);
            }

            /* Modern Account Center Styles */
            .modern-account-wrapper {
                position: relative;
            }

            .modern-account-btn {
                background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
                border: none;
                border-radius: 12px;
                width: 42px;
                height: 42px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.3);
                position: relative;
                z-index: 1002;
            }

            .modern-account-btn i {
                color: white;
                font-size: 22px;
                transition: transform 0.3s ease;
            }

            .modern-account-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4);
            }

            .modern-account-btn.active {
                border-radius: 12px 12px 0 0;
                box-shadow: none;
            }

            .modern-account-btn.active i {
                transform: rotate(180deg);
            }

            /* Dropdown Content */
            .account-center-content {
                position: absolute;
                top: 100%;
                right: 0;
                width: 320px;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(226, 232, 240, 0.8);
                border-radius: 16px 0 16px 16px;
                /* Top right corner sharp to match button */
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                overflow: hidden;

                /* Animation State */
                max-height: 0;
                opacity: 0;
                transform-origin: top right;
                transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
                z-index: 1001;
                margin-top: 0;
                /* Connected to button */
            }

            .account-center-content.open {
                max-height: 600px;
                /* Arbitrary large enough height */
                opacity: 1;
                padding: 16px;
            }

            /* Loading State */
            .account-loader {
                display: flex;
                justify-content: center;
                padding: 20px;
            }

            .spinner {
                width: 24px;
                height: 24px;
                border: 3px solid #e2e8f0;
                border-top-color: #6366f1;
                border-radius: 50%;
                animation: spin 0.8s linear infinite;
            }

            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }

            /* Modern List Items */
            .account-list-item {
                display: flex;
                align-items: center;
                padding: 12px 16px;
                border-radius: 10px;
                color: #475569;
                text-decoration: none;
                transition: all 0.2s;
                margin-bottom: 4px;
            }

            .account-list-item:hover {
                background: #f1f5f9;
                color: #4f46e5;
                transform: translateX(4px);
            }

            .account-list-item i {
                font-size: 18px;
                margin-right: 12px;
                color: #94a3b8;
                transition: color 0.2s;
            }

            .account-list-item:hover i {
                color: #6366f1;
            }

            .account-section-title {
                font-size: 0.75rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #94a3b8;
                margin: 16px 0 8px 12px;
            }

            /* Animated Account Button (User Provided Style) */
            .account-btn-animated {
                width: 35px;
                height: 35px;
                border-radius: 30%;
                /* background: #fc5c7c46; */
                /* fallback for old browsers */
                /* background: -webkit-linear-gradient(to right, #6A82FB, #FC5C7D); */
                /* Chrome 10-25, Safari 5.1-6 */
                background: linear-gradient(to right, #6A82FB, #FC5C7D);
                /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */


                border: none;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0px 0px 0px 4px rgba(223, 230, 244, 0.25);
                /* Blue Shadow */
                cursor: pointer;
                transition-duration: 0.3s;
                overflow: hidden;
                position: relative;
                text-decoration: none !important;
                color: white !important;
            }

            .account-btn-animated .svgIcon {
                font-size: 20px;
                transition-duration: 0.3s;
                display: flex;
                align-items: center;
            }

            .account-btn-animated:hover {
                width: 140px;
                border-radius: 36px;
                transition-duration: 0.3s;
                /* background: #948E99; */
                /* fallback for old browsers */
                /* background: -webkit-linear-gradient(to right, #2E1437, #948E99); */
                /* Chrome 10-25, Safari 5.1-6 */
                background: linear-gradient(to right, #6A82FB, #FC5C7D);

                /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */

                /* Blue match */
                align-items: center;
            }

            .account-btn-animated:hover .svgIcon {
                transition-duration: 0.3s;
                transform: translateY(-200%);
            }

            .account-btn-animated::before {
                position: absolute;
                bottom: -20px;
                content: "Account Center";
                color: white;
                font-size: 0px;
                white-space: nowrap;
            }

            .account-btn-animated:hover::before {
                font-size: 13px;
                opacity: 1;
                bottom: unset;
                transition-duration: 0.3s;
            }

            /* Hiding on mobile to prevent layout issues if needed, or keeping it small */
            @media (max-width: 768px) {
                .account-btn-animated:hover {
                    width: 50px;
                    /* Stay circle on mobile */
                }

                .account-btn-animated:hover::before {
                    display: none;
                }

                .account-btn-animated:hover .svgIcon {
                    transform: none;
                }
            }
        </style>
        <div class="date-time">
            @if (session('branch_access') === 1)
            <div class="modern-branch-switcher">
                <div class="dropdown">
                    <button type="button" class="btn modern-dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="ri-building-2-line me-2"></i>
                        <span class="branch-text text-truncate d-inline-block" style="max-width: 200px; vertical-align: middle;">
                            @foreach ($branch as $item)
                            @if (session('branch_id') == $item->branch_id)
                            {{ $item->Name }} Branch
                            @endif
                            @endforeach
                        </span>
                        <i class="ri-arrow-down-s-line ms-2 dropdown-arrow"></i>
                    </button>
                    <ul class="dropdown-menu modern-dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                        @foreach ($branch as $item)
                        <li>
                            <a class="dropdown-item modern-dropdown-item branch-option" href="#"
                                data-branch-id="{{ $item->branch_id }}"
                                data-branch-name="{{ $item->Name }} Branch">
                                <i class="ri-building-2-line me-2"></i>
                                {{ $item->Name }} Branch
                                @if (session('branch_id') == $item->branch_id)
                                <i class="ri-check-line ms-auto text-success"></i>
                                @endif
                            </a>
                        </li>
                        @endforeach

                    </ul>

                </div>
            </div>
            @else
            @if (isset($allowedBranches) && $allowedBranches->count() > 1)
            <div class="modern-branch-switcher">
                <div class="dropdown">
                    <button type="button" class="btn modern-dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="ri-building-2-line me-2"></i>
                        <span class="branch-text text-truncate d-inline-block" style="max-width: 200px; vertical-align: middle;">
                            @php $currentName = optional($allowedBranches->firstWhere('branch_id', session('branch_id')))->Name; @endphp
                            {{ ($currentName ?? session('branch_name')) . ' Branch' }}
                        </span>
                        <i class="ri-arrow-down-s-line ms-2 dropdown-arrow"></i>
                    </button>
                    <ul class="dropdown-menu modern-dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                        @foreach ($allowedBranches as $item)
                        <li>
                            <a class="dropdown-item modern-dropdown-item branch-option" href="#"
                                data-branch-id="{{ $item->branch_id }}"
                                data-branch-name="{{ $item->Name }} Branch">
                                <i class="ri-building-2-line me-2"></i>
                                {{ $item->Name }} Branch
                                @if (session('branch_id') == $item->branch_id)
                                <i class="ri-check-line ms-auto text-success"></i>
                                @endif
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @else
            <h2 id="date" class="d-none d-md-block">{{ session('branch_name') . ' Branch' }}</h2>
            @endif
            @endif
        </div>
        <ul class="topbar-menu d-flex align-items-center gap-3">
            <!-- Animated Account Button (No Dropdown) -->
            <li class="nav-item">
                <a href="/" class="account-btn-animated">
                    <i class="ri-bank-fill svgIcon"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('approval.pending') }}">
                    <i class="ri-notification-3-line"></i>
                    <span class="d-none d-sm-inline">Approvals</span>

                    {{-- Badge only for Head Office --}}
                    @if (session('branch_id') == -1)
                    <span id="approvalBadge" class="badge bg-danger ms-1"
                        style="display:none; min-width:20px;">0</span>
                    @endif
                </a>
            </li>

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
                <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        @php
                        $companyItem = !empty($company) ? $company[0] : null;
                        $logoPath = $companyItem && $companyItem->logo ? 'storage/' . $companyItem->logo : '';
                        $logoUrl = ($logoPath && file_exists(public_path($logoPath))) ? asset($logoPath) : asset('assets/images/users/avatar-1.jpg');
                        @endphp
                        <img src="{{ $logoUrl }}" alt="user-image" width="32" class="rounded-circle">

                    </span>
                    <span class="d-lg-block d-none">
                        <h5 class="my-0 fw-normal">{{ session('Full_Name') }} <i
                                class="ri-arrow-down-s-line d-none d-sm-inline-block align-middle"></i></h5>
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">

                    <!-- item-->
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>
                    @if ($privilege)
                    @if (optional($privilege)->my_account == 1)
                    <a href="/company" class="dropdown-item">
                        <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                        <span>My Account</span>
                    </a>
                    @endif
                    @if (session('branch_id') == -1)
                    @if (optional($privilege)->settings == 1)
                    <a href="/setting" class="dropdown-item">
                        <i class="ri-settings-4-line fs-18 align-middle me-1"></i>
                        <span>Settings</span>
                    </a>
                    @endif
                    @endif


                    @if (optional($privilege)->sms_format == 1)


                    <?php
                    $query = "SELECT * FROM company where branch_id='" . session('branch_id') . "'";
                    $user_details = DB::select($query);
                    $companyMask = (!empty($user_details) && isset($user_details[0])) ? $user_details[0]->mask : null;
                    ?>

                    @if ($companyMask != null)
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

                    @endif

                    @if (optional($privilege)->document_format == 1)
                    <a href="/agreement" class="dropdown-item">
                        <i class="ri-file-paper-2-fill fs-18 align-middle me-1"></i>
                        <span>Document Format</span>
                    </a>
                    @endif

                    @if (optional($privilege)->company_holidays == 1)
                    <a href="/holidays" class="dropdown-item">
                        <i class="ri-moon-clear-line fs-18 align-middle me-1"></i>
                        <span>Company Holidays</span>
                    </a>
                    @endif
                    @if (session('branch_id') == -1)
                    @if (optional($privilege)->branches == 1)
                    <a href="/branch" class="dropdown-item">
                        <i class="ri-building-2-fill fs-18 align-middle me-1"></i>
                        <span>Branches</span>
                    </a>
                    @endif
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
                    $cashier = DB::table('user')->where('id', $user_id)->where('cashier', '=', '1')->first();
                    ?>
                    @if ($cashier)
                    <hr>
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Cashier Section</h6>
                    </div>
                    @if (optional($privilege)->cashier_start == 1)
                    <a class="dropdown-item d-flex align-items-center" href="#"
                        data-bs-toggle="modal" data-bs-target="#cashierStartModal">
                        <i class="ri-money-dollar-box-line font-size-17 align-middle me-1"></i> Cashier
                        Start
                    </a>
                    @endif

                    @if (optional($privilege)->cashier_close == 1)
                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                        data-bs-target="#dayEndModal">
                        <i class="mdi mdi-lock-open-outline font-size-17 align-middle me-1"></i> Cashier
                        Close
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
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
        <div class="date-time">
            @if (session('branch_access') === 1)
                <div class="modern-branch-switcher">
                    <div class="dropdown">
                        <button type="button" class="btn modern-dropdown-toggle" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="ri-building-2-line me-2"></i>
                            <span class="branch-text">
                                @foreach ($branch as $item)
                                    @if (session('branch_id') == $item->branch_id)
                                        {{ $item->Name }} Branch
                                    @endif
                                @endforeach
                            </span>
                            <i class="ri-arrow-down-s-line ms-2 dropdown-arrow"></i>
                        </button>
                        <ul class="dropdown-menu modern-dropdown-menu">
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
                                <span class="branch-text">
                                    @php $currentName = optional($allowedBranches->firstWhere('branch_id', session('branch_id')))->Name; @endphp
                                    {{ ($currentName ?? session('branch_name')) . ' Branch' }}
                                </span>
                                <i class="ri-arrow-down-s-line ms-2 dropdown-arrow"></i>
                            </button>
                            <ul class="dropdown-menu modern-dropdown-menu">
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
                    <h2 id="date">{{ session('branch_name') . ' Branch' }}</h2>
                @endif
            @endif
        </div>
        <ul class="topbar-menu d-flex align-items-center gap-3">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('approval.pending') }}">
                    <i class="ri-notification-3-line"></i>
                    <span>Approvals</span>

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
                        @foreach ($company as $item)
                            @php
                                $logoPath = 'storage/' . $item->logo;
                            @endphp
                            @if ($item->logo && file_exists(public_path($logoPath)))
                                <img src="{{ asset($logoPath) }}" alt="user-image" width="32"
                                    class="rounded-circle">
                            @else
                                <img src="{{ asset('assets/images/users/avatar-1.jpg') }}" alt="user-image"
                                    width="32" class="rounded-circle">
                            @endif
                        @endforeach

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
                            ?>


                            @foreach ($user_details as $item)
                                @if ($item->mask != null)
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







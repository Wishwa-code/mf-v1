<div class="navbar-custom">
    <div class="topbar container-fluid d-flex flex-wrap justify-content-between align-items-center">

        <div class="d-flex align-items-center mb-2 mb-md-0">

            <!-- Sidebar Menu Toggle Button -->
            <!-- Renamed to custom class to prevent double-toggle by app.min.js -->
            <button class="button-toggle-menu-custom me-2">
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

        <!-- Modern Date/Time Display -->
        <div class="modern-date-time d-none d-lg-flex flex-column align-items-end justify-content-center me-3">
            <span class="day-text" id="modern-day">{{ date('l') }}</span>
            <span class="date-text" id="modern-date">{{ date('d F Y') }}</span>
        </div>

        <div class="d-flex align-items-center justify-content-center gap-3">
            @if (session('branch_access') === 0)
            <div class="modern-branch-switcher">
                <div class="dropdown">
                    <button type="button" class="btn modern-dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="ri-building-2-line me-2"></i>
                        <span class="branch-text text-truncate d-inline-block" style="max-width: 200px; vertical-align: middle;">
                            @foreach ($branch as $item)
                            @php $item = (object) $item; @endphp
                            @if (session('branch_id') == $item->idBranch)
                            {{ $item->Name }}
                            @endif
                            @endforeach
                        </span>
                        <i class="ri-arrow-down-s-line ms-2 dropdown-arrow"></i>
                    </button>
                    <ul class="dropdown-menu modern-dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                        @foreach ($branch as $item)
                        @php $item = (object) $item; @endphp
                        <li>
                            <a class="dropdown-item modern-dropdown-item branch-option" href="#"
                                data-branch-id="{{ $item->idBranch }}"
                                data-branch-name="{{ $item->Name }} Branch">
                                <i class="ri-building-2-line me-2"></i>
                                {{ $item->Name }}
                                @if (session('branch_id') == $item->idBranch)
                                <i class="ri-check-line ms-auto text-success"></i>
                                @endif
                            </a>
                        </li>
                        @endforeach

                    </ul>

                </div>
            </div>
            @else
            @if (isset($allowedBranches))
            <div class="modern-branch-switcher">
                <div class="dropdown">
                    <button type="button" class="btn modern-dropdown-toggle" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="ri-building-2-line me-2"></i>
                        <span class="branch-text text-truncate d-inline-block" style="max-width: 200px; vertical-align: middle;">
                            @php
                            $col = collect($allowedBranches)->map(function($item){ return (object)$item; });
                            $current = $col->firstWhere('idBranch', session('branch_id'));
                            $currentName = optional($current)->Name;
                            @endphp
                            {{ ($currentName)}}
                        </span>
                        <i class="ri-arrow-down-s-line ms-2 dropdown-arrow"></i>
                    </button>
                    <ul class="dropdown-menu modern-dropdown-menu" style="max-height: 300px; overflow-y: auto;">
                        @foreach ($allowedBranches as $item)
                        @php $item = (object) $item; @endphp
                        <li>
                            <a class="dropdown-item modern-dropdown-item branch-option" href="#"
                                data-branch-id="{{ $item->idBranch }}"
                                data-branch-name="{{ $item->Name }} Branch">
                                <i class="ri-building-2-line me-2"></i>
                                {{ $item->Name }}
                                @if (session('branch_id') == $item->idBranch)
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

            <!-- Animated Account Button (No Dropdown) -->
            <div class="position-relative" style="width: 35px; height: 35px;">
                <a href="https://accountcenter.asipbook.com/" class="account-btn-animated position-absolute top-0 start-0" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Account Center">
                    <i class="ri-bank-fill svgIcon"></i>
                </a>
            </div>
        </div>

        <ul class="topbar-menu d-flex align-items-center gap-2">

            <li class="nav-item">
                <a class="modern-action-btn" href="{{ route('approval.pending') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Approvals">
                    <i class="ri-notification-3-line"></i>
                    {{-- Badge only for Head Office --}}
                    @if (session('head_branch') == session('branch_id'))
                    <span id="approvalBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="display:none; font-size: 0.6rem; padding: 0.25em 0.4em;">0</span>
                    @endif
                </a>
            </li>

            <li class="d-none d-sm-inline-block">
                <div class="modern-action-btn" id="light-dark-mode" role="button" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Toggle Theme">
                    <i class="ri-moon-line fs-22"></i>
                </div>
            </li>

            <li class="dropdown">
                <a class="nav-link dropdown-toggle arrow-none nav-user" data-bs-toggle="dropdown" href="#"
                    role="button" aria-haspopup="false" aria-expanded="false">
                    <span class="account-user-avatar">
                        @php
                        $logoPath = $companyItem && $companyItem->Logo ? 'storage/' . $companyItem->Logo : '';
                        $logoUrl = ($logoPath && file_exists(public_path($logoPath))) ? asset($logoPath) : asset('assets/images/users/avatar-1.jpg');
                        @endphp
                        <img src="{{ $logoUrl }}" alt="user-image" width="32" class="rounded-circle">

                    </span>
                    <span class="d-lg-block d-none">
                        <h5 class="my-0 fw-normal">{{ user_data('full_name') }} <i
                                class="ri-arrow-down-s-line d-none d-sm-inline-block align-middle"></i></h5>
                    </span>
                </a>

                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animated profile-dropdown">

                    <!-- item-->
                    <div class=" dropdown-header noti-title">
                        <h6 class="text-overflow m-0">Welcome !</h6>
                    </div>

                    @hasPrivilege('MY_ACCOUNT')
                    <a href="/company" class="dropdown-item">
                        <i class="ri-account-circle-line fs-18 align-middle me-1"></i>
                        <span>Company/User Info</span>
                    </a>
                    @endhasPrivilege

                    @if (session('head_branch') == session('branch_id'))
                    @hasPrivilege('SETTINGS')
                    <a href="/setting" class="dropdown-item">
                        <i class="ri-settings-4-line fs-18 align-middle me-1"></i>
                        <span>Settings</span>
                    </a>
                    @endhasPrivilege
                    @endif

                    @hasPrivilege('SMS_FORMAT')

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

                    @endhasPrivilege

                    @hasPrivilege('DOCUMENT_FORMAT')
                    <a href="/agreement" class="dropdown-item">
                        <i class="ri-file-paper-2-fill fs-18 align-middle me-1"></i>
                        <span>Document Format</span>
                    </a>
                    @endhasPrivilege

                    @hasPrivilege('COMPANY_HOLIDAYS')
                    <a href="/holidays" class="dropdown-item">
                        <i class="ri-moon-clear-line fs-18 align-middle me-1"></i>
                        <span>Company Holidays</span>
                    </a>
                    @endhasPrivilege

                    @if (session('head_branch') == session('branch_id'))
                    @hasPrivilege('BRANCHES')
                    <a href="/branch" class="dropdown-item">
                        <i class="ri-building-2-fill fs-18 align-middle me-1"></i>
                        <span>Branches</span>
                    </a>
                    @endhasPrivilege
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
                    @hasPrivilege('CASHIER_START')
                    <a class="dropdown-item d-flex align-items-center" href="#"
                        data-bs-toggle="modal" data-bs-target="#cashierStartModal">
                        <i class="ri-money-dollar-box-line font-size-17 align-middle me-1"></i> Cashier
                        Start
                    </a>
                    @endhasPrivilege

                    @hasPrivilege('CASHIER_CLOSE')
                    <a class="dropdown-item" href="#" data-bs-toggle="modal"
                        data-bs-target="#dayEndModal">
                        <i class="mdi mdi-lock-open-outline font-size-17 align-middle me-1"></i> Cashier
                        Close
                    </a>
                    @endhasPrivilege

                </div>

            </li>
        </ul>

    </div>

</div>
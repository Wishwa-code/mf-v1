<nav class="navbar navbar-expand-lg navbar-light fixed-top" style="height: 80px; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(0,0,0,0.05);">
    <div class="container-fluid px-4 h-100">
        <div class="d-flex align-items-center justify-content-between h-100 w-100 position-relative">

            <!-- Left: Sidebar Toggle -->
            <div class="d-flex align-items-center" style="z-index: 10;">
                <button type="button" class="button-toggle-menu btn btn-light text-secondary me-4 p-2 rounded-circle border-0 shadow-sm d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="ri-menu-line fs-5"></i>
                </button>

                <!-- Optional: Date/Time (Desktop) -->
                <div class="d-none d-lg-flex flex-column align-items-start ms-2">
                    <span class="fs-5 fw-bold text-dark tracking-tight" style="font-family: 'Inter', sans-serif;">{{ date('l') }}</span>
                    <span class="small fw-medium text-muted text-uppercase" style="letter-spacing: 0.5px;">{{ date('d F Y') }}</span>
                </div>
            </div>

            <!-- Center: Branch Switcher (Absolute Center) -->
            <div class="position-absolute start-50 top-50 translate-middle d-none d-md-block">
                @if (session('branch_access') === 1)
                <div class="dropdown">
                    <button type="button" class="btn border-0 d-flex align-items-center gap-2 text-white fw-semibold shadow-sm rounded-pill px-4 py-2 hover-scale" data-bs-toggle="dropdown" aria-expanded="false" style="background: linear-gradient(135deg, #4f46e5 0%, #ec4899 100%);" data-bs-placement="bottom"  data-bs-custom-class="shadcn-tooltip">
                        <div class="d-flex align-items-center justify-content-center bg-white bg-opacity-25 rounded-circle me-1" style="width: 24px; height: 24px;">
                            <i class="ri-building-2-fill fs-6 text-white"></i>
                        </div>
                        @if(isset($branch) && count($branch) > 0)
                        @php $currentBranch = collect($branch)->firstWhere('idBranch', session('branch_id')); @endphp
                        <span class="text-truncate" style="max-width: 200px;">{{ $currentBranch['Name'] ?? 'Select Branch' }}</span>
                        @else
                        <span class="text-truncate" style="max-width: 200px;">{{ session('branch_name') . ' Branch' }}</span>
                        @endif
                        <i class="ri-arrow-down-s-line ms-2 text-white opacity-75"></i>
                    </button>
                    <!-- Bootstrap Dropdown Menu -->
                    <ul class="dropdown-menu dropdown-menu-center shadow-lg border-0 mt-3 p-2 rounded-4" style="max-height: 400px; overflow-y: auto; min-width: 280px; transform: translateX(-15%);">
                        <div class="px-3 py-2 text-muted small fw-bold text-uppercase ls-1">Select Branch</div>
                        @if(isset($branch))
                        @foreach ($branch as $item)
                        @php $item = (object) $item; @endphp
                        <li>
                            <a class="dropdown-item d-flex align-items-center px-3 py-2 rounded-3 text-secondary branch-option mb-1 {{ session('branch_id') == $item->idBranch ? 'bg-indigo-50 text-primary fw-bold' : '' }}" href="#" data-branch-id="{{ $item->idBranch }}" data-branch-name="{{ $item->Name }} Branch">
                                {{ $item->Name }}
                                @if (session('branch_id') == $item->idBranch) <i class="ri-check-line ms-auto text-primary"></i> @endif
                            </a>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
                @else
                <div class="d-flex align-items-center gap-2 bg-white text-primary px-4 py-2 rounded-pill border border-primary-subtle fw-bold small shadow-sm">
                    <i class="ri-building-2-line"></i>
                    {{ session('branch_name') . ' Branch' }}
                </div>
                @endif
            </div>

            <!-- Right: Actions & Profile -->
            <div class="d-flex align-items-center gap-3" style="z-index: 10;">

                <!-- Mobile Branch Toggle (Visible only on mobile) -->
                <div class="d-md-none">
                    <!-- Simplified mobile branch indicator/icon could go here if needed -->
                </div>

                <!-- Theme Toggle -->
                <div class="btn btn-light rounded-circle d-flex align-items-center justify-content-center text-secondary shadow-sm border-0 transition-all hover-scale" style="width: 42px; height: 42px; cursor: pointer;" id="light-dark-mode" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Toggle Theme" data-bs-custom-class="shadcn-tooltip">
                    <i class="ri-moon-line fs-5"></i>
                </div>

                <!-- Approvals / Notifications -->
                <a href="{{ route('approval.pending') }}" class="btn btn-light rounded-circle d-flex align-items-center justify-content-center text-secondary position-relative shadow-sm border-0 transition-all hover-scale" style="width: 42px; height: 42px;" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Notifications" data-bs-custom-class="shadcn-tooltip">
                    <i class="ri-notification-3-line fs-5"></i>
                    @if (session('head_branch') == session('branch_id'))
                    <span id="approvalBadge" class="position-absolute badge rounded-circle bg-danger border border-2 border-white p-1" style="display:none; width: 12px; height: 12px; top: 10px; right: 8px;"></span>
                    @endif
                </a>

                <!-- User Profile Dropdown -->
                <div class="dropdown ms-2">
                    <button class="btn btn-link text-decoration-none d-flex align-items-center gap-2 p-0 focus-none" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-placement="bottom" data-bs-custom-class="shadcn-tooltip">
                        @php
                        $logoPath = $companyItem && $companyItem->Logo ? 'storage/' . $companyItem->Logo : '';
                        $logoUrl = ($logoPath && file_exists(public_path($logoPath))) ? asset($logoPath) : asset('assets/images/users/avatar-1.jpg');
                        @endphp
                        <img class="rounded-circle border border-2 border-white shadow-sm object-fit-cover" src="{{ $logoUrl }}" alt="" style="width: 40px; height: 40px;">
                        <div class="d-none d-lg-block text-start">
                            <p class="small fw-bold text-dark mb-0 text-truncate" style="max-width: 100px;">{{ user_data('full_name') }}</p>
                            <p class="text-muted mb-0" style="font-size: 10px;">Admin</p>
                        </div>
                        <i class="ri-arrow-down-s-line text-muted small"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 rounded-4" style="width: 240px;">

                        <div class="px-3 py-3 bg-light rounded-3 mb-2">
                            <p class="small fw-medium text-uppercase text-muted mb-1" style="font-size: 10px;">Signed in as</p>
                            <p class="small fw-bold text-dark mb-0 text-truncate">{{ user_data('full_name') }}</p>
                        </div>

                        @hasPrivilege('MY_ACCOUNT')
                        <li>
                            <a href="/company" class="dropdown-item d-flex align-items-center px-3 py-2 text-secondary rounded-3 mb-1">
                                <i class="ri-building-line me-2"></i> Company Info
                            </a>
                        </li>
                        @endhasPrivilege

                        @if (session('head_branch') == session('branch_id'))

                        @hasPrivilege('SETTINGS')
                        <li>
                            <a href="/setting" class="dropdown-item d-flex align-items-center px-3 py-2 text-secondary rounded-3 mb-1">
                                <i class="ri-settings-4-line me-2"></i> Settings
                            </a>
                        </li>
                        @endhasPrivilege

                        <!-- Additional Head Office Items -->
                        <li>
                            <a href="/users" class="dropdown-item d-flex align-items-center px-3 py-2 text-secondary rounded-3 mb-1">
                                <i class="ri-user-settings-line me-2"></i> Users
                            </a>
                        </li>
                        <li>
                            <a href="/roles" class="dropdown-item d-flex align-items-center px-3 py-2 text-secondary rounded-3 mb-1">
                                <i class="ri-shield-user-line me-2"></i> Roles & Permissions
                            </a>
                        </li>
                        @endif

                        @hasPrivilege('SMS_FORMAT')
                        <li>
                            <a href="{{ $companyMask != null ? '/sms' : '#' }}" class="dropdown-item d-flex align-items-center px-3 py-2 text-secondary rounded-3 mb-1">
                                <i class="ri-message-2-line me-2"></i> SMS Format
                            </a>
                        </li>
                        @endhasPrivilege

                        <!-- Support & Logout -->
                        <li>
                            <hr class="dropdown-divider my-2">
                        </li>

                        <li>
                            <a href="#" class="dropdown-item d-flex align-items-center px-3 py-2 text-secondary rounded-3 mb-1">
                                <i class="ri-customer-service-2-line me-2"></i> Support
                            </a>
                        </li>

                        <li>
                            <a href="/logout" class="dropdown-item d-flex align-items-center px-3 py-2 text-danger rounded-3 hover-bg-red-50">
                                <i class="ri-logout-box-line me-2"></i> Sign out
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
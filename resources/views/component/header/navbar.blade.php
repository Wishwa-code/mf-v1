<?php
// ...existing code...
use Illuminate\Support\Facades\DB;

// All branches
$branch = session('user_data')['branches'] ?? [];

// Allowed branches 
$allowedBranches = $branch;

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

     @media (max-width: 768px) {
         .modern-branch-switcher .modern-dropdown-toggle {
             width: 35px;
             height: 35px;
             padding: 0;
             display: flex;
             align-items: center;
             justify-content: center;
             border-radius: 12px;
             background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
             box-shadow: 0 4px 10px rgba(99, 102, 241, 0.4);
             min-width: 0px !important;
         }

         .modern-branch-switcher .modern-dropdown-toggle:hover {
             transform: translateY(-2px);
             box-shadow: 0 6px 15px rgba(99, 102, 241, 0.5);
         }

         .modern-branch-switcher .modern-dropdown-toggle i {
             font-size: 1.2rem;
             margin-right: 0 !important;
         }

         /* Hide text and arrow on mobile */
         .modern-branch-switcher .branch-text,
         .modern-branch-switcher .dropdown-arrow {
             display: none !important;
         }
     }

     /* Modern Date/Time */
     .modern-date-time {
         line-height: 1;
         text-align: right;
     }

     .modern-date-time .day-text {
         font-family: 'Poppins', sans-serif;
         font-size: 1.5rem;
         font-weight: 800;
         color: #334155;
         text-transform: uppercase;
         letter-spacing: 0.5px;
         background: linear-gradient(135deg, #475569 0%, #1e293b 100%);
         -webkit-background-clip: text;
         background-clip: text;
         -webkit-text-fill-color: transparent;
         display: block;
         margin-bottom: 0px;
     }

     .modern-date-time .date-text {
         font-family: 'Poppins', sans-serif;
         font-size: 0.85rem;
         font-weight: 600;
         color: #94a3b8;
         display: block;
     }

     /* Modern Action Buttons */
     .modern-action-btn {
         width: 40px;
         height: 40px;
         display: flex;
         align-items: center;
         justify-content: center;
         border-radius: 12px;
         background: #fff;
         color: #64748b;
         transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
         border: 1px solid #e2e8f0;
         text-decoration: none !important;
         position: relative;
     }

     .modern-action-btn:hover {
         background: #f8fafc;
         border-color: #cbd5e1;
         transform: translateY(-2px);
         box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
     }

     /* Specific Action Colors */
     .modern-action-btn[title="Approvals"] {
         color: #ef4444;
         background: #fef2f2;
         border-color: #fee2e2;
     }

     .modern-action-btn[title="Approvals"]:hover {
         background: #ef4444;
         color: white;
         border-color: #ef4444;
         box-shadow: 0 4px 10px rgba(239, 68, 68, 0.2);
     }

     .modern-action-btn[title="Toggle Theme"] {
         color: #6366f1;
         background: #eef2ff;
         border-color: #e0e7ff;
     }

     .modern-action-btn[title="Toggle Theme"]:hover {
         background: #6366f1;
         color: white;
         border-color: #6366f1;
         box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
     }

     .modern-action-btn i {
         font-size: 1.25rem;
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
         border-radius: 12px;
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
         height: 40px;
         border-radius: 12px;
         transition-duration: 0.3s;
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
 </style>


 <div class="navbar-custom">
     <div class="topbar container-fluid">
         <div class="d-flex align-items-center">

             <!-- Sidebar Menu Toggle Button -->
             <button class="button-toggle-menu me-2">
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
             @if (session('branch_access') === 1)
             <div class="modern-branch-switcher">
                 <div class="dropdown">
                     <button type="button" class="btn modern-dropdown-toggle" data-bs-toggle="dropdown"
                         aria-expanded="false">
                         <i class="ri-building-2-line me-2"></i>
                         <span class="branch-text text-truncate d-inline-block" style="max-width: 200px; vertical-align: middle;">
                             @foreach ($branch as $item)
                             @php $item = (object) $item; @endphp
                             @if (session('branch_id') == $item->idBranch)
                             {{ $item->Name }} Branch
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
                                 {{ $item->Name }} Branch
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
                             {{ ($currentName ?? session('branch_name')) . ' Branch' }}
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
                                 {{ $item->Name }} Branch
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
                     @if (session('branch_id') == -1)
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
     <script>
         document.addEventListener("DOMContentLoaded", function() {
             function updateTime() {
                 var now = new Date();

                 // Day Name (e.g. Saturday)
                 var dayOptions = {
                     weekday: 'long'
                 };
                 var dayName = now.toLocaleDateString('en-US', dayOptions);

                 // Date (e.g. 27 December 2025)
                 var dateOptions = {
                     year: 'numeric',
                     month: 'long',
                     day: 'numeric'
                 };
                 var formattedDate = now.toLocaleDateString('en-US', dateOptions);

                 if (document.getElementById('modern-day')) {
                     document.getElementById('modern-day').innerText = dayName;
                 }
                 if (document.getElementById('modern-date')) {
                     document.getElementById('modern-date').innerText = formattedDate;
                 }
             }

             updateTime();
             setInterval(updateTime, 60000);
         });
     </script>
 </div>
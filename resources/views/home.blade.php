@extends('layout.admin')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

    <style>
        body {
            background: linear-gradient(120deg, #f6f9ff, #e9f3ff);
            background-size: 400% 400%;
            animation: gradientBackground 20s ease infinite;
        }

        @keyframes gradientBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
            transition: all 0.3s ease-in-out;
        }

        .glass-card:hover {
            transform: scale(1.02);
        }

        .animated-dashboard {
            animation: fadeInDashboard 1s ease-in-out both;
        }

        @keyframes fadeInDashboard {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animated-card {
            animation: fadeInUp 0.8s ease forwards;
        }

        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        /* Modern Modal Styles */
        .modern-modal {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            backdrop-filter: blur(10px);
        }

        .modern-modal-header {
            border-radius: 16px 16px 0 0 !important;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        .modal-icon-container {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
        }

        .modern-modal-body {
            padding: 2rem;
            background: #fafbfc;
        }

        /* Modern Table Styles */
        .modern-table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.08);
        }

        .modern-table {
            border-radius: 12px;
            overflow: hidden;
            border: none;
        }

        .modern-table-header {
            background: #f8f9fa;
            color: #495057;
            position: relative;
            border-bottom: 2px solid #dee2e6;
        }

        .modern-table-warning {
            background: #f8f9fa !important;
            color: #495057 !important;
            border-bottom: 2px solid #ffc107 !important;
        }

        .modern-table-danger {
            background: #f8f9fa !important;
            color: #495057 !important;
            border-bottom: 2px solid #dc3545 !important;
        }

        .modern-table-header th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            background: #f8f9fa;
        }

        /* Remove DataTable sorting arrows */
        .modern-table thead th.sorting:before,
        .modern-table thead th.sorting:after,
        .modern-table thead th.sorting_asc:before,
        .modern-table thead th.sorting_asc:after,
        .modern-table thead th.sorting_desc:before,
        .modern-table thead th.sorting_desc:after {
            display: none !important;
        }

        .modern-table thead th {
            cursor: default !important;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
            border: none;
        }

        .modern-table tbody tr:hover {
            background: linear-gradient(135deg, #f8f9ff, #e3f2fd);
            transform: translateX(2px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .modern-table tbody td {
            border: none;
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
        }

        .modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Loading Skeleton */
        .skeleton-loader {
            padding: 0 2rem;
        }

        .skeleton-row {
            height: 20px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 2s infinite;
            border-radius: 4px;
            margin: 0.75rem 0;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Enhanced DataTable Styling */
        .modern-table-container .dataTables_wrapper .dataTables_length,
        .modern-table-container .dataTables_wrapper .dataTables_filter {
            margin: 1rem;
        }

        .modern-table-container .dataTables_wrapper .dataTables_info,
        .modern-table-container .dataTables_wrapper .dataTables_paginate {
            margin: 1rem;
        }

        .modern-table-container .dt-buttons {
            margin: 1rem;
        }

        .modern-table-container .dt-button {
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
            border: none !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 0.5rem 1rem !important;
            margin-right: 0.5rem !important;
            transition: all 0.3s ease !important;
        }

        .modern-table-container .dt-button:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4) !important;
        }

        /* Modal Animation */
        .modal.fade .modal-dialog {
            transition: transform 0.4s ease-in-out, opacity 0.4s ease-in-out;
            transform: translate(0, -100px) scale(0.9);
        }

        .modal.show .modal-dialog {
            transform: translate(0, 0) scale(1);
        }

        .modern-table-header th[title] {
            cursor: help;
            position: relative;
        }

        .modern-table-header th[title]:hover {
            background: #e9ecef !important;
        }

        /* Shortcut tiles */
        .shortcut-tile {
            border-radius: 12px;
            transition: all 0.25s ease-in-out;
            color: white;
        }
        .shortcut-tile:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
        }
        .shortcut-icon {
            font-size: 1.8rem;
        }
        .shortcut-label {
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 6px;
        }
        /* Make modal body scroll instead of full page */
        .modal-dialog.modal-dialog-scrollable .modern-modal-body {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

    </style>
@endsection

@section('content')
    @if($dashboard == 1)
        <div class="container-fluid py-4 animated-dashboard">
            @php
                $all_loan = $customer_loan_current_Count + $setteled_loan_Count;
                $loan_completion_percentage = $all_loan > 0 ? round(($setteled_loan_Count / $all_loan) * 100, 2) : 0;
            @endphp

            <div class="container-fluid py-4">
                {{-- Welcome Banner --}}
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="glass-card card text-white shadow-lg animated-card" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
                                <div>
                                    @if(session('branch_id') == -1)
                                        <h2 class="mb-1">All Branches Overview 🏢</h2>
                                    @else
                                        <h2 class="mb-1">Welcome Back 👋</h2>
                                    @endif
                                    <p class="mb-0" id="live-datetime"></p>
                                </div>
                                @if(session('branch_id') == -1)
                                    <i class="ri-building-2-line display-4"></i>
                                @else
                                    <i class="ri-user-smile-line display-4"></i>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Statistic Cards --}}
                    <div class="row">
                        @php
                            $cards = [
                                ['title' => 'Pending Loans', 'icon' => 'ri-eye-line', 'value' => $customer_loan_pending_Amount, 'count' => $customer_loan_pending_Count, 'link' => '/pendingloan', 'bg' => '#ff758c', 'prefix' => ''],
                                ['title' => 'Current Loans', 'icon' => 'ri-wallet-2-line', 'value' => $customer_loan_current_Amount, 'count' => $customer_loan_current_Count, 'link' => '/payment_step_1', 'bg' => '#43cea2', 'prefix' => ''],
                                ['title' => 'Settled Loans', 'icon' => 'ri-file-paper-2-fill', 'value' => $setteled_loan_current_Amount, 'count' => $setteled_loan_Count, 'link' => '/showsettleloan', 'bg' => '#f7971e', 'prefix' => ''],
                                ['title' => 'Portfolio', 'icon' => 'ri-pie-chart-line', 'value' => $portfolio, 'count' => '', 'link' => '', 'bg' => '#9b59b6', 'prefix' => ''],
                                ['title' => 'Current Month Lending', 'icon' => 'ri-calendar-line', 'value' => $currentMonthLending, 'count' => '', 'link' => '', 'bg' => '#1abc9c', 'prefix' => ''],
                                ['title' => 'Customers', 'icon' => 'ri-group-2-line', 'value' => $customerCount, 'count' => '', 'link' => '/showcustomers', 'bg' => '#667eea', 'prefix' => ''],
                            ];
                        @endphp

                        @foreach($cards as $index => $card)
                            <div class="col-md-3 mb-4">
                                <a href="{{ $card['link'] }}" class="text-decoration-none">
                                    <div class="glass-card card text-white shadow animated-card" style="background-color: {{ $card['bg'] }};">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="text-uppercase">{{ $card['title'] }} {!! $card['count'] !== '' ? '('.$card['count'].')' : '' !!}</h6>
                                                    <h4><span id="stat-card-{{ $index }}"></span></h4>
                                                </div>
                                                <i class="{{ $card['icon'] }} fs-2"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach

                        {{-- Summary Cards --}}
                        @php
                            $extra = [
                                ['title' => 'Today Due Amount', 'value' => $todayinstallment, 'color' => '#1e3c72'],
                                ['title' => 'Today Due Balance', 'value' => $todayinstallment_balance, 'color' => '#f7971e'],
                                ['title' => 'Today Not Paid', 'value' => $todayNotPaid, 'color' => '#e74c3c'],
                                ['title' => 'Total Arrears', 'value' => $arrease, 'color' => '#ef473a'],
                                ['title' => 'Cheque Payments', 'value' => $checqueamount, 'color' => '#3498db'],
                                ['title' => 'Due Outstanding (Installment Due + Arrears)', 'value' => ($todayNotPaid + $arrease), 'color' => '#0072ff'],
                                ['title' => 'Today Collected Amount', 'value' => $todaycollected, 'color' => '#ef803a'],
                                ['title' => 'Total Outstanding', 'value' => $totalOutstanding, 'color' => '#01503c'],
                                ['title' => 'Penalty Balance', 'value' => $penaltyBalance, 'color' => '#c0392b'],
                            ];
                        @endphp

                        @foreach($extra as $i => $item)
                            <div class="col-md-2 mb-4">
                                @if($item['title'] === 'Total Outstanding')
                                    <a href="#" onclick="showTotalOutstandingModal()" class="text-decoration-none">
                                        <div class="glass-card card text-white shadow animated-card" style="background-color: {{ $item['color'] }};">
                                            <div class="card-body">
                                                <h6 class="text-uppercase">{{ $item['title'] }}</h6>
                                                <h4><span id="extra-card-{{ $i }}"></span></h4>
                                            </div>
                                        </div>
                                    </a>
                                @elseif($item['title'] === 'Penalty Balance')
                                    <a href="#" onclick="showPenaltyBalanceModal()" class="text-decoration-none">
                                        <div class="glass-card card text-white shadow animated-card" style="background-color: {{ $item['color'] }};">
                                            <div class="card-body">
                                                <h6 class="text-uppercase">{{ $item['title'] }}</h6>
                                                <h4><span id="extra-card-{{ $i }}"></span></h4>
                                            </div>
                                        </div>
                                    </a>
                                @else
                                    <div class="glass-card card text-white shadow animated-card" style="background-color: {{ $item['color'] }};">
                                        <div class="card-body">
                                            <h6 class="text-uppercase">{{ $item['title'] }}</h6>
                                            <h4><span id="extra-card-{{ $i }}"></span></h4>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        {{-- This Week Arrears --}}
                        <div class="col-md-6 col-lg-5 mb-4">
                            <a href="#" onclick="showWeeklyNotPaidModal()" class="text-decoration-none">
                                <div class="glass-card card text-white shadow animated-card" style="background: linear-gradient(135deg, #8e44ad, #2c3e50);">
                                    <div class="card-body">
                                        <h6 class="text-uppercase mb-3">This Week Arers</h6>
                                        <div class="row g-3 align-items-stretch">
                                            <div class="col-4">
                                                <div class="text-center">
                                                    <h4><span id="weekly-unpaid-count"></span></h4>
                                                    <small class="text-light">Installments</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-center">
                                                    <h4><span id="weekly-unpaid-headcount"></span></h4>
                                                    <small class="text-light">Customers</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-center">
                                                    <h4><span id="weekly-unpaid-amount"></span></h4>
                                                    <small class="text-light">Amount</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        {{-- Current Week Pending Payment --}}
                        <div class="col-md-6 col-lg-5 mb-4">
                            <a href="#" onclick="showCurrentWeekPendingModal()" class="text-decoration-none">
                                <div class="glass-card card text-white shadow animated-card" style="background: linear-gradient(135deg, #3498db, #2c3e50);">
                                    <div class="card-body">
                                        <h6 class="text-uppercase mb-3">Current Week Pending Payment</h6>
                                        <div class="row g-3 align-items-stretch">
                                            <div class="col-4">
                                                <div class="text-center">
                                                    <h4><span id="current-week-pending-count"></span></h4>
                                                    <small class="text-light">Installments</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-center">
                                                    <h4><span id="current-week-pending-headcount"></span></h4>
                                                    <small class="text-light">Customers</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="text-center">
                                                    <h4><span id="current-week-pending-amount"></span></h4>
                                                    <small class="text-light">Amount</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    {{-- Charts --}}
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="glass-card card shadow animated-card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">📈 Monthly Payments</h5>
                                    <div id="monthly-revenue-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="glass-card card shadow animated-card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">💼 Loan Status</h5>
                                    <div id="loan-type-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-4">
                            <div class="glass-card card shadow animated-card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">📊 Weekly Comparison</h5>
                                    <div id="bar-comparison-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 mb-4">
                            <div class="glass-card card shadow animated-card">
                                <div class="card-body">
                                    <h5 class="card-title mb-3">✅ Loan Completion</h5>
                                    <div id="radial-progress-chart" style="height: 300px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Shortcuts --}}
                    @if($shortcut_count > 0)
                        <div class="row mt-5">
                            <div class="col-12 mb-3">
                                <div class="glass-card card border-0 shadow-sm animated-card"
                                     style="background: rgba(255,255,255,0.15); backdrop-filter: blur(6px);">
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <h4 class="mb-0 text-dark fw-bold">Quick Access Shortcuts</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php
                        $colors = ['#f1c40f', '#2ecc71', '#e67e22', '#3498db', '#9b59b6', '#1abc9c', '#34495e', '#e74c3c'];
                    @endphp

                    <div class="row g-3">
                        @foreach($shortcut as $index => $item)
                            @php
                                $url = "/";
                                $name = "";
                                $icon = "";
                                $bgColor = $colors[$index % count($colors)];

                                switch($item->name) {
                                    case "Add_Customer": $url = "/customers"; $name = "Add Customer"; $icon = "fas fa-user-plus"; break;
                                    case "View_Customer": $url = "/showcustomers"; $name = "View Customers"; $icon = "fas fa-users"; break;
                                    case "Assign_Customers_to_group": $url = "/customergroupassign"; $name = "Assign to Group"; $icon = "fas fa-user-friends"; break;
                                    case "View_Products": $url = "/viewproduct"; $name = "View Products"; $icon = "fas fa-box-open"; break;
                                    case "Pending_Loans": $url = "/pendingloan"; $name = "Pending Loans"; $icon = "fas fa-hourglass-half"; break;
                                    case "Current_Loans": $url = "/payment_step_1"; $name = "Current Loans"; $icon = "fas fa-hand-holding-usd"; break;
                                    case "Loan_In_arrears": $url = "/latePayment"; $name = "Loan Arrears"; $icon = "fas fa-exclamation-triangle"; break;
                                    case "Add_Repayment": $url = "/payment"; $name = "Add Repayment"; $icon = "fas fa-money-check-alt"; break;
                                    case "Repayment_details": $url = "/viewpayment"; $name = "View Repayment"; $icon = "fas fa-file-invoice-dollar"; break;
                                    case "Collector_wise_collections": $url = "/collection"; $name = "Agent Collections"; $icon = "fas fa-user-tie"; break;
                                    case "Loan_Calculator": $url = "/calculator"; $name = "Loan Calculator"; $icon = "fas fa-calculator"; break;
                                    case "Add_Expenses": $url = "/expenses"; $name = "Add Expenses"; $icon = "fas fa-receipt"; break;
                                    case "Add_Income": $url = "/income"; $name = "Add Income"; $icon = "fas fa-hand-holding-usd"; break;
                                }
                            @endphp

                            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                                <a href="{{ $url }}" class="text-decoration-none">
                                    <div class="shortcut-tile text-center p-3 shadow-sm animated-card" style="background-color: {{ $bgColor }};">
                                        <i class="{{ $icon }} shortcut-icon"></i>
                                        <div class="shortcut-label">{{ $name }}</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Hidden button for loan process --}}
            <button id="startLoanProcess" hidden>Start Processing Loans</button>

            {{-- ===================== MODALS ===================== --}}

            <!-- Total Outstanding Modal -->
            <!-- Total Outstanding Modal -->
            <div class="modal fade"
                 id="totalOutstandingModal"
                 tabindex="-1"
                 aria-labelledby="totalOutstandingModalLabel"
                 aria-hidden="true"
                 data-backdrop="static"
                 data-keyboard="false">

                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content modern-modal">
                        <div class="modal-header modern-modal-header" style="background: #ffffff; color: #2d3748; border-bottom: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center">
                                <div class="modal-icon-container me-3" style="background: #edf2f7; color: #01503c;">
                                    <i class="ri-money-dollar-circle-line fs-4"></i>
                                </div>
                                <div>
                                    <h4 class="modal-title mb-0" id="totalOutstandingModalLabel">💰 Total Outstanding</h4>
                                    <small class="text-muted">Complete loan portfolio overview</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body modern-modal-body">
                            <!-- Loading State -->
                            <div id="outstanding-loading" class="d-none">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                                    <h5 class="text-muted">Loading outstanding loans...</h5>
                                    <div class="skeleton-loader mt-4">
                                        <div class="skeleton-row"></div>
                                        <div class="skeleton-row"></div>
                                        <div class="skeleton-row"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Table Container -->
                            <div id="outstanding-content">
                                <div class="table-responsive modern-table-container">
                                    <table class="table modern-table mb-0" id="outstanding_table_modal">
                                        <thead class="modern-table-header">
                                        <tr>
                                            <th>Loan ID</th>
                                            <th>Customer ID</th>
                                            <th>Customer Name</th>
                                            <th class="text-end" title="Loan Amount - Capital amount">Capital Amount</th>
                                            <th class="text-end" title="Full Loan Amount - Capital and interest">Full Loan Amount</th>
                                            <th class="text-end" title="Total Outstanding Amount - Capital balance + interest balance">Total Outstanding</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Data will be loaded via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <!-- Weekly Not Paid Modal -->
            <!-- Weekly Not Paid Modal -->
            <div class="modal fade"
                 id="weeklyNotPaidModal"
                 tabindex="-1"
                 aria-labelledby="weeklyNotPaidModalLabel"
                 aria-hidden="true"
                 data-backdrop="static"
                 data-keyboard="false">

                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content modern-modal">
                        <div class="modal-header modern-modal-header" style="background: #ffffff; color: #2d3748; border-bottom: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center">
                                <div class="modal-icon-container me-3" style="background: #fef5e7; color: #f39c12;">
                                    <i class="ri-calendar-event-line fs-4"></i>
                                </div>
                                <div>
                                    <h4 class="modal-title mb-0" id="weeklyNotPaidModalLabel">📅 This Week Arers</h4>
                                    <small class="text-muted">Loans with missed payments this week (Sunday To Today)</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body modern-modal-body">
                            <div id="weekly-loading" class="d-none">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-warning mb-3" style="width: 3rem; height: 3rem;"></div>
                                    <h5 class="text-muted">Loading weekly unpaid loans...</h5>
                                    <div class="skeleton-loader mt-4">
                                        <div class="skeleton-row"></div>
                                        <div class="skeleton-row"></div>
                                        <div class="skeleton-row"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="weekly-content">
                                <div class="table-responsive modern-table-container">
                                    <table class="table modern-table mb-0" id="weekly_not_paid_table_modal">
                                        <thead class="modern-table-header modern-table-warning">
                                        <tr>
                                            <th>Loan ID</th>
                                            <th>Customer ID</th>
                                            <th>Customer Name</th>
                                            <th class="text-end" title="Loan Amount - Capital amount">Capital Amount</th>
                                            <th class="text-end" title="Full Loan Amount - Capital + interest">Full Loan Amount</th>
                                            <th class="text-end" title="This Week Not Paid Amount">Week Not Paid</th>
                                            <th class="text-end" title="Total Not Paid Amount - Arrears">Total Arrears</th>
                                            <th class="text-center" title="Not Paid Installment Count">Unpaid Count</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Data via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>


            <!-- Current Week Pending Payment Modal -->
            <!-- Current Week Pending Payment Modal -->
            <div class="modal fade"
                 id="currentWeekPendingModal"
                 tabindex="-1"
                 aria-labelledby="currentWeekPendingModalLabel"
                 aria-hidden="true"
                 data-backdrop="static"
                 data-keyboard="false">

                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content modern-modal">
                        <div class="modal-header modern-modal-header" style="background: #ffffff; color: #2d3748; border-bottom: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center">
                                <div class="modal-icon-container me-3" style="background: #e3f2fd; color: #2196f3;">
                                    <i class="ri-calendar-check-line fs-4"></i>
                                </div>
                                <div>
                                    <h4 class="modal-title mb-0" id="currentWeekPendingModalLabel">📅 Current Week Pending Payment</h4>
                                    <small class="text-muted">All pending payments for the current week (Sunday to Saturday)</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body modern-modal-body">
                            <div id="current-week-loading" class="d-none">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>
                                    <h5 class="text-muted">Loading current week pending payments...</h5>
                                    <div class="skeleton-loader mt-4">
                                        <div class="skeleton-row"></div>
                                        <div class="skeleton-row"></div>
                                        <div class="skeleton-row"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="current-week-content">
                                <div class="table-responsive modern-table-container">
                                    <table class="table modern-table mb-0" id="current_week_pending_table_modal">
                                        <thead class="modern-table-header">
                                        <tr>
                                            <th>Loan ID</th>
                                            <th>Customer ID</th>
                                            <th>Customer Name</th>
                                            <th class="text-end" title="Loan Amount - Capital amount">Capital Amount</th>
                                            <th class="text-end" title="Full Loan Amount - Capital + interest">Full Loan Amount</th>
                                            <th class="text-end" title="Current Week Pending Amount">Week Pending</th>
                                            <th class="text-end" title="Total Not Paid Amount - Arrears">Total Arrears</th>
                                            <th class="text-center" title="Not Paid Installment Count">Unpaid Count</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Data via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            <!-- Penalty Balance Modal -->
            <!-- Penalty Balance Modal -->
            <div class="modal fade"
                 id="penaltyBalanceModal"
                 tabindex="-1"
                 aria-labelledby="penaltyBalanceModalLabel"
                 aria-hidden="true"
                 data-backdrop="static"
                 data-keyboard="false">

                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content modern-modal">
                        <div class="modal-header modern-modal-header" style="background: #ffffff; color: #2d3748; border-bottom: 1px solid #e2e8f0;">
                            <div class="d-flex align-items-center">
                                <div class="modal-icon-container me-3" style="background: #fed7d7; color: #c53030;">
                                    <i class="ri-error-warning-line fs-4"></i>
                                </div>
                                <div>
                                    <h4 class="modal-title mb-0" id="penaltyBalanceModalLabel">⚠️ Penalty Balance</h4>
                                    <small class="text-muted">Outstanding penalty charges</small>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body modern-modal-body">
                            <div id="penalty-loading" class="d-none">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-danger mb-3" style="width: 3rem; height: 3rem;"></div>
                                    <h5 class="text-muted">Loading penalty balances...</h5>
                                    <div class="skeleton-loader mt-4">
                                        <div class="skeleton-row"></div>
                                        <div class="skeleton-row"></div>
                                        <div class="skeleton-row"></div>
                                    </div>
                                </div>
                            </div>

                            <div id="penalty-content">
                                <div class="table-responsive modern-table-container">
                                    <table class="table modern-table mb-0" id="penalty_balance_table_modal">
                                        <thead class="modern-table-header modern-table-danger">
                                        <tr>
                                            <th>Loan ID</th>
                                            <th>Customer ID</th>
                                            <th>Customer Name</th>
                                            <th class="text-end" title="Loan Amount - Capital amount">Capital Amount</th>
                                            <th class="text-end" title="Full Loan Amount - Capital and interest">Full Loan Amount</th>
                                            <th class="text-end" title="Total Outstanding Amount - Capital balance + interest balance">Total Outstanding</th>
                                            <th class="text-end" title="Penalty Balance">Penalty Balance</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <!-- Data via AJAX -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- ================== END MODALS =================== --}}
        </div>
    @endif
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.7/countUp.umd.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        // Live DateTime
        function updateDateTime() {
            const dt = new Date();
            const el = document.getElementById('live-datetime');
            if (el) el.innerText = dt.toLocaleString();
        }
        updateDateTime();
        setInterval(updateDateTime, 1000);

        document.addEventListener("DOMContentLoaded", function () {
            // Animate Stat Cards
            const statValues = [
                {{ $customer_loan_pending_Amount }},
                {{ $customer_loan_current_Amount }},
                {{ $setteled_loan_current_Amount }},
                {{ $portfolio }},
                {{ $currentMonthLending }},
                {{ $customerCount }}
            ];
            const prefixes = ['', '', '', '', '', ''];

            statValues.forEach((val, i) => {
                const numAnim = new countUp.CountUp('stat-card-' + i, val, {
                    prefix: prefixes[i],
                    separator: ',',
                    decimalPlaces: 0
                });
                if (!numAnim.error) numAnim.start();
            });

            const extraValues = [
                {{ $todayinstallment }},
                {{ $todayinstallment_balance }},
                {{ $todayNotPaid }},
                {{ $arrease }},
                {{ $checqueamount }},
                {{ $todayNotPaid + $arrease }},
                {{ $todaycollected }},
                {{ $totalOutstanding }},
                {{ $penaltyBalance }}
            ];
            extraValues.forEach((val, i) => {
                const extraAnim = new countUp.CountUp('extra-card-' + i, val, {
                    separator: ',',
                    decimalPlaces: 2
                });
                if (!extraAnim.error) extraAnim.start();
            });

            const weeklyCountAnim = new countUp.CountUp('weekly-unpaid-count', {{ $weeklyUnpaidCount }}, {
                separator: ',',
                decimalPlaces: 0
            });
            if (!weeklyCountAnim.error) weeklyCountAnim.start();

            const weeklyHeadAnim = new countUp.CountUp('weekly-unpaid-headcount', {{ $weeklyUnpaidCustomerCount ?? 0 }}, {
                separator: ',',
                decimalPlaces: 0
            });
            if (!weeklyHeadAnim.error) weeklyHeadAnim.start();

            const weeklyAmountAnim = new countUp.CountUp('weekly-unpaid-amount', {{ $weeklyUnpaidAmount }}, {
                separator: ',',
                decimalPlaces: 2
            });
            if (!weeklyAmountAnim.error) weeklyAmountAnim.start();

            const currentWeekCountAnim = new countUp.CountUp('current-week-pending-count', {{ $currentWeekPendingCount ?? 0 }}, {
                separator: ',',
                decimalPlaces: 0
            });
            if (!currentWeekCountAnim.error) currentWeekCountAnim.start();

            const currentWeekHeadAnim = new countUp.CountUp('current-week-pending-headcount', {{ $currentWeekPendingCustomerCount ?? 0 }}, {
                separator: ',',
                decimalPlaces: 0
            });
            if (!currentWeekHeadAnim.error) currentWeekHeadAnim.start();

            const currentWeekAmountAnim = new countUp.CountUp('current-week-pending-amount', {{ $currentWeekPendingAmount ?? 0 }}, {
                separator: ',',
                decimalPlaces: 2
            });
            if (!currentWeekAmountAnim.error) currentWeekAmountAnim.start();

            // Monthly Area Chart
            new ApexCharts(document.querySelector("#monthly-revenue-chart"), {
                chart: { type: 'area', height: 300 },
                series: [{ name: 'Payments', data: @json($monthlyData) }],
                colors: ['#8e44ad'],
                stroke: { curve: 'smooth', width: 3 },
                xaxis: { categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] }
            }).render();

            // Loan Type Pie
            new ApexCharts(document.querySelector("#loan-type-chart"), {
                chart: { type: 'pie', height: 300 },
                series: [{{ $customer_loan_current_Count }}, {{ $customer_loan_pending_Count }}, {{ $setteled_loan_Count }}, {{ $deleted_loan_Count }}],
                labels: ['Ongoing Loans', 'Pending Loans', 'Settle Loans', 'Delete Loans'],
                colors: ['#1abc9c', '#3498db', '#e67e22', '#e74c3c']
            }).render();

            // Weekly Comparison
            new ApexCharts(document.querySelector("#bar-comparison-chart"), {
                chart: { type: 'bar', height: 300 },
                series: [
                    { name: 'This Week', data: @json($weeklyComparison['current']) },
                    { name: 'Last Week', data: @json($weeklyComparison['last']) }
                ],
                xaxis: { categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] },
                colors: ['#1abc9c', '#e74c3c'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%'
                    }
                }
            }).render();

            // Radial Completion
            new ApexCharts(document.querySelector("#radial-progress-chart"), {
                chart: { type: 'radialBar', height: 300 },
                series: [{{ $loan_completion_percentage ?? 0 }}],
                labels: ['Completion'],
                colors: ['#f39c12'],
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: { fontSize: '16px' },
                            value: { fontSize: '30px', fontWeight: 'bold' }
                        }
                    }
                }
            }).render();
        });
    </script>

    {{-- Loan process Swal --}}
    <script>
        $('#startLoanProcess').on('click', function () {
            $.get('/get-loan-ids', function (data) {
                const loanIds = data.loan_ids;
                const total = loanIds.length;
                let index = 0;

                Swal.fire({
                    title: 'Processing Loans...',
                    html: `<div style="font-size:14px;">Please wait while we process ${total} loans.</div>
                           <div id="swal-progress" style="margin-top:15px; background:#eee; border-radius:4px; overflow:hidden;">
                               <div id="swal-progress-bar" style="height:15px; width:0%; background:#4caf50;"></div>
                           </div>
                           <div style="margin-top:10px; font-size:13px;">
                               <span id="swal-count">0/${total}</span>
                           </div>`,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        processNextLoan();
                    }
                });

                function updateProgress() {
                    const percentage = Math.round((index / total) * 100);
                    $('#swal-progress-bar').css('width', percentage + '%');
                    $('#swal-count').text(`${index}/${total}`);
                }

                function processNextLoan() {
                    if (index >= total) {
                        Swal.fire({
                            icon: 'success',
                            title: 'All loans processed!',
                            text: `${total} loans have been successfully updated.`,
                            confirmButtonText: 'OK'
                        });
                        return;
                    }

                    const loan_id = loanIds[index];

                    $.ajax({
                        url: '/loan_log/' + loan_id,
                        method: 'GET',
                        success: function (res) {
                            console.log(`Loan ${loan_id}: `, res.message);
                            index++;
                            updateProgress();
                            processNextLoan();
                        },
                        error: function (xhr) {
                            console.error(`Loan ${loan_id} failed: `, xhr.responseText);
                            index++;
                            updateProgress();
                            processNextLoan();
                        }
                    });
                }
            });
        });
    </script>

    {{-- DataTables + AJAX for Modals --}}
    <script>
        let outstandingTable = null;
        let weeklyNotPaidTable = null;
        let currentWeekPendingTable = null;
        let penaltyBalanceTable = null;

        function showTotalOutstandingModal() {
            $('#totalOutstandingModal').modal('show');
            $('#outstanding-loading').removeClass('d-none');
            $('#outstanding-content').addClass('d-none');

            if (outstandingTable) {
                outstandingTable.destroy();
                outstandingTable = null;
            }

            $.get('/total-outstanding-data', function(response) {
                $('#outstanding-loading').addClass('d-none');
                $('#outstanding-content').removeClass('d-none');

                if (response.data && response.data.length > 0) {
                    let tbody = '';
                    response.data.forEach(function(item) {
                        tbody += `
                            <tr>
                                <td>${item.loan_id}</td>
                                <td>${item.customer_id}</td>
                                <td>${item.customer_name}</td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.capital_amount).toFixed(2))}</td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.full_loan_amount).toFixed(2))}</td>
                                <td class="text-end"><strong>${new Intl.NumberFormat().format(parseFloat(item.total_outstanding).toFixed(2))}</strong></td>
                            </tr>`;
                    });
                    $('#outstanding_table_modal tbody').html(tbody);

                    outstandingTable = $('#outstanding_table_modal').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            { extend: 'excel', text: 'Export Excel', className: 'btn btn-success btn-sm' },
                            { extend: 'pdf', text: 'Export PDF', className: 'btn btn-danger btn-sm' }
                        ],
                        responsive: true,
                        pageLength: 25,
                        lengthMenu: [[10,25,50,100,-1],[10,25,50,100,"All"]],
                        order: [[5,'desc']],
                        columnDefs: [
                            { className: "text-end", targets: [3,4,5] },
                            { orderable: false, targets: '_all' }
                        ]
                    });
                } else {
                    $('#outstanding_table_modal tbody').html('<tr><td colspan="6" class="text-center">No outstanding loans found</td></tr>');
                }
            }).fail(function() {
                $('#outstanding-loading').addClass('d-none');
                $('#outstanding-content').removeClass('d-none');
                $('#outstanding_table_modal tbody').html('<tr><td colspan="6" class="text-center text-danger">Error loading data</td></tr>');
            });
        }

        $('#totalOutstandingModal').on('hidden.bs.modal', function () {
            if (outstandingTable) {
                outstandingTable.destroy();
                outstandingTable = null;
            }
        });

        function showWeeklyNotPaidModal() {
            $('#weeklyNotPaidModal').modal('show');
            $('#weekly-loading').removeClass('d-none');
            $('#weekly-content').addClass('d-none');

            if (weeklyNotPaidTable) {
                weeklyNotPaidTable.destroy();
                weeklyNotPaidTable = null;
            }

            $.get('/weekly-not-paid-data', function(response) {
                $('#weekly-loading').addClass('d-none');
                $('#weekly-content').removeClass('d-none');

                if (response.data && response.data.length > 0) {
                    let tbody = '';
                    response.data.forEach(function(item) {
                        tbody += `
                            <tr>
                                <td>${item.loan_id}</td>
                                <td>${item.customer_id}</td>
                                <td>${item.customer_name}</td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.capital_amount).toFixed(2))}</td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.full_loan_amount).toFixed(2))}</td>
                                <td class="text-end"><strong>${new Intl.NumberFormat().format(parseFloat(item.this_week_not_paid).toFixed(2))}</strong></td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.total_arrears).toFixed(2))}</td>
                                <td class="text-center">${item.not_paid_installment_count}</td>
                            </tr>`;
                    });
                    $('#weekly_not_paid_table_modal tbody').html(tbody);

                    weeklyNotPaidTable = $('#weekly_not_paid_table_modal').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            { extend: 'excel', text: 'Export Excel', className: 'btn btn-success btn-sm' },
                            { extend: 'pdf', text: 'Export PDF', className: 'btn btn-danger btn-sm' }
                        ],
                        responsive: true,
                        pageLength: 25,
                        lengthMenu: [[10,25,50,100,-1],[10,25,50,100,"All"]],
                        order: [[5,'desc']],
                        columnDefs: [
                            { className: "text-end", targets: [3,4,5,6] },
                            { className: "text-center", targets: [7] },
                            { orderable: false, targets: '_all' }
                        ]
                    });
                } else {
                    $('#weekly_not_paid_table_modal tbody').html('<tr><td colspan="8" class="text-center">No unpaid loans found for this week</td></tr>');
                }
            }).fail(function() {
                $('#weekly-loading').addClass('d-none');
                $('#weekly-content').removeClass('d-none');
                $('#weekly_not_paid_table_modal tbody').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
            });
        }

        $('#weeklyNotPaidModal').on('hidden.bs.modal', function () {
            if (weeklyNotPaidTable) {
                weeklyNotPaidTable.destroy();
                weeklyNotPaidTable = null;
            }
        });

        function showCurrentWeekPendingModal() {
            $('#currentWeekPendingModal').modal('show');
            $('#current-week-loading').removeClass('d-none');
            $('#current-week-content').addClass('d-none');

            if (currentWeekPendingTable) {
                currentWeekPendingTable.destroy();
                currentWeekPendingTable = null;
            }

            $.get('/current-week-pending-data', function(response) {
                $('#current-week-loading').addClass('d-none');
                $('#current-week-content').removeClass('d-none');

                if (response.data && response.data.length > 0) {
                    let tbody = '';
                    response.data.forEach(function(item) {
                        tbody += `
                            <tr>
                                <td>${item.loan_id}</td>
                                <td>${item.customer_id}</td>
                                <td>${item.customer_name}</td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.capital_amount).toFixed(2))}</td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.full_loan_amount).toFixed(2))}</td>
                                <td class="text-end"><strong>${new Intl.NumberFormat().format(parseFloat(item.current_week_pending).toFixed(2))}</strong></td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.total_arrears).toFixed(2))}</td>
                                <td class="text-center">${item.not_paid_installment_count}</td>
                            </tr>`;
                    });
                    $('#current_week_pending_table_modal tbody').html(tbody);

                    currentWeekPendingTable = $('#current_week_pending_table_modal').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            { extend: 'excel', text: 'Export Excel', className: 'btn btn-success btn-sm' },
                            { extend: 'pdf', text: 'Export PDF', className: 'btn btn-danger btn-sm' }
                        ],
                        responsive: true,
                        pageLength: 25,
                        lengthMenu: [[10,25,50,100,-1],[10,25,50,100,"All"]],
                        order: [[5,'desc']],
                        columnDefs: [
                            { className: "text-end", targets: [3,4,5,6] },
                            { className: "text-center", targets: [7] },
                            { orderable: false, targets: '_all' }
                        ]
                    });
                } else {
                    $('#current_week_pending_table_modal tbody').html('<tr><td colspan="8" class="text-center">No pending payments found for this week</td></tr>');
                }
            }).fail(function() {
                $('#current-week-loading').addClass('d-none');
                $('#current-week-content').removeClass('d-none');
                $('#current_week_pending_table_modal tbody').html('<tr><td colspan="8" class="text-center text-danger">Error loading data</td></tr>');
            });
        }

        function showPenaltyBalanceModal() {
            $('#penaltyBalanceModal').modal('show');
            $('#penalty-loading').removeClass('d-none');
            $('#penalty-content').addClass('d-none');

            if (penaltyBalanceTable) {
                penaltyBalanceTable.destroy();
                penaltyBalanceTable = null;
            }

            $.get('/penalty-balance-data', function(response) {
                $('#penalty-loading').addClass('d-none');
                $('#penalty-content').removeClass('d-none');

                if (response.data && response.data.length > 0) {
                    let tbody = '';
                    response.data.forEach(function(item) {
                        tbody += `
                            <tr>
                                <td>${item.loan_id}</td>
                                <td>${item.customer_id}</td>
                                <td>${item.customer_name}</td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.capital_amount).toFixed(2))}</td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.full_loan_amount).toFixed(2))}</td>
                                <td class="text-end">${new Intl.NumberFormat().format(parseFloat(item.total_outstanding).toFixed(2))}</td>
                                <td class="text-end"><strong>${new Intl.NumberFormat().format(parseFloat(item.penalty_balance).toFixed(2))}</strong></td>
                            </tr>`;
                    });
                    $('#penalty_balance_table_modal tbody').html(tbody);

                    penaltyBalanceTable = $('#penalty_balance_table_modal').DataTable({
                        dom: 'Bfrtip',
                        buttons: [
                            { extend: 'excel', text: 'Export Excel', className: 'btn btn-success btn-sm' },
                            { extend: 'pdf', text: 'Export PDF', className: 'btn btn-danger btn-sm' }
                        ],
                        responsive: true,
                        pageLength: 25,
                        lengthMenu: [[10,25,50,100,-1],[10,25,50,100,"All"]],
                        order: [[6,'desc']],
                        columnDefs: [
                            { className: "text-end", targets: [3,4,5,6] },
                            { orderable: false, targets: '_all' }
                        ]
                    });
                } else {
                    $('#penalty_balance_table_modal tbody').html('<tr><td colspan="7" class="text-center">No penalty balances found</td></tr>');
                }
            }).fail(function() {
                $('#penalty-loading').addClass('d-none');
                $('#penalty-content').removeClass('d-none');
                $('#penalty_balance_table_modal tbody').html('<tr><td colspan="7" class="text-center text-danger">Error loading data</td></tr>');
            });
        }

        $('#penaltyBalanceModal').on('hidden.bs.modal', function () {
            if (penaltyBalanceTable) {
                penaltyBalanceTable.destroy();
                penaltyBalanceTable = null;
            }
        });
    </script>
@endsection

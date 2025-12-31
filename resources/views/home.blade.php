@extends('layout.admin')

@section('head')
<!-- CSS Dependencies -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css">

<style>
    /* Glassmorphism / Card Class */
    .glass-panel {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        border-radius: 16px;
        /* Modern rounded corners */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .glass-panel:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.12);
    }

    /* Stats Cards */
    .stat-card {
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stat-icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
        opacity: 0.1;
        transform: rotate(-15deg);
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.2rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Welcome Banner */
    .welcome-banner {
        padding: 2rem;
        background: linear-gradient(135deg, #1f2937 0%, #111827 100%);
        /* Deep Dark for Contrast */
        color: white;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .gradient-card-1 {
        background: linear-gradient(135deg, #6a5e87 0%, #8e7db3 100%);
        color: white;
        border: none;
    }

    .gradient-card-2 {
        background: linear-gradient(135deg, #ea7074 0%, #ff8f94 100%);
        color: white;
        border: none;
    }

    .gradient-card-3 {
        background: linear-gradient(135deg, #ffc184 0%, #ffd4a3 100%);
        color: white;
        border: none;
    }

    .gradient-card-4 {
        background: linear-gradient(135deg, #313a46 0%, #1f262d 100%);
        color: white;
        border: none;
    }

    .gradient-card .stat-label {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .gradient-card .stat-value {
        color: white !important;
    }

    .gradient-card .badge {
        background-color: rgba(255, 255, 255, 0.25) !important;
        color: white !important;
        border: none !important;
    }

    .gradient-card .icon-circle {
        background-color: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }

    .gradient-card .stat-icon-bg {
        color: rgba(255, 255, 255, 0.15) !important;
    }

    .welcome-pattern {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    /* Charts */
    .chart-container {
        padding: 1.5rem;
        height: 100%;
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Shortcut Grid */
    .shortcut-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        text-align: center;
        height: 100%;
        color: var(--dark-color);
        text-decoration: none;
        border: 1px solid rgba(0, 0, 0, 0.05);
        background: rgba(255, 255, 255, 0.6);
        border-radius: 16px;
        transition: all 0.3s ease;
    }

    .shortcut-btn:hover {
        background: white;
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        color: var(--primary-color);
    }

    .shortcut-icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 0.8rem;
        background: rgba(0, 121, 107, 0.1);
        color: var(--primary-color);
        transition: all 0.3s;
    }

    .shortcut-btn:hover .shortcut-icon-circle {
        background: var(--primary-color);
        color: white;
    }

    /* Utility specific colors */
    .text-teal {
        color: #009688;
    }

    .text-blue {
        color: #2196f3;
    }

    .text-orange {
        color: #ff9800;
    }

    .text-red {
        color: #f44336;
    }

    .text-purple {
        color: #9c27b0;
    }

    .bg-gradient-teal {
        background: linear-gradient(135deg, #48a999 0%, #00796b 100%);
    }

    .bg-gradient-blue {
        background: linear-gradient(135deg, #42a5f5 0%, #1565c0 100%);
    }

    .bg-gradient-orange {
        background: linear-gradient(135deg, #ffcc80 0%, #ef6c00 100%);
    }

    .bg-gradient-purple {
        background: linear-gradient(135deg, #ce93d8 0%, #7b1fa2 100%);
    }

    /* Modal Overrides */
    .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        background: var(--light-bg);
        border-radius: 20px 20px 0 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }

    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }

    .table thead th {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background-color: #f8f9fa;
        border-bottom: 2px solid #edf2f7;
        color: #8898aa;
        font-weight: 600;
    }
</style>
@endsection

@section('content')
@hasPrivilege('DASHBOARD')
<div class="container-fluid py-4">

    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-banner shadow-lg animated-card">
                <div class="welcome-pattern"></div>
                <div class="d-flex justify-content-between align-items-center position-relative" style="z-index: 1;">
                    <div>
                        <h2 class="fw-bold mb-1">
                            @if (session('head_branch') == session('branch_id'))
                            Headquarters Overview 🏢
                            @else
                            Dashboard Overview 👋
                            @endif
                        </h2>
                        <p class="mb-0 opacity-75" id="live-datetime-display">Loading date...</p>
                    </div>
                    <div class="d-none d-md-block">
                        <span class="badge bg-white text-primary px-3 py-2 rounded-pill fs-6 shadow-sm">
                            <i class="ri-user-star-line me-1"></i> {{ session('user_data')['name'] ?? 'User' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Primary KPI Cards -->
    <div class="row g-4 mb-4">
        <!-- Pending Loans -->
        <div class="col-xl-3 col-md-6 col-12">
            <a href="/pendingloan" class="text-decoration-none">
                <div class="glass-panel stat-card gradient-card gradient-card-1">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Pending Loans</p>
                            <h3 class="stat-value">
                                <span class="counter" data-target="{{ $customer_loan_pending_Amount ?? 0 }}">0</span>
                            </h3>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge rounded-pill px-2">
                                    {{ $customer_loan_pending_Count ?? 0 }} Appls.
                                </span>
                            </div>
                        </div>
                        <div class="icon-circle rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="ri-loader-4-line fs-4"></i>
                        </div>
                    </div>
                    <i class="ri-loader-4-line stat-icon-bg"></i>
                </div>
            </a>
        </div>

        <!-- Ongoing Loans -->
        <div class="col-xl-3 col-md-6 col-12">
            <a href="/payment_step_1" class="text-decoration-none">
                <div class="glass-panel stat-card gradient-card gradient-card-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Active Portfolio</p>
                            <h3 class="stat-value">
                                <span class="counter" data-target="{{ $customer_loan_current_Amount ?? 0 }}">0</span>
                            </h3>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge rounded-pill px-2">
                                    {{ $customer_loan_current_Count ?? 0 }} Active
                                </span>
                            </div>
                        </div>
                        <div class="icon-circle rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="ri-wallet-3-line fs-4"></i>
                        </div>
                    </div>
                    <i class="ri-wallet-3-line stat-icon-bg"></i>
                </div>
            </a>
        </div>

        <!-- Settled Loans -->
        <div class="col-xl-3 col-md-6 col-12">
            <a href="/showsettleloan" class="text-decoration-none">
                <div class="glass-panel stat-card gradient-card gradient-card-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="stat-label">Settled Loans</p>
                            <h3 class="stat-value">
                                <span class="counter" data-target="{{ $setteled_loan_current_Amount ?? 0 }}">0</span>
                            </h3>
                            <div class="d-flex align-items-center mt-2">
                                <span class="badge rounded-pill px-2">
                                    {{ $setteled_loan_Count ?? 0 }} Closed
                                </span>
                            </div>
                        </div>
                        <div class="icon-circle rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="ri-checkbox-circle-line fs-4"></i>
                        </div>
                    </div>
                    <i class="ri-checkbox-circle-line stat-icon-bg"></i>
                </div>
            </a>
        </div>

        <!-- Current Month Lending -->
        <div class="col-xl-3 col-md-6 col-12">
            <div class="glass-panel stat-card gradient-card gradient-card-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="stat-label">Month Lending</p>
                        <h3 class="stat-value">
                            <span class="counter" data-target="{{ $currentMonthLending ?? 0 }}">0</span>
                        </h3>
                        <div class="d-flex align-items-center mt-2">
                            <span class="badge rounded-pill px-2">
                                This Month
                            </span>
                        </div>
                    </div>
                    <div class="icon-circle rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="ri-calendar-check-line fs-4"></i>
                    </div>
                </div>
                <i class="ri-calendar-check-line stat-icon-bg"></i>
            </div>
        </div>
    </div>

    <!-- Secondary Metrics Slide/Grid -->
    <div class="row g-4 mb-4">
        <!-- Total Outstanding -->
        <div class="col-lg-3 col-md-6 col-12">
            <a href="#" onclick="showTotalOutstandingModal()" class="text-decoration-none">
                <div class="glass-panel p-3 d-flex align-items-center justify-content-between h-100">
                    <div>
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Total Portfolio</p>
                        <h4 class="mb-0 fw-bold text-dark"><span class="counter" data-target="{{ $totalOutstanding ?? 0 }}">0</span></h4>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #e0f2f1; color: #00897b;">
                        <i class="ri-bank-line fs-5"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- Today Collected -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="glass-panel p-3 d-flex align-items-center justify-content-between h-100">
                <div>
                    <p class="text-muted small mb-1 text-uppercase fw-bold">Today Collected</p>
                    <h4 class="mb-0 fw-bold text-success"><span class="counter" data-target="{{ $todaycollected ?? 0 }}">0</span></h4>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #e8f5e9; color: #2e7d32;">
                    <i class="ri-hand-coin-line fs-5"></i>
                </div>
            </div>
        </div>

        <!-- Today Due -->
        <div class="col-lg-3 col-md-6 col-12">
            <div class="glass-panel p-3 d-flex align-items-center justify-content-between h-100">
                <div>
                    <p class="text-muted small mb-1 text-uppercase fw-bold">Today Due</p>
                    <h4 class="mb-0 fw-bold text-primary"><span class="counter" data-target="{{ $todayinstallment ?? 0 }}">0</span></h4>
                    <small class="text-danger" style="font-size: 0.75rem;">Bal: {{ number_format($todayinstallment_balance ?? 0, 2) }}</small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #e3f2fd; color: #1565c0;">
                    <i class="ri-calendar-event-line fs-5"></i>
                </div>
            </div>
        </div>

        <!-- Penalty Balance -->
        <div class="col-lg-3 col-md-6 col-12">
            <a href="#" onclick="showPenaltyBalanceModal()" class="text-decoration-none">
                <div class="glass-panel p-3 d-flex align-items-center justify-content-between h-100">
                    <div>
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Penalty Balance</p>
                        <h4 class="mb-0 fw-bold text-danger"><span class="counter" data-target="{{ $penaltyBalance ?? 0 }}">0</span></h4>
                    </div>
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #ffebee; color: #c62828;">
                        <i class="ri-alarm-warning-line fs-5"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <!-- Monthly Collections -->
        <div class="col-lg-8 col-12">
            <div class="glass-panel chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="chart-title"><i class="ri-line-chart-line text-primary"></i> Monthly Collections</h5>
                </div>
                <div id="monthly-revenue-chart" style="min-height: 320px;"></div>
            </div>
        </div>

        <!-- Weekly Comparison -->
        <div class="col-lg-4 col-12">
            <div class="glass-panel chart-container">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="chart-title"><i class="ri-bar-chart-groupped-line text-success"></i> Weekly Performance</h5>
                </div>
                <div id="bar-comparison-chart" style="min-height: 320px;"></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Loan Completion Radial -->
        <div class="col-lg-4 col-12">
            <div class="glass-panel chart-container">
                <h5 class="chart-title"><i class="ri-pie-chart-line text-warning"></i> Portfolio Health</h5>
                <div id="loan-type-chart" style="min-height: 300px;"></div>
            </div>
        </div>

        <!-- Arrears Warning Cards -->
        <div class="col-lg-8 col-12">
            <div class="row h-100 g-4">
                <div class="col-md-6 col-12">
                    <a href="#" onclick="showWeeklyNotPaidModal()" class="text-decoration-none">
                        <div class="glass-panel p-4 h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #fff3e0, #ffffff);">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ri-calendar-close-line fs-3 text-warning me-2"></i>
                                <h6 class="fw-bold text-dark mb-0">This Week Arrears</h6>
                            </div>
                            <div class="row text-center mt-3">
                                <div class="col-4 border-end">
                                    <h4 class="fw-bold mb-0">{{ $weeklyUnpaidCount ?? 0 }}</h4>
                                    <small class="text-muted">Loans</small>
                                </div>
                                <div class="col-8">
                                    <h4 class="fw-bold mb-0 text-danger">{{ number_format($weeklyUnpaidAmount ?? 0, 2) }}</h4>
                                    <small class="text-muted">Total Amount</small>
                                </div>
                            </div>
                            <div class="position-absolute bottom-0 end-0 opacity-10 p-2">
                                <i class="ri-error-warning-fill" style="font-size: 6rem; color: #ff9800;"></i>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-md-6 col-12">
                    <a href="#" onclick="showCurrentWeekPendingModal()" class="text-decoration-none">
                        <div class="glass-panel p-4 h-100 position-relative overflow-hidden" style="background: linear-gradient(135deg, #e3f2fd, #ffffff);">
                            <div class="d-flex align-items-center mb-3">
                                <i class="ri-time-line fs-3 text-primary me-2"></i>
                                <h6 class="fw-bold text-dark mb-0">Week Pending</h6>
                            </div>
                            <div class="row text-center mt-3">
                                <div class="col-4 border-end">
                                    <h4 class="fw-bold mb-0">{{ $currentWeekPendingCount ?? 0 }}</h4>
                                    <small class="text-muted">Due</small>
                                </div>
                                <div class="col-8">
                                    <h4 class="fw-bold mb-0 text-primary">{{ number_format($currentWeekPendingAmount ?? 0, 2) }}</h4>
                                    <small class="text-muted">Total Due</small>
                                </div>
                            </div>
                            <div class="position-absolute bottom-0 end-0 opacity-10 p-2">
                                <i class="ri-time-fill" style="font-size: 6rem; color: #2196f3;"></i>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts -->
    @if ($shortcut_count > 0)
    <div class="mb-5">
        <h5 class="fw-bold mb-3 text-dark"><i class="ri-apps-line me-2"></i>Quick Access</h5>
        <div class="row row-cols-2 row-cols-md-4 row-cols-lg-6 g-3">
            @php
            $icons = [
            'Add_Customer' => 'ri-user-add-line',
            'View_Customer' => 'ri-team-line',
            'Assign_Customers_to_group' => 'ri-user-follow-line',
            'View_Products' => 'ri-shopping-bag-3-line',
            'Pending_Loans' => 'ri-loader-2-line',
            'Current_Loans' => 'ri-hand-coin-line',
            'Loan_In_arrears' => 'ri-alarm-warning-line',
            'Add_Repayment' => 'ri-money-dollar-circle-line',
            'Repayment_details' => 'ri-file-list-3-line',
            'Collector_wise_collections' => 'ri-user-location-line',
            'Loan_Calculator' => 'ri-calculator-line',
            'Add_Expenses' => 'ri-file-reduce-line',
            'Add_Income' => 'ri-file-add-line',
            ];
            $links = [
            'Add_Customer' => '/customers',
            'View_Customer' => '/showcustomers',
            'Assign_Customers_to_group' => '/customergroupassign',
            'View_Products' => '/viewproduct',
            'Pending_Loans' => '/pendingloan',
            'Current_Loans' => '/payment_step_1',
            'Loan_In_arrears' => '/latePayment',
            'Add_Repayment' => '/payment',
            'Repayment_details' => '/viewpayment',
            'Collector_wise_collections' => '/collection',
            'Loan_Calculator' => '/calculator',
            'Add_Expenses' => '/expenses',
            'Add_Income' => '/income',
            ];
            $labels = [
            'Add_Customer' => 'Add Customer',
            'View_Customer' => 'Customers',
            'Assign_Customers_to_group' => 'Group Assign',
            'View_Products' => 'Products',
            'Pending_Loans' => 'Pending',
            'Current_Loans' => 'Active Loans',
            'Loan_In_arrears' => 'Arrears',
            'Add_Repayment' => 'Repayment',
            'Repayment_details' => 'History',
            'Collector_wise_collections' => 'Collections',
            'Loan_Calculator' => 'Calculator',
            'Add_Expenses' => 'Expenses',
            'Add_Income' => 'Income',
            ];
            @endphp

            @foreach ($shortcut as $item)
            @if(array_key_exists($item->name, $links))
            <div class="col">
                <a href="{{ $links[$item->name] }}" class="shortcut-btn glass-panel">
                    <div class="shortcut-icon-circle">
                        <i class="{{ $icons[$item->name] ?? 'ri-star-line' }}"></i>
                    </div>
                    <span class="small fw-semibold">{{ $labels[$item->name] ?? $item->name }}</span>
                </a>
            </div>
            @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Hidden button for loan process --}}
    <button id="startLoanProcess" hidden>Start Processing Loans</button>

</div>
@endhasPrivilege

<!-- Modals Section -->
<!-- Total Outstanding Modal -->
<div class="modal fade" id="totalOutstandingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="ri-money-dollar-circle-line me-2 text-primary"></i>Total Outstanding</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div id="outstanding-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2 text-muted">Loading data...</p>
                </div>
                <div id="outstanding-content" class="table-responsive bg-white p-3 shadow-sm rounded">
                    <table class="table table-hover w-100" id="outstanding_table_modal">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Customer ID</th>
                                <th>Name</th>
                                <th class="text-end">Capital</th>
                                <th class="text-end">Full Amount</th>
                                <th class="text-end">Outstanding</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Not Paid Modal -->
<div class="modal fade" id="weeklyNotPaidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="ri-calendar-close-line me-2 text-warning"></i>This Week Arrears</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div id="weekly-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-warning" role="status"></div>
                </div>
                <div id="weekly-content" class="table-responsive bg-white p-3 shadow-sm rounded">
                    <table class="table table-hover w-100" id="weekly_not_paid_table_modal">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Center</th>
                                <th>Cus ID</th>
                                <th>Name</th>
                                <th class="text-end">Capital</th>
                                <th class="text-end">Full Amount</th>
                                <th class="text-end text-danger">Unpaid</th>
                                <th class="text-end">Total Arrears</th>
                                <th class="text-center">Count</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Week Pending Modal -->
<div class="modal fade" id="currentWeekPendingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="ri-time-line me-2 text-primary"></i>Current Week Pending</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div id="current-week-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div id="current-week-content" class="table-responsive bg-white p-3 shadow-sm rounded">
                    <table class="table table-hover w-100" id="current_week_pending_table_modal">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Center</th>
                                <th>Cus ID</th>
                                <th>Name</th>
                                <th class="text-end">Capital</th>
                                <th class="text-end">Full Amount</th>
                                <th class="text-end text-primary">Pending</th>
                                <th class="text-end">Total Arrears</th>
                                <th class="text-center">Count</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Total Outstanding Modal -->
<div class="modal fade" id="totalOutstandingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="ri-money-dollar-circle-line me-2 text-primary"></i>Total Outstanding Portfolio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="text-center py-5 d-none spinner">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <div class="table-responsive bg-white p-3 shadow-sm rounded content">
                    <table class="table table-hover w-100" id="total_outstanding_table">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Cus ID</th>
                                <th>Name</th>
                                <th class="text-end">Capital</th>
                                <th class="text-end">Full Amount</th>
                                <th class="text-end text-primary">Total Outstanding</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Weekly Arrears Modal -->
<div class="modal fade" id="weeklyArrearsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="ri-calendar-close-line me-2 text-warning"></i>Weekly Arrears</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="text-center py-5 d-none spinner">
                    <div class="spinner-border text-warning" role="status"></div>
                </div>
                <div class="table-responsive bg-white p-3 shadow-sm rounded content">
                    <table class="table table-hover w-100" id="weekly_arrears_table">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Center</th>
                                <th>Cus ID</th>
                                <th>Name</th>
                                <th class="text-end">Capital</th>
                                <th class="text-end">Full Amount</th>
                                <th class="text-end text-warning">This Week Not Paid</th>
                                <th class="text-end text-danger">Total Arrears</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Current Week Pending Modal -->
<div class="modal fade" id="weekPendingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="ri-time-line me-2 text-info"></i>Current Week Pending</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="text-center py-5 d-none spinner">
                    <div class="spinner-border text-info" role="status"></div>
                </div>
                <div class="table-responsive bg-white p-3 shadow-sm rounded content">
                    <table class="table table-hover w-100" id="week_pending_table">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Center</th>
                                <th>Cus ID</th>
                                <th>Name</th>
                                <th class="text-end">Capital</th>
                                <th class="text-end">Full Amount</th>
                                <th class="text-end text-info">Week Pending</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Penalty Balance Modal -->
<div class="modal fade" id="penaltyBalanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="ri-error-warning-line me-2 text-danger"></i>Penalty Balance</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light">
                <div id="penalty-loading" class="text-center py-5 d-none">
                    <div class="spinner-border text-danger" role="status"></div>
                </div>
                <div id="penalty-content" class="table-responsive bg-white p-3 shadow-sm rounded">
                    <table class="table table-hover w-100" id="penalty_balance_table_modal">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Cus ID</th>
                                <th>Name</th>
                                <th class="text-end">Capital</th>
                                <th class="text-end">Full Amount</th>
                                <th class="text-end">Outstanding</th>
                                <th class="text-end text-danger">Penalty</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

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
    // Theme Colors
    const colors = {
        primary: '#313a46', // Sidebar
        secondary: '#6a5e87', // Purple
        success: '#ea7074', // Salmon
        warning: '#f9a63a', // Orange
        danger: '#f13a3b', // Red
        info: '#ffc184' // Peach
    };

    // Live Clock
    function updateClock() {
        const now = new Date();
        const options = {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        };
        if (document.getElementById('live-datetime-display')) {
            document.getElementById('live-datetime-display').innerText = now.toLocaleDateString('en-US', options);
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    document.addEventListener("DOMContentLoaded", function() {
        // CountUp Animations
        document.querySelectorAll('.counter').forEach(function(el) {
            const target = parseFloat(el.getAttribute('data-target'));
            if (!isNaN(target)) {
                new countUp.CountUp(el, target, {
                    separator: ',',
                    decimalPlaces: target % 1 !== 0 ? 2 : 0,
                    duration: 2
                }).start();
            }
        });

        // --- APEX CHARTS CONFIG ---

        // 1. Monthly Revenue (Area)
        const monthlyOptions = {
            chart: {
                type: 'area',
                height: 320,
                toolbar: {
                    show: false
                },
                fontFamily: 'Inter, sans-serif'
            },
            series: [{
                name: 'Revenue',
                data: @json($monthlyData ?? [])
            }],
            colors: [colors.primary],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            grid: {
                borderColor: '#f1f3fa',
            },
            tooltip: {
                theme: 'light'
            }
        };
        new ApexCharts(document.querySelector("#monthly-revenue-chart"), monthlyOptions).render();

        // 2. Weekly Comparison (Bar)
        const weeklyOptions = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                    show: false
                },
                fontFamily: 'Inter, sans-serif'
            },
            series: [{
                name: 'This Week',
                data: @json($weeklyComparison['current'] ?? [])
            }, {
                name: 'Last Week',
                data: @json($weeklyComparison['last'] ?? [])
            }],
            colors: [colors.success, '#e0e0e0'],
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    columnWidth: '60%',
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            xaxis: {
                categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            grid: {
                borderColor: '#f1f3fa',
            },
            tooltip: {
                theme: 'light'
            }
        };
        new ApexCharts(document.querySelector("#bar-comparison-chart"), weeklyOptions).render();

        // 3. Loan Status (Donut/Pie)
        const loanStatusOptions = {
            chart: {
                type: 'donut',
                height: 300,
                fontFamily: 'Inter, sans-serif'
            },
            series: [
                @json($customer_loan_current_Count ?? 0),
                @json($customer_loan_pending_Count ?? 0),
                @json($setteled_loan_Count ?? 0)
            ],
            labels: ['Active', 'Pending', 'Settled'],
            colors: [colors.secondary, colors.primary, colors.warning],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                fontSize: '14px'
                            },
                            value: {
                                fontSize: '20px',
                                fontWeight: 600
                            },
                            total: {
                                show: true,
                                label: 'Total',
                                formatter: function(w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            legend: {
                position: 'bottom'
            },
            tooltip: {
                theme: 'light'
            }
        };
        new ApexCharts(document.querySelector("#loan-type-chart"), loanStatusOptions).render();

    });

    // Generic Table Loader
    function loadTableData(modalId, url, tableId, columns) {
        const modal = $(modalId);
        const spinner = modal.find('.spinner-border').parent();
        const content = modal.find('.table-responsive');

        spinner.removeClass('d-none');
        content.addClass('d-none');

        // Destroy existing DT if exists
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().destroy();
        }

        $.get(url, function(response) {
            spinner.addClass('d-none');
            content.removeClass('d-none');

            const data = response.data || [];

            $(tableId).DataTable({
                data: data,
                columns: columns.map(c => ({
                    ...c,
                    className: (c.className || '') + (['capital_amount', 'full_loan_amount', 'total_outstanding', 'this_week_not_paid', 'total_arrears', 'current_week_pending', 'penalty_balance'].includes(c.data) ? ' text-end' : '')
                })),
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'excel',
                        className: 'btn btn-success btn-sm',
                        text: '<i class="ri-file-excel-2-line"></i> Excel'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger btn-sm',
                        text: '<i class="ri-file-pdf-line"></i> PDF'
                    }
                ],
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50],
                language: {
                    emptyTable: "No records found"
                }
            });
        }).fail(function() {
            spinner.addClass('d-none');
            content.removeClass('d-none');
            $(tableId).find('tbody').html('<tr><td colspan="' + columns.length + '" class="text-center text-danger">Failed to load data.</td></tr>');
        });
    }

    // Modal Close Fix (Force Close on Click)
    $(document).ready(function() {
        $(document).on('click', '[data-bs-dismiss="modal"]', function() {
            const modal = $(this).closest('.modal');
            modal.modal('hide');
        });
    });

    // Loan Processing Logic (Hidden) - Keeping generic structure
    $('#startLoanProcess').on('click', function() {
        startLoanProcessingRPC();
    });

    function startLoanProcessingRPC() {
        // Replicating original logic
        $.get('/get-loan-ids', function(data) {
            const loanIds = data.loan_ids;
            const total = loanIds.length;
            let index = 0;
            Swal.fire({
                title: 'Processing Loans...',
                html: `<div style="font-size:14px;">Please wait while we process ${total} loans.</div><div style="margin-top:10px;"><span id="swal-count">0/${total}</span></div><div id="swal-progress" style="margin-top:10px; background:#ddd; height:5px; width:100%;"><div id="swal-bar" style="height:5px; width:0%; background:green;"></div></div>`,
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    processNextLoan();
                }
            });

            function processNextLoan() {
                if (index >= total) {
                    Swal.fire('Success', 'All loans processed', 'success');
                    return;
                }
                const pct = Math.round((index / total) * 100);
                $('#swal-bar').css('width', pct + '%');
                $.ajax({
                    url: '/loan_log/' + loanIds[index],
                    method: 'GET',
                    success: () => {
                        index++;
                        $('#swal-count').text(`${index}/${total}`);
                        processNextLoan();
                    },
                    error: () => {
                        index++;
                        $('#swal-count').text(`${index}/${total}`);
                        processNextLoan();
                    }
                });
            }
        });
    }
</script>
@endsection
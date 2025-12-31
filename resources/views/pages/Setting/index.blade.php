@extends('layout.admin')

@section('head')
<!-- Bootstrap 5 CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
<!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<meta name="session-branch" content="{{ (int) session('branch_id') }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4895ef;
        --success-color: #4cc9f0;
        --bg-light: #f8f9fa;
    }

    body {
        background-color: var(--bg-light);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    /* Glassmorphism & Cards */
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .glass-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
    }

    /* Sidebar Tabs */
    .settings-sidebar .nav-link {
        color: #4b5563;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 4px;
        transition: all 0.2s;
        display: flex;
        align-items: center;
    }

    .settings-sidebar .nav-link i {
        width: 24px;
        margin-right: 12px;
        font-size: 1.1em;
        color: #9ca3af;
    }

    .settings-sidebar .nav-link:hover {
        background-color: #eff6ff;
        color: var(--primary-color);
    }

    .settings-sidebar .nav-link:hover i {
        color: var(--primary-color);
    }

    .settings-sidebar .nav-link.active {
        background-color: var(--primary-color);
        color: white;
        box-shadow: 0 4px 6px -1px rgba(67, 97, 238, 0.3);
    }

    .settings-sidebar .nav-link.active i {
        color: white;
    }

    /* Section Headers */
    .section-header {
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #111827;
    }

    .section-desc {
        color: #6b7280;
        font-size: 0.875rem;
    }

    /* Shortcut Cards */
    .shortcut-card {
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px;
        height: 100%;
        transition: all 0.2s;
        cursor: pointer;
        position: relative;
        background: white;
    }

    .shortcut-card:hover {
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .shortcut-card.active {
        border-color: var(--primary-color);
        background-color: #eff6ff;
    }

    .shortcut-card .form-check-input {
        position: absolute;
        top: 16px;
        right: 16px;
        cursor: pointer;
    }

    .shortcut-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background-color: #eff6ff;
        color: var(--primary-color);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        font-size: 1.2rem;
    }

    /* Custom Switch */
    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.25em;
        cursor: pointer;
    }

    /* Badges */
    .badge-soft {
        background-color: #dbeafe;
        color: var(--primary-color);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-soft-danger {
        background-color: #fee2e2;
        color: #ef4444;
    }

    /* Form Controls */
    .form-control,
    .form-select {
        border-radius: 8px;
        border-color: #d1d5db;
        padding: 0.625rem 0.875rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .settings-container {
            flex-direction: column;
        }

        .settings-sidebar {
            width: 100%;
            margin-bottom: 20px;
            overflow-x: auto;
        }

        .settings-sidebar .nav {
            flex-direction: row;
            flex-wrap: nowrap;
        }

        .settings-sidebar .nav-link {
            white-space: nowrap;
            margin-right: 8px;
            margin-bottom: 0;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800">Settings</h1>
            <p class="text-muted">Manage your application preferences and configurations</p>
        </div>
        <!-- Additional header actions if needed -->
    </div>

    <div class="row settings-container">
        <!-- Sidebar Navigation -->
        <div class="col-md-3 col-lg-2">
            <nav class="settings-sidebar card glass-card p-2 sticky-top" style="top: 20px; z-index: 100;">
                <div class="nav nav-pills flex-column" id="settings-tab" role="tablist">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-shortcuts">
                        <i class="fa-solid fa-bolt"></i> Shortcuts
                    </button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-loan-restrict">
                        <i class="fa-solid fa-shield-halved"></i> Restrictions
                    </button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-ho-approval">
                        <i class="fa-solid fa-building-columns"></i> HO Approval
                    </button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-general">
                        <i class="fa-solid fa-gear"></i> General
                    </button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-recovery">
                        <i class="fa-solid fa-hand-holding-medical"></i> Recovery
                    </button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-agreement">
                        <i class="fa-solid fa-file-signature"></i> Agreements
                    </button>
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-commission">
                        <i class="fa-solid fa-percent"></i> Commission
                    </button>
                </div>
            </nav>
        </div>

        <!-- Content Area -->
        <div class="col-md-9 col-lg-10">
            <div class="tab-content" id="settings-tabContent">

                <!-- 1. Shortcuts Tab -->
                <div class="tab-pane fade show active" id="tab-shortcuts" role="tabpanel">
                    <div class="glass-card p-4">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="section-title">Quick Access Shortcuts</h2>
                                <p class="section-desc">Enable shortcuts to appear on your dashboard for quick access.</p>
                            </div>
                            <div>
                                <input type="text" id="shortcutSearch" class="form-control form-control-sm" placeholder="Search shortcuts...">
                            </div>
                        </div>

                        <div id="shortcutsContainer">
                            <!-- Group 1: Customer Management -->
                            <h6 class="fw-bold text-uppercase text-muted mb-3 fs-7 ls-1">Customer Management</h6>
                            <div class="row g-3 mb-4 shortcut-group">
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Add_Customer')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-user-plus"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Add_Customer" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Add Customer</h6>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('View_Customer')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-users-viewfinder"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="View_Customer" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">View Customer</h6>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Assign_Customers_to_group')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-users-gear"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Assign_Customers_to_group" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Assign Group</h6>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 2: Loan Management -->
                            <h6 class="fw-bold text-uppercase text-muted mb-3 fs-7 ls-1">Loan Management</h6>
                            <div class="row g-3 mb-4 shortcut-group">
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Pending_Loans')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Pending_Loans" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Pending Loans</h6>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Current_Loans')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-money-check-dollar"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Current_Loans" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Current Loans</h6>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Loan_In_arrears')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-circle-exclamation text-danger"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Loan_In_arrears" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Loans In Arrears</h6>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('View_Products')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-box-open"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="View_Products" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">View Products</h6>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Loan_Calculator')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-calculator"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Loan_Calculator" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Loan Calculator</h6>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 3: Payments & Collections -->
                            <h6 class="fw-bold text-uppercase text-muted mb-3 fs-7 ls-1">Payments & Collections</h6>
                            <div class="row g-3 mb-4 shortcut-group">
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Add_Repayment')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Add_Repayment" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Add Repayment</h6>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Repayment_details')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Repayment_details" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Repayment Details</h6>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Collector_wise_collections')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-briefcase"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Collector_wise_collections" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Collector Wise</h6>
                                    </div>
                                </div>
                            </div>

                            <!-- Group 4: Finance -->
                            <h6 class="fw-bold text-uppercase text-muted mb-3 fs-7 ls-1">Finance</h6>
                            <div class="row g-3 mb-4 shortcut-group">
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Add_Expenses')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-wallet text-danger"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Add_Expenses" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Add Expenses</h6>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <div class="shortcut-card" onclick="toggleShortcut('Add_Income')">
                                        <div class="d-flex justify-content-between">
                                            <div class="shortcut-icon"><i class="fa-solid fa-wallet text-success"></i></div>
                                            <input class="form-check-input access_module" type="checkbox" id="Add_Income" onclick="event.stopPropagation()">
                                        </div>
                                        <h6 class="fw-bold mb-1">Add Income</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <button class="btn btn-primary" onclick="saveShortcut(event)">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Shortcuts
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 2. Loan Restrictions -->
                <div class="tab-pane fade" id="tab-loan-restrict" role="tabpanel">
                    <div class="glass-card p-4">
                        <div class="section-header">
                            <h2 class="section-title">Loan Restrictions</h2>
                            <p class="section-desc">Configure validation rules and restrictions for loan creation.</p>
                        </div>

                        <div class="row g-4">
                            <!-- Document Upload -->
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-2">Document Upload</h6>
                                        <p class="text-muted small mb-3">Is document upload mandatory for loan creation?</p>
                                        <select id="document_upload_restriction" class="form-select">
                                            <option value="" disabled selected>-- Select --</option>
                                            <option value="required">Required</option>
                                            <option value="not_required">Not Required</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Guarantees -->
                            <div class="col-md-6">
                                <div class="card h-100 shadow-sm border-0 bg-light">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-2">Guarantees</h6>
                                        <p class="text-muted small mb-3">Are guarantors mandatory for loan creation?</p>
                                        <select id="guarantees_restriction" class="form-select">
                                            <option value="" disabled selected>-- Select --</option>
                                            <option value="required">Required</option>
                                            <option value="not_required">Not Required</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Details Editing -->
                            <div class="col-md-12">
                                <div class="card shadow-sm border-0 bg-light">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="fw-bold mb-1">Edit Product Details</h6>
                                                <p class="text-muted small mb-0">Allow modifying product defaults during loan creation.</p>
                                            </div>
                                            <div style="width: 200px;">
                                                <select id="change_product_details" class="form-select">
                                                    <option value="editable">Editable</option>
                                                    <option value="not_editable">Locked</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- First Installment Date -->
                            <div class="col-md-12">
                                <h6 class="fw-bold mt-3 mb-3">Installment Date Constraints</h6>
                                <div class="card shadow-sm border-0 p-3">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Daily Loans (Max Days)</label>
                                            <input type="number" id="first_installment_daily" class="form-control" placeholder="Days">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Weekly Loans (Max Days)</label>
                                            <input type="number" id="first_installment_weekly" class="form-control" placeholder="Days">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Monthly Loans (Max Days)</label>
                                            <input type="number" id="first_installment_monthly" class="form-control" placeholder="Days">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <button id="btnUpdateLoanRestrictions" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Update Restrictions
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 3. HO Approval -->
                <div class="tab-pane fade" id="tab-ho-approval" role="tabpanel">
                    <div class="glass-card p-4">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="section-title">Head Office Approval</h2>
                                <p class="section-desc">Toggle which actions require Head Office verification.</p>
                            </div>
                            <button id="btnSaveAllApprovals" class="btn btn-primary btn-sm">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save All Changes
                            </button>
                        </div>

                        <div class="accordion" id="accordionHO">
                            <!-- User Management -->
                            <div class="accordion-item mb-3 border bg-white rounded">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        <i class="fa-solid fa-users-gear me-2"></i> User Management
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionHO">
                                    <div class="accordion-body p-0">
                                        <div class="list-group list-group-flush rounded-bottom">
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>User Creation</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="101" id="approval_101">
                                                </div>
                                            </label>
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>User Details Update</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="102" id="approval_102">
                                                </div>
                                            </label>
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>User Privilege Change</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="103" id="approval_103">
                                                </div>
                                            </label>
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>User Designation Change</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="104" id="approval_104">
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Management -->
                            <div class="accordion-item mb-3 border bg-white rounded">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                        <i class="fa-solid fa-user-tag me-2"></i> Customer Management
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionHO">
                                    <div class="accordion-body p-0">
                                        <div class="list-group list-group-flush rounded-bottom">
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>Customer Creation</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="301" id="approval_301">
                                                </div>
                                            </label>
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>Customer Update</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="302" id="approval_302">
                                                </div>
                                            </label>
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>Status Change</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="303" id="approval_303">
                                                </div>
                                            </label>
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>Blacklist</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="304" id="approval_304">
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial -->
                            <div class="accordion-item mb-3 border bg-white rounded">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                        <i class="fa-solid fa-coins me-2"></i> Financial & Loans
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionHO">
                                    <div class="accordion-body p-0">
                                        <div class="list-group list-group-flush rounded-bottom">
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>Loan Approval</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="401" id="approval_401">
                                                </div>
                                            </label>
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>Payment Undo</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="502" id="approval_502">
                                                </div>
                                            </label>
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>Payment Reversal</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="503" id="approval_503">
                                                </div>
                                            </label>
                                            <label class="list-group-item d-flex justify-content-between align-items-center cursor-pointer">
                                                <span>Expense Creation</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input approval-toggle" type="checkbox" data-type="601" id="approval_601">
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. General Settings -->
                <div class="tab-pane fade" id="tab-general" role="tabpanel">
                    <div class="glass-card p-4">
                        <div class="section-header">
                            <h2 class="section-title">General Settings</h2>
                            <p class="section-desc">Global application configuration.</p>
                        </div>

                        <div class="row g-4">
                            <!-- Loan Disbursement Policy -->
                            <div class="col-lg-6">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h6 class="fw-bold">Loan Disbursement Policy</h6>
                                            <div class="badge-soft">Critical</div>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="loan_policy" id="strict_mode" value="strict">
                                            <label class="form-check-label" for="strict_mode">
                                                <strong>Strict Mode</strong><br><span class="text-muted small">Deny if insufficient balance</span>
                                            </label>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="radio" name="loan_policy" id="flexible_mode" value="flexible">
                                            <label class="form-check-label" for="flexible_mode">
                                                <strong>Flexible Mode</strong><br><span class="text-muted small">Allow overdrafts (negative balance)</span>
                                            </label>
                                        </div>
                                        <button id="btnUpdateLoanPolicy" class="btn btn-outline-primary btn-sm w-100">Update Policy</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Options -->
                            <div class="col-lg-6">
                                <div class="card h-100 shadow-sm border-0">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">Payment Options</h6>

                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Member Name Format</label>
                                            <div class="input-group input-group-sm">
                                                <select id="payment_member_name" class="form-select">
                                                    <option value="full_name">Full Name</option>
                                                    <option value="with_initial">With Initial</option>
                                                </select>
                                                <button id="btnUpdatePaymentMemberName" class="btn btn-outline-secondary">Save</button>
                                            </div>
                                        </div>

                                        <div class="mb-1">
                                            <label class="form-label small fw-bold">Backdated Payments</label>
                                            <div class="input-group input-group-sm">
                                                <select id="payment_backdate" class="form-select">
                                                    <option value="disabled">Disabled</option>
                                                    <option value="enabled">Enabled</option>
                                                </select>
                                                <button id="btnUpdatePaymentBackdate" class="btn btn-outline-secondary">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Collection Days -->
                            <div class="col-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="fw-bold m-0">Allowed Collection Days</h6>
                                            <button id="btnUpdateCollectionDays" class="btn btn-sm btn-outline-primary">Save Days</button>
                                        </div>

                                        <div class="d-flex flex-wrap gap-2">
                                            <div class="btn-group" role="group">
                                                <input type="checkbox" class="btn-check collection-day" id="col_day_1" value="1">
                                                <label class="btn btn-outline-secondary" for="col_day_1">Mon</label>

                                                <input type="checkbox" class="btn-check collection-day" id="col_day_2" value="2">
                                                <label class="btn btn-outline-secondary" for="col_day_2">Tue</label>

                                                <input type="checkbox" class="btn-check collection-day" id="col_day_3" value="3">
                                                <label class="btn btn-outline-secondary" for="col_day_3">Wed</label>

                                                <input type="checkbox" class="btn-check collection-day" id="col_day_4" value="4">
                                                <label class="btn btn-outline-secondary" for="col_day_4">Thu</label>

                                                <input type="checkbox" class="btn-check collection-day" id="col_day_5" value="5">
                                                <label class="btn btn-outline-secondary" for="col_day_5">Fri</label>

                                                <input type="checkbox" class="btn-check collection-day" id="col_day_6" value="6">
                                                <label class="btn btn-outline-secondary" for="col_day_6">Sat</label>

                                                <input type="checkbox" class="btn-check collection-day" id="col_day_0" value="0">
                                                <label class="btn btn-outline-secondary" for="col_day_0">Sun</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Types -->
                            <div class="col-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">Customer Document Types</h6>
                                        <div class="input-group mb-3">
                                            <input type="text" id="new_document_type" class="form-control" placeholder="Add new document type...">
                                            <button class="btn btn-success" id="btnAddDocumentType"><i class="fa-solid fa-plus"></i></button>
                                        </div>
                                        <div id="document_types_list" class="d-flex flex-wrap gap-2">
                                            <!-- Loaded via JS -->
                                        </div>
                                        <div class="mt-3 text-end">
                                            <button id="btnUpdateDocumentTypes" class="btn btn-primary btn-sm">Save Changes</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Recovery -->
                <div class="tab-pane fade" id="tab-recovery" role="tabpanel">
                    <div class="glass-card p-4">
                        <div class="section-header">
                            <h2 class="section-title">Recovery Settings</h2>
                            <p class="section-desc">Manage settings for recovery officers and leads.</p>
                        </div>

                        <div class="row g-4">
                            <!-- Recovery Status -->
                            <div class="col-md-12">
                                <div class="card p-3 shadow-sm border-0 bg-white">
                                    <label class="form-label fw-bold">Recovery Account Status</label>
                                    <div class="d-flex gap-2">
                                        <select id="recovery_account_status" class="form-select" style="max-width:300px">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        <button id="btnUpdateRecoveryAccount" class="btn btn-primary">Update</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Image Types Sections -->
                            @foreach(['Regular' => '', 'Guardian' => 'guardian_', 'Guarantor' => 'guarantor_'] as $label => $prefix)
                            <div class="col-md-12">
                                <div class="card shadow-sm border-0">
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-3">{{ $label }} Image Types</h6>
                                        <div class="row g-2 align-items-center mb-3">
                                            <div class="col-md-6">
                                                <input type="text" id="new_{{ $prefix }}image_type" class="form-control" placeholder="New type name...">
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="{{ $prefix }}is_required">
                                                    <label class="form-check-label" for="{{ $prefix }}is_required">Required</label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <button id="btnAdd{{ $label == 'Regular' ? '' : $label }}ImageType" class="btn btn-outline-success w-100">Add</button>
                                            </div>
                                        </div>
                                        <div id="{{ $prefix }}image_types_list" class="d-flex flex-wrap gap-2"></div>
                                        <div class="mt-3 text-end">
                                            <button id="btnUpdate{{ $label == 'Regular' ? '' : $label }}ImageTypes" class="btn btn-primary btn-sm">Save List</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- 6. Agreements -->
                <div class="tab-pane fade" id="tab-agreement" role="tabpanel">
                    <div class="glass-card p-4">
                        <div class="section-header">
                            <h2 class="section-title">Agreement Settings</h2>
                        </div>
                        <!-- Agreement Images -->
                        <div class="card shadow-sm border-0">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Agreement Image Types</h6>
                                <div class="row g-2 align-items-center mb-3">
                                    <div class="col-md-6">
                                        <input type="text" id="new_agreement_image_type" class="form-control" placeholder="New type name...">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="agreement_is_required">
                                            <label class="form-check-label" for="agreement_is_required">Required</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button id="btnAddAgreementImageType" class="btn btn-outline-success w-100">Add</button>
                                    </div>
                                </div>
                                <div id="agreement_image_types_list" class="d-flex flex-wrap gap-2"></div>
                                <div class="mt-3 text-end">
                                    <button id="btnUpdateAgreementImageTypes" class="btn btn-primary btn-sm">Save List</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 7. Commission -->
                <div class="tab-pane fade" id="tab-commission" role="tabpanel">
                    <div class="glass-card p-4">
                        <div class="section-header d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="section-title">Commission Settings</h2>
                                <p class="section-desc">Manage collector commission rates.</p>
                            </div>
                            <button class="btn btn-outline-primary btn-sm" id="btnAddCommissionPerson">
                                <i class="fa-solid fa-plus"></i> Add Person
                            </button>
                        </div>

                        <div class="row g-2 mb-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Select Branch</label>
                                <select class="form-select" id="commission_branch">
                                    <option value="0">-- Select --</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button class="btn btn-success w-100" id="btnReloadCommission">Load</button>
                            </div>
                        </div>

                        <div class="table-responsive rounded border">
                            <table class="table table-hover mb-0" id="commissionTable">
                                <thead class="bg-light"></thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center p-3 text-muted">Select a branch to load data.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 text-end">
                            <button class="btn btn-primary" id="btnSaveCommissionRates">Save Rates</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Commission Person Modal -->
<div class="modal fade" id="commissionPersonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Commission Person</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12"><label class="form-label">Full Name</label><input type="text" class="form-control" id="cp_full_name"></div>
                    <div class="col-6"><label class="form-label">Contact</label><input type="text" class="form-control" id="cp_contact"></div>
                    <div class="col-6"><label class="form-label">NIC</label><input type="text" class="form-control" id="cp_nic"></div>
                    <div class="col-12"><label class="form-label">Description</label><input type="text" class="form-control" id="cp_brief"></div>
                    <div class="col-12">
                        <hr class="my-2">
                    </div>
                    <div class="col-6"><label class="form-label">Bank</label><input type="text" class="form-control" id="cp_bank_name"></div>
                    <div class="col-6"><label class="form-label">Branch</label><input type="text" class="form-control" id="cp_bank_branch"></div>
                    <div class="col-6"><label class="form-label">Account No</label><input type="text" class="form-control" id="cp_bank_acc_no"></div>
                    <div class="col-6"><label class="form-label">Account Name</label><input type="text" class="form-control" id="cp_bank_acc_name"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success" id="btnSaveCommissionPerson">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Global State
    let APP_SETTINGS = {};

    $(document).ready(function() {
        fetchSettings();
        fetchShortcuts();
        loadCommissionBranches();

        // Search Shortcuts
        $('#shortcutSearch').on('keyup', function() {
            const val = $(this).val().toLowerCase();
            $('.shortcut-card').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).closest('[class^="col-"]').toggle(text.includes(val));
            });
            // Should also hide empty groups? Maybe too complex for now.
        });

        // Upsert Handlers
        $('#btnUpdateLoanPolicy').click(() => save_setting('loan_disbursement_policy', $('input[name="loan_policy"]:checked').val()));
        $('#btnUpdatePaymentMemberName').click(() => save_setting('payment_member_name', $('#payment_member_name').val()));
        $('#btnUpdatePaymentBackdate').click(() => save_setting('payment_backdate', $('#payment_backdate').val()));
        $('#btnUpdateRecoveryAccount').click(() => save_setting('recovery_account_status', $('#recovery_account_status').val()));

        // Document Types
        $('#btnAddDocumentType').click(addDocumentType);
        $('#btnUpdateDocumentTypes').click(() => save_setting('document_types', JSON.stringify(getCurrentList('document_types_list'))));

        // Image Types Logic (Generic)
        const setupImageTypeHandlers = (prefix, key) => {
            $(`#btnAdd${prefix}ImageType`).click(() => {
                const name = $(`#new_${prefix.toLowerCase()}image_type`).val();
                const req = $(`#${prefix.toLowerCase()}is_required`).is(':checked');
                if (!name) return Swal.fire('Error', 'Name required', 'error');

                const listId = `#${prefix.toLowerCase()}image_types_list`;
                const html = `
                    <div class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2 item-badge" data-name="${name}" data-req="${req}">
                        ${name} ${req ? '<span class="text-danger">*</span>' : ''}
                        <i class="fa-solid fa-times text-danger cursor-pointer" onclick="$(this).parent().remove()"></i>
                    </div>`;
                $(listId).append(html);
                $(`#new_${prefix.toLowerCase()}image_type`).val('');
            });
            $(`#btnUpdate${prefix}ImageTypes`).click(() => {
                const list = [];
                $(`#${prefix.toLowerCase()}image_types_list .item-badge`).each(function() {
                    list.push({
                        name: $(this).data('name'),
                        is_required: $(this).data('req')
                    });
                });
                save_setting(key, JSON.stringify(list));
            });
        };

        setupImageTypeHandlers('', 'image_types');
        setupImageTypeHandlers('Guardian', 'guardian_image_types');
        setupImageTypeHandlers('Guarantor', 'guarantor_image_types');
        setupImageTypeHandlers('Agreement', 'agreement_image_types');

        // Commission Logic (Legacy compatibility)
        $('#btnReloadCommission').click(loadCommissionData);
        $('#btnSaveCommissionRates').click(saveCommissionRates);
        $('#btnAddCommissionPerson').click(() => new bootstrap.Modal('#commissionPersonModal').show());
        $('#btnSaveCommissionPerson').click(saveCommissionPerson);
        // Bind change event for branch fetch
        // Note: Branches loaded via /branches/all route in existing app? Check routes.
        // Route: /branches/all -> CommissionController@branches_all
    });

    // Fetch All Settings
    function fetchSettings() {
        $.get("{{ route('settings.all') }}", function(res) {
            APP_SETTINGS = res.items;
            applySettingsToUI();
        });
    }

    function applySettingsToUI() {
        // Simple inputs
        if (APP_SETTINGS.loan_disbursement_policy) $(`input[name="loan_policy"][value="${APP_SETTINGS.loan_disbursement_policy}"]`).prop('checked', true);
        if (APP_SETTINGS.payment_member_name) $('#payment_member_name').val(APP_SETTINGS.payment_member_name);
        if (APP_SETTINGS.payment_backdate) $('#payment_backdate').val(APP_SETTINGS.payment_backdate);
        if (APP_SETTINGS.recovery_account_status) $('#recovery_account_status').val(APP_SETTINGS.recovery_account_status);

        // Loan Restrictions
        if (APP_SETTINGS.document_upload_restriction) $('#document_upload_restriction').val(APP_SETTINGS.document_upload_restriction);
        if (APP_SETTINGS.guarantees_restriction) $('#guarantees_restriction').val(APP_SETTINGS.guarantees_restriction);
        if (APP_SETTINGS.change_product_details) $('#change_product_details').val(APP_SETTINGS.change_product_details);
        $('#first_installment_daily').val(APP_SETTINGS.first_installment_daily);
        $('#first_installment_weekly').val(APP_SETTINGS.first_installment_weekly);
        $('#first_installment_monthly').val(APP_SETTINGS.first_installment_monthly);

        // Lists/Arrays
        renderList('document_types_list', APP_SETTINGS.document_types);
        renderImageTypeList('image_types_list', APP_SETTINGS.image_types);
        renderImageTypeList('guardian_image_types_list', APP_SETTINGS.guardian_image_types);
        renderImageTypeList('guarantor_image_types_list', APP_SETTINGS.guarantor_image_types);
        renderImageTypeList('agreement_image_types_list', APP_SETTINGS.agreement_image_types);

        // Collection Days
        try {
            const days = JSON.parse(APP_SETTINGS.collection_days || '[]');
            days.forEach(d => $(`#col_day_${d}`).prop('checked', true));
        } catch (e) {}

        // HO Approvals
        $('.approval-toggle').each(function() {
            const type = $(this).data('type');
            const key = 'headoffice_approval_' + type;
            if (APP_SETTINGS[key] === 'required') $(this).prop('checked', true);
        });
    }

    // Shortcuts Logic
    function fetchShortcuts() {
        // Calls the CompanyController@show
        $.get('/shortcuts/all', function(res) {
            if (res.items) {
                res.items.forEach(item => {
                    $(`input.access_module[id="${item.name}"]`).prop('checked', true);
                    $(`input.access_module[id="${item.name}"]`).closest('.shortcut-card').addClass('active');
                });
            }
        });
    }

    window.toggleShortcut = function(id) {
        const checkbox = document.getElementById(id);
        checkbox.checked = !checkbox.checked;
        $(checkbox).closest('.shortcut-card').toggleClass('active', checkbox.checked);
    }

    window.saveShortcut = function(e) {
        e.preventDefault();
        const checkboxValues = {};
        $('.access_module:checked').each(function() {
            checkboxValues[$(this).attr('id')] = "on";
        });

        $.ajax({
            url: "{{ route('company.shortcuts') }}", // Uses existing route
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                checkboxValues: checkboxValues
            },
            success: () => Swal.fire('Saved', 'Shortcuts updated', 'success'),
            error: () => Swal.fire('Error', 'Failed to save', 'error')
        });
    }

    // Generic Save
    function save_setting(key, value) {
        $.ajax({
            url: "{{ route('settings.upsert') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                key: key,
                value: value
            },
            success: () => Swal.fire({
                icon: 'success',
                title: 'Saved',
                timer: 1000,
                showConfirmButton: false
            }),
            error: (xhr) => Swal.fire('Error', xhr.responseJSON?.message || 'Failed', 'error')
        });
    }

    // Helper Renders
    function renderList(containerId, jsonString) {
        const container = $(`#${containerId}`).empty();
        try {
            const list = JSON.parse(jsonString || '[]');
            list.forEach(item => {
                container.append(`<div class="badge bg-white text-dark border p-2 d-flex align-items-center gap-2 item-simple">${item} <i class="fa-solid fa-times text-danger cursor-pointer" onclick="$(this).parent().remove()"></i></div>`);
            });
        } catch (e) {}
    }

    function renderImageTypeList(containerId, jsonString) {
        const container = $(`#${containerId}`).empty();
        try {
            const list = JSON.parse(jsonString || '[]');
            list.forEach(item => {
                const html = `
                    <div class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2 item-badge" data-name="${item.name}" data-req="${item.is_required}">
                        ${item.name} ${item.is_required ? '<span class="text-danger">*</span>' : ''}
                        <i class="fa-solid fa-times text-danger cursor-pointer" onclick="$(this).parent().remove()"></i>
                    </div>`;
                container.append(html);
            });
        } catch (e) {}
    }

    function addDocumentType() {
        const name = $('#new_document_type').val();
        if (!name) return;
        $('#document_types_list').append(`<div class="badge bg-white text-dark border p-2 d-flex align-items-center gap-2 item-simple">${name} <i class="fa-solid fa-times text-danger cursor-pointer" onclick="$(this).parent().remove()"></i></div>`);
        $('#new_document_type').val('');
    }

    function getCurrentList(containerId) {
        const list = [];
        $(`#${containerId} .item-simple`).each(function() {
            // Text includes the icon, need to clean it. Actually specific extraction is safer.
            // But here we rely on text(). The icon <i..> has no text.
            list.push($(this).text().trim());
        });
        return list;
    }

    // Commission Logic (Simplified for integration)
    // Commission Logic
    let COMM_PRODUCTS = [];
    let COMM_PEOPLE = [];
    let COMM_RATE_MAP = {}; // key: personId_productId => rate

    const getSelectedCommissionBranch = () => parseInt($("#commission_branch").val() || "0");

    function loadCommissionBranches() {
        $.get('/branches/all', function(res) {
            const sel = $('#commission_branch');
            // Check if res is array or object with items
            const items = Array.isArray(res) ? res : (res.items || []);
            // Clear existing options except first
            sel.find('option:not(:first)').remove();
            items.forEach(b => sel.append(`<option value="${b.branch_id}">${b.Name}</option>`));

            // Auto-select session branch if available
            const sessionBranch = parseInt($('meta[name="session-branch"]').attr('content') || '0');
            if (sessionBranch > 0) sel.val(sessionBranch).trigger('change');
        });
    }

    function loadCommissionData() {
        const branch_id = getSelectedCommissionBranch();
        if (branch_id <= 0) {
            $("#commissionTable thead").html('');
            $("#commissionTable tbody").html('<tr><td colspan="99" class="text-center text-muted">Select a branch to load commissions.</td></tr>');
            return;
        }

        $.ajax({
            type: "GET",
            url: "/commissions/all",
            data: {
                branch_id
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            success: function(res) {
                COMM_PRODUCTS = res.products || [];
                COMM_PEOPLE = res.people || [];
                COMM_RATE_MAP = {};
                (res.rates || []).forEach(r => COMM_RATE_MAP[`${r.commission_person_id}_${r.product_id}`] = r.rate);
                renderCommissionTable();
            },
            error: function(xhr) {
                Swal.fire("Error", xhr.responseJSON?.message || "Failed to load commissions.", "error");
            }
        });
    }

    function renderCommissionTable() {
        const $thead = $("#commissionTable thead");
        const $tbody = $("#commissionTable tbody");

        // Head
        let headHtml = `<tr>
            <th style="min-width:120px;">Type</th>
            <th style="min-width:220px;">Name</th>`;
        COMM_PRODUCTS.forEach(p => {
            headHtml += `<th class="text-center" style="min-width:140px;">${p.name}</th>`;
        });
        headHtml += `</tr>`;
        $thead.html(headHtml);

        // Body rows
        let bodyHtml = "";
        COMM_PEOPLE.forEach(person => {
            bodyHtml += `<tr data-person-id="${person.id}">
                <td><span class="badge ${person.type === 'Collector' ? 'bg-primary' : 'bg-secondary'}">${person.type}</span></td>
                <td>
                    <div class="fw-bold">${person.full_name}</div>
                    <small class="text-muted">
                        ${person.contact_number || ''}
                        ${person.nic_number ? ' | ' + person.nic_number : ''}
                        ${person.bank_name ? `<br><i class="fa fa-bank"></i> ${person.bank_name} ${person.bank_branch ? ' | ' + person.bank_branch : ''}` : ''}
                        ${person.bank_account_number ? '<br>Acc No: ' + person.bank_account_number : ''}
                    </small>
                </td>`;

            COMM_PRODUCTS.forEach(p => {
                const key = `${person.id}_${p.id}`;
                const val = (COMM_RATE_MAP[key] ?? 0);
                bodyHtml += `
                    <td class="text-center">
                        <input type="number" class="form-control form-control-sm comm-rate"
                             min="0" step="0.01"
                             data-person="${person.id}" data-product="${p.id}"
                             value="${val}">
                    </td>`;
            });
            bodyHtml += `</tr>`;
        });

        if (COMM_PEOPLE.length === 0) {
            bodyHtml = '<tr><td colspan="99" class="text-center text-muted p-4">No commission people found for this branch.</td></tr>';
        }

        $tbody.html(bodyHtml);
    }

    function saveCommissionRates() {
        const branch_id = getSelectedCommissionBranch();
        if (branch_id <= 0) return Swal.fire("Warning", "Please select a branch first.", "warning");

        const rates = [];
        $(".comm-rate").each(function() {
            rates.push({
                person_id: $(this).data('person'),
                product_id: $(this).data('product'),
                rate: $(this).val()
            });
        });

        $.ajax({
            type: "POST",
            url: "/commissions/rates/save",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            data: {
                branch_id,
                rates
            },
            success: function() {
                Swal.fire({
                    icon: "success",
                    title: "Saved!",
                    timer: 1200,
                    showConfirmButton: false
                });
            },
            error: function(xhr) {
                Swal.fire("Error", xhr.responseJSON?.message || "Failed to save", "error");
            }
        });
    }

    function saveCommissionPerson() {
        const branch_id = getSelectedCommissionBranch();
        if (branch_id <= 0) return Swal.fire("Warning", "Please select a branch first.", "warning");

        const payload = {
            branch_id,
            full_name: $('#cp_full_name').val().trim(),
            contact_number: $('#cp_contact').val().trim(),
            nic_number: $('#cp_nic').val().trim(),
            brief_description: $('#cp_brief').val().trim(),
            bank_name: $('#cp_bank_name').val().trim(),
            bank_branch: $('#cp_bank_branch').val().trim(),
            bank_account_number: $('#cp_bank_acc_no').val().trim(),
            bank_account_name: $('#cp_bank_acc_name').val().trim(),
        };

        if (!payload.full_name) return Swal.fire("Warning", "Full Name is required.", "warning");

        $.ajax({
            type: "POST",
            url: "/commissions/person/store",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            data: payload,
            success: function() {
                Swal.fire({
                    icon: "success",
                    title: "Person Added!",
                    timer: 1200,
                    showConfirmButton: false
                });
                bootstrap.Modal.getInstance(document.getElementById('commissionPersonModal')).hide();
                loadCommissionData();
            },
            error: function(xhr) {
                Swal.fire("Error", xhr.responseJSON?.message || "Failed to add person", "error");
            }
        });
    }

    // Bind Commission Branch Change
    $(document).ready(function() {
        $("#commission_branch").on("change", loadCommissionData);
    });
</script>
@endsection
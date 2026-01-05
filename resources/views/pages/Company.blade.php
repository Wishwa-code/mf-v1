@extends('layout.admin')

@section('head')
<!-- Font Awesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<style>
    .company-logo-container {
        width: 120px;
        height: 120px;
        margin: 0 auto;
        position: relative;
    }

    .company-logo {
        width: 100%;
        height: 100%;
        object-fit: contain;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .company-logo-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-size: 2.5rem;
    }

    .info-card {
        transition: transform 0.2s ease-in-out;
        border: none;
        background: #fff;
    }

    .info-card:hover {
        transform: translateY(-2px);
    }

    .info-label {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 1.05rem;
        color: #212529;
        font-weight: 500;
    }

    .branch-badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    /* Fix for table responsiveness */
    .table-responsive {
        margin-bottom: 0;
        border-radius: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row g-4">
        <!-- Company Overview Column -->
        <div class="col-12 col-md-12 col-lg-5 col-xl-4">
            <!-- Company Identity Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 text-center info-card">
                <div class="card-body p-4">
                    <div class="mb-3 company-logo-container">
                        @if(session('user_data.company.Logo'))
                        <img src="{{ session('user_data.company.Logo') }}"
                            alt="Company Logo"
                            id="company-logo"
                            class="company-logo bg-light"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="company-logo-placeholder" style="display: none;">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        @else
                        <img src=""
                            alt="Company Logo"
                            id="company-logo"
                            class="company-logo bg-light"
                            style="display: none;"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="company-logo-placeholder">
                            <i class="fa-solid fa-building"></i>
                        </div>
                        @endif
                    </div>
                    <h3 class="fw-bold mb-1 text-dark" id="company-name">{{ session('user_data.company.Company_Name', 'Company Name') }}</h3>
                    <p class="text-secondary mb-3" id="company-city">{{ session('user_data.company.City', 'Main Branch') }}</p>

                    <div class="d-flex justify-content-center gap-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 border border-primary border-opacity-10">
                            <i class="fa-solid fa-id-badge me-2"></i>ID: <span id="company-id">{{ session('user_data.company.idCompany', 'N/A') }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card border-0 shadow-sm rounded-4 info-card">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-address-card me-2 text-primary"></i>Contact Info</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <div class="info-label"><i class="fa-solid fa-location-dot me-2"></i>Address</div>
                        <div class="info-value text-break" id="company-address">
                            {{ implode(', ', array_filter([
                                    session('user_data.company.Address01'),
                                    session('user_data.company.Address02'),
                                    session('user_data.company.Address03'),
                                    session('user_data.company.City')
                                ])) ?: 'Not Provided' }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="info-label"><i class="fa-solid fa-phone me-2"></i>Contact Number</div>
                        <div class="info-value" id="company-contact">{{ session('user_data.company.Contact_No', 'Not Provided') }}</div>
                    </div>

                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center h-100">
                                <div class="info-label">Format Type</div>
                                <div class="info-value small text-wrap" id="company-format">{{ session('user_data.company.Customer_No_Format_Type', 'Auto') }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded-3 text-center h-100">
                                <div class="info-label">Status</div>
                                <div class="info-value" id="company-status">{{ session('user_data.company.Status', 'Active') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Information Column -->
        <div class="col-12 col-md-12 col-lg-7 col-xl-8">
            <!-- Branch Details Section -->
            <div class="card border-0 shadow-sm rounded-4 h-100-desktop-only mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-network-wired me-2 text-primary"></i>Branch Network</h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill" onclick="fetchBranchDetails()">
                        <i class="fa-solid fa-rotate me-1"></i> Refresh
                    </button>
                </div>
                <div class="card-body p-4">
                    <div id="branch-loading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading details...</p>
                    </div>

                    <div id="branch-error" class="alert alert-danger d-none rounded-3" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i> <span id="error-message">Failed to load data.</span>
                        <button class="btn btn-link btn-sm p-0 ms-2 align-baseline" onclick="fetchBranchDetails()">Try again</button>
                    </div>

                    <div id="branch-content" class="d-none">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle border-light mb-0" id="branches-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 ps-3 text-nowrap rounded-start">Branch Name</th>
                                        <th class="py-3 text-nowrap">Code</th>
                                        <th class="py-3 text-nowrap">Services</th>
                                        <th class="py-3 pe-3 text-nowrap rounded-end text-end">Type</th>
                                    </tr>
                                </thead>
                                <tbody id="branches-tbody">
                                    <!-- Rows populated by JS -->
                                </tbody>
                            </table>
                        </div>
                        <div id="no-branches-msg" class="text-center py-3 d-none text-muted">
                            <i class="fa-regular fa-building fa-2x mb-3 opacity-50"></i>
                            <p>No registered branches found.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Privileges Section -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user-shield me-2 text-primary"></i>User Privileges</h5>
                </div>
                <div class="card-body p-4">
                    <div id="privileges-loading" class="d-none">
                        <!-- Shared loading state with branch card usually, but good to have placeholder if needed -->
                    </div>
                    <div id="privileges-content" class="d-none">
                        <div class="d-flex flex-wrap gap-2" id="privileges-list">
                            <!-- Privileges populated by JS -->
                        </div>
                    </div>
                    <div id="no-privileges-msg" class="text-center py-3 d-none text-muted">
                        <p>No privileges assigned.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetchBranchDetails();

        // Search functionality
        const searchInput = document.getElementById('privileges-search');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase();
                const container = document.getElementById('privileges-list');
                const badges = container.getElementsByTagName('span');
                let hasVisible = false;

                for (let i = 0; i < badges.length; i++) {
                    const txtValue = badges[i].textContent || badges[i].innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        badges[i].style.display = "";
                        hasVisible = true;
                    } else {
                        badges[i].style.display = "none";
                    }
                }

                const noResults = document.getElementById('no-results-msg');
                if (noResults) {
                    noResults.classList.toggle('d-none', hasVisible);
                }
            });
        }
    });

    async function fetchBranchDetails() {
        const loadingEl = document.getElementById('branch-loading');
        const contentEl = document.getElementById('branch-content');
        const errorEl = document.getElementById('branch-error');
        const tbody = document.getElementById('branches-tbody');
        const noDataMsg = document.getElementById('no-branches-msg');
        const errorMsg = document.getElementById('error-message');

        // Reset UI
        loadingEl.classList.remove('d-none');
        contentEl.classList.add('d-none');
        errorEl.classList.add('d-none');
        tbody.innerHTML = '';

        try {
            const response = await fetch('/company/branches-proxy', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            // Populate Company Info
            const company = data?.userData?.company || {};
            console.log(company);


            if (Object.keys(company).length > 0) {
                document.getElementById('company-name').textContent = company.Company_Name || 'Company Name';
                document.getElementById('company-city').textContent = company.City || 'Main Branch';
                document.getElementById('company-id').textContent = company.idCompany || 'N/A';

                const addressParts = [
                    company.Address01,
                    company.Address02,
                    company.Address03,
                    company.City
                ].filter(part => part && part.trim() !== '');
                document.getElementById('company-address').textContent = addressParts.length > 0 ? addressParts.join(', ') : 'Not Provided';

                document.getElementById('company-contact').textContent = company.Contact_No || 'Not Provided';
                document.getElementById('company-format').textContent = company.Customer_No_Format_Type || 'Auto';
                document.getElementById('company-status').textContent = company.Status || 'Active';

                const logoImg = document.getElementById('company-logo');
                if (logoImg) {
                    const placeholder = logoImg.nextElementSibling;
                    if (company.Logo) {
                        logoImg.src = company.Logo;
                        logoImg.style.display = 'block';
                        if (placeholder) placeholder.style.display = 'none';
                    } else {
                        logoImg.style.display = 'none';
                        if (placeholder) placeholder.style.display = 'flex';
                    }
                }
            }

            const branches = data?.userData?.branches || [];
            const privileges = data?.userData?.privileges || [];

            // Populate Branches
            if (branches.length === 0) {
                noDataMsg.classList.remove('d-none');
            } else {
                noDataMsg.classList.add('d-none');

                branches.forEach(branch => {
                    const row = document.createElement('tr');

                    const name = branch.Name || 'N/A';
                    const code = branch.Branch_Code || 'N/A';

                    let services = '';
                    if (branch.isMicrofinance == 1) services += '<span class="badge bg-info-soft text-info me-1">Microfinance</span>';
                    if (branch.isPawning == 1) services += '<span class="badge bg-warning-soft text-warning me-1">Pawning</span>';
                    if (!services) services = '<span class="text-muted small">-</span>';

                    const typeBadge = branch.Branch_Type == 1 ?
                        '<span class="badge bg-primary-soft text-primary rounded-pill branch-badge">Head Office</span>' :
                        '<span class="badge bg-success-soft text-success rounded-pill branch-badge">Branch</span>';

                    row.innerHTML = `
                        <td class="ps-3 fw-medium text-dark">${name}</td>
                        <td><span class="badge bg-light text-dark border">${code}</span></td>
                        <td>${services}</td>
                        <td class="pe-3 text-end">${typeBadge}</td> 
                    `;
                    tbody.appendChild(row);
                });
            }

            // Populate Privileges
            const privList = document.getElementById('privileges-list');
            const privContent = document.getElementById('privileges-content');
            const noPrivMsg = document.getElementById('no-privileges-msg');

            privList.innerHTML = ''; // Clear previous

            if (privileges.length === 0) {
                privContent.classList.add('d-none');
                noPrivMsg.classList.remove('d-none');
            } else {
                noPrivMsg.classList.add('d-none');
                privContent.classList.remove('d-none');

                const filteredPrivileges = privileges.filter(priv => priv.isMicroFinancePrivilages == 1);

                if (filteredPrivileges.length === 0) {
                    privContent.classList.add('d-none');
                    noPrivMsg.classList.remove('d-none');
                } else {
                    const categories = {
                        'Loan Management': ['loan', 'credit', 'repayment', 'schedule', 'arrear'],
                        'Customer Management': ['customer', 'client', 'document', 'kyc', 'guarantor'],
                        'Financials': ['payment', 'transaction', 'bank', 'cash', 'voucher', 'expense', 'fee', 'charge', 'fine'],
                        'Approvals & Verification': ['approve', 'verify', 'authorize', 'check', 'reject'],
                        'Reports & Analytics': ['report', 'summary', 'analytic', 'dashboard', 'statement'],
                        'User & Admin': ['user', 'role', 'permission', 'branch', 'setting', 'config']
                    };

                    const groupedPrivileges = {};

                    // Helper to determine category
                    const getCategory = (text) => {
                        const lowerText = text.toLowerCase();
                        for (const [catName, keywords] of Object.entries(categories)) {
                            if (keywords.some(k => lowerText.includes(k))) {
                                return catName;
                            }
                        }
                        return 'Other';
                    };

                    // Group privileges
                    filteredPrivileges.forEach(priv => {
                        let text = priv.Description || 'Unknown';
                        // Clean text
                        text = text.replace(/_/g, ' ').toLowerCase().replace(/\b\w/g, s => s.toUpperCase());

                        const category = getCategory(text);
                        if (!groupedPrivileges[category]) {
                            groupedPrivileges[category] = [];
                        }
                        groupedPrivileges[category].push({
                            text,
                            original: priv
                        });
                    });

                    // Render Cards
                    privList.className = 'row g-3'; // Change container to row for cards
                    privList.innerHTML = ''; // Clear

                    // Order categories: defined keys first, then 'Other'
                    const sortedCategories = Object.keys(categories);
                    if (groupedPrivileges['Other']) sortedCategories.push('Other');

                    sortedCategories.forEach(catName => {
                        if (!groupedPrivileges[catName]) return;

                        const items = groupedPrivileges[catName];

                        // Card Column
                        const col = document.createElement('div');
                        col.className = 'col-12 col-md-6';

                        // Card HTML
                        // Card HTML Structure
                        col.innerHTML = `
                            <div class="card h-100 border border-light bg-light bg-opacity-50">
                                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                                    <h6 class="fw-bold text-dark mb-0 small text-uppercase letter-spacing-1">${catName}</h6>
                                </div>
                                <div class="card-body pt-2">
                                    <div class="d-flex flex-wrap gap-2 badge-container">
                                        <!-- Badges will be appended here -->
                                    </div>
                                </div>
                            </div>
                        `;

                        // Append Badges via DOM (safest against HTML escaping)
                        const container = col.querySelector('.badge-container');

                        items.forEach(item => {
                            const text = item.text;
                            const lowerText = text.toLowerCase();

                            let iconClass = 'fa-circle-check';
                            let badgeClass = 'badge bg-white text-secondary border fw-normal d-inline-flex align-items-center gap-2 shadow-sm';

                            if (lowerText.includes('create') || lowerText.includes('add')) {
                                iconClass = 'fa-plus text-success';
                            } else if (lowerText.includes('update') || lowerText.includes('edit')) {
                                iconClass = 'fa-pen-to-square text-warning';
                            } else if (lowerText.includes('delete') || lowerText.includes('remove')) {
                                iconClass = 'fa-trash text-danger';
                            } else if (lowerText.includes('view') || lowerText.includes('read')) {
                                iconClass = 'fa-eye text-info';
                            } else if (lowerText.includes('approv') || lowerText.includes('verify')) {
                                iconClass = 'fa-check-double text-primary';
                            } else if (lowerText.includes('report')) {
                                iconClass = 'fa-file-lines text-secondary';
                            }

                            const span = document.createElement('span');
                            span.className = badgeClass;
                            span.innerHTML = `<i class="fa-solid ${iconClass}"></i> ${text}`;
                            container.appendChild(span);
                        });

                        privList.appendChild(col);
                    });
                }
            }

            loadingEl.classList.add('d-none');
            contentEl.classList.remove('d-none');

        } catch (error) {
            console.error('Fetch error:', error);
            loadingEl.classList.add('d-none');
            // Show more specific error if available or fallback
            errorMsg.textContent = "Unable to connect to branch server.";
            errorEl.classList.remove('d-none');
        }
    }
</script>
@endsection
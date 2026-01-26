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

    /* Welcome Banner - Navy Blue */
    .welcome-banner {
        padding: 2.5rem 2rem;
        background: #1e293b;
        /* Navy Blue / Slate 800 */
        color: white;
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    /* KPI Cards - Pastel Colors */
    .kpi-card-purple {
        background: #8b5cf6;
        /* Pastel Purple */
        background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);
        color: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 8px 20px rgba(139, 92, 246, 0.2);
    }

    .kpi-card-red {
        background: #f43f5e;
        /* Soft Red */
        background: linear-gradient(135deg, #fb7185 0%, #f43f5e 100%);
        color: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 8px 20px rgba(244, 63, 94, 0.2);
    }

    .kpi-card-orange {
        background: #f97316;
        /* Light Orange */
        background: linear-gradient(135deg, #fdba74 0%, #f97316 100%);
        color: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 8px 20px rgba(249, 115, 22, 0.2);
    }

    .kpi-metric {
        font-size: 3rem;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .kpi-subtext {
        font-size: 0.85rem;
        opacity: 0.9;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Notification Styled List */
    .notification-item {
        background: #f8fafc;
        /* Subtle Grey */
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        align-items: flex-start;
        border-left: 2px solid transparent;
        /* Default */
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }

    .notification-item:hover {
        background: #f1f5f9;
    }

    .notification-border-red {
        border-left-color: #ef4444;
    }

    .notification-border-orange {
        border-left-color: #f59e0b;
    }

    .notification-border-blue {
        border-left-color: #3b82f6;
    }

    .welcome-pattern {
        /* ... existing pattern ... */

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

    /* Timeline Styles */
    .timeline-item {
        position: relative;
        padding-left: 30px;
        border-left: 2px solid #e2e8f0;
        margin-bottom: 25px;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
        border-left-color: transparent;
    }

    .timeline-dot {
        position: absolute;
        left: -6px;
        top: 0;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--primary-color);
        box-shadow: 0 0 0 4px #fff;
    }

    .timeline-time {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .timeline-content {
        font-size: 0.9rem;
        color: #334155;
    }

    /* Notification Dots */
    .dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }

    .dot-warning {
        background-color: #f59e0b;
    }

    .dot-danger {
        background-color: #ef4444;
    }

    .dot-info {
        background-color: #3b82f6;
    }

    .dot-success {
        background-color: #10b981;
    }

    /* Chart Dark Grid Context */
    .chart-dark-wrap {
        background: #0f172a;
        /* Slate 900 */
        border-radius: 16px;
        padding: 20px;
    }
</style>
@endsection

@section('content')

<div class="container-fluid" style="padding: 32px !important;">

    <!-- Welcome Section (Navy Blue Banner) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="welcome-banner">
                <div class="position-relative z-2">
                    <h2 class="text-white mb-2" style="font-weight: 700;">Hello, {{ user_data('full_name') }}! 👋</h2>
                    <p class="text-white-50 mb-0" style="max-width: 600px;">Here's what's happening with your microfinance portfolio today.</p>
                </div>
                <div class="welcome-pattern"></div>
            </div>
        </div>
    </div>

    <!-- Row 1: 3 Equal KPI Cards (Purple, Red, Orange) -->
    <div class="row g-4 mb-4">
        <!-- Stat 1: Total Outstanding (Purple) -->
        <div class="col-md-4">
            <div class="stat-card kpi-card-purple h-100 p-4" onclick="showTotalOutstandingModal()" role="button">
                <div class="position-relative z-2">
                    <div class="kpi-subtext mb-2">Total Outstanding</div>
                    <div class="kpi-metric counter" data-target="{{ $total_outstanding ?? 0 }}">0</div>
                    <div class="d-flex align-items-center mt-3">
                        <i class="ri-arrow-right-up-line me-1"></i>
                        <span class="small opacity-75">View Details</span>
                    </div>
                </div>
                <i class="ri-wallet-3-line stat-icon-bg" style="font-size: 6rem; opacity: 0.1;"></i>
            </div>
        </div>

        <!-- Stat 2: Weekly Not Paid (Red) -->
        <div class="col-md-4">
            <div class="stat-card kpi-card-red h-100 p-4" onclick="showWeeklyNotPaidModal()" role="button">
                <div class="position-relative z-2">
                    <div class="kpi-subtext mb-2">Weekly Not Paid</div>
                    <div class="kpi-metric counter" data-target="{{ $weekly_not_paid ?? 0 }}">0</div>
                    <div class="d-flex align-items-center mt-3">
                        <i class="ri-alert-line me-1"></i>
                        <span class="small opacity-75">Requires Attention</span>
                    </div>
                </div>
                <i class="ri-calendar-close-line stat-icon-bg" style="font-size: 6rem; opacity: 0.1;"></i>
            </div>
        </div>

        <!-- Stat 3: Penalty Balance (Orange) -->
        <div class="col-md-4">
            <div class="stat-card kpi-card-orange h-100 p-4" onclick="showPenaltyBalanceModal()" role="button">
                <div class="position-relative z-2">
                    <div class="kpi-subtext mb-2">Penalty Balance</div>
                    <div class="kpi-metric counter" data-target="{{ $penalty_balance ?? 0 }}">0</div>
                    <div class="d-flex align-items-center mt-3">
                        <i class="ri-scales-3-line me-1"></i>
                        <span class="small opacity-75">Accumulated</span>
                    </div>
                </div>
                <i class="ri-scales-3-line stat-icon-bg" style="font-size: 6rem; opacity: 0.1;"></i>
            </div>
        </div>
    </div>

    <!-- Row 2: Timeline & Notifications -->
    <div class="row g-4 mb-4">
        <!-- Timeline (Left) -->
        <div class="col-lg-6"> <!-- Explicit 6 columns as per user request (2 cols bottom sec?) No, "Two columns; left... right..." usually implies equal or split. Let's do 6/6 for balance or 5/7. User said "Two columns". -->
            <div class="glass-panel h-100 p-4 bg-white">
                <h5 class="card-title mb-4 text-secondary">Today’s Timeline</h5>
                <div class="timeline-box ps-2">
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #3b82f6;"></div>
                        <div class="timeline-time">09:00 AM</div>
                        <div class="timeline-content"><strong>Market Open</strong> - Routine check completed.</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #ef4444;"></div>
                        <div class="timeline-time">10:30 AM</div>
                        <div class="timeline-content"><strong>Payment Alert</strong> - 5 High-value loans overdue.</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #10b981;"></div>
                        <div class="timeline-time">02:15 PM</div>
                        <div class="timeline-content"><strong>System Sync</strong> - Database backup successful.</div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background: #f59e0b;"></div>
                        <div class="timeline-time">04:00 PM</div>
                        <div class="timeline-content"><strong>Meeting</strong> - Branch Manager review.</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications (Right) -->
        <div class="col-lg-6">
            <div class="glass-panel h-100 p-4 bg-white">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="card-title mb-0 text-secondary">Categorized Notifications</h5>
                    <span class="badge bg-soft-info text-info rounded-pill px-3">Live</span>
                </div>

                <div class="d-flex flex-column gap-3">
                    <!-- Notification Item 1 -->
                    <div class="notification-item notification-border-red">
                        <div class="flex-grow-1">
                            <h6 class="m-0 text-dark fw-bold" style="font-size: 0.95rem;">Critical Alert</h6>
                            <p class="m-0 small text-muted mt-1">2 Centers reported collection discrepancies.</p>
                        </div>
                        <small class="text-muted" style="white-space: nowrap;">2m ago</small>
                    </div>

                    <!-- Notification Item 2 -->
                    <div class="notification-item notification-border-orange">
                        <div class="flex-grow-1">
                            <h6 class="m-0 text-dark fw-bold" style="font-size: 0.95rem;">Loan Approval</h6>
                            <p class="m-0 small text-muted mt-1">15 New applications pending verification.</p>
                        </div>
                        <small class="text-muted" style="white-space: nowrap;">1h ago</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 3: 3-Series Area Chart (Bottom - Keep existing but ensure light mode container) -->
    <!-- User prompt implied "Bottom Section: Two columns...". 
         Wait, the prompt says "Two columns; left... timeline... right... notifications". It doesn't mention the chart in the bottom section description explicitly in THIS prompt, but previously did.
         However, checking the uploaded image (I cannot see it, but I assume the user wants the chart somewhere or maybe removed?).
         "The bottom half of the screen should be a large, comprehensive Area Chart...". 
         Ah, the previous prompt asked for Chart. This prompt says "Bottom Section: Two columns...".
         Maybe the user wants the chart REMOVED or merged.
         I will keep the chart but maybe it's less prioritized. Or maybe the prompt implies the *View* is Top/Middle/Bottom and that's it.
         I will place the Chart at the very bottom (Row 4) just in case, labeled "Financial Performance".
         Wait, looking at the previous prompt "Bottom half... large Area Chart".
         This prompt "Bottom Section: Two columns...".
         I'll leave the chart at the bottom, it's safer to have more than less.
         
         Update: Ensure Chart Container is NOT dark grid if the whole UI is "Light Mode... airy".
         The prompt says "Aesthetic: Clean, airy...".
         But previous prompt asked for "Dark Grid".
         This prompt does NOT mention Dark Grid.
         I will switch Chart to **Light Mode** to match "Clean, airy".
    -->
    <div class="row mb-4">
        <div class="col-12">
            <!-- Light Mode Chart Container -->
            <div class="glass-panel p-4 bg-white">
                <h5 class="card-title mb-4 text-secondary">Financial Performance</h5>
                <div id="monthly-revenue-chart" class="apex-charts" dir="ltr"></div>
            </div>
        </div>
    </div>


    <!-- Modals for Shortcuts/Details (Hidden by default) -->
    @include('layout.partials.dashboard-modals')

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

        // 1. Multi-Series Area Chart (High Fidelity)
        const monthlyOptions = {
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                },
                fontFamily: 'Inter, sans-serif',
                background: 'transparent'
            },
            series: [{
                    name: 'Net Profit',
                    data: [31, 40, 28, 51, 42, 109, 100, 95, 80, 75, 50, 60] // Placeholder or mix with real
                },
                {
                    name: 'Gross Revenue',
                    data: [11, 32, 45, 32, 34, 52, 41, 45, 30, 25, 20, 15] // Placeholder
                },
                {
                    name: 'Projected',
                    data: [45, 50, 55, 60, 65, 70, 75, 80, 85, 90, 95, 100] // Placeholder
                }
            ],
            // Orange, Blue, Teal
            colors: ['#f97316', '#3b82f6', '#14b8a6'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.6,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                labels: {
                    style: {
                        colors: '#94a3b8'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#94a3b8'
                    }
                }
            },
            grid: {
                borderColor: '#e2e8f0', // Light grid
                strokeDashArray: 4,
            },
            theme: {
                mode: 'light' // Light mode
            },
            tooltip: {
                theme: 'light'
            },
            legend: {
                show: false
            } // Custom legend built in HTML
        };

        // Remove old renderer calls if they exist or just overwrite container
        if (document.querySelector("#monthly-revenue-chart")) {
            new ApexCharts(document.querySelector("#monthly-revenue-chart"), monthlyOptions).render();
        }

        // Clean up unused charts code from previous implementation
        // ... (We can leave them or remove)

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
                    className: (c.className || '') + (['capital_amount', 'full_loan_amount', 'total_outstanding', 'this_week_not_paid', 'total_arrears', 'current_week_pending', 'penalty_balance', 'Amount', 'Total_Loan_Amount'].includes(c.data) ? ' text-end' : '')
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

    function showTotalOutstandingModal() {
        $('#totalOutstandingModal').modal('show');
        loadTableData('#totalOutstandingModal', '/total-outstanding-data', '#outstanding_table_modal', [{
                data: 'loan_id',
                title: 'Loan ID'
            },
            {
                data: 'customer_id',
                title: 'Cus ID'
            },
            {
                data: 'customer_name',
                title: 'Name'
            },
            {
                data: 'capital_amount',
                title: 'Capital',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'full_loan_amount',
                title: 'Full Amount',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'total_outstanding',
                title: 'Total Outstanding',
                render: $.fn.dataTable.render.number(',', '.', 2)
            }
        ]);
    }

    function showWeeklyNotPaidModal() {
        $('#weeklyNotPaidModal').modal('show');
        loadTableData('#weeklyNotPaidModal', '/weekly-not-paid-data', '#weekly_not_paid_table_modal', [{
                data: 'loan_id',
                title: 'Loan ID'
            },
            {
                data: 'center_name',
                title: 'Center'
            },
            {
                data: 'customer_id',
                title: 'Cus ID'
            },
            {
                data: 'customer_name',
                title: 'Name'
            },
            {
                data: 'capital_amount',
                title: 'Capital',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'full_loan_amount',
                title: 'Full Amount',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'this_week_not_paid',
                title: 'Unpaid',
                render: $.fn.dataTable.render.number(',', '.', 2),
                className: 'text-danger'
            },
            {
                data: 'total_arrears',
                title: 'Total Arrears',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'not_paid_installment_count',
                title: 'Count',
                className: 'text-center'
            }
        ]);
    }

    function showCurrentWeekPendingModal() {
        $('#currentWeekPendingModal').modal('show');
        loadTableData('#currentWeekPendingModal', '/current-week-pending-data', '#current_week_pending_table_modal', [{
                data: 'loan_id',
                title: 'Loan ID'
            },
            {
                data: 'center_name',
                title: 'Center'
            },
            {
                data: 'customer_id',
                title: 'Cus ID'
            },
            {
                data: 'customer_name',
                title: 'Name'
            },
            {
                data: 'capital_amount',
                title: 'Capital',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'full_loan_amount',
                title: 'Full Amount',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'current_week_pending',
                title: 'Pending',
                render: $.fn.dataTable.render.number(',', '.', 2),
                className: 'text-primary'
            },
            {
                data: 'total_arrears',
                title: 'Total Arrears',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'not_paid_installment_count',
                title: 'Count',
                className: 'text-center'
            }
        ]);
    }

    function showPenaltyBalanceModal() {
        $('#penaltyBalanceModal').modal('show');
        loadTableData('#penaltyBalanceModal', '/penalty-balance-data', '#penalty_balance_table_modal', [{
                data: 'loan_id',
                title: 'Loan ID'
            },
            {
                data: 'customer_id',
                title: 'Cus ID'
            },
            {
                data: 'customer_name',
                title: 'Name'
            },
            {
                data: 'capital_amount',
                title: 'Capital',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'full_loan_amount',
                title: 'Full Amount',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'total_outstanding',
                title: 'Outstanding',
                render: $.fn.dataTable.render.number(',', '.', 2)
            },
            {
                data: 'penalty_balance',
                title: 'Penalty',
                render: $.fn.dataTable.render.number(',', '.', 2),
                className: 'text-danger'
            }
        ]);
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
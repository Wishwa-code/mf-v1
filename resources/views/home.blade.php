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
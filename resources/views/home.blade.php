@extends('layout.admin')

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <style>
        body {
            background: #f6f9ff;
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
        .animated-card {
            animation: fadeInUp 0.8s ease forwards;
        }
        @keyframes fadeInUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
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

        .animated-dashboard {
            animation: fadeInDashboard 1s ease-in-out both;
        }
        @keyframes fadeInDashboard {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
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
                                <h2 class="mb-1">Welcome Back 👋</h2>
                                <p class="mb-0" id="live-datetime"></p>
                            </div>
                            <i class="ri-user-smile-line display-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Statistic Cards --}}
            <div class="row">
                @php
                    $cards = [
                        ['title' => 'Pending Loans', 'icon' => 'ri-eye-line', 'value' => $customer_loan_pending_Amount, 'count' => $customer_loan_pending_Count, 'link' => '/pendingloan', 'bg' => '#ff758c', 'prefix' => 'Rs.'],
                        ['title' => 'Current Loans', 'icon' => 'ri-wallet-2-line', 'value' => $customer_loan_current_Amount, 'count' => $customer_loan_current_Count, 'link' => '/payment_step_1', 'bg' => '#43cea2', 'prefix' => 'Rs.'],
                        ['title' => 'Settled Loans', 'icon' => 'ri-file-paper-2-fill', 'value' => $setteled_loan_current_Amount, 'count' => $setteled_loan_Count, 'link' => '/showsettleloan', 'bg' => '#f7971e', 'prefix' => 'Rs.'],
                        ['title' => 'Portfolio', 'icon' => 'ri-pie-chart-line', 'value' => $portfolio, 'count' => '', 'link' => '', 'bg' => '#9b59b6', 'prefix' => 'Rs.'],
                        ['title' => 'Current Month Lending', 'icon' => 'ri-calendar-line', 'value' => $currentMonthLending, 'count' => '', 'link' => '', 'bg' => '#1abc9c', 'prefix' => 'Rs.'],
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
                                            <h3><span id="stat-card-{{ $index }}"></span></h3>
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
                        ['title' => 'Today Installment', 'value' => $todayinstallment, 'color' => '#1e3c72'],
                        ['title' => 'Total Arrears', 'value' => $arrease, 'color' => '#ef473a'],
                        ['title' => 'Cheque Payments', 'value' => $checqueamount, 'color' => '#3498db'],
                        ['title' => 'Due Outstanding (Installment Due + Arrears)', 'value' => ($todayinstallment + $arrease), 'color' => '#0072ff'],
                        ['title' => 'Today Collected Amount', 'value' => $todaycollected, 'color' => '#ef803a'],
//                        ['title' => 'Total Outstanding', 'value' => ($todayinstallment + $checqueamount + $arrease), 'color' => '#01503c'],
                    ];
                @endphp

                @foreach($extra as $i => $item)
                    <div class="col-md-2 mb-4">
                        <div class="glass-card card text-white shadow animated-card" style="background-color: {{ $item['color'] }};">
                            <div class="card-body">
                                <h6 class="text-uppercase">{{ $item['title'] }}</h6>
                                <h3>LKR <span id="extra-card-{{ $i }}"></span></h3>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Combined Not-Paid Card --}}
                <div class="col-md-4 mb-4">
                    <div class="glass-card card text-white shadow animated-card" style="background: linear-gradient(135deg, #8e44ad, #2c3e50);">
                        <div class="card-body">
                            <h6 class="text-uppercase mb-3">This Week Not-Paid</h6>
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4><span id="weekly-unpaid-count"></span></h4>
                                        <small class="text-light">Installments</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4>LKR <span id="weekly-unpaid-amount"></span></h4>
                                        <small class="text-light">Amount</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Charts --}}
            <div class="row">
                <div class="col-lg-12 mb-4">
                    <div class="glass-card card shadow animated-card">
                        <div class="card-body p-0">
                            <h5 class="card-title p-3">📈 Live Currency Exchange (USD to LKR)</h5>
                            <div class="tradingview-widget-container">
                                <div id="tradingview_advanced"></div>
                            </div>
                        </div>
                    </div>
                </div>


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

                <div class="col-lg-3 mb-4">
                    <div class="glass-card card shadow animated-card">
                        <div class="card-body text-center">
                            <h5 class="card-title mb-3">📟 Profit Gauge</h5>
                            <div id="profit-gauge" style="height: 200px;"></div>
                            <h4 class="mt-3 text-success fw-bold" id="profit-display">Rs. 0</h4>
                        </div>
                    </div>
                </div>



            </div>


            @if($shortcut_count > 0)
                <div class="row mt-5">
                    <div class="col-12 mb-3">
                        <div class="glass-card card border-0 shadow-sm animated-card"
                             style="background: rgba(255,255,255,0.15); backdrop-filter: blur(6px);">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <h4 class="mb-0 text-dark fw-bold">Quick Access Shortcuts</h4>
{{--                                <i class="ri-apps-line fs-4 text-muted"></i>--}}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @php
                $colors = ['#f1c40f', '#2ecc71', '#e67e22', '#3498db', '#9b59b6', '#1abc9c', '#34495e', '#e74c3c'];
            @endphp

            <style>
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
            </style>

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
        <button id="startLoanProcess" hidden>Start Processing Loans</button>
    @endif
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.0.7/countUp.umd.js"></script>
    <script>
        // Live DateTime (Updated Every Second)
        function updateDateTime() {
            const dt = new Date();
            document.getElementById('live-datetime').innerText = dt.toLocaleString();
        }
        updateDateTime(); // initial call
        setInterval(updateDateTime, 1000); // update every second

        document.addEventListener("DOMContentLoaded", function () {
            // Live DateTime
            const dt = new Date();
            document.getElementById('live-datetime').innerText = dt.toLocaleString();

            // Animate Stat Cards
            const statValues = [
                {{ $customer_loan_pending_Amount }},
                {{ $customer_loan_current_Amount }},
                {{ $setteled_loan_current_Amount }},
                {{ $portfolio }},
                {{ $currentMonthLending }},
                {{ $customerCount }}
            ];
            const prefixes = ['Rs. ', 'Rs. ', 'Rs. ', 'Rs. ', 'Rs. ', ''];

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
                {{ $arrease }},
                {{ $checqueamount }},
                {{ $todayinstallment + $arrease }},
                {{ $todaycollected }}
            ];
            extraValues.forEach((val, i) => {
                const extraAnim = new countUp.CountUp('extra-card-' + i, val, {
                    separator: ',',
                    decimalPlaces: 2
                });
                if (!extraAnim.error) extraAnim.start();
            });

            // Animate weekly unpaid metrics
            const weeklyCountAnim = new countUp.CountUp('weekly-unpaid-count', {{ $weeklyUnpaidCount }}, {
                separator: ',',
                decimalPlaces: 0
            });
            if (!weeklyCountAnim.error) weeklyCountAnim.start();

            const weeklyAmountAnim = new countUp.CountUp('weekly-unpaid-amount', {{ $weeklyUnpaidAmount }}, {
                separator: ',',
                decimalPlaces: 2
            });
            if (!weeklyAmountAnim.error) weeklyAmountAnim.start();

            // Monthly Area Chart
            new ApexCharts(document.querySelector("#monthly-revenue-chart"), {
                chart: { type: 'area', height: 300 },
                series: [{ name: 'Payments', data: @json($monthlyData) }],
                colors: ['#8e44ad'],
                stroke: { curve: 'smooth', width: 3 },
                xaxis: { categories: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] }
            }).render();

            // Donut Chart
            // Loan Type - Pie Chart (Fixed with 4 values)
            new ApexCharts(document.querySelector("#loan-type-chart"), {
                chart: { type: 'pie', height: 300 },
                series: [{{ $customer_loan_current_Count }}, {{ $customer_loan_pending_Count }}, {{ $setteled_loan_Count }}, {{ $deleted_loan_Count }}],
                labels: ['Ongoing Loans', 'Pending Loans', 'Settle Loans', 'Delete Loans'],
                colors: ['#1abc9c', '#3498db', '#e67e22', '#e74c3c'] // 4 colors
            }).render();

            // Weekly Comparison Chart
            new ApexCharts(document.querySelector("#bar-comparison-chart"), {
                chart: { type: 'bar', height: 300 },
                series: [
                    {
                        name: 'This Week',
                        data: @json($weeklyComparison['current'])
                    },
                    {
                        name: 'Last Week',
                        data: @json($weeklyComparison['last'])
                    }
                ],
                xaxis: {
                    categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
                },
                colors: ['#1abc9c', '#e74c3c'],
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '50%'
                    }
                }
            }).render();


            // Radial Chart
            new ApexCharts(document.querySelector("#radial-progress-chart"), {
                chart: { type: 'radialBar', height: 300 },
                series: [{{ $loan_completion_percentage ?? 0 }}],
                labels: ['Completion'],
                colors: ['#f39c12'],
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: { fontSize: '18px' },
                            value: { fontSize: '32px', fontWeight: 'bold' }
                        }
                    }
                }
            }).render();



        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const profit = {{ $profit }};
            const target = {{ $profitTarget }};
            const percent = Math.min((profit / target) * 100, 100);

            new ApexCharts(document.querySelector("#profit-gauge"), {
                chart: {
                    type: 'radialBar',
                    height: 300,
                    offsetY: -20
                },
                series: [percent],
                labels: [''],
                plotOptions: {
                    radialBar: {
                        startAngle: -120,
                        endAngle: 120,
                        hollow: {
                            margin: 0,
                            size: '65%',
                            background: 'transparent',
                        },
                        track: {
                            background: '#eee',
                            strokeWidth: '100%',
                            margin: 0
                        },
                        dataLabels: {
                            show: true,
                            name: {
                                show: false
                            },
                            value: {
                                offsetY: 10,
                                fontSize: '22px',
                                color: '#2d3436',
                                formatter: function () {
                                    return percent.toFixed(1) + '%';
                                }
                            }
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'dark',
                        type: 'horizontal',
                        gradientToColors: ['#00b894'],
                        stops: [0, 50, 100]
                    }
                },
                colors: [
                    percent < 50 ? '#e74c3c' : (percent < 80 ? '#f39c12' : '#00b894')
                ]
            }).render();

            // CountUp actual Rs. value
            const animatedRs = new countUp.CountUp('profit-display', profit, {
                prefix: 'Rs. ',
                separator: ',',
                duration: 2.5
            });
            if (!animatedRs.error) animatedRs.start();
        });
    </script>

    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
    <script type="text/javascript">
        new TradingView.widget({
            "container_id": "tradingview_advanced",
            "width": "100%",
            "height": 600,
            "symbol": "FX_IDC:USDLKR",
            "interval": "15",
            "timezone": "Asia/Colombo",
            "theme": "light",
            "style": "3", // Beautiful hollow candles
            "locale": "en",
            "toolbar_bg": "#f1f3f6",
            "enable_publishing": false,
            "withdateranges": true,
            "hide_side_toolbar": false,
            "save_image": false,
            "studies": [
                "MACD@tv-basicstudies",
                "RSI@tv-basicstudies",
                "Volume@tv-basicstudies"
            ],
            "show_popup_button": true,
            "popup_width": "1000",
            "popup_height": "650"
        });
    </script>
    <script>
        $('#startLoanProcess').on('click', function () {
            $.get('/get-loan-ids', function (data) {
                const loanIds = data.loan_ids;
                const total = loanIds.length;
                let index = 0;

                // Start with a Swal loading popup
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
                            processNextLoan(); // Continue even on failure
                        }
                    });
                }
            });
        });
    </script>

@endsection

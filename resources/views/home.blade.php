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
                        ['title' => 'Today Collection', 'value' => $todayinstallment, 'color' => '#1e3c72'],
                        ['title' => 'Total Arrears', 'value' => $arrease, 'color' => '#ef473a'],
                        ['title' => 'Cheque Payments', 'value' => $checqueamount, 'color' => '#3498db'],
                        ['title' => 'Total Outstanding', 'value' => ($todayinstallment + $checqueamount + $arrease), 'color' => '#0072ff'],
                    ];
                @endphp

                @foreach($extra as $i => $item)
                    <div class="col-md-3 mb-4">
                        <div class="glass-card card text-white shadow animated-card" style="background-color: {{ $item['color'] }};">
                            <div class="card-body">
                                <h6 class="text-uppercase">{{ $item['title'] }}</h6>
                                <h3>LKR <span id="extra-card-{{ $i }}"></span></h3>
                            </div>
                        </div>
                    </div>
                @endforeach
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

                <div class="col-lg-6 mb-4">
                    <div class="glass-card card shadow animated-card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">✅ Loan Completion</h5>
                            <div id="radial-progress-chart" style="height: 300px;"></div>
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
                {{ $customerCount }}
            ];
            const prefixes = ['Rs. ', 'Rs. ', 'Rs. ', ''];

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
                {{ $todayinstallment + $arrease + $checqueamount }}
            ];
            extraValues.forEach((val, i) => {
                const extraAnim = new countUp.CountUp('extra-card-' + i, val, {
                    separator: ',',
                    decimalPlaces: 2
                });
                if (!extraAnim.error) extraAnim.start();
            });

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


            // Weekly Comparison
            new ApexCharts(document.querySelector("#bar-comparison-chart"), {
                chart: {
                    type: 'bar',
                    height: 300
                },
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
                    categories: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']
                },
                colors: ['#1abc9c', '#e74c3c'],
                plotOptions: {
                    bar: {
                        columnWidth: '55%',
                        borderRadius: 4
                    }
                }
            }).render();


        });
    </script>
@endsection

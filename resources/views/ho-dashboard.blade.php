@extends('layout.admin')

@section('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="{{ asset('css/ho-dashboard.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
    <div class="dashboard-header">
        <button class="refresh-btn" id="refreshBranches">
            <i class="ri-refresh-line"></i>Refresh
        </button>
        <h1 class="dashboard-title"><i class="ri-dashboard-3-line dashboard-title-icon"></i>Head Office Dashboard</h1>
        <p class="dashboard-subtitle">{{ date('d/m/Y, H:i:s A') }}</p>
    </div>

    @if(count($branchMetrics) === 0)
        <div class="empty">
            <h4>No active branches found.</h4>
            <p>Add branches or ensure they are active to view aggregated performance.</p>
        </div>
    @else
        <div class="branch-grid">
            @php
                $colors = ['purple', 'pink', 'blue', 'orange', 'green'];
                $index = 0;
            @endphp
            @foreach($branchMetrics as $b)
                <div class="branch-card {{ $colors[$index % count($colors)] }}" data-branch="{{ $b['id'] }}" title="Click to switch to {{ $b['name'] }} branch">
                    <div class="card-inner">
                        <!-- Front of Card -->
                        <div class="card-front">
                            <div class="branch-header">
                                <h3 class="branch-name">{{ $b['name'] }}</h3>
                                <div class="branch-icon"><i class="ri-building-line"></i></div>
                            </div>
                            
                            <div class="metrics-grid">
                                <div class="metric-card">
                                    <div class="metric-label">Customers</div>
                                    <div class="metric-value">{{ number_format($b['customers']) }}</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-label">Portfolio</div>
                                    <div class="metric-value">{{ number_format($b['portfolio'], 2) }}</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-label">Current Loans</div>
                                    <div class="metric-value">{{ number_format($b['current_loans_count']) }}</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-label">Today Due</div>
                                    <div class="metric-value">{{ number_format($b['today_installment'], 2) }}</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-label">Collected</div>
                                    <div class="metric-value">{{ number_format($b['today_collected'], 2) }}</div>
                                </div>
                                <div class="metric-card">
                                    <div class="metric-label">Arrears</div>
                                    <div class="metric-value">{{ number_format($b['arrears'], 2) }}</div>
                                </div>
                            </div>
                            
                            <div class="collection-rate">
                                <div class="collection-rate-label">Arrears %: {{ number_format((($b['portfolio'] ?? 0) > 0) ? ((($b['arrears'] ?? 0) / ($b['portfolio'] ?? 0)) * 100) : 0, 2) }}%</div>
                            </div>
                        </div>
                        
                        <!-- Back of Card -->
                        <div class="card-back">
                            <div class="switch-icon">
                                <i class="ri-arrow-left-right-line"></i>
                            </div>
                            <div class="switch-text">Switch Branch</div>
                            <div class="switch-subtitle">Click to switch to {{ $b['name'] }}</div>
                        </div>
                    </div>
                </div>
                @php $index++; @endphp
            @endforeach
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            @php
                $totalCustomers = collect($branchMetrics)->sum('customers');
                $totalPortfolio = collect($branchMetrics)->sum('portfolio');
                $totalActiveLoans = collect($branchMetrics)->sum('current_loans_count');
                $totalTodayDue = collect($branchMetrics)->sum('today_installment');
                $totalCollected = collect($branchMetrics)->sum('today_collected');
                $totalArrears = collect($branchMetrics)->sum('arrears');
            @endphp
            
            <div class="summary-card navy">
                <div class="summary-label">Total Customers</div>
                <div class="summary-value">{{ number_format($totalCustomers) }}</div>
            </div>
            
            <div class="summary-card green">
                <div class="summary-label">Total Portfolio</div>
                <div class="summary-value">{{ number_format($totalPortfolio, 2) }}</div>
            </div>
            
            <div class="summary-card blue">
                <div class="summary-label">Active Loans</div>
                <div class="summary-value">{{ number_format($totalActiveLoans) }}</div>
            </div>
            
            <div class="summary-card orange">
                <div class="summary-label">Today Due</div>
                <div class="summary-value">{{ number_format($totalTodayDue, 2) }}</div>
            </div>
            
            <div class="summary-card teal">
                <div class="summary-label">Collected Today</div>
                <div class="summary-value">{{ number_format($totalCollected, 2) }}</div>
            </div>
            
            <div class="summary-card red">
                <div class="summary-label">Total Arrears</div>
                <div class="summary-value">{{ number_format($totalArrears, 2) }}</div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('script')
<script>
    document.querySelectorAll('.branch-card').forEach(card => {
        card.addEventListener('click', () => {
            const id = card.getAttribute('data-branch');
            if(!id) return;
            fetch('/update-branch', {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
                body:JSON.stringify({branch_id:id})
            }).then(r=>{
                if(!r.ok) throw new Error('Switch failed');
                return r.json().catch(()=>({}));
            }).then(()=>{
                window.location.href='/'
            }).catch(()=>{
                alert('Failed to switch branch');
            });
        });
    });
    document.getElementById('refreshBranches').addEventListener('click',()=>window.location.reload());
</script>
@endsection

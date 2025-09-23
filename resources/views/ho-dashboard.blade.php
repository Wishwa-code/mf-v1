@extends('layout.admin')

@section('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { 
        background-color: #f8fafc;
        min-height: 100vh;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .container-fluid {
        animation: fadeIn 0.8s ease-out;
        padding: 20px;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .dashboard-header {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        text-align: center;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }

    .dashboard-subtitle {
        color: #64748b;
        font-size: 1rem;
        margin-top: 8px;
    }

    .branch-grid {
        display: grid;
        gap: 24px;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        margin-bottom: 30px;
    }
    
    .branch-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 24px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        min-height: 420px;
    }

    .branch-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
        z-index: -1;
    }
    
    .branch-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
    }

    .branch-card.purple {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .branch-card.pink {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .branch-card.blue {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .branch-card.orange {
        background: linear-gradient(135deg, #fd746c 0%, #ff9068 100%);
    }

    .branch-card.green {
        background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    }
    
    .branch-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }
    
    .branch-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: white;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .branch-icon {
        font-size: 2rem;
        opacity: 0.8;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 20px;
    }
    
    .metric-card {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
        backdrop-filter: blur(5px);
        min-height: 80px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    
    .metric-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.2;
    }
    
    .metric-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        line-height: 1.2;
        word-break: break-all;
    }

    .collection-rate {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 12px;
        text-align: center;
        margin-top: 16px;
    }

    .collection-rate-label {
        font-size: 0.85rem;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0;
        font-weight: 600;
    }

    .collection-rate-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: white;
    }

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .summary-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 25px;
        text-align: center;
    }

    .summary-card.navy { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); }
    .summary-card.green { background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); }
    .summary-card.blue { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); }
    .summary-card.orange { background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); }
    .summary-card.teal { background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%); }
    .summary-card.red { background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%); }

    .summary-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: white;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    .empty {
        text-align: center;
        padding: 80px 40px;
        background: #ffffff;
        border: 1px dashed #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .empty h4 {
        color: #475569;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .empty p {
        color: #64748b;
        margin: 0;
    }

    .refresh-btn {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #4a5568;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: absolute;
        top: 30px;
        right: 30px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .refresh-btn:hover {
        background: #f7fafc;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <button class="refresh-btn" id="refreshBranches">
        <i class="ri-refresh-line"></i>Refresh
    </button>
    
    <div class="dashboard-header">
        <h1 class="dashboard-title">📊 Head Office Dashboard</h1>
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
                    <div class="branch-header">
                        <h3 class="branch-name">{{ $b['name'] }}</h3>
                        <div class="branch-icon">🏢</div>
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
                        <div class="collection-rate-label">Collection Rate: 0.0%</div>
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

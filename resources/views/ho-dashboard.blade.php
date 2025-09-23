@extends('layout.admin')

@section('head')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --card-shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.25);
        --glass-bg: rgba(255, 255, 255, 0.15);
        --glass-border: rgba(255, 255, 255, 0.2);
        --text-white-primary: white;
        --text-white-secondary: rgba(255, 255, 255, 0.9);
        --text-white-tertiary: rgba(255, 255, 255, 0.8);
    }

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
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        border: 1px solid #c7d2fe;
        border-radius: 32px;
        padding: 60px 40px;
        margin-bottom: 40px;
        text-align: center;
        box-shadow: 0 15px 35px rgba(99, 102, 241, 0.2);
        position: relative;
        min-height: 180px;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        animation: headerFloat 6s ease-in-out infinite;
        cursor: pointer;
        overflow: hidden;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transform: translateX(-100%) translateY(-100%) rotate(45deg);
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        pointer-events: none;
    }

    .dashboard-header:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(99, 102, 241, 0.3);
        border-color: #a5b4fc;
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 50%, #ddd6fe 100%);
    }

    .dashboard-header:hover::before {
        transform: translateX(100%) translateY(100%) rotate(45deg);
    }

    @keyframes headerFloat {
        0%, 100% {
            transform: translateY(0px) rotate(0deg);
        }
        25% {
            transform: translateY(-5px) rotate(0.5deg);
        }
        50% {
            transform: translateY(-3px) rotate(0deg);
        }
        75% {
            transform: translateY(-7px) rotate(-0.5deg);
        }
    }

    .dashboard-title {
        font-size: 3.5rem;
        font-weight: 800;
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 0;
        position: relative;
        z-index: 1;
        letter-spacing: -0.02em;
        line-height: 1.1;
        transition: var(--primary-transition);
        transform-origin: center;
    }

    .dashboard-header:hover .dashboard-title {
        transform: scale(1.05);
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4f46e5 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: 0.01em;
    }

    .dashboard-title i {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-right: 20px;
        font-size: 3.2rem;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        display: inline-block;
        transform-origin: center;
    }

    .dashboard-header:hover .dashboard-title i {
        transform: rotate(360deg) scale(1.1);
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #ec4899 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .dashboard-subtitle {
        color: #4338ca;
        font-size: 1.3rem;
        font-weight: 600;
        margin-top: 16px;
        position: relative;
        z-index: 1;
        transition: var(--primary-transition);
        transform-origin: center;
    }

    .dashboard-header:hover .dashboard-subtitle {
        color: #312e81;
        transform: translateY(-3px);
        font-size: 1.35rem;
    }

    .branch-grid {
        display: grid;
        gap: 24px;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        margin-bottom: 30px;
    }
    
    .branch-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.6s ease;
        position: relative;
        overflow: hidden;
        height: 450px;
        transform-style: preserve-3d;
        perspective: 1000px;
    }

    .card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        transition: transform 0.6s ease;
        transform-style: preserve-3d;
    }

    .branch-card:hover .card-inner {
        transform: rotateY(180deg);
    }

    .card-front, .card-back {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 20px;
        padding: 24px;
        box-sizing: border-box;
    }

    .card-front {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .card-back {
        transform: rotateY(180deg);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(20px);
    }

    .branch-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-shrink: 0;
    }
    
    .branch-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--text-white-primary);
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .branch-icon {
        font-size: 1.8rem;
        opacity: 0.8;
        color: var(--text-white-secondary);
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        flex: 1;
        margin-bottom: 16px;
    }
    
    .metric-card {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 12px;
        text-align: center;
        backdrop-filter: blur(5px);
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 70px;
    }
    
    .metric-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-white-secondary);
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.2;
    }
    
    .metric-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-white-primary);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        line-height: 1.2;
        word-break: break-word;
    }

    .collection-rate {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 10px;
        text-align: center;
        flex-shrink: 0;
    }

    .collection-rate-label {
        font-size: 0.8rem;
        color: var(--text-white-secondary);
        margin: 0;
        font-weight: 600;
    }

    .switch-text {
        color: var(--text-white-primary);
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 12px;
        text-align: center;
    }

    .switch-icon {
        color: var(--text-white-primary);
        font-size: 3rem;
        margin-bottom: 16px;
        animation: switchRotate 2s ease-in-out infinite;
    }

    @keyframes switchRotate {
        0%, 100% { transform: rotate(0deg); }
        50% { transform: rotate(180deg); }
    }

    .switch-subtitle {
        color: var(--text-white-tertiary);
        font-size: 1rem;
        text-align: center;
        font-weight: 500;
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
    
    .branch-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--card-shadow-hover);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }

    .summary-card {
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
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
        color: var(--text-white-tertiary);
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-value {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text-white-primary);
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
        border: 1px solid #a5b4fc;
        color: #4338ca;
        padding: 8px 16px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: var(--primary-transition);
        position: absolute;
        top: 20px;
        right: 30px;
        z-index: 2;
        min-width: 90px;
        justify-content: center;
        box-shadow: 0 2px 8px rgba(67, 56, 202, 0.15);
        transform-origin: center;
    }

    .refresh-btn:hover {
        background: #f0f4ff;
        border-color: #8b5cf6;
        color: #312e81;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(67, 56, 202, 0.3);
    }

    .refresh-btn i {
        font-size: 14px;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    .refresh-btn:hover i {
        transform: rotate(360deg) scale(1.2);
    }

    .dashboard-header:hover .refresh-btn {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(67, 56, 202, 0.3);
    }

    .dashboard-title-icon {
        margin-right: 12px;
    }
</style>
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
                                <div class="collection-rate-label">Collection Rate: 0.0%</div>
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

@extends('layouts.dashboard')
@section('title', 'Profit & Loss - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <div class="header-top">
            <div><h1 class="page-title">Profit & Loss Statement</h1><p class="page-subtitle">{{ $from }} to {{ $to }}</p></div>
            <div class="header-actions">
                <a href="{{ route('accounting.reports.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Reports</a>
            </div>
        </div>
    </div>
    <form method="GET" action="{{ route('accounting.reports.profit-loss') }}" style="display:flex;gap:16px;align-items:end;margin-bottom:30px;flex-wrap:wrap;">
        <div class="form-group" style="flex:1;min-width:150px;"><label>From</label><input type="date" name="from" value="{{ $from }}"></div>
        <div class="form-group" style="flex:1;min-width:150px;"><label>To</label><input type="date" name="to" value="{{ $to }}"></div>
        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Generate</button>
    </form>
    <div class="stats-grid" style="margin-bottom:30px;">
        <div class="stat-card"><div class="stat-icon" style="background:#28a745;"><i class="fas fa-arrow-down"></i></div><div class="stat-info"><h3>Rs. {{ number_format($revenue, 2) }}</h3><p>Total Revenue</p></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#dc3545;"><i class="fas fa-arrow-up"></i></div><div class="stat-info"><h3>Rs. {{ number_format($expenses, 2) }}</h3><p>Total Expenses</p></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:{{ $netProfit >= 0 ? '#28a745' : '#dc3545' }};"><i class="fas fa-balance-scale"></i></div><div class="stat-info"><h3>Rs. {{ number_format($netProfit, 2) }}</h3><p>{{ $netProfit >= 0 ? 'Net Profit' : 'Net Loss' }}</p></div></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:30px;">
        <div class="dashboard-card">
            <div class="card-header"><h3><i class="fas fa-chart-bar"></i> Monthly Revenue vs Expenses</h3></div>
            <div class="card-body">
                @php $allMonths = collect(array_keys($revenueByMonth->toArray() + $expensesByMonth->toArray()))->sort(); @endphp
                @forelse($allMonths as $month)
                @php
                    $rev = $revenueByMonth[$month] ?? 0;
                    $exp = $expensesByMonth[$month] ?? 0;
                @endphp
                <div style="margin-bottom:16px;">
                    <div style="display:flex;justify-content:space-between;font-size:var(--fs-1-2);color:#6c757d;margin-bottom:4px;">
                        <span>{{ $month }}</span><span>Rs. {{ number_format($rev - $exp, 2) }}</span>
                    </div>
                    <div style="display:flex;gap:4px;height:20px;">
                        <div style="flex:{{ $rev }};background:#28a745;border-radius:4px;min-width:{{ $rev > 0 ? '4px' : '0' }};height:100%;"></div>
                        <div style="flex:{{ $exp }};background:#dc3545;border-radius:4px;min-width:{{ $exp > 0 ? '4px' : '0' }};height:100%;"></div>
                    </div>
                </div>
                @empty
                <p style="color:#6c757d;">No data for this period.</p>
                @endforelse
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-header"><h3><i class="fas fa-pie-chart"></i> Expenses by Category</h3></div>
            <div class="card-body">
                @php $totalExpCat = $expensesByCategory->sum(); @endphp
                @forelse($expensesByCategory as $cat => $amt)
                @php $pct = $totalExpCat > 0 ? round(($amt / $totalExpCat) * 100) : 0; @endphp
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                    <div style="min-width:120px;font-size:var(--fs-1-3);color:#333;">{{ $cat }}</div>
                    <div style="flex:1;height:24px;background:#f0f0f0;border-radius:12px;overflow:hidden;">
                        <div style="height:100%;width:{{ $pct }}%;background:#CD2737;border-radius:12px;transition:width .5s;"></div>
                    </div>
                    <div style="min-width:100px;text-align:right;font-size:var(--fs-1-3);font-weight:600;">Rs. {{ number_format($amt, 2) }}</div>
                </div>
                @empty
                <p style="color:#6c757d;">No expenses for this period.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
<style>
.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:var(--fs-2-2);font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:var(--fs-1-3)}.form-group{display:flex;flex-direction:column;gap:6px}.form-group label{font-weight:600;color:#1a1a2e;font-size:var(--fs-1-3)}.form-group input{padding:10px 14px;border:2px solid #e9ecef;border-radius:8px;font-size:var(--fs-1-4);font-family:'Noto Sans Devanagari',sans-serif;background:#f8f9fa}.form-group input:focus{outline:none;border-color:#CD2737;background:white;box-shadow:0 0 0 3px rgba(205,39,55,0.1)}.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px}.stat-card{background:white;border-radius:14px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:var(--fs-1-8);flex-shrink:0}.stat-info h3{font-size:var(--fs-2);font-weight:700;color:#1a1a2e;margin:0 0 2px 0}.stat-info p{color:#6c757d;font-size:var(--fs-1-3);margin:0}.dashboard-card{background:white;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden}.card-header{padding:20px 24px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center}.card-header h3{font-size:var(--fs-1-5);font-weight:600;color:#1a1a2e;margin:0;display:flex;align-items:center;gap:8px}.card-header h3 i{color:#CD2737}.card-body{padding:20px 24px}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:var(--fs-1-4);font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}@media(max-width:768px){.dashboard-content{padding:20px}}
</style>
@endsection

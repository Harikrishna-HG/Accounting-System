@extends('layouts.dashboard')
@section('title', 'Accounting Dashboard')
@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <div class="header-top">
            <div>
                <h1 class="page-title">Accounting Dashboard</h1>
                <p class="page-subtitle">Financial overview and summary</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('accounting.invoices.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Invoice
                </a>
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #28a745;">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-info">
                <h3>Rs. {{ number_format($totalRevenue, 2) }}</h3>
                <p>Total Revenue</p>
            </div>
            <div class="stat-change up"><i class="fas fa-arrow-up"></i> Revenue</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #dc3545;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3>Rs. {{ number_format($totalDue, 2) }}</h3>
                <p>Total Due</p>
            </div>
            <div class="stat-change" style="color:#dc3545;"><i class="fas fa-arrow-down"></i> Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #ffc107;">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
                <h3>Rs. {{ number_format($totalExpenses, 2) }}</h3>
                <p>Total Expenses</p>
            </div>
            <div class="stat-change"><i class="fas fa-clock"></i> Spent</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #0d6efd;">
                <i class="fas fa-boxes"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalProducts }}</h3>
                <p>Products</p>
            </div>
            <div class="stat-change"><i class="fas fa-box"></i> Inventory</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #6f42c1;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalClients }}</h3>
                <p>Clients</p>
            </div>
            <div class="stat-change"><i class="fas fa-user"></i> Active</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fd7e14;">
                <i class="fas fa-truck"></i>
            </div>
            <div class="stat-info">
                <h3>{{ $totalSuppliers }}</h3>
                <p>Suppliers</p>
            </div>
            <div class="stat-change"><i class="fas fa-building"></i> Partners</div>
        </div>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-file-invoice"></i> Recent Invoices</h3>
                <a href="{{ route('accounting.invoices.index') }}" class="card-link">All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="card-body">
                @if($recentInvoices->count() > 0)
                    <table class="news-table">
                        <thead><tr><th>Invoice#</th><th>Client</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            @foreach($recentInvoices as $inv)
                            <tr>
                                <td><a href="{{ route('accounting.invoices.show', $inv) }}" style="color:#CD2737;">{{ $inv->invoice_number }}</a></td>
                                <td>{{ $inv->client_name }}</td>
                                <td>Rs. {{ number_format($inv->total, 2) }}</td>
                                <td>
                                    @if($inv->status == 'paid') <span class="badge badge-success">Paid</span>
                                    @elseif($inv->status == 'partial') <span class="badge badge-warning">Partial</span>
                                    @else <span class="badge badge-danger">Unpaid</span>
                                    @endif
                                </td>
                                <td>{{ $inv->invoice_date->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state"><i class="fas fa-file-invoice"></i><p>No invoices yet.</p></div>
                @endif
            </div>
        </div>
        <div class="dashboard-card">
            <div class="card-header">
                <h3><i class="fas fa-chart-line"></i> This Month</h3>
            </div>
            <div class="card-body">
                <div style="display:grid;gap:20px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px;background:#e8f5e9;border-radius:12px;">
                        <div><strong style="font-size:1.4rem;color:#1a1a2e;">Revenue</strong><br><small style="color:#6c757d;">This month</small></div>
                        <div style="font-size:2rem;font-weight:700;color:#28a745;">Rs. {{ number_format($monthlyRevenue, 2) }}</div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px;background:#fff3cd;border-radius:12px;">
                        <div><strong style="font-size:1.4rem;color:#1a1a2e;">Expenses</strong><br><small style="color:#6c757d;">This month</small></div>
                        <div style="font-size:2rem;font-weight:700;color:#dc3545;">Rs. {{ number_format($monthlyExpenses, 2) }}</div>
                    </div>
                    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px;background:#e3f2fd;border-radius:12px;">
                        <div><strong style="font-size:1.4rem;color:#1a1a2e;">Net Profit</strong><br><small style="color:#6c757d;">This month</small></div>
                        <div style="font-size:2rem;font-weight:700;color:{{ $monthlyRevenue - $monthlyExpenses >= 0 ? '#28a745' : '#dc3545' }};">
                            Rs. {{ number_format($monthlyRevenue - $monthlyExpenses, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.dashboard-content { padding: 30px; }
.page-header { margin-bottom: 30px; }
.header-top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
.page-title { font-size: 2.2rem; font-weight: 700; color: #1a1a2e; margin: 0; }
.page-subtitle { color: #6c757d; margin: 5px 0 0 0; font-size: 1.3rem; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: white; border-radius: 14px; padding: 20px; display: flex; align-items: center; gap: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); position: relative; }
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
.stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.8rem; flex-shrink: 0; }
.stat-info h3 { font-size: 2.0rem; font-weight: 700; color: #1a1a2e; margin: 0 0 2px 0; }
.stat-info p { color: #6c757d; font-size: 1.3rem; margin: 0; }
.stat-change { position: absolute; top: 12px; right: 12px; font-size: 1.1rem; padding: 2px 8px; border-radius: 20px; background: #f8f9fa; color: #6c757d; }
.stat-change.up { color: #28a745; background: #e8f5e9; }
.dashboard-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; }
@media (max-width: 1024px) { .dashboard-grid { grid-template-columns: 1fr; } }
.dashboard-card { background: white; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden; }
.card-header { padding: 20px 24px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
.card-header h3 { font-size: 1.5rem; font-weight: 600; color: #1a1a2e; margin: 0; display: flex; align-items: center; gap: 8px; }
.card-header h3 i { color: #CD2737; }
.card-link { color: #CD2737; text-decoration: none; font-size: 1.3rem; font-weight: 500; display: flex; align-items: center; gap: 4px; }
.card-link:hover { gap: 8px; }
.card-body { padding: 16px 20px; }
.news-table { width: 100%; border-collapse: collapse; }
.news-table th { text-align: left; font-size: 1.2rem; font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; padding: 10px 8px; border-bottom: 2px solid #f0f0f0; }
.news-table td { padding: 10px 8px; border-bottom: 1px solid #f8f9fa; font-size: 1.3rem; color: #333; }
.news-table tr:last-child td { border-bottom: none; }
.news-table tr:hover td { background: #fafafa; }
.empty-state { text-align: center; padding: 30px 20px; color: #6c757d; }
.empty-state i { font-size: 3.6rem; color: #dee2e6; margin-bottom: 12px; }
.badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 1.1rem; font-weight: 600; }
.badge-success { background: #d4edda; color: #155724; }
.badge-danger { background: #f8d7da; color: #721c24; }
.badge-warning { background: #fff3cd; color: #856404; }
.badge-info { background: #d1ecf1; color: #0c5460; }
.btn { padding: 10px 22px; border: none; border-radius: 8px; font-size: 1.4rem; font-weight: 600; cursor: pointer; font-family: 'Noto Sans Devanagari', sans-serif; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-primary { background: #CD2737; color: white; box-shadow: 0 4px 15px rgba(205,39,55,0.3); }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(205,39,55,0.4); }
@media (max-width: 768px) { .dashboard-content { padding: 20px; } .stats-grid { grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); } }
</style>
@endsection

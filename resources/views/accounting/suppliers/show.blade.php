@extends('layouts.dashboard')
@section('title', 'Supplier Details - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <div class="header-top">
            <div><h1 class="page-title">{{ $supplier->name }}</h1><p class="page-subtitle">Supplier details and history</p></div>
            <div class="header-actions">
                <a href="{{ route('accounting.suppliers.edit', $supplier) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('accounting.suppliers.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-card"><div class="info-label">Email</div><div class="info-value">{{ $supplier->email ?? '—' }}</div></div>
        <div class="info-card"><div class="info-label">Phone</div><div class="info-value">{{ $supplier->phone ?? '—' }}</div></div>
        <div class="info-card"><div class="info-label">Address</div><div class="info-value">{{ $supplier->address ?? '—' }}</div></div>
        <div class="info-card"><div class="info-label">Company</div><div class="info-value">{{ $supplier->company ?? '—' }}</div></div>
        <div class="info-card"><div class="info-label">PAN/VAT</div><div class="info-value">{{ $supplier->pan_vat ?? '—' }}</div></div>
        <div class="info-card"><div class="info-label">Status</div><div class="info-value">@if($supplier->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Inactive</span>@endif</div></div>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:30px;">
        <div class="stat-card"><div class="stat-icon" style="background:#28a745;"><i class="fas fa-wallet"></i></div><div class="stat-info"><h3>Rs. {{ number_format($supplier->total_purchases, 2) }}</h3><p>Total Purchases</p></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:{{ $supplier->balance > 0 ? '#dc3545' : '#28a745' }};"><i class="fas fa-balance-scale"></i></div><div class="stat-info"><h3>Rs. {{ number_format($supplier->balance, 2) }}</h3><p>Balance</p></div></div>
    </div>
    <div class="dashboard-card" style="margin-bottom:30px;">
        <div class="card-header"><h3><i class="fas fa-clipboard-list"></i> Purchase Orders</h3></div>
        <div class="card-body">
            <table class="news-table">
                <thead><tr><th>Order#</th><th>Date</th><th>Expected</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($purchaseOrders as $po)
                    <tr>
                        <td><a href="{{ route('accounting.purchase-orders.edit', $po) }}" style="color:#CD2737;">{{ $po->order_number }}</a></td>
                        <td>{{ $po->order_date->format('Y-m-d') }}</td>
                        <td>{{ $po->expected_date?->format('Y-m-d') ?? '—' }}</td>
                        <td>Rs. {{ number_format($po->total, 2) }}</td>
                        <td>@if($po->status=='received')<span class="badge badge-success">Received</span>@elseif($po->status=='pending')<span class="badge badge-warning">Pending</span>@elseif($po->status=='cancelled')<span class="badge badge-danger">Cancelled</span>@else<span class="badge badge-info">{{ ucfirst($po->status) }}</span>@endif</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:20px;color:#6c757d;">No purchase orders for this supplier.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination-wrapper">{{ $purchaseOrders->links() }}</div>
        </div>
    </div>
    <div class="dashboard-card">
        <div class="card-header"><h3><i class="fas fa-shopping-cart"></i> Expenses</h3></div>
        <div class="card-body">
            <table class="news-table">
                <thead><tr><th>Expense#</th><th>Date</th><th>Category</th><th>Amount</th></tr></thead>
                <tbody>
                    @forelse($expenses as $exp)
                    <tr>
                        <td><a href="{{ route('accounting.expenses.edit', $exp) }}" style="color:#CD2737;">{{ $exp->expense_number }}</a></td>
                        <td>{{ $exp->expense_date->format('Y-m-d') }}</td>
                        <td>{{ $exp->category }}</td>
                        <td>Rs. {{ number_format($exp->amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;padding:20px;color:#6c757d;">No expenses for this supplier.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="pagination-wrapper">{{ $expenses->links() }}</div>
        </div>
    </div>
</div>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:30px}.info-card{background:white;border-radius:12px;padding:16px 20px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.info-label{font-size:1.1rem;color:#6c757d;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px}.info-value{font-size:1.4rem;font-weight:600;color:#1a1a2e}.stat-card{background:white;border-radius:14px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:1.8rem;flex-shrink:0}.stat-info h3{font-size:2rem;font-weight:700;color:#1a1a2e;margin:0 0 2px 0}.stat-info p{color:#6c757d;font-size:1.3rem;margin:0}.dashboard-card{background:white;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden}.card-header{padding:20px 24px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center}.card-header h3{font-size:1.5rem;font-weight:600;color:#1a1a2e;margin:0;display:flex;align-items:center;gap:8px}.card-header h3 i{color:#CD2737}.card-body{padding:16px 20px}.news-table{width:100%;border-collapse:collapse}.news-table th{text-align:left;font-size:1.2rem;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:0.5px;padding:10px 8px;border-bottom:2px solid #f0f0f0}.news-table td{padding:10px 8px;border-bottom:1px solid #f8f9fa;font-size:1.3rem;color:#333}.news-table a{text-decoration:none;font-weight:500}.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:1.1rem;font-weight:600}.badge-success{background:#d4edda;color:#155724}.badge-danger{background:#f8d7da;color:#721c24}.badge-warning{background:#fff3cd;color:#856404}.badge-info{background:#d1ecf1;color:#0c5460}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:1.4rem;font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}</style>
@endsection

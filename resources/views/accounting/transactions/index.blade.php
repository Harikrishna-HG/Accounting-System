@extends('layouts.dashboard')
@section('title', 'Ledger - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">General Ledger</h1><p class="page-subtitle">All financial transactions</p></div></div></div>
    <div class="stats-grid" style="margin-bottom:20px;">
        <div class="stat-card"><div class="stat-icon" style="background:#28a745;"><i class="fas fa-arrow-down"></i></div><div class="stat-info"><h3>Rs. {{ number_format($totalDebit, 2) }}</h3><p>Total Debit (Incoming)</p></div></div>
        <div class="stat-card"><div class="stat-icon" style="background:#dc3545;"><i class="fas fa-arrow-up"></i></div><div class="stat-info"><h3>Rs. {{ number_format($totalCredit, 2) }}</h3><p>Total Credit (Outgoing)</p></div></div>
    </div>
    <div class="list-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Transaction#</th><th>Type</th><th>Category</th><th>Description</th><th>Debit</th><th>Credit</th><th>Balance</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td><strong>{{ $t->transaction_number }}</strong></td>
                        <td><span class="type-badge">{{ ucfirst($t->type) }}</span></td>
                        <td>{{ $t->category }}</td>
                        <td>{{ Str::limit($t->description, 50) ?? '—' }}</td>
                        <td style="color:#28a745;font-weight:600;">@if($t->debit > 0)Rs. {{ number_format($t->debit, 2) }}@else—@endif</td>
                        <td style="color:#dc3545;font-weight:600;">@if($t->credit > 0)Rs. {{ number_format($t->credit, 2) }}@else—@endif</td>
                        <td>Rs. {{ number_format($t->balance, 2) }}</td>
                        <td>{{ $t->transaction_date->format('Y-m-d') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:#6c757d;"><i class="fas fa-book" style="font-size:3.6rem;display:block;margin-bottom:12px;color:#dee2e6;"></i>No transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $transactions->links() }}</div>
    </div>
</div>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px}.stat-card{background:white;border-radius:14px;padding:20px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;color:white;font-size:1.8rem;flex-shrink:0}.stat-info h3{font-size:2rem;font-weight:700;color:#1a1a2e;margin:0 0 2px 0}.stat-info p{color:#6c757d;font-size:1.3rem;margin:0}.list-card{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden}.table-responsive{overflow-x:auto}.data-table{width:100%;border-collapse:collapse}.data-table th{text-align:left;padding:10px 12px;font-size:1.2rem;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:0.5px;background:#fafafa;border-bottom:1px solid #f0f0f0}.data-table td{padding:10px 12px;border-bottom:1px solid #f8f9fa;font-size:1.3rem;color:#333;vertical-align:middle}.type-badge{background:#e3f2fd;color:#1976d2;padding:3px 10px;border-radius:12px;font-size:1.1rem;font-weight:500}.pagination-wrapper{padding:12px 20px;border-top:1px solid #f0f0f0}</style>
@endsection

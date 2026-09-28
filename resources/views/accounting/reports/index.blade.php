@extends('layouts.dashboard')
@section('title', 'Reports - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">Financial Reports</h1><p class="page-subtitle">Accounting reports and analysis</p></div></div></div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;">
        <a href="{{ route('accounting.reports.profit-loss') }}" class="dashboard-card" style="text-decoration:none;color:inherit;cursor:pointer;transition:transform .2s,box-shadow .2s;">
            <div class="card-header"><h3><i class="fas fa-chart-line" style="color:#28a745;"></i> Profit & Loss</h3></div>
            <div class="card-body">
                <p style="color:#6c757d;font-size:1.3rem;">View profit and loss statement for any period. Track revenue, expenses, and net profit.</p>
                <div style="display:flex;gap:16px;margin-top:16px;">
                    <div style="flex:1;padding:12px;background:#e8f5e9;border-radius:8px;text-align:center;"><strong style="color:#28a745;">Revenue</strong></div>
                    <div style="flex:1;padding:12px;background:#fff3cd;border-radius:8px;text-align:center;"><strong style="color:#dc3545;">Expenses</strong></div>
                    <div style="flex:1;padding:12px;background:#e3f2fd;border-radius:8px;text-align:center;"><strong style="color:#1976d2;">Net</strong></div>
                </div>
            </div>
        </a>
        <a href="{{ route('accounting.transactions.index') }}" class="dashboard-card" style="text-decoration:none;color:inherit;cursor:pointer;">
            <div class="card-header"><h3><i class="fas fa-book" style="color:#6f42c1;"></i> General Ledger</h3></div>
            <div class="card-body">
                <p style="color:#6c757d;font-size:1.3rem;">View all financial transactions with debit/credit entries and running balance.</p>
                <div style="display:flex;gap:16px;margin-top:16px;">
                    <div style="flex:1;padding:12px;background:#e8f5e9;border-radius:8px;text-align:center;"><strong style="color:#28a745;">Debits</strong></div>
                    <div style="flex:1;padding:12px;background:#fce4ec;border-radius:8px;text-align:center;"><strong style="color:#dc3545;">Credits</strong></div>
                </div>
            </div>
        </a>
        <a href="{{ route('accounting.invoices.index') }}" class="dashboard-card" style="text-decoration:none;color:inherit;cursor:pointer;">
            <div class="card-header"><h3><i class="fas fa-file-invoice" style="color:#CD2737;"></i> Invoices</h3></div>
            <div class="card-body">
                <p style="color:#6c757d;font-size:1.3rem;">View all invoices, track paid/unpaid status, and manage billing.</p>
            </div>
        </a>
        <a href="{{ route('accounting.expenses.index') }}" class="dashboard-card" style="text-decoration:none;color:inherit;cursor:pointer;">
            <div class="card-header"><h3><i class="fas fa-shopping-cart" style="color:#fd7e14;"></i> Expenses</h3></div>
            <div class="card-body">
                <p style="color:#6c757d;font-size:1.3rem;">Track and categorize all business expenses.</p>
            </div>
        </a>
    </div>
</div>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}.dashboard-card{background:white;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden}.dashboard-card:hover{transform:translateY(-4px);box-shadow:0 8px 25px rgba(0,0,0,0.1)}.card-header{padding:20px 24px;border-bottom:1px solid #f0f0f0}.card-header h3{font-size:1.5rem;font-weight:600;color:#1a1a2e;margin:0;display:flex;align-items:center;gap:8px}.card-body{padding:20px 24px}</style>
@endsection

@extends('layouts.dashboard')
@section('title', 'Expense Details - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">Expense Details</h1><p class="page-subtitle">Expense #{{ $expense->expense_number }}</p></div><div class="header-actions"><a href="{{ route('accounting.expenses.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a><a href="{{ route('accounting.expenses.edit', $expense) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a></div></div></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="form-card">
        <div class="detail-grid">
            <div class="detail-row"><label>Expense #</label><span class="value">{{ $expense->expense_number }}</span></div>
            <div class="detail-row"><label>Category</label><span class="value"><span class="type-badge">{{ $expense->category }}</span></span></div>
            <div class="detail-row"><label>Amount</label><span class="value" style="font-weight:700;font-size:var(--fs-1-8);color:#dc3545;">Rs. {{ number_format($expense->amount, 2) }}</span></div>
            <div class="detail-row"><label>Expense Date</label><span class="value">{{ $expense->expense_date->format('Y-m-d') }}</span></div>
            <div class="detail-row"><label>Payment Method</label><span class="value">{{ ucfirst($expense->payment_method ?? 'N/A') }}</span></div>
            <div class="detail-row"><label>Reference</label><span class="value">{{ $expense->reference ?? 'â€”' }}</span></div>
            <div class="detail-row"><label>Supplier</label><span class="value">{{ $expense->supplier?->name ?? 'N/A' }}</span></div>
            <div class="detail-row"><label>Receipt</label><span class="value">@if($expense->receipt)<a href="{{ asset('storage/'.$expense->receipt) }}" target="_blank" class="btn btn-sm btn-primary"><i class="fas fa-file"></i> View Receipt</a>@else No receipt @endif</span></div>
            <div class="detail-row full-width"><label>Description</label><span class="value">{{ $expense->description ?? 'No description' }}</span></div>
            <div class="detail-row full-width"><label>Notes</label><span class="value">{{ $expense->notes ?? 'No notes' }}</span></div>
        </div>
    </div>
</div>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:var(--fs-2-2);font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:var(--fs-1-3)}.form-card{background:white;border-radius:16px;padding:40px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.detail-row label{display:block;font-weight:600;color:#6c757d;font-size:var(--fs-1-2);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px}.detail-row .value{font-size:var(--fs-1-5);color:#1a1a2e;font-weight:500}.type-badge{background:#fce4ec;color:#CD2737;padding:3px 10px;border-radius:12px;font-size:var(--fs-1-1);font-weight:500}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:var(--fs-1-4);font-weight:600;cursor:pointer;transition:all 0.3s;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}.btn-sm{padding:6px 12px;font-size:var(--fs-1-2)}.alert{padding:12px 16px;border-radius:8px;font-size:var(--fs-1-3);font-weight:500;margin-bottom:20px}.alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb}</style>
@endsection

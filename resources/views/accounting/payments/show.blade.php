@extends('layouts.dashboard')
@section('title', 'Payment Details - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">Payment Details</h1><p class="page-subtitle">Payment #{{ $payment->payment_number }}</p></div><div class="header-actions"><a href="{{ route('accounting.payments.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a><a href="{{ route('accounting.payments.edit', $payment) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a></div></div></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="form-card">
        <div class="detail-grid">
            <div class="detail-row"><label>Payment #</label><span class="value">{{ $payment->payment_number }}</span></div>
            <div class="detail-row"><label>Type</label><span class="value"><span class="badge {{ $payment->type === 'incoming' ? 'badge-success' : 'badge-danger' }}">{{ ucfirst($payment->type) }}</span></span></div>
            <div class="detail-row"><label>Amount</label><span class="value" style="font-weight:700;font-size:1.8rem;">Rs. {{ number_format($payment->amount, 2) }}</span></div>
            <div class="detail-row"><label>Payment Method</label><span class="value">{{ ucfirst($payment->payment_method ?? 'N/A') }}</span></div>
            <div class="detail-row"><label>Reference</label><span class="value">{{ $payment->reference ?? '—' }}</span></div>
            <div class="detail-row"><label>Payment Date</label><span class="value">{{ $payment->payment_date->format('Y-m-d') }}</span></div>
            <div class="detail-row"><label>Invoice</label><span class="value">@if($payment->invoice)<a href="{{ route('accounting.invoices.show', $payment->invoice) }}">{{ $payment->invoice->invoice_number }}</a>@else N/A @endif</span></div>
            <div class="detail-row"><label>Client</label><span class="value">@if($payment->client){{ $payment->client->name }}@elseif($payment->invoice && $payment->invoice->client){{ $payment->invoice->client->name }}@else Walk-in @endif</span></div>
            <div class="detail-row full-width"><label>Notes</label><span class="value">{{ $payment->notes ?? 'No notes' }}</span></div>
            <div class="detail-row full-width"><label>Created</label><span class="value">{{ $payment->created_at->format('Y-m-d H:i:s') }}</span></div>
        </div>
    </div>
</div>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}.form-card{background:white;border-radius:16px;padding:40px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}.detail-row.full-width{grid-column:1/-1}.detail-row label{display:block;font-weight:600;color:#6c757d;font-size:1.2rem;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px}.detail-row .value{font-size:1.5rem;color:#1a1a2e;font-weight:500}.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:1.1rem;font-weight:600}.badge-success{background:#d4edda;color:#155724}.badge-danger{background:#f8d7da;color:#721c24}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:1.4rem;font-weight:600;cursor:pointer;transition:all 0.3s;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}.alert{padding:12px 16px;border-radius:8px;font-size:1.3rem;font-weight:500;margin-bottom:20px}.alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb}</style>
@endsection

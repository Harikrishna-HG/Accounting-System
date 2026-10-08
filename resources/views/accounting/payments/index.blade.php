@extends('layouts.dashboard')
@section('title', 'Payments - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">Payments</h1><p class="page-subtitle">Track incoming and outgoing payments</p></div><div class="header-actions"><a href="{{ route('accounting.payments.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Record Payment</a></div></div></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="list-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Payment#</th><th>Invoice</th><th>Type</th><th>Method</th><th>Amount</th><th>Date</th><th>Reference</th><th>Actions</th></tr></thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr>
                        <td><strong>{{ $p->payment_number }}</strong></td>
                        <td>{{ $p->invoice?->invoice_number ?? '—' }}</td>
                        <td>@if($p->type=='incoming')<span class="badge badge-success">Incoming</span>@else<span class="badge badge-danger">Outgoing</span>@endif</td>
                        <td>{{ ucfirst($p->payment_method ?? 'N/A') }}</td>
                        <td style="font-weight:600;">Rs. {{ number_format($p->amount, 2) }}</td>
                        <td>{{ $p->payment_date->format('Y-m-d') }}</td>
                        <td>{{ $p->reference ?? '—' }}</td>
                        <td class="actions-cell"><div class="action-btns">
                            <a href="{{ route('accounting.payments.show', $p) }}" class="action-btn view"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('accounting.payments.edit', $p) }}" class="action-btn edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('accounting.payments.destroy', $p) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this payment?')">@csrf @method('DELETE')<button type="submit" class="action-btn delete"><i class="fas fa-trash"></i></button></form>
                        </div></td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:#6c757d;"><i class="fas fa-credit-card" style="font-size:var(--fs-3-6);display:block;margin-bottom:12px;color:#dee2e6;"></i>No payments recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $payments->links() }}</div>
    </div>
</div>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:var(--fs-2-2);font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:var(--fs-1-3)}.alert{padding:10px 16px;border-radius:8px;margin-bottom:20px;font-size:var(--fs-1-3)}.alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb}.list-card{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden}.table-responsive{overflow-x:auto}.data-table{width:100%;border-collapse:collapse}.data-table th{text-align:left;padding:10px 12px;font-size:var(--fs-1-2);font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:0.5px;background:#fafafa;border-bottom:1px solid #f0f0f0}.data-table td{padding:10px 12px;border-bottom:1px solid #f8f9fa;font-size:var(--fs-1-3);color:#333;vertical-align:middle}.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:var(--fs-1-1);font-weight:600}.badge-success{background:#d4edda;color:#155724}.badge-danger{background:#f8d7da;color:#721c24}.badge-warning{background:#fff3cd;color:#856404}.actions-cell{white-space:nowrap}.action-btns{display:flex;gap:4px}.action-btn{width:32px;height:32px;border:none;border-radius:6px;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;font-size:var(--fs-1-3);text-decoration:none;transition:all .2s}.action-btn.edit{background:#e3f2fd;color:#1976d2}.action-btn.edit:hover{background:#1976d2;color:white}.action-btn.delete{background:#fce4ec;color:#CD2737}.action-btn.delete:hover{background:#CD2737;color:white}.action-btn.view{background:#e8f5e9;color:#28a745}.action-btn.view:hover{background:#28a745;color:white}.pagination-wrapper{padding:12px 20px;border-top:1px solid #f0f0f0}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:var(--fs-1-4);font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}</style>
@endsection

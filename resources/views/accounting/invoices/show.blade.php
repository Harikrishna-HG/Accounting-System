@extends('layouts.dashboard')
@section('title', 'Invoice ' . $invoice->invoice_number)
@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <div class="header-top">
            <div><h1 class="page-title">Invoice {{ $invoice->invoice_number }}</h1><p class="page-subtitle">Invoice details</p></div>
            <div class="header-actions">
                <a href="{{ route('accounting.invoices.edit', $invoice) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('accounting.invoices.print', $invoice) }}" class="btn btn-secondary" target="_blank"><i class="fas fa-print"></i> Print</a>
                <a href="{{ route('accounting.invoices.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>
    <div class="form-card" style="padding:30px;">
        <div style="display:flex;justify-content:space-between;margin-bottom:30px;flex-wrap:wrap;gap:20px;">
            <div>
                <h2 style="font-size:2rem;font-weight:700;color:#1a1a2e;margin:0 0 4px;">INVOICE</h2>
                <p style="color:#6c757d;font-size:1.3rem;">{{ $invoice->invoice_number }}</p>
            </div>
            <div style="text-align:right;">
                <p style="font-size:1.3rem;color:#6c757d;"><strong>Date:</strong> {{ $invoice->invoice_date->format('Y-m-d') }}</p>
                @if($invoice->due_date)<p style="font-size:1.3rem;color:#6c757d;"><strong>Due:</strong> {{ $invoice->due_date->format('Y-m-d') }}</p>@endif
                <p style="font-size:1.3rem;">
                    @if($invoice->status=='paid')<span class="badge badge-success" style="font-size:1.3rem;padding:6px 16px;">PAID</span>
                    @elseif($invoice->status=='partial')<span class="badge badge-warning" style="font-size:1.3rem;padding:6px 16px;">PARTIAL</span>
                    @elseif($invoice->status=='cancelled')<span class="badge badge-danger" style="font-size:1.3rem;padding:6px 16px;">CANCELLED</span>
                    @else<span class="badge badge-danger" style="font-size:1.3rem;padding:6px 16px;">UNPAID</span>@endif
                </p>
            </div>
        </div>
        <div style="padding:16px;background:#f8f9fa;border-radius:12px;margin-bottom:24px;">
            <p style="font-size:1.4rem;font-weight:600;color:#1a1a2e;margin:0 0 8px;">Bill To:</p>
            <p style="font-size:1.3rem;color:#333;margin:0;">{{ $invoice->client_name }}</p>
            @if($invoice->client_phone)<p style="font-size:1.3rem;color:#333;margin:0;">{{ $invoice->client_phone }}</p>@endif
            @if($invoice->client_address)<p style="font-size:1.3rem;color:#333;margin:0;">{{ $invoice->client_address }}</p>@endif
            @if($invoice->client_pan)<p style="font-size:1.3rem;color:#333;margin:0;">PAN: {{ $invoice->client_pan }}</p>@endif
        </div>
        <table style="width:100%;border-collapse:collapse;margin-bottom:24px;">
            <thead>
                <tr style="background:#f8f9fa;">
                    <th style="padding:12px;text-align:left;font-size:1.2rem;font-weight:600;color:#6c757d;text-transform:uppercase;border-bottom:2px solid #f0f0f0;">Item</th>
                    <th style="padding:12px;text-align:center;font-size:1.2rem;font-weight:600;color:#6c757d;text-transform:uppercase;border-bottom:2px solid #f0f0f0;">Qty</th>
                    <th style="padding:12px;text-align:right;font-size:1.2rem;font-weight:600;color:#6c757d;text-transform:uppercase;border-bottom:2px solid #f0f0f0;">Price</th>
                    <th style="padding:12px;text-align:right;font-size:1.2rem;font-weight:600;color:#6c757d;text-transform:uppercase;border-bottom:2px solid #f0f0f0;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td style="padding:10px 12px;border-bottom:1px solid #f8f9fa;font-size:1.3rem;">{{ $item->product_name }}</td>
                    <td style="padding:10px 12px;border-bottom:1px solid #f8f9fa;font-size:1.3rem;text-align:center;">{{ $item->quantity }}</td>
                    <td style="padding:10px 12px;border-bottom:1px solid #f8f9fa;font-size:1.3rem;text-align:right;">Rs. {{ number_format($item->price, 2) }}</td>
                    <td style="padding:10px 12px;border-bottom:1px solid #f8f9fa;font-size:1.3rem;text-align:right;">Rs. {{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div style="text-align:right;padding:16px;background:#f8f9fa;border-radius:12px;">
            <div style="display:flex;justify-content:flex-end;gap:40px;flex-wrap:wrap;">
                <div><strong style="font-size:1.4rem;color:#1a1a2e;">Subtotal:</strong> <span style="font-size:1.6rem;">Rs. {{ number_format($invoice->subtotal, 2) }}</span></div>
                @if($invoice->discount > 0)<div><strong style="font-size:1.4rem;color:#1a1a2e;">Discount:</strong> <span style="font-size:1.6rem;color:#dc3545;">-Rs. {{ number_format($invoice->discount, 2) }}</span></div>@endif
                @if($invoice->tax > 0)<div><strong style="font-size:1.4rem;color:#1a1a2e;">Tax ({{ strtoupper($invoice->tax_type) }}):</strong> <span style="font-size:1.6rem;">Rs. {{ number_format($invoice->tax, 2) }}</span></div>@endif
                <div><strong style="font-size:1.6rem;color:#1a1a2e;">Total:</strong> <span style="font-size:2rem;font-weight:700;color:#CD2737;">Rs. {{ number_format($invoice->total, 2) }}</span></div>
            </div>
            <div style="margin-top:12px;display:flex;justify-content:flex-end;gap:40px;flex-wrap:wrap;">
                <div><strong style="font-size:1.4rem;color:#28a745;">Paid:</strong> <span style="font-size:1.6rem;color:#28a745;">Rs. {{ number_format($invoice->paid_amount, 2) }}</span></div>
                <div><strong style="font-size:1.4rem;color:#dc3545;">Due:</strong> <span style="font-size:1.6rem;color:#dc3545;">Rs. {{ number_format($invoice->due_amount, 2) }}</span></div>
            </div>
        </div>
        @if($invoice->notes)
        <div style="margin-top:20px;padding:12px;background:#f8f9fa;border-radius:8px;">
            <p style="font-size:1.3rem;color:#6c757d;margin:0;"><strong>Notes:</strong> {{ $invoice->notes }}</p>
        </div>
        @endif
    </div>
    @if($invoice->payments->count() > 0)
    <div class="dashboard-card" style="margin-top:30px;">
        <div class="card-header"><h3><i class="fas fa-credit-card"></i> Payments</h3></div>
        <div class="card-body">
            <table class="news-table">
                <thead><tr><th>Payment#</th><th>Date</th><th>Method</th><th>Reference</th><th>Amount</th></tr></thead>
                <tbody>
                    @foreach($invoice->payments as $p)
                    <tr>
                        <td>{{ $p->payment_number }}</td>
                        <td>{{ $p->payment_date->format('Y-m-d') }}</td>
                        <td>{{ ucfirst($p->payment_method ?? 'N/A') }}</td>
                        <td>{{ $p->reference ?? '—' }}</td>
                        <td style="color:#28a745;font-weight:600;">Rs. {{ number_format($p->amount, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}.form-card{background:white;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:1.1rem;font-weight:600}.badge-success{background:#d4edda;color:#155724}.badge-danger{background:#f8d7da;color:#721c24}.badge-warning{background:#fff3cd;color:#856404}.dashboard-card{background:white;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden}.card-header{padding:20px 24px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center}.card-header h3{font-size:1.5rem;font-weight:600;color:#1a1a2e;margin:0;display:flex;align-items:center;gap:8px}.card-header h3 i{color:#CD2737}.card-body{padding:16px 20px}.news-table{width:100%;border-collapse:collapse}.news-table th{text-align:left;font-size:1.2rem;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:0.5px;padding:10px 8px;border-bottom:2px solid #f0f0f0}.news-table td{padding:10px 8px;border-bottom:1px solid #f8f9fa;font-size:1.3rem;color:#333}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:1.4rem;font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}</style>
@endsection

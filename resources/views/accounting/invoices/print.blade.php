<!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Invoice {{ $invoice->invoice_number }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Mukta','DejaVu Sans',sans-serif;font-size:12px;color:#333;padding:40px;line-height:1.5}
.company-header{text-align:center;margin-bottom:16px}
.company-header h1{font-size:22px;color:#CD2737;margin:0 0 4px}
.company-header p{font-size:11px;color:#666;margin:0}
.invoice-header{display:flex;justify-content:space-between;gap:24px;border-top:2px solid #CD2737;padding-top:16px;margin-bottom:24px}
.bill-to p{margin:2px 0;font-size:12px}
.bill-to strong{font-size:13px;color:#1a1a2e}
.invoice-title h2{font-size:24px;color:#1a1a2e;margin:0 0 4px;text-align:right}
.invoice-title p{font-size:11px;color:#666;margin:0;text-align:right}
table{width:100%;border-collapse:collapse;margin-bottom:24px}
th{background:#f8f9fa;padding:10px 12px;text-align:left;font-size:11px;font-weight:600;color:#6c757d;text-transform:uppercase;border-bottom:2px solid #CD2737}
td{padding:10px 12px;border-bottom:1px solid #eee;font-size:12px}
tr:last-child td{border-bottom:none}
.text-center{text-align:center}
.text-right{text-align:right}
.amounts{width:48%;margin-left:auto}
.amounts th,.amounts td{border:1px solid #d9d9d9;padding:8px 12px}
.amounts .grand td{font-weight:700;color:#CD2737;font-size:13px;background:#fdf2f4}
.amounts .due td{font-weight:700}
.status-badge{display:inline-block;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700}
.paid{background:#d4edda;color:#155724}
.unpaid{background:#f8d7da;color:#721c24}
.partial{background:#fff3cd;color:#856404}
.notes{margin-top:20px;padding:12px;background:#f8f9fa;border-radius:8px;font-size:11px;color:#666}
.footer{margin-top:40px;text-align:center;font-size:10px;color:#999;border-top:1px solid #eee;padding-top:20px}
@media print{body{padding:20px}.no-print{display:none}}

/* The px sizes above are the printed invoice and must not move: paper has no
   viewport. On a screen the same file is read at 390px or 1920px, so sizes
   come from the shared fluid scale instead, and the header is allowed to wrap.
   Nothing here applies in print, so the printed sheet is unchanged. */
@media screen{
@include('layouts._type-scale')
html{font-size:78.125%}
body{font-size:var(--fs-1-2);padding:24px}
.company-header h1{font-size:var(--fs-2)}
.company-header p{font-size:var(--fs-1-15)}
.invoice-title h2{font-size:var(--fs-2-2)}
.invoice-title p{font-size:var(--fs-1-15)}
.bill-to p{font-size:var(--fs-1-2)}
.bill-to strong{font-size:var(--fs-1-3)}
th{font-size:var(--fs-1-15)}
td{font-size:var(--fs-1-2)}
.amounts .grand td{font-size:var(--fs-1-4)}
.status-badge{font-size:var(--fs-1-15)}
.notes{font-size:var(--fs-1-15)}
.footer{font-size:var(--fs-1-1)}
}
@media screen and (max-width:640px){
body{padding:16px}
.invoice-header{flex-direction:column;gap:12px}
.invoice-title,.invoice-title h2,.invoice-title p{text-align:left}
.amounts{width:100%}
table{display:block;overflow-x:auto;white-space:nowrap}
}
</style></head>
<body>
<div class="company-header">
    <h1>{{ config('accounting.business.name') }}</h1>
    <p>{{ config('accounting.business.address') }}</p>
    @if(config('accounting.business.phone'))<p>Phone: {{ config('accounting.business.phone') }}</p>@endif
    @if(config('accounting.business.email'))<p>Email: {{ config('accounting.business.email') }}</p>@endif
    @if(config('accounting.business.pan'))<p>PAN: {{ config('accounting.business.pan') }}</p>@endif
</div>
<div class="invoice-header">
    <div class="bill-to">
        <p><strong>Bill To:</strong></p>
        <p>{{ $invoice->client_name }}</p>
        @if($invoice->client_phone)<p>{{ $invoice->client_phone }}</p>@endif
        @if($invoice->client_address)<p>{{ $invoice->client_address }}</p>@endif
        @if($invoice->client_pan)<p>PAN: {{ $invoice->client_pan }}</p>@endif
    </div>
    <div class="invoice-title">
        <h2>INVOICE</h2>
        <p>{{ $invoice->invoice_number }}</p>
        <p>Date: {{ $invoice->invoice_date->format('Y-m-d') }}</p>
        @if($invoice->due_date)<p>Due: {{ $invoice->due_date->format('Y-m-d') }}</p>@endif
        <p><span class="status-badge {{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span></p>
    </div>
</div>
<table>
    <thead><tr><th>#</th><th>Item</th><th class="text-center">Qty</th><th class="text-right">Price</th><th class="text-right">Total</th></tr></thead>
    <tbody>
        @foreach($invoice->items as $i=>$item)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $item->product_name }}</td>
            <td class="text-center">{{ $item->quantity }}</td>
            <td class="text-right">Rs. {{ number_format($item->price, 2) }}</td>
            <td class="text-right">Rs. {{ number_format($item->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<table class="amounts">
    <thead><tr><th>Description</th><th class="text-right">Amount (Rs.)</th></tr></thead>
    <tbody>
        <tr><td>Subtotal</td><td class="text-right">{{ number_format($invoice->subtotal, 2) }}</td></tr>
        @if($invoice->discount > 0)<tr><td>Discount</td><td class="text-right">- {{ number_format($invoice->discount, 2) }}</td></tr>@endif
        @if($invoice->tax > 0)<tr><td>Tax ({{ strtoupper($invoice->tax_type) }})</td><td class="text-right">{{ number_format($invoice->tax, 2) }}</td></tr>@endif
        <tr class="grand"><td>Total</td><td class="text-right">{{ number_format($invoice->total, 2) }}</td></tr>
        <tr><td>Paid</td><td class="text-right">{{ number_format($invoice->paid_amount, 2) }}</td></tr>
        <tr class="due"><td>Due</td><td class="text-right">{{ number_format($invoice->due_amount, 2) }}</td></tr>
    </tbody>
</table>
@if($invoice->notes)<div class="notes"><strong>Notes:</strong> {{ $invoice->notes }}</div>@endif
<div class="footer">This is a computer-generated invoice. Thank you for your business!</div>
</body></html>

<!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Invoice {{ $invoice->invoice_number }}</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DejaVu Sans','Noto Sans Devanagari',sans-serif;font-size:12px;color:#333;padding:40px;line-height:1.5}
.invoice-header{display:flex;justify-content:space-between;margin-bottom:30px;border-bottom:2px solid #CD2737;padding-bottom:20px}
.company-info h1{font-size:22px;color:#CD2737;margin:0 0 4px}
.company-info p{font-size:11px;color:#666;margin:0}
.invoice-title h2{font-size:24px;color:#1a1a2e;margin:0 0 4px;text-align:right}
.invoice-title p{font-size:11px;color:#666;margin:0;text-align:right}
.bill-to{padding:16px;background:#f8f9fa;border-radius:8px;margin-bottom:24px}
.bill-to p{margin:2px 0;font-size:12px}
.bill-to strong{font-size:13px;color:#1a1a2e}
table{width:100%;border-collapse:collapse;margin-bottom:24px}
th{background:#f8f9fa;padding:10px 12px;text-align:left;font-size:11px;font-weight:600;color:#6c757d;text-transform:uppercase;border-bottom:2px solid #CD2737}
td{padding:10px 12px;border-bottom:1px solid #eee;font-size:12px}
tr:last-child td{border-bottom:none}
.text-center{text-align:center}
.text-right{text-align:right}
.totals{padding:16px;background:#f8f9fa;border-radius:8px;text-align:right}
.totals div{margin:4px 0}
.totals .total{font-size:18px;font-weight:700;color:#CD2737}
.totals .grand-total{font-size:14px;font-weight:600}
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
.invoice-header{flex-wrap:wrap;gap:16px}
.company-info h1{font-size:var(--fs-2)}
.company-info p{font-size:var(--fs-1-15)}
.invoice-title h2{font-size:var(--fs-2-2)}
.invoice-title p{font-size:var(--fs-1-15)}
.bill-to p{font-size:var(--fs-1-2)}
.bill-to strong{font-size:var(--fs-1-3)}
th{font-size:var(--fs-1-15)}
td{font-size:var(--fs-1-2)}
.totals .total{font-size:var(--fs-1-8)}
.totals .grand-total{font-size:var(--fs-1-4)}
.status-badge{font-size:var(--fs-1-15)}
.notes{font-size:var(--fs-1-15)}
.footer{font-size:var(--fs-1-1)}
}
@media screen and (max-width:640px){
body{padding:16px}
.invoice-header{flex-direction:column;gap:12px}
.invoice-title,.invoice-title h2,.invoice-title p{text-align:left}
.bill-to,.totals{padding:12px}
table{display:block;overflow-x:auto;white-space:nowrap}
}
</style></head>
<body>
<div class="invoice-header">
    <div class="company-info">
        <h1>Accounting Management</h1>
        <p>Nepal</p>
        <p>Phone: 01-4XXXXXX | Email: info@accounting.com</p>
        <p>PAN: XXXXXXXXX</p>
    </div>
    <div class="invoice-title">
        <h2>INVOICE</h2>
        <p>{{ $invoice->invoice_number }}</p>
        <p>Date: {{ $invoice->invoice_date->format('Y-m-d') }}</p>
        @if($invoice->due_date)<p>Due: {{ $invoice->due_date->format('Y-m-d') }}</p>@endif
        <p><span class="status-badge {{ $invoice->status }}">{{ strtoupper($invoice->status) }}</span></p>
    </div>
</div>
<div class="bill-to">
    <p><strong>Bill To:</strong></p>
    <p>{{ $invoice->client_name }}</p>
    @if($invoice->client_phone)<p>{{ $invoice->client_phone }}</p>@endif
    @if($invoice->client_address)<p>{{ $invoice->client_address }}</p>@endif
    @if($invoice->client_pan)<p>PAN: {{ $invoice->client_pan }}</p>@endif
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
<div class="totals">
    <div>Subtotal: Rs. {{ number_format($invoice->subtotal, 2) }}</div>
    @if($invoice->discount > 0)<div>Discount: -Rs. {{ number_format($invoice->discount, 2) }}</div>@endif
    @if($invoice->tax > 0)<div>Tax ({{ strtoupper($invoice->tax_type) }}): Rs. {{ number_format($invoice->tax, 2) }}</div>@endif
    <div class="grand-total">Total: Rs. {{ number_format($invoice->total, 2) }}</div>
    <div style="margin-top:8px;border-top:1px solid #ddd;padding-top:8px;">
        <div style="color:#28a745;">Paid: Rs. {{ number_format($invoice->paid_amount, 2) }}</div>
        <div class="total">Due: Rs. {{ number_format($invoice->due_amount, 2) }}</div>
    </div>
</div>
@if($invoice->notes)<div class="notes"><strong>Notes:</strong> {{ $invoice->notes }}</div>@endif
<div class="footer">This is a computer-generated invoice. Thank you for your business!</div>
</body></html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page { margin: 30px 34px; }
        * { box-sizing: border-box; }
        body { font-family: mukta; font-size: 11px; color: #263238; line-height: 1.45; }
        table { width: 100%; border-collapse: collapse; }
        .accent { color: #b42332; }
        .muted { color: #667085; }
        .right { text-align: right; }
        .center { text-align: center; }
        .bold { font-weight: bold; }
        .company-header { border-top: 4px solid #b42332; }
        .company-header td { padding: 13px 0 12px; text-align: center; }
        .company-name { font-size: 21px; font-weight: bold; color: #182230; }
        .company-line { font-size: 10px; color: #667085; }
        .rule { border-bottom: 1px solid #d0d5dd; margin: 0 0 17px; }
        .invoice-title { font-size: 23px; font-weight: bold; letter-spacing: 2px; color: #182230; }
        .invoice-number { font-size: 12px; font-weight: bold; color: #b42332; }
        .section-label { margin-bottom: 5px; font-size: 9px; font-weight: bold; letter-spacing: 1px; color: #667085; text-transform: uppercase; }
        .details td { vertical-align: top; }
        .bill-to { padding: 10px 12px; border: 1px solid #eaecf0; }
        .meta { padding: 0 0 0 12px; }
        .meta-row { padding: 2px 0; }
        .meta-label { display: inline-block; width: 88px; color: #667085; }
        .status { margin-top: 6px; padding: 3px 8px; display: inline-block; border: 1px solid #d0d5dd; font-size: 9px; font-weight: bold; color: #344054; }
        .items { margin-top: 18px; }
        .items th { padding: 8px 9px; background: #f2f4f7; border-top: 1px solid #d0d5dd; border-bottom: 1px solid #d0d5dd; text-align: left; font-size: 9px; font-weight: bold; color: #475467; text-transform: uppercase; }
        .items td { padding: 8px 9px; border-bottom: 1px solid #eaecf0; font-size: 11px; }
        .items tr { page-break-inside: avoid; }
        .amounts th { padding: 6px 9px; background: #f2f4f7; border: 1px solid #d0d5dd; text-align: left; font-size: 9px; color: #475467; text-transform: uppercase; }
        .amounts td { padding: 6px 9px; border: 1px solid #eaecf0; font-size: 10px; }
        .amounts .grand td { background: #fbf1f2; border-color: #e8c6ca; font-size: 12px; font-weight: bold; color: #9d1c2a; }
        .amounts .due td { font-weight: bold; }
        .notes { margin-top: 15px; padding: 9px 11px; border-left: 3px solid #b42332; background: #f8fafc; font-size: 10px; color: #475467; }
        .signatures { margin-top: 35px; }
        .signature-cell { width: 50%; padding-top: 24px; }
        .signature-line { width: 160px; border-top: 1px solid #98a2b3; padding-top: 5px; color: #667085; font-size: 9px; }
        .footer { margin-top: 24px; border-top: 1px solid #eaecf0; padding-top: 9px; text-align: center; font-size: 9px; color: #667085; }
    </style>
</head>
<body>

    <table class="company-header">
        <tr>
            <td>
                <div class="company-name">{{ $business['name'] }}</div>
                @if($business['address'])<div class="company-line">{{ $business['address'] }}</div>@endif
                @if($business['phone'])<div class="company-line">Phone: {{ $business['phone'] }}</div>@endif
                @if($business['email'])<div class="company-line">Email: {{ $business['email'] }}</div>@endif
                @if($business['pan'])<div class="company-line">PAN No.: {{ $business['pan'] }}</div>@endif
            </td>
        </tr>
    </table>

    <div class="rule"></div>

    <table class="details">
        <tr>
            <td style="width: 55%;">
                <div class="bill-to">
                    <div class="section-label">Bill To</div>
                    <div class="bold" style="font-size: 13px;">{{ $invoice->client_name }}</div>
                    @if($invoice->client_phone)<div class="muted">Phone: {{ $invoice->client_phone }}</div>@endif
                    @if($invoice->client_address)<div class="muted">{{ $invoice->client_address }}</div>@endif
                    @if($invoice->client_pan)<div class="muted">PAN / VAT No.: {{ $invoice->client_pan }}</div>@endif
                </div>
            </td>
            <td style="width: 45%;" class="right meta">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <div class="meta-row"><span class="meta-label">Date (AD)</span>{{ $invoice->invoice_date->format('Y-m-d') }}</div>
                <div class="meta-row"><span class="meta-label">मिति (BS)</span>{{ \App\Support\NepaliDate::format($invoice->invoice_date) }}</div>
                @if($invoice->due_date)
                    <div class="meta-row"><span class="meta-label">Due (AD)</span>{{ $invoice->due_date->format('Y-m-d') }}</div>
                    <div class="meta-row"><span class="meta-label">बुझाउने मिति</span>{{ \App\Support\NepaliDate::format($invoice->due_date) }}</div>
                @endif
                <div class="status">{{ strtoupper($invoice->status) }}</div>
            </td>
        </tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th>Description</th>
                <th class="center" style="width: 12%;">Qty</th>
                <th class="right" style="width: 20%;">Rate (NPR)</th>
                <th class="right" style="width: 22%;">Amount (NPR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="right">Rs. {{ number_format($item->price, 2) }}</td>
                    <td class="right">Rs. {{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="margin-top: 14px;">
        <tr>
            <td style="width: 52%; vertical-align: top;">
                @if($invoice->payment_method)
                    <div class="section-label">Payment Method</div>
                    <div>{{ ucfirst($invoice->payment_method) }}</div>
                @endif
            </td>
            <td style="width: 48%; vertical-align: top;">
                <table class="amounts">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="right">Amount (NPR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Subtotal</td>
                            <td class="right">{{ number_format($invoice->subtotal, 2) }}</td>
                        </tr>
                        @if($invoice->discount > 0)
                            <tr>
                                <td>Discount</td>
                                <td class="right">- {{ number_format($invoice->discount, 2) }}</td>
                            </tr>
                        @endif
                        @if($invoice->tax > 0)
                            <tr>
                                <td>Tax ({{ strtoupper($invoice->tax_type) }})</td>
                                <td class="right">{{ number_format($invoice->tax, 2) }}</td>
                            </tr>
                        @endif
                        <tr class="grand">
                            <td>Total</td>
                            <td class="right">Rs. {{ number_format($invoice->total, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Paid</td>
                            <td class="right">{{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="due">
                            <td>Balance Due</td>
                            <td class="right">Rs. {{ number_format($invoice->due_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    @if($invoice->notes)
        <div class="notes"><span class="bold">Notes:</span> {{ $invoice->notes }}</div>
    @endif

    <table class="signatures">
        <tr>
            <td class="signature-cell"><div class="signature-line">Customer's Signature</div></td>
            <td class="signature-cell"><div class="signature-line right" style="margin-left: auto;">For {{ $business['name'] }} - Authorized Signature</div></td>
        </tr>
    </table>

    <div class="footer">Thank you for your business. This invoice is issued in Nepalese Rupees (NPR).</div>

</body>
</html>

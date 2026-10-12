<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Invoice {{ $invoice->invoice_number }}</title>
<style>
    @page { margin: 30px 34px; }
    * { box-sizing: border-box; }
    body { font-family: mukta; font-size: 12px; color: #333333; line-height: 1.45; }
    table { width: 100%; border-collapse: collapse; }
    .accent { color: #CD2737; }
    .muted { color: #6c757d; }
    .right { text-align: right; }
    .center { text-align: center; }
    .bold { font-weight: bold; }
    .company-header { text-align: center; }
    .company-name { font-size: 22px; font-weight: bold; }
    .company-line { font-size: 11px; color: #555555; }
    .invoice-title { font-size: 24px; font-weight: bold; letter-spacing: 3px; color: #1a1a2e; }
    .rule { border-bottom: 2px solid #CD2737; margin: 12px 0 16px; }
    .section-label { font-size: 10px; font-weight: bold; letter-spacing: 1px; color: #6c757d; text-transform: uppercase; }
    .items th { background: #f8f9fa; padding: 8px 10px; text-align: left; font-size: 11px; color: #6c757d; text-transform: uppercase; border-bottom: 2px solid #CD2737; }
    .items td { padding: 8px 10px; border-bottom: 1px solid #eeeeee; font-size: 12px; }
    .amounts th { background: #f8f9fa; border: 1px solid #d9d9d9; padding: 6px 10px; text-align: left; font-size: 11px; color: #6c757d; text-transform: uppercase; }
    .amounts td { border: 1px solid #d9d9d9; padding: 6px 10px; font-size: 12px; }
    .amounts .grand td { background: #fdf2f4; font-weight: bold; color: #CD2737; font-size: 13px; }
    .amounts .due td { font-weight: bold; }
    .notes { background: #f8f9fa; padding: 10px 12px; font-size: 11px; color: #555555; }
    .footer { margin-top: 26px; border-top: 1px solid #eeeeee; padding-top: 12px; text-align: center; font-size: 10px; color: #999999; }
</style>
</head>
<body>

    <table class="company-header">
        <tr>
            <td style="text-align: center;">
                <div class="company-name accent">{{ $business['name'] }}</div>
                @if($business['address'])<div class="company-line">{{ $business['address'] }}</div>@endif
                @if($business['phone'])<div class="company-line">Phone: {{ $business['phone'] }}</div>@endif
                @if($business['email'])<div class="company-line">Email: {{ $business['email'] }}</div>@endif
                @if($business['pan'])<div class="company-line">PAN: {{ $business['pan'] }}</div>@endif
            </td>
        </tr>
    </table>

    <div class="rule"></div>

    <table>
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <div class="section-label">Bill To</div>
                <div class="bold" style="font-size: 13px;">{{ $invoice->client_name }}</div>
                @if($invoice->client_phone)<div class="muted">{{ $invoice->client_phone }}</div>@endif
                @if($invoice->client_address)<div class="muted">{{ $invoice->client_address }}</div>@endif
                @if($invoice->client_pan)<div class="muted">PAN: {{ $invoice->client_pan }}</div>@endif
            </td>
            <td style="width: 40%; vertical-align: top;" class="right">
                <div class="invoice-title">INVOICE</div>
                <div class="bold">{{ $invoice->invoice_number }}</div>
                <div class="muted">Date: {{ $invoice->invoice_date->format('Y-m-d') }}</div>
                <div class="muted">मिति: {{ \App\Support\NepaliDate::format($invoice->invoice_date) }}</div>
                @if($invoice->due_date)
                    <div class="muted">Due: {{ $invoice->due_date->format('Y-m-d') }}</div>
                    <div class="muted">बुझाउने मिति: {{ \App\Support\NepaliDate::format($invoice->due_date) }}</div>
                @endif
                <div class="bold">Status: {{ strtoupper($invoice->status) }}</div>
            </td>
        </tr>
    </table>

    <table class="items" style="margin-top: 16px;">
        <thead>
            <tr>
                <th style="width: 6%;">#</th>
                <th>Item</th>
                <th class="center" style="width: 12%;">Qty</th>
                <th class="right" style="width: 20%;">Price</th>
                <th class="right" style="width: 22%;">Total</th>
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

    <table style="margin-top: 12px;">
        <tr>
            <td style="width: 52%;"></td>
            <td style="width: 48%; vertical-align: top;">
                <table class="amounts">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th class="right">Amount (Rs.)</th>
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
                            <td class="right">{{ number_format($invoice->total, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Paid</td>
                            <td class="right">{{ number_format($invoice->paid_amount, 2) }}</td>
                        </tr>
                        <tr class="due">
                            <td>Due</td>
                            <td class="right">{{ number_format($invoice->due_amount, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    @if($invoice->notes)
        <div class="notes" style="margin-top: 14px;"><span class="bold">Notes:</span> {{ $invoice->notes }}</div>
    @endif

    <div class="footer">This is a computer-generated invoice. Thank you for your business!</div>

</body>
</html>

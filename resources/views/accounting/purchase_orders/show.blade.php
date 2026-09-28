@extends('layouts.dashboard')
@section('title', 'Purchase Order Details - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">Purchase Order Details</h1><p class="page-subtitle">{{ $purchaseOrder->order_number }}</p></div><div class="header-actions"><a href="{{ route('accounting.purchase-orders.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a><a href="{{ route('accounting.purchase-orders.edit', $purchaseOrder) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a></div></div></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    <div class="form-card">
        <div class="detail-grid">
            <div class="detail-row"><label>Order #</label><span class="value">{{ $purchaseOrder->order_number }}</span></div>
            <div class="detail-row"><label>Status</label><span class="value">@if($purchaseOrder->status=='pending')<span class="badge badge-warning">Pending</span>@elseif($purchaseOrder->status=='approved')<span class="badge badge-info">Approved</span>@elseif($purchaseOrder->status=='received')<span class="badge badge-success">Received</span>@elseif($purchaseOrder->status=='cancelled')<span class="badge badge-danger">Cancelled</span>@endif</span></div>
            <div class="detail-row"><label>Supplier</label><span class="value">{{ $purchaseOrder->supplier_name }}@if($purchaseOrder->supplier) ({{ $purchaseOrder->supplier->company ?? '' }})@endif</span></div>
            <div class="detail-row"><label>Order Date</label><span class="value">{{ $purchaseOrder->order_date->format('Y-m-d') }}</span></div>
            <div class="detail-row"><label>Expected Date</label><span class="value">{{ $purchaseOrder->expected_date?->format('Y-m-d') ?? 'N/A' }}</span></div>
            <div class="detail-row"><label>Total Amount</label><span class="value" style="font-weight:700;font-size:1.8rem;">Rs. {{ number_format($purchaseOrder->total, 2) }}</span></div>
            <div class="detail-row full-width"><label>Notes</label><span class="value">{{ $purchaseOrder->notes ?? 'No notes' }}</span></div>
        </div>
    </div>

    <div class="list-card" style="margin-top:24px;">
        <div class="card-header"><h3>Order Items</h3></div>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>#</th><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead>
                <tbody>
                    @forelse($purchaseOrder->items as $i => $item)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>Rs. {{ number_format($item->price, 2) }}</td>
                        <td>Rs. {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:40px;">No items.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr><td colspan="4" style="text-align:right;font-weight:700;padding:12px;">Subtotal</td><td style="font-weight:700;">Rs. {{ number_format($purchaseOrder->subtotal, 2) }}</td></tr>
                    <tr><td colspan="4" style="text-align:right;font-weight:700;padding:12px;">Total</td><td style="font-weight:700;font-size:1.6rem;">Rs. {{ number_format($purchaseOrder->total, 2) }}</td></tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}.form-card{background:white;border-radius:16px;padding:40px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.detail-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}.detail-row.full-width{grid-column:1/-1}.detail-row label{display:block;font-weight:600;color:#6c757d;font-size:1.2rem;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px}.detail-row .value{font-size:1.5rem;color:#1a1a2e;font-weight:500}.list-card{background:white;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden}.card-header{padding:20px 24px;border-bottom:1px solid #f0f0f0}.card-header h3{font-size:1.5rem;font-weight:600;color:#1a1a2e;margin:0}.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:1.1rem;font-weight:600}.badge-success{background:#d4edda;color:#155724}.badge-danger{background:#f8d7da;color:#721c24}.badge-warning{background:#fff3cd;color:#856404}.badge-info{background:#d1ecf1;color:#0c5460}.table-responsive{overflow-x:auto}.data-table{width:100%;border-collapse:collapse}.data-table th{background:#f8f9fa;color:#1a1a2e;font-weight:600;font-size:1.2rem;text-transform:uppercase;letter-spacing:0.5px;padding:12px;text-align:left;border-bottom:2px solid #e9ecef}.data-table td{padding:10px 12px;border-bottom:1px solid #e9ecef;font-size:1.3rem}.data-table tfoot td{border-bottom:none;background:#fafafa}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:1.4rem;font-weight:600;cursor:pointer;transition:all 0.3s;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}.alert{padding:12px 16px;border-radius:8px;font-size:1.3rem;font-weight:500;margin-bottom:20px}.alert-success{background:#d4edda;color:#155724;border:1px solid #c3e6cb}</style>
@endsection

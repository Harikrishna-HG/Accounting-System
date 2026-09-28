@extends('layouts.dashboard')
@section('title', 'Edit Purchase Order - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">Edit PO {{ $purchaseOrder->order_number }}</h1><p class="page-subtitle">Modify purchase order</p></div></div></div>
    <div class="form-card">
        <form action="{{ route('accounting.purchase-orders.update', $purchaseOrder) }}" method="POST">@csrf @method('PUT')
            <div class="form-grid">
                <div class="form-group"><label>Supplier</label><select name="supplier_id" onchange="fillSupplier()"><option value="">— Manual —</option>@foreach($suppliers as $s)<option value="{{ $s->id }}" data-name="{{ $s->name }}" {{ $purchaseOrder->supplier_id==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach</select></div>
                <div class="form-group"><label>Supplier Name <span class="required">*</span></label><input type="text" name="supplier_name" id="supplierName" value="{{ old('supplier_name', $purchaseOrder->supplier_name) }}" required></div>
                <div class="form-group"><label>Order Date</label><input type="date" name="order_date" value="{{ old('order_date', $purchaseOrder->order_date->format('Y-m-d')) }}" required></div>
                <div class="form-group"><label>Expected Date</label><input type="date" name="expected_date" value="{{ old('expected_date', $purchaseOrder->expected_date?->format('Y-m-d')) }}"></div>
                <div class="form-group"><label>Status</label><select name="status"><option value="pending" {{ $purchaseOrder->status=='pending'?'selected':'' }}>Pending</option><option value="approved" {{ $purchaseOrder->status=='approved'?'selected':'' }}>Approved</option><option value="received" {{ $purchaseOrder->status=='received'?'selected':'' }}>Received</option><option value="cancelled" {{ $purchaseOrder->status=='cancelled'?'selected':'' }}>Cancelled</option></select></div>
                <div class="form-group full-width"><label>Notes</label><textarea name="notes" rows="2">{{ old('notes', $purchaseOrder->notes) }}</textarea></div>
            </div>
            <h3 style="font-size:1.6rem;font-weight:600;color:#1a1a2e;margin:30px 0 16px;">Order Items</h3>
            <div id="itemsContainer">
                @foreach($purchaseOrder->items as $idx => $item)
                <div class="item-row" style="display:grid;grid-template-columns:3fr 1fr 1fr auto;gap:12px;margin-bottom:12px;align-items:end;">
                    <div class="form-group"><label>Product</label><select name="items[{{ $idx }}][product_id]" class="productSelect" onchange="fillProduct(this)"><option value="">— Manual —</option>@foreach($products as $p)<option value="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->cost_price }}" {{ $item->product_id==$p->id?'selected':'' }}>{{ $p->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label>Name</label><input type="text" name="items[{{ $idx }}][product_name]" class="productName" value="{{ $item->product_name }}" required></div>
                    <div class="form-group"><label>Qty</label><input type="number" name="items[{{ $idx }}][quantity]" class="quantity" value="{{ $item->quantity }}" min="1" required></div>
                    <div class="form-group"><label>Price</label><input type="number" step="0.01" name="items[{{ $idx }}][price]" class="price" value="{{ $item->price }}" required></div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="addRow()"><i class="fas fa-plus"></i> Add Item</button>
            <div class="form-actions" style="margin-top:20px;"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update PO</button><a href="{{ route('accounting.purchase-orders.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div>
</div>
<script>
let idx = {{ $purchaseOrder->items->count() }};
function addRow() {
    const c = document.getElementById('itemsContainer');
    const r = document.createElement('div'); r.className='item-row'; r.style.cssText='display:grid;grid-template-columns:3fr 1fr 1fr auto;gap:12px;margin-bottom:12px;align-items:end;';
    r.innerHTML = `<div class="form-group"><label>Product</label><select name="items[${idx}][product_id]" class="productSelect" onchange="fillProduct(this)"><option value="">— Manual —</option>@foreach($products as $p)<option value="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->cost_price }}">{{ $p->name }}</option>@endforeach</select></div>
    <div class="form-group"><label>Name</label><input type="text" name="items[${idx}][product_name]" class="productName" required></div>
    <div class="form-group"><label>Qty</label><input type="number" name="items[${idx}][quantity]" class="quantity" value="1" min="1" required></div>
    <div class="form-group"><label>Price</label><input type="number" step="0.01" name="items[${idx}][price]" class="price" required></div>`;
    c.appendChild(r); idx++;
}
function fillProduct(select){const opt=select.options[select.selectedIndex];if(opt.value){select.closest('.item-row').querySelector('.productName').value=opt.dataset.name;select.closest('.item-row').querySelector('.price').value=opt.dataset.price;}}
function fillSupplier(){const sel=document.querySelector('select[name="supplier_id"]');const opt=sel.options[sel.selectedIndex];if(opt.value)document.getElementById('supplierName').value=opt.dataset.name;}
</script>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}.form-card{background:white;border-radius:16px;padding:40px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}.form-group.full-width{grid-column:1/-1}.form-group{display:flex;flex-direction:column;gap:8px}.form-group label{font-weight:600;color:#1a1a2e;font-size:1.3rem}.form-group .required{color:#CD2737}.form-group input,.form-group select,.form-group textarea{padding:10px 14px;border:2px solid #e9ecef;border-radius:8px;font-size:1.4rem;font-family:'Noto Sans Devanagari',sans-serif;background:#f8f9fa}.form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:#CD2737;background:white;box-shadow:0 0 0 3px rgba(205,39,55,0.1)}.form-actions{display:flex;gap:16px;padding-top:8px}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:1.4rem;font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-sm{padding:6px 12px;font-size:1.2rem}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}@media(max-width:768px){.dashboard-content{padding:20px}.form-card{padding:24px}.form-grid{grid-template-columns:1fr}.item-row{grid-template-columns:1fr!important}}</style>
@endsection

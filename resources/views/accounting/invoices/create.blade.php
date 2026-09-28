@extends('layouts.dashboard')
@section('title', 'Create Invoice - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">Create Invoice</h1><p class="page-subtitle">Generate a new sales invoice</p></div></div></div>
    <div class="form-card">
        <form action="{{ route('accounting.invoices.store') }}" method="POST" id="invoiceForm">@csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Client <span class="required">*</span></label>
                    <select name="client_id" id="clientSelect" onchange="fillClientData()">
                        <option value="">— Select Existing Client —</option>
                        @foreach($clients as $c)<option value="{{ $c->id }}" data-name="{{ $c->name }}" data-phone="{{ $c->phone }}" data-address="{{ $c->address }}" data-pan="{{ $c->pan_vat }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Or New Client Name <span class="required">*</span></label>
                    <input type="text" name="client_name" id="clientName" value="{{ old('client_name') }}" placeholder="Client name">
                    @error('client_name')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group"><label>Phone</label><input type="text" name="client_phone" id="clientPhone" value="{{ old('client_phone') }}"></div>
                <div class="form-group"><label>PAN/VAT</label><input type="text" name="client_pan" id="clientPan" value="{{ old('client_pan') }}"></div>
                <div class="form-group full-width"><label>Address</label><input type="text" name="client_address" id="clientAddress" value="{{ old('client_address') }}"></div>
                <div class="form-group"><label>Invoice Date <span class="required">*</span></label><input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required>@error('invoice_date')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="form-group"><label>Due Date</label><input type="date" name="due_date" value="{{ old('due_date') }}">@error('due_date')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="form-group"><label>Discount (Rs.)</label><input type="number" step="0.01" name="discount" id="discount" value="{{ old('discount', 0) }}" oninput="calculateTotal()">@error('discount')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="form-group"><label>Tax Type</label><select name="tax_type" id="taxType" onchange="calculateTotal()"><option value="none" {{ old('tax_type') == 'none' ? 'selected' : '' }}>No Tax</option><option value="vat" {{ old('tax_type') == 'vat' ? 'selected' : '' }}>VAT (13%)</option><option value="service" {{ old('tax_type') == 'service' ? 'selected' : '' }}>Service Charge (5%)</option></select></div>
                <div class="form-group"><label>Payment Method</label><select name="payment_method"><option value="">— Select —</option><option value="cash" {{ old('payment_method')=='cash'?'selected':'' }}>Cash</option><option value="bank" {{ old('payment_method')=='bank'?'selected':'' }}>Bank Transfer</option><option value="cheque" {{ old('payment_method')=='cheque'?'selected':'' }}>Cheque</option><option value="mobile" {{ old('payment_method')=='mobile'?'selected':'' }}>Mobile Banking</option><option value="card" {{ old('payment_method')=='card'?'selected':'' }}>Card Payment</option></select></div>
                <div class="form-group full-width"><label>Notes</label><textarea name="notes" rows="2">{{ old('notes') }}</textarea></div>
            </div>
            <h3 style="font-size:1.6rem;font-weight:600;color:#1a1a2e;margin:30px 0 16px;">Invoice Items</h3>
            <div id="itemsContainer">
                <div class="item-row" style="display:grid;grid-template-columns:3fr 1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;align-items:end;">
                    <div class="form-group"><label>Product</label><select name="items[0][product_id]" class="productSelect" onchange="fillProductData(this)"><option value="">— Manual —</option>@foreach($products as $p)<option value="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->price }}">{{ $p->name }}</option>@endforeach</select></div>
                    <div class="form-group"><label>Product Name</label><input type="text" name="items[0][product_name]" class="productName" required></div>
                    <div class="form-group"><label>Qty</label><input type="number" name="items[0][quantity]" class="quantity" value="1" min="1" oninput="calculateTotal()" required></div>
                    <div class="form-group"><label>Price</label><input type="number" step="0.01" name="items[0][price]" class="price" oninput="calculateTotal()" required></div>
                    <button type="button" class="btn btn-danger btn-sm" style="margin-top:8px;" onclick="this.closest('.item-row').remove();calculateTotal()"><i class="fas fa-times"></i></button>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="addItemRow()" style="margin-bottom:20px;"><i class="fas fa-plus"></i> Add Item</button>
            <div style="text-align:right;padding:16px;background:#f8f9fa;border-radius:12px;margin-bottom:20px;">
                <div style="display:flex;justify-content:flex-end;gap:40px;">
                    <div><strong style="font-size:1.4rem;color:#1a1a2e;">Subtotal:</strong> <span id="subtotalDisplay" style="font-size:1.6rem;font-weight:700;">Rs. 0.00</span></div>
                    <div><strong style="font-size:1.4rem;color:#1a1a2e;">Tax:</strong> <span id="taxDisplay" style="font-size:1.6rem;font-weight:700;">Rs. 0.00</span></div>
                    <div><strong style="font-size:1.4rem;color:#1a1a2e;">Total:</strong> <span id="totalDisplay" style="font-size:2rem;font-weight:700;color:#CD2737;">Rs. 0.00</span></div>
                </div>
            </div>
            <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Create Invoice</button><a href="{{ route('accounting.invoices.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div>
</div>
<script>
let itemIndex = 1;
function addItemRow() {
    const container = document.getElementById('itemsContainer');
    const row = document.createElement('div');
    row.className = 'item-row';
    row.style.cssText = 'display:grid;grid-template-columns:3fr 1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;align-items:end;';
    const productOpts = document.querySelector('.productSelect').innerHTML;
    row.innerHTML = `<div class="form-group"><label>Product</label><select name="items[${itemIndex}][product_id]" class="productSelect" onchange="fillProductData(this)">${productOpts}</select></div>
    <div class="form-group"><label>Name</label><input type="text" name="items[${itemIndex}][product_name]" class="productName" required></div>
    <div class="form-group"><label>Qty</label><input type="number" name="items[${itemIndex}][quantity]" class="quantity" value="1" min="1" oninput="calculateTotal()" required></div>
    <div class="form-group"><label>Price</label><input type="number" step="0.01" name="items[${itemIndex}][price]" class="price" oninput="calculateTotal()" required></div>
    <button type="button" class="btn btn-danger btn-sm" style="margin-top:8px;" onclick="this.closest('.item-row').remove();calculateTotal()"><i class="fas fa-times"></i></button>`;
    container.appendChild(row);
    itemIndex++;
}
function fillProductData(select) {
    const opt = select.options[select.selectedIndex];
    const row = select.closest('.item-row');
    if (opt.value) {
        row.querySelector('.productName').value = opt.dataset.name;
        row.querySelector('.price').value = opt.dataset.price;
    }
    calculateTotal();
}
function fillClientData() {
    const select = document.getElementById('clientSelect');
    const opt = select.options[select.selectedIndex];
    if (opt.value) {
        document.getElementById('clientName').value = opt.dataset.name;
        document.getElementById('clientPhone').value = opt.dataset.phone || '';
        document.getElementById('clientAddress').value = opt.dataset.address || '';
        document.getElementById('clientPan').value = opt.dataset.pan || '';
    }
}
function calculateTotal() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.quantity').value) || 0;
        const price = parseFloat(row.querySelector('.price').value) || 0;
        subtotal += qty * price;
    });
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const taxable = subtotal - discount;
    const taxType = document.getElementById('taxType').value;
    let tax = 0;
    if (taxType === 'vat') tax = taxable * 0.13;
    else if (taxType === 'service') tax = taxable * 0.05;
    const total = taxable + tax;
    document.getElementById('subtotalDisplay').textContent = 'Rs. ' + subtotal.toFixed(2);
    document.getElementById('taxDisplay').textContent = 'Rs. ' + tax.toFixed(2);
    document.getElementById('totalDisplay').textContent = 'Rs. ' + total.toFixed(2);
}
calculateTotal();
</script>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}.form-card{background:white;border-radius:16px;padding:40px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}.form-group.full-width{grid-column:1/-1}.form-group{display:flex;flex-direction:column;gap:8px}.form-group label{font-weight:600;color:#1a1a2e;font-size:1.3rem}.form-group .required{color:#CD2737}.form-group input,.form-group select,.form-group textarea{padding:10px 14px;border:2px solid #e9ecef;border-radius:8px;font-size:1.4rem;font-family:'Noto Sans Devanagari',sans-serif;background:#f8f9fa}.form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:#CD2737;background:white;box-shadow:0 0 0 3px rgba(205,39,55,0.1)}.form-actions{display:flex;gap:16px;padding-top:8px}.field-error{font-size:1.2rem;color:#CD2737}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:1.4rem;font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-sm{padding:6px 12px;font-size:1.2rem}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}.btn-danger{background:#dc3545;color:white}.btn-danger:hover{background:#c82333;transform:translateY(-2px)}@media(max-width:768px){.dashboard-content{padding:20px}.form-card{padding:24px}.form-grid{grid-template-columns:1fr}.item-row{grid-template-columns:1fr !important}}</style>
@endsection

@extends('layouts.dashboard')
@section('title', 'Record Payment - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">Record Payment</h1><p class="page-subtitle">Record a new payment</p></div></div></div>
    <div class="form-card">
        <form action="{{ route('accounting.payments.store') }}" method="POST">@csrf
            <div class="form-grid">
                <div class="form-group"><label>Invoice</label><select name="invoice_id" id="invoiceSelect" onchange="fillInvoiceClient()"><option value="">— No Invoice —</option>@foreach($invoices as $inv)<option value="{{ $inv->id }}" data-amount="{{ $inv->due_amount }}" {{ old('invoice_id')==$inv->id?'selected':'' }}>{{ $inv->invoice_number }} - {{ $inv->client_name }} (Due: Rs. {{ number_format($inv->due_amount,2) }})</option>@endforeach</select></div>
                <div class="form-group"><label>Client</label><select name="client_id"><option value="">— Select Client —</option>@foreach($clients as $c)<option value="{{ $c->id }}" {{ old('client_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select></div>
                <div class="form-group"><label>Amount (Rs.) <span class="required">*</span></label><input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount') }}" required>@error('amount')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="form-group"><label>Payment Method</label><select name="payment_method"><option value="">— Select —</option><option value="cash" {{ old('payment_method')=='cash'?'selected':'' }}>Cash</option><option value="bank" {{ old('payment_method')=='bank'?'selected':'' }}>Bank Transfer</option><option value="cheque" {{ old('payment_method')=='cheque'?'selected':'' }}>Cheque</option><option value="mobile" {{ old('payment_method')=='mobile'?'selected':'' }}>Mobile Banking</option><option value="card" {{ old('payment_method')=='card'?'selected':'' }}>Card Payment</option></select></div>
                <div class="form-group"><label>Reference</label><input type="text" name="reference" value="{{ old('reference') }}" placeholder="Cheque/Transaction ref"></div>
                <div class="form-group"><label>Payment Date <span class="required">*</span></label><input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required></div>
                <div class="form-group full-width"><label>Notes</label><textarea name="notes" rows="2">{{ old('notes') }}</textarea></div>
            </div>
            <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Record Payment</button><a href="{{ route('accounting.payments.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div>
</div>
<script>
function fillInvoiceClient() {
    const sel = document.getElementById('invoiceSelect');
    const opt = sel.options[sel.selectedIndex];
    if (opt.value && opt.dataset.amount) {
        document.getElementById('amount').value = opt.dataset.amount;
    }
}
</script>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:var(--fs-2-2);font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:var(--fs-1-3)}.form-card{background:white;border-radius:16px;padding:40px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}.form-group.full-width{grid-column:1/-1}.form-group{display:flex;flex-direction:column;gap:8px}.form-group label{font-weight:600;color:#1a1a2e;font-size:var(--fs-1-3)}.form-group .required{color:#CD2737}.form-group input,.form-group select,.form-group textarea{padding:10px 14px;border:2px solid #e9ecef;border-radius:8px;font-size:var(--fs-1-4);font-family:'Noto Sans Devanagari',sans-serif;background:#f8f9fa}.form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:#CD2737;background:white;box-shadow:0 0 0 3px rgba(205,39,55,0.1)}.form-actions{display:flex;gap:16px;padding-top:8px}.field-error{font-size:var(--fs-1-2);color:#CD2737}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:var(--fs-1-4);font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}@media(max-width:768px){.dashboard-content{padding:20px}.form-card{padding:24px}.form-grid{grid-template-columns:1fr}}</style>
@endsection

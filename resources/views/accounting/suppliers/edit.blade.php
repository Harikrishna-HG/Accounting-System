@extends('layouts.dashboard')
@section('title', 'Edit Supplier - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">Edit Supplier</h1><p class="page-subtitle">{{ $supplier->name }}</p></div></div></div>
    <div class="form-card">
        <form action="{{ route('accounting.suppliers.update', $supplier) }}" method="POST">@csrf @method('PUT')
            <div class="form-grid">
                <div class="form-group"><label>Supplier Name <span class="required">*</span></label><input type="text" name="name" value="{{ old('name', $supplier->name) }}" required>@error('name')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="form-group"><label>Email</label><input type="email" name="email" value="{{ old('email', $supplier->email) }}">@error('email')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}">@error('phone')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="form-group"><label>Company</label><input type="text" name="company" value="{{ old('company', $supplier->company) }}">@error('company')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="form-group"><label>PAN/VAT</label><input type="text" name="pan_vat" value="{{ old('pan_vat', $supplier->pan_vat) }}">@error('pan_vat')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div class="form-group"><label class="status-toggle"><input type="checkbox" name="is_active" {{ $supplier->is_active ? 'checked' : '' }}> Active</label></div>
                <div class="form-group full-width"><label>Address</label><textarea name="address" rows="2">{{ old('address', $supplier->address) }}</textarea>@error('address')<span class="field-error">{{ $message }}</span>@enderror</div>
            </div>
            <div class="form-actions"><button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update Supplier</button><a href="{{ route('accounting.suppliers.index') }}" class="btn btn-secondary">Cancel</a></div>
        </form>
    </div>
</div>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}.form-card{background:white;border-radius:16px;padding:40px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:24px}.form-group.full-width{grid-column:1/-1}.form-group{display:flex;flex-direction:column;gap:8px}.form-group label{font-weight:600;color:#1a1a2e;font-size:1.3rem}.form-group .required{color:#CD2737}.form-group input,.form-group select,.form-group textarea{padding:10px 14px;border:2px solid #e9ecef;border-radius:8px;font-size:1.4rem;font-family:'Noto Sans Devanagari',sans-serif;background:#f8f9fa}.form-group input:focus,.form-group select:focus,.form-group textarea:focus{outline:none;border-color:#CD2737;background:white;box-shadow:0 0 0 3px rgba(205,39,55,0.1)}.form-actions{display:flex;gap:16px;padding-top:8px}.field-error{font-size:1.2rem;color:#CD2737}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:1.4rem;font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}.status-toggle{display:inline-flex;align-items:center;gap:8px;cursor:pointer}.status-toggle input[type="checkbox"]{width:18px;height:18px;accent-color:#CD2737}@media(max-width:768px){.dashboard-content{padding:20px}.form-card{padding:24px}.form-grid{grid-template-columns:1fr}}</style>
@endsection

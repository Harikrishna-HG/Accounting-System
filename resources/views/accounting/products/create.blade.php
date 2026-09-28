@extends('layouts.dashboard')
@section('title', 'Add Product - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <div class="header-top">
            <div>
                <h1 class="page-title">Add Product</h1>
                <p class="page-subtitle">Add a new product to inventory</p>
            </div>
        </div>
    </div>
    <div class="form-card">
        <form action="{{ route('accounting.products.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Product Name <span class="required">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                    @error('name')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" value="{{ old('category') }}" placeholder="e.g., Laptops, Accessories">
                    @error('category')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Selling Price (Rs.) <span class="required">*</span></label>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required>
                    @error('price')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Cost Price (Rs.)</label>
                    <input type="number" step="0.01" name="cost_price" value="{{ old('cost_price') }}">
                    @error('cost_price')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Stock Quantity</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}">
                    @error('stock_quantity')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>SKU</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Unique SKU code">
                    @error('sku')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group full-width">
                    <label>Description</label>
                    <textarea name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="url" name="image_url" value="{{ old('image_url') }}">
                    @error('image_url')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label>Source URL</label>
                    <input type="url" name="source_url" value="{{ old('source_url') }}">
                    @error('source_url')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="status-toggle">
                        <input type="checkbox" name="is_active" checked> Active
                    </label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Product</button>
                <a href="{{ route('accounting.products.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
<style>
.dashboard-content { padding: 30px; }
.page-header { margin-bottom: 30px; }
.header-top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
.page-title { font-size: 2.2rem; font-weight: 700; color: #1a1a2e; margin: 0; }
.page-subtitle { color: #6c757d; margin: 5px 0 0 0; font-size: 1.3rem; }
.form-card { background: white; border-radius: 16px; padding: 40px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.form-group.full-width { grid-column: 1 / -1; }
.form-group { display: flex; flex-direction: column; gap: 8px; }
.form-group label { font-weight: 600; color: #1a1a2e; font-size: 1.3rem; }
.form-group .required { color: #CD2737; }
.form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1.4rem; font-family: 'Noto Sans Devanagari', sans-serif; transition: border-color 0.2s, box-shadow 0.2s; background: #f8f9fa; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #CD2737; background: white; box-shadow: 0 0 0 3px rgba(205,39,55,0.1); }
.form-actions { display: flex; gap: 16px; padding-top: 8px; }
.field-error { font-size: 1.2rem; color: #CD2737; }
.btn { padding: 10px 22px; border: none; border-radius: 8px; font-size: 1.4rem; font-weight: 600; cursor: pointer; font-family: 'Noto Sans Devanagari', sans-serif; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-primary { background: #CD2737; color: white; box-shadow: 0 4px 15px rgba(205,39,55,0.3); }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(205,39,55,0.4); }
.btn-secondary { background: #6c757d; color: white; }
.btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
.status-toggle { display: inline-flex; align-items: center; gap: 8px; cursor: pointer; }
.status-toggle input[type="checkbox"] { width: 18px; height: 18px; accent-color: #CD2737; cursor: pointer; }
@media (max-width: 768px) { .dashboard-content { padding: 20px; } .form-card { padding: 24px; } .form-grid { grid-template-columns: 1fr; } }
</style>
@endsection

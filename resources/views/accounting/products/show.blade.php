@extends('layouts.dashboard')
@section('title', 'Product Details - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <div class="header-top">
            <div><h1 class="page-title">{{ $product->name }}</h1><p class="page-subtitle">Product details</p></div>
            <div class="header-actions">
                <a href="{{ route('accounting.products.edit', $product) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('accounting.products.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
            </div>
        </div>
    </div>
    <div class="info-grid">
        <div class="info-card"><div class="info-label">Category</div><div class="info-value">{{ $product->category ?? '—' }}</div></div>
        <div class="info-card"><div class="info-label">SKU</div><div class="info-value">{{ $product->sku ?? '—' }}</div></div>
        <div class="info-card"><div class="info-label">Price</div><div class="info-value">Rs. {{ number_format($product->price, 2) }}</div></div>
        <div class="info-card"><div class="info-label">Cost Price</div><div class="info-value">Rs. {{ number_format($product->cost_price, 2) }}</div></div>
        <div class="info-card"><div class="info-label">Stock</div><div class="info-value">{{ $product->stock_quantity ?? 0 }}</div></div>
        <div class="info-card"><div class="info-label">Status</div><div class="info-value">@if($product->is_active)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Inactive</span>@endif</div></div>
    </div>
    @if($product->description)
    <div class="dashboard-card" style="margin-bottom:30px;">
        <div class="card-header"><h3><i class="fas fa-align-left"></i> Description</h3></div>
        <div class="card-body"><p style="font-size:1.4rem;color:#333;line-height:1.7;">{{ $product->description }}</p></div>
    </div>
    @endif
    @if($product->image_url)
    <div class="dashboard-card" style="margin-bottom:30px;">
        <div class="card-header"><h3><i class="fas fa-image"></i> Image</h3></div>
        <div class="card-body" style="text-align:center;"><img src="{{ $product->image_url }}" alt="{{ $product->name }}" style="max-width:400px;max-height:400px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,0.1);"></div>
    </div>
    @endif
    @if($product->source_url)
    <div class="dashboard-card">
        <div class="card-header"><h3><i class="fas fa-globe"></i> Source</h3></div>
        <div class="card-body">
            <p style="font-size:1.3rem;">Source: <strong>{{ $product->source ?? 'N/A' }}</strong></p>
            @if($product->source_url)<a href="{{ $product->source_url }}" target="_blank" style="color:#CD2737;font-size:1.3rem;">View original <i class="fas fa-external-link-alt"></i></a>@endif
        </div>
    </div>
    @endif
</div>
<style>.dashboard-content{padding:30px}.page-header{margin-bottom:30px}.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}.info-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;margin-bottom:30px}.info-card{background:white;border-radius:12px;padding:16px 20px;box-shadow:0 2px 12px rgba(0,0,0,0.06)}.info-label{font-size:1.1rem;color:#6c757d;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:4px}.info-value{font-size:1.4rem;font-weight:600;color:#1a1a2e}.dashboard-card{background:white;border-radius:16px;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden}.card-header{padding:20px 24px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center}.card-header h3{font-size:1.5rem;font-weight:600;color:#1a1a2e;margin:0;display:flex;align-items:center;gap:8px}.card-header h3 i{color:#CD2737}.card-body{padding:16px 20px}.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:1.1rem;font-weight:600}.badge-success{background:#d4edda;color:#155724}.badge-danger{background:#f8d7da;color:#721c24}.btn{padding:10px 22px;border:none;border-radius:8px;font-size:1.4rem;font-weight:600;cursor:pointer;font-family:'Noto Sans Devanagari',sans-serif;display:inline-flex;align-items:center;gap:6px;text-decoration:none}.btn-primary{background:#CD2737;color:white;box-shadow:0 4px 15px rgba(205,39,55,0.3)}.btn-primary:hover{transform:translateY(-2px);box-shadow:0 6px 20px rgba(205,39,55,0.4)}.btn-secondary{background:#6c757d;color:white}.btn-secondary:hover{background:#5a6268;transform:translateY(-2px)}</style>
@endsection

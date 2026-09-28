@extends('layouts.dashboard')
@section('title', 'New Role - Accounting System')
@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <div class="header-top">
            <div>
                <h1 class="page-title">New Role</h1>
                <p class="page-subtitle">Create a new role with permissions</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('dashboard.roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Roles
                </a>
            </div>
        </div>
    </div>

    <div class="form-card">
        <form action="{{ route('dashboard.roles.store') }}" method="POST">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label for="name">Name <span class="required">*</span></label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}">
                    @error('name') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label for="slug">Slug <span class="required">*</span></label>
                    <input type="text" id="slug" name="slug" required value="{{ old('slug') }}" placeholder="role-slug">
                    @error('slug') <span class="field-error">{{ $message }}</span> @enderror
                </div>
                <div class="form-group full-width">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="2">{{ old('description') }}</textarea>
                </div>
                <div class="form-group full-width">
                    <label>Permissions</label>
                    <div class="permissions-grid">
                        @foreach($permissions as $perm)
                        <label class="perm-checkbox">
                            <input type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                {{ in_array($perm->id, old('permissions', [])) ? 'checked' : '' }}>
                            <span>{{ $perm->name }}</span>
                            <small>{{ $perm->slug }}</small>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-actions full-width">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Role
                    </button>
                </div>
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
.form-card { background: white; border-radius: 14px; padding: 30px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.form-group.full-width { grid-column: 1 / -1; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group label { font-weight: 600; color: #1a1a2e; font-size: 1.3rem; }
.form-group .required { color: #CD2737; }
.form-group input, .form-group select, .form-group textarea { padding: 10px 14px; border: 2px solid #e9ecef; border-radius: 8px; font-size: 1.4rem; font-family: "Noto Sans Devanagari", sans-serif; background: #f8f9fa; transition: border-color 0.2s; }
.form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #CD2737; background: white; }
.permissions-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 8px; }
.perm-checkbox { display: flex; align-items: center; gap: 6px; padding: 8px 12px; border: 1px solid #e9ecef; border-radius: 6px; cursor: pointer; transition: all 0.2s; }
.perm-checkbox:hover { border-color: #CD2737; background: #fff5f5; }
.perm-checkbox input { width: 16px; height: 16px; accent-color: #CD2737; }
.perm-checkbox span { font-size: 1.3rem; font-weight: 500; color: #333; }
.perm-checkbox small { font-size: 1.1rem; color: #6c757d; margin-left: auto; }
.form-actions { display: flex; gap: 12px; padding-top: 8px; }
.btn { padding: 10px 22px; border: none; border-radius: 8px; font-size: 1.4rem; font-weight: 600; cursor: pointer; transition: all 0.3s; font-family: "Noto Sans Devanagari", sans-serif; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-primary { background: #CD2737; color: white; box-shadow: 0 4px 15px rgba(205,39,55,0.3); }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(205,39,55,0.4); }
.btn-secondary { background: #6c757d; color: white; }
.btn-secondary:hover { background: #5a6268; transform: translateY(-2px); }
</style>
@endsection

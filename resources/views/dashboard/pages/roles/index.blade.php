@extends('layouts.dashboard')
@section('title', 'Roles Management - Accounting System')
@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <div class="header-top">
            <div>
                <h1 class="page-title">Role Management</h1>
                <p class="page-subtitle">Manage roles and permissions</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('dashboard.roles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Role
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="roles-grid">
        @foreach($roles as $role)
        <div class="role-card">
            <div class="role-header">
                <h3>{{ $role->name }}</h3>
                <code>{{ $role->slug }}</code>
            </div>
            @if($role->description)
                <p class="role-desc">{{ $role->description }}</p>
            @endif
            <div class="role-stats">
                <div class="stat">
                    <span class="stat-value">{{ $role->users_count }}</span>
                    <span class="stat-label">Users</span>
                </div>
                <div class="stat">
                    <span class="stat-value">{{ $role->permissions_count }}</span>
                    <span class="stat-label">Permissions</span>
                </div>
            </div>
            <div class="role-permissions">
                <strong>Permissions:</strong>
                <div class="perm-tags">
                    @forelse($role->permissions as $perm)
                        <span class="perm-tag">{{ $perm->name }}</span>
                    @empty
                        <span class="perm-tag none">No permissions</span>
                    @endforelse
                </div>
            </div>
            <div class="role-actions">
                <a href="{{ route('dashboard.roles.edit', $role) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @if($role->slug !== 'super-admin')
                <form action="{{ route('dashboard.roles.destroy', $role) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this role?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
<style>
.dashboard-content { padding: 30px; }
.page-header { margin-bottom: 30px; }
.header-top { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; }
.page-title { font-size:var(--fs-2-2); font-weight: 700; color: #1a1a2e; margin: 0; }
.page-subtitle { color: #6c757d; margin: 5px 0 0 0; font-size:var(--fs-1-3); }
.alert { padding: 10px 16px; border-radius: 8px; margin-bottom: 20px; font-size:var(--fs-1-3); }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-error { background: #fce4ec; color: #CD2737; border: 1px solid #f8d7da; }
.roles-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
.role-card { background: white; border-radius: 14px; padding: 20px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.role-header { margin-bottom: 10px; }
.role-header h3 { font-size:var(--fs-1-5); font-weight: 700; color: #1a1a2e; margin: 0 0 4px 0; }
.role-header code { background: #f0f0f0; padding: 2px 6px; border-radius: 4px; font-size:var(--fs-1-2); color: #6c757d; }
.role-desc { color: #6c757d; font-size:var(--fs-1-3); margin-bottom: 12px; }
.role-stats { display: flex; gap: 16px; margin-bottom: 12px; }
.role-stats .stat { display: flex; flex-direction: column; }
.role-stats .stat-value { font-size:var(--fs-2); font-weight: 700; color: #1a1a2e; }
.role-stats .stat-label { font-size:var(--fs-1-1); color: #6c757d; }
.role-permissions { margin-bottom: 12px; }
.role-permissions strong { display: block; font-size:var(--fs-1-2); color: #1a1a2e; margin-bottom: 6px; }
.perm-tags { display: flex; flex-wrap: wrap; gap: 4px; }
.perm-tag { background: #e3f2fd; color: #1976d2; padding: 3px 8px; border-radius: 6px; font-size:var(--fs-1-1); }
.perm-tag.none { background: #f0f0f0; color: #6c757d; }
.role-actions { display: flex; gap: 6px; padding-top: 12px; border-top: 1px solid #f0f0f0; }
.btn { padding: 10px 22px; border: none; border-radius: 8px; font-size:var(--fs-1-4); font-weight: 600; cursor: pointer; transition: all 0.3s; font-family: 'Noto Sans Devanagari', sans-serif; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-sm { padding: 6px 12px; font-size:var(--fs-1-2); }
.btn-primary { background: #CD2737; color: white; }
.btn-primary:hover { background: #b3202e; }
.btn-danger { background: #fce4ec; color: #CD2737; }
.btn-danger:hover { background: #CD2737; color: white; }
</style>
@endsection


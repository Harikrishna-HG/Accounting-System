@extends('layouts.dashboard')
@section('title', 'User Management - Accounting System')
@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <div class="header-top">
            <div>
                <h1 class="page-title">User Management</h1>
                <p class="page-subtitle">Manage system users</p>
            </div>
            <div class="header-actions">
                <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New User
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

    <div class="list-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td class="title-cell">
                            <span class="user-name">{{ $user->name }}</span>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role)
                                <span class="role-badge">{{ $user->role->name }}</span>
                            @else
                                <span class="role-badge none">—</span>
                            @endif
                        </td>
                        <td class="date-cell">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="actions-cell">
                            <div class="action-btns">
                                <a href="{{ route('dashboard.users.edit', $user) }}" class="action-btn edit" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('dashboard.users.destroy', $user) }}" method="POST" style="display:inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn delete" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
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
.list-card { background: white; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden; }
.table-responsive { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { text-align: left; padding: 10px 12px; font-size:var(--fs-1-2); font-weight: 600; color: #6c757d; text-transform: uppercase; letter-spacing: 0.5px; background: #fafafa; border-bottom: 1px solid #f0f0f0; }
.data-table td { padding: 10px 12px; border-bottom: 1px solid #f8f9fa; font-size:var(--fs-1-3); color: #333; vertical-align: middle; }
.data-table tr:hover td { background: #fafafa; }
.title-cell { min-width: 150px; }
.user-name { font-weight: 500; color: #1a1a2e; }
.role-badge { background: #e3f2fd; color: #1976d2; padding: 3px 10px; border-radius: 12px; font-size:var(--fs-1-1); font-weight: 600; }
.role-badge.none { background: #f0f0f0; color: #6c757d; }
.date-cell { color: #6c757d; font-size:var(--fs-1-2); }
.actions-cell { white-space: nowrap; }
.action-btns { display: flex; gap: 4px; }
.action-btn { width: 32px; height: 32px; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size:var(--fs-1-3); transition: all 0.2s; text-decoration: none; }
.action-btn.edit { background: #e3f2fd; color: #1976d2; }
.action-btn.edit:hover { background: #1976d2; color: white; }
.action-btn.delete { background: #fce4ec; color: #CD2737; }
.action-btn.delete:hover { background: #CD2737; color: white; }
.pagination-wrapper { padding: 12px 20px; border-top: 1px solid #f0f0f0; }
.btn { padding: 10px 22px; border: none; border-radius: 8px; font-size:var(--fs-1-4); font-weight: 600; cursor: pointer; transition: all 0.3s; font-family: 'Noto Sans Devanagari', sans-serif; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-primary { background: #CD2737; color: white; box-shadow: 0 4px 15px rgba(205,39,55,0.3); }
.btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(205,39,55,0.4); }
</style>
@endsection


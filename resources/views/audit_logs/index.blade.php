@extends('layouts.dashboard')
@section('title', 'Audit Log - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header"><div class="header-top"><div><h1 class="page-title">Audit Log</h1><p class="page-subtitle">Append-only record of every change and authentication event</p></div></div></div>

    <div class="list-card" style="margin-bottom:20px;">
        <form method="GET" action="{{ route('accounting.audit-logs.index') }}" class="filter-form">
            <input type="text" name="search" placeholder="Search description, route, IP..." value="{{ request('search') }}" class="filter-input">
            <select name="event" class="filter-input">
                <option value="">All events</option>
                @foreach($eventTypes as $event)
                    <option value="{{ $event }}" @selected(request('event') === $event)>{{ $event }}</option>
                @endforeach
            </select>
            <select name="auditable_type" class="filter-input">
                <option value="">All models</option>
                @foreach($modelTypes as $modelType)
                    <option value="{{ $modelType }}" @selected(request('auditable_type') === $modelType)>{{ class_basename($modelType) }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" class="filter-input">
            <input type="date" name="to" value="{{ request('to') }}" class="filter-input">
            <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Filter</button>
            <a href="{{ route('accounting.audit-logs.index') }}" class="btn-reset">Reset</a>
        </form>
    </div>

    <div class="list-card">
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>When ({{ config('app.display_timezone') }})</th><th>Event</th><th>Actor</th><th>Model</th><th>Description</th><th>IP</th><th></th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td style="white-space:nowrap;">{{ $log->occurredAtLabel() }}</td>
                        <td>
                            @php
                                [$color, $bg] = match(true) {
                                    str_starts_with($log->event, 'created') => ['#28a745', '#e8f7ec'],
                                    str_starts_with($log->event, 'updated') => ['#b8860b', '#fdf6e3'],
                                    str_starts_with($log->event, 'deleted') => ['#dc3545', '#fdeaea'],
                                    str_starts_with($log->event, 'login') => ['#1976d2', '#e3f2fd'],
                                    default => ['#6c757d', '#f1f3f5'],
                                };
                            @endphp
                            <span class="event-badge" style="color:{{ $color }};background:{{ $bg }};">{{ $log->event }}</span>
                        </td>
                        <td>
                            @if($log->user)
                                {{ $log->user->name }}
                                <div style="font-size:var(--fs-1-1);color:#6c757d;">{{ $log->user->email }}</div>
                            @else
                                <span style="color:#6c757d;">—</span>
                            @endif
                        </td>
                        <td>
                            @if($log->auditable_type)
                                {{ class_basename($log->auditable_type) }}
                                <div style="font-size:var(--fs-1-1);color:#6c757d;">#{{ $log->auditable_id }}</div>
                            @else
                                <span style="color:#6c757d;">—</span>
                            @endif
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($log->description, 70) ?? '—' }}</td>
                        <td style="font-family:monospace;">{{ $log->ip_address ?? '—' }}</td>
                        <td><a href="{{ route('accounting.audit-logs.show', $log) }}" class="link-detail">Details</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;padding:40px;color:#6c757d;"><i class="fas fa-clock-rotate-left" style="font-size:var(--fs-3-6);display:block;margin-bottom:12px;color:#dee2e6;"></i>No audit records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-wrapper">{{ $logs->links() }}</div>
    </div>
</div>
<style>
.dashboard-content{padding:30px}
.page-header{margin-bottom:30px}
.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}
.page-title{font-size:var(--fs-2-2);font-weight:700;color:#1a1a2e;margin:0}
.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:var(--fs-1-3)}
.list-card{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden}
.table-responsive{overflow-x:auto}
.data-table{width:100%;border-collapse:collapse}
.data-table th{text-align:left;padding:10px 12px;font-size:var(--fs-1-2);font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:0.5px;background:#fafafa;border-bottom:1px solid #f0f0f0}
.data-table td{padding:10px 12px;border-bottom:1px solid #f8f9fa;font-size:var(--fs-1-3);color:#333;vertical-align:middle}
.event-badge{padding:3px 10px;border-radius:12px;font-size:var(--fs-1-1);font-weight:600;white-space:nowrap}
.link-detail{color:#CD2737;font-weight:600;text-decoration:none}
.link-detail:hover{text-decoration:underline}
.filter-form{display:flex;flex-wrap:wrap;gap:10px;padding:15px 20px;align-items:center}
.filter-input{padding:8px 12px;border:1px solid #e0e0e0;border-radius:8px;font-size:var(--fs-1-3);font-family:inherit}
.btn-filter{background:#CD2737;color:white;border:none;padding:8px 16px;border-radius:8px;font-size:var(--fs-1-3);cursor:pointer;font-weight:600}
.btn-filter:hover{background:#b02130}
.btn-reset{padding:8px 16px;border-radius:8px;font-size:var(--fs-1-3);color:#6c757d;text-decoration:none;border:1px solid #e0e0e0}
.btn-reset:hover{background:#f8f9fa}
.pagination-wrapper{padding:12px 20px;border-top:1px solid #f0f0f0}
</style>
@endsection

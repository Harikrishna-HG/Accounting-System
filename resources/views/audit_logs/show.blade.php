@extends('layouts.dashboard')
@section('title', 'Audit Entry - Accounting')
@section('content')
<div class="dashboard-content">
    <div class="page-header">
        <div class="header-top">
            <div>
                <h1 class="page-title">Audit Entry #{{ $auditLog->id }}</h1>
                <p class="page-subtitle">{{ $auditLog->event }} &middot; {{ $auditLog->occurredAtLabel() }}</p>
            </div>
            <a href="{{ route('accounting.audit-logs.index') }}" class="btn-back"><i class="fas fa-arrow-left"></i> Back to Audit Log</a>
        </div>
    </div>

    <div class="list-card" style="margin-bottom:20px;">
        <div class="card-body">
            <dl class="meta-grid">
                <div><dt>Event</dt><dd>{{ $auditLog->event }}</dd></div>
                <div><dt>Actor</dt><dd>{{ $auditLog->user?->name ?? 'System' }}@if($auditLog->user)<br><small>{{ $auditLog->user->email }}</small>@endif</dd></div>
                <div><dt>Model</dt><dd>{{ $auditLog->auditable_type ? class_basename($auditLog->auditable_type) : '—' }}@if($auditLog->auditable_id) #{{ $auditLog->auditable_id }}@endif</dd></div>
                <div><dt>IP Address</dt><dd>{{ $auditLog->ip_address ?? '—' }}</dd></div>
                <div><dt>Route</dt><dd>{{ $auditLog->route_name ?? '—' }}</dd></div>
                <div><dt>URL</dt><dd><small>{{ $auditLog->url ?? '—' }}</small></dd></div>
                <div><dt>User Agent</dt><dd><small>{{ $auditLog->user_agent ?? '—' }}</small></dd></div>
            </dl>
            @if($auditLog->description)
                <p class="description">{{ $auditLog->description }}</p>
            @endif
        </div>
    </div>

    @php
        $old = $auditLog->old_values ?? [];
        $new = $auditLog->new_values ?? [];
        $keys = array_values(array_unique(array_merge(array_keys($old), array_keys($new))));
    @endphp

    <div class="list-card">
        <div class="card-body">
            <h2 class="section-title">Field Changes</h2>
            @if(empty($keys))
                <p style="color:#6c757d;">This event recorded no field values.</p>
            @else
                <div class="table-responsive">
                    <table class="data-table">
                        <thead><tr><th>Field</th><th>Before</th><th>After</th></tr></thead>
                        <tbody>
                            @foreach($keys as $key)
                                @php
                                    $before = $old[$key] ?? null;
                                    $after = $new[$key] ?? null;
                                @endphp
                                <tr class="{{ $before !== $after ? 'changed' : '' }}">
                                    <td><strong>{{ $key }}</strong></td>
                                    <td class="before">{{ is_array($before) ? json_encode($before) : ($before === null ? '—' : $before) }}</td>
                                    <td class="after">{{ is_array($after) ? json_encode($after) : ($after === null ? '—' : $after) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
<style>
.dashboard-content{padding:30px}
.page-header{margin-bottom:30px}
.header-top{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px}
.page-title{font-size:2.2rem;font-weight:700;color:#1a1a2e;margin:0}
.page-subtitle{color:#6c757d;margin:5px 0 0 0;font-size:1.3rem}
.list-card{background:white;border-radius:14px;box-shadow:0 2px 12px rgba(0,0,0,0.06);overflow:hidden}
.card-body{padding:20px}
.meta-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:18px;margin:0}
.meta-grid dt{font-size:1.15rem;font-weight:600;color:#6c757d;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:3px}
.meta-grid dd{margin:0;font-size:1.4rem;color:#1a1a2e}
.description{margin:20px 0 0;padding:12px 14px;background:#fafafa;border-left:3px solid #CD2737;border-radius:6px;font-size:1.35rem;color:#333}
.section-title{font-size:1.6rem;font-weight:700;color:#1a1a2e;margin:0 0 14px 0}
.table-responsive{overflow-x:auto}
.data-table{width:100%;border-collapse:collapse}
.data-table th{text-align:left;padding:10px 12px;font-size:1.2rem;font-weight:600;color:#6c757d;text-transform:uppercase;background:#fafafa;border-bottom:1px solid #f0f0f0}
.data-table td{padding:10px 12px;border-bottom:1px solid #f8f9fa;font-size:1.3rem;color:#333;vertical-align:top}
.data-table tr.changed td.before{color:#dc3545}
.data-table tr.changed td.after{color:#28a745;font-weight:600}
.btn-back{padding:9px 16px;border-radius:8px;font-size:1.3rem;color:#CD2737;text-decoration:none;border:1px solid #CD2737;font-weight:600}
.btn-back:hover{background:#fdeaea}
</style>
@endsection

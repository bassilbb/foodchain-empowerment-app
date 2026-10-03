@extends('admin.layouts.app')

@section('title', 'Marketplace Audit Log')
@section('header', 'Audit Log')
@section('subheader', 'Immutable trail of privileged marketplace actions (approvals, rejections, payouts, status changes).')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Audit Log</h1>
        <p>{{ number_format($logs->total()) }} recorded actions.</p>
    </div>
    <div class="header-actions-btns">
        <a href="{{ route('admin.marketplace.audit-logs.index') }}" class="btn btn-secondary">Refresh</a>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.marketplace.audit-logs.index') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 240px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ $search }}" class="form-input" placeholder="Description, subject or IP...">
        </div>
        <div style="min-width: 200px;">
            <label class="form-label">Action</label>
            <select name="action" class="form-select">
                <option value="">All actions</option>
                @foreach ($actionCounts as $actionCount)
                <option value="{{ $actionCount->action }}" {{ $currentAction === $actionCount->action ? 'selected' : '' }}>
                    {{ $actionCount->action }} ({{ $actionCount->total }})
                </option>
                @endforeach
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.marketplace.audit-logs.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>When</th>
                <th>Admin</th>
                <th>Action</th>
                <th>Subject</th>
                <th>Details</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td style="white-space: nowrap;">{{ $log->created_at->format('M d, Y h:i A') }}</td>
                <td>
                    @if ($log->adminUser)
                    <span style="font-weight: 600; color: #1e293b;">{{ $log->adminUser->name ?? $log->adminUser->username ?? 'Admin #'.$log->admin_user_id }}</span>
                    @else
                    <span style="color: #94a3b8;">System / self-service</span>
                    @endif
                </td>
                <td><span class="badge badge-info">{{ $log->action }}</span></td>
                <td>
                    @if ($log->subject_type)
                    <span style="font-size: 12px; color: #64748b;">{{ class_basename($log->subject_type) }}</span>
                    <span style="font-size: 12px; color: #94a3b8;">#{{ $log->subject_id }}</span>
                    @else
                    <span style="color: #94a3b8;">—</span>
                    @endif
                </td>
                <td>
                    <span style="color: #475569;">{{ $log->description ?? '—' }}</span>
                    @if ($log->changes)
                    <details style="margin-top: 4px;">
                        <summary style="font-size: 12px; color: #2563eb; cursor: pointer;">Changes</summary>
                        <pre style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 8px; font-size: 11px; margin-top: 4px;">{{ json_encode($log->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </details>
                    @endif
                </td>
                <td style="font-family: monospace; font-size: 12px; color: #64748b;">{{ $log->ip_address ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6">
                    <div class="empty-state">No audit entries recorded yet.</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $logs->links() }}
</div>
@endsection
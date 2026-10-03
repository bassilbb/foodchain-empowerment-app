@extends('admin.layouts.app')

@section('title', 'Incidents - Admin')
@section('header', 'Incidents')
@section('subheader', 'Manage and track all incidents')

@section('content')
<div class="page-header">
    <div>
        <h1>@yield('header')</h1>
        <p>@yield('subheader')</p>
    </div>
    <div class="header-actions-btns">
        <a href="{{ route('admin.incidents.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Create Incident
        </a>
    </div>
</div>

<div class="filter-card">
    <form method="GET" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; align-items: end;">
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="escalated" {{ request('status') == 'escalated' ? 'selected' : '' }}>Escalated</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-select">
                <option value="">All Priorities</option>
                <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom: 0;">
            <label class="form-label">Branch</label>
            <select name="branch_id" class="form-select">
                <option value="">All Branches</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary" style="margin-bottom: 0;">
            Filter
        </button>
    </form>
</div>

<div class="page-card">
    <div class="page-card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Branch</th>
                    <th>Department</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Assigned To</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($incidents as $incident)
                <tr>
                    <td style="font-family: monospace; font-size: 13px; color: #64748b;">{{ $incident->incident_number }}</td>
                    <td>
                        <a href="{{ route('admin.incidents.show', $incident) }}" style="color: #1e293b; text-decoration: none; font-weight: 500;">
                            {{ Str::limit($incident->title, 40) }}
                        </a>
                    </td>
                    <td>{{ $incident->branch?->name ?? 'N/A' }}</td>
                    <td>{{ $incident->department?->name ?? 'N/A' }}</td>
                    <td>
                        <span class="badge
                            @if($incident->priority === 'critical') badge-error
                            @elseif($incident->priority === 'high') badge-warning
                            @elseif($incident->priority === 'medium') badge-info
                            @else badge-success
                            @endif">
                            {{ ucfirst($incident->priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge
                            @if(in_array($incident->status, ['open', 'pending'])) badge-warning
                            @elseif($incident->status === 'in_progress') badge-info
                            @elseif(in_array($incident->status, ['escalated'])) badge-error
                            @elseif($incident->status === 'resolved') badge-success
                            @else badge-dark
                            @endif">
                            {{ ucfirst(str_replace('_', ' ', $incident->status)) }}
                        </span>
                    </td>
                    <td>{{ $incident->assignedTo?->name ?? 'Unassigned' }}</td>
                    <td style="color: #94a3b8; font-size: 13px;">{{ $incident->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="{{ route('admin.incidents.show', $incident) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">View</a>
                            <a href="{{ route('admin.incidents.edit', $incident) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            <p>No incidents found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination">
    {{ $incidents->links() }}
</div>
@endsection

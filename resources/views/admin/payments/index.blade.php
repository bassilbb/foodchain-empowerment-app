@extends('admin.layouts.app')

@section('title', 'Payments')
@section('header', 'Payment Management')
@section('subheader', 'Track and manage all platform payments')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Total Revenue</p>
        <p class="stat-value">{{ format_currency($totalAmount) }}</p>
    </div>
    <div class="stat-card success-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Successful</p>
        <p class="stat-value">{{ $successfulCount }}</p>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Pending</p>
        <p class="stat-value">{{ $pendingCount }}</p>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon red">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Failed</p>
        <p class="stat-value">{{ $failedCount }}</p>
    </div>
</div>

<div class="page-card">
    <div class="page-card-body">
        <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label class="form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="form-input">
            </div>
            <div class="form-group" style="min-width: 150px; margin-bottom: 0;">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    <option value="membership" {{ request('type') == 'membership' ? 'selected' : '' }}>Membership</option>
                    <option value="starter_pack" {{ request('type') == 'starter_pack' ? 'selected' : '' }}>Starter Pack</option>
                </select>
            </div>
            <div class="form-group" style="min-width: 150px; margin-bottom: 0;">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="successful" {{ request('status') == 'successful' ? 'selected' : '' }}>Successful</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">Filter</button>
                <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="page-card">
    <div class="page-card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>User</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td style="font-family: monospace; font-size: 0.875rem; color: #1e293b;">{{ $payment->reference }}</td>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;">{{ $payment->user?->full_name ?? 'Unknown' }}</div>
                        <div style="font-size: 0.875rem; color: #64748b;">{{ $payment->user?->email ?? '-' }}</div>
                    </td>
                    <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $payment->type) }}</td>
                    <td style="font-weight: 700; color: #1e293b;">{{ format_currency($payment->amount) }}</td>
                    <td>
                        <span class="badge
                            @if($payment->status === 'successful') badge-success
                            @elseif($payment->status === 'pending') badge-warning
                            @else badge-error
                            @endif">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td style="font-size: 0.875rem; color: #64748b;">{{ $payment->created_at->format('M d, Y h:i A') }}</td>
                    <td>
                        <a href="{{ route('admin.payments.show', $payment) }}" class="action-link">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                            <p>No payments found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($payments->hasPages())
<div class="pagination">
    {{ $payments->links() }}
</div>
@endif
@endsection

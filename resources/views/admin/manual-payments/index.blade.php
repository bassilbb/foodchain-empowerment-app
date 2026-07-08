@extends('admin.layouts.app')

@section('title', 'Manual Payments')
@section('header', 'Manual Payments')
@section('subheader', 'Verify and manage manual bank transfer payments')

@section('content')
<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Pending</p>
        <p class="stat-value">{{ number_format($stats['pending']) }}</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Verified</p>
        <p class="stat-value">{{ number_format($stats['verified']) }}</p>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon red">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Rejected</p>
        <p class="stat-value">{{ number_format($stats['rejected']) }}</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        </div>
        <p class="stat-label">Total</p>
        <p class="stat-value">{{ number_format($stats['total']) }}</p>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="flex: 1; min-width: 200px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by reference, name, email..." class="form-input">
        </div>
        <div class="form-group" style="min-width: 150px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="form-group" style="min-width: 150px;">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="annual_subscription" {{ request('type') === 'annual_subscription' ? 'selected' : '' }}>Annual Subscription</option>
                <option value="instant_food_pack" {{ request('type') === 'instant_food_pack' ? 'selected' : '' }}>Instant Food Pack</option>
                <option value="donation" {{ request('type') === 'donation' ? 'selected' : '' }}>Donation</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</div>

<!-- Payments Table -->
<div class="page-card">
    <div class="page-card-body" style="padding: 0; overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>User</th>
                    <th>Bank</th>
                    <th>Amount</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr>
                    <td>
                        <span style="font-family: monospace; color: #1e293b;">{{ $payment->reference }}</span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #10b981, #0ea5e9); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 700;">
                                {{ substr($payment->user?->first_name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $payment->user?->full_name ?? 'N/A' }}</p>
                                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">{{ $payment->user?->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        {{ $payment->bankAccount?->bank_name ?? 'N/A' }}
                        <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">{{ $payment->bankAccount?->account_number ?? '' }}</p>
                    </td>
                    <td style="font-weight: 700; color: #1e293b;">{{ format_currency($payment->amount) }}</td>
                    <td>
                        <span class="badge badge-dark" style="text-transform: capitalize;">{{ str_replace('_', ' ', $payment->type) }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $payment->status === 'pending' ? 'warning' : ($payment->status === 'verified' ? 'success' : 'error') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.manual-payments.show', $payment) }}" class="action-link">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="empty-state">
                        <p>No manual payments found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($payments->hasPages())
<div class="pagination">{{ $payments->links() }}</div>
@endif
@endsection

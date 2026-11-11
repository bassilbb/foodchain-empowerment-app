@extends('admin.layouts.app')

@section('title', 'Payment Verification')
@section('header', 'Payment Verification')
@section('subheader', 'Review bank transfer notifications and confirm funds before approving')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Pending Verification</p>
        <p class="stat-value">{{ number_format($stats['pending']) }}</p>
    </div>
    <div class="stat-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Approved</p>
        <p class="stat-value">{{ number_format($stats['approved']) }}</p>
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
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Amount Awaiting Verification</p>
        <p class="stat-value">{{ format_currency($stats['pending_amount']) }}</p>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
        <div class="form-group" style="flex: 1; min-width: 200px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Reference, name, email, order number..." class="form-input">
        </div>
        <div class="form-group" style="min-width: 150px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Approved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="form-group" style="min-width: 160px;">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="marketplace_order" {{ request('type') === 'marketplace_order' ? 'selected' : '' }}>Marketplace Order</option>
                <option value="wallet_topup" {{ request('type') === 'wallet_topup' ? 'selected' : '' }}>Wallet Top-up</option>
                <option value="annual_subscription" {{ request('type') === 'annual_subscription' ? 'selected' : '' }}>Annual Subscription</option>
                <option value="instant_food_pack" {{ request('type') === 'instant_food_pack' ? 'selected' : '' }}>Instant Food Pack</option>
                <option value="donation" {{ request('type') === 'donation' ? 'selected' : '' }}>Donation</option>
            </select>
        </div>
        <div class="form-group" style="min-width: 150px;">
            <label class="form-label">Order Number</label>
            <input type="text" name="order_number" value="{{ request('order_number') }}" placeholder="e.g. ORD-..." class="form-input">
        </div>
        <div class="form-group" style="min-width: 150px;">
            <label class="form-label">Reference</label>
            <input type="text" name="reference" value="{{ request('reference') }}" placeholder="MP-..." class="form-input">
        </div>
        <div class="form-group" style="min-width: 160px;">
            <label class="form-label">Customer</label>
            <input type="text" name="customer" value="{{ request('customer') }}" placeholder="Name, email or phone" class="form-input">
        </div>
        <div class="form-group" style="min-width: 150px;">
            <label class="form-label">From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input">
        </div>
        <div class="form-group" style="min-width: 150px;">
            <label class="form-label">To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['search', 'status', 'type', 'order_number', 'reference', 'customer', 'date_from', 'date_to']))
            <a href="{{ route('admin.manual-payments.index') }}" class="btn" style="background: #f1f5f9; color: #475569;">Reset</a>
        @endif
    </form>
</div>

<!-- Payments Table -->
<div class="page-card">
    <div class="page-card-body" style="padding: 0; overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Order</th>
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
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, var(--fc-primary-light), var(--fc-secondary)); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 700;">
                                {{ substr($payment->user?->first_name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $payment->user?->full_name ?? 'N/A' }}</p>
                                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">{{ $payment->user?->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($payment->marketplaceOrder)
                            <span style="font-family: monospace; font-size: 0.8rem; color: #1e293b;">{{ $payment->marketplaceOrder->order_number }}</span>
                            <p style="font-size: 0.72rem; color: #94a3b8; margin: 0;">Total: {{ format_currency($payment->marketplaceOrder->total) }}</p>
                        @else
                            <span style="color: #94a3b8;">—</span>
                        @endif
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
                            {{ $payment->status === 'verified' ? 'Approved' : ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td>{{ $payment->created_at->format('M d, Y') }}</td>
                    <td style="text-align: right;">
                        <a href="{{ route('admin.manual-payments.show', $payment) }}" class="action-link">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="empty-state">
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

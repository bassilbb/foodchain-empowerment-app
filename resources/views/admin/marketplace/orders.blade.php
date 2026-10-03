@extends('admin.layouts.app')

@section('title', 'Marketplace Orders')
@section('header', 'Marketplace Orders')
@section('subheader', 'Review, confirm and manage customer orders end-to-end.')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Orders</h1>
        <p>{{ number_format($orders->total()) }} order(s) found.</p>
    </div>
    <div class="header-actions-btns">
        <a href="{{ route('admin.marketplace.reports') }}" class="btn btn-secondary">View Reports</a>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.marketplace.orders.index') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 220px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Order number or customer name...">
        </div>
        <div style="min-width: 180px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All statuses</option>
                @foreach($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.marketplace.orders.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Items</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr>
                <td style="font-weight: 600; color: #1e293b; white-space: nowrap;">{{ $order->order_number }}</td>
                <td>
                    <div style="font-weight: 600; color: #475569;">{{ $order->user?->name ?? 'Unknown' }}</div>
                    <div style="font-size: 12px; color: #94a3b8;">{{ $order->user?->email ?? '' }}</div>
                </td>
                <td>{{ number_format($order->items_count ?? $order->items->count()) }}</td>
                <td style="font-weight: 700; color: #059669; white-space: nowrap;">{{ format_currency($order->total) }}</td>
                <td style="white-space: nowrap;">
                    <span style="font-size: 12px; color: #475569;">{{ \App\Models\Marketplace\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</span>
                    <div>
                        <span class="badge
                            @if($order->payment_status === 'paid') badge-success
                            @elseif($order->payment_status === 'pending') badge-warning
                            @elseif($order->payment_status === 'partially_paid') badge-info
                            @else badge-error @endif">{{ str_replace('_', ' ', ucfirst($order->payment_status)) }}</span>
                    </div>
                </td>
                <td>
                    <span class="badge
                        @if($order->status === 'delivered') badge-success
                        @elseif(in_array($order->status, ['cancelled', 'refunded'])) badge-error
                        @elseif(in_array($order->status, ['shipped', 'packed', 'processing', 'confirmed', 'paid'])) badge-info
                        @else badge-warning @endif">{{ $statuses[$order->status] ?? ucfirst($order->status) }}</span>
                </td>
                <td style="white-space: nowrap; font-size: 13px; color: #64748b;">{{ $order->created_at->format('M d, Y') }}</td>
                <td style="white-space: nowrap;">
                    <a href="{{ route('admin.marketplace.orders.show', $order) }}" class="action-link">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8"><div class="empty-state">No orders found</div></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $orders->links() }}
</div>
@endsection

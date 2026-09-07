@extends('admin.layouts.app')

@section('title', 'Order '.$order->order_number)
@section('header', 'Order Details')
@section('subheader', $order->order_number)

@section('content')
<style>
    .status-timeline { display: flex; flex-direction: column; gap: 0; }
    .timeline-item { display: flex; gap: 14px; position: relative; padding-bottom: 20px; }
    .timeline-item:not(:last-child)::before { content: ''; position: absolute; left: 11px; top: 24px; bottom: 0; width: 2px; background: #e2e8f0; }
    .timeline-dot { width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: #f1f5f9; color: #94a3b8; }
    .timeline-dot.current { background: #d1fae5; color: #059669; }
    .timeline-dot.error { background: #fee2e2; color: #dc2626; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 12px;">
    <a href="{{ route('admin.marketplace.orders.index') }}" class="btn btn-secondary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Orders
    </a>
    <span class="badge
        @if($order->status === 'delivered') badge-success
        @elseif(in_array($order->status, ['cancelled', 'refunded'])) badge-error
        @elseif(in_array($order->status, ['shipped', 'packed', 'processing', 'confirmed', 'paid'])) badge-info
        @else badge-warning @endif" style="font-size: 14px; padding: 8px 16px;">
        {{ \App\Models\Marketplace\Order::STATUSES[$order->status] ?? ucfirst($order->status) }}
    </span>
</div>

@if(session('success'))
<div class="alert alert-success" style="margin-bottom: 1rem;">{{ session('success') }}</div>
@endif

<!-- Status update -->
<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Update Order Status</h3>
            <p class="page-card-subtitle">Advance or resolve this order (customer is notified by email)</p>
        </div>
    </div>
    <div class="page-card-body">
        <form method="POST" action="{{ route('admin.marketplace.orders.status', $order) }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
            @csrf
            <div style="min-width: 220px; flex: 1;">
                <label class="form-label">New Status</label>
                <select name="status" class="form-select">
                    @foreach(\App\Models\Marketplace\Order::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ $order->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div style="min-width: 300px; flex: 2;">
                <label class="form-label">Note (optional)</label>
                <input type="text" name="note" class="form-input" placeholder="e.g., Courier dispatched, tracking FCE-1234">
            </div>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
        @if($order->wallet_amount > 0 && $order->payment_status !== 'refunded')
        <p style="font-size: 13px; color: #d97706; margin: 12px 0 0 0;">
            Setting status to <strong>Refunded</strong> will automatically refund {{ format_currency($order->wallet_amount) }} to the customer's FoodChain wallet.
        </p>
        @endif
    </div>
</div>

<div class="grid-2" style="margin-bottom: 2rem;">
    <!-- Order info -->
    <div class="page-card">
        <div class="page-card-header">
            <div><h3 class="page-card-title">Order Information</h3></div>
        </div>
        <div class="page-card-body">
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Order Number</span><span style="font-weight: 600; color: #1e293b;">{{ $order->order_number }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Placed On</span><span style="font-weight: 600; color: #1e293b;">{{ $order->created_at->format('M d, Y h:i A') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Customer</span>
                <span style="font-weight: 600; color: #1e293b;">
                    @if($order->user)
                    <a href="{{ route('admin.users.show', $order->user->id) }}" class="action-link">{{ $order->user->name }}</a>
                    <div style="font-size: 12px; color: #94a3b8; text-align: right;">{{ $order->user->email }}</div>
                    @else — @endif
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Delivery Type</span>
                <span style="font-weight: 600; color: #1e293b;">{{ ucwords(str_replace('_', ' ', $order->delivery_type ?? '—')) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Delivery Address</span>
                <span style="font-weight: 600; color: #1e293b; text-align: right; max-width: 60%;">{{ $order->delivery_address ?? '—' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">City / State</span><span style="font-weight: 600; color: #1e293b;">{{ $order->delivery_city ?? '—' }}, {{ $order->delivery_state ?? '—' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Pickup Location</span><span style="font-weight: 600; color: #1e293b;">{{ $order->pickup_location ?? '—' }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Est. Delivery</span><span style="font-weight: 600; color: #1e293b;">{{ $order->estimated_delivery_date?->format('M d, Y') ?? '—' }}</span>
            </div>
            @if($order->delivery_notes)
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <span style="color: #64748b;">Notes</span><span style="font-weight: 600; color: #1e293b; text-align: right; max-width: 60%;">{{ $order->delivery_notes }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Payment info -->
    <div class="page-card">
        <div class="page-card-header">
            <div><h3 class="page-card-title">Payment Information</h3></div>
        </div>
        <div class="page-card-body">
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Subtotal</span><span style="font-weight: 600; color: #1e293b;">{{ format_currency($order->subtotal) }}</span>
            </div>
            @if($order->discount_amount > 0)
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Discount</span><span style="font-weight: 600; color: #059669;">-{{ format_currency($order->discount_amount) }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Shipping</span><span style="font-weight: 600; color: #1e293b;">{{ format_currency($order->shipping_fee) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Wallet Applied</span><span style="font-weight: 600; color: #1e293b;">{{ format_currency($order->wallet_amount) }}</span>
            </div>
            @if($order->cash_amount > 0)
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Cash on Delivery</span><span style="font-weight: 600; color: #1e293b;">{{ format_currency($order->cash_amount) }}</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="font-weight: 700; color: #1e293b;">Total</span><span style="font-weight: 700; color: #059669; font-size: 18px;">{{ format_currency($order->total) }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Payment Method</span>
                <span style="font-weight: 600; color: #1e293b;">{{ \App\Models\Marketplace\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Payment Status</span>
                <span class="badge
                    @if($order->payment_status === 'paid') badge-success
                    @elseif($order->payment_status === 'pending') badge-warning
                    @elseif($order->payment_status === 'partially_paid') badge-info
                    @else badge-error @endif">{{ str_replace('_', ' ', ucfirst($order->payment_status)) }}</span>
            </div>
            @if($order->payment_gateway)
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Gateway</span><span style="font-weight: 600; color: #1e293b; text-transform: capitalize;">{{ $order->payment_gateway }}</span>
            </div>
            @endif
            @if($order->payment_reference)
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Reference</span><span style="font-weight: 600; color: #1e293b;">{{ $order->payment_reference }}</span>
            </div>
            @endif
            @if($order->coupon)
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Coupon</span>
                <span class="badge badge-primary">{{ $order->coupon->code }} (-{{ format_currency($order->coupon_discount) }})</span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f1f5f9;">
                <span style="color: #64748b;">Loyalty Points Earned</span><span style="font-weight: 700; color: #7c3aed;">{{ number_format($order->loyalty_points_earned) }}</span>
            </div>
            @if($order->paid_at)
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <span style="color: #64748b;">Paid At</span><span style="font-weight: 600; color: #1e293b;">{{ $order->paid_at->format('M d, Y h:i A') }}</span>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Items -->
<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Order Items</h3>
            <p class="page-card-subtitle">{{ $order->items->count() }} item(s)</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Vendor</th>
                <th>Unit Price</th>
                <th>Qty</th>
                <th>Discount</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        @if($item->product && $item->product->image)
                        <img src="{{ $item->product->image }}" alt="" style="width: 36px; height: 36px; border-radius: 8px; object-fit: cover;">
                        @endif
                        <div>
                            <div style="font-weight: 600; color: #1e293b;">{{ $item->product_name }}</div>
                            <div style="font-size: 12px; color: #94a3b8;">{{ $item->product_sku }}</div>
                        </div>
                    </div>
                </td>
                <td style="white-space: nowrap;">{{ $item->vendor?->business_name ?? '—' }}</td>
                <td>{{ format_currency($item->unit_price) }}</td>
                <td>{{ number_format($item->quantity) }}</td>
                <td>{{ $item->discount_amount > 0 ? '-'.format_currency($item->discount_amount) : '—' }}</td>
                <td style="font-weight: 700; color: #059669;">{{ format_currency($item->total_price) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Status history -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Status History</h3>
            <p class="page-card-subtitle">{{ $order->statusHistories->count() }} status change(s)</p>
        </div>
    </div>
    <div class="page-card-body">
        <div class="status-timeline">
            @forelse($order->statusHistories as $index => $history)
            @php
                $isLast = $index === $order->statusHistories->count() - 1;
                $isError = in_array($history->status, ['cancelled', 'refunded']);
            @endphp
            <div class="timeline-item">
                <div class="timeline-dot {{ $isLast ? ($isError ? 'error' : 'current') : '' }}">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; color: #1e293b;">{{ \App\Models\Marketplace\Order::STATUSES[$history->status] ?? ucfirst($history->status) }}</span>
                        <span style="font-size: 12px; color: #94a3b8;">{{ $history->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($history->note)
                    <p style="font-size: 13px; color: #64748b; margin: 4px 0 0 0;">{{ $history->note }}</p>
                    @endif
                </div>
            </div>
            @empty
            <p style="color: #94a3b8;">No status history recorded.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

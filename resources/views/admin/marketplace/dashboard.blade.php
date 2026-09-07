@extends('admin.layouts.app')

@section('title', 'Marketplace Overview')
@section('header', 'Marketplace Overview')
@section('subheader', 'Sales, products, vendors and activity across the FoodChain marketplace.')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Marketplace Dashboard</h1>
        <p>Track orders, revenue and catalogue health for the FoodChain marketplace.</p>
    </div>
    <div class="header-actions-btns">
        <a href="{{ route('admin.marketplace.products.index') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            Manage Products
        </a>
        <a href="{{ route('admin.marketplace.orders.index', ['status' => 'pending']) }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            Pending Orders ({{ number_format($pendingOrders) }})
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card accent">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Total Revenue</p>
        <p class="stat-value">{{ format_currency($totalRevenue) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge info">{{ format_currency($avgOrderValue) }} avg / order</span>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-icon teal">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </div>
        <p class="stat-label">Orders</p>
        <p class="stat-value">{{ number_format($orderCount) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge warning">{{ number_format($pendingOrders) }} pending</span>
            <span class="stat-badge success">{{ number_format($customerCount) }} customers</span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
        </div>
        <p class="stat-label">Products</p>
        <p class="stat-value">{{ number_format($productCount) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge info">{{ number_format($vendorCount) }} vendors</span>
            <a href="{{ route('admin.marketplace.products.index') }}" class="action-link">View →</a>
        </div>
    </div>

    <div class="stat-card danger">
        <div class="stat-icon red">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <p class="stat-label">Needs Attention</p>
        <p class="stat-value">{{ number_format($lowStockCount + $pendingReviews + $pendingVendors + $openBulkOrders) }}</p>
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="stat-badge error">{{ number_format($lowStockCount) }} low stock</span>
            <span class="stat-badge warning">{{ number_format($pendingReviews) }} reviews</span>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid-2" style="margin-bottom: 2rem;">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Sales Trend</h3>
                <p class="page-card-subtitle">Orders & revenue over the last 14 days</p>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #dbeafe; display: flex; align-items: center; justify-content: center;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #2563eb;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </div>
        </div>
        <div class="page-card-body">
            <canvas id="salesChart" style="width: 100%; height: 256px;"></canvas>
        </div>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Orders by Status</h3>
                <p class="page-card-subtitle">Current distribution across the 9 statuses</p>
            </div>
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #d1fae5; display: flex; align-items: center; justify-content: center;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #059669;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9 9 0 1020.945 13H11V3.055zM20.488 9H15V3.512a9.025 9.025 0 015.488 5.488z"></path></svg>
            </div>
        </div>
        <div class="page-card-body">
            @foreach(\App\Models\Marketplace\Order::STATUSES as $key => $label)
            @php $count = $statusCounts[$key] ?? 0; $pct = $orderCount > 0 ? ($count / $orderCount) * 100 : 0; @endphp
            <div style="margin-bottom: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <span style="font-weight: 600; color: #475569; font-size: 0.85rem;">{{ $label }}</span>
                    <span style="font-weight: 700; color: #1e293b; font-size: 0.85rem;">{{ number_format($count) }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill emerald" style="width: {{ $pct }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Recent Orders + Top Products -->
<div class="grid-2">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Recent Orders</h3>
                <p class="page-card-subtitle">Latest marketplace orders</p>
            </div>
            <a href="{{ route('admin.marketplace.orders.index') }}" class="action-link">View all →</a>
        </div>
        <div style="padding: 0;">
            @foreach($recentOrders as $order)
            <div class="list-item">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 44px; height: 44px; border-radius: 12px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #475569;">
                        {{ $order->order_number ? substr($order->order_number, -3) : '#' }}
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $order->order_number }}</p>
                        <p style="color: #64748b; font-size: 0.85rem; margin: 0;">{{ $order->user?->name ?? 'Unknown' }} · {{ format_currency($order->total) }}</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <span class="badge
                        @if($order->status === 'delivered') badge-success
                        @elseif(in_array($order->status, ['cancelled', 'refunded'])) badge-error
                        @elseif(in_array($order->status, ['shipped', 'packed', 'processing', 'confirmed', 'paid'])) badge-info
                        @else badge-warning @endif">{{ ucfirst($order->status) }}</span>
                    <p style="color: #94a3b8; font-size: 0.75rem; margin: 0;">{{ $order->created_at->format('M d, h:i A') }}</p>
                </div>
            </div>
            @endforeach
            @if($recentOrders->isEmpty())
            <div class="empty-state">
                <p>No marketplace orders yet.</p>
            </div>
            @endif
        </div>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Top Products</h3>
                <p class="page-card-subtitle">Best sellers by units sold</p>
            </div>
            <a href="{{ route('admin.marketplace.products.index') }}" class="action-link">View all →</a>
        </div>
        <div style="padding: 0;">
            @foreach($topProducts as $product)
            <div class="list-item">
                <div style="display: flex; align-items: center; gap: 1rem; min-width: 0;">
                    @if($product->image)
                    <img src="{{ $product->image }}" alt="" style="width: 44px; height: 44px; border-radius: 10px; object-fit: cover; background: #f1f5f9;">
                    @else
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 700; color: #94a3b8;">{{ substr($product->name, 0, 1) }}</div>
                    @endif
                    <div style="min-width: 0;">
                        <p style="font-weight: 600; color: #1e293b; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $product->name }}</p>
                        <p style="color: #64748b; font-size: 0.85rem; margin: 0;">{{ $product->category?->name ?? 'Uncategorised' }}</p>
                    </div>
                </div>
                <div style="text-align: right;">
                    <p style="font-weight: 700; color: #059669; margin: 0;">{{ number_format($product->sold_count) }} sold</p>
                    <p style="color: #94a3b8; font-size: 0.75rem; margin: 0;">{{ format_currency($product->selling_price) }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Attention Cards -->
<div class="stats-grid" style="margin-top: 2rem;">
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Low Stock Items</p>
        <p class="stat-value">{{ number_format($lowStockCount) }}</p>
        <a href="{{ route('admin.marketplace.products.index') }}" class="action-link">Review stock →</a>
    </div>
    <div class="stat-card info">
        <div class="stat-icon pink">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
        </div>
        <p class="stat-label">Pending Reviews</p>
        <p class="stat-value">{{ number_format($pendingReviews) }}</p>
        <a href="{{ route('admin.marketplace.reviews.index', ['pending' => 1]) }}" class="action-link">Moderate →</a>
    </div>
    <div class="stat-card accent">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <p class="stat-label">Pending Vendors</p>
        <p class="stat-value">{{ number_format($pendingVendors) }}</p>
        <a href="{{ route('admin.marketplace.vendors.index', ['status' => 'pending']) }}" class="action-link">Approve →</a>
    </div>
    <div class="stat-card success">
        <div class="stat-icon teal">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        </div>
        <p class="stat-label">Open Bulk Orders</p>
        <p class="stat-value">{{ number_format($openBulkOrders) }}</p>
        <a href="{{ route('admin.marketplace.bulk-orders.index') }}" class="action-link">Manage →</a>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const salesEl = document.getElementById('salesChart');
    if (salesEl) {
        const data = @json($salesChartData);
        new Chart(salesEl, {
            type: 'line',
            data: {
                labels: data.map(d => d.date),
                datasets: [
                    {
                        label: 'Revenue (₦)',
                        data: data.map(d => d.revenue),
                        borderColor: '#059669',
                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y',
                    },
                    {
                        label: 'Orders',
                        data: data.map(d => d.orders),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.08)',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y1',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: true, position: 'left', ticks: { callback: v => '₦' + Number(v).toLocaleString() } },
                    y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false }, ticks: { precision: 0 } }
                }
            }
        });
    }
});
</script>
@endpush

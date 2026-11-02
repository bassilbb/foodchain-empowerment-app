@extends('admin.layouts.app')

@section('title', 'Marketplace Reports')
@section('header', 'Marketplace Reports')
@section('subheader', 'Sales analytics and performance insights across the marketplace.')

@section('content')
<div class="page-header">
    <div>
        <h1>Reports</h1>
        <p>Last 30 days of marketplace activity.</p>
    </div>
</div>

@php
    $totalRevenue30 = $salesByDay->sum('revenue');
    $totalOrders30 = $salesByDay->sum('orders');
    $avgOrder30 = $totalOrders30 > 0 ? $totalRevenue30 / $totalOrders30 : 0;
    $deliveredRevenue = $salesByStatus->where('status', 'delivered')->sum('revenue');
@endphp

<!-- Stats -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card accent">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Revenue (30 days)</p>
        <p class="stat-value">{{ format_currency($totalRevenue30) }}</p>
    </div>
    <div class="stat-card success">
        <div class="stat-icon teal">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </div>
        <p class="stat-label">Orders (30 days)</p>
        <p class="stat-value">{{ number_format($totalOrders30) }}</p>
    </div>
    <div class="stat-card info">
        <div class="stat-icon pink">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        </div>
        <p class="stat-label">Avg Order Value</p>
        <p class="stat-value">{{ format_currency($avgOrder30) }}</p>
    </div>
    <div class="stat-card success-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Delivered Revenue</p>
        <p class="stat-value">{{ format_currency($deliveredRevenue) }}</p>
    </div>
</div>

<!-- Charts -->
<div class="grid-2" style="margin-bottom: 2rem;">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Daily Sales (30 days)</h3>
                <p class="page-card-subtitle">Revenue by day</p>
            </div>
        </div>
        <div class="page-card-body">
            <canvas id="dailySalesChart" style="width: 100%; height: 260px;"></canvas>
        </div>
    </div>
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Revenue by Status</h3>
                <p class="page-card-subtitle">Distribution across order statuses</p>
            </div>
        </div>
        <div class="page-card-body">
            <canvas id="statusChart" style="width: 100%; height: 260px;"></canvas>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Top vendors -->
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Top Vendors</h3>
                <p class="page-card-subtitle">By units sold</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Units Sold</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topVendors as $vendor)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;">{{ $vendor->business_name ?? $vendor->name }}</div>
                        <a href="{{ route('admin.marketplace.vendors.show', $vendor) }}" class="action-link" style="font-size: 12px;">View</a>
                    </td>
                    <td style="font-weight: 700; color: #059669;">{{ number_format($vendor->products_sum_sold_count) }}</td>
                    <td>
                        <span class="badge
                            @if($vendor->status === 'approved') badge-success
                            @elseif($vendor->status === 'pending') badge-warning
                            @else badge-error @endif">{{ ucfirst($vendor->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No vendor data yet.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Top categories -->
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Top Categories</h3>
                <p class="page-card-subtitle">By product count</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Products</th>
                    <th>Share</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topCategories as $category)
                @php
                    $total = $topCategories->sum('products_count');
                    $share = $total > 0 ? ($category->products_count / $total) * 100 : 0;
                @endphp
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #1e293b;">{{ $category->icon ? $category->icon.' ' : '' }}{{ $category->name }}</div>
                        @if($category->parent)<div style="font-size: 12px; color: #94a3b8;">{{ $category->parent->name }}</div>@endif
                    </td>
                    <td><span class="badge badge-primary">{{ number_format($category->products_count) }}</span></td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <div class="progress-bar" style="flex: 1; min-width: 80px;">
                                <div class="progress-fill sky" style="width: {{ $share }}%;"></div>
                            </div>
                            <span style="font-size: 12px; color: #64748b; white-space: nowrap;">{{ number_format($share, 0) }}%</span>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No category data yet.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const daily = document.getElementById('dailySalesChart');
    if (daily) {
        const data = @json($dailySalesChartData);
        new Chart(daily, {
            type: 'bar',
            data: {
                labels: data.map(d => d.date),
                datasets: [{
                    label: 'Revenue (₦)',
                    data: data.map(d => d.revenue),
                    backgroundColor: 'rgba(16, 185, 129, 0.75)',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { callback: v => '₦' + Number(v).toLocaleString() } } }
            }
        });
    }

    const statusChart = document.getElementById('statusChart');
    if (statusChart) {
        const data = @json($statusChartData);
        new Chart(statusChart, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.status),
                datasets: [{
                    data: data.map(d => d.revenue),
                    backgroundColor: ['#f59e0b', '#8b5cf6', '#2563eb', '#0ea5e9', '#14b8a6', '#10b981', '#84cc16', '#ef4444', '#64748b'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });
    }
});
</script>
@endpush

@extends('admin.layouts.app')

@section('title', 'Reports')
@section('header', 'Reports')
@section('subheader', 'Marketplace performance across sales, customers, products, vendors and payouts.')

@section('content')
<div class="page-header">
    <div>
        <h1>Reports</h1>
        <p>Everything below is measured from live marketplace orders.</p>
    </div>
</div>

@php
    $reports = [
        ['route' => 'admin.reports.sales', 'label' => 'Sales', 'desc' => 'Revenue, order volume, payment mix and refunds', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['route' => 'admin.reports.customers', 'label' => 'Customers', 'desc' => 'Buyers, repeat rate, top spenders and wallet liability', 'icon' => 'M16 11V7a4 4 0 10-8 0v4m-3 0h14l1 12H4L5 11zm7 0v6'],
        ['route' => 'admin.reports.products', 'label' => 'Products', 'desc' => 'Best sellers, revenue leaders, low stock and category performance', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
        ['route' => 'admin.reports.vendors', 'label' => 'Vendors', 'desc' => 'Supplier performance, commission owed and wallet balances', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m16 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
        ['route' => 'admin.reports.payouts', 'label' => 'Payouts & Wallets', 'desc' => 'Vendor payout pipeline and customer wallet movement', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
    ];
@endphp

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 32px;">
    @foreach($reports as $report)
        <a href="{{ route($report['route']) }}" class="page-card" style="text-decoration: none;">
            <div style="display: flex; align-items: flex-start; gap: 16px;">
                <div class="stat-icon emerald" style="flex-shrink: 0;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $report['icon'] }}"></path></svg>
                </div>
                <div>
                    <h3 class="page-card-title" style="margin-bottom: 6px;">{{ $report['label'] }}</h3>
                    <p class="page-card-subtitle">{{ $report['desc'] }}</p>
                </div>
            </div>
        </a>
    @endforeach
</div>

<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card accent">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Lifetime Revenue</p>
        <p class="stat-value">{{ format_currency($revenue) }}</p>
    </div>
    <div class="stat-card success-card">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
        </div>
        <p class="stat-label">Orders</p>
        <p class="stat-value">{{ number_format($orderCount) }}</p>
    </div>
    <div class="stat-card info">
        <div class="stat-icon indigo">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 10-8 0v4m-3 0h14l1 12H4L5 11zm7 0v6"></path></svg>
        </div>
        <p class="stat-label">Customers</p>
        <p class="stat-value">{{ number_format($customerCount) }}</p>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </div>
        <p class="stat-label">Avg Order Value</p>
        <p class="stat-value">{{ format_currency($avgOrderValue) }}</p>
    </div>
</div>

<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Revenue Trend</h3>
            <p class="page-card-subtitle">Last 12 months</p>
        </div>
        <div style="text-align: right;">
            <span class="badge badge-primary">{{ number_format($productCount) }} products</span>
            <span class="badge badge-success">{{ number_format($vendorCount) }} active vendors</span>
        </div>
    </div>
    <div class="chart-box tall">
        <canvas id="monthlySalesChart"></canvas>
    </div>
</div>

<div class="grid-2">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Top Categories</h3>
                <p class="page-card-subtitle">By revenue</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Units</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topCategories as $row)
                <tr>
                    <td style="font-weight: 600;">{{ $row->icon ? $row->icon.' ' : '' }}{{ $row->name }}</td>
                    <td>{{ number_format($row->units) }}</td>
                    <td style="font-weight: 700; color: #059669;">{{ format_currency($row->revenue) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No sales recorded yet.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Top Vendors</h3>
                <p class="page-card-subtitle">By revenue</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Units</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topVendors as $row)
                <tr>
                    <td>
                        <a href="{{ route('admin.marketplace.vendors.show', $row->id) }}" class="action-link" style="font-weight: 600; color: #1e293b;">
                            {{ $row->business_name ?? $row->name }}
                        </a>
                    </td>
                    <td>{{ number_format($row->units) }}</td>
                    <td style="font-weight: 700; color: #059669;">{{ format_currency($row->revenue) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No sales recorded yet.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="page-card" style="margin-top: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Top Products</h3>
            <p class="page-card-subtitle">By revenue</p>
        </div>
        @if($refunded > 0)
            <span class="badge badge-error">{{ format_currency($refunded) }} refunded</span>
        @endif
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>SKU</th>
                <th>Units</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topProducts as $row)
            <tr>
                <td>
                    <a href="{{ route('admin.marketplace.products.show', $row->id) }}" class="action-link" style="font-weight: 600; color: #1e293b;">
                        {{ $row->name }}
                    </a>
                </td>
                <td style="color: #64748b;">{{ $row->sku }}</td>
                <td>{{ number_format($row->units) }}</td>
                <td style="font-weight: 700; color: #059669;">{{ format_currency($row->revenue) }}</td>
            </tr>
            @empty
            <tr><td colspan="4"><div class="empty-state">No sales recorded yet.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('monthlySalesChart');
    if (el) {
        const data = @json($monthlyChart);
        new Chart(el, {
            type: 'line',
            data: {
                labels: data.map(d => d.month),
                datasets: [{
                    label: 'Revenue',
                    data: data.map(d => d.revenue),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.12)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 3,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { callback: v => '₦' + Number(v).toLocaleString() } } }
            }
        });
    }
});
</script>
@endpush

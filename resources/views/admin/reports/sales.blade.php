@extends('admin.layouts.app')

@section('title', 'Sales Report')
@section('header', 'Sales Report')
@section('subheader', 'Revenue, order volume and payment mix for the store.')

@section('content')
<div class="page-header">
    <div>
        <h1>Sales Report</h1>
        <p>Order value for the last {{ $days }} days, excluding cancelled orders.</p>
    </div>
    <div class="header-actions-btns">
        @foreach($ranges as $range)
            <a href="{{ route('admin.reports.sales', ['days' => $range]) }}"
               class="btn {{ $days === $range ? 'btn-primary' : 'btn-secondary' }}">{{ $range }}d</a>
        @endforeach
    </div>
</div>

<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card accent">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Gross Sales ({{ $days }}d)</p>
        <p class="stat-value">{{ format_currency($grossSales) }}</p>
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
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
        </div>
        <p class="stat-label">Avg Order Value</p>
        <p class="stat-value">{{ format_currency($avgOrderValue) }}</p>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </div>
        <p class="stat-label">Refunds</p>
        <p class="stat-value">{{ format_currency($refundTotal) }}</p>
    </div>
</div>

<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Sales Revenue</h3>
            <p class="page-card-subtitle">
                Last {{ $days }} days, grouped by {{ $seriesInterval }}
                &middot; {{ count($dailyChart) }} {{ $seriesInterval }}s plotted
            </p>
        </div>
    </div>
    @if($hasSeriesRevenue)
        <div class="chart-box tall">
            <canvas id="dailySalesChart"></canvas>
        </div>
    @else
        <div class="chart-box tall">
            <div class="chart-empty">No sales recorded in the last {{ $days }} days.</div>
        </div>
    @endif
</div>

<div class="grid-2" style="margin-bottom: 2rem;">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Revenue by Status</h3>
                <p class="page-card-subtitle">Where order value currently sits</p>
            </div>
        </div>
        @if($hasSeriesRevenue || $salesByStatus->isNotEmpty())
            <div class="chart-box">
                <canvas id="statusChart"></canvas>
            </div>
        @else
            <div class="chart-box">
                <div class="chart-empty">No order activity in this period.</div>
            </div>
        @endif
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Order Value Summary</h3>
                <p class="page-card-subtitle">What the {{ $days }}-day figure is made of</p>
            </div>
        </div>
        <div class="stats-grid-3" style="margin-bottom: 0;">
            <div class="stat-card">
                <p class="stat-label">Net Sales</p>
                <p class="stat-value">{{ format_currency($netSales) }}</p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Shipping Collected</p>
                <p class="stat-value">{{ format_currency($shipping) }}</p>
            </div>
            <div class="stat-card">
                <p class="stat-label">Discounts Given</p>
                <p class="stat-value">{{ format_currency($discounts) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="page-card" style="margin-bottom: 2rem;">
    <div class="alert alert-warning">
        <strong>How these figures relate.</strong>
        Gross Sales counts every order that was actually charged, including orders that were later
        refunded, and Refunds is deducted from it exactly once to give Net Sales. Cancelled orders were
        never charged, so they are excluded from Gross Sales and from every count, chart and average
        on this page. A <em>partially</em> refunded order is treated as refunded for those counts, but
        its unrefunded remainder is still carried in Net Sales &mdash; so Net Sales can read slightly
        higher than Orders multiplied by the chart.
    </div>
</div>

<div class="grid-2">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">By Payment Method</h3>
                <p class="page-card-subtitle">How customers paid</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Method</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byPaymentMethod as $row)
                <tr>
                    <td style="font-weight: 600;">{{ \App\Models\Marketplace\Order::PAYMENT_METHODS[$row->payment_method] ?? ucfirst(str_replace('_', ' ', $row->payment_method)) }}</td>
                    <td>{{ number_format($row->orders) }}</td>
                    <td style="font-weight: 700; color: #059669;">{{ format_currency($row->revenue) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No orders in this period.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Top Delivery States</h3>
                <p class="page-card-subtitle">Where orders are shipped</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>State</th>
                    <th>Orders</th>
                    <th>Revenue</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byState as $row)
                <tr>
                    <td style="font-weight: 600;">{{ $row->delivery_state }}</td>
                    <td>{{ number_format($row->orders) }}</td>
                    <td style="font-weight: 700; color: #059669;">{{ format_currency($row->revenue) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No delivery data yet.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="page-card" style="margin-top: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Orders by Status</h3>
            <p class="page-card-subtitle">Every status, including voided orders</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Status</th>
                <th>Orders</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($salesByStatus as $row)
            <tr>
                <td>
                    <span class="badge @if($row->status === 'delivered') badge-success @elseif($row->status === 'cancelled' || $row->status === 'refunded') badge-error @else badge-warning @endif">
                        {{ \App\Models\Marketplace\Order::STATUSES[$row->status] ?? ucfirst($row->status) }}
                    </span>
                </td>
                <td>{{ number_format($row->orders) }}</td>
                <td style="font-weight: 700; color: #059669;">{{ format_currency($row->revenue) }}</td>
            </tr>
            @empty
            <tr><td colspan="3"><div class="empty-state">No orders in this period.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const naira = v => '₦' + Number(v || 0).toLocaleString('en-NG', { maximumFractionDigits: 0 });
    const nairaExact = v => '₦' + Number(v || 0).toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const daily = document.getElementById('dailySalesChart');
    if (daily) {
        const data = @json($dailyChart);
        const interval = @json($seriesInterval);

        new Chart(daily, {
            type: 'bar',
            data: {
                labels: data.map(d => d.label),
                datasets: [{
                    label: 'Revenue',
                    data: data.map(d => d.revenue),
                    backgroundColor: data.map(d => d.revenue > 0 ? 'rgba(16, 185, 129, 0.8)' : 'rgba(203, 213, 225, 0.45)'),
                    borderRadius: 6,
                    maxBarThickness: 56,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            title: items => (interval === 'week' ? 'Week of ' : '') + items[0].label,
                            label: item => '  Revenue: ' + nairaExact(item.parsed.y),
                            afterLabel: item => '  Orders: ' + data[item.dataIndex].orders
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { autoSkip: true, maxRotation: 0, minRotation: 0 }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false },
                        ticks: { callback: v => naira(v) }
                    }
                }
            }
        });
    }

    const statusChart = document.getElementById('statusChart');
    if (statusChart) {
        const data = @json($statusChart);
        const total = data.reduce((sum, d) => sum + d.revenue, 0);

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
                cutout: '58%',
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 12, padding: 10 } },
                    tooltip: {
                        callbacks: {
                            label: item => {
                                const share = total > 0 ? ' (' + ((item.parsed / total) * 100).toFixed(1) + '%)' : '';
                                return '  ' + item.label + ': ' + nairaExact(item.parsed) + share;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush

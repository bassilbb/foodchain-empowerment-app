@extends('vendor.layouts.app')

@section('title', 'Vendor Dashboard')
@section('header', 'Dashboard')
@section('subheader', $vendor->business_name ?? $vendor->name)

@section('content')

@php
$lowStockCount = $stats['low_stock'];
$pendingPayoutCount = $stats['pending_payout'] > 0 ? 1 : null;
@endphp

@if ($vendor->status !== 'approved')
<div class="mb-5 flex flex-wrap items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4">
    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
        </svg>
    </span>
    <div class="min-w-0 flex-1">
        <p class="text-sm font-bold text-amber-900">Your vendor account is {{ $vendor->status }}</p>
        <p class="text-xs text-amber-700">
            @if ($vendor->status === 'pending')
            An administrator needs to approve your store before your products appear on the storefront.
            @else
            Your store is not currently active on the marketplace.
            @endif
        </p>
    </div>
</div>
@endif

{{-- Primary KPIs --}}
<div class="grid grid-cols-2 gap-4 animate-slide-up lg:grid-cols-4" style="animation-delay: 90ms">
    <x-marketplace.stat-card label="Today's Sales" :value="format_currency($stats['today_sales'])" icon="spend" tone="primary" hint="Delivered items only" />
    <x-marketplace.stat-card label="This Month" :value="format_currency($stats['month_sales'])" icon="analytics" tone="info" :hint="'Week: '.format_currency($stats['week_sales'])" />
    <x-marketplace.stat-card label="Pending Orders" :value="number_format($stats['pending_orders'])" icon="pending" tone="cta"
        :href="route('vendor.orders.index', ['status' => 'new'])" hint="Awaiting your action" />
    <x-marketplace.stat-card label="Products" :value="number_format($stats['products'])" icon="products" tone="neutral"
        :href="route('vendor.products.index')" :hint="$stats['active_products'].' active'" />
</div>

{{-- Secondary KPIs --}}
<div class="mt-4 grid grid-cols-2 gap-4 animate-slide-up lg:grid-cols-4" style="animation-delay: 140ms">
    <x-marketplace.stat-card label="Low Stock" :value="number_format($stats['low_stock'])" icon="pending" tone="cta"
        :href="route('vendor.products.index', ['stock' => 'low'])" :hint="$stats['out_of_stock'].' out of stock'" />
    <x-marketplace.stat-card label="Total Sales" :value="format_currency($stats['total_sales'])" icon="spend" tone="primary" hint="Lifetime delivered" />
    <x-marketplace.stat-card label="Pending Payout" :value="format_currency($stats['pending_payout'])" icon="wallet" tone="neutral"
        :href="route('vendor.payouts.index')" hint="Requested or processing" />
    <x-marketplace.stat-card label="Available" :value="format_currency($stats['available'])" icon="delivered" tone="info" hint="Ready to withdraw" />
</div>

{{-- Sales chart --}}
@php
$dailyLabels = array_column($chart['daily'], 'date');
$dailyValues = array_column($chart['daily'], 'total');
$monthlyLabels = array_column($chart['monthly'], 'period');
$monthlyValues = array_column($chart['monthly'], 'total');
@endphp

<div class="mt-6 animate-slide-up" style="animation-delay: 200ms">
    <x-marketplace.card title="Sales Trend" subtitle="Delivered sales over the last 14 days">
        <x-slot:actions>
            <a href="{{ route('vendor.reports') }}" class="fc-btn-ghost text-xs">Full analytics</a>
        </x-slot:actions>

        @if (empty($dailyValues) || array_sum($dailyValues)
        <= 0)
            <x-marketplace.empty-state
            icon="analytics"
            title="No delivered sales yet"
            message="Your sales chart appears once orders are delivered. Earnings are recognised for delivered items only." />
        @else
        <div class="h-64">
            <canvas id="vendorSalesChart"></canvas>
        </div>
        @endif
    </x-marketplace.card>
</div>

{{-- Recent orders + top products --}}
<div class="mt-6 grid grid-cols-1 gap-5 animate-slide-up lg:grid-cols-2" style="animation-delay: 250ms">

    <x-marketplace.card title="Recent Order Items" :flush="true">
        <x-slot:actions>
            <a href="{{ route('vendor.orders.index') }}" class="fc-btn-ghost text-xs">View all</a>
        </x-slot:actions>

        @if ($recentItems->isEmpty())
        <x-marketplace.empty-state
            icon="orders"
            title="No orders yet"
            message="Orders containing your products will appear here." />
        @else
        <ul class="divide-y divide-slate-50">
            @foreach ($recentItems as $item)
            <li class="flex items-center gap-4 px-5 py-3.5">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $item->product_name }}</p>
                    <p class="mt-0.5 text-[11px] text-slate-500">
                        {{ $item->order?->order_number }} ·
                        {{ $item->order?->user?->full_name ?? 'Customer' }} ·
                        {{ $item->quantity }}×
                    </p>
                </div>

                <div class="shrink-0 text-right">
                    <p class="text-sm font-bold text-slate-800">{{ format_currency($item->total_price) }}</p>
                    <div class="mt-0.5 flex justify-end">
                        <x-marketplace.status-badge :status="$item->status" type="order" />
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        @endif
    </x-marketplace.card>

    <x-marketplace.card title="Best Selling Products" :flush="true">
        <x-slot:actions>
            <a href="{{ route('vendor.products.index') }}" class="fc-btn-ghost text-xs">Manage</a>
        </x-slot:actions>

        @if ($topProducts->isEmpty())
        <x-marketplace.empty-state
            icon="products"
            title="No sales data yet"
            message="Your best sellers appear after your first delivered orders."
            :action-url="route('vendor.products.create')"
            action-label="Add a product" />
        @else
        <ul class="divide-y divide-slate-50">
            @foreach ($topProducts as $index => $row)
            <li class="flex items-center gap-4 px-5 py-3.5">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-[11px] font-bold text-emerald-700">{{ $index + 1 }}</span>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $row->product?->name ?? 'Product removed' }}</p>
                    <p class="text-[11px] text-slate-500">{{ $row->units }} units sold</p>
                </div>

                <p class="shrink-0 text-sm font-bold text-emerald-700">{{ format_currency($row->revenue) }}</p>
            </li>
            @endforeach
        </ul>
        @endif
    </x-marketplace.card>
</div>

{{-- Low stock alert --}}
@if ($lowStockProducts->isNotEmpty())
<div class="mt-6 animate-slide-up" style="animation-delay: 300ms">
    <x-marketplace.card title="Low Stock Alert" subtitle="These products are close to running out" :flush="true">
        <x-slot:actions>
            <a href="{{ route('vendor.inventory') }}" class="fc-btn-ghost text-xs">Manage inventory</a>
        </x-slot:actions>

        <ul class="divide-y divide-slate-50">
            @foreach ($lowStockProducts as $product)
            <li class="flex items-center justify-between gap-4 px-5 py-3.5">
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $product->name }}</p>
                    <p class="text-[11px] text-slate-500">Threshold: {{ $product->low_stock_threshold }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-3">
                    <span class="text-sm font-bold text-amber-600">{{ $product->stock_quantity }} left</span>
                    <a href="{{ route('vendor.products.edit', $product) }}" class="fc-btn-ghost text-xs">Restock</a>
                </div>
            </li>
            @endforeach
        </ul>
    </x-marketplace.card>
</div>
@endif

@endsection

@push('scripts')
@if (! empty($dailyValues) && array_sum($dailyValues) > 0)
<script>
    (function() {
        const canvas = document.getElementById('vendorSalesChart');
        if (!canvas || typeof Chart === 'undefined') return;

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: @json($dailyLabels),
                datasets: [{
                    label: 'Sales (₦)',
                    data: @json($dailyValues),
                    borderColor: '#059669',
                    backgroundColor: 'rgba(5, 150, 105, 0.10)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3,
                    pointBackgroundColor: '#059669',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => '₦' + Number(ctx.parsed.y).toLocaleString(undefined, {
                                minimumFractionDigits: 2
                            })
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => '₦' + Number(v).toLocaleString()
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    })();
</script>
@endif
@endpush
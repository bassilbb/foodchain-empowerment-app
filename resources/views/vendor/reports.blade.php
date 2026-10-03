@extends('vendor.layouts.app')

@section('title', 'Sales Analytics')
@section('header', 'Sales Analytics')
@section('subheader', 'Your performance across a chosen date range.')

@section('content')

@php
$dailyLabels = array_column($daily, 'date');
$dailyValues = array_column($daily, 'total');
$monthlyLabels = array_column($monthly, 'period');
$monthlyValues = array_column($monthly, 'total');
$hasDaily = ! empty($dailyValues) && array_sum($dailyValues) > 0;
$hasMonthly = ! empty($monthlyValues) && array_sum($monthlyValues) > 0;
@endphp

{{-- Range presets --}}
<div class="mb-5 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
    <div class="flex flex-wrap gap-2">
        @foreach ([
        'today' => 'Today', 'yesterday' => 'Yesterday', '7days' => 'Last 7 Days',
        '30days' => 'Last 30 Days', 'week' => 'This Week', 'month' => 'This Month',
        'last_month' => 'Last Month', 'year' => 'This Year',
        ] as $key => $label)
        <a href="{{ route('vendor.reports', ['preset' => $key]) }}"
            class="rounded-full px-3.5 py-1.5 text-xs font-bold transition
                          {{ $preset === $key ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('vendor.reports') }}" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        <input type="hidden" name="preset" value="custom">
        <div>
            <label for="from" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">From</label>
            <input type="date" id="from" name="from" value="{{ $from->toDateString() }}"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none">
        </div>
        <div>
            <label for="to" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">To</label>
            <input type="date" id="to" name="to" value="{{ $to->toDateString() }}"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none">
        </div>
        <div class="flex items-end">
            <button type="submit" class="fc-btn-primary w-full justify-center text-sm">Apply range</button>
        </div>
    </form>

    <p class="mt-3 border-t border-slate-100 pt-3 text-xs text-slate-500">
        Showing <strong class="text-slate-700">{{ $from->format('d M Y') }}</strong> to
        <strong class="text-slate-700">{{ $to->format('d M Y') }}</strong>.
        Only <strong>delivered</strong> items count towards sales and earnings.
    </p>
</div>

{{-- KPI cards --}}
<div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
    <x-marketplace.stat-card label="Total Sales" :value="format_currency($summary['gross_sales'])" icon="spend" tone="primary" hint="Delivered, in range" />
    <x-marketplace.stat-card label="Orders" :value="number_format($sales['orders'])" icon="orders" tone="info" :hint="$summary['items_sold'].' units sold'" />
    <x-marketplace.stat-card label="Avg Order Value" :value="format_currency($averageOrderValue)" icon="analytics" tone="neutral" />
    <x-marketplace.stat-card label="You Earned" :value="format_currency($summary['net_earnings'])" icon="delivered" tone="cta" :hint="'After '.$summary['commission_rate'].'% commission'" />
</div>

{{-- Charts --}}
<div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <x-marketplace.card title="Daily Sales" subtitle="Sales within the selected range">
            @if (! $hasDaily)
            <x-marketplace.empty-state
                icon="analytics"
                title="No delivered sales in this range"
                message="Try a wider date range, or check back once orders are delivered." />
            @else
            <div class="h-64"><canvas id="dailyChart"></canvas></div>
            @endif
        </x-marketplace.card>
    </div>

    <x-marketplace.card title="Monthly Sales" subtitle="Last 6 months">
        @if (! $hasMonthly)
        <x-marketplace.empty-state
            icon="analytics"
            title="No history yet"
            message="Monthly totals build up as you make delivered sales." />
        @else
        <div class="h-64"><canvas id="monthlyChart"></canvas></div>
        @endif
    </x-marketplace.card>
</div>

{{-- Earnings breakdown + best sellers --}}
<div class="mt-5 grid grid-cols-1 gap-5 lg:grid-cols-2">

    <x-marketplace.card title="Earnings Breakdown" subtitle="How your payout figure is calculated">
        <dl class="space-y-3 text-sm">
            <div class="flex items-center justify-between">
                <dt class="text-slate-500">Gross sales</dt>
                <dd class="font-semibold text-slate-800">{{ format_currency($summary['gross_sales']) }}</dd>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                <dt class="text-slate-500">Marketplace commission ({{ $summary['commission_rate'] }}%)</dt>
                <dd class="font-semibold text-rose-600">−{{ format_currency($summary['commission_amount']) }}</dd>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                <dt class="text-slate-500">Other deductions</dt>
                <dd class="font-semibold text-slate-600">{{ format_currency($summary['other_deductions']) }}</dd>
            </div>
            <div class="flex items-center justify-between border-t-2 border-emerald-100 pt-3">
                <dt class="font-bold text-slate-800">Net earnings</dt>
                <dd class="font-extrabold text-emerald-700">{{ format_currency($summary['net_earnings']) }}</dd>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                <dt class="text-slate-500">Already paid out</dt>
                <dd class="font-semibold text-slate-700">{{ format_currency($summary['paid']) }}</dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-slate-500">Pending payout</dt>
                <dd class="font-semibold text-amber-600">{{ format_currency($summary['pending']) }}</dd>
            </div>
            <div class="flex items-center justify-between rounded-xl bg-emerald-50 p-3">
                <dt class="font-bold text-emerald-800">Available to withdraw</dt>
                <dd class="font-extrabold text-emerald-700">{{ format_currency($summary['available']) }}</dd>
            </div>
        </dl>

        <a href="{{ route('vendor.payouts.index') }}" class="fc-btn-primary mt-4 w-full justify-center text-sm">Go to payouts</a>
    </x-marketplace.card>

    <x-marketplace.card title="Best Selling Products" subtitle="By units sold in this range" :flush="true">
        @if ($bestSellers->isEmpty())
        <x-marketplace.empty-state
            icon="products"
            title="No sales in this range"
            message="Your best sellers will appear here once orders are delivered." />
        @else
        <ul class="divide-y divide-slate-50">
            @foreach ($bestSellers as $index => $row)
            <li class="flex items-center gap-4 px-5 py-3.5">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-[11px] font-bold text-amber-700">{{ $index + 1 }}</span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $row->product?->name ?? 'Product removed' }}</p>
                    <p class="text-[11px] text-slate-500">{{ $row->units }} units</p>
                </div>
                <p class="shrink-0 text-sm font-bold text-emerald-700">{{ format_currency($row->revenue) }}</p>
            </li>
            @endforeach
        </ul>
        @endif
    </x-marketplace.card>
</div>

@endsection

@push('scripts')
<script>
    (function() {
        if (typeof Chart === 'undefined') return;

        const money = {
            ticks: {
                callback: v => '₦' + Number(v).toLocaleString()
            }
        };
        const tooltip = {
            callbacks: {
                label: ctx => '₦' + Number(ctx.parsed.y).toLocaleString(undefined, {
                    minimumFractionDigits: 2
                })
            }
        };

        @if($hasDaily)
        const dailyCanvas = document.getElementById('dailyChart');
        if (dailyCanvas) {
            new Chart(dailyCanvas, {
                type: 'line',
                data: {
                    labels: @json($dailyLabels),
                    datasets: [{
                        label: 'Sales',
                        data: @json($dailyValues),
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5,150,105,0.10)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
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
                        tooltip
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: money.ticks,
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
        }
        @endif

        @if($hasMonthly)
        const monthlyCanvas = document.getElementById('monthlyChart');
        if (monthlyCanvas) {
            new Chart(monthlyCanvas, {
                type: 'bar',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [{
                        label: 'Sales',
                        data: @json($monthlyValues),
                        backgroundColor: 'rgba(217,119,6,0.75)',
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: money.ticks,
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
        }
        @endif
    })();
</script>
@endpush
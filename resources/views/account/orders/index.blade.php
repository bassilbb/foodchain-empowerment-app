@extends('account.layouts.app')

@section('title', 'My Orders')
@section('header', 'My Orders')
@section('subheader', 'Track, review and manage everything you have ordered.')

@section('content')

{{-- Status filter chips --}}
<div class="mb-5 flex flex-wrap gap-2">
    <a href="{{ route('account.orders.index', array_filter(['q' => $search ?: null, 'from' => $from, 'to' => $to])) }}"
        class="rounded-full px-4 py-1.5 text-xs font-bold transition {{ ! $currentStatus ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/30' : 'bg-white text-slate-600 ring-1 ring-inset ring-slate-200 hover:bg-slate-50' }}">
        All ({{ $totalCount }})
    </a>

    @foreach ($groups as $key => $group)
    <a href="{{ route('account.orders.index', array_filter(['status' => $key, 'q' => $search ?: null, 'from' => $from, 'to' => $to])) }}"
        class="rounded-full px-4 py-1.5 text-xs font-bold transition {{ $currentStatus === $key ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-600/30' : 'bg-white text-slate-600 ring-1 ring-inset ring-slate-200 hover:bg-slate-50' }}">
        {{ $group['label'] }} ({{ $groupCounts[$key] ?? 0 }})
    </a>
    @endforeach
</div>

{{-- Search + date filters --}}
<form method="GET" action="{{ route('account.orders.index') }}"
    class="mb-6 grid grid-cols-1 gap-3 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-4">
    @if ($currentStatus)
    <input type="hidden" name="status" value="{{ $currentStatus }}">
    @endif

    <div class="lg:col-span-2">
        <label for="q" class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-500">Search</label>
        <input type="text" name="q" id="q" value="{{ $search }}"
            placeholder="Order number or product name…"
            class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>

    <div>
        <label for="from" class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-500">From</label>
        <input type="date" name="from" id="from" value="{{ $from }}"
            class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>

    <div>
        <label for="to" class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-500">To</label>
        <input type="date" name="to" id="to" value="{{ $to }}"
            class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
    </div>

    <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-4">
        <button type="submit" class="fc-btn-primary text-sm">Apply filters</button>
        <a href="{{ route('account.orders.index') }}" class="fc-btn-ghost text-sm">Reset</a>
    </div>
</form>

@if ($orders->isEmpty())
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm">
    <x-marketplace.empty-state
        icon="orders"
        title="{{ $totalCount === 0 ? 'No orders yet' : 'No orders match your filters' }}"
        message="{{ $totalCount === 0 ? 'Start shopping to see your orders here.' : 'Try adjusting your search or date range.' }}"
        :action-url="$totalCount === 0 ? route('marketplace.shop') : route('account.orders.index')"
        :action-label="$totalCount === 0 ? 'Start shopping' : 'Clear filters'" />
</div>
@else
<div class="space-y-3">
    @foreach ($orders as $order)
    @php
    $itemCount = $order->items->count();
    $extraItems = $order->items->skip(3)->count();
    @endphp

    <article class="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition hover:shadow-md">
        {{-- Order header --}}
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/70 px-5 py-3">
            <div class="min-w-0">
                <p class="text-sm font-extrabold text-slate-800">{{ $order->order_number }}</p>
                <p class="text-xs text-slate-500">Placed {{ $order->created_at->format('d M Y, g:i A') }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-marketplace.status-badge :status="$order->payment_status" type="payment" />
                <x-marketplace.status-badge :status="$order->status" type="order" />
            </div>
        </div>

        {{-- Body --}}
        <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center">
            <div class="min-w-0 flex-1">
                <ul class="space-y-1">
                    @foreach ($order->items->take(3) as $item)
                    <li class="flex items-baseline gap-2 text-sm text-slate-700">
                        <span class="font-bold text-emerald-700">{{ $item->quantity }}×</span>
                        <span class="truncate">{{ $item->product_name }}</span>
                    </li>
                    @endforeach
                    @if ($extraItems > 0)
                    <li class="text-xs text-slate-400">+{{ $extraItems }} more {{ \Illuminate\Support\Str::plural('item', $extraItems) }}</li>
                    @endif
                </ul>

                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                    <span>{{ $itemCount }} {{ \Illuminate\Support\Str::plural('item', $itemCount) }}</span>
                    <span>{{ \App\Models\Marketplace\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</span>
                    @if ($order->estimated_delivery_date)
                    <span>Est. delivery {{ $order->estimated_delivery_date->format('d M Y') }}</span>
                    @endif
                </div>
            </div>

            <div class="shrink-0 text-left sm:text-right">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total</p>
                <p class="text-xl font-extrabold text-slate-800">{{ format_currency($order->total) }}</p>
                <a href="{{ route('account.orders.show', $order) }}" class="fc-btn-primary mt-2 w-full justify-center text-xs sm:w-auto">View Order</a>
            </div>
        </div>
    </article>
    @endforeach
</div>

<div class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
    <x-marketplace.pagination :paginator="$orders" />
</div>
@endif

@endsection
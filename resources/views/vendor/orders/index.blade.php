@extends('vendor.layouts.app')

@section('title', 'Orders')
@section('header', 'My Orders')
@section('subheader', 'Only items allocated to your store are shown here.')

@section('content')

{{-- Filters --}}
<div class="mb-5 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('vendor.orders.index', array_filter(['q' => $search, 'from' => $from, 'to' => $to])) }}"
            class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-bold transition
                      {{ ! $currentStatus ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            All
            <span class="rounded-full {{ ! $currentStatus ? 'bg-white/25' : 'bg-white' }} px-1.5 py-0.5 text-[10px]">{{ $totalCount }}</span>
        </a>

        @foreach ($groups as $key => $group)
        <a href="{{ route('vendor.orders.index', array_filter(['status' => $key, 'q' => $search, 'from' => $from, 'to' => $to])) }}"
            class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1.5 text-xs font-bold transition
                          {{ $currentStatus === $key ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            {{ $group['label'] }}
            <span class="rounded-full {{ $currentStatus === $key ? 'bg-white/25' : 'bg-white' }} px-1.5 py-0.5 text-[10px]">{{ $groupCounts[$key] ?? 0 }}</span>
        </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('vendor.orders.index') }}" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
        @if ($currentStatus)<input type="hidden" name="status" value="{{ $currentStatus }}">@endif

        <div class="lg:col-span-2">
            <label for="q" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Search</label>
            <input type="text" id="q" name="q" value="{{ $search }}" placeholder="Order number, product or SKU..."
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </div>

        <div>
            <label for="from" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">From</label>
            <input type="date" id="from" name="from" value="{{ $from }}"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none">
        </div>

        <div>
            <label for="to" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">To</label>
            <div class="flex gap-2">
                <input type="date" id="to" name="to" value="{{ $to }}"
                    class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none">
                <button type="submit" class="fc-btn-primary shrink-0 px-4 text-xs">Filter</button>
            </div>
        </div>
    </form>
</div>

<x-marketplace.card :flush="true" title="Order Items" :subtitle="$items->total().' item(s) in your store'">
    @if ($items->isEmpty())
    <x-marketplace.empty-state
        icon="orders"
        title="{{ $search || $currentStatus || $from || $to ? 'No orders match your filters' : 'No orders yet' }}"
        message="{{ $search || $currentStatus || $from || $to
                    ? 'Try clearing your filters to see all of your order items.'
                    : 'When customers buy your products, the items appear here.' }}"
        :action-url="$search || $currentStatus || $from || $to ? route('vendor.orders.index') : null"
        :action-label="$search || $currentStatus || $from || $to ? 'Clear filters' : null" />
    @else
    <div class="divide-y divide-slate-100">
        @foreach ($items as $item)
        @php
        $order = $item->order;
        $isActionable = in_array($item->status, ['pending', 'confirmed', 'processing', 'packed', 'shipped'], true);
        @endphp

        <div class="p-5 transition hover:bg-slate-50/60">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs font-bold text-slate-700">{{ $order?->order_number ?? '—' }}</span>
                        <x-marketplace.status-badge :status="$item->status" type="order" />
                        @if ($order)
                        <x-marketplace.status-badge :status="$order->payment_status" type="payment" />
                        @endif
                    </div>

                    <p class="mt-1.5 text-sm font-semibold text-slate-800">{{ $item->product_name }}</p>
                    <p class="text-[11px] text-slate-500">
                        SKU {{ $item->product_sku ?: '—' }} ·
                        {{ $item->quantity }} × {{ format_currency($item->unit_price) }}
                        @if ($order) · Ordered {{ $order->created_at->format('d M Y') }} @endif
                    </p>
                    @if ($order)
                    <p class="mt-0.5 text-[11px] text-slate-500">
                        Buyer: {{ $order->user?->full_name ?? 'Customer' }}
                        @if ($order->delivery_state) · Deliver to {{ $order->delivery_state }} @endif
                    </p>
                    @endif
                </div>

                <div class="shrink-0 text-right">
                    <p class="text-lg font-extrabold text-slate-800">{{ format_currency($item->total_price) }}</p>
                    <p class="text-[11px] text-slate-400">Item total</p>
                </div>
            </div>

            {{-- Vendor actions --}}
            @if ($isActionable)
            <div class="mt-3 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3">
                @foreach (['confirmed' => 'Accept', 'processing' => 'Process', 'packed' => 'Mark Packed', 'shipped' => 'Mark Shipped', 'cancelled' => 'Cancel'] as $status => $label)
                <form method="POST" action="{{ route('vendor.orders.status', $item) }}"
                    @if ($status==='cancelled' ) data-confirm="Cancel this item?" @endif>
                    @csrf
                    <input type="hidden" name="status" value="{{ $status }}">
                    <button type="submit"
                        class="rounded-xl px-3 py-2 text-xs font-bold transition
                                                       {{ $status === 'cancelled'
                                                            ? 'text-rose-600 hover:bg-rose-50'
                                                            : ($item->status === $status ? 'bg-slate-100 text-slate-400' : 'fc-btn-ghost') }}">
                        {{ $label }}
                    </button>
                </form>
                @endforeach
            </div>
            @else
            <p class="mt-3 border-t border-slate-100 pt-3 text-[11px] text-slate-400">
                @if ($item->status === 'delivered')
                Delivery was confirmed by the customer. No further action is available.
                @else
                This item is {{ $item->status }} and no longer accepts status changes.
                @endif
            </p>
            @endif
        </div>
        @endforeach
    </div>

    <x-marketplace.pagination :paginator="$items" />
    @endif
</x-marketplace.card>

<p class="mt-5 rounded-2xl border border-slate-100 bg-white p-4 text-xs text-slate-500 shadow-sm">
    You cannot mark an item as <strong>Delivered</strong>. Delivery is confirmed by the buyer or by a FoodChain administrator,
    which is what releases the earnings for that item.
</p>

@endsection
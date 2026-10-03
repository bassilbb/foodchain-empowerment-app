@extends('vendor.layouts.app')

@section('title', 'Packing Slips')
@section('header', 'Packing Slips')
@section('subheader', 'Items awaiting packing or dispatch.')

@section('content')

<x-marketplace.card :flush="true" title="Items to Prepare"
    :subtitle="$items->total().' item(s) in confirmed, processing or packed state'">

    @if ($items->isEmpty())
    <x-marketplace.empty-state
        icon="products"
        title="Nothing to pack right now"
        message="Items appear here once you accept an order and start processing it." />
    @else
    <div class="divide-y divide-slate-100">
        @foreach ($items as $item)
        <div class="p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-mono text-xs font-bold text-slate-700">{{ $item->order?->order_number ?? '—' }}</span>
                        <x-marketplace.status-badge :status="$item->status" type="order" />
                    </div>

                    <p class="mt-1.5 text-sm font-bold text-slate-800">{{ $item->product_name }}</p>
                    <p class="text-[11px] text-slate-500">
                        SKU {{ $item->product_sku ?: '—' }} · Qty {{ $item->quantity }}
                    </p>
                </div>

                <div class="shrink-0 rounded-xl bg-emerald-50 px-4 py-2 text-center ring-1 ring-inset ring-emerald-100">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Pack</p>
                    <p class="text-xl font-extrabold text-emerald-700">{{ $item->quantity }}</p>
                </div>
            </div>

            @if ($item->order)
            <div class="mt-3 grid grid-cols-1 gap-3 rounded-xl bg-slate-50 p-3 text-xs sm:grid-cols-2">
                <div>
                    <p class="font-bold uppercase tracking-wider text-slate-400">Deliver To</p>
                    <p class="mt-0.5 font-semibold text-slate-700">{{ $item->order->user?->full_name ?? 'Customer' }}</p>
                    @if ($item->order->user?->phone)
                    <p class="text-slate-500">{{ $item->order->user->phone }}</p>
                    @endif
                </div>
                <div>
                    <p class="font-bold uppercase tracking-wider text-slate-400">Address</p>
                    @if ($item->order->delivery_address)
                    <p class="mt-0.5 text-slate-600">{{ $item->order->delivery_address }}</p>
                    @endif
                    <p class="text-slate-500">
                        {{ collect([$item->order->delivery_city, $item->order->delivery_state])->filter()->implode(', ') ?: 'See order' }}
                    </p>
                </div>
            </div>
            @endif

            {{-- Progress this item to packed / shipped --}}
            <div class="mt-3 flex flex-wrap gap-2 border-t border-slate-100 pt-3">
                @foreach (['packed' => 'Mark Packed', 'shipped' => 'Mark Shipped'] as $status => $label)
                @if ($item->status !== $status)
                <form method="POST" action="{{ route('vendor.orders.status', $item) }}">
                    @csrf
                    <input type="hidden" name="status" value="{{ $status }}">
                    <button type="submit" class="fc-btn-ghost text-xs">{{ $label }}</button>
                </form>
                @endif
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    <x-marketplace.pagination :paginator="$items" />
    @endif
</x-marketplace.card>

@endsection
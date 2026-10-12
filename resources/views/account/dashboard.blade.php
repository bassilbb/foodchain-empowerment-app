@extends('account.layouts.app')

@section('title', 'My Dashboard')
@section('header', 'Welcome back, ' . \Illuminate\Support\Str::before(auth()->user()->full_name ?? auth()->user()->name, ' '))
@section('subheader', 'Here is what is happening with your FoodChain orders.')

@section('content')

{{-- Vendor application banner --}}
@php
    $vendorRecord = $vendor ?? auth()->user()->marketplaceVendor;
@endphp
@if ($vendorRecord && $vendorRecord->status !== 'approved')
<div class="mb-5 flex flex-wrap items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4">
    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
        </svg>
    </span>
    <div class="min-w-0 flex-1">
        <p class="text-sm font-bold text-amber-900">Your vendor store is {{ $vendorRecord->status }}</p>
        <p class="text-xs text-amber-700">
            @if ($vendorRecord->status === 'pending')
            We are reviewing your vendor application. Products you list will go live once approved.
            @else
            Your store is not currently active. Contact support for assistance.
            @endif
        </p>
    </div>
    @if ($vendorRecord->status === 'pending')
    <a href="{{ route('vendor.dashboard') }}" class="rounded-xl bg-amber-500 px-4 py-2 text-xs font-bold text-white hover:bg-amber-600">Go to vendor area</a>
    @endif
</div>
@elseif (! $vendorRecord)
<div class="mb-5 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
    <div class="flex flex-wrap items-center gap-3">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-emerald-900">Sell on FoodChain</p>
            <p class="text-xs text-emerald-700">Open your own store, list products and earn from every sale.</p>
        </div>
        <a href="{{ route('account.become-vendor.create') }}" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">Apply to become a vendor</a>
    </div>
</div>
@else
<div class="mb-5 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
    <div class="flex flex-wrap items-center gap-3">
        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
        </span>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-bold text-emerald-900">You are a FoodChain vendor</p>
            <p class="text-xs text-emerald-700">Manage your store, products, orders and earnings.</p>
        </div>
        <a href="{{ route('vendor.dashboard') }}" class="rounded-xl bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">Open Vendor Dashboard</a>
    </div>
</div>
@endif

{{-- Summary cards --}}
<div class="grid animate-slide-up grid-cols-2 gap-4 lg:grid-cols-4">
    <x-marketplace.stat-card label="Total Orders" :value="number_format($stats['total'])" icon="orders" tone="primary"
        :href="\Illuminate\Support\Facades\Route::has('account.orders.index') ? route('account.orders.index') : null" />
    <x-marketplace.stat-card label="Pending Orders" :value="number_format($stats['pending'])" icon="pending" tone="cta"
        :href="\Illuminate\Support\Facades\Route::has('account.orders.index') ? route('account.orders.index', ['status' => 'pending']) : null" />
    <x-marketplace.stat-card label="Delivered" :value="number_format($stats['delivered'])" icon="delivered" tone="info"
        :href="\Illuminate\Support\Facades\Route::has('account.orders.index') ? route('account.orders.index', ['status' => 'delivered']) : null" />
    <x-marketplace.stat-card label="Wishlist" :value="number_format($stats['wishlist'])" icon="wishlist" tone="danger"
        :href="\Illuminate\Support\Facades\Route::has('account.wishlist.index') ? route('account.wishlist.index') : null" />
</div>

{{-- Secondary metrics --}}
<div class="mt-4 grid animate-slide-up grid-cols-1 gap-4 sm:grid-cols-3" style="animation-delay: 90ms">
    <x-marketplace.stat-card label="Wallet Balance" :value="format_currency($stats['wallet_balance'])" icon="wallet" tone="primary"
        hint="Available to spend on orders" />
    <x-marketplace.stat-card label="Total Spent" :value="format_currency($stats['lifetime_spend'])" icon="spend" tone="info"
        hint="Excludes cancelled & refunded orders" />
    <x-marketplace.stat-card label="Unread Notifications" :value="number_format($stats['unread_notifications'])" icon="bell" tone="cta"
        :href="\Illuminate\Support\Facades\Route::has('account.notifications.index') ? route('account.notifications.index') : null" />
</div>

{{-- Recent orders --}}
<div class="mt-6 animate-slide-up" style="animation-delay: 140ms">
    <x-marketplace.card title="Recent Orders" subtitle="Your five most recent marketplace orders" :flush="true">
        <x-slot:actions>
            @if (\Illuminate\Support\Facades\Route::has('account.orders.index'))
            <a href="{{ route('account.orders.index') }}" class="fc-btn-ghost text-xs">View all orders</a>
            @endif
        </x-slot:actions>

        @if ($recentOrders->isEmpty())
        <x-marketplace.empty-state
            icon="orders"
            title="No orders yet"
            message="Start shopping to see your orders here."
            :action-url="route('marketplace.shop')"
            action-label="Start shopping" />
        @else
        {{-- Desktop table --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/70 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-3">Order</th>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Items</th>
                        <th class="px-5 py-3">Amount</th>
                        <th class="px-5 py-3">Payment</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach ($recentOrders as $order)
                    <tr class="transition hover:bg-slate-50/70">
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-800">{{ $order->order_number }}</span>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $order->created_at->format('d M, Y') }}</td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $order->items->count() }} {{ \Illuminate\Support\Str::plural('item', $order->items->count()) }}
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-800">{{ format_currency($order->total) }}</td>
                        <td class="px-5 py-4"><x-marketplace.status-badge :status="$order->payment_status" type="payment" /></td>
                        <td class="px-5 py-4"><x-marketplace.status-badge :status="$order->status" type="order" /></td>
                        <td class="px-5 py-4 text-right">
                            @if (\Illuminate\Support\Facades\Route::has('account.orders.show'))
                                <a href="{{ route('account.orders.show', $order) }}" class="fc-btn-ghost text-xs">View Order</a>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="divide-y divide-slate-100 md:hidden">
            @foreach ($recentOrders as $order)
            <div class="p-4">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-slate-800">{{ $order->order_number }}</p>
                        <p class="mt-0.5 text-xs text-slate-500">{{ $order->created_at->format('d M, Y') }} · {{ $order->items->count() }} {{ \Illuminate\Support\Str::plural('item', $order->items->count()) }}</p>
                    </div>
                    <p class="shrink-0 text-sm font-extrabold text-slate-800">{{ format_currency($order->total) }}</p>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <x-marketplace.status-badge :status="$order->payment_status" type="payment" />
                    <x-marketplace.status-badge :status="$order->status" type="order" />
                </div>

                @if (\Illuminate\Support\Facades\Route::has('account.orders.show'))
                    <a href="{{ route('account.orders.show', $order) }}" class="fc-btn-ghost mt-3 w-full justify-center text-xs">View Order</a>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </x-marketplace.card>
</div>

{{-- Quick actions --}}
@php
$quickActions = collect([
['label' => 'Continue Shopping', 'route' => 'marketplace.shop', 'icon' => 'store'],
['label' => 'My Wishlist', 'route' => 'account.wishlist.index', 'icon' => 'wishlist'],
['label' => 'Delivery Addresses', 'route' => 'account.addresses.index', 'icon' => 'map'],
['label' => 'My Profile', 'route' => 'account.profile.show', 'icon' => 'user'],
])->filter(fn ($a) => \Illuminate\Support\Facades\Route::has($a['route']));
@endphp

@if ($quickActions->isNotEmpty())
<div class="mt-6 grid animate-slide-up grid-cols-2 gap-3 sm:grid-cols-4" style="animation-delay: 200ms">
    @foreach ($quickActions as $action)
    <a href="{{ route($action['route']) }}"
        class="flex flex-col items-center gap-2 rounded-2xl border border-slate-100 bg-white p-4 text-center shadow-sm transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-900/5">
        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ [
                                'store' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                                'wishlist' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                                'map' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z',
                                'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                            ][$action['icon']] }}" />
            </svg>
        </span>
        <span class="text-xs font-bold text-slate-700">{{ $action['label'] }}</span>
    </a>
    @endforeach
</div>
@endif

@endsection
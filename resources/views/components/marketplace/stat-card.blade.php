{{--
    Dashboard KPI / summary card used by all three dashboards.

    Usage:
        <x-marketplace.stat-card label="Total Orders" :value="$stats['orders']" icon="orders" tone="primary" />
        <x-marketplace.stat-card label="Wallet" value="₦12,500" icon="wallet" tone="cta" hint="Available balance" />

    tone: primary | cta | info | danger | neutral
--}}
@props([
'label' => '',
'value' => '0',
'icon' => null,
'tone' => 'primary',
'hint' => null,
'href' => null,
])

@php
$tones = [
'primary' => ['bar' => 'from-emerald-500 to-emerald-400', 'chip' => 'bg-emerald-50 text-emerald-600 ring-emerald-100'],
'cta' => ['bar' => 'from-amber-500 to-orange-400', 'chip' => 'bg-amber-50 text-amber-600 ring-amber-100'],
'info' => ['bar' => 'from-teal-500 to-cyan-400', 'chip' => 'bg-teal-50 text-teal-600 ring-teal-100'],
'danger' => ['bar' => 'from-rose-500 to-red-400', 'chip' => 'bg-rose-50 text-rose-600 ring-rose-100'],
'neutral' => ['bar' => 'from-slate-400 to-slate-300', 'chip' => 'bg-slate-50 text-slate-600 ring-slate-100'],
];
$t = $tones[$tone] ?? $tones['primary'];

$icons = [
'orders' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
'pending' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
'delivered' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
'cancelled' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
'wishlist' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
'bell' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
'wallet' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
'spend' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
'products' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
'users' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
'store' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
];
$path = $icons[$icon] ?? $icons['orders'];

$tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    class="group relative block overflow-hidden rounded-2xl border border-slate-200/70 bg-white p-5 shadow-3d-card transition duration-300 hover:-translate-y-1 hover:border-emerald-300/70 hover:shadow-3d-hover">
    <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $t['bar'] }}"></span>
    <span class="pointer-events-none absolute -right-8 -top-8 h-24 w-24 rounded-full bg-gradient-to-br {{ $t['bar'] }} opacity-10 blur-2xl transition group-hover:opacity-20"></span>

    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $label }}</p>
            <p class="mt-1.5 truncate text-2xl font-extrabold text-slate-800">{{ $value }}</p>
            @if ($hint)
            <p class="mt-1 truncate text-xs text-slate-500">{{ $hint }}</p>
            @endif
        </div>

        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl ring-1 ring-inset {{ $t['chip'] }}">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}" />
            </svg>
        </span>
    </div>
</{{ $tag }}>
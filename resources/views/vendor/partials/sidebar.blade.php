{{--
    Vendor dashboard sidebar navigation (Admin class structure, exact Admin CSS via layout).
    Only renders items whose route exists. Preserves all routes, params, permissions guards, badges, and labels.
--}}
@php
$vendor = auth()->user()->marketplaceVendor;

// Injectable from any vendor view that extends the layout via a `sidebarBadges` section.
$lowStockCount = $lowStockCount ?? null;
$pendingPayoutCount = $pendingPayoutCount ?? null;

$menu = [
    [
        'heading' => null,
        'items' => [
            ['label' => 'Dashboard', 'route' => 'vendor.dashboard', 'icon' => 'home', 'active' => ['vendor.dashboard']],
        ],
    ],
    [
        'heading' => 'Products',
        'items' => [
            ['label' => 'All Products', 'route' => 'vendor.products.index', 'icon' => 'box', 'active' => ['vendor.products.index', 'vendor.products.edit']],
            ['label' => 'Add Product', 'route' => 'vendor.products.create', 'icon' => 'plus', 'active' => ['vendor.products.create']],
            ['label' => 'Low Stock', 'route' => 'vendor.products.index', 'params' => ['stock' => 'low'], 'icon' => 'alert', 'active' => [], 'badge' => $lowStockCount ?? null],
        ],
    ],
    [
        'heading' => 'Orders',
        'items' => [
            ['label' => 'All Orders', 'route' => 'vendor.orders.index', 'icon' => 'orders', 'active' => ['vendor.orders.index']],
            ['label' => 'New Orders', 'route' => 'vendor.orders.index', 'params' => ['status' => 'new'], 'icon' => 'sparkle', 'active' => []],
            ['label' => 'Processing', 'route' => 'vendor.orders.index', 'params' => ['status' => 'processing'], 'icon' => 'cog', 'active' => []],
            ['label' => 'Shipped', 'route' => 'vendor.orders.index', 'params' => ['status' => 'shipped'], 'icon' => 'truck', 'active' => []],
            ['label' => 'Delivered', 'route' => 'vendor.orders.index', 'params' => ['status' => 'delivered'], 'icon' => 'check', 'active' => []],
            ['label' => 'Cancelled', 'route' => 'vendor.orders.index', 'params' => ['status' => 'cancelled'], 'icon' => 'x', 'active' => []],
        ],
    ],
    [
        'heading' => 'Inventory',
        'items' => [
            ['label' => 'Stock Levels', 'route' => 'vendor.inventory', 'icon' => 'layers', 'active' => ['vendor.inventory']],
            ['label' => 'Packing Slips', 'route' => 'vendor.orders.packing-slips', 'icon' => 'doc', 'active' => ['vendor.orders.packing-slips']],
        ],
    ],
    [
        'heading' => 'Sales',
        'items' => [
            ['label' => 'Analytics', 'route' => 'vendor.reports', 'icon' => 'chart', 'active' => ['vendor.reports']],
        ],
    ],
    [
        'heading' => 'Payments',
        'items' => [
            ['label' => 'Earnings', 'route' => 'vendor.payouts.index', 'icon' => 'wallet', 'active' => ['vendor.payouts.*'], 'badge' => ($pendingPayoutCount ?? 0) ?: null],
        ],
    ],
    [
        'heading' => 'Store',
        'items' => [
            ['label' => 'Storefront', 'route' => 'marketplace.vendor', 'params' => $vendor?->slug ? ['vendor' => $vendor->slug] : [], 'icon' => 'store', 'active' => [], 'external' => true],
            ['label' => 'Store Settings', 'route' => 'vendor.settings.index', 'icon' => 'cog', 'active' => ['vendor.settings.*']],
        ],
    ],
];

$icons = [
    'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
    'box' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
    'plus' => 'M12 4v16m8-8H4',
    'alert' => 'M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z',
    'orders' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
    'sparkle' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z',
    'cog' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
    'truck' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1',
    'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
    'x' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
    'layers' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
    'doc' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
    'chart' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
    'wallet' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
    'store' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
];
@endphp

<nav class="sidebar-nav">
    @foreach ($menu as $group)
        @php
            $items = collect($group['items'])->filter(fn ($i) => \Illuminate\Support\Facades\Route::has($i['route']));
            $items = $items->reject(fn ($i) => ! empty($i['params']) && collect($i['params'])->contains(null));
        @endphp

        @continue($items->isEmpty())

        <div class="nav-section">
            @if ($group['heading'])
                <div class="nav-section-title">{{ $group['heading'] }}</div>
            @endif

            @foreach ($items as $item)
                @php
                    $isActive = ! empty($item['active']) && request()->routeIs($item['active']);
                    $href = route($item['route'], $item['params'] ?? []);
                    $badge = $item['badge'] ?? null;
                    $target = ! empty($item['external']) ? ' target="_blank" rel="noopener"' : '';
                @endphp

                <a href="{{ $href }}" {!! $target !!} aria-label="{{ $item['label'] }}"
                   class="nav-link {{ $isActive ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icons[$item['icon']] ?? $icons['home'] }}" />
                    </svg>
                    <span class="nav-label">{{ $item['label'] }}</span>
                    @if ($badge)
                        <span class="nav-badge">{{ $badge > 99 ? '99+' : $badge }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endforeach

    {{-- Vendor status notice --}}
    @if ($vendor && $vendor->status !== 'approved')
        <div class="sidebar-notice">
            <div class="sidebar-notice-title">Account {{ ucfirst($vendor->status) }}</div>
            <div class="sidebar-notice-text">
                @if ($vendor->status === 'pending')
                    Your store is awaiting approval. You can prepare products in the meantime.
                @else
                    Your store is not currently active. Contact support for assistance.
                @endif
            </div>
        </div>
    @endif
</nav>

<div class="sidebar-footer">
    <a href="{{ route('dashboard') }}" class="footer-link">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
        </svg>
        Member Dashboard
    </a>

    <a href="{{ route('marketplace.home') }}" class="footer-link">
        <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to store
    </a>

    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
        @csrf
        <button type="submit" class="logout-btn">
            <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
        </button>
    </form>
</div>
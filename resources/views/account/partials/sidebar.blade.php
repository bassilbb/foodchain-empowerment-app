{{--
    Customer account sidebar navigation.

    Only renders menu items whose route actually exists, so the nav can never
    advertise functionality the application does not have.
--}}
@php
    $user = auth()->user();

    // Grouped menu definition. Each item may declare an `exists` route guard.
    $menu = [
        [
            'heading' => null,
            'items' => [
                ['label' => 'Dashboard', 'route' => 'account.dashboard', 'icon' => 'home', 'active' => ['account.dashboard']],
            ],
        ],
        [
            'heading' => 'My Orders',
            'items' => [
                ['label' => 'All Orders', 'route' => 'account.orders.index', 'icon' => 'orders', 'active' => ['account.orders.index', 'account.orders.show']],
                ['label' => 'Pending', 'route' => 'account.orders.index', 'params' => ['status' => 'pending'], 'icon' => 'clock', 'active' => []],
                ['label' => 'Processing', 'route' => 'account.orders.index', 'params' => ['status' => 'processing'], 'icon' => 'cog', 'active' => []],
                ['label' => 'Shipped', 'route' => 'account.orders.index', 'params' => ['status' => 'shipped'], 'icon' => 'truck', 'active' => []],
                ['label' => 'Delivered', 'route' => 'account.orders.index', 'params' => ['status' => 'delivered'], 'icon' => 'check', 'active' => []],
                ['label' => 'Cancelled', 'route' => 'account.orders.index', 'params' => ['status' => 'cancelled'], 'icon' => 'x', 'active' => []],
            ],
        ],
        [
            'heading' => 'My Account',
            'items' => [
                ['label' => 'Profile', 'route' => 'account.profile.show', 'icon' => 'user', 'active' => ['account.profile.*']],
            ],
        ],
        [
            'heading' => 'Addresses',
            'items' => [
                ['label' => 'Delivery Addresses', 'route' => 'account.addresses.index', 'icon' => 'map', 'active' => ['account.addresses.index', 'account.addresses.edit']],
                ['label' => 'Add New Address', 'route' => 'account.addresses.create', 'icon' => 'plus', 'active' => ['account.addresses.create']],
            ],
        ],
        [
            'heading' => 'Shopping',
            'items' => [
                ['label' => 'Wishlist', 'route' => 'account.wishlist.index', 'icon' => 'heart', 'active' => ['account.wishlist.*'], 'badge' => $wishlistCount ?? null],
                ['label' => 'Notifications', 'route' => 'account.notifications.index', 'icon' => 'bell', 'active' => ['account.notifications.*'], 'badge' => $unreadNotifications ?? null],
            ],
        ],
        [
            'heading' => 'Payments',
            'items' => [
                ['label' => 'Payment History', 'route' => 'account.payments.index', 'icon' => 'card', 'active' => ['account.payments.*']],
            ],
        ],
        [
            'heading' => 'Wallet',
            'items' => [
                ['label' => 'Wallet Balance', 'route' => 'account.wallet.index', 'icon' => 'wallet', 'active' => ['account.wallet.index']],
                ['label' => 'Wallet Transactions', 'route' => 'account.wallet.transactions', 'icon' => 'list', 'active' => ['account.wallet.transactions']],
            ],
        ],
        [
            'heading' => 'Support',
            'items' => [
                ['label' => 'Help Center', 'route' => 'help', 'icon' => 'help', 'active' => []],
                ['label' => 'Contact Support', 'route' => 'support.index', 'icon' => 'chat', 'active' => ['support.*']],
            ],
        ],
    ];

    $iconPaths = [
        'home' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
        'orders' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
        'clock' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
        'cog' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z',
        'truck' => 'M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1',
        'check' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
        'x' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
        'user' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        'map' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z',
        'plus' => 'M12 4v16m8-8H4',
        'heart' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
        'bell' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
        'card' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'wallet' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z',
        'list' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
        'help' => 'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'chat' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
    ];
@endphp

<nav class="flex-1 space-y-6 overflow-y-auto px-3 py-4">
    @foreach ($menu as $group)
        @php
            // Drop items whose route is not registered in this application.
            $items = collect($group['items'])->filter(fn ($i) => \Illuminate\Support\Facades\Route::has($i['route']));
        @endphp

        @continue($items->isEmpty())

        <div>
            @if ($group['heading'])
                <p class="px-3 pb-2 text-[12px] font-extrabold uppercase tracking-[0.14em] text-emerald-200/80">{{ $group['heading'] }}</p>
            @endif

            <ul class="space-y-0.5">
                @foreach ($items as $item)
                    @php
                        $isActive = ! empty($item['active']) && request()->routeIs($item['active']);
                        $href = route($item['route'], $item['params'] ?? []);
                        $badge = $item['badge'] ?? null;
                    @endphp

                    <li>
                        <a href="{{ $href }}"
                           aria-label="{{ $item['label'] }}"
                           class="group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium transition
                                  {{ $isActive
                                        ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-lg shadow-emerald-900/30'
                                        : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                            <svg class="h-[18px] w-[18px] shrink-0 {{ $isActive ? 'text-white' : 'text-emerald-200/70 group-hover:text-white' }}"
                                 fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $iconPaths[$item['icon']] ?? $iconPaths['home'] }}" />
                            </svg>

                            <span class="truncate">{{ $item['label'] }}</span>

                            @if ($badge)
                                <span class="ml-auto rounded-full bg-orange-500 px-2 py-0.5 text-[12px] font-bold text-white">{{ $badge > 99 ? '99+' : $badge }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach

    {{-- Sell on FoodChain entry point --}}
    @if (\Illuminate\Support\Facades\Route::has('account.become-vendor.create'))
        <div class="rounded-xl bg-white/5 p-3 ring-1 ring-inset ring-white/10">
            <p class="text-xs font-bold text-white">Become a vendor</p>
            <p class="mt-0.5 text-[11px] leading-relaxed text-white/70">List your products and reach thousands of buyers.</p>

            @php($vendorEntry = $user->marketplaceVendor)
            @if ($vendorEntry && $vendorEntry->isApproved())
                {{-- Vendor dashboard route is always registered; keep the guard for safety. --}}
                @if (\Illuminate\Support\Facades\Route::has('vendor.dashboard'))
                    <a href="{{ route('vendor.dashboard') }}" class="mt-2.5 inline-flex w-full items-center justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 px-3 py-2 text-xs font-bold text-white">
                        Open Vendor Dashboard
                    </a>
                @endif
            @elseif ($vendorEntry)
                <a href="{{ route('vendor.dashboard') ?? '#' }}" class="mt-2.5 inline-flex w-full items-center justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 px-3 py-2 text-xs font-bold text-white">
                    Open Vendor Dashboard
                </a>
            @elseif ($vendorEntry)
                <span class="mt-2.5 inline-flex w-full items-center justify-center rounded-lg bg-amber-500/20 px-3 py-2 text-xs font-bold text-amber-200 ring-1 ring-inset ring-amber-400/30">
                    Application {{ ucfirst($vendorEntry->status) }}
                </span>
            @else
                <a href="{{ route('account.become-vendor.create') }}" class="mt-2.5 inline-flex w-full items-center justify-center rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 px-3 py-2 text-xs font-bold text-white">
                    Sell on FoodChain
                </a>
            @endif
        </div>
    @endif
</nav>

<div class="border-t border-white/10 p-3">
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-xl bg-rose-500/10 px-3 py-2.5 text-xs font-bold text-rose-300 ring-1 ring-inset ring-rose-400/20 transition hover:bg-rose-500/20 hover:text-rose-200">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            Logout
        </button>
    </form>
</div>
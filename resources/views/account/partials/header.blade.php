{{--
    Shared dashboard top header: mobile menu toggle, page title, search,
    notifications dropdown and user profile menu.
--}}
@php
$user = auth()->user();
$initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->first_name ?? $user->name ?? 'U', 0, 1));

$unread = $unreadNotifications ?? \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->count();
$latestNotifications = \App\Models\Notification::where('user_id', $user->id)->latest()->limit(6)->get();

$roleLabel = $user->marketplaceVendor ? 'Vendor' : 'Customer';
@endphp

<header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
    <div class="flex items-center gap-3 px-4 py-3 sm:px-6">

        {{-- Drawer toggle --}}
        <button type="button" id="accountSidebarToggle"
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-600"
            aria-label="Open navigation">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        {{-- Title --}}
        <div class="min-w-0 flex-1">
            <h1 class="truncate text-base font-extrabold text-slate-800 sm:text-lg">@yield('header', 'Dashboard')</h1>
            <p class="hidden truncate text-xs text-slate-500 sm:block">@yield('subheader', 'Welcome back!')</p>
        </div>

        {{-- Search (optional per page) --}}
        @hasSection('header-search')
        @yield('header-search')
        @endif

        {{-- Notifications --}}
        @if (\Illuminate\Support\Facades\Route::has('account.notifications.index'))
        <div class="relative" data-dropdown>
            <button type="button" data-dropdown-trigger aria-label="Notifications"
                class="relative flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 text-slate-600 transition hover:border-emerald-300 hover:text-emerald-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                @if ($unread > 0)
                <span class="absolute -right-1 -top-1 flex h-[18px] min-w-[18px] items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ $unread > 99 ? '99+' : $unread }}</span>
                @endif
            </button>

            <div data-dropdown-panel
                class="absolute right-0 mt-2 hidden w-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <p class="text-sm font-bold text-slate-800">Notifications</p>
                    @if ($unread > 0)
                    <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-bold text-rose-600">{{ $unread }} new</span>
                    @endif
                </div>

                <div class="max-h-80 divide-y divide-slate-50 overflow-y-auto">
                    @forelse ($latestNotifications as $n)
                    <a href="{{ route('account.notifications.index') }}" class="block px-4 py-3 transition hover:bg-slate-50 {{ $n->is_read ? '' : 'bg-emerald-50/40' }}">
                        <p class="text-sm font-semibold text-slate-800">{{ $n->title }}</p>
                        <p class="mt-0.5 line-clamp-2 text-xs text-slate-500">{{ $n->message }}</p>
                        <p class="mt-1 text-[10px] text-slate-400">{{ $n->created_at->diffForHumans() }}</p>
                    </a>
                    @empty
                    <p class="px-4 py-8 text-center text-sm text-slate-400">You're all caught up.</p>
                    @endforelse
                </div>

                <a href="{{ route('account.notifications.index') }}" class="block border-t border-slate-100 px-4 py-3 text-center text-xs font-bold text-emerald-600 hover:bg-slate-50">
                    View all notifications
                </a>
            </div>
        </div>
        @endif

        {{-- User menu --}}
        <div class="relative" data-dropdown>
            <button type="button" data-dropdown-trigger
                class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 py-1.5 pl-1.5 pr-3 transition hover:border-emerald-300">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 text-sm font-bold text-white">{{ $initials }}</span>
                <span class="hidden text-left leading-tight sm:block">
                    <span class="block max-w-[130px] truncate text-xs font-bold text-slate-800">{{ $user->full_name ?? $user->name }}</span>
                    <span class="block text-[10px] text-slate-500">{{ $roleLabel }}</span>
                </span>
                <svg class="hidden h-4 w-4 text-slate-400 sm:block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div data-dropdown-panel
                class="absolute right-0 mt-2 hidden w-64 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-900/10">
                <div class="border-b border-slate-100 px-4 py-3">
                    <p class="truncate text-sm font-bold text-slate-800">{{ $user->full_name ?? $user->name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $user->email }}</p>
                </div>

                <div class="py-1">
                    @if (\Illuminate\Support\Facades\Route::has('account.profile.show'))
                    <a href="{{ route('account.profile.show') }}" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50">My Profile</a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('account.orders.index'))
                    <a href="{{ route('account.orders.index') }}" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50">My Orders</a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('account.wishlist.index'))
                    <a href="{{ route('account.wishlist.index') }}" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50">Wishlist</a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('account.addresses.index'))
                    <a href="{{ route('account.addresses.index') }}" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50">Addresses</a>
                    @endif
                    @if (\Illuminate\Support\Facades\Route::has('marketplace.shop'))
                    <a href="{{ route('marketplace.shop') }}" class="block px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50">Continue Shopping</a>
                    @endif
                </div>

                <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                    @csrf
                    <button type="submit" class="block w-full px-4 py-2.5 text-left text-sm font-semibold text-rose-600 hover:bg-rose-50">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>
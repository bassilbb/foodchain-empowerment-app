<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $title ?? 'Marketplace') - {{ app_name() }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.vite-css')
    @stack('styles')
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen flex flex-col">

    {{-- Flash messages --}}
    @if (session('success') || session('error') || session('info'))
        <div class="fixed top-4 right-4 z-[999] w-80 space-y-2" id="flash-container">
            @if (session('success'))
                <div class="flash-item bg-white border-l-4 border-emerald-500 shadow-lg rounded-lg p-4 flex items-start gap-3">
                    <span class="text-emerald-500 text-lg leading-none mt-0.5">✓</span>
                    <p class="text-sm text-gray-800 flex-1">{{ session('success') }}</p>
                    <button class="close-flash text-gray-400 hover:text-gray-600">✕</button>
                </div>
            @endif
            @if (session('error'))
                <div class="flash-item bg-white border-l-4 border-red-500 shadow-lg rounded-lg p-4 flex items-start gap-3">
                    <span class="text-red-500 text-lg leading-none mt-0.5">✕</span>
                    <p class="text-sm text-gray-800 flex-1">{{ session('error') }}</p>
                    <button class="close-flash text-gray-400 hover:text-gray-600">✕</button>
                </div>
            @endif
            @if (session('info'))
                <div class="flash-item bg-white border-l-4 border-blue-500 shadow-lg rounded-lg p-4 flex items-start gap-3">
                    <span class="text-blue-500 text-lg leading-none mt-0.5">ℹ</span>
                    <p class="text-sm text-gray-800 flex-1">{{ session('info') }}</p>
                    <button class="close-flash text-gray-400 hover:text-gray-600">✕</button>
                </div>
            @endif
        </div>
    @endif

    {{-- Top utility bar --}}
    @php $mpStates = mp_pricing()->states(); @endphp
    <div class="bg-emerald-900 text-emerald-100 text-xs">
        <div class="max-w-[1440px] mx-auto px-4 py-2 flex items-center justify-between gap-3">
            <div class="hidden md:flex items-center gap-5">
                <p class="text-emerald-200/90">Free delivery on orders above ₦50,000 · Secure payments</p>
            </div>

            <div class="flex items-center gap-3 text-[11px]">
                <label for="state-selector" class="hidden sm:flex items-center gap-1.5 text-emerald-300">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                    Deliver to
                </label>
                <select id="state-selector" onchange="if(this.value){window.location=this.value;}"
                        class="bg-emerald-900 border border-emerald-700 rounded px-1.5 py-1 text-emerald-100 text-[11px] font-medium focus:outline-none focus:ring-1 focus:ring-emerald-400 cursor-pointer">
                    <option value="{{ route('marketplace.shop') }}" disabled>Select state...</option>
                    @foreach ($mpStates as $st)
                        <option value="{{ route('marketplace.set-state', $st) }}" @selected(mp_state() === $st)>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-4 ml-auto">
                @auth
                    <a href="{{ route('marketplace.orders.index') }}" class="hover:text-white hidden sm:inline">My Orders</a>
                    <span class="text-emerald-700 hidden sm:inline">|</span>
                    <a href="{{ route('dashboard') }}" class="hover:text-white font-medium hidden sm:inline">Dashboard</a>
                    <span class="text-emerald-700 hidden sm:inline">|</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="hover:text-white">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-white">Sign in</a>
                    <span class="text-emerald-700">|</span>
                    <a href="{{ route('register') }}" class="hover:text-white font-medium">Join FoodChain</a>
                @endauth
            </div>
        </div>
    </div>

    {{-- Main store header --}}
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-[1440px] mx-auto px-4 py-3 flex items-center gap-4">

            {{-- Logo --}}
            <a href="{{ route('marketplace.home') }}" class="flex items-center gap-2 shrink-0">
                <span class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-extrabold text-lg shadow">FC</span>
                <span class="hidden lg:block leading-tight">
                    <span class="block text-emerald-800 font-extrabold text-lg tracking-tight">FoodChain</span>
                    <span class="block text-[11px] text-emerald-500 font-semibold uppercase tracking-widest">Marketplace</span>
                </span>
            </a>

            {{-- Search --}}
            <div class="flex-1">
                <form action="{{ route('marketplace.search') }}" method="GET" class="relative max-w-3xl mx-auto" id="search-form">
                    <input
                        type="text"
                        name="q"
                        id="search-input"
                        placeholder="Search rice, beans, tomato paste, seasonings..."
                        value="{{ $searchTerm ?? ($term ?? '') }}"
                        autocomplete="off"
                        class="w-full rounded-lg py-2.5 pl-4 pr-28 text-sm text-gray-800 bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                    >
                    <button type="submit" class="absolute right-1 top-1 bottom-1 px-5 bg-orange-500 hover:bg-orange-400 text-white rounded-md text-sm font-bold flex items-center gap-1.5 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Search
                    </button>
                    <div id="search-suggestions" class="hidden absolute top-full mt-2 left-0 right-0 bg-white rounded-xl shadow-2xl overflow-hidden z-50"></div>
                </form>
            </div>

            {{-- Account + cart cluster --}}
            <div class="flex items-center gap-1.5 shrink-0">
                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:flex flex-col items-center px-3 py-1.5 rounded-lg hover:bg-gray-50 group" title="My Account">
                        <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-[10px] text-gray-500 group-hover:text-emerald-600 font-medium">Hi, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') ?: 'there' }}</span>
                    </a>
                    <a href="{{ route('marketplace.orders.index') }}" class="hidden xl:flex flex-col items-center px-3 py-1.5 rounded-lg hover:bg-gray-50 group" title="Orders">
                        <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        <span class="text-[10px] text-gray-500 group-hover:text-emerald-600 font-medium">Orders</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="hidden sm:flex flex-col items-center px-3 py-1.5 rounded-lg hover:bg-gray-50 group">
                        <svg class="w-6 h-6 text-gray-600 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span class="text-[10px] text-gray-500 group-hover:text-emerald-600 font-medium">Sign in / Join</span>
                    </a>
                @endauth

                <a href="{{ route('marketplace.cart.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-emerald-50 group" title="Cart">
                    <span class="relative">
                        <svg class="w-7 h-7 text-emerald-700 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6a1 1 0 001 1.4H20"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
                        <span id="cart-count-badge" class="absolute -top-1.5 -right-2 bg-orange-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] px-1 flex items-center justify-center">{{ $cartCount ?? 0 }}</span>
                    </span>
                    <span class="hidden md:block text-left leading-tight">
                        <span class="block text-[10px] text-gray-500">Basket</span>
                        <span class="block text-sm font-bold text-emerald-800">Cart</span>
                    </span>
                </a>

                <a href="{{ route('marketplace.vendor.dashboard') }}" class="hidden lg:block bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold rounded-lg px-3.5 py-2.5 ml-1">Sell on FoodChain</a>
            </div>
        </div>

        {{-- Desktop category strip --}}
        <nav class="hidden lg:block bg-emerald-900">
            <div class="max-w-[1440px] mx-auto px-4">
                <ul class="flex items-center gap-1 text-[13px] text-emerald-100 whitespace-nowrap overflow-x-auto">
                    <li>
                        <a href="{{ route('marketplace.shop') }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 font-semibold text-white bg-emerald-800/70 hover:bg-emerald-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            All Categories
                        </a>
                    </li>
                    @php $navCategories = \App\Services\Marketplace\ProductService::getCategoriesStatic() ?? collect(); @endphp
                    @foreach (($navCategories ?? collect())->take(14) as $navCat)
                        <li>
                            <a href="{{ route('marketplace.category', $navCat->slug) }}" class="inline-flex items-center gap-1.5 px-3 py-2.5 hover:bg-emerald-700 hover:text-white transition-colors">
                                @if ($navCat->icon)<span>{{ $navCat->icon }}</span>@endif
                                {{ $navCat->name }}
                            </a>
                        </li>
                    @endforeach
                    <li><a href="{{ route('marketplace.flash-sale') }}" class="px-3 py-2.5 hover:bg-emerald-700 hover:text-white font-semibold text-orange-400 transition-colors">Flash Deals</a></li>
                    <li><a href="{{ route('marketplace.vendors') }}" class="px-3 py-2.5 hover:bg-emerald-700 hover:text-white transition-colors">Vendors</a></li>
                    <li><a href="{{ route('marketplace.bulk.index') }}" class="px-3 py-2.5 hover:bg-emerald-700 hover:text-white transition-colors">Community Bulk</a></li>
                </ul>
            </div>
        </nav>

        {{-- Mobile category strip --}}
        <div class="lg:hidden bg-emerald-900 overflow-x-auto">
            <ul class="flex items-center text-[13px] text-emerald-100 whitespace-nowrap px-3">
                @php $mobileNav = $navCategories ?? collect(); @endphp
                <li><a href="{{ route('marketplace.shop') }}" class="inline-block px-3 py-2.5 font-semibold text-white">All</a></li>
                @foreach ($mobileNav->take(8) as $navCat)
                    <li><a href="{{ route('marketplace.category', $navCat->slug) }}" class="inline-block px-3 py-2.5 hover:text-white">{{ $navCat->name }}</a></li>
                @endforeach
                <li><a href="{{ route('marketplace.flash-sale') }}" class="inline-block px-3 py-2.5 font-semibold text-orange-400">Deals</a></li>
            </ul>
        </div>
    </header>

    {{-- Main content --}}
    <main class="flex-1">
        <div class="max-w-[1440px] mx-auto px-4 py-5">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-emerald-950 text-emerald-200 mt-10">
        <div class="max-w-[1440px] mx-auto px-4 py-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 text-sm">
            <div>
                <h3 class="text-white font-bold mb-3 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-extrabold text-sm">FC</span>
                    FoodChain Marketplace
                </h3>
                <p class="text-emerald-300/80 leading-relaxed">Quality Nigerian groceries and household essentials delivered to your doorstep. Empowering communities through affordable food.</p>
            </div>
            <div>
                <h3 class="text-white font-bold mb-3">Shop</h3>
                <ul class="space-y-2">
                    @php $footerCats = \App\Services\Marketplace\ProductService::getCategoriesStatic() ?? collect(); @endphp
                    @foreach ($footerCats->take(6) as $footerCat)
                        <li><a href="{{ route('marketplace.category', $footerCat->slug) }}" class="hover:text-white">{{ $footerCat->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('marketplace.shop') }}" class="hover:text-white font-medium text-emerald-300">View all products</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-bold mb-3">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="{{ route('marketplace.flash-sale') }}" class="hover:text-white">Flash Sales</a></li>
                    <li><a href="{{ route('marketplace.vendors') }}" class="hover:text-white">Our Vendors</a></li>
                    <li><a href="{{ route('marketplace.bulk.index') }}" class="hover:text-white">Community Bulk Purchase</a></li>
                    <li><a href="{{ route('marketplace.vendors') }}" class="hover:text-white">Our Vendors</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-white font-bold mb-3">My Account</h3>
                <ul class="space-y-2">
                    @auth
                        <li><a href="{{ route('marketplace.orders.index') }}" class="hover:text-white">My Orders</a></li>
                        <li><a href="{{ route('marketplace.wallet.index') }}" class="hover:text-white">My Wallet</a></li>
                        <li><a href="{{ route('marketplace.loyalty.index') }}" class="hover:text-white">Loyalty Points</a></li>
                        <li><a href="{{ route('dashboard') }}" class="hover:text-white">Member Dashboard</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="hover:text-white">Login</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-white">Create Account</a></li>
                    @endauth
                </ul>
                <div class="mt-4 rounded-lg bg-emerald-900/60 px-3 py-2.5 text-xs text-emerald-300/90">
                    <p class="font-semibold text-emerald-200 mb-0.5">We Accept</p>
                    <p>FoodChain Wallet · Paystack · Flutterwave · Bank Transfer · Cash on Delivery</p>
                </div>
            </div>
        </div>
        <div class="border-t border-emerald-800/60">
            <p class="text-center text-xs text-emerald-400/70 py-4">&copy; {{ date('Y') }} {{ app_name() }}. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
    <script>
        // Flash auto-dismiss
        document.querySelectorAll('.flash-item').forEach(item => {
            setTimeout(() => item.classList.add('opacity-0', 'transition-opacity', 'duration-500'), 5000);
            setTimeout(() => item.remove(), 5500);
            item.querySelector('.close-flash')?.addEventListener('click', () => item.remove());
        });

        // Live search suggestions
        const searchInput = document.getElementById('search-input');
        const suggestionsBox = document.getElementById('search-suggestions');
        if (searchInput) {
            let debounceTimer;
            searchInput.addEventListener('input', () => {
                clearTimeout(debounceTimer);
                const term = searchInput.value.trim();
                if (term.length < 2) { suggestionsBox.classList.add('hidden'); suggestionsBox.innerHTML = ''; return; }
                debounceTimer = setTimeout(async () => {
                    try {
                        const res = await fetch(`/shop/autocomplete?q=${encodeURIComponent(term)}`);
                        const data = await res.json();
                        renderSuggestions(data, term);
                    } catch (e) { suggestionsBox.classList.add('hidden'); }
                }, 250);
            });
            document.addEventListener('click', (e) => {
                if (!e.target.closest('#search-form')) suggestionsBox.classList.add('hidden');
            });
        }
        function renderSuggestions(data, term) {
            let html = '';
            if ((data.categories || []).length) {
                html += '<div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase">Categories</div>';
                data.categories.forEach(c => { html += `<a href="/shop/category/${c.slug}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50">${c.name}</a>`; });
            }
            if ((data.products || []).length) {
                html += '<div class="px-4 py-2 text-xs font-bold text-gray-400 uppercase">Products</div>';
                data.products.forEach(p => { html += `<a href="/shop/product/${p.slug}" class="flex items-center gap-3 px-4 py-2 hover:bg-emerald-50">${p.image ? `<img src="${p.image}" class="w-8 h-8 rounded object-cover">` : `<span class="w-8 h-8 rounded bg-gray-100"></span>`}<div><div class="text-sm text-gray-800">${p.name}</div><div class="text-xs text-emerald-600">${formatNaira(p.selling_price)}</div></div></a>`; });
            }
            if (!html) html = '<div class="px-4 py-4 text-sm text-gray-500">No results for "' + term + '"</div>';
            suggestionsBox.innerHTML = html;
            suggestionsBox.classList.remove('hidden');
        }
        function formatNaira(v) { return '₦' + Number(v).toLocaleString(undefined, { minimumFractionDigits: 2 }); }

        // Cart badge update helper
        window.updateCartCount = async function () {
            const badge = document.getElementById('cart-count-badge');
            if (!badge) return;
            try {
                const res = await fetch('/marketplace/cart/count', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                badge.textContent = data.count;
                badge.classList.toggle('hidden', data.count === 0);
            } catch (e) {}
        };
    </script>
</body>
</html>

@extends('marketplace.layouts.app')

@section('content')
    {{-- Hero --}}
    <section class="relative rounded-2xl overflow-hidden bg-gradient-to-r from-emerald-900 via-emerald-800 to-emerald-700 mb-8">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="relative px-6 md:px-12 py-12 md:py-16 max-w-2xl">
            <span class="inline-block bg-orange-500 text-white text-xs font-bold uppercase tracking-wider px-3 py-1 rounded-full mb-4">Fresh from the Farm</span>
            <h1 class="text-white text-3xl md:text-5xl font-extrabold leading-tight">Quality Nigerian Groceries, <span class="text-emerald-300">Delivered Home</span></h1>
            <p class="text-emerald-100/90 mt-4 text-sm md:text-base leading-relaxed">Shop rice, beans, seasonings, proteins and household essentials at wholesale-friendly prices. Members enjoy exclusive deals.</p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('marketplace.shop') }}" class="bg-orange-500 hover:bg-orange-400 text-white font-semibold rounded-lg px-6 py-3 transition-colors">Shop Now</a>
                <a href="{{ route('marketplace.flash-sale') }}" class="bg-white/10 hover:bg-white/20 text-white font-semibold rounded-lg px-6 py-3 backdrop-blur transition-colors">View Flash Sales</a>
            </div>
            <div class="mt-8 flex items-center gap-6 text-emerald-100 text-xs">
                <span>✓ 300+ Products</span>
                <span>✓ Secure Payments</span>
                <span>✓ Nationwide Delivery</span>
            </div>
        </div>
    </section>

    {{-- Categories --}}
    <section class="mb-10">
        <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-4">Shop by Category</h2>
        <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-8 gap-3">
            @foreach ($categories as $cat)
                <a href="{{ route('marketplace.category', $cat->slug) }}" class="bg-white rounded-xl p-3 text-center shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-50 flex items-center justify-center text-2xl mb-2">{{ $cat->icon ?: '🛒' }}</div>
                    <p class="text-xs font-semibold text-gray-700 leading-tight">{{ $cat->name }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $cat->products_count ?? '' }}</p>
                </a>
            @endforeach
            <a href="{{ route('marketplace.shop') }}" class="bg-emerald-600 rounded-xl p-3 text-center shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
                <div class="w-12 h-12 mx-auto rounded-full bg-white/20 flex items-center justify-center text-white text-2xl mb-2">→</div>
                <p class="text-xs font-semibold text-white leading-tight">View All</p>
            </a>
        </div>
    </section>

    {{-- Flash Sale --}}
    @if ($flashSale && $flashSaleProducts->isNotEmpty())
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg md:text-xl font-bold text-gray-900">{{ $flashSale->name }}</h2>
                    <div class="flex items-center gap-1 text-white text-xs font-bold" id="flash-timer">
                        <span class="bg-red-500 rounded px-1.5 py-0.5" id="fh">00</span>:<span class="bg-red-500 rounded px-1.5 py-0.5" id="fm">00</span>:<span class="bg-red-500 rounded px-1.5 py-0.5" id="fs">00</span>
                    </div>
                </div>
                <a href="{{ route('marketplace.flash-sale') }}" class="text-emerald-700 text-sm font-semibold hover:underline">View all →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($flashSaleProducts as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Today's Deals --}}
    @if ($todayDeals->isNotEmpty())
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-900">🔥 Today's Deals</h2>
                <a href="{{ route('marketplace.shop', ['discounted' => 1]) }}" class="text-emerald-700 text-sm font-semibold hover:underline">View all →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($todayDeals as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Featured --}}
    @if ($featured->isNotEmpty())
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-900">⭐ Featured Products</h2>
                <a href="{{ route('marketplace.shop') }}" class="text-emerald-700 text-sm font-semibold hover:underline">View all →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($featured as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Community Choice --}}
    @if ($communityChoice->isNotEmpty())
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-900">🤝 Community Choice</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                @foreach ($communityChoice as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Recommended --}}
    @if ($recommended->isNotEmpty())
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-900">✨ Recommended for You</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($recommended as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- New Arrivals --}}
    @if ($newArrivals->isNotEmpty())
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-900">🆕 New Arrivals</h2>
                <a href="{{ route('marketplace.shop', ['sort' => 'newest']) }}" class="text-emerald-700 text-sm font-semibold hover:underline">View all →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($newArrivals as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Best Sellers --}}
    @if ($bestSellers->isNotEmpty())
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-900">🏆 Best Sellers</h2>
                <a href="{{ route('marketplace.shop', ['sort' => 'best_selling']) }}" class="text-emerald-700 text-sm font-semibold hover:underline">View all →</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($bestSellers as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Member Specials --}}
    @if ($specials->isNotEmpty())
        <section class="mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-900">🎁 Member Specials</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($specials as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Recently Viewed --}}
    @if ($recentlyViewed->isNotEmpty())
        <section class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-900">🕒 Recently Viewed</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($recentlyViewed as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endif

    {{-- Promo banner --}}
    <section class="rounded-2xl bg-gradient-to-r from-emerald-800 to-emerald-600 p-6 md:p-8 mb-4 flex flex-col md:flex-row items-center justify-between gap-4">
        <div>
            <h3 class="text-white text-xl md:text-2xl font-bold">Save more with Community Bulk Purchase</h3>
            <p class="text-emerald-100 mt-1 text-sm">Team up with your community to buy in bulk and unlock bigger discounts.</p>
        </div>
        <a href="{{ route('marketplace.bulk.index') }}" class="bg-white text-emerald-800 font-bold rounded-lg px-6 py-3 hover:bg-emerald-50 transition-colors whitespace-nowrap">Explore Bulk Deals</a>
    </section>

    @push('scripts')
    @if ($flashSale && $flashSale->ends_at)
    <script>
        const endTime = new Date("{{ $flashSale->ends_at->toIso8601String() }}").getTime();
        const timer = setInterval(() => {
            const now = new Date().getTime();
            const dist = endTime - now;
            if (dist <= 0) { clearInterval(timer); document.getElementById('flash-timer').innerHTML = 'Ended'; return; }
            const h = Math.floor(dist / 3600000);
            const m = Math.floor((dist % 3600000) / 60000);
            const s = Math.floor((dist % 60000) / 1000);
            document.getElementById('fh').textContent = String(h).padStart(2, '0');
            document.getElementById('fm').textContent = String(m).padStart(2, '0');
            document.getElementById('fs').textContent = String(s).padStart(2, '0');
        }, 1000);
    </script>
    @endif
    @endpush
@endsection

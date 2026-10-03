@extends('marketplace.layouts.app')

@section('content')
    {{-- Hero --}}
    <section class="relative overflow-hidden rounded-[28px] bg-gradient-to-r from-emerald-900 via-emerald-800 to-teal-700 mb-8 shadow-[0_30px_80px_rgba(16,185,129,0.18)]">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 20% 50%, white 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="absolute right-0 top-0 h-full w-1/2 bg-gradient-to-l from-white/10 to-transparent"></div>

        <div class="relative grid gap-8 px-6 py-8 md:px-10 md:py-10 lg:grid-cols-[1.4fr_0.8fr] lg:items-end">
            <div class="max-w-2xl">
                <span class="inline-block bg-orange-500 text-white text-[11px] font-bold uppercase tracking-[0.18em] px-3 py-1.5 rounded-full mb-4 shadow-lg shadow-orange-500/25">Fresh from the farm</span>
                <h1 class="text-white text-3xl md:text-5xl font-extrabold leading-[1.05]">
                    Quality groceries for every home,
                    <span class="text-emerald-200">at member prices.</span>
                </h1>
                <p class="text-emerald-50/90 mt-4 text-sm md:text-base leading-relaxed max-w-xl">
                    Shop rice, beans, seasonings, proteins and household essentials with same-day dispatch, secure checkout and community deals designed to stretch your budget further.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    <a href="{{ route('marketplace.shop') }}" class="bg-orange-500 hover:bg-orange-400 text-white font-semibold rounded-xl px-6 py-3.5 transition-all shadow-lg shadow-orange-500/25">Shop Now</a>
                    <a href="{{ route('marketplace.flash-sale') }}" class="bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl px-6 py-3.5 backdrop-blur transition-all border border-white/15">View Flash Sales</a>
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-4 text-emerald-100 text-xs md:text-sm">
                    <span class="inline-flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-emerald-300"></span> 300+ products</span>
                    <span class="inline-flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-emerald-300"></span> Secure payments</span>
                    <span class="inline-flex items-center gap-2"><span class="inline-block h-2 w-2 rounded-full bg-emerald-300"></span> Delivery to your location</span>
                </div>
            </div>

            <div class="rounded-[24px] bg-white/10 backdrop-blur-md border border-white/15 p-4 shadow-2xl shadow-emerald-900/25">
                <div class="rounded-[18px] bg-white p-4 text-gray-900">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.18em] text-emerald-600 font-bold">This week</p>
                            <h3 class="text-xl font-extrabold">Member savings</h3>
                        </div>
                        <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full">-25%</span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between rounded-xl bg-emerald-50 p-3">
                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-emerald-700 font-bold">Rice bundle</p>
                                <p class="text-sm font-semibold text-gray-700">10kg family pack</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs line-through text-gray-400">₦12,500</p>
                                <p class="text-base font-extrabold text-emerald-700">₦9,250</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between rounded-xl bg-amber-50 p-3">
                            <div>
                                <p class="text-[11px] uppercase tracking-wider text-amber-700 font-bold">Protein box</p>
                                <p class="text-sm font-semibold text-gray-700">Chicken & tuna combo</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs line-through text-gray-400">₦8,300</p>
                                <p class="text-base font-extrabold text-amber-600">₦6,200</p>
                            </div>
                        </div>
                    </div>
                </div>
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

    <section class="mb-10">
        <div class="mb-5 flex items-center justify-between gap-3">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Why shoppers trust FoodChain</h2>
        </div>
        <div class="grid gap-4 md:grid-cols-3">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-2xl mb-3">🚚</div>
                <h3 class="font-bold text-gray-900 mb-2">Fast delivery</h3>
                <p class="text-sm text-gray-600">Delivery in our coverage locations with flexible windows and reliable order tracking.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-2xl mb-3">🔐</div>
                <h3 class="font-bold text-gray-900 mb-2">Secure checkout</h3>
                <p class="text-sm text-gray-600">Protected payments and a trusted marketplace experience built for peace of mind.</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-2xl mb-3">💰</div>
                <h3 class="font-bold text-gray-900 mb-2">Better value</h3>
                <p class="text-sm text-gray-600">Weekly offers, member pricing and bulk deals help households get more for less.</p>
            </div>
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
    <section class="rounded-[28px] bg-gradient-to-r from-emerald-800 to-emerald-600 p-6 md:p-8 mb-4 flex flex-col md:flex-row items-center justify-between gap-4 shadow-[0_18px_50px_rgba(16,185,129,0.25)]">
        <div>
            <p class="text-[11px] uppercase tracking-[0.18em] text-emerald-100 font-bold mb-2">Everyday savings</p>
            <h3 class="text-white text-xl md:text-2xl font-bold">Save more with Flash Deals &amp; Loyalty Points</h3>
            <p class="text-emerald-100 mt-1 text-sm">Cash in on weekly flash sales and earn points you can redeem as wallet credit on future orders.</p>
        </div>
        <a href="{{ route('marketplace.flash-sale') }}" class="bg-white text-emerald-800 font-bold rounded-xl px-6 py-3 hover:bg-emerald-50 transition-colors whitespace-nowrap shadow-lg shadow-emerald-900/15">See Today's Deals</a>
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

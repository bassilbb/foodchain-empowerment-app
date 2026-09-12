@php
    $isSearch = ! empty($term);
    $baseUrl = $isSearch ? route('marketplace.search') : route('marketplace.shop');
    $queryAll = request()->query();

    $makeUrl = function (array $extra = [], array $remove = []) use ($baseUrl, $queryAll): string {
        $q = array_filter(
            $queryAll,
            fn ($v, $k) => ! in_array($k, $remove, true) && $v !== null && $v !== '',
            ARRAY_FILTER_USE_BOTH,
        );

        return $baseUrl.'?'.http_build_query(array_merge($q, $extra));
    };

    $activeCount = collect($queryAll)->filter(fn ($v, $k) => in_array($k, ['brand', 'category', 'vendor', 'min_price', 'max_price', 'rating', 'in_stock', 'discounted', 'q'], true) && $v !== null && $v !== '')->count();

    $brandNames = $brands->keyBy('id')->map->name;

    $selectedBrand = (int) ($filters['brand'] ?? 0);
    $minPrice = $filters['min_price'] ?? '';
    $maxPrice = $filters['max_price'] ?? '';
    $selectedRating = (int) ($filters['rating'] ?? 0);
    $hasStock = ! empty($filters['in_stock']);
    $hasDiscount = ! empty($filters['discounted']);
    $sort = $filters['sort'] ?? '';

    $categoryCounts = \Illuminate\Support\Facades\DB::table('marketplace_products')
        ->where('is_active', true)
        ->whereNull('deleted_at')
        ->selectRaw('category_id, count(*) as total')
        ->groupBy('category_id')
        ->pluck('total', 'category_id');

    $childCount = function ($cat) use ($categoryCounts) {
        return (int) ($categoryCounts[$cat->id] ?? 0);
    };

    $groupCount = function ($parent) use ($categoryCounts) {
        return $parent->children->sum(fn ($c) => (int) ($categoryCounts[$c->id] ?? 0));
    };
@endphp

@extends('marketplace.layouts.app')

@section('content')
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-[13px] text-gray-500 mb-3">
        <a href="{{ route('marketplace.home') }}" class="hover:text-emerald-700">Home</a>
        <span>/</span>
        @if ($isSearch)
            <a href="{{ route('marketplace.shop') }}" class="hover:text-emerald-700">All Products</a>
            <span>/</span>
        @endif
        <span class="text-gray-800 font-medium">{{ $isSearch ? 'Search results' : 'All Products' }}</span>
    </div>

    @unless ($isSearch)
        @php
            $heroProds = $products->take(8);
        @endphp
        <style>
            .mhero-viewport { overflow: hidden; position: relative; border-radius: 0.75rem; background: linear-gradient(90deg, #065f46, #059669, #10b981); padding: 12px 0; }
            .mhero-track { display: flex; width: max-content; animation: mhero-scroll 30s linear infinite; }
            .mhero-viewport:hover .mhero-track { animation-play-state: paused; }
            @keyframes mhero-scroll { from { transform: translateX(0); } to { transform: translateX(-50%); } }
            .hero-slide { display: none; }
            .hero-slide.active { display: block; animation: hero-fade .5s ease; }
            @keyframes hero-fade { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
        </style>

        <section class="mb-4">
            <div class="grid grid-cols-1 lg:grid-cols-[1.35fr_1fr] gap-3">
                <div class="relative overflow-hidden rounded-xl bg-gradient-to-r from-emerald-900 via-emerald-700 to-emerald-600 text-white flex">
                    <div class="relative z-10 px-5 py-4 md:px-7 md:py-5 flex flex-col justify-center flex-1">
                        @foreach ([
                            ['kicker' => 'Nationwide grocery delivery', 'title' => 'Buy at your local price, anywhere in Nigeria', 'sub' => 'Switch your delivery state and every price updates instantly.'],
                            ['kicker' => 'Fresh from trusted vendors', 'title' => 'Quality staples, delivered to your doorstep', 'sub' => 'Rice, beans, yams, spices and more from verified FoodChain vendors.'],
                            ['kicker' => 'Big savings every day', 'title' => 'Flash deals and member-low prices', 'sub' => 'Watch your cart total drop as deals roll in. Free delivery on orders above ₦50,000.'],
                        ] as $i => $slide)
                            <div class="hero-slide {{ $i === 0 ? 'active' : '' }}" data-hero-slide>
                                <p class="text-[10px] md:text-[11px] font-bold uppercase tracking-widest text-emerald-200/90 mb-1">{{ $slide['kicker'] }}</p>
                                <h1 class="text-base md:text-2xl font-extrabold leading-tight max-w-xl">{{ $slide['title'] }}</h1>
                                <p class="text-[11px] md:text-[12px] text-emerald-100/90 mt-1 max-w-lg">{{ $slide['sub'] }}</p>
                                <div class="mt-2.5 flex flex-wrap gap-2">
                                    <a href="{{ route('marketplace.shop') }}" class="bg-orange-500 hover:bg-orange-400 text-white text-[11px] md:text-xs font-bold rounded-md px-3 py-1.5 transition-colors">Shop now</a>
                                    <a href="{{ route('register') }}" class="bg-white/10 hover:bg-white/20 border border-white/30 text-white text-[11px] md:text-xs font-semibold rounded-md px-3 py-1.5 transition-colors">Join free today</a>
                                </div>
                            </div>
                        @endforeach
                        <div class="flex gap-1.5 mt-2.5" data-hero-dots></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-3 flex flex-col">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-xs font-bold text-gray-800">Trending now in {{ mp_state() }}</h2>
                        <a href="{{ route('marketplace.shop') }}" class="text-[11px] text-emerald-700 font-semibold hover:underline">View all</a>
                    </div>
                    <div class="space-y-1.5 flex-1">
                        @foreach ($heroProds->take(6) as $heroProd)
                            <a href="{{ route('marketplace.product', $heroProd->slug) }}" class="flex items-center gap-2 group">
                                <span class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 font-bold text-xs flex items-center justify-center shrink-0 overflow-hidden">
                                    @if ($heroProd->image)
                                        <img src="{{ $heroProd->image }}" alt="" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='/images/product-placeholder.svg';">
                                    @else
                                        {{ strtoupper(substr($heroProd->name, 0, 1)) }}
                                    @endif
                                </span>
                                <span class="flex-1 min-w-0">
                                    <span class="block text-[11px] text-gray-600 font-medium truncate group-hover:text-emerald-700">{{ $heroProd->name }}</span>
                                    <span class="block text-emerald-700 font-bold text-[11px]">₦{{ number_format(mp_price($heroProd), 0) }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            @if ($heroProds->count() >= 3)
                <div class="mt-2.5 mhero-viewport">
                    <div class="mhero-track items-center">
                        @for ($dup = 0; $dup < 2; $dup++)
                            @foreach ($heroProds as $heroProd)
                                <a href="{{ route('marketplace.product', $heroProd->slug) }}" class="flex items-center gap-2 pr-6">
                                    <span class="w-11 h-11 rounded-lg overflow-hidden shrink-0 ring-1 ring-white/25 bg-white/10 flex items-center justify-center font-bold text-emerald-100 text-xs">
                                        @if ($heroProd->image)
                                            <img src="{{ $heroProd->image }}" alt="" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='/images/product-placeholder.svg';">
                                        @else
                                            {{ strtoupper(substr($heroProd->name, 0, 1)) }}
                                        @endif
                                    </span>
                                    <span class="whitespace-nowrap text-white/95 text-[11px] md:text-xs font-semibold">{{ $heroProd->name }}</span>
                                </a>
                            @endforeach
                        @endfor
                    </div>
                </div>
            @endif
            </section>
            <script>
                (function () {
                    const slides = Array.from(document.querySelectorAll('[data-hero-slide]'));
                    const dotsWrap = document.querySelector('[data-hero-dots]');
                    if (!slides.length) return;
                    let idx = 0;
                    slides.forEach((_, i) => {
                        const dot = document.createElement('button');
                        dot.type = 'button';
                        dot.className = 'w-1.5 h-1.5 rounded-full transition-colors ' + (i === 0 ? 'bg-white' : 'bg-white/40');
                        dot.setAttribute('aria-label', 'Slide ' + (i + 1));
                        dot.addEventListener('click', () => go(i));
                        dotsWrap.appendChild(dot);
                    });
                    const dots = Array.from(dotsWrap.children);
                    function go(i) {
                        slides[idx].classList.remove('active');
                        dots[idx].classList.remove('bg-white');
                        dots[idx].classList.add('bg-white/40');
                        idx = (i + slides.length) % slides.length;
                        slides[idx].classList.add('active');
                        dots[idx].classList.remove('bg-white/40');
                        dots[idx].classList.add('bg-white');
                    }
                    setInterval(() => go(idx + 1), 6000);
                })();
            </script>
        </section>
    @endunless


    <div class="grid grid-cols-1 lg:grid-cols-[270px_1fr] gap-5">

        {{-- Sidebar: categories + filters --}}
        <button type="button" id="filter-toggle" class="lg:hidden w-full bg-white border border-gray-300 rounded-lg px-4 py-2.5 text-sm font-semibold text-gray-700 flex items-center justify-center gap-2 mb-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
            Show Filters @if ($activeCount)({{ $activeCount }})@endif
        </button>

        <aside id="shop-filter-sidebar" class="hidden lg:block">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden lg:sticky lg:top-36 lg:max-h-[calc(100vh-10rem)] lg:overflow-y-auto">

                {{-- Category tree --}}
                <div class="border-b border-gray-200">
                    <h3 class="px-4 pt-4 pb-2 text-[13px] font-bold text-gray-800 uppercase tracking-wide">Categories</h3>
                    <a href="{{ route('marketplace.shop') }}"
                       class="flex items-center justify-between px-4 py-2 text-[13px] {{ $isSearch ? '' : 'bg-emerald-50 text-emerald-700 font-semibold border-r-2 border-emerald-600' }} hover:bg-gray-50">
                        <span>All Products</span>
                        <span class="text-xs text-gray-400">{{ $products->total() }}</span>
                    </a>
                    @foreach ($categories as $group)
                        <div class="border-t border-gray-100">
                            <button type="button" data-cat-toggle class="w-full flex items-center gap-2 px-4 py-2.5 text-[13px] font-semibold text-gray-700 hover:bg-gray-50 text-left">
                                <span class="text-gray-400 text-[11px] transition-transform duration-150" data-cat-arrow>▸</span>
                                @if ($group->icon)<span class="text-sm">{{ $group->icon }}</span>@endif
                                {{ $group->name }}
                                <span class="ml-auto text-xs text-gray-400 font-normal">{{ number_format($groupCount($group)) }}</span>
                            </button>
                            <div data-cat-children class="hidden">
                                @foreach ($group->children as $child)
                                    <a href="{{ route('marketplace.category', $child->slug) }}"
                                       class="flex items-center justify-between pl-8 pr-4 py-1.5 text-[13px] text-gray-600 hover:text-emerald-700 hover:bg-emerald-50/50">
                                        {{ $child->name }}
                                        <span class="text-xs text-gray-400">{{ number_format($childCount($child)) }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Filter form --}}
                <form method="GET" action="{{ $baseUrl }}" class="p-4">
                    @if ($isSearch)
                        <input type="hidden" name="q" value="{{ $term }}">
                    @endif

                    {{-- Brand --}}
                    <div class="mb-4">
                        <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wide mb-2">Brand</h3>
                        <div class="space-y-1.5 max-h-44 overflow-y-auto pr-1">
                            <label class="flex items-center gap-2 text-[13px] text-gray-600 cursor-pointer hover:text-gray-900">
                                <input type="radio" name="brand" value="" @checked(! $selectedBrand)
                                       class="border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                <span class="flex-1">All brands</span>
                            </label>
                            @foreach ($brands as $brand)
                                <label class="flex items-center gap-2 text-[13px] text-gray-600 cursor-pointer hover:text-gray-900">
                                    <input type="radio" name="brand" value="{{ $brand->id }}" @checked($selectedBrand === $brand->id)
                                           class="border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                    <span class="flex-1 truncate">{{ $brand->name }}</span>
                                    <span class="text-xs text-gray-400">{{ number_format($brand->products_count) }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="mb-4">
                        <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wide mb-2">Price (₦)</h3>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" placeholder="Min" value="{{ $minPrice }}" class="w-full text-[13px] rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <span class="text-gray-400 text-xs">-</span>
                            <input type="number" name="max_price" placeholder="Max" value="{{ $maxPrice }}" class="w-full text-[13px] rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                    </div>

                    {{-- Rating --}}
                    <div class="mb-4">
                        <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wide mb-2">Rating</h3>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-[13px] text-gray-600 cursor-pointer hover:text-gray-900">
                                <input type="radio" name="rating" value="" @checked(! $selectedRating)
                                       class="border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                Any rating
                            </label>
                            @foreach ([4, 3, 2, 1] as $star)
                                <label class="flex items-center gap-2 text-[13px] text-gray-600 cursor-pointer hover:text-gray-900">
                                    <input type="radio" name="rating" value="{{ $star }}" @checked($selectedRating === $star)
                                           class="border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                    <span class="flex items-center gap-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <svg class="w-3.5 h-3.5 {{ $i <= $star ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        @endfor
                                        <span class="text-xs text-gray-500">&amp; up</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Availability --}}
                    <div class="mb-4">
                        <h3 class="text-[13px] font-bold text-gray-800 uppercase tracking-wide mb-2">Availability</h3>
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-[13px] text-gray-600 cursor-pointer">
                                <input type="checkbox" name="in_stock" value="1" @checked($hasStock)
                                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                In stock only
                            </label>
                            <label class="flex items-center gap-2 text-[13px] text-gray-600 cursor-pointer">
                                <input type="checkbox" name="discounted" value="1" @checked($hasDiscount)
                                       class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                                On discount
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-md py-2.5 transition-colors">Apply Filters</button>
                    @if ($activeCount)
                        <a href="{{ $makeUrl([], ['category', 'vendor', 'brand', 'min_price', 'max_price', 'rating', 'in_stock', 'discounted']) }}" class="block text-center mt-2 text-xs text-gray-500 hover:text-emerald-700">Clear all filters</a>
                    @endif
                </form>
            </div>
        </aside>

        {{-- Products --}}
        <div>
            @if ($isSearch)
                <div class="bg-white rounded-lg border border-gray-200 px-4 py-3 mb-3">
                    <h1 class="text-base font-bold text-gray-800">Results for "{{ $term }}"</h1>
                    <p class="text-[13px] text-gray-500">{{ number_format($products->total()) }} product(s) found</p>
                </div>
            @endif

            {{-- Toolbar --}}
            <div class="flex flex-wrap items-center justify-between gap-3 bg-white rounded-lg border border-gray-200 px-4 py-2.5 mb-3">
                <p class="text-[13px] text-gray-600">
                    <span class="font-bold text-gray-800">{{ number_format($products->total()) }}</span> products
                    @if ($selectedBrand && $brandNames->has($selectedBrand))
                        in <span class="font-medium text-gray-800">{{ $brandNames[$selectedBrand] }}</span>
                    @endif
                </p>
                <form method="GET" class="flex items-center gap-2">
                    @foreach (request()->except(['sort', 'page']) as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $v)<input type="hidden" name="{{ $key }}[]" value="{{ $v }}">@endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label for="sort-select" class="text-[13px] text-gray-500 hidden sm:inline">Sort by:</label>
                    <select name="sort" id="sort-select" onchange="this.form.submit()"
                            class="text-[13px] rounded-md border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 py-1.5">
                        <option value="">Relevance</option>
                        <option value="price_asc" @selected($sort === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected($sort === 'price_desc')>Price: High to Low</option>
                        <option value="newest" @selected($sort === 'newest')>Newest First</option>
                        <option value="best_selling" @selected($sort === 'best_selling')>Best Selling</option>
                        <option value="rating" @selected($sort === 'rating')>Top Rated</option>
                        <option value="name_asc" @selected($sort === 'name_asc')>Name A-Z</option>
                    </select>
                </form>
            </div>

            {{-- Active filter chips --}}
            @if ($activeCount)
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @if ($isSearch)
                        <a href="{{ $makeUrl([], ['q']) }}" class="inline-flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium rounded-full px-3 py-1.5 hover:bg-emerald-100">
                            "{{ $term }}"
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                    @if ($selectedBrand && $brandNames->has($selectedBrand))
                        <a href="{{ $makeUrl([], ['brand']) }}" class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full px-3 py-1.5 hover:bg-gray-200">
                            {{ $brandNames[$selectedBrand] }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                    @if ($minPrice !== '' || $maxPrice !== '')
                        @php
                            $rangeParts = [];
                            if ($minPrice !== '') { $rangeParts[] = 'From ₦'.number_format((float) $minPrice); }
                            if ($maxPrice !== '') { $rangeParts[] = 'To ₦'.number_format((float) $maxPrice); }
                            $rangeText = implode(' ', $rangeParts);
                        @endphp
                        <a href="{{ $makeUrl([], ['min_price', 'max_price']) }}" class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full px-3 py-1.5 hover:bg-gray-200">
                            {{ $rangeText }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                    @if ($selectedRating)
                        <a href="{{ $makeUrl([], ['rating']) }}" class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full px-3 py-1.5 hover:bg-gray-200">
                            {{ $selectedRating }} stars &amp; up
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                    @if ($hasStock)
                        <a href="{{ $makeUrl([], ['in_stock']) }}" class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full px-3 py-1.5 hover:bg-gray-200">
                            In stock
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                    @if ($hasDiscount)
                        <a href="{{ $makeUrl([], ['discounted']) }}" class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 text-xs font-medium rounded-full px-3 py-1.5 hover:bg-gray-200">
                            On discount
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </a>
                    @endif
                </div>
            @endif

            @if ($products->isEmpty())
                <div class="bg-white rounded-lg border border-gray-200 p-16 text-center">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <h2 class="text-lg font-bold text-gray-800 mb-1">No products found</h2>
                    <p class="text-sm text-gray-500">Try adjusting your filters or browse other categories.</p>
                    <a href="{{ $baseUrl }}" class="inline-block mt-4 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-md px-5 py-2.5">Clear Filters</a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-2.5 sm:gap-3">
                    @foreach ($products as $product)
                        @include('marketplace.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Mobile filter drawer toggle
        const filterToggle = document.getElementById('filter-toggle');
        const filterSidebar = document.getElementById('shop-filter-sidebar');
        if (filterToggle && filterSidebar) {
            filterToggle.addEventListener('click', () => {
                const hidden = filterSidebar.classList.toggle('hidden');
                filterSidebar.classList.toggle('block', !hidden);
                filterToggle.setAttribute('aria-expanded', String(!hidden));
            });
        }

        // Category accordion
        document.querySelectorAll('[data-cat-toggle]').forEach(btn => {
            btn.addEventListener('click', () => {
                const box = btn.closest('div');
                const children = box.querySelector('[data-cat-children]');
                const arrow = btn.querySelector('[data-cat-arrow]');
                const open = !children.classList.contains('hidden');
                children.classList.toggle('hidden', open);
                arrow.classList.toggle('rotate-90', !open);
                btn.classList.toggle('text-emerald-700', !open);
            });
        });
    </script>
@endsection

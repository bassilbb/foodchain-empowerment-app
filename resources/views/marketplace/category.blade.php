@extends('marketplace.layouts.app')

@section('content')
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('marketplace.home') }}" class="hover:text-emerald-700">Home</a>
        <span>/</span>
        @if ($category->parent)
            <a href="{{ route('marketplace.category', $category->parent->slug) }}" class="hover:text-emerald-700">{{ $category->parent->name }}</a>
            <span>/</span>
        @endif
        <span class="text-gray-800 font-medium">{{ $category->name }}</span>
    </div>

    <div class="bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-700 rounded-[28px] p-6 md:p-8 mb-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-5 shadow-[0_20px_50px_rgba(6,95,70,0.22)]">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-100">Marketplace category</p>
            <h1 class="text-white text-2xl md:text-3xl font-extrabold mt-2">{{ $category->icon ?? '' }} {{ $category->name }}</h1>
            @if ($category->description)
                <p class="text-emerald-100 text-sm mt-2 max-w-2xl">{{ $category->description }}</p>
            @endif
        </div>
        @if ($category->children->isNotEmpty())
            <div class="hidden md:flex items-center gap-2 flex-wrap justify-end max-w-lg">
                @foreach ($category->children as $child)
                    <a href="{{ route('marketplace.category', $child->slug) }}" class="bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-full px-3 py-1.5 border border-white/15">{{ $child->name }}</a>
                @endforeach
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-[22px] border border-gray-100 shadow-[0_16px_35px_rgba(15,23,42,0.05)] p-4 sticky top-24">
                <h3 class="font-bold text-gray-900 mb-3">Refine</h3>
                <form method="GET" action="{{ route('marketplace.category', $category->slug) }}">
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-2 block">Brand</label>
                        <select name="brand" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">All Brands</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(($filters['brand'] ?? null) == $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-2 block">Price</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" placeholder="Min" value="{{ $filters['min_price'] ?? '' }}" class="w-full text-sm rounded-lg border-gray-300">
                            <span class="text-gray-400">-</span>
                            <input type="number" name="max_price" placeholder="Max" value="{{ $filters['max_price'] ?? '' }}" class="w-full text-sm rounded-lg border-gray-300">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-2 block">Rating</label>
                        <select name="rating" class="w-full text-sm rounded-lg border-gray-300">
                            <option value="">Any</option>
                            @foreach ([4, 3, 2, 1] as $r)
                                <option value="{{ $r }}" @selected(($filters['rating'] ?? null) == $r)>{{ $r }}★ & above</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input type="checkbox" name="in_stock" value="1" @checked(isset($filters['in_stock'])) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            In Stock Only
                        </label>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl py-2.5 shadow-lg shadow-emerald-600/20">Apply</button>
                </form>
            </div>
        </aside>

        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-4 bg-white rounded-[20px] border border-gray-100 shadow-[0_12px_28px_rgba(15,23,42,0.04)] p-3">
                <p class="text-sm text-gray-600"><span class="font-bold text-gray-900">{{ $products->total() }}</span> product(s)</p>
                <form method="GET" action="{{ route('marketplace.category', $category->slug) }}">
                    @foreach (request()->except(['sort']) as $key => $value)
                        @if (is_array($value))
                            @foreach ($value as $v)<input type="hidden" name="{{ $key }}[]" value="{{ $v }}">@endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <select name="sort" onchange="this.form.submit()" class="text-sm rounded-lg border-gray-300">
                        <option value="">Sort: Default</option>
                        <option value="price_asc" @selected(($filters['sort'] ?? null) === 'price_asc')>Price: Low to High</option>
                        <option value="price_desc" @selected(($filters['sort'] ?? null) === 'price_desc')>Price: High to Low</option>
                        <option value="newest" @selected(($filters['sort'] ?? null) === 'newest')>Newest First</option>
                        <option value="best_selling" @selected(($filters['sort'] ?? null) === 'best_selling')>Best Selling</option>
                        <option value="rating" @selected(($filters['sort'] ?? null) === 'rating')>Top Rated</option>
                    </select>
                </form>
            </div>

            @if ($products->isEmpty())
                <div class="bg-white rounded-[22px] border border-gray-100 shadow-[0_12px_28px_rgba(15,23,42,0.04)] p-12 text-center">
                    <div class="text-5xl mb-4">📦</div>
                    <h3 class="text-lg font-bold text-gray-800 mb-1">No products in this category yet</h3>
                    <p class="text-sm text-gray-500">Check back soon or browse other categories.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
                    @foreach ($products as $product)
                        @include('marketplace.partials.product-card', ['product' => $product])
                    @endforeach
                </div>
                <div class="mt-6">{{ $products->links() }}</div>
            @endif
        </div>
    </div>
@endsection

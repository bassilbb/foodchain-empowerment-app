@extends('marketplace.layouts.app')

@section('content')
    <div class="mb-6 rounded-[28px] bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-700 p-6 md:p-8 text-white shadow-[0_20px_50px_rgba(6,95,70,0.22)]">
        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-100">Trusted sellers</p>
        <h1 class="text-2xl md:text-3xl font-extrabold mt-2">Explore verified vendors</h1>
        <p class="text-emerald-100 mt-2 max-w-2xl">Shop from reliable FoodChain vendors with verified products, dependable delivery, and trusted customer service.</p>
    </div>

    @if ($vendors->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">🏪</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No vendors yet</h3>
            <p class="text-sm text-gray-500">Check back soon.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($vendors as $vendor)
                <a href="{{ route('marketplace.vendor', $vendor) }}" class="group bg-white rounded-[22px] border border-gray-100 shadow-[0_16px_35px_rgba(15,23,42,0.05)] hover:shadow-[0_20px_45px_rgba(15,23,42,0.07)] transition-all p-5 flex items-start gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center text-emerald-700 text-2xl font-extrabold shrink-0 group-hover:scale-105 transition-transform">
                        {{ strtoupper(substr($vendor->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 truncate">{{ $vendor->business_name ?: $vendor->name }}</h3>
                        @if ($vendor->rating > 0)
                            <div class="flex items-center gap-1 mt-0.5">
                                <span class="text-yellow-400">★</span>
                                <span class="text-xs text-gray-600">{{ number_format($vendor->rating, 1) }} ({{ $vendor->review_count }})</span>
                            </div>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">{{ $vendor->city ?: 'Nigeria' }} · {{ $vendor->products_count }} products</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $vendors->links() }}</div>
    @endif
@endsection

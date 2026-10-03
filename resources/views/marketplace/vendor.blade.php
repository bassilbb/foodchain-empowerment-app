@extends('marketplace.layouts.app')

@section('content')
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('marketplace.vendors') }}" class="hover:text-emerald-700">Vendors</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">{{ $vendor->business_name ?: $vendor->name }}</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 flex items-center gap-4">
        <div class="w-20 h-20 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-700 text-3xl font-extrabold">
            {{ strtoupper(substr($vendor->name, 0, 1)) }}
        </div>
        <div class="flex-1">
            <h1 class="text-2xl font-extrabold text-gray-900">{{ $vendor->business_name ?: $vendor->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $vendor->city ?: 'Nigeria' }} · {{ $products->total() }} products</p>
            @if ($vendor->rating > 0)
                <div class="flex items-center gap-1 mt-1">
                    <span class="text-yellow-400">★</span>
                    <span class="text-sm text-gray-600">{{ number_format($vendor->rating, 1) }} ({{ $vendor->review_count }} reviews)</span>
                </div>
            @endif
        </div>
    </div>

    @if ($vendor->description)
        <p class="text-sm text-gray-600 mb-6 leading-relaxed">{{ $vendor->description }}</p>
    @endif

    @if ($products->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
            <div class="text-5xl mb-4">📦</div>
            <h3 class="text-lg font-bold text-gray-800">No products from this vendor yet</h3>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach ($products as $product)
                @include('marketplace.partials.product-card', ['product' => $product])
            @endforeach
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    @endif
@endsection

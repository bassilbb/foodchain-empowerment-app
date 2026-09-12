@extends('marketplace.layouts.app')

@section('content')
    <h1 class="text-2xl font-extrabold text-gray-900 mb-6">Our Vendors</h1>

    @if ($vendors->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">🏪</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No vendors yet</h3>
            <p class="text-sm text-gray-500">Check back soon.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($vendors as $vendor)
                <a href="{{ route('marketplace.vendor', $vendor) }}" class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-5 flex items-start gap-4">
                    <div class="w-16 h-16 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-700 text-2xl font-extrabold shrink-0">
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

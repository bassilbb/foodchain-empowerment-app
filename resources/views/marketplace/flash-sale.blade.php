@extends('marketplace.layouts.app')

@section('content')
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('marketplace.home') }}" class="hover:text-emerald-700">Home</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">{{ $flashSale->name }}</span>
    </div>

    <div class="bg-gradient-to-r from-red-600 to-orange-500 rounded-2xl p-8 mb-6 text-white">
        <h1 class="text-2xl md:text-3xl font-extrabold">⚡ {{ $flashSale->name }}</h1>
        @if ($flashSale->description)
            <p class="text-red-100 mt-2 text-sm">{{ $flashSale->description }}</p>
        @endif
        @if ($flashSale->ends_at)
            <div class="flex items-center gap-2 mt-4 text-sm">
                <span class="bg-white/20 rounded px-2 py-1">Ends in</span>
                <div class="flex items-center gap-1 font-bold" id="fs-timer">
                    <span class="bg-black/30 rounded px-2 py-1" id="fh">00</span>:
                    <span class="bg-black/30 rounded px-2 py-1" id="fm">00</span>:
                    <span class="bg-black/30 rounded px-2 py-1" id="fs">00</span>
                </div>
            </div>
        @endif
    </div>

    @if ($products->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-12 text-center">
            <div class="text-5xl mb-4">⚡</div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">This flash sale has no items yet</h3>
        </div>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach ($products as $product)
                @include('marketplace.partials.product-card', ['product' => $product])
            @endforeach
        </div>
    @endif

    @push('scripts')
    @if ($flashSale->ends_at)
    <script>
        const endTime = new Date("{{ $flashSale->ends_at->toIso8601String() }}").getTime();
        const timer = setInterval(() => {
            const dist = endTime - new Date().getTime();
            if (dist <= 0) { clearInterval(timer); document.getElementById('fs-timer').innerHTML = '<span class="bg-black/30 rounded px-2 py-1">Ended</span>'; return; }
            document.getElementById('fh').textContent = String(Math.floor(dist / 3600000)).padStart(2, '0');
            document.getElementById('fm').textContent = String(Math.floor((dist % 3600000) / 60000)).padStart(2, '0');
            document.getElementById('fs').textContent = String(Math.floor((dist % 60000) / 1000)).padStart(2, '0');
        }, 1000);
    </script>
    @endif
    @endpush
@endsection

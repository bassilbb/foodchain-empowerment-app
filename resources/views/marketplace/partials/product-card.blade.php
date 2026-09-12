@php
    $image = $product->image;
    $soldOut = $product->stock_quantity <= 0;
    $rating = round($product->average_rating);
    $statePrice = mp_price($product);
    $regular = (float) $product->price;
    $comparePrice = $statePrice < $regular ? $regular : null;
    $discount = $comparePrice ? (int) round((($regular - $statePrice) / $regular) * 100) : null;
    $savings = $comparePrice ? $regular - $statePrice : null;
@endphp
<div class="group relative bg-white rounded-lg overflow-hidden border border-gray-200/80 hover:border-emerald-300 hover:shadow-md transition-all duration-200 flex flex-col">

    {{-- Image --}}
    <a href="{{ route('marketplace.product', $product->slug) }}" class="relative block bg-white" aria-label="{{ $product->name }}">
        <div class="relative w-full aspect-square bg-white overflow-hidden">
            @if ($image)
                <img src="{{ $image }}" alt="{{ $product->name }}" loading="lazy" onerror="this.onerror=null;this.src='/images/product-placeholder.svg';"
                     class="w-full h-full object-contain p-2 transition-transform duration-300 group-hover:scale-105">
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-emerald-50 to-emerald-100">
                    <span class="text-emerald-300 text-5xl font-bold">{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                </div>
            @endif

            @if ($discount)
                <span class="absolute top-2 left-2 bg-red-500 text-white text-[11px] font-bold px-1.5 py-0.5 rounded leading-none shadow-sm">-{{ $discount }}%</span>
            @endif

            @if ($soldOut)
                <div class="absolute inset-0 bg-white/70 flex items-center justify-center">
                    <span class="bg-gray-700 text-white text-xs font-semibold px-3 py-1 rounded">Out of Stock</span>
                </div>
            @elseif ($product->isLowStock())
                <span class="absolute bottom-2 left-2 bg-amber-100 text-amber-700 border border-amber-300 text-[10px] font-semibold px-1.5 py-0.5 rounded leading-none">Low Stock</span>
            @endif
        </div>
    </a>

    {{-- Info --}}
    <div class="px-2.5 pt-2 flex-1 flex flex-col">
        @if ($product->rating_count > 0)
            <div class="flex items-center gap-1 mb-1">
                <span class="inline-flex items-center gap-0.5 mr-1">
                    @for ($i = 1; $i <= 5; $i++)
                        <svg class="w-3 h-3 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    @endfor
                </span>
                <span class="text-[11px] text-gray-400">({{ $product->rating_count }})</span>
            </div>
        @endif

        @if ($product->brand)
            <p class="text-[10px] text-gray-400 uppercase tracking-wide mb-0.5 leading-none">{{ $product->brand->name }}</p>
        @endif

        <a href="{{ route('marketplace.product', $product->slug) }}" class="block text-[13px] text-gray-700 leading-snug line-clamp-2 hover:text-emerald-700 flex-1">
            {{ $product->name }}
        </a>

        <div class="mt-1.5">
            <div class="flex items-baseline gap-1.5 flex-wrap">
                <span class="text-orange-500 font-bold text-[15px] leading-tight">₦{{ number_format($statePrice, 0) }}</span>
                @if ($comparePrice)
                    <span class="text-[11px] text-gray-400 line-through">₦{{ number_format($comparePrice, 0) }}</span>
                @endif
            </div>
            @if ($savings && $savings > 0)
                <p class="text-[11px] text-emerald-600 font-medium mt-0.5">You save ₦{{ number_format($savings, 0) }}</p>
            @endif
            @if ($product->vendor && $product->vendor->business_name)
                <p class="text-[10px] text-gray-400 mt-0.5 truncate">Sold by {{ $product->vendor->business_name }}</p>
            @endif
        </div>
    </div>

    {{-- Action --}}
    <div class="p-2.5 pt-2">
        @auth
            @if ($soldOut)
                <div class="w-full text-center bg-gray-100 text-gray-400 rounded-md py-2 text-xs font-semibold cursor-not-allowed">Out of Stock</div>
            @else
                <form action="{{ route('marketplace.cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-md py-2 text-xs font-bold uppercase tracking-wide transition-colors">
                        Add to Cart
                    </button>
                </form>
            @endif
        @else
            <a href="{{ route('login') }}" class="block w-full text-center border border-emerald-600 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-md py-2 text-xs font-bold uppercase tracking-wide transition-colors">Login to Buy</a>
        @endauth
    </div>
</div>

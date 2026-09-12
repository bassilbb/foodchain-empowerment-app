@extends('marketplace.layouts.app')

@section('content')
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4 flex-wrap">
        <a href="{{ route('marketplace.home') }}" class="hover:text-emerald-700">Home</a>
        <span>/</span>
        <a href="{{ route('marketplace.category', $product->category->slug) }}" class="hover:text-emerald-700">{{ $product->category->name }}</a>
        @if ($product->brand)
            <span>/</span>
            <a href="{{ route('marketplace.brand', $product->brand) }}" class="hover:text-emerald-700">{{ $product->brand->name }}</a>
        @endif
        <span>/</span>
        <span class="text-gray-800 font-medium line-clamp-1">{{ $product->name }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-10">
        {{-- Image gallery --}}
        <div>
            @php
                $galleryImages = $product->real_images->isNotEmpty() ? $product->real_images : $product->images;
            @endphp
            @if ($galleryImages->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm p-3 mb-3">
                    <div class="aspect-square bg-gray-50 rounded-xl overflow-hidden" id="main-image-wrap">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" id="main-image" onerror="this.onerror=null;this.src='/images/product-placeholder.svg';" class="w-full h-full object-cover">
                    </div>
                </div>
                @if ($galleryImages->count() > 1)
                    <div class="flex gap-2 overflow-x-auto">
                        @foreach ($galleryImages as $image)
                            <button type="button" onclick="setMainImage('{{ $image->url }}', this)" class="w-20 h-20 rounded-lg overflow-hidden border-2 {{ $product->primary_image && $product->primary_image->id === $image->id ? 'border-emerald-600' : 'border-transparent' }} hover:border-emerald-400 shrink-0 bg-white">
                                <img src="{{ $image->url }}" class="w-full h-full object-cover" alt="" onerror="this.onerror=null;this.src='/images/product-placeholder.svg';">
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="aspect-square rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center">
                    <span class="text-emerald-300 text-8xl font-extrabold">{{ strtoupper(substr($product->name, 0, 1)) }}</span>
                </div>
            @endif

            @if ($product->video_url)
                <div class="mt-3">
                    <div class="bg-white rounded-xl shadow-sm p-4">
                        <h4 class="text-sm font-bold text-gray-800 mb-2">📹 Product Video</h4>
                        <div class="aspect-video rounded-lg overflow-hidden bg-black">
                            @php
                                $video = $product->video_url;
                                $youtube = false;
                                if (preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/)|youtu\.be\/)([\w-]{11})/', $video, $m)) { $youtube = true; $video = 'https://www.youtube.com/embed/'.$m[1]; }
                            @endphp
                            @if ($youtube)
                                <iframe src="{{ $video }}" class="w-full h-full" frameborder="0" allowfullscreen></iframe>
                            @else
                                <video src="{{ $video }}" controls class="w-full h-full"></video>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Details --}}
        <div>
            <div class="bg-white rounded-2xl shadow-sm p-6">
                @if ($product->brand)
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ $product->brand->name }}</p>
                @endif
                <h1 class="text-2xl font-extrabold text-gray-900 leading-snug">{{ $product->name }}</h1>

                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    <div class="flex items-center gap-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= round($product->average_rating) ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                        <span class="text-sm text-gray-500 ml-1">{{ $product->average_rating > 0 ? number_format($product->average_rating, 1) : 'New' }}</span>
                        @if ($product->rating_count > 0)<span class="text-xs text-gray-400">({{ $product->rating_count }} reviews)</span>@endif
                    </div>
                    @if ($product->sold_count > 0)
                        <span class="text-xs text-gray-400">{{ $product->sold_count }}+ sold</span>
                    @endif
                </div>

                <div class="mt-4 flex items-end gap-3">
                    @php
                        $statePrice = mp_price($product);
                        $comparePrice = mp_compare($product);
                        $discPct = $comparePrice ? (int) round((((float) $product->price - $statePrice) / (float) $product->price) * 100) : null;
                    @endphp
                    <div>
                        <p class="text-[11px] text-gray-400 font-medium mb-0.5">Price in {{ mp_state() }}</p>
                        <p class="text-3xl font-extrabold text-emerald-700 leading-none">₦{{ number_format($statePrice, 2) }}</p>
                    </div>
                    @if ($comparePrice)
                        <div>
                            <p class="text-lg text-gray-400 line-through">₦{{ number_format($product->price, 2) }}</p>
                            @if ($discPct)
                                <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded">Save {{ $discPct }}%</span>
                            @endif
                        </div>
                    @endif
                </div>

                @if ($product->member_selling_price)
                    <div class="mt-2 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2 text-xs text-emerald-800">
                        <span>FoodChain members (optional) pay <strong>₦{{ number_format($product->member_selling_price, 2) }}</strong> on this item. Buying here is free for every registered customer.</span>
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-3 mt-4 text-sm">
                    @if ($product->unit)<div class="bg-gray-50 rounded-lg px-3 py-2"><span class="text-gray-500 text-xs">Unit</span><p class="font-semibold">{{ $product->unit }}</p></div>@endif
                    @if ($product->weight)<div class="bg-gray-50 rounded-lg px-3 py-2"><span class="text-gray-500 text-xs">Weight</span><p class="font-semibold">{{ $product->weight }} {{ $product->weight_unit ?: 'kg' }}</p></div>@endif
                    @if ($product->sku)<div class="bg-gray-50 rounded-lg px-3 py-2"><span class="text-gray-500 text-xs">SKU</span><p class="font-semibold">{{ $product->sku }}</p></div>@endif
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <span class="text-gray-500 text-xs">Availability</span>
                        <p class="font-semibold {{ $product->stock_quantity > 0 ? 'text-emerald-600' : 'text-red-500' }}">{{ $product->stock_quantity > 0 ? $product->stock_quantity.' in stock' : 'Out of stock' }}</p>
                    </div>
                    @if ($product->expiry_date)<div class="bg-gray-50 rounded-lg px-3 py-2"><span class="text-gray-500 text-xs">Expiry</span><p class="font-semibold {{ $product->isExpiringSoon() ? 'text-orange-500' : '' }}">{{ $product->expiry_date->format('d M Y') }}</p></div>@endif
                    @if ($product->country_of_origin)<div class="bg-gray-50 rounded-lg px-3 py-2"><span class="text-gray-500 text-xs">Origin</span><p class="font-semibold">🇳🇬 {{ $product->country_of_origin }}</p></div>@endif
                </div>

                {{-- Delivery estimate --}}
                <div class="mt-4 bg-gray-50 rounded-lg p-3 text-xs text-gray-600">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-semibold">🚚 Delivery to</span>
                        <select id="delivery-state" class="text-xs rounded border-gray-300">
                            @foreach ($states as $st)
                                <option value="{{ $st }}" @selected($st === mp_state() || ($st === 'Lagos' && !in_array(mp_state(), $states, true)))>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p id="delivery-estimate">Standard delivery: <strong>₦{{ number_format($deliveryEstimate['fee'], 2) }}</strong> · {{ $deliveryEstimate['estimated_days'] }} day(s)</p>
                </div>

                {{-- Add to cart --}}
                @auth
                    <div class="mt-5 flex items-center gap-3">
                        <div class="flex items-center border-2 border-gray-200 rounded-lg">
                            <button type="button" class="qty-btn w-10 h-11 text-lg text-gray-600 hover:bg-gray-50" data-delta="-1">−</button>
                            <input type="number" id="qty-input" value="{{ $inCartQuantity ?: 1 }}" min="1" max="{{ $product->stock_quantity ?: 99 }}" class="w-14 h-11 text-center border-x border-gray-200 text-sm font-bold focus:outline-none">
                            <button type="button" class="qty-btn w-10 h-11 text-lg text-gray-600 hover:bg-gray-50" data-delta="1">+</button>
                        </div>
                        <form action="{{ route('marketplace.cart.add') }}" method="POST" class="flex-1" id="add-cart-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" id="add-qty" value="{{ $inCartQuantity ?: 1 }}">
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg py-3 transition-colors" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }}>
                                {{ $product->stock_quantity > 0 ? 'Add to Cart' : 'Out of Stock' }}
                            </button>
                        </form>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">
                        @if ($inCartQuantity > 0)
                            You have <strong>{{ $inCartQuantity }}</strong> in cart already.
                        @else
                            You'll earn <strong>{{ app(\App\Services\Marketplace\LoyaltyService::class)->pointsForAmount($statePrice) }} loyalty points</strong> on this purchase.
                        @endif
                    </p>
                @else
                    <a href="{{ route('login') }}" class="mt-5 block text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg py-3 transition-colors">Login to Add to Cart</a>
                @endauth

                @if ($product->vendor)
                    <div class="mt-5 border-t border-gray-100 pt-4 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">{{ strtoupper(substr($product->vendor->name, 0, 1)) }}</div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-800">{{ $product->vendor->business_name ?: $product->vendor->name }}</p>
                            <p class="text-xs text-gray-500">Ships from {{ $product->vendor->city ?: 'Nigeria' }}</p>
                        </div>
                        <a href="{{ route('marketplace.vendor', $product->vendor) }}" class="text-xs text-emerald-700 font-semibold hover:underline">Visit store →</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Description / Info tabs --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm p-6">
            <div class="flex gap-4 border-b border-gray-100 mb-4">
                <button class="tab-btn text-sm font-bold text-emerald-700 border-b-2 border-emerald-600 pb-2" data-tab="description">Description</button>
                @if ($product->ingredients)<button class="tab-btn text-sm font-semibold text-gray-500 border-b-2 border-transparent pb-2" data-tab="ingredients">Ingredients</button>@endif
                @if ($product->nutritional_info)<button class="tab-btn text-sm font-semibold text-gray-500 border-b-2 border-transparent pb-2" data-tab="nutrition">Nutritional Info</button>@endif
                @if ($product->storage_instructions)<button class="tab-btn text-sm font-semibold text-gray-500 border-b-2 border-transparent pb-2" data-tab="storage">Storage</button>@endif
            </div>

            <div class="tab-panel" id="tab-description">
                <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->description ?: 'No description available for this product.' }}</p>
            </div>
            @if ($product->ingredients)
                <div class="tab-panel hidden" id="tab-ingredients">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->ingredients }}</p>
                </div>
            @endif
            @if ($product->nutritional_info)
                <div class="tab-panel hidden" id="tab-nutrition">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->nutritional_info }}</p>
                </div>
            @endif
            @if ($product->storage_instructions)
                <div class="tab-panel hidden" id="tab-storage">
                    <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-line">{{ $product->storage_instructions }}</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-3">Why shop with FoodChain?</h3>
            <ul class="space-y-3 text-sm text-gray-600">
                <li class="flex gap-2"><span>🛡️</span><span>Verified quality products from trusted vendors</span></li>
                <li class="flex gap-2"><span>💳</span><span>Pay with wallet, card, transfer or cash on delivery</span></li>
                <li class="flex gap-2"><span>🚚</span><span>Nationwide delivery with express options</span></li>
                <li class="flex gap-2"><span>👑</span><span>Exclusive member prices and loyalty points</span></li>
                <li class="flex gap-2"><span>↩️</span><span>Easy returns on delivered orders</span></li>
            </ul>
        </div>
    </div>

    {{-- Reviews --}}
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Customer Reviews ({{ $product->rating_count }})</h3>
        </div>

        @if ($reviews->isEmpty())
            <p class="text-sm text-gray-500">No reviews yet. Be the first to review this product!</p>
        @else
            <div class="space-y-4">
                @foreach ($reviews as $review)
                    <div class="border-b border-gray-50 pb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">{{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $review->user->name ?? 'Customer' }}</p>
                                <div class="flex items-center gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-3 h-3 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    @endfor
                                    <span class="text-[10px] text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        @if ($review->title)<p class="text-sm font-semibold text-gray-700 mt-2">{{ $review->title }}</p>@endif
                        @if ($review->comment)<p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ $review->comment }}</p>@endif
                    </div>
                @endforeach
            </div>
            <div class="mt-4">{{ $reviews->links() }}</div>
        @endif
    </div>

    {{-- Related --}}
    @if ($related->isNotEmpty())
        <div class="mb-10">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Related Products</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($related as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    @endif

    @if ($vendorProducts->isNotEmpty())
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-900 mb-4">More from this vendor</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach ($vendorProducts as $product)
                    @include('marketplace.partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        // Gallery
        function setMainImage(src, btn) {
            document.getElementById('main-image').src = src;
            document.querySelectorAll('#main-image-wrap + div button, [onclick^="setMainImage"]').forEach(b => b.classList.remove('border-emerald-600'));
            btn.classList.add('border-emerald-600');
        }
        // Quantity
        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById('qty-input');
                let v = parseInt(input.value) + parseInt(btn.dataset.delta);
                v = Math.max(1, Math.min(parseInt(input.max), v));
                input.value = v;
                document.getElementById('add-qty').value = v;
            });
        });
        // Tabs
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('text-emerald-700', 'border-emerald-600'); b.classList.add('text-gray-500'); });
                btn.classList.add('text-emerald-700', 'border-emerald-600'); btn.classList.remove('text-gray-500');
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
                document.getElementById('tab-' + btn.dataset.tab).classList.remove('hidden');
            });
        });
        // Delivery estimate
        document.getElementById('delivery-state').addEventListener('change', async (e) => {
            const state = e.target.value;
            document.getElementById('delivery-estimate').textContent = 'Calculating...';
            try {
                const res = await fetch('{{ route("marketplace.checkout.estimate") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ state, delivery_type: 'home_delivery' })
                });
                const data = await res.json();
                if (data.delivery_fee !== undefined) {
                    document.getElementById('delivery-estimate').innerHTML = `Standard delivery: <strong>₦${Number(data.delivery_fee).toLocaleString()}</strong> · ${data.estimated_days} day(s)`;
                }
            } catch (err) {}
        });
    </script>
    @endpush
@endsection

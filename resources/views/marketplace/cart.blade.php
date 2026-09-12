@extends('marketplace.layouts.app')

@section('content')
    <h1 class="text-2xl font-extrabold text-gray-900 mb-6">Shopping Cart</h1>

    @if ($cartItems->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">🛒</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Your cart is empty</h3>
            <p class="text-sm text-gray-500 mb-6">Add some delicious food items to get started.</p>
            <a href="{{ route('marketplace.shop') }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg px-8 py-3 transition-colors">Start Shopping</a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-3">
                @foreach ($cartItems as $item)
                    <div class="bg-white rounded-xl shadow-sm p-4 flex gap-4" data-cart-row="{{ $item->product_id }}">
                        <a href="{{ route('marketplace.product', $item->product->slug) }}" class="w-24 h-24 rounded-lg overflow-hidden bg-gray-50 shrink-0">
                            @if ($item->product->image)
                                <img src="{{ $item->product->image }}" alt="" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='/images/product-placeholder.svg';">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-emerald-300 font-bold text-2xl">{{ strtoupper(substr($item->product->name, 0, 1)) }}</div>
                            @endif
                        </a>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <a href="{{ route('marketplace.product', $item->product->slug) }}" class="text-sm font-semibold text-gray-800 hover:text-emerald-700 line-clamp-2">{{ $item->product->name }}</a>
                                <button type="button" class="remove-btn text-gray-400 hover:text-red-500 text-sm" data-product-id="{{ $item->product_id }}">✕</button>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">₦{{ number_format(mp_price($item->product), 2) }} each</p>
                            <div class="flex items-center justify-between mt-3">
                                <div class="flex items-center border border-gray-200 rounded-lg">
                                    <button type="button" class="qty-btn w-8 h-8 text-gray-600 hover:bg-gray-50" data-product-id="{{ $item->product_id }}" data-delta="-1">−</button>
                                    <span class="qty-display w-10 text-center text-sm font-bold" data-product-id="{{ $item->product_id }}">{{ $item->quantity }}</span>
                                    <button type="button" class="qty-btn w-8 h-8 text-gray-600 hover:bg-gray-50" data-product-id="{{ $item->product_id }}" data-delta="1">+</button>
                                </div>
                                <p class="text-emerald-700 font-bold text-sm" data-line-total="{{ $item->product_id }}">₦{{ number_format(mp_price($item->product) * $item->quantity, 2) }}</p>
                            </div>
                            <form action="{{ route('marketplace.cart.save-for-later') }}" method="POST" class="mt-2">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                <button type="submit" class="text-xs text-emerald-700 hover:underline">Save for later</button>
                            </form>
                        </div>
                    </div>
                @endforeach

                {{-- Saved for later --}}
                @if ($savedItems->isNotEmpty())
                    <h3 class="font-bold text-gray-900 pt-4">Saved for Later ({{ $savedItems->count() }})</h3>
                    @foreach ($savedItems as $item)
                        <div class="bg-white rounded-xl shadow-sm p-4 flex gap-4 opacity-75">
                            <a href="{{ route('marketplace.product', $item->product->slug) }}" class="w-20 h-20 rounded-lg overflow-hidden bg-gray-50 shrink-0">
                                @if ($item->product->image)
                                    <img src="{{ $item->product->image }}" alt="" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='/images/product-placeholder.svg';">
                                @endif
                            </a>
                            <div class="flex-1">
                                <a href="{{ route('marketplace.product', $item->product->slug) }}" class="text-sm font-semibold text-gray-800 line-clamp-2">{{ $item->product->name }}</a>
                                <p class="text-xs text-gray-500 mt-1">₦{{ number_format(mp_price($item->product), 2) }}</p>
                                <form action="{{ route('marketplace.cart.move-to-cart') }}" method="POST" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                    <button type="submit" class="text-xs text-emerald-700 hover:underline">Move to cart</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Summary --}}
            <div>
                <div class="bg-white rounded-xl shadow-sm p-6 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-4">Order Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600"><span>Subtotal <span class="text-[10px] text-emerald-600 font-semibold uppercase tracking-wide">· {{ mp_state() }}</span></span><span id="subtotal-display">₦{{ number_format($subtotal, 2) }}</span></div>
                        <div class="flex justify-between text-gray-600"><span>Delivery</span><span>Calculated at checkout</span></div>
                        <div class="flex justify-between text-gray-600"><span>Discounts</span><span>Applied at checkout</span></div>
                    </div>
                    <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total</span>
                        <span class="font-extrabold text-emerald-700" id="grand-total-display">₦{{ number_format($subtotal, 2) }}</span>
                    </div>
                    <a href="{{ route('marketplace.checkout.index') }}" class="block mt-4 text-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg py-3 transition-colors">Proceed to Checkout</a>
                    <a href="{{ route('marketplace.shop') }}" class="block mt-2 text-center text-sm text-emerald-700 hover:underline">Continue shopping</a>
                    <div class="mt-4 bg-emerald-50 rounded-lg p-3 text-xs text-emerald-800">
                        💰 <strong>Wallet balance:</strong>
                        @auth
                            ₦{{ number_format(auth()->user()->wallet_balance, 2) }}
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        async function postForm(url, data) {
            const fd = new FormData();
            Object.entries(data).forEach(([k, v]) => fd.append(k, v));
            fd.append('_token', '{{ csrf_token() }}');
            return fetch(url, { method: 'POST', body: fd, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } }).then(r => r.json());
        }

        document.querySelectorAll('.qty-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const pid = btn.dataset.productId;
                const current = parseInt(document.querySelector(`.qty-display[data-product-id="${pid}"]`).textContent);
                const next = Math.max(1, current + parseInt(btn.dataset.delta));
                if (next === current) return;
                const res = await postForm('{{ route("marketplace.cart.update") }}', { product_id: pid, quantity: next });
                if (res.success) {
                    document.querySelector(`.qty-display[data-product-id="${pid}"]`).textContent = next;
                    const row = document.querySelector(`[data-cart-row="${pid}"]`);
                    const unit = parseFloat(res.subtotal);
                    // recompute line total via data
                    document.querySelector(`[data-line-total="${pid}"]`).textContent = '₦' + (res.line_total).toLocaleString(undefined, { minimumFractionDigits: 2 });
                    document.getElementById('subtotal-display').textContent = '₦' + Number(res.subtotal).toLocaleString(undefined, { minimumFractionDigits: 2 });
                    document.getElementById('grand-total-display').textContent = '₦' + Number(res.subtotal).toLocaleString(undefined, { minimumFractionDigits: 2 });
                    window.updateCartCount();
                }
            });
        });

        document.querySelectorAll('.remove-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const pid = btn.dataset.productId;
                const res = await postForm('{{ route("marketplace.cart.remove") }}', { product_id: pid });
                if (res.success) {
                    document.querySelector(`[data-cart-row="${pid}"]`).remove();
                    document.getElementById('subtotal-display').textContent = '₦' + Number(res.subtotal).toLocaleString(undefined, { minimumFractionDigits: 2 });
                    document.getElementById('grand-total-display').textContent = '₦' + Number(res.subtotal).toLocaleString(undefined, { minimumFractionDigits: 2 });
                    window.updateCartCount();
                }
            });
        });
    </script>
    @endpush
@endsection

@extends('marketplace.layouts.app')

@section('content')
    <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-600">Checkout</p>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('marketplace.cart.index') }}" class="hover:text-emerald-700">Cart</a>
                <span>/</span>
                <span class="text-gray-800 font-medium">Secure payment</span>
            </div>
        </div>
        <span class="bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full px-3 py-1.5 text-xs font-semibold">Safe & encrypted</span>
    </div>

    @if (! empty($data['unavailable_items']))
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6">
            <p class="text-sm font-semibold text-amber-800">Some items can no longer be bought</p>
            <p class="text-sm text-amber-700 mt-1">
                {{ implode(', ', $data['unavailable_items']) }} {{ count($data['unavailable_items']) === 1 ? 'is' : 'are' }} sold out, delisted or
                below the quantity in your cart.
                <a href="{{ route('marketplace.cart.index') }}" class="font-semibold underline">Review your cart</a>
                before paying.
            </p>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <ul class="text-sm text-red-700 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('marketplace.checkout.store') }}" id="checkout-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                {{-- Delivery --}}
                <div class="bg-white rounded-2xl shadow-[0_18px_45px_rgba(15,23,42,0.06)] border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-4">1. Delivery Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Delivery Method *</label>
                            <select name="delivery_type" id="delivery-type" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                                @foreach ($deliveryTypes as $key => $label)
                                    <option value="{{ $key }}" @selected(old('delivery_type', 'home_delivery') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">State *</label>
                            <select name="delivery_state" id="delivery-state" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                                @php $defaultCheckoutState = in_array(mp_state(), $states, true) ? mp_state() : 'Lagos'; @endphp
                                @foreach ($states as $state)
                                    <option value="{{ $state }}" @selected((old('delivery_state') ?: $defaultCheckoutState) === $state)>{{ $state }}</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-emerald-700 mt-1">Prices above reflect {{ mp_state() }}. Delivery state can be changed below.</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">LGA</label>
                            <input type="text" name="delivery_lga" value="{{ old('delivery_lga') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Ikeja">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">City / Town</label>
                            <input type="text" name="delivery_city" value="{{ old('delivery_city') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Delivery Address</label>
                            <textarea name="delivery_address" rows="2" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="House number, street, area">{{ old('delivery_address') }}</textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Pickup Location (for pickup orders)</label>
                            <select name="pickup_location" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">Select a pickup station</option>
                                @foreach ($pickupLocations as $loc)
                                    <option value="{{ $loc->name }} ({{ $loc->address ?? '' }})" @selected(old('pickup_location') === $loc->name)>{{ $loc->name }} @if ($loc->address) - {{ $loc->address }}@endif</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Delivery Notes (optional)</label>
                            <textarea name="delivery_notes" rows="2" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('delivery_notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Payment --}}
                <div class="bg-white rounded-2xl shadow-[0_18px_45px_rgba(15,23,42,0.06)] border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900 mb-4">2. Payment Method</h3>
                    <div class="space-y-3">
                        @foreach ($paymentMethods as $key => $label)
                            <label class="flex items-center gap-3 p-3 border-2 rounded-xl cursor-pointer transition-colors payment-option {{ old('payment_method', 'wallet') === $key ? 'border-emerald-600 bg-emerald-50' : 'border-gray-200 hover:border-emerald-300' }}" data-method="{{ $key }}">
                                <input type="radio" name="payment_method" value="{{ $key }}" @checked(old('payment_method', 'wallet') === $key) class="text-emerald-600 focus:ring-emerald-500" onchange="onPaymentChange(this.value)">
                                <span class="text-sm font-semibold text-gray-800">{{ $label }}</span>
                                @if ($key === 'wallet')
                                    <span class="ml-auto text-xs text-emerald-700 font-semibold">Balance: ₦{{ number_format($data['wallet_balance'], 2) }}</span>
                                @endif
                            </label>
                        @endforeach
                    </div>

                    <div id="wallet-amount-wrap" class="hidden mt-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Amount to pay from wallet (₦)</label>
                        <input type="number" name="wallet_amount" id="wallet-amount" min="0" max="{{ $data['wallet_balance'] }}" value="{{ old('wallet_amount', min($data['total'], $data['wallet_balance'])) }}" step="0.01" class="w-full sm:w-64 text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        <p class="text-xs text-gray-500 mt-1">Remaining balance is payable via card.</p>
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            <div>
                <div class="bg-white rounded-2xl shadow-[0_18px_45px_rgba(15,23,42,0.06)] border border-gray-100 p-6 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-4">Order Summary</h3>

                    <div class="space-y-2 max-h-64 overflow-y-auto mb-4">
                        @foreach ($data['cart_items'] as $item)
                            <div class="flex items-center gap-2 text-sm">
                                <span class="w-6 h-6 rounded bg-emerald-100 text-emerald-700 text-xs font-bold flex items-center justify-center">{{ $item->quantity }}</span>
                                <span class="text-gray-700 flex-1 line-clamp-1">{{ $item->product->name }}</span>
                                <span class="text-gray-500 font-medium">₦{{ number_format(mp_price($item->product) * $item->quantity, 2) }}</span>
                            </div>
                        @endforeach
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₦{{ number_format($data['subtotal'], 2) }}</span></div>
                        @if ($data['coupon_discount'] > 0)
                            <div class="flex justify-between text-emerald-600"><span>Coupon ({{ $data['coupon_code'] }})</span><span>-₦{{ number_format($data['coupon_discount'], 2) }}</span></div>
                        @endif
                        <div class="flex justify-between text-gray-600"><span>Delivery</span><span id="checkout-delivery-fee">₦{{ number_format($data['shipping_fee'], 2) }}</span></div>
                    </div>

                    <div class="border-t border-gray-100 mt-4 pt-4 flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total</span>
                        <span class="font-extrabold text-emerald-700 text-lg" id="checkout-total">₦{{ number_format($data['total'], 2) }}</span>
                    </div>

                    <p class="text-xs text-gray-500 mt-2 flex items-center gap-1">
                        <span>⭐</span> You'll earn <strong>{{ $data['points_to_earn'] }} loyalty points</strong> on this order
                    </p>

                    <div class="mt-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Coupon Code</label>
                        <div class="flex gap-2">
                            <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" class="flex-1 text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. FC10OFF">
                            <a href="{{ route('marketplace.checkout.index') }}" class="text-xs text-emerald-700 hover:underline self-center">refresh</a>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Order Note (optional)</label>
                        <textarea name="note" rows="2" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('note') }}</textarea>
                    </div>

                    <button type="submit" id="place-order-btn" class="w-full mt-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl py-3.5 shadow-lg shadow-emerald-600/20 transition-colors">Place Order</button>
                    <p class="text-xs text-gray-400 text-center mt-2">By placing this order you agree to our terms and delivery policy.</p>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function onPaymentChange(method) {
            document.querySelectorAll('.payment-option').forEach(el => {
                el.classList.toggle('border-emerald-600', el.dataset.method === method);
                el.classList.toggle('bg-emerald-50', el.dataset.method === method);
            });
            const wrap = document.getElementById('wallet-amount-wrap');
            wrap.classList.toggle('hidden', !['wallet_cash', 'wallet_paystack'].includes(method));
        }
        onPaymentChange(document.querySelector('input[name="payment_method"]:checked').value);

        const deliveryType = document.getElementById('delivery-type');
        const deliveryState = document.getElementById('delivery-state');

        async function updateDelivery() {
            const type = deliveryType.value;
            const state = deliveryState.value;
            const res = await fetch('{{ route("marketplace.checkout.estimate") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ state, delivery_type: type })
            });
            const data = await res.json();
            if (data.delivery_fee !== undefined) {
                document.getElementById('checkout-delivery-fee').textContent = '₦' + Number(data.delivery_fee).toLocaleString(undefined, { minimumFractionDigits: 2 });
            }
        }
        deliveryType.addEventListener('change', updateDelivery);
        deliveryState.addEventListener('change', updateDelivery);
    </script>
    @endpush
@endsection

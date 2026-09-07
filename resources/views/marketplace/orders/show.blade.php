@extends('marketplace.layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('marketplace.orders.index') }}" class="hover:text-emerald-700">Orders</a>
                <span>/</span>
                <span class="text-gray-800 font-medium">#{{ $order->order_number }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-gray-900 mt-1">Order #{{ $order->order_number }}</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('marketplace.orders.invoice', $order) }}" class="bg-white hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg px-4 py-2 border transition-colors">Download Invoice</a>
            @if ($order->isCancellable())
                <button onclick="document.getElementById('cancel-modal').classList.remove('hidden')" class="bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold rounded-lg px-4 py-2 transition-colors">Cancel Order</button>
            @endif
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <ul class="text-sm text-red-700 space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Items --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-4">Order Items</h3>
                <div class="space-y-3">
                    @foreach ($order->items as $item)
                        <div class="flex gap-3 border-b border-gray-50 pb-3">
                            <a href="{{ route('marketplace.product', $item->product->slug) }}" class="w-16 h-16 rounded-lg overflow-hidden bg-gray-50 shrink-0">
                                @if ($item->product->image)
                                    <img src="{{ $item->product->image }}" alt="" class="w-full h-full object-cover">
                                @endif
                            </a>
                            <div class="flex-1">
                                <a href="{{ route('marketplace.product', $item->product->slug) }}" class="text-sm font-semibold text-gray-800 hover:text-emerald-700 line-clamp-2">{{ $item->product_name }}</a>
                                <p class="text-xs text-gray-500 mt-0.5">₦{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                                @if ($item->review_id)
                                    <span class="text-xs text-emerald-600">✓ Reviewed</span>
                                @elseif (in_array($order->status, ['delivered']))
                                    <button onclick="openReview({{ $item->id }})" class="text-xs text-emerald-700 hover:underline mt-1">Write a review</button>
                                @endif
                            </div>
                            <p class="font-bold text-gray-900 text-sm">₦{{ number_format($item->total_price, 2) }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Timeline --}}
            @if ($order->statusHistories->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Order Timeline</h3>
                    <div class="space-y-3">
                        @foreach ($order->statusHistories->sortByDesc('created_at') as $history)
                            <div class="flex gap-3">
                                <div class="flex flex-col items-center">
                                    <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ \App\Models\Marketplace\Order::STATUSES[$history->status] ?? ucfirst($history->status) }}</p>
                                    @if ($history->note)<p class="text-xs text-gray-500">{{ $history->note }}</p>@endif
                                    <p class="text-xs text-gray-400">{{ $history->created_at->format('d M Y, g:i A') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Summary --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Payment Summary</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600"><span>Subtotal</span><span>₦{{ number_format($order->subtotal, 2) }}</span></div>
                    @if ($order->coupon_discount > 0)
                        <div class="flex justify-between text-emerald-600"><span>Coupon ({{ $order->coupon_code }})</span><span>-₦{{ number_format($order->coupon_discount, 2) }}</span></div>
                    @endif
                    <div class="flex justify-between text-gray-600"><span>Delivery</span><span>₦{{ number_format($order->shipping_fee, 2) }}</span></div>
                    @if ($order->wallet_amount > 0)
                        <div class="flex justify-between text-blue-600"><span>Paid via Wallet</span><span>₦{{ number_format($order->wallet_amount, 2) }}</span></div>
                    @endif
                    @if ($order->cash_amount > 0)
                        <div class="flex justify-between text-orange-600"><span>Cash on Delivery</span><span>₦{{ number_format($order->cash_amount, 2) }}</span></div>
                    @endif
                    <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                        <span class="font-bold text-gray-900">Total</span>
                        <span class="font-extrabold text-emerald-700">₦{{ number_format($order->total, 2) }}</span>
                    </div>
                </div>
                <div class="mt-4 space-y-2 text-xs text-gray-500">
                    <p>Payment: <strong>{{ \App\Models\Marketplace\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</strong></p>
                    <p>Payment status: <span class="font-semibold {{ $order->payment_status === 'paid' ? 'text-emerald-600' : 'text-orange-500' }}">{{ ucwords(str_replace('_', ' ', $order->payment_status)) }}</span></p>
                    @if ($order->loyalty_points_earned > 0)
                        <p class="text-emerald-600">⭐ Earned {{ $order->loyalty_points_earned }} loyalty points</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-3">Delivery Details</h3>
                <div class="space-y-1 text-sm text-gray-600">
                    <p><strong class="text-gray-800">{{ ucwords(str_replace('_', ' ', $order->delivery_type)) }}</strong></p>
                    @if ($order->delivery_address)<p>{{ $order->delivery_address }}</p>@endif
                    @if ($order->delivery_city)<p>{{ $order->delivery_city }}, {{ $order->delivery_state }}</p>@endif
                    @if ($order->pickup_location)<p>Pickup: {{ $order->pickup_location }}</p>@endif
                    @if ($order->estimated_delivery_date)<p class="mt-2">Est. delivery: <strong>{{ $order->estimated_delivery_date->format('d M Y') }}</strong></p>@endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="bg-white rounded-2xl shadow-sm p-6 space-y-3">
                @if ($order->status === 'shipped')
                    <form action="{{ route('marketplace.orders.confirm-delivery', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg py-3 text-sm transition-colors">Confirm Delivery</button>
                    </form>
                @endif
                @if (in_array($order->status, ['delivered', 'cancelled', 'refunded']))
                    <form action="{{ route('marketplace.orders.reorder', $order) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-50 font-bold rounded-lg py-3 text-sm transition-colors">Reorder</button>
                    </form>
                @endif
                @if ($order->isReturnable())
                    <button onclick="document.getElementById('return-modal').classList.remove('hidden')" class="w-full bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-semibold rounded-lg py-3 text-sm transition-colors">Request Return</button>
                @endif
            </div>
        </div>
    </div>

    {{-- Cancel modal --}}
    <div id="cancel-modal" class="hidden fixed inset-0 bg-black/50 z-[1000] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full">
            <h3 class="font-bold text-gray-900 mb-3">Cancel Order?</h3>
            <form action="{{ route('marketplace.orders.cancel', $order) }}" method="POST">
                @csrf
                <textarea name="reason" rows="3" required placeholder="Reason for cancellation (optional)" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 mb-4"></textarea>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('cancel-modal').classList.add('hidden')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg py-2.5 text-sm">Keep Order</button>
                    <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg py-2.5 text-sm">Cancel Order</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Return modal --}}
    <div id="return-modal" class="hidden fixed inset-0 bg-black/50 z-[1000] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full">
            <h3 class="font-bold text-gray-900 mb-3">Request Return</h3>
            <form action="{{ route('marketplace.orders.request-return', $order) }}" method="POST">
                @csrf
                <textarea name="reason" rows="3" required placeholder="Tell us why you're returning this order (min 10 chars)" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 mb-4"></textarea>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('return-modal').classList.add('hidden')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg py-2.5 text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg py-2.5 text-sm">Submit Request</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Review modal --}}
    <div id="review-modal" class="hidden fixed inset-0 bg-black/50 z-[1000] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full">
            <h3 class="font-bold text-gray-900 mb-3">Write a Review</h3>
            <form action="" method="POST" id="review-form">
                @csrf
                <div class="mb-3">
                    <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Rating *</label>
                    <select name="rating" required class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        @for ($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}">{{ $i }} star{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </select>
                </div>
                <div class="mb-3">
                    <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Title</label>
                    <input type="text" name="title" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div class="mb-4">
                    <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Comment</label>
                    <textarea name="comment" rows="3" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('review-modal').classList.add('hidden')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg py-2.5 text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg py-2.5 text-sm">Submit Review</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openReview(itemId) {
            document.getElementById('review-form').action = '{{ route("marketplace.orders.review", [$order, ':itemId']) }}'.replace(':itemId', itemId);
            document.getElementById('review-modal').classList.remove('hidden');
        }
    </script>
    @endpush
@endsection

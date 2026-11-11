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
                                    <img src="{{ $item->product->image }}" alt="" class="w-full h-full object-cover" onerror="this.onerror=null;this.src='/images/product-placeholder.svg';">
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
                    @if (($order->service_charge ?? 0) > 0)
                        <div class="flex justify-between text-gray-600"><span>Service Charge ({{ number_format(($order->service_charge_rate ?? 0) * 100, 0) }}%)</span><span>₦{{ number_format($order->service_charge, 2) }}</span></div>
                    @endif
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

            {{-- Bank transfer payment --}}
            @if ($order->payment_method === 'bank_transfer' && $order->payment_status !== 'paid')
                @php
                    $defaultBankAccount = $bankAccounts->firstWhere('is_default', true) ?? $bankAccounts->first();
                @endphp
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Bank Transfer Payment</h3>

                    @if (session('payment_notification_reference'))
                        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                            <p class="text-sm font-bold text-emerald-900">Payment notification submitted</p>
                            <p class="text-sm text-emerald-800 mt-1">Your payment notification has been received successfully. Your order is awaiting payment verification by our administration team.</p>
                            <p class="text-xs text-emerald-900 mt-2">Reference: <span class="font-mono font-bold">{{ session('payment_notification_reference') }}</span></p>
                            <p class="text-xs font-bold text-amber-700 mt-1">Status: Pending Admin Confirmation</p>
                        </div>
                    @endif

                    @if (! $bankTransferEnabled)
                        <p class="text-sm text-amber-800">Bank transfer is currently unavailable. Please contact support.</p>
                    @elseif ($bankAccounts->isEmpty() || ! $defaultBankAccount)
                        <p class="text-sm text-gray-500">No bank account is configured for transfers. Please contact support.</p>
                    @else
                        <div class="space-y-2 text-sm text-gray-600">
                            <p><strong class="text-gray-800">Bank:</strong> {{ $defaultBankAccount->bank_name }}</p>
                            <p><strong class="text-gray-800">Account Name:</strong> {{ $defaultBankAccount->account_name }}</p>
                            <p class="flex items-center gap-2 flex-wrap">
                                <strong class="text-gray-800">Account Number:</strong>
                                <span id="order-bank-account-number" class="font-mono font-bold text-gray-900">{{ $defaultBankAccount->account_number }}</span>
                                <button type="button" id="order-copy-account-number" class="text-xs font-semibold text-emerald-700 border border-emerald-300 rounded-lg px-2.5 py-1 hover:bg-emerald-50 transition-colors">Copy Account Number</button>
                            </p>
                            <p><strong class="text-gray-800">Amount to Transfer:</strong> <span class="font-bold text-emerald-700">₦{{ number_format($order->total, 2) }}</span></p>
                            <p><strong class="text-gray-800">Order Reference:</strong> <span class="font-mono">{{ $order->order_number }}</span></p>
                            @if ($defaultBankAccount->instructions)
                                <p class="text-xs text-gray-500 mt-2">{{ $defaultBankAccount->instructions }}</p>
                            @endif
                        </div>

                        <p class="mt-3 text-xs text-gray-500">Transfer the exact amount above. Your order is only confirmed after our administration team verifies the payment.</p>

                        @if ($bankTransferPayment && $bankTransferPayment->isPending())
                            <div class="mt-4 rounded-lg bg-amber-50 border border-amber-200 p-3 text-sm text-amber-800">
                                <p class="font-semibold">Payment notification submitted</p>
                                <p class="mt-1">Your transfer notification (ref: <span class="font-mono font-bold">{{ $bankTransferPayment->reference }}</span>) is awaiting payment verification by our administration team.</p>
                                <p class="mt-1 text-xs font-bold text-amber-700">Status: Pending Admin Confirmation</p>
                            </div>
                        @else
                            @if ($latestRejectedPayment)
                                <div class="mt-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-800">
                                    <p class="font-semibold">Previous notification rejected</p>
                                    <p class="mt-1">{{ $latestRejectedPayment->rejection_reason ?: 'The transfer could not be confirmed.' }}</p>
                                    <p class="mt-1 text-xs">You can submit a new notification once you have made the transfer.</p>
                                </div>
                            @endif

                            <form action="{{ route('marketplace.orders.bank-transfer', $order) }}" method="POST" data-bank-transfer-form class="mt-4">
                                @csrf
                                <input type="hidden" name="bank_account_id" value="{{ $defaultBankAccount->id }}">
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold rounded-lg py-3 text-sm transition-colors">
                                    I Have Made Payment
                                </button>
                            </form>

                            <button type="button" onclick="document.getElementById('bank-transfer-modal').classList.remove('hidden')" class="w-full mt-2 bg-white border border-emerald-300 text-emerald-700 hover:bg-emerald-50 font-semibold rounded-lg py-2.5 text-sm transition-colors">
                                Add transfer details (optional)
                            </button>
                        @endif
                    @endif
                </div>
            @endif

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

    {{-- Bank transfer modal --}}
    @if ($order->payment_method === 'bank_transfer' && $order->payment_status !== 'paid' && $bankTransferEnabled && ! $bankAccounts->isEmpty() && ! ($bankTransferPayment && $bankTransferPayment->isPending()))
        @php $defaultBankAccount = $bankAccounts->firstWhere('is_default', true) ?? $bankAccounts->first(); @endphp
        <div id="bank-transfer-modal" class="{{ $showBankTransferModal ? '' : 'hidden' }} fixed inset-0 bg-black/50 z-[1000] flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl p-6 max-w-lg w-full">
                <h3 class="font-bold text-gray-900 mb-3">Confirm Bank Transfer</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Transfer <strong>₦{{ number_format($order->total, 2) }}</strong> to
                    <strong>{{ $defaultBankAccount->bank_name }}</strong>
                    ({{ $defaultBankAccount->account_number }}), then submit the details below.
                </p>
                <form action="{{ route('marketplace.orders.bank-transfer', $order) }}" method="POST" enctype="multipart/form-data" data-bank-transfer-form>
                    @csrf
                    @if ($bankAccounts->count() > 1)
                        <div class="mb-3">
                            <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Bank Account</label>
                            <select name="bank_account_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                @foreach ($bankAccounts as $account)
                                    <option value="{{ $account->id }}" @selected($account->id === $defaultBankAccount->id)>{{ $account->bank_name }} - {{ $account->account_number }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <input type="hidden" name="bank_account_id" value="{{ $defaultBankAccount->id }}">
                    @endif
                    <div class="mb-3">
                        <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Sender Name</label>
                        <input type="text" name="sender_name" value="{{ old('sender_name', auth()->user()->name) }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Sender Account Number (optional)</label>
                        <input type="text" name="sender_account_number" value="{{ old('sender_account_number') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Sender Bank (optional)</label>
                        <input type="text" name="sender_bank_name" value="{{ old('sender_bank_name') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div class="mb-3">
                        <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Transfer Date</label>
                        <input type="date" name="transfer_date" value="{{ old('transfer_date', now()->toDateString()) }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div class="mb-4">
                        <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Proof of Payment (optional)</label>
                        <input type="file" name="proof_image" accept="image/*" class="w-full text-sm">
                    </div>
                    <div class="flex gap-2">
                        <button type="button" onclick="document.getElementById('bank-transfer-modal').classList.add('hidden')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg py-2.5 text-sm">Cancel</button>
                        <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg py-2.5 text-sm">I have completed the payment</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Payment notification spinner overlay --}}
    <div id="payment-spinner-overlay" class="hidden fixed inset-0 z-[2000] bg-black/60 backdrop-blur-sm items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl px-8 py-7 max-w-sm w-full text-center">
            <div class="mx-auto w-14 h-14 rounded-full border-4 border-emerald-100 border-t-emerald-600 animate-spin"></div>
            <p class="mt-5 text-base font-bold text-gray-900">Submitting your payment notification…</p>
            <p class="mt-2 text-sm text-gray-500">Please wait, this can take a few seconds. Do not close or refresh this page.</p>
        </div>
    </div>

    @push('scripts')
    <script>
        function openReview(itemId) {
            document.getElementById('review-form').action = '{{ route("marketplace.orders.review", [$order, ':itemId']) }}'.replace(':itemId', itemId);
            document.getElementById('review-modal').classList.remove('hidden');
        }

        (function () {
            const copyBtn = document.getElementById('order-copy-account-number');
            const copyTarget = document.getElementById('order-bank-account-number');

            if (copyBtn && copyTarget) {
                copyBtn.addEventListener('click', async function () {
                    const number = copyTarget.textContent.trim();
                    const original = copyBtn.textContent;

                    try {
                        if (navigator.clipboard && window.isSecureContext) {
                            await navigator.clipboard.writeText(number);
                        } else {
                            const input = document.createElement('textarea');
                            input.value = number;
                            input.style.position = 'fixed';
                            input.style.opacity = '0';
                            document.body.appendChild(input);
                            input.select();
                            document.execCommand('copy');
                            document.body.removeChild(input);
                        }
                        copyBtn.textContent = 'Copied!';
                    } catch (e) {
                        copyBtn.textContent = 'Copy failed';
                    }

                    setTimeout(function () { copyBtn.textContent = original; }, 2000);
                });
            }

            const overlay = document.getElementById('payment-spinner-overlay');
            let submitting = false;

            document.querySelectorAll('[data-bank-transfer-form]').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    // Guard against double clicks / double submit.
                    if (submitting) {
                        event.preventDefault();
                        return;
                    }

                    event.preventDefault();
                    submitting = true;

                    form.querySelectorAll('button[type="submit"]').forEach(function (btn) {
                        btn.disabled = true;
                        btn.classList.add('opacity-60', 'cursor-not-allowed');
                        btn.textContent = 'Submitting…';
                    });

                    if (overlay) {
                        overlay.classList.remove('hidden');
                        overlay.classList.add('flex');
                    }

                    // Give the customer visible confirmation before the request
                    // is sent, then perform a native submit (bypasses this handler).
                    setTimeout(function () { form.submit(); }, 5000);
                });
            });
        })();
    </script>
    @endpush
@endsection

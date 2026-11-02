@extends('account.layouts.app')

@section('title', 'Order ' . $order->order_number)
@section('header', 'Order ' . $order->order_number)
@section('subheader', 'Placed on ' . $order->created_at->format('d M Y, g:i A'))

@section('content')

{{-- Back + actions bar --}}
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <a href="{{ route('account.orders.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-emerald-700">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Back to orders
    </a>

    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('account.orders.invoice', $order) }}" target="_blank" class="fc-btn-ghost text-xs">
            Print Invoice
        </a>

        @if ($order->isCancellable())
        <button type="button" data-modal-open="cancel-order-modal" class="rounded-xl bg-rose-50 px-4 py-2 text-xs font-bold text-rose-600 ring-1 ring-inset ring-rose-200 hover:bg-rose-100">
            Cancel Order
        </button>
        @endif

        @if ($order->status === 'shipped')
        <form method="POST" action="{{ route('account.orders.confirm-delivery', $order) }}">
            @csrf
            <button type="submit" class="fc-btn-primary text-xs">Confirm Delivery</button>
        </form>
        @endif

        @if ($order->status === 'delivered')
        <form method="POST" action="{{ route('account.orders.reorder', $order) }}">
            @csrf
            <button type="submit" class="fc-btn-primary text-xs">Reorder</button>
        </form>

        @if (! ($order->meta['return_requested'] ?? false))
        <button type="button" data-modal-open="return-order-modal" class="fc-btn-ghost text-xs">Request Return</button>
        @else
        <span class="rounded-xl bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-200">Return requested</span>
        @endif
        @endif

        <a href="{{ route('support.create') }}" class="fc-btn-ghost text-xs">Contact Support</a>
    </div>
</div>

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- Left: timeline + items --}}
    <div class="space-y-5 lg:col-span-2">

        {{-- Delivery timeline --}}
        <x-marketplace.card title="Order Progress" subtitle="Live status of your delivery">
            <ol class="relative space-y-5 border-l-2 border-slate-100 pl-6">
                @foreach ($timeline as $step)
                <li class="relative">
                    {{-- Node --}}
                    <span class="absolute -left-[31px] flex h-5 w-5 items-center justify-center rounded-full ring-4 ring-white
                                {{ $step['done']
                                    ? (($step['terminal'] ?? false) ? 'bg-rose-500' : 'bg-emerald-500')
                                    : 'bg-slate-200' }}">
                        @if ($step['done'] && ! ($step['terminal'] ?? false))
                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        @elseif ($step['terminal'] ?? false)
                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        @endif
                    </span>

                    <div>
                        <p class="text-sm font-bold {{ $step['done'] ? 'text-slate-800' : 'text-slate-400' }}">{{ $step['label'] }}</p>
                        @if ($step['at'])
                        <p class="text-xs text-slate-500">{{ $step['at']->format('d M Y, g:i A') }}</p>
                        @endif
                        @if ($step['note'])
                        <p class="mt-0.5 text-xs text-slate-400">{{ $step['note'] }}</p>
                        @endif
                    </div>
                </li>
                @endforeach
            </ol>
        </x-marketplace.card>

        {{-- Items --}}
        <x-marketplace.card title="Items in this order" :subtitle="$order->items->count() . ' ' . \Illuminate\Support\Str::plural('item', $order->items->count())" :flush="true">
            <div class="divide-y divide-slate-100">
                @foreach ($order->items as $item)
                @php $image = $item->product?->image; @endphp
                <div class="flex flex-col gap-3 p-5 sm:flex-row sm:items-center">
                    <div class="flex min-w-0 flex-1 items-center gap-3">
                        @if ($image)
                        <img src="{{ $image }}" alt="{{ $item->product_name }}" class="h-14 w-14 shrink-0 rounded-xl object-cover ring-1 ring-slate-100" onerror="this.style.display='none'">
                        @else
                        <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </span>
                        @endif

                        <div class="min-w-0">
                            <p class="truncate text-sm font-bold text-slate-800">{{ $item->product_name }}</p>
                            <p class="text-xs text-slate-500">
                                {{ $item->product_sku ? 'SKU: '.$item->product_sku.' · ' : '' }}{{ format_currency($item->unit_price) }} each
                            </p>
                            @if ($item->vendor)
                            <p class="text-xs text-slate-400">Sold by {{ $item->vendor->business_name ?: $item->vendor->name }}</p>
                            @endif
                            <div class="mt-1.5"><x-marketplace.status-badge :status="$item->status" /></div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-4 sm:justify-end">
                        <div class="text-sm">
                            <p class="text-xs text-slate-500">Qty {{ $item->quantity }}</p>
                            <p class="font-extrabold text-slate-800">{{ format_currency($item->total_price) }}</p>
                        </div>

                        @if ($order->status === 'delivered')
                        @if ($item->review)
                        <span class="rounded-xl bg-emerald-50 px-3 py-1.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                            Reviewed
                        </span>
                        @else
                        <button type="button"
                            data-modal-open="review-modal"
                            data-review-action="{{ route('account.orders.review', [$order, $item]) }}"
                            data-review-product="{{ $item->product_name }}"
                            class="fc-btn-ghost text-xs">
                            Write Review
                        </button>
                        @endif
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </x-marketplace.card>

        {{-- Delivery details --}}
        <x-marketplace.card title="Delivery Information">
            <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Delivery Type</dt>
                    <dd class="mt-0.5 font-semibold text-slate-700">{{ \Illuminate\Support\Str::headline($order->delivery_type) }}</dd>
                </div>

                @if ($order->delivery_address)
                <div class="sm:col-span-2">
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Address</dt>
                    <dd class="mt-0.5 font-semibold text-slate-700">
                        {{ $order->delivery_address }}
                        @if ($order->delivery_lga || $order->delivery_city || $order->delivery_state)
                        <span class="block text-xs font-normal text-slate-500">
                            {{ collect([$order->delivery_lga, $order->delivery_city, $order->delivery_state])->filter()->implode(', ') }}
                        </span>
                        @endif
                    </dd>
                </div>
                @endif

                @if ($order->pickup_location)
                <div>
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pickup Location</dt>
                    <dd class="mt-0.5 font-semibold text-slate-700">{{ $order->pickup_location }}</dd>
                </div>
                @endif

                @if ($order->estimated_delivery_date)
                <div>
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Estimated Delivery</dt>
                    <dd class="mt-0.5 font-semibold text-slate-700">{{ $order->estimated_delivery_date->format('d M Y') }}</dd>
                </div>
                @endif

                @if ($order->delivered_at)
                <div>
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Delivered On</dt>
                    <dd class="mt-0.5 font-semibold text-emerald-700">{{ $order->delivered_at->format('d M Y, g:i A') }}</dd>
                </div>
                @endif

                @if ($order->delivery_notes)
                <div class="sm:col-span-2">
                    <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Delivery Instructions</dt>
                    <dd class="mt-0.5 text-slate-600">{{ $order->delivery_notes }}</dd>
                </div>
                @endif
            </dl>
        </x-marketplace.card>
    </div>

    {{-- Right: summary + status --}}
    <div class="space-y-5">

        {{-- Current status --}}
        <x-marketplace.card title="Status">
            <div class="space-y-3">
                <div class="flex items-center justify-between gap-3">
                    <span class="text-xs font-semibold text-slate-500">Order</span>
                    <x-marketplace.status-badge :status="$order->status" type="order" />
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-xs font-semibold text-slate-500">Payment</span>
                    <x-marketplace.status-badge :status="$order->payment_status" type="payment" />
                </div>
                <div class="flex items-center justify-between gap-3">
                    <span class="text-xs font-semibold text-slate-500">Payment Method</span>
                    <span class="text-xs font-bold text-slate-700">{{ \App\Models\Marketplace\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</span>
                </div>
                @if ($order->payment_reference)
                <div class="flex items-center justify-between gap-3">
                    <span class="text-xs font-semibold text-slate-500">Reference</span>
                    <span class="truncate text-xs font-mono text-slate-600">{{ $order->payment_reference }}</span>
                </div>
                @endif
            </div>
        </x-marketplace.card>

        {{-- Financial breakdown --}}
        <x-marketplace.card title="Payment Summary">
            <dl class="space-y-2.5 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-slate-600">Subtotal</dt>
                    <dd class="font-semibold text-slate-800">{{ format_currency($order->subtotal) }}</dd>
                </div>

                @if ((float) $order->coupon_discount > 0)
                <div class="flex items-center justify-between">
                    <dt class="text-slate-600">Coupon {{ $order->coupon_code ? '('.$order->coupon_code.')' : '' }}</dt>
                    <dd class="font-semibold text-emerald-600">−{{ format_currency($order->coupon_discount) }}</dd>
                </div>
                @endif

                @if ((float) $order->discount_amount > 0)
                <div class="flex items-center justify-between">
                    <dt class="text-slate-600">Discount</dt>
                    <dd class="font-semibold text-emerald-600">−{{ format_currency($order->discount_amount) }}</dd>
                </div>
                @endif

                <div class="flex items-center justify-between">
                    <dt class="text-slate-600">Delivery Fee</dt>
                    <dd class="font-semibold text-slate-800">{{ format_currency($order->shipping_fee) }}</dd>
                </div>

                @if (($order->service_charge ?? 0) > 0)
                <div class="flex items-center justify-between">
                    <dt class="text-slate-600">Service Charge ({{ number_format(($order->service_charge_rate ?? 0) * 100, 0) }}%)</dt>
                    <dd class="font-semibold text-slate-800">{{ format_currency($order->service_charge) }}</dd>
                </div>
                @endif

                <div class="mt-1 flex items-center justify-between border-t border-slate-100 pt-3">
                    <dt class="text-sm font-extrabold text-slate-800">Grand Total</dt>
                    <dd class="text-lg font-extrabold text-emerald-700">{{ format_currency($order->total) }}</dd>
                </div>

                @if ((float) $order->wallet_amount > 0)
                <div class="flex items-center justify-between pt-1">
                    <dt class="text-xs text-slate-500">Paid from wallet</dt>
                    <dd class="text-xs font-semibold text-slate-600">{{ format_currency($order->wallet_amount) }}</dd>
                </div>
                @endif

                @if ((float) $order->cash_amount > 0)
                <div class="flex items-center justify-between">
                    <dt class="text-xs text-slate-500">Payable in cash</dt>
                    <dd class="text-xs font-semibold text-amber-600">{{ format_currency($order->cash_amount) }}</dd>
                </div>
                @endif

                @if ((float) $order->refund_amount > 0)
                <div class="flex items-center justify-between">
                    <dt class="text-xs text-slate-500">Refunded</dt>
                    <dd class="text-xs font-semibold text-slate-600">{{ format_currency($order->refund_amount) }}</dd>
                </div>
                @endif
            </dl>
        </x-marketplace.card>

        {{-- Need help --}}
        <x-marketplace.card title="Need help?">
            <p class="text-sm text-slate-600">Questions about this order? Our support team can help.</p>
            <a href="{{ route('support.create') }}" class="fc-btn-primary mt-3 w-full justify-center text-xs">Contact Support</a>
        </x-marketplace.card>
    </div>
</div>

{{-- Cancel modal --}}
@if ($order->isCancellable())
<div id="cancel-order-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-900/50 p-4" data-modal>
    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
        <form method="POST" action="{{ route('account.orders.cancel', $order) }}">
            @csrf
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-base font-bold text-slate-800">Cancel this order?</h3>
                <p class="mt-0.5 text-xs text-slate-500">This cannot be undone. Any wallet payment will be refunded.</p>
            </div>

            <div class="p-5">
                <label for="cancel-reason" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Reason (optional)</label>
                <textarea name="reason" id="cancel-reason" rows="3" maxlength="500"
                    class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="Let us know why you are cancelling…"></textarea>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
                <button type="button" data-modal-close class="fc-btn-ghost text-sm">Keep order</button>
                <button type="submit" class="rounded-xl bg-rose-600 px-4 py-2 text-sm font-bold text-white hover:bg-rose-700">Cancel order</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Return modal --}}
@if ($order->status === 'delivered' && ! ($order->meta['return_requested'] ?? false))
<div id="return-order-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-900/50 p-4" data-modal>
    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
        <form method="POST" action="{{ route('account.orders.request-return', $order) }}">
            @csrf
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-base font-bold text-slate-800">Request a return</h3>
                <p class="mt-0.5 text-xs text-slate-500">Tell us what went wrong and we will review your request.</p>
            </div>

            <div class="p-5">
                <label for="return-reason" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Reason</label>
                <textarea name="reason" id="return-reason" rows="4" required minlength="10" maxlength="2000"
                    class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500"
                    placeholder="Describe the issue with your order (at least 10 characters)…"></textarea>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
                <button type="button" data-modal-close class="fc-btn-ghost text-sm">Close</button>
                <button type="submit" class="fc-btn-primary text-sm">Submit request</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Review modal (one shared modal, action swapped per item) --}}
@if ($order->status === 'delivered')
<div id="review-modal" class="fixed inset-0 z-[80] hidden items-center justify-center bg-slate-900/50 p-4" data-modal>
    <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
        <form method="POST" action="" id="review-form">
            @csrf
            <div class="border-b border-slate-100 px-5 py-4">
                <h3 class="text-base font-bold text-slate-800">Write a review</h3>
                <p class="mt-0.5 text-xs text-slate-500" id="review-product-name"></p>
            </div>

            <div class="space-y-4 p-5">
                <div>
                    <label for="review-rating" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Rating</label>
                    <select name="rating" id="review-rating" required class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="5">5 — Excellent</option>
                        <option value="4">4 — Very good</option>
                        <option value="3">3 — Average</option>
                        <option value="2">2 — Poor</option>
                        <option value="1">1 — Very poor</option>
                    </select>
                </div>

                <div>
                    <label for="review-title" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Title (optional)</label>
                    <input type="text" name="title" id="review-title" maxlength="120"
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div>
                    <label for="review-comment" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Your review (optional)</label>
                    <textarea name="comment" id="review-comment" rows="4" maxlength="2000"
                        class="w-full rounded-xl border-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
                <button type="button" data-modal-close class="fc-btn-ghost text-sm">Close</button>
                <button type="submit" class="fc-btn-primary text-sm">Submit review</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    (function() {
        const open = id => document.getElementById(id)?.classList.replace('hidden', 'flex');
        const close = el => el.classList.replace('flex', 'hidden');

        document.querySelectorAll('[data-modal-open]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = document.getElementById(btn.dataset.modalOpen);
                if (!modal) return;

                // Swap the review form action + product name for the clicked item.
                if (btn.dataset.reviewAction) {
                    document.getElementById('review-form').setAttribute('action', btn.dataset.reviewAction);
                    document.getElementById('review-product-name').textContent = btn.dataset.reviewProduct || '';
                }

                open(btn.dataset.modalOpen);
            });
        });

        document.querySelectorAll('[data-modal]').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) close(modal);
            });
            modal.querySelectorAll('[data-modal-close]').forEach(b => b.addEventListener('click', () => close(modal)));
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') document.querySelectorAll('[data-modal]').forEach(close);
        });
    })();
</script>
@endpush
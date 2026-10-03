{{--
    Reusable status badge for all dashboards (customer / vendor / admin).

    Usage:
        <x-marketplace.status-badge :status="$order->status" />
        <x-marketplace.status-badge :status="$order->payment_status" type="payment" />
        <x-marketplace.status-badge status="low_stock" type="stock" />

    Supported types: generic (default), order, payment, delivery, stock, vendor, product, coupon
--}}
@props([
    'status' => null,
    'type' => 'generic',
    'label' => null,
])

@php
    use Illuminate\Support\Str;

    $key = Str::of((string) $status)->trim()->lower()->replace([' ', '-'], '_')->toString();

    // Map every status the marketplace can produce onto one semantic style.
    // Styles are resolved centrally so no page hard-codes its own colours.
    $map = [
        // Orders (marketplace_orders.status)
        'pending' => ['label' => 'Pending', 'class' => 'bg-amber-100 text-amber-800 ring-amber-200'],
        'paid' => ['label' => 'Paid', 'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-200'],
        'confirmed' => ['label' => 'Confirmed', 'class' => 'bg-sky-100 text-sky-800 ring-sky-200'],
        'processing' => ['label' => 'Processing', 'class' => 'bg-blue-100 text-blue-800 ring-blue-200'],
        'packed' => ['label' => 'Packed', 'class' => 'bg-indigo-100 text-indigo-800 ring-indigo-200'],
        'shipped' => ['label' => 'Shipped', 'class' => 'bg-violet-100 text-violet-800 ring-violet-200'],
        'out_for_delivery' => ['label' => 'Out for Delivery', 'class' => 'bg-cyan-100 text-cyan-800 ring-cyan-200'],
        'delivered' => ['label' => 'Delivered', 'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-200'],
        'completed' => ['label' => 'Completed', 'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-200'],
        'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-rose-100 text-rose-800 ring-rose-200'],
        'canceled' => ['label' => 'Cancelled', 'class' => 'bg-rose-100 text-rose-800 ring-rose-200'],
        'refunded' => ['label' => 'Refunded', 'class' => 'bg-slate-100 text-slate-700 ring-slate-200'],
        'returned' => ['label' => 'Returned', 'class' => 'bg-orange-100 text-orange-800 ring-orange-200'],

        // Payments (marketplace_orders.payment_status)
        'unpaid' => ['label' => 'Unpaid', 'class' => 'bg-amber-100 text-amber-800 ring-amber-200'],
        'partially_paid' => ['label' => 'Partially Paid', 'class' => 'bg-amber-100 text-amber-800 ring-amber-200'],
        'failed' => ['label' => 'Failed', 'class' => 'bg-rose-100 text-rose-800 ring-rose-200'],
        'successful' => ['label' => 'Successful', 'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-200'],
        'verified' => ['label' => 'Verified', 'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-200'],

        // Vendor / product / review moderation
        'approved' => ['label' => 'Approved', 'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-200'],
        'rejected' => ['label' => 'Rejected', 'class' => 'bg-rose-100 text-rose-800 ring-rose-200'],
        'suspended' => ['label' => 'Suspended', 'class' => 'bg-rose-100 text-rose-800 ring-rose-200'],
        'active' => ['label' => 'Active', 'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-200'],
        'inactive' => ['label' => 'Inactive', 'class' => 'bg-slate-100 text-slate-700 ring-slate-200'],
        'draft' => ['label' => 'Draft', 'class' => 'bg-slate-100 text-slate-700 ring-slate-200'],

        // Stock
        'in_stock' => ['label' => 'In Stock', 'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-200'],
        'low_stock' => ['label' => 'Low Stock', 'class' => 'bg-amber-100 text-amber-800 ring-amber-200'],
        'out_of_stock' => ['label' => 'Out of Stock', 'class' => 'bg-rose-100 text-rose-800 ring-rose-200'],

        // Coupons / payouts / generic lifecycle
        'expired' => ['label' => 'Expired', 'class' => 'bg-slate-100 text-slate-700 ring-slate-200'],
        'requested' => ['label' => 'Requested', 'class' => 'bg-amber-100 text-amber-800 ring-amber-200'],
        'on_hold' => ['label' => 'On Hold', 'class' => 'bg-amber-100 text-amber-800 ring-amber-200'],
        'paid_out' => ['label' => 'Paid Out', 'class' => 'bg-emerald-100 text-emerald-800 ring-emerald-200'],
        'open' => ['label' => 'Open', 'class' => 'bg-sky-100 text-sky-800 ring-sky-200'],
        'closed' => ['label' => 'Closed', 'class' => 'bg-slate-100 text-slate-700 ring-slate-200'],
    ];

    $resolved = $map[$key] ?? [
        'label' => $label ?: Str::headline((string) $status ?: 'Unknown'),
        'class' => 'bg-slate-100 text-slate-700 ring-slate-200',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset whitespace-nowrap '.$resolved['class']]) }}>
    {{ $label ?? $resolved['label'] }}
</span>
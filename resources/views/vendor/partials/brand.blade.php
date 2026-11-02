{{--
    Vendor identity block shared by the canonical Vendor Centre sidebar
    (vendor.partials.sidebar) and the legacy /marketplace/vendor centre so
    both surfaces show the same store name, ID and status.
--}}
@php
$brandVendor = $brandVendor ?? auth()->user()?->marketplaceVendor;
@endphp

<div class="flex items-center gap-3 border-b border-white/10 px-4 py-5">
    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/10 text-lg font-extrabold text-white ring-1 ring-inset ring-white/15">
        {{ strtoupper(substr($brandVendor?->business_name ?: ($brandVendor?->name ?? 'V'), 0, 1)) }}
    </div>
    <div class="min-w-0">
        <p class="truncate text-sm font-extrabold leading-tight text-white">
            {{ $brandVendor?->business_name ?: ($brandVendor?->name ?? 'Vendor') }}
        </p>
        <p class="mt-0.5 truncate text-[11px] font-medium text-emerald-200/80">
            @if ($brandVendor)
            Vendor ID: #{{ $brandVendor->id }} · {{ ucfirst($brandVendor->status) }}
            @else
            Vendor Centre
            @endif
        </p>
    </div>
</div>
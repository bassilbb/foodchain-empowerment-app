@extends('account.layouts.app')

@section('title', 'My Wishlist')
@section('header', 'My Wishlist')
@section('subheader', 'Products you have saved for later.')

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">
        {{ $items->total() }} saved {{ \Illuminate\Support\Str::plural('product', $items->total()) }}
    </p>
    <a href="{{ route('marketplace.shop') }}" class="fc-btn-ghost text-sm">Continue shopping</a>
</div>

@if ($items->isEmpty())
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm">
    <x-marketplace.empty-state
        icon="wishlist"
        title="No wishlist items yet"
        message="Save products you love and they will appear here."
        :action-url="route('marketplace.shop')"
        action-label="Browse products" />
</div>
@else
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
    @foreach ($items as $item)
    @php
    $product = $item->product;
    // Saved rows can outlive a deleted product.
    $vendorName = $product?->vendor?->business_name ?: $product?->vendor?->name;
    @endphp

    @if (! $product)
    <div class="flex flex-col rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-4">
        <p class="text-sm font-semibold text-slate-500">This product is no longer available.</p>
        <form method="POST" action="{{ route('account.wishlist.destroy', $item) }}" class="mt-3">
            @csrf
            @method('DELETE')
            <button type="submit" class="fc-btn-ghost w-full justify-center text-xs">Remove</button>
        </form>
    </div>
    @continue
    @endif

    @php
    // Price must come from the state-based pricing engine, never
    // from the raw product price column (Section 26).
    $price = mp_price($product);
    $compare = mp_compare($product);
    $inStock = $product->isInStock();
    $lowStock = $product->isLowStock();
    @endphp

    <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
        {{-- Image --}}
        <a href="{{ route('marketplace.product', $product->slug) }}" class="relative block aspect-square overflow-hidden bg-slate-50">
            @if ($product->image)
            <img src="{{ $product->image }}" alt="{{ $product->name }}"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                onerror="this.onerror=null;this.src='/images/product-placeholder.svg';">
            @else
            <span class="flex h-full w-full items-center justify-center text-slate-300">
                <svg class="h-12 w-12" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </span>
            @endif

            @unless ($inStock)
            <span class="absolute inset-0 flex items-center justify-center bg-slate-900/55 text-xs font-bold uppercase tracking-wide text-white">Out of Stock</span>
            @endunless

            @if ($product->discount_percent)
            <span class="absolute left-2 top-2 rounded-full bg-rose-500 px-2 py-0.5 text-[10px] font-bold text-white">-{{ $product->discount_percent }}%</span>
            @endif
        </a>

        {{-- Body --}}
        <div class="flex flex-1 flex-col p-4">
            @if ($vendorName)
            <p class="mb-1 truncate text-[11px] font-semibold uppercase tracking-wide text-emerald-600">{{ $vendorName }}</p>
            @endif

            <a href="{{ route('marketplace.product', $product->slug) }}"
                class="line-clamp-2 text-sm font-semibold leading-snug text-slate-800 hover:text-emerald-700">{{ $product->name }}</a>

            <div class="mt-2 flex flex-wrap items-baseline gap-2">
                <span class="text-lg font-extrabold text-slate-800">{{ format_currency($price) }}</span>
                @if ($compare)
                <span class="text-xs text-slate-400 line-through">{{ format_currency($compare) }}</span>
                @endif
            </div>

            <div class="mt-2">
                @if (! $inStock)
                <x-marketplace.status-badge status="out_of_stock" type="stock" />
                @elseif ($lowStock)
                <x-marketplace.status-badge status="low_stock" type="stock" />
                @else
                <x-marketplace.status-badge status="in_stock" type="stock" />
                @endif
            </div>

            {{-- Actions --}}
            <div class="mt-4 space-y-2 border-t border-slate-100 pt-3">
                @if ($inStock)
                <form method="POST" action="{{ route('account.wishlist.move-to-cart', $item) }}">
                    @csrf
                    <button type="submit" class="fc-btn-primary w-full justify-center text-xs">Add to Cart</button>
                </form>
                @else
                <button type="button" disabled
                    class="w-full cursor-not-allowed rounded-xl bg-slate-100 px-4 py-2 text-xs font-bold text-slate-400">
                    Out of Stock
                </button>
                @endif

                <form method="POST" action="{{ route('account.wishlist.destroy', $item) }}"
                    data-confirm="Remove this product from your wishlist?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-xl px-4 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-50">
                        Remove from wishlist
                    </button>
                </form>
            </div>
        </div>
    </article>
    @endforeach
</div>

<div class="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
    <x-marketplace.pagination :paginator="$items" />
</div>
@endif

@endsection
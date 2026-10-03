@extends('vendor.layouts.app')

@section('title', $product ? 'Edit Product' : 'Add Product')
@section('header', $product ? 'Edit Product' : 'Add New Product')
@section('subheader', $product ? $product->name : 'List a new product on the marketplace')

@section('content')

@if ($errors->any())
<div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4">
    <p class="mb-1 text-sm font-bold text-rose-800">Please correct the following:</p>
    <ul class="list-inside list-disc space-y-0.5 text-sm text-rose-700">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@php
$v = fn (string $field, $fallback = null) => old($field, $product?->{$field} ?? $fallback);
@endphp

<form method="POST"
    action="{{ $product ? route('vendor.products.update', $product) : route('vendor.products.store') }}"
    enctype="multipart/form-data"
    class="grid grid-cols-1 gap-5 lg:grid-cols-3">
    @csrf
    @if ($product) @method('PUT') @endif

    <div class="space-y-5 lg:col-span-2">

        {{-- Basic information --}}
        <x-marketplace.card title="Basic Information">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Product Name <span class="text-rose-500">*</span></label>
                    <input type="text" id="name" name="name" required value="{{ $v('name') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div>
                    <label for="category_id" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Category <span class="text-rose-500">*</span></label>
                    <select id="category_id" name="category_id" required
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                        <option value="">Select category...</option>
                        @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" @selected((string) $v('category_id')===(string) $cat->id)>{{ $cat->name }}</option>
                        @foreach ($cat->children as $child)
                        <option value="{{ $child->id }}" @selected((string) $v('category_id')===(string) $child->id)>&nbsp;&nbsp;— {{ $child->name }}</option>
                        @endforeach
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="brand_id" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Brand</label>
                    <select id="brand_id" name="brand_id"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                        <option value="">None</option>
                        @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" @selected((string) $v('brand_id')===(string) $brand->id)>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="unit" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Unit</label>
                    <input type="text" id="unit" name="unit" value="{{ $v('unit') }}" placeholder="bag, pack, kg, litre"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div>
                    <label class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Weight</label>
                    <div class="flex gap-2">
                        <input type="number" name="weight" step="0.01" min="0" value="{{ $v('weight') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                        <select name="weight_unit" class="w-24 rounded-xl border border-slate-200 bg-white px-2 py-2.5 text-sm">
                            @foreach (['kg', 'g', 'L', 'ml'] as $unitOption)
                            <option value="{{ $unitOption }}" @selected($v('weight_unit')===$unitOption)>{{ $unitOption }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="sm:col-span-2">
                    <label for="description" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Description</label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">{{ $v('description') }}</textarea>
                </div>
            </div>
        </x-marketplace.card>

        {{-- Pricing and stock --}}
        <x-marketplace.card title="Pricing & Stock" subtitle="Your commission rate is applied to delivered sales.">
            @php $rate = (float) $vendor->commission_rate; @endphp

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="price" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Regular Price (₦) <span class="text-rose-500">*</span></label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required value="{{ $v('price') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <p class="mt-1 text-[11px] text-slate-400">Default price before any state-based pricing.</p>
                </div>

                <div>
                    <label for="discount_price" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Discount Price (₦)</label>
                    <input type="number" id="discount_price" name="discount_price" step="0.01" min="0" value="{{ $v('discount_price') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <p class="mt-1 text-[11px] text-slate-400">Must be lower than the regular price.</p>
                </div>

                <div>
                    <label for="member_price" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Member Price (₦)</label>
                    <input type="number" id="member_price" name="member_price" step="0.01" min="0" value="{{ $v('member_price') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div>
                    <label for="stock_quantity" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Stock Quantity <span class="text-rose-500">*</span></label>
                    <input type="number" id="stock_quantity" name="stock_quantity" min="0" required value="{{ $v('stock_quantity', 0) }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div>
                    <label for="low_stock_threshold" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Low Stock Alert At</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold" min="0" value="{{ $v('low_stock_threshold', 5) }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>

                <div>
                    <label for="barcode" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Barcode</label>
                    <input type="text" id="barcode" name="barcode" value="{{ $v('barcode') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
            </div>

            {{-- Earnings preview so the vendor sees take-home, not just list price --}}
            <div class="mt-4 rounded-xl border border-emerald-100 bg-emerald-50/60 p-4" id="earnings-preview">
                <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">What you earn per unit</p>
                <div class="mt-2 grid grid-cols-3 gap-3 text-sm">
                    <div>
                        <p class="text-[11px] text-emerald-600">Selling price</p>
                        <p class="font-bold text-slate-800" id="preview-price">₦0.00</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-emerald-600">Commission ({{ $rate }}%)</p>
                        <p class="font-bold text-rose-600" id="preview-commission">−₦0.00</p>
                    </div>
                    <div>
                        <p class="text-[11px] text-emerald-600">You receive</p>
                        <p class="font-extrabold text-emerald-700" id="preview-net">₦0.00</p>
                    </div>
                </div>
                <p class="mt-2 text-[11px] text-emerald-600/80">Earnings are recognised when an order is delivered.</p>
            </div>
        </x-marketplace.card>

        {{-- State-based pricing --}}
        @if ($states->isNotEmpty())
        <x-marketplace.card title="Prices by State (₦)"
            subtitle="The price buyers pay in each state. Leave blank to fall back to the regular price.">

            @unless ($product)
            <p class="mb-3 rounded-xl border border-amber-100 bg-amber-50 p-3 text-[11px] text-amber-700">
                Save the product first — state prices are stored against a product that already exists.
            </p>
            @endunless

            <div class="mb-3 flex flex-wrap items-center gap-2">
                <button type="button" id="fill-state-prices" class="fc-btn-ghost text-xs">
                    Set all to the regular price
                </button>
                <button type="button" id="clear-state-prices" class="fc-btn-ghost text-xs">
                    Clear all
                </button>
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                @foreach ($states as $state)
                <div>
                    <label for="state-price-{{ $state->id }}" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">
                        {{ $state->name }}
                    </label>
                    <input type="number" id="state-price-{{ $state->id }}" name="prices[{{ $state->id }}]"
                        step="0.01" min="0" data-state-price
                        value="{{ old('prices.' . $state->id, isset($statePrices[$state->id]) ? number_format((float) $statePrices[$state->id], 2, '.', '') : '') }}"
                        placeholder="{{ $v('price') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
                @endforeach
            </div>
        </x-marketplace.card>
        @endif

        {{-- Extended details --}}
        <x-marketplace.card title="Product Details">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="ingredients" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Ingredients</label>
                    <textarea id="ingredients" name="ingredients" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">{{ $v('ingredients') }}</textarea>
                </div>
                <div>
                    <label for="nutritional_info" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nutritional Info</label>
                    <textarea id="nutritional_info" name="nutritional_info" rows="3" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">{{ $v('nutritional_info') }}</textarea>
                </div>
                <div>
                    <label for="storage_instructions" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Storage Instructions</label>
                    <textarea id="storage_instructions" name="storage_instructions" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">{{ $v('storage_instructions') }}</textarea>
                </div>
                <div>
                    <label for="video_url" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Video URL</label>
                    <input type="url" id="video_url" name="video_url" value="{{ $v('video_url') }}" placeholder="https://..."
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label for="manufacturing_date" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Manufacturing Date</label>
                    <input type="date" id="manufacturing_date" name="manufacturing_date" value="{{ $v('manufacturing_date') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label for="expiry_date" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Expiry Date</label>
                    <input type="date" id="expiry_date" name="expiry_date" value="{{ $v('expiry_date') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label for="country_of_origin" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Country of Origin</label>
                    <input type="text" id="country_of_origin" name="country_of_origin" value="{{ $v('country_of_origin', 'Nigeria') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label for="batch_number" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Batch Number</label>
                    <input type="text" id="batch_number" name="batch_number" value="{{ $v('batch_number') }}"
                        class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                </div>
            </div>
        </x-marketplace.card>
    </div>

    {{-- Sidebar column --}}
    <div class="space-y-5">

        <x-marketplace.card title="Product Images">
            @if ($product && $product->images->isNotEmpty())
            <div class="mb-3 flex flex-wrap gap-2">
                @foreach ($product->images as $img)
                <div class="relative">
                    <img src="{{ $img->url }}" alt=""
                        class="h-20 w-20 rounded-lg border object-cover {{ $img->is_primary ? 'border-emerald-500' : 'border-slate-200' }}"
                        onerror="this.onerror=null;this.src='/images/product-placeholder.svg';">
                    @if ($img->is_primary)
                    <span class="absolute -right-1 -top-1 rounded bg-emerald-500 px-1 text-[8px] font-bold text-white">PRIMARY</span>
                    @endif

                    @unless ($img->is_placeholder)
                    <button type="submit" form="delete-image-{{ $img->id }}"
                        class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white">×</button>
                    @endunless
                </div>
                @endforeach
            </div>
            @endif

            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp"
                class="w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-emerald-700">
            <p class="mt-2 text-[11px] text-slate-400">JPEG/PNG/WebP, max 2MB each, up to 6. The first image becomes primary.</p>
        </x-marketplace.card>

        <x-marketplace.card title="Visibility">
            <label class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true))
                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span>
                    <span class="block text-sm font-semibold text-slate-700">Active</span>
                    <span class="block text-[11px] text-slate-500">Visible to buyers in the storefront.</span>
                </span>
            </label>

            <label class="mt-2 flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product?->is_featured ?? false))
                       class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="block text-sm font-semibold text-slate-700">Featured</span>
                    <span class="block text-[11px] text-slate-500">Request homepage placement. An administrator must approve it.</span>
                </span>
            </label>
        </x-marketplace.card>

        <div class="space-y-2">
            <button type="submit" class="fc-btn-primary w-full justify-center text-sm">
                {{ $product ? 'Save changes' : 'Create product' }}
            </button>
            <a href="{{ route('vendor.products.index') }}" class="fc-btn-ghost w-full justify-center text-sm">Cancel</a>
        </div>

        @if ($product)
        <p class="rounded-xl border border-slate-100 bg-white p-3 text-[11px] text-slate-500">
            SKU <span class="font-mono font-semibold text-slate-700">{{ $product->sku }}</span><br>
            Created {{ $product->created_at->format('d M Y') }} · {{ $product->sold_count }} sold
        </p>
        @endif
    </div>
</form>

{{-- Image delete forms live outside the main form (nested forms are invalid HTML). --}}
@if ($product)
@foreach ($product->images as $img)
@unless ($img->is_placeholder)
<form id="delete-image-{{ $img->id }}" method="POST" action="{{ route('vendor.products.images.destroy', [$product, $img->id]) }}"
    data-confirm="Remove this image?" class="hidden">
    @csrf
    @method('DELETE')
</form>
@endunless
@endforeach
@endif

@endsection

@push('scripts')
<script>
    (function() {
        const rate = {
            {
                $rate
            }
        };
        const priceEl = document.getElementById('price');
        const discountEl = document.getElementById('discount_price');
        if (!priceEl) return;

        const fmt = n => '₦' + Number(n || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        function recalc() {
            const list = parseFloat(priceEl.value) || 0;
            const discount = parseFloat(discountEl?.value) || 0;

            // The effective selling price is the discount when one is set and lower.
            const selling = (discount > 0 && discount < list) ? discount : list;
            const commission = selling * (rate / 100);
            const net = selling - commission;

            document.getElementById('preview-price').textContent = fmt(selling);
            document.getElementById('preview-commission').textContent = '−' + fmt(commission);
            document.getElementById('preview-net').textContent = fmt(net);
        }

        priceEl.addEventListener('input', recalc);
        discountEl?.addEventListener('input', recalc);
        recalc();

        // State-price helpers. "Fill" uses the regular price field, which is the
        // documented fallback the pricing engine applies for blank states.
        document.getElementById('fill-state-prices')?.addEventListener('click', function () {
            const value = parseFloat(priceEl.value) || 0;
            document.querySelectorAll('[data-state-price]').forEach(input => {
                input.value = value.toFixed(2);
            });
        });

        document.getElementById('clear-state-prices')?.addEventListener('click', function () {
            document.querySelectorAll('[data-state-price]').forEach(input => {
                input.value = '';
            });
        });
    })();
</script>
@endpush
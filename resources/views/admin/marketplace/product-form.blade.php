@extends('admin.layouts.app')

@section('title', 'Edit '.$product->name)
@section('header', 'Edit Product')
@section('subheader', $product->name)

@section('content')
<style>
    .form-card { background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 32px; }
    .section-title { font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0; }
    .section-sub { font-size: 13px; color: #94a3b8; margin: 0 0 20px 0; }
</style>

<div style="max-width: 960px; margin: 0 auto;">
    <div class="form-card">
        <form method="POST" action="{{ route('admin.marketplace.products.update', $product) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h3 class="section-title">Basic Information</h3>
            <p class="section-sub">Core product identity and vendor assignment</p>
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px;">
                <div>
                    <label class="form-label">Product Name *</label>
                    <input type="text" name="name" required class="form-input" value="{{ old('name', $product->name) }}">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Category *</label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @foreach($category->children as $child)
                            <option value="{{ $child->id }}" {{ old('category_id', $product->category_id) == $child->id ? 'selected' : '' }}>
                                — {{ $child->name }}
                            </option>
                            @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Vendor *</label>
                        <select name="vendor_id" class="form-select" required>
                            @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ old('vendor_id', $product->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->business_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Brand</label>
                        <select name="brand_id" class="form-select">
                            <option value="">— None —</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px;">
                    <div>
                        <label class="form-label">Unit *</label>
                        <input type="text" name="unit" required class="form-input" value="{{ old('unit', $product->unit) }}" placeholder="kg, pack, bottle, bag">
                    </div>
                    <div>
                        <label class="form-label">Weight</label>
                        <input type="number" name="weight" step="0.01" min="0" class="form-input" value="{{ old('weight', $product->weight) }}">
                    </div>
                    <div>
                        <label class="form-label">Weight Unit</label>
                        <input type="text" name="weight_unit" maxlength="10" class="form-input" value="{{ old('weight_unit', $product->weight_unit) }}" placeholder="kg, g, l">
                    </div>
                </div>
            </div>

            <h3 class="section-title">Pricing</h3>
            <p class="section-sub">List price, promotional discount and member pricing</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 32px;">
                <div>
                    <label class="form-label">Price (₦) *</label>
                    <input type="number" name="price" step="0.01" min="0" required class="form-input" value="{{ old('price', $product->price) }}">
                </div>
                <div>
                    <label class="form-label">Discount Price (₦)</label>
                    <input type="number" name="discount_price" step="0.01" min="0" class="form-input" value="{{ old('discount_price', $product->discount_price) }}">
                </div>
                <div>
                    <label class="form-label">Member Price (₦)</label>
                    <input type="number" name="member_price" step="0.01" min="0" class="form-input" value="{{ old('member_price', $product->member_price) }}">
                </div>
            </div>

            <h3 class="section-title">Inventory</h3>
            <p class="section-sub">Stock levels and low-stock alert threshold</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 32px;">
                <div>
                    <label class="form-label">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" min="0" required class="form-input" value="{{ old('stock_quantity', $product->stock_quantity) }}">
                </div>
                <div>
                    <label class="form-label">Low Stock Threshold</label>
                    <input type="number" name="low_stock_threshold" min="0" class="form-input" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 10) }}">
                </div>
            </div>

            <h3 class="section-title">Shelf Life & Origin</h3>
            <p class="section-sub">Dates relevant to perishable food items</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 32px;">
                <div>
                    <label class="form-label">Manufacturing Date</label>
                    <input type="date" name="manufacturing_date" class="form-input" value="{{ old('manufacturing_date', $product->manufacturing_date?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="form-label">Expiry Date</label>
                    <input type="date" name="expiry_date" class="form-input" value="{{ old('expiry_date', $product->expiry_date?->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="form-label">Country of Origin</label>
                    <input type="text" name="country_of_origin" maxlength="60" class="form-input" value="{{ old('country_of_origin', $product->country_of_origin) }}" placeholder="Nigeria">
                </div>
            </div>

            <h3 class="section-title">Description & Food Info</h3>
            <p class="section-sub">Detailed product content shown on the storefront</p>
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px;">
                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-input">{{ old('description', $product->description) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Ingredients</label>
                    <textarea name="ingredients" rows="2" class="form-input">{{ old('ingredients', $product->ingredients) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Nutritional Info</label>
                    <textarea name="nutritional_info" rows="3" class="form-input">{{ old('nutritional_info', $product->nutritional_info) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Storage Instructions</label>
                    <textarea name="storage_instructions" rows="2" class="form-input">{{ old('storage_instructions', $product->storage_instructions) }}</textarea>
                </div>
            </div>

            <h3 class="section-title">Images & Flags</h3>
            <p class="section-sub">Gallery and visibility settings</p>
            <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 32px;">
                <div>
                    <label class="form-label">Add Images</label>
                    <input type="file" name="images[]" accept="image/*" multiple class="form-input">
                    @if($product->images->count())
                    <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 12px;">
                        @foreach($product->images as $image)
                        <div style="text-align: center;">
                            <img src="{{ $image->image_path }}" alt="" style="width: 72px; height: 72px; border-radius: 10px; object-fit: cover; border: 2px solid {{ $image->is_primary ? '#10b981' : '#e2e8f0' }};">
                            @if($image->is_primary)<div style="font-size: 11px; color: #059669; font-weight: 600;">Primary</div>@endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
                <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }}>
                        <span style="font-weight: 500; color: #475569;">Active (visible on storefront)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="checkbox" name="is_featured" value="1" {{ $product->is_featured ? 'checked' : '' }}>
                        <span style="font-weight: 500; color: #475569;">Featured</span>
                    </label>
                </div>
            </div>

            <div style="display: flex; gap: 16px; padding-top: 16px; border-top: 1px solid #f1f5f9;">
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="{{ route('admin.marketplace.products.show', $product) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <div class="form-card" style="margin-top: 20px;">
        <form method="POST" action="{{ route('admin.marketplace.products.state-prices', $product) }}">
            @csrf
            <h3 class="section-title">Prices by State (₦)</h3>
            <p class="section-sub">The price customers pay in each state. Leave blank to fall back to the national selling price.</p>
            <div style="margin-bottom: 16px;">
                <button type="button" class="btn btn-secondary" onclick="fillStatePrices({{ $product->price }})">Set all to ₦{{ number_format($product->price, 2) }}</button>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(230px, 1fr)); gap: 12px;">
                @foreach($states as $state)
                <div>
                    <label class="form-label" style="margin-bottom: 4px;">{{ $state->name }}</label>
                    <input type="number" name="prices[{{ $state->id }}]" step="0.01" min="0" class="form-input"
                           value="{{ isset($statePrices[$state->id]) ? number_format((float) $statePrices[$state->id], 2, '.', '') : '' }}"
                           placeholder="{{ $product->price }}"
                           data-state-price>
                </div>
                @endforeach
            </div>
            <div style="display: flex; gap: 16px; padding-top: 16px; margin-top: 16px; border-top: 1px solid #f1f5f9;">
                <button type="submit" class="btn btn-primary">Save State Prices</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function fillStatePrices(price) {
        document.querySelectorAll('[data-state-price]').forEach(function (input) {
            input.value = price;
        });
    }
</script>
@endpush

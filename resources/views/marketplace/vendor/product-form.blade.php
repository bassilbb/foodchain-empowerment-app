@extends('marketplace.layouts.app')

@section('content')
    @include('marketplace.vendor.partials.nav', ['vendor' => $vendor])

    <h1 class="text-xl font-extrabold text-gray-900 mb-6">{{ $product ? 'Edit Product' : 'Add New Product' }}</h1>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <ul class="text-sm text-red-700 space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ $product ? route('marketplace.vendor.products.update', $product) : route('marketplace.vendor.products.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        @csrf
        @if ($product) @method('PUT') @endif

        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Product Name *</label>
                        <input type="text" name="name" required value="{{ old('name', $product->name ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Category *</label>
                        <select name="category_id" required class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Select category...</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $product->category_id ?? '') == $cat->id)>{{ $cat->name }}</option>
                                @foreach ($cat->children as $child)
                                    <option value="{{ $child->id }}" @selected(old('category_id', $product->category_id ?? '') == $child->id)>&nbsp;&nbsp;- {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Brand</label>
                        <select name="brand_id" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">None</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected(old('brand_id', $product->brand_id ?? '') == $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Unit (e.g. bag, pack, kg) *</label>
                        <input type="text" name="unit" required value="{{ old('unit', $product->unit ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Weight</label>
                        <div class="flex gap-2">
                            <input type="number" name="weight" step="0.01" min="0" value="{{ old('weight', $product->weight ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            <select name="weight_unit" class="w-24 text-sm rounded-lg border-gray-300">
                                <option value="kg">kg</option>
                                <option value="g">g</option>
                                <option value="L">L</option>
                            </select>
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Description</label>
                        <textarea name="description" rows="4" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('description', $product->description ?? '') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Pricing & Stock</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Regular Price (₦) *</label>
                        <input type="number" name="price" step="0.01" min="0" required value="{{ old('price', $product->price ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Discount Price (₦)</label>
                        <input type="number" name="discount_price" step="0.01" min="0" value="{{ old('discount_price', $product->discount_price ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Member Price (₦)</label>
                        <input type="number" name="member_price" step="0.01" min="0" value="{{ old('member_price', $product->member_price ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Stock Quantity *</label>
                        <input type="number" name="stock_quantity" min="0" required value="{{ old('stock_quantity', $product->stock_quantity ?? 0) }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Barcode</label>
                        <input type="text" name="barcode" value="{{ old('barcode', $product->barcode ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Product Details</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Ingredients</label>
                        <textarea name="ingredients" rows="3" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('ingredients', $product->ingredients ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Nutritional Info</label>
                        <textarea name="nutritional_info" rows="3" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('nutritional_info', $product->nutritional_info ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Storage Instructions</label>
                        <textarea name="storage_instructions" rows="2" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('storage_instructions', $product->storage_instructions ?? '') }}</textarea>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Video URL (YouTube or MP4)</label>
                        <input type="url" name="video_url" value="{{ old('video_url', $product->video_url ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Manufacturing Date</label>
                        <input type="date" name="manufacturing_date" value="{{ old('manufacturing_date', $product->manufacturing_date ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Expiry Date</label>
                        <input type="date" name="expiry_date" value="{{ old('expiry_date', $product->expiry_date ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Country of Origin</label>
                        <input type="text" name="country_of_origin" value="{{ old('country_of_origin', $product->country_of_origin ?? 'Nigeria') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Batch Number</label>
                        <input type="text" name="batch_number" value="{{ old('batch_number', $product->batch_number ?? '') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Product Images</h3>
                <div class="flex flex-wrap gap-2 mb-3">
                    @if ($product)
                        @foreach ($product->images as $img)
                            <div class="relative">
                                <img src="{{ $img->image_path }}" class="w-20 h-20 rounded-lg object-cover border {{ $img->is_primary ? 'border-emerald-500' : '' }}">
                                @if ($img->is_primary)<span class="absolute -top-1 -right-1 bg-emerald-500 text-white text-[8px] font-bold px-1 rounded">PRIMARY</span>@endif
                            </div>
                        @endforeach
                    @endif
                </div>
                <input type="file" name="images[]" multiple accept="image/*" class="w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-emerald-700 file:text-sm file:font-semibold">
                <p class="text-xs text-gray-400 mt-2">JPEG/PNG/WebP, max 2MB each. First image becomes primary.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-3">Status</h3>
                <label class="flex items-center gap-2 text-sm text-gray-700 mb-2">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active ?? true)) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"> Active (visible in shop)
                </label>
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product->is_featured ?? false)) class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"> Featured on homepage
                </label>
            </div>

            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg py-3 transition-colors">{{ $product ? 'Update Product' : 'Create Product' }}</button>
        </div>
    </form>
@endsection

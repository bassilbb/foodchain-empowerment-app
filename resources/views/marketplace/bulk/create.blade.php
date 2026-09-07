@extends('marketplace.layouts.app')

@section('content')
    <h1 class="text-2xl font-extrabold text-gray-900 mb-6">Start a Community Bulk Order</h1>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <ul class="text-sm text-red-700 space-y-1">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('marketplace.bulk.store') }}" id="bulk-form">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                    <h3 class="font-bold text-gray-900 mb-4">Bulk Order Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Title *</label>
                            <input type="text" name="title" required value="{{ old('title') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Community rice & beans bulk buy">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Description</label>
                            <textarea name="description" rows="3" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Target Date *</label>
                            <input type="date" name="target_date" required value="{{ old('target_date') }}" min="{{ now()->addDay()->toDateString() }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Bulk Discount (%)</label>
                            <input type="number" name="discount_percentage" min="0" max="50" step="0.5" value="{{ old('discount_percentage', 5) }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Delivery Method *</label>
                            <select name="delivery_type" required class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="community_delivery">Community Delivery</option>
                                <option value="pickup">Pickup</option>
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase mb-1.5 block">Delivery Location</label>
                            <input type="text" name="delivery_location" value="{{ old('delivery_location') }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="e.g. Ikeja, Lagos">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900">Products Needed</h3>
                        <button type="button" onclick="addItemRow()" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg px-3 py-2 transition-colors">+ Add Product</button>
                    </div>
                    <div id="items-container">
                        <div class="item-row grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3">
                            <div class="sm:col-span-2">
                                <select name="items[0][product_id]" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" required>
                                    <option value="">Select product...</option>
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} - ₦{{ number_format($product->selling_price, 2) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="number" name="items[0][quantity]" min="1" placeholder="Quantity" required class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">Items are shown at current prices. The bulk discount applies once the order is complete.</p>
                </div>
            </div>

            <div>
                <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-3">How it works</h3>
                    <ol class="space-y-3 text-sm text-gray-600">
                        <li class="flex gap-2"><span class="text-emerald-600 font-bold">1.</span> Set up your community bulk order with the items you need.</li>
                        <li class="flex gap-2"><span class="text-emerald-600 font-bold">2.</span> Share the link with your community members.</li>
                        <li class="flex gap-2"><span class="text-emerald-600 font-bold">3.</span> Members contribute quantities they need using their wallet.</li>
                        <li class="flex gap-2"><span class="text-emerald-600 font-bold">4.</span> Once the target quantity is met, the order completes with the bulk discount applied.</li>
                    </ol>
                    <button type="submit" class="w-full mt-5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg py-3 transition-colors">Create Bulk Order</button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => $p->selling_price]));
        let itemIndex = 1;
        function addItemRow() {
            const container = document.getElementById('items-container');
            const row = document.createElement('div');
            row.className = 'item-row grid grid-cols-1 sm:grid-cols-3 gap-3 mb-3';
            let opts = '<option value="">Select product...</option>';
            products.forEach(p => { opts += `<option value="${p.id}">${p.name} - ₦${Number(p.price).toLocaleString(undefined, {minimumFractionDigits: 2})}</option>`; });
            row.innerHTML = `
                <div class="sm:col-span-2">
                    <select name="items[${itemIndex}][product_id]" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                        ${opts}
                    </select>
                </div>
                <div class="flex gap-2">
                    <input type="number" name="items[${itemIndex}][quantity]" min="1" placeholder="Quantity" class="flex-1 w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="button" onclick="this.closest('.item-row').remove()" class="text-red-400 hover:text-red-600 text-lg px-2">✕</button>
                </div>`;
            container.appendChild(row);
            itemIndex++;
        }
    </script>
    @endpush
@endsection

@extends('marketplace.layouts.app')

@section('content')
    @include('marketplace.vendor.partials.nav', ['vendor' => $vendor])

    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <h1 class="text-xl font-extrabold text-gray-900">My Products</h1>
        <a href="{{ route('marketplace.vendor.products.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg px-4 py-2.5 transition-colors">+ Add Product</a>
    </div>

    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or SKU..." class="flex-1 min-w-40 text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        <select name="status" class="text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            <option value="">All Status</option>
            <option value="active" @selected(request('status') === 'active')>Active</option>
            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg px-4 py-2 transition-colors">Filter</button>
    </form>

    @if ($products->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">📦</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No products yet</h3>
            <a href="{{ route('marketplace.vendor.products.create') }}" class="inline-block mt-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg px-8 py-3 transition-colors">Add Your First Product</a>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs text-gray-500 border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3">Product</th>
                        <th class="px-4 py-3">SKU</th>
                        <th class="px-4 py-3">Price</th>
                        <th class="px-4 py-3">Stock</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-10 h-10 rounded bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">{{ strtoupper(substr($product->name, 0, 1)) }}</div>
                                    <div>
                                        <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $product->category->name ?? 'No category' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $product->sku }}</td>
                            <td class="px-4 py-3">
                                <p class="font-semibold text-emerald-700">₦{{ number_format($product->selling_price, 2) }}</p>
                                @if ($product->discount_percent)
                                    <p class="text-xs text-gray-400 line-through">₦{{ number_format($product->price, 2) }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="{{ $product->isLowStock() ? 'text-orange-500 font-bold' : 'text-gray-600' }}">{{ $product->stock_quantity }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $product->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('marketplace.vendor.products.edit', $product) }}" class="text-emerald-700 hover:underline text-xs font-semibold mr-3">Edit</a>
                                <form action="{{ route('marketplace.vendor.products.delete', $product) }}" method="POST" class="inline" onsubmit="return confirm('Deactivate this product?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-500 hover:underline text-xs font-semibold">Deactivate</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-6">{{ $products->links() }}</div>
    @endif
@endsection

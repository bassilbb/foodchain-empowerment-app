@extends('marketplace.layouts.app')

@section('content')
    @include('marketplace.vendor.partials.nav', ['vendor' => $vendor])

    <h1 class="text-xl font-extrabold text-gray-900 mb-6">Vendor Dashboard</h1>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase">Products</p>
            <p class="text-3xl font-extrabold text-emerald-700 mt-1">{{ $productCount }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase">Orders</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $orderCount }}</p>
            <p class="text-xs text-orange-500 mt-1">{{ $pendingItems }} pending item(s)</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase">Revenue (delivered)</p>
            <p class="text-2xl font-extrabold text-emerald-700 mt-1">₦{{ number_format($revenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase">Attention</p>
            <p class="text-3xl font-extrabold text-red-500 mt-1">{{ $lowStock }}</p>
            <p class="text-xs text-gray-400 mt-1">low stock · {{ $expiringSoon }} expiring</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4">Recent Orders</h3>
            @if ($recentOrders->isEmpty())
                <p class="text-sm text-gray-500">No orders yet.</p>
            @else
                <div class="space-y-2">
                    @foreach ($recentOrders as $item)
                        <div class="flex items-center justify-between text-sm border-b border-gray-50 pb-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">{{ ucfirst($item->status) }}</span>
                                <span class="text-gray-800 truncate">{{ $item->product_name }}</span>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <span class="text-xs text-gray-400">{{ $item->quantity }}×</span>
                                <span class="font-semibold text-emerald-700">₦{{ number_format($item->total_price, 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4">Top Products</h3>
            @if ($topProducts->isEmpty())
                <p class="text-sm text-gray-500">No products yet. <a href="{{ route('marketplace.vendor.products.create') }}" class="text-emerald-700 hover:underline">Add your first product</a></p>
            @else
                <div class="space-y-3">
                    @foreach ($topProducts as $product)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-8 h-8 rounded bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">{{ strtoupper(substr($product->name, 0, 1)) }}</div>
                                <span class="text-sm text-gray-800 truncate">{{ $product->name }}</span>
                            </div>
                            <span class="text-xs text-gray-500 shrink-0">{{ $product->sold_count }} sold</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

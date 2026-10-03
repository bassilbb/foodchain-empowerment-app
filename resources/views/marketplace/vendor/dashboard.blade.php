@extends('marketplace.layouts.app')

@section('content')
    @include('marketplace.vendor.partials.nav', ['vendor' => $vendor])

    <div class="mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-emerald-600">Seller overview</p>
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900">Vendor Dashboard</h1>
        </div>
        <a href="{{ route('marketplace.vendor.products.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl px-4 py-2.5 shadow-lg shadow-emerald-600/20 transition-colors">Add Product</a>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-[22px] border border-gray-100 shadow-[0_16px_35px_rgba(15,23,42,0.05)] p-5">
            <p class="text-xs text-gray-500 uppercase tracking-[0.12em]">Products</p>
            <p class="text-3xl font-extrabold text-emerald-700 mt-2">{{ $productCount }}</p>
        </div>
        <div class="bg-white rounded-[22px] border border-gray-100 shadow-[0_16px_35px_rgba(15,23,42,0.05)] p-5">
            <p class="text-xs text-gray-500 uppercase tracking-[0.12em]">Orders</p>
            <p class="text-3xl font-extrabold text-gray-900 mt-2">{{ $orderCount }}</p>
            <p class="text-xs text-orange-500 mt-1">{{ $pendingItems }} pending item(s)</p>
        </div>
        <div class="bg-white rounded-[22px] border border-gray-100 shadow-[0_16px_35px_rgba(15,23,42,0.05)] p-5">
            <p class="text-xs text-gray-500 uppercase tracking-[0.12em]">Revenue</p>
            <p class="text-2xl font-extrabold text-emerald-700 mt-2">₦{{ number_format($revenue, 2) }}</p>
        </div>
        <div class="bg-white rounded-[22px] border border-gray-100 shadow-[0_16px_35px_rgba(15,23,42,0.05)] p-5">
            <p class="text-xs text-gray-500 uppercase tracking-[0.12em]">Attention</p>
            <p class="text-3xl font-extrabold text-red-500 mt-2">{{ $lowStock }}</p>
            <p class="text-xs text-gray-400 mt-1">low stock · {{ $expiringSoon }} expiring</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-[22px] border border-gray-100 shadow-[0_16px_35px_rgba(15,23,42,0.05)] p-6">
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

        <div class="bg-white rounded-[22px] border border-gray-100 shadow-[0_16px_35px_rgba(15,23,42,0.05)] p-6">
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

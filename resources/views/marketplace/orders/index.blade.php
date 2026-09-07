@extends('marketplace.layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <h1 class="text-2xl font-extrabold text-gray-900">My Orders</h1>
        <a href="{{ route('marketplace.shop') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg px-4 py-2 transition-colors">Continue Shopping</a>
    </div>

    <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
        <a href="{{ route('marketplace.orders.index') }}" class="px-4 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap {{ ! $currentStatus ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">All</a>
        @foreach ($statuses as $key => $label)
            <a href="{{ route('marketplace.orders.index', ['status' => $key]) }}" class="px-4 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap {{ $currentStatus === $key ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">{{ $label }}</a>
        @endforeach
    </div>

    @if ($orders->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">📦</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No orders yet</h3>
            <p class="text-sm text-gray-500 mb-6">Your orders will appear here once you place them.</p>
            <a href="{{ route('marketplace.shop') }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg px-8 py-3 transition-colors">Start Shopping</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <a href="{{ route('marketplace.orders.show', $order) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">Order #{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y, g:i A') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ match($order->status) { 'delivered' => 'bg-emerald-100 text-emerald-700', 'cancelled', 'refunded' => 'bg-red-100 text-red-600', 'pending' => 'bg-yellow-100 text-yellow-700', default => 'bg-blue-100 text-blue-700' } }}">
                                {{ \App\Models\Marketplace\Order::STATUSES[$order->status] ?? $order->status }}
                            </span>
                            <span class="font-extrabold text-emerald-700 text-sm">₦{{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($order->items->take(4) as $item)
                            <div class="flex items-center gap-2 bg-gray-50 rounded-lg px-2 py-1">
                                <span class="text-xs text-gray-700">{{ $item->quantity }}× {{ $item->product_name }}</span>
                            </div>
                        @endforeach
                        @if ($order->items->count() > 4)
                            <span class="text-xs text-gray-400">+{{ $order->items->count() - 4 }} more</span>
                        @endif
                    </div>
                    <div class="mt-3 flex flex-wrap gap-4 text-xs text-gray-500">
                        <span>💳 {{ \App\Models\Marketplace\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</span>
                        <span>🚚 {{ ucwords(str_replace('_', ' ', $order->delivery_type)) }}</span>
                        @if ($order->estimated_delivery_date)
                            <span>📅 Est. delivery: {{ $order->estimated_delivery_date->format('d M Y') }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $orders->links() }}</div>
    @endif
@endsection

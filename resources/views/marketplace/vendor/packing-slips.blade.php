@extends('marketplace.layouts.app')

@section('content')
    @include('marketplace.vendor.partials.nav', ['vendor' => $vendor])

    <h1 class="text-xl font-extrabold text-gray-900 mb-6">Packing Slips</h1>

    @if ($items->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">📄</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No packed items</h3>
            <p class="text-sm text-gray-500">Items move here once you mark them as <strong>Packed</strong> in the Orders section.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($items->groupBy('order_id') as $orderId => $orderItems)
                @php $order = $orderItems->first()->order; @endphp
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <div class="flex items-center justify-between mb-3 border-b border-gray-100 pb-3">
                        <div>
                            <p class="font-bold text-gray-900 text-sm">Order #{{ $order->order_number }}</p>
                            <p class="text-xs text-gray-500">{{ $order->created_at->format('d M Y') }}</p>
                        </div>
                        <span class="text-xs font-semibold text-emerald-700">Pack & Ship</span>
                    </div>
                    <div class="space-y-2">
                        @foreach ($orderItems as $item)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">{{ $item->quantity }}× {{ $item->product_name }}</span>
                                <span class="text-gray-500">{{ $item->product_sku }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 text-xs text-gray-500 border-t border-gray-100 pt-3">
                        <p>Customer: {{ $order->user->name ?? 'N/A' }}</p>
                        <p>Delivery: {{ ucwords(str_replace('_', ' ', $order->delivery_type)) }}{{ $order->delivery_state ? ' · '.$order->delivery_state : '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $items->links() }}</div>
    @endif
@endsection

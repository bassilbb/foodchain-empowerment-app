@extends('marketplace.layouts.app')

@section('content')
    @include('marketplace.vendor.partials.nav', ['vendor' => $vendor])

    <h1 class="text-xl font-extrabold text-gray-900 mb-6">Orders</h1>

    <div class="flex gap-2 mb-6 overflow-x-auto pb-1">
        <a href="{{ route('marketplace.vendor.orders') }}" class="px-4 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap {{ ! $currentStatus ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600' }}">All</a>
        @foreach (['pending', 'confirmed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled'] as $st)
            <a href="{{ route('marketplace.vendor.orders', ['status' => $st]) }}" class="px-4 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap {{ $currentStatus === $st ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600' }}">{{ ucfirst($st) }}</a>
        @endforeach
    </div>

    @if ($items->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">🧾</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No orders yet</h3>
            <p class="text-sm text-gray-500">Orders containing your products will appear here.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($grouped as $orderId => $orderItems)
                @php $order = $orderItems->first()->order; @endphp
                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-3 bg-gray-50 flex flex-wrap items-center justify-between gap-2 border-b border-gray-100">
                        <div>
                            <span class="font-bold text-gray-900 text-sm">Order #{{ $order->order_number }}</span>
                            <span class="text-xs text-gray-500 ml-2">{{ $order->created_at->format('d M Y, g:i A') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500">Customer: {{ $order->user->name ?? 'N/A' }}</span>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $order->payment_status === 'paid' ? 'bg-emerald-100 text-emerald-700' : 'bg-yellow-100 text-yellow-700' }}">{{ strtoupper($order->payment_status) }}</span>
                        </div>
                    </div>
                    @foreach ($orderItems as $item)
                        <div class="px-5 py-3 border-b border-gray-50 flex flex-wrap items-center gap-3">
                            <div class="flex-1 min-w-40">
                                <p class="text-sm font-medium text-gray-800">{{ $item->product_name }}</p>
                                <p class="text-xs text-gray-400">₦{{ number_format($item->unit_price, 2) }} × {{ $item->quantity }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">{{ ucfirst($item->status) }}</span>
                                <form action="{{ route('marketplace.vendor.orders.status', $item) }}" method="POST" class="flex items-center gap-1">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="text-xs rounded border-gray-300">
                                        @foreach (['confirmed', 'processing', 'packed', 'shipped', 'delivered', 'cancelled'] as $st)
                                            <option value="{{ $st }}" @selected($item->status === $st)>{{ ucfirst($st) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
        <div class="mt-6">{{ $items->links() }}</div>
    @endif
@endsection

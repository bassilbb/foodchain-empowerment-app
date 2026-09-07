@extends('marketplace.layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900">Community Bulk Purchase</h1>
            <p class="text-sm text-gray-500 mt-1">Team up with your community to buy in bulk and unlock bigger discounts.</p>
        </div>
        <a href="{{ route('marketplace.bulk.create') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg px-5 py-2.5 transition-colors">+ Start a Bulk Order</a>
    </div>

    @if ($bulkOrders->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm p-16 text-center">
            <div class="text-6xl mb-4">🤝</div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No open bulk orders</h3>
            <p class="text-sm text-gray-500 mb-6">Be the first to start a community bulk order.</p>
            <a href="{{ route('marketplace.bulk.create') }}" class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg px-8 py-3 transition-colors">Start One Now</a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($bulkOrders as $bulkOrder)
                <a href="{{ route('marketplace.bulk.show', $bulkOrder) }}" class="bg-white rounded-xl shadow-sm hover:shadow-lg transition-all p-5">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-bold text-gray-900">{{ $bulkOrder->title }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $bulkOrder->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">{{ ucfirst($bulkOrder->status) }}</span>
                    </div>
                    @if ($bulkOrder->description)
                        <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ $bulkOrder->description }}</p>
                    @endif
                    <div class="grid grid-cols-3 gap-2 text-center bg-gray-50 rounded-lg p-3">
                        <div>
                            <p class="text-lg font-extrabold text-emerald-700">{{ $bulkOrder->discount_percentage }}%</p>
                            <p class="text-[10px] text-gray-500 uppercase">Discount</p>
                        </div>
                        <div>
                            <p class="text-lg font-extrabold text-gray-900">{{ $bulkOrder->total_contributed_qty }}/{{ $bulkOrder->total_required_qty }}</p>
                            <p class="text-[10px] text-gray-500 uppercase">Units</p>
                        </div>
                        <div>
                            <p class="text-lg font-extrabold text-gray-900">{{ $bulkOrder->total_contributors }}</p>
                            <p class="text-[10px] text-gray-500 uppercase">Members</p>
                        </div>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                        <span>by {{ $bulkOrder->creator->name ?? 'Member' }}</span>
                        <span>Target: {{ $bulkOrder->target_date->format('d M Y') }}</span>
                    </div>
                    <div class="mt-2 h-2 bg-gray-100 rounded-full overflow-hidden">
                        @php
                            $pct = $bulkOrder->total_required_qty > 0 ? min(100, ($bulkOrder->total_contributed_qty / $bulkOrder->total_required_qty) * 100) : 0;
                        @endphp
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="mt-6">{{ $bulkOrders->links() }}</div>
    @endif
@endsection

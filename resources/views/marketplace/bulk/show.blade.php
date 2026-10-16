@extends('marketplace.layouts.app')

@section('content')
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('marketplace.bulk.index') }}" class="hover:text-emerald-700">Bulk Orders</a>
        <span>/</span>
        <span class="text-gray-800 font-medium">{{ $bulkOrder->title }}</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-gray-900">{{ $bulkOrder->title }}</h1>
                <p class="text-sm text-gray-500 mt-1">Started by {{ $bulkOrder->creator->name ?? 'Member' }} · Target {{ $bulkOrder->target_date->format('d M Y') }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $bulkOrder->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' }}">{{ ucfirst($bulkOrder->status) }}</span>
        </div>
        @if ($bulkOrder->description)
            <p class="text-sm text-gray-600 mt-3">{{ $bulkOrder->description }}</p>
        @endif

        @php
            $pct = $bulkOrder->total_required_qty > 0 ? min(100, ($bulkOrder->total_contributed_qty / $bulkOrder->total_required_qty) * 100) : 0;
        @endphp
        <div class="mt-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>{{ $bulkOrder->total_contributed_qty }} / {{ $bulkOrder->total_required_qty }} units contributed</span>
                <span>{{ round($pct) }}%</span>
            </div>
            <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-emerald-500 rounded-full transition-all" style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Items & Contributions</h3>

                @if ($bulkOrder->items->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-6">No items in this bulk order.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($bulkOrder->items as $item)
                            <div class="border border-gray-100 rounded-xl p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm">{{ $item->product->name ?? 'Product' }}</p>
                                        <p class="text-xs text-gray-500">₦{{ number_format($item->unit_price, 2) }}{{ $bulkOrder->discount_percentage > 0 ? ' → ₦'.number_format($item->unit_price * (1 - $bulkOrder->discount_percentage/100), 2).' with bulk discount' : '' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-emerald-700">{{ $item->contributed_quantity }}/{{ $item->required_quantity }}</p>
                                        <p class="text-xs text-gray-400">units</p>
                                    </div>
                                </div>
                                <div class="h-2 bg-gray-100 rounded-full overflow-hidden mb-3">
                                    @php $ipct = $item->required_quantity > 0 ? min(100, ($item->contributed_quantity / $item->required_quantity) * 100) : 0; @endphp
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $ipct }}%"></div>
                                </div>

                                @if ($bulkOrder->isOpen() && auth()->user()->hasSufficientWalletBalance($item->unit_price * (1 - $bulkOrder->discount_percentage/100)))
                                    <form action="{{ route('marketplace.bulk.contribute', $bulkOrder) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $item->id }}">
                                        <input type="number" name="quantity" min="1" max="{{ max(1, $item->required_quantity - $item->contributed_quantity) }}" placeholder="Qty" required class="w-24 text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg px-4 py-2.5 transition-colors">Contribute via Wallet</button>
                                    </form>
                                @elseif ($bulkOrder->isOpen())
                                    <p class="text-xs text-red-500">Insufficient wallet balance to contribute. <a href="{{ route('manual-payments.create', ['type' => 'wallet_topup']) }}" class="underline">Top up wallet</a></p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                <h3 class="font-bold text-gray-900 mb-3">Contributors</h3>
                @if ($bulkOrder->contributions->isEmpty())
                    <p class="text-sm text-gray-500">No contributions yet.</p>
                @else
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        @foreach ($bulkOrder->contributions->groupBy('user_id') as $userId => $contribs)
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 text-xs font-bold">{{ strtoupper(substr($contribs->first()->user->name ?? 'U', 0, 1)) }}</div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $contribs->first()->user->name ?? 'Member' }}</p>
                                    <p class="text-xs text-gray-400">{{ $contribs->sum('quantity') }} units · ₦{{ number_format($contribs->sum('amount'), 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-gradient-to-r from-emerald-800 to-emerald-600 rounded-2xl p-6 text-white">
                <p class="text-emerald-200 text-sm">Total Bulk Value</p>
                <p class="text-2xl font-extrabold mt-1">₦{{ number_format($bulkOrder->total_value, 2) }}</p>
                <p class="text-emerald-200 text-xs mt-2">{{ $bulkOrder->total_contributors }} member(s) contributed</p>
                @if ($bulkOrder->discount_percentage > 0)
                    <p class="text-emerald-200 text-xs mt-1">Unlocks {{ $bulkOrder->discount_percentage }}% community discount</p>
                @endif
            </div>
        </div>
    </div>
@endsection

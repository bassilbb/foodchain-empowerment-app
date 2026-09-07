@extends('marketplace.layouts.app')

@section('content')
    @include('marketplace.vendor.partials.nav', ['vendor' => $vendor])

    <h1 class="text-xl font-extrabold text-gray-900 mb-6">Sales Reports</h1>

    <form method="GET" class="bg-white rounded-2xl shadow-sm p-4 mb-6 flex flex-wrap items-end gap-3">
        <div>
            <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}" class="text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}" class="text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg px-4 py-2 transition-colors">Generate</button>
    </form>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase">Total Sales</p>
            <p class="text-2xl font-extrabold text-emerald-700 mt-1">₦{{ number_format($totalSales, 2) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase">Items Sold</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($totalItems) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase">Orders</p>
            <p class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($orderCount) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5">
            <p class="text-xs text-gray-500 uppercase">Net (after {{ $vendor->commission_rate }}%)</p>
            <p class="text-2xl font-extrabold text-blue-700 mt-1">₦{{ number_format($net, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-gray-900 mb-4">Daily Sales</h3>
        @if ($daily->isEmpty())
            <p class="text-sm text-gray-500">No sales in this period.</p>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 border-b border-gray-100">
                            <th class="px-3 py-2">Date</th>
                            <th class="px-3 py-2">Items</th>
                            <th class="px-3 py-2 text-right">Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($daily as $date => $row)
                            <tr class="border-b border-gray-50">
                                <td class="px-3 py-2 text-gray-700">{{ now()->parse($date)->format('d M Y') }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $row['items'] }}</td>
                                <td class="px-3 py-2 text-right font-semibold text-emerald-700">₦{{ number_format($row['sales'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

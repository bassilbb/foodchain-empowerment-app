{{-- resources/views/admin/reports/payments.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Payment Reports')
@section('page-title', 'Payment Reports & Revenue Analytics')

@section('content')
<!-- Date Filter -->
<div class="mb-6 bg-white p-4 rounded-lg shadow">
    <form method="GET" class="flex gap-4 items-end">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}"
                   class="px-4 py-2 border rounded-lg">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}"
                   class="px-4 py-2 border rounded-lg">
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Apply Filter
        </button>
        <a href="{{ route('admin.reports.payments') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            Reset
        </a>
    </form>
</div>

<!-- Revenue Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-green-400 to-green-600 text-white p-6 rounded-lg shadow-lg">
        <p class="text-green-100 text-sm mb-1">Total Revenue</p>
        <p class="text-4xl font-bold">{{ format_currency($totalRevenue) }}</p>
    </div>
    <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white p-6 rounded-lg shadow-lg">
        <p class="text-blue-100 text-sm mb-1">Membership Revenue</p>
        <p class="text-4xl font-bold">{{ format_currency($membershipRevenue) }}</p>
    </div>
    <div class="bg-gradient-to-br from-purple-400 to-purple-600 text-white p-6 rounded-lg shadow-lg">
        <p class="text-purple-100 text-sm mb-1">Starter Pack Revenue</p>
        <p class="text-4xl font-bold">{{ format_currency($starterPackRevenue) }}</p>
    </div>
</div>

<!-- Payment Status Breakdown -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-semibold mb-4">Payment Status Breakdown</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($paymentsByStatus as $status)
        <div class="border rounded-lg p-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium capitalize">{{ $status->status }}</span>
                <span class="px-2 py-1 text-xs font-semibold rounded
                    {{ $status->status === 'successful' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $status->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $status->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ $status->count }}
                </span>
            </div>
            <p class="text-2xl font-bold">{{ format_currency($status->total) }}</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Daily Revenue Chart -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-semibold mb-4">Daily Revenue Trend</h3>
    <canvas id="revenueChart"></canvas>
</div>

<!-- Recent Transactions -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold">Recent Successful Transactions</h3>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($dailyRevenue->take(15) as $day)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">-</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $day->count }} transactions</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">Mixed</td>
                <td class="px-6 py-4 whitespace-nowrap text-right font-semibold text-green-600">
                    {{ format_currency($day->total) }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('revenueChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($dailyRevenue->pluck('date')),
        datasets: [{
            label: 'Daily Revenue (₦)',
            data: @json($dailyRevenue->pluck('total')),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '₦' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection

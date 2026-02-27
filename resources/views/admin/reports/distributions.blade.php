{{-- resources/views/admin/reports/distributions.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Distribution Reports')
@section('page-title', 'Distribution Reports & Analytics')

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
        <a href="{{ route('admin.reports.distributions') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            Reset
        </a>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-400 to-blue-600 text-white p-6 rounded-lg shadow-lg">
        <p class="text-blue-100 text-sm mb-1">Total Distributions</p>
        <p class="text-4xl font-bold">{{ number_format($totalDistributions) }}</p>
        <p class="text-blue-100 text-xs mt-2">In selected period</p>
    </div>
    <div class="bg-gradient-to-br from-green-400 to-green-600 text-white p-6 rounded-lg shadow-lg">
        <p class="text-green-100 text-sm mb-1">Total Value Distributed</p>
        <p class="text-4xl font-bold">{{ format_currency($totalValue) }}</p>
        <p class="text-green-100 text-xs mt-2">Food packages distributed</p>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- By Status -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Distribution by Status</h3>
        <canvas id="statusChart"></canvas>
        <div class="mt-4 space-y-2">
            @foreach($distributionsByStatus as $status)
            <div class="flex justify-between items-center text-sm">
                <span class="capitalize">{{ $status->status }}</span>
                <span class="font-semibold">{{ $status->count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- By Tier -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Distribution by Membership Tier</h3>
        <div class="space-y-3">
            @foreach($distributionsByTier as $tier)
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-medium">{{ $tier->tier->name }}</span>
                    <span class="text-sm text-gray-600">{{ $tier->count }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex-1 bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full"
                             style="width: {{ ($tier->count / $totalDistributions) * 100 }}%"></div>
                    </div>
                    <span class="text-xs text-gray-500 font-mono">{{ format_currency($tier->total_value) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- By Type -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Distribution by Type</h3>
        <div class="space-y-4">
            @foreach($distributionsByType as $type)
            <div class="border-b pb-3 last:border-b-0">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium capitalize">{{ str_replace('_', ' ', $type->distribution_type) }}</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                        {{ $type->count }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full"
                         style="width: {{ ($type->count / $totalDistributions) * 100 }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ number_format(($type->count / $totalDistributions) * 100, 1) }}% of total
                </p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Distribution Status Breakdown -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-semibold mb-4">Distribution Status Breakdown</h3>
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        @foreach($distributionsByStatus as $status)
        <div class="text-center p-4 border rounded-lg hover:shadow-md transition">
            <div class="mb-2">
                <span class="px-3 py-1 text-xs font-semibold rounded
                    {{ $status->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $status->status === 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $status->status === 'packaged' ? 'bg-purple-100 text-purple-800' : '' }}
                    {{ $status->status === 'dispatched' ? 'bg-indigo-100 text-indigo-800' : '' }}
                    {{ $status->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $status->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($status->status) }}
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-800">{{ $status->count }}</p>
            <p class="text-xs text-gray-500 mt-1">
                {{ number_format(($status->count / $totalDistributions) * 100, 1) }}%
            </p>
        </div>
        @endforeach
    </div>
</div>

<!-- Value by Tier Chart -->
<div class="bg-white rounded-lg shadow p-6 mb-8">
    <h3 class="text-lg font-semibold mb-4">Distribution Value by Tier</h3>
    <canvas id="tierValueChart"></canvas>
</div>

<!-- Recent Distributions Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold">Recent Distributions (Last 25)</h3>
        <a href="{{ route('admin.distributions.index') }}" class="text-sm text-blue-600 hover:underline">
            View All →
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distribution #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $recentDistributions = \App\Models\FoodDistribution::with(['user', 'tier'])
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->latest()
                        ->take(25)
                        ->get();
                @endphp
                @forelse($recentDistributions as $dist)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $dist->created_at->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono text-sm font-medium text-gray-900">
                            {{ $dist->distribution_number }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div class="font-medium text-gray-900">{{ $dist->user->full_name }}</div>
                            <div class="text-gray-500 text-xs">{{ $dist->user->email }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded {{ tier_badge_color($dist->tier->slug) }} text-white">
                            {{ $dist->tier->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm capitalize text-gray-700">
                            {{ str_replace('_', ' ', $dist->distribution_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right">
                        <span class="text-sm font-semibold text-green-600">
                            {{ format_currency($dist->package_value) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-2 py-1 text-xs font-semibold rounded
                            {{ $dist->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $dist->status === 'approved' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $dist->status === 'packaged' ? 'bg-purple-100 text-purple-800' : '' }}
                            {{ $dist->status === 'dispatched' ? 'bg-indigo-100 text-indigo-800' : '' }}
                            {{ $dist->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $dist->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($dist->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <a href="{{ route('admin.distributions.show', $dist) }}"
                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-8 text-center">
                        <div class="text-gray-400">
                            <svg class="mx-auto h-12 w-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-sm">No distributions found in the selected date range</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Export Options -->
<div class="mt-6 bg-white rounded-lg shadow p-4">
    <div class="flex justify-between items-center">
        <div>
            <h4 class="font-semibold text-gray-800">Export Report</h4>
            <p class="text-sm text-gray-600">Download this report in various formats</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium">
                🖨️ Print
            </button>
            <button onclick="alert('Excel export feature coming soon!')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                📊 Export Excel
            </button>
            <button onclick="alert('PDF export feature coming soon!')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                📄 Export PDF
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: @json($distributionsByStatus->pluck('status')->map(function($s) { return ucfirst($s); })),
        datasets: [{
            data: @json($distributionsByStatus->pluck('count')),
            backgroundColor: [
                'rgba(251, 191, 36, 0.8)',  // pending - yellow
                'rgba(59, 130, 246, 0.8)',  // approved - blue
                'rgba(168, 85, 247, 0.8)',  // packaged - purple
                'rgba(99, 102, 241, 0.8)',  // dispatched - indigo
                'rgba(34, 197, 94, 0.8)',   // delivered - green
                'rgba(239, 68, 68, 0.8)'    // cancelled - red
            ],
            borderColor: [
                'rgba(251, 191, 36, 1)',
                'rgba(59, 130, 246, 1)',
                'rgba(168, 85, 247, 1)',
                'rgba(99, 102, 241, 1)',
                'rgba(34, 197, 94, 1)',
                'rgba(239, 68, 68, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 10,
                    font: {
                        size: 11
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.parsed || 0;
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = ((value / total) * 100).toFixed(1);
                        return label + ': ' + value + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Tier Value Chart
const tierValueCtx = document.getElementById('tierValueChart').getContext('2d');
new Chart(tierValueCtx, {
    type: 'bar',
    data: {
        labels: @json($distributionsByTier->pluck('tier.name')),
        datasets: [{
            label: 'Total Value (₦)',
            data: @json($distributionsByTier->pluck('total_value')),
            backgroundColor: [
                'rgba(255, 159, 64, 0.6)',  // Bronze
                'rgba(192, 192, 192, 0.6)', // Silver
                'rgba(255, 205, 86, 0.6)',  // Gold
                'rgba(75, 192, 192, 0.6)'   // Emerald
            ],
            borderColor: [
                'rgba(255, 159, 64, 1)',
                'rgba(192, 192, 192, 1)',
                'rgba(255, 205, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 2
        }, {
            label: 'Distribution Count',
            data: @json($distributionsByTier->pluck('count')),
            backgroundColor: 'rgba(59, 130, 246, 0.6)',
            borderColor: 'rgba(59, 130, 246, 1)',
            borderWidth: 2,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                ticks: {
                    callback: function(value) {
                        return '₦' + value.toLocaleString();
                    }
                },
                title: {
                    display: true,
                    text: 'Total Value (₦)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                grid: {
                    drawOnChartArea: false,
                },
                title: {
                    display: true,
                    text: 'Distribution Count'
                }
            }
        }
    }
});
</script>
@endpush
@endsection

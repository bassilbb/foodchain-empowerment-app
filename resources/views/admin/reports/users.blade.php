{{-- resources/views/admin/reports/users.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'User Reports')
@section('page-title', 'User Reports & Analytics')

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
        <a href="{{ route('admin.reports.users') }}" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            Reset
        </a>
    </form>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Total Users</p>
        <p class="text-3xl font-bold text-gray-800">{{ number_format($totalUsers) }}</p>
    </div>
    <div class="bg-green-50 p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Active Users</p>
        <p class="text-3xl font-bold text-green-600">{{ number_format($activeUsers) }}</p>
    </div>
    <div class="bg-blue-50 p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">New Users (Period)</p>
        <p class="text-3xl font-bold text-blue-600">{{ number_format($newUsers) }}</p>
    </div>
    <div class="bg-purple-50 p-6 rounded-lg shadow">
        <p class="text-gray-500 text-sm">Inactive Users</p>
        <p class="text-3xl font-bold text-purple-600">{{ number_format($totalUsers - $activeUsers) }}</p>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Users by Tier -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Users by Membership Tier</h3>
        <canvas id="tierChart"></canvas>
    </div>

    <!-- Users by State -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Top 10 States</h3>
        <div class="space-y-3">
            @foreach($usersByState as $state)
            <div>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-sm font-medium">{{ $state->state }}</span>
                    <span class="text-sm text-gray-600">{{ number_format($state->count) }} users</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full"
                         style="width: {{ ($state->count / $totalUsers) * 100 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Top Referrers -->
<div class="bg-white rounded-lg shadow overflow-hidden mb-8">
    <div class="px-6 py-4 border-b">
        <h3 class="text-lg font-semibold">Top 10 Referrers</h3>
    </div>
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tier</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Referrals</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active Referrals</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($topReferrers as $index => $user)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="font-bold text-lg">{{ $index + 1 }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <a href="{{ route('admin.users.show', ['user' => $user->id]) }}" class="text-blue-600 hover:underline font-medium">
                        {{ $user->full_name }}
                    </a>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $user->email }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded {{ tier_badge_color($user->currentTier->slug) }} text-white">
                        {{ $user->currentTier->name }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span class="text-2xl font-bold text-blue-600">{{ $user->direct_referrals_count }}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span class="text-lg font-semibold text-green-600">{{ $user->activeReferrals()->count() }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Tier Distribution Chart
const tierCtx = document.getElementById('tierChart').getContext('2d');
new Chart(tierCtx, {
    type: 'doughnut',
    data: {
        labels: @json($usersByTier->pluck('tier')),
        datasets: [{
            data: @json($usersByTier->pluck('count')),
            backgroundColor: [
                'rgba(255, 159, 64, 0.8)',  // Bronze
                'rgba(192, 192, 192, 0.8)', // Silver
                'rgba(255, 205, 86, 0.8)',  // Gold
                'rgba(75, 192, 192, 0.8)'   // Emerald
            ],
            borderColor: [
                'rgba(255, 159, 64, 1)',
                'rgba(192, 192, 192, 1)',
                'rgba(255, 205, 86, 1)',
                'rgba(75, 192, 192, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush
@endsection

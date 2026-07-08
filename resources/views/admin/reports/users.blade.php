{{-- resources/views/admin/reports/users.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'User Reports')
@section('page-title', 'User Reports & Analytics')

@section('content')
<style>
    .filter-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 20px; margin-bottom: 24px; }
    .input-field { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 14px; transition: all 0.3s ease; }
    .input-field:focus { background: #ffffff; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); outline: none; }
    .btn-filter { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 12px 24px; border: none; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; }
    .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); }
    .btn-reset { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 12px 24px; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
    .btn-reset:hover { background: #e2e8f0; }
    .stat-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 24px; transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 50px -12px rgba(16, 185, 129, 0.2); }
    .stat-card.green { border-left: 4px solid #10b981; }
    .stat-card.blue { border-left: 4px solid #3b82f6; }
    .stat-card.purple { border-left: 4px solid #8b5cf6; }
    .stat-value { font-size: 32px; font-weight: 700; color: #1e293b; }
    .stat-card.green .stat-value { color: #10b981; }
    .stat-card.blue .stat-value { color: #3b82f6; }
    .stat-card.purple .stat-value { color: #8b5cf6; }
    .stat-label { font-size: 14px; color: #64748b; margin-top: 4px; }
    .chart-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 24px; }
    .chart-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; }
    .progress-bar { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); border-radius: 10px; height: 8px; overflow: hidden; }
    .progress-fill { background: linear-gradient(135deg, #10b981, #059669); height: 100%; border-radius: 10px; transition: width 0.5s ease; }
    .state-row { margin-bottom: 16px; }
    .state-name { font-size: 14px; font-weight: 500; color: #1e293b; }
    .state-count { font-size: 14px; color: #64748b; }
    .table-container { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); overflow: hidden; }
    .table-header { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border-bottom: 1px solid #e2e8f0; }
    .table-header th { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px; text-align: left; }
    .table-row { border-bottom: 1px solid #f1f5f9; transition: all 0.2s ease; }
    .table-row:hover { background: linear-gradient(180deg, #f0fdf4, #dcfce7); }
    .table-cell { padding: 16px; color: #475569; font-size: 14px; }
    .rank-badge { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: white; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; }
    .user-link { color: #10b981; font-weight: 500; text-decoration: none; }
    .user-link:hover { color: #059669; text-shadow: 0 2px 8px rgba(16, 185, 129, 0.3); }
    .tier-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .count-badge { font-size: 24px; font-weight: 700; color: #3b82f6; }
    .active-badge { font-size: 18px; font-weight: 600; color: #10b981; }
</style>

<div class="filter-card">
    <form method="GET" style="display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">From Date</label>
            <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="input-field">
        </div>
        <div>
            <label style="display: block; font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px; text-transform: uppercase;">To Date</label>
            <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="input-field">
        </div>
        <button type="submit" class="btn-filter">Apply Filter</button>
        <a href="{{ route('admin.reports.users') }}" class="btn-reset">Reset</a>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 24px; margin-bottom: 32px;">
    <div class="stat-card">
        <p class="stat-label">Total Users</p>
        <p class="stat-value">{{ number_format($totalUsers) }}</p>
    </div>
    <div class="stat-card green">
        <p class="stat-label">Active Users</p>
        <p class="stat-value">{{ number_format($activeUsers) }}</p>
    </div>
    <div class="stat-card blue">
        <p class="stat-label">New Users (Period)</p>
        <p class="stat-value">{{ number_format($newUsers) }}</p>
    </div>
    <div class="stat-card purple">
        <p class="stat-label">Inactive Users</p>
        <p class="stat-value">{{ number_format($totalUsers - $activeUsers) }}</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 32px;">
    <div class="chart-card">
        <h3 class="chart-title">Users by Membership Tier</h3>
        <canvas id="tierChart"></canvas>
    </div>

    <div class="chart-card">
        <h3 class="chart-title">Top 10 States</h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($usersByState as $state)
            <div class="state-row">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span class="state-name">{{ $state->state }}</span>
                    <span class="state-count">{{ number_format($state->count) }} users</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ ($state->count / $totalUsers) * 100 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="table-container">
    <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; background: linear-gradient(180deg, #f8fafc, #f1f5f9);">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Top 10 Referrers</h3>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <thead class="table-header">
            <tr>
                <th>Rank</th>
                <th>User</th>
                <th>Email</th>
                <th>Tier</th>
                <th style="text-align: center;">Total Referrals</th>
                <th style="text-align: center;">Active Referrals</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topReferrers as $index => $user)
            <tr class="table-row">
                <td class="table-cell"><span class="rank-badge">{{ $index + 1 }}</span></td>
                <td class="table-cell">
                    <a href="{{ route('admin.users.show', ['user' => $user->id]) }}" class="user-link">{{ $user->full_name }}</a>
                </td>
                <td class="table-cell" style="color: #64748b;">{{ $user->email }}</td>
                <td class="table-cell">
                    <span class="tier-badge" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">{{ $user->currentTier?->name ?? 'N/A' }}</span>
                </td>
                <td class="table-cell" style="text-align: center;"><span class="count-badge">{{ $user->direct_referrals_count }}</span></td>
                <td class="table-cell" style="text-align: center;"><span class="active-badge">{{ $user->activeReferrals()->count() }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const tierCtx = document.getElementById('tierChart').getContext('2d');
new Chart(tierCtx, {
    type: 'doughnut',
    data: {
        labels: @json($usersByTier->pluck('tier')),
        datasets: [{
            data: @json($usersByTier->pluck('count')),
            backgroundColor: ['rgba(16, 185, 129, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(245, 158, 11, 0.8)', 'rgba(139, 92, 246, 0.8)'],
            borderColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6'],
            borderWidth: 2
        }]
    },
    options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom' } } }
});
</script>
@endpush
@endsection
{{-- resources/views/admin/reports/distributions.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Distribution Reports')
@section('page-title', 'Distribution Reports & Analytics')

@section('content')
<style>
    .filter-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 20px; margin-bottom: 24px; }
    .input-field { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 14px; transition: all 0.3s ease; }
    .input-field:focus { background: #ffffff; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); outline: none; }
    .btn-filter { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 12px 24px; border: none; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; }
    .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); }
    .btn-reset { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 12px 24px; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
    .btn-reset:hover { background: #e2e8f0; }
    .stat-card { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 20px; padding: 28px; color: white; box-shadow: 0 10px 40px -10px rgba(59, 130, 246, 0.4); transition: all 0.3s ease; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 50px -12px rgba(59, 130, 246, 0.5); }
    .stat-card.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 10px 40px -10px rgba(16, 185, 129, 0.4); }
    .stat-card.green:hover { box-shadow: 0 20px 50px -12px rgba(16, 185, 129, 0.5); }
    .stat-label { color: rgba(255,255,255,0.8); font-size: 14px; margin-bottom: 4px; }
    .stat-value { font-size: 32px; font-weight: 700; }
    .stat-sub { color: rgba(255,255,255,0.7); font-size: 12px; margin-top: 8px; }
    .chart-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 24px; }
    .chart-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; }
    .progress-bar { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); border-radius: 10px; height: 8px; overflow: hidden; }
    .progress-fill { background: linear-gradient(135deg, #10b981, #059669); height: 100%; border-radius: 10px; transition: width 0.5s ease; }
    .progress-fill.purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
    .tier-row { margin-bottom: 16px; }
    .tier-name { font-size: 14px; font-weight: 500; color: #1e293b; }
    .tier-count { font-size: 14px; color: #64748b; }
    .status-grid { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; }
    .status-item { background: linear-gradient(180deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; text-align: center; transition: all 0.3s ease; }
    .status-item:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -4px rgba(0,0,0,0.15); }
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 600; display: inline-block; margin-bottom: 8px; }
    .status-badge.pending { background: #fef3c7; color: #92400e; }
    .status-badge.approved { background: #dbeafe; color: #1e40af; }
    .status-badge.packaged { background: #ede9fe; color: #7c3aed; }
    .status-badge.dispatched { background: #e0e7ff; color: #4338ca; }
    .status-badge.delivered { background: #d1fae5; color: #065f46; }
    .status-badge.cancelled { background: #fee2e2; color: #991b1b; }
    .status-count { font-size: 28px; font-weight: 700; color: #1e293b; }
    .status-percent { font-size: 12px; color: #94a3b8; margin-top: 4px; }
    .table-container { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); overflow: hidden; }
    .table-header { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border-bottom: 1px solid #e2e8f0; }
    .table-header th { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px; text-align: left; }
    .table-row { border-bottom: 1px solid #f1f5f9; transition: all 0.2s ease; }
    .table-row:hover { background: linear-gradient(180deg, #f0fdf4, #dcfce7); }
    .table-cell { padding: 16px; color: #475569; font-size: 14px; }
    .mono-text { font-family: monospace; font-size: 13px; color: #1e293b; }
    .user-name { font-weight: 500; color: #1e293b; }
    .user-email { font-size: 12px; color: #94a3b8; }
    .value-text { font-weight: 600; color: #10b981; }
    .view-link { color: #10b981; font-weight: 500; text-decoration: none; }
    .view-link:hover { color: #059669; }
    .export-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 20px; margin-top: 24px; }
    .export-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .export-desc { font-size: 14px; color: #64748b; margin-bottom: 16px; }
    .btn-export { padding: 10px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; border: none; }
    .btn-print { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; }
    .btn-excel { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .btn-pdf { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
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
        <a href="{{ route('admin.reports.distributions') }}" class="btn-reset">Reset</a>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; margin-bottom: 32px;">
    <div class="stat-card">
        <p class="stat-label">Total Distributions</p>
        <p class="stat-value">{{ number_format($totalDistributions) }}</p>
        <p class="stat-sub">In selected period</p>
    </div>
    <div class="stat-card green">
        <p class="stat-label">Total Value Distributed</p>
        <p class="stat-value">{{ format_currency($totalValue) }}</p>
        <p class="stat-sub">Food packages distributed</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px;">
    <div class="chart-card">
        <h3 class="chart-title">Distribution by Status</h3>
        <canvas id="statusChart"></canvas>
    </div>

    <div class="chart-card">
        <h3 class="chart-title">Distribution by Membership Tier</h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($distributionsByTier as $tier)
            <div class="tier-row">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span class="tier-name">{{ $tier->tier->name }}</span>
                    <span class="tier-count">{{ $tier->count }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ ($tier->count / $totalDistributions) * 100 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="chart-card">
        <h3 class="chart-title">Distribution by Type</h3>
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @foreach($distributionsByType as $type)
            <div style="padding-bottom: 12px; border-bottom: 1px solid #f1f5f9;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 14px; font-weight: 500; color: #1e293b; text-transform: capitalize;">{{ str_replace('_', ' ', $type->distribution_type) }}</span>
                    <span class="status-badge packaged">{{ $type->count }}</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill purple" style="width: {{ ($type->count / $totalDistributions) * 100 }}%"></div>
                </div>
                <p style="font-size: 12px; color: #94a3b8; margin-top: 4px;">{{ number_format(($type->count / $totalDistributions) * 100, 1) }}% of total</p>
            </div>
            @endforeach
        </div>
    </div>
</div>

<div class="chart-card" style="margin-bottom: 24px;">
    <h3 class="chart-title">Distribution Status Breakdown</h3>
    <div class="status-grid">
        @foreach($distributionsByStatus as $status)
        <div class="status-item">
            <div><span class="status-badge {{ $status->status }}">{{ ucfirst($status->status) }}</span></div>
            <p class="status-count">{{ $status->count }}</p>
            <p class="status-percent">{{ number_format(($status->count / $totalDistributions) * 100, 1) }}%</p>
        </div>
        @endforeach
    </div>
</div>

<div class="chart-card" style="margin-bottom: 24px;">
    <h3 class="chart-title">Distribution Value by Tier</h3>
    <canvas id="tierValueChart"></canvas>
</div>

<div class="table-container">
    <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; background: linear-gradient(180deg, #f8fafc, #f1f5f9); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Recent Distributions (Last 25)</h3>
        <a href="{{ route('admin.distributions.index') }}" style="font-size: 14px; color: #10b981; text-decoration: none; font-weight: 500;">View All →</a>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <thead class="table-header">
            <tr>
                <th>Date</th>
                <th>Distribution #</th>
                <th>User</th>
                <th>Tier</th>
                <th>Type</th>
                <th style="text-align: right;">Value</th>
                <th style="text-align: center;">Status</th>
                <th style="text-align: center;">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $recentDistributions = \App\Models\FoodDistribution::with(['user', 'tier'])
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->latest()
                    ->take(25)
                    ->get();
            @endphp
            @forelse($recentDistributions as $dist)
            <tr class="table-row">
                <td class="table-cell" style="color: #64748b;">{{ $dist->created_at->format('M d, Y') }}</td>
                <td class="table-cell"><span class="mono-text">{{ $dist->distribution_number }}</span></td>
                <td class="table-cell">
                    <div class="user-name">{{ $dist->user->full_name }}</div>
                    <div class="user-email">{{ $dist->user->email }}</div>
                </td>
                <td class="table-cell">
                    <span class="status-badge" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">{{ $dist->tier->name }}</span>
                </td>
                <td class="table-cell" style="text-transform: capitalize;">{{ str_replace('_', ' ', $dist->distribution_type) }}</td>
                <td class="table-cell" style="text-align: right;"><span class="value-text">{{ format_currency($dist->package_value) }}</span></td>
                <td class="table-cell" style="text-align: center;">
                    <span class="status-badge {{ $dist->status }}">{{ ucfirst($dist->status) }}</span>
                </td>
                <td class="table-cell" style="text-align: center;">
                    <a href="{{ route('admin.distributions.show', $dist) }}" class="view-link">View</a>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align: center; padding: 32px; color: #94a3b8;">No distributions found</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="export-card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h4 class="export-title">Export Report</h4>
            <p class="export-desc">Download this report in various formats</p>
        </div>
        <div style="display: flex; gap: 12px;">
            <button onclick="window.print()" class="btn-export btn-print">Print</button>
            <a href="{{ route('admin.reports.distributions.export') }}" class="btn-export btn-excel">Export Excel</a>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, { type: 'doughnut', data: { labels: @json($distributionsByStatus->pluck('status')->map(function($s) { return ucfirst($s); })), datasets: [{ data: @json($distributionsByStatus->pluck('count')), backgroundColor: ['rgba(251, 191, 36, 0.8)', 'rgba(59, 130, 246, 0.8)', 'rgba(168, 85, 247, 0.8)', 'rgba(99, 102, 241, 0.8)', 'rgba(34, 197, 94, 0.8)', 'rgba(239, 68, 68, 0.8)'], borderWidth: 2 }] }, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });
const tierValueCtx = document.getElementById('tierValueChart').getContext('2d');
new Chart(tierValueCtx, { type: 'bar', data: { labels: @json($distributionsByTier->pluck('tier.name')), datasets: [{ label: 'Total Value (₦)', data: @json($distributionsByTier->pluck('total_value')), backgroundColor: 'rgba(16, 185, 129, 0.6)', borderColor: '#10b981', borderWidth: 2 }, { label: 'Count', data: @json($distributionsByTier->pluck('count')), backgroundColor: 'rgba(59, 130, 246, 0.6)', borderColor: '#3b82f6', borderWidth: 2, yAxisID: 'y1' }] }, options: { responsive: true, scales: { y: { ticks: { callback: function(v) { return '₦' + v.toLocaleString(); } } } } } });
</script>
@endpush
@endsection
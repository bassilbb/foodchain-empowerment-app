{{-- resources/views/admin/reports/payments.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Payment Reports')
@section('page-title', 'Payment Reports & Revenue Analytics')

@section('content')
<style>
    .filter-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 20px; margin-bottom: 24px; }
    .input-field { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; font-size: 14px; transition: all 0.3s ease; }
    .input-field:focus { background: #ffffff; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); outline: none; }
    .btn-filter { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 12px 24px; border: none; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; }
    .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); }
    .btn-reset { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 12px 24px; border: 1px solid #e2e8f0; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; }
    .btn-reset:hover { background: #e2e8f0; }
    .revenue-card { background: linear-gradient(135deg, #10b981 0%, #059669 100%); border-radius: 20px; padding: 28px; color: white; box-shadow: 0 10px 40px -10px rgba(16, 185, 129, 0.4); transition: all 0.3s ease; }
    .revenue-card:hover { transform: translateY(-4px); box-shadow: 0 20px 50px -12px rgba(16, 185, 129, 0.5); }
    .revenue-card.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); box-shadow: 0 10px 40px -10px rgba(59, 130, 246, 0.4); }
    .revenue-card.blue:hover { box-shadow: 0 20px 50px -12px rgba(59, 130, 246, 0.5); }
    .revenue-card.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); box-shadow: 0 10px 40px -10px rgba(139, 92, 246, 0.4); }
    .revenue-card.purple:hover { box-shadow: 0 20px 50px -12px rgba(139, 92, 246, 0.5); }
    .revenue-label { color: rgba(255,255,255,0.8); font-size: 14px; margin-bottom: 4px; }
    .revenue-value { font-size: 32px; font-weight: 700; }
    .status-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 20px; transition: all 0.3s ease; }
    .status-card:hover { transform: translateY(-2px); box-shadow: 0 12px 30px -8px rgba(0,0,0,0.15); }
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-successful { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .badge-pending { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
    .badge-failed { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
    .status-count { font-size: 24px; font-weight: 700; color: #1e293b; }
    .status-amount { font-size: 20px; font-weight: 600; color: #10b981; }
    .chart-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 24px; margin-bottom: 24px; }
    .chart-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; }
    .table-container { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); overflow: hidden; }
    .table-header { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border-bottom: 1px solid #e2e8f0; }
    .table-header th { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; padding: 16px; text-align: left; }
    .table-row { border-bottom: 1px solid #f1f5f9; transition: all 0.2s ease; }
    .table-row:hover { background: linear-gradient(180deg, #f0fdf4, #dcfce7); }
    .table-cell { padding: 16px; color: #475569; font-size: 14px; }
    .mono-text { font-family: monospace; font-size: 13px; }
    .amount-text { font-weight: 600; color: #10b981; }
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
        <a href="{{ route('admin.reports.payments') }}" class="btn-reset">Reset</a>
    </form>
</div>

<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px;">
    <div class="revenue-card">
        <p class="revenue-label">Total Revenue</p>
        <p class="revenue-value">{{ format_currency($totalRevenue) }}</p>
    </div>
    <div class="revenue-card blue">
        <p class="revenue-label">Membership Revenue</p>
        <p class="revenue-value">{{ format_currency($membershipRevenue) }}</p>
    </div>
    <div class="revenue-card purple">
        <p class="revenue-label">Starter Pack Revenue</p>
        <p class="revenue-value">{{ format_currency($starterPackRevenue) }}</p>
    </div>
</div>

<div class="chart-card">
    <h3 class="chart-title">Payment Status Breakdown</h3>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px;">
        @foreach($paymentsByStatus as $status)
        <div class="status-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <span style="font-size: 14px; font-weight: 500; color: #475569; text-transform: capitalize;">{{ $status->status }}</span>
                <span class="status-badge {{ $status->status === 'successful' ? 'badge-successful' : ($status->status === 'pending' ? 'badge-pending' : 'badge-failed') }}">{{ $status->count }}</span>
            </div>
            <p class="status-amount">{{ format_currency($status->total) }}</p>
        </div>
        @endforeach
    </div>
</div>

<div class="chart-card">
    <h3 class="chart-title">Daily Revenue Trend</h3>
    <canvas id="revenueChart"></canvas>
</div>

<div class="table-container">
    <div style="padding: 16px 24px; border-bottom: 1px solid #e2e8f0; background: linear-gradient(180deg, #f8fafc, #f1f5f9);">
        <h3 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">Recent Successful Transactions</h3>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <thead class="table-header">
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>User</th>
                <th>Type</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailyRevenue->take(15) as $day)
            <tr class="table-row">
                <td class="table-cell" style="color: #64748b;">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                <td class="table-cell mono-text">-</td>
                <td class="table-cell">{{ $day->count }} transactions</td>
                <td class="table-cell">Mixed</td>
                <td class="table-cell" style="text-align: right;"><span class="amount-text">{{ format_currency($day->total) }}</span></td>
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
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return '₦' + value.toLocaleString(); } } } } }
});
</script>
@endpush
@endsection
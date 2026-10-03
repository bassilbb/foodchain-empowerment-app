@extends('admin.layouts.app')

@section('title', 'Payouts Report')
@section('header', 'Payouts Report')
@section('subheader', 'Vendor payout pipeline and customer wallet movement.')

@section('content')
<div class="page-header">
    <div>
        <h1>Payouts &amp; Wallets</h1>
        <p>{{ format_currency($requestedAmount + $approvedAmount + $processingAmount) }} still in the payout pipeline.</p>
    </div>
    <div class="header-actions-btns">
        @foreach($ranges as $range)
            <a href="{{ route('admin.reports.payouts', ['days' => $range]) }}"
               class="btn {{ $days === $range ? 'btn-primary' : 'btn-secondary' }}">{{ $range }}d</a>
        @endforeach
    </div>
</div>

<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Requested</p>
        <p class="stat-value">{{ format_currency($requestedAmount) }}</p>
    </div>
    <div class="stat-card info">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Approved</p>
        <p class="stat-value">{{ format_currency($approvedAmount) }}</p>
    </div>
    <div class="stat-card accent">
        <div class="stat-icon indigo">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </div>
        <p class="stat-label">Processing</p>
        <p class="stat-value">{{ format_currency($processingAmount) }}</p>
    </div>
    <div class="stat-card success-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        </div>
        <p class="stat-label">Paid</p>
        <p class="stat-value">{{ format_currency($paidAmount) }}</p>
    </div>
</div>

<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Payouts by Status</h3>
            <p class="page-card-subtitle">Last {{ $days }} days</p>
        </div>
    </div>
    <div class="grid-2">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Count</th>
                    <th>Amount</th>
                    <th>Commission</th>
                </tr>
            </thead>
            <tbody>
                @forelse($byStatus as $row)
                <tr>
                    <td>
                        <span class="badge @if($row->status === 'paid') badge-success @elseif($row->status === 'rejected' || $row->status === 'cancelled') badge-error @else badge-warning @endif">
                            {{ ucfirst($row->status) }}
                        </span>
                    </td>
                    <td>{{ number_format($row->payouts) }}</td>
                    <td style="font-weight: 700; color: #059669;">{{ format_currency($row->amount) }}</td>
                    <td>{{ format_currency($row->commission) }}</td>
                </tr>
                @empty
                <tr><td colspan="4"><div class="empty-state">No payout activity in this period.</div></td></tr>
                @endforelse
            </tbody>
        </table>

        <div>
            @if($statusChart)
                <div class="chart-box">
                    <canvas id="payoutStatusChart"></canvas>
                </div>
            @else
                <div class="chart-box">
                    <div class="chart-empty">No payouts recorded in this period.</div>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="grid-2">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Top Vendors by Payout</h3>
                <p class="page-card-subtitle">Last {{ $days }} days</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Vendor</th>
                    <th>Payouts</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($topVendors as $row)
                <tr>
                    <td style="font-weight: 600;">{{ $row['vendor']->business_name ?? $row['vendor']->name ?? 'Deleted vendor' }}</td>
                    <td>{{ number_format($row['payouts']) }}</td>
                    <td style="font-weight: 700; color: #059669;">{{ format_currency($row['amount']) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No payouts in this period.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Customer Wallet Movement</h3>
                <p class="page-card-subtitle">Last {{ $days }} days</p>
            </div>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Direction</th>
                    <th>Transactions</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($walletByType as $row)
                <tr>
                    <td>
                        <span class="badge {{ $row->type === 'credit' ? 'badge-success' : 'badge-error' }}">
                            {{ ucfirst($row->type) }}
                        </span>
                    </td>
                    <td>{{ number_format($row->transactions) }}</td>
                    <td style="font-weight: 700;">{{ format_currency($row->amount) }}</td>
                </tr>
                @empty
                <tr><td colspan="3"><div class="empty-state">No wallet activity in this period.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="page-card" style="margin-top: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Balances Held</h3>
            <p class="page-card-subtitle">Total liability the platform is currently holding</p>
        </div>
    </div>
    <div class="stats-grid-3" style="margin-bottom: 1.5rem;">
        <div class="stat-card">
            <div class="stat-icon amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <p class="stat-label">Commission Owed</p>
            <p class="stat-value">{{ format_currency($commissionOwed) }}</p>
            <p class="stat-footnote">Unpaid vendor payouts</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon indigo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
            </div>
            <p class="stat-label">Vendor Wallets</p>
            <p class="stat-value">{{ format_currency($vendorWalletHeld) }}</p>
            <p class="stat-footnote">Credited to {{ number_format($vendorCount) }} vendors</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon sky">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
            </div>
            <p class="stat-label">Customer Wallets</p>
            <p class="stat-value">{{ format_currency($customerWalletHeld) }}</p>
            <p class="stat-footnote">Store credit across {{ number_format($customerWalletHolderCount) }} buyers</p>
        </div>
    </div>

    <div class="stat-card emphasis">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l9 3m-9 3l3 1m-3 1l9 3m0-6l3 1M3 6l3-1m12 1l-3-1m-9 0l9 3m0 0l3 1m-3 1l-3-1"></path></svg>
        </div>
        <p class="stat-label">Total Wallet Liability</p>
        <p class="stat-value">{{ format_currency($vendorWalletHeld + $customerWalletHeld) }}</p>
        <p class="stat-footnote">Vendor balances plus customer balances, excluding unpaid commission</p>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('payoutStatusChart');
    if (el) {
        const data = @json($statusChart);
        new Chart(el, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.status),
                datasets: [{
                    data: data.map(d => d.amount),
                    backgroundColor: ['#f59e0b', '#3b82f6', '#8b5cf6', '#10b981', '#ef4444', '#64748b'],
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });
    }
});
</script>
@endpush

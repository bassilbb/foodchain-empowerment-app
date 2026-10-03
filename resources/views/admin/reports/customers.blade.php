@extends('admin.layouts.app')

@section('title', 'Customers Report')
@section('header', 'Customers Report')
@section('subheader', 'Who buys from the store, how often, and what they are worth.')

@section('content')
<div class="page-header">
    <div>
        <h1>Customers</h1>
        <p>{{ number_format($periodBuyers) }} customer(s) placed orders in the last {{ $days }} days.</p>
    </div>
    <div class="header-actions-btns">
        @foreach($ranges as $range)
            <a href="{{ route('admin.reports.customers', ['days' => $range]) }}"
               class="btn {{ $days === $range ? 'btn-primary' : 'btn-secondary' }}">{{ $range }}d</a>
        @endforeach
    </div>
</div>

<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card accent">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 10-8 0v4m-3 0h14l1 12H4L5 11zm7 0v6"></path></svg>
        </div>
        <p class="stat-label">Buyers (All Time)</p>
        <p class="stat-value">{{ number_format($buyers) }}</p>
    </div>
    <div class="stat-card success-card">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        <p class="stat-label">Active Buyers ({{ $days }}d)</p>
        <p class="stat-value">{{ number_format($periodBuyers) }}</p>
    </div>
    <div class="stat-card info">
        <div class="stat-icon indigo">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        </div>
        <p class="stat-label">Repeat Buyer Rate</p>
        <p class="stat-value">{{ $repeatRate }}%</p>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Avg Lifetime Value</p>
        <p class="stat-value">{{ format_currency($avgLifetimeValue) }}</p>
    </div>
</div>

<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Top Spenders</h3>
            <p class="page-card-subtitle">Highest value customers in the selected period</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Customer</th>
                <th>State</th>
                <th>Orders</th>
                <th>Spend</th>
                <th>Last Order</th>
                <th>Wallet</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topSpenders as $row)
            <tr>
                <td>
                    <div style="font-weight: 600; color: #1e293b;">{{ trim($row->first_name.' '.$row->last_name) ?: 'Unnamed customer' }}</div>
                    <div style="font-size: 12px; color: #94a3b8;">{{ $row->email }}</div>
                </td>
                <td>{{ $row->state ?? '—' }}</td>
                <td>{{ number_format($row->period_orders) }}</td>
                <td style="font-weight: 700; color: #059669;">{{ format_currency($row->period_spend) }}</td>
                <td>{{ \Illuminate\Support\Carbon::parse($row->last_order_at)->diffForHumans() }}</td>
                <td>{{ format_currency($row->wallet_balance) }}</td>
            </tr>
            @empty
            <tr><td colspan="6"><div class="empty-state">No purchases in this period.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="grid-2" style="margin-bottom: 2rem;">
    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Customer Base</h3>
                <p class="page-card-subtitle">Registered accounts vs actual buyers</p>
            </div>
        </div>
        <table class="data-table">
            <tbody>
                <tr>
                    <td>Registered accounts that have ordered</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($lifetimeBuyers) }}</td>
                </tr>
                <tr>
                    <td>Registered accounts with no orders</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($registeredNoOrders) }}</td>
                </tr>
                <tr>
                    <td>First-time buyers in the last {{ $days }} days</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($newBuyers) }}</td>
                </tr>
                <tr>
                    <td>Repeat buyers in the last {{ $days }} days</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($repeatBuyers) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <div>
                <h3 class="page-card-title">Outstanding Balances</h3>
                <p class="page-card-subtitle">Liability the store owes customers</p>
            </div>
        </div>
        <table class="data-table">
            <tbody>
                <tr>
                    <td>Customer wallet balances</td>
                    <td style="text-align: right; font-weight: 700; color: #059669;">{{ format_currency($walletLiability) }}</td>
                </tr>
                <tr>
                    <td>Loyalty points outstanding</td>
                    <td style="text-align: right; font-weight: 700;">{{ number_format($loyaltyOutstanding) }} pts</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Buyers by Delivery State</h3>
            <p class="page-card-subtitle">Where the store's customers are</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>State</th>
                <th>Buyers</th>
                <th>Revenue</th>
            </tr>
        </thead>
        <tbody>
            @forelse($byState as $row)
            @php $share = $periodBuyers > 0 ? ($row->buyers / $periodBuyers) * 100 : 0; @endphp
            <tr>
                <td style="font-weight: 600;">{{ $row->delivery_state }}</td>
                <td>{{ number_format($row->buyers) }}</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="progress-bar" style="flex: 1; min-width: 80px;">
                            <div class="progress-fill emerald" style="width: {{ $share }}%;"></div>
                        </div>
                        <span style="font-size: 12px; color: #64748b; white-space: nowrap;">{{ format_currency($row->revenue) }}</span>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="3"><div class="empty-state">No delivery data yet.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

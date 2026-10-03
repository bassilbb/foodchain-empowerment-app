@extends('admin.layouts.app')

@section('title', 'Vendors Report')
@section('header', 'Vendors Report')
@section('subheader', 'Who supplies the store, what they sell, and what is owed to them.')

@section('content')
<div class="page-header">
    <div>
        <h1>Vendors</h1>
        <p>{{ number_format($approvedCount) }} approved, {{ number_format($pendingCount) }} awaiting review.</p>
    </div>
    <div class="header-actions-btns">
        @foreach($ranges as $range)
            <a href="{{ route('admin.reports.vendors', ['days' => $range]) }}"
               class="btn {{ $days === $range ? 'btn-primary' : 'btn-secondary' }}">{{ $range }}d</a>
        @endforeach
    </div>
</div>

<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card success-card">
        <div class="stat-icon emerald">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m16 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
        </div>
        <p class="stat-label">Approved Vendors</p>
        <p class="stat-value">{{ number_format($approvedCount) }}</p>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon amber">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Pending Approval</p>
        <p class="stat-value">{{ number_format($pendingCount) }}</p>
    </div>
    <div class="stat-card danger">
        <div class="stat-icon red">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
        </div>
        <p class="stat-label">Suspended</p>
        <p class="stat-value">{{ number_format($suspendedCount) }}</p>
    </div>
    <div class="stat-card accent">
        <div class="stat-icon sky">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        </div>
        <p class="stat-label">Vendor Wallets Held</p>
        <p class="stat-value">{{ format_currency($walletHeld) }}</p>
    </div>
</div>

<div class="page-card" style="margin-bottom: 2rem;">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Vendor Performance</h3>
            <p class="page-card-subtitle">Ranked by revenue in the last {{ $days }} days</p>
        </div>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Vendor</th>
                <th>Status</th>
                <th>Units Sold</th>
                <th>Revenue</th>
                <th>Share</th>
            </tr>
        </thead>
        <tbody>
            @forelse($leaders as $row)
            @php $share = $leaders->sum('revenue') > 0 ? ($row->revenue / $leaders->sum('revenue')) * 100 : 0; @endphp
            <tr>
                <td>
                    <a href="{{ route('admin.marketplace.vendors.show', $row->id) }}" class="action-link" style="font-weight: 600; color: #1e293b;">
                        {{ $row->business_name ?? $row->name }}
                    </a>
                </td>
                <td>
                    <span class="badge @if($row->status === 'approved') badge-success @elseif($row->status === 'pending') badge-warning @else badge-error @endif">
                        {{ ucfirst($row->status) }}
                    </span>
                </td>
                <td>{{ number_format($row->units) }}</td>
                <td style="font-weight: 700; color: #059669;">{{ format_currency($row->revenue) }}</td>
                <td>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="progress-bar" style="flex: 1; min-width: 80px;">
                            <div class="progress-fill emerald" style="width: {{ $share }}%;"></div>
                        </div>
                        <span style="font-size: 12px; color: #64748b; white-space: nowrap;">{{ number_format($share, 0) }}%</span>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5"><div class="empty-state">No vendor sales in this period.</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Commission Position</h3>
            <p class="page-card-subtitle">Money the store still owes vendors</p>
        </div>
    </div>
    <div class="stats-grid">
        <div class="stat-card">
            <p class="stat-label">Owed (unpaid payouts)</p>
            <p class="stat-value">{{ format_currency($commissionOwed) }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Paid to Date</p>
            <p class="stat-value">{{ format_currency($commissionPaid) }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Held in Vendor Wallets</p>
            <p class="stat-value">{{ format_currency($walletHeld) }}</p>
        </div>
    </div>
</div>
@endsection

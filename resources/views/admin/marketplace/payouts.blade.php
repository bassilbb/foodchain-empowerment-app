@extends('admin.layouts.app')

@section('title', 'Vendor Payouts')
@section('header', 'Vendor Payouts')
@section('subheader', 'Review, approve, process and settle vendor payout requests.')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div>
        <h1>Payouts</h1>
        <p>{{ number_format($payouts->total()) }} payout request(s).</p>
    </div>
</div>

<!-- Summary Cards -->
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); margin-bottom: 20px;">
    @foreach($statuses as $key => $label)
    <div class="kpi-card">
        <div class="kpi-label">{{ $label }}</div>
        <div class="kpi-value">{{ number_format($counts[$key] ?? 0) }}</div>
        @if(($counts[$key] ?? 0) > 0)
        <div class="kpi-trend">₦{{ number_format($amounts[$key] ?? 0, 2) }}</div>
        @endif
    </div>
    @endforeach
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.marketplace.payouts.index') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="flex: 1; min-width: 220px;">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" class="form-input" placeholder="Search by vendor name or business name...">
        </div>
        <div style="min-width: 160px;">
            <label class="form-label">Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending (need action)</option>
                @foreach($statuses as $key => $label)
                <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.marketplace.payouts.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Reference</th>
                <th>Vendor</th>
                <th>Amount</th>
                <th>Commission</th>
                <th>Account</th>
                <th>Requested</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payouts as $payout)
            <tr>
                <td>
                    <a href="{{ route('admin.marketplace.payouts.show', $payout) }}" class="action-link">{{ $payout->reference }}</a>
                </td>
                <td>
                    <div style="font-weight: 600; color: #1e293b;">{{ $payout->vendor->business_name ?? $payout->vendor->name }}</div>
                    <div style="font-size: 12px; color: #94a3b8;">{{ $payout->vendor->email }}</div>
                </td>
                <td style="font-weight: 600; color: #1e293b;">₦{{ number_format((float) $payout->amount, 2) }}</td>
                <td style="font-size: 13px; color: #475569;">₦{{ number_format((float) $payout->commission_amount, 2) }} <span style="color: #94a3b8;">({{ number_format((float) $payout->commission_rate, 1) }}%)</span></td>
                <td>
                    <div style="font-size: 13px; color: #475569;">{{ $payout->bank_name }}</div>
                    <div style="font-size: 12px; color: #94a3b8;">{{ $payout->account_name }} · {{ $payout->account_number }}</div>
                </td>
                <td style="font-size: 13px; color: #475569;">{{ optional($payout->requested_at)->format('M j, Y g:i A') }}</td>
                <td>
                    <span class="badge
                        @if($payout->status === 'paid') badge-success
                        @elseif($payout->status === 'requested') badge-warning
                        @elseif($payout->status === 'approved') badge-primary
                        @elseif($payout->status === 'processing') badge-primary
                        @else badge-error @endif">{{ ucfirst($payout->status) }}</span>
                </td>
                <td style="white-space: nowrap;">
                    <a href="{{ route('admin.marketplace.payouts.show', $payout) }}" class="action-link">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8"><div class="empty-state">No payout requests found</div></td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $payouts->links() }}
</div>
@endsection
@extends('admin.layouts.app')

@section('title', 'Marketplace Wallet Transactions')
@section('header', 'Marketplace Wallet Transactions')
@section('subheader', 'Audit trail of wallet credits, debits and refunds on the marketplace.')

@section('content')
<div class="page-header">
    <div>
        <h1>Wallet Transactions</h1>
        <p>{{ number_format($transactions->total()) }} transaction(s).</p>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <form method="GET" action="{{ route('admin.marketplace.wallet-transactions.index') }}" style="display: flex; gap: 16px; flex-wrap: wrap; align-items: flex-end;">
        <div style="min-width: 160px;">
            <label class="form-label">Type</label>
            <select name="type" class="form-select">
                <option value="">All</option>
                <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>Credit</option>
                <option value="debit" {{ request('type') === 'debit' ? 'selected' : '' }}>Debit</option>
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.marketplace.wallet-transactions.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>
</div>

<div class="page-card">
    <table class="data-table">
        <thead>
            <tr>
                <th>Reference</th>
                <th>User</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Balance After</th>
                <th>Description</th>
                <th>Order</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaction)
            <tr>
                <td style="font-family: monospace; font-size: 13px; color: #64748b;">{{ $transaction->reference }}</td>
                <td style="white-space: nowrap;">
                    <div style="font-weight: 600; color: #475569;">{{ $transaction->user?->name ?? 'Unknown' }}</div>
                    <div style="font-size: 12px; color: #94a3b8;">{{ $transaction->user?->email ?? '' }}</div>
                </td>
                <td>
                    <span class="badge {{ $transaction->type === 'credit' ? 'badge-success' : 'badge-error' }}">{{ ucfirst($transaction->type) }}</span>
                </td>
                <td style="font-weight: 700; {{ $transaction->type === 'credit' ? 'color: #059669;' : 'color: #dc2626;' }} white-space: nowrap;">
                    {{ $transaction->type === 'credit' ? '+' : '-' }}{{ format_currency($transaction->amount) }}
                </td>
                <td style="white-space: nowrap;">{{ format_currency($transaction->balance_after) }}</td>
                <td style="color: #475569; font-size: 13px; max-width: 260px;">{{ $transaction->description }}</td>
                <td>
                    @if($transaction->order)
                    <a href="{{ route('admin.marketplace.orders.show', $transaction->order) }}" class="action-link">{{ $transaction->order->order_number }}</a>
                    @else
                    <span style="color: #94a3b8;">—</span>
                    @endif
                </td>
                <td style="white-space: nowrap; font-size: 13px; color: #64748b;">{{ $transaction->created_at->format('M d, Y h:i A') }}</td>
            </tr>
            @empty
            <tr><td colspan="8"><div class="empty-state">No wallet transactions found</div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="pagination">
    {{ $transactions->links() }}
</div>
@endsection

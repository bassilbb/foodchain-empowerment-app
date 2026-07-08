@extends('layouts.user')
@section('title', 'Transactions')
@section('header', 'Transactions')
@section('subheader', 'All account and wallet transactions')

@section('content')
<div style="margin-bottom: 1.5rem; display: flex; gap: 0.75rem; flex-wrap: wrap;">
    <a href="{{ route('user.accounts.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Accounts
    </a>
    <a href="{{ route('user.accounts.wallet') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        My Wallet
    </a>
</div>

<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">{{ $account ? $account->account_name . ' ' : '' }}Transactions</h3>
            <p class="page-card-subtitle">Complete transaction history</p>
        </div>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Account</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $txn)
                <tr>
                    <td style="color: #475569;">{{ $txn->created_at->format('M d, Y') }}</td>
                    <td>
                        @if($txn->account)
                        <span style="font-weight: 500; color: #1e293b;">{{ $txn->account->account_name }}</span>
                        @else
                        <span style="color: #94a3b8;">Wallet</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $txn->type === 'contribution' ? 'badge-success' : ($txn->type === 'top_up' ? 'badge-info' : ($txn->type === 'refund' ? 'badge-purple' : 'badge-error')) }}">
                            {{ ucfirst(str_replace('_', ' ', $txn->type)) }}
                        </span>
                    </td>
                    <td style="color: #475569;">{{ $txn->description ?? '-' }}</td>
                    <td style="text-align: right; font-weight: 600; color: {{ $txn->type === 'contribution' ? '#dc2626' : '#16a34a' }};">
                        {{ $txn->type === 'contribution' ? '-' : '+' }}₦{{ number_format($txn->amount, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 3rem;">
                        <div class="empty-icon" style="width: 80px; height: 80px; margin: 0 auto 1rem; border-radius: 20px; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 40px; height: 40px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        </div>
                        <p class="empty-title" style="color: #475569; font-weight: 600;">No transactions found</p>
                        <p class="empty-text" style="color: #94a3b8; font-size: 0.875rem;">Transactions will appear here once you start using your accounts.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9;">
        {{ $transactions->links() }}
    </div>
</div>
@endsection

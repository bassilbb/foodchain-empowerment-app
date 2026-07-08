@extends('layouts.user')
@section('title', 'My Accounts')
@section('header', 'My Accounts')
@section('subheader', 'Manage your linked participation accounts')

@section('content')
<div style="display: flex; justify-content: flex-end; margin-bottom: 1.5rem;">
    <a href="{{ route('user.accounts.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Create New Account
    </a>
</div>

@forelse($accounts as $account)
<div class="page-card" style="padding: 1.5rem; margin-bottom: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.25rem;">
                {{ substr($account->account_name, 0, 1) }}
            </div>
            <div>
                <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0;">{{ $account->account_name }}</h3>
                <p style="font-size: 0.875rem; color: #64748b; margin: 0.25rem 0 0;">{{ $account->plan_name }} · ₦{{ number_format($account->weekly_contribution_amount, 2) }}/wk</p>
            </div>
        </div>
        <div style="display: flex; align-items: center; gap: 1.5rem;">
            <div style="text-align: right;">
                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Contributed</p>
                <p style="font-size: 1.125rem; font-weight: 700; color: #065f46; margin: 0;">₦{{ number_format($account->total_contributions, 2) }}</p>
            </div>
            <div style="text-align: right;">
                <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Available</p>
                <p style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0;">₦{{ number_format($account->available_balance, 2) }}</p>
            </div>
            <span class="badge {{ $account->status === 'active' ? 'badge-success' : ($account->status === 'paused' ? 'badge-warning' : 'badge-error') }}">{{ ucfirst($account->status) }}</span>
            <a href="{{ route('user.accounts.show', $account->id) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">View</a>
        </div>
    </div>
</div>
@empty
<div class="page-card">
    <div class="empty-state" style="padding: 3rem;">
        <div class="empty-icon" style="width: 80px; height: 80px; margin: 0 auto 1rem; border-radius: 20px; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 40px; height: 40px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
        </div>
        <p class="empty-title" style="color: #475569; font-weight: 600;">No accounts yet</p>
        <p class="empty-text" style="color: #94a3b8; font-size: 0.875rem;">Create your first participation account to get started.</p>
        <a href="{{ route('user.accounts.create') }}" class="btn btn-primary" style="margin-top: 1rem;">Create Account</a>
    </div>
</div>
@endforelse

<div style="margin-top: 1.5rem;">
    <a href="{{ route('user.accounts.wallet') }}" class="btn btn-secondary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
        View Wallet
    </a>
    <a href="{{ route('user.accounts.transactions') }}" class="btn btn-secondary" style="margin-left: 0.5rem;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
        All Transactions
    </a>
</div>
@endsection

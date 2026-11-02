@extends('layouts.user')
@section('title', $account->account_name)
@section('header', $account->account_name)
@section('subheader', 'Participation account details and activity')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('user.accounts.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Accounts
    </a>
</div>

<!-- Account Overview Card -->
<div class="page-card" style="padding: 1.5rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #f0fdf4 0%, #ecfeff 100%);">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.5rem;">
                {{ substr($account->account_name, 0, 1) }}
            </div>
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0;">{{ $account->account_name }}</h2>
                <p style="color: #64748b; margin: 0.25rem 0 0;">{{ $account->plan_name }}</p>
                <span class="badge {{ $account->status === 'active' ? 'badge-success' : ($account->status === 'paused' ? 'badge-warning' : 'badge-error') }}" style="margin-top: 0.5rem;">{{ ucfirst($account->status) }}</span>
            </div>
        </div>
        <div style="text-align: right;">
            <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Available Balance</p>
            <p style="font-size: 2rem; font-weight: 700; color: #065f46; margin: 0;">₦{{ number_format($account->available_balance, 2) }}</p>
        </div>
    </div>
</div>

@php
    $clearance = $account->clearanceRequest;
    $isMatured = $account->isMatured();
@endphp

@if ($isMatured && $account->status !== 'completed')
    <div class="page-card" style="padding: 1.5rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%); border: 1px solid #fde68a;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: #fbbf24; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 24px; height: 24px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #92400e; margin: 0;">Account Matured!</h3>
                    <p style="color: #b45309; margin: 0.25rem 0 0; font-size: 0.875rem;">
                        @if (!$clearance || $clearance->status === 'pending_payment')
                            Your savings plan is complete. Pay the ₦3,000 clearance fee to proceed with admin review.
                        @elseif ($clearance->status === 'pending_approval')
                            Your clearance fee has been paid. Awaiting admin approval.
                        @elseif ($clearance->status === 'rejected')
                            Your clearance request was rejected: {{ $clearance->admin_notes }}
                        @endif
                    </p>
                </div>
            </div>
            <div>
                @if (!$clearance || $clearance->status === 'pending_payment')
                    <form action="{{ route('user.accounts.clearance-pay', $account->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-size: 0.875rem;">
                            Pay Clearance Fee (₦3,000)
                        </button>
                    </form>
                @elseif ($clearance->status === 'pending_approval')
                    <span class="badge badge-warning" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Pending Approval</span>
                @elseif ($clearance->status === 'approved')
                    <span class="badge badge-success" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Approved</span>
                @endif
            </div>
        </div>
    </div>
@endif

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #f0fdf4 100%);">
        <div class="stat-icon" style="background: #d1fae5;">
            <svg style="width: 24px; height: 24px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Total Contributions</p>
        <p class="stat-value">₦{{ number_format($account->total_contributions, 2) }}</p>
    </div>
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #fefce8 100%);">
        <div class="stat-icon" style="background: #fef3c7;">
            <svg style="width: 24px; height: 24px; color: #d97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        <p class="stat-label">Weeks Completed</p>
        <p class="stat-value">{{ $account->current_tariff_weeks }}</p>
    </div>
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #f0f9ff 100%);">
        <div class="stat-icon" style="background: #dbeafe;">
            <svg style="width: 24px; height: 24px; color: #2563eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
        </div>
        <p class="stat-label">Progress</p>
        <p class="stat-value">{{ $account->plan_duration_weeks > 0 ? round(($account->current_tariff_weeks / $account->plan_duration_weeks) * 100) : 0 }}%</p>
    </div>
    <div class="stat-card" style="background: linear-gradient(145deg, #ffffff 0%, #f5f3ff 100%);">
        <div class="stat-icon" style="background: #f3e8ff;">
            <svg style="width: 24px; height: 24px; color: #9333ea;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="stat-label">Weekly Amount</p>
        <p class="stat-value">₦{{ number_format($account->weekly_contribution_amount, 2) }}</p>
    </div>
</div>

<!-- Recent Transactions -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Recent Transactions</h3>
            <p class="page-card-subtitle">Activity for this account</p>
        </div>
        <a href="{{ route('user.accounts.account-transactions', $account->id) }}" style="color: #10b981; font-weight: 600; font-size: 0.875rem; text-decoration: none;">View all →</a>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
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
                        <span class="badge {{ $txn->type === 'contribution' ? 'badge-success' : ($txn->type === 'top_up' ? 'badge-info' : ($txn->type === 'refund' ? 'badge-purple' : 'badge-error')) }}">
                            {{ ucfirst(str_replace('_', ' ', $txn->type)) }}
                        </span>
                    </td>
                    <td style="color: #475569;">{{ $txn->description ?? '-' }}</td>
                    <td style="text-align: right; font-weight: 600; color: #16a34a;">+₦{{ number_format($txn->amount, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem;">
                        <p class="empty-title" style="color: #475569; font-weight: 600;">No transactions yet</p>
                        <p class="empty-text" style="color: #94a3b8; font-size: 0.875rem;">Contributions will appear here once processed.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

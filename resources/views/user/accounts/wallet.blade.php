@extends('layouts.user')
@section('title', 'Wallet')
@section('header', 'My Wallet')
@section('subheader', 'Manage your wallet balance')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('user.accounts.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Accounts
    </a>
</div>

<!-- Wallet Balance Card -->
<div class="page-card" style="padding: 2rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #065f46 0%, #059669 50%, #10b981 100%);">
    <div style="text-align: center;">
        <p style="color: rgba(255,255,255,0.8); font-size: 0.875rem; margin: 0;">Available Wallet Balance</p>
        <p style="font-size: 3rem; font-weight: 800; color: white; margin: 0.5rem 0;">₦{{ number_format($balance, 2) }}</p>
        <p style="color: rgba(255,255,255,0.7); font-size: 0.75rem; margin: 0;">Use this balance to fund your participation accounts</p>
    </div>
</div>

<!-- Top Up Card -->
<div class="page-card" style="padding: 1.5rem; margin-bottom: 1.5rem;">
    <div class="page-card-header" style="padding: 0 0 1rem 0;">
        <h3 class="page-card-title">Top Up Wallet</h3>
        <p class="page-card-subtitle">Transfer to our bank account and upload payment proof</p>
    </div>

    @if(count($bankAccounts) > 0)
    @php $defaultAccount = $bankAccounts->firstWhere('is_default', true) ?? $bankAccounts->first(); @endphp

    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
        <p style="font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.75rem;">Bank Account Details</p>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
            <div>
                <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">Bank</p>
                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $defaultAccount->bank_name }}</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">Account Number</p>
                <p style="font-size: 1.125rem; font-weight: 700; color: #059669; margin: 0; letter-spacing: 0.1em;">{{ $defaultAccount->account_number }}</p>
            </div>
            <div style="grid-column: 1 / -1;">
                <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">Account Name</p>
                <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $defaultAccount->account_name }}</p>
            </div>
            @if($defaultAccount->instructions)
            <div style="grid-column: 1 / -1; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 0.75rem;">
                <p style="font-size: 0.75rem; color: #92400e; margin: 0;">{{ $defaultAccount->instructions }}</p>
            </div>
            @endif
        </div>
    </div>

    <form action="{{ route('user.accounts.top-up') }}" method="POST" enctype="multipart/form-data" style="max-width: 500px;">
        @csrf

        <div class="form-group">
            <label class="form-label">Amount (₦)</label>
            <input type="number" name="amount" required class="form-input" placeholder="Enter amount" min="100" step="100">
        </div>

        <div class="form-group">
            <label class="form-label">Select Bank Account</label>
            <select name="bank_account_id" required class="form-select">
                <option value="">-- Select Bank --</option>
                @foreach($bankAccounts as $account)
                <option value="{{ $account->id }}" @if($account->id === $defaultAccount->id) selected @endif>
                    {{ $account->bank_name }} - {{ $account->account_number }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label class="form-label">Your Name (as on bank account)</label>
            <input type="text" name="sender_name" required class="form-input" value="{{ $user->full_name ?? '' }}">
        </div>

        <div class="form-group">
            <label class="form-label">Transfer Date</label>
            <input type="date" name="transfer_date" required class="form-input" value="{{ date('Y-m-d') }}">
        </div>

        <div class="form-group">
            <label class="form-label">Payment Proof (Screenshot)</label>
            <input type="file" name="proof_image" required accept="image/*" class="form-input">
            <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Upload a screenshot of your transfer confirmation. Max 2MB (jpeg, png, jpg, gif).</p>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Submit Payment for Verification
        </button>
    </form>
    @else
    <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 1.5rem; text-align: center;">
        <p style="font-weight: 600; color: #92400e;">No bank accounts available</p>
        <p style="font-size: 0.875rem; color: #a16207;">Please contact support for bank account details.</p>
    </div>
    @endif

    <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 1rem;">Your payment will be verified by our team and your wallet credited within 24 hours.</p>
</div>

<!-- Wallet Transactions -->
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Wallet Transactions</h3>
            <p class="page-card-subtitle">Your wallet activity</p>
        </div>
        <a href="{{ route('user.accounts.transactions') }}" style="color: #10b981; font-weight: 600; font-size: 0.875rem; text-decoration: none;">View all →</a>
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
                        <span class="badge {{ $txn->type === 'top_up' ? 'badge-success' : ($txn->type === 'contribution' ? 'badge-info' : ($txn->type === 'refund' ? 'badge-purple' : 'badge-error')) }}">
                            {{ ucfirst(str_replace('_', ' ', $txn->type)) }}
                        </span>
                    </td>
                    <td style="color: #475569;">{{ $txn->description ?? ($txn->account ? 'Contribution to ' . $txn->account->account_name : '-') }}</td>
                    <td style="text-align: right; font-weight: 600; color: {{ $txn->type === 'contribution' ? '#dc2626' : '#16a34a' }};">
                        {{ $txn->type === 'contribution' ? '-' : '+' }}₦{{ number_format($txn->amount, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center; padding: 3rem;">
                        <p class="empty-title" style="color: #475569; font-weight: 600;">No transactions yet</p>
                        <p class="empty-text" style="color: #94a3b8; font-size: 0.875rem;">Top up your wallet to get started.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

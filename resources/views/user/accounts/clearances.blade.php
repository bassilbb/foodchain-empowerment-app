@extends('layouts.user')
@section('title', 'Account Clearances')
@section('header', 'Account Clearances')
@section('subheader', 'Complete clearance for your matured savings plans')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{!! session('error') !!}</div>
@endif
@if(session('info'))
<div class="alert alert-warning">{{ session('info') }}</div>
@endif
@if($accounts->isEmpty())
<div class="page-card">
    <div class="empty-state" style="padding: 3rem; text-align: center;">
        <div style="width: 80px; height: 80px; margin: 0 auto 1rem; border-radius: 20px; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
            <svg style="width: 40px; height: 40px; color: #94a3b8;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        </div>
        <p class="empty-title" style="color: #475569; font-weight: 600;">No accounts due for clearance</p>
        <p class="empty-text" style="color: #94a3b8; font-size: 0.875rem;">Your savings plans are still in progress. Once a plan matures, you can complete the clearance here.</p>
        <a href="{{ route('user.accounts.index') }}" class="btn btn-primary" style="margin-top: 1rem;">View My Accounts</a>
    </div>
</div>
@else

<div class="page-card" style="padding: 1.5rem; margin-bottom: 1.5rem; background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%); border: 1px solid #fde68a;">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <div style="width: 48px; height: 48px; border-radius: 12px; background: #fbbf24; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
            <svg style="width: 24px; height: 24px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <div>
            <h3 style="font-size: 1rem; font-weight: 700; color: #92400e; margin: 0;">Accounts Due for Clearance</h3>
            <p style="font-size: 0.875rem; color: #b45309; margin: 0.25rem 0 0;">You have <strong>{{ $accounts->count() }}</strong> {{ Str::plural('account', $accounts->count()) }} that {{ $accounts->count() === 1 ? 'has' : 'have' }} reached maturity. Select accounts below and pay the ₦3,000 clearance fee per account from your wallet. <strong>Wallet balance: ₦{{ number_format($user->wallet_balance, 2) }}</strong></p>
        </div>
    </div>
</div>

<form id="batchClearanceForm" action="{{ route('user.accounts.clearance-batch-pay') }}" method="POST">
    @csrf
    <div id="accountsList">
        @foreach($accounts as $account)
        @php
            $clearance = $account->clearanceRequest;
            $canSelect = !$clearance || $clearance->status === 'pending_payment';
        @endphp
        <div class="page-card account-card" data-account-id="{{ $account->id }}" style="padding: 1.5rem; margin-bottom: 0.75rem; {{ $canSelect ? 'cursor: pointer;' : 'opacity: 0.7;' }} position: relative;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                @if($canSelect)
                <div style="flex-shrink: 0;">
                    <input type="checkbox" name="account_ids[]" value="{{ $account->id }}" id="acc_{{ $account->id }}" class="account-checkbox" style="width: 20px; height: 20px; accent-color: #10b981; cursor: pointer;">
                </div>
                @endif
                <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.25rem; flex-shrink: 0;">
                    {{ substr($account->account_name, 0, 1) }}
                </div>
                <div style="flex: 1; min-width: 150px;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0;">{{ $account->account_name }}</h3>
                    <p style="font-size: 0.875rem; color: #64748b; margin: 0.25rem 0 0;">
                        {{ $account->plan_name }} · {{ $account->plan_duration_weeks }} weeks
                        @if($clearance)
                        <span class="badge
                            @if($clearance->status === 'pending_payment') badge-warning
                            @elseif($clearance->status === 'pending_approval') badge-info
                            @elseif($clearance->status === 'approved') badge-success
                            @else badge-error
                            @endif" style="margin-left: 0.5rem;">
                            {{ ucfirst(str_replace('_', ' ', $clearance->status)) }}
                        </span>
                        @endif
                    </p>
                </div>
                <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                    <div style="text-align: right;">
                        <p style="font-size: 0.75rem; color: #475569; margin: 0;">Weeks</p>
                        <p style="font-size: 1rem; font-weight: 700; color: #065f46; margin: 0;">{{ $account->current_tariff_weeks }}/{{ $account->plan_duration_weeks }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 0.75rem; color: #475569; margin: 0;">Balance</p>
                        <p style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">₦{{ number_format($account->available_balance, 2) }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 0.75rem; color: #475569; margin: 0;">Fee</p>
                        <p style="font-size: 1rem; font-weight: 700; color: #dc2626; margin: 0;">₦3,000</p>
                    </div>
                    <div>
                        @if(!$clearance || $clearance->status === 'pending_payment')
                        <a href="{{ route('user.accounts.show', $account->id) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">View</a>
                        @elseif($clearance->status === 'pending_approval')
                        <span class="badge badge-info" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Awaiting Approval</span>
                        @elseif($clearance->status === 'approved')
                        <span class="badge badge-success" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Cleared</span>
                        @elseif($clearance->status === 'rejected')
                        <span class="badge badge-error" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Rejected</span>
                        @endif
                    </div>
                </div>
            </div>
            @if($clearance && $clearance->status === 'rejected' && $clearance->admin_notes)
            <div style="margin-top: 0.75rem; padding: 0.75rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; font-size: 0.875rem; color: #991b1b;">
                <strong>Admin Notes:</strong> {{ $clearance->admin_notes }}
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div id="batchBar" style="display: none; position: sticky; bottom: 1rem; background: white; border: 2px solid #10b981; border-radius: 16px; padding: 1rem 1.5rem; margin-top: 1.5rem; box-shadow: 0 10px 40px rgba(16, 185, 129, 0.2); display: none; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <div>
            <p style="font-size: 0.875rem; color: #475569; margin: 0;">
                <strong id="selectedCount">0</strong> account(s) selected
            </p>
            <p style="font-size: 1.25rem; font-weight: 800; color: #059669; margin: 0;">
                Total: ₦<span id="totalAmount">0</span>
            </p>
        </div>
        <button type="submit" id="batchPayBtn" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem; font-weight: 700;" disabled>
            Pay from Wallet
        </button>
    </div>
</form>

@endif
@endsection

@push('scripts')
<script>
const FEE_PER_ACCOUNT = 3000;
const batchBar = document.getElementById('batchBar');
const selectedCountEl = document.getElementById('selectedCount');
const totalAmountEl = document.getElementById('totalAmount');
const batchPayBtn = document.getElementById('batchPayBtn');
const checkboxes = document.querySelectorAll('.account-checkbox');
const accountCards = document.querySelectorAll('.account-card');

function updateBatchBar() {
    const checked = document.querySelectorAll('.account-checkbox:checked');
    const count = checked.length;
    const total = count * FEE_PER_ACCOUNT;

    selectedCountEl.textContent = count;
    totalAmountEl.textContent = total.toLocaleString();

    if (count > 0) {
        batchBar.style.display = 'flex';
        batchPayBtn.disabled = false;
    } else {
        batchBar.style.display = 'none';
        batchPayBtn.disabled = true;
    }

    accountCards.forEach(card => {
        const cb = card.querySelector('.account-checkbox');
        if (cb) {
            card.style.borderColor = cb.checked ? '#10b981' : '#e2e8f0';
            card.style.background = cb.checked ? '#f0fdf4' : 'white';
        }
    });
}

checkboxes.forEach(cb => {
    cb.addEventListener('change', updateBatchBar);
});

accountCards.forEach(card => {
    const cb = card.querySelector('.account-checkbox');
    if (cb) {
        card.addEventListener('click', (e) => {
            if (e.target.type !== 'checkbox') {
                cb.checked = !cb.checked;
                updateBatchBar();
            }
        });
    }
});

document.getElementById('batchClearanceForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.account-checkbox:checked');
    if (checked.length === 0) {
        e.preventDefault();
        return;
    }
    batchPayBtn.disabled = true;
    batchPayBtn.textContent = 'Processing...';
});
</script>
@endpush

@push('styles')
<style>
.account-card {
    transition: all 0.2s ease;
    border: 2px solid #e2e8f0;
}
.account-card:hover {
    border-color: #94a3b8;
    box-shadow: 0 4px 12px rgba(0,0,0,0.06);
}
#batchBar {
    animation: slideUp 0.3s ease-out;
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
@endpush
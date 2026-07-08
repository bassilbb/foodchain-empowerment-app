@extends('admin.layouts.app')

@section('title', 'Bank Accounts')
@section('page-title', 'Bank Account Management')

@section('header', 'Bank Accounts')
@section('subheader', 'Manage payment bank accounts')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div class="page-card-header">
    <p style="margin: 0;">Manage bank accounts for manual payment transfers</p>
    <button onclick="document.getElementById('addAccountModal').style.display = 'block'" class="btn-primary">
        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Bank Account
    </button>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px;">
    @forelse($accounts as $account)
    <div class="page-card" style="padding: 24px; position: relative;">
        @if($account->is_default)
        <div style="position: absolute; top: 12px; right: 12px;">
            <span class="badge badge-primary">Default</span>
        </div>
        @endif

        <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 16px;">
            <div style="width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981, #059669); color: white;">
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <div>
                <h3 style="font-weight: 700; margin: 0;">{{ $account->bank_name }}</h3>
                <p style="font-size: 14px; color: #64748b; margin: 0;">{{ $account->account_type ?? 'Current' }}</p>
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <div style="margin-bottom: 12px;">
                <p style="font-size: 12px; color: #94a3b8; margin: 0 0 4px 0;">Account Number</p>
                <p style="font-family: monospace; font-weight: 600; margin: 0;">{{ $account->account_number }}</p>
            </div>
            <div>
                <p style="font-size: 12px; color: #94a3b8; margin: 0 0 4px 0;">Account Name</p>
                <p style="font-weight: 500; margin: 0;">{{ $account->account_name }}</p>
            </div>
        </div>

        @if($account->instructions)
        <div style="margin-bottom: 16px; padding: 12px; border-radius: 12px; background: #f8fafc;">
            <p style="font-size: 12px; color: #64748b; margin: 0 0 4px 0;">Instructions</p>
            <p style="font-size: 14px; color: #475569; margin: 0;">{{ $account->instructions }}</p>
        </div>
        @endif

        <div style="display: flex; gap: 8px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
            <span class="badge badge-{{ $account->is_active ? 'success' : 'dark' }}">
                {{ $account->is_active ? 'Active' : 'Inactive' }}
            </span>
            @if(!$account->is_default)
            <form method="POST" action="{{ route('admin.bank-accounts.update', $account) }}" style="margin-left: auto;">
                @csrf
                @method('PUT')
                <input type="hidden" name="is_default" value="1">
                <button type="submit" class="btn-secondary btn-sm" style="background: none; border: none; font-weight: 500; cursor: pointer;">Set as Default</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div style="grid-column: 1 / -1;">
        <div class="empty-state">
            <p>No bank accounts found. Add a bank account to receive manual payments.</p>
            <button onclick="document.getElementById('addAccountModal').style.display = 'block'" class="btn-primary" style="margin-top: 12px;">
                Add First Account
            </button>
        </div>
    </div>
    @endforelse
</div>

<div id="addAccountModal" style="display: none; position: fixed; inset: 0; z-index: 50; overflow-y: auto;">
    <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px;">
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7);" onclick="document.getElementById('addAccountModal').style.display = 'none'"></div>
        <div class="page-card" style="position: relative; width: 100%; max-width: 480px; padding: 24px;">
            <h3 class="page-card-title" style="margin: 0 0 20px 0;">Add Bank Account</h3>
            <form method="POST" action="{{ route('admin.bank-accounts.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" required class="form-input" placeholder="e.g., First Bank">
                </div>
                <div class="form-group">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" required class="form-input" placeholder="e.g., 1234567890">
                </div>
                <div class="form-group">
                    <label class="form-label">Account Name</label>
                    <input type="text" name="account_name" required class="form-input" placeholder="e.g., FoodChain Ltd">
                </div>
                <div class="form-group">
                    <label class="form-label">Account Type</label>
                    <select name="account_type" class="form-select">
                        <option value="current">Current</option>
                        <option value="savings">Savings</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Instructions (optional)</label>
                    <textarea name="instructions" rows="2" class="form-input" placeholder="Payment instructions..."></textarea>
                </div>
                <label style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px;">
                    <input type="checkbox" name="is_default" value="1" style="width: 18px; height: 18px; accent-color: #10b981;">
                    <span style="font-size: 14px; color: #64748b;">Set as default account</span>
                </label>
                <div style="display: flex; gap: 12px; padding-top: 16px;">
                    <button type="button" onclick="document.getElementById('addAccountModal').style.display = 'none'" class="btn-secondary" style="flex: 1;">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary" style="flex: 1;">
                        Add Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

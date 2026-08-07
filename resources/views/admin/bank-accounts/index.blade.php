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
            <div style="margin-left: auto; display: flex; gap: 6px;">
                <button type="button"
                    style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; padding: 6px 14px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 11px; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(59,130,246,0.3);"
                    onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''"
                    onclick="openEditModal({{ $account->id }}, '{{ addslashes($account->bank_name) }}', '{{ addslashes($account->account_number) }}', '{{ addslashes($account->account_name) }}', '{{ $account->account_type ?? 'current' }}', '{{ addslashes($account->instructions ?? '') }}', {{ $account->is_active ? 'true' : 'false' }}, {{ $account->is_default ? 'true' : 'false' }})">
                    Edit
                </button>
                <form method="POST" action="{{ route('admin.bank-accounts.destroy', $account) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        data-confirm='{"title":"Delete Bank Account?","text":"Are you sure you want to delete this bank account?","btnText":"Delete","btnClass":"confirm-danger","iconClass":"danger"}'
                        style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white; border: none; padding: 6px 14px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 11px; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(239,68,68,0.3);"
                        onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                        Delete
                    </button>
                </form>
                @if(!$account->is_default)
                <form method="POST" action="{{ route('admin.bank-accounts.update', $account) }}" style="display: inline;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="is_default" value="1">
                    <button type="submit"
                        style="background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; padding: 6px 14px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 11px; transition: all 0.2s ease; box-shadow: 0 2px 8px rgba(16,185,129,0.3);"
                        onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform=''">
                        Default
                    </button>
                </form>
                @endif
            </div>
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

<div id="editAccountModal" style="display: none; position: fixed; inset: 0; z-index: 50; overflow-y: auto;">
    <div style="display: flex; align-items: center; justify-content: center; min-height: 100vh; padding: 16px;">
        <div style="position: fixed; inset: 0; background: rgba(15, 23, 42, 0.7);" onclick="closeEditModal()"></div>
        <div class="page-card" style="position: relative; width: 100%; max-width: 480px; padding: 24px;">
            <h3 class="page-card-title" style="margin: 0 0 20px 0;">Edit Bank Account</h3>
            <form method="POST" action="" id="editAccountForm">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label class="form-label">Bank Name</label>
                    <input type="text" name="bank_name" id="edit_bank_name" required class="form-input" placeholder="e.g., First Bank">
                </div>
                <div class="form-group">
                    <label class="form-label">Account Number</label>
                    <input type="text" name="account_number" id="edit_account_number" required class="form-input" placeholder="e.g., 1234567890">
                </div>
                <div class="form-group">
                    <label class="form-label">Account Name</label>
                    <input type="text" name="account_name" id="edit_account_name" required class="form-input" placeholder="e.g., FoodChain Ltd">
                </div>
                <div class="form-group">
                    <label class="form-label">Account Type</label>
                    <select name="account_type" id="edit_account_type" class="form-select">
                        <option value="current">Current</option>
                        <option value="savings">Savings</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Instructions (optional)</label>
                    <textarea name="instructions" id="edit_instructions" rows="2" class="form-input" placeholder="Payment instructions..."></textarea>
                </div>
                <div style="display: flex; gap: 16px; margin-top: 12px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; color: #1e293b;">
                        <input type="checkbox" name="is_active" id="edit_is_active" value="1" style="width: 18px; height: 18px; accent-color: #10b981;">
                        Active
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; color: #1e293b;">
                        <input type="checkbox" name="is_default" id="edit_is_default" value="1" style="width: 18px; height: 18px; accent-color: #f59e0b;">
                        Default Account
                    </label>
                </div>
                <div style="display: flex; gap: 12px; padding-top: 16px;">
                    <button type="button" onclick="closeEditModal()" class="btn-secondary" style="flex: 1;">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary" style="flex: 1;">
                        Update Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openEditModal(id, bankName, accountNumber, accountName, accountType, instructions, isActive, isDefault) {
    document.getElementById('editAccountForm').action = '{{ route('admin.bank-accounts.update', '_id_') }}'.replace('_id_', id);
    document.getElementById('edit_bank_name').value = bankName;
    document.getElementById('edit_account_number').value = accountNumber;
    document.getElementById('edit_account_name').value = accountName;
    document.getElementById('edit_account_type').value = accountType;
    document.getElementById('edit_instructions').value = instructions;
    document.getElementById('edit_is_active').checked = isActive;
    document.getElementById('edit_is_default').checked = isDefault;
    document.getElementById('editAccountModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editAccountModal').style.display = 'none';
}
</script>
@endpush
@endsection

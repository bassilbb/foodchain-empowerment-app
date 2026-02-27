@extends('admin.layouts.app')

@section('title', 'Bank Accounts')
@section('page-title', 'Bank Account Management')

@section('header', 'Bank Accounts')
@section('subheader', 'Manage payment bank accounts')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <p class="text-slate-500">Manage bank accounts for manual payment transfers</p>
    </div>
    <button onclick="document.getElementById('addAccountModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transform hover:-translate-y-0.5 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add Bank Account
    </button>
</div>

<!-- Bank Accounts Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($accounts as $account)
    <div class="card-3d p-6 relative">
        @if($account->is_default)
        <div class="absolute top-3 right-3">
            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-700">Default</span>
        </div>
        @endif
        
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-slate-800">{{ $account->bank_name }}</h3>
                <p class="text-sm text-slate-500">{{ $account->account_type ?? 'Current' }}</p>
            </div>
        </div>

        <div class="space-y-2 mb-4">
            <div>
                <p class="text-xs text-slate-400">Account Number</p>
                <p class="font-mono font-semibold text-slate-800">{{ $account->account_number }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-400">Account Name</p>
                <p class="font-medium text-slate-800">{{ $account->account_name }}</p>
            </div>
        </div>

        @if($account->instructions)
        <div class="mb-4 p-3 rounded-lg bg-slate-50">
            <p class="text-xs text-slate-500">Instructions</p>
            <p class="text-sm text-slate-700">{{ $account->instructions }}</p>
        </div>
        @endif

        <div class="flex gap-2 pt-4 border-t border-slate-100">
            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $account->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                {{ $account->is_active ? 'Active' : 'Inactive' }}
            </span>
            @if(!$account->is_default)
            <form method="POST" action="{{ route('admin.bank-accounts.update', $account) }}" class="ml-auto">
                @csrf
                @method('PUT')
                <input type="hidden" name="is_default" value="1">
                <button type="submit" class="text-xs text-primary-600 hover:text-primary-700 font-medium">Set as Default</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full">
        <div class="card-3d p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-slate-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <h3 class="text-lg font-semibold text-slate-800 mb-2">No Bank Accounts</h3>
            <p class="text-slate-500 mb-4">Add a bank account to receive manual payments</p>
            <button onclick="document.getElementById('addAccountModal').classList.remove('hidden')" class="px-5 py-2.5 bg-primary-500 text-white font-medium rounded-xl hover:bg-primary-600 transition-colors">
                Add First Account
            </button>
        </div>
    </div>
    @endforelse
</div>

<!-- Add Account Modal -->
<div id="addAccountModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('addAccountModal').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Add Bank Account</h3>
            <form method="POST" action="{{ route('admin.bank-accounts.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Bank Name</label>
                    <input type="text" name="bank_name" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500" placeholder="e.g., First Bank">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Account Number</label>
                    <input type="text" name="account_number" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500" placeholder="e.g., 1234567890">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Account Name</label>
                    <input type="text" name="account_name" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500" placeholder="e.g., FoodChain Ltd">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Account Type</label>
                    <select name="account_type" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500">
                        <option value="current">Current</option>
                        <option value="savings">Savings</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Instructions (optional)</label>
                    <textarea name="instructions" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500" placeholder="Payment instructions..."></textarea>
                </div>
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="is_default" value="1" class="w-4 h-4 rounded border-slate-300 text-primary-500">
                    <span class="text-sm text-slate-600">Set as default account</span>
                </label>
                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="document.getElementById('addAccountModal').classList.add('hidden')" class="flex-1 px-4 py-3 bg-slate-100 text-slate-600 font-medium rounded-xl hover:bg-slate-200 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-primary-500 text-white font-medium rounded-xl hover:bg-primary-600 transition-colors">
                        Add Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Payment Details')
@section('page-title', 'Manual Payment Details')

@section('header', 'Payment Details')
@section('subheader', 'View and verify manual payment')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.manual-payments.index') }}" class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Payments
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Payment Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="card-3d p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-800">Payment Information</h3>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $manualPayment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $manualPayment->status === 'verified' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $manualPayment->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ ucfirst($manualPayment->status) }}
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Reference</p>
                    <p class="font-mono font-semibold text-slate-800">{{ $manualPayment->reference }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Type</p>
                    <p class="font-medium text-slate-800">{{ ucfirst(str_replace('_', ' ', $manualPayment->type)) }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Amount</p>
                    <p class="text-2xl font-bold text-slate-800">{{ format_currency($manualPayment->amount) }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Transfer Date</p>
                    <p class="font-medium text-slate-800">{{ $manualPayment->transfer_date ? $manualPayment->transfer_date->format('M d, Y') : 'N/A' }}</p>
                </div>
            </div>

            @if($manualPayment->sender_name || $manualPayment->sender_account)
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h4 class="font-semibold text-slate-800 mb-4">Sender Details</h4>
                <div class="grid grid-cols-2 gap-4">
                    @if($manualPayment->sender_name)
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Sender Name</p>
                        <p class="font-medium text-slate-800">{{ $manualPayment->sender_name }}</p>
                    </div>
                    @endif
                    @if($manualPayment->sender_account)
                    <div>
                        <p class="text-sm text-slate-500 mb-1">Sender Account</p>
                        <p class="font-medium text-slate-800">{{ $manualPayment->sender_account }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            @if($manualPayment->notes)
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h4 class="font-semibold text-slate-800 mb-2">Notes</h4>
                <p class="text-slate-600">{{ $manualPayment->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Proof of Payment -->
        @if($manualPayment->proof_image)
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Proof of Payment</h3>
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <img src="{{ Storage::url($manualPayment->proof_image) }}" alt="Proof of payment" class="w-full h-auto">
            </div>
            <div class="mt-4 flex gap-3">
                <a href="{{ Storage::url($manualPayment->proof_image) }}" target="_blank" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg font-medium hover:bg-slate-200 transition-colors">
                    View Full Size
                </a>
            </div>
        </div>
        @endif
    </div>

    <!-- User & Bank Details -->
    <div class="space-y-6">
        <!-- User -->
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">User</h3>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-400 to-accent-400 flex items-center justify-center text-white font-bold">
                    {{ substr($manualPayment->user->first_name ?? 'U', 0, 1) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-800">{{ $manualPayment->user->full_name ?? 'N/A' }}</p>
                    <p class="text-sm text-slate-500">{{ $manualPayment->user->email ?? '' }}</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.users.show', $manualPayment->user) }}" class="text-primary-600 hover:text-primary-700 font-medium">View Profile →</a>
            </div>
        </div>

        <!-- Bank Account -->
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Bank Account</h3>
            @if($manualPayment->bankAccount)
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-slate-500">Bank</p>
                    <p class="font-medium text-slate-800">{{ $manualPayment->bankAccount->bank_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Account Number</p>
                    <p class="font-medium text-slate-800">{{ $manualPayment->bankAccount->account_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Account Name</p>
                    <p class="font-medium text-slate-800">{{ $manualPayment->bankAccount->account_name }}</p>
                </div>
            </div>
            @else
            <p class="text-slate-500">No bank account linked</p>
            @endif
        </div>

        <!-- Actions -->
        @if($manualPayment->status === 'pending')
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Actions</h3>
            <form method="POST" action="{{ route('admin.manual-payments.verify', $manualPayment) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Action</label>
                    <select name="action" id="action" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500">
                        <option value="verify">Verify Payment</option>
                        <option value="reject">Reject Payment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Notes (optional)</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500" placeholder="Add notes..."></textarea>
                </div>
                <button type="submit" class="w-full px-4 py-3 bg-primary-500 text-white font-medium rounded-xl hover:bg-primary-600 transition-colors">
                    Submit
                </button>
            </form>
        </div>
        @endif

        <!-- Verification Info -->
        @if($manualPayment->status !== 'pending')
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Verification Info</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-slate-500">Status</p>
                    <p class="font-medium {{ $manualPayment->status === 'verified' ? 'text-green-600' : 'text-red-600' }}">
                        {{ ucfirst($manualPayment->status) }}
                    </p>
                </div>
                @if($manualPayment->verifier)
                <div>
                    <p class="text-sm text-slate-500">Verified By</p>
                    <p class="font-medium text-slate-800">{{ $manualPayment->verifier->name }}</p>
                </div>
                @endif
                @if($manualPayment->verified_at)
                <div>
                    <p class="text-sm text-slate-500">Verified At</p>
                    <p class="font-medium text-slate-800">{{ $manualPayment->verified_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif
                @if($manualPayment->rejection_reason)
                <div>
                    <p class="text-sm text-slate-500">Rejection Reason</p>
                    <p class="font-medium text-red-600">{{ $manualPayment->rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

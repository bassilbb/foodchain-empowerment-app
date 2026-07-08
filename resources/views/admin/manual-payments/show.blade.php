@extends('admin.layouts.app')

@section('title', 'Payment Details')
@section('page-title', 'Manual Payment Details')

@section('header', 'Payment Details')
@section('subheader', 'View and verify manual payment')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.manual-payments.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #10b981; font-weight: 500; text-decoration: none;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Payments
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; @media(min-width: 1024px) { grid-template-columns: 2fr 1fr; }">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Payment Information</h3>
                <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
                    @if($manualPayment->status === 'pending') background: #fef3c7; color: #d97706; @endif
                    @if($manualPayment->status === 'verified') background: #dcfce7; color: #16a34a; @endif
                    @if($manualPayment->status === 'rejected') background: #fee2e2; color: #dc2626; @endif">
                    {{ ucfirst($manualPayment->status) }}
                </span>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Reference</p>
                        <p style="font-family: monospace; font-weight: 600; color: #1e293b;">{{ $manualPayment->reference }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Type</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ ucfirst(str_replace('_', ' ', $manualPayment->type)) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Amount</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">{{ format_currency($manualPayment->amount) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Transfer Date</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->transfer_date ? $manualPayment->transfer_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>

                @if($manualPayment->sender_name || $manualPayment->sender_account)
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                    <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 1rem;">Sender Details</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        @if($manualPayment->sender_name)
                        <div>
                            <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Sender Name</p>
                            <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->sender_name }}</p>
                        </div>
                        @endif
                        @if($manualPayment->sender_account)
                        <div>
                            <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Sender Account</p>
                            <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->sender_account }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($manualPayment->notes)
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                    <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Notes</h4>
                    <p style="color: #475569;">{{ $manualPayment->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($manualPayment->proof_image_path)
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Proof of Payment</h3>
            </div>
            <div class="page-card-body">
                <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                    <img src="{{ Storage::url($manualPayment->proof_image_path) }}" alt="Proof of payment" style="width: 100%; height: auto;">
                </div>
                <div style="margin-top: 1rem; display: flex; gap: 0.75rem;">
                    <a href="{{ Storage::url($manualPayment->proof_image_path) }}" target="_blank" style="padding: 0.5rem 1rem; background: #f1f5f9; color: #64748b; border-radius: 8px; font-weight: 500; text-decoration: none;">
                        View Full Size
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">User</h3>
            </div>
            <div class="page-card-body">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 3rem; height: 3rem; border-radius: 50%; background: linear-gradient(135deg, #10b981, #0ea5e9); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                        {{ substr($manualPayment->user->first_name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $manualPayment->user->full_name ?? 'N/A' }}</p>
                        <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $manualPayment->user->email ?? '' }}</p>
                    </div>
                </div>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <a href="{{ route('admin.users.show', $manualPayment->user) }}" style="color: #10b981; font-weight: 500; text-decoration: none;">View Profile →</a>
                </div>
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Bank Account</h3>
            </div>
            <div class="page-card-body">
                @if($manualPayment->bankAccount)
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Bank</p>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $manualPayment->bankAccount->bank_name }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Account Number</p>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $manualPayment->bankAccount->account_number }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Account Name</p>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $manualPayment->bankAccount->account_name }}</p>
                    </div>
                </div>
                @else
                <p style="color: #64748b;">No bank account linked</p>
                @endif
            </div>
        </div>

        @if($manualPayment->status === 'pending')
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Actions</h3>
            </div>
            <div class="page-card-body">
                <form method="POST" action="{{ route('admin.manual-payments.verify', $manualPayment) }}" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Action</label>
                        <select name="action" id="action" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem;">
                            <option value="verify">Verify Payment</option>
                            <option value="reject">Reject Payment</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Notes (optional)</label>
                        <textarea name="notes" rows="3" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem;" placeholder="Add notes..."></textarea>
                    </div>
                    <button type="submit" style="width: 100%; padding: 0.75rem 1rem; background: linear-gradient(135deg, #10b981, #059669); color: white; font-weight: 500; border-radius: 12px; border: none; cursor: pointer;">
                        Submit
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($manualPayment->status !== 'pending')
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Verification Info</h3>
            </div>
            <div class="page-card-body">
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Status</p>
                        <p style="font-weight: 500; @if($manualPayment->status === 'verified') color: #16a34a; @else color: #dc2626; @endif margin: 0;">
                            {{ ucfirst($manualPayment->status) }}
                        </p>
                    </div>
                    @if($manualPayment->verifier)
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Verified By</p>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $manualPayment->verifier->name }}</p>
                    </div>
                    @endif
                    @if($manualPayment->verified_at)
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Verified At</p>
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $manualPayment->verified_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                    @if($manualPayment->rejection_reason)
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b;">Rejection Reason</p>
                        <p style="font-weight: 500; color: #dc2626; margin: 0;">{{ $manualPayment->rejection_reason }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

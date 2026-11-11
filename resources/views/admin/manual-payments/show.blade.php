@extends('admin.layouts.app')

@section('title', 'Payment Details')
@section('page-title', 'Payment Verification Details')

@section('header', 'Payment Details')
@section('subheader', 'Confirm the funds and amount before approving')

@section('content')
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif

<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.manual-payments.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #10b981; font-weight: 500; text-decoration: none;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Payment Verification
    </a>
</div>

@if(! $amountMatches)
<div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.5rem;">
    <p style="font-weight: 700; color: #b91c1c; margin: 0;">Amount mismatch</p>
    <p style="color: #991b1b; font-size: 0.875rem; margin: 0.25rem 0 0;">
        The notified amount ({{ format_currency($manualPayment->amount) }}) does not match the expected amount
        ({{ format_currency($expectedAmount) }}). Do not approve until the exact amount has been received.
    </p>
</div>
@endif

<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Payment Information</h3>
                <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
                    @if($manualPayment->status === 'pending') background: #fef3c7; color: #d97706; @endif
                    @if($manualPayment->status === 'verified') background: #dcfce7; color: #16a34a; @endif
                    @if($manualPayment->status === 'rejected') background: #fee2e2; color: #dc2626; @endif">
                    {{ $manualPayment->status === 'verified' ? 'Approved' : ucfirst($manualPayment->status) }}
                </span>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Reference</p>
                        <p style="font-family: monospace; font-weight: 600; color: #1e293b;">{{ $manualPayment->reference }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Type</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ ucfirst(str_replace('_', ' ', $manualPayment->type)) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Notified Amount</p>
                        <p style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">{{ format_currency($manualPayment->amount) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Expected Amount</p>
                        <p style="font-size: 1.25rem; font-weight: 700; color: {{ $amountMatches ? '#16a34a' : '#dc2626' }};">{{ format_currency($expectedAmount) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Currency</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->currency ?? 'NGN' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Notification Date</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Transfer Date</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->transfer_date ? $manualPayment->transfer_date->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Payment Status</p>
                        <p style="font-weight: 500; color: #1e293b; text-transform: capitalize;">{{ str_replace('_', ' ', $manualPayment->payment_status ?? $manualPayment->status) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Verification Status</p>
                        <p style="font-weight: 500; color: #1e293b; text-transform: capitalize;">{{ $manualPayment->verification_status ?? $manualPayment->status }}</p>
                    </div>
                </div>

                @if($manualPayment->sender_name || $manualPayment->sender_account_number || $manualPayment->sender_bank_name)
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                    <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 1rem;">Sender Details</h4>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;">
                        @if($manualPayment->sender_name)
                        <div>
                            <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Sender Name</p>
                            <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->sender_name }}</p>
                        </div>
                        @endif
                        @if($manualPayment->sender_account_number)
                        <div>
                            <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Sender Account Number</p>
                            <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->sender_account_number }}</p>
                        </div>
                        @endif
                        @if($manualPayment->sender_bank_name)
                        <div>
                            <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Sender Bank</p>
                            <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->sender_bank_name }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if($manualPayment->admin_notes)
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                    <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Admin Notes</h4>
                    <p style="color: #475569;">{{ $manualPayment->admin_notes }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($order)
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Order Details</h3>
                <span class="badge badge-dark">{{ $order->order_number }}</span>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Order Total</p>
                        <p style="font-weight: 700; color: #1e293b;">{{ format_currency($order->total) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Payment Method</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ \App\Models\Marketplace\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Order Status</p>
                        <p style="font-weight: 500; color: #1e293b; text-transform: capitalize;">{{ str_replace('_', ' ', $order->status) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Payment Status</p>
                        <p style="font-weight: 500; color: {{ $order->payment_status === 'paid' ? '#16a34a' : '#d97706' }}; text-transform: capitalize;">{{ str_replace('_', ' ', $order->payment_status) }}</p>
                    </div>
                </div>

                <div style="border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                    <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 0.75rem;">Items</h4>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="text-align: left; color: #64748b; font-size: 0.8rem;">
                                <th style="padding: 0.4rem 0;">Product</th>
                                <th style="padding: 0.4rem 0; text-align: right;">Qty</th>
                                <th style="padding: 0.4rem 0; text-align: right;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr style="border-top: 1px solid #f1f5f9; font-size: 0.875rem;">
                                <td style="padding: 0.5rem 0; color: #1e293b;">{{ $item->product_name }}</td>
                                <td style="padding: 0.5rem 0; text-align: right; color: #475569;">{{ $item->quantity }}</td>
                                <td style="padding: 0.5rem 0; text-align: right; color: #1e293b; font-weight: 600;">{{ format_currency($item->total_price) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <a href="{{ route('admin.marketplace.orders.show', $order) }}" style="color: #10b981; font-weight: 500; text-decoration: none;">View full order →</a>
                </div>
            </div>
        </div>
        @endif

        @if($manualPayment->proof_image_path && $manualPayment->proof_image_path !== 'manual-payments/proofs/not-provided')
        @php
            $proofData = null;
            $proofMime = 'image/jpeg';
            $diskPath = $manualPayment->proof_image_path;
            try {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($diskPath)) {
                    $proofData = \Illuminate\Support\Facades\Storage::disk('public')->get($diskPath);
                    $proofMime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($diskPath);
                }
            } catch (\Exception $e) {
                $proofData = null;
            }
        @endphp
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Proof of Payment</h3>
            </div>
            <div class="page-card-body">
                @if($proofData)
                <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                    <img src="data:{{ $proofMime }};base64,{{ base64_encode($proofData) }}" alt="Proof of payment" style="width: 100%; height: auto;">
                </div>
                @else
                <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 1.5rem; text-align: center;">
                    <p style="font-weight: 600; color: #92400e;">Image not available</p>
                    <p style="font-size: 0.875rem; color: #a16207; margin-top: 0.25rem;">The proof image could not be found on the server.</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Customer</h3>
            </div>
            <div class="page-card-body">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 3rem; height: 3rem; border-radius: 50%; background: linear-gradient(135deg, var(--fc-primary-light), var(--fc-secondary)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                        {{ substr($manualPayment->user->first_name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $manualPayment->user->full_name ?? 'N/A' }}</p>
                        <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $manualPayment->user->email ?? '' }}</p>
                    </div>
                </div>
                @if($manualPayment->user)
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <a href="{{ route('admin.users.show', $manualPayment->user) }}" style="color: #10b981; font-weight: 500; text-decoration: none;">View Profile →</a>
                </div>
                @endif
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Company Bank Account</h3>
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
                <h3 class="page-card-title">Verify or Reject</h3>
            </div>
            <div class="page-card-body">
                <form method="POST" action="{{ route('admin.manual-payments.verify', $manualPayment) }}" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Action</label>
                        <select name="action" id="action" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem;">
                            <option value="verify" {{ old('action') === 'verify' ? 'selected' : '' }}>Approve payment</option>
                            <option value="reject" {{ old('action') === 'reject' ? 'selected' : '' }}>Reject notification</option>
                        </select>
                    </div>

                    <div id="confirmed-amount-wrap">
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Confirmed amount received ({{ $manualPayment->currency ?? 'NGN' }})</label>
                        <input type="number" step="0.01" name="confirmed_amount" value="{{ old('confirmed_amount', $manualPayment->amount) }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem;">
                        <p style="font-size: 0.75rem; color: #94a3b8; margin: 0.35rem 0 0;">Must match the expected amount ({{ format_currency($expectedAmount) }}) before approval.</p>
                    </div>

                    <label id="amount-confirmed-wrap" style="display: flex; gap: 0.6rem; align-items: flex-start; font-size: 0.85rem; color: #334155;">
                        <input type="checkbox" name="amount_confirmed" value="1" {{ old('amount_confirmed') ? 'checked' : '' }} style="margin-top: 0.2rem;">
                        <span>I have confirmed the funds landed in the company account and the amount matches the order total.</span>
                    </label>

                    <div>
                        <label id="notes-label" style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Notes / rejection reason</label>
                        <textarea name="notes" rows="3" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem;" placeholder="Add notes. A reason is required when rejecting.">{{ old('notes') }}</textarea>
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
                            {{ $manualPayment->status === 'verified' ? 'Approved' : ucfirst($manualPayment->status) }}
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

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Audit Trail</h3>
            </div>
            <div class="page-card-body">
                @forelse($auditLogs as $log)
                <div style="border-bottom: 1px solid #f1f5f9; padding: 0.6rem 0;">
                    <p style="font-size: 0.85rem; color: #1e293b; margin: 0; font-weight: 600;">{{ str_replace(['manual_payment.', '_'], ['', ' '], $log->action) }}</p>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0.15rem 0 0;">
                        {{ $log->adminUser?->name ?? 'System' }} · {{ $log->created_at->format('M d, Y h:i A') }}
                    </p>
                    @if($log->description)
                    <p style="font-size: 0.75rem; color: #94a3b8; margin: 0.15rem 0 0;">{{ $log->description }}</p>
                    @endif
                </div>
                @empty
                <p style="color: #94a3b8; font-size: 0.85rem;">No audit entries yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function () {
        const action = document.getElementById('action');
        if (!action) return;

        const confirmedWrap = document.getElementById('confirmed-amount-wrap');
        const amountConfirmedWrap = document.getElementById('amount-confirmed-wrap');
        const notesLabel = document.getElementById('notes-label');

        function sync() {
            const isVerify = action.value === 'verify';
            if (confirmedWrap) confirmedWrap.style.display = isVerify ? 'block' : 'none';
            if (amountConfirmedWrap) amountConfirmedWrap.style.display = isVerify ? 'flex' : 'none';
            if (notesLabel) notesLabel.textContent = isVerify ? 'Notes (optional)' : 'Rejection reason (required)';
        }

        action.addEventListener('change', sync);
        sync();
    })();
</script>
@endpush
@endsection

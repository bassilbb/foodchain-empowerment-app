@extends('layouts.user')
@section('title', 'Payment Details')
@section('header', 'Payment Details')
@section('subheader', 'View your manual payment details')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('manual-payments.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: white; border-radius: 999px; font-size: 0.8125rem; font-weight: 600; color: #475569; text-decoration: none; box-shadow: 0 2px 8px rgba(0,0,0,0.06); transition: all 0.2s;" onmouseover="this.style.boxShadow='0 4px 16px rgba(0,0,0,0.1)'; this.style.transform='translateX(-3px)'" onmouseout="this.style.boxShadow='0 2px 8px rgba(0,0,0,0.06)'; this.style.transform='translateX(0)'">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Payments
    </a>
</div>

<div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem;">
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
                        <p style="font-size: 1.5rem; font-weight: 700; color: #1e293b;">{{ number_format($manualPayment->amount, 2) }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Transfer Date</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->transfer_date ? date('M d, Y', strtotime($manualPayment->transfer_date)) : 'N/A' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Submitted At</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                    <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 1rem;">Sender Details</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Sender Name</p>
                            <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->sender_name ?? 'N/A' }}</p>
                        </div>
                        @if($manualPayment->sender_account_number)
                        <div>
                            <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Account Number</p>
                            <p style="font-weight: 500; color: #1e293b;">{{ $manualPayment->sender_account_number }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($manualPayment->proof_image_path)
        @php
            $proofData = null;
            $proofMime = 'image/jpeg';
            $diskPath = $manualPayment->proof_image_path;
            try {
                $fullPath = storage_path('app/public/' . $diskPath);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($diskPath)) {
                    $proofData = \Illuminate\Support\Facades\Storage::disk('public')->get($diskPath);
                    $proofMime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($diskPath);
                } elseif (file_exists($fullPath)) {
                    $proofData = file_get_contents($fullPath);
                    $proofMime = mime_content_type($fullPath);
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

        @if($manualPayment->admin_notes)
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Admin Notes</h3>
            </div>
            <div class="page-card-body">
                <p style="color: #475569; margin: 0;">{{ $manualPayment->admin_notes }}</p>
            </div>
        </div>
        @endif

        <div class="page-card" style="background: linear-gradient(135deg, #f0fdf4, #ecfeff);">
            <div class="page-card-body" style="text-align: center;">
                <div style="width: 48px; height: 48px; border-radius: 50%; margin: 0 auto 0.75rem; display: flex; align-items: center; justify-content: center;
                    @if($manualPayment->status === 'pending') background: #fef3c7; @endif
                    @if($manualPayment->status === 'verified') background: #dcfce7; @endif
                    @if($manualPayment->status === 'rejected') background: #fee2e2; @endif">
                    @if($manualPayment->status === 'pending')
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; color: #d97706;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @endif
                    @if($manualPayment->status === 'verified')
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; color: #16a34a;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @endif
                    @if($manualPayment->status === 'rejected')
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px; color: #dc2626;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @endif
                </div>
                <p style="font-weight: 600; color: #1e293b; margin: 0;">
                    @if($manualPayment->status === 'pending')
                    Awaiting Verification
                    @endif
                    @if($manualPayment->status === 'verified')
                    Payment Verified
                    @endif
                    @if($manualPayment->status === 'rejected')
                    Payment Rejected
                    @endif
                </p>
                <p style="font-size: 0.875rem; color: #64748b; margin: 0.25rem 0 0;">
                    @if($manualPayment->status === 'pending')
                    Your payment is being reviewed by our team
                    @endif
                    @if($manualPayment->status === 'verified')
                    Your payment has been confirmed
                    @endif
                    @if($manualPayment->status === 'rejected')
                    Please review and resubmit
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
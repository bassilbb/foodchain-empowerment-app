@extends('admin.layouts.app')

@section('title', 'Clearance Request Details')
@section('header', 'Clearance Request')
@section('subheader', 'Review and approve account clearance')

@section('content')
@if(session('success'))
<div style="background: #d1fae5; border: 1px solid #10b981; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #065f46;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background: #fee2e2; border: 1px solid #ef4444; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #991b1b;">{{ session('error') }}</div>
@endif

<!-- Back -->
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.clearances.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; background: #f1f5f9; color: #475569; border-radius: 10px; font-size: 0.85rem; font-weight: 500; text-decoration: none;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Back to Clearances
    </a>
</div>

<!-- Status Banner -->
@php
    $bannerMap = [
        'pending_payment' => ['bg' => '#fef9c3', 'border' => '#fde68a', 'icon' => '⏳', 'title' => 'Pending Payment', 'text' => 'User has not paid the clearance fee yet.'],
        'pending_approval' => ['bg' => '#eff6ff', 'border' => '#bfdbfe', 'icon' => '📋', 'title' => 'Pending Approval', 'text' => 'Fee has been paid. Awaiting admin review.'],
        'approved' => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'icon' => '✓', 'title' => 'Approved', 'text' => 'This clearance has been approved and the account closed.'],
    ];
    $banner = $bannerMap[$clearance->status] ?? ['bg' => '#f1f5f9', 'border' => '#e2e8f0', 'icon' => '?', 'title' => ucfirst($clearance->status), 'text' => ''];
@endphp
<div style="background: {{ $banner['bg'] }}; border: 1px solid {{ $banner['border'] }}; border-radius: 16px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 1rem;">
    <span style="font-size: 2rem;">{{ $banner['icon'] }}</span>
    <div>
        <p style="font-weight: 700; color: #1e293b; margin: 0; font-size: 1.1rem;">{{ $banner['title'] }}</p>
        <p style="color: #64748b; margin: 0.15rem 0 0 0; font-size: 0.9rem;">{{ $banner['text'] }}</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
    <!-- User & Account Info -->
    <div class="page-card">
        <div class="page-card-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
            <h3 class="page-card-title" style="margin: 0; font-size: 1rem;">User & Account Details</h3>
        </div>
        <div class="page-card-body" style="padding-top: 1.25rem;">
            <!-- User -->
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.25rem; padding-bottom: 1.25rem; border-bottom: 1px solid #f1f5f9;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #10b981, #0ea5e9); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.1rem; font-weight: 700;">
                    {{ substr($clearance->user?->first_name ?? '?', 0, 1) }}{{ substr($clearance->user?->last_name ?? '', 0, 1) }}
                </div>
                <div>
                    <p style="font-weight: 700; color: #1e293b; margin: 0; font-size: 1rem;">{{ $clearance->user?->full_name ?? 'N/A' }}</p>
                    <p style="font-size: 0.85rem; color: #64748b; margin: 0.1rem 0 0 0;">{{ $clearance->user?->email ?? '' }}</p>
                </div>
            </div>
            <!-- Account Info -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <p style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; color: #94a3b8; margin: 0 0 0.2rem 0;">Account Name</p>
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $clearance->userAccount?->account_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; color: #94a3b8; margin: 0 0 0.2rem 0;">Plan</p>
                    <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $clearance->userAccount?->plan_name ?? 'N/A' }}</p>
                </div>
            </div>
            <!-- Progress Bar -->
            @if($clearance->userAccount)
            @php
                $pct = $clearance->userAccount->plan_duration_weeks > 0
                    ? min(100, round(($clearance->userAccount->current_tariff_weeks / $clearance->userAccount->plan_duration_weeks) * 100))
                    : 0;
            @endphp
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                    <p style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; color: #94a3b8; margin: 0;">Maturity Progress</p>
                    <p style="font-weight: 700; color: #059669; margin: 0; font-size: 0.9rem;">{{ $clearance->userAccount->current_tariff_weeks }}/{{ $clearance->userAccount->plan_duration_weeks }} weeks</p>
                </div>
                <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                    <div style="height: 100%; width: {{ $pct }}%; background: linear-gradient(135deg, #10b981, #059669); border-radius: 4px;"></div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Clearance Details -->
    <div class="page-card">
        <div class="page-card-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
            <h3 class="page-card-title" style="margin: 0; font-size: 1rem;">Clearance Details</h3>
        </div>
        <div class="page-card-body" style="padding-top: 1.25rem;">
            <!-- Fee & Balance -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.25rem;">
                <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 1rem; text-align: center;">
                    <p style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; color: #059669; margin: 0 0 0.25rem 0;">Fee Amount</p>
                    <p style="font-size: 1.5rem; font-weight: 800; color: #059669; margin: 0;">₦{{ number_format($clearance->fee_amount, 2) }}</p>
                </div>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1rem; text-align: center;">
                    <p style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; color: #64748b; margin: 0 0 0.25rem 0;">Available Balance</p>
                    <p style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0;">₦{{ number_format($clearance->userAccount?->available_balance ?? 0, 2) }}</p>
                </div>
            </div>
            <!-- Status -->
            <div style="margin-bottom: 0.75rem;">
                <p style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; color: #94a3b8; margin: 0 0 0.3rem 0;">Status</p>
                @php
                    $badgeMap = [
                        'pending_payment' => ['bg' => '#fef3c7', 'color' => '#d97706', 'label' => 'Pending Payment'],
                        'pending_approval' => ['bg' => '#dbeafe', 'color' => '#2563eb', 'label' => 'Pending Approval'],
                        'approved' => ['bg' => '#dcfce7', 'color' => '#16a34a', 'label' => 'Approved'],
                    ];
                    $s = $badgeMap[$clearance->status] ?? ['bg' => '#f1f5f9', 'color' => '#64748b', 'label' => ucfirst($clearance->status)];
                @endphp
                <span style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; background: {{ $s['bg'] }}; color: {{ $s['color'] }};">
                    <span style="width: 6px; height: 6px; border-radius: 50%; background: {{ $s['color'] }};"></span>
                    {{ $s['label'] }}
                </span>
            </div>
            <!-- Payment Details -->
            <div style="display: grid; gap: 0.6rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                @if($clearance->payment_reference)
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8rem; color: #64748b;">Payment Reference</span>
                    <span style="font-weight: 600; color: #1e293b; font-family: monospace;">{{ $clearance->payment_reference }}</span>
                </div>
                @endif
                @if($clearance->fee_paid_at)
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8rem; color: #64748b;">Fee Paid At</span>
                    <span style="font-weight: 500; color: #1e293b;">{{ $clearance->fee_paid_at->format('M d, Y h:i A') }}</span>
                </div>
                @endif
                @if($clearance->approved_at)
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8rem; color: #64748b;">Approved At</span>
                    <span style="font-weight: 500; color: #1e293b;">{{ $clearance->approved_at->format('M d, Y h:i A') }}</span>
                </div>
                @endif
                @if($clearance->approvedBy)
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8rem; color: #64748b;">Approved By</span>
                    <span style="font-weight: 500; color: #1e293b;">{{ $clearance->approvedBy->name ?? 'Admin #'.$clearance->approved_by }}</span>
                </div>
                @endif
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.8rem; color: #64748b;">Requested</span>
                    <span style="font-weight: 500; color: #1e293b;">{{ $clearance->created_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admin Notes -->
@if($clearance->admin_notes)
<div class="page-card" style="margin-bottom: 1.5rem;">
    <div class="page-card-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
        <h3 class="page-card-title" style="margin: 0; font-size: 1rem;">Admin Notes</h3>
    </div>
    <div class="page-card-body">
        <div style="background: #fef9c3; border: 1px solid #fde68a; border-radius: 10px; padding: 1rem;">
            <p style="color: #92400e; margin: 0;">{{ $clearance->admin_notes }}</p>
        </div>
    </div>
</div>
@endif

<!-- Actions -->
@if($clearance->status === 'pending_approval')
<div class="page-card">
    <div class="page-card-header" style="border-bottom: 1px solid #e2e8f0; padding-bottom: 1rem;">
        <h3 class="page-card-title" style="margin: 0; font-size: 1rem;">Actions</h3>
    </div>
    <div class="page-card-body">
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <!-- Approve -->
            <button type="button" onclick="document.getElementById('approveModal').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 2rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 0.9rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Approve Clearance
            </button>
            <!-- Reject -->
            <button type="button" onclick="document.getElementById('rejectForm').style.display='block'; this.style.display='none'" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 2rem; background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; border-radius: 12px; font-weight: 600; font-size: 0.9rem; cursor: pointer;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Reject
            </button>
        </div>

        <!-- Reject Form -->
        <div id="rejectForm" style="display: none; margin-top: 1.5rem; padding: 1.5rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 16px;">
            <p style="font-weight: 700; color: #991b1b; margin: 0 0 0.25rem 0;">Reject Clearance Request</p>
            <p style="font-size: 0.85rem; color: #64748b; margin: 0 0 1rem 0;">Provide a reason for rejecting this clearance request. The user will be notified.</p>
            <form action="{{ route('admin.clearances.reject', $clearance->id) }}" method="POST">
                @csrf
                <textarea name="admin_notes" rows="3" required style="width: 100%; padding: 0.75rem; border: 1px solid #fca5a5; border-radius: 10px; font-size: 0.9rem; margin-bottom: 1rem;" placeholder="Explain why this clearance request is being rejected..."></textarea>
                <div style="display: flex; gap: 0.75rem;">
                    <button type="submit" style="padding: 0.6rem 1.5rem; background: #dc2626; color: white; border: none; border-radius: 10px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                        Submit Rejection
                    </button>
                    <button type="button" onclick="document.getElementById('rejectForm').style.display='none'; document.querySelector('[onclick*=\"rejectForm.style.display=\\'block\\'\"]').style.display='inline-flex'" style="padding: 0.6rem 1.5rem; background: #f1f5f9; color: #475569; border: none; border-radius: 10px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Approve Modal -->
<div id="approveModal" style="display: none; position: fixed; inset: 0; z-index: 9999; align-items: center; justify-content: center;" onclick="if(event.target===this) document.getElementById('approveModal').style.display='none'">
    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);"></div>
    <div style="position: relative; background: white; border-radius: 24px; max-width: 440px; width: 90%; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); overflow: hidden;">
        <!-- Top accent -->
        <div style="height: 6px; background: linear-gradient(135deg, #10b981, #059669);"></div>
        <div style="padding: 2rem 1.75rem 1.5rem; text-align: center;">
            <!-- Icon -->
            <div style="width: 64px; height: 64px; border-radius: 50%; background: #f0fdf4; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 32px; height: 32px; color: #16a34a;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem 0;">Approve Clearance?</h3>
            <p style="color: #64748b; margin: 0 0 0.25rem 0; font-size: 0.9rem;">
                This will close the account, zero out the balance, and deduct the clearance fee.
            </p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin: 1.25rem 0; background: #f8fafc; border-radius: 12px; padding: 1rem; text-align: left;">
                <div>
                    <p style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; color: #94a3b8; margin: 0 0 0.15rem 0;">Payout</p>
                    <p style="font-weight: 700; color: #1e293b; margin: 0;">₦{{ number_format($clearance->userAccount?->available_balance ?? 0, 2) }}</p>
                </div>
                <div style="text-align: right;">
                    <p style="font-size: 0.7rem; text-transform: uppercase; font-weight: 600; color: #94a3b8; margin: 0 0 0.15rem 0;">Fee</p>
                    <p style="font-weight: 700; color: #dc2626; margin: 0;">-₦{{ number_format($clearance->fee_amount, 2) }}</p>
                </div>
            </div>
            <form action="{{ route('admin.clearances.approve', $clearance->id) }}" method="POST">
                @csrf
                <div style="display: flex; gap: 0.75rem;">
                    <button type="button" onclick="document.getElementById('approveModal').style.display='none'" style="flex: 1; padding: 0.7rem; background: #f1f5f9; color: #475569; border: none; border-radius: 12px; font-weight: 600; font-size: 0.85rem; cursor: pointer;">
                        Cancel
                    </button>
                    <button type="submit" style="flex: 1; padding: 0.7rem; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 12px; font-weight: 600; font-size: 0.85rem; cursor: pointer; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
                        Confirm & Approve
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

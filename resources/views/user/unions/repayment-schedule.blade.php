@extends('layouts.user')
@section('title', 'Repayment Schedule - ' . $union->name)
@section('header', 'Repayment Schedule')
@section('subheader', $union->name)

@section('content')
<style>
    .back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; color: #475569; text-decoration: none; font-size: 0.875rem; font-weight: 500; margin-bottom: 24px; transition: all 0.2s; text-decoration: none; }
    .back-btn:hover { background: #f1f5f9; border-color: #10b981; color: #10b981; }
    .detail-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px; }
    .detail-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 16px; border: 1px solid #e2e8f0; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.3s ease; }
    .detail-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15); }
    .detail-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; }
    .detail-value { font-size: 1.25rem; font-weight: 700; color: #1e293b; }
    .detail-value.emerald { color: #10b981; }
    .progress-section { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; padding: 24px; margin-bottom: 24px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); }
    .progress-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; }
    .progress-title { font-weight: 700; color: #1e293b; font-size: 1.1rem; }
    .progress-stats { font-size: 0.875rem; color: #64748b; }
    .progress-bar-large { width: 100%; height: 12px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; margin-bottom: 12px; }
    .progress-fill { height: 100%; background: linear-gradient(135deg, #10b981, #059669); border-radius: 9999px; transition: width 0.5s ease; }
    .schedule-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); overflow: hidden; }
    .schedule-header { background: linear-gradient(135deg, #10b981, #059669); padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; }
    .schedule-title { color: white; font-weight: 700; font-size: 1.1rem; margin: 0; }
    .schedule-badge { background: rgba(255,255,255,0.2); color: white; padding: 6px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    .schedule-list { padding: 0; margin: 0; list-style: none; }
    .schedule-item { display: flex; align-items: center; padding: 16px 24px; border-bottom: 1px solid #f1f5f9; transition: all 0.2s; }
    .schedule-item:hover { background: #fafbfc; }
    .schedule-item:last-child { border-bottom: none; }
    .installment-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1rem; margin-right: 16px; flex-shrink: 0; }
    .installment-icon.paid { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46; }
    .installment-icon.pending { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #92400e; }
    .installment-icon.overdue { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b; }
    .installment-icon.current { background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4); }
    .installment-info { flex: 1; }
    .installment-num { font-weight: 600; color: #1e293b; margin-bottom: 4px; }
    .installment-date { font-size: 0.8rem; color: #64748b; }
    .installment-amount { text-align: right; }
    .amount-due { font-weight: 700; color: #1e293b; font-family: monospace; font-size: 1rem; }
    .amount-paid { font-size: 0.8rem; color: #10b981; font-weight: 500; }
    .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; text-transform: capitalize; margin-left: 12px; }
    .badge-paid { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-overdue { background: #fee2e2; color: #991b1b; }
    .badge-current { background: #10b981; color: white; }
    .btn-primary { background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45); }
    .empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .empty-state svg { width: 64px; height: 64px; margin-bottom: 16px; opacity: 0.5; }
    .summary-row { display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
    .summary-row:last-child { border-bottom: none; }
    .summary-label { color: #64748b; font-size: 0.875rem; }
    .summary-value { font-weight: 600; color: #1e293b; font-family: monospace; }
    .next-payment { background: linear-gradient(135deg, #10b981, #059669); border-radius: 16px; padding: 24px; color: white; margin-bottom: 24px; box-shadow: 0 10px 40px -10px rgba(16, 185, 129, 0.4); }
    .next-payment-label { font-size: 0.75rem; opacity: 0.8; margin-bottom: 8px; }
    .next-payment-amount { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
    .next-payment-date { font-size: 0.875rem; opacity: 0.9; }
</style>

<a href="{{ route('unions.show', $union->id) }}" class="back-btn">
    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
    Back to Union
</a>

@if(session('success'))
<div style="background: linear-gradient(135deg, #d1fae5, #a7f3d0); border: 1px solid #10b981; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #065f46; font-weight: 500; display: flex; align-items: center; gap: 12px;">
    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    {{ session('success') }}
</div>
@endif

<div class="detail-grid">
    <div class="detail-card">
        <div class="detail-label">Loan Reference</div>
        <div class="detail-value" style="font-family: monospace;">{{ $loan->reference }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Status</div>
        <div class="detail-value" style="text-transform: capitalize;">{{ $loan->status }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Total Repayment</div>
        <div class="detail-value emerald">₦{{ number_format($loan->total_repayment_amount, 2) }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Amount Repaid</div>
        <div class="detail-value">₦{{ number_format($loan->amount_repaid, 2) }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Remaining Balance</div>
        <div class="detail-value emerald">₦{{ number_format($loan->remaining_balance, 2) }}</div>
    </div>
    <div class="detail-card">
        <div class="detail-label">Interest Rate</div>
        <div class="detail-value">{{ $loan->interest_rate }}%</div>
    </div>
</div>

<div class="progress-section">
    <div class="progress-header">
        <span class="progress-title">Overall Repayment Progress</span>
        <span class="progress-stats">{{ $loan->installments_paid }} of {{ $loan->total_installments }} installments paid</span>
    </div>
    <div class="progress-bar-large">
        <div style="width: {{ $loan->getRepaymentProgress() }}%;" class="progress-fill"></div>
    </div>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <span style="color: #64748b; font-size: 0.875rem;">{{ round($loan->getRepaymentProgress()) }}% Complete</span>
        <span style="color: #10b981; font-weight: 700; font-size: 1.1rem;">{{ round($loan->getRepaymentProgress()) }}%</span>
    </div>
</div>

@php
$nextPayment = $repayments->where('status', '!=', 'paid')->first();
@endphp

@if($nextPayment && $isLeader)
<div class="next-payment">
    <div class="next-payment-label">Next Payment Due</div>
    <div class="next-payment-amount">₦{{ number_format($nextPayment->amount_due, 2) }}</div>
    <div class="next-payment-date">Due: {{ \Carbon\Carbon::parse($nextPayment->due_date)->format('F d, Y') }}</div>
</div>
@endif

@if($isLeader && in_array($loan->status, ['disbursed', 'repaying']))
<div style="margin-bottom: 24px; text-align: center;">
    <button onclick="document.getElementById('repaymentModal').showModal()" class="btn-primary">
        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
        Make Payment
    </button>
</div>
@endif

<div class="schedule-card">
    <div class="schedule-header">
        <h3 class="schedule-title">Repayment Schedule</h3>
        <span class="schedule-badge">{{ $repayments->count() }} Installments</span>
    </div>
    
    @if($repayments->count() > 0)
    <ul class="schedule-list">
        @php $isCurrentInstallment = true; @endphp
        @foreach($repayments as $repayment)
        @php
            $isPaid = $repayment->status === 'paid';
            $isOverdue = $repayment->isOverdue() && !$isPaid;
            $iconClass = $isPaid ? 'paid' : ($isOverdue ? 'overdue' : ($isCurrentInstallment ? 'current' : 'pending'));
            if ($isPaid) $isCurrentInstallment = false;
        @endphp
        <li class="schedule-item">
            <div class="installment-icon {{ $iconClass }}">
                @if($isPaid)
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                @else
                {{ $repayment->installment_number }}
                @endif
            </div>
            <div class="installment-info">
                <div class="installment-num">
                    Installment {{ $repayment->installment_number }}
                    <span class="status-badge badge-{{ $repayment->status }}">{{ $repayment->status }}</span>
                </div>
                <div class="installment-date">
                    Due: {{ \Carbon\Carbon::parse($repayment->due_date)->format('F d, Y') }}
                    @if($isPaid && $repayment->paid_date)
                    | Paid: {{ \Carbon\Carbon::parse($repayment->paid_date)->format('F d, Y') }}
                    @endif
                </div>
            </div>
            <div class="installment-amount">
                <div class="amount-due">₦{{ number_format($repayment->amount_due, 2) }}</div>
                @if($isPaid)
                <div class="amount-paid">Paid</div>
                @endif
            </div>
        </li>
        @endforeach
    </ul>
    
    <div style="padding: 20px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0;">
        <div class="summary-row">
            <span class="summary-label">Total Amount</span>
            <span class="summary-value">₦{{ number_format($loan->total_repayment_amount, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Paid</span>
            <span class="summary-value" style="color: #10b981;">₦{{ number_format($loan->amount_repaid, 2) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Remaining Balance</span>
            <span class="summary-value" style="color: #f59e0b;">₦{{ number_format($loan->remaining_balance, 2) }}</span>
        </div>
    </div>
    @else
    <div class="empty-state">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        <p style="font-size: 1rem; font-weight: 500;">No repayment schedule</p>
        <p style="font-size: 0.875rem;">The repayment schedule will be generated when the loan is disbursed.</p>
    </div>
    @endif
</div>

@if($isLeader && in_array($loan->status, ['disbursed', 'repaying']))
<dialog id="repaymentModal" style="border: none; border-radius: 16px; padding: 0; max-width: 28rem; width: 100%; background: white; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
    <div style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid #f1f5f9;">
            <h3 style="font-weight: 700; color: #1e293b; margin: 0; font-size: 1.1rem;">Make Repayment</h3>
            <button onclick="document.getElementById('repaymentModal').close()" style="background: none; border: none; cursor: pointer; color: #64748b; padding: 0.25rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('unions.repayment', $union->id) }}" method="POST" style="display: grid; gap: 1rem;">
            @csrf
            <div style="margin: 0;">
                <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 8px;">Repayment Amount (₦)</label>
                <input type="number" name="amount" required min="100" step="100" placeholder="Enter amount" style="width: 100%; padding: 12px 14px; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.875rem; box-sizing: border-box;">
                <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">Remaining balance: ₦{{ number_format($loan->remaining_balance, 2) }}</p>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('repaymentModal').close()" style="flex: 1; padding: 10px 16px; background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.875rem; cursor: pointer;">Cancel</button>
                <button type="submit" style="flex: 1; padding: 10px 16px; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 10px; font-size: 0.875rem; font-weight: 500; cursor: pointer;">Make Payment</button>
            </div>
        </form>
    </div>
</dialog>
@endif
@endsection

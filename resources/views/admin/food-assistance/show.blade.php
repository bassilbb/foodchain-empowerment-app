@extends('admin.layouts.app')
@section('title', 'Food Assistance Plan')
@section('header', 'Plan Details')
@section('subheader', 'View and manage food assistance plan')

@section('content')
<style>
    .detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
    .card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); }
    .card-title { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
    .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; }
    .info-label { color: #64748b; }
    .info-value { font-weight: 600; color: #1e293b; }
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-processing_fee_paid { background: #dbeafe; color: #1e40af; }
    .badge-first_installment_paid { background: #ede9fe; color: #5b21b6; }
    .badge-active { background: #d1fae5; color: #065f46; }
    .badge-completed { background: #10b981; color: white; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .badge-paid { background: #d1fae5; color: #065f46; }
    .badge-overdue { background: #fee2e2; color: #991b1b; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; }
    td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; color: #475569; }
    .btn-action { padding: 8px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-approve { background: #10b981; color: white; }
    .btn-collect { background: #3b82f6; color: white; }
    .btn-complete { background: #059669; color: white; }
    .btn-cancel { background: #fee2e2; color: #991b1b; }
    .user-card { display: flex; align-items: center; gap: 12px; padding: 16px; background: #f8fafc; border-radius: 12px; }
    .user-avatar { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem; }
    .progress-bar { width: 100%; height: 10px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; margin: 8px 0; }
    .progress-fill { height: 100%; background: linear-gradient(135deg, #10b981, #059669); border-radius: 9999px; transition: width 0.3s; }
</style>

@if(session('success'))
<div style="background: #d1fae5; border: 1px solid #10b981; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #065f46;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div style="background: #fee2e2; border: 1px solid #ef4444; border-radius: 12px; padding: 16px; margin-bottom: 24px; color: #991b1b;">{{ session('error') }}</div>
@endif

<div class="detail-grid">
    <div>
        <div class="card" style="margin-bottom: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                <h3 class="card-title" style="margin: 0;">Installment Schedule</h3>
                <span class="badge badge-{{ $assistancePlan->status }}">{{ str_replace('_', ' ', $assistancePlan->status) }}</span>
            </div>
            <div class="progress-bar"><div style="width: {{ $assistancePlan->getProgress() }}%;" class="progress-fill"></div></div>
            <div style="font-size: 0.8rem; color: #64748b; text-align: right; margin-bottom: 16px;">{{ round($assistancePlan->getProgress()) }}% complete</div>
            @if($installments->count() > 0)
            <table>
                <thead><tr><th>#</th><th>Type</th><th>Due</th><th>Amount</th><th>Paid</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($installments as $inst)
                    <tr>
                        <td>{{ $inst->installment_number }}</td>
                        <td>{{ str_replace('_', ' ', $inst->type) }}</td>
                        <td>{{ $inst->due_date->format('d M Y') }}</td>
                        <td>₦{{ number_format($inst->amount_due, 2) }}</td>
                        <td>₦{{ number_format($inst->amount_paid, 2) }}</td>
                        <td><span class="badge badge-{{ $inst->status }}">{{ $inst->status }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="card">
            <h3 class="card-title">Actions</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @if($assistancePlan->status === 'pending')
                <form action="{{ route('admin.food-assistance.approve', $assistancePlan) }}" method="POST" style="display: inline;">
                    @csrf <button class="btn-action btn-approve">Approve</button>
                </form>
                @endif
                @if($assistancePlan->status === 'first_installment_paid')
                <form action="{{ route('admin.food-assistance.mark-collected', $assistancePlan) }}" method="POST" style="display: inline;">
                    @csrf <button class="btn-action btn-collect">Mark Collected</button>
                </form>
                @endif
                @if(!in_array($assistancePlan->status, ['completed', 'cancelled']))
                <form action="{{ route('admin.food-assistance.complete', $assistancePlan) }}" method="POST" style="display: inline;">
                    @csrf <button class="btn-action btn-complete">Mark Completed</button>
                </form>
                <form action="{{ route('admin.food-assistance.cancel', $assistancePlan) }}" method="POST" style="display: inline;" onsubmit="return confirm('Cancel this plan?')">
                    @csrf <input type="hidden" name="admin_notes" value="Cancelled by admin">
                    <button class="btn-action btn-cancel">Cancel</button>
                </form>
                @endif
            </div>
            @if($assistancePlan->admin_notes)
            <div style="margin-top: 16px; padding: 12px; background: #fef3c7; border-radius: 8px; font-size: 0.85rem; color: #92400e;">
                <strong>Notes:</strong> {{ $assistancePlan->admin_notes }}
            </div>
            @endif
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom: 24px;">
            <h3 class="card-title">User Info</h3>
            <div class="user-card">
                <div class="user-avatar">{{ substr($assistancePlan->user->first_name ?? 'U', 0, 1) }}</div>
                <div>
                    <div style="font-weight: 600; color: #1e293b;">{{ $assistancePlan->user->full_name ?? 'N/A' }}</div>
                    <div style="font-size: 0.8rem; color: #64748b;">{{ $assistancePlan->user->email ?? '' }}</div>
                    <div style="font-size: 0.8rem; color: #64748b;">{{ $assistancePlan->user->phone ?? '' }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">Plan Summary</h3>
            <div class="info-row"><span class="info-label">Reference</span><span class="info-value" style="font-family: monospace;">{{ $assistancePlan->reference }}</span></div>
            <div class="info-row"><span class="info-label">Tier</span><span class="info-value">{{ $assistancePlan->tier->name ?? 'N/A' }}</span></div>
            <div class="info-row"><span class="info-label">Total Amount</span><span class="info-value">₦{{ number_format($assistancePlan->total_amount, 2) }}</span></div>
            <div class="info-row"><span class="info-label">Processing Fee</span><span class="info-value">₦{{ number_format($assistancePlan->processing_fee, 2) }}</span></div>
            <div class="info-row"><span class="info-label">First Instalment (30%)</span><span class="info-value">₦{{ number_format($assistancePlan->first_installment_amount, 2) }}</span></div>
            <div class="info-row"><span class="info-label">Balance (12 weeks)</span><span class="info-value">₦{{ number_format($assistancePlan->balance_amount, 2) }}</span></div>
            <div class="info-row"><span class="info-label">Weekly Instalment</span><span class="info-value">₦{{ number_format($assistancePlan->weekly_installment_amount, 2) }}</span></div>
            <div class="info-row"><span class="info-label">Total Paid</span><span class="info-value" style="color: #10b981;">₦{{ number_format($assistancePlan->total_paid, 2) }}</span></div>
            <div class="info-row"><span class="info-label">Applied</span><span class="info-value">{{ $assistancePlan->created_at->format('d M Y') }}</span></div>
            @if($assistancePlan->collected_at)
            <div class="info-row"><span class="info-label">Collected</span><span class="info-value">{{ $assistancePlan->collected_at->format('d M Y') }}</span></div>
            @endif
        </div>
    </div>
</div>
@endsection

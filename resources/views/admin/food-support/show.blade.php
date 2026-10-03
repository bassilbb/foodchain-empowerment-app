@extends('admin.layouts.app')
@section('title', 'Food Support Plan')
@section('header', 'Support Plan Details')
@section('subheader', 'View and manage food support plan')

@section('content')
<style>
    .detail-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }
    .card { background: white; border-radius: 20px; border: 1px solid #e2e8f0; padding: 24px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); }
    .card-title { font-size: 1rem; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
    .info-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 0.875rem; }
    .info-label { color: #64748b; }
    .info-value { font-weight: 600; color: #1e293b; }
    .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
    .badge-active { background: #dbeafe; color: #1e40af; }
    .badge-fully_funded { background: #ede9fe; color: #5b21b6; }
    .badge-matured { background: #d1fae5; color: #065f46; }
    .badge-withdrawn { background: #10b981; color: white; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
    .btn-action { padding: 8px 16px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .btn-mature { background: #8b5cf6; color: white; }
    .btn-withdraw { background: #10b981; color: white; }
    .btn-cancel { background: #fee2e2; color: #991b1b; }
    .user-card { display: flex; align-items: center; gap: 12px; padding: 16px; background: #f8fafc; border-radius: 12px; }
    .user-avatar { width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem; }
    .progress-bar { width: 100%; height: 10px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; margin: 8px 0; }
    .progress-fill { height: 100%; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 9999px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #f8fafc; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; }
    td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; color: #475569; }
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
            <h3 class="card-title">Contribution History</h3>
            @if($contributions->count() > 0)
            <table>
                <thead><tr><th>Date</th><th>Amount</th><th>Reference</th><th>Method</th></tr></thead>
                <tbody>
                    @foreach($contributions as $c)
                    <tr>
                        <td>{{ $c->contribution_date->format('d M Y') }}</td>
                        <td style="font-weight: 600; color: #10b981;">₦{{ number_format($c->amount, 2) }}</td>
                        <td style="font-family: monospace; font-size: 0.8rem;">{{ $c->payment_reference ?? 'N/A' }}</td>
                        <td>{{ $c->payment_method ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="color: #94a3b8;">No contributions yet.</p>
            @endif
        </div>

        <div class="card">
            <h3 class="card-title">Actions</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                @if(in_array($supportPlan->status, ['active', 'fully_funded']))
                <form action="{{ route('admin.food-support.mark-matured', $supportPlan) }}" method="POST" style="display: inline;">
                    @csrf <button class="btn-action btn-mature">Mark Matured (+20%)</button>
                </form>
                @endif
                @if($supportPlan->status === 'matured')
                <form action="{{ route('admin.food-support.withdraw', $supportPlan) }}" method="POST" style="display: inline;">
                    @csrf <button class="btn-action btn-withdraw"
                        data-confirm='{"title":"Process Withdrawal?","text":"Process withdrawal? Distribution & voucher will be created.","btnText":"Process","btnClass":"confirm","iconClass":"warning"}'>Process Withdrawal</button>
                </form>
                @endif
                @if(!in_array($supportPlan->status, ['matured', 'withdrawn', 'cancelled']))
                <form action="{{ route('admin.food-support.cancel', $supportPlan) }}" method="POST" style="display: inline;">
                    @csrf <button class="btn-action btn-cancel"
                        data-confirm='{"title":"Cancel Plan?","text":"Cancel this plan?","btnText":"Cancel","btnClass":"confirm-danger","iconClass":"danger"}'>Cancel</button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div>
        <div class="card" style="margin-bottom: 24px;">
            <h3 class="card-title">User Info</h3>
            <div class="user-card">
                <div class="user-avatar">{{ substr($supportPlan->user?->first_name ?? 'U', 0, 1) }}</div>
                <div>
                    <div style="font-weight: 600; color: #1e293b;">{{ $supportPlan->user?->full_name ?? 'N/A' }}</div>
                    <div style="font-size: 0.8rem; color: #64748b;">{{ $supportPlan->user?->email ?? '' }}</div>
                </div>
            </div>
        </div>

        <div class="card">
            <h3 class="card-title">Plan Summary</h3>
            <div class="info-row"><span class="info-label">Reference</span><span class="info-value" style="font-family: monospace;">{{ $supportPlan->reference }}</span></div>
            <div class="info-row"><span class="info-label">Plan Type</span><span class="info-value">{{ str_replace('_', ' ', $supportPlan->plan_type) }}</span></div>
            <div class="info-row"><span class="info-label">Target Value</span><span class="info-value">₦{{ number_format($supportPlan->plan_value, 2) }}</span></div>
            <div class="info-row"><span class="info-label">Total Contributed</span><span class="info-value">₦{{ number_format($supportPlan->total_contributed, 2) }}</span></div>
            <div class="info-row"><span class="info-label">20% Bonus</span><span class="info-value" style="color: #10b981;">₦{{ number_format($supportPlan->top_up_amount, 2) }}</span></div>
            <div class="info-row"><span class="info-label">Total Payout</span><span class="info-value" style="color: #10b981;">₦{{ number_format($supportPlan->total_payout_value, 2) }}</span></div>
            <div class="info-row"><span class="info-label">Status</span><span class="info-value"><span class="badge badge-{{ $supportPlan->status }}">{{ str_replace('_', ' ', $supportPlan->status) }}</span></span></div>
            <div class="info-row"><span class="info-label">Started</span><span class="info-value">{{ $supportPlan->start_date ? $supportPlan->start_date->format('d M Y') : 'N/A' }}</span></div>
            <div class="info-row"><span class="info-label">Maturity</span><span class="info-value">{{ $supportPlan->maturity_date ? $supportPlan->maturity_date->format('d M Y') : 'N/A' }}</span></div>
            @if($supportPlan->withdrawn_at)
            <div class="info-row"><span class="info-label">Withdrawn</span><span class="info-value">{{ $supportPlan->withdrawn_at->format('d M Y') }}</span></div>
            @endif
            <div class="progress-bar" style="margin-top: 16px;"><div style="width: {{ $supportPlan->plan_value > 0 ? min(100, ($supportPlan->total_contributed / $supportPlan->plan_value) * 100) : 0 }}%;" class="progress-fill"></div></div>
            <div style="text-align: right; font-size: 0.8rem; color: #64748b; margin-top: 4px;">
                {{ $supportPlan->plan_value > 0 ? round(($supportPlan->total_contributed / $supportPlan->plan_value) * 100) : 0 }}% funded
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.user')
@section('title', 'Food Support Plan')
@section('header', 'Support Plan Details')
@section('subheader', 'Reference: ' . $supportPlan->reference)

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">{{ str_replace('_', ' ', ucfirst($supportPlan->plan_type)) }} Plan</h3>
            <p class="page-card-subtitle">{{ $supportPlan->reference }}</p>
        </div>
        @php
            $statusClasses = ['pending' => 'badge-warning', 'active' => 'badge-info', 'fully_funded' => 'badge-success', 'matured' => 'badge-success', 'withdrawn' => 'badge-success', 'cancelled' => 'badge-error'];
        @endphp
        <span class="badge {{ $statusClasses[$supportPlan->status] ?? 'badge-warning' }}">{{ str_replace('_', ' ', ucfirst($supportPlan->status)) }}</span>
    </div>
    <div class="page-card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <style>
            .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; }
            .detail-item { background: #f8fafc; border-radius: 12px; padding: 1rem; }
            .detail-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
            .detail-value { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
            .detail-value.green { color: #10b981; }
            .detail-value.blue { color: #3b82f6; }
            .contribution-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
            .contribution-table th { background: #f8fafc; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; }
            .contribution-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; color: #475569; }
        </style>

        <div style="width: 100%; height: 10px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; margin-bottom: 1.5rem;">
            <div style="height: 100%; width: {{ $progress }}%; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 9999px; transition: width 0.5s;"></div>
        </div>
        <div style="text-align: center; font-size: 0.85rem; color: #64748b; margin-bottom: 1.5rem;">
            {{ round($progress) }}% funded — ₦{{ number_format($supportPlan->total_contributed, 2) }} of ₦{{ number_format($supportPlan->plan_value, 2) }}
        </div>

        <div class="detail-grid">
            <div class="detail-item"><div class="detail-label">Target</div><div class="detail-value blue">₦{{ number_format($supportPlan->plan_value, 2) }}</div></div>
            <div class="detail-item"><div class="detail-label">Saved So Far</div><div class="detail-value green">₦{{ number_format($supportPlan->total_contributed, 2) }}</div></div>
            <div class="detail-item"><div class="detail-label">20% Bonus</div><div class="detail-value green">₦{{ number_format($supportPlan->top_up_amount, 2) }}</div></div>
            <div class="detail-item"><div class="detail-label">Total Payout</div><div class="detail-value green">₦{{ number_format($supportPlan->total_payout_value, 2) }}</div></div>
        </div>

        @if($supportPlan->maturity_date)
        <div style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 12px; padding: 1.25rem; color: white; margin-bottom: 1.5rem; text-align: center;">
            <div style="font-size: 0.8rem; opacity: 0.9;">Maturity Date</div>
            <div style="font-size: 1.5rem; font-weight: 700;">{{ $supportPlan->maturity_date->format('d F Y') }}</div>
            @php $daysLeft = now()->startOfDay()->diffInDays($supportPlan->maturity_date, false); @endphp
            @if($daysLeft > 0)
                <div style="font-size: 0.85rem; opacity: 0.9;">{{ $daysLeft }} days remaining</div>
            @elseif($supportPlan->status === 'matured' || $supportPlan->status === 'withdrawn')
                <div style="font-size: 0.85rem; opacity: 0.9;">Plan has matured</div>
            @else
                <div style="font-size: 0.85rem; opacity: 0.9;">Ready to mature! Contact admin.</div>
            @endif
        </div>
        @endif

        @if(in_array($supportPlan->status, ['pending', 'active', 'fully_funded']) && !$supportPlan->isMatured())
        <div style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; padding: 1.25rem; color: white; margin-bottom: 1.5rem; text-align: center;">
            <div style="font-size: 0.8rem; opacity: 0.9; margin-bottom: 8px;">Make a Contribution</div>
            <form action="{{ route('user.food-support.contribute', $supportPlan) }}" method="POST" style="display: flex; gap: 8px;">
                @csrf
                <input type="number" name="amount" class="form-control" min="100" step="100" placeholder="Amount" required style="flex: 1; padding: 10px; border: none; border-radius: 8px; font-size: 1rem;" value="{{ number_format($supportPlan->plan_value - $supportPlan->total_contributed, 2) }}">
                <button type="submit" class="btn" style="background: white; color: #10b981; font-weight: 700; white-space: nowrap;">Pay Now</button>
            </form>
        </div>
        @endif

        <h4 style="color: #1e293b; margin: 1.5rem 0 0.5rem;">Contribution History</h4>
        @if($contributions->count() > 0)
        <table class="contribution-table">
            <thead><tr><th>Date</th><th>Amount</th><th>Reference</th></tr></thead>
            <tbody>
                @foreach($contributions as $c)
                <tr>
                    <td>{{ $c->contribution_date->format('d M Y') }}</td>
                    <td style="font-weight: 600; color: #10b981;">₦{{ number_format($c->amount, 2) }}</td>
                    <td style="font-family: monospace; font-size: 0.8rem;">{{ $c->payment_reference ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color: #94a3b8;">No contributions yet.</p>
        @endif
    </div>
</div>
@endsection

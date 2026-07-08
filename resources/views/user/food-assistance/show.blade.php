@extends('layouts.user')
@section('title', 'Food Assistance Plan')
@section('header', 'Plan Details')
@section('subheader', 'Reference: ' . $assistancePlan->reference)

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">{{ $assistancePlan->tier->name ?? 'N/A' }} Plan</h3>
            <p class="page-card-subtitle">{{ $assistancePlan->reference }}</p>
        </div>
        @php
            $statusClasses = ['pending' => 'badge-warning', 'processing_fee_paid' => 'badge-info', 'first_installment_paid' => 'badge-info', 'active' => 'badge-success', 'completed' => 'badge-success', 'cancelled' => 'badge-error'];
        @endphp
        <span class="badge {{ $statusClasses[$assistancePlan->status] ?? 'badge-warning' }}">{{ str_replace('_', ' ', ucfirst($assistancePlan->status)) }}</span>
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
            .detail-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px; }
            .detail-value { font-size: 1.1rem; font-weight: 700; color: #1e293b; }
            .detail-value.green { color: #10b981; }
            .detail-value.blue { color: #3b82f6; }
            .detail-value.red { color: #ef4444; }
            .installment-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
            .installment-table th { background: #f8fafc; padding: 12px; text-align: left; font-size: 0.75rem; font-weight: 600; color: #64748b; text-transform: uppercase; }
            .installment-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 0.85rem; color: #475569; }
            .action-btn { width: 100%; padding: 12px; border-radius: 12px; font-weight: 600; border: none; cursor: pointer; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 8px; }
        </style>

        <div style="width: 100%; height: 10px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; margin-bottom: 1.5rem;">
            <div style="height: 100%; width: {{ $assistancePlan->getProgress() }}%; background: linear-gradient(135deg, #10b981, #059669); border-radius: 9999px; transition: width 0.5s;"></div>
        </div>
        <div style="text-align: center; font-size: 0.85rem; color: #64748b; margin-bottom: 1.5rem;">
            {{ round($assistancePlan->getProgress()) }}% complete — ₦{{ number_format($assistancePlan->total_paid, 2) }} paid of ₦{{ number_format($assistancePlan->total_amount + $assistancePlan->processing_fee, 2) }}
        </div>

        <div class="detail-grid">
            <div class="detail-item"><div class="detail-label">Plan Value</div><div class="detail-value green">₦{{ number_format($assistancePlan->total_amount, 2) }}</div></div>
            <div class="detail-item"><div class="detail-label">Processing Fee</div><div class="detail-value red">₦{{ number_format($assistancePlan->processing_fee, 2) }}</div></div>
            <div class="detail-item"><div class="detail-label">First Installment (30%)</div><div class="detail-value blue">₦{{ number_format($assistancePlan->first_installment_amount, 2) }}</div></div>
            <div class="detail-item"><div class="detail-label">Weekly Installment</div><div class="detail-value green">₦{{ number_format($assistancePlan->weekly_installment_amount, 2) }}</div></div>
        </div>

        @if($nextInstallment)
        <div style="background: linear-gradient(135deg, #10b981, #059669); border-radius: 12px; padding: 1.25rem; color: white; margin-bottom: 1.5rem; text-align: center;">
            <div style="font-size: 0.8rem; opacity: 0.9; margin-bottom: 4px;">Next Payment Due</div>
            <div style="font-size: 1.5rem; font-weight: 700;">₦{{ number_format($nextInstallment->amount_due, 2) }}</div>
            <div style="font-size: 0.8rem; opacity: 0.9; margin-bottom: 12px;">{{ $nextInstallment->type === 'processing_fee' ? 'Processing Fee' : ($nextInstallment->type === 'first_installment' ? 'First Installment' : 'Weekly Repayment') }} — Due {{ $nextInstallment->due_date->format('d M Y') }}</div>

            @if($nextInstallment->type === 'processing_fee')
            <form action="{{ route('user.food-assistance.pay-processing-fee', $assistancePlan) }}" method="POST">
                @csrf <button class="action-btn" style="background: white; color: #10b981;">Pay ₦{{ number_format($nextInstallment->amount_due, 2) }} Now</button>
            </form>
            @elseif($nextInstallment->type === 'first_installment')
            <form action="{{ route('user.food-assistance.pay-first-installment', $assistancePlan) }}" method="POST">
                @csrf <button class="action-btn" style="background: white; color: #10b981;">Pay ₦{{ number_format($nextInstallment->amount_due, 2) }} Now</button>
            </form>
            @elseif($nextInstallment->type === 'weekly_repayment')
            <form action="{{ route('user.food-assistance.pay-weekly-installment', $assistancePlan) }}" method="POST">
                @csrf <button class="action-btn" style="background: white; color: #10b981;">Pay ₦{{ number_format($nextInstallment->amount_due, 2) }} Now</button>
            </form>
            @endif
        </div>
        @endif

        <h4 style="color: #1e293b; margin: 1.5rem 0 0.5rem;">Installment Schedule</h4>
        @if($installments->count() > 0)
        <table class="installment-table">
            <thead><tr><th>#</th><th>Type</th><th>Due</th><th>Amount</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($installments as $inst)
                <tr>
                    <td>{{ $inst->installment_number }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($inst->type)) }}</td>
                    <td>{{ $inst->due_date->format('d M Y') }}</td>
                    <td style="font-weight: 600;">₦{{ number_format($inst->amount_due, 2) }}</td>
                    <td>
                        @if($inst->status === 'paid')
                            <span class="badge badge-success">Paid</span>
                        @elseif($inst->status === 'overdue')
                            <span class="badge badge-error">Overdue</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection

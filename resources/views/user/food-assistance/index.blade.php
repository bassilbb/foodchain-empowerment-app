@extends('layouts.user')
@section('title', 'Food Assistance Plan')
@section('header', 'Food Assistance Plan')
@section('subheader', 'Apply for food assistance and manage your plan')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Your Assistance Plans</h3>
            <p class="page-card-subtitle">Track your food assistance applications</p>
        </div>
        <a href="{{ route('user.food-assistance.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Apply Now
        </a>
    </div>
    <div class="page-card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if($plans->isEmpty())
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <h3 class="empty-title">No Plans Yet</h3>
                <p class="empty-text">Apply for the Food Assistance Plan to get food support with flexible weekly payments.</p>
                <a href="{{ route('user.food-assistance.create') }}" class="btn btn-primary" style="margin-top: 1rem;">Apply Now</a>
            </div>
        @else
            <div style="display: grid; gap: 1.5rem;">
                @foreach($plans as $plan)
                <div style="background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem; transition: all 0.3s;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div>
                            <h4 style="font-weight: 700; color: #1e293b; margin: 0;">{{ $plan->tier->name ?? 'N/A' }} Plan</h4>
                            <span style="font-size: 0.75rem; color: #64748b; font-family: monospace;">{{ $plan->reference }}</span>
                        </div>
                        @php
                            $statusClasses = ['pending' => 'badge-warning', 'processing_fee_paid' => 'badge-info', 'first_installment_paid' => 'badge-info', 'active' => 'badge-success', 'completed' => 'badge-success', 'cancelled' => 'badge-error'];
                            $class = $statusClasses[$plan->status] ?? 'badge-warning';
                        @endphp
                        <span class="badge {{ $class }}">{{ str_replace('_', ' ', ucfirst($plan->status)) }}</span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1rem;">
                        <div><span style="color: #64748b; font-size: 0.8rem;">Total</span><div style="font-weight: 700; color: #1e293b;">₦{{ number_format($plan->total_amount, 2) }}</div></div>
                        <div><span style="color: #64748b; font-size: 0.8rem;">Paid</span><div style="font-weight: 700; color: #10b981;">₦{{ number_format($plan->total_paid, 2) }}</div></div>
                        <div><span style="color: #64748b; font-size: 0.8rem;">Progress</span><div style="font-weight: 700; color: #3b82f6;">{{ round($plan->getProgress()) }}%</div></div>
                    </div>
                    <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; margin-bottom: 1rem;">
                        <div style="height: 100%; width: {{ $plan->getProgress() }}%; background: linear-gradient(135deg, #10b981, #059669); border-radius: 9999px;"></div>
                    </div>
                    <a href="{{ route('user.food-assistance.show', $plan) }}" class="btn btn-secondary" style="width: 100%; justify-content: center;">View Details</a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

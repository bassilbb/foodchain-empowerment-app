@extends('layouts.user')
@section('title', 'Food Support Plan')
@section('header', 'Food Support Plan')
@section('subheader', 'Save towards food and earn 20% bonus')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <h3 class="page-card-title">Your Support Plans</h3>
            <p class="page-card-subtitle">Track your savings and maturity</p>
        </div>
        <a href="{{ route('user.food-support.create') }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Start New Plan
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
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a4 4 0 00-4-4H6.5a2.5 2.5 0 110 0 2.5 2.5 0 010 5H12m0 0V6a4 4 0 00-4-4h1.5a2.5 2.5 0 110 5H12m0 0V6a4 4 0 014-4h1.5a2.5 2.5 0 110 5H12"></path></svg>
                </div>
                <h3 class="empty-title">No Plans Yet</h3>
                <p class="empty-text">Start a Food Support Plan today. Save towards food and earn 20% bonus at maturity.</p>
                <a href="{{ route('user.food-support.create') }}" class="btn btn-primary" style="margin-top: 1rem;">Start a Plan</a>
            </div>
        @else
            <div style="display: grid; gap: 1.5rem;">
                @foreach($plans as $plan)
                @php
                    $progress = $plan->plan_value > 0 ? min(100, ($plan->total_contributed / $plan->plan_value) * 100) : 0;
                    $statusClasses = ['pending' => 'badge-warning', 'active' => 'badge-info', 'fully_funded' => 'badge-success', 'matured' => 'badge-success', 'withdrawn' => 'badge-success', 'cancelled' => 'badge-error'];
                @endphp
                <div style="background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%); border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.5rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                        <div>
                            <h4 style="font-weight: 700; color: #1e293b; margin: 0;">{{ str_replace('_', ' ', ucfirst($plan->plan_type)) }} Plan</h4>
                            <span style="font-size: 0.75rem; color: #64748b; font-family: monospace;">{{ $plan->reference }}</span>
                        </div>
                        <span class="badge {{ $statusClasses[$plan->status] ?? 'badge-warning' }}">{{ str_replace('_', ' ', ucfirst($plan->status)) }}</span>
                    </div>
                    <div style="width: 100%; height: 6px; background: #e2e8f0; border-radius: 9999px; overflow: hidden; margin-bottom: 0.75rem;">
                        <div style="height: 100%; width: {{ $progress }}%; background: linear-gradient(135deg, #3b82f6, #2563eb); border-radius: 9999px;"></div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem;">
                        <div><span style="color: #64748b; font-size: 0.8rem;">Saved</span><div style="font-weight: 700;">₦{{ number_format($plan->total_contributed, 2) }}</div></div>
                        <div><span style="color: #64748b; font-size: 0.8rem;">Target</span><div style="font-weight: 700;">₦{{ number_format($plan->plan_value, 2) }}</div></div>
                        <div><span style="color: #64748b; font-size: 0.8rem;">Bonus</span><div style="font-weight: 700; color: #10b981;">+20%</div></div>
                    </div>
                    @if($plan->maturity_date)
                    <div style="font-size: 0.85rem; color: #64748b; margin-bottom: 1rem;">
                        Matures: {{ $plan->maturity_date->format('d M Y') }}
                        @if($plan->isMatured() && $plan->status !== 'matured')
                            <span style="color: #10b981; font-weight: 600;">(Ready!)</span>
                        @endif
                    </div>
                    @endif
                    <a href="{{ route('user.food-support.show', $plan) }}" class="btn btn-secondary" style="width: 100%; justify-content: center;">View Details</a>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection

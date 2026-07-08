@extends('layouts.user')
@section('title', 'Create Union')
@section('header', 'Create Union')
@section('subheader', 'Start a new union and invite members')

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <a href="{{ route('unions.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Unions
            </a>
            <h3 class="page-card-title" style="margin-top: 0.5rem;">Create a New Union</h3>
            <p class="page-card-subtitle">Start a community and apply for food loans together</p>
        </div>
    </div>
    <div class="page-card-body">
        @if(session('error'))
            <div class="alert alert-error" style="margin-bottom: 1.5rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                {{ session('error') }}
            </div>
        @endif

        <div style="background: linear-gradient(135deg, #ecfdf5 0%, #eff6ff 100%); border: 1px solid #d1fae5; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
            <h3 style="font-weight: 600; color: #065f46; margin: 0 0 1rem 0; font-size: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Union Requirements
            </h3>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #d1fae5; display: flex; align-items: center; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #059669;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Min Members</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $tier->union_min_members ?? 10 }}</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #dbeafe; display: flex; align-items: center; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #2563eb;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Equity Required</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $tier->union_equity_percentage ?? 30 }}%</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #fef3c7; display: flex; align-items: center; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #d97706;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Loan Tenor</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $tier->union_loan_tenor_months ?? 3 }} months</p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #f3e8ff; display: flex; align-items: center; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #9333ea;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </div>
                    <div>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0;">Repayment</p>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">Every {{ $tier->union_repayment_frequency_weeks ?? 2 }} weeks</p>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('unions.store') }}" method="POST" style="display: grid; gap: 1.25rem;">
            @csrf
            <div class="form-group">
                <label class="form-label">Union Name</label>
                <input type="text" name="name" required class="form-input" placeholder="e.g., Alaba Market Union">
                @error('name')
                    <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Your Equity Contribution (Optional)</label>
                <div style="position: relative;">
                    <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #64748b;">₦</span>
                    <input type="number" name="equity_contribution" min="0" step="100" class="form-input" style="padding-left: 2rem;" placeholder="0.00">
                </div>
                <p style="font-size: 0.75rem; color: #64748b; margin-top: 0.5rem;">Contributing equity now helps your union qualify for loans faster</p>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: #d1fae5; display: flex; align-items: center; justify-content: center;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px; color: #059669;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">Your Tier: {{ $tier->name }}</p>
                    </div>
                </div>
                <p style="font-size: 0.875rem; color: #475569; margin: 0;">As the union leader, you need an active membership. Once your union reaches {{ $tier->union_min_members ?? 10 }} members with required equity, you can apply for a food loan.</p>
            </div>

            <div style="display: flex; gap: 0.75rem; padding-top: 0.5rem;">
                <a href="{{ route('unions.index') }}" class="btn btn-secondary" style="flex: 1; justify-content: center;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="flex: 1; justify-content: center;">Create Union</button>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('admin.layouts.app')

@section('title', 'User Referrals')
@section('header', 'Referral Tree')
@section('subheader', $user->full_name . ' - Referral Network')

@section('content')
<div class="page-card" style="margin-bottom: 1.5rem;">
    <div class="page-card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin: 0;">{{ $user->full_name }}</h3>
            <p style="font-size: 0.875rem; color: #64748b; margin: 0.25rem 0 0;">{{ $user->email }} · {{ $user->referral_code }}</p>
        </div>
        <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
            Back to User
        </a>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3 style="font-size: 1rem; font-weight: 700; color: #1e293b; margin: 0;">Direct Referrals ({{ count($referralTree) }})</h3>
    </div>
    <div class="page-card-body" style="padding-top: 0;">
        @if(count($referralTree) === 0)
        <div style="padding: 2rem; text-align: center; color: #94a3b8;">
            <p>This user has no referrals yet.</p>
        </div>
        @else
        <div style="display: grid; gap: 0.75rem;">
            @foreach($referralTree as $referral)
            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden;">
                <div style="padding: 1rem; display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.875rem; flex-shrink: 0;">
                        {{ substr($referral['name'] ?? 'U', 0, 1) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $referral['name'] ?? 'N/A' }}</p>
                        <p style="font-size: 0.8rem; color: #64748b; margin: 0.125rem 0 0;">{{ $referral['email'] ?? '' }}</p>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <span class="badge" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; font-size: 0.625rem;">{{ $referral['tier'] ?? 'N/A' }}</span>
                        <p style="font-size: 0.75rem; color: #64748b; margin: 0.25rem 0 0;">{{ $referral['referral_count'] ?? 0 }} referrals</p>
                        <p style="font-size: 0.7rem; color: #94a3b8; margin: 0;">Joined {{ $referral['joined_at'] ?? 'N/A' }}</p>
                    </div>
                </div>
                @if(!empty($referral['children']))
                <div style="padding: 0 1rem 1rem 3rem; display: grid; gap: 0.5rem;">
                    @foreach($referral['children'] as $child)
                    <div style="padding: 0.75rem; background: white; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 0.75rem; color: #475569; flex-shrink: 0;">
                            {{ substr($child['name'] ?? 'U', 0, 1) }}
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-weight: 500; color: #1e293b; margin: 0; font-size: 0.875rem;">{{ $child['name'] ?? 'N/A' }}</p>
                            <p style="font-size: 0.75rem; color: #64748b; margin: 0;">{{ $child['email'] ?? '' }}</p>
                        </div>
                        <span class="badge badge-info" style="font-size: 0.625rem;">Level 2</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
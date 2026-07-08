@extends('admin.layouts.app')

@section('title', $membershipTier->name . ' Members')
@section('page-title', 'Tier Members')

@section('header', $membershipTier->name . ' Members')
@section('subheader', 'Members in this membership tier')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.membership-tiers.show', $membershipTier) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #10b981; font-weight: 500; text-decoration: none;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Tier
    </a>
</div>

<div class="page-card" style="overflow: hidden;">
    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                <tr>
                    <th style="text-align: left; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Member</th>
                    <th style="text-align: left; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Email</th>
                    <th style="text-align: left; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Tier</th>
                    <th style="text-align: left; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Status</th>
                    <th style="text-align: left; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Joined</th>
                    <th style="text-align: right; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Actions</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid #f1f5f9;">
                @forelse($members as $member)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem 1.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 2.5rem; height: 2.5rem; border-radius: 50%; background: linear-gradient(135deg, #10b981, #0ea5e9); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                                {{ substr($member->first_name, 0, 1) }}
                            </div>
                            <div>
                                <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $member->full_name }}</p>
                                <p style="font-size: 0.875rem; color: #64748b; margin: 0;">Ref: {{ $member->referral_code }}</p>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1rem 1.5rem; color: #475569;">{{ $member->email }}</td>
                    <td style="padding: 1rem 1.5rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: white;
                            @if(($member->currentTier->slug ?? 'bronze') === 'bronze') background: linear-gradient(135deg, #f59e0b, #d97706); @endif
                            @if(($member->currentTier->slug ?? 'bronze') === 'silver') background: linear-gradient(135deg, #64748b, #475569); @endif
                            @if(($member->currentTier->slug ?? 'bronze') === 'gold') background: linear-gradient(135deg, #eab308, #ca8a04); @endif
                            @if(($member->currentTier->slug ?? 'bronze') === 'emerald') background: linear-gradient(135deg, #10b981, #059669); @endif">
                            {{ $member->currentTier->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td style="padding: 1rem 1.5rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
                            @if($member->is_active) background: #dcfce7; color: #16a34a; @else background: #f1f5f9; color: #64748b; @endif">
                            {{ $member->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td style="padding: 1rem 1.5rem; color: #64748b;">{{ $member->created_at->format('M d, Y') }}</td>
                    <td style="padding: 1rem 1.5rem; text-align: right;">
                        <a href="{{ route('admin.users.show', ['user' => $member->id]) }}" style="color: #10b981; font-weight: 500; text-decoration: none;">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 2rem; text-align: center; color: #64748b;">No members found in this tier</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($members->hasPages())
    <div style="padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9;">
        {{ $members->links() }}
    </div>
    @endif
</div>
@endsection

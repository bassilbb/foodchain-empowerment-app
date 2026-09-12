@extends('admin.layouts.app')

@section('title', $membershipTier->name . ' Unions')
@section('page-title', 'Tier Unions')

@section('header', $membershipTier->name . ' Unions')
@section('subheader', 'Unions in this membership tier')

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
                    <th style="text-align: left; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Union Name</th>
                    <th style="text-align: left; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Leader</th>
                    <th style="text-align: left; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Members</th>
                    <th style="text-align: left; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Status</th>
                    <th style="text-align: left; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Created</th>
                    <th style="text-align: right; padding: 1rem 1.5rem; font-size: 0.875rem; font-weight: 600; color: #475569;">Actions</th>
                </tr>
            </thead>
            <tbody style="border-top: 1px solid #f1f5f9;">
                @forelse($unions as $union)
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1rem 1.5rem;">
                        <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $union->name }}</p>
                        <p style="font-size: 0.875rem; color: #64748b; margin: 0;">Code: {{ $union->union_code }}</p>
                    </td>
                    <td style="padding: 1rem 1.5rem;">
                        @if($union->leader)
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="width: 2rem; height: 2rem; border-radius: 50%; background: linear-gradient(135deg, #10b981, #0ea5e9); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem; font-weight: 700;">
                                {{ substr($union->leader->first_name, 0, 1) }}
                            </div>
                            <span style="color: #1e293b;">{{ $union->leader->full_name }}</span>
                        </div>
                        @else
                        <span style="color: #94a3b8;">No leader</span>
                        @endif
                    </td>
                    <td style="padding: 1rem 1.5rem; color: #475569;">{{ $union->members_count ?? $union->members->count() }}</td>
                    <td style="padding: 1rem 1.5rem;">
                        <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
                            @if($union->status === 'active') background: #dcfce7; color: #16a34a; @endif
                            @if($union->status === 'pending') background: #fef3c7; color: #d97706; @endif
                            @if(in_array($union->status, ['disbursed', 'repaying'])) background: #dbeafe; color: #2563eb; @endif
                            @if($union->status === 'completed') background: #f1f5f9; color: #475569; @endif">
                            {{ ucfirst($union->status) }}
                        </span>
                    </td>
                    <td style="padding: 1rem 1.5rem; color: #64748b;">{{ $union->created_at->format('M d, Y') }}</td>
                    <td style="padding: 1rem 1.5rem; text-align: right;">
                        <a href="{{ route('unions.show', $union) }}" style="color: #10b981; font-weight: 500; text-decoration: none;">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 2rem; text-align: center; color: #64748b;">No unions found in this tier</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($unions->hasPages())
    <div style="padding: 1rem 1.5rem; border-top: 1px solid #f1f5f9;">
        {{ $unions->links() }}
    </div>
    @endif
</div>
@endsection

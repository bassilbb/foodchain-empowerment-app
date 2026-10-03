@extends('admin.layouts.app')

@section('title', 'Membership Tiers')
@section('header', 'Membership Tiers')
@section('subheader', 'Manage membership grade levels and benefits')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
    <p style="color: #64748b; margin: 0;">Configure the 4-tier membership system (Bronze, Silver, Gold, Emerald)</p>
    <a href="{{ route('admin.membership-tiers.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add New Tier
    </a>
</div>

<!-- Tiers Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    @foreach($tiers as $tier)
    <div class="page-card" style="position: relative; overflow: hidden;">
        @if($tier->slug === 'bronze')
        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: #f59e0b; border-radius: 50%; filter: blur(40px); opacity: 0.2;"></div>
        @elseif($tier->slug === 'silver')
        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: #64748b; border-radius: 50%; filter: blur(40px); opacity: 0.2;"></div>
        @elseif($tier->slug === 'gold')
        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: #eab308; border-radius: 50%; filter: blur(40px); opacity: 0.2;"></div>
        @else
        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: #10b981; border-radius: 50%; filter: blur(40px); opacity: 0.2;"></div>
        @endif

        <div style="padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    @if($tier->slug === 'bronze') background: linear-gradient(135deg, #f59e0b, #d97706); @endif
                    @if($tier->slug === 'silver') background: linear-gradient(135deg, #64748b, #475569); @endif
                    @if($tier->slug === 'gold') background: linear-gradient(135deg, #eab308, #ca8a04); @endif
                    @if($tier->slug === 'emerald') background: linear-gradient(135deg, #10b981, #059669); @endif">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 24px; height: 24px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <span class="badge badge-{{ $tier->is_active ? 'success' : 'dark' }}">
                    {{ $tier->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>

            <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin: 0 0 0.25rem 0;">{{ $tier->name }}</h3>
            <p style="font-size: 0.875rem; color: #64748b; margin: 0 0 1rem 0;">Min. {{ $tier->min_referrals }} referrals</p>

            <div style="display: grid; gap: 0.5rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                    <span style="color: #64748b;">Annual Fee</span>
                    <span style="font-weight: 600; color: #1e293b;">{{ format_currency($tier->annual_fee) }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                    <span style="color: #64748b;">Union Size</span>
                    <span style="font-weight: 600; color: #1e293b;">{{ $tier->union_min_members }}+</span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                    <span style="color: #64748b;">Community</span>
                    <span style="font-weight: 600; color: #1e293b;">{{ $tier->community_min_members }}-{{ $tier->community_max_members }}</span>
                </div>
            </div>

            <div style="display: flex; gap: 0.5rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                <a href="{{ route('admin.membership-tiers.show', $tier) }}" class="btn btn-secondary" style="flex: 1;">
                    View Details
                </a>
                <a href="{{ route('admin.membership-tiers.edit', $tier) }}" class="btn btn-secondary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Benefits Overview -->
<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Tier Benefits Comparison</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="text-align: left;">Benefit</th>
                    @foreach($tiers as $tier)
                    <th style="text-align: center;">{{ $tier->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Annual Fee</td>
                    @foreach($tiers as $tier)
                    <td style="text-align: center; font-weight: 600; color: #1e293b;">{{ format_currency($tier->annual_fee) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td>Min. Referrals</td>
                    @foreach($tiers as $tier)
                    <td style="text-align: center; font-weight: 600; color: #1e293b;">{{ $tier->min_referrals }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td>Free Food Pack</td>
                    @foreach($tiers as $tier)
                    <td style="text-align: center; font-weight: 600; color: #1e293b; text-transform: capitalize;">{{ $tier->free_food_pack_type }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td>Delivery</td>
                    @foreach($tiers as $tier)
                    <td style="text-align: center; font-weight: 600; color: #1e293b;">
                        @if($tier->free_delivery)
                        <span style="color: #10b981;">Free</span>
                        @elseif($tier->has_delivery)
                        <span>{{ $tier->delivery_subsidy_percentage }}% subsidized</span>
                        @else
                        <span style="color: #94a3b8;">Not available</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <td>Union Loan Equity</td>
                    @foreach($tiers as $tier)
                    <td style="text-align: center; font-weight: 600; color: #1e293b;">{{ $tier->union_equity_percentage }}%</td>
                    @endforeach
                </tr>
                <tr>
                    <td>Loan Tenor</td>
                    @foreach($tiers as $tier)
                    <td style="text-align: center; font-weight: 600; color: #1e293b;">{{ $tier->union_loan_tenor_months }} months</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', 'Membership Tiers')
@section('page-title', 'Membership Tiers Management')

@section('header', 'Membership Tiers')
@section('subheader', 'Manage membership grade levels and benefits')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <p class="text-slate-500">Configure the 4-tier membership system (Bronze, Silver, Gold, Emerald)</p>
    </div>
    <a href="{{ route('admin.membership-tiers.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transform hover:-translate-y-0.5 transition-all">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Add New Tier
    </a>
</div>

<!-- Tiers Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @foreach($tiers as $tier)
    <div class="card-3d p-6 relative overflow-hidden">
        @if($tier->slug === 'bronze')
            <div class="absolute top-0 right-0 w-24 h-24 bg-amber-400 rounded-full blur-[50px] opacity-20"></div>
        @elseif($tier->slug === 'silver')
            <div class="absolute top-0 right-0 w-24 h-24 bg-slate-400 rounded-full blur-[50px] opacity-20"></div>
        @elseif($tier->slug === 'gold')
            <div class="absolute top-0 right-0 w-24 h-24 bg-yellow-400 rounded-full blur-[50px] opacity-20"></div>
        @else
            <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-400 rounded-full blur-[50px] opacity-20"></div>
        @endif
        
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center
                    {{ $tier->slug === 'bronze' ? 'bg-gradient-to-br from-amber-400 to-amber-600' : '' }}
                    {{ $tier->slug === 'silver' ? 'bg-gradient-to-br from-slate-400 to-slate-600' : '' }}
                    {{ $tier->slug === 'gold' ? 'bg-gradient-to-br from-yellow-400 to-yellow-600' : '' }}
                    {{ $tier->slug === 'emerald' ? 'bg-gradient-to-br from-emerald-400 to-emerald-600' : '' }}
                    text-white shadow-lg">
                    @if($tier->slug === 'bronze')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    @elseif($tier->slug === 'silver')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    @elseif($tier->slug === 'gold')
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    @endif
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $tier->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                    {{ $tier->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            
            <h3 class="text-xl font-bold text-slate-800 mb-1">{{ $tier->name }}</h3>
            <p class="text-sm text-slate-500">Min. {{ $tier->min_referrals }} referrals</p>
            
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Annual Fee</span>
                    <span class="font-semibold text-slate-800">{{ format_currency($tier->annual_fee) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Union Size</span>
                    <span class="font-semibold text-slate-800">{{ $tier->union_min_members }}+</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-slate-500">Community</span>
                    <span class="font-semibold text-slate-800">{{ $tier->community_min_members }}-{{ $tier->community_max_members }}</span>
                </div>
            </div>
            
            <div class="flex gap-2 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.membership-tiers.show', $tier) }}" class="flex-1 px-3 py-2 text-center text-sm font-medium text-primary-600 bg-primary-50 rounded-lg hover:bg-primary-100 transition-colors">
                    View Details
                </a>
                <a href="{{ route('admin.membership-tiers.edit', $tier) }}" class="px-3 py-2 text-center text-sm font-medium text-slate-600 bg-slate-50 rounded-lg hover:bg-slate-100 transition-colors">
                    <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Benefits Overview -->
<div class="card-3d p-6">
    <h3 class="text-lg font-bold text-slate-800 mb-6">Tier Benefits Comparison</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200">
                    <th class="text-left py-3 px-4 text-sm font-semibold text-slate-600">Benefit</th>
                    @foreach($tiers as $tier)
                    <th class="text-center py-3 px-4 text-sm font-semibold text-slate-600">{{ $tier->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="py-3 px-4 text-sm text-slate-600">Annual Fee</td>
                    @foreach($tiers as $tier)
                    <td class="py-3 px-4 text-sm text-center font-medium text-slate-800">{{ format_currency($tier->annual_fee) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="py-3 px-4 text-sm text-slate-600">Min. Referrals</td>
                    @foreach($tiers as $tier)
                    <td class="py-3 px-4 text-sm text-center font-medium text-slate-800">{{ $tier->min_referrals }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="py-3 px-4 text-sm text-slate-600">Free Food Pack</td>
                    @foreach($tiers as $tier)
                    <td class="py-3 px-4 text-sm text-center font-medium text-slate-800">{{ ucfirst($tier->free_food_pack_type) }}</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="py-3 px-4 text-sm text-slate-600">Delivery</td>
                    @foreach($tiers as $tier)
                    <td class="py-3 px-4 text-sm text-center font-medium text-slate-800">
                        @if($tier->free_delivery)
                            <span class="text-green-600">Free</span>
                        @elseif($tier->has_delivery)
                            <span>{{ $tier->delivery_subsidy_percentage }}% subsidized</span>
                        @else
                            <span class="text-slate-400">Not available</span>
                        @endif
                    </td>
                    @endforeach
                </tr>
                <tr>
                    <td class="py-3 px-4 text-sm text-slate-600">Union Loan Equity</td>
                    @foreach($tiers as $tier)
                    <td class="py-3 px-4 text-sm text-center font-medium text-slate-800">{{ $tier->union_equity_percentage }}%</td>
                    @endforeach
                </tr>
                <tr>
                    <td class="py-3 px-4 text-sm text-slate-600">Loan Tenor</td>
                    @foreach($tiers as $tier)
                    <td class="py-3 px-4 text-sm text-center font-medium text-slate-800">{{ $tier->union_loan_tenor_months }} months</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@extends('admin.layouts.app')

@section('title', $membershipTier->name . ' Members')
@section('page-title', 'Tier Members')

@section('header', $membershipTier->name . ' Members')
@section('subheader', 'Members in this membership tier')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.membership-tiers.show', $membershipTier) }}" class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Tier
    </a>
</div>

<div class="card-3d overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Member</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Email</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Tier</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Status</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Joined</th>
                    <th class="text-right py-4 px-6 text-sm font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($members as $member)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-accent-400 flex items-center justify-center text-white font-bold">
                                {{ substr($member->first_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">{{ $member->full_name }}</p>
                                <p class="text-sm text-slate-500">Ref: {{ $member->referral_code }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-slate-600">{{ $member->email }}</td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ tier_badge_color($member->currentTier->slug ?? 'bronze') }} text-white">
                            {{ $member->currentTier->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $member->is_active ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' }}">
                            {{ $member->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-slate-500">{{ $member->created_at->format('M d, Y') }}</td>
                    <td class="py-4 px-6 text-right">
                        <a href="{{ route('admin.users.show', ['user' => $member->id]) }}" class="text-primary-600 hover:text-primary-700 font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500">No members found in this tier</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($members->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $members->links() }}
    </div>
    @endif
</div>
@endsection

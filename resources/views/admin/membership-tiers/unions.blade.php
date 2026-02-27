@extends('admin.layouts.app')

@section('title', $membershipTier->name . ' Unions')
@section('page-title', 'Tier Unions')

@section('header', $membershipTier->name . ' Unions')
@section('subheader', 'Unions in this membership tier')

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
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Union Name</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Leader</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Members</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Status</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Created</th>
                    <th class="text-right py-4 px-6 text-sm font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($unions as $union)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-6">
                        <p class="font-medium text-slate-800">{{ $union->name }}</p>
                        <p class="text-sm text-slate-500">Code: {{ $union->union_code }}</p>
                    </td>
                    <td class="py-4 px-6">
                        @if($union->leader)
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-accent-400 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($union->leader->first_name, 0, 1) }}
                            </div>
                            <span class="text-slate-800">{{ $union->leader->full_name }}</span>
                        </div>
                        @else
                        <span class="text-slate-400">No leader</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-slate-600">{{ $union->members_count ?? $union->members->count() }}</td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $union->status === 'active' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $union->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ in_array($union->status, ['disbursed', 'repaying']) ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $union->status === 'completed' ? 'bg-slate-100 text-slate-700' : '' }}">
                            {{ ucfirst($union->status) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-slate-500">{{ $union->created_at->format('M d, Y') }}</td>
                    <td class="py-4 px-6 text-right">
                        <a href="{{ route('admin.unions.show', $union) }}" class="text-primary-600 hover:text-primary-700 font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-slate-500">No unions found in this tier</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($unions->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $unions->links() }}
    </div>
    @endif
</div>
@endsection

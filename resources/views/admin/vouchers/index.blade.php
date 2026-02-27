@extends('admin.layouts.app')

@section('title', 'Vouchers')
@section('page-title', 'Voucher Management')

@section('header', 'Vouchers')
@section('subheader', 'Manage food collection vouchers')

@section('content')
<!-- Create Voucher -->
<div class="card-3d p-6 mb-6">
    <h3 class="text-lg font-bold text-slate-800 mb-4">Issue New Voucher</h3>
    <form method="POST" action="{{ route('admin.vouchers.store') }}" class="flex flex-wrap gap-4 items-end">
        @csrf
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-slate-700 mb-2">User</label>
            <select name="user_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500" required>
                <option value="">Select User</option>
                @foreach(\App\Models\User::where('is_active', true)->limit(50)->get() as $user)
                <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-slate-700 mb-2">Distribution (optional)</label>
            <select name="food_distribution_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500">
                <option value="">Select Distribution</option>
                @foreach(\App\Models\FoodDistribution::where('status', 'approved')->limit(20)->get() as $dist)
                <option value="{{ $dist->id }}">{{ $dist->name }} - {{ $dist->distribution_date->format('M d, Y') }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-5 py-2.5 bg-primary-500 text-white font-medium rounded-xl hover:bg-primary-600 transition-colors">
            Issue Voucher
        </button>
    </form>
</div>

<!-- Filters -->
<div class="card-3d p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by voucher number, collection code..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500">
        </div>
        <select name="status" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Issued</option>
            <option value="collected" {{ request('status') === 'collected' ? 'selected' : '' }}>Collected</option>
            <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
        </select>
        <button type="submit" class="px-5 py-2.5 bg-primary-500 text-white font-medium rounded-xl hover:bg-primary-600 transition-colors">
            Filter
        </button>
    </form>
</div>

<!-- Vouchers Table -->
<div class="card-3d overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Voucher #</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">User</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Collection Code</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Distribution</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Status</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Created</th>
                    <th class="text-right py-4 px-6 text-sm font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($vouchers as $voucher)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-6">
                        <span class="font-mono font-semibold text-slate-800">{{ $voucher->voucher_number }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-accent-400 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($voucher->user->first_name ?? 'U', 0, 1) }}
                            </div>
                            <span class="font-medium text-slate-800">{{ $voucher->user->full_name ?? 'N/A' }}</span>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        @if($voucher->collection_code)
                        <span class="font-mono text-sm text-slate-600">{{ $voucher->collection_code }}</span>
                        @else
                        <span class="text-slate-400">-</span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-slate-600">
                        @if($voucher->distribution)
                        {{ $voucher->distribution->name }}
                        @else
                        <span class="text-slate-400">Manual</span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $voucher->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $voucher->status === 'issued' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $voucher->status === 'collected' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $voucher->status === 'expired' ? 'bg-slate-100 text-slate-600' : '' }}">
                            {{ ucfirst($voucher->status) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-slate-500 text-sm">{{ $voucher->created_at->format('M d, Y') }}</td>
                    <td class="py-4 px-6 text-right">
                        <a href="{{ route('admin.vouchers.show', $voucher) }}" class="text-primary-600 hover:text-primary-700 font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-8 text-center text-slate-500">No vouchers found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vouchers->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $vouchers->links() }}
    </div>
    @endif
</div>
@endsection

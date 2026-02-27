@extends('admin.layouts.app')

@section('title', 'Manual Payments')
@section('page-title', 'Manual Payment Verification')

@section('header', 'Manual Payments')
@section('subheader', 'Verify and manage manual bank transfer payments')

@section('content')
<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="stat-card group">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 rounded-xl bg-yellow-100">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Pending</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['pending']) }}</p>
    </div>
    <div class="stat-card group">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 rounded-xl bg-green-100">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Verified</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['verified']) }}</p>
    </div>
    <div class="stat-card group">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 rounded-xl bg-red-100">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Rejected</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['rejected']) }}</p>
    </div>
    <div class="stat-card group">
        <div class="flex items-center gap-2 mb-3">
            <div class="p-2 rounded-xl bg-primary-100">
                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            </div>
            <span class="text-sm font-medium text-slate-500">Total</span>
        </div>
        <p class="text-3xl font-bold text-slate-800">{{ number_format($stats['total']) }}</p>
    </div>
</div>

<!-- Filters -->
<div class="card-3d p-4 mb-6">
    <form method="GET" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by reference, name, email..." class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500">
        </div>
        <select name="status" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
            <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        <select name="type" class="px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500">
            <option value="">All Types</option>
            <option value="annual_subscription" {{ request('type') === 'annual_subscription' ? 'selected' : '' }}>Annual Subscription</option>
            <option value="instant_food_pack" {{ request('type') === 'instant_food_pack' ? 'selected' : '' }}>Instant Food Pack</option>
            <option value="donation" {{ request('type') === 'donation' ? 'selected' : '' }}>Donation</option>
        </select>
        <button type="submit" class="px-5 py-2.5 bg-primary-500 text-white font-medium rounded-xl hover:bg-primary-600 transition-colors">
            Filter
        </button>
    </form>
</div>

<!-- Payments Table -->
<div class="card-3d overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Reference</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">User</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Bank</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Amount</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Type</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Status</th>
                    <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Date</th>
                    <th class="text-right py-4 px-6 text-sm font-semibold text-slate-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($payments as $payment)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="py-4 px-6">
                        <span class="font-mono text-sm text-slate-800">{{ $payment->reference }}</span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-400 to-accent-400 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr($payment->user->first_name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <p class="font-medium text-slate-800">{{ $payment->user->full_name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500">{{ $payment->user->email ?? '' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-slate-600">
                        {{ $payment->bankAccount->bank_name ?? 'N/A' }}
                        <p class="text-xs text-slate-400">{{ $payment->bankAccount->account_number ?? '' }}</p>
                    </td>
                    <td class="py-4 px-6 font-semibold text-slate-800">{{ format_currency($payment->amount) }}</td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                            {{ ucfirst(str_replace('_', ' ', $payment->type)) }}
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            {{ $payment->status === 'verified' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $payment->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-slate-500 text-sm">{{ $payment->created_at->format('M d, Y') }}</td>
                    <td class="py-4 px-6 text-right">
                        <a href="{{ route('admin.manual-payments.show', $payment) }}" class="text-primary-600 hover:text-primary-700 font-medium">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-8 text-center text-slate-500">No manual payments found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="px-6 py-4 border-t border-slate-100">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection

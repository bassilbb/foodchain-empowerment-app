@extends('admin.layouts.app')

@section('title', 'Voucher Details')
@section('page-title', 'Voucher Details')

@section('header', 'Voucher Details')
@section('subheader', 'View and manage voucher')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.vouchers.index') }}" class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Vouchers
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Voucher Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="card-3d p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-slate-800">Voucher Information</h3>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    {{ $voucher->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $voucher->status === 'issued' ? 'bg-blue-100 text-blue-700' : '' }}
                    {{ $voucher->status === 'collected' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $voucher->status === 'expired' ? 'bg-slate-100 text-slate-600' : '' }}">
                    {{ ucfirst($voucher->status) }}
                </span>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Voucher Number</p>
                    <p class="font-mono font-bold text-xl text-slate-800">{{ $voucher->voucher_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Collection Code</p>
                    @if($voucher->collection_code)
                    <p class="font-mono font-bold text-xl text-primary-600">{{ $voucher->collection_code }}</p>
                    @else
                    <p class="text-slate-400">Not issued yet</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Created</p>
                    <p class="font-medium text-slate-800">{{ $voucher->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Expires</p>
                    <p class="font-medium text-slate-800">{{ $voucher->expires_at ? $voucher->expires_at->format('M d, Y') : 'No expiry' }}</p>
                </div>
            </div>

            @if($voucher->notes)
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h4 class="font-semibold text-slate-800 mb-2">Notes</h4>
                <p class="text-slate-600">{{ $voucher->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Collection Info -->
        @if($voucher->collected_at)
        <div class="card-3d p-6 border-l-4 border-green-500">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Collection Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Collected At</p>
                    <p class="font-medium text-slate-800">{{ $voucher->collected_at->format('M d, Y h:i A') }}</p>
                </div>
                @if($voucher->collector)
                <div>
                    <p class="text-sm text-slate-500 mb-1">Collected By</p>
                    <p class="font-medium text-slate-800">{{ $voucher->collector->name }}</p>
                </div>
                @endif
                @if($voucher->collection_notes)
                <div class="col-span-2">
                    <p class="text-sm text-slate-500 mb-1">Collection Notes</p>
                    <p class="font-medium text-slate-800">{{ $voucher->collection_notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- User -->
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">User</h3>
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-400 to-accent-400 flex items-center justify-center text-white font-bold">
                    {{ substr($voucher->user->first_name ?? 'U', 0, 1) }}
                </div>
                <div>
                    <p class="font-semibold text-slate-800">{{ $voucher->user->full_name ?? 'N/A' }}</p>
                    <p class="text-sm text-slate-500">{{ $voucher->user->email ?? '' }}</p>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.users.show', $voucher->user) }}" class="text-primary-600 hover:text-primary-700 font-medium">View Profile →</a>
            </div>
        </div>

        <!-- Distribution -->
        @if($voucher->distribution)
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Distribution</h3>
            <p class="font-medium text-slate-800">{{ $voucher->distribution->name }}</p>
            <p class="text-sm text-slate-500">{{ $voucher->distribution->distribution_date->format('M d, Y') }}</p>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.distributions.show', $voucher->distribution) }}" class="text-primary-600 hover:text-primary-700 font-medium">View Distribution →</a>
            </div>
        </div>
        @endif

        <!-- Actions -->
        @if($voucher->status === 'pending')
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Actions</h3>
            <form method="POST" action="{{ route('admin.vouchers.issue', $voucher) }}">
                @csrf
                <button type="submit" class="w-full px-4 py-3 bg-primary-500 text-white font-medium rounded-xl hover:bg-primary-600 transition-colors">
                    Issue Voucher
                </button>
            </form>
        </div>
        @endif

        @if($voucher->status === 'issued')
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Collect Voucher</h3>
            <form method="POST" action="{{ route('admin.vouchers.collect', $voucher) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Collection Code</label>
                    <input type="text" name="collection_code" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500" placeholder="Enter collection code">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Notes (optional)</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500"></textarea>
                </div>
                <button type="submit" class="w-full px-4 py-3 bg-green-500 text-white font-medium rounded-xl hover:bg-green-600 transition-colors">
                    Mark as Collected
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection

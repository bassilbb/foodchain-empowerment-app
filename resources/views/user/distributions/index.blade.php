{{-- resources/views/user/distributions/index.blade.php --}}
@extends('layouts.user')
@section('title', 'Distributions')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Your Distributions</h1>
    <p class="mt-1 text-sm text-gray-500">
        View your earnings from referral rewards, bonuses, and profit shares
    </p>
</div>

@if($distributions->isEmpty())
    <div class="bg-white shadow rounded-lg p-8 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">No distributions yet</h3>
        <p class="mt-1 text-sm text-gray-500">Distributions are processed monthly based on your referral activity.</p>
        <div class="mt-4">
            <a href="{{ route('referrals.tree') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                View Referral Tree
            </a>
        </div>
    </div>
@else
    <div class="bg-white shadow overflow-hidden rounded-lg">
        <ul class="divide-y divide-gray-200">
            @foreach($distributions as $distribution)
                <li class="px-6 py-4 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900">
                                    {{ $distribution->type_label }}
                                </h4>
                                <p class="text-sm text-gray-500">
                                    {{ $distribution->period_label }} • Processed {{ $distribution->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">
                                +{{ $distribution->formatted_amount }}
                            </p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($distribution->status === 'paid') bg-green-100 text-green-800
                                @elseif($distribution->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($distribution->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 ml-14">
                        <a href="{{ route('distributions.show', $distribution) }}"
                            class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                            View Details →
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $distributions->links() }}
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-sm text-gray-500">Total Earned</p>
            <p class="text-xl font-bold text-purple-600">{{ $totalEarned }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-sm text-gray-500">Pending Payout</p>
            <p class="text-xl font-bold text-yellow-600">{{ $pendingPayout }}</p>
        </div>
        <div class="bg-white shadow rounded-lg p-4 text-center">
            <p class="text-sm text-gray-500">Last Distribution</p>
            <p class="text-xl font-bold text-gray-900">
                {{ $lastDistributionDate ? $lastDistributionDate->format('M d') : '—' }}
            </p>
        </div>
    </div>
@endif
@endsection

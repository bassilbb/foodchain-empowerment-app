{{-- resources/views/user/distributions/show.blade.php --}}
@extends('layouts.user')
@section('title', 'Distribution Details')
@section('content')
<div class="mb-6">
    <a href="{{ route('distributions.index') }}" class="flex items-center text-sm text-gray-600 hover:text-gray-900">
        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Distributions
    </a>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-5">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $distribution->type_label }}</h1>
                <p class="text-purple-100 mt-1">{{ $distribution->period_label }}</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                @if($distribution->status === 'paid') bg-green-500 text-white
                @elseif($distribution->status === 'pending') bg-yellow-500 text-gray-900
                @else bg-gray-500 text-white
                @endif">
                {{ ucfirst($distribution->status) }}
            </span>
        </div>
    </div>

    <!-- Amount & Date -->
    <div class="px-6 py-6 border-b border-gray-200">
        <div class="text-center">
            <p class="text-sm text-gray-500">Total Amount</p>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $distribution->formatted_amount }}</p>
            <p class="mt-2 text-sm text-gray-600">
                Processed on {{ $distribution->created_at->format('F j, Y') }}
            </p>
        </div>
    </div>

    <!-- Breakdown -->
    <div class="px-6 py-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Earnings Breakdown</h2>

        @if($distribution->breakdown->isEmpty())
            <p class="text-sm text-gray-500">No detailed breakdown available.</p>
        @else
            <div class="space-y-3">
                @foreach($distribution->breakdown as $item)
                    <div class="flex justify-between items-center pb-2 border-b border-gray-100">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $item['description'] }}</p>
                            <p class="text-xs text-gray-500">{{ $item['referral_name'] ?? 'System' }}</p>
                        </div>
                        <p class="text-sm font-medium text-gray-900">+{{ $item['amount'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Payout Info -->
        @if($distribution->status === 'paid' && $distribution->payout_method)
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-900 mb-2">Payout Method</h3>
                <p class="text-sm text-gray-600">
                    @if($distribution->payout_method === 'bank')
                        Bank Transfer (•••• {{ $user->bank_last_four }})
                    @elseif($distribution->payout_method === 'wallet')
                        Platform Wallet
                    @else
                        {{ ucfirst($distribution->payout_method) }}
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
        @if($distribution->receipt_url)
            <a href="{{ $distribution->receipt_url }}" target="_blank"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Download Receipt
            </a>
        @endif
        <a href="{{ route('distributions.index') }}"
            class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700">
            Back to List
        </a>
    </div>
</div>
@endsection

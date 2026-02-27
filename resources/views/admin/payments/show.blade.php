{{-- resources/views/admin/payments/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Payment Details')
@section('page-title', 'Payment: ' . $payment->reference)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Payment Info -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Payment Information</h3>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-sm text-gray-500">Reference</label>
                    <p class="font-mono font-medium">{{ $payment->reference }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Amount</label>
                    <p class="text-2xl font-bold">{{ format_currency($payment->amount) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Type</label>
                    <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Status</label>
                    <p><span class="px-2 py-1 text-xs font-semibold rounded
                        {{ $payment->status === 'successful' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($payment->status) }}
                    </span></p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Payment Method</label>
                    <p class="font-medium">{{ ucfirst($payment->payment_method) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Date</label>
                    <p class="font-medium">{{ $payment->created_at->format('M d, Y h:i A') }}</p>
                </div>
                @if($payment->paid_at)
                <div>
                    <label class="text-sm text-gray-500">Paid At</label>
                    <p class="font-medium">{{ $payment->paid_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif
                @if($payment->paystack_reference)
                <div>
                    <label class="text-sm text-gray-500">Paystack Reference</label>
                    <p class="font-mono text-sm">{{ $payment->paystack_reference }}</p>
                </div>
                @endif
            </div>

            @if($payment->isPending())
            <div class="mt-6">
                <form method="POST" action="{{ route('admin.payments.verify', $payment) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Verify Payment
                    </button>
                </form>
            </div>
            @endif
        </div>

        @if($payment->paystack_response)
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Paystack Response</h3>
            <pre class="bg-gray-50 p-4 rounded text-xs overflow-auto">{{ json_encode($payment->paystack_response, JSON_PRETTY_PRINT) }}</pre>
        </div>
        @endif
    </div>

    <!-- User Info -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">User Details</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500">Name</label>
                    <p class="font-medium">{{ $payment->user->full_name }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Email</label>
                    <p class="font-medium">{{ $payment->user->email }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Phone</label>
                    <p class="font-medium">{{ $payment->user->phone }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Tier</label>
                    <p><span class="px-2 py-1 text-xs font-semibold rounded {{ tier_badge_color($payment->user->currentTier->slug) }} text-white">
                        {{ $payment->user->currentTier->name }}
                    </span></p>
                </div>
            </div>
            <div class="mt-6">
                <a href="{{ route('admin.users.show', $payment->user) }}" class="w-full block text-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    View User Profile
                </a>
            </div>
        </div>

        @if($payment->starterPack)
        <div class="bg-white rounded-lg shadow p-6 mt-6">
            <h3 class="text-lg font-semibold mb-4">Starter Pack</h3>
            <div class="space-y-3">
                <div>
                    <label class="text-sm text-gray-500">Package Value</label>
                    <p class="font-semibold">{{ format_currency($payment->starterPack->package_value) }}</p>
                </div>
                <div>
                    <label class="text-sm text-gray-500">Status</label>
                    <p class="font-medium">{{ ucfirst($payment->starterPack->status) }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

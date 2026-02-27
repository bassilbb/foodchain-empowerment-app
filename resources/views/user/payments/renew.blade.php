{{-- resources/views/user/payments/renew.blade.php --}}
@extends('layouts.user')
@section('title', 'Renew Membership')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">Renew Your Membership</h1>
    <p class="mt-1 text-sm text-gray-500">
        Extend your access and continue enjoying all platform benefits
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Plan Summary -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Current Plan</h2>

            <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded {{ tier_badge_color($user->currentTier->slug) }} text-white">
                            {{ $user->currentTier->name }}
                        </span>
                        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $user->currentTier->formatted_price }}/year</p>
                        <p class="mt-1 text-sm text-gray-600">
                            @if($user->membership_expires_at)
                                Current expiry: {{ $user->membership_expires_at->format('M d, Y') }}
                            @else
                                Lifetime member (eligible for renewal to extend benefits)
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-500">Next Billing</span>
                        <p class="font-medium">{{ now()->addYear()->format('M d, Y') }}</p>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Includes:</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        @foreach(explode("\n", trim($user->currentTier->features)) as $feature)
                            <li>✓ {{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- Payment Form -->
            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Details</h3>

                <form action="{{ route('payments.process') }}" method="POST" id="payment-form">
                    @csrf
                    <input type="hidden" name="tier_id" value="{{ $user->currentTier->id }}">

                    <!-- Stripe Elements Placeholder (or your gateway) -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Card Information</label>
                        <div id="card-element" class="border border-gray-300 rounded-md p-3 focus-within:ring-2 focus-within:ring-green-500"></div>
                        <div id="card-errors" class="mt-2 text-sm text-red-600"></div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="save_card" id="save_card" class="h-4 w-4 text-green-600 rounded">
                        <label for="save_card" class="ml-2 block text-sm text-gray-700">
                            Save this card for future use
                        </label>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" id="pay-button"
                            class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium flex items-center">
                            <span>Pay {{ $user->currentTier->formatted_price }}</span>
                            <svg class="ml-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Info Sidebar -->
    <div class="space-y-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h3 class="font-medium text-blue-800 mb-2">💡 Why Renew?</h3>
            <ul class="text-sm text-blue-700 list-disc pl-4 space-y-1">
                <li>Keep earning from referrals</li>
                <li>Maintain your current tier benefits</li>
                <li>Access exclusive community events</li>
                <li>Avoid account deactivation</li>
            </ul>
        </div>

        <div class="bg-white shadow rounded-lg p-4">
            <h3 class="font-medium text-gray-900 mb-2">Need Help?</h3>
            <p class="text-sm text-gray-600 mb-3">
                Contact our support team if you have questions about billing.
            </p>
            <a href="{{ route('support.create') }}"
                class="text-sm text-green-600 hover:underline font-medium">
                Open a Support Ticket →
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Load Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#424770',
                '::placeholder': {
                    color: '#aab7c4',
                },
            },
            invalid: {
                color: '#9e2146',
            },
        },
    });
    cardElement.mount('#card-element');

    const form = document.getElementById('payment-form');
    const payButton = document.getElementById('pay-button');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        payButton.disabled = true;
        payButton.textContent = 'Processing...';

        const { error, paymentMethod } = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            payButton.disabled = false;
            payButton.innerHTML = 'Pay {{ $user->currentTier->formatted_price }}';
        } else {
            // Insert payment method ID into form
            const hiddenInput = document.createElement('input');
            hiddenInput.setAttribute('type', 'hidden');
            hiddenInput.setAttribute('name', 'payment_method');
            hiddenInput.setAttribute('value', paymentMethod.id);
            form.appendChild(hiddenInput);

            form.submit();
        }
    });
});
</script>
@endpush

<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Models\StarterPack;
use App\Models\MembershipRenewal;
use App\Models\Union;
use App\Models\UnionMember;
use App\Models\FoodLoan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('sk_test_66969c29bb7d1f2a3a0ffbad611ebb668d1b815b');
        $this->publicKey = config('pk_test_589b8524b2a5ee0488cbe01564e5c9273090799c');
    }

    /**
     * Initialize a payment with Paystack
     */
    public function initializePayment(User $user, string $type, float $amount, array $metadata = []): array
    {
        // Create payment record
        $payment = Payment::create([
            'user_id' => $user->id,
            'reference' => $this->generateReference(),
            'type' => $type,
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => 'paystack',
            'meta' => $metadata,
        ]);

        // Initialize with Paystack
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/transaction/initialize', [
            'email' => $user->email,
            'amount' => $amount * 100, // Convert to kobo
            'reference' => $payment->reference,
            'callback_url' => route('payment.callback'),
            'metadata' => array_merge($metadata, [
                'user_id' => $user->id,
                'payment_type' => $type,
                'payment_id' => $payment->id,
            ]),
        ]);

        if ($response->successful() && $response->json('status')) {
            $data = $response->json('data');

            $payment->update([
                'paystack_reference' => $data['reference'],
            ]);

            return [
                'status' => 'success',
                'payment_id' => $payment->id,
                'authorization_url' => $data['authorization_url'],
                'access_code' => $data['access_code'],
                'reference' => $data['reference'],
            ];
        }

        $payment->markAsFailed($response->json());

        return [
            'status' => 'error',
            'message' => $response->json('message', 'Payment initialization failed'),
        ];
    }

    /**
     * Verify payment with Paystack
     */
    public function verifyPayment(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->secretKey,
        ])->get($this->baseUrl . "/transaction/verify/{$reference}");

        if (!$response->successful()) {
            return [
                'status' => 'error',
                'message' => 'Payment verification failed',
            ];
        }

        $data = $response->json('data');

        if ($data['status'] !== 'success') {
            return [
                'status' => 'failed',
                'message' => 'Payment was not successful',
            ];
        }

        // Find and update payment
        $payment = Payment::where('reference', $data['reference'])->first();

        if (!$payment) {
            return [
                'status' => 'error',
                'message' => 'Payment record not found',
            ];
        }

        DB::transaction(function () use ($payment, $data) {
            $payment->markAsSuccessful($data);

            // Process based on payment type
            match ($payment->type) {
                'membership' => $this->processMembershipPayment($payment),
                'starter_pack' => $this->processStarterPackPayment($payment),
                'instant_food_pack' => $this->processInstantFoodPackPayment($payment),
                'union_equity' => $this->processUnionEquityPayment($payment),
                'delivery' => $this->processDeliveryPayment($payment),
                'loan_repayment' => $this->processLoanRepaymentPayment($payment),
                default => null,
            };
        });

        return [
            'status' => 'success',
            'payment' => $payment,
            'message' => 'Payment verified successfully',
        ];
    }

    /**
     * Process membership payment
     */
    private function processMembershipPayment(Payment $payment): void
    {
        $user = $payment->user;

        // Activate user account
        if (!$user->is_active) {
            $user->update(['is_active' => true]);
        }

        // Set membership expiry (1 year from now, unless Emerald)
        $expiryDate = $user->isEmerald() ? null : now()->addYear();

        $user->update(['membership_expires_at' => $expiryDate]);

        // Create renewal record
        MembershipRenewal::create([
            'user_id' => $user->id,
            'payment_id' => $payment->id,
            'renewal_date' => now(),
            'expiry_date' => $expiryDate ?? now()->addYears(100), // Placeholder for Emerald
            'amount' => $payment->amount,
            'status' => 'active',
        ]);

        // Track referral payment if applicable
        if ($user->referred_by) {
            app(ReferralService::class)->trackReferralPayment($user, 'membership', $payment->amount);
        }

        // Send notification
        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    /**
     * Process starter pack payment
     */
    private function processStarterPackPayment(Payment $payment): void
    {
        $user = $payment->user;

        // Create or update starter pack record
        StarterPack::updateOrCreate(
            ['user_id' => $user->id],
            [
                'payment_id' => $payment->id,
                'amount_paid' => $payment->amount,
                'package_value' => 22000.00,
                'status' => 'paid',
                'purchased_at' => now(),
            ]
        );

        // Track referral payment if applicable
        if ($user->referred_by) {
            app(ReferralService::class)->trackReferralPayment($user, 'starter_pack', $payment->amount);
        }

        // Send notification
        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    /**
     * Process instant food pack payment
     */
    private function processInstantFoodPackPayment(Payment $payment): void
    {
        $user = $payment->user;
        $metadata = $payment->meta ?? [];

        $foodPackValue = $metadata['food_pack_value'] ?? $payment->amount;
        
        $distribution = \App\Models\FoodDistribution::create([
            'user_id' => $user->id,
            'tier_id' => $user->current_tier_id,
            'type' => 'instant_purchase',
            'status' => 'paid',
            'total_value' => $foodPackValue,
            'scheduled_date' => now(),
        ]);

        if ($user->referred_by) {
            app(ReferralService::class)->trackReferralPayment($user, 'instant_food_pack', $payment->amount);
        }

        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    /**
     * Process union equity contribution payment
     */
    private function processUnionEquityPayment(Payment $payment): void
    {
        $user = $payment->user;
        $metadata = $payment->meta ?? [];
        $unionId = $metadata['union_id'] ?? null;

        if (!$unionId) {
            throw new \Exception('Union ID not found in payment metadata');
        }

        $union = Union::find($unionId);
        
        if (!$union) {
            throw new \Exception('Union not found');
        }

        $unionMember = $union->members()->where('user_id', $user->id)->first();
        
        if (!$unionMember) {
            throw new \Exception('User is not a member of this union');
        }

        $unionMember->updateEquityContribution($payment->amount);
        $payment->update(['union_id' => $union->id]);

        if ($union->hasMetEquityRequirement() && $union->status === 'forming') {
            $union->update(['status' => 'active']);
        }

        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    /**
     * Process delivery fee payment
     */
    private function processDeliveryPayment(Payment $payment): void
    {
        $user = $payment->user;
        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    /**
     * Process loan repayment payment
     */
    private function processLoanRepaymentPayment(Payment $payment): void
    {
        $user = $payment->user;
        $metadata = $payment->meta ?? [];
        $loanId = $metadata['food_loan_id'] ?? null;

        if (!$loanId) {
            throw new \Exception('Loan ID not found in payment metadata');
        }

        $foodLoan = FoodLoan::find($loanId);
        
        if (!$foodLoan) {
            throw new \Exception('Food loan not found');
        }

        $foodLoan->recordPayment($payment->amount);
        $payment->update(['food_loan_id' => $foodLoan->id]);

        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    /**
     * Generate unique payment reference
     */
    private function generateReference(): string
    {
        do {
            $reference = 'PAY-' . strtoupper(uniqid());
        } while (Payment::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * Get Paystack public key
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}


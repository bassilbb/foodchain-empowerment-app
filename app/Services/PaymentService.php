<?php

namespace App\Services;

use App\Models\FoodAssistancePlan;
use App\Models\FoodLoan;
use App\Models\FoodSupportPlan;
use App\Models\MembershipRenewal;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\StarterPack;
use App\Models\Union;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    /**
     * Paystack credentials.
     *
     * Nullable on purpose: the service must be constructible in environments
     * where Paystack is not configured (local dev, tests, or a deployment that
     * has not set its keys yet). A non-nullable typed property would throw a
     * TypeError from the constructor and take down any controller that injects
     * this service, even for code paths that never call Paystack.
     */
    private ?string $secretKey;

    private ?string $publicKey;

    private ?string $flutterwaveSecretKey;

    private ?string $flutterwaveSecretHash;

    private ?string $monoSecretKey;

    private ?string $monoWebhookSecret;

    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('paystack.secret_key') ?: Setting::get('paystack_secret_key');
        $this->publicKey = config('paystack.public_key') ?: Setting::get('paystack_public_key');
        $this->flutterwaveSecretKey = config('services.flutterwave.secret_key') ?: Setting::get('flutterwave_secret_key');
        $this->flutterwaveSecretHash = config('services.flutterwave.secret_hash') ?: Setting::get('flutterwave_secret_hash') ?: $this->flutterwaveSecretKey;
        $this->monoSecretKey = config('services.mono.secret_key') ?: Setting::get('mono_secret_key');
        $this->monoWebhookSecret = config('services.mono.webhook_secret') ?: Setting::get('mono_webhook_secret') ?: $this->monoSecretKey;
    }

    /** Whether Paystack credentials are available for outbound API calls. */
    public function isConfigured(): bool
    {
        return ! empty($this->secretKey);
    }

    /** Whether Flutterwave credentials are available for outbound API calls. */
    public function isFlutterwaveConfigured(): bool
    {
        return ! empty($this->flutterwaveSecretKey);
    }

    /** Whether Mono credentials are available for outbound API calls. */
    public function isMonoConfigured(): bool
    {
        return ! empty($this->monoSecretKey);
    }

    /**
     * Initialize a payment with a supported gateway.
     *
     * Supported gateways: paystack (default), flutterwave, mono.
     */
    public function initializePayment(User $user, string $type, float $amount, array $metadata = [], string $gateway = 'paystack'): array
    {
        return match ($gateway) {
            'flutterwave' => $this->initializeFlutterwavePayment($user, $type, $amount, $metadata),
            'mono' => $this->initializeMonoPayment($user, $type, $amount, $metadata),
            default => $this->initializePaystackPayment($user, $type, $amount, $metadata),
        };
    }

    /**
     * Initialize a payment with Paystack
     */
    private function initializePaystackPayment(User $user, string $type, float $amount, array $metadata = []): array
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
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Paystack is not configured. Set PAYSTACK_SECRET_KEY or the paystack_secret_key setting.');
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->secretKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl.'/transaction/initialize', [
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
     * Initialize a Flutterwave payment. The payment reference doubles as the
     * tx_ref Flutterwave returns on callback.
     */
    private function initializeFlutterwavePayment(User $user, string $type, float $amount, array $metadata = []): array
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'reference' => $this->generateReference(),
            'type' => $type,
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => 'flutterwave',
            'meta' => $metadata,
        ]);

        if (! $this->isFlutterwaveConfigured()) {
            throw new \RuntimeException('Flutterwave is not configured. Set FLUTTERWAVE_SECRET_KEY or the flutterwave_secret_key setting.');
        }

        $response = Http::withToken($this->flutterwaveSecretKey)
            ->asJson()
            ->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $payment->reference,
                'amount' => $amount,
                'currency' => 'NGN',
                'redirect_url' => route('payment.callback'),
                'customer' => [
                    'email' => $user->email,
                    'name' => $user->full_name,
                    'phonenumber' => $user->phone,
                ],
                'customizations' => [
                    'title' => app_name().' Payment',
                    'description' => ucwords(str_replace('_', ' ', $type)),
                ],
                'meta' => array_merge($metadata, [
                    'user_id' => $user->id,
                    'payment_type' => $type,
                    'payment_id' => $payment->id,
                ]),
            ]);

        if ($response->successful() && $response->json('status') === 'success') {
            $data = $response->json('data');

            $payment->update([
                'paystack_reference' => $data['tx_ref'] ?? $payment->reference,
            ]);

            return [
                'status' => 'success',
                'payment_id' => $payment->id,
                'authorization_url' => $data['link'],
                'reference' => $payment->reference,
            ];
        }

        $payment->markAsFailed($response->json());

        return [
            'status' => 'error',
            'message' => $response->json('message', 'Flutterwave payment initialization failed'),
        ];
    }

    /**
     * Initialize a Mono DirectPay (one-time) payment. Returns the hosted Mono
     * checkout URL.
     */
    private function initializeMonoPayment(User $user, string $type, float $amount, array $metadata = []): array
    {
        $payment = Payment::create([
            'user_id' => $user->id,
            'reference' => $this->generateReference(),
            'type' => $type,
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => 'mono',
            'meta' => $metadata,
        ]);

        if (! $this->isMonoConfigured()) {
            throw new \RuntimeException('Mono is not configured. Set MONO_SECRET_KEY or the mono_secret_key setting.');
        }

        $response = Http::withHeaders([
            'mono-sec-key' => $this->monoSecretKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post('https://api.withmono.com/v2/payments/initiate', [
            'amount' => (int) round($amount * 100), // Mono amounts are in kobo
            'type' => 'onetime-debit',
            'method' => 'transfer',
            'description' => ucwords(str_replace('_', ' ', $type)),
            'reference' => $payment->reference,
            'redirect_url' => route('payment.callback'),
            'customer' => [
                'name' => $user->full_name,
                'email' => $user->email,
            ],
            'meta' => array_merge($metadata, [
                'user_id' => $user->id,
                'payment_type' => $type,
                'payment_id' => $payment->id,
            ]),
        ]);

        if ($response->successful() && $response->json('status') === 'successful') {
            $data = $response->json('data');

            $payment->update([
                'paystack_reference' => $data['reference'] ?? $payment->reference,
            ]);

            return [
                'status' => 'success',
                'payment_id' => $payment->id,
                'authorization_url' => $data['mono_url'] ?? $data['link'],
                'reference' => $payment->reference,
            ];
        }

        $payment->markAsFailed($response->json());

        return [
            'status' => 'error',
            'message' => $response->json('message', $response->json('error', 'Mono payment initialization failed')),
        ];
    }

    /**
     * Verify a payment with whichever gateway created it.
     */
    public function verifyPayment(string $reference): array
    {
        $payment = Payment::where('reference', $reference)->first();

        if (! $payment) {
            return [
                'status' => 'error',
                'message' => 'Payment record not found',
            ];
        }

        return match ($payment->payment_method) {
            'flutterwave' => $this->verifyFlutterwavePayment($payment),
            'mono' => $this->verifyMonoPayment($payment),
            default => $this->verifyPaystackPayment($payment),
        };
    }

    private function verifyPaystackPayment(Payment $payment): array
    {
        if (! $this->isConfigured()) {
            return [
                'status' => 'error',
                'message' => 'Paystack is not configured',
            ];
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->secretKey,
        ])->get($this->baseUrl."/transaction/verify/{$payment->reference}");

        if (! $response->successful()) {
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

        return $this->fulfilPayment($payment, $data);
    }

    private function verifyFlutterwavePayment(Payment $payment): array
    {
        if (! $this->isFlutterwaveConfigured()) {
            return [
                'status' => 'error',
                'message' => 'Flutterwave is not configured',
            ];
        }

        $response = Http::withToken($this->flutterwaveSecretKey)
            ->get('https://api.flutterwave.com/v3/transactions/'.$payment->reference.'/verify');

        if (! $response->successful()) {
            return [
                'status' => 'error',
                'message' => 'Payment verification failed',
            ];
        }

        $data = $response->json('data');

        if (($data['status'] ?? null) !== 'successful') {
            return [
                'status' => 'failed',
                'message' => 'Payment was not successful',
            ];
        }

        return $this->fulfilPayment($payment, $data);
    }

    private function verifyMonoPayment(Payment $payment): array
    {
        if (! $this->isMonoConfigured()) {
            return [
                'status' => 'error',
                'message' => 'Mono is not configured',
            ];
        }

        $response = Http::withHeaders([
            'mono-sec-key' => $this->monoSecretKey,
            'Accept' => 'application/json',
        ])->get('https://api.withmono.com/v2/payments/verify/'.$payment->reference);

        if (! $response->successful()) {
            return [
                'status' => 'error',
                'message' => 'Payment verification failed',
            ];
        }

        $body = $response->json();
        $data = $body['data'] ?? $body;
        $status = $data['status'] ?? $body['status'] ?? null;

        if (! in_array($status, ['successful', 'success'], true)) {
            return [
                'status' => 'failed',
                'message' => 'Payment was not successful',
            ];
        }

        return $this->fulfilPayment($payment, is_array($data) ? $data : []);
    }

    /**
     * Mark a verified payment as successful and run the type-specific processor.
     */
    private function fulfilPayment(Payment $payment, array $gatewayData): array
    {
        DB::transaction(function () use ($payment, $gatewayData) {
            $payment->markAsSuccessful($gatewayData);

            // Process based on payment type
            match ($payment->type) {
                'membership' => $this->processMembershipPayment($payment),
                'starter_pack' => $this->processStarterPackPayment($payment),
                'instant_food_pack' => $this->processInstantFoodPackPayment($payment),
                'union_equity' => $this->processUnionEquityPayment($payment),
                'delivery' => $this->processDeliveryPayment($payment),
                'loan_repayment' => $this->processLoanRepaymentPayment($payment),
                'assistance_processing_fee' => $this->processAssistanceProcessingFee($payment),
                'assistance_first_installment' => $this->processAssistanceFirstInstallment($payment),
                'assistance_weekly_repayment' => $this->processAssistanceWeeklyRepayment($payment),
                'support_plan_contribution' => $this->processSupportPlanContribution($payment),
                'wallet_topup' => $this->processWalletTopup($payment),
                'marketplace_order' => $this->processMarketplaceOrderPayment($payment),
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
        if (! $user->is_active) {
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

        if (! $unionId) {
            throw new \Exception('Union ID not found in payment metadata');
        }

        $union = Union::find($unionId);

        if (! $union) {
            throw new \Exception('Union not found');
        }

        $unionMember = $union->members()->where('user_id', $user->id)->first();

        if (! $unionMember) {
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
        $metadata = $payment->meta ?? [];
        $distributionId = $metadata['distribution_id'] ?? null;

        if ($distributionId) {
            $distribution = FoodDistribution::find($distributionId);
            if ($distribution) {
                $notes = $distribution->admin_notes ?? '';
                $notes .= ($notes ? "\n" : '').'Delivery fee paid via '.$payment->reference.' on '.now()->toDateTimeString();
                $distribution->update([
                    'delivery_fee' => 0,
                    'admin_notes' => $notes,
                ]);
            }
        }

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

        if (! $loanId) {
            throw new \Exception('Loan ID not found in payment metadata');
        }

        $foodLoan = FoodLoan::find($loanId);

        if (! $foodLoan) {
            throw new \Exception('Food loan not found');
        }

        $foodLoan->recordPayment($payment->amount);
        $payment->update(['food_loan_id' => $foodLoan->id]);

        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    private function processAssistanceProcessingFee(Payment $payment): void
    {
        $user = $payment->user;
        $metadata = $payment->meta ?? [];
        $planId = $metadata['assistance_plan_id'] ?? null;

        if (! $planId) {
            throw new \Exception('Assistance plan ID not found in payment metadata');
        }

        $plan = FoodAssistancePlan::find($planId);
        if (! $plan) {
            throw new \Exception('Food assistance plan not found');
        }

        $plan->payProcessingFee($payment->reference, $payment->payment_method);
        $payment->update(['food_assistance_plan_id' => $plan->id]);

        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    private function processAssistanceFirstInstallment(Payment $payment): void
    {
        $user = $payment->user;
        $metadata = $payment->meta ?? [];
        $planId = $metadata['assistance_plan_id'] ?? null;

        if (! $planId) {
            throw new \Exception('Assistance plan ID not found in payment metadata');
        }

        $plan = FoodAssistancePlan::find($planId);
        if (! $plan) {
            throw new \Exception('Food assistance plan not found');
        }

        $plan->payFirstInstallment($payment->reference, $payment->payment_method);
        $payment->update(['food_assistance_plan_id' => $plan->id]);

        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    private function processAssistanceWeeklyRepayment(Payment $payment): void
    {
        $user = $payment->user;
        $metadata = $payment->meta ?? [];
        $planId = $metadata['assistance_plan_id'] ?? null;

        if (! $planId) {
            throw new \Exception('Assistance plan ID not found in payment metadata');
        }

        $plan = FoodAssistancePlan::find($planId);
        if (! $plan) {
            throw new \Exception('Food assistance plan not found');
        }

        $plan->payWeeklyInstallment($payment->reference, $payment->payment_method);
        $payment->update(['food_assistance_plan_id' => $plan->id]);

        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    private function processSupportPlanContribution(Payment $payment): void
    {
        $user = $payment->user;
        $metadata = $payment->meta ?? [];
        $planId = $metadata['support_plan_id'] ?? null;

        if (! $planId) {
            throw new \Exception('Support plan ID not found in payment metadata');
        }

        $plan = FoodSupportPlan::find($planId);
        if (! $plan) {
            throw new \Exception('Food support plan not found');
        }

        $plan->addContribution($payment->amount, $payment->reference, $payment->payment_method);
        $payment->update(['food_support_plan_id' => $plan->id]);

        if ($plan->isMatured()) {
            $plan->processMaturity();
        }

        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    private function processWalletTopup(Payment $payment): void
    {
        $user = $payment->user;
        $amount = (float) $payment->amount;

        $user->addToWallet($amount);

        app(\App\Services\Marketplace\MarketplaceWalletService::class)
            ->logTransaction($user, null, $amount, 'credit', 'Wallet top-up: '.$payment->reference);

        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    /**
     * Process marketplace order payment
     */
    private function processMarketplaceOrderPayment(Payment $payment): void
    {
        $metadata = $payment->meta ?? [];
        $orderId = $metadata['marketplace_order_id'] ?? null;

        if (! $orderId) {
            return;
        }

        $order = \App\Models\Marketplace\Order::find($orderId);
        if (! $order) {
            return;
        }

        app(\App\Services\Marketplace\OrderService::class)->confirmExternalPayment($order, $payment->payment_method ?? 'paystack', $payment->reference);
    }

    /**
     * Generate unique payment reference
     */
    private function generateReference(): string
    {
        do {
            $reference = 'PAY-'.strtoupper(uniqid());
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

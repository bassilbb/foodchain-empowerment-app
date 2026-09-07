<?php

namespace App\Services;

use App\Models\AccountTransaction;
use App\Models\ClearanceRequest;
use App\Models\FoodAssistancePlan;
use App\Models\FoodLoan;
use App\Models\FoodSupportPlan;
use App\Models\MembershipRenewal;
use App\Models\Payment;
use App\Models\SavingsPlanOption;
use App\Models\Setting;
use App\Models\StarterPack;
use App\Models\Union;
use App\Models\User;
use App\Models\UserAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    private string $secretKey;

    private string $publicKey;

    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = config('paystack.secret_key') ?: Setting::get('paystack_secret_key');
        $this->publicKey = config('paystack.public_key') ?: Setting::get('paystack_public_key');
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
     * Verify payment with Paystack
     */
    public function verifyPayment(string $reference): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->secretKey,
        ])->get($this->baseUrl."/transaction/verify/{$reference}");

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

        // Find and update payment
        $payment = Payment::where('reference', $data['reference'])->first();

        if (! $payment) {
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
                'savings_plan_registration' => $this->processSavingsPlanRegistrationPayment($payment),
                'instant_food_pack' => $this->processInstantFoodPackPayment($payment),
                'union_equity' => $this->processUnionEquityPayment($payment),
                'delivery' => $this->processDeliveryPayment($payment),
                'loan_repayment' => $this->processLoanRepaymentPayment($payment),
                'assistance_processing_fee' => $this->processAssistanceProcessingFee($payment),
                'assistance_first_installment' => $this->processAssistanceFirstInstallment($payment),
                'assistance_weekly_repayment' => $this->processAssistanceWeeklyRepayment($payment),
                'support_plan_contribution' => $this->processSupportPlanContribution($payment),
                'wallet_topup' => $this->processWalletTopup($payment),
                'account_clearance' => $this->processAccountClearancePayment($payment),
                'batch_account_clearance' => $this->processBatchAccountClearancePayment($payment),
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
     * Process savings plan registration payment
     */
    private function processSavingsPlanRegistrationPayment(Payment $payment): void
    {
        $this->processMembershipPayment($payment);

        $metadata = $payment->meta ?? [];
        $optionId = $metadata['savings_plan_option_id'] ?? null;

        if (! $optionId) {
            return;
        }

        $option = SavingsPlanOption::with('plan')->find($optionId);
        if (! $option) {
            return;
        }

        $exists = UserAccount::where('primary_user_id', $payment->user_id)
            ->where('savings_plan_option_id', $option->id)
            ->exists();

        if ($exists) {
            return;
        }

        UserAccount::create([
            'primary_user_id' => $payment->user_id,
            'account_name' => $option->plan->name.' Savings Plan',
            'savings_plan_option_id' => $option->id,
            'weekly_contribution_amount' => $option->weekly_contribution,
            'current_tariff_weeks' => 0,
            'status' => 'active',
        ]);
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

        app(WalletService::class)->topUp($user, $payment->amount, 'Paystack top-up: '.$payment->reference);

        app(NotificationService::class)->sendPaymentSuccessNotification($user, $payment);
    }

    private function processAccountClearancePayment(Payment $payment): void
    {
        $metadata = $payment->meta ?? [];
        $accountId = $metadata['user_account_id'] ?? null;

        if (! $accountId) {
            return;
        }

        $account = UserAccount::find($accountId);
        if (! $account) {
            return;
        }

        $clearanceRequest = ClearanceRequest::where('user_account_id', $account->id)
            ->where('status', 'pending_payment')
            ->first();

        if (! $clearanceRequest) {
            return;
        }

        $clearanceRequest->update([
            'status' => 'pending_approval',
            'fee_paid_at' => now(),
            'payment_reference' => $payment->reference,
        ]);

        AccountTransaction::create([
            'primary_user_id' => $payment->user_id,
            'user_account_id' => $account->id,
            'amount' => $payment->amount,
            'type' => 'penalty',
            'description' => 'Clearance fee - '.$account->account_name,
        ]);

        app(NotificationService::class)->sendPaymentSuccessNotification($payment->user, $payment);
    }

    private function processBatchAccountClearancePayment(Payment $payment): void
    {
        $metadata = $payment->meta ?? [];
        $accountIds = $metadata['user_account_ids'] ?? [];

        if (empty($accountIds)) {
            return;
        }

        foreach ($accountIds as $accountId) {
            $account = UserAccount::find($accountId);
            if (! $account) {
                continue;
            }

            $clearanceRequest = ClearanceRequest::where('user_account_id', $account->id)
                ->where('status', 'pending_payment')
                ->first();

            if (! $clearanceRequest) {
                continue;
            }

            $clearanceRequest->update([
                'status' => 'pending_approval',
                'fee_paid_at' => now(),
                'payment_reference' => $payment->reference,
            ]);

            AccountTransaction::create([
                'primary_user_id' => $payment->user_id,
                'user_account_id' => $account->id,
                'amount' => $clearanceRequest->fee_amount,
                'type' => 'penalty',
                'description' => 'Clearance fee - '.$account->account_name,
            ]);
        }

        app(NotificationService::class)->sendPaymentSuccessNotification($payment->user, $payment);
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

        app(\App\Services\Marketplace\OrderService::class)->confirmExternalPayment($order, 'paystack', $payment->reference);
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

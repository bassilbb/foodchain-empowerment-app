<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\SavingsPlanOption;
use App\Models\User;
use App\Models\UserAccount;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = auth()->user()->payments()->latest()->paginate(20);

        return view('user.payments.index', compact('payments'));
    }

    public function pending(Request $request)
    {
        $userId = $request->get('user_id');
        $amount = $request->get('amount');
        $paymentType = $request->get('payment_type');

        $user = User::findOrFail($userId);
        $bankAccounts = BankAccount::getAllActive();

        $existingPending = Payment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        $savingsPlanOption = null;
        $paymentDescription = 'Membership Only';

        if ($paymentType === 'with_savings_plan') {
            $optionId = session('pending_savings_plan_option_id');
            if ($optionId) {
                $savingsPlanOption = SavingsPlanOption::with('plan')->find($optionId);
                if ($savingsPlanOption) {
                    $paymentDescription = 'Membership + '.$savingsPlanOption->plan->name
                        .' Savings Plan ('.$savingsPlanOption->duration_weeks.' weeks)';
                }
            }
            if (! $savingsPlanOption) {
                $paymentDescription = 'Membership + Savings Plan';
            }
        }

        return view('auth.payment-pending', compact('user', 'amount', 'paymentDescription', 'bankAccounts', 'paymentType', 'savingsPlanOption', 'existingPending'));
    }

    public function submitManualPayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:100',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'sender_name' => 'required|string|max:255',
            'transfer_date' => 'required|date',
        ]);

        $user = User::findOrFail($request->input('user_id'));
        $bankAccount = BankAccount::findOrFail($request->input('bank_account_id'));

        $paymentType = $request->input('payment_type');
        $type = $paymentType === 'with_savings_plan' ? 'savings_plan_registration' : 'membership';

        $existingPending = Payment::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('type', $type)
            ->first();

        if ($existingPending) {
            return redirect()->route('login')->with('info', 'Your payment is already pending. We will notify you once verified.');
        }

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('manual-payments/proofs', 'public');
        }

        Payment::create([
            'user_id' => $user->id,
            'reference' => 'MANUAL-'.strtoupper(uniqid()),
            'type' => $type,
            'amount' => $request->input('amount'),
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
            'meta' => [
                'bank_account_id' => $bankAccount->id,
                'sender_name' => $request->input('sender_name'),
                'transfer_date' => $request->input('transfer_date'),
                'proof_image' => $proofPath,
                'savings_plan_option_id' => session('pending_savings_plan_option_id'),
            ],
        ]);

        session()->forget('pending_savings_plan_option_id');
        session()->forget('pending_registration_user_id');

        return redirect()->route('login')->with('success', 'Payment submitted successfully! Your account will be activated after verification.');
    }

    public function handleCallback(Request $request)
    {
        $reference = $request->get('reference');

        if (! $reference) {
            return redirect()->route('login')->with('error', 'Invalid payment reference.');
        }

        $paymentService = app(PaymentService::class);
        $result = $paymentService->verifyPayment($reference);

        if ($result['status'] === 'success') {
            $payment = $result['payment'];

            $this->createAccountFromSavingsPlan($payment);

            if (session()->has('pending_registration_user_id')) {
                $userId = session('pending_registration_user_id');
                Auth::loginUsingId($userId);
                session()->forget('pending_registration_user_id');
                session()->forget('pending_savings_plan_option_id');

                return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to FoodChain Community.');
            }

            return redirect()->route('dashboard')->with('success', 'Payment successful!');
        }

        return redirect()->route('login')->with('error', 'Payment verification failed. Please contact support.');
    }

    private function createAccountFromSavingsPlan(Payment $payment): void
    {
        $optionId = $payment->meta['savings_plan_option_id'] ?? session('pending_savings_plan_option_id');

        if (! $optionId) {
            return;
        }

        $option = SavingsPlanOption::with('plan')->find($optionId);
        if (! $option) {
            return;
        }

        if (UserAccount::where('primary_user_id', $payment->user_id)
            ->where('savings_plan_option_id', $option->id)
            ->exists()) {
            return;
        }

        $user = $payment->user;

        UserAccount::create([
            'primary_user_id' => $user->id,
            'account_name' => $option->plan->name.' Savings Plan',
            'savings_plan_option_id' => $option->id,
            'weekly_contribution_amount' => $option->weekly_contribution,
            'current_tariff_weeks' => 0,
            'status' => 'active',
        ]);
    }

    public function showRenewalForm()
    {
        $user = auth()->user();
        $membershipFee = $user->isEmerald() ? 0 : 5000;

        return view('user.payments.renew', compact('user', 'membershipFee'));
    }

    public function initiateRenewal(Request $request)
    {
        $user = auth()->user();

        if ($user->isEmerald()) {
            return back()->with('error', 'Emerald members have lifetime membership.');
        }

        $paymentService = app(PaymentService::class);
        $result = $paymentService->initializePayment(
            $user,
            'membership',
            5000,
            ['renewal' => true]
        );

        if ($result['status'] === 'success') {
            return redirect($result['authorization_url']);
        }

        return back()->with('error', $result['message']);
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'amount' => 'required|numeric|min:100',
        ]);

        $user = auth()->user();
        $paymentService = app(PaymentService::class);

        $result = $paymentService->initializePayment(
            $user,
            $request->type,
            $request->amount,
            $request->except(['type', 'amount'])
        );

        if ($result['status'] === 'success') {
            return redirect($result['authorization_url']);
        }

        return back()->with('error', $result['message']);
    }

    public function payWithCard(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:100',
            'payment_type' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->input('user_id'));

        $type = $request->input('payment_type') === 'with_savings_plan'
            ? 'savings_plan_registration'
            : 'membership';

        $service = app(PaymentService::class);
        $result = $service->initializePayment($user, $type, $request->input('amount'), [
            'payment_type' => $request->input('payment_type'),
            'savings_plan_option_id' => session('pending_savings_plan_option_id'),
        ]);

        if ($result['status'] === 'success') {
            return redirect($result['authorization_url']);
        }

        return back()->with('error', $result['message']);
    }

    public function handleWebhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $payload = $request->getContent();

        if (! $signature || $signature !== hash_hmac('sha512', $payload, config('paystack.secret_key') ?: config('services.paystack.secret'))) {
            return response('Invalid signature', 400);
        }

        $event = $request->input('event');

        if ($event === 'charge.success') {
            $reference = $request->input('data.reference');
            $paymentService = app(PaymentService::class);
            $paymentService->verifyPayment($reference);
        }

        return response('OK', 200);
    }
}

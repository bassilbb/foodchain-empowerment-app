<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\Payment;
use App\Models\User;
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

        $paymentDescription = 'Membership Only';

        return view('auth.payment-pending', compact('user', 'amount', 'paymentDescription', 'bankAccounts', 'paymentType', 'existingPending'));
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

        $type = 'membership';

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
            ],
        ]);

        session()->forget('pending_registration_user_id');

        return redirect()->route('login')->with('success', 'Payment submitted successfully! Your account will be activated after verification.');
    }

    public function handleCallback(Request $request)
    {
        // Paystack and Mono redirect with "reference"; Flutterwave redirects
        // with "tx_ref".
        $reference = $request->get('reference') ?: $request->get('tx_ref');

        if (! $reference) {
            return redirect()->route('login')->with('error', 'Invalid payment reference.');
        }

        $paymentService = app(PaymentService::class);
        $result = $paymentService->verifyPayment($reference);

        if ($result['status'] === 'success') {
            $payment = $result['payment'];

            if ($payment->type === 'marketplace_order') {
                $orderId = $payment->meta['marketplace_order_id'] ?? null;
                if ($orderId && \App\Models\Marketplace\Order::find($orderId)) {
                    return redirect()->route('marketplace.orders.show', $orderId)
                        ->with('success', 'Payment successful! Your order has been confirmed.');
                }

                return redirect()->route('marketplace.orders.index')->with('success', 'Payment successful!');
            }

            if (session()->has('pending_registration_user_id')) {
                $userId = session('pending_registration_user_id');
                Auth::loginUsingId($userId);
                session()->forget('pending_registration_user_id');

                return redirect()->route('dashboard')->with('success', 'Registration successful! Welcome to FoodChain Community.');
            }

            return redirect()->route('dashboard')->with('success', 'Payment successful!');
        }

        return redirect()->route('login')->with('error', 'Payment verification failed. Please contact support.');
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

        $service = app(PaymentService::class);
        $result = $service->initializePayment($user, 'membership', $request->input('amount'));

        if ($result['status'] === 'success') {
            return redirect($result['authorization_url']);
        }

        return back()->with('error', $result['message']);
    }

    public function handleWebhook(Request $request)
    {
        $signature = $request->header('x-paystack-signature');
        $payload = $request->getContent();

        $secretKey = config('paystack.secret_key') ?: config('services.paystack.secret') ?: \App\Models\Setting::get('paystack_secret_key');

        if (! $signature || ! $secretKey || $signature !== hash_hmac('sha512', $payload, $secretKey)) {
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

    public function handleFlutterwaveWebhook(Request $request)
    {
        $secretHash = config('services.flutterwave.secret_hash') ?: \App\Models\Setting::get('flutterwave_secret_hash');

        $hash = $request->header('verif-hash');
        if (! $secretHash || ! $hash || $hash !== $secretHash) {
            return response('Invalid signature', 400);
        }

        $event = $request->input('event');
        if ($event === 'charge.completed' || $event === 'charge.success') {
            $data = $request->input('data', []);
            $status = $data['status'] ?? null;

            if ($status === 'successful') {
                $reference = $data['tx_ref'] ?? null;
                if ($reference) {
                    app(PaymentService::class)->verifyPayment($reference);
                }
            }
        }

        return response('OK', 200);
    }

    public function handleMonoWebhook(Request $request)
    {
        $secret = config('services.mono.webhook_secret') ?: config('services.mono.secret_key')
            ?: \App\Models\Setting::get('mono_webhook_secret') ?: \App\Models\Setting::get('mono_secret_key');

        $signature = $request->header('mono-webhook-secret');
        if (! $secret || ! $signature || $signature !== $secret) {
            return response('Invalid signature', 400);
        }

        $event = $request->input('event');
        if ($event === 'direct_debit.payment_successful') {
            $data = $request->input('data', []);
            $object = $data['object'] ?? $data['payment'] ?? [];
            $reference = $object['reference'] ?? $data['reference'] ?? null;

            if ($reference) {
                app(PaymentService::class)->verifyPayment($reference);
            }
        }

        return response('OK', 200);
    }
}

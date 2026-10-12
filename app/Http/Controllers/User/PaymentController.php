<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = auth()->user()->payments()->latest()->paginate(20);

        return view('user.payments.index', compact('payments'));
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

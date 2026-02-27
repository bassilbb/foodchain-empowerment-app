<?php
// app/Http/Controllers/User/PaymentController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Payment;
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

    public function handleCallback(Request $request)
    {
        $reference = $request->get('reference');

        if (!$reference) {
            return redirect()->route('login')->with('error', 'Invalid payment reference.');
        }

        $paymentService = app(PaymentService::class);
        $result = $paymentService->verifyPayment($reference);

        if ($result['status'] === 'success') {
            $payment = $result['payment'];

            // If this was a registration payment, log the user in
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

        // Emerald members don't pay
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
}

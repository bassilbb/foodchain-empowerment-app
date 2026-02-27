<?php
// app/Http/Controllers/Auth/RegisterController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MembershipTier;
use App\Models\PickupLocation;
use App\Services\PaymentService;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $referralCode = $request->get('ref');
        $referrer = null;

        if ($referralCode) {
            $referrer = User::where('referral_code', $referralCode)->first();
        }

        $tiers = MembershipTier::active()->ordered()->get();
        $locations = PickupLocation::active()->get();

        return view('auth.register', compact('referralCode', 'referrer', 'tiers', 'locations'));
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'address' => 'required|string',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'referral_code' => 'nullable|exists:users,referral_code',
            'payment_type' => 'required|in:membership_only,with_starter_pack',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Create user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => 'Nigeria',
            'current_tier_id' => 1, // Bronze by default
            'is_active' => false, // Will be activated after payment
        ]);

        // Track referral if provided
        if ($request->referral_code) {
            app(ReferralService::class)->trackReferral($user, $request->referral_code);
        }

        // Calculate payment amount
        $amount = 5000; // Membership fee
        if ($request->payment_type === 'with_starter_pack') {
            $amount += 20000; // Add starter pack
        }

        // Initialize payment
        $paymentService = app(PaymentService::class);
        $paymentType = $request->payment_type === 'membership_only' ? 'membership' : 'starter_pack';

        $result = $paymentService->initializePayment(
            $user,
            $paymentType,
            $amount,
            [
                'payment_type' => $request->payment_type,
                'includes_starter_pack' => $request->payment_type === 'with_starter_pack',
            ]
        );

        if ($result['status'] === 'success') {
            // Store user_id in session for callback
            session(['pending_registration_user_id' => $user->id]);

            // Redirect to Paystack
            return redirect($result['authorization_url']);
        }

        // Payment initialization failed
        $user->delete();
        return back()->with('error', $result['message'])->withInput();
    }
}

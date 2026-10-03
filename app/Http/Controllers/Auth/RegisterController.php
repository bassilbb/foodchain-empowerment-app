<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        $referralCode = $request->get('ref');
        $referrer = null;

        if ($referralCode) {
            $referrer = User::where('referral_code', $referralCode)->first();
        }

        $statesList = \Illuminate\Support\Facades\Schema::hasTable('nigerian_states')
            ? \App\Models\NigerianState::active()->ordered()->pluck('name')->all()
            : [];

        return view('auth.register', compact('referralCode', 'referrer', 'statesList'));
    }

    public function register(Request $request)
    {
        $paymentType = $request->input('payment_type', 'membership_only');

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
            'payment_type' => ['nullable', 'string', Rule::in(['membership_only'])],
            'selected_tier_id' => ['nullable', 'exists:membership_tiers,id'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tier = $request->filled('selected_tier_id')
            ? MembershipTier::find($request->selected_tier_id)
            : (MembershipTier::where('slug', 'bronze')->first()
                ?? MembershipTier::active()->ordered()->first());

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
            'current_tier_id' => $tier?->id,
            'is_active' => false,
        ]);

        if ($request->referral_code) {
            app(ReferralService::class)->trackReferral($user, $request->referral_code);
        }

        session()->put('marketplace_state', $request->state);
        session()->put('pending_registration_user_id', $user->id);

        Auth::login($user);

        $redirectAmount = $tier?->annual_fee ?? 5000;

        return redirect()->route('payment.pending', [
            'user_id' => $user->id,
            'amount' => $redirectAmount,
            'payment_type' => $paymentType,
        ])->with('warning', 'Complete your membership payment to activate your account.');
    }
}

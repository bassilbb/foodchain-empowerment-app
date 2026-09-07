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
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tier = MembershipTier::where('slug', 'bronze')->first()
            ?? MembershipTier::active()->ordered()->first();

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
            'is_active' => true,
        ]);

        if ($request->referral_code) {
            app(ReferralService::class)->trackReferral($user, $request->referral_code);
        }

        Auth::login($user);
        session()->put('marketplace_state', $request->state);

        return redirect()->route('marketplace.home')
            ->with('success', 'Welcome to FoodChain Marketplace, '.$user->first_name.'! Your free account is ready.');
    }
}

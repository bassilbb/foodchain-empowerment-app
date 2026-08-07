<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\MembershipTier;
use App\Models\PickupLocation;
use App\Models\SavingsPlan;
use App\Models\User;
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
        $savingsPlans = SavingsPlan::with('activeOptions')->active()->get();
        $bankAccounts = BankAccount::getAllActive();

        $plansData = $savingsPlans->mapWithKeys(function ($plan) {
            return [$plan->id => [
                'name' => $plan->name,
                'target' => $plan->target_amount,
                'options' => $plan->activeOptions->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'weeks' => $option->duration_weeks,
                        'amount' => (float) $option->weekly_contribution,
                    ];
                }),
            ]];
        });

        return view('auth.register', compact('referralCode', 'referrer', 'tiers', 'locations', 'savingsPlans', 'plansData', 'bankAccounts'));
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
            'payment_type' => 'required|in:membership_only,with_savings_plan',
            'selected_tier_id' => 'nullable|exists:membership_tiers,id',
            'savings_plan_option_id' => 'nullable|exists:savings_plan_options,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $tier = MembershipTier::find($request->selected_tier_id) ?? MembershipTier::where('slug', 'bronze')->first();

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
            'current_tier_id' => $tier->id,
            'is_active' => false,
        ]);

        if ($request->referral_code) {
            app(ReferralService::class)->trackReferral($user, $request->referral_code);
        }

        $amount = $tier->annual_fee;

        session([
            'pending_registration_user_id' => $user->id,
            'pending_savings_plan_option_id' => $request->savings_plan_option_id,
        ]);

        return redirect()->route('payment.pending', [
            'user_id' => $user->id,
            'amount' => $amount,
            'payment_type' => $request->payment_type,
            'tier_id' => $tier->id,
        ]);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMembership
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->is_active) {
            if (! $request->routeIs('dashboard', 'payments.index', 'payments.renew', 'payments.initiate', 'profile.*', 'logout', 'manual-payments.*', 'support.*', 'distributions.*', 'quarterly-benefits.*', 'referrals.*', 'unions.*', 'user.incidents.*', 'notifications.*', 'user.food-assistance.*', 'user.food-support.*', 'user.accounts.*', 'switch-account')) {
                return redirect()->route('payment.pending', [
                    'user_id' => $user->id,
                    'amount' => 5000,
                    'payment_type' => 'membership',
                ])->with('warning', 'Please complete your payment to activate your account.');
            }
        }

        return $next($request);
    }
}

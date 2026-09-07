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
            // Public marketplace browsing stays available to inactive members;
            // purchasing (cart/checkout/orders/wallet/bulk/vendor) remains gated.
            $marketplaceBrowse = ['marketplace.home', 'marketplace.shop', 'marketplace.flash-sale', 'marketplace.search', 'marketplace.autocomplete', 'marketplace.vendors', 'marketplace.vendor', 'marketplace.brand', 'marketplace.category', 'marketplace.product', 'marketplace.browse'];

            if (! $request->routeIs(array_merge($marketplaceBrowse, ['dashboard', 'payments.index', 'payments.renew', 'payments.initiate', 'profile.*', 'logout', 'manual-payments.*', 'support.*', 'distributions.*', 'quarterly-benefits.*', 'referrals.*', 'unions.*', 'user.incidents.*', 'notifications.*', 'user.food-assistance.*', 'user.food-support.*', 'user.accounts.*', 'switch-account']))) {
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

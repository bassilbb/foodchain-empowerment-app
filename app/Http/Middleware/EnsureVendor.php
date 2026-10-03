<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate for the Vendor Centre.
 *
 * A customer has no `marketplaceVendors` record, so this blocks them from the
 * whole vendor area. It is the first line of defence; each controller still
 * re-checks ownership of the individual record it is acting on.
 */
class EnsureVendor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->marketplaceVendor) {
            abort(403, 'No vendor account is linked to your profile.');
        }

        if (! $user->marketplaceVendor->isApproved()) {
            if ($user->marketplaceVendor->isPending()) {
                return redirect()
                    ->route('account.dashboard')
                    ->with('error', 'Your vendor account is pending approval. You will be able to use the Vendor Centre once an administrator activates it.');
            }

            abort(403, 'Your vendor account is not active. Contact support if you believe this is a mistake.');
        }

        return $next($request);
    }
}

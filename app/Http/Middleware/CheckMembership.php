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
        $user = auth()->user();

        if (!$user->is_active) {
            return redirect()->route('membership.renew')
                ->with('warning', 'Please activate your membership to continue.');
        }

        if (!$user->isMembershipActive()) {
            return redirect()->route('membership.renew')
                ->with('warning', 'Your membership has expired. Please renew to continue.');
        }

        return $next($request);
    }
}

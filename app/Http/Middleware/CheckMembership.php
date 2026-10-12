<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMembership
{
    /**
     * Handle an incoming request.
     *
     * Membership activation payments were removed to simplify registration:
     * every registered account is active immediately, so nothing is gated here.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}

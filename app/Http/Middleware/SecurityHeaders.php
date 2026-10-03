<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Baseline security response headers.
 *
 * The app ships with no security headers at all, which leaves every page —
 * including the admin area — open to clickjacking and MIME sniffing, and
 * leaks full URLs to third parties via the default Referrer-Policy.
 */
class SecurityHeaders
{
    /**
     * Third-party origins the front end genuinely loads.
     *
     * Anything not listed here is blocked, so a compromised template or an
     * injected <script> cannot phone home to an arbitrary host.
     */
    private const SCRIPT_ORIGINS = [
        'https://cdn.jsdelivr.net',      // Swiper, Chart.js
        'https://cdnjs.cloudflare.com', // Font Awesome
    ];

    private const STYLE_ORIGINS = [
        'https://cdn.jsdelivr.net',
        'https://cdnjs.cloudflare.com',
        'https://fonts.googleapis.com',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Legacy clickjacking defence. CSP frame-ancestors below is the modern
        // control and takes precedence in current browsers, but this header is
        // still honoured by older agents and some crawlers.
        $response->headers->set('X-Frame-Options', 'DENY', false);

        // Stops a browser second-guessing a declared Content-Type, which is
        // what turns an uploaded file into executable script.
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);

        // Default in modern browsers leaks only the origin, not the full path.
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin', false);

        // Nothing on this site needs the camera, microphone, geolocation or
        // payment APIs in an iframe.
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(), interest-cohort=()', false);

        // Only the non-public surfaces opt out of indexing. This must NOT be
        // set globally or the whole marketing site drops out of search.
        if ($this->shouldHideFromCrawlers($request)) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow', false);
        }

        $csp = $this->contentSecurityPolicy($request);
        if ($csp !== null) {
            $response->headers->set('Content-Security-Policy', $csp, false);
        }

        // Only ever advertise HSTS on a connection that is already secure —
        // sending it over plain HTTP in local/dev would pin the dev host to
        // HTTPS in the browser and break every subsequent request.
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains', false);
        }

        return $response;
    }

    /**
     * Whether this response belongs to a surface that must stay out of search
     * results: the admin area and anything behind authentication.
     *
     * The public marketing, blog and legal pages are deliberately absent from
     * this list — indexing those is the point of the site.
     */
    private function shouldHideFromCrawlers(Request $request): bool
    {
        if ($request->is('admin', 'admin/*')) {
            return true;
        }

        if ($request->user() || $request->is('login', 'register', 'logout', 'forgot-password', 'reset-password/*')) {
            return true;
        }

        return $request->is(
            'dashboard',
            'account',
            'account/*',
            'vendor',
            'vendor/*',
            'marketplace/*',
            'profile',
            'profile/*',
            'payments',
            'payments/*',
            'manual-payments',
            'manual-payments/*',
            'support',
            'support/*',
            'incidents',
            'incidents/*',
            'vouchers',
            'vouchers/*',
            'distributions',
            'distributions/*',
            'food-assistance',
            'food-assistance/*',
            'food-support',
            'food-support/*',
            'referrals',
            'referrals/*',
            'unions',
            'unions/*',
            'quarterly-benefits',
            'quarterly-benefits/*',
            'notifications',
            'notifications/*',
            'payment',
            'payment/*',
            'membership',
            'membership/*'
        );
    }

    private function contentSecurityPolicy(Request $request): ?string
    {
        // The admin area is a token-authenticated SPA-ish surface; it needs no
        // third-party origins at all, so it gets a tighter policy.
        if ($request->is('admin*')) {
            $directives = [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline'",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                "font-src 'self' data: https://fonts.gstatic.com",
                "img-src 'self' data: blob:",
                "connect-src 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'none'",
            ];
        } else {
            $directives = [
                "default-src 'self'",
                'script-src \'self\' \'unsafe-inline\' '.implode(' ', self::SCRIPT_ORIGINS),
                'style-src \'self\' \'unsafe-inline\' '.implode(' ', self::STYLE_ORIGINS),
                "font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com",
                "img-src 'self' data: blob: https://images.unsplash.com",
                // Product pages embed a YouTube player for the video field.
                "frame-src https://www.youtube.com",
                "media-src 'self' blob:",
                "connect-src 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'none'",
            ];
        }

        // Only meaningful over TLS; harmless but noisy over plain HTTP.
        if ($request->isSecure()) {
            $directives[] = 'upgrade-insecure-requests';
        }

        return implode('; ', $directives);
    }
}

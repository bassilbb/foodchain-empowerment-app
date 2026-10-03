<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * robots.txt.
 *
 * The default Laravel stub disallows nothing, which invites crawlers into
 * /admin, /dashboard, the vendor centre and every authenticated account page.
 */
class RobotsController extends Controller
{
    public function index(): Response
    {
        $lines = [
            'User-agent: *',
            '',
            '# Authenticated and administrative surfaces',
            'Disallow: /admin',
            'Disallow: /dashboard',
            'Disallow: /account',
            'Disallow: /vendor',
            'Disallow: /marketplace',
            'Disallow: /profile',
            'Disallow: /payments',
            'Disallow: /manual-payments',
            'Disallow: /support',
            'Disallow: /incidents',
            'Disallow: /vouchers',
            'Disallow: /distributions',
            'Disallow: /food-assistance',
            'Disallow: /food-support',
            'Disallow: /referrals',
            'Disallow: /unions',
            'Disallow: /quarterly-benefits',
            'Disallow: /notifications',
            'Disallow: /loans',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /logout',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /payment',
            'Disallow: /serve-logo',
            '',
            '# Payment provider callbacks are not content',
            'Disallow: /paystack/',
            'Disallow: /flutterwave/',
            'Disallow: /mono/',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n", 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }
}

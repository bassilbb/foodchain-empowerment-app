<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Join our exclusive community. Earn rewards through referrals, enjoy tiered benefits, and grow with us.">
    <title>{{ config('app.name', 'Referral Network') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
    </style>
</head>
<body class="bg-gray-50">

<!-- Hero Section -->
<header class="gradient-bg text-white">
    <div class="container mx-auto px-4 py-16 md:py-24">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl md:text-6xl font-bold tracking-tight">
                Build Your Network.<br>Grow Your Rewards.
            </h1>
            <p class="mt-6 text-xl text-green-100 max-w-2xl mx-auto">
                Join thousands of members earning passive income through our tiered referral system. No hidden fees. Real results.
            </p>
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-white text-green-700 font-semibold rounded-lg shadow-lg hover:bg-gray-100 transition">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-3 bg-white text-green-700 font-semibold rounded-lg shadow-lg hover:bg-gray-100 transition">
                        Get Started — Free
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-3 bg-transparent border-2 border-white text-white font-semibold rounded-lg hover:bg-white/10 transition">
                        Sign In
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>

<!-- Stats Banner -->
<div class="bg-white py-6">
    <div class "container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 text-center">
            <div>
                <p class="text-2xl md:text-3xl font-bold text-gray-900">12,500+</p>
                <p class="text-sm text-gray-600">Active Members</p>
            </div>
            <div>
                <p class="text-2xl md:text-3xl font-bold text-gray-900">$2.4M+</p>
                <p class="text-sm text-gray-600">Paid to Members</p>
            </div>
            <div>
                <p class="text-2xl md:text-3xl font-bold text-gray-900">3.2x</p>
                <p class="text-sm text-gray-600">Avg. Referral Growth</p>
            </div>
            <div>
                <p class="text-2xl md:text-3xl font-bold text-gray-900">98%</p>
                <p class="text-sm text-gray-600">Member Satisfaction</p>
            </div>
        </div>
    </div>
</div>

<!-- How It Works -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-3xl font-bold text-gray-900">How It Works</h2>
            <p class="mt-4 text-gray-600">Start earning in just 3 simple steps</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-green-700">1</span>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Sign Up Free</h3>
                <p class="mt-3 text-gray-600">Create your account in under 60 seconds. No payment required to start.</p>
            </div>
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-green-700">2</span>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Share Your Link</h3>
                <p class="mt-3 text-gray-600">Invite friends using your unique referral link. Track clicks in real time.</p>
            </div>
            <div class="text-center p-6">
                <div class="w-16 h-16 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-green-700">3</span>
                </div>
                <h3 class="mt-6 text-xl font-semibold text-gray-900">Earn & Upgrade</h3>
                <p class="mt-3 text-gray-600">Get rewarded for every referral. Unlock higher tiers with better payouts.</p>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <h2 class="text-3xl font-bold text-gray-900">Powerful Features</h2>
            <p class="mt-4 text-gray-600">Everything you need to succeed</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-xl shadow-sm feature-card transition">
                <div class="text-green-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Tiered Earnings</h3>
                <p class="mt-2 text-gray-600">Higher tiers = bigger commissions. Climb the ladder as your network grows.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm feature-card transition">
                <div class="text-green-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Weekly Payouts</h3>
                <p class="mt-2 text-gray-600">Get paid fast. Distributions processed every Friday to your preferred method.</p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm feature-card transition">
                <div class="text-green-600">
                    <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">Real-Time Analytics</h3>
                <p class="mt-2 text-gray-600">Track referrals, earnings, and team performance with live dashboards.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 gradient-bg text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl md:text-4xl font-bold max-w-2xl mx-auto">Ready to Start Earning?</h2>
        <p class="mt-4 text-green-100 max-w-xl mx-auto">
            Join today and get your first referral bonus instantly.
        </p>
        <div class="mt-8">
            @auth
                <a href="{{ route('dashboard') }}" class="px-8 py-3 bg-white text-green-700 font-semibold rounded-lg shadow-lg hover:bg-gray-100 transition">
                    Go to Your Dashboard
                </a>
            @else
                <a href="{{ route('register') }}" class="px-8 py-3 bg-white text-green-700 font-semibold rounded-lg shadow-lg hover:bg-gray-100 transition">
                    Create Your Free Account
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Footer -->
@include('partials.site-footer')

</body>
</html>

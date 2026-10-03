@php
    $page = $page ?? 'privacy';

    $pages = [
        'privacy' => [
            'title' => 'Privacy Policy',
            'tag' => 'Legal',
            'intro' => 'This Privacy Policy explains how the Food Chain Community Empowerment Initiative (FCCEI) collects, uses and protects the information you share when you use our website, mobile storefront and community programmes.',
            'sections' => [
                ['Information We Collect', 'We collect the details you provide when you register, place an order, join a community programme or contact us. This typically includes your name, phone number, email address, delivery address and state of residence. We also collect basic technical information such as your device type and pages visited so we can keep the service secure and reliable.'],
                ['How We Use Your Information', 'Your information is used to process orders and deliveries, personalise state-based pricing, manage your account and wallet, respond to support requests, and send service updates. With your consent we may also share news about programmes and offers.'],
                ['Sharing and Disclosure', 'We do not sell your personal data. We share information only with trusted partners who help us operate the platform, such as payment providers, delivery riders and vendors fulfilling your order, and only to the extent needed to serve you.'],
                ['Data Security', 'We apply reasonable technical and organisational safeguards to protect your information against loss, misuse and unauthorised access. No online system is completely secure, so please keep your account password confidential.'],
                ['Your Rights', 'You may request access to, correction of, or deletion of your personal information, and you may withdraw consent for marketing communications at any time by contacting us.'],
            ],
        ],
        'terms' => [
            'title' => 'Terms of Service',
            'tag' => 'Legal',
            'intro' => 'By accessing or using FCCEI services, including the FoodChain marketplace, you agree to these Terms of Service. Please read them carefully before using the platform.',
            'sections' => [
                ['Accounts', 'You must provide accurate information when creating an account and keep your login details secure. You are responsible for activity that happens under your account. Accounts found to be fraudulent or abusive may be suspended.'],
                ['Orders and Pricing', 'Product prices are displayed per state and may differ between states because of transport, storage and local market conditions. The price shown for your selected state at the time of checkout is the price that applies to your order.'],
                ['Payments and Wallet', 'Orders may be paid for using supported payment methods or your FoodChain wallet. Payment confirmations are subject to verification by our payment partners.'],
                ['Delivery', 'Delivery timelines and fees depend on your selected delivery zone. We work with verified logistics partners to get your order to you as quickly and safely as possible.'],
                ['Acceptable Use', 'You agree not to misuse the platform, interfere with its operation, or use it for unlawful purposes. We may update these terms from time to time, and continued use of the platform means you accept the updated terms.'],
            ],
        ],
        'cookies' => [
            'title' => 'Cookie Policy',
            'tag' => 'Legal',
            'intro' => 'Cookies are small text files stored on your device that help websites work properly. This policy explains how FCCEI uses cookies and similar technologies.',
            'sections' => [
                ['Why We Use Cookies', 'We use cookies to keep you signed in, remember your selected state and cart, improve page performance, and understand how visitors use our site so we can make it better.'],
                ['Types of Cookies', 'Essential cookies are required for the platform to function. Preference cookies remember your settings such as your state. Analytics cookies give us aggregated, anonymous insight into usage.'],
                ['Managing Cookies', 'You can control or delete cookies through your browser settings. Disabling essential cookies may prevent parts of the platform, such as login and checkout, from working correctly.'],
            ],
        ],
        'compliance' => [
            'title' => 'Compliance',
            'tag' => 'Legal',
            'intro' => 'FCCEI is committed to operating transparently, ethically and in line with applicable Nigerian laws and regulations.',
            'sections' => [
                ['Regulatory Commitment', 'We align our operations with Nigerian consumer protection, data protection and food safety requirements, and we review our practices as regulations evolve.'],
                ['Anti-Fraud', 'We monitor transactions for fraud and misuse and cooperate with relevant authorities where required. Vendors and riders are onboarded and verified before they can trade on the platform.'],
                ['Reporting Concerns', 'If you suspect fraud, misconduct or a compliance issue, please report it through our Help Center. Every report is reviewed by our team.'],
            ],
        ],
        'help' => [
            'title' => 'Help Center',
            'tag' => 'Support',
            'intro' => 'Need a hand? Here are answers to the questions we hear most often. If you still need help, our team is only a message away.',
            'sections' => [
                ['Orders and Delivery', 'You can track the status of every order from your account. If a delivery is delayed, please allow for the estimated window for your zone, then contact support with your order number.'],
                ['Payments and Wallet', 'Your wallet balance and transaction history are available in your account. If a payment has not reflected, keep your payment reference handy and contact support.'],
                ['Vendors and Riders', 'Vendors and riders can manage their listings, orders and profile from their dashboards. Our team is available to help you get set up.'],
                ['Contact Support', 'Reach us through the WhatsApp button on any page or send us a message from the contact form on the home page. We aim to respond as quickly as possible.'],
            ],
        ],
        'faq' => [
            'title' => 'Frequently Asked Questions',
            'tag' => 'Support',
            'intro' => 'Quick answers about accounts, pricing, ordering, payment and delivery on the FoodChain marketplace.',
            'sections' => [
                ['Do I need to pay a membership fee?', 'No. Registration is free and instant. Any registered, active user can browse and buy from the marketplace.'],
                ['Why do prices change by state?', 'The true cost of food differs across Nigeria because of transport, storage and local demand. We show the price for the state you select, and that price is locked into your order.'],
                ['How do I choose my state?', 'Use the state selector on the marketplace. Every product, cart total and checkout figure updates to reflect your local price.'],
                ['How do I place an order?', 'Add items to your cart, review your delivery details, and complete checkout using a supported payment method or your wallet.'],
                ['What payment options do I have?', 'You can pay on delivery or pay before delivery. Before delivery, choose a supported payment method (card, transfer or wallet) securely at checkout.'],
                ['How do loyalty points and flash sales work?', 'Every payment, whether made before or on delivery, earns you a loyalty point. Loyalty points qualify you for flash sale purchases at special member-low prices.'],
                ['How much does delivery cost?', 'Delivery costs are based on the state you select and are shown before you complete your order, so there are no surprises.'],
                ['How do I become a vendor or rider?', 'Visit the Become a Supplier or Become a Rider pages, or register an account and follow the vendor or rider onboarding steps.'],
            ],
        ],
        'community' => [
            'title' => 'Community',
            'tag' => 'Programmes',
            'intro' => 'FCCEI exists to strengthen households and local food businesses. Our community programmes are the heart of that mission.',
            'sections' => [
                ['Food Security Programmes', 'We connect members to affordable staple foods, quality-checked groceries, flash-deal savings and periodic food support initiatives across our coverage locations.'],
                ['Empowering Local Vendors', 'We help small vendors and producers reach more customers with a free digital storefront, fair state-based pricing and order management tools.'],
                ['Get Involved', 'Whether you want to volunteer, partner, sponsor a programme or support a community, there are many ways to take part. Reach out through our contact channels to learn more.'],
            ],
        ],
        'affiliate' => [
            'title' => 'Affiliate Program',
            'tag' => 'Partners',
            'intro' => 'Earn rewards for introducing people to a marketplace that makes everyday food more affordable across Nigeria.',
            'sections' => [
                ['How It Works', 'Share your unique referral link with your network. When people you refer join and start buying, you earn a reward on qualifying activity.'],
                ['Who Can Join', 'Affiliates can be individuals, community groups or businesses. You do not need special technical skills, just a genuine interest in helping people access affordable food.'],
                ['Getting Started', 'Create an account and express your interest through our contact form or support channel. Our partnerships team will guide you through onboarding and share your referral tools.'],
            ],
        ],
    ];

    $content = $pages[$page] ?? $pages['privacy'];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $content['title'] }} · FCCEI</title>
    @include('partials.seo-meta', [
        'seoTitle' => $content['title'].' · FCCEI',
        'seoDesc' => $content['intro'],
        'schema' => array_merge(
            [[
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => $content['title'].' · FCCEI',
                'url' => url()->current(),
                'description' => $content['intro'],
            ]],
            $page === 'faq' ? [[
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => collect($content['sections'])->map(fn ($qa) => [
                    '@type' => 'Question',
                    'name' => $qa[0],
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa[1]],
                ])->all(),
            ]] : [],
        ),
    ])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    @include('partials.vite-css')
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap');
        body { font-family: 'Inter', system-ui, sans-serif; }
        h1, h2, h3, .font-display { font-family: 'Poppins', system-ui, sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 text-slate-700 flex flex-col">
    @include('partials.skip-link')
    {{-- Simple unified header --}}
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-slate-100 shadow-sm">
        <nav class="container mx-auto px-4">
            <div class="flex justify-between items-center py-3 gap-4">
                <a href="{{ route('landing') }}" class="flex items-center gap-3 shrink-0">
                    <span class="w-12 h-12 rounded-2xl bg-white ring-1 ring-emerald-100 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full p-1">
                    </span>
                    <span class="flex flex-col leading-tight">
                        <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">FCCEI</span>
                        <span class="text-[9px] font-bold uppercase tracking-[0.18em] text-slate-400">Food Chain Community Empowerment</span>
                    </span>
                </a>
                <div class="hidden md:flex items-center gap-1 text-sm font-semibold text-slate-500">
                    <a href="{{ route('landing') }}" class="px-3 py-2 rounded-full hover:bg-emerald-50 hover:text-emerald-700 transition">Home</a>
                    <a href="{{ route('marketplace.home') }}" class="px-3 py-2 rounded-full hover:bg-emerald-50 hover:text-emerald-700 transition">Marketplace</a>
                    <a href="{{ route('pages.help') }}" class="px-3 py-2 rounded-full hover:bg-emerald-50 hover:text-emerald-700 transition">Help</a>
                    <a href="{{ route('login') }}" class="ml-2 px-5 py-2.5 rounded-full text-white bg-gradient-to-r from-emerald-600 to-teal-600 shadow-md hover:-translate-y-0.5 transition">Login</a>
                </div>
                <a href="{{ route('marketplace.home') }}" class="md:hidden px-4 py-2 rounded-full text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-teal-600">Marketplace</a>
            </div>
        </nav>
    </header>

    {{-- Hero --}}
    <section class="bg-gradient-to-br from-emerald-950 via-emerald-900 to-teal-900 text-white">
        <div class="container mx-auto px-4 py-14 md:py-16">
            <span class="inline-block text-[11px] font-bold uppercase tracking-[0.2em] text-emerald-300 bg-white/5 border border-emerald-700/50 px-4 py-1.5 rounded-full">{{ $content['tag'] }}</span>
            <h1 class="text-3xl md:text-5xl font-extrabold mt-5 max-w-3xl">{{ $content['title'] }}</h1>
            <p class="text-emerald-200/85 mt-4 max-w-3xl leading-relaxed">{{ $content['intro'] }}</p>
        </div>
    </section>

    {{-- Content --}}
    <main class="flex-1" id="main-content">
        <div class="container mx-auto px-4 py-12 md:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-10">
                {{-- Side nav --}}
                <aside class="hidden lg:block">
                    <div class="sticky top-28 bg-white rounded-2xl border border-slate-100 shadow-sm p-4">
                        <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 mb-3">Related Pages</p>
                        <nav class="space-y-1 text-sm">
                            <a href="{{ route('pages.privacy') }}" class="block px-3 py-2 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 {{ $page === 'privacy' ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">Privacy Policy</a>
                            <a href="{{ route('pages.terms') }}" class="block px-3 py-2 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 {{ $page === 'terms' ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">Terms of Service</a>
                            <a href="{{ route('pages.cookies') }}" class="block px-3 py-2 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 {{ $page === 'cookies' ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">Cookie Policy</a>
                            <a href="{{ route('pages.compliance') }}" class="block px-3 py-2 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 {{ $page === 'compliance' ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">Compliance</a>
                            <a href="{{ route('pages.help') }}" class="block px-3 py-2 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 {{ $page === 'help' ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">Help Center</a>
                            <a href="{{ route('pages.faq') }}" class="block px-3 py-2 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 {{ $page === 'faq' ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">FAQ</a>
                            <a href="{{ route('pages.community') }}" class="block px-3 py-2 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 {{ $page === 'community' ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">Community</a>
                            <a href="{{ route('pages.affiliate') }}" class="block px-3 py-2 rounded-lg hover:bg-emerald-50 hover:text-emerald-700 {{ $page === 'affiliate' ? 'bg-emerald-50 text-emerald-700 font-semibold' : '' }}">Affiliate Program</a>
                        </nav>
                    </div>
                </aside>

                {{-- Article --}}
                <article class="max-w-3xl">
                    @foreach ($content['sections'] as $i => $section)
                        <div class="mb-8">
                            <h2 class="text-xl font-bold text-slate-900 mb-2 flex items-start gap-3">
                                <span class="mt-1 w-7 h-7 shrink-0 rounded-lg bg-emerald-100 text-emerald-700 text-sm font-extrabold flex items-center justify-center">{{ $i + 1 }}</span>
                                {{ $section[0] }}
                            </h2>
                            <p class="leading-relaxed text-slate-600 pl-10">{{ $section[1] }}</p>
                        </div>
                    @endforeach

                    <div class="mt-10 rounded-2xl bg-emerald-50 border border-emerald-100 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <p class="font-bold text-slate-900">Still need help?</p>
                            <p class="text-sm text-slate-600">Our support team is happy to assist you.</p>
                        </div>
                        <a href="https://wa.me/+2348168018123" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-6 py-3 rounded-full text-white font-semibold bg-gradient-to-r from-emerald-600 to-teal-600 shadow-md hover:-translate-y-0.5 transition">
                            <i class="fab fa-whatsapp"></i> Chat with us
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </main>

    @include('partials.site-footer')
</body>
</html>

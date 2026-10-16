@if(!App\Models\PageContent::isPageActive('landing'))
    <!DOCTYPE html>
    <html lang="en">
    <head><title>Page Unavailable</title><style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;background:#f1f5f9;color:#475569;text-align:center;padding:20px;}h1{font-size:2rem;margin-bottom:1rem;}.card{background:white;padding:40px;border-radius:20px;box-shadow:0 10px 40px rgba(0,0,0,0.08);}</style></head>
    <body><div class="card"><h1>Page Currently Unavailable</h1><p>This page has been disabled by the administrator. Please check back later.</p></div></body>
    </html>
@else
@php
    $heroImageOne = asset('assets/landing/images/buildi.jpeg');
    $heroImageTwo = asset('assets/landing/images/empowerment.jpeg');
    $heroImageThree = asset('assets/landing/images/foodchan.jpeg');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <base target="_self">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ App\Models\PageContent::getContent('landing', 'hero_title', 'FCCEI Platform') }}</title>
    @include('partials.seo-meta', [
        'seoTitle' => App\Models\PageContent::getContent('landing', 'hero_title', 'FCCEI Platform'),
        'seoDesc' => App\Models\PageContent::getContent('landing', 'hero_subtitle', 'Join the food security network that empowers households with quality staple foods.'),
        'seoType' => 'website',
        'schema' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => config('app.name', 'FoodChain Community Empowerment Initiative'),
                'url' => url('/'),
                'logo' => asset('assets/landing/images/FCCEILOGO.png'),
                'description' => 'A community-driven marketplace empowering low-income households across Nigeria with affordable staple foods and free membership.',
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => config('app.name', 'FoodChain Community Empowerment Initiative'),
                'url' => url('/'),
            ],
        ],
    ])
    <meta name="keywords" content="FCCEI, food chain, community empowerment, food security, Nigeria">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    @include('partials.vite-css')
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap');
        :root { --ink: #10231d; --muted: #61736d; --green: #087f5b; --mint: #e8f5ee; --gold: #f2a41d; }
        html { scroll-behavior: smooth; }
        body { color: var(--ink); font-family: 'DM Sans', sans-serif; background: #f7faf8; }
        h1, h2, h3, h4, .font-display { font-family: 'Manrope', sans-serif; letter-spacing: -0.02em; }
        section { scroll-margin-top: 6rem; }
        .landing-shell { position: relative; overflow-x: hidden; overflow-y: visible; }
        .landing-shell::before { content: ''; position: absolute; inset: 0 0 auto; height: 520px; pointer-events: none; background: radial-gradient(circle at 82% 12%, rgba(16,185,129,.14), transparent 32%), radial-gradient(circle at 18% 0%, rgba(245,158,11,.10), transparent 28%); }
        .hero-copy { text-shadow: 0 2px 24px rgba(0,0,0,.18); animation: hero-copy-in .7s cubic-bezier(.2,.8,.2,1) both; }
        @keyframes hero-copy-in { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
        .hero-panel { background: rgba(6, 78, 59, .72); border: 1px solid rgba(255,255,255,.18); backdrop-filter: blur(14px); }
        .section-kicker { color: var(--green); font-size: .72rem; font-weight: 800; letter-spacing: .18em; text-transform: uppercase; }
        .soft-panel { border: 1px solid rgba(16, 185, 129, .10); box-shadow: 0 18px 45px rgba(15, 23, 42, .06); }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; transition-duration: .01ms !important; } }
        .stat-number { font-family: 'Manrope', sans-serif; letter-spacing: -.04em; }
        .landing-slide-1 { background-image: linear-gradient(rgba(0,0,0,.58), rgba(0,0,0,.58)), url('{{ $heroImageOne }}'); }
        .landing-slide-2 { background-image: linear-gradient(rgba(0,0,0,.58), rgba(0,0,0,.58)), url('{{ $heroImageTwo }}'); }
        .landing-slide-3 { background-image: linear-gradient(rgba(0,0,0,.58), rgba(0,0,0,.58)), url('{{ $heroImageThree }}'); }
        .hero-slide { background-size: cover; background-position: center; transition: opacity .8s ease-in-out; }
        .slider-dot.active { background-color: #ffffff !important; width: 2rem !important; }
        .nav-link { position: relative; }
        .nav-link::after { content: ''; position: absolute; width: 0; height: 2px; bottom: -5px; left: 0; background-color: #059669; transition: width 0.3s ease; }
        .nav-link:hover::after { width: 100%; }
        .product-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); }
        .blog-card { transition: all 0.3s ease; }
        .blog-card:hover { transform: translateY(-5px); }
        .faq-content { transition: max-height 0.4s ease-in-out; }
        .rotate-180 { transform: rotate(180deg); }
        .gradient-text { background: linear-gradient(90deg, #059669 0%, #10B981 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
    </style>
</head>
<body class="min-h-screen font-sans landing-shell">
    @include('partials.skip-link')
    @include('partials.header')

    <main id="main-content">
        <section id="home" class="relative overflow-hidden min-h-[620px] md:min-h-[700px] bg-black shadow-[0_24px_70px_rgba(4,47,37,0.18)]">
            <div id="hero-slider" class="absolute inset-0">
                @php $heroImage = App\Models\PageContent::getContent('landing', 'hero_image'); @endphp
                <div class="hero-slide landing-slide-1 absolute inset-0 bg-cover bg-center flex items-center opacity-100 z-10" @if($heroImage) style="background-image: url('{{ public_storage_url($heroImage) }}');" @endif>
                    <div class="container mx-auto px-4 py-20 md:py-24">
                        <div class="max-w-3xl animate-slide-in hero-copy">
                            @php $heroTitle = App\Models\PageContent::getContent('landing', 'hero_title', 'Join the FoodChain Marketplace'); @endphp
                            <p class="text-xs md:text-sm font-bold uppercase tracking-[0.2em] text-emerald-200 mb-4">Food security, trusted marketplace</p>
                            <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold leading-[1.02] text-white mb-6 font-display">{{ $heroTitle }}</h1>
                            <p class="text-base md:text-xl text-white/90 mb-8 max-w-2xl leading-relaxed">{{ App\Models\PageContent::getContent('landing', 'hero_subtitle', 'Shop quality groceries from trusted vendors with free registration, instant account access and prices that adjust to your state.') }}</p>
                            <div class="flex flex-wrap gap-3">
                                <a href="{{ route('marketplace.shop') }}" class="px-6 py-3.5 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-400 transition shadow-lg shadow-amber-500/30"><i class="fas fa-store mr-2"></i>Shop groceries</a>
                                <a href="{{ route('register') }}" class="px-6 py-3.5 bg-white text-emerald-800 font-bold rounded-xl hover:bg-emerald-50 transition shadow-lg shadow-black/10">Create free account</a>
                                <a href="#how-it-works" class="px-6 py-3.5 border border-white/50 text-white font-bold rounded-xl hover:bg-white/10 transition">See how it works</a>
                            </div>
                            <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-emerald-100"><span><i class="fas fa-check-circle text-emerald-300 mr-2"></i>Free to join</span><span><i class="fas fa-check-circle text-emerald-300 mr-2"></i>Secure payments</span><span><i class="fas fa-check-circle text-emerald-300 mr-2"></i>Access across locations</span></div>
                        </div>
                    </div>
                </div>
                <div class="hero-slide landing-slide-2 absolute inset-0 bg-cover bg-center flex items-center opacity-0">
                    <div class="container mx-auto px-4 py-20 md:py-24">
                        <div class="max-w-2xl">
                            <h2 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">Shop Groceries at Your <span class="text-amber-400">Local</span> Price</h2>
                            <p class="text-xl text-white/90 mb-8">Pick your delivery state and every price updates instantly. Order rice, beans, proteins and more from trusted vendors.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('marketplace.shop') }}" class="px-8 py-3 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 transition shadow-lg shadow-amber-500/30"><i class="fas fa-shopping-basket mr-2"></i>Start Shopping Now</a>
                                <a href="{{ route('register') }}" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition">Join Free</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero-slide landing-slide-3 absolute inset-0 bg-cover bg-center flex items-center opacity-0">
                    <div class="container mx-auto px-4 py-20 md:py-24">
                        <div class="max-w-2xl">
                            <h2 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">Join the FoodChain <span class="text-emerald-400">Free</span></h2>
                            <p class="text-xl text-white/90 mb-8">Create your account in under a minute. No registration fee and no waiting. Your account is active the moment you sign up.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('register') }}" class="px-8 py-3 bg-emerald-500 text-white font-bold rounded-lg hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/30">Join Free Today</a>
                                <a href="{{ route('marketplace.shop') }}" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition">Browse Groceries</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3 z-20" role="group" aria-label="Hero carousel slides">
                <button type="button" onclick="showSlide(0)" aria-label="Go to slide 1" class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" id="dot-0"></button>
                <button type="button" onclick="showSlide(1)" aria-label="Go to slide 2" class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" id="dot-1"></button>
                <button type="button" onclick="showSlide(2)" aria-label="Go to slide 3" class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" id="dot-2"></button>
            </div>
        </section>

        <section class="py-5 bg-gray-900 text-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center"><div class="text-4xl md:text-5xl font-bold mb-2 gradient-text stat-number">50K+</div><div class="text-gray-300">{{ App\Models\PageContent::getContent('landing', 'stats_members', 'Active Members') }}</div></div>
                    <div class="text-center"><div class="text-4xl md:text-5xl font-bold mb-2 gradient-text stat-number">₦10M+</div><div class="text-gray-300">{{ App\Models\PageContent::getContent('landing', 'stats_distributions', 'Food Distributions') }}</div></div>
                    <div class="text-center"><div class="text-4xl md:text-5xl font-bold mb-2 gradient-text stat-number">20+</div><div class="text-gray-300">{{ App\Models\PageContent::getContent('landing', 'stats_communities', 'Communities Reached') }}</div></div>
                    <div class="text-center"><div class="text-4xl md:text-5xl font-bold mb-2 gradient-text stat-number">24/7</div><div class="text-gray-300">Support</div></div>
                </div>
            </div>
        </section>

        <section id="store" class="py-20 bg-gradient-to-br from-emerald-900 via-emerald-800 to-teal-900 text-white overflow-hidden">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <span class="inline-flex items-center px-4 py-1.5 bg-amber-400 text-emerald-950 text-xs font-bold uppercase tracking-wider rounded-full mb-6">
                            <i class="fas fa-store mr-2"></i>FoodChain Marketplace
                        </span>
                        <h2 class="text-4xl md:text-5xl font-bold text-white mb-4 font-display">Shop Quality <span class="text-amber-400">Groceries</span> Online</h2>
                        <p class="text-emerald-100 text-lg mb-8 max-w-xl">
                            Browse hundreds of food staples and household essentials from trusted vendors and enjoy member prices, weekly deals and home or office delivery.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
                            <div class="flex items-start"><i class="fas fa-seedling text-amber-400 mt-1 mr-3"></i><span class="text-emerald-50 text-sm">Grains, proteins, seasonings &amp; more</span></div>
                            <div class="flex items-start"><i class="fas fa-percent text-amber-400 mt-1 mr-3"></i><span class="text-emerald-50 text-sm">Flash sales, coupons &amp; member-only discounts</span></div>
                            <div class="flex items-start"><i class="fas fa-wallet text-amber-400 mt-1 mr-3"></i><span class="text-emerald-50 text-sm">Pay securely with your wallet or card</span></div>
                            <div class="flex items-start"><i class="fas fa-truck text-amber-400 mt-1 mr-3"></i><span class="text-emerald-50 text-sm">Home, pickup &amp; delivery options</span></div>
                        </div>
                        <div class="flex flex-wrap gap-4">
                            <a href="{{ route('marketplace.shop') }}" class="px-8 py-3 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 transition shadow-lg shadow-amber-500/30"><i class="fas fa-shopping-basket mr-2"></i>Browse the Store</a>
                            <a href="{{ route('marketplace.home') }}" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition">Today's Deals</a>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur rounded-3xl p-8 border border-white/20">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-white">Popular Categories</h3>
                            <a href="{{ route('marketplace.shop') }}" class="text-amber-300 text-sm font-semibold hover:text-amber-200">View all <i class="fas fa-arrow-right ml-1"></i></a>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mb-8">
                            @php
                                $storeCats = collect();
                                if (\Illuminate\Support\Facades\Schema::hasTable('marketplace_categories')) {
                                    $storeCats = \App\Models\Marketplace\Category::active()->parents()
                                        ->withCount('products')->orderByDesc('products_count')->take(6)->get();
                                }
                                $storeIcons = ['fa-seedling', 'fa-tint', 'fa-mortar-pestle', 'fa-drumstick-bite', 'fa-wine-bottle', 'fa-home'];
                            @endphp
                            @if ($storeCats->isNotEmpty())
                                @foreach ($storeCats as $catIdx => $cat)
                                    <a href="{{ route('marketplace.category', $cat->slug) }}" class="group flex items-center gap-3 bg-emerald-950/40 hover:bg-emerald-950/70 border border-white/10 rounded-xl px-3 py-3 transition-all">
                                        <i class="fas {{ $storeIcons[$catIdx % count($storeIcons)] }} text-amber-400"></i>
                                        <span class="text-sm text-emerald-50 font-medium group-hover:text-white">{{ $cat->name }}</span>
                                    </a>
                                @endforeach
                            @else
                                @foreach (['Staples', 'Proteins', 'Cereals', 'Seasonings', 'Household', 'Drinks'] as $idx => $label)
                                    <div class="flex items-center gap-3 bg-emerald-950/40 border border-white/10 rounded-xl px-3 py-3">
                                        <i class="fas {{ $storeIcons[$idx % count($storeIcons)] }} text-amber-400"></i>
                                        <span class="text-sm text-emerald-50 font-medium">{{ $label }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="bg-emerald-950/40 border border-amber-400/30 rounded-2xl p-5 flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <div class="text-amber-300 font-bold mb-1"><i class="fas fa-bolt mr-1"></i>Earn while you shop</div>
                                <p class="text-emerald-100 text-sm">Earn loyalty points on every order &amp; redeem them as wallet credit.</p>
                            </div>
                            <a href="{{ route('register') }}" class="px-5 py-2 bg-white text-emerald-900 font-bold rounded-lg hover:bg-gray-100 transition whitespace-nowrap">Start Shopping</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="how-it-works" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">How It <span class="text-emerald-500">Works</span></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Sign up in under a minute and start shopping. No fees and no waiting. It is free to join.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                        <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full bg-emerald-50">
                            <i class="fas fa-user-plus text-3xl text-emerald-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">1. Create a Free Account</h3>
                        <p class="text-gray-600">Register in under a minute. There is no registration fee and your account is active instantly.</p>
                    </div>
                    <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                        <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full bg-cyan-50">
                            <i class="fas fa-store text-3xl text-cyan-600"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">2. Shop at Your Local Price</h3>
                        <p class="text-gray-600">Choose your delivery state and prices update instantly. Order staples from trusted vendors in your selected location.</p>
                    </div>
                    <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                        <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full bg-amber-50">
                            <i class="fas fa-truck text-3xl text-amber-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">3. Get It Delivered</h3>
                        <p class="text-gray-600">Choose home, pickup or delivery and earn loyalty points on every order.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="products" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Explore the <span class="text-emerald-500">Marketplace</span></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">One marketplace for everything you need. Shop groceries at your local price, unlock flash deals, enjoy fast pickup or delivery, and sell to households across different locations.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="p-8">
                            <div class="w-16 h-16 bg-emerald-50 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-basket-shopping text-2xl text-emerald-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Shop at Local Prices</h3>
                            <p class="text-sm text-gray-500 mb-6">Fresh groceries, your state</p>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-location-dot text-emerald-500 mt-1 mr-3"></i><span>Prices update instantly based on your delivery state.</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-store text-emerald-500 mt-1 mr-3"></i><span>Staples and essentials from trusted vendors across different locations.</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-wallet text-cyan-600 mt-1 mr-3"></i><span>Pay securely with wallet balance or at checkout.</span></li>
                            </ul>
                            <a href="{{ route('marketplace.shop') }}" class="block w-full py-3 border-2 border-emerald-600 text-emerald-600 text-center font-bold rounded-lg hover:bg-emerald-600 hover:text-white transition-all">Browse the Store</a>
                        </div>
                    </div>
                    <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="p-8">
                            <div class="w-16 h-16 bg-amber-50 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-bolt text-2xl text-amber-500"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Flash Deals &amp; Loyalty</h3>
                            <p class="text-sm text-gray-500 mb-6">Save more every week</p>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-percent text-amber-500 mt-1 mr-3"></i><span>Weekly flash sales with deep discounts on essentials.</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-coins text-amber-500 mt-1 mr-3"></i><span>Earn loyalty points on every order you place.</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-gift text-cyan-600 mt-1 mr-3"></i><span>Redeem points for wallet credit on future purchases.</span></li>
                            </ul>
                            <a href="{{ route('marketplace.flash-sale') }}" class="block w-full py-3 border-2 border-emerald-600 text-emerald-600 text-center font-bold rounded-lg hover:bg-emerald-600 hover:text-white transition-all">See Today's Deals</a>
                        </div>
                    </div>
                    <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 relative">
                        <div class="absolute top-0 right-0 bg-amber-400 text-white px-4 py-1 rounded-bl-lg text-xs font-bold uppercase">Popular</div>
                        <div class="p-8">
                            <div class="w-16 h-16 bg-cyan-50 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-truck-fast text-2xl text-cyan-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Pickup &amp; Fast Delivery</h3>
                            <p class="text-sm text-gray-500 mb-6">Collect or get it delivered</p>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-store text-cyan-600 mt-1 mr-3"></i><span>Collect from our food hubs and collection centres in our coverage locations.</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-truck text-cyan-600 mt-1 mr-3"></i><span>Reliable home or office delivery through our logistics partner network.</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-clock text-emerald-500 mt-1 mr-3"></i><span>Skip the market trip and shop online to get your essentials without the stress.</span></li>
                            </ul>
                            <a href="{{ route('marketplace.shop') }}" class="block w-full py-3 border-2 border-emerald-600 text-emerald-600 text-center font-bold rounded-lg hover:bg-emerald-600 hover:text-white transition-all">Browse the Store</a>
                        </div>
                    </div>
                    <div class="product-card bg-emerald-900 rounded-2xl shadow-xl overflow-hidden text-white">
                        <div class="p-8">
                            <div class="w-16 h-16 bg-emerald-700 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-store text-2xl text-emerald-300"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Sell Your Products</h3>
                            <p class="text-emerald-300 text-sm mb-6">Become a vendor</p>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-shop text-emerald-400 mt-1 mr-3"></i><span>Open your vendor storefront and list products for free.</span></li>
                                <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-chart-line text-emerald-400 mt-1 mr-3"></i><span>Reach thousands of households across your selected delivery locations.</span></li>
                                <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-envelope-open-text text-emerald-400 mt-1 mr-3"></i><span>Manage inventory, orders and earnings in one place.</span></li>
                            </ul>
                            <a href="{{ route('marketplace.vendors') }}" class="block w-full py-3 bg-amber-400 text-emerald-950 text-center font-bold rounded-lg hover:bg-amber-300 transition-all">Start Selling</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="about" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl font-bold text-gray-900 mb-6 font-display">{{ App\Models\PageContent::getContent('landing', 'about_title', 'About FCCEI') }}</h2>
                        @php $aboutContent = trim((string) App\Models\PageContent::getContent('landing', 'about_content', '')); @endphp
                        @if($aboutContent !== '')
                        <p class="text-lg text-gray-700 mb-6 leading-relaxed font-medium border-l-4 border-emerald-500 pl-4">{{ $aboutContent }}</p>
                        @endif
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            Our core value propositions include: <strong>QUALITY</strong>, <strong>CONVENIENCE</strong> and <strong>AFFORDABILITY</strong>.
                        </p>
                        <p class="text-gray-600 mb-3 leading-relaxed">
                            <strong>Quality:</strong> We make sure that all our products are clean, nutritious, well processed and approved by NAFDAC or other regulations in Nigeria.
                        </p>
                        <p class="text-gray-600 mb-3 leading-relaxed">
                            <strong>Convenience:</strong> We have some food hubs distribution network within our coverage locations for PICKUP. We also partner with Logistics services to provide HOME or OFFICE delivery. This is to eliminate the stress of going to the market for busy working-class ladies and gentlemen, business women and students of tertiary institutions.
                        </p>
                        <p class="text-gray-600 mb-8 leading-relaxed">
                            <strong>Affordability:</strong> We make sure that our prices are highly competitive and better than what you can get in open markets and supermarkets, making food and grocery more affordable for members of the community within the Cities where we operate. We eliminate middlemen inflation and lower retail food and grocery Prices.
                        </p>
                        <button id="openModalBtn" class="inline-block bg-emerald-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-emerald-700 transition duration-300 shadow-lg shadow-emerald-600/30">
                            Read More
                        </button>
                        <div class="grid grid-cols-2 gap-6 mt-8">
                            <div><div class="text-3xl font-bold mb-2 text-emerald-500">Local</div><div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">State-Based Prices</div></div>
                            <div><div class="text-3xl font-bold mb-2 text-cyan-600">Free</div><div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">To Join and Shop</div></div>
                        </div>
                    </div>
                    <div class="relative">
                        <img src="{{ asset('assets/landing/images/about.jpg') }}" alt="FCCEI Food Staples" class="rounded-2xl shadow-xl w-full h-[400px] object-cover" loading="lazy" />
                        <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-2xl shadow-lg w-72 border-t-4 border-emerald-500">
                            <div class="text-3xl font-bold mb-2 text-emerald-600">Marketplace</div>
                            <div class="text-gray-600 text-sm italic">Verified vendors, quality groceries and fair state-based prices.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div id="aboutModal" class="fixed inset-0 z-40 hidden" role="dialog" aria-modal="true" aria-labelledby="aboutModalTitle">
            <div class="absolute inset-0 bg-black bg-opacity-60" data-about-backdrop></div>
            <div class="relative z-10 mx-auto max-w-6xl w-full px-4 pb-4" style="margin-top: 5.5rem;">
                <div class="bg-white rounded-2xl shadow-2xl flex flex-col md:flex-row overflow-y-auto" style="max-height: calc(100vh - 7rem);">
                    <button type="button" id="closeModalBtn" aria-label="Close details dialog" class="absolute top-3 right-3 z-50 w-9 h-9 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-800 text-2xl leading-none">&times;</button>
                    <div class="flex-1 p-6 md:p-8">
                        <h2 id="aboutModalTitle" class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 font-display pr-10">{{ App\Models\PageContent::getContent('landing', 'about_title', 'About FCCEI') }}</h2>
                        <p class="mb-4 leading-relaxed">FoodChain Community Empowerment is a technology-driven retail food distribution network. It is a food and grocery marketplace where people shop for household essentials. It is a marketplace with social impact designed to reduce cost, time and increase the quality of food and grocery shopping for households.</p>
                        <p class="mb-4 leading-relaxed">Our core value propositions include: <strong>QUALITY</strong>, <strong>CONVENIENCE</strong> and <strong>AFFORDABILITY</strong>.</p>
                        <p class="mb-4 leading-relaxed"><strong>Quality:</strong> We make sure that all our products are clean, nutritious, well processed and approved by NAFDAC or other regulations in Nigeria.</p>
                        <p class="mb-4 leading-relaxed"><strong>Convenience:</strong> We have some food hubs distribution network within our coverage locations for PICKUP. We also partner with Logistics services to provide HOME or OFFICE delivery. This is to eliminate the stress of going to the market for busy working-class ladies and gentlemen, business women and students of tertiary institutions.</p>
                        <p class="mb-4 leading-relaxed"><strong>Affordability:</strong> We make sure that our prices are highly competitive and better than what you can get in open markets and supermarkets, making food and grocery more affordable for members of the community within the Cities where we operate. We eliminate middlemen inflation and lower retail food and grocery Prices.</p>
                    </div>
                    <div class="relative md:w-[45%] md:min-w-[320px] shrink-0">
                        <img src="{{ asset('assets/landing/images/about.jpg') }}" alt="FoodChain Marketplace groceries" class="w-full h-56 md:h-full object-cover rounded-b-2xl md:rounded-none md:rounded-r-2xl" loading="lazy" />
                        <div class="absolute bottom-4 right-4 bg-white p-5 rounded-2xl shadow-lg w-64 border-t-4 border-emerald-500">
                            <div class="text-lg font-bold mb-2 text-emerald-600">Who is This For?</div>
                            <div class="text-gray-600 text-sm italic">Women, Low-income earners, Micro-business owners and Students of tertiary institutions.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section id="values-vision" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-20">
                    <div class="p-8 rounded-2xl bg-white shadow-md border-l-8 border-emerald-500">
                        <h3 class="text-2xl font-bold mb-4 text-emerald-600">Vision Statement</h3>
                        <p class="text-gray-700 leading-relaxed">To be Nigeria&rsquo;s trusted community-driven food ecosystem, where the FoodChain Marketplace is the everyday store every household turns to for affordable, quality staple food, and where local vendors grow without middlemen.</p>
                    </div>
                    <div class="p-8 rounded-2xl bg-white shadow-md border-l-8 border-cyan-600">
                        <h3 class="text-2xl font-bold mb-4 text-cyan-600">Mission Statement</h3>
                        <p class="text-gray-700 leading-relaxed">To run an open online food store that connects verified local vendors directly with households, with fair state-based pricing, secure wallet and card payments, and reliable delivery to your selected location, so that food security, shared growth and dignity in humanity are within everyone&rsquo;s reach.</p>
                    </div>
                </div>
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Core Values: <span class="text-amber-500">SECURE FOOD</span></h2>
                    <p class="text-gray-600 max-w-3xl mx-auto">Our values are embedded in the concept of SECURE FOOD, and they are put to work every day in the FoodChain Marketplace, the store where verified local vendors sell directly to households at fair, state-based prices.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-20">
                    @php
                    $values = [
                        ['letter' => 'S', 'title' => 'Sustainability', 'color' => 'emerald', 'desc' => 'A marketplace that keeps local vendors trading and households fed over the long term, not one-off handouts.'],
                        ['letter' => 'E', 'title' => 'Empowerment', 'color' => 'cyan', 'desc' => 'Vendors set their own prices and reach customers beyond their stall; shoppers take control of what they pay.'],
                        ['letter' => 'C', 'title' => 'Credibility', 'color' => 'amber', 'desc' => 'Every vendor is verified before listing, and every product shows a real price for the buyer\u{2019}s state.'],
                        ['letter' => 'U', 'title' => 'Unlimited Access', 'color' => 'emerald', 'desc' => 'An open storefront anyone can shop \u{2014} staples, groceries and household food items, delivered across selected locations.'],
                        ['letter' => 'R', 'title' => 'Respect for All', 'color' => 'cyan', 'desc' => 'A fair, dignified shopping experience for every customer and every vendor on the platform.'],
                        ['letter' => 'E', 'title' => 'Equity', 'color' => 'amber', 'desc' => 'Cutting out middleman inflation so food support and fair prices reach those who need them most.'],
                        ['letter' => 'F', 'title' => 'Food Security', 'color' => 'amber', 'desc' => 'Sufficient, safe and quality food within reach \u{2014} the reason the marketplace exists.'],
                        ['letter' => 'O', 'title' => 'Opportunity', 'color' => 'amber', 'desc' => 'Income and visibility for small food businesses trading online for the first time.'],
                        ['letter' => 'O', 'title' => 'Optimum Nutrition', 'color' => 'amber', 'desc' => 'A curated range of quality food items that support healthy living.'],
                        ['letter' => 'D', 'title' => 'Delightful Experience', 'color' => 'amber', 'desc' => 'Secure wallet and card payments, and delivery that arrives when promised.'],
                    ];
                    @endphp
                    @foreach($values as $v)
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
                        <span class="text-4xl font-black block mb-2 text-{{ $v['color'] }}-500">{{ $v['letter'] }}</span>
                        <h4 class="font-bold text-lg mb-2">{{ $v['title'] }}</h4>
                        <p class="text-sm text-gray-600">{{ $v['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
                <div class="bg-white p-10 rounded-3xl shadow-lg border border-gray-100 text-center max-w-4xl mx-auto">
                    <h3 class="text-2xl font-bold mb-6 text-cyan-600">Policy Statement</h3>
                    <p class="text-gray-600 italic leading-relaxed">FCCEI is committed to promoting food security through a structured, transparent, and community-driven model. In the FoodChain Marketplace, that commitment is enforced by policy: only verified vendors may list and sell; every product carries a published price for the buyer&rsquo;s state, so pricing is transparent and comparable; orders are settled through secure wallet and card payments; delivery is available in our coverage locations or collected at a micro-outlet; and every member participates responsibly and benefits equitably from the store.</p>
                </div>
            </div>
        </section>

        <section id="impact" class="py-20 bg-white">
            <div class="container mx-auto px-4 max-w-6xl">
                <div class="flex flex-col md:flex-row items-center justify-between mb-20 gap-10">
                    <div class="max-w-2xl text-center md:text-left">
                        <div class="inline-block mb-6">
                            <span class="text-emerald-700 font-bold uppercase tracking-widest text-sm border-b-2 border-emerald-700 pb-1">Our Track Record</span>
                        </div>
                        <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-8">From a Community Programme<br/><span class="text-gray-400">to a Community Food Store.</span></h2>
                        <p class="text-gray-600 text-lg leading-relaxed">We spent years supporting households directly with food assistance and food loans. That work taught us what shoppers actually struggle with: inflated prices, inconsistent quality, and no reliable way to compare what a staple should cost. The FoodChain Marketplace is the answer: the same community, now buying and selling on an open store where prices are transparent, vendors are verified, and delivery reaches your selected location.</p>
                    </div>
                    <div class="relative flex-shrink-0">
                        <div class="w-72 h-72 md:w-[380px] md:h-[380px] rounded-full border-[15px] border-white shadow-[0_20px_50px_rgba(0,0,0,0.1)] overflow-hidden">
                            <img src="{{ asset('assets/landing/images/impactwoman.webp') }}" alt="Impact Focus" class="w-full h-full object-cover" loading="lazy" decoding="async">
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-24">
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-black text-emerald-700 counter" data-target="15000">0</div>
                        <p class="text-gray-600 font-semibold text-sm">Meals Provided &amp; Ordered</p>
                    </div>
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-black text-emerald-700 counter" data-target="500">0</div>
                        <p class="text-gray-600 font-semibold text-sm">Women Shoppers &amp; Vendors Empowered</p>
                    </div>
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-black text-emerald-700 counter" data-target="36">0</div>
                        <p class="text-gray-600 font-semibold text-sm">States With Live Pricing &amp; Delivery</p>
                    </div>
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-black text-emerald-700 counter" data-target="98">0</div>
                        <p class="text-gray-600 font-semibold text-sm">Verified Vendors Onboarded (%)</p>
                    </div>
                </div>
                <div class="bg-gray-50/50 rounded-[3.5rem] p-10 md:p-16 flex flex-col lg:flex-row gap-16 items-center border border-gray-100">
                    <div class="lg:w-1/2 space-y-8">
                        <h4 class="text-3xl font-bold text-gray-900 leading-tight">Revolutionizing Food Security</h4>
                        <p class="text-gray-600 text-lg leading-relaxed">At FCCEI, we believe food security is the foundation of economic stability. Food inflation happens in the space between the farm and the shopper, and that is exactly where the FoodChain Marketplace intervenes. By putting verified local vendors and households on one open store, we remove the middleman markup, so the savings go to the family and the income goes to the vendor.</p>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4"><div class="bg-emerald-600 text-white p-1.5 rounded-full shadow-md"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div><span class="text-gray-800 font-semibold">Real prices for every state, so shoppers can compare before they buy.</span></div>
                            <div class="flex items-center gap-4"><div class="bg-emerald-600 text-white p-1.5 rounded-full shadow-md"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div><span class="text-gray-800 font-semibold">A storefront for verified vendors to grow past their own locality.</span></div>
                            <div class="flex items-center gap-4"><div class="bg-emerald-600 text-white p-1.5 rounded-full shadow-md"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div><span class="text-gray-800 font-semibold">Secure wallet and card payments, with home, pickup and delivery.</span></div>
                        </div>
                        <a href="#contact" class="inline-block bg-emerald-700 text-white px-10 py-4 rounded-2xl font-bold hover:bg-emerald-800 transition shadow-xl">Partner With Us</a>
                    </div>
                    <div class="lg:w-1/2 relative">
                        <div class="rounded-[3rem] overflow-hidden shadow-2xl border-4 border-white">
                            <img src="{{ asset('assets/landing/images/warehouse.webp') }}" alt="FCCEI Operations" class="w-full h-[400px] object-cover" loading="lazy" decoding="async">
                        </div>
                        <div class="absolute -bottom-6 -right-4 md:-right-10 bg-white p-8 rounded-3xl shadow-2xl border border-gray-50 max-w-xs animate-bounce-slow">
                            <p class="text-gray-700 font-medium italic mb-4 leading-relaxed">"The support I received changed my business and helped me provide for my family when I had nothing."</p>
                            <p class="text-emerald-700 text-xs font-bold uppercase tracking-widest">Beneficiary from Lagos</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="blog" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Latest <span class="text-emerald-500">Blog</span> Posts</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Insights, tips, and success stories from our marketplace community</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <img src="{{ asset('assets/landing/images/blogsa.jpeg') }}" alt="Quality Groceries" class="w-full h-48 object-cover" loading="lazy" decoding="async" />
                        <div class="p-6">
                            <div class="text-sm text-emerald-600 font-medium mb-2">QUALITY &amp; SAFETY</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">How We Keep Your Groceries Quality, Safe and Approved</h3>
                            <p class="text-gray-600 mb-4">From clean, well-processed staples to NAFDAC-approved products, discover how we hold every item on the marketplace to a high quality standard.</p>
                            <div class="flex items-center justify-between"><span class="text-sm text-gray-500">Jan 5, 2026</span><a href="{{ route('blog') }}" class="text-emerald-600 font-medium hover:text-emerald-700">Read More →</a></div>
                        </div>
                    </article>
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <img src="{{ asset('assets/landing/images/blogpost.jpeg') }}" alt="Local Vendor Stories" class="w-full h-48 object-cover" loading="lazy" decoding="async" />
                        <div class="p-6">
                            <div class="text-sm text-cyan-600 font-medium mb-2">VENDOR SPOTLIGHT</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">From Local Stall to a Growing Marketplace Store</h3>
                            <p class="text-gray-600 mb-4">Discover how grassroots food businesses grew their reach using the marketplace, landing orders from customers in new locations every single week.</p>
                            <div class="flex items-center justify-between"><span class="text-sm text-gray-500">Jan 5, 2026</span><a href="{{ route('blog') }}" class="text-emerald-600 font-medium hover:text-emerald-700">Read More →</a></div>
                        </div>
                    </article>
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <img src="{{ asset('assets/landing/images/bloggpo.webp') }}" alt="Shopping Tips" class="w-full h-48 object-cover" loading="lazy" decoding="async" />
                        <div class="p-6">
                            <div class="text-sm text-amber-600 font-medium mb-2">SHOPPING TIPS</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Getting Better Grocery Value With Loyalty Points</h3>
                            <p class="text-gray-600 mb-4">Learn how to build points on everyday orders, spot flash deals, and redeem rewards to lower the real cost of your monthly food shop.</p>
                            <div class="flex items-center justify-between"><span class="text-sm text-gray-500">Jan 5, 2026</span><a href="{{ route('blog') }}" class="text-emerald-600 font-medium hover:text-emerald-700">Read More →</a></div>
                        </div>
                    </article>
                </div>
                <div class="text-center mt-12">
                    <a href="{{ route('blog') }}" class="px-8 py-3 border-2 border-emerald-600 text-emerald-600 font-bold rounded-lg hover:bg-emerald-600 hover:text-white transition-all duration-200">View All Posts</a>
                </div>
            </div>
        </section>

        <section id="partners-sponsors" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold mb-4 font-display text-emerald-600">Our Partners</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">We collaborate with organizations and individuals committed to ending hunger and empowering communities.</p>
                </div>
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        @foreach(['partnermain.jpeg', 'imagesse.png', 'imagelands.jpg', 'imagesland.png', 'imagesleds.png', 'imageslogo.png'] as $partner)
                        <div class="swiper-slide relative flex justify-center items-center p-4 bg-white rounded-lg shadow hover:scale-105 transition-transform duration-300">
                            <img src="{{ asset('assets/landing/images/' . $partner) }}" alt="Partner Logo" class="max-h-20 object-contain mx-auto" loading="lazy" decoding="async">
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-pagination"></div>
                </div>
                <div class="mt-16 text-center">
                    <!-- <h3 class="text-3xl font-bold mb-4 text-emerald-600">Become a Partner or Sponsor</h3> -->
                    <p class="text-gray-600 mb-6">Support our mission to eradicate hunger and empower communities.</p>
                    <button id="openSponsorModal" class="px-8 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition shadow-lg">Get Involved</button>
                </div>

                <div id="sponsorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                    <div class="bg-white rounded-lg max-w-2xl w-full p-6 relative shadow-lg">
                        <button id="closeSponsorModal" type="button" aria-label="Close sponsor form" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                        <h4 class="text-2xl font-semibold mb-4 text-emerald-600">Join as a Sponsor</h4>
                        @include('partials.form-status')
                        <form action="{{ route('landing.sponsor') }}" method="POST" class="space-y-4">
                            @csrf
                            <div><label for="sponsor-name" class="block text-gray-700 mb-2">Name</label><input id="sponsor-name" type="text" name="name" required autocomplete="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="Your Name"></div>
                            <div><label for="sponsor-organization" class="block text-gray-700 mb-2">Organization</label><input id="sponsor-organization" type="text" name="organization" required autocomplete="organization" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="Your Organization"></div>
                            <div><label for="sponsor-email" class="block text-gray-700 mb-2">Email</label><input id="sponsor-email" type="email" name="email" required autocomplete="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="you@example.com"></div>
                            <div><label for="sponsor-message" class="block text-gray-700 mb-2">Message</label><textarea id="sponsor-message" name="message" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="Tell us about your sponsorship interest"></textarea></div>
                            <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition">Send Inquiry</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section id="faq" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Frequently Asked <span class="text-emerald-500">Questions</span></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Quick answers about accounts, pricing, ordering, payment and delivery on the FoodChain marketplace.</p>
                </div>
                <div class="max-w-3xl mx-auto space-y-4">
                    @php
                    $faqs = [
                        ['q' => 'Do I need to pay a membership fee?', 'a' => 'No. Registration is free and instant. Any registered, active user can browse and buy from the marketplace.'],
                        ['q' => 'Why do prices change by state?', 'a' => 'The true cost of food differs across Nigeria because of transport, storage and local demand. We show the price for the state you select, and that price is locked into your order.'],
                        ['q' => 'How do I choose my state?', 'a' => 'Use the state selector on the marketplace. Every product, cart total and checkout figure updates to reflect your local price.'],
                        ['q' => 'How do I place an order?', 'a' => 'Add items to your cart, review your delivery details, and complete checkout using a supported payment method or your wallet.'],
                        ['q' => 'What payment options do I have?', 'a' => 'You can pay on delivery or pay before delivery. Before delivery, choose a supported payment method (card, transfer or wallet) securely at checkout.'],
                        ['q' => 'How do loyalty points and flash sales work?', 'a' => 'Every payment, whether made before or on delivery, earns you a loyalty point. Loyalty points qualify you for flash sale purchases at special member-low prices.'],
                        ['q' => 'How much does delivery cost?', 'a' => 'Delivery costs are based on the state you select and are shown before you complete your order, so there are no surprises.'],
                        ['q' => 'How do I become a vendor or rider?', 'a' => 'Visit the Become a Supplier or Become a Rider pages, or register an account and follow the vendor or rider onboarding steps.'],
                    ];
                    @endphp
                    @foreach($faqs as $i => $faq)
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                        <button class="faq-toggle-btn w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-all focus:outline-none">
                            <span class="text-lg font-bold text-gray-900">Q{{ $i+1 }}. {{ $faq['q'] }}</span>
                            <i class="fas fa-chevron-down transition-transform duration-300 text-emerald-500"></i>
                        </button>
                        <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                            <div class="p-6 pt-0 border-t border-gray-50"><p class="text-gray-600">{!! $faq['a'] !!}</p></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="contact" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Get In <span class="text-emerald-500">Touch</span></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Questions about the marketplace, selling on the store, or getting started? We&rsquo;re here to help.</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-8">Contact Information</h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center mr-4"><i class="fas fa-map-marker-alt text-emerald-600"></i></div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Office Address for Enquiry:</h4>
                                    <p class="text-gray-600">No. 9B, Adejumo Tennis Club Road, opposite DE-RESTED PHARMACY,<br>Ilamoshe Estate, Oke Afa, Ejigbo, Lagos.</p>
                                    <p class="text-gray-600 mt-3"><strong>Central Warehouse 1:</strong><br>31, Ofada Street, Off Ladipo Rd, Mushin.</p>
                                    <p class="text-gray-600 mt-3"><strong>Our Distribution Hubs in Lagos:</strong></p>
                                    <ul class="list-disc list-inside text-gray-600 mt-1 space-y-1">
                                        <li><strong>Oyingbo outlet:</strong> Beside Chisco Plaza, Oyingbo Market, Oyingbo, Lagos.</li>
                                        <li><strong>Ebutte-ero outlet:</strong> Ejalonibu Plaza, Ebutte-ero Main Market, Lagos Island.</li>
                                        <li><strong>Igando outlet:</strong> Behind BRT, beside Eko Five Hotel, Igando Plaza Market, Igando.</li>
                                        <li><strong>Ikorodu 1 outlet:</strong> 22, Ayangburen Road, Ikorodu.</li>
                                        <li><strong>Ikorodu 2 outlet:</strong> No. 37 Alhaji Street, Ladega, before LG Office, Ladega.</li>
                                        <li><strong>Sango outlet:</strong> 29 Tarmac Rd, Sango Otta, Sango.</li>
                                        <li><strong>Ejigbo outlet:</strong> Shop MKT/EJ/S-H 11,13,19, Oba Ojon Central Market, Power Line, Iyana Ejigbo.</li>
                                        <li><strong>Ojuwoye outlet:</strong> 7, Olumorokun Street, Ojuwoye, Mushin.</li>
                                        <li><strong>Post Office outlet:</strong> 11/12 Post Office Rd, Mushin.</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-cyan-50 rounded-lg flex items-center justify-center mr-4"><i class="fas fa-phone text-cyan-600"></i></div>
                                <div><h4 class="font-bold text-gray-900 mb-1">Phone Number</h4><p class="text-gray-600">+234 816 801 8123, +234 807 070 1855</p></div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-amber-50 rounded-lg flex items-center justify-center mr-4"><i class="fas fa-envelope text-amber-600"></i></div>
                                <div><h4 class="font-bold text-gray-900 mb-1">Email Address</h4><p class="text-gray-600">info@foodchainempowerment.com.ng</p></div>
                            </div>
                        </div>
                        <div class="mt-12">
                            <h4 class="font-bold text-gray-900 mb-4">Follow Us</h4>
                            <div class="flex space-x-4">
                                <a href="https://web.facebook.com/profile.php?id=61585986685899" aria-label="FoodChain Empowerment on Facebook" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all duration-200"><i class="fab fa-facebook-f" aria-hidden="true"></i></a>
                                <a href="https://www.instagram.com/foodchainempowerment/" aria-label="FoodChain Empowerment on Instagram" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all duration-200"><i class="fab fa-instagram" aria-hidden="true"></i></a>
                                <a href="https://www.tiktok.com/@foodchainempowerm?lang=en" aria-label="FoodChain Empowerment on TikTok" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all duration-200"><i class="fab fa-tiktok" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h3>
                        @include('partials.form-status')
                        <form action="{{ route('landing.contact') }}" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label for="contact-name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label><input id="contact-name" type="text" name="name" autocomplete="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required></div>
                                <div><label for="contact-email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label><input id="contact-email" type="email" name="email" autocomplete="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required></div>
                            </div>
                            <div><label for="contact-subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label><input id="contact-subject" type="text" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required></div>
                            <div><label for="contact-message" class="block text-sm font-medium text-gray-700 mb-2">Message</label><textarea id="contact-message" name="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required></textarea></div>
                            <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition-all duration-200 shadow-lg">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="pt-16 pb-24 -mb-16 bg-gradient-to-r from-emerald-600 to-emerald-800">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-white mb-4 font-display">Stay Updated</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">Subscribe to our newsletter for the latest tips, success stories, and platform updates</p>
                @include('partials.form-status')
                <form action="{{ route('landing.newsletter') }}" method="POST" class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
                    @csrf
                    <div class="flex-1 text-left">
                        <label for="newsletter-email" class="sr-only">Email address</label>
                        <input id="newsletter-email" type="email" name="email" required autocomplete="email" placeholder="Your email address" class="w-full px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white">
                    </div>
                    <button type="submit" class="px-8 py-3 bg-white text-emerald-700 font-bold rounded-lg hover:bg-gray-100 transition-all duration-200">Subscribe</button>
                </form>
            </div>
        </section>

        <!-- <section class="py-20 bg-gradient-to-r from-emerald-600 to-cyan-600">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-4xl font-bold text-white mb-6">{{ App\Models\PageContent::getContent('landing', 'cta_title', 'Ready to Make a Difference?') }}</h2>
                <p class="text-xl text-white/90 max-w-2xl mx-auto mb-8">{{ App\Models\PageContent::getContent('landing', 'cta_content', 'Join thousands of members already benefiting from FoodChain community programs.') }}</p>
                <a href="{{ route('register') }}" class="inline-block px-10 py-4 bg-white text-emerald-700 font-bold rounded-lg text-lg hover:bg-gray-100 transition shadow-xl">Get Started Today</a>
            </div>
        </section> -->
    </main>

    @include('partials.site-footer')

    <a href="https://chat.whatsapp.com/C8V1raWRahMEknKjmZUOmj" target="_blank" class="fixed bottom-8 right-8 z-[60] group">
        <span class="absolute right-full mr-4 top-1/2 -translate-y-1/2 bg-gray-900 text-white text-xs font-bold px-4 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow-xl">Chat with an Event Manager</span>
        <div class="bg-[#25D366] p-4 rounded-full shadow-2xl hover:scale-110 transition duration-300">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </div>
    </a>

    <script>
        const heroSlides = document.querySelectorAll('#hero-slider > div');
        const dots = document.querySelectorAll('.slider-dot');
        let currentSlide = 0;

        function showSlide(index) {
            heroSlides.forEach((slide, i) => {
                const isActive = i === index;
                slide.style.opacity = isActive ? '1' : '0';
                slide.style.zIndex = isActive ? '10' : '1';
                // Inactive slides must not paint over the active one nor capture clicks.
                slide.style.visibility = isActive ? 'visible' : 'hidden';
                slide.style.pointerEvents = isActive ? 'auto' : 'none';
                slide.setAttribute('aria-hidden', isActive ? 'false' : 'true');
            });
            dots.forEach(dot => dot.classList.remove('active'));
            dots[index].classList.add('active');
            currentSlide = index;
        }
        dots.forEach((dot, index) => dot.addEventListener('click', () => showSlide(index)));
        setInterval(() => { showSlide((currentSlide + 1) % heroSlides.length); }, 5000);
        showSlide(0);

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                const targetElement = document.querySelector(targetId);
                if (targetElement) window.scrollTo({ top: targetElement.offsetTop - 80, behavior: 'smooth' });
            });
        });

        const aboutModal = document.getElementById('aboutModal');
        const openAbout = () => { aboutModal.classList.remove('hidden'); aboutModal.classList.add('block'); document.body.style.overflow = 'hidden'; };
        const closeAbout = () => { aboutModal.classList.add('hidden'); aboutModal.classList.remove('block'); document.body.style.overflow = ''; };
        document.getElementById('openModalBtn').addEventListener('click', openAbout);
        document.getElementById('closeModalBtn').addEventListener('click', closeAbout);
        aboutModal.addEventListener('click', function(e) { if (e.target === this || e.target.hasAttribute('data-about-backdrop')) closeAbout(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !aboutModal.classList.contains('hidden')) closeAbout(); });

        document.getElementById('openSponsorModal').addEventListener('click', () => {
            document.getElementById('sponsorModal').classList.remove('hidden');
            document.getElementById('sponsorModal').classList.add('flex');
        });
        document.getElementById('closeSponsorModal').addEventListener('click', () => {
            document.getElementById('sponsorModal').classList.add('hidden');
            document.getElementById('sponsorModal').classList.remove('flex');
        });
        window.addEventListener('click', (e) => { if (e.target === document.getElementById('sponsorModal')) { document.getElementById('sponsorModal').classList.add('hidden'); document.getElementById('sponsorModal').classList.remove('flex'); } });

        document.querySelectorAll('.faq-toggle-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const content = btn.nextElementSibling;
                const icon = btn.querySelector('i');
                if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                    content.style.maxHeight = '0px';
                    icon.classList.remove('rotate-180');
                } else {
                    document.querySelectorAll('.faq-content').forEach(el => { el.style.maxHeight = '0px'; el.previousElementSibling.querySelector('i').classList.remove('rotate-180'); });
                    content.style.maxHeight = content.scrollHeight + "px";
                    icon.classList.add('rotate-180');
                }
            });
        });

        new Swiper('.mySwiper', {
            slidesPerView: 1, spaceBetween: 20, loop: true,
            navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
            pagination: { el: '.swiper-pagination', clickable: true },
            breakpoints: { 768: { slidesPerView: 3 }, 1024: { slidesPerView: 4 } },
            autoplay: { delay: 3000, disableOnInteraction: false }
        });

        document.addEventListener('DOMContentLoaded', () => {
            const counters = document.querySelectorAll('.counter');
            const runCounter = (el) => {
                const target = +el.getAttribute('data-target');
                const duration = 2000;
                const increment = target / (duration / 16);
                let currentCount = 0;
                const updateCount = () => {
                    currentCount += increment;
                    if (currentCount < target) { el.innerText = Math.ceil(currentCount).toLocaleString(); requestAnimationFrame(updateCount); }
                    else { el.innerText = target.toLocaleString(); }
                };
                updateCount();
            };
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => { if (entry.isIntersecting) { runCounter(entry.target); observer.unobserve(entry.target); } });
            }, { threshold: 0.5 });
            counters.forEach(c => observer.observe(c));
        });
    </script>
</body>
</html>
@endif
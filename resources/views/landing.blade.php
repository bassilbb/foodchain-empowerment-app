@if(!App\Models\PageContent::isPageActive('landing'))
    <!DOCTYPE html>
    <html lang="en">
    <head><title>Page Unavailable</title><style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;background:#f1f5f9;color:#475569;text-align:center;padding:20px;}h1{font-size:2rem;margin-bottom:1rem;}.card{background:white;padding:40px;border-radius:20px;box-shadow:0 10px 40px rgba(0,0,0,0.08);}</style></head>
    <body><div class="card"><h1>Page Currently Unavailable</h1><p>This page has been disabled by the administrator. Please check back later.</p></div></body>
    </html>
@else
<!DOCTYPE html>
<html lang="en">
<head>
    <base target="_self">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ App\Models\PageContent::getContent('landing', 'hero_title', 'FCCEI Platform') }}</title>
    <meta name="description" content="{{ App\Models\PageContent::getContent('landing', 'hero_subtitle', 'Join the food security network that empowers households with quality staple foods.') }}">
    <meta name="keywords" content="FCCEI, food chain, community empowerment, food security, Nigeria">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: "#059669", light: "#10B981", dark: "#047857" },
                        secondary: { DEFAULT: "#0D9488", light: "#14B8A6", dark: "#0F766E" },
                        accent: { DEFAULT: "#D97706", light: "#F59E0B", dark: "#B45309" }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        'display': ['Poppins', 'system-ui', 'sans-serif']
                    },
                    backgroundImage: {
                        'slide-1': "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("assets/landing/images/buildi.jpeg") }}')",
                        'slide-2': "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("assets/landing/images/empowerment.jpeg") }}')",
                        'slide-3': "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("assets/landing/images/foodchan.jpeg") }}')"
                    },
                    animation: {
                        'slide-in': 'slideIn 0.5s ease-out forwards',
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'float': 'float 3s ease-in-out infinite',
                        'bounce-slow': 'bounceSlow 5s ease-in-out infinite'
                    },
                    keyframes: {
                        slideIn: { '0%': { transform: 'translateY(20px)', opacity: '0' }, '100%': { transform: 'translateY(0)', opacity: '1' } },
                        fadeIn: { '0%': { opacity: '0' }, '100%': { opacity: '1' } },
                        float: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } },
                        bounceSlow: { '0%, 100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-15px)' } }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap');
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
<body class="min-h-screen bg-gray-50 font-sans">
    @include('partials.header')

    <main>
        <section id="home" class="relative overflow-hidden h-[600px] bg-black">
            <div id="hero-slider" class="relative h-full">
                <div class="hero-slide absolute inset-0 bg-slide-1 bg-cover bg-center flex items-center opacity-100 z-10">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl animate-slide-in">
                            @php $heroTitle = App\Models\PageContent::getContent('landing', 'hero_title', 'Zero Hunger Through Community'); @endphp
                            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">{!! $heroTitle !!}</h1>
                            <p class="text-xl text-white/90 mb-8">{{ App\Models\PageContent::getContent('landing', 'hero_subtitle', 'Shop quality groceries from trusted vendors with free registration — your account is active instantly and prices adjust to your state.') }}</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('register') }}" class="px-8 py-3 bg-emerald-500 text-white font-bold rounded-lg hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/30">Create Free Account</a>
                                <a href="{{ route('marketplace.shop') }}" class="px-8 py-3 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 transition shadow-lg shadow-amber-500/30"><i class="fas fa-store mr-2"></i>Shop Groceries Now</a>
                                <a href="#store" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition">Explore the Store</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero-slide absolute inset-0 bg-slide-2 bg-cover bg-center flex items-center opacity-0">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl">
                            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">Shop Groceries at Your <span class="text-amber-400">Local</span> Price</h1>
                            <p class="text-xl text-white/90 mb-8">Pick your delivery state and every price updates instantly — order rice, beans, proteins and more from trusted vendors.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('marketplace.shop') }}" class="px-8 py-3 bg-amber-500 text-white font-bold rounded-lg hover:bg-amber-600 transition shadow-lg shadow-amber-500/30"><i class="fas fa-shopping-basket mr-2"></i>Start Shopping Now</a>
                                <a href="{{ route('register') }}" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition">Join Free</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="hero-slide absolute inset-0 bg-slide-3 bg-cover bg-center flex items-center opacity-0">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl">
                            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">Join the Community <span class="text-emerald-400">Free</span></h1>
                            <p class="text-xl text-white/90 mb-8">Create your account in under a minute. No registration fee and no waiting — your account is active the moment you sign up.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('register') }}" class="px-8 py-3 bg-emerald-500 text-white font-bold rounded-lg hover:bg-emerald-600 transition shadow-lg shadow-emerald-500/30">Join Free Today</a>
                                <a href="{{ route('marketplace.shop') }}" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition">Browse Groceries</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3 z-20">
                <button onclick="showSlide(0)" class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" id="dot-0"></button>
                <button onclick="showSlide(1)" class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" id="dot-1"></button>
                <button onclick="showSlide(2)" class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" id="dot-2"></button>
            </div>
        </section>

        <section class="py-5 bg-gray-900 text-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center"><div class="text-5xl font-bold mb-2 gradient-text">50K+</div><div class="text-gray-300">{{ App\Models\PageContent::getContent('landing', 'stats_members', 'Active Members') }}</div></div>
                    <div class="text-center"><div class="text-5xl font-bold mb-2 gradient-text">₦10M+</div><div class="text-gray-300">{{ App\Models\PageContent::getContent('landing', 'stats_distributions', 'Food Distributions') }}</div></div>
                    <div class="text-center"><div class="text-5xl font-bold mb-2 gradient-text">20+</div><div class="text-gray-300">{{ App\Models\PageContent::getContent('landing', 'stats_communities', 'Communities Reached') }}</div></div>
                    <div class="text-center"><div class="text-5xl font-bold mb-2 gradient-text">24/7</div><div class="text-gray-300">Support</div></div>
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
                            Browse hundreds of food staples and household essentials from trusted vendors — enjoy member prices, weekly deals, and doorstep or community-collection delivery.
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-10">
                            <div class="flex items-start"><i class="fas fa-seedling text-amber-400 mt-1 mr-3"></i><span class="text-emerald-50 text-sm">Grains, proteins, seasonings &amp; more</span></div>
                            <div class="flex items-start"><i class="fas fa-percent text-amber-400 mt-1 mr-3"></i><span class="text-emerald-50 text-sm">Flash sales, coupons &amp; member-only discounts</span></div>
                            <div class="flex items-start"><i class="fas fa-wallet text-amber-400 mt-1 mr-3"></i><span class="text-emerald-50 text-sm">Pay securely with your wallet or card</span></div>
                            <div class="flex items-start"><i class="fas fa-truck text-amber-400 mt-1 mr-3"></i><span class="text-emerald-50 text-sm">Home, pickup &amp; community delivery options</span></div>
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
                                $storeCats = \App\Models\Marketplace\Category::active()->parents()
                                    ->withCount('products')->orderByDesc('products_count')
                                    ->take(6)->get();
                                $storeIcons = ['fa-seedling', 'fa-tint', 'fa-mortar-pestle', 'fa-drumstick-bite', 'fa-wine-bottle', 'fa-home'];
                            @endphp
                            @foreach ($storeCats as $catIdx => $cat)
                                <a href="{{ route('marketplace.category', $cat->slug) }}" class="group flex items-center gap-3 bg-emerald-950/40 hover:bg-emerald-950/70 border border-white/10 rounded-xl px-3 py-3 transition-all">
                                    <i class="fas {{ $storeIcons[$catIdx % count($storeIcons)] }} text-amber-400"></i>
                                    <span class="text-sm text-emerald-50 font-medium group-hover:text-white">{{ $cat->name }}</span>
                                </a>
                            @endforeach
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
                    <p class="text-gray-600 max-w-2xl mx-auto">Sign up in under a minute and start shopping. No fees and no waiting — it is free to join.</p>
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
                        <p class="text-gray-600">Choose your delivery state and prices update instantly. Order staples from trusted vendors nationwide.</p>
                    </div>
                    <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                        <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full bg-amber-50">
                            <i class="fas fa-truck text-3xl text-amber-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">3. Get It Delivered</h3>
                        <p class="text-gray-600">Choose home, pickup or community delivery — and earn loyalty points on every order.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="products" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Program <span class="text-emerald-500">Levels</span></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Registration is free for everyone — there is no ₦5,000 sign-up and no starter pack to buy. Advance through the optional community levels below to unlock increasing food benefits.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="p-8">
                            <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-seedling text-2xl text-orange-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Bronze Benefits</h3>
                            <p class="text-sm text-gray-500 mb-6">Initial Entry Level</p>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-check text-green-500 mt-1 mr-3"></i><span>Membership Category: Open to All</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i><span>Target Beneficiaries: Women, Low-Income Households, Micro-Business Owners, Students, and the Working Class (Private and Public Sectors).</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-people-carry text-cyan-600 mt-1 mr-3"></i><span>Designed to provide essential food security benefits through our structured membership program.</span></li>
                                <!-- <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-check text-green-500 mt-1 mr-3"></i><span>Target: Women, Low-Income Households, Micro-Business Owners & Students</span></li> -->
                            </ul>
                            <div class="mb-4"><button onclick="toggleBenefits('bronze')" class="text-emerald-600 text-sm font-semibold hover:text-emerald-700 transition">+ Additional Benefits</button></div>
                            <div id="bronze-additional" class="hidden space-y-3 mb-6 pl-2 border-l-2 border-emerald-200">
                                <!-- <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-check text-green-500 mt-1 mr-3"></i><span>Build Community of 10-40 Members <b>(Get Mini Free Food Pack Quarterly)</b></span></li> -->
                                <!-- <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i><span>Pastries & Confectionaries Loan: Union of 10, 30% contribution, bi-weekly repayment</span></li> -->
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-truck-loading text-green-500 mt-1 mr-3"></i><span>Pickup & Delivery: Available (fees paid by member)</span></li>
                            </div>
                            <a href="{{ route('register') }}" class="block w-full py-3 border-2 border-emerald-600 text-emerald-600 text-center font-bold rounded-lg hover:bg-emerald-600 hover:text-white transition-all">Join Now</a>
                        </div>
                    </div>
                    <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
                        <div class="p-8">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-medal text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Silver Benefits</h3>
                            <p class="text-sm text-gray-500 mb-6">Standard Level</p>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-utensils text-cyan-600 mt-1 mr-3"></i><span>Membership Category: Open to All</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i><span>Target Beneficiaries: Women, Low-Income Households, Micro-Business Owners, Students, and the Working Class (Private and Public Sectors).</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-check text-green-500 mt-1 mr-3"></i><span>Provides enhanced food security benefits with greater access to support and opportunities as members progress within the program.</span></li>
                                <!-- <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-infinity text-green-500 mt-1 mr-3"></i><span>Target: All Categories</span></li> -->
                            </ul>
                            <div class="mb-4"><button onclick="toggleBenefits('silver')" class="text-emerald-600 text-sm font-semibold hover:text-emerald-700 transition">+ Additional Benefits</button></div>
                            <div id="silver-additional" class="hidden space-y-3 mb-6 pl-2 border-l-2 border-emerald-200">
                                <!-- <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-users text-cyan-600 mt-1 mr-3"></i><span>Build Community of 50-90 Members <b>(Get Average Free Food Pack Quarterly)</b></span></li> -->
                                <!-- <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i><span>Pastries & Confectionaries Loan: Union of 10, 30% contribution</span></li> -->
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-truck-loading text-green-500 mt-1 mr-3"></i><span>Pickup & Delivery: Fees Paid by Member</span></li>
                            </div>
                            <a href="{{ route('register') }}" class="block w-full py-3 border-2 border-emerald-600 text-emerald-600 text-center font-bold rounded-lg hover:bg-emerald-600 hover:text-white transition-all">Join Now</a>
                        </div>
                    </div>
                    <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 relative">
                        <div class="absolute top-0 right-0 bg-amber-400 text-white px-4 py-1 rounded-bl-lg text-xs font-bold uppercase">Popular</div>
                        <div class="p-8">
                            <div class="w-16 h-16 bg-yellow-50 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-crown text-2xl text-yellow-500"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Gold Benefits</h3>
                            <p class="text-sm text-gray-500 mb-6">Advanced Impact</p>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-shopping-basket text-yellow-600 mt-1 mr-3"></i><span>Membership Category: Open to All</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i><span>Target Beneficiaries: Women, Low-Income Households, Micro-Business Owners, Students, and the Working Class (Private and Public Sectors).</span></li>
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-check text-green-500 mt-1 mr-3"></i><span>Delivers premium food security benefits, offering increased support and exclusive opportunities for committed members.</span></li>
                                <!-- <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-people-carry text-yellow-600 mt-1 mr-3"></i><span>Community Building Category: All</span></li> -->
                            </ul>
                            <div class="mb-4"><button onclick="toggleBenefits('gold')" class="text-emerald-600 text-sm font-semibold hover:text-emerald-700 transition">+ Additional Benefits</button></div>
                            <div id="gold-additional" class="hidden space-y-3 mb-6 pl-2 border-l-2 border-emerald-200">
                                <!-- <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-users text-yellow-600 mt-1 mr-3"></i><span>Build Community of 100-499 Members <b>(Get Standard Free Food Pack Quarterly)</b></span></li> -->
                                <!-- <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i><span>Pastries & Confectionaries Loan: Union of 10, 30% contribution</span></li> -->
                                <li class="flex items-start text-gray-600 text-sm"><i class="fas fa-truck-loading text-green-500 mt-1 mr-3"></i><span>Pickup & Delivery: Free</span></li>
                            </div>
                            <a href="{{ route('register') }}" class="block w-full py-3 border-2 border-emerald-600 text-emerald-600 text-center font-bold rounded-lg hover:bg-emerald-600 hover:text-white transition-all">Join Now</a>
                        </div>
                    </div>
                    <div class="product-card bg-emerald-900 rounded-2xl shadow-xl overflow-hidden text-white">
                        <div class="p-8">
                            <div class="w-16 h-16 bg-emerald-700 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-gem text-2xl text-emerald-300"></i>
                            </div>
                            <h3 class="text-2xl font-bold mb-2">Emerald Benefits</h3>
                            <p class="text-emerald-300 text-sm mb-6">Prestige Level</p>
                            <ul class="space-y-3 mb-6">
                                <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-infinity text-emerald-400 mt-1 mr-3"></i><span>FREE Annual Subscription</span></li>
                                <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-truck-loading text-emerald-400 mt-1 mr-3"></i><span>Membership Category: Open to All</span></li>
                                <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-users-cog text-emerald-400 mt-1 mr-3"></i><span>Target Beneficiaries: Women, Low-Income Households, Micro-Business Owners, Students, and the Working Class (Private and Public Sectors).</span></li>
                                <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-users-cog text-emerald-400 mt-1 mr-3"></i><span>Our highest membership tier, providing comprehensive food security benefits, priority access to program opportunities, and exclusive member privileges.</span></li>
                                <!-- <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-infinity text-emerald-400 mt-1 mr-3"></i><span>Target: All Categories</span></li> -->
                            </ul>
                            <div class="mb-4"><button onclick="toggleBenefits('emerald')" class="text-emerald-300 text-sm font-semibold hover:text-emerald-200 transition">+ Additional Benefits</button></div>
                            <div id="emerald-additional" class="hidden space-y-3 mb-6 pl-2 border-l-2 border-emerald-500">
                                <!-- <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-users-cog text-emerald-400 mt-1 mr-3"></i><span>Build Community of 500-1000 Members <b>(Get Premium Free Food Pack Quarterly)</b></span></li> -->
                                <!-- <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-users-cog text-emerald-400 mt-1 mr-3"></i><span>Pastries & Confectionaries Loan: Union of 10, 30% contribution</span></li> -->
                                <li class="flex items-start text-emerald-50 text-sm"><i class="fas fa-truck-loading text-emerald-400 mt-1 mr-3"></i><span>Pickup & Delivery: Free</span></li>
                            </div>
                            <div class="py-3 text-center bg-emerald-800 rounded-lg font-bold">Ultimate Milestone</div>
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
                        <p class="text-gray-600 mb-6 leading-relaxed">
                            <strong>FoodChain Community Empowerment Initiative</strong> is a technology-driven food distribution network with social impact, designed to combat hunger and food insecurity among women, workforce in both public and private sectors, self-employed and low-income earners in Nigeria.
                        </p>
                        <p class="text-gray-600 mb-8 leading-relaxed">
                            It is an initiative from Foodchain Enterprises Limited – a member of Doorstep Farms Limited <b>incorporated on 12th, February 2015, RC: 1241873, </b> to address the social challenges of hunger and starvation caused by economic crisis, inflation, unemployment, insecurity, poor standard of living, social conflicts, social instability, policy changes and social unrest.
                        </p>
                        <button id="openModalBtn" class="inline-block bg-emerald-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-emerald-700 transition duration-300 shadow-lg shadow-emerald-600/30">
                            Read More
                        </button>
                        <div class="grid grid-cols-2 gap-6 mt-8">
                            <div><div class="text-3xl font-bold mb-2 text-emerald-500">Zero</div><div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Hunger Mission</div></div>
                            <div><div class="text-3xl font-bold mb-2 text-cyan-600">Free</div><div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Staple Food Items</div></div>
                        </div>
                    </div>
                    <div class="relative">
                        <img src="{{ asset('assets/landing/images/about.jpg') }}" alt="FCCEI Food Staples" class="rounded-2xl shadow-xl w-full h-[400px] object-cover" loading="lazy" />
                        <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-2xl shadow-lg w-72 border-t-4 border-emerald-500">
                            <div class="text-3xl font-bold mb-2 text-emerald-600">SDG 2</div>
                            <div class="text-gray-600 text-sm italic">End hunger, achieve food security and improved nutrition.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div id="aboutModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white max-w-6xl w-full p-6 rounded-lg shadow-lg relative flex flex-col lg:flex-row max-h-full overflow-y-auto">
                <button id="closeModalBtn" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800 text-3xl z-50">&times;</button>
                <div class="flex-1 p-4 overflow-y-auto">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6 font-display">{{ App\Models\PageContent::getContent('landing', 'about_title', 'About FCCEI') }}</h2>
                    <p class="mb-4 leading-relaxed"><strong>FoodChain Community Empowerment Initiative</strong> is a technology-driven food distribution network with social impact, designed to combat hunger and food insecurity among women, workforce in both public and private sectors, self-employed and low-income earners in Nigeria. </p>
                    <p class="mb-4 leading-relaxed">It is an initiative from Foodchain Enterprises Limited – a member of Doorstep Farms Limited <b>incorporated on 12th, February 2015, RC: 1241873, </b> to address the social challenges of hunger and starvation caused by economic crisis, inflation, unemployment, insecurity, poor standard of living, social conflicts, social instability, policy changes and social unrest.</p>
                    <p class="mb-4 leading-relaxed">Our core values hover around securing food for members of the community and make them affordable and accessible for members of the community. Our records show that greater number of the members of the community are satisfied with the exceptional and delightful experience. The food distribution network makes use of counter funding to purchase food items through our local suppliers and members access their staple food when due, at a very affordable rate, removing the bottle neck of food inflation caused by the market distribution and retail forces. This is to help the community members purchase staple food at more affordable prices and save more money for their families and other needs. </p>
                    <p class="mb-4 leading-relaxed">We offer logistics to deliver food items to members at any place in Nigeria and we also operate micro-outlets as collection centres for pick-up within Lagos. The company pursue both financial stability and measurable social impact, which means that our commercial operations are been conducted in a manner that actively reduce food inflation, economic crisis, improves market access for members and enhances food security in Nigeria.</p>
                    <p class="mb-4 leading-relaxed">The company’s objective is to establish a high-efficient retail distribution network, securing market share in high-density, low-income urban areas and making food affordable for the urban poor. We deploy micro-warehouses and informal retail partnerships to eliminate middleman inflation and lower retail food prices.</p>
                    <p class="mb-4 leading-relaxed">Our unique value proposition is to establish a community of common-wealth among women, corporate employees, self-employed and low-income households in rural, peri-urban, and urban underserved areas in Nigeria where each member of the community has access to Food support based on their needs.</p>
                    <p class="mb-4 leading-relaxed">Our benefits package is designed to encourage members to build resilience and create social impact which ensures continuous access to food support and assistance.</p>
                </div>
                <div class="flex-1 p-4 flex items-center justify-center">
                    <div class="w-full h-full relative">
                        <img src="{{ asset('assets/landing/images/about.jpg') }}" alt="Community Food" class="absolute inset-0 w-full h-full object-cover rounded-lg shadow-md" />
                        <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-2xl shadow-lg w-72 border-t-4 border-emerald-500">
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
                        <p class="text-gray-700 leading-relaxed">To create a community-driven, sustainable food support system that provides continuous access to essential food resources for families.</p>
                    </div>
                    <div class="p-8 rounded-2xl bg-white shadow-md border-l-8 border-cyan-600">
                        <h3 class="text-2xl font-bold mb-4 text-cyan-600">Mission Statement</h3>
                        <p class="text-gray-700 leading-relaxed">To build a common-wealth community where members contribute according to their abilities and receive according to their needs, fostering food security shared growth and dignity in humanity.</p>
                    </div>
                </div>
                <div class="text-center mb-12">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4">Core Values: <span class="text-amber-500">SECURE FOOD</span></h2>
                    <p class="text-gray-600">Our values are embedded in the concept of SECURE FOOD, representing our commitment to hunger eradication.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-20">
                    @php
                    $values = [
                        ['letter' => 'S', 'title' => 'Sustainability', 'color' => 'emerald', 'desc' => 'Consistent and reliable support that strengthens families facing food insecurity.'],
                        ['letter' => 'E', 'title' => 'Empowerment', 'color' => 'cyan', 'desc' => 'Empowering individuals to take control of their food stability through collaboration.'],
                        ['letter' => 'C', 'title' => 'Credibility', 'color' => 'amber', 'desc' => 'Transparency, integrity, and trustworthiness in our operations and member relations.'],
                        ['letter' => 'U', 'title' => 'Unlimited Access', 'color' => 'emerald', 'desc' => 'Continuous access to essential food items for all members regardless of status.'],
                        ['letter' => 'R', 'title' => 'Respect for All', 'color' => 'cyan', 'desc' => 'Treating every member with dignity and valuing their unique circumstances.'],
                        ['letter' => 'E', 'title' => 'Equity', 'color' => 'amber', 'desc' => 'Promoting fairness to ensure food support reaches those who need it most.'],
                        ['letter' => 'F', 'title' => 'Food Security', 'color' => 'amber', 'desc' => 'Ensuring access to sufficient, safe and nutritious food for good health.'],
                        ['letter' => 'O', 'title' => 'Opportunity', 'color' => 'amber', 'desc' => 'Creating avenues for economic and social upliftment through community participation.'],
                        ['letter' => 'O', 'title' => 'Optimum Nutrition', 'color' => 'amber', 'desc' => 'Distributing quality food items that contribute to healthy living.'],
                        ['letter' => 'D', 'title' => 'Delightful Experience', 'color' => 'amber', 'desc' => 'Delivering a seamless, positive, and supportive experience to every member.'],
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
                    <p class="text-gray-600 italic leading-relaxed">FCCEI is committed to promoting food security through a structured, transparent, and community-driven model. Our policies ensure that all members participate responsibly, uphold program integrity, and benefit equitably from food distribution.</p>
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
                        <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-8">Empowering Communities,<br/><span class="text-gray-400">One Household at a Time.</span></h2>
                    </div>
                    <div class="relative flex-shrink-0">
                        <div class="w-72 h-72 md:w-[380px] md:h-[380px] rounded-full border-[15px] border-white shadow-[0_20px_50px_rgba(0,0,0,0.1)] overflow-hidden">
                            <img src="{{ asset('assets/landing/images/impactwoman.png') }}" alt="Impact Focus" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-24">
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-black text-emerald-700 counter" data-target="15000">0</div>
                        <p class="text-gray-600 font-semibold text-sm">Meals Provided</p>
                    </div>
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-black text-emerald-700 counter" data-target="500">0</div>
                        <p class="text-gray-600 font-semibold text-sm">Women Empowered</p>
                    </div>
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-black text-emerald-700 counter" data-target="25">0</div>
                        <p class="text-gray-600 font-semibold text-sm">Tertiary Institutions</p>
                    </div>
                    <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                        <div class="text-3xl md:text-4xl font-black text-emerald-700 counter" data-target="98">0</div>
                        <p class="text-gray-600 font-semibold text-sm">Loan Repayment Rate</p>
                    </div>
                </div>
                <div class="bg-gray-50/50 rounded-[3.5rem] p-10 md:p-16 flex flex-col lg:flex-row gap-16 items-center border border-gray-100">
                    <div class="lg:w-1/2 space-y-8">
                        <h4 class="text-3xl font-bold text-gray-900 leading-tight">Revolutionizing Food Security</h4>
                        <p class="text-gray-600 text-lg leading-relaxed">At FCCEI, we believe food security is the foundation of economic stability. Through our innovative community-driven approach, we empower individuals and families to achieve long-term financial independence and sustainable food security.</p>
                        <div class="space-y-4">
                            <div class="flex items-center gap-4"><div class="bg-emerald-600 text-white p-1.5 rounded-full shadow-md"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div><span class="text-gray-800 font-semibold">Direct support for female-led households.</span></div>
                            <div class="flex items-center gap-4"><div class="bg-emerald-600 text-white p-1.5 rounded-full shadow-md"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div><span class="text-gray-800 font-semibold">Educational grants for students.</span></div>
                        </div>
                        <a href="#contact" class="inline-block bg-emerald-700 text-white px-10 py-4 rounded-2xl font-bold hover:bg-emerald-800 transition shadow-xl">Partner With Us</a>
                    </div>
                    <div class="lg:w-1/2 relative">
                        <div class="rounded-[3rem] overflow-hidden shadow-2xl border-4 border-white">
                            <img src="{{ asset('assets/landing/images/warehouse.png') }}" alt="FCCEI Operations" class="w-full h-[400px] object-cover">
                        </div>
                        <div class="absolute -bottom-6 -right-4 md:-right-10 bg-white p-8 rounded-3xl shadow-2xl border border-gray-50 max-w-xs animate-bounce-slow">
                            <p class="text-gray-700 font-medium italic mb-4 leading-relaxed">"The support I received changed my business and helped me provide for my family when I had nothing."</p>
                            <p class="text-emerald-700 text-xs font-bold uppercase tracking-widest">— Beneficiary from Lagos</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="blog" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Latest <span class="text-emerald-500">Blog</span> Posts</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Insights, tips, and success stories from our community</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <img src="{{ asset('assets/landing/images/blogsa.jpeg') }}" alt="Community Building" class="w-full h-48 object-cover" loading="lazy" />
                        <div class="p-6">
                            <div class="text-sm text-emerald-600 font-medium mb-2">COMMUNITY BUILDING</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Build a Strong Food-Secure Community in 2026</h3>
                            <p class="text-gray-600 mb-4">Discover practical ways to grow a supportive community where members collaborate and ensure continuous access to essential food resources.</p>
                            <div class="flex items-center justify-between"><span class="text-sm text-gray-500">Jan 5, 2026</span><a href="{{ route('blog') }}" class="text-emerald-600 font-medium hover:text-emerald-700">Read More →</a></div>
                        </div>
                    </article>
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <img src="{{ asset('assets/landing/images/blogpost.jpeg') }}" alt="Success Stories" class="w-full h-48 object-cover" loading="lazy" />
                        <div class="p-6">
                            <div class="text-sm text-cyan-600 font-medium mb-2">SUCCESS STORIES</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">From Struggle to Stability: A Community Transformation Story</h3>
                            <p class="text-gray-600 mb-4">Discover how active participation and shared support within our community helped transform lives, improve livelihoods, and create sustainable food access.</p>
                            <div class="flex items-center justify-between"><span class="text-sm text-gray-500">Jan 5, 2026</span><a href="{{ route('blog') }}" class="text-emerald-600 font-medium hover:text-emerald-700">Read More →</a></div>
                        </div>
                    </article>
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <img src="{{ asset('assets/landing/images/bloggpo.png') }}" alt="Education" class="w-full h-48 object-cover" loading="lazy" />
                        <div class="p-6">
                            <div class="text-sm text-amber-600 font-medium mb-2">EDUCATION</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Understanding Community Empowerment & Sustainable Growth</h3>
                            <p class="text-gray-600 mb-4">Learn how education, collaboration, and shared resources help individuals and communities build lasting impact and food security.</p>
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
                            <img src="{{ asset('assets/landing/images/' . $partner) }}" alt="Partner Logo" class="max-h-20 object-contain mx-auto">
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
                        <button id="closeSponsorModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                        <h4 class="text-2xl font-semibold mb-4 text-emerald-600">Join as a Sponsor</h4>
                        <form action="#submit" method="POST" class="space-y-4">
                            <div><label class="block text-gray-700 mb-2">Name</label><input type="text" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="Your Name"></div>
                            <div><label class="block text-gray-700 mb-2">Organization</label><input type="text" name="organization" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="Your Organization"></div>
                            <div><label class="block text-gray-700 mb-2">Email</label><input type="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="you@example.com"></div>
                            <div><label class="block text-gray-700 mb-2">Message</label><textarea name="message" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400" placeholder="Tell us about your sponsorship interest"></textarea></div>
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
                    <p class="text-gray-600 max-w-2xl mx-auto">Everything you need to know about creating your free account, shopping, and the optional community program.</p>
                </div>
                <div class="max-w-3xl mx-auto space-y-4">
                    @php
                    $faqs = [
                        ['q' => 'Is Foodchain Community Empowerment Initiative a ponzi scheme?', 'a' => 'No it is not a ponzi scheme. Foodchain Community Empowerment is an initiative from a reputable firm known as <b>Doorstep Farms Limited</b> designed to support low-income earners, micro-business owners and students of tertiary institutions.'],
                        ['q' => 'How does it work if I want to join the Community?', 'a' => 'Create a free FoodChain account — registration takes under a minute and costs nothing. Once your account is active you can shop immediately. Joining the optional community food program unlocks additional assistance and support.'],
                        ['q' => 'Will I start getting food items regularly once I register?', 'a' => 'Yes, once you become a member and build your community, you will get food regularly.'],
                        ['q' => 'How many people do I require to be eligible for regular food?', 'a' => '<strong>BRONZE:</strong> Create a free account and start shopping instantly.<br><strong>SILVER:</strong> Build a community of 10 members.<br><strong>GOLD:</strong> Build a community of 100 members.<br><strong>EMERALD:</strong> Build a community of 1000 members.'],
                        ['q' => 'If you build a community less than 10 members?', 'a' => 'If you build less than 10 members, you are entitled to food items within the discretion of your ADMIN. If you build no community at all, you are not entitled to free food items.'],
                        ['q' => 'Is registration really free? Will I ever be asked to pay?', 'a' => 'Registration is completely free. If you later choose an optional food-assistance subscription, it is renewable annually.'],
                        ['q' => 'How do I get my food if I live far from your office?', 'a' => 'Food items will be delivered to your doorstep. The cost of delivery is settled by the member.'],
                        ['q' => 'If I am not living in Nigeria, how do I benefit?', 'a' => 'You can become a member from any part of the world via our online link, but entitlements must be delivered to a verifiable address in Nigeria.'],
                        ['q' => 'What happens if I don\'t renew my subscription?', 'a' => 'Any year you don\'t renew, you lose community benefits. However, at the <strong>EMERALD</strong> stage, you become a community leader and are exempted from annual subscriptions.'],
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
                    <p class="text-gray-600 max-w-2xl mx-auto">Have questions? We're here to help you get started</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-8">Contact Information</h3>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center mr-4"><i class="fas fa-map-marker-alt text-emerald-600"></i></div>
                                <div><h4 class="font-bold text-gray-900 mb-1">Office Address / Collection Centre:</h4><p class="text-gray-600">No. 9B, Adejumo Tennis Club Road, opposite DE-RESTED PHARMACY,<br>Ilamoshe Estate, Oke Afa, Ejigbo, Lagos.</p></div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center mr-4"><i class="fas fa-map-marker-alt text-emerald-600"></i></div>
                                <div><h4 class="font-bold text-gray-900 mb-1">Food Store/ Warehouse:</h4><p class="text-gray-600">KM 54, Lagos - Ibadan Expressway, Mowe, Ogun State.</p></div>
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
                                <a href="https://web.facebook.com/profile.php?id=61585986685899" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all duration-200"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all duration-200"><i class="fab fa-twitter"></i></a>
                                <a href="https://www.instagram.com/foodchainempowerment/" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all duration-200"><i class="fab fa-instagram"></i></a>
                                <a href="https://www.tiktok.com/@foodchainempowerm?lang=en" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all duration-200"><i class="fab fa-tiktok"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h3>
                        <form id="contact-form" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label><input type="text" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label><input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required></div>
                            </div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-2">Subject</label><input type="text" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required></div>
                            <div><label class="block text-sm font-medium text-gray-700 mb-2">Message</label><textarea name="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" required></textarea></div>
                            <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition-all duration-200 shadow-lg">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-16 bg-gradient-to-r from-emerald-600 to-emerald-800">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-white mb-4 font-display">Stay Updated</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">Subscribe to our newsletter for the latest tips, success stories, and platform updates</p>
                <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
                    <input type="email" placeholder="Your email address" class="flex-1 px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white">
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
            heroSlides.forEach(slide => { slide.style.opacity = '0'; slide.style.zIndex = '1'; });
            dots.forEach(dot => dot.classList.remove('active'));
            heroSlides[index].style.opacity = '1';
            heroSlides[index].style.zIndex = '10';
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

        document.getElementById('openModalBtn').addEventListener('click', () => {
            document.getElementById('aboutModal').classList.remove('hidden');
            document.getElementById('aboutModal').classList.add('flex');
        });
        document.getElementById('closeModalBtn').addEventListener('click', () => {
            document.getElementById('aboutModal').classList.add('hidden');
            document.getElementById('aboutModal').classList.remove('flex');
        });
        document.getElementById('aboutModal').addEventListener('click', function(e) {
            if (e.target === this) { this.classList.add('hidden'); this.classList.remove('flex'); }
        });

        document.getElementById('openSponsorModal').addEventListener('click', () => {
            document.getElementById('sponsorModal').classList.remove('hidden');
            document.getElementById('sponsorModal').classList.add('flex');
        });
        document.getElementById('closeSponsorModal').addEventListener('click', () => {
            document.getElementById('sponsorModal').classList.add('hidden');
            document.getElementById('sponsorModal').classList.remove('flex');
        });
        window.addEventListener('click', (e) => { if (e.target === document.getElementById('sponsorModal')) { document.getElementById('sponsorModal').classList.add('hidden'); document.getElementById('sponsorModal').classList.remove('flex'); } });

        function toggleBenefits(tier) {
            const el = document.getElementById(tier + '-additional');
            el.classList.toggle('hidden');
        }

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

        document.getElementById('contact-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Thank you for your message! We will get back to you soon.');
            this.reset();
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
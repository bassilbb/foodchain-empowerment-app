<!DOCTYPE html>
<html lang="en">
<head>
    <base target="_self">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCCEI  Platform</title>
    <meta name="description" content="Join ReferralPro - The ultimate multi-level referral subscription platform. Earn through referrals, grow your network, and maximize your income potential.">
    <meta name="keywords" content="referral program, MLM, subscription platform, network marketing, passive income">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: "#4F46E5",
                            light: "#6366F1",
                            dark: "#4338CA"
                        },
                        secondary: {
                            DEFAULT: "#10B981",
                            light: "#34D399",
                            dark: "#059669"
                        },
                        accent: {
                            DEFAULT: "#F59E0B",
                            light: "#FBBF24",
                            dark: "#D97706"
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        'display': ['Poppins', 'system-ui', '-apple-system', 'sans-serif']
                    },
                    animation: {
                        'slide-in': 'slideIn 0.5s ease-out',
                        'fade-in': 'fadeIn 0.8s ease-out',
                        'float': 'float 3s ease-in-out infinite'
                    },
                    keyframes: {
                        slideIn: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script> -->

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { DEFAULT: "#4F46E5", light: "#6366F1", dark: "#4338CA" },
                        secondary: { DEFAULT: "#10B981", light: "#34D399", dark: "#059669" },
                        accent: { DEFAULT: "#F59E0B", light: "#FBBF24", dark: "#D97706" }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                        'display': ['Poppins', 'system-ui', 'sans-serif']
                    },
                    backgroundImage: {
                        /* Dark overlays added directly to config for better text contrast ('{{ asset("assets/landing/images/buildi.jpeg") }}') empowerment foodchan*/
                        'slide-1': "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("assets/landing/images/buildi.jpeg") }}')",
                        'slide-2': "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("assets/landing/images/empowerment.jpeg") }}')",
                        'slide-3': "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("assets/landing/images/foodchan.jpeg") }}')"
                    },
                    animation: {
                        'slide-in': 'slideIn 0.5s ease-out forwards',
                        'fade-in': 'fadeIn 0.8s ease-out forwards',
                        'float': 'float 3s ease-in-out infinite'
                    },
                    keyframes: {
                        slideIn: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap');

        .hero-slide {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.9) 0%, rgba(16, 185, 129, 0.9) 100%);
        }

        .stat-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }

        .gradient-text {
            background: linear-gradient(90deg, #4F46E5 0%, #10B981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: #4F46E5;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .team-card {
            transition: all 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-10px);
        }

        .blog-card {
            transition: all 0.3s ease;
        }

        .blog-card:hover {
            transform: translateY(-5px);
        }
    </style>

        <style>
        /* Slow bounce for the floating quote bubble */
        @keyframes bounce-slow {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        .animate-bounce-slow {
            animation: bounce-slow 5s ease-in-out infinite;
        }

        /* Counter appearance animation */
        .counter-visible {
            opacity: 1;
            transform: translateY(0);
            transition: all 0.6s ease-out;
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans">
    <!-- Navigation -->
    @include('partials.header')

    <main>
        <!-- Hero Section with Sliding Images -->
        <!-- <section id="home" class="relative overflow-hidden"> -->
            <!-- Sliding Images Container -->
            <!-- <div id="hero-slider" class="relative h-[600px]"> -->
                <!-- Slide 1 -->
                <!-- <div class="absolute inset-0 hero-slide flex items-center">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl animate-slide-in">
                            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">Earn Through <span class="gradient-text">Referrals</span></h1>
                            <p class="text-xl text-white/90 mb-8">Join our multi-level subscription platform and start earning passive income through your network. No experience needed.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="/register" class="px-8 py-3 bg-white text-primary font-bold rounded-lg hover:bg-gray-100 transition-all duration-200">Start Earning Now</a>
                                <a href="#how-it-works" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition-all duration-200">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Slide 2 -->
                <!-- <div class="absolute inset-0 bg-gradient-to-r from-accent to-secondary flex items-center opacity-0">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl">
                            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">Build Your <span class="text-gray-900">Network</span></h1>
                            <p class="text-xl text-white/90 mb-8">Grow your team and maximize your earning potential with our advanced referral tracking system.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="/register" class="px-8 py-3 bg-gray-900 text-white font-bold rounded-lg hover:bg-gray-800 transition-all duration-200">Build Network</a>
                                <a href="#features" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition-all duration-200">View Features</a>
                            </div>
                        </div>
                    </div>
                </div>
                 -->
                <!-- Slide 3 -->
                <!-- <div class="absolute inset-0 bg-gradient-to-r from-primary-dark to-accent flex items-center opacity-0">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl">
                            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">Passive <span class="text-secondary">Income</span> Stream</h1>
                            <p class="text-xl text-white/90 mb-8">Create multiple income streams with our tiered subscription model and referral bonuses.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="/register" class="px-8 py-3 bg-secondary text-white font-bold rounded-lg hover:bg-secondary-dark transition-all duration-200">Start Today</a>
                                <a href="#pricing" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition-all duration-200">View Plans</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->

            <!-- Slider Controls -->
            <!-- <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3">
                <button class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200 active" data-slide="0"></button>
                <button class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" data-slide="1"></button>
                <button class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" data-slide="2"></button>
            </div> -->
        <!-- </section> -->

        <section id="home" class="relative overflow-hidden h-[600px] bg-black">
            <div id="hero-slider" class="relative h-full">

                <div class="hero-slide absolute inset-0 bg-slide-1 bg-cover bg-center flex items-center opacity-100 z-10">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl animate-slide-in">
                            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">Zero Hunger <span class="text-secondary">Through Community</span></h1>
                            <p class="text-xl text-white/90 mb-8">Join the food security network that empowers households with quality staple foods and growing the community .</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('register') }}" class="px-8 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition">Join the Initiative</a>
                                <a href="#about" class="px-8 py-3 border-2 border-white text-white font-bold rounded-lg hover:bg-white/10 transition">Learn More</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hero-slide absolute inset-0 bg-slide-2 bg-cover bg-center flex items-center opacity-0">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl">
                            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">Build Your <span class="text-accent">Network</span></h1>
                            <p class="text-xl text-white/90 mb-8">Grow your community and maximize your food benefits through our advanced food security system.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('register') }}" class="px-8 py-3 bg-accent text-white font-bold rounded-lg hover:bg-accent-dark transition">Start Building</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="hero-slide absolute inset-0 bg-slide-3 bg-cover bg-center flex items-center opacity-0">
                    <div class="container mx-auto px-4">
                        <div class="max-w-2xl">
                            <h1 class="text-5xl md:text-6xl font-bold text-white mb-6 font-display">Sustainable <span class="text-secondary">Empowerment</span></h1>
                            <p class="text-xl text-white/90 mb-8">From Bronze to Emerald, secure your household's future by building your community Sustainabllity.</p>
                            <div class="flex flex-wrap gap-4">
                                <a href="{{ route('register') }}" class="px-8 py-3 bg-secondary text-white font-bold rounded-lg hover:bg-secondary-dark transition">View Stages</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex space-x-3 z-20">
                <button onclick="setSlide(0)" class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" id="dot-0"></button>
                <button onclick="setSlide(1)" class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" id="dot-1"></button>
                <button onclick="setSlide(2)" class="slider-dot w-3 h-3 rounded-full bg-white/50 hover:bg-white transition-all duration-200" id="dot-2"></button>
            </div>
        </section>


        <!-- Stats Section -->
        <section class="py-5 bg-gray-900 text-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div class="text-center">
                        <div class="text-5xl font-bold mb-2 gradient-text">50K+</div>
                        <div class="text-gray-300">Active Members</div>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold mb-2 gradient-text">₦10M+</div>
                        <div class="text-gray-300">Food  Sustainable</div>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold mb-2 gradient-text">20+</div>
                        <div class="text-gray-300">States</div>
                    </div>
                    <div class="text-center">
                        <div class="text-5xl font-bold mb-2 gradient-text">24/7</div>
                        <div class="text-gray-300">Support</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- How It Works
        <section id="how-it-works" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">How It <span class="gradient-text">Works</span></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Our multi-level referral system is designed to help you maximize your earning potential</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                        <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-user-plus text-3xl text-primary"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">1. Sign Up</h3>
                        <p class="text-gray-600">Create your account and choose your subscription plan</p>
                    </div>

                    <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                        <div class="w-20 h-20 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-share-alt text-3xl text-secondary"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">2. Refer Friends</h3>
                        <p class="text-gray-600">Share your unique referral link with friends and family</p>
                    </div>

                    <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                        <div class="w-20 h-20 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <i class="fas fa-chart-line text-3xl text-accent"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4">3. Earn Commissions</h3>
                        <p class="text-gray-600">Earn from direct referrals and their network growth</p>
                    </div>
                </div>
            </div>
        </section> -->

        <section id="how-it-works" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">
                    How It <span style="color: #26C1C9;">Works</span>
                </h2>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Join our community-driven initiative designed to make essential food items accessible and affordable for everyone.
                </p>
            </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full" style="background-color: rgba(38, 193, 201, 0.1);">
                    <i class="fas fa-user-plus text-3xl" style="color: #26C1C9;"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">1. Register</h3>
                <p class="text-gray-600">Join the FCCEI community by creating your secure account on our platform. and (subcribe annually)</p>
            </div>

            <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full" style="background-color: rgba(29, 95, 181, 0.1);">
                    <i class="fas fa-seedling text-3xl" style="color: #1D5FB5;"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">2. Purchase a Stater-Pack</h3>
                <p class="text-gray-600">Get Instant Food package.</p>
            </div>

            <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full" style="background-color: rgba(255, 127, 39, 0.1);">
                    <i class="fas fa-users text-3xl" style="color: #FF7F27;"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">3. Build Your Community </h3>
                <p class="text-gray-600">Invite neighbors and friends to join the initiative using your unique link.</p>
            </div>


            <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full" style="background-color: rgba(29, 95, 181, 0.1);">
                    <i class="fas fa-shopping-basket text-3xl" style="color: #1D5FB5;"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">4. Empower Members</h3>
                <p class="text-gray-600">Get your monthly supply of rice, beans, oil, and more delivered to your point.</p>
            </div>


            <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full" style="background-color: rgba(255, 127, 39, 0.1);">
                    <i class="fas fa-chart-line text-3xl" style="color: #FF7F27;"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">5. Scale Up</h3>
                <p class="text-gray-600">Watch your impact grow as your community network becomes more sustainable.</p>
            </div>

             <div class="text-center p-8 rounded-2xl bg-gray-50 hover:shadow-xl transition-all duration-300">
                <div class="w-20 h-20 flex items-center justify-center mx-auto mb-6 rounded-full" style="background-color: rgba(38, 193, 201, 0.1);">
                    <i class="fas fa-hand-holding-heart text-3xl" style="color: #26C1C9;"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">6. Become a Community Leader</h3>
                <p class="text-gray-600">Free annual Subscription, Free quarterly food  benefits .</p>
            </div>

        </div>
    </div>
</section>

        <!-- Products Section -->
        <!-- <section id="products" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Our <span class="gradient-text">Products</span></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Premium subscription plans designed for maximum earning potential</p>
                </div> -->

                <!-- <div class="grid grid-cols-1 md:grid-cols-3 gap-8"> -->
                    <!-- Product 1 -->
                    <!-- <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-8">
                            <div class="w-16 h-16 bg-primary/10 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-star text-2xl text-primary"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Starter Plan</h3>
                            <div class="text-4xl font-bold text-gray-900 mb-6">$29<span class="text-lg text-gray-500">/month</span></div>
                            <ul class="space-y-3 mb-8">
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>Up to 5 direct referrals</span>
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>2-level commission structure</span>
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>Basic analytics dashboard</span>
                                </li>
                            </ul>
                            <a href="/register?plan=starter" class="block w-full py-3 bg-primary text-white text-center font-bold rounded-lg hover:bg-primary-dark transition-all duration-200">Get Started</a>
                        </div>
                    </div> -->

                    <!-- Product 2 -->
                    <!-- <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden relative">
                        <div class="absolute top-0 right-0 bg-accent text-white px-4 py-1 rounded-bl-lg">
                            Most Popular
                        </div>
                        <div class="p-8">
                            <div class="w-16 h-16 bg-secondary/10 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-crown text-2xl text-secondary"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Pro Plan</h3>
                            <div class="text-4xl font-bold text-gray-900 mb-6">$79<span class="text-lg text-gray-500">/month</span></div>
                            <ul class="space-y-3 mb-8">
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>Unlimited direct referrals</span>
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>5-level commission structure</span>
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>Advanced analytics & reports</span>
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>Priority support</span>
                                </li>
                            </ul>
                            <a href="/register?plan=pro" class="block w-full py-3 bg-secondary text-white text-center font-bold rounded-lg hover:bg-secondary-dark transition-all duration-200">Get Started</a>
                        </div>
                    </div> -->

                    <!-- Product 3 -->
                    <!-- <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="p-8">
                            <div class="w-16 h-16 bg-accent/10 rounded-lg flex items-center justify-center mb-6">
                                <i class="fas fa-rocket text-2xl text-accent"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Enterprise Plan</h3>
                            <div class="text-4xl font-bold text-gray-900 mb-6">$199<span class="text-lg text-gray-500">/month</span></div>
                            <ul class="space-y-3 mb-8">
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>Unlimited network levels</span>
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>Custom commission rates</span>
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>White-label solution</span>
                                </li>
                                <li class="flex items-center text-gray-600">
                                    <i class="fas fa-check text-secondary mr-3"></i>
                                    <span>Dedicated account manager</span>
                                </li>
                            </ul>
                            <a href="/contact?plan=enterprise" class="block w-full py-3 bg-accent text-white text-center font-bold rounded-lg hover:bg-accent-dark transition-all duration-200">Contact Sales</a>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->

        <!-- <section id="products" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Membership <span style="color: #26C1C9;">Stages</span></h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Build your community and unlock higher levels of food security benefits.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 transition-transform hover:-translate-y-2">
                    <div class="p-8">
                        <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-seedling text-2xl text-orange-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Bronze Stage</h3>
                        <p class="text-sm text-gray-500 mb-6">Initial Entry</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start text-gray-600 text-sm">
                                <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                <span>N25,000 + 1 year Annual Subscription</span>
                            </li>
                            <li class="flex items-start text-gray-600 text-sm">
                                <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                <span>Get Instant Food Starter Pack</span>
                            </li>
                            <li class="flex items-start text-gray-600 text-sm">
                                <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                <span>Member Welcome Kit</span>
                            </li>
                        </ul>
                        <a href="#register" class="block w-full py-3 border-2 border-orange-600 text-orange-600 text-center font-bold rounded-lg hover:bg-orange-600 hover:text-white transition-all">Join Now</a>
                    </div>
                </div>

                <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 transition-transform hover:-translate-y-2">
                    <div class="p-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-medal text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Silver Stage</h3>
                        <p class="text-sm text-gray-500 mb-6">Community Builder</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start text-gray-600 text-sm">
                                <i class="fas fa-users text-blue-500 mt-1 mr-3"></i>
                                <span>10 Person Community</span>
                            </li>
                            <li class="flex items-start text-gray-600 text-sm">
                                <i class="fas fa-utensils text-blue-500 mt-1 mr-3"></i>
                                <span>N30,000 Worth of Food</span>
                            </li>
                            <li class="flex items-start text-gray-600 text-sm">
                                <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                <span>Regular Food Eligibility</span>
                            </li>
                        </ul>
                        <div class="py-3 text-center text-gray-400 font-medium italic">Unlocked via Growth</div>
                    </div>
                </div>

                <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 relative transition-transform hover:-translate-y-2">
                    <div class="absolute top-0 right-0 bg-yellow-400 text-white px-4 py-1 rounded-bl-lg text-xs font-bold uppercase">Popular</div>
                    <div class="p-8">
                        <div class="w-16 h-16 bg-yellow-50 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-crown text-2xl text-yellow-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Gold Stage</h3>
                        <p class="text-sm text-gray-500 mb-6">Advanced Impact</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start text-gray-600 text-sm">
                                <i class="fas fa-users text-yellow-600 mt-1 mr-3"></i>
                                <span>100 Person Community</span>
                            </li>
                            <li class="flex items-start text-gray-600 text-sm">
                                <i class="fas fa-shopping-basket text-yellow-600 mt-1 mr-3"></i>
                                <span>N100,000 Worth of Food</span>
                            </li>
                            <li class="flex items-start text-gray-600 text-sm">
                                <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                <span>Priority Pickup Support</span>
                            </li>
                        </ul>
                        <div class="py-3 text-center text-gray-400 font-medium italic">Unlocked via Growth</div>
                    </div>
                </div>

                <div class="product-card bg-green-900 rounded-2xl shadow-xl overflow-hidden text-white transition-transform hover:-translate-y-2">
                    <div class="p-8">
                        <div class="w-16 h-16 bg-green-700 rounded-lg flex items-center justify-center mb-6">
                            <i class="fas fa-gem text-2xl text-green-300"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">Emerald Stage</h3>
                        <p class="text-green-300 text-sm mb-6">Community Leader</p>
                        <ul class="space-y-3 mb-8">
                            <li class="flex items-start text-green-50 text-sm">
                                <i class="fas fa-users-cog text-green-400 mt-1 mr-3"></i>
                                <span>1,000 Person Community</span>
                            </li>
                            <li class="flex items-start text-green-50 text-sm">
                                <i class="fas fa-truck-loading text-green-400 mt-1 mr-3"></i>
                                <span>N200,000 Food Every Quarter</span>
                            </li>
                            <li class="flex items-start text-green-50 text-sm">
                                <i class="fas fa-infinity text-green-400 mt-1 mr-3"></i>
                                <span>FREE Annual Subscription</span>
                            </li>
                        </ul>
                        <div class="py-3 text-center bg-green-800 rounded-lg font-bold">Ultimate Milestone</div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->



<!-- <section id="products" class="py-20 bg-gray-50">
  <div class="container mx-auto px-4">
    <div class="text-center mb-16">
      <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">
        Membership <span style="color: #26C1C9;">Benefits</span>
      </h2>
      <p class="text-gray-600 max-w-2xl mx-auto">
        Build your community and unlock higher levels of food security benefits with our structured membership grades.
      </p>
    </div> -->

    <!-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6"> -->

      <!-- Bronze Membership -->
      <!-- <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 transition-transform hover:-translate-y-2">
        <div class="p-8">
          <div class="w-16 h-16 bg-orange-100 rounded-lg flex items-center justify-center mb-6">
            <i class="fas fa-seedling text-2xl text-orange-600"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">Bronze Benefits</h3>
          <p class="text-sm text-gray-500 mb-6">Initial Entry Level</p>
          <ul class="space-y-3 mb-8">
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Renewable Annual Subscription: ₦5,000</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Purchase Instant Food Pack: ₦20,000</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
              <span>Food Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Food Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Target Audience: Low-Income Households, Micro-Business Owners, Working Class, Students</span>
            </li>
            <h3><b>Additional Benefits</b></h3>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Build a Community of 10-40 Members <b>(Get Mini Free Food Pack Quarterly)</b></span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
              <span>Pastries & Confectionaries Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Pastries & Confectionaries Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li>
            <!- Moved to bottom -->
            <!-- <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-truck-loading text-green-500 mt-1 mr-3"></i>
              <span>Pickup & Delivery: Available (fees paid by member)</span>
            </li>
          </ul>
          <a href="#register" class="block w-full py-3 border-2 border-orange-600 text-orange-600 text-center font-bold rounded-lg hover:bg-orange-600 hover:text-white transition-all">Join Now</a>
        </div>
      </div> --> -

      <!-- Silver Membership -->
      <!-- <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 transition-transform hover:-translate-y-2">
        <div class="p-8">
          <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-6">
            <i class="fas fa-medal text-2xl text-gray-400"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">Silver Benefits</h3>
          <p class="text-sm text-gray-500 mb-6">Community Builder</p>
          <ul class="space-y-3 mb-8">
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Renewable Annual Subscription: ₦5,000</span>
            </li>

            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-utensils text-blue-500 mt-1 mr-3"></i>
              <span>Purchase Instant Food Pack Worth ₦50,000</span>
            </li>

            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
              <span>Food Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Food Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li>

            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Regular Food Eligibility</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-people-carry text-blue-500 mt-1 mr-3"></i>
              <span>Community Building Category: All</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-infinity text-green-500 mt-1 mr-3"></i>
              <span>Target Audience: All Categories</span>
            </li>
            <h3><b>Additional Benefits</b></h3>
             <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users text-blue-500 mt-1 mr-3"></i>
              <span>Build a Community of 50-90 Members <b>(Get Average Free Food Pack Quarterly)</b></span>
            </li>

            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
              <span>Pastries & Confectionaries Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Pastries & Confectionaries Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li> -->
            <!-- Moved to bottom -->
            <!-- <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-truck-loading text-green-500 mt-1 mr-3"></i>
              <span>Pickup & Delivery: Fees Paid by Member</span>
            </li>
          </ul>
          <div class="py-3 text-center text-gray-400 font-medium italic">Unlocked via Growth</div>
        </div>
      </div> -->

      <!-- Gold Membership -->
      <!-- <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 relative transition-transform hover:-translate-y-2">
        <div class="absolute top-0 right-0 bg-yellow-400 text-white px-4 py-1 rounded-bl-lg text-xs font-bold uppercase">Popular</div>
        <div class="p-8">
          <div class="w-16 h-16 bg-yellow-50 rounded-lg flex items-center justify-center mb-6">
            <i class="fas fa-crown text-2xl text-yellow-500"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">Gold Benefits</h3>
          <p class="text-sm text-gray-500 mb-6">Advanced Impact</p>
          <ul class="space-y-3 mb-8">
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Renewable Annual Subscription: ₦5,000</span>
            </li>

            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-shopping-basket text-yellow-600 mt-1 mr-3"></i>
              <span>Purchase Instant Food Pack Worth ₦100,000</span>
            </li>

           <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
              <span>Food Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Food Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Priority Pickup Support</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-people-carry text-yellow-600 mt-1 mr-3"></i>
              <span>Community Building Category: All</span>
            </li> -->
            <!-- Moved to bottom -->
            <!-- <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-infinity text-green-500 mt-1 mr-3"></i>
              <span>Target Audience: All Categories</span>
            </li>
            <h3><b>Additional Benefits</b></h3>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users text-yellow-600 mt-1 mr-3"></i>
              <span>Build a Community of 100-499 Members <br><b>(Get Standard Free Food Pack Quarterly)</b></span>
            </li>

            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
              <span>Pastries & Confectionaries Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Pastries & Confectionaries Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li>

            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-truck-loading text-green-500 mt-1 mr-3"></i>
              <span>Pickup & Delivery: Fees Charged</span>
            </li> -->

          <!-- </ul>
          <div class="py-3 text-center text-gray-400 font-medium italic">Unlocked via Growth</div>
        </div>
      </div> -->

      <!-- Emerald Membership -->
      <!-- <div class="product-card bg-green-900 rounded-2xl shadow-xl overflow-hidden text-white transition-transform hover:-translate-y-2">
        <div class="p-8">
          <div class="w-16 h-16 bg-green-700 rounded-lg flex items-center justify-center mb-6">
            <i class="fas fa-gem text-2xl text-green-300"></i>
          </div>
          <h3 class="text-2xl font-bold mb-2">Emerald Benefits</h3>
          <p class="text-green-300 text-sm mb-6">Community Leader</p>
          <ul class="space-y-3 mb-8">
           <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-infinity text-green-400 mt-1 mr-3"></i>
              <span>FREE Annual Subscription</span>
            </li>

            <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-truck-loading text-green-400 mt-1 mr-3"></i>
              <span>Purchase Instant Food Pack Worth ₦200,000 </span>
            </li>
              <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-users-cog text-green-400 mt-1 mr-3"></i>
              <span>Food Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Food Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li>
            <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-users-cog text-green-400 mt-1 mr-3"></i>
              <span>Community Building Category: All</span>
            </li>

            <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-infinity text-green-400 mt-1 mr-3"></i>
              <span>Target Audience: All Categories</span>
            </li>

            <h3><b>Additional Benefits</b></h3>
            <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-users-cog text-green-400 mt-1 mr-3"></i>
              <span>Build a Community of 500-1000 Members<br><b>(Get Premium Free Food Pack Quarterly).</b></span>
            </li>

            <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-users-cog text-green-400 mt-1 mr-3"></i>
              <span>Pastries & Confectionaries Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Pastries & Confectionaries Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li> -->
            <!-- Moved to bottom -->
            <!-- <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-truck-loading text-green-400 mt-1 mr-3"></i>
              <span>Pickup & Delivery: Free</span>
            </li>
          </ul>
          <div class="py-3 text-center bg-green-800 rounded-lg font-bold">Ultimate Milestone</div>
        </div>
      </div>

    </div>
  </div>
</section> -->


<section id="products" class="py-20 bg-gray-50">
  <div class="container mx-auto px-4">
    <div class="text-center mb-16">
      <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">
        Membership <span style="color: #26C1C9;">Benefits</span>
      </h2>
      <p class="text-gray-600 max-w-2xl mx-auto">
        Build your community and unlock higher levels of food security benefits with our structured membership grades.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

      <!-- Bronze Membership -->
      <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 transition-transform hover:-translate-y-2">
        <div class="p-8">
          <div class="w-16 h-15 bg-orange-100 rounded-lg flex items-center justify-center mb-6">
            <i class="fas fa-seedling text-2xl text-orange-600"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">Bronze Benefits</h3>
          <p class="text-sm text-gray-500 mb-6">Initial Entry Level</p>
          <ul class="space-y-3 mb-8">
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Renewable Annual Subscription: ₦5,000</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Purchase Instant  Food Pack: <br>₦20,000</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
              <span>Food Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Food Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li>

            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-people-carry text-blue-500 mt-1 mr-3"></i>
              <span>Community Building Category: All</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Target Audience: Women, Low-Income Households, Micro-Business Owners and Students of tertiary institutions</span>
            </li>
            <!-- Additional Benefits toggle -->
            <h3 id="bronze-additional-header" style="cursor:pointer; margin-top:10px;"><b>Click to View Additional Benefits</b></h3>
            <div id="bronze-additional-content" style="display:none;">
              <li class="flex items-start text-gray-600 text-sm pl-4">
                <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                <span>Build a Community of 10-40 Members <b>(Get Mini Free Food Pack Quarterly)</b></span>
              </li>
              <li class="flex items-start text-gray-600 text-sm pl-4">
                <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
                <span>Pastries & Confectionaries Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Pastries & Confectionaries Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
              </li>
              <!-- Moved to bottom -->
              <li class="flex items-start text-gray-600 text-sm pl-4">
                <i class="fas fa-truck-loading text-green-500 mt-1 mr-3"></i>
                <span>Pickup & Delivery: Available (fees paid by member)</span>
              </li>
            </div>
          </ul>
          <a href="{{ route('register') }}" class="block w-full py-3 border-2 border-orange-600 text-orange-600 text-center font-bold rounded-lg hover:bg-orange-600 hover:text-white transition-all">Join Now</a>
        </div>
      </div>

      <!-- Silver Membership -->
      <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 transition-transform hover:-translate-y-2">
        <div class="p-8">
          <div class="w-16 h-15 bg-gray-100 rounded-lg flex items-center justify-center mb-6">
            <i class="fas fa-medal text-2xl text-gray-400"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">Silver Benefits</h3>
          <p class="text-sm text-gray-500 mb-6">Community Builder</p>
          <ul class="space-y-3 mb-8">
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Renewable Annual Subscription: ₦5,000</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-utensils text-blue-500 mt-1 mr-3"></i>
              <span>Purchase Instant Food Pack Worth ₦50,000</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
              <span>Food Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Food Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Regular Food Eligibility</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-people-carry text-blue-500 mt-1 mr-3"></i>
              <span>Community Building Category: All</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-infinity text-green-500 mt-1 mr-3"></i>
              <span>Target Audience: Women, Low-Income Households, Micro-Business Owners and Students of tertiary institutions</span>
            </li>
            <!-- Additional Benefits toggle -->
            <h3 id="silver-additional-header" style="cursor:pointer; margin-top:10px;"><b>Click to View Additional Benefits</b></h3>
            <div id="silver-additional-content" style="display:none;">
              <li class="flex items-start text-gray-600 text-sm pl-4">
                <i class="fas fa-users text-blue-500 mt-1 mr-3"></i>
                <span>Build a Community of 50-90 Members <b>(Get Average Free Food Pack Quarterly)</b></span>
              </li>
              <li class="flex items-start text-gray-600 text-sm pl-4">
                <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
                <span>Pastries & Confectionaries Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Pastries & Confectionaries Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
              </li>
              <!-- Moved to bottom -->
              <li class="flex items-start text-gray-600 text-sm pl-4">
                <i class="fas fa-truck-loading text-green-500 mt-1 mr-3"></i>
                <span>Pickup & Delivery: Fees Paid by Member</span>
              </li>
            </div>
          </ul>
          <!-- <div class="py-3 text-center text-gray-400 font-medium italic">Unlocked via Growth</div> -->
          <a href="{{ route('register') }}" class="block w-full py-3 border-2 border-orange-600 text-orange-600 text-center font-bold rounded-lg hover:bg-orange-600 hover:text-white transition-all">Join Now</a>
        </div>
      </div>

      <!-- Gold Membership -->
      <div class="product-card bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 relative transition-transform hover:-translate-y-2">
        <div class="absolute top-0 right-0 bg-yellow-400 text-white px-4 py-1 rounded-bl-lg text-xs font-bold uppercase">Popular</div>
        <div class="p-8">
          <div class="w-16 h-15 bg-yellow-50 rounded-lg flex items-center justify-center mb-6">
            <i class="fas fa-crown text-2xl text-yellow-500"></i>
          </div>
          <h3 class="text-2xl font-bold text-gray-900 mb-2">Gold Benefits</h3>
          <p class="text-sm text-gray-500 mb-6">Advanced Impact</p>
          <ul class="space-y-3 mb-8">
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Renewable Annual Subscription: ₦5,000</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-shopping-basket text-yellow-600 mt-1 mr-3"></i>
              <span>Purchase Instant Food Pack Worth ₦100,000</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
              <span>Food Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Food Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
              <span>Priority Pickup Support</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-people-carry text-yellow-600 mt-1 mr-3"></i>
              <span>Community Building Category: All</span>
            </li>
            <li class="flex items-start text-gray-600 text-sm">
              <i class="fas fa-people-carry text-yellow-600 mt-1 mr-3"></i>
              <span>Target Audience: Women, Low-Income Households, Micro-Business Owners and Students of tertiary institutions</span>
            </li>

            <!-- Additional Benefits toggle -->
            <h3 id="gold-additional-header" style="cursor:pointer; margin-top:10px;"><b>Click to View Additional Benefits</b></h3>
            <div id="gold-additional-content" style="display:none;">
              <li class="flex items-start text-gray-600 text-sm pl-4">
                <i class="fas fa-users text-yellow-600 mt-1 mr-3"></i>
                <span>Build a Community of 100-499 Members <br><b>(Get Standard Free Food Pack Quarterly)</b></span>
              </li>
              <li class="flex items-start text-gray-600 text-sm pl-4">
                <i class="fas fa-users-cog text-green-500 mt-1 mr-3"></i>
                <span>Pastries & Confectionaries Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Pastries & Confectionaries Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
              </li>
              <!-- Moved to bottom -->
              <li class="flex items-start text-gray-600 text-sm pl-4">
                <i class="fas fa-truck-loading text-green-500 mt-1 mr-3"></i>
                <span>Pickup & Delivery: Free</span>
              </li>
            </div>
          </ul>
          <!-- <div class="py-3 text-center bg-green-800 rounded-lg font-bold">Gold Milestone</div> -->
          <a href="{{ route('register') }}" class="block w-full py-3 border-2 border-orange-600 text-orange-600 text-center font-bold rounded-lg hover:bg-orange-600 hover:text-white transition-all">Join Now</a>

        </div>
      </div>

      <!-- Emerald Membership -->
      <div class="product-card bg-green-900 rounded-2xl shadow-xl overflow-hidden text-white transition-transform hover:-translate-y-2">
        <div class="p-8">
          <div class="w-16 h-15 bg-green-700 rounded-lg flex items-center justify-center mb-6">
            <i class="fas fa-gem text-2xl text-green-300"></i>
          </div>
          <h3 class="text-2xl font-bold mb-2">Emerald Benefits</h3>
          <p class="text-green-300 text-sm mb-6">Community Leader</p>
          <ul class="space-y-3 mb-8">
           <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-infinity text-green-400 mt-1 mr-3"></i>
              <span>FREE Annual Subscription</span>
            </li>
            <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-truck-loading text-green-400 mt-1 mr-3"></i>
              <span>Purchase Instant Food Pack Worth ₦200,000 </span>
            </li>
            <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-users-cog text-green-400 mt-1 mr-3"></i>
              <span>Food Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Food Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
            </li>
            <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-users-cog text-green-400 mt-1 mr-3"></i>
              <span>Community Building Category: All</span>
            </li>
            <li class="flex items-start text-green-50 text-sm">
              <i class="fas fa-infinity text-green-400 mt-1 mr-3"></i>
              <span>Target Audience: Women, Low-Income Households, Micro-Business Owners and Students of tertiary institutions</span>
            </li>
            <!-- Additional Benefits toggle -->
            <h3 id="emerald-additional-header" style="cursor:pointer; margin-top:10px;"><b>Click to View Additional Benefits</b></h3>
            <div id="emerald-additional-content" style="display:none;">
              <li class="flex items-start text-green-50 text-sm">
                <i class="fas fa-users-cog text-green-400 mt-1 mr-3"></i>
                <span>Build a Community of 500-1000 Members<br><b>(Get Premium Free Food Pack Quarterly).</b></span>
              </li>
              <li class="flex items-start text-green-50 text-sm">
                <i class="fas fa-users-cog text-green-400 mt-1 mr-3"></i>
                <span>Pastries & Confectionaries Loan: Create Union of 10 Members, <br>Contribute 30%, <br> Pastries & Confectionaries Loan Repayment Bi-Weekly <br> Tenor 3 Months,</span>
              </li>
              <!-- Moved to bottom -->
              <li class="flex items-start text-green-50 text-sm">
                <i class="fas fa-truck-loading text-green-400 mt-1 mr-3"></i>
                <span>Pickup & Delivery: Free</span>
              </li>
            </div>
          </ul>
          <!-- <div class="py-3 text-center bg-green-800 rounded-lg font-bold">Ultimate Milestone</div> -->
          <a href="{{ route('register') }}" class="block w-full py-3 border-2 border-orange-600 text-orange-600 text-center font-bold rounded-lg hover:bg-orange-600 hover:text-white transition-all">Join Now</a>

        </div>
      </div>

    </div>
  </div>
</section>

<script>
  // Bronze
  document.getElementById('bronze-additional-header').addEventListener('click', function() {
    const content = document.getElementById('bronze-additional-content');
    if (content.style.display === 'none') {
      content.style.display = 'block';
    } else {
      content.style.display = 'none';
    }
  });
  // Silver
  document.getElementById('silver-additional-header').addEventListener('click', function() {
    const content = document.getElementById('silver-additional-content');
    if (content.style.display === 'none') {
      content.style.display = 'block';
    } else {
      content.style.display = 'none';
    }
  });
  // Gold
  document.getElementById('gold-additional-header').addEventListener('click', function() {
    const content = document.getElementById('gold-additional-content');
    if (content.style.display === 'none') {
      content.style.display = 'block';
    } else {
      content.style.display = 'none';
    }
  });
  // Emerald
  document.getElementById('emerald-additional-header').addEventListener('click', function() {
    const content = document.getElementById('emerald-additional-content');
    if (content.style.display === 'none') {
      content.style.display = 'block';
    } else {
      content.style.display = 'none';
    }
  });
</script>

        <!-- About Section -->
        <!-- <section id="about" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl font-bold text-gray-900 mb-6 font-display">About <span class="gradient-text">ReferralPro</span></h2>
                        <p class="text-gray-600 mb-6">We're revolutionizing the way people earn through referrals. Our platform combines cutting-edge technology with proven multi-level marketing principles to create sustainable income opportunities.</p>
                        <p class="text-gray-600 mb-8">Founded in 2020, we've helped thousands of individuals build passive income streams through our innovative referral system.</p>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="text-3xl font-bold text-primary mb-2">100%</div>
                                <div class="text-gray-600">Transparent Commissions</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold text-secondary mb-2">Secure</div>
                                <div class="text-gray-600">Payment System</div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <img
                            src="https://picsum.photos/600/400?random=4"
                            alt="ReferralPro team collaboration"
                            class="rounded-2xl shadow-xl"
                            loading="lazy"
                        />
                        <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-2xl shadow-lg w-64">
                            <div class="text-3xl font-bold text-primary mb-2">$10M+</div>
                            <div class="text-gray-600">Paid to our members</div>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->

            <!-- <section id="about" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl font-bold text-gray-900 mb-6 font-display">About <span style="color: #26C1C9;">FCCEI</span></h2>

                        <p class="text-gray-600 mb-6 leading-relaxed">
                            <strong>FoodChain Community Empowerment Initiative</strong> is a social enterprise from Doorstep Farms Limited designed to combat hunger and food insecurity among low-income earners in Nigeria. We address the social challenges caused by economic crisis, inflation, and unemployment.
                        </p>

                        <p class="text-gray-600 mb-8 leading-relaxed">
                            Our unique value proposition is to establish a community of common-wealth where members have regular access to <strong>FREE staple food items</strong>. By growing the community base, members build resilience and create a profound social impact on public safety, health, and education.<br>
                            low-income earners and students of higher institutions in Nigeria. We address......
                        </p>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <div class="text-3xl font-bold mb-2" style="color: #26C1C9;">Zero</div>
                                <div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Hunger Mission</div>
                            </div>
                            <div>
                                <div class="text-3xl font-bold mb-2" style="color: #1D5FB5;">Free</div>
                                <div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Staple Food Items</div>
                            </div>
                        </div>
                    </div>

                    <div class="relative">
                        <img
                            src="images\about.jpg"
                            alt="FCCEI Food Staples - Rice, Beans, and Oils"
                            class="rounded-2xl shadow-xl w-full h-[400px] object-cover"
                            loading="lazy"
                        />

                        <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-2xl shadow-lg w-72 border-t-4" style="border-color: #FF7F27;">
                            <div class="text-3xl font-bold mb-2" style="color: #1D5FB5;">SDG 2</div>
                            <div class="text-gray-600 text-sm italic">End hunger, achieve food security and improved nutrition.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->

<!-- About Section with Button -->
<section id="about" class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl font-bold text-gray-900 mb-6 font-display">About <span style="color: #26C1C9;">FCCEI</span></h2>

                <p class="text-gray-600 mb-6 leading-relaxed">
                    <strong>FoodChain Community Empowerment Initiative</strong> is a technology-driven social enterprise designed to combat hunger and food insecurity among women, low-income earners and students of tertiary institutions in Nigeria. It is an initiative from Doorstep Farms Limited <b> incorporated on 12th, February 2015 with RC Number 1241873.</b> to address the social challenges of hunger and starvation caused by economic crisis, inflation, unemployment, insecurity, poor standard of living, social conflicts, social instability, policy changes and restiveness.
                </p>

                <p class="text-gray-600 mb-8 leading-relaxed">
                    Hunger is a significant driver of social unrest, conflict and criminal activities in Nigeria, therefore eradicating hunger would have profound social impacts, including: enhanced public safety and security, improved health and education outcomes, stronger community cohesion and a reduction in distress-driven migration. The second of the UN’s 17 Sustainable Development Goals is to end hunger, achieve food security and improved nutrition and promote sustainable agriculture......

                </p>

                <!-- <p class="text-gray-600 mb-8 leading-relaxed">
                    Our unique value proposition is to establish a community of common-wealth where members have regular access to <strong>FREE staple food items</strong>. By growing the community base, members build resilience and create a profound social impact on public safety, health, and education.<br>
                    low-income earners and students of higher institutions in Nigeria. We address......
                </p> -->

                <!-- Read More Button -->
                <button
                    id="openModalBtn"
                    class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition duration-300"
                >
                    Read More
                </button>

                <div class="grid grid-cols-2 gap-6 mt-8">
                    <div>
                        <div class="text-3xl font-bold mb-2" style="color: #26C1C9;">Zero</div>
                        <div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Hunger Mission</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold mb-2" style="color: #1D5FB5;">Free</div>
                        <div class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Staple Food Items</div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <img
                    src="{{ asset('assets/landing/images/about.jpg') }}"
                    alt="FCCEI Food Staples - Rice, Beans, and Oils"
                    class="rounded-2xl shadow-xl w-full h-[400px] object-cover"
                    loading="lazy"
                />

                <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-2xl shadow-lg w-72 border-t-4" style="border-color: #FF7F27;">
                    <div class="text-3xl font-bold mb-2" style="color: #1D5FB5;">SDG 2</div>
                    <div class="text-gray-600 text-sm italic">End hunger, achieve food security and improved nutrition.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Structure -->
<div id="aboutModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <div class="bg-white max-w-6xl w-full p-6 rounded-lg shadow-lg relative flex flex-col lg:flex-row max-h-full overflow-y-auto">
    <!-- Close Button -->
    <button id="closeModalBtn" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800 text-3xl z-50">
      &times;
    </button>
    <!-- Left Content: Text -->
    <div class="flex-1 p-4 overflow-y-auto">
    <h2 class="text-4xl font-bold text-gray-900 mb-6 font-display">About <span style="color: #26C1C9;">FCCEI</span></h2>
      <!-- <h2 class="text-4xl mb-4 font-bold" <span style="color: #26C1C9;">FCCEI</span> style="color: #26C1C9;">About FCCEI</h2> -->
      <p class="mb-4 leading-relaxed">
        <strong>FoodChain Community Empowerment Initiative</strong> is a technology-driven social enterprise designed to combat hunger and food insecurity among women, low-income earners and students of tertiary institutions in Nigeria. It is an initiative from Doorstep Farms Limited <b> incorporated on 12th, February 2015 with RC Number 1241873.</b> to address the social challenges of hunger and starvation caused by economic crisis, inflation, unemployment, insecurity, poor standard of living, social conflicts, social instability, policy changes and restiveness.
      </p>
      <p class="mb-4 leading-relaxed">
        Hunger is a significant driver of social unrest, conflict and criminal activities in Nigeria, therefore eradicating hunger would have profound social impacts, including: enhanced public safety and security, improved health and education outcomes, stronger community cohesion and a reduction in distress-driven migration. The second of the UN’s 17 Sustainable Development Goals is to end hunger, achieve food security and improved nutrition and promote sustainable agriculture.
      </p>
      <p class="mb-4 leading-relaxed">
        People who are food insecure are disproportionately affected by diet-sensitive chronic diseases such as diabetes and high blood pressure, and according to research by the World Food Program (WFP), food insecurity is also linked to many adverse effects on overall health. Food insecurity is not just a physical lack of food. It also affects mental and emotional well-being and can lead to a vicious cycle of poverty and malnutrition by increasing vulnerability to illness and poor birth outcomes, limited cognitive development, and productivity later in life. According to UN- Food and Agriculture Organization (FAO) report, people facing hunger struggle with high living costs, expensive housing, unemployment and low-wage jobs.
      </p>
      <p class="mb-4 leading-relaxed">
        Our unique value proposition is to establish a community of common-wealth among women, low-income households and students of tertiary institutions in rural, peri-urban, and urban underserved areas in Nigeria and the West African sub-region, where each member has access to Food Loan and  FREE staple food items based on their needs.
      </p>
      <p class="mb-4 leading-relaxed">
        Our benefits package is designed to encourage members to build resilience and create social impact by growing the community base, which ensures continuous access to food Loan and free food items.
      </p>
    </div>
    <!-- Right Side: Image -->
    <div class="flex-1 p-4 flex items-center justify-center">
      <div class="w-full h-full relative">
        <img src="{{ asset('assets/landing/images/about.jpg') }}"
             alt="Community Food"
             class="absolute inset-0 w-full h-full object-cover rounded-lg shadow-md" />
            <div class="absolute -bottom-6 -right-6 bg-white p-6 rounded-2xl shadow-lg w-72 border-t-4" style="border-color: #FF7F27;">
                 <div class="text-3xl font-bold mb-2" style="color: #1D5FB5;">Who is This Initiative For?</div>
                 <div class="text-gray-600 text-sm italic">This Initiative is specifically designed for Women, Low-income earners, Micro-business owners and Students of tertiary institutions.</div>
            </div>
      </div>
    </div>
  </div>
</div>

<!-- JavaScript to toggle modal -->
<script>
  document.getElementById('openModalBtn').addEventListener('click', function() {
    document.getElementById('aboutModal').classList.remove('hidden');
    document.getElementById('aboutModal').classList.add('flex');
  });

  document.getElementById('closeModalBtn').addEventListener('click', function() {
    document.getElementById('aboutModal').classList.add('hidden');
    document.getElementById('aboutModal').classList.remove('flex');
  });

  // Close when clicking outside modal content
  document.getElementById('aboutModal').addEventListener('click', function(e) {
    if (e.target === this) {
      this.classList.add('hidden');
      this.classList.remove('flex');
    }
  });
</script>


        <!-- CORE VALUES SECTION -->
<section id="values-vision" class="py-20 bg-gray-50">
  <div class="container mx-auto px-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-20">
      <div class="p-8 rounded-2xl bg-white shadow-md border-l-8" style="border-color: #26C1C9;">
        <h3 class="text-2xl font-bold mb-4" style="color: #26C1C9;">Vision Statement</h3>
        <p class="text-gray-700 leading-relaxed">To create a community-driven, sustainable food support system that provides continuous access to essential food resources for families.</p>
      </div>
      <div class="p-8 rounded-2xl bg-white shadow-md border-l-8" style="border-color: #1D5FB5;">
        <h3 class="text-2xl font-bold mb-4" style="color: #1D5FB5;">Mission Statement</h3>
        <p class="text-gray-700 leading-relaxed">To build a common-wealth community where members contribute according to their abilities and receive according to their needs, fostering food security shared growth and dignity in humanity.</p>
      </div>
    </div>

    <div class="text-center mb-12">
      <h2 class="text-4xl font-bold text-gray-900 mb-4">Core Values: <span style="color: #FF7F27;">SECURE FOOD</span></h2>
      <p class="text-gray-600">Our values are embedded in the concept of SECURE FOOD, representing our commitment to hunger eradication.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-20">
      <!-- Existing core values -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
        <span class="text-4xl font-black block mb-2" style="color: #26C1C9;">S</span>
        <h4 class="font-bold text-lg mb-2">Sustainabllity</h4>
        <p class="text-sm text-gray-600">Consistent and reliable support that strengthens families facing food insecurity.</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
        <span class="text-4xl font-black block mb-2" style="color: #1D5FB5;">E</span>
        <h4 class="font-bold text-lg mb-2">Empowerment</h4>
        <p class="text-sm text-gray-600">Empowering individuals to take control of their food stability through collaboration.</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
        <span class="text-4xl font-black block mb-2" style="color: #FF7F27;">C</span>
        <h4 class="font-bold text-lg mb-2">Credibility</h4>
        <p class="text-sm text-gray-600">Transparency, integrity, and trustworthiness in our operations and member relations.</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
        <span class="text-4xl font-black block mb-2" style="color: #26C1C9;">U</span>
        <h4 class="font-bold text-lg mb-2">Unlimited Access</h4>
        <p class="text-sm text-gray-600">Continuous access to essential food items for all members regardless of status.</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
        <span class="text-4xl font-black block mb-2" style="color: #1D5FB5;">R</span>
        <h4 class="font-bold text-lg mb-2">Respect for All</h4>
        <p class="text-sm text-gray-600">Treating every member with dignity and valuing their unique circumstances.</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
        <span class="text-4xl font-black block mb-2" style="color: #FF7F27;">E</span>
        <h4 class="font-bold text-lg mb-2">Equity</h4>
        <p class="text-sm text-gray-600">Promoting fairness to ensure food support reaches those who need it most.</p>
      </div>

      <!-- New core values -->
      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
        <span class="text-4xl font-black block mb-2" style="color: #FF7F27;">F</span>
        <h4 class="font-bold text-lg mb-2">Food Security</h4>
        <p class="text-sm text-gray-600">Ensuring that members have access to sufficient, safe and nutritious food, which is crucial for maintaining good health and preventing malnutrition.</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
        <span class="text-4xl font-black block mb-2" style="color: #FF7F27;">O</span>
        <h4 class="font-bold text-lg mb-2">Opportunity</h4>
        <p class="text-sm text-gray-600">We create avenues for economic and social upliftment through community-driven participation.</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
        <span class="text-4xl font-black block mb-2" style="color: #FF7F27;">O</span>
        <h4 class="font-bold text-lg mb-2">Optimum Nutrition</h4>
        <p class="text-sm text-gray-600">We are committed to distributing quality food items that contribute to healthy living and improved nutrition.</p>
      </div>

      <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 transition-transform hover:scale-105">
        <span class="text-4xl font-black block mb-2" style="color: #FF7F27;">D</span>
        <h4 class="font-bold text-lg mb-2">Delightful Experience</h4>
        <p class="text-sm text-gray-600">We aim to deliver a seamless, positive, and supportive experience to every member throughout their journey.</p>
      </div>
    </div>

    <div class="bg-white p-10 rounded-3xl shadow-lg border border-gray-100 text-center max-w-4xl mx-auto">
      <h3 class="text-2xl font-bold mb-6" style="color: #1D5FB5;">Policy Statement</h3>
      <p class="text-gray-600 italic leading-relaxed">
        FCCEI is committed to promoting food security through a structured, transparent, and community-driven model. Our policies ensure that all members participate responsibly, uphold program integrity, and benefit equitably from food distribution.
      </p>
    </div>
  </div>
</section>

<!-- Board Members Section -->
<!-- <section id="board-members" class="py-20 bg-gray-100">
  <div class="container mx-auto px-4">
    <div class="text-center mb-16">
      <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Discover Our Esteemed <span class="gradient-text">Board Members</span></h2>
      <p class="text-gray-600 max-w-2xl mx-auto">The experienced leaders guiding FCCEI's vision and growth</p>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
       -->
      <!-- Board Member 1 -->
      <!-- <div class="member-card bg-white rounded-2xl overflow-hidden shadow-lg transition-transform hover:scale-105">
        <img
          src="images/board1.png"
          alt="John Doe - Chairperson"
          class="w-full h-64 object-cover"
          loading="lazy"
        />
        <div class="p-6 text-center">
          <h3 class="text-xl font-bold text-gray-900 mb-2">John Williams</h3>
          <div class="text-primary font-medium mb-3">Chairperson</div>
          <p class="text-gray-600 text-sm">Over 20 years in leadership and strategic planning, driving organizational success.</p>
        </div>
      </div> -->

      <!-- Board Member 2 -->
      <!-- <div class="member-card bg-white rounded-2xl overflow-hidden shadow-lg transition-transform hover:scale-105">
        <img
          src="images/board2.png"
          alt="Jane Smith - Treasurer"
          class="w-full h-64 object-cover"
          loading="lazy"
        />
        <div class="p-6 text-center">
          <h3 class="text-xl font-bold text-gray-900 mb-2">Jane Ubong</h3>
          <div class="text-primary font-medium mb-3">Treasurer</div>
          <p class="text-gray-600 text-sm">Expert in finance and governance, ensuring sustainable growth and accountability.</p>
        </div>
      </div> -->

      <!-- Board Member 3 -->
      <!-- <div class="member-card bg-white rounded-2xl overflow-hidden shadow-lg transition-transform hover:scale-105">
        <img
          src="images/board3.png"
          alt="Michael Lee - Advisor"
          class="w-full h-64 object-cover"
          loading="lazy"
        />
        <div class="p-6 text-center">
          <h3 class="text-xl font-bold text-gray-900 mb-2">Michael Ike</h3>
          <div class="text-primary font-medium mb-3">Advisor</div>
          <p class="text-gray-600 text-sm">Brings extensive experience in industry innovation and strategic partnerships.</p>
        </div>
      </div> -->

      <!-- Board Member 4 -->
      <!-- <div class="member-card bg-white rounded-2xl overflow-hidden shadow-lg transition-transform hover:scale-105">
        <img
          src="images/board4.png"
          alt="Laura Kim - Director"
          class="w-full h-64 object-cover"
          loading="lazy"
        />
        <div class="p-6 text-center">
          <h3 class="text-xl font-bold text-gray-900 mb-2">Benson Joseph</h3>
          <div class="text-primary font-medium mb-3">Director</div>
          <p class="text-gray-600 text-sm">Dedicated to fostering innovation and community engagement within our organization.</p>
        </div>
      </div>

    </div>
  </div>
</section> -->

<!----Team Section----->
        <!-- <section id="team" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Introducing Our <span class="gradient-text"> Exceptional Team</span></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">The passionate individuals behind FCCEI's success</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">  -->
                    <!-- Team Member 1--->
                     <!-- <div class="team-card bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-100 flex flex-col h-full">
                        <div class="relative w-full aspect-[4/5] overflow-hidden">
                            <img
                                src="images/frankojipics.png"
                                alt="Frank Ogochukwu Orji - CEO"
                                class="absolute inset-0 w-full h-full object-cover object-top"
                                loading="lazy"
                            />
                        </div>

                        <div class="p-6 flex flex-col flex-grow">
                            <h3 class="text-xl font-bold text-gray-900 mb-1">Frank Ogochukwu Orji</h3>
                            <div class="text-green-700 font-semibold mb-4 text-sm uppercase tracking-wider">CEO & Founder</div>

                            <div class="text-gray-600 text-sm leading-relaxed space-y-3">
                                <p>
                                    Frank Ogochukwu Orji is the visionary Founder and Chief Executive Officer of Food Chain Community Empowerment Initiative, a dynamic social enterprise committed to delivering innovative solutions that transform lives of women from Low-income households, small business owners, and students.
                                </p>
                                <p>
                                    With over 15 years of leadership experience, Frank holds a Bachelor of Arts in Philosophy from the University of Nigeria, Nsukka, and is currently pursuing an Executive MBA at the UNN Business School.
                                </p>
                                <p class="font-medium text-gray-800">
                                    Experience span: Healthcare, Agri-Business, Logistics, FMCG and Insurance.
                                </p>
                            </div>
                        </div>
                    </div> -->








<!-- <section id="impact" class="py-24 bg-white">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mb-16">
            <h2 class="text-green-700 font-bold uppercase tracking-widest text-sm mb-3">Our Track Record</h2>
            <h3 class="text-4xl md:text-5xl font-bold text-gray-900 leading-tight">
                Empowering Communities, <br/>
                <span class="text-gray-400">One Household at a Time.</span>
            </h3>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-20">
            <div class="p-8 bg-gray-50 rounded-3xl border border-gray-100 text-center">
                <div class="text-4xl font-extrabold text-green-700 mb-2">15,000+</div>
                <div class="text-gray-600 font-medium">Meals Provided</div>
            </div>
            <div class="p-8 bg-gray-50 rounded-3xl border border-gray-100 text-center">
                <div class="text-4xl font-extrabold text-green-700 mb-2">500+</div>
                <div class="text-gray-600 font-medium">Women Empowered</div>
            </div>
            <div class="p-8 bg-gray-50 rounded-3xl border border-gray-100 text-center">
                <div class="text-4xl font-extrabold text-green-700 mb-2">25+</div>
                <div class="text-gray-600 font-medium">Tertiary Institutions</div>
            </div>
            <div class="p-8 bg-gray-50 rounded-3xl border border-gray-100 text-center">
                <div class="text-4xl font-extrabold text-green-700 mb-2">98%</div>
                <div class="text-gray-600 font-medium">Loan Repayment</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div class="space-y-6">
                <h4 class="text-2xl font-bold text-gray-900">Revolutionizing Food Security</h4>
                <p class="text-gray-600 text-lg leading-relaxed">
                    At FCCEI, we believe food security is the foundation of economic stability. Through our innovative revolving food loan model, we provide immediate relief to low-income earners while fostering long-term financial independence.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start space-x-3">
                        <div class="bg-green-100 p-1 rounded-full text-green-700 mt-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
                        </div>
                        <span class="text-gray-700 font-medium">Direct support for female-led households.</span>
                    </li>
                    <li class="flex items-start space-x-3">
                        <div class="bg-green-100 p-1 rounded-full text-green-700 mt-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg>
                        </div>
                        <span class="text-gray-700 font-medium">Educational grants for students in tertiary institutions.</span>
                    </li>
                </ul>
                <div class="pt-4">
                    <a href="#contact" class="inline-block bg-green-700 text-white px-8 py-4 rounded-2xl font-bold hover:bg-green-800 transition shadow-lg">Partner With Us</a>
                </div>
            </div>
            <div class="relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl">
                    <img src="images/impact-visual.jpg" alt="FCCEI Community Impact" class="w-full h-auto object-cover">
                </div>
                <div class="absolute -bottom-6 -left-6 bg-white p-6 rounded-2xl shadow-xl border border-gray-100 hidden md:block max-w-xs">
                    <p class="text-gray-900 font-bold mb-1 italic">"The revolving loan changed my business and fed my family when I had nothing."</p>
                    <p class="text-green-700 text-sm font-semibold">— Beneficiary from Lagos</p>
                </div>
            </div>
        </div>
    </div>
</section> -->
<section id="impact" class="py-20 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">

        <div class="flex flex-col md:flex-row items-center justify-between mb-20 gap-10">
            <div class="max-w-2xl text-center md:text-left">
                <div class="inline-block mb-6">
                    <span class="text-green-700 font-bold uppercase tracking-widest text-sm border-b-2 border-green-700 pb-1">
                        Our Track Record
                    </span>
                </div>

                <h2 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-tight mb-8">
                    Empowering Communities, <br/>
                    <span class="text-gray-400">One Household at a Time.</span>
                </h2>

                <div class="flex items-center justify-center md:justify-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-gray-50 shadow-inner flex items-center justify-center border border-gray-100">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path></svg>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-gray-50 shadow-inner flex items-center justify-center border border-gray-100">
                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>
                    </div>
                    <span class="text-gray-400 text-sm font-medium ml-2">w 24 h hsuiridg</span>
                </div>
            </div>

            <div class="relative flex-shrink-0">
                <div class="w-72 h-72 md:w-[380px] md:h-[380px] rounded-full border-[15px] border-white shadow-[0_20px_50px_rgba(0,0,0,0.1)] overflow-hidden">
                    <img src="{{ asset('assets/landing/images/impactwoman.png') }}" alt="Impact Focus" class="w-full h-full object-cover">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-24">
            <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                <div class="text-3xl md:text-4xl font-black text-green-700 mb-2">15,000+</div>
                <p class="text-gray-600 font-semibold text-sm">Meals Provided</p>
            </div>
            <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                <div class="text-3xl md:text-4xl font-black text-green-700 mb-2">500+</div>
                <p class="text-gray-600 font-semibold text-sm">Women Empowered</p>
            </div>
            <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                <div class="text-3xl md:text-4xl font-black text-green-700 mb-2">25+</div>
                <p class="text-gray-600 font-semibold text-sm">Tertiary Institutions</p>
            </div>
            <div class="bg-white p-10 rounded-[2.5rem] shadow-[0_10px_30px_rgba(0,0,0,0.04)] border border-gray-50 text-center hover:shadow-lg transition-all duration-300">
                <div class="text-3xl md:text-4xl font-black text-green-700 mb-2">98%</div>
                <p class="text-gray-600 font-semibold text-sm">Loan Repayment</p>
            </div>
        </div>

        <div class="bg-gray-50/50 rounded-[3.5rem] p-10 md:p-16 flex flex-col lg:flex-row gap-16 items-center border border-gray-100">
            <div class="lg:w-1/2 space-y-8">
                <h4 class="text-3xl font-bold text-gray-900 leading-tight">Revolutionizing Food Security</h4>
                <p class="text-gray-600 text-lg leading-relaxed">
                    At FCCEI, we believe food security is the foundation of economic stability. Through our innovative revolving food loan model, we foster long-term financial independence.
                </p>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-green-600 text-white p-1.5 rounded-full shadow-md"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                        <span class="text-gray-800 font-semibold">Direct support for female-led households.</span>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="bg-green-600 text-white p-1.5 rounded-full shadow-md"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></div>
                        <span class="text-gray-800 font-semibold">Educational grants for students.</span>
                    </div>
                </div>
                <button class="bg-green-700 text-white px-10 py-4 rounded-2xl font-bold hover:bg-green-800 transition-all shadow-xl hover:shadow-green-200">Partner With Us</button>
            </div>

            <div class="lg:w-1/2 relative">
                <div class="rounded-[3rem] overflow-hidden shadow-2xl border-4 border-white">
                    <img src="{{ asset('assets/landing/images/warehouse.png') }}" alt="FCCEI Operations" class="w-full h-[400px] object-cover">
                </div>
                <div class="absolute -bottom-6 -right-4 md:-right-10 bg-white p-8 rounded-3xl shadow-2xl border border-gray-50 max-w-xs animate-bounce-slow">
                    <p class="text-gray-700 font-medium italic mb-4 leading-relaxed">
                        "The revolving loan changed my business and fed my family when I had nothing."
                    </p>
                    <p class="text-green-700 text-xs font-bold uppercase tracking-widest">— Beneficiary from Lagos</p>
                </div>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('.counter');

    const runCounter = (el) => {
        const target = +el.getAttribute('data-target');
        const duration = 2000; // 2 seconds to reach the number
        const increment = target / (duration / 16); // 16ms per frame (60fps)

        let currentCount = 0;

        const updateCount = () => {
            currentCount += increment;
            if (currentCount < target) {
                el.innerText = Math.ceil(currentCount).toLocaleString();
                requestAnimationFrame(updateCount);
            } else {
                el.innerText = target.toLocaleString();
            }
        };

        updateCount();
    };

    // Trigger animation only when section is scrolled into view
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                runCounter(entry.target);
                observer.unobserve(entry.target); // Run only once
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(c => observer.observe(c));
});
</script>

</section>



        <!-- Blog Section -->
        <section id="blog" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Latest <span class="gradient-text">Blog</span> Posts</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Insights, tips, and success stories from our community</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Blog Post 1 -->
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <img
                            src="{{ asset('assets/landing/images/blogsa.jpeg') }}"
                            alt="Maximizing Your Referral Earnings"
                            class="w-full h-48 object-cover"
                            loading="lazy"
                        />
                        <div class="p-6">
                            <div class="text-sm text-primary font-medium mb-2">COMMUNITY BUILDING</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Build a Strong Food-Secure Community in 2026</h3>
                            <p class="text-gray-600 mb-4">Discover practical ways to grow a supportive community where members collaborate, empower one another, and ensure continuous access to essential food resources.</p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Jan 5, 2026</span>
                                <a href="{{ route('blog') }}" class="text-primary font-medium hover:text-primary-dark">Read More →</a>
                            </div>
                        </div>
                    </article>

                    <!-- Blog Post 2 -->
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <img
                            src="{{ asset('assets/landing/images/blogpost.jpeg') }}"
                            alt="Success Story: From Zero to $10K/Month"
                            class="w-full h-48 object-cover"
                            loading="lazy"
                        />
                        <div class="p-6">
                            <div class="text-sm text-secondary font-medium mb-2">SUCCESS STORIES</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">From Struggle to Stability: A Community Transformation Story</h3>
                            <p class="text-gray-600 mb-4">
                                Discover how active participation and shared support within our community helped transform lives, improve livelihoods, and create sustainable food access.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Jan 5, 2026</span>
                                <a href="blog.html" class="text-primary font-medium hover:text-primary-dark">Read More →</a>
                            </div>
                        </div>
                    </article>

                    <!-- Blog Post 3 -->
                    <article class="blog-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <img
                            src="{{ asset('assets/landing/images/bloggpo.png') }}"
                            alt="Understanding Multi-Level Commission Structures"
                            class="w-full h-48 object-cover"
                            loading="lazy"
                        />
                        <div class="p-6">
                            <div class="text-sm text-accent font-medium mb-2">EDUCATION</div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">Understanding Community Empowerment & Sustainable Growth</h3>
                            <p class="text-gray-600 mb-4">
                                Learn how education, collaboration, and shared resources help individuals and communities build lasting impact and food security.
                            </p>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Jan 5, 2026</span>
                                <a href="{{ route('blog') }}" class="text-primary font-medium hover:text-primary-dark">Read More →</a>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="text-center mt-12">
                    <a href="{{ route('blog') }}" class="px-8 py-3 border-2 border-primary text-primary font-bold rounded-lg hover:bg-primary hover:text-white transition-all duration-200">View All Posts</a>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <!-- <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Frequently Asked <span class="gradient-text">Questions</span></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Get answers to common questions about our platform</p>
                </div> -->

                <!-- <div class="max-w-3xl mx-auto space-y-6"> -->
                    <!-- FAQ 1 -->
                    <!-- <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <button class="faq-question w-full text-left p-6 flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">How does the referral commission system work?</span>
                            <i class="fas fa-chevron-down text-primary"></i>
                        </button>
                        <div class="faq-answer p-6 pt-0 hidden">
                            <p class="text-gray-600">Our commission system works on multiple levels. You earn commissions from direct referrals (Level 1) and also from their referrals (Level 2-5, depending on your plan). Commissions are calculated based on subscription fees and are paid out weekly.</p>
                        </div>
                    </div>
                     -->
                    <!-- FAQ 2 -->
                    <!-- <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <button class="faq-question w-full text-left p-6 flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Is there a minimum payout threshold?</span>
                            <i class="fas fa-chevron-down text-primary"></i>
                        </button>
                        <div class="faq-answer p-6 pt-0 hidden">
                            <p class="text-gray-600">Yes, the minimum payout threshold is $50. Once your earnings reach this amount, you can request a payout. We process payments weekly via PayPal, bank transfer, or cryptocurrency.</p>
                        </div>
                    </div>
                     -->
                    <!-- FAQ 3 -->
                    <!-- <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <button class="faq-question w-full text-left p-6 flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Can I upgrade or downgrade my plan?</span>
                            <i class="fas fa-chevron-down text-primary"></i>
                        </button>
                        <div class="faq-answer p-6 pt-0 hidden">
                            <p class="text-gray-600">Yes, you can upgrade or downgrade your plan at any time. When upgrading, you'll gain access to additional features immediately. When downgrading, changes take effect at the start of your next billing cycle.</p>
                        </div>
                    </div> -->

                    <!-- FAQ 4 -->
                    <!-- <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <button class="faq-question w-full text-left p-6 flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900">Is there a mobile app available?</span>
                            <i class="fas fa-chevron-down text-primary"></i>
                        </button>
                        <div class="faq-answer p-6 pt-0 hidden">
                            <p class="text-gray-600">Yes, we have mobile apps available for both iOS and Android. You can track your referrals, monitor earnings, and manage your account directly from your mobile device.</p>
                        </div>
                    </div> -->

                    <!-- FAQ 5 -->
                    <!-- <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <button class="faq-question w-full text-left p-6 flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-900"> there a phone app available Now?</span>
                            <i class="fas fa-chevron-down text-primary"></i>
                        </button>
                        <div class="faq-answer p-6 pt-0 hidden">
                            <p class="text-gray-600">Yes, we have mobile apps available for both iOS and Android. You can track your referrals, monitor earnings, and manage your account directly from your mobile device.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section> -->


<!-- Partner & Sponsorship Section -->
<section id="partners-sponsors" class="py-20 bg-gray-50">
  <div class="container mx-auto px-4">
    <div class="text-center mb-16">
      <h2 class="text-4xl font-bold mb-4 font-display" style="color: #26C1C9;">Our Partners </h2>
      <p class="text-gray-600 max-w-2xl mx-auto">We collaborate with organizations and individuals committed to ending hunger and empowering communities. Join us as a partner or sponsor to make a greater impact.</p>
    </div>
    <!-- Swiper Container -->
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <!-- Slide 1 -->
        <div class="swiper-slide relative flex justify-center items-center p-4 bg-white rounded-lg shadow hover:scale-105 transition-transform duration-300">
          <img src="{{ asset('assets/landing/images/partnermain.jpeg') }}" alt="Partner 1 Logo" class="max-h-20 object-contain mx-auto transition filter hover:brightness-110 hover:contrast-110">

        </div>
        <!-- Slide 2 -->
        <div class="swiper-slide relative flex justify-center items-center p-4 bg-white rounded-lg shadow hover:scale-105 transition-transform duration-300">
          <img src="{{ asset('assets/landing/images/imagesse.png') }}" alt="Partner 2 Logo" class="max-h-20 object-contain mx-auto transition filter hover:brightness-110 hover:contrast-110">

        </div>
        <!-- Slide 3 -->
        <div class="swiper-slide relative flex justify-center items-center p-4 bg-white rounded-lg shadow hover:scale-105 transition-transform duration-300">
          <img src="{{ asset('assets/landing/images/imagelands.jpg') }}" alt="Partner 3 Logo" class="max-h-20 object-contain mx-auto transition filter hover:brightness-110 hover:contrast-110">
        </div>

        <!-- Slide 4 -->
        <div class="swiper-slide relative flex justify-center items-center p-4 bg-white rounded-lg shadow hover:scale-105 transition-transform duration-300">
          <img src="{{ asset('assets/landing/images/imagesland.png') }}" alt="Partner 4 Logo" class="max-h-20 object-contain mx-auto transition filter hover:brightness-110 hover:contrast-110">

        </div>
        <!-- Slide 5 -->
        <div class="swiper-slide relative flex justify-center items-center p-4 bg-white rounded-lg shadow hover:scale-105 transition-transform duration-300">
          <img src="{{ asset('assets/landing/images/imagesleds.png') }}" alt="Partner 5 Logo" class="max-h-20 object-contain mx-auto transition filter hover:brightness-110 hover:contrast-110">

        </div>
         <!-- Slide 6 -->
        <div class="swiper-slide relative flex justify-center items-center p-4 bg-white rounded-lg shadow hover:scale-105 transition-transform duration-300">
          <img src="{{ asset('assets/landing/images/imageslogo.png') }}" alt="Partner 5 Logo" class="max-h-20 object-contain mx-auto transition filter hover:brightness-110 hover:contrast-110">

        </div>
        <!-- Add more slides as needed -->
      </div>
      <!-- Navigation buttons -->
      <div class="swiper-button-next"></div>
      <div class="swiper-button-prev"></div>
      <!-- Pagination dots -->
      <div class="swiper-pagination"></div>
    </div>
    <!-- <div class="mt-16 text-center">
      <h3 class="text-3xl font-bold mb-4" style="color: #26C1C9;">Become a Partner or Sponsor</h3>
      <p class="text-gray-600 mb-6">Support our mission to eradicate hunger and empower communities. Your partnership or sponsorship can help us reach more households and create sustainable impact.</p>
      <a href="#contact" class="px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition">Get Involved</a>
    </div> -->

    <!-- Main Sponsor Section -->
<div class="mt-16 text-center">
  <h3 class="text-3xl font-bold mb-4" style="color: #26C1C9;">Become a Partner or Sponsor</h3>
  <p class="text-gray-600 mb-6">Support our mission to eradicate hunger and empower communities. Your partnership or sponsorship can help us reach more households and create sustainable impact.</p>
  <button id="openModal" class="px-8 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition">Get Involved</button>
</div>

<!-- Sponsor Logos -->
<!-- <div class="flex flex-wrap justify-center items-center mb-8 mt-8 space-x-4 space-y-4">
  <img src="images/finacialpics.png" alt="Sponsor 1" class="h-16 object-contain mx-2">
  <img src="sponsor2.png" alt="Sponsor 2" class="h-16 object-contain mx-2">
  <img src="sponsor3.png" alt="Sponsor 3" class="h-16 object-contain mx-2">
  <img src="sponsor4.png" alt="Sponsor 4" class="h-16 object-contain mx-2">
  Add more sponsor images as needed -->
<!-- </div> -->

<!-- Modal Overlay -->
<div id="sponsorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <!-- Modal Content -->
  <div class="bg-white rounded-lg max-w-2xl w-full p-6 relative shadow-lg">
    <!-- Close Button -->
    <button id="closeModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
    <h4 class="text-2xl font-semibold mb-4" style="color: #26C1C9;">Join as a Sponsor</h4>
    <form action="#submit" method="POST" class="space-y-4">
      <div>
        <label for="name" class="block text-gray-700 mb-2">Name</label>
        <input type="text" id="name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Your Name">
      </div>
      <div>
        <label for="organization" class="block text-gray-700 mb-2">Organization</label>
        <input type="text" id="organization" name="organization" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Your Organization">
      </div>
      <div>
        <label for="email" class="block text-gray-700 mb-2">Email</label>
        <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="you@example.com">
      </div>
      <div>
        <label for="message" class="block text-gray-700 mb-2">Message</label>
        <textarea id="message" name="message" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Tell us about your sponsorship interest"></textarea>
      </div>
      <button type="submit" class="w-full px-4 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition">Send Inquiry</button>
    </form>
  </div>
</div>

<!-- JavaScript to handle modal open/close -->
<script>
  const openBtn = document.getElementById('openModal');
  const closeBtn = document.getElementById('closeModal');
  const modal = document.getElementById('sponsorModal');

  openBtn.addEventListener('click', () => {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
  });

  closeBtn.addEventListener('click', () => {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  });

  // Close modal when clicking outside the modal content
  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  });
</script>
  </div>
</section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Frequently Asked <span style="color: #26C1C9;">Questions</span></h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Everything you need to know about joining the FCCEI community and achieving food security.</p>
        </div>

        <div class="max-w-3xl mx-auto space-y-4">
            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <button class="faq-toggle-btn w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-all focus:outline-none">
                    <span class="text-lg font-bold text-gray-900">Q1. Is Foodchain Community Empowerment Initiative a ponzi scheme?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" style="color: #26C1C9;"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 pt-0 border-t border-gray-50">
                        <p class="text-gray-600">No it not a ponzi scheme, Foodchain Community Empowerment is an initiative from a reputable firm known as <b>Doorstep Farms Limited</b> designed to support low-income earners, micro-business owners and students of tertiary institutions, providing staple foods for their households..</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <button class="faq-toggle-btn w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-all focus:outline-none">
                    <span class="text-lg font-bold text-gray-900">Q2. How does it work if I want to join the Community?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" style="color: #1D5FB5;"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 pt-0 border-t border-gray-50">
                        <!-- <p class="text-gray-600">Register with an annual subscription of N5,000 (renewable annually). After approval, purchase a one-time N20,000 food starter pack to get items like Rice, Beans, Spaghetti, cooking oil, and more depending on your needs.</p> -->

                        <p class="text-gray-600">First annual subscription with instant food starter pack of N25,000. Then renewable annual subscription of N5000.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <button class="faq-toggle-btn w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-all focus:outline-none">
                    <span class="text-lg font-bold text-gray-900">Q3. Will I start getting food items regularly once I register?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" style="color: #26C1C9;"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 pt-0 border-t border-gray-50">
                        <p class="text-gray-600">Yes, once you become a member and build your community, you will get food regularly.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <button class="faq-toggle-btn w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-all focus:outline-none">
                    <span class="text-lg font-bold text-gray-900">Q4. How many people do I require to be eligible for regular food?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" style="color: #FF7F27;"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 pt-0 border-t border-gray-50">
                        <div class="space-y-3">
                            <p class="text-sm text-gray-600"><strong>BRONZE:</strong> Subscribe and get a food starter pack instantly.</p>
                            <p class="text-sm text-gray-600"><strong>SILVER:</strong> Build a community of 10 members .</p>
                            <p class="text-sm text-gray-600"><strong>GOLD:</strong> Build a community of 100 members.</p>
                            <p class="text-sm text-gray-600"><strong>EMERALD:</strong> Build a community of 1000 members</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <button class="faq-toggle-btn w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-all focus:outline-none">
                    <span class="text-lg font-bold text-gray-900">Q5. If you build a community less than 10 members........</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" style="color: #26C1C9;"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 pt-0 border-t border-gray-50">
                        <p class="text-gray-600">If you build less than 10 members, you are entitled to food items within the discretion of your ADMIN. If you build no community at all, you are not entitled to free food items.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <button class="faq-toggle-btn w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-all focus:outline-none">
                    <span class="text-lg font-bold text-gray-900">Q6. How many times will I pay for subscription or starter pack?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" style="color: #1D5FB5;"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 pt-0 border-t border-gray-50">
                        <p class="text-gray-600">Membership subscription is renewable annually. The food starter pack is a one-time payment. Note - You will get higher Value for the Food starter pack as a way of supporting community members.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <button class="faq-toggle-btn w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-all focus:outline-none">
                    <span class="text-lg font-bold text-gray-900">Q7. How do I get my food if I live far from your office?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" style="color: #FF7F27;"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 pt-0 border-t border-gray-50">
                        <p class="text-gray-600">Food items will be delivered to your doorstep. The cost of delivery is settled by the member.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <button class="faq-toggle-btn w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-all focus:outline-none">
                    <span class="text-lg font-bold text-gray-900">Q8. If I am not living in Nigeria, how do I benefit?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" style="color: #26C1C9;"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 pt-0 border-t border-gray-50">
                        <p class="text-gray-600">You can become a member from any part of the world via our online link, but entitlements must be delivered to a verifiable address in Nigeria provided by you.</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100">
                <button class="faq-toggle-btn w-full text-left p-6 flex justify-between items-center hover:bg-gray-50 transition-all focus:outline-none">
                    <span class="text-lg font-bold text-gray-900">Q9. What happens if I don’t renew my subscription?</span>
                    <i class="fas fa-chevron-down transition-transform duration-300" style="color: #1D5FB5;"></i>
                </button>
                <div class="faq-content max-h-0 overflow-hidden transition-all duration-300 ease-in-out">
                    <div class="p-6 pt-0 border-t border-gray-50">
                        <p class="text-gray-600">Any year you don't renew, you lose community benefits. However, at the <strong>EMERALD</strong> stage, you become a community leader and community leaders are exempted from annual subscriptions..</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .faq-content { transition: max-height 0.4s ease-in-out; }
    .rotate-180 { transform: rotate(180deg); }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const faqBtns = document.querySelectorAll('.faq-toggle-btn');

        faqBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const content = btn.nextElementSibling;
                const icon = btn.querySelector('i');

                // Toggle current
                if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                    content.style.maxHeight = '0px';
                    icon.classList.remove('rotate-180');
                } else {
                    // Close others
                    document.querySelectorAll('.faq-content').forEach(el => {
                        el.style.maxHeight = '0px';
                        el.previousElementSibling.querySelector('i').classList.remove('rotate-180');
                    });
                    // Open this
                    content.style.maxHeight = content.scrollHeight + "px";
                    icon.classList.add('rotate-180');
                }
            });
        });
    });
</script>

        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-16">
                    <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Get In <span class="gradient-text">Touch</span></h2>
                    <p class="text-gray-600 max-w-2xl mx-auto">Have questions? We're here to help you get started</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                    <!-- Contact Info -->
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-8">Contact Information</h3>

                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Office Address</h4>
                                    <p class="text-gray-600">Food Store/ Warehouse: KM 54, Lagos - Ibadan Expressway, Mowe, <br>Ogun State</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-secondary/10 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-phone text-secondary"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Phone Number</h4>
                                    <p class="text-gray-600">+234 816 801 8123<br>Mon-Fri 9AM-6PM PST</p>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-accent/10 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-envelope text-accent"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-1">Email Address</h4>
                                    <p class="text-gray-600"> info@foodchainempowerment.com.ng
                                    <br>Sales@foodchainempowerment.com.ng</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-12">
                            <h4 class="font-bold text-gray-900 mb-4">Follow Us</h4>
                            <div class="flex space-x-4">
                                <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-all duration-200">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-all duration-200">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-all duration-200">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-all duration-200">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h3>

                        <form id="contact-form" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" id="name" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                                </div>
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                <input type="text" id="subject" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                                <textarea id="message" name="message" rows="5" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent" required></textarea>
                            </div>

                            <button type="submit" class="w-full py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition-all duration-200">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Newsletter -->
        <section class="py-16 bg-gradient-to-r from-primary to-secondary">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-white mb-4 font-display">Stay Updated</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">Subscribe to our newsletter for the latest tips, success stories, and platform updates</p>

                <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
                    <input type="email" placeholder="Your email address" class="flex-1 px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white">
                    <button type="submit" class="px-8 py-3 bg-white text-primary font-bold rounded-lg hover:bg-gray-100 transition-all duration-200">Subscribe</button>
                </form>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <!-- <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">RP</span>
                        </div>
                        <span class="text-2xl font-bold text-white">Referral<span class="text-primary">Pro</span></span>
                    </div>
                    <p class="text-gray-400 mb-6">Empowering individuals to build sustainable income through smart referrals.</p>
                </div> -->
                <!-- <div>
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-20 h-20 flex items-center justify-center overflow-hidden">
                            <img src="C:\Users\Mquid Devops - 3\Downloads\FCCEI\images\FCCEILOGOr.png" alt="FCCEI Logo" class="object-contain w-full h-full">
                        </div>

                        <div class="flex flex-col">
                            <span class="text-3xl font-extrabold leading-none" style="color: #26C1C9;">FCCEI</span>
                            <span class="text-[11px] font-bold text-white uppercase tracking-widest mt-1" style="color: #1D5FB5;">
                                Food Chain Community Empowerment Initiative
                            </span>
                        </div>
                    </div>

                    <p class="text-gray-400 mb-6">
                        Empowering communities to achieve food security and sustainable growth through collaborative local initiatives.
                    </p>
                </div> -->

                <div>
                <div class="flex items-center space-x-4">
                    <div class="w-32 h-32 flex items-center justify-center overflow-hidden">
                        <img src="images\FCCEILOGOr.png" alt="FCCEI Logo" class="object-contain w-full h-full">
                    </div>

                    <div class="flex flex-col">
                        <span class="text-3xl font-extrabold leading-none" style="color: #26C1C9;">FCCEI</span>
                        <span class="text-[11px] font-bold uppercase tracking-widest" style="color: #1D5FB5;">
                            Food Chain Community Empowerment Initiative
                        </span>
                    </div>
                </div>

                <p class="text-gray-400 mb-6 -mt-2">
                    Empowering communities to achieve food security and sustainable growth through collaborative local initiatives.
                </p>
                <div class="flex space-x-4">
                <a href="https://web.facebook.com/profile.php?id=61585986685899" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
                    <i class="fab fa-facebook-f text-brandTeal group-hover:text-white"></i>
                </a>
                <!-- <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
                    <i class="fab fa-tiktok text-brandTeal group-hover:text-white"></i>
                </a> -->

                <a href="https://www.tiktok.com/@foodchainempowerm?lang=en" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
                    <i class="fab fa-tiktok text-brandTeal group-hover:text-white"></i>
                </a>

                <a href="https://www.instagram.com/foodchainempowerment/" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
                    <i class="fab fa-instagram text-brandTeal group-hover:text-white"></i>
                </a>
                <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
                    <i class="fab fa-whatsapp text-brandTeal group-hover:text-white"></i>
                </a>
            </div>
            </div>
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#home" class="text-gray-400 hover:text-white transition-colors duration-200">Home</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors duration-200">About Us</a></li>
                        <li><a href="#products" class="text-gray-400 hover:text-white transition-colors duration-200">Products</a></li>
                        <li><a href="#blog" class="text-gray-400 hover:text-white transition-colors duration-200">Blog</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="text-lg font-bold mb-6">Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="/privacy" class="text-gray-400 hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                        <li><a href="/terms" class="text-gray-400 hover:text-white transition-colors duration-200">Terms of Service</a></li>
                        <li><a href="/cookies" class="text-gray-400 hover:text-white transition-colors duration-200">Cookie Policy</a></li>
                        <li><a href="/compliance" class="text-gray-400 hover:text-white transition-colors duration-200">Compliance</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h4 class="text-lg font-bold mb-6">Resources</h4>
                    <ul class="space-y-3">
                        <li><a href="/help" class="text-gray-400 hover:text-white transition-colors duration-200">Help Center</a></li>
                        <li><a href="/faq" class="text-gray-400 hover:text-white transition-colors duration-200">FAQ</a></li>
                        <li><a href="/community" class="text-gray-400 hover:text-white transition-colors duration-200">Community</a></li>
                        <li><a href="/affiliate" class="text-gray-400 hover:text-white transition-colors duration-200">Affiliate Program</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2024 FCCEI. All rights reserved. | Designed By Bassiprog Digital Solution </p>
            </div>
        </div>
    </footer>

    <script>
        // Define data at the beginning
        const heroSlides = [
            {
                "title": "Earn Through Referrals",
                "highlight": "Referrals",
                "description": "Join our multi-level subscription platform and start earning passive income through your network. No experience needed.",
                "primaryButton": { "text": "Start Earning Now", "url": "/register" },
                "secondaryButton": { "text": "Learn More", "url": "#how-it-works" }
            },
            {
                "title": "Build Your Network",
                "highlight": "Network",
                "description": "Grow your team and maximize your earning potential with our advanced referral tracking system.",
                "primaryButton": { "text": "Build Network", "url": "/register" },
                "secondaryButton": { "text": "View Features", "url": "#features" }
            },
            {
                "title": "Passive Income Stream",
                "highlight": "Income",
                "description": "Create multiple income streams with our tiered subscription model and referral bonuses.",
                "primaryButton": { "text": "Start Today", "url": "/register" },
                "secondaryButton": { "text": "View Plans", "url": "#pricing" }
            }
        ];

        // const teamMembers = [
        //     {
        //         "id": 1,
        //         "name": "Sarah Johnson",
        //         "position": "CEO & Founder",
        //         "description": "10+ years in network marketing and platform development",
        //         "image": "https://picsum.photos/400/300?random=5"
        //     },
        //     {
        //         "id": 2,
        //         "name": "Michael Chen",
        //         "position": "Chief Technology Officer",
        //         "description": "Expert in scalable platform architecture and security",
        //         "image": "https://picsum.photos/400/300?random=6"
        //     },
        //     {
        //         "id": 3,
        //         "name": "Emma Rodriguez",
        //         "position": "Head of Marketing",
        //         "description": "Specializes in growth strategies and community building",
        //         "image": "https://picsum.photos/400/300?random=7"
        //     },
        //     {
        //         "id": 4,
        //         "name": "David Kim",
        //         "position": "Head of Support",
        //         "description": "Ensures our members receive exceptional support 24/7",
        //         "image": "https://picsum.photos/400/300?random=8"
        //     }
        // ];

        const teamMembers = [
    {
        "id": 1,
        "name": "Sarah Johnson",
        "position": "CEO & Founder",
        "description": "10+ years in community empowerment and social enterprise development.",
        "image": "{{ asset('assets/landing/images/1717874260332.jpg') }}"
    },
    {
        "id": 2,
        "name": "Kofi Mensah",
        "position": "Chief Technology Officer",
        "description": "Expert in scalable platform architecture and digital food distribution systems.",
        "image": "{{ asset('assets/landing/images/1717874260332.jpg') }}"
    },
    {
        "id": 3,
        "name": "Amina Okoro",
        "position": "Head of Marketing",
        "description": "Specializes in community growth strategies and sustainable food network building.",
        "image": "{{ asset('assets/landing/images/1717874260332.jpg') }}"
    },
    {
        "id": 4,
        "name": "David Adebayo",
        "position": "Head of Support",
        "description": "Ensures our community members receive exceptional assistance and guidance.",
        "image": "{{ asset('assets/landing/images/1717874260332.jpg') }}"
    }
];

        const blogPosts = [
            {
                "id": 1,
                "category": "STRATEGIES",
                "title": "Maximizing Your Referral Earnings in 2024",
                "description": "Learn advanced strategies to grow your network and increase your commission earnings.",
                "date": "Jan 15, 2024",
                "url": "/blog/maximizing-earnings",
                "image": "https://picsum.photos/400/250?random=9"
            },
            {
                "id": 2,
                "category": "SUCCESS STORIES",
                "title": "From Zero to $10K/Month: A Member's Journey",
                "description": "Discover how one member built a thriving network and achieved financial freedom.",
                "date": "Jan 10, 2024",
                "url": "/blog/success-story",
                "image": "https://picsum.photos/400/250?random=10"
            },
            {
                "id": 3,
                "category": "EDUCATION",
                "title": "Understanding Multi-Level Commission Structures",
                "description": "A comprehensive guide to how our commission system works and how you benefit.",
                "date": "Jan 5, 2024",
                "url": "/blog/commission-structures",
                "image": "https://picsum.photos/400/250?random=11"
            }
        ];

        const faqItems = [
            {
                "question": "How does the referral commission system work?",
                "answer": "Our commission system works on multiple levels. You earn commissions from direct referrals (Level 1) and also from their referrals (Level 2-5, depending on your plan). Commissions are calculated based on subscription fees and are paid out weekly."
            },
            {
                "question": "Is there a minimum payout threshold?",
                "answer": "Yes, the minimum payout threshold is $50. Once your earnings reach this amount, you can request a payout. We process payments weekly via PayPal, bank transfer, or cryptocurrency."
            },
            {
                "question": "Can I upgrade or downgrade my plan?",
                "answer": "Yes, you can upgrade or downgrade your plan at any time. When upgrading, you'll gain access to additional features immediately. When downgrading, changes take effect at the start of your next billing cycle."
            },
            {
                "question": "Is there a mobile app available?",
                "answer": "Yes, we have mobile apps available for both iOS and Android. You can track your referrals, monitor earnings, and manage your account directly from your mobile device."
            }
        ];

        // Mobile Menu Toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
        });

        // Hero Slider
        let currentSlide = 0;
        const slides = document.querySelectorAll('#hero-slider > div');
        const dots = document.querySelectorAll('.slider-dot');

        function showSlide(index) {
            // Hide all slides
            slides.forEach(slide => {
                slide.style.opacity = '0';
                slide.style.zIndex = '1';
            });

            // Remove active class from all dots
            dots.forEach(dot => dot.classList.remove('active'));

            // Show current slide
            slides[index].style.opacity = '1';
            slides[index].style.zIndex = '10';

            // Add active class to current dot
            dots[index].classList.add('active');

            currentSlide = index;
        }

        // Add click events to dots
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                showSlide(index);
            });
        });

        // Auto slide every 5 seconds
        setInterval(() => {
            let nextSlide = (currentSlide + 1) % slides.length;
            showSlide(nextSlide);
        }, 5000);

        // FAQ Accordion
        document.querySelectorAll('.faq-question').forEach(button => {
            button.addEventListener('click', () => {
                const answer = button.nextElementSibling;
                const icon = button.querySelector('i');

                // Toggle answer visibility
                answer.classList.toggle('hidden');

                // Rotate icon
                if (answer.classList.contains('hidden')) {
                    icon.style.transform = 'rotate(0deg)';
                } else {
                    icon.style.transform = 'rotate(180deg)';
                }
            });
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Contact form submission
        document.getElementById('contact-form').addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // In a real Laravel app, this would be an AJAX request to the server
            console.log('Form submitted:', data);

            // Show success message
            alert('Thank you for your message! We will get back to you soon.');
            this.reset();
        });

        // Initialize first slide
        showSlide(0);
    </script>

    <script>
        const swiper = new Swiper('.mySwiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
            },
            pagination: {
            el: '.swiper-pagination',
            clickable: true,
            },
            breakpoints: {
            768: {
                slidesPerView: 3,
            },
            1024: {
                slidesPerView: 4,
            },
            },
            autoplay: {
            delay: 3000,
            disableOnInteraction: false,
            },
        });
    </script>


<a href="https://wa.me/+2348168018123" target="_blank" class="fixed bottom-8 right-8 z-[60] group">
        <span
            class="absolute right-full mr-4 top-1/2 -translate-y-1/2 bg-gray-900 text-white text-xs font-bold px-4 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow-xl">
            Chat with an Event Manager
        </span>
        <div class="bg-[#25D366] p-4 rounded-full shadow-2xl hover:scale-110 transition duration-300">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
            </svg>
        </div>
    </a>

</body>
</html>

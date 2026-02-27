

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCCEI - Build Your Network, Multiply Your Success</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981',
                        'primary-foreground': '#ffffff',
                        emerald: { light: '#34d399', DEFAULT: '#059669', dark: '#064e3b' },
                        gold: { light: '#fbbf24', DEFAULT: '#f59e0b', dark: '#b45309' },
                        secondary: { DEFAULT: '#0f172a', foreground: '#f8fafc' },
                        background: '#ffffff',
                        foreground: '#0f172a',
                        muted: { DEFAULT: '#f1f5f9', foreground: '#64748b' }
                    },
                    fontFamily: { display: ['Plus Jakarta Sans', 'sans-serif'], sans: ['Plus Jakarta Sans', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        .gradient-text { background: linear-gradient(to right, #10b981, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-gradient { background: radial-gradient(circle at top center, #1e293b 0%, #0f172a 100%); }
        .mesh-gradient { background-image: radial-gradient(at 50% 50%, rgba(16, 185, 129, 0.1) 0, transparent 50%), radial-gradient(at 100% 0%, rgba(245, 158, 11, 0.05) 0, transparent 50%); }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .glow-emerald { box-shadow: 0 0 20px rgba(16, 185, 129, 0.4); }

        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 6s ease-in-out infinite; animation-delay: 3s; }
    </style>
</head>
<body class="bg-background text-foreground selection:bg-emerald/20">

    <nav x-data="{ isOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-background/80 backdrop-blur-xl border-b border-muted/50">
        <div class="container mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center">
                    <span class="text-primary-foreground font-bold text-xl">F</span>
                </div>
                <span class="font-bold text-xl tracking-tight">FCCEI</span>
            </div>
            <div class="hidden lg:flex items-center gap-8 text-muted-foreground font-medium">
                <a href="#features" class="hover:text-primary transition-colors">Features</a>
                <a href="#how-it-works" class="hover:text-primary transition-colors">How It Works</a>
                <a href="#pricing" class="hover:text-primary transition-colors">Pricing</a>
                <a href="#faq" class="hover:text-primary transition-colors">FAQ</a>
            </div>
            <div class="hidden lg:flex items-center gap-4">
                <button class="px-5 py-2 text-sm font-semibold hover:text-primary transition-colors">Sign In</button>
                <button class="px-6 py-2.5 bg-primary hover:bg-emerald text-primary-foreground rounded-full text-sm font-bold shadow-lg shadow-primary/20 transition-all">Get Started</button>
            </div>
            <button @click="isOpen = !isOpen" class="lg:hidden p-2 text-foreground">
                <svg x-show="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                <svg x-show="isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </nav>

    <section class="relative min-h-screen hero-gradient overflow-hidden flex items-center">
        <div class="absolute inset-0 mesh-gradient opacity-50"></div>
        <div class="absolute top-20 left-10 w-72 h-72 bg-emerald/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-gold/10 rounded-full blur-3xl animate-float-delayed"></div>

        <div class="container mx-auto px-6 relative pt-20">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald/10 border border-emerald/20 text-emerald-light text-sm font-medium mb-8">
                    <span class="flex h-2 w-2 rounded-full bg-emerald animate-pulse"></span>
                    Join 10,000+ members growing together
                </div>
                <h1 class="text-4xl lg:text-7xl font-bold text-primary-foreground leading-tight mb-6">
                    Build Your Network,<br><span class="gradient-text">Multiply Your Success</span>
                </h1>
                <p class="text-lg lg:text-xl text-primary-foreground/70 max-w-2xl mx-auto mb-10">
                    FCCEI empowers you to grow through community. Earn rewards by sharing value, building connections, and helping others succeed in our multi-level referral ecosystem.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                    <button class="w-full sm:w-auto px-8 py-4 bg-primary hover:bg-emerald text-primary-foreground rounded-2xl font-bold text-lg flex items-center justify-center gap-2 transition-all hover:scale-105 shadow-xl shadow-primary/20">
                        Start Earning Today
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                    <button class="w-full sm:w-auto px-8 py-4 bg-white/5 hover:bg-white/10 text-primary-foreground border border-white/10 rounded-2xl font-bold text-lg flex items-center justify-center gap-2 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                        Watch Demo
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-3xl mx-auto">
                    <div class="glass-card rounded-2xl p-6 border-white/10">
                        <div class="flex items-center justify-center gap-3 mb-2">
                            <div class="p-2 rounded-lg bg-emerald/20 text-emerald"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                            <span class="text-3xl font-bold text-primary-foreground">10K+</span>
                        </div>
                        <p class="text-primary-foreground/60 text-sm">Active Members</p>
                    </div>
                    <div class="glass-card rounded-2xl p-6 border-white/10">
                        <div class="flex items-center justify-center gap-3 mb-2">
                            <div class="p-2 rounded-lg bg-gold/20 text-gold"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg></div>
                            <span class="text-3xl font-bold text-primary-foreground">₱50M+</span>
                        </div>
                        <p class="text-primary-foreground/60 text-sm">Rewards Distributed</p>
                    </div>
                    <div class="glass-card rounded-2xl p-6 border-white/10">
                        <div class="flex items-center justify-center gap-3 mb-2">
                            <div class="p-2 rounded-lg bg-emerald/20 text-emerald"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04"></path></svg></div>
                            <span class="text-3xl font-bold text-primary-foreground">100%</span>
                        </div>
                        <p class="text-primary-foreground/60 text-sm">Secure & Transparent</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-background to-transparent"></div>
    </section>

    <section id="features" class="py-24 bg-background relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4 inline-block">Features</span>
                <h2 class="text-4xl lg:text-5xl font-bold mb-6 text-foreground">Everything You Need to <span class="gradient-text">Succeed</span></h2>
                <p class="text-muted-foreground text-lg">Our platform is designed with your success in mind. From powerful referral tracking to instant payouts.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $feats = [
                        ['t' => 'Multi-Level Referrals', 'd' => 'Earn commissions up to 5 levels deep. Your network grows exponentially.', 'g' => 'from-emerald to-emerald-light'],
                        ['t' => 'Instant Payouts', 'd' => 'Get your earnings directly to your wallet. No waiting periods, no hidden fees.', 'g' => 'from-gold to-gold-light'],
                        ['t' => 'Community First', 'd' => 'Join a supportive community of like-minded individuals working towards freedom.', 'g' => 'from-emerald to-emerald-light'],
                        ['t' => 'Rank Rewards', 'd' => 'Climb the ranks and unlock exclusive bonuses and recognition.', 'g' => 'from-gold to-gold-light'],
                        ['t' => 'Smart Dashboard', 'd' => 'Track your network, earnings, and growth with real-time analytics.', 'g' => 'from-emerald to-emerald-light'],
                        ['t' => 'Secure & Compliant', 'd' => 'Built on robust technology with full regulatory compliance.', 'g' => 'from-gold to-gold-light']
                    ];
                @endphp
                @foreach($feats as $f)
                <div class="p-8 rounded-3xl border border-muted bg-white hover:shadow-xl transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $f['g'] }} flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                         <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">{{ $f['t'] }}</h3>
                    <p class="text-muted-foreground">{{ $f['d'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="how-it-works" class="py-24 bg-muted/30">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4 inline-block">How It Works</span>
                <h2 class="text-4xl font-bold mb-4">Simple Steps to <span class="gradient-text">Financial Growth</span></h2>
            </div>
            <div class="max-w-5xl mx-auto relative">
                <div class="absolute left-8 lg:left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-primary/50 via-primary to-gold/50 hidden md:block lg:-translate-x-px"></div>
                @php
                    $steps = [
                        ['n' => '01', 't' => 'Create Your Account', 'd' => 'Sign up in minutes and choose your subscription plan.'],
                        ['n' => '02', 't' => 'Share & Invite', 'd' => 'Share your referral link with friends, family, and your network.'],
                        ['n' => '03', 't' => 'Watch Your Network Grow', 'd' => 'As your referrals join, your network expands through multiple levels.'],
                        ['n' => '04', 't' => 'Earn Rewards', 'd' => 'Receive commissions from your entire network. More levels, more earnings.']
                    ];
                @endphp
                @foreach($steps as $i => $s)
                <div class="relative flex flex-col lg:flex-row items-start lg:items-center gap-8 mb-16 last:mb-0 {{ $i % 2 === 1 ? 'lg:flex-row-reverse' : '' }}">
                    <div class="flex-1 {{ $i % 2 === 1 ? 'lg:text-right' : '' }}">
                        <div class="bg-white rounded-3xl p-8 border border-muted hover:shadow-lg transition-all {{ $i % 2 === 1 ? 'lg:ml-auto' : '' }} max-w-md">
                            <span class="text-5xl font-bold text-primary/10 mb-4 block">{{ $s['n'] }}</span>
                            <h3 class="text-2xl font-bold mb-3">{{ $s['t'] }}</h3>
                            <p class="text-muted-foreground leading-relaxed">{{ $s['d'] }}</p>
                        </div>
                    </div>
                    <div class="absolute left-0 lg:left-1/2 lg:-translate-x-1/2 flex items-center justify-center z-10">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary to-emerald-light flex items-center justify-center shadow-lg text-white">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                    <div class="flex-1 hidden lg:block"></div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="pricing" class="py-24 bg-background">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4 inline-block">Pricing</span>
                <h2 class="text-4xl font-bold mb-4">Choose Your Path to <span class="gradient-text">Success</span></h2>
            </div>
            <div class="grid lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="p-8 rounded-[2.5rem] bg-white border border-muted hover:shadow-xl transition-all relative">
                    <h3 class="text-xl font-bold mb-2">Starter</h3>
                    <div class="text-4xl font-bold mb-4">₱499<span class="text-lg text-muted-foreground font-normal">/mo</span></div>
                    <p class="text-muted-foreground mb-8">Perfect for beginners ready to start.</p>
                    <button class="w-full py-4 border border-primary text-primary font-bold rounded-2xl hover:bg-primary/5 transition-all mb-8">Choose Starter</button>
                    <ul class="space-y-4 text-sm text-muted-foreground">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> 2-Level Referral Earnings</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> Community Access</li>
                    </ul>
                </div>
                <div class="p-8 rounded-[2.5rem] bg-white border-2 border-primary shadow-2xl relative lg:scale-105">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white text-xs font-bold rounded-full uppercase tracking-widest">Most Popular</div>
                    <h3 class="text-xl font-bold mb-2">Pro</h3>
                    <div class="text-4xl font-bold mb-4 text-primary">₱999<span class="text-lg text-muted-foreground font-normal">/mo</span></div>
                    <p class="text-muted-foreground mb-8">For serious networkers ready to scale.</p>
                    <button class="w-full py-4 bg-primary text-white font-bold rounded-2xl hover:bg-emerald transition-all mb-8 shadow-xl shadow-primary/20">Choose Pro</button>
                    <ul class="space-y-4 text-sm font-medium">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> 4-Level Referral Earnings</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> Team Management</li>
                    </ul>
                </div>
                <div class="p-8 rounded-[2.5rem] bg-white border border-muted hover:shadow-xl transition-all relative">
                    <h3 class="text-xl font-bold mb-2">Elite</h3>
                    <div class="text-4xl font-bold mb-4">₱2,499<span class="text-lg text-muted-foreground font-normal">/mo</span></div>
                    <p class="text-muted-foreground mb-8">For leaders building empires.</p>
                    <button class="w-full py-4 bg-gold text-white font-bold rounded-2xl hover:bg-gold-dark transition-all mb-8">Choose Elite</button>
                    <ul class="space-y-4 text-sm text-muted-foreground">
                        <li class="flex items-center gap-3 font-semibold text-foreground"><svg class="w-5 h-5 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> 5-Level Referral Earnings</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> 1-on-1 Coaching</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="py-24 bg-background">
        <div class="container mx-auto px-6 max-w-3xl">
            <div class="text-center mb-16">
                <span class="px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4 inline-block">FAQ</span>
                <h2 class="text-4xl font-bold mb-4 text-foreground">Frequently Asked <span class="gradient-text">Questions</span></h2>
            </div>
            <div x-data="{ active: null }" class="space-y-4">
                @php
                    $faqs = [
                        ['q' => "What is FCCEI and how does it work?", 'a' => "FCCEI is a multi-level referral-based subscription platform that rewards you for building a network. You earn commissions from subscriptions up to multiple levels deep."],
                        ['q' => "How much can I really earn?", 'a' => "Earnings depend on your tier. Starter members typically earn ₱2,000-10,000/month, while Elite members can exceed ₱200,000+ monthly."],
                        ['q' => "Is this legal and legitimate?", 'a' => "Absolutely. Unlike illegal schemes, our platform provides real value through tools and community access. Compensation is based on subscription sales."],
                        ['q' => "How do I get paid?", 'a' => "We support GCash, bank transfer, and e-wallets. Payouts are processed weekly and trackable via your dashboard."]
                    ];
                @endphp
                @foreach($faqs as $i => $faq)
                <div class="rounded-2xl border border-muted bg-slate-50/50 overflow-hidden">
                    <button @click="active = (active === {{ $i }} ? null : {{ $i }})" class="w-full p-6 text-left flex justify-between items-center group">
                        <span class="font-bold text-foreground group-hover:text-primary transition-colors">{{ $faq['q'] }}</span>
                        <svg class="w-5 h-5 transition-transform" :class="active === {{ $i }} ? 'rotate-180 text-primary' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="active === {{ $i }}" x-collapse class="px-6 pb-6 text-muted-foreground text-sm leading-relaxed">
                        {{ $faq['a'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-24 lg:py-32 hero-gradient relative overflow-hidden">
        <div class="absolute inset-0 mesh-gradient opacity-50"></div>
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald/10 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-gold/10 rounded-full blur-3xl animate-float-delayed"></div>

        <div class="container mx-auto px-6 relative">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald/10 border border-emerald/20 text-emerald-light text-sm font-medium mb-8">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2-6-4.8-6 4.8 2.4-7.2-6-4.8h7.6z"></path></svg>
                    Limited Time: First Month 50% Off
                </div>
                <h2 class="text-3xl lg:text-6xl font-bold text-primary-foreground mb-6">
                    Ready to Transform <span class="gradient-text">Your Future?</span>
                </h2>
                <p class="text-lg text-primary-foreground/70 max-w-2xl mx-auto mb-10">
                    Join thousands of Filipinos who are already building their financial freedom with FCCEI. Your network is waiting. Your success story starts today.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <button class="w-full sm:w-auto px-10 py-5 bg-primary hover:bg-emerald text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-xl shadow-primary/20">
                        Start Your Journey
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                    <button class="w-full sm:w-auto px-10 py-5 bg-white/5 hover:bg-white/10 text-white border border-white/10 font-bold rounded-2xl flex items-center justify-center">
                        Talk to an Advisor
                    </button>
                </div>
                <p class="mt-8 text-primary-foreground/50 text-sm">
                    No credit card required • Cancel anytime • 30-day money-back guarantee
                </p>
            </div>
        </div>
    </section>

    <footer class="bg-secondary text-secondary-foreground pt-16 pb-10">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-5 gap-12 mb-16">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center font-bold text-xl">F</div>
                        <span class="font-bold text-xl">FCCEI</span>
                    </div>
                    <p class="text-secondary-foreground/70 max-w-sm mb-6 italic">Empowering Filipinos to achieve financial freedom through community-driven referral networks.</p>
                    <div class="space-y-3 text-secondary-foreground/60 text-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            support@fccei.com
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Product</h4>
                    <ul class="space-y-2 text-secondary-foreground/60 text-sm">
                        <li><a href="#features" class="hover:text-primary">Features</a></li>
                        <li><a href="#pricing" class="hover:text-primary">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Company</h4>
                    <ul class="space-y-2 text-secondary-foreground/60 text-sm">
                        <li><a href="#" class="hover:text-primary">About Us</a></li>
                        <li><a href="#" class="hover:text-primary">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Legal</h4>
                    <ul class="space-y-2 text-secondary-foreground/60 text-sm">
                        <li><a href="#" class="hover:text-primary">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-primary">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/5 pt-10 flex flex-col md:row justify-between items-center gap-4 text-secondary-foreground/40 text-xs">
                <p>© {{ date('Y') }} FCCEI. All rights reserved.</p>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center hover:bg-primary transition-colors cursor-pointer">FB</div>
                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center hover:bg-primary transition-colors cursor-pointer">TW</div>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>


<!-- ========================Below is incomplete-============================ -->

<!-- <!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCCEI - Build Your Network, Multiply Your Success</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981',
                        'primary-foreground': '#ffffff',
                        emerald: { light: '#34d399', DEFAULT: '#059669', dark: '#064e3b' },
                        gold: { light: '#fbbf24', DEFAULT: '#f59e0b', dark: '#b45309' },
                        secondary: { DEFAULT: '#0f172a', foreground: '#f8fafc' },
                        background: '#f8fafc', // Soft Off-White
                        foreground: '#1e293b', // Deep Slate for better readability
                        muted: { DEFAULT: '#f1f5f9', foreground: '#64748b' }
                    },
                    fontFamily: { display: ['Plus Jakarta Sans', 'sans-serif'], sans: ['Plus Jakarta Sans', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        .gradient-text { background: linear-gradient(to right, #10b981, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-gradient { background: radial-gradient(circle at top center, #1e293b 0%, #0f172a 100%); }
        .mesh-gradient { background-image: radial-gradient(at 50% 50%, rgba(16, 185, 129, 0.1) 0, transparent 50%), radial-gradient(at 100% 0%, rgba(245, 158, 11, 0.05) 0, transparent 50%); }
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }

        /* New helper for white cards on the slate background */
        .white-card { @apply bg-white border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow; }

        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 6s ease-in-out infinite; animation-delay: 3s; }
    </style>
</head>
<body class="bg-background text-foreground selection:bg-emerald/20">

    <nav x-data="{ isOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-background/90 backdrop-blur-xl border-b border-slate-200/50">
        <div class="container mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center">
                    <span class="text-primary-foreground font-bold text-xl">F</span>
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-900">FCCEI</span>
            </div>
            <div class="hidden lg:flex items-center gap-8 text-slate-600 font-medium">
                <a href="#features" class="hover:text-primary transition-colors">Features</a>
                <a href="#how-it-works" class="hover:text-primary transition-colors">How It Works</a>
                <a href="#pricing" class="hover:text-primary transition-colors">Pricing</a>
                <a href="#faq" class="hover:text-primary transition-colors">FAQ</a>
            </div>
            <div class="hidden lg:flex items-center gap-4">
                <button class="px-5 py-2 text-sm font-semibold hover:text-primary transition-colors">Sign In</button>
                <button class="px-6 py-2.5 bg-primary hover:bg-emerald text-primary-foreground rounded-full text-sm font-bold shadow-lg shadow-primary/20 transition-all">Get Started</button>
            </div>
        </div>
    </nav>

    <section class="relative min-h-screen hero-gradient overflow-hidden flex items-center">
        <div class="absolute inset-0 mesh-gradient opacity-50"></div>
        <div class="container mx-auto px-6 relative pt-20">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald/10 border border-emerald/20 text-emerald-light text-sm font-medium mb-8">
                    <span class="flex h-2 w-2 rounded-full bg-emerald animate-pulse"></span>
                    Join 10,000+ members growing together
                </div>
                <h1 class="text-4xl lg:text-7xl font-bold text-white leading-tight mb-6">
                    Build Your Network,<br><span class="gradient-text">Multiply Your Success</span>
                </h1>
                <p class="text-lg lg:text-xl text-slate-300 max-w-2xl mx-auto mb-10">
                    FCCEI empowers you to grow through community. Earn rewards by sharing value, building connections, and helping others succeed in our multi-level referral ecosystem.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                    <button class="w-full sm:w-auto px-8 py-4 bg-primary hover:bg-emerald text-white rounded-2xl font-bold text-lg flex items-center justify-center gap-2 transition-all hover:scale-105 shadow-xl shadow-primary/20">
                        Start Earning Today
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                    <button class="w-full sm:w-auto px-8 py-4 bg-white/5 hover:bg-white/10 text-white border border-white/10 rounded-2xl font-bold text-lg flex items-center justify-center gap-2 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                        Watch Demo
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-3xl mx-auto">
                    <div class="glass-card rounded-2xl p-6">
                        <span class="text-3xl font-bold text-white block mb-1">10K+</span>
                        <p class="text-slate-400 text-sm">Active Members</p>
                    </div>
                    <div class="glass-card rounded-2xl p-6">
                        <span class="text-3xl font-bold text-white block mb-1">₱50M+</span>
                        <p class="text-slate-400 text-sm">Rewards Distributed</p>
                    </div>
                    <div class="glass-card rounded-2xl p-6">
                        <span class="text-3xl font-bold text-white block mb-1">100%</span>
                        <p class="text-slate-400 text-sm">Secure & Transparent</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-background to-transparent"></div>
    </section>

    <section id="features" class="py-24 bg-background relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4 inline-block">Features</span>
                <h2 class="text-4xl lg:text-5xl font-bold mb-6 text-slate-900">Everything You Need to <span class="gradient-text">Succeed</span></h2>
                <p class="text-slate-600 text-lg">Our platform is designed with your success in mind. From powerful referral tracking to instant payouts.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $feats = [
                        ['t' => 'Multi-Level Referrals', 'd' => 'Earn commissions up to 5 levels deep. Your network grows exponentially.', 'g' => 'from-emerald to-emerald-light'],
                        ['t' => 'Instant Payouts', 'd' => 'Get your earnings directly to your wallet. No waiting periods, no hidden fees.', 'g' => 'from-gold to-gold-light'],
                        ['t' => 'Community First', 'd' => 'Join a supportive community of like-minded individuals working towards freedom.', 'g' => 'from-emerald to-emerald-light'],
                        ['t' => 'Rank Rewards', 'd' => 'Climb the ranks and unlock exclusive bonuses and recognition.', 'g' => 'from-gold to-gold-light'],
                        ['t' => 'Smart Dashboard', 'd' => 'Track your network, earnings, and growth with real-time analytics.', 'g' => 'from-emerald to-emerald-light'],
                        ['t' => 'Secure & Compliant', 'd' => 'Built on robust technology with full regulatory compliance.', 'g' => 'from-gold to-gold-light']
                    ];
                @endphp
                @foreach($feats as $f)
                <div class="p-8 rounded-3xl bg-white border border-slate-200 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $f['g'] }} flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                         <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900">{{ $f['t'] }}</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $f['d'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="pricing" class="py-24 bg-slate-100/50">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4 inline-block">Pricing</span>
                <h2 class="text-4xl font-bold mb-4 text-slate-900">Choose Your Path to <span class="gradient-text">Success</span></h2>
            </div>
            <div class="grid lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="p-8 rounded-[2.5rem] bg-white border-2 border-primary shadow-2xl relative lg:scale-105 z-10">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white text-xs font-bold rounded-full uppercase tracking-widest">Most Popular</div>
                    <h3 class="text-xl font-bold mb-2 text-slate-900">Pro</h3>
                    <div class="text-4xl font-bold mb-4 text-primary">₱999<span class="text-lg text-slate-500 font-normal">/mo</span></div>
                    <p class="text-slate-600 mb-8">For serious networkers ready to scale.</p>
                    <button class="w-full py-4 bg-primary text-white font-bold rounded-2xl hover:bg-emerald transition-all mb-8 shadow-lg shadow-primary/20">Choose Pro</button>
                    <ul class="space-y-4 text-sm font-medium text-slate-700">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> 4-Level Referral Earnings</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> Team Management</li>
                    </ul>
                </div>
                </div>
        </div>
    </section>

    <footer class="bg-secondary text-secondary-foreground pt-16 pb-10">
        <div class="container mx-auto px-6">
            <div class="border-t border-white/5 pt-10 flex flex-col md:flex-row justify-between items-center gap-4 text-slate-500 text-xs">
                <p>© 2025 FCCEI. All rights reserved.</p>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center hover:bg-primary transition-colors cursor-pointer">FB</div>
                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center hover:bg-primary transition-colors cursor-pointer">TW</div>
                </div>
            </div>
        </div>
    </footer>

</body>
</html> -->


<!-- =========================================Ends here======================== -->


<!-- <!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FCCEI - Build Your Network, Multiply Your Success</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10b981',
                        'primary-foreground': '#ffffff',
                        emerald: { light: '#34d399', DEFAULT: '#059669', dark: '#064e3b' },
                        gold: { light: '#fbbf24', DEFAULT: '#f59e0b', dark: '#b45309' },
                        secondary: { DEFAULT: '#0f172a', foreground: '#f8fafc' },
                        background: '#f8fafc', // Soft light slate to reduce glare
                        foreground: '#1e293b', // Deep slate for better readability
                        muted: { DEFAULT: '#f1f5f9', foreground: '#475569' } // Darkened muted text
                    },
                    fontFamily: { display: ['Plus Jakarta Sans', 'sans-serif'], sans: ['Plus Jakarta Sans', 'sans-serif'] },
                }
            }
        }
    </script>

    <style>
        .gradient-text { background: linear-gradient(to right, #10b981, #f59e0b); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .hero-gradient { background: radial-gradient(circle at top center, #1e293b 0%, #0f172a 100%); }
        .mesh-gradient { background-image: radial-gradient(at 50% 50%, rgba(16, 185, 129, 0.1) 0, transparent 50%), radial-gradient(at 100% 0%, rgba(245, 158, 11, 0.05) 0, transparent 50%); }
        .glass-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .glow-emerald { box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); }

        /* New card style to make cards pop against the off-white background */
        .card-surface { @apply bg-white border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-300; }

        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-20px); } }
        .animate-float { animation: float 6s ease-in-out infinite; }
        .animate-float-delayed { animation: float 6s ease-in-out infinite; animation-delay: 3s; }
    </style>
</head>
<body class="bg-background text-foreground selection:bg-emerald/20">

    <nav x-data="{ isOpen: false }" class="fixed top-0 left-0 right-0 z-50 bg-background/90 backdrop-blur-xl border-b border-slate-200/60">
        <div class="container mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center">
                    <span class="text-primary-foreground font-bold text-xl">F</span>
                </div>
                <span class="font-bold text-xl tracking-tight text-slate-900">FCCEI</span>
            </div>
            <div class="hidden lg:flex items-center gap-8 text-slate-600 font-medium">
                <a href="#features" class="hover:text-primary transition-colors">Features</a>
                <a href="#how-it-works" class="hover:text-primary transition-colors">How It Works</a>
                <a href="#pricing" class="hover:text-primary transition-colors">Pricing</a>
                <a href="#faq" class="hover:text-primary transition-colors">FAQ</a>
            </div>
            <div class="hidden lg:flex items-center gap-4">
                <button class="px-5 py-2 text-sm font-semibold text-slate-600 hover:text-primary transition-colors">Sign In</button>
                <button class="px-6 py-2.5 bg-primary hover:bg-emerald text-primary-foreground rounded-full text-sm font-bold shadow-lg shadow-primary/20 transition-all">Get Started</button>
            </div>
            <button @click="isOpen = !isOpen" class="lg:hidden p-2 text-slate-900">
                <svg x-show="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                <svg x-show="isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </nav>

    <section class="relative min-h-screen hero-gradient overflow-hidden flex items-center">
        <div class="absolute inset-0 mesh-gradient opacity-50"></div>
        <div class="absolute top-20 left-10 w-72 h-72 bg-emerald/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 right-10 w-96 h-96 bg-gold/10 rounded-full blur-3xl animate-float-delayed"></div>

        <div class="container mx-auto px-6 relative pt-20">
            <div class="max-w-5xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald/10 border border-emerald/20 text-emerald-light text-sm font-medium mb-8">
                    <span class="flex h-2 w-2 rounded-full bg-emerald animate-pulse"></span>
                    Join 10,000+ members growing together
                </div>
                <h1 class="text-4xl lg:text-7xl font-bold text-white leading-tight mb-6">
                    Build Your Network,<br><span class="gradient-text">Multiply Your Success</span>
                </h1>
                <p class="text-lg lg:text-xl text-slate-300 max-w-2xl mx-auto mb-10">
                    FCCEI empowers you to grow through community. Earn rewards by sharing value, building connections, and helping others succeed in our multi-level referral ecosystem.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                    <button class="w-full sm:w-auto px-8 py-4 bg-primary hover:bg-emerald text-primary-foreground rounded-2xl font-bold text-lg flex items-center justify-center gap-2 transition-all hover:scale-105 shadow-xl shadow-primary/20">
                        Start Earning Today
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                    <button class="w-full sm:w-auto px-8 py-4 bg-white/5 hover:bg-white/10 text-primary-foreground border border-white/10 rounded-2xl font-bold text-lg flex items-center justify-center gap-2 transition-all">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"></path></svg>
                        Watch Demo
                    </button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-3xl mx-auto">
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex items-center justify-center gap-3 mb-2">
                            <div class="p-2 rounded-lg bg-emerald/20 text-emerald"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                            <span class="text-3xl font-bold text-white">10K+</span>
                        </div>
                        <p class="text-slate-400 text-sm">Active Members</p>
                    </div>
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex items-center justify-center gap-3 mb-2">
                            <div class="p-2 rounded-lg bg-gold/20 text-gold"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg></div>
                            <span class="text-3xl font-bold text-white">₱50M+</span>
                        </div>
                        <p class="text-slate-400 text-sm">Rewards Distributed</p>
                    </div>
                    <div class="glass-card rounded-2xl p-6">
                        <div class="flex items-center justify-center gap-3 mb-2">
                            <div class="p-2 rounded-lg bg-emerald/20 text-emerald"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04"></path></svg></div>
                            <span class="text-3xl font-bold text-white">100%</span>
                        </div>
                        <p class="text-slate-400 text-sm">Secure & Transparent</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-background to-transparent"></div>
    </section>

    <section id="features" class="py-24 bg-background relative">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4 inline-block">Features</span>
                <h2 class="text-4xl lg:text-5xl font-bold mb-6 text-slate-900">Everything You Need to <span class="gradient-text">Succeed</span></h2>
                <p class="text-slate-600 text-lg">Our platform is designed with your success in mind. From powerful referral tracking to instant payouts.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                @php
                    $feats = [
                        ['t' => 'Multi-Level Referrals', 'd' => 'Earn commissions up to 5 levels deep. Your network grows exponentially.', 'g' => 'from-emerald to-emerald-light'],
                        ['t' => 'Instant Payouts', 'd' => 'Get your earnings directly to your wallet. No waiting periods, no hidden fees.', 'g' => 'from-gold to-gold-light'],
                        ['t' => 'Community First', 'd' => 'Join a supportive community of like-minded individuals working towards freedom.', 'g' => 'from-emerald to-emerald-light'],
                        ['t' => 'Rank Rewards', 'd' => 'Climb the ranks and unlock exclusive bonuses and recognition.', 'g' => 'from-gold to-gold-light'],
                        ['t' => 'Smart Dashboard', 'd' => 'Track your network, earnings, and growth with real-time analytics.', 'g' => 'from-emerald to-emerald-light'],
                        ['t' => 'Secure & Compliant', 'd' => 'Built on robust technology with full regulatory compliance.', 'g' => 'from-gold to-gold-light']
                    ];
                @endphp
                @foreach($feats as $f)
                <div class="p-8 rounded-3xl card-surface group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $f['g'] }} flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                         <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 text-slate-900">{{ $f['t'] }}</h3>
                    <p class="text-slate-600">{{ $f['d'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="how-it-works" class="py-24 bg-slate-100/50">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4 inline-block">How It Works</span>
                <h2 class="text-4xl font-bold mb-4 text-slate-900">Simple Steps to <span class="gradient-text">Financial Growth</span></h2>
            </div>
            <div class="max-w-5xl mx-auto relative">
                <div class="absolute left-8 lg:left-1/2 top-0 bottom-0 w-px bg-gradient-to-b from-primary/50 via-primary to-gold/50 hidden md:block lg:-translate-x-px"></div>
                @php
                    $steps = [
                        ['n' => '01', 't' => 'Create Your Account', 'd' => 'Sign up in minutes and choose your subscription plan.'],
                        ['n' => '02', 't' => 'Share & Invite', 'd' => 'Share your referral link with friends, family, and your network.'],
                        ['n' => '03', 't' => 'Watch Your Network Grow', 'd' => 'As your referrals join, your network expands through multiple levels.'],
                        ['n' => '04', 't' => 'Earn Rewards', 'd' => 'Receive commissions from your entire network. More levels, more earnings.']
                    ];
                @endphp
                @foreach($steps as $i => $s)
                <div class="relative flex flex-col lg:flex-row items-start lg:items-center gap-8 mb-16 last:mb-0 {{ $i % 2 === 1 ? 'lg:flex-row-reverse' : '' }}">
                    <div class="flex-1 {{ $i % 2 === 1 ? 'lg:text-right' : '' }}">
                        <div class="bg-white rounded-3xl p-8 border border-slate-200/60 shadow-sm hover:shadow-md transition-all {{ $i % 2 === 1 ? 'lg:ml-auto' : '' }} max-w-md">
                            <span class="text-5xl font-bold text-primary/10 mb-4 block">{{ $s['n'] }}</span>
                            <h3 class="text-2xl font-bold mb-3 text-slate-900">{{ $s['t'] }}</h3>
                            <p class="text-slate-600 leading-relaxed">{{ $s['d'] }}</p>
                        </div>
                    </div>
                    <div class="absolute left-0 lg:left-1/2 lg:-translate-x-1/2 flex items-center justify-center z-10">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary to-emerald-light flex items-center justify-center shadow-lg text-white">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                    <div class="flex-1 hidden lg:block"></div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="pricing" class="py-24 bg-background">
        <div class="container mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20">
                <span class="px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4 inline-block">Pricing</span>
                <h2 class="text-4xl font-bold mb-4 text-slate-900">Choose Your Path to <span class="gradient-text">Success</span></h2>
            </div>
            <div class="grid lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <div class="p-8 rounded-[2.5rem] bg-white border border-slate-200 hover:shadow-xl transition-all relative">
                    <h3 class="text-xl font-bold mb-2 text-slate-900">Starter</h3>
                    <div class="text-4xl font-bold mb-4">₱499<span class="text-lg text-slate-500 font-normal">/mo</span></div>
                    <p class="text-slate-600 mb-8">Perfect for beginners ready to start.</p>
                    <button class="w-full py-4 border border-primary text-primary font-bold rounded-2xl hover:bg-primary/5 transition-all mb-8">Choose Starter</button>
                    <ul class="space-y-4 text-sm text-slate-600">
                        <li class="flex items-center gap-3 font-medium"><svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> 2-Level Referral Earnings</li>
                        <li class="flex items-center gap-3 font-medium"><svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> Community Access</li>
                    </ul>
                </div>
                <div class="p-8 rounded-[2.5rem] bg-white border-2 border-primary shadow-2xl relative lg:scale-105 z-10">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 px-4 py-1 bg-primary text-white text-xs font-bold rounded-full uppercase tracking-widest">Most Popular</div>
                    <h3 class="text-xl font-bold mb-2 text-slate-900">Pro</h3>
                    <div class="text-4xl font-bold mb-4 text-primary">₱999<span class="text-lg text-slate-500 font-normal">/mo</span></div>
                    <p class="text-slate-600 mb-8">For serious networkers ready to scale.</p>
                    <button class="w-full py-4 bg-primary text-white font-bold rounded-2xl hover:bg-emerald transition-all mb-8 shadow-xl shadow-primary/20">Choose Pro</button>
                    <ul class="space-y-4 text-sm font-medium text-slate-800">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> 4-Level Referral Earnings</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> Team Management</li>
                    </ul>
                </div>
                <div class="p-8 rounded-[2.5rem] bg-white border border-slate-200 hover:shadow-xl transition-all relative">
                    <h3 class="text-xl font-bold mb-2 text-slate-900">Elite</h3>
                    <div class="text-4xl font-bold mb-4 text-gold-dark">₱2,499<span class="text-lg text-slate-500 font-normal">/mo</span></div>
                    <p class="text-slate-600 mb-8">For leaders building empires.</p>
                    <button class="w-full py-4 bg-gold text-white font-bold rounded-2xl hover:bg-gold-dark transition-all mb-8 shadow-lg shadow-gold/20">Choose Elite</button>
                    <ul class="space-y-4 text-sm text-slate-600">
                        <li class="flex items-center gap-3 font-semibold text-slate-900"><svg class="w-5 h-5 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> 5-Level Referral Earnings</li>
                        <li class="flex items-center gap-3 font-semibold text-slate-900"><svg class="w-5 h-5 text-gold" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"></path></svg> 1-on-1 Coaching</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="py-24 bg-slate-100/50">
        <div class="container mx-auto px-6 max-w-3xl">
            <div class="text-center mb-16">
                <span class="px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-semibold mb-4 inline-block">FAQ</span>
                <h2 class="text-4xl font-bold mb-4 text-slate-900">Frequently Asked <span class="gradient-text">Questions</span></h2>
            </div>
            <div x-data="{ active: null }" class="space-y-4">
                @php
                    $faqs = [
                        ['q' => "What is FCCEI and how does it work?", 'a' => "FCCEI is a multi-level referral-based subscription platform that rewards you for building a network. You earn commissions from subscriptions up to multiple levels deep."],
                        ['q' => "How much can I really earn?", 'a' => "Earnings depend on your tier. Starter members typically earn ₱2,000-10,000/month, while Elite members can exceed ₱200,000+ monthly."],
                        ['q' => "Is this legal and legitimate?", 'a' => "Absolutely. Unlike illegal schemes, our platform provides real value through tools and community access. Compensation is based on subscription sales."],
                        ['q' => "How do I get paid?", 'a' => "We support GCash, bank transfer, and e-wallets. Payouts are processed weekly and trackable via your dashboard."]
                    ];
                @endphp
                @foreach($faqs as $i => $faq)
                <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden shadow-sm">
                    <button @click="active = (active === {{ $i }} ? null : {{ $i }})" class="w-full p-6 text-left flex justify-between items-center group">
                        <span class="font-bold text-slate-800 group-hover:text-primary transition-colors">{{ $faq['q'] }}</span>
                        <svg class="w-5 h-5 transition-transform text-slate-400" :class="active === {{ $i }} ? 'rotate-180 text-primary' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="active === {{ $i }}" x-collapse class="px-6 pb-6 text-slate-600 text-sm leading-relaxed">
                        {{ $faq['a'] }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-24 lg:py-32 hero-gradient relative overflow-hidden">
        <div class="absolute inset-0 mesh-gradient opacity-50"></div>
        <div class="container mx-auto px-6 relative">
            <div class="max-w-4xl mx-auto text-center">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald/10 border border-emerald/20 text-emerald-light text-sm font-medium mb-8">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l2.4 7.2h7.6l-6 4.8 2.4 7.2-6-4.8-6 4.8 2.4-7.2-6-4.8h7.6z"></path></svg>
                    Limited Time: First Month 50% Off
                </div>
                <h2 class="text-3xl lg:text-6xl font-bold text-white mb-6">
                    Ready to Transform <span class="gradient-text">Your Future?</span>
                </h2>
                <p class="text-lg text-slate-300 max-w-2xl mx-auto mb-10">
                    Join thousands of Filipinos who are already building their financial freedom with FCCEI. Your network is waiting. Your success story starts today.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <button class="w-full sm:w-auto px-10 py-5 bg-primary hover:bg-emerald text-white font-bold rounded-2xl flex items-center justify-center gap-2 transition-all shadow-xl shadow-primary/20">
                        Start Your Journey
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                    <button class="w-full sm:w-auto px-10 py-5 bg-white/5 hover:bg-white/10 text-white border border-white/10 font-bold rounded-2xl flex items-center justify-center">
                        Talk to an Advisor
                    </button>
                </div>
                <p class="mt-8 text-slate-500 text-sm italic">
                    No credit card required • Cancel anytime • 30-day money-back guarantee
                </p>
            </div>
        </div>
    </section>

    <footer class="bg-secondary text-secondary-foreground pt-16 pb-10">
        <div class="container mx-auto px-6">
            <div class="grid lg:grid-cols-5 gap-12 mb-16">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center font-bold text-xl">F</div>
                        <span class="font-bold text-xl">FCCEI</span>
                    </div>
                    <p class="text-slate-400 max-w-sm mb-6 italic">Empowering Filipinos to achieve financial freedom through community-driven referral networks.</p>
                    <div class="space-y-3 text-slate-400 text-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            support@fccei.com
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Product</h4>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li><a href="#features" class="hover:text-primary transition-colors">Features</a></li>
                        <li><a href="#pricing" class="hover:text-primary transition-colors">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Company</h4>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li><a href="#" class="hover:text-primary transition-colors">About Us</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-bold mb-4">Legal</h4>
                    <ul class="space-y-2 text-slate-400 text-sm">
                        <li><a href="#" class="hover:text-primary transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-white/5 pt-10 flex flex-col md:flex-row justify-between items-center gap-4 text-slate-500 text-xs">
                <p>© 2025 FCCEI. All rights reserved.</p>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center hover:bg-primary transition-colors cursor-pointer font-bold">FB</div>
                    <div class="w-8 h-8 rounded bg-white/5 flex items-center justify-center hover:bg-primary transition-colors cursor-pointer font-bold">TW</div>
                </div>
            </div>
        </div>
    </footer>

</body>
</html> -->



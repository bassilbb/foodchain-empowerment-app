<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - FoodChain</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }
        .sidebar { 
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .sidebar-link { 
            transition: all 0.2s ease; 
        }
        .sidebar-link:hover { 
            transform: translateX(6px); 
        }
        .sidebar-link.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35);
        }
        .sidebar-link.active svg {
            color: white;
        }
        .card-3d {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 2px 8px -2px rgba(0,0,0,0.06), 0 8px 24px -8px rgba(0,0,0,0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-3d:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px -12px rgba(0,0,0,0.15);
        }
        .header-blur {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="fixed left-0 top-0 h-full w-72 sidebar border-r border-slate-200/60 shadow-xl z-50">
            <!-- Logo -->
            <div class="p-6 border-b border-slate-200/60">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 flex items-center justify-center shadow-lg shadow-primary-500/30">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-primary-600 to-accent-500 bg-clip-text text-transparent">FoodChain</h1>
                        <p class="text-xs text-slate-500 font-medium">Member Portal</p>
                    </div>
                </div>
            </div>

            <!-- User Info Card -->
            <div class="p-4 border-b border-slate-200/60">
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-primary-400 to-accent-400 flex items-center justify-center text-white font-bold text-lg shadow-md">
                        {{ substr(auth()->user()->first_name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-slate-800 truncate">{{ auth()->user()->full_name ?? 'User' }}</p>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ tier_badge_color(auth()->user()->currentTier->slug ?? 'bronze') }} text-white">
                                {{ auth()->user()->currentTier->name ?? 'Bronze' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-250px)]">
                <a href="{{ route('dashboard') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('dashboard') ? 'active' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                <div class="pt-2">
                    <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">My Account</p>
                </div>

                <a href="{{ route('profile.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('profile.*') ? 'active' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    <span class="font-medium">Profile</span>
                </a>

                <a href="{{ route('referrals.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('referrals.*') ? 'active' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <span class="font-medium">My Referrals</span>
                    @if(auth()->user()->direct_referrals_count > 0)
                    <span class="ml-auto px-2 py-0.5 rounded-full text-xs font-medium bg-primary-100 text-primary-600">
                        {{ auth()->user()->direct_referrals_count }}
                    </span>
                    @endif
                </a>

                <a href="{{ route('payments.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('payments.*') ? 'active' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    <span class="font-medium">Payments</span>
                </a>

                <a href="{{ route('manual-payments.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('manual-payments.*') ? 'active' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span class="font-medium">Manual Payments</span>
                </a>

                <a href="{{ route('distributions.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('distributions.*') ? 'active' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    <span class="font-medium">My Distributions</span>
                </a>

                <div class="pt-2">
                    <p class="px-4 text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Support</p>
                </div>

                <a href="{{ route('support.index') }}" class="sidebar-link flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('support.*') ? 'active' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="font-medium">Support</span>
                </a>
            </nav>

            <!-- Membership Card -->
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-200/60">
                <div class="p-4 rounded-2xl bg-gradient-to-br from-primary-500 to-accent-500 text-white shadow-lg">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium opacity-90">Current Tier</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-white/20">
                            {{ auth()->user()->currentTier->name ?? 'Bronze' }}
                        </span>
                    </div>
                    <p class="text-xs opacity-75 mb-3">Build your community to unlock more benefits!</p>
                    <div class="w-full bg-white/20 rounded-full h-2">
                        <div class="bg-white h-2 rounded-full" style="width: {{ min((auth()->user()->direct_referrals_count ?? 0) * 2, 100) }}%"></div>
                    </div>
                    <p class="text-xs mt-1 opacity-75">{{ auth()->user()->direct_referrals_count ?? 0 }} / 50 to next tier</p>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="ml-72 flex-1">
            <!-- Top Header -->
            <header class="header-blur border-b border-slate-200/60 sticky top-0 z-40">
                <div class="flex items-center justify-between px-8 py-4">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">@yield('header', 'Dashboard')</h2>
                        <p class="text-sm text-slate-500">@yield('subheader', 'Welcome back!')</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <!-- Notifications -->
                        <button class="relative p-2.5 rounded-xl bg-slate-100 text-slate-600 hover:text-primary-600 hover:bg-primary-50 transition-colors shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                            @endif
                        </button>
                        
                        <!-- User Menu -->
                        <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                            <div class="text-right">
                                <p class="text-sm font-semibold text-slate-800">{{ auth()->user()->full_name ?? 'User' }}</p>
                                <p class="text-xs text-slate-500">{{ auth()->user()->email ?? '' }}</p>
                            </div>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-400 to-accent-400 flex items-center justify-center text-white font-bold shadow-md">
                                {{ substr(auth()->user()->first_name ?? 'U', 0, 1) }}
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-8">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>

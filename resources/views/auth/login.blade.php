<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FoodChain</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
            min-height: 100vh;
        }
        .glass-effect { 
            background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px); 
        }
        .input-glow:focus { 
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2), 0 0 15px rgba(16, 185, 129, 0.1); 
            border-color: #10b981;
        }
        .card-3d { 
            transform-style: preserve-3d; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        .card-3d:hover { 
            transform: translateY(-8px);
            box-shadow: 0 25px 60px -20px rgba(0,0,0,0.2);
        }
        .floating-food { position: absolute; animation: floatFood 10s ease-in-out infinite; }
        .floating-food:nth-child(1) { top: 15%; left: 10%; animation-delay: 0s; }
        .floating-food:nth-child(2) { top: 25%; right: 15%; animation-delay: 2s; }
        .floating-food:nth-child(3) { bottom: 20%; left: 20%; animation-delay: 4s; }
        .floating-food:nth-child(4) { bottom: 30%; right: 10%; animation-delay: 6s; }
        @keyframes floatFood { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-20px) rotate(5deg); } }
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px -5px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-white to-slate-100 relative overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="floating-food text-4xl opacity-20">🍎</div>
        <div class="floating-food text-3xl opacity-15">🥦</div>
        <div class="floating-food text-5xl opacity-20">🍊</div>
        <div class="floating-food text-3xl opacity-15">🥕</div>
    </div>
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-primary-200 rounded-full blur-[100px] opacity-40"></div>
        <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-accent-200 rounded-full blur-[100px] opacity-40"></div>
    </div>
    <div class="relative z-10 min-h-screen flex">
        <div class="hidden lg:flex lg:w-1/2 relative items-center justify-center p-12">
            <div class="max-w-lg text-center">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-gradient-to-br from-primary-400 to-primary-600 shadow-lg mb-8 animate-float">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h1 class="text-5xl font-extrabold text-slate-800 mb-4">
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-accent-500">FoodChain</span>
                </h1>
                <p class="text-xl text-slate-600 mb-8">Join our community and get access to quality food packages</p>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/80 shadow-lg">
                        <div class="w-12 h-12 rounded-xl bg-primary-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-semibold text-slate-800">Secure Membership</h3>
                            <p class="text-sm text-slate-500">Safe and secure payments</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/80 shadow-lg">
                        <div class="w-12 h-12 rounded-xl bg-accent-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-semibold text-slate-800">Community Benefits</h3>
                            <p class="text-sm text-slate-500">Build network & earn rewards</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/80 shadow-lg">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <div class="text-left">
                            <h3 class="font-semibold text-slate-800">Quarterly Food Packs</h3>
                            <p class="text-sm text-slate-500">Free food based on community</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="w-full max-w-md">
                <div class="lg:hidden text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 shadow-lg mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                </div>
                <div class="bg-white rounded-3xl shadow-2xl border border-slate-200/60 p-8 card-3d">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold text-slate-800">Welcome Back</h2>
                        <p class="text-slate-500 mt-2">Sign in to your account</p>
                    </div>
                    @if($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>@foreach($errors->all() as $error)<p class="text-sm text-red-600">{{ $error }}</p>@endforeach</div>
                            </div>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-semibold text-slate-700">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary-500 focus:bg-white transition-all input-glow">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                                <input type="password" name="password" id="password" required placeholder="Enter your password" class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:border-primary-500 focus:bg-white transition-all input-glow">
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="remember" class="w-5 h-5 rounded border-slate-300 bg-white text-primary-500 focus:ring-primary-500">
                                <span class="ml-3 text-sm text-slate-600">Remember me</span>
                            </label>
                            <a href="#" class="text-sm font-medium text-primary-600 hover:text-primary-700">Forgot password?</a>
                        </div>
                        <button type="submit" class="w-full py-4 px-6 rounded-xl btn-primary text-white font-semibold text-lg shadow-lg">
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                Sign In
                            </span>
                        </button>
                    </form>
                    @if(Route::has('register'))
                    <div class="mt-8 pt-6 border-t border-slate-100">
                        <p class="text-center text-slate-600">Don't have an account? <a href="{{ route('register') }}" class="font-semibold text-primary-600 hover:text-primary-700">Sign up now</a></p>
                    </div>
                    @endif
                </div>
                <div class="mt-6 text-center">
                    <a href="/" class="inline-flex items-center gap-2 text-slate-500 hover:text-primary-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

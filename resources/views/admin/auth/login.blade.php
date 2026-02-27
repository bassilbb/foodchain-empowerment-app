<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - FoodChain</title>
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
        .login-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 2px 8px -2px rgba(0,0,0,0.06), 0 20px 50px -15px rgba(0,0,0,0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .login-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 60px -20px rgba(0,0,0,0.2);
        }
        .input-glow:focus { 
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2), 0 0 20px rgba(16, 185, 129, 0.1); 
            border-color: #10b981;
        }
        .floating-shapes { position: absolute; width: 100%; height: 100%; overflow: hidden; z-index: 0; }
        .shape { position: absolute; border-radius: 50%; animation: float 8s ease-in-out infinite; }
        .shape-1 { width: 300px; height: 300px; background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), transparent); top: -100px; right: -50px; }
        .shape-2 { width: 200px; height: 200px; background: linear-gradient(135deg, rgba(14, 165, 233, 0.15), transparent); bottom: -50px; left: -50px; animation-delay: 2s; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-30px); } }
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px -5px rgba(16, 185, 129, 0.4);
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
    </div>
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-primary-500/10 rounded-full blur-[128px]"></div>
    <div class="absolute bottom-1/4 right-1/4 w-80 h-80 bg-accent-500/10 rounded-full blur-[100px]"></div>
    
    <div class="relative z-10 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600 mb-4 shadow-lg shadow-primary-500/30">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h1 class="text-4xl font-extrabold text-slate-800">FoodChain</h1>
            <p class="text-slate-500 mt-2">Admin Portal</p>
        </div>
        
        <div class="login-card rounded-2xl shadow-2xl border border-slate-200/60 p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-slate-800">Welcome Back</h2>
                <p class="text-slate-500 mt-2">Sign in to your admin account</p>
            </div>
            
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>@foreach($errors->all() as $error)<p class="text-sm text-red-600">{{ $error }}</p>@endforeach</div>
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-5">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        </div>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="admin@foodchain.com" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white transition-all input-glow">
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input type="password" name="password" id="password" required placeholder="Enter your password" class="w-full pl-12 pr-4 py-3.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:bg-white transition-all input-glow">
                    </div>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 bg-slate-50 text-primary-500 focus:ring-primary-500">
                    <span class="ml-3 text-sm text-slate-600">Remember me</span>
                </div>
                <button type="submit" class="w-full py-3.5 px-6 rounded-xl btn-primary text-white font-semibold shadow-lg">
                    Sign In
                </button>
            </form>
            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <a href="/" class="text-slate-500 hover:text-primary-600 transition-colors">← Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html>

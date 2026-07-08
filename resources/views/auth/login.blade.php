<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - {{ app_name() }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            min-height: 100vh;
        }
        
        /* Background Effects */
        .bg-effects {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            overflow: hidden;
            z-index: 0;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
        }
        .orb-1 { width: 400px; height: 400px; background: rgba(16, 185, 129, 0.25); top: -150px; right: -100px; }
        .orb-2 { width: 500px; height: 500px; background: rgba(14, 165, 233, 0.15); bottom: -150px; left: -100px; }
        .float-icon { position: absolute; font-size: 40px; opacity: 0.15; animation: float 8s ease-in-out infinite; }
        .float-1 { top: 15%; left: 8%; animation-delay: 0s; }
        .float-2 { top: 25%; right: 10%; animation-delay: 2s; }
        .float-3 { bottom: 20%; left: 15%; animation-delay: 4s; }
        .float-4 { bottom: 30%; right: 8%; animation-delay: 6s; }
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-25px) rotate(5deg); } }
        
        /* Main Layout */
        .main-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
        }
        
        /* Brand Panel (Left) */
        .brand-panel {
            display: none;
            width: 50%;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        @media (min-width: 1024px) {
            .brand-panel { display: flex; }
        }
        .brand-content {
            max-width: 480px;
            text-align: center;
        }
        .brand-logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 32px;
            box-shadow: 0 25px 50px rgba(16, 185, 129, 0.35);
        }
        .brand-logo svg { width: 48px; height: 48px; color: white; }
        .brand-title {
            font-size: 52px;
            font-weight: 800;
            color: #111827;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #10b981 0%, #0ea5e9 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .brand-subtitle { font-size: 20px; color: #6b7280; margin-bottom: 40px; }
        
        .feature-item {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
            text-align: left;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .feature-icon svg { width: 24px; height: 24px; }
        .feature-title { font-weight: 600; color: #111827; font-size: 16px; }
        .feature-desc { font-size: 14px; color: #6b7280; }
        
        /* Form Panel (Right) */
        .form-panel {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }
        @media (min-width: 1024px) {
            .form-panel { width: 50%; }
        }
        .login-card { 
            background: white; 
            border-radius: 28px; 
            box-shadow: 0 25px 60px -20px rgba(0,0,0,0.15);
            padding: 48px;
            width: 100%;
            max-width: 440px;
        }
        .login-header { text-align: center; margin-bottom: 36px; }
        .login-header h1 { font-size: 30px; font-weight: 700; color: #111827; margin-bottom: 8px; }
        .login-header p { color: #6b7280; font-size: 15px; }
        
        .form-group { margin-bottom: 24px; }
        .form-group label { display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px; }
        .form-group input { 
            width: 100%; 
            padding: 14px 18px; 
            border: 1px solid #e5e7eb; 
            border-radius: 12px; 
            font-size: 15px; 
            transition: all 0.2s; 
        }
        .form-group input:focus { 
            outline: none; 
            border-color: #10b981; 
            box-shadow: 0 0 0 4px rgba(16,185,129,0.1); 
        }
        .form-group input::placeholder { color: #9ca3af; }
        
        .checkbox-row { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            margin-bottom: 28px; 
        }
        .checkbox-row label { 
            display: flex; 
            align-items: center; 
            gap: 10px; 
            font-size: 14px; 
            color: #6b7280; 
            cursor: pointer; 
        }
        .checkbox-row input[type="checkbox"] { width: 18px; height: 18px; accent-color: #10b981; }
        .checkbox-row a { font-size: 14px; color: #10b981; text-decoration: none; font-weight: 500; }
        .checkbox-row a:hover { text-decoration: underline; }
        
        .btn-primary { 
            width: 100%; 
            padding: 16px; 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            color: white; 
            border: none; 
            border-radius: 12px; 
            font-size: 16px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.2s; 
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 15px 30px rgba(16,185,129,0.35); 
        }
        
        .form-footer { text-align: center; margin-top: 28px; padding-top: 24px; border-top: 1px solid #f3f4f6; }
        .form-footer p { font-size: 14px; color: #6b7280; }
        .form-footer a { color: #10b981; font-weight: 600; text-decoration: none; }
        .form-footer a:hover { text-decoration: underline; }
        
        .alert { padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .back-link { text-align: center; margin-top: 24px; }
        .back-link a { color: #6b7280; text-decoration: none; font-size: 14px; }
        .back-link a:hover { color: #10b981; }

        @media (max-width: 1023px) {
            .login-card { padding: 36px 28px; }
            .brand-title { font-size: 42px; }
        }
        @media (max-width: 480px) {
            .login-card { padding: 28px 20px; }
            .login-header h1 { font-size: 26px; }
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="bg-effects">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="float-icon float-1">🍎</div>
        <div class="float-icon float-2">🥦</div>
        <div class="float-icon float-3">🍊</div>
        <div class="float-icon float-4">🥕</div>
    </div>

    <div class="main-wrapper">
        <!-- Brand Panel -->
        <div class="brand-panel">
            <div class="brand-content">
                <div class="brand-logo">
                    @if(site_logo())
                    <img src="{{ site_logo() }}" alt="{{ app_name() }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 28px;">
                    @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    @endif
                </div>
                <h1 class="brand-title">{{ app_name() }}</h1>
                <p class="brand-subtitle">Join our community and get access to quality food packages</p>
                
                <div class="feature-item">
                    <div class="feature-icon bg-emerald-100">
                        <svg class="text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <div>
                        <div class="feature-title">Secure Membership</div>
                        <div class="feature-desc">Safe and secure payments</div>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon bg-sky-100">
                        <svg class="text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <div class="feature-title">Community Benefits</div>
                        <div class="feature-desc">Build network & earn rewards</div>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon bg-amber-100">
                        <svg class="text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <div class="feature-title">Quarterly Food Packs</div>
                        <div class="feature-desc">Free food based on community</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Panel -->
        <div class="form-panel">
        <div class="login-card">
            <div class="login-header">
                <div class="form-logo">
                    @if(site_logo())
                    <img src="{{ site_logo() }}" alt="{{ app_name() }}" style="height: 48px; width: auto; margin-bottom: 16px;">
                    @endif
                </div>
                <h1>Welcome Back</h1>
                <p>Sign in to your {{ app_name() }} account</p>
            </div>

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="you@example.com">
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" required placeholder="Enter your password">
                    </div>

                    <div class="checkbox-row">
                        <label>
                            <input type="checkbox" name="remember">
                            Remember me
                        </label>
                        <a href="#">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-primary">Sign In</button>
                </form>

                <div class="form-footer">
                    <p>Don't have an account? <a href="{{ route('register') }}">Sign up now</a></p>
                </div>

                <div class="back-link">
                    <a href="/">← Back to Home</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

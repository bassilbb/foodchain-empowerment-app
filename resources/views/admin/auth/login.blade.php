<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - {{ app_name() }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <link rel="icon" type="image/x-icon" href="{{ site_favicon() ?? '/favicon.ico' }}">
    <link rel="apple-touch-icon" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.vite-css')
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        ::selection { background: #a7f3d0; color: #064e3b; }
        body {
            font-family: 'Inter', sans-serif; 
            background: radial-gradient(circle at 82% 8%, rgba(16,185,129,.22), transparent 30%), radial-gradient(circle at 12% 92%, rgba(14,165,233,.16), transparent 32%), #f5faf7;
            min-height: 100vh;
            display: block;
            padding: 2rem 1rem;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            font-feature-settings: "kern" 1, "liga" 1, "cv11" 1;
        }
        
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
            filter: blur(90px);
            opacity: .5;
            animation: orbFloat 14s ease-in-out infinite;
        }
        .orb-1 { width: 440px; height: 440px; background: rgba(16, 185, 129, 0.30); top: -150px; right: -100px; }
        .orb-2 { width: 520px; height: 520px; background: rgba(14, 165, 233, 0.18); bottom: -150px; left: -100px; animation-delay: 2.5s; }
        .orb-3 { width: 300px; height: 300px; background: rgba(45, 212, 191, 0.20); top: 45%; left: 42%; animation-delay: 5s; }
        @keyframes orbFloat {
            0%, 100% { transform: translate3d(0,0,0) scale(1); }
            50% { transform: translate3d(20px,-30px,0) scale(1.07); }
        }
        
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 72rem;
            margin: 0 auto;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 440px);
            align-items: center;
            gap: 3.5rem;
        }
        
        .logo-section {
            text-align: center;
            margin: 0;
            padding: 3rem;
            grid-column: 1;
            grid-row: 1;
            position: relative;
            z-index: 2;
        }
        .login-wrapper::before {
            display: none;
        }
        .logo-icon {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 160px;
            height: 101px;
            border-radius: 1.1rem;
            background: linear-gradient(135deg, #10b981, #059669);
            padding: 7px;
            margin-bottom: 1rem;
            box-shadow: 0 24px 44px -10px rgba(16, 185, 129, 0.5), 0 12px 22px rgba(4,47,37,.2), inset 0 2px 0 rgba(255,255,255,.4), inset 0 -3px 8px rgba(4,120,87,.28);
            transform: perspective(1100px) rotateX(9deg) rotateY(-15deg);
            transform-style: preserve-3d;
            transition: transform .55s cubic-bezier(.2,.8,.2,1), box-shadow .55s cubic-bezier(.2,.8,.2,1);
            animation: logoIconFloat 7s ease-in-out infinite;
        }
        .logo-icon::before {
            content: '';
            position: absolute;
            inset: -8px;
            border-radius: 1.5rem;
            background: radial-gradient(circle at 30% 25%, rgba(16,185,129,.32), transparent 62%);
            filter: blur(14px);
            transform: translateZ(-40px);
            pointer-events: none;
        }
        .logo-icon:hover {
            transform: perspective(1100px) rotateX(3deg) rotateY(-6deg);
            box-shadow: 0 34px 58px -14px rgba(16,185,129,.58), 0 18px 32px rgba(4,47,37,.3), inset 0 2px 0 rgba(255,255,255,.5);
        }
        .logo-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: .85rem;
            display: block;
            filter: drop-shadow(0 6px 12px rgba(4,47,37,.16)) contrast(1.03);
            transition: translate .45s cubic-bezier(.2,.8,.2,1);
        }
        .logo-icon:hover img { translate: 0 0 40px; }
        @keyframes logoIconFloat {
            0%, 100% { translate: 0 0 0; }
            50% { translate: 0 -6px 16px; }
        }
        .logo-icon svg { width: 40px; height: 40px; color: white; filter: drop-shadow(0 3px 6px rgba(0,0,0,.20)); }
        .logo-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1e293b;
        }
        .logo-subtitle {
            color: #64748b;
            margin-top: 0.5rem;
        }
        .admin-benefits { display: grid; gap: 0.8rem; max-width: 25rem; margin: 2rem auto 0; text-align: left; }
        .admin-benefit { display: flex; align-items: center; gap: 0.85rem; padding: 1rem; background: rgba(255,255,255,.82); border: 1px solid rgba(16,185,129,.12); border-radius: 1rem; box-shadow: 0 10px 24px rgba(4,47,37,.06), inset 0 1px 0 rgba(255,255,255,.6); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); transform: translateZ(0); transition: transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s cubic-bezier(.2,.8,.2,1); }
        .admin-benefit:hover { transform: translate3d(8px, -4px, 10px); box-shadow: 0 22px 44px rgba(4,47,37,.12), inset 0 1px 0 rgba(255,255,255,.6); }
        .admin-benefit-icon { width: 2.75rem; height: 2.75rem; border-radius: .85rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background: #ecfdf5; color: #059669; font-size: 1.25rem; transition: transform .3s cubic-bezier(.2,.8,.2,1); }
        .admin-benefit:hover .admin-benefit-icon { transform: scale(1.12) rotate(-6deg); }
        .admin-benefit-title { color: #1e293b; font-size: .9rem; font-weight: 700; }
        .admin-benefit-copy { color: #64748b; font-size: .78rem; margin-top: .15rem; }
        
        .login-card {
            background: white;
            border-radius: 1.25rem;
            box-shadow: 0 30px 70px -24px rgba(4,47,37,.28), 0 8px 24px rgba(4,47,37,.10), inset 0 1px 0 rgba(255,255,255,.9);
            border: 1px solid rgba(16,185,129,.12);
            padding: 48px;
            width: 100%;
            max-width: 440px;
            grid-column: 2;
            grid-row: 1;
            position: relative;
            transform-style: preserve-3d;
            transform: perspective(1200px) rotateY(-1.2deg);
            animation: admin-card-in .7s cubic-bezier(.2,.8,.2,1) both;
            transition: transform .25s ease-out, box-shadow .25s ease-out;
        }
        .login-card.is-tilting { transition: transform .08s linear, box-shadow .08s linear; will-change: transform; }
        .login-card::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            background: linear-gradient(130deg, rgba(16,185,129,.05), rgba(16,185,129,.16) 34%, rgba(14,165,233,.10) 66%, rgba(255,255,255,0) 100%);
            z-index: 0;
            pointer-events: none;
        }
        .login-card::after { content: ''; position: absolute; inset: 0; pointer-events: none; border-radius: inherit; background: linear-gradient(115deg, rgba(255,255,255,.42), transparent 28%, transparent 70%, rgba(16,185,129,.06)); z-index: 1; }
        .login-card > * { position: relative; z-index: 2; }
        @keyframes admin-card-in { from { opacity: 0; transform: perspective(1200px) translate3d(0, 24px, 0) rotateY(-5deg); } to { opacity: 1; transform: perspective(1200px) rotateY(-1.2deg); } }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
        }
        .login-header p {
            color: #64748b;
            margin-top: 0.5rem;
        }
        
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: #dc2626;
            font-size: 0.875rem;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }
        .input-wrapper {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            color: #94a3b8;
        }
        .form-input {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            color: #1e293b;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15);
            background-color: #fff;
        }
        
        .checkbox-row {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .checkbox-row input {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 1px solid #cbd5e1;
            accent-color: #10b981;
        }
        .checkbox-row span {
            margin-left: 0.75rem;
            font-size: 0.875rem;
            color: #64748b;
        }
        
        .btn-primary {
            width: 100%;
            padding: 14px 24px;
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            background-size: 200% 200%;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 8px 22px rgba(16,185,129,.28);
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            background-position: 100% 100%;
            box-shadow: 0 16px 32px rgba(16, 185, 129, 0.40), 0 0 24px rgba(16,185,129,.14) inset;
        }
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0; left: -120%;
            width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.35), transparent);
            transform: skewX(-20deg);
            animation: btnShine 3.2s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes btnShine { 0%, 55% { left: -120%; } 85%, 100% { left: 130%; } }
        
        .form-footer {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }
        .form-footer a {
            color: #64748b;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.2s;
        }
        .form-footer a:hover {
            color: #10b981;
        }
        @media (max-width: 900px) {
            body { padding: 1rem; }
            .login-wrapper { display: block; max-width: 28rem; }
            .login-wrapper::before { display: none; }
            .logo-section { padding: 0; margin-bottom: 2rem; }
            .logo-title { color: #1e293b; }
            .logo-subtitle { color: #64748b; }
            .login-card { transform: none; animation: none; }
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="bg-effects">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="login-wrapper">
        <div class="logo-section">
            <div class="logo-icon">
                <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 1rem; display: block;">
            </div>
            <h1 class="logo-title">{{ app_name() }}</h1>
            <p class="logo-subtitle">Join our community and get access to quality food packages</p>
            <div class="admin-benefits">
                <div class="admin-benefit">
                    <div class="admin-benefit-icon">&#128737;</div>
                    <div>
                        <div class="admin-benefit-title">Secure Membership</div>
                        <div class="admin-benefit-copy">Safe and secure payments</div>
                    </div>
                </div>
                <div class="admin-benefit">
                    <div class="admin-benefit-icon">&#128101;</div>
                    <div>
                        <div class="admin-benefit-title">Community Benefits</div>
                        <div class="admin-benefit-copy">Build network &amp; earn rewards</div>
                    </div>
                </div>
                <div class="admin-benefit">
                    <div class="admin-benefit-icon">&#128230;</div>
                    <div>
                        <div class="admin-benefit-title">Quarterly Food Packs</div>
                        <div class="admin-benefit-copy">Free food based on community</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="login-card">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your admin account</p>
            </div>
            
            @if($errors->any())
                <div class="alert-error">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            @foreach($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus placeholder="admin@foodchain.com" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        <input type="password" name="password" id="password" required placeholder="Enter your password" class="form-input">
                    </div>
                </div>
                <div class="checkbox-row">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Remember me</span>
                </div>
                <button type="submit" class="btn-primary">Sign In</button>
            </form>
            <div class="form-footer">
                <a href="/">← Back to Website</a>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var card = document.querySelector('.login-card');
            if (!card || !window.matchMedia('(pointer: fine)').matches) return;
            var enabled = true, maxTilt = 7;
            card.addEventListener('mouseenter', function() { card.classList.add('is-tilting'); });
            card.addEventListener('mouseleave', function() {
                card.classList.remove('is-tilting');
                card.style.transform = 'perspective(1200px) rotateY(-1.2deg)';
            });
            card.addEventListener('mousemove', function(e) {
                if (!enabled) return;
                var rect = card.getBoundingClientRect();
                var px = (e.clientX - rect.left) / rect.width;
                var py = (e.clientY - rect.top) / rect.height;
                var rotateY = -1.2 + (px - 0.5) * maxTilt * 2;
                var rotateX = (0.5 - py) * maxTilt * 2;
                card.style.transform = 'perspective(1200px) rotateY(' + rotateY.toFixed(2) + 'deg) rotateX(' + rotateX.toFixed(2) + 'deg)';
            });
            card.addEventListener('mousedown', function() {
                enabled = false;
                card.style.transform = 'perspective(1200px) rotateY(-1.2deg) scale(0.99)';
            });
            window.addEventListener('mouseup', function() { enabled = true; });
        })();
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - {{ app_name() }}</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
            font-feature-settings: "kern" 1, "liga" 1, "cv11" 1;
        }
        .bg-effects { position: fixed; inset: 0; overflow: hidden; z-index: 0; }
        .orb { position: absolute; border-radius: 50%; filter: blur(90px); opacity: .5; animation: orbFloat 14s ease-in-out infinite; }
        .orb-1 { width: 440px; height: 440px; background: rgba(16, 185, 129, 0.30); top: -150px; right: -100px; }
        .orb-2 { width: 520px; height: 520px; background: rgba(14, 165, 233, 0.18); bottom: -150px; left: -100px; animation-delay: 2.5s; }
        .orb-3 { width: 300px; height: 300px; background: rgba(45, 212, 191, 0.20); top: 45%; left: 42%; animation-delay: 5s; }
        @keyframes orbFloat { 0%, 100% { transform: translate3d(0,0,0) scale(1); } 50% { transform: translate3d(20px,-30px,0) scale(1.07); } }

        .auth-wrap { position: relative; z-index: 1; width: 100%; max-width: 460px; }
        .card {
            background: white;
            border-radius: 22px;
            border: 1px solid rgba(16,185,129,.12);
            box-shadow: 0 30px 70px -24px rgba(4,47,37,.28), 0 8px 24px rgba(4,47,37,.10), inset 0 1px 0 rgba(255,255,255,.9);
            padding: 48px;
            width: 100%;
            position: relative;
            transform-style: preserve-3d;
            transform: perspective(1200px) rotateY(-1.2deg);
            animation: auth-card-in .7s cubic-bezier(.2,.8,.2,1) both;
            transition: transform .25s ease-out, box-shadow .25s ease-out;
        }
        .card.is-tilting { transition: transform .08s linear, box-shadow .08s linear; will-change: transform; }
        .card::before { content: ''; position: absolute; inset: -1px; border-radius: inherit; background: linear-gradient(130deg, rgba(16,185,129,.05), rgba(16,185,129,.16) 34%, rgba(14,165,233,.10) 66%, rgba(255,255,255,0) 100%); z-index: 0; pointer-events: none; }
        .card::after { content: ''; position: absolute; inset: 0; pointer-events: none; border-radius: inherit; background: linear-gradient(115deg, rgba(255,255,255,.42), transparent 28%, transparent 70%, rgba(16,185,129,.06)); z-index: 1; }
        .card > * { position: relative; z-index: 2; }
        @keyframes auth-card-in { from { opacity: 0; transform: perspective(1200px) translate3d(0, 24px, 0) rotateY(-5deg); } to { opacity: 1; transform: perspective(1200px) rotateY(-1.2deg); } }

        .logo { text-align: center; margin-bottom: 24px; }
        .logo-img {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
            box-shadow: 0 15px 30px rgba(16,185,129,.3), 0 6px 14px rgba(4,47,37,.16), inset 0 2px 0 rgba(255,255,255,.35);
            transform: perspective(900px) rotateY(-8deg) rotateX(4deg);
            transition: transform .5s cubic-bezier(.2,.8,.2,1);
        }
        .logo-img:hover { transform: perspective(900px) rotateY(0deg) rotateX(0deg) translateZ(24px); }
        .logo-img svg { width: 36px; height: 36px; color: white; filter: drop-shadow(0 3px 6px rgba(0,0,0,.20)); }

        .header { text-align: center; margin-bottom: 16px; }
        .header h1 { font-size: 24px; font-weight: 700; color: #111827; margin-bottom: 8px; }
        .header p { color: #6b7280; font-size: 14px; line-height: 1.6; }

        .intro { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 12px; padding: 14px 18px; font-size: 13.5px; color: #1e40af; line-height: 1.6; margin-bottom: 20px; }

        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; line-height: 1.5; }

        .form-group { margin-bottom: 24px; }
        .form-group label { display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px; }
        .form-group input {
            width: 100%; padding: 14px 18px;
            border: 1px solid #e5e7eb; border-radius: 12px; font-size: 15px;
            transition: all 0.2s;
        }
        .form-group input:focus { outline: none; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16,185,129,0.1); }
        .form-group input::placeholder { color: #9ca3af; }
        .field-error { margin-top: 8px; font-size: 13px; color: #dc2626; }
        .field-error p { margin-bottom: 2px; }

        .btn-primary {
            width: 100%; padding: 16px;
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            background-size: 200% 200%;
            color: white; border: none; border-radius: 12px;
            font-size: 16px; font-weight: 600; cursor: pointer;
            transition: all 0.2s;
            position: relative; overflow: hidden;
            box-shadow: 0 8px 22px rgba(16,185,129,.30), 0 0 0 1px rgba(16,185,129,.10) inset;
        }
        .btn-primary:hover { transform: translateY(-2px); background-position: 100% 100%; box-shadow: 0 16px 32px rgba(16,185,129,.40), 0 0 24px rgba(16,185,129,.14) inset; }
        .btn-primary::before {
            content: ''; position: absolute; top: 0; left: -120%; width: 60%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.35), transparent);
            transform: skewX(-20deg); animation: btnShine 3.2s ease-in-out infinite; pointer-events: none;
        }
        @keyframes btnShine { 0%, 55% { left: -120%; } 85%, 100% { left: 130%; } }

        .back-link { text-align: center; margin-top: 24px; }
        .back-link a { color: #6b7280; text-decoration: none; font-size: 14px; transition: color .2s; }
        .back-link a:hover { color: #10b981; }

        @media (max-width: 480px) { .card { padding: 32px 24px; } }
        @media (prefers-reduced-motion: reduce) { .orb, .card, .btn-primary::before, .logo-img { animation: none !important; } }
    </style>
</head>
<body>
    <div class="bg-effects">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <div class="auth-wrap">
        <div class="card">
            <div class="logo">
                <div class="logo-img">
                    @if(site_logo())
                    <img src="{{ site_logo() }}" alt="{{ app_name() }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 18px;">
                    @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    @endif
                </div>
            </div>

            <div class="header">
                <h1>Forgot Your Password?</h1>
                <p>No problem. Just enter your email address and we'll send you a link to choose a new one.</p>
            </div>

            @if(session('status'))
            <div class="alert-success">
                {{ session('status') }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="email" placeholder="you@example.com">
                    @if($errors->get('email'))
                    <div class="field-error">
                        @foreach($errors->get('email') as $error)
                        <p>{{ $error }}</p>
                        @endforeach
                    </div>
                    @endif
                </div>

                <button type="submit" class="btn-primary">Email Password Reset Link</button>
            </form>

            <div class="back-link">
                <a href="{{ route('login') }}">← Back to Login</a>
            </div>
        </div>
    </div>

    <script>
        (function() {
            var card = document.querySelector('.card');
            if (!card || !window.matchMedia('(pointer: fine)').matches) return;
            var enabled = true, maxTilt = 7;
            card.addEventListener('mouseenter', function() { card.classList.add('is-tilting'); });
            card.addEventListener('mouseleave', function() { card.classList.remove('is-tilting'); card.style.transform = 'perspective(1200px) rotateY(-1.2deg)'; });
            card.addEventListener('mousemove', function(e) {
                if (!enabled) return;
                var rect = card.getBoundingClientRect();
                var px = (e.clientX - rect.left) / rect.width;
                var py = (e.clientY - rect.top) / rect.height;
                card.style.transform = 'perspective(1200px) rotateY(' + (-1.2 + (px - 0.5) * maxTilt * 2).toFixed(2) + 'deg) rotateX(' + ((0.5 - py) * maxTilt * 2).toFixed(2) + 'deg)';
            });
            card.addEventListener('mousedown', function() { enabled = false; card.style.transform = 'perspective(1200px) rotateY(-1.2deg) scale(0.99)'; });
            window.addEventListener('mouseup', function() { enabled = true; });
        })();
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <link rel="icon" type="image/x-icon" href="{{ site_favicon() ?? '/favicon.ico' }}">
    <link rel="apple-touch-icon" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); min-height: 100vh; }
        .bg-effects { position: fixed; top: 0; left: 0; right: 0; bottom: 0; overflow: hidden; z-index: 0; }
        .orb { position: absolute; border-radius: 50%; filter: blur(100px); }
        .orb-1 { width: 400px; height: 400px; background: rgba(16, 185, 129, 0.25); top: -150px; right: -100px; }
        .orb-2 { width: 500px; height: 500px; background: rgba(14, 165, 233, 0.15); bottom: -150px; left: -100px; }
        .float-icon { position: absolute; font-size: 40px; opacity: 0.15; animation: float 8s ease-in-out infinite; }
        .float-1 { top: 15%; left: 8%; animation-delay: 0s; }
        .float-2 { top: 25%; right: 10%; animation-delay: 2s; }
        .float-3 { bottom: 20%; left: 15%; animation-delay: 4s; }
        .float-4 { bottom: 30%; right: 8%; animation-delay: 6s; }
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-25px) rotate(5deg); } }
        .main-wrapper { position: relative; z-index: 1; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .auth-card { background: white; border-radius: 24px; box-shadow: 0 25px 60px rgba(0,0,0,0.08); width: 100%; max-width: 440px; padding: 40px; position: relative; overflow: hidden; }
        .auth-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #059669, #10B981); }
        .auth-header { text-align: center; margin-bottom: 32px; }
        .auth-logo { width: 64px; height: 64px; margin: 0 auto 16px; background: linear-gradient(135deg, #059669, #10B981); border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(5, 150, 105, 0.25); }
        .auth-logo svg { width: 32px; height: 32px; color: white; }
        .auth-title { font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
        .auth-subtitle { font-size: 14px; color: #64748b; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #374151; margin-bottom: 6px; }
        .form-input { width: 100%; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 14px; transition: all 0.2s; background: #f8fafc; outline: none; font-family: 'Inter', sans-serif; }
        .form-input:focus { border-color: #10B981; background: white; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); }
        .form-input.error { border-color: #ef4444; }
        .error-text { font-size: 12px; color: #ef4444; margin-top: 4px; }
        .btn-primary { width: 100%; padding: 14px; background: linear-gradient(135deg, #059669, #10B981); color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 16px rgba(5, 150, 105, 0.3); font-family: 'Inter', sans-serif; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(5, 150, 105, 0.4); }
        .btn-primary:active { transform: translateY(0); }
        .auth-footer { text-align: center; margin-top: 24px; font-size: 14px; color: #64748b; }
        .auth-footer a { color: #059669; font-weight: 600; text-decoration: none; }
        .auth-footer a:hover { text-decoration: underline; }
        .status-msg { padding: 12px 16px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; color: #166534; font-size: 14px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="bg-effects">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="float-icon float-1">🌾</div>
        <div class="float-icon float-2">🍚</div>
        <div class="float-icon float-3">🌿</div>
        <div class="float-icon float-4">🥘</div>
    </div>
    <div class="main-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">
                    @if(site_logo())
                    <img src="{{ site_logo() }}" alt="{{ app_name() }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 16px;">
                    @else
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    @endif
                </div>
                <h1 class="auth-title">{{ config('app.name', 'FCCEI') }}</h1>
                <p class="auth-subtitle">{{ $pageSubtitle ?? 'Community Empowerment Initiative' }}</p>
            </div>
            @if (session('status'))
                <div class="status-msg">{{ session('status') }}</div>
            @endif
            {{ $slot }}
        </div>
    </div>
</body>
</html>
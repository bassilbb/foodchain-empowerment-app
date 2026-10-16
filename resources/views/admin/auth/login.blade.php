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
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
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
            filter: blur(100px);
        }
        .orb-1 { width: 400px; height: 400px; background: rgba(16, 185, 129, 0.25); top: -150px; right: -100px; }
        .orb-2 { width: 500px; height: 500px; background: rgba(14, 165, 233, 0.15); bottom: -150px; left: -100px; }
        
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 28rem;
            margin: auto;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 1rem;
            background: linear-gradient(135deg, #10b981, #059669);
            margin-bottom: 1rem;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        }
        .logo-icon svg { width: 40px; height: 40px; color: white; }
        .logo-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #1e293b;
        }
        .logo-subtitle {
            color: #64748b;
            margin-top: 0.5rem;
        }
        
        .login-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            padding: 2rem;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }
        
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(16, 185, 129, 0.4);
        }
        
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
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="bg-effects">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <div class="login-wrapper">
        <div class="logo-section">
            <div class="logo-icon">
                @if(site_logo())
                <img src="{{ site_logo() }}" alt="{{ app_name() }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 1rem;">
                @else
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                @endif
            </div>
            <h1 class="logo-title">{{ app_name() }}</h1>
            <p class="logo-subtitle">Admin Portal</p>
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
</body>
</html>
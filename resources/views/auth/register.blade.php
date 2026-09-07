<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ app_name() }}</title>
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
        .register-card { 
            background: white; 
            border-radius: 28px; 
            box-shadow: 0 25px 60px -20px rgba(0,0,0,0.15);
            padding: 40px;
            width: 100%;
            max-width: 480px;
        }
        .register-header { text-align: center; margin-bottom: 28px; }
        .register-header h1 { font-size: 28px; font-weight: 700; color: #111827; margin-bottom: 6px; }
        .register-header p { color: #6b7280; font-size: 14px; }
        
        /* Step Indicator */
        .step-indicator { display: flex; justify-content: center; gap: 20px; margin-bottom: 32px; }
        .step-number { 
            width: 40px; height: 40px; 
            border-radius: 50%; 
            background: #e5e7eb; 
            color: #9ca3af; 
            font-size: 15px; font-weight: 600; 
            display: flex; align-items: center; justify-content: center; 
            transition: all 0.3s;
        }
        .step.active .step-number { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            color: white;
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
        }
        .step.completed .step-number { 
            background: #10b981; 
            color: white; 
        }
        
        .form-group { margin-bottom: 20px; }
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
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s;
            background-color: #fff;
            color: #374151;
        }
        .form-group select:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16,185,129,0.1);
        }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        
        .step-content { display: none; }
        .step-content.active { display: block; }
        
        .btn-row { display: flex; gap: 14px; margin-top: 28px; }
        .btn { 
            flex: 1; 
            padding: 16px; 
            border-radius: 12px; 
            font-size: 15px; font-weight: 600; 
            cursor: pointer; 
            transition: all 0.2s; 
            border: none; 
        }
        .btn-primary { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            color: white; 
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 15px 30px rgba(16,185,129,0.35); 
        }
        .btn-secondary { 
            background: #f3f4f6; 
            color: #374151; 
        }
        .btn-secondary:hover { 
            background: #e5e7eb; 
        }
        
        /* Payment Options */
        .payment-option { 
            border: 1px solid #e5e7eb; 
            border-radius: 14px; 
            padding: 20px; 
            margin-bottom: 14px; 
            cursor: pointer; 
            transition: all 0.2s; 
        }
        .payment-option:hover { 
            border-color: #10b981; 
            background: #f0fdf4; 
        }
        .payment-option.selected { 
            border-color: #10b981; 
            background: #ecfdf5; 
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }
        .payment-option-header { display: flex; justify-content: space-between; align-items: center; }
        .payment-option-title { font-weight: 600; color: #111827; font-size: 15px; }
        .payment-option-price { font-weight: 700; color: #10b981; font-size: 20px; }
        .payment-option-desc { font-size: 13px; color: #6b7280; margin-top: 4px; }
        .best-badge { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            color: white; 
            font-size: 11px; 
            padding: 4px 10px; 
            border-radius: 6px; 
            font-weight: 600; 
            margin-left: 10px; 
            vertical-align: middle;
        }
        
            .plan-card-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
            .plan-card { position: relative; background: white; border: 2px solid #e2e8f0; border-radius: 16px; padding: 1.25rem; text-align: center; cursor: pointer; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden; }
            .plan-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
            .plan-card.selected { border-color: #10b981; background: #f0fdf4; }
            .plan-card.disabled { opacity: 0.5; cursor: not-allowed; }
            .plan-card-glow { position: absolute; top: -30px; right: -30px; width: 80px; height: 80px; border-radius: 50%; pointer-events: none; }
            .plan-card-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; margin: 0 auto 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
            .plan-card-icon svg { width: 20px; height: 20px; }
            .plan-card-name { font-size: 1rem; font-weight: 700; color: #1e293b; }
            .plan-card-target { font-size: 0.8rem; color: #64748b; margin-top: 2px; }
            .plan-card-check { position: absolute; top: 8px; right: 8px; width: 22px; height: 22px; border-radius: 50%; background: #10b981; color: white; display: none; align-items: center; justify-content: center; }
            .plan-card-check svg { width: 14px; height: 14px; }
            .plan-card.selected .plan-card-check { display: flex; }
            .options-list { display: flex; flex-direction: column; gap: 0.5rem; }
            .option-radio { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: white; border: 2px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.2s; }
            .option-radio:hover { border-color: #94a3b8; }
            .option-radio.selected { border-color: #10b981; background: #f0fdf4; }
            .option-radio input[type="radio"] { width: 18px; height: 18px; accent-color: #10b981; margin: 0; }

            @media (max-width: 640px) {
                .plan-card-grid { grid-template-columns: 1fr 1fr; gap: 0.5rem; }
                .plan-card { padding: 1rem; }
            }

            .checkbox-group { display: flex; align-items: flex-start; gap: 12px; margin: 20px 0; }
        .checkbox-group input[type="checkbox"] { width: 18px; height: 18px; accent-color: #10b981; margin-top: 2px; }
        .checkbox-group label { font-size: 13px; color: #6b7280; line-height: 1.5; }
        .checkbox-group a { color: #10b981; text-decoration: none; }
        .checkbox-group a:hover { text-decoration: underline; }
        
        .form-footer { text-align: center; margin-top: 28px; padding-top: 24px; border-top: 1px solid #f3f4f6; }
        .form-footer p { font-size: 14px; color: #6b7280; }
        .form-footer a { color: #10b981; font-weight: 600; text-decoration: none; }
        .form-footer a:hover { text-decoration: underline; }
        
        .back-link { text-align: center; margin-top: 24px; }
        .back-link a { color: #6b7280; text-decoration: none; font-size: 14px; }
        .back-link a:hover { color: #10b981; }
        
        .alert { padding: 14px 18px; border-radius: 12px; margin-bottom: 24px; font-size: 14px; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        
        .referral-banner { 
            background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%); 
            border-left: 4px solid #10b981; 
            padding: 14px 18px; 
            border-radius: 10px; 
            margin-bottom: 24px; 
            font-size: 14px; 
            color: #065f46; 
        }

        @media (max-width: 1023px) {
            .register-card { padding: 32px 24px; }
            .brand-title { font-size: 42px; }
        }
        @media (max-width: 640px) {
            .register-card { padding: 24px 18px; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
            .step-indicator { gap: 14px; }
            .step-number { width: 36px; height: 36px; font-size: 14px; }
        }
    </style>
</head>
<body>
    <!-- Background Effects -->
    <div class="bg-effects">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
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
        <div class="register-card">
            <div class="register-header">
                <div class="form-logo">
                    @if(site_logo())
                    <img src="{{ site_logo() }}" alt="{{ app_name() }}" style="height: 48px; width: auto; margin-bottom: 16px;">
                    @endif
                </div>
                <h1>Create Your Account</h1>
                <p>Join our community and get access to quality food packages</p>
            </div>

                @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if($referrer)
                <div class="referral-banner">
                    ✓ Referred by {{ $referrer->full_name }}
                </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}" id="registerForm">
                    @csrf
                    <input type="hidden" name="referral_code" value="{{ $referralCode }}">
                    
                    <div class="step-indicator">
                        <div class="step active" id="step-ind-1"><div class="step-number">1</div></div>
                        <div class="step" id="step-ind-2"><div class="step-number">2</div></div>
                        <div class="step" id="step-ind-3"><div class="step-number">3</div></div>
                    </div>

                    <!-- Step 1: Personal -->
                    <div id="step1" class="step-content active">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="first_name">First Name *</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required placeholder="John">
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name *</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required placeholder="Doe">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="you@example.com">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone *</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="+234 800 000 0000">
                        </div>
                        <button type="button" onclick="nextStep(1)" class="btn btn-primary">Continue</button>
                    </div>

                    <!-- Step 2: Location -->
                    <div id="step2" class="step-content">
                        <div class="form-group">
                            <label for="address">Delivery Address *</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" required placeholder="Street address">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City *</label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" required placeholder="e.g. Ikeja">
                            </div>
                            <div class="form-group">
                                <label for="state">State *</label>
                                <select name="state" id="state" required class="form-input">
                                    <option value="">Select your state</option>
                                    @foreach($statesList as $st)
                                        <option value="{{ $st }}" @selected(old('state') === $st)>{{ $st }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <p style="font-size: 12px; color: #6b7280; margin: -6px 0 12px;">Prices you see in the marketplace adjust to your state automatically.</p>
                        <div class="btn-row">
                            <button type="button" onclick="prevStep(2)" class="btn btn-secondary">Back</button>
                            <button type="button" onclick="nextStep(2)" class="btn btn-primary">Continue</button>
                        </div>
                    </div>

                    <!-- Step 3: Security -->
                    <div id="step3" class="step-content">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="password">Password *</label>
                                <input type="password" name="password" id="password" required placeholder="Min 8 characters">
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation">Confirm *</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Confirm password">
                            </div>
                        </div>
                        <div class="checkbox-group">
                            <input type="checkbox" id="terms" required>
                            <label for="terms">I agree to the <a href="javascript:void(0)" onclick="openTermsModal()" style="color: #10b981; text-decoration: underline; cursor: pointer;">Terms & Conditions</a></label>
                        </div>
                        <div class="btn-row">
                            <button type="button" onclick="prevStep(3)" class="btn btn-secondary">Back</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Create Free Account</button>
                        </div>
                        <p style="text-align: center; font-size: 12px; color: #10b981; font-weight: 600; margin-top: 12px;">No registration fee. Start shopping immediately.</p>
                    </div>

                <div class="form-footer">
                    <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
                </div>

                <div class="back-link">
                    <a href="/">← Back to Home</a>
                </div>
            </div>
        </div>
    </div>

        <script>
        var TOTAL_STEPS = 3;

        function updateStepIndicator(step) {
            for (let i = 1; i <= TOTAL_STEPS; i++) {
                const el = document.getElementById('step-ind-' + i);
                if (!el) continue;
                if (i < step) {
                    el.className = 'step completed';
                } else if (i === step) {
                    el.className = 'step active';
                } else {
                    el.className = 'step';
                }
            }
        }

        function validateStep(step) {
            const stepDiv = document.getElementById('step' + step);
            let valid = true;
            stepDiv.querySelectorAll('input[required]').forEach(input => {
                if (input.type === 'checkbox') {
                    if (!input.checked) { valid = false; }
                    return;
                }
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = '#ef4444';
                } else {
                    input.style.borderColor = '#e5e7eb';
                }
            });
            const select = stepDiv.querySelector('select[required]');
            if (select && !select.value) {
                valid = false;
                select.style.borderColor = '#ef4444';
            }
            if (!valid) {
                showStepError('Please complete the highlighted fields to continue');
            }
            return valid;
        }

        function nextStep(current) {
            if (!validateStep(current)) {
                return;
            }
            document.getElementById('step' + current).classList.remove('active');
            document.getElementById('step' + (current + 1)).classList.add('active');
            updateStepIndicator(current + 1);
        }

        function prevStep(current) {
            document.getElementById('step' + current).classList.remove('active');
            document.getElementById('step' + (current - 1)).classList.add('active');
            updateStepIndicator(current - 1);
        }

        function showStepError(msg) {
            const existing = document.getElementById('stepError');
            if (existing) existing.remove();
            const active = document.querySelector('.step-content.active');
            if (!active) return;
            const div = document.createElement('div');
            div.id = 'stepError';
            div.className = 'alert alert-error';
            div.textContent = msg;
            active.insertBefore(div, active.firstChild);
            setTimeout(() => { if (div.parentNode) div.remove(); }, 4000);
        }

        document.getElementById('registerForm').addEventListener('submit', function (e) {
            const activeStep = parseInt(document.querySelector('.step-content.active').id.replace('step', ''), 10);
            for (let i = 1; i <= activeStep; i++) {
                if (!validateStep(i)) {
                    e.preventDefault();
                    document.getElementById('step' + activeStep).classList.remove('active');
                    document.getElementById('step' + i).classList.add('active');
                    updateStepIndicator(i);
                    return;
                }
            }
        });

        // Terms Modal
        function openTermsModal() {
            document.getElementById('termsModal').style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeTermsModal() {
            document.getElementById('termsModal').style.display = 'none';
            document.body.style.overflow = '';
        }

        function acceptTerms() {
            document.getElementById('terms').checked = true;
            closeTermsModal();
        }

        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('termsModal');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === this) closeTermsModal();
                });
            }
        });
    </script></script>

    <!-- Terms Modal -->
    <div id="termsModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 9999; align-items: center; justify-content: center; padding: 20px;">
        <div style="background: white; border-radius: 20px; max-width: 600px; width: 100%; max-height: 80vh; overflow: hidden; box-shadow: 0 25px 60px -20px rgba(0,0,0,0.3);">
            <div style="padding: 24px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 20px; font-weight: 700; color: #111827; margin: 0;">Terms & Conditions</h2>
                <button onclick="closeTermsModal()" style="background: none; border: none; cursor: pointer; padding: 8px; color: #6b7280;">
                    <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div style="padding: 24px; overflow-y: auto; max-height: calc(80vh - 140px); font-size: 14px; color: #374151; line-height: 1.7;">
                <h3 style="font-weight: 600; color: #111827; margin-bottom: 12px; margin-top: 20px;">1. Acceptance of Terms</h3>
                <p style="margin-bottom: 12px;">By accessing and using the FoodChain platform, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our services.</p>

                <h3 style="font-weight: 600; color: #111827; margin-bottom: 12px; margin-top: 20px;">2. Membership Eligibility</h3>
                <p style="margin-bottom: 12px;">You must be at least 18 years old and provide accurate information during registration. You are responsible for maintaining the confidentiality of your account.</p>

                <h3 style="font-weight: 600; color: #111827; margin-bottom: 12px; margin-top: 20px;">3. Payment & Fees</h3>
                <p style="margin-bottom: 12px;">Membership fees are annual and non-refundable. Starter packs and additional purchases are final. All payments are processed securely through our payment partners.</p>

                <h3 style="font-weight: 600; color: #111827; margin-bottom: 12px; margin-top: 20px;">4. Referral Program</h3>
                <p style="margin-bottom: 12px;">You may earn rewards by referring new members. Rewards are subject to verification and can be withdrawn according to our reward policy. We reserve the right to modify referral terms at any time.</p>

                <h3 style="font-weight: 600; color: #111827; margin-bottom: 12px; margin-top: 20px;">5. Food Distribution</h3>
                <p style="margin-bottom: 12px;">Food packs are distributed quarterly based on membership tier. Distribution dates and locations are subject to availability. We do not guarantee specific food items.</p>

                <h3 style="font-weight: 600; color: #111827; margin-bottom: 12px; margin-top: 20px;">6. Privacy & Data</h3>
                <p style="margin-bottom: 12px;">We collect and store your personal information securely. Your data will not be shared with third parties except as necessary for service delivery. See our Privacy Policy for details.</p>

                <h3 style="font-weight: 600; color: #111827; margin-bottom: 12px; margin-top: 20px;">7. Account Termination</h3>
                <p style="margin-bottom: 12px;">We reserve the right to suspend or terminate accounts that violate these terms, engage in fraud, or cause harm to other users or the platform.</p>

                <h3 style="font-weight: 600; color: #111827; margin-bottom: 12px; margin-top: 20px;">8. Liability</h3>
                <p style="margin-bottom: 12px;">FoodChain is not liable for any indirect, incidental, or consequential damages arising from the use of our services. Our liability is limited to the amount paid for membership.</p>

                <h3 style="font-weight: 600; color: #111827; margin-bottom: 12px; margin-top: 20px;">9. Contact Information</h3>
                <p style="margin-bottom: 12px;">For questions about these terms, please contact our support team at support@foodchain.ng</p>
            </div>
            <div style="padding: 20px; border-top: 1px solid #e5e7eb; display: flex; gap: 12px;">
                <button onclick="closeTermsModal()" style="flex: 1; padding: 14px; background: #f3f4f6; color: #374151; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">Cancel</button>
                <button onclick="acceptTerms()" style="flex: 1; padding: 14px; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer;">Accept & Continue</button>
            </div>
        </div>
    </div>
</body>
</html>

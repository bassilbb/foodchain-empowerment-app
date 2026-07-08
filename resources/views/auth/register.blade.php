<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ app_name() }}</title>
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
                        <div class="step" id="step-ind-4"><div class="step-number">4</div></div>
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
                            <label for="address">Address *</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" required placeholder="Your address">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">City *</label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}" required placeholder="City">
                            </div>
                            <div class="form-group">
                                <label for="state">State *</label>
                                <input type="text" name="state" id="state" value="{{ old('state') }}" required placeholder="State">
                            </div>
                        </div>
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
                        <div class="btn-row">
                            <button type="button" onclick="prevStep(3)" class="btn btn-secondary">Back</button>
                            <button type="button" onclick="nextStep(3)" class="btn btn-primary">Continue</button>
                        </div>
                    </div>

                    <!-- Step 4: Membership Tier, Payment & Savings Plan -->
                    <div id="step4" class="step-content">
                        <input type="hidden" name="payment_type" id="paymentType" value="">
                        <input type="hidden" name="savings_plan_option_id" id="savingsPlanOptionId" value="">
                        <input type="hidden" name="selected_tier_id" id="selectedTierId" value="{{ $tiers->first()->id ?? '' }}">

                        <div class="form-group">
                            <label>Choose Your Membership Plan</label>
                            <p style="font-size: 13px; color: #6b7280; margin-bottom: 12px;">Select your membership tier to unlock benefits and food packages.</p>
                            <div class="plan-card-grid">
                                @php
                                    $tierColors = [
                                        'Bronze' => ['bg' => 'linear-gradient(135deg, #f59e0b, #d97706)', 'glow' => '#f59e0b', 'icon' => '🥉'],
                                        'Silver' => ['bg' => 'linear-gradient(135deg, #64748b, #475569)', 'glow' => '#64748b', 'icon' => '🥈'],
                                        'Gold' => ['bg' => 'linear-gradient(135deg, #eab308, #ca8a04)', 'glow' => '#eab308', 'icon' => '🥇'],
                                        'Emerald' => ['bg' => 'linear-gradient(135deg, #10b981, #059669)', 'glow' => '#10b981', 'icon' => '💎'],
                                    ];
                                @endphp
                                @foreach($tiers as $tier)
                                @php $c = $tierColors[$tier->name] ?? $tierColors['Bronze']; @endphp
                                <div class="plan-card tier-card" data-tier-id="{{ $tier->id }}" data-tier-fee="{{ $tier->annual_fee }}" onclick="selectTier({{ $tier->id }}, {{ $tier->annual_fee }})" @if($loop->first) style="border-color: #10b981; background: #f0fdf4;" @endif>
                                    <div class="plan-card-glow" style="background: radial-gradient(circle, {{ $c['glow'] }}10 0%, transparent 70%);"></div>
                                    <div class="plan-card-icon" style="background: {{ $c['bg'] }};">
                                        <span style="font-size: 20px;">{{ $c['icon'] }}</span>
                                    </div>
                                    <div class="plan-card-name">{{ $tier->name }}</div>
                                    <div class="plan-card-target">₦{{ number_format($tier->annual_fee, 0) }}/year</div>
                                    <div class="plan-card-check">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Select Payment Type</label>
                            <div class="payment-option" data-value="membership_only" onclick="selectPaymentType('membership_only')">
                                <div class="payment-option-header">
                                    <div>
                                        <div class="payment-option-title">Membership Only</div>
                                        <div class="payment-option-desc">Pay the annual membership fee and get started</div>
                                    </div>
                                    <div class="payment-option-price" id="membershipOnlyPrice">₦{{ number_format($tiers->first()->annual_fee ?? 5000, 0) }}</div>
                                </div>
                            </div>
                            <div class="payment-option" data-value="with_savings_plan" onclick="selectPaymentType('with_savings_plan')">
                                <div class="payment-option-header">
                                    <div>
                                        <div class="payment-option-title">With Savings Plan</div>
                                        <div class="payment-option-desc">Pay annual fee + choose a savings plan for your food fund</div>
                                    </div>
                                    <div class="payment-option-price" id="savingsPlanPrice">₦{{ number_format($tiers->first()->annual_fee ?? 5000, 0) }}</div>
                                </div>
                            </div>
                        </div>

                        <div id="savingsPlanSection" style="display: none;">
                            <div class="form-group">
                                <label>Choose Your Savings Plan <span style="font-weight: 400; color: #9ca3af;">(optional)</span></label>
                                <p style="font-size: 13px; color: #6b7280; margin-bottom: 12px;">Select a savings plan to start contributing towards your food fund. You can also skip this and set one up later.</p>
                                <div class="plan-card-grid">
                                    @php
                                        $colors = [
                                            'Bronze' => ['bg' => 'linear-gradient(135deg, #f59e0b, #d97706)', 'glow' => '#f59e0b', 'light' => '#fef3c7'],
                                            'Silver' => ['bg' => 'linear-gradient(135deg, #64748b, #475569)', 'glow' => '#64748b', 'light' => '#f1f5f9'],
                                            'Gold' => ['bg' => 'linear-gradient(135deg, #eab308, #ca8a04)', 'glow' => '#eab308', 'light' => '#fefce8'],
                                            'Emerald' => ['bg' => 'linear-gradient(135deg, #10b981, #059669)', 'glow' => '#10b981', 'light' => '#ecfdf5'],
                                        ];
                                    @endphp
                                    @foreach($savingsPlans as $plan)
                                    @php $c = $colors[$plan->name] ?? $colors['Emerald']; @endphp
                                    <div class="plan-card" data-plan-id="{{ $plan->id }}" onclick="selectPlan({{ $plan->id }})" style="border-color: {{ $plan->activeOptions->count() > 0 ? '#e2e8f0' : '#fecaca' }};">
                                        <div class="plan-card-glow" style="background: radial-gradient(circle, {{ $c['glow'] }}10 0%, transparent 70%);"></div>
                                        <div class="plan-card-icon" style="background: {{ $c['bg'] }};">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        </div>
                                        <div class="plan-card-name">{{ $plan->name }}</div>
                                        <div class="plan-card-target">Target: ₦{{ number_format($plan->target_amount, 2) }}</div>
                                        @if($plan->activeOptions->count() === 0)
                                        <div style="font-size: 0.7rem; color: #ef4444; margin-top: 4px;">No options available</div>
                                        @endif
                                        <div class="plan-card-check">
                                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div id="optionsSection" class="form-group" style="display: none;">
                                <label>Choose Your Savings Duration</label>
                                <div id="optionsList" class="options-list"></div>
                            </div>

                            <div id="planDetails" style="display: none; background: #f0fdf4; border: 2px solid #d1fae5; border-radius: 14px; padding: 16px; margin-bottom: 16px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                                    <div>
                                        <div style="font-size: 12px; color: #6b7280;">Weekly Amount</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #059669;" id="planWeekly">₦0</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 12px; color: #6b7280;">Duration</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #1e293b;" id="planWeeks">0 weeks</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 12px; color: #6b7280;">Total Contribution</div>
                                        <div style="font-size: 18px; font-weight: 700; color: #d97706;" id="planTotal">₦0</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="terms" required>
                            <label for="terms">I agree to the <a href="javascript:void(0)" onclick="openTermsModal()" style="color: #10b981; text-decoration: underline; cursor: pointer;">Terms & Conditions</a></label>
                        </div>
                        <div class="btn-row">
                            <button type="button" onclick="prevStep(4)" class="btn btn-secondary">Back</button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">Submit & Pay <span id="submitAmount" style="font-weight: 400;">(₦5,000)</span></button>
                        </div>
                    </div>
                </form>

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
        function updateStepIndicator(step) {
            for (let i = 1; i <= 4; i++) {
                const el = document.getElementById('step-ind-' + i);
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
            const inputs = stepDiv.querySelectorAll('input[required]');
            let valid = true;
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    valid = false;
                    input.style.borderColor = '#ef4444';
                } else {
                    input.style.borderColor = '#e5e7eb';
                }
            });
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

        const plansData = @json($plansData);
        const tiers = @json($tiers->keyBy->id->map(function($t) { return ['id' => $t->id, 'name' => $t->name, 'annual_fee' => (float) $t->annual_fee]; }));

        let selectedPlanId = null;
        let selectedOptionId = null;
        let selectedTierFee = {{ $tiers->first()->annual_fee ?? 5000 }};

        function selectTier(tierId, fee) {
            document.querySelectorAll('.tier-card').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.tier-card').forEach(c => c.style.borderColor = '#e2e8f0');
            document.querySelectorAll('.tier-card').forEach(c => c.style.background = 'white');
            const card = document.querySelector(`.tier-card[data-tier-id="${tierId}"]`);
            if (card) {
                card.classList.add('selected');
                card.style.borderColor = '#10b981';
                card.style.background = '#f0fdf4';
            }
            document.getElementById('selectedTierId').value = tierId;
            selectedTierFee = fee;
            updateSubmitAmount();
        }

        function selectPlan(planId) {
            selectPaymentType('with_savings_plan');
            document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
            document.querySelector(`.plan-card[data-plan-id="${planId}"]`).classList.add('selected');
            selectedPlanId = planId;
            selectedOptionId = null;
            document.getElementById('savingsPlanOptionId').value = '';
            document.getElementById('planDetails').style.display = 'none';
            document.getElementById('submitBtn').disabled = false;

            const plan = plansData[planId];
            const list = document.getElementById('optionsList');
            list.innerHTML = '';

            if (plan && plan.options.length > 0) {
                document.getElementById('optionsSection').style.display = 'block';
                plan.options.forEach(opt => {
                    const div = document.createElement('div');
                    div.className = 'option-radio';
                    div.onclick = () => selectOption(opt.id);
                    div.innerHTML = `
                        <input type="radio" name="option_radio" value="${opt.id}">
                        <div style="flex: 1;">
                            <div style="font-weight: 600; color: #1e293b;">${opt.weeks} Weeks</div>
                            <div style="font-size: 0.85rem; color: #10b981; font-weight: 600;">₦${opt.amount.toLocaleString(undefined, {minimumFractionDigits: 2})} / week</div>
                        </div>
                    `;
                    list.appendChild(div);
                });
            } else {
                document.getElementById('optionsSection').style.display = 'none';
            }
            updateSubmitAmount();
        }

        function selectOption(optionId) {
            document.querySelectorAll('.option-radio').forEach(r => r.classList.remove('selected'));
            document.querySelectorAll('.option-radio input').forEach(r => { if (r.value == optionId) { r.checked = true; r.closest('.option-radio').classList.add('selected'); }});
            selectedOptionId = optionId;
            document.getElementById('savingsPlanOptionId').value = optionId;

            const plan = plansData[selectedPlanId];
            const opt = plan.options.find(o => o.id == optionId);
            if (opt) {
                const total = Math.round(opt.amount * opt.weeks);
                document.getElementById('planWeekly').textContent = '₦' + opt.amount.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('planWeeks').textContent = opt.weeks + ' weeks';
                document.getElementById('planTotal').textContent = '₦' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
                document.getElementById('planDetails').style.display = 'block';
            }
            updateSubmitAmount();
        }

        function selectPaymentType(type) {
            document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
            document.querySelector(`.payment-option[data-value="${type}"]`).classList.add('selected');
            document.getElementById('paymentType').value = type;

            const section = document.getElementById('savingsPlanSection');
            if (type === 'with_savings_plan') {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
                resetSavingsPlan();
            }
            updateSubmitAmount();
        }

        function resetSavingsPlan() {
            document.querySelectorAll('.plan-card').forEach(c => c.classList.remove('selected'));
            document.querySelectorAll('.option-radio').forEach(r => r.classList.remove('selected'));
            document.getElementById('optionsSection').style.display = 'none';
            document.getElementById('planDetails').style.display = 'none';
            document.getElementById('savingsPlanOptionId').value = '';
            selectedPlanId = null;
            selectedOptionId = null;
        }

        function updateSubmitAmount() {
            const fee = selectedTierFee || 5000;
            document.getElementById('submitAmount').textContent = '(₦' + fee.toLocaleString() + ')';
            document.getElementById('membershipOnlyPrice').textContent = '₦' + fee.toLocaleString();
            document.getElementById('savingsPlanPrice').textContent = '₦' + fee.toLocaleString();
        }

        // Override validateStep for Step 4
        const _origValidate = validateStep;
        validateStep = function(step) {
            if (step === 4) {
                if (!document.getElementById('paymentType').value) {
                    showStepError('Please select a payment type to continue');
                    return false;
                }
                if (!document.getElementById('terms').checked) {
                    showStepError('Please agree to the Terms & Conditions to continue');
                    return false;
                }
                return true;
            }
            return _origValidate(step);
        };

        function showStepError(msg) {
            const existing = document.getElementById('stepError');
            if (existing) existing.remove();
            const div = document.createElement('div');
            div.id = 'stepError';
            div.className = 'alert alert-error';
            div.textContent = msg;
            document.getElementById('step4').insertBefore(div, document.getElementById('step4').firstChild);
            setTimeout(() => { if (div.parentNode) div.remove(); }, 4000);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateSubmitAmount();
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

        // Close modal on outside click
        document.getElementById('termsModal').addEventListener('click', function(e) {
            if (e.target === this) closeTermsModal();
        });
    </script>

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

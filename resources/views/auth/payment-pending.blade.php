<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Pending - FoodChain</title>
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
        
        .main-wrapper {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
        }
        
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
        .brand-content { max-width: 480px; text-align: center; }
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
        .feature-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .feature-icon svg { width: 24px; height: 24px; }
        .feature-title { font-weight: 600; color: #111827; font-size: 16px; }
        .feature-desc { font-size: 14px; color: #6b7280; }
        
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
        
        .payment-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 25px 60px -20px rgba(0,0,0,0.15);
            padding: 28px;
            width: 100%;
            max-width: 480px;
        }
        
        .payment-header { text-align: center; margin-bottom: 20px; }
        .payment-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
        }
        .payment-icon svg { width: 28px; height: 28px; color: #10b981; }
        .payment-title { font-size: 22px; font-weight: 700; color: #111827; margin-bottom: 6px; }
        .payment-subtitle { font-size: 14px; color: #6b7280; }
        
        .amount-box {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 20px;
            text-align: center;
        }
        .amount-label { font-size: 13px; color: #6b7280; margin-bottom: 6px; }
        .amount-value { font-size: 28px; font-weight: 800; color: #10b981; }
        .amount-desc { font-size: 13px; color: #059669; margin-top: 6px; font-weight: 500; }
        
        /* Tabs */
        .tabs { 
            display: flex; 
            gap: 4px; 
            margin-bottom: 16px; 
            background: #f3f4f6; 
            padding: 4px; 
            border-radius: 12px; 
        }
        .tab {
            flex: 1;
            padding: 10px 4px;
            border: none;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            background: transparent;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }
        .tab.active {
            background: white;
            color: #10b981;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        }
        .tab svg { width: 14px; height: 14px; flex-shrink: 0; }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .section-title { font-size: 14px; font-weight: 600; color: #111827; margin-bottom: 12px; }
        
        /* Bank Details Card */
        .bank-card {
            border: 2px solid #10b981;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 12px;
            text-align: center;
        }
        .bank-card-name { font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 12px; }
        .bank-card-number { 
            font-size: 26px; 
            font-weight: 800; 
            color: #10b981; 
            letter-spacing: 3px; 
            margin-bottom: 8px;
            cursor: pointer;
        }
        .bank-card-number:active { color: #059669; }
        .bank-card-label { font-size: 11px; color: #6b7280; margin-bottom: 2px; }
        .bank-card-value { font-size: 14px; font-weight: 600; color: #111827; }
        .copy-hint { 
            font-size: 10px; 
            color: #10b981; 
            margin-top: 4px; 
            opacity: 0; 
            transition: opacity 0.3s;
        }
        .bank-card-number:active + .copy-hint { opacity: 1; }
        
        .copy-btn {
            width: 100%;
            padding: 10px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
            margin-bottom: 12px;
        }
        .copy-btn:hover { border-color: #10b981; color: #10b981; }
        .copy-btn svg { width: 18px; height: 18px; }
        
        .bank-option {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .bank-option:hover { border-color: #10b981; background: #f0fdf4; }
        .bank-option.selected { border-color: #10b981; background: #ecfdf5; }
        .bank-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .bank-name { font-weight: 600; color: #111827; font-size: 14px; }
        .bank-badge { background: #d1fae5; color: #059669; font-size: 11px; font-weight: 600; padding: 3px 8px; border-radius: 5px; }
        .bank-details { display: grid; gap: 6px; }
        .bank-row { display: flex; justify-content: space-between; font-size: 13px; }
        .bank-label { color: #6b7280; }
        .bank-value { font-weight: 600; color: #111827; }
        .bank-number { font-size: 18px; font-weight: 700; color: #10b981; }
        
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-size: 13px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        .form-group input, .form-group select { 
            width: 100%; 
            padding: 12px 14px; 
            border: 1px solid #e5e7eb; 
            border-radius: 10px; 
            font-size: 14px; 
            transition: all 0.2s; 
            background: #f9fafb;
        }
        .form-group input:focus, .form-group select:focus { 
            outline: none; 
            border-color: #10b981; 
            box-shadow: 0 0 0 3px rgba(16,185,129,0.1); 
            background: white;
        }
        .form-group input::placeholder { color: #9ca3af; }
        
        .btn-primary { 
            width: 100%; 
            padding: 14px; 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
            color: white; 
            border: none; 
            border-radius: 12px; 
            font-size: 15px; 
            font-weight: 600; 
            cursor: pointer; 
            transition: all 0.2s; 
        }
        .btn-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 15px 30px rgba(16,185,129,0.35); 
        }
        
        .form-footer { text-align: center; margin-top: 24px; padding-top: 20px; border-top: 1px solid #f3f4f6; }
        .form-footer a { color: #10b981; font-weight: 600; text-decoration: none; }
        .form-footer a:hover { text-decoration: underline; }

        .toast {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: #111827;
            color: white;
            padding: 14px 24px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            opacity: 0;
            transition: all 0.3s;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .toast.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        @media (max-width: 1023px) {
            .payment-card { padding: 32px 24px; }
            .brand-title { font-size: 42px; }
        }
        @media (max-width: 480px) {
            .payment-card { padding: 24px 18px; }
            .payment-title { font-size: 22px; }
            .amount-value { font-size: 28px; }
            .tab { font-size: 11px; padding: 10px 4px; }
            .bank-card-number { font-size: 26px; letter-spacing: 2px; }
        }
    </style>
</head>
<body>
    <div class="bg-effects">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="float-icon float-1">🍎</div>
        <div class="float-icon float-2">🥦</div>
        <div class="float-icon float-3">🍊</div>
        <div class="float-icon float-4">🥕</div>
    </div>

    <div class="toast" id="toast">
        <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
        <span id="toastMsg">Copied!</span>
    </div>

    <div class="main-wrapper">
        <div class="brand-panel">
            <div class="brand-content">
                <div class="brand-logo">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h1 class="brand-title">FoodChain</h1>
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

        <div class="form-panel">
            <div class="payment-card">
                <div class="payment-header">
                    <div class="payment-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h1 class="payment-title">Registration Successful!</h1>
                    <p class="payment-subtitle">Complete your payment to activate your account</p>
                </div>

                <div class="amount-box">
                    <div class="amount-label">Amount to Pay</div>
                    <div class="amount-value">₦{{ number_format($amount, 0) }}</div>
                    <div class="amount-desc">{{ $paymentDescription }}</div>
                    @if(isset($savingsPlanOption) && $savingsPlanOption)
                    <div style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #a7f3d0; font-size: 13px; color: #065f46;">
                        <strong>{{ $savingsPlanOption->plan->name }} Plan:</strong>
                        ₦{{ number_format($savingsPlanOption->weekly_contribution, 2) }}/week for {{ $savingsPlanOption->duration_weeks }} weeks
                        <br><span style="font-size: 11px; opacity: 0.7;">A savings account will be created after payment</span>
                    </div>
                    @endif
                </div>

                @if(isset($existingPending) && $existingPending)
                <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 12px; padding: 16px; margin-bottom: 16px; text-align: center;">
                    <svg style="width: 32px; height: 32px; color: #d97706; margin: 0 auto 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h3 style="font-size: 16px; font-weight: 700; color: #92400e; margin-bottom: 4px;">Payment Already Submitted</h3>
                    <p style="font-size: 13px; color: #b45309;">Your payment proof has been received and is pending verification. You will be notified once your account is activated.</p>
                </div>
                @endif

                <!-- Tabs -->
                <div class="tabs">
                    <button class="tab active" onclick="switchTab('details')">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        Account Details
                    </button>
                    <button class="tab {{ isset($existingPending) && $existingPending ? '' : '' }}" onclick="switchTab('form')">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Submit Proof
                    </button>
                    <button class="tab" onclick="switchTab('card')">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Pay with Card
                    </button>
                </div>

                <!-- Tab 1: Account Details -->
                <div id="details-tab" class="tab-content active">
                    <h2 class="section-title">Select Bank Account</h2>
                    
                    @foreach($bankAccounts as $account)
                    <div class="bank-option" onclick="selectBankAndSwitch('{{ $account->id }}', this, 'form')">
                        <div class="bank-header">
                            <span class="bank-name">{{ $account->bank_name }}</span>
                            <span class="bank-badge">Active</span>
                        </div>
                        <div class="bank-details">
                            <div class="bank-row">
                                <span class="bank-label">Account Name:</span>
                                <span class="bank-value">{{ $account->account_name }}</span>
                            </div>
                            <div class="bank-row">
                                <span class="bank-label">Account Number:</span>
                                <span class="bank-number">{{ $account->account_number }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    
                    <div style="background: #f0fdf4; border-radius: 10px; padding: 12px; margin-top: 12px; font-size: 12px; color: #374151; line-height: 1.5;">
                        <strong style="color: #059669;">Note:</strong> Transfer exactly <strong>₦{{ number_format($amount, 0) }}</strong>. Use phone as reference.
                    </div>
                </div>

                <!-- Tab 2: Submit Proof Form -->
                <div id="form-tab" class="tab-content">
                    @if(isset($existingPending) && $existingPending)
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 24px; text-align: center;">
                        <svg style="width: 40px; height: 40px; color: #16a34a; margin: 0 auto 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 style="font-size: 16px; font-weight: 700; color: #166534; margin-bottom: 4px;">Payment Submitted</h3>
                        <p style="font-size: 13px; color: #15803d;">Your payment proof has been received and is pending verification.</p>
                    </div>
                    @else
                    <form action="{{ route('payment.manual') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <input type="hidden" name="amount" value="{{ $amount }}">
                        <input type="hidden" name="payment_type" value="{{ request('payment_type') }}">

                        <div class="form-group">
                            <label>Select Bank / App</label>
                            <select name="bank_account_id" required id="bankSelect">
                                <option value="">-- Select Bank --</option>
                                @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->account_number }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Your Name (as on bank account)</label>
                            <input type="text" name="sender_name" required placeholder="John Doe">
                        </div>

                        <div class="form-group">
                            <label>Transfer Date</label>
                            <input type="date" name="transfer_date" required>
                        </div>

                        <div class="form-group">
                            <label>Payment Proof (screenshot) - Optional</label>
                            <input type="file" name="proof_image" accept="image/*">
                        </div>

                        <button type="submit" class="btn-primary">Submit Payment Proof</button>
                    </form>
                    @endif
                </div>

                <!-- Tab 3: Card Payment -->
                <div id="card-tab" class="tab-content">
                    @if(isset($existingPending) && $existingPending)
                    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 24px; text-align: center;">
                        <svg style="width: 40px; height: 40px; color: #16a34a; margin: 0 auto 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 style="font-size: 16px; font-weight: 700; color: #166534; margin-bottom: 4px;">Payment Submitted</h3>
                        <p style="font-size: 13px; color: #15803d;">Your payment proof has been received and is pending verification.</p>
                    </div>
                    @else
                    <div style="text-align: center; padding: 16px 0;">
                        <div style="background: #f0fdf4; border-radius: 16px; padding: 24px; margin-bottom: 20px;">
                            <svg style="width: 48px; height: 48px; color: #10b981; margin: 0 auto 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"></path></svg>
                            <h3 style="font-size: 18px; font-weight: 700; color: #065f46; margin-bottom: 4px;">Pay with Card</h3>
                            <p style="font-size: 13px; color: #047857; margin-bottom: 4px;">Pay quickly with your debit/credit card</p>
                            <p style="font-size: 24px; font-weight: 800; color: #10b981; margin: 12px 0;">₦{{ number_format($amount, 0) }}</p>
                        </div>

                        <form action="{{ route('payment.pay-with-card') }}" method="POST">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <input type="hidden" name="amount" value="{{ $amount }}">
                            <input type="hidden" name="payment_type" value="{{ request('payment_type') }}">

                            <div style="background: #f9fafb; border-radius: 12px; padding: 16px; margin-bottom: 16px; text-align: left;">
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                                    <svg style="width: 24px; height: 24px; color: #10b981; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    <span style="font-size: 14px; color: #374151;">Secured by <strong>Paystack</strong></span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                                    <svg style="width: 24px; height: 24px; color: #10b981; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    <span style="font-size: 14px; color: #374151;">Visa, Mastercard, Verve accepted</span>
                                </div>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <svg style="width: 24px; height: 24px; color: #10b981; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    <span style="font-size: 14px; color: #374151;">Instant account activation</span>
                                </div>
                            </div>

                            <button type="submit" class="btn-primary">
                                <svg style="width: 18px; height: 18px; display: inline; vertical-align: middle; margin-right: 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                Pay ₦{{ number_format($amount, 0) }} with Paystack
                            </button>
                        </form>
                    </div>
                    @endif
                </div>

                <div class="form-footer">
                    Already paid? <a href="{{ route('login') }}">Login here</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            const tabs = ['details', 'form', 'card'];
            const index = tabs.indexOf(tab);
            document.querySelectorAll('.tab')[index].classList.add('active');
            document.getElementById(tab + '-tab').classList.add('active');
        }
        
        function selectBank(id, element) {
            document.querySelectorAll('.bank-option').forEach(opt => opt.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById('bankSelect').value = id;
        }
        
        function selectBankAndSwitch(id, element, tab) {
            selectBank(id, element);
            switchTab(tab);
        }
        
        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showCopyToast();
                }).catch(() => {
                    fallbackCopy(text);
                });
            } else {
                fallbackCopy(text);
            }
        }

        function fallbackCopy(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '-9999px';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                showCopyToast();
            } catch (err) {
                alert('Failed to copy: ' + text);
            }
            document.body.removeChild(textarea);
        }

        function showCopyToast() {
            const toast = document.getElementById('toast');
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2000);
        }
    </script>
</body>
</html>

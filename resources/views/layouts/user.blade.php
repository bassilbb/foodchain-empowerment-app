<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ app_name() }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        /* Base Styles */
        * { box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
        }
        
        /* Layout */
        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar { 
            width: 280px;
            background: linear-gradient(180deg, #065f46 0%, #064e3b 50%, #022c22 100%);
            border-right: 1px solid rgba(255,255,255,0.06);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 32px rgba(0,0,0,0.2);
        }
        
        .sidebar-logo {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        
        .sidebar-logo-inner {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .logo-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .logo-icon svg { width: 24px; height: 24px; color: white; }
        .logo-text { font-size: 1.25rem; font-weight: 700; color: #ffffff; }
        .logo-subtitle { font-size: 0.75rem; color: rgba(255,255,255,0.6); }
        
        .sidebar-user {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        
        .sidebar-user-inner {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(255,255,255,0.06);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #34d399, #38bdf8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 0.875rem;
        }
        
        .user-info { flex: 1; }
        .user-name { font-weight: 600; color: #ffffff; font-size: 0.875rem; }
        .user-tier { 
            display: inline-block;
            padding: 0.125rem 0.5rem;
            background: linear-gradient(135deg, #f97316, #ea580c);
            color: white;
            border-radius: 4px;
            font-size: 0.625rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .sidebar-nav {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
        }
        
        .nav-section { margin-bottom: 1.5rem; }
        .nav-section-title {
            font-size: 0.675rem;
            font-weight: 700;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            padding: 0.5rem 1rem;
            margin-bottom: 0.5rem;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
            opacity: 0.9;
        }
        .nav-section-title-collapsible {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            user-select: none;
        }
        .nav-section-title-collapsible:hover {
            color: rgba(255,255,255,0.6);
        }
        .nav-section-toggle-icon {
            width: 14px;
            height: 14px;
            transition: transform 0.2s;
            color: rgba(255,255,255,0.35);
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            margin-bottom: 0.25rem;
        }
        
        .nav-link:hover {
            background: rgba(255,255,255,0.12);
            color: #ffffff;
            transform: translateX(6px);
        }
        .nav-link:hover svg {
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.15));
        }
        
        .nav-link.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            box-shadow: 0 4px 16px rgba(16, 185, 129, 0.35), 0 2px 4px rgba(0,0,0,0.1);
        }
        .nav-link.active svg {
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
        }
        
        .nav-link svg { width: 20px; height: 20px; }
        .nav-badge {
            margin-left: auto;
            padding: 0.125rem 0.5rem;
            background: #10b981;
            color: white;
            border-radius: 9999px;
            font-size: 0.625rem;
            font-weight: 600;
        }
        
        .sidebar-footer {
            padding: 1rem;
            border-top: 1px solid rgba(255,255,255,0.08);
        }
        
        .logout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            color: #fca5a5;
            text-decoration: none;
            font-size: 0.75rem;
            font-weight: 600;
            border: 1px solid rgba(239,68,68,0.15);
            background: rgba(239,68,68,0.1);
            cursor: pointer;
            transition: all 0.2s;
            width: auto;
        }
        
        .logout-btn:hover { 
            background: rgba(239,68,68,0.2);
            color: #fecaca;
            border-color: rgba(239,68,68,0.25);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239,68,68,0.15);
        }
        
        .logout-btn svg { width: 14px; height: 14px; }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
        }
        
        /* Header */
        .top-header {
            background: white;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        .header-title-section h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .header-title-section p {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .notification-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .notification-btn:hover {
            background: #f1f5f9;
            border-color: #10b981;
        }
        
        .notification-btn svg { width: 20px; height: 20px; color: #64748b; }
        
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 18px;
            height: 18px;
            border-radius: 9999px;
            background: #ef4444;
            color: white;
            font-size: 0.625rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
        }
        
        .header-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-left: 1rem;
            border-left: 1px solid #e2e8f0;
        }
        
        .header-user-info { text-align: right; }
        .header-user-name { font-weight: 600; color: #1e293b; font-size: 0.875rem; }
        .header-user-email { font-size: 0.75rem; color: #64748b; }
        
        .header-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #34d399, #38bdf8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }
        
        /* Page Content */
        .page-content {
            padding: 2rem;
        }
        
        /* Cards */
        .page-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            box-shadow: 
                0 10px 40px rgba(0,0,0,0.08),
                0 0 0 1px rgba(255,255,255,0.5) inset,
                0 -2px 10px rgba(16, 185, 129, 0.03) inset;
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .page-card:hover {
            transform: translateY(-4px);
            box-shadow: 
                0 20px 60px rgba(0,0,0,0.12),
                0 0 0 1px rgba(16, 185, 129, 0.1) inset;
        }
        
        .page-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-card-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .page-card-subtitle {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0.25rem 0 0 0;
        }
        
        .page-card-body {
            padding: 1.5rem;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            background-size: 200% 200%;
            color: white;
            box-shadow: 
                0 4px 15px rgba(16, 185, 129, 0.4),
                0 0 0 1px rgba(16, 185, 129, 0.1) inset;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: all 0.5s;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 15px 35px rgba(16, 185, 129, 0.5),
                0 0 25px rgba(16, 185, 129, 0.3) inset;
            background-position: 100% 100%;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-secondary {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            color: #475569;
            border: 1px solid #e2e8f0;
            box-shadow: 
                0 4px 15px rgba(0,0,0,0.05),
                0 0 0 1px rgba(255,255,255,0.5) inset;
        }
        
        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
        .btn svg { width: 16px; height: 16px; flex-shrink: 0; }
        
        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #b91c1c 100%);
            background-size: 200% 200%;
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.35);
        }
        
        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(239, 68, 68, 0.5);
            background-position: 100% 100%;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
            background-size: 200% 200%;
            color: white;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.35);
        }
        
        .btn-warning:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(245, 158, 11, 0.5);
            background-position: 100% 100%;
        }
        
        .btn-sm {
            padding: 0.5rem 0.9rem;
            font-size: 0.8rem;
            border-radius: 8px;
        }
        
        .btn-sm svg { width: 14px; height: 14px; }
        
        .btn-lg {
            padding: 1rem 1.75rem;
            font-size: 1rem;
            border-radius: 14px;
        }
        
        .btn-lg svg { width: 18px; height: 18px; }
        
        .btn-outline {
            background: transparent;
            color: #10b981;
            border: 2px solid #10b981;
            box-shadow: none;
        }
        
        .btn-outline:hover {
            background: #10b981;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }
        
        /* Tables */
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .data-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        
        .data-table tr:hover td {
            background: #f8fafc;
        }
        
        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-error { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        .badge-purple { background: #f3e8ff; color: #9333ea; }
        
        /* Form Elements */
        .form-group { margin-bottom: 1.25rem; }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-input,
        .form-input-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.875rem;
            color: #374151;
            transition: all 0.2s;
        }
        
        .form-input:focus,
        .form-input-textarea:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.875rem;
            color: #374151;
            background: white;
            cursor: pointer;
        }
        
        /* Stat Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        @media (min-width: 768px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
        
        @media (min-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(4, 1fr); }
        }
        
        .stat-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 
                0 8px 30px rgba(0,0,0,0.1),
                0 0 0 1px rgba(255,255,255,0.5) inset;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 
                0 20px 50px rgba(16, 185, 129, 0.2),
                0 0 30px rgba(16, 185, 129, 0.1);
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }
        
        .stat-card.orange::before { background: linear-gradient(90deg, #f97316, #ea580c); }
        .stat-card.cyan::before { background: linear-gradient(90deg, #06b6d4, #3b82f6); }
        .stat-card.purple::before { background: linear-gradient(90deg, #a855f7, #ec4899); }
        .stat-card.rose::before { background: linear-gradient(90deg, #f43f5e, #e11d48); }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .stat-icon.orange { background: #fed7aa; }
        .stat-icon.cyan { background: #cffafe; }
        .stat-icon.purple { background: #f3e8ff; }
        .stat-icon.rose { background: #ffe4e6; }
        
        .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }
        
        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        
        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        
        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem;
        }
        
        .empty-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            border-radius: 20px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .empty-icon svg { width: 40px; height: 40px; color: #94a3b8; }
        .empty-title { color: #475569; font-weight: 600; }
        .empty-text { color: #94a3b8; font-size: 0.875rem; }

        /* Mobile Hamburger */
        .mobile-toggle { display: none; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0; cursor: pointer; margin-right: 1rem; }
        .mobile-toggle svg { width: 20px; height: 20px; color: #64748b; }
        .sidebar-backdrop { display: none; }

        @media (max-width: 768px) {
            .mobile-toggle { display: flex; }
            .sidebar { transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 90; }
            .sidebar-backdrop.show { display: block; }
            .top-header { padding: 1rem; }
            .page-content { padding: 1rem; }
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <!-- Logo -->
            <div class="sidebar-logo">
                <div class="sidebar-logo-inner">
                    <div class="logo-icon">
                        @if(site_logo())
                        <img src="{{ site_logo() }}" alt="{{ app_name() }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                        @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        @endif
                    </div>
                    <div>
                        <div class="logo-text">{{ app_name() }}</div>
                        <div class="logo-subtitle">Member Portal</div>
                    </div>
                </div>
            </div>

            <!-- User Info -->
            <div class="sidebar-user">
                <div class="sidebar-user-inner">
                    <div class="user-avatar">
                        {{ substr(auth()->user()?->first_name ?? 'U', 0, 1) }}
                    </div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()?->full_name ?? 'User' }}</div>
                        <span class="user-tier">{{ auth()->user()?->currentTier?->name ?? 'Bronze' }}</span>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                </div>

                @php
                    $accountRoutes = ['user.accounts.*', 'profile.*', 'referrals.*', 'payments.*', 'manual-payments.*', 'distributions.*', 'vouchers.*', 'unions.*', 'quarterly-benefits.*', 'user.food-assistance.*', 'user.food-support.*'];
                    $isAccountActive = request()->routeIs(implode('|', $accountRoutes));
                @endphp
                <div class="nav-section">
                    <div class="nav-section-title nav-section-title-collapsible" onclick="toggleUserSection('account')">
                        <span>My Account</span>
                        <svg class="nav-section-toggle-icon" id="accountToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div id="accountItems" style="display: {{ $isAccountActive ? 'block' : 'none' }};">
                    <a href="{{ route('user.accounts.index') }}" class="nav-link {{ request()->routeIs('user.accounts.index') || request()->routeIs('user.accounts.create') || request()->routeIs('user.accounts.show') || request()->routeIs('user.accounts.account-transactions') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        My Accounts
                    </a>
                    <a href="{{ route('user.accounts.clearances') }}" class="nav-link {{ request()->routeIs('user.accounts.clearances') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Clearances
                    </a>
                    <a href="{{ route('user.accounts.wallet') }}" class="nav-link {{ request()->routeIs('user.accounts.wallet') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        My Wallet
                    </a>
                    <a href="{{ route('profile.index') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Profile
                    </a>
                    @if(module_enabled('referrals'))
                    <a href="{{ route('referrals.index') }}" class="nav-link {{ request()->routeIs('referrals.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        My Referrals
                        @if(auth()->user()?->direct_referrals_count > 0)
                        <span class="nav-badge">{{ auth()->user()?->direct_referrals_count }}</span>
                        @endif
                    </a>
                    @endif
                    <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Payments
                    </a>
                    @if(module_enabled('manual_payments'))
                    <a href="{{ route('manual-payments.index') }}" class="nav-link {{ request()->routeIs('manual-payments.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Manual Payments
                    </a>
                    @endif
                    @if(module_enabled('distributions'))
                    <a href="{{ route('distributions.index') }}" class="nav-link {{ request()->routeIs('distributions.*') && !request()->routeIs('vouchers.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        My Distributions
                    </a>
                    <a href="{{ route('vouchers.index') }}" class="nav-link {{ request()->routeIs('vouchers.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        My Vouchers
                    </a>
                    @endif
                    @if(module_enabled('unions'))
                    <a href="{{ route('unions.index') }}" class="nav-link {{ request()->routeIs('unions.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        My Unions
                    </a>
                    @endif
                    @if(module_enabled('quarterly_benefits'))
                    <a href="{{ route('quarterly-benefits.index') }}" class="nav-link {{ request()->routeIs('quarterly-benefits.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a4 4 0 00-4-4H6.5a2.5 2.5 0 110 0 2.5 2.5 0 010 5H12m0 0V6a4 4 0 00-4-4h1.5a2.5 2.5 0 110 5H12m0 0V6a4 4 0 014-4h1.5a2.5 2.5 0 110 5H12"></path></svg>
                        Quarterly Benefits
                    </a>
                    @endif
                    @if(module_enabled('food_assistance'))
                    <a href="{{ route('user.food-assistance.index') }}" class="nav-link {{ request()->routeIs('user.food-assistance.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Food Assistance
                    </a>
                    @endif
                    @if(module_enabled('food_support'))
                    <a href="{{ route('user.food-support.index') }}" class="nav-link {{ request()->routeIs('user.food-support.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Food Support
                    </a>
                    @endif
                    </div>
                </div>

                @php
                    $supportRoutes = ['support.*', 'user.incidents.*'];
                    $isUserSupportActive = request()->routeIs(implode('|', $supportRoutes));
                @endphp
                <div class="nav-section">
                    <div class="nav-section-title nav-section-title-collapsible" onclick="toggleUserSection('support')">
                        <span>Support</span>
                        <svg class="nav-section-toggle-icon" id="supportToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div id="supportItems" style="display: {{ $isUserSupportActive ? 'block' : 'none' }};">
                    <a href="{{ route('support.index') }}" class="nav-link {{ request()->routeIs('support.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Support
                    </a>
                    <a href="{{ route('user.incidents.index') }}" class="nav-link {{ request()->routeIs('user.incidents.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        My Incidents
                        @php $userIncidentCount = \App\Models\Incident::where('reported_by', auth()->id())->whereIn('status', ['open', 'in_progress'])->count(); @endphp
                        @if($userIncidentCount > 0)
                        <span class="nav-badge" style="background: #ef4444;">{{ $userIncidentCount }}</span>
                        @endif
                    </a>
                    </div>
                </div>
            </nav>

            <!-- Logout -->
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Header -->
            <header class="top-header">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <button class="mobile-toggle" id="mobileSidebarToggle" aria-label="Toggle sidebar">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div class="header-title-section">
                        <h2>@yield('header', 'Dashboard')</h2>
                        <p>@yield('subheader', 'Welcome back!')</p>
                    </div>
                </div>
                <div class="header-actions">
                    <!-- Notifications -->
                    @php 
                        $unreadCount = auth()->user()?->notifications()?->where('is_read', false)->count() ?? 0;
                        $incidentCount = \App\Models\Incident::where('reported_by', auth()->id())->whereIn('status', ['open', 'in_progress'])->count() ?? 0;
                        $totalCount = $unreadCount + $incidentCount;
                    @endphp
                    <button id="notificationBtn" class="notification-btn" onclick="window.location.href='{{ route('notifications.index') }}'">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if($totalCount > 0)
                        <span class="notification-badge">{{ $totalCount > 99 ? '99+' : $totalCount }}</span>
                        @endif
                    </button>
                    
                    <!-- Account Switcher -->
                    @php $activeAccountId = session('active_account_id'); @endphp
                    @if(auth()->user()->linkedAccounts()->count() > 0)
                    <div style="position: relative; display: inline-block;" id="accountSwitcher">
                        <button onclick="toggleAccountDropdown()" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 1rem; border-radius: 10px; background: #f0fdf4; border: 1px solid #bbf7d0; cursor: pointer; font-size: 0.75rem; font-weight: 600; color: #065f46; transition: all 0.2s;">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Accounts
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div id="accountDropdown" style="display: none; position: absolute; top: 100%; right: 0; margin-top: 0.5rem; min-width: 250px; background: white; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; z-index: 200; overflow: hidden;">
                            <div style="padding: 0.75rem 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                                <p style="font-size: 0.75rem; font-weight: 600; color: #64748b; margin: 0;">Linked Accounts</p>
                            </div>
                            <div style="max-height: 300px; overflow-y: auto;">
                                @foreach(auth()->user()->linkedAccounts as $acc)
                                <form action="{{ route('switch-account', $acc->id) }}" method="POST" style="margin: 0;">
                                    @csrf
                                    <button type="submit" style="display: flex; align-items: center; gap: 0.75rem; width: 100%; padding: 0.75rem 1rem; border: none; background: {{ $activeAccountId == $acc->id ? '#f0fdf4' : 'white' }}; cursor: pointer; font-size: 0.875rem; text-align: left; transition: background 0.2s; border-bottom: 1px solid #f1f5f9;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='{{ $activeAccountId == $acc->id ? '#f0fdf4' : 'white' }}'">
                                        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.75rem;">{{ substr($acc->account_name, 0, 1) }}</div>
                                        <div style="flex: 1;">
                                            <p style="font-weight: 600; color: #1e293b; margin: 0; font-size: 0.875rem;">{{ $acc->account_name }}</p>
                                            <p style="font-size: 0.75rem; color: #64748b; margin: 0;">₦{{ number_format($acc->available_balance, 2) }}</p>
                                        </div>
                                        @if($activeAccountId == $acc->id)
                                        <span class="badge badge-success" style="font-size: 0.625rem;">Active</span>
                                        @endif
                                    </button>
                                </form>
                                @endforeach
                            </div>
                            <a href="{{ route('user.accounts.index') }}" style="display: block; padding: 0.75rem 1rem; text-align: center; color: #10b981; font-weight: 600; font-size: 0.875rem; text-decoration: none; border-top: 1px solid #e2e8f0; transition: background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">Manage Accounts →</a>
                        </div>
                    </div>
                    @endif

                    <!-- User Menu -->
                    <div class="header-user">
                        <div class="header-user-info">
                            <div class="header-user-name">{{ auth()->user()?->full_name ?? 'User' }}</div>
                            <div class="header-user-email">{{ auth()->user()?->email ?? '' }}</div>
                        </div>
                        <div class="header-user-avatar">
                            {{ substr(auth()->user()?->first_name ?? 'U', 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
            <div style="margin: 1rem 2rem 0; padding: 0.75rem 1rem; background: #dcfce7; border: 1px solid #86efac; border-radius: 8px; color: #166534; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div style="margin: 1rem 2rem 0; padding: 0.75rem 1rem; background: #fee2e2; border: 1px solid #fca5a5; border-radius: 8px; color: #991b1b; font-size: 0.875rem; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 18px; height: 18px; flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('error') }}
            </div>
            @endif
            @if($errors->any())
            <div style="margin: 1rem 2rem 0; padding: 0.75rem 1rem; background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; font-size: 0.875rem;">
                <p style="font-weight: 600; color: #92400e; margin: 0 0 0.25rem;">Please fix the following errors:</p>
                <ul style="margin: 0; padding-left: 1.25rem; color: #b45309;">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Page Content -->
            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile sidebar backdrop -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const notificationBtn = document.getElementById('notificationBtn');
        if (notificationBtn) {
            notificationBtn.addEventListener('click', function() {
                window.location.href = '{{ route('notifications.index') }}';
            });
        }

        // Close account dropdown on outside click
        document.addEventListener('click', function(e) {
            const switcher = document.getElementById('accountSwitcher');
            const dropdown = document.getElementById('accountDropdown');
            if (switcher && dropdown && !switcher.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    });

    function toggleAccountDropdown() {
        const dropdown = document.getElementById('accountDropdown');
        if (dropdown) {
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        }
    }

    // Mobile sidebar toggle
    const mobileToggle = document.getElementById('mobileSidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    if (mobileToggle && sidebar) {
        function closeSidebar() {
            sidebar.classList.remove('open');
            if (backdrop) backdrop.classList.remove('show');
        }
        mobileToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            sidebar.classList.toggle('open');
            if (backdrop) backdrop.classList.toggle('show');
        });
        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeSidebar();
        });
    }

    function toggleUserSection(id) {
        var items = document.getElementById(id + 'Items');
        var icon = document.getElementById(id + 'ToggleIcon');
        if (items) {
            var isOpen = items.style.display === 'block';
            items.style.display = isOpen ? 'none' : 'block';
            if (icon) icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }

    @if($isAccountActive)
    document.addEventListener('DOMContentLoaded', function() {
        var icon = document.getElementById('accountToggleIcon');
        if (icon) icon.style.transform = 'rotate(180deg)';
    });
    @endif
    @if($isUserSupportActive)
    document.addEventListener('DOMContentLoaded', function() {
        var icon = document.getElementById('supportToggleIcon');
        if (icon) icon.style.transform = 'rotate(180deg)';
    });
    @endif
    </script>
</body>
</html>
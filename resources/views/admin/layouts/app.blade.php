<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Dashboard') - {{ app_name() }} Admin</title>
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <link rel="icon" type="image/x-icon" href="{{ site_favicon() ?? '/favicon.ico' }}">
    <link rel="apple-touch-icon" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.vite-css')
    <style>
        * { box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index: 1000; justify-content: center; align-items: center; padding: 20px; }
        .modal-overlay.active { display: flex; }
        .modal-box { background: #fff; border-radius: 16px; padding: 32px; max-width: 420px; width: 100%; box-shadow: 0 25px 50px rgba(0,0,0,0.25); animation: modalIn 0.2s ease-out; text-align: center; }
        @keyframes modalIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .modal-icon { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 24px; }
        .modal-icon.warning { background: #fef3c7; color: #d97706; }
        .modal-icon.danger { background: #fee2e2; color: #dc2626; }
        .modal-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
        .modal-text { font-size: 14px; color: #64748b; margin-bottom: 24px; line-height: 1.5; }
        .modal-actions { display: flex; gap: 12px; justify-content: center; }
        .modal-btn { padding: 10px 24px; border-radius: 10px; font-weight: 600; font-size: 14px; border: none; cursor: pointer; transition: all 0.15s; }
        .modal-btn:hover { transform: translateY(-1px); }
        .modal-btn.cancel { background: #f1f5f9; color: #475569; }
        .modal-btn.cancel:hover { background: #e2e8f0; }
        .modal-btn.confirm { background: linear-gradient(135deg, #f97316, #ea580c); color: #fff; box-shadow: 0 4px 14px rgba(249,115,22,0.35); }
        .modal-btn.confirm-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; box-shadow: 0 4px 14px rgba(239,68,68,0.35); }
        .modal-btn.confirm-purple { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: #fff; box-shadow: 0 4px 14px rgba(139,92,246,0.35); }
        .app-wrapper {
            display: flex;
            min-height: 100vh;
        }
        
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
            border-radius: 14px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .logo-icon svg { width: 24px; height: 24px; color: white; }
        .logo-text { font-size: 1.25rem; font-weight: 700; color: #ffffff; }
        .logo-subtitle { font-size: 0.75rem; color: rgba(255,255,255,0.6); }
        
        .sidebar-admin {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        
        .sidebar-admin-inner {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem;
            background: rgba(255,255,255,0.06);
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.08);
        }
        
        .admin-info { flex: 1; }
        .admin-name { font-weight: 600; color: #ffffff; font-size: 0.875rem; }
        .admin-email { font-size: 0.75rem; color: rgba(255,255,255,0.5); }
        
        .sidebar-nav {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
        }
        
        .nav-section { margin-bottom: 1rem; }
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
            background: #ef4444;
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
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            color: #fca5a5;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            background: rgba(239,68,68,0.1);
            border: 1px solid rgba(239,68,68,0.15);
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
        }
        
        .logout-btn:hover { 
            background: rgba(239,68,68,0.2);
            color: #fecaca;
        }
        .logout-btn svg { width: 20px; height: 20px; }
        
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
        }
        
        .top-header {
            background: linear-gradient(135deg, rgba(255,255,255,0.92) 0%, rgba(255,255,255,0.97) 100%);
            backdrop-filter: blur(20px) saturate(1.2);
            -webkit-backdrop-filter: blur(20px) saturate(1.2);
            border-bottom: 1px solid rgba(16,185,129,0.15);
            box-shadow: 0 1px 0 rgba(16,185,129,0.05), 0 4px 20px rgba(0,0,0,0.04);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 50;
        }
        
        .header-title h2 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .header-title p {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .header-btn {
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
        
        .header-btn:hover {
            background: #f1f5f9;
            border-color: #10b981;
        }
        
        .header-btn svg { width: 20px; height: 20px; color: #64748b; }

        .mobile-toggle { display: none; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 10px; background: #f8fafc; border: 1px solid #e2e8f0; cursor: pointer; margin-right: 1rem; position: relative; z-index: 110; }
        .mobile-toggle svg { width: 20px; height: 20px; color: #64748b; }
        .sidebar-backdrop { display: none; }

        @media (max-width: 768px) {
            .mobile-toggle { display: flex; }
            .sidebar { transform: translateX(-100%); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
            .sidebar.open { transform: translateX(0); }
            .sidebar.open ~ .main-content { margin-left: 0; }
            .main-content { margin-left: 0; }
            .sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 90; }
            .sidebar-backdrop.show { display: block; }
            body.sidebar-open { overflow: hidden; }
            .top-header { padding: 1rem; }
            .page-content { padding: 1rem; }
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
        .header-user-role { font-size: 0.75rem; color: #64748b; text-transform: capitalize; }
        
        .header-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, #10b981, #0ea5e9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }
        
        .page-content {
            padding: 2rem;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
        }
        
        .page-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .page-header p {
            color: #64748b;
            margin-top: 0.25rem;
        }
        
        .header-actions-btns {
            display: flex;
            gap: 0.75rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            line-height: 1;
            white-space: nowrap;
        }
        
        .btn svg { width: 16px; height: 16px; flex-shrink: 0; }
        
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
            transition: left 0.5s;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 15px 35px rgba(16, 185, 129, 0.5),
                0 0 25px rgba(16, 185, 129, 0.15) inset;
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
            background: #f1f5f9;
            border-color: #cbd5e1;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        
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
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #10b981, #0ea5e9);
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        }
        
        .stat-card.accent::before { background: linear-gradient(90deg, #0ea5e9, #8b5cf6); }
        .stat-card.warning::before { background: linear-gradient(90deg, #f59e0b, #ef4444); }
        .stat-card.info::before { background: linear-gradient(90deg, #8b5cf6, #ec4899); }
        .stat-card.danger::before { background: linear-gradient(90deg, #ef4444, #f97316); }
        .stat-card.success-card::before { background: linear-gradient(90deg, #10b981, #059669); }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
        }
        
        .stat-icon.emerald { background: #d1fae5; }
        .stat-icon.emerald svg { color: #059669; }
        .stat-icon.sky { background: #dbeafe; }
        .stat-icon.sky svg { color: #2563eb; }
        .stat-icon.amber { background: #fef3c7; }
        .stat-icon.amber svg { color: #d97706; }
        .stat-icon.pink { background: #fce7f3; }
        .stat-icon.pink svg { color: #db2777; }
        .stat-icon.red { background: #fee2e2; }
        .stat-icon.red svg { color: #dc2626; }
        .stat-icon.purple { background: #f3e8ff; }
        .stat-icon.purple svg { color: #9333ea; }
        .stat-icon.orange { background: #ffedd5; }
        .stat-icon.orange svg { color: #ea580c; }
        .stat-icon.indigo { background: #e0e7ff; }
        .stat-icon.indigo svg { color: #4f46e5; }
        .stat-icon.cyan { background: #cffafe; }
        .stat-icon.cyan svg { color: #0891b2; }
        .stat-icon.rose { background: #ffe4e6; }
        .stat-icon.rose svg { color: #e11d48; }
        
        .stat-icon svg { width: 20px; height: 20px; }
        
        .stat-label {
            font-size: 0.875rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }
        
        .stat-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .stat-badge.success { background: #d1fae5; color: #059669; }
        .stat-badge.warning { background: #fef3c7; color: #d97706; }
        .stat-badge.info { background: #dbeafe; color: #2563eb; }
        
        .page-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .page-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
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
        
        .alert-warning {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%);
            border: 1px solid #fde047;
            border-left: 4px solid #eab308;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        
        .alert-warning svg { width: 24px; height: 24px; color: #ca8a04; }
        
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        
        @media (min-width: 1024px) {
            .grid-2 { grid-template-columns: repeat(2, 1fr); }
        }
        
        .progress-bar {
            width: 100%;
            height: 12px;
            background: #f1f5f9;
            border-radius: 9999px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.5s ease;
        }
        
        .progress-fill.emerald { background: linear-gradient(90deg, #10b981, #059669); }
        .progress-fill.amber { background: linear-gradient(90deg, #f59e0b, #d97706); }
        .progress-fill.slate { background: linear-gradient(90deg, #64748b, #475569); }
        
        .list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.2s;
        }
        
        .list-item:hover { background: #f8fafc; }
        
        .list-item:last-child { border-bottom: none; }
        
        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, #10b981, #0ea5e9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .badge-success { background: #dcfce7; color: #16a34a; }
        .badge-warning { background: #fef3c7; color: #d97706; }
        .badge-error { background: #fee2e2; color: #dc2626; }
        .badge-info { background: #dbeafe; color: #2563eb; }
        
        .badge.bronze { background: linear-gradient(135deg, #fcd34d, #f59e0b); color: white; }
        .badge.silver { background: linear-gradient(135deg, #e2e8f0, #94a3b8); color: white; }
        .badge.gold { background: linear-gradient(135deg, #fcd34d, #f59e0b); color: white; }
        .badge.emerald { background: linear-gradient(135deg, #34d399, #059669); color: white; }
        
        .action-link {
            color: #10b981;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        
        .action-link:hover {
            color: #047857;
            transform: translateX(2px);
        }
        
        .quick-actions-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        @media (min-width: 768px) {
            .quick-actions-grid { grid-template-columns: repeat(4, 1fr); }
        }
        
        @media (min-width: 1024px) {
            .quick-actions-grid { grid-template-columns: repeat(6, 1fr); }
        }
        
        .quick-action {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.25rem;
            background: white;
            border-radius: 12px;
            text-decoration: none;
            border: 2px solid transparent;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .quick-action:hover {
            border-color: #10b981;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.15);
        }
        
        .quick-action-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
        }
        
        .quick-action-icon svg { width: 24px; height: 24px; }
        
        .quick-action-icon.blue { background: #dbeafe; }
        .quick-action-icon.blue svg { color: #2563eb; }
        .quick-action-icon.green { background: #d1fae5; }
        .quick-action-icon.green svg { color: #059669; }
        .quick-action-icon.yellow { background: #fef3c7; }
        .quick-action-icon.yellow svg { color: #d97706; }
        .quick-action-icon.purple { background: #f3e8ff; }
        .quick-action-icon.purple svg { color: #9333ea; }
        .quick-action-icon.orange { background: #ffedd5; }
        .quick-action-icon.orange svg { color: #ea580c; }
        .quick-action-icon.pink { background: #fce7f3; }
        .quick-action-icon.pink svg { color: #db2777; }
        
        .quick-action-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table th {
            padding: 0.75rem 1rem;
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
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #475569;
            border-bottom: 1px solid #f1f5f9;
        }

        .data-table tr:hover td {
            background: #f8fafc;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
            margin-bottom: 0.5rem;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.875rem;
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: #94a3b8;
        }

        .empty-state svg {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            display: block;
            color: #cbd5e1;
        }

        .empty-state p {
            font-size: 0.875rem;
            margin: 0;
        }

        .badge-primary { background: #dbeafe; color: #2563eb; }
        .badge-dark { background: #e2e8f0; color: #475569; }
        .badge-purple { background: #f3e8ff; color: #9333ea; }

        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .alert-success {
            background: #dcfce7;
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-info {
            background: #dbeafe;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem;
            flex-wrap: wrap;
        }

        .pagination a,
        .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 0.5rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            color: #475569;
            background: white;
            border: 1px solid #e2e8f0;
            transition: all 0.2s;
        }

        .pagination a:hover {
            border-color: #10b981;
            color: #10b981;
        }

        .pagination .active span {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-color: transparent;
        }

        .filter-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            padding: 1.5rem;
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
                        <img src="{{ site_logo() }}" alt="{{ app_name() }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 14px;">
                        @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        @endif
                    </div>
                    <div>
                        <div class="logo-text">{{ app_name() }}</div>
                        <div class="logo-subtitle">Admin Portal</div>
                    </div>
                </div>
            </div>

            <!-- Admin Info -->
            <div class="sidebar-admin">
                <div class="sidebar-admin-inner">
                    <div class="admin-info">
                        <div class="admin-name">{{ auth()->guard('admin')->user()->name ?? 'Admin' }}</div>
                        <div class="admin-email">{{ auth()->guard('admin')->user()->email ?? '' }}</div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Users
                    </a>
                </div>

                @php $admin = auth()->guard('admin')->user(); @endphp

                @if($admin->isSuperAdmin())
                @php
                    $managementRoutes = ['admin.membership-tiers.*', 'admin.payments.*', 'admin.manual-payments.*', 'admin.vouchers.*', 'admin.bank-accounts.*', 'admin.food-loans.*', 'admin.food-assistance-tiers.*', 'admin.food-assistance.*', 'admin.food-support.*', 'admin.savings-plans.*'];
                    $isManagementActive = request()->routeIs(implode('|', $managementRoutes));
                @endphp
                <div class="nav-section">
                    <div class="nav-section-title nav-section-title-collapsible" onclick="toggleManagement()">
                        <span>Management</span>
                        <svg class="nav-section-toggle-icon" id="managementToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div id="managementItems" style="display: {{ $isManagementActive ? 'block' : 'none' }};">
                    <a href="{{ route('admin.membership-tiers.index') }}" class="nav-link {{ request()->routeIs('admin.membership-tiers.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Membership Tiers
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Payments
                    </a>
                    <a href="{{ route('admin.manual-payments.index') }}" class="nav-link {{ request()->routeIs('admin.manual-payments.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Manual Payments
                    </a>
                    <a href="{{ route('admin.vouchers.index') }}" class="nav-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        Vouchers
                    </a>
                    <a href="{{ route('admin.bank-accounts.index') }}" class="nav-link {{ request()->routeIs('admin.bank-accounts.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Bank Accounts
                    </a>
                    <a href="{{ route('admin.food-loans.index') }}" class="nav-link {{ request()->routeIs('admin.food-loans.*') && !request()->routeIs('admin.food-loans.eligibility-rules') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Food Loans
                    </a>
                    <a href="{{ route('admin.food-loans.eligibility-rules') }}" class="nav-link {{ request()->routeIs('admin.food-loans.eligibility-rules') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        Eligibility Rules
                    </a>
                    <a href="{{ route('admin.food-assistance-tiers.index') }}" class="nav-link {{ request()->routeIs('admin.food-assistance-tiers.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Assistance Tiers
                    </a>
                    <a href="{{ route('admin.food-assistance.index') }}" class="nav-link {{ request()->routeIs('admin.food-assistance.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Food Assistance
                    </a>
                    <a href="{{ route('admin.food-support.index') }}" class="nav-link {{ request()->routeIs('admin.food-support.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Food Support
                    </a>
                    <a href="{{ route('admin.savings-plans.index') }}" class="nav-link {{ request()->routeIs('admin.savings-plans.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Savings Plans
                    </a>
                    </div>
                </div>
                @endif

                @if($admin->isSuperAdmin() || $admin->isInventoryManager())
                @php
                    $operationsRoutes = ['admin.distributions.*', 'admin.clearances.*', 'admin.inventory.*', 'admin.food-items.*', 'admin.packages.*', 'admin.locations.*', 'admin.issuance.*', 'admin.branches.*', 'admin.departments.*'];
                    $isOperationsActive = request()->routeIs(implode('|', $operationsRoutes));
                @endphp
                <div class="nav-section">
                    <div class="nav-section-title nav-section-title-collapsible" onclick="toggleOperations()">
                        <span>Operations</span>
                        <svg class="nav-section-toggle-icon" id="operationsToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div id="operationsItems" style="display: {{ $isOperationsActive ? 'block' : 'none' }};">
                    <a href="{{ route('admin.distributions.index') }}" class="nav-link {{ request()->routeIs('admin.distributions.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Distributions
                    </a>
                    @if($admin->isSuperAdmin())
                    <a href="{{ route('admin.clearances.index') }}" class="nav-link {{ request()->routeIs('admin.clearances.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Clearances
                    </a>
                    @endif
                    <a href="{{ route('admin.inventory.index') }}" class="nav-link {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Inventory
                    </a>
                    <a href="{{ route('admin.food-items.index') }}" class="nav-link {{ request()->routeIs('admin.food-items.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Food Items
                    </a>
                    <a href="{{ route('admin.packages.index') }}" class="nav-link {{ request()->routeIs('admin.packages.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Packages
                    </a>
                    <a href="{{ route('admin.locations.index') }}" class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Pickup Locations
                    </a>
                    <a href="{{ route('admin.issuance.index') }}" class="nav-link {{ request()->routeIs('admin.issuance.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Issuance
                    </a>
                    <a href="{{ route('admin.branches.index') }}" class="nav-link {{ request()->routeIs('admin.branches.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Branches
                    </a>
                    <a href="{{ route('admin.departments.index') }}" class="nav-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Departments
                    </a>
                    </div>
                </div>
                @endif

                @if($admin->isSuperAdmin() || $admin->isInventoryManager())
                @php
                    $marketplaceRoutes = ['admin.marketplace.*'];
                    $isMarketplaceActive = request()->routeIs(implode('|', $marketplaceRoutes));
                @endphp
                <div class="nav-section">
                    <div class="nav-section-title nav-section-title-collapsible" onclick="toggleMarketplace()">
                        <span>Marketplace</span>
                        <svg class="nav-section-toggle-icon" id="marketplaceToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div id="marketplaceItems" style="display: {{ $isMarketplaceActive ? 'block' : 'none' }};">
                    <a href="{{ route('admin.marketplace.dashboard') }}" class="nav-link {{ request()->routeIs('admin.marketplace.dashboard') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        Marketplace Overview
                    </a>
                    <a href="{{ route('admin.marketplace.products.index') }}" class="nav-link {{ request()->routeIs('admin.marketplace.products.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        Products
                    </a>
                    <a href="{{ route('admin.marketplace.orders.index') }}" class="nav-link {{ request()->routeIs('admin.marketplace.orders.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Orders
                        @php $mpPending = \App\Models\Marketplace\Order::whereIn('status', ['pending', 'paid'])->count(); @endphp
                        @if($mpPending > 0)<span class="nav-badge">{{ $mpPending }}</span>@endif
                    </a>
                    <a href="{{ route('admin.marketplace.vendors.index') }}" class="nav-link {{ request()->routeIs('admin.marketplace.vendors.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Vendors
                        @php $mpPendingVendors = \App\Models\Marketplace\Vendor::where('status', 'pending')->count(); @endphp
                        @if($mpPendingVendors > 0)<span class="nav-badge">{{ $mpPendingVendors }}</span>@endif
                    </a>
                    <a href="{{ route('admin.marketplace.categories.index') }}" class="nav-link {{ request()->routeIs('admin.marketplace.categories.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        Categories
                    </a>
                    <a href="{{ route('admin.marketplace.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.marketplace.coupons.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                        Coupons
                    </a>
                    <a href="{{ route('admin.marketplace.flash-sales.index') }}" class="nav-link {{ request()->routeIs('admin.marketplace.flash-sales.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Flash Sales
                    </a>
                    <a href="{{ route('admin.marketplace.reviews.index') }}" class="nav-link {{ request()->routeIs('admin.marketplace.reviews.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        Reviews
                        @php $mpPendingReviews = \App\Models\Marketplace\Review::where('is_approved', false)->count(); @endphp
                        @if($mpPendingReviews > 0)<span class="nav-badge">{{ $mpPendingReviews }}</span>@endif
                    </a>
                    <a href="{{ route('admin.marketplace.delivery-zones.index') }}" class="nav-link {{ request()->routeIs('admin.marketplace.delivery-zones.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Delivery Zones
                    </a>
                    <a href="{{ route('admin.marketplace.bulk-orders.index') }}" class="nav-link {{ request()->routeIs('admin.marketplace.bulk-orders.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        Bulk Orders
                    </a>
                    <a href="{{ route('admin.marketplace.wallet-transactions.index') }}" class="nav-link {{ request()->routeIs('admin.marketplace.wallet-transactions.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Wallet Transactions
                    </a>
                    <a href="{{ route('admin.marketplace.reports') }}" class="nav-link {{ request()->routeIs('admin.marketplace.reports') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Reports
                    </a>
                    </div>
                </div>
                @endif

                @if($admin->isSuperAdmin() || $admin->isCustomerSupport())
                @php
                    $supportRoutes = ['admin.incidents.*', 'admin.support.*', 'admin.reports.*'];
                    $isSupportActive = request()->routeIs(implode('|', $supportRoutes));
                @endphp
                <div class="nav-section">
                    <div class="nav-section-title nav-section-title-collapsible" onclick="toggleSupport()">
                        <span>Support</span>
                        <svg class="nav-section-toggle-icon" id="supportToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div id="supportItems" style="display: {{ $isSupportActive ? 'block' : 'none' }};">
                    <a href="{{ route('admin.incidents.index') }}" class="nav-link {{ request()->routeIs('admin.incidents.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Incidents
                        @php $openIncidents = \App\Models\Incident::whereIn('status', ['open', 'in_progress'])->count(); @endphp
                        @if($openIncidents > 0)
                        <span class="nav-badge">{{ $openIncidents }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.support.index') }}" class="nav-link {{ request()->routeIs('admin.support.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Support Tickets
                    </a>
                    <a href="{{ route('admin.reports.overview') }}" class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Reports
                    </a>
                    </div>
                </div>
                @endif

                @if($admin->isSuperAdmin())
                @php
                    $contentRoutes = ['admin.page-contents.*', 'admin.posts.*'];
                    $isContentActive = request()->routeIs(implode('|', $contentRoutes));
                @endphp
                <div class="nav-section">
                    <div class="nav-section-title nav-section-title-collapsible" onclick="toggleContent()">
                        <span>Content</span>
                        <svg class="nav-section-toggle-icon" id="contentToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div id="contentItems" style="display: {{ $isContentActive ? 'block' : 'none' }};">
                    <a href="{{ route('admin.page-contents.index') }}" class="nav-link {{ request()->routeIs('admin.page-contents.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Pages
                    </a>
                    <a href="{{ route('admin.posts.index') }}" class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        Blog Posts
                    </a>
                    </div>
                </div>
                @php
                    $settingsRoutes = ['admin.settings.*', 'admin.admin-users.*'];
                    $isSettingsActive = request()->routeIs(implode('|', $settingsRoutes));
                @endphp
                <div class="nav-section">
                    <div class="nav-section-title nav-section-title-collapsible" onclick="toggleSettings()">
                        <span>Settings</span>
                        <svg class="nav-section-toggle-icon" id="settingsToggleIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                    <div id="settingsItems" style="display: {{ $isSettingsActive ? 'block' : 'none' }};">
                    <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        General Settings
                    </a>
                    <a href="{{ route('admin.settings.index') }}#payment-settings" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Payment Settings
                    </a>
                    <a href="{{ route('admin.admin-users.index') }}" class="nav-link {{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Admin Users
                    </a>
                    </div>
                </div>
                @endif
            </nav>

            <!-- Logout -->
            <div class="sidebar-footer">
                <form method="POST" action="{{ route('admin.logout') }}">
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
                <div style="display: flex; align-items: center;">
                    <button class="mobile-toggle" id="mobileSidebarToggle" aria-label="Toggle sidebar">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div class="header-title">
                        <h2>@yield('header', 'Dashboard')</h2>
                        <p>@yield('subheader', 'Manage your FoodChain platform')</p>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="header-btn">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>
                    <div class="header-user">
                        <div class="header-user-info">
                            <div class="header-user-name">{{ auth()->guard('admin')->user()->name ?? 'Admin' }}</div>
                            <div class="header-user-role">{{ ucfirst(auth()->guard('admin')->user()->role ?? 'admin') }}</div>
                        </div>
                        <div class="header-user-avatar">
                            {{ substr(auth()->guard('admin')->user()->name ?? 'A', 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="page-content">
                @yield('content')
            </main>
        </div>
    </div>

    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    @stack('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var mobileToggle = document.getElementById('mobileSidebarToggle');
        var sidebar = document.querySelector('.sidebar');
        var backdrop = document.getElementById('sidebarBackdrop');
        if (mobileToggle && sidebar) {
            function closeSidebar() {
                sidebar.classList.remove('open');
                if (backdrop) backdrop.classList.remove('show');
                document.body.classList.remove('sidebar-open');
            }
            mobileToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('open');
                if (backdrop) backdrop.classList.toggle('show');
                document.body.classList.toggle('sidebar-open');
            });
            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closeSidebar();
            });
            sidebar.querySelectorAll('a').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 768) closeSidebar();
                });
            });
        }
    });

    function toggleSection(id) {
        var items = document.getElementById(id + 'Items');
        var icon = document.getElementById(id + 'ToggleIcon');
        if (items) {
            var isOpen = items.style.display === 'block';
            items.style.display = isOpen ? 'none' : 'block';
            if (icon) icon.style.transform = isOpen ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    }
    function toggleManagement() { toggleSection('management'); }
    function toggleOperations() { toggleSection('operations'); }
    function toggleSupport() { toggleSection('support'); }
    function toggleContent() { toggleSection('content'); }
    function toggleSettings() { toggleSection('settings'); }
    function toggleMarketplace() { toggleSection('marketplace'); }

    @if($isManagementActive)
    document.addEventListener('DOMContentLoaded', function() {
        var icon = document.getElementById('managementToggleIcon');
        if (icon) icon.style.transform = 'rotate(180deg)';
    });
    @endif
    @if($isOperationsActive)
    document.addEventListener('DOMContentLoaded', function() {
        var icon = document.getElementById('operationsToggleIcon');
        if (icon) icon.style.transform = 'rotate(180deg)';
    });
    @endif
    @if($isSupportActive)
    document.addEventListener('DOMContentLoaded', function() {
        var icon = document.getElementById('supportToggleIcon');
        if (icon) icon.style.transform = 'rotate(180deg)';
    });
    @endif
    @if($isContentActive)
    document.addEventListener('DOMContentLoaded', function() {
        var icon = document.getElementById('contentToggleIcon');
        if (icon) icon.style.transform = 'rotate(180deg)';
    });
    @endif
    @if($isSettingsActive)
    document.addEventListener('DOMContentLoaded', function() {
        var icon = document.getElementById('settingsToggleIcon');
        if (icon) icon.style.transform = 'rotate(180deg)';
    });
    @endif
    @if($isMarketplaceActive)
    document.addEventListener('DOMContentLoaded', function() {
        var icon = document.getElementById('marketplaceToggleIcon');
        if (icon) icon.style.transform = 'rotate(180deg)';
    });
    @endif
    </script>

<div id="confirmModal" class="modal-overlay">
    <div class="modal-box">
        <div id="modalIcon" class="modal-icon warning">
            <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
        </div>
        <div class="modal-title" id="modalTitle">Are you sure?</div>
        <div class="modal-text" id="modalText">This action cannot be undone.</div>
        <div class="modal-actions">
            <button class="modal-btn cancel" onclick="closeConfirmModal()">Cancel</button>
            <button class="modal-btn confirm" id="modalConfirmBtn" onclick="executeConfirm()">Confirm</button>
        </div>
    </div>
</div>

<script>
let pendingConfirm = null;

function openConfirmModal(config) {
    var overlay = document.getElementById('confirmModal');
    document.getElementById('modalTitle').textContent = config.title || 'Are you sure?';
    document.getElementById('modalText').textContent = config.text || 'This action cannot be undone.';
    
    var iconEl = document.getElementById('modalIcon');
    iconEl.className = 'modal-icon ' + (config.iconClass || 'warning');
    iconEl.innerHTML = config.icon || '<svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>';
    
    var btn = document.getElementById('modalConfirmBtn');
    btn.className = 'modal-btn ' + (config.btnClass || 'confirm');
    btn.textContent = config.btnText || 'Confirm';
    
    pendingConfirm = config.onConfirm;
    overlay.classList.add('active');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.remove('active');
    pendingConfirm = null;
}

function executeConfirm() {
    if (pendingConfirm) pendingConfirm();
    closeConfirmModal();
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-confirm]').forEach(function(el) {
        el.addEventListener('click', function(e) {
            e.preventDefault();
            var data = JSON.parse(el.getAttribute('data-confirm'));
            openConfirmModal({
                title: data.title || 'Are you sure?',
                text: data.text || '',
                iconClass: data.iconClass || 'warning',
                icon: data.icon || undefined,
                btnClass: data.btnClass || 'confirm',
                btnText: data.btnText || 'Confirm',
                onConfirm: function() {
                    if (el.tagName === 'FORM') {
                        el.submit();
                    } else if (el.tagName === 'BUTTON' && el.type === 'submit') {
                        el.closest('form').submit();
                    } else {
                        var form = el.closest('form');
                        if (form) form.submit();
                    }
                }
            });
        });
    });
});
</script>
</body>
</html>
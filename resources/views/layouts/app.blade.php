<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - {{ $header ?? 'Dashboard' }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <link rel="icon" type="image/x-icon" href="{{ site_favicon() ?? '/favicon.ico' }}">
    <link rel="apple-touch-icon" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }
        .app-wrapper { min-height: 100vh; display: flex; flex-direction: column; }
        .page-content { flex: 1; padding: 2rem; max-width: 1280px; margin: 0 auto; width: 100%; }
        @media (max-width: 768px) { .page-content { padding: 1rem; } }

        .page-header {
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

        .page-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: all 0.3s;
        }
        .page-card:hover {
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
        .page-card-body {
            padding: 1.5rem;
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
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
            background-size: 200% 200%;
            color: white;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4);
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(16, 185, 129, 0.5);
            background-position: 100% 100%;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #475569;
            margin-bottom: 0.5rem;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 0.875rem;
            background: white;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-family: inherit;
        }
        .form-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        .error-text {
            font-size: 0.8rem;
            color: #dc2626;
            margin-top: 0.25rem;
        }
        .status-msg {
            font-size: 0.9rem;
            color: #16a34a;
            font-weight: 500;
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
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.12);
        }
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

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .grid-2 { grid-template-columns: repeat(2, 1fr); }
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
    </style>
</head>
<body>
    <div class="app-wrapper">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header style="background: white; border-bottom: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="max-width: 1280px; margin: 0 auto; padding: 1.5rem 2rem;">
                <h1 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0;">{{ $header }}</h1>
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main class="page-content">
            {{ $slot }}
        </main>
    </div>
</body>
</html>

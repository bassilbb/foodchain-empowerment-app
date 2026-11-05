<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'My Account') - {{ app_name() }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @include('partials.vite-css')
    @stack('styles')
    <style>
        /* Member sidebar — Admin Dashboard exact visual/behavior reference (single source of truth) */
        #accountSidebar.sidebar {
            flex: 0 0 264px;
            width: 264px;
            min-width: 264px;
            max-width: 264px;
            box-sizing: border-box;
            background: linear-gradient(180deg, #073f32 0%, #06382d 52%, #04251e 100%);
            border-right: 1px solid rgba(255,255,255,0.05);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 4px 0 30px rgba(0,0,0,0.22);
            position: relative;
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        #accountSidebar .sidebar-glow {
            position: absolute; left: -30%; top: -8rem; width: 18rem; height: 18rem;
            background: rgba(16,185,129,0.18); border-radius: 9999px;
            filter: blur(48px); pointer-events: none;
        }
        #accountSidebar .sidebar-logo { padding: 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; }
        #accountSidebar .sidebar-logo-inner { display: flex; align-items: center; gap: 0.875rem; text-decoration: none; min-width: 0; }
        #accountSidebar .sidebar-logo-inner .logo-icon { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; background: rgba(255,255,255,0.08); }
        #accountSidebar .sidebar-logo-inner .logo-icon img { width: 100%; height: 100%; object-fit: contain; border-radius: 14px; padding: 4px; }
        #accountSidebar .logo-text { font-size: 1.125rem; font-weight: 800; color: #fff; }
        #accountSidebar .logo-subtitle { font-size: 0.65rem; color: #ffffff; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500; margin-top: 1px; opacity: 0.9; }
        #accountSidebar .sidebar-admin { padding: 0 1.5rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.08); position: relative; }
        #accountSidebar .sidebar-admin-inner { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: rgba(255,255,255,0.06); border-radius: 12px; border: 1px solid rgba(255,255,255,0.08); min-width: 0; }
        #accountSidebar .admin-avatar { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 0.875rem; font-weight: 700; color: #fff; background: linear-gradient(135deg, #34d399, #14b8a6); flex-shrink: 0; }
        #accountSidebar .admin-info { flex: 1; min-width: 0; }
        #accountSidebar .admin-name { font-weight: 600; color: #ffffff; font-size: 0.875rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        #accountSidebar .admin-email { font-size: 0.75rem; color: rgba(255,255,255,0.5); overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        #accountSidebar .sidebar-nav { flex: 1; padding: 1rem; overflow-y: auto; position: relative; }
        #accountSidebar .nav-section { margin-bottom: 1rem; }
        #accountSidebar .nav-section-title { font-size: 0.675rem; font-weight: 700; color: #ffffff; text-transform: uppercase; letter-spacing: 0.08em; padding: 0.5rem 1rem; margin-bottom: 0.5rem; text-shadow: 0 1px 3px rgba(0,0,0,0.2); opacity: 0.9; }
        #accountSidebar .nav-link { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 10px; color: rgba(255,255,255,0.88); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all 0.2s; margin-bottom: 0.25rem; min-width: 0; }
        #accountSidebar .nav-link:hover { background: rgba(255,255,255,0.12); color: #ffffff; transform: translateX(6px); }
        #accountSidebar .nav-link:hover svg { filter: drop-shadow(0 1px 2px rgba(0,0,0,0.15)); }
        #accountSidebar .nav-link.active { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.35), 0 2px 4px rgba(0,0,0,0.1); }
        #accountSidebar .nav-link.active svg { filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1)); }
        #accountSidebar .nav-link svg { width: 20px; height: 20px; flex-shrink: 0; }
        #accountSidebar .nav-link span.nav-label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: inherit; }
        #accountSidebar .nav-badge { margin-left: auto; padding: 0.125rem 0.5rem; background: #ef4444; color: white; border-radius: 9999px; font-size: 0.625rem; font-weight: 600; flex-shrink: 0; }
        #accountSidebar .sidebar-footer { padding: 1rem; border-top: 1px solid rgba(255,255,255,0.08); background: transparent; position: relative; }
        #accountSidebar .logout-btn { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 10px; color: #fca5a5; text-decoration: none; font-size: 0.875rem; font-weight: 500; background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.15); cursor: pointer; transition: all 0.2s; width: 100%; }
        #accountSidebar .logout-btn:hover { background: rgba(239,68,68,0.2); color: #fecaca; }
        #accountSidebar .logout-btn svg { width: 20px; height: 20px; }
        #accountSidebar .footer-link { display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.625rem 1rem; border-radius: 10px; color: rgba(255,255,255,0.88); text-decoration: none; font-size: 0.8125rem; font-weight: 500; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); transition: all 0.2s; width: 100%; margin-bottom: 0.5rem; }
        #accountSidebar .footer-link:hover { background: rgba(255,255,255,0.12); color: #fff; }
        #accountSidebar .sidebar-notice { border-radius: 12px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.08); padding: 0.75rem 1rem; margin: 1rem 0 0; }
        #accountSidebar .sidebar-notice-title { font-size: 0.75rem; font-weight: 700; color: #fff; }
        #accountSidebar .sidebar-notice-text { margin-top: 0.25rem; font-size: 0.7rem; line-height: 1.45; color: rgba(255,255,255,0.72); }
        #accountSidebar .sidebar-notice .footer-link { margin-top: 0.625rem; margin-bottom: 0; background: rgba(255,255,255,0.12); }

        .account-main { transition: margin-left .28s cubic-bezier(.4, 0, .2, 1); }
        [data-dropdown-panel].open { display: block; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

        @media (max-width: 1023px) {
            #accountSidebar.sidebar { position: fixed; top: 0; left: 0; bottom: 0; height: 100vh; width: 264px; min-width: 264px; max-width: 264px; transform: translateX(-100%); z-index: 50; }
            #accountSidebar.sidebar.open { transform: translateX(0); }
            #accountSidebarBackdrop:not(.hidden) { display: block !important; }
        }
        @media (min-width: 1024px) {
            #accountSidebar.sidebar { position: sticky; top: 0; height: 100vh; flex: 0 0 264px; width: 264px; min-width: 264px; max-width: 264px; flex-shrink: 0; transform: translateX(0) !important; z-index: 30; }
            #accountSidebar.sidebar.collapsed { display: none !important; }
            #accountSidebarBackdrop { display: none !important; }
        }
    </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-emerald-50/80 via-slate-50 to-teal-50/60 text-slate-800 antialiased">

    {{-- Flash messages --}}
    @if (session('success') || session('error') || session('info') || session('warning'))
    <div class="fixed right-4 top-4 z-[100] w-80 space-y-2" id="flash-container">
        @foreach (['success' => ['border-emerald-500', 'text-emerald-500', '✓'], 'error' => ['border-red-500', 'text-red-500', '✕'], 'info' => ['border-blue-500', 'text-blue-500', 'ℹ'], 'warning' => ['border-amber-500', 'text-amber-500', '!']] as $key => $meta)
        @if (session($key))
        <div class="flash-item flex items-start gap-3 rounded-xl border-l-4 {{ $meta[0] }} bg-white p-4 shadow-lg">
            <span class="{{ $meta[1] }} mt-0.5 text-lg leading-none">{{ $meta[2] }}</span>
            <p class="flex-1 text-sm text-slate-800">{{ session($key) }}</p>
            <button type="button" class="close-flash text-slate-400 hover:text-slate-600">✕</button>
        </div>
        @endif
        @endforeach
    </div>
    @endif

    <div class="flex min-h-screen w-full items-start">

        {{-- Sidebar: Admin-exact 264px, exact green, drawer on mobile / in-flow on desktop (no overlap) --}}
        <aside id="accountSidebar" class="sidebar">
            <div aria-hidden="true" class="sidebar-glow"></div>

            <div class="sidebar-logo">
                <a href="{{ route('marketplace.home') }}" class="sidebar-logo-inner">
                    <div class="logo-icon">
                        <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo">
                    </div>
                    <div>
                        <div class="logo-text">{{ app_name() }}</div>
                        <div class="logo-subtitle">Marketplace</div>
                    </div>
                </a>
                <button type="button" id="accountSidebarClose"
                    class="lg:hidden" style="position:absolute; right:1rem; top:1.5rem; display:inline-flex; align-items:center; justify-content:center; width:2rem; height:2rem; border-radius:0.5rem; color:rgba(255,255,255,0.8); background:transparent; border:0; cursor:pointer;"
                    aria-label="Close navigation">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:1rem;height:1rem;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="sidebar-admin">
                <div class="sidebar-admin-inner">
                    <span class="admin-avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()->first_name ?? auth()->user()->name ?? 'U', 0, 1)) }}</span>
                    <div class="admin-info">
                        <div class="admin-name">{{ auth()->user()->full_name ?? auth()->user()->name }}</div>
                        <div class="admin-email">{{ auth()->user()->email }}</div>
                    </div>
                </div>
            </div>

            @include('account.partials.sidebar')
        </aside>

        {{-- Mobile backdrop --}}
        <div id="accountSidebarBackdrop" class="fixed inset-0 z-40 hidden bg-slate-900/50 lg:hidden"></div>

        {{-- Main column (flex sibling of sidebar on desktop; no margin overlap) --}}
        <div class="account-main flex min-w-0 w-full flex-1 flex-col">
            {{-- Ambient background orbs (depth / 3D atmosphere) --}}
            <div aria-hidden="true" class="pointer-events-none fixed bottom-10 right-10 h-72 w-72 rounded-full bg-emerald-200/30 blur-3xl animate-float"></div>
            <div aria-hidden="true" class="pointer-events-none fixed -left-16 top-1/3 h-80 w-80 rounded-full bg-teal-200/25 blur-3xl animate-float" style="animation-delay: 1.5s"></div>

            @include('account.partials.header')

            <main class="relative z-10 min-w-0 flex-1 animate-slide-up px-4 py-5 sm:px-6 sm:py-6 lg:px-8">
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 px-4 py-5 text-center text-xs text-slate-400 sm:px-6">
                &copy; {{ date('Y') }} {{ app_name() }}. All rights reserved.
            </footer>
        </div>
    </div>

    @stack('scripts')
    <script>
        // ---- Sidebar drawer ----
        (function() {
            const sidebar = document.getElementById('accountSidebar');
            const backdrop = document.getElementById('accountSidebarBackdrop');
            const isDesktop = () => window.innerWidth >= 1024;
            const open = () => {
                if (isDesktop()) {
                    sidebar.classList.add('collapsed');
                    return;
                }
                sidebar.classList.add('open');
                backdrop.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };
            const close = () => {
                sidebar.classList.remove('open');
                sidebar.classList.remove('collapsed');
                backdrop.classList.add('hidden');
                document.body.style.overflow = '';
            };

            document.getElementById('accountSidebarToggle')?.addEventListener('click', () => {
                if (isDesktop()) {
                    sidebar.classList.toggle('collapsed');
                } else {
                    sidebar.classList.contains('open') ? close() : open();
                }
            });
            document.getElementById('accountSidebarClose')?.addEventListener('click', close);
            backdrop?.addEventListener('click', close);
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') close();
            });
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) close();
            });
        })();

        // ---- Dropdowns (notifications + profile) ----
        (function() {
            const all = document.querySelectorAll('[data-dropdown]');
            const closeAll = () => all.forEach(d => d.querySelector('[data-dropdown-panel]')?.classList.remove('open'));

            all.forEach(dropdown => {
                const trigger = dropdown.querySelector('[data-dropdown-trigger]');
                const panel = dropdown.querySelector('[data-dropdown-panel]');
                if (!trigger || !panel) return;

                trigger.addEventListener('click', e => {
                    e.stopPropagation();
                    const isOpen = panel.classList.contains('open');
                    closeAll();
                    if (!isOpen) panel.classList.add('open');
                });
                panel.addEventListener('click', e => e.stopPropagation());
            });

            document.addEventListener('click', closeAll);
        })();

        // ---- Confirmation dialogs for destructive actions ----
        document.addEventListener('submit', function (e) {
            const form = e.target;
            const message = form.getAttribute('data-confirm');
            if (message && ! window.confirm(message)) {
                e.preventDefault();
            }
        });

        // ---- Flash auto-dismiss ----
        document.querySelectorAll('.flash-item').forEach(item => {
            setTimeout(() => item.classList.add('opacity-0', 'transition-opacity', 'duration-500'), 5000);
            setTimeout(() => item.remove(), 5500);
            item.querySelector('.close-flash')?.addEventListener('click', () => item.remove());
        });

        window.formatNaira = v => '₦' + Number(v || 0).toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    </script>
</body>

</html>
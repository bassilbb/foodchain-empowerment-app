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
        /* Sidebar drawer transitions — kept minimal; everything else is Tailwind. */
        #accountSidebar {
            transition: transform .28s cubic-bezier(.4, 0, .2, 1);
        }

        @media (max-width: 1023px) {
            #accountSidebar {
                transform: translateX(-100%);
            }

            #accountSidebar.open {
                transform: translateX(0);
            }
        }

        @media (min-width: 1024px) {
            #accountSidebar.collapsed {
                transform: translateX(-100%);
            }

            #accountSidebar.collapsed ~ .account-main {
                margin-left: 0;
            }
        }

        .account-main {
            transition: margin-left .28s cubic-bezier(.4, 0, .2, 1);
        }

        [data-dropdown-panel].open {
            display: block;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
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

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside id="accountSidebar"
            class="fixed inset-y-0 left-0 z-50 flex w-[264px] flex-col bg-gradient-to-b from-emerald-950 via-emerald-900 to-teal-950 shadow-2xl shadow-emerald-950/40 ring-1 ring-white/10 ring-inset">

            {{-- Top glow --}}
            <div aria-hidden="true" class="pointer-events-none absolute inset-x-0 top-0 h-40 bg-gradient-to-b from-emerald-400/20 to-transparent"></div>

            {{-- Brand --}}
            <div class="flex items-center gap-3 border-b border-white/10 px-4 py-4">
                <a href="{{ route('marketplace.home') }}" class="flex items-center gap-2.5">
                    <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="h-10 w-10 rounded-xl object-contain bg-white ring-1 ring-white/15">
                    <span class="leading-tight">
                        <span class="block text-base font-extrabold tracking-tight text-white">{{ app_name() }}</span>
                        <span class="block text-[10px] font-semibold uppercase tracking-[0.16em] text-emerald-300/70">Marketplace</span>
                    </span>
                </a>

                <button type="button" id="accountSidebarClose"
                    class="ml-auto flex h-8 w-8 items-center justify-center rounded-lg text-emerald-200/70 hover:bg-white/10 hover:text-white lg:hidden"
                    aria-label="Close navigation">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- User card --}}
            <div class="border-b border-white/10 px-4 py-3">
                <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/5 p-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 text-sm font-bold text-white">
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()->first_name ?? auth()->user()->name ?? 'U', 0, 1)) }}
                    </span>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-bold text-white">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
                        <p class="truncate text-[11px] text-emerald-300/70">{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>

            @include('account.partials.sidebar')
        </aside>

        {{-- Mobile backdrop --}}
        <div id="accountSidebarBackdrop" class="fixed inset-0 z-40 hidden bg-slate-900/50 lg:hidden"></div>

        {{-- Main column --}}
        <div class="account-main flex min-w-0 flex-1 flex-col lg:ml-[264px]">
            {{-- Ambient background orbs (depth / 3D atmosphere) --}}
            <div aria-hidden="true" class="pointer-events-none fixed bottom-10 right-10 h-72 w-72 rounded-full bg-emerald-200/30 blur-3xl animate-float"></div>
            <div aria-hidden="true" class="pointer-events-none fixed -left-16 top-1/3 h-80 w-80 rounded-full bg-teal-200/25 blur-3xl animate-float" style="animation-delay: 1.5s"></div>

            @include('account.partials.header')

            <main class="relative z-10 flex-1 animate-slide-up px-4 py-5 sm:px-6 sm:py-6">
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
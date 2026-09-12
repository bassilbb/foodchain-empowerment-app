  <header class="sticky top-0 z-50">
    <div class="h-1 w-full bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>
    <div class="bg-white/85 backdrop-blur-xl border-b border-slate-100 shadow-[0_8px_30px_rgba(4,120,87,0.06)]">
        <nav class="container mx-auto px-4">
            <div class="flex justify-between items-center py-3 gap-4">
                <!-- Logo -->
                <a href="{{ route('landing') }}" class="group flex items-center gap-3 shrink-0">
                    <span class="w-14 h-14 rounded-2xl bg-white ring-1 ring-emerald-100 shadow-sm shadow-emerald-600/10 flex items-center justify-center overflow-hidden transition-transform duration-300 group-hover:-translate-y-0.5">
                        <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full p-1">
                    </span>
                    <span class="hidden sm:flex flex-col leading-tight">
                        <span class="text-2xl font-extrabold tracking-tight bg-gradient-to-r from-emerald-600 to-teal-500 bg-clip-text text-transparent">FCCEI</span>
                        <span class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Food Chain Community Empowerment</span>
                    </span>
                </a>

                <!-- Desktop Navigation -->
                <div class="hidden xl:flex items-center gap-1 bg-slate-50/90 ring-1 ring-slate-200/70 rounded-full p-1">
                    <a href="{{ route('landing') }}" class="nav-pill">Home</a>

                    <div class="relative dropdown-container">
                        <button class="nav-pill dropdown-toggle" data-dropdown="desktop-about">
                            About
                            <svg class="ml-1 w-3.5 h-3.5 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                        </button>
                        <div id="desktop-about" class="dropdown-menu absolute left-0 mt-3 w-52 bg-white shadow-xl rounded-2xl p-2 z-20 ring-1 ring-slate-100 opacity-0 invisible -translate-y-2 transition-all duration-200">
                            <a href="{{ route('team') }}" class="dropdown-item">Team</a>
                        </div>
                    </div>

                    <a href="#products" class="nav-pill">Membership</a>
                    <a href="{{ route('listing') }}" class="nav-pill">Listing</a>

                    <div class="relative dropdown-container">
                        <button class="nav-pill dropdown-toggle" data-dropdown="desktop-blog">
                            Blog
                            <svg class="ml-1 w-3.5 h-3.5 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                        </button>
                        <div id="desktop-blog" class="dropdown-menu absolute left-0 mt-3 w-52 bg-white shadow-xl rounded-2xl p-2 z-20 ring-1 ring-slate-100 opacity-0 invisible -translate-y-2 transition-all duration-200">
                            <a href="{{ route('gallery') }}" class="dropdown-item">Gallery</a>
                        </div>
                    </div>

                    <div class="relative dropdown-container">
                        <button class="nav-pill dropdown-toggle" data-dropdown="desktop-partner">
                            Partner
                            <svg class="ml-1 w-3.5 h-3.5 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                        </button>
                        <div id="desktop-partner" class="dropdown-menu absolute right-0 mt-3 w-64 bg-white shadow-xl rounded-2xl p-2 z-20 ring-1 ring-slate-100 opacity-0 invisible -translate-y-2 transition-all duration-200">
                            <a href="{{ route('sponsor') }}" class="dropdown-item">Become Our Sponsor</a>
                            <a href="{{ route('supplier') }}" class="dropdown-item">Become Supplier</a>
                            <a href="{{ route('rider') }}" class="dropdown-item">Become Our Logistics Channel</a>
                        </div>
                    </div>

                    <a href="#contact" class="nav-pill">Contact</a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden lg:flex items-center gap-2">
                    <a href="{{ route('marketplace.shop') }}" class="btn-primary !py-2.5 !px-5">
                        <i class="fas fa-store"></i>Shop
                    </a>
                    <a href="{{ route('login') }}" class="btn-ghost">Login</a>
                    <a href="{{ route('register') }}" class="btn-primary">Get Started</a>
                    <a href="{{ route('admin.login') }}" class="text-xs font-semibold text-slate-400 hover:text-emerald-600 transition-colors px-2" title="Admin Login">Admin</a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="lg:hidden text-slate-700 p-2.5 rounded-xl ring-1 ring-slate-200 bg-white hover:bg-slate-50 transition">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden lg:hidden mt-2 mb-4">
                <div class="rounded-3xl bg-white ring-1 ring-slate-100 shadow-xl p-3 space-y-1">
                    <a href="{{ route('landing') }}" class="mobile-link">Home</a>
                    <a href="{{ route('marketplace.shop') }}" class="mobile-link !bg-gradient-to-r !from-emerald-600 !to-teal-600 !text-white"><i class="fas fa-store mr-2"></i>Shop the Marketplace</a>

                    <div>
                        <button onclick="toggleMobileDropdown('mobile-about', event)" class="mobile-link w-full justify-between">
                            <span>About</span>
                            <svg id="mobile-about-icon" class="w-4 h-4 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                        </button>
                        <div id="mobile-about" class="hidden pl-4 mt-1 space-y-1 border-l-2 border-emerald-100">
                            <a href="{{ route('team') }}" class="mobile-sub">Team</a>
                        </div>
                    </div>

                    <a href="#products" class="mobile-link">Membership</a>
                    <a href="{{ route('listing') }}" class="mobile-link">Listing</a>

                    <div>
                        <button onclick="toggleMobileDropdown('mobile-blog', event)" class="mobile-link w-full justify-between">
                            <span>Blog</span>
                            <svg id="mobile-blog-icon" class="w-4 h-4 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                        </button>
                        <div id="mobile-blog" class="hidden pl-4 mt-1 space-y-1 border-l-2 border-emerald-100">
                            <a href="{{ route('gallery') }}" class="mobile-sub">Gallery</a>
                        </div>
                    </div>

                    <div>
                        <button onclick="toggleMobileDropdown('mobile-partner', event)" class="mobile-link w-full justify-between">
                            <span>Partner</span>
                            <svg id="mobile-partner-icon" class="w-4 h-4 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
                        </button>
                        <div id="mobile-partner" class="hidden pl-4 mt-1 space-y-1 border-l-2 border-emerald-100">
                            <a href="{{ route('sponsor') }}" class="mobile-sub">Become Our Sponsor</a>
                            <a href="{{ route('supplier') }}" class="mobile-sub">Become Supplier</a>
                            <a href="{{ route('rider') }}" class="mobile-sub">Become Our Logistics Channel</a>
                        </div>
                    </div>

                    <a href="#contact" class="mobile-link">Contact</a>

                    <div class="grid grid-cols-2 gap-2 pt-3 mt-2 border-t border-slate-100">
                        <a href="{{ route('login') }}" class="btn-ghost w-full justify-center">Login</a>
                        <a href="{{ route('register') }}" class="btn-primary w-full justify-center">Get Started</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <style>
        .nav-pill { display: inline-flex; align-items: center; padding: 0.55rem 0.9rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; color: #475569; transition: all 0.2s ease; white-space: nowrap; }
        .nav-pill:hover { color: #047857; background: #ffffff; box-shadow: 0 2px 10px rgba(4,120,87,0.08); }
        .dropdown-item { display: block; padding: 0.55rem 0.85rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 500; color: #475569; transition: all 0.15s ease; }
        .dropdown-item:hover { background: #ecfdf5; color: #047857; }
        .mobile-link { display: flex; align-items: center; padding: 0.8rem 0.9rem; border-radius: 0.9rem; font-size: 0.95rem; font-weight: 600; color: #334155; transition: all 0.15s ease; }
        .mobile-link:hover { background: #ecfdf5; color: #047857; }
        .mobile-sub { display: block; padding: 0.5rem 0.75rem; border-radius: 0.6rem; font-size: 0.875rem; font-weight: 500; color: #64748b; transition: all 0.15s ease; }
        .mobile-sub:hover { color: #047857; }
        .btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.15rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; color: #ffffff; background-image: linear-gradient(to right, #059669, #0d9488); box-shadow: 0 8px 20px -6px rgba(5,150,105,0.55); transition: all 0.2s ease; }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 12px 24px -6px rgba(5,150,105,0.65); }
        .btn-ghost { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1.15rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; color: #047857; background: #ffffff; border: 1.5px solid #a7f3d0; transition: all 0.2s ease; }
        .btn-ghost:hover { background: #ecfdf5; border-color: #34d399; }
    </style>

    <script>
        // Desktop Dropdown Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            let activeDropdown = null;

            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdownId = this.getAttribute('data-dropdown');
                    const dropdown = document.getElementById(dropdownId);
                    const svg = this.querySelector('svg');

                    if (activeDropdown && activeDropdown !== dropdown) {
                        activeDropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
                        activeDropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                        const prevSvg = activeDropdown.previousElementSibling.querySelector('svg');
                        if (prevSvg) prevSvg.classList.remove('rotate-180');
                    }

                    if (dropdown.classList.contains('opacity-0')) {
                        dropdown.classList.remove('opacity-0', 'invisible', '-translate-y-2');
                        dropdown.classList.add('opacity-100', 'visible', 'translate-y-0');
                        svg.classList.add('rotate-180');
                        activeDropdown = dropdown;
                    } else {
                        dropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
                        dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                        svg.classList.remove('rotate-180');
                        activeDropdown = null;
                    }
                });
            });

            document.addEventListener('click', function() {
                if (activeDropdown) {
                    activeDropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
                    activeDropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                    const toggle = activeDropdown.previousElementSibling;
                    const svg = toggle.querySelector('svg');
                    if (svg) svg.classList.remove('rotate-180');
                    activeDropdown = null;
                }
            });
        });

        // Mobile Menu Toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Mobile Dropdown Toggle
        function toggleMobileDropdown(id, event) {
            event.stopPropagation();
            const dropdown = document.getElementById(id);
            const icon = document.getElementById(id + '-icon');
            dropdown.classList.toggle('hidden');
            if (icon) icon.classList.toggle('rotate-180');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
            }
        });
    </script>

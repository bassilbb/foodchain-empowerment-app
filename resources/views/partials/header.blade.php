  <header class="sticky top-0 z-50 bg-white shadow-sm">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-15 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full">
                    </div>

                    <div class="flex flex-col justify-center">
                        <span class="text-3xl font-extrabold leading-tight" style="color: #26C1C9;">FCCEI</span>

                        <span class="text-sm font-bold uppercase tracking-widest" style="color: #1D5FB5;">
                            Food Chain Community Empowerment Initiative
                        </span>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('landing') }}" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Home</a>

                    <!-- About Dropdown -->
                    <div class="relative dropdown-container">
                        <button class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center dropdown-toggle" data-dropdown="desktop-about">
                            About
                            <svg class="ml-1 w-4 h-4 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="desktop-about" class="dropdown-menu absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2 z-20 border border-gray-100 opacity-0 invisible -translate-y-2 transition-all duration-200">
                            <a href="{{ route('team') }}" class="block px-4 py-2 text-gray-700 hover:bg-emerald-50 hover:text-primary transition-colors duration-150">Team</a>
                        </div>
                    </div>

                    <a href="#products" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Membership</a>
                    <a href="{{ route('listing') }}" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Listing</a>

                    <!-- Blog Dropdown -->
                    <div class="relative dropdown-container">
                        <button class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center dropdown-toggle" data-dropdown="desktop-blog">
                            Blog
                            <svg class="ml-1 w-4 h-4 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="desktop-blog" class="dropdown-menu absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg py-2 z-20 border border-gray-100 opacity-0 invisible -translate-y-2 transition-all duration-200">
                            <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-emerald-50 hover:text-primary transition-colors duration-150">Gallary</a>
                        </div>
                    </div>

                    <!-- Partner Dropdown -->
                    <div class="relative dropdown-container">
                        <button class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center dropdown-toggle" data-dropdown="desktop-partner">
                            Partner
                            <svg class="ml-1 w-4 h-4 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="desktop-partner" class="dropdown-menu absolute left-0 mt-2 w-56 bg-white shadow-lg rounded-lg py-2 z-20 border border-gray-100 opacity-0 invisible -translate-y-2 transition-all duration-200">
                            <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-emerald-50 hover:text-primary transition-colors duration-150">Become Our Sponsor</a>
                            <a href="{{ route('supplier') }}" class="block px-4 py-2 text-gray-700 hover:bg-emerald-50 hover:text-primary transition-colors duration-150">Become Supplier</a>
                            <a href="{{ route('rider') }}" class="block px-4 py-2 text-gray-700 hover:bg-emerald-50 hover:text-primary transition-colors duration-150">Become Our Logistics Channel</a>
                        </div>
                    </div>

                    <a href="#contact" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Contact</a>
                    <a href="{{ route('marketplace.shop') }}" class="nav-link flex items-center text-white bg-primary hover:bg-primary-dark font-semibold transition-colors duration-200 px-5 py-2 rounded-lg">
                        <i class="fas fa-store mr-2"></i>Shop
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="px-5 py-2 text-primary font-medium hover:bg-primary hover:text-white border-2 border-primary rounded-lg transition-all duration-200">Login</a>
                    <a href="{{ route('register') }}" class="px-5 py-2 bg-primary text-white font-medium hover:bg-primary-dark rounded-lg transition-all duration-200">Get Started</a>
                    <a href="{{ route('admin.login') }}" class="text-sm text-gray-400 hover:text-primary transition-colors duration-200" title="Admin Login">Admin</a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700 p-2">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4 border-t border-gray-200">
                <div class="pt-4 space-y-2">
                    <a href="{{ route('landing') }}" class="block py-3 px-2 text-gray-700 hover:bg-emerald-50 hover:text-primary font-medium rounded-lg transition-colors duration-150">Home</a>
                    <a href="{{ route('marketplace.shop') }}" class="block py-3 px-2 text-white bg-primary hover:bg-primary-dark font-semibold rounded-lg transition-colors duration-150"><i class="fas fa-store mr-2"></i>Shop the Marketplace Store</a>

                    <!-- Mobile About Dropdown -->
                    <div class="relative">
                        <button onclick="toggleMobileDropdown('mobile-about', event)" class="w-full text-left py-3 px-2 text-gray-700 hover:bg-emerald-50 hover:text-primary font-medium flex items-center justify-between rounded-lg transition-colors duration-150">
                            <span>About</span>
                            <svg id="mobile-about-icon" class="w-4 h-4 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="mobile-about" class="hidden pl-4 mt-1 space-y-1 border-l-2 border-emerald-200">
                            <a href="{{ route('team') }}" class="block py-2 px-2 text-gray-600 hover:text-primary font-medium rounded transition-colors duration-150">Team</a>
                        </div>
                    </div>

                    <a href="#products" class="block py-3 px-2 text-gray-700 hover:bg-emerald-50 hover:text-primary font-medium rounded-lg transition-colors duration-150">Membership</a>
                    <a href="{{ route('listing') }}" class="block py-3 px-2 text-gray-700 hover:bg-emerald-50 hover:text-primary font-medium rounded-lg transition-colors duration-150">Listing</a>

                    <!-- Mobile Blog Dropdown -->
                    <div class="relative">
                        <button onclick="toggleMobileDropdown('mobile-blog', event)" class="w-full text-left py-3 px-2 text-gray-700 hover:bg-emerald-50 hover:text-primary font-medium flex items-center justify-between rounded-lg transition-colors duration-150">
                            <span>Blog</span>
                            <svg id="mobile-blog-icon" class="w-4 h-4 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="mobile-blog" class="hidden pl-4 mt-1 space-y-1 border-l-2 border-emerald-200">
                            <a href="{{ route('gallery') }}" class="block py-2 px-2 text-gray-600 hover:text-primary font-medium rounded transition-colors duration-150">Gallary</a>
                        </div>
                    </div>

                    <!-- Mobile Partner Dropdown -->
                    <div class="relative">
                        <button onclick="toggleMobileDropdown('mobile-partner', event)" class="w-full text-left py-3 px-2 text-gray-700 hover:bg-emerald-50 hover:text-primary font-medium flex items-center justify-between rounded-lg transition-colors duration-150">
                            <span>Partner</span>
                            <svg id="mobile-partner-icon" class="w-4 h-4 transition-transform duration-200" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="mobile-partner" class="hidden pl-4 mt-1 space-y-1 border-l-2 border-emerald-200">
                            <a href="{{ route('sponsor') }}" class="block py-2 px-2 text-gray-600 hover:text-primary font-medium rounded transition-colors duration-150">Become Our Sponsor</a>
                            <a href="{{ route('supplier') }}" class="block py-2 px-2 text-gray-600 hover:text-primary font-medium rounded transition-colors duration-150">Become Supplier</a>
                            <a href="{{ route('rider') }}" class="block py-2 px-2 text-gray-600 hover:text-primary font-medium rounded transition-colors duration-150">Become Our Logistics Channel</a>
                        </div>
                    </div>

                    <a href="#contact" class="block py-3 px-2 text-gray-700 hover:bg-emerald-50 hover:text-primary font-medium rounded-lg transition-colors duration-150">Contact</a>
                </div>

                <div class="pt-4 mt-4 border-t border-gray-200 space-y-2">
                    <a href="{{ route('login') }}" class="block py-3 text-center text-primary font-medium border-2 border-primary rounded-lg hover:bg-primary hover:text-white transition-colors duration-200">Login</a>
                    <a href="{{ route('register') }}" class="block py-3 text-center bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-colors duration-200">Get Started</a>
                    <a href="{{ route('admin.login') }}" class="block py-2 text-center text-xs text-gray-400 hover:text-primary transition-colors duration-200">Admin Login</a>
                </div>
            </div>
        </nav>
    </header>

    <script>
        // Desktop Dropdown Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            const dropdownContainers = document.querySelectorAll('.dropdown-container');
            let activeDropdown = null;

            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const dropdownId = this.getAttribute('data-dropdown');
                    const dropdown = document.getElementById(dropdownId);
                    const svg = this.querySelector('svg');

                    // Close other dropdowns
                    if (activeDropdown && activeDropdown !== dropdown) {
                        activeDropdown.classList.add('opacity-0', 'invisible', '-translate-y-2');
                        activeDropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                        const prevToggle = activeDropdown.previousElementSibling.querySelector('svg');
                        if (prevToggle) prevToggle.classList.remove('rotate-180');
                    }

                    // Toggle current dropdown
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

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
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
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
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
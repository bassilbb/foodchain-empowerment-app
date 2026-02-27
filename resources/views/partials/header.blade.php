  <header class="sticky top-0 z-50 bg-white shadow-sm">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <!-- <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-xl">RP</span>
                    </div>
                    <span class="text-2xl font-bold text-gray-900">Referral<span class="text-primary">Pro</span></span>
                </div> -->
                {{-- {{ asset('assets/landing/images/rice.jpg') }}" FCCEILOGO.png--}}
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
                <!-- <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Home</a>
                    <a href="#about" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">About</a>
                    <a href="#products" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Membership</a>
                    <a href="#board-members" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Board Members</a>
                    <a href="#team" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Team</a>
                    <a href="#blog" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Blog</a>
                    <a href="#contact" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Contact</a>
                    <a href="gallary.html" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Gallary</a>

                </div> -->

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('landing') }}" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Home</a>

                    <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="#about" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        About
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                        </a>
                        <!-- Dropdown menu -->
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <!-- <a href="#board-members" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Board Members</a> -->
                        <a href="{{ route('team') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a>
                        </div>
                    </div>

                    <a href="#products" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Membership</a>

                    <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="#blog" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        Blog
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                        </a>
                        <!-- Dropdown menu -->
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Gallary</a>
                        <!-- <a href="#team" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a> -->
                        </div>
                    </div>

                    <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="#" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        Partner
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                        </a>

                        <!-- Dropdown menu -->
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our Sponsor</a>
                        <a href="{{ route('supplier') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become supplier</a>
                        <a href="{{ route('rider') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our Logistics Channel (Rider Only)</a>
                        </div>

                    </div>



                    <!-- <a href="#blog" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Blog</a> -->
                    <a href="#contact" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Contact</a>
                    <!-- <a href="gallary.html" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Gallary</a> -->
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                     <!-- <a href="/login" class="px-6 py-2 text-primary font-medium hover:bg-primary hover:text-white border-2 border-primary rounded-lg transition-all duration-200">Login</a> -->
                    <a href="{{ route('login') }}" class="px-6 py-2 text-primary font-medium hover:bg-primary hover:text-white border-2 border-primary rounded-lg transition-all duration-200">Login</a>
                    <!-- <a href="/register" class="px-6 py-2 bg-primary text-white font-medium hover:bg-primary-dark rounded-lg transition-all duration-200">Get Started</a> -->
                    <a href="{{ route('register') }}" class="px-6 py-2 bg-primary text-white font-medium hover:bg-primary-dark rounded-lg transition-all duration-200">Get Started</a>

                </div>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 space-y-4">
                <a href="#home" class="block py-2 text-gray-700 hover:text-primary font-medium">Home</a>
                <!-- <a href="#about" class="block py-2 text-gray-700 hover:text-primary font-medium">About</a> -->

                 <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="#about" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        About
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                        </a>
                        <!-- Dropdown menu -->
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <!-- /<a href="#board-members" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Board Members</a> -->
                        <a href="{{ route('team') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a>
                        </div>
                    </div>

                <a href="#products" class="block py-2 text-gray-700 hover:text-primary font-medium">Membership</a>

                <!-- <a href="#board-members" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Board Members</a> -->
                <!-- <a href="#team" class="block py-2 text-gray-700 hover:text-primary font-medium">Team</a> -->

                <!-- <a href="#blog" class="block py-2 text-gray-700 hover:text-primary font-medium">Blog</a> -->

                  <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="#blog" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        Blog
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                        </a>
                        <!-- Dropdown menu -->
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Gallary</a>
                        <!-- <a href="#team" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a> -->
                        </div>
                    </div>

                    <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="#" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        Partner
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                        </a>

                        <!-- Dropdown menu -->
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our Sponsor</a>
                        <a href="{{ route('supplier') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become supplier</a>
                        <a href="{{ route('rider') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our Logistics Channel (Rider Only)</a>
                        </div>

                    </div>


                <a href="#contact" class="block py-2 text-gray-700 hover:text-primary font-medium">Contact</a>
                <div class="pt-4 space-y-2">
                    <a href="{{ route('login') }}" class="block py-2 text-center text-primary font-medium border-2 border-primary rounded-lg">Login</a>
                    <a href="{{ route('register') }}" class="block py-2 text-center bg-primary text-white font-medium rounded-lg">Get Started</a>
                </div>
            </div>
        </nav>
    </header>

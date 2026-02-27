<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog Posts</title>
    <!-- Tailwind CSS CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800 font-sans">

    <!-- Navigation -->
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo Section -->
                <div class="flex items-center space-x-4">
                    <div class="w-20 h-15 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full" />
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="text-3xl font-extrabold leading-tight" style="color: #26C1C9;">FCCEI</span>
                        <span class="text-sm font-bold uppercase tracking-widest" style="color: #1D5FB5;">
                            Food Chain Community Empowerment Initiative
                        </span>
                    </div>
                </div>
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('landing') }}"
                        class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Home</a>
                    <!-- <a href="#about" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">About</a> -->
                    <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="#about"
                            class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                            About
                            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <!-- Dropdown menu -->
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                            <a href="#board-members" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Board
                                Members</a>
                            <a href="{{ route('team') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a>
                        </div>
                    </div>

                    <a href="#products"
                        class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Membership</a>
                    <!-- <a href="#team" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Team</a> -->
                    <!-- <a href="#blog" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Blog</a> -->
                    <!-- Blog dropdown -->
                    <div class="relative group">
                        <a href="#blog"
                            class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                            Blog
                            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                            <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Gallary</a>
                        </div>
                    </div>

                    <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="{{ route('sponsor') }}"
                            class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                            Partner
                            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>

                        <!-- Dropdown menu -->
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                            <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our
                                Sponsor</a>
                            <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become
                                supplier</a>
                            <a href="{{ route('rider') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our
                                Logistics Channel (Rider Only)</a>
                        </div>

                    </div>

                    <a href="#contact"
                        class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Contact</a>
                </div>
                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}"
                        class="px-6 py-2 text-primary font-medium hover:bg-primary hover:text-white border-2 border-primary rounded-lg transition-all duration-200">Login</a>
                    <a href="{{ route('register') }}"
                        class="px-6 py-2 bg-primary text-white font-medium hover:bg-primary-dark rounded-lg transition-all duration-200">Get
                        Started</a>
                </div>
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 space-y-4">
                <a href="{{ route('landing') }}" class="block py-2 text-gray-700 hover:text-primary font-medium">Home</a>
                <!-- <a href="#about" class="block py-2 text-gray-700 hover:text-primary font-medium">About</a> -->
                <!-- About with dropdown -->
                <div class="relative group">
                    <a href="#about"
                        class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        About
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    <!-- Dropdown menu -->
                    <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <!-- <a href="#board-members" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Board Members</a> -->
                        <a href="{{ route('team') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a>
                    </div>
                </div>

                <a href="#products" class="block py-2 text-gray-700 hover:text-primary font-medium">Products</a>
                <!-- <a href="#team" class="block py-2 text-gray-700 hover:text-primary font-medium">Team</a>
        <a href="#blog" class="block py-2 text-gray-700 hover:text-primary font-medium">Blog</a> -->

                <!-- Blog dropdown -->
                <div class="relative group">
                    <a href="#blog"
                        class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        Blog
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                    <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Gallary</a>
                    </div>
                </div>
                <!-- About with dropdown -->
                <div class="relative group">
                    <a href="#"
                        class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        Partners
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20"
                            xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>

                    <!-- Dropdown menu -->
                    <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our
                            Sponsor</a>
                        <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become
                            supplier</a>
                        <a href="{{ route('rider') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our
                            Logistics Channel (Rider Only)</a>
                    </div>

                </div>


                <a href="#contact" class="block py-2 text-gray-700 hover:text-primary font-medium">Contact</a>
                <div class="pt-4 space-y-2">
                    <a href="{{ route('login') }}"
                        class="block py-2 text-center text-primary font-medium border-2 border-primary rounded-lg">Login</a>
                    <a href="{{ route('register') }}"
                        class="block py-2 text-center bg-primary text-white font-medium rounded-lg hover:bg-primary-dark transition-all duration-200">Get
                        Started</a>
                </div>
            </div>
        </nav>
    </header>
     {{-- @include('partials.header') --}}

    <main class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">

            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">Latest <span
                        class="text-[#26C1C9]">Blog</span> Posts</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Insights, tips, and success stories from our community</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-12">

                <div class="lg:w-3/4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        <article
                            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                            <img src="{{ asset('assets/landing/images/blogsa.jpeg') }}" alt="Maximizing Your Referral Earnings"
                                class="w-full h-48 object-cover" loading="lazy" />
                            <div class="p-6">
                                <div class="text-sm text-[#26C1C9] font-bold mb-2 uppercase tracking-widest mb-2">
                                    COMMUNITY BUILDING</div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3">Build a Strong Food-Secure Community
                                    in 2024</h3>
                                <p class="text-gray-600 mb-4">Discover practical ways to grow a supportive community
                                    where members collaborate, empower one another, and ensure continuous access to
                                    essential food resources.</p>
                                <a href="{{ route('blog_details') }}" class="text-[#1D5FB5] font-bold hover:underline">Read More
                                    →</a>
                            </div>
                        </article>

                        <article
                            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                            <img src="{{ asset('assets/landing/images/blogpost.jpeg') }}" alt="Success Story" class="w-full h-48 object-cover"
                                loading="lazy" />
                            <div class="p-6">
                                <div class="text-sm text-[#26C1C9] font-bold mb-2 uppercase tracking-widest mb-2">
                                    SUCCESS STORIES</div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3">From Struggle to Stability: A
                                    Community Transformation Story</h3>
                                <p class="text-gray-600 mb-4">
                                    Discover how active participation and shared support within our community helped
                                    transform lives, improve livelihoods, and create sustainable food access.
                                </p>
                                <a href="{{ route('blog_details') }}" class="text-[#1D5FB5] font-bold hover:underline">Read More
                                    →</a>
                            </div>
                        </article>

                        <article
                            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                            <img src="{{ asset('assets/landing/images/about-uss.png') }}" alt="Education" class="w-full h-48 object-cover"
                                loading="lazy" />
                            <div class="p-6">
                                <div class="text-sm text-[#26C1C9] font-bold mb-2 uppercase tracking-widest">EDUCATION
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-3">Understanding Community Empowerment &
                                    Sustainable Growth</h3>
                                <p class="text-gray-600 mb-4">
                                    Learn how education, collaboration, and shared resources help individuals and
                                    communities build lasting impact and food security.
                                </p>
                                <a href="{{ route('blog_details') }}" class="text-[#1D5FB5] font-bold hover:underline">Read More
                                    →</a>
                            </div>
                        </article>

                        <article
                            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                            <img src="{{ asset('assets/landing/images/blogf.jpeg') }}" alt="Education" class="w-full h-48 object-cover"
                                loading="lazy" />
                            <div class="p-6">
                                <div class="text-sm text-[#26C1C9] font-bold mb-2 uppercase tracking-widest">Community
                                    Learning</div>
                                <h2 class="text-xl font-bold text-gray-900 mb-3 hover:text-[#1D5FB5] transition">
                                    Building Strong & Sustainable Communities
                                </h2>
                                <p class="text-gray-600 mb-4 text-sm leading-relaxed">
                                    Discover how shared knowledge, collaboration, and inclusive participation empower
                                    individuals to grow, thrive, and create lasting impact.
                                </p>

                                <a href="{{ route('blog_details') }}" class="text-[#1D5FB5] font-bold hover:underline">Read More
                                    →</a>
                            </div>
                        </article>
                    </div>

                    <div class="flex justify-center mb-12">
                        <a href="#"
                            class="px-8 py-3 border-2 border-[#26C1C9] text-[#26C1C9] font-bold rounded-lg hover:bg-[#26C1C9] hover:text-white transition-all">
                            Load More Posts
                        </a>
                    </div>
                </div>

                <aside class="lg:w-1/4 space-y-8">

                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-bolt text-[#26C1C9] mr-2"></i> Recent Updates
                        </h3>
                        <div class="space-y-6">
                            <a href="#" class="group flex items-center gap-3">
                                <img src="{{ asset('assets/landing/images/about-uss.png') }}" class="w-12 h-12 rounded-lg object-cover">
                                <div>
                                    <h4
                                        class="text-xs font-bold text-gray-800 group-hover:text-[#1D5FB5] transition leading-tight">
                                        Emerald Stage Rewards 2024</h4>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase">2 days ago</p>
                                </div>
                            </a>
                            <a href="#" class="group flex items-center gap-3">
                                <img src="{{ asset('assets/landing/images/about-uss.png') }}" class="w-12 h-12 rounded-lg object-cover">
                                <div>
                                    <h4
                                        class="text-xs font-bold text-gray-800 group-hover:text-[#1D5FB5] transition leading-tight">
                                        New Food Pack Logistics</h4>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase">5 days ago</p>
                                </div>
                            </a>
                            <a href="#" class="group flex items-center gap-3">
                                <img src="{{ asset('assets/landing/images/about-uss.png') }}" class="w-12 h-12 rounded-lg object-cover">
                                <div>
                                    <h4
                                        class="text-xs font-bold text-gray-800 group-hover:text-[#1D5FB5] transition leading-tight">
                                        New Food Pack Logistics</h4>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase">5 days ago</p>
                                </div>
                            </a>
                            <a href="#" class="group flex items-center gap-3">
                                <img src="{{ asset('assets/landing/images/about-uss.png') }}" class="w-12 h-12 rounded-lg object-cover">
                                <div>
                                    <h4
                                        class="text-xs font-bold text-gray-800 group-hover:text-[#1D5FB5] transition leading-tight">
                                        New Food Pack Logistics</h4>
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase">5 days ago</p>
                                </div>
                            </a>
                        </div>
                    </div>

                    <div class="bg-[#26C1C9] p-6 rounded-2xl shadow-lg border-b-4 border-[#1D5FB5]">
                        <h3 class="text-white font-black text-sm uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i> Quick Facts
                        </h3>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-4 h-4 bg-[#1D5FB5] rounded flex-shrink-0 flex items-center justify-center mt-1">
                                    <i class="fas fa-check text-[8px] text-white"></i>
                                </div>
                                <span class="text-white text-xs leading-tight">Active in <strong
                                        class="text-yellow-200">36 States</strong> of Nigeria.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-4 h-4 bg-[#1D5FB5] rounded flex-shrink-0 flex items-center justify-center mt-1">
                                    <i class="fas fa-check text-[8px] text-white"></i>
                                </div>
                                <span class="text-white text-xs leading-tight">Over <strong
                                        class="text-yellow-200">50K+</strong> households empowered.</span>
                            </li>
                        </ul>
                        <button
                            class="w-full mt-6 py-3 bg-[#1D5FB5] text-white text-xs font-bold rounded-lg hover:bg-opacity-90 transition shadow-md">
                            JOIN THE NETWORK
                        </button>
                    </div>
                </aside>

            </div>

            <div class="mt-12 text-center border-t pt-8">
                <a href="{{ route('landing') }}" class="text-gray-500 hover:text-[#26C1C9] font-medium transition">
                    <i class="fas fa-arrow-left mr-2 text-xs"></i> Back to Home
                </a>
            </div>

        </div>
    </main>

    <!-- Bottom Newsletter -->
    <section class="py-8" style="background: linear-gradient(to right, #4F46E5, #10B981);">
        <div class="max-w-7xl mx-auto px-4 text-center text-white">
            <h2 class="text-3xl font-bold mb-4 font-display">Stay Updated</h2>
            <p class="mb-8 max-w-2xl mx-auto">Subscribe to our newsletter for the latest tips, success stories, and
                platform updates</p>
            <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
                <input type="email" placeholder="Your email address"
                    class="flex-1 px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white" />
                <button type="submit"
                    class="px-8 py-3 bg-white text-primary font-bold rounded-lg hover:bg-gray-100 transition-all duration-200">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div>
                    <div class="flex items-center space-x-4">
                        <div class="w-32 h-32 flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('assets/landing/images/FCCEILOGOr.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full" />
                        </div>
                        <div class="flex flex-col">
                            <span class="text-3xl font-extrabold leading-none" style="color: #26C1C9;">FCCEI</span>
                            <span class="text-[11px] font-bold uppercase tracking-widest" style="color: #1D5FB5;">Food
                                Chain Community Empowerment Initiative</span>
                        </div>
                    </div>
                    <p class="text-gray-400 mb-6 -mt-2">
                        Empowering communities to achieve food security and sustainable growth through collaborative
                        local initiatives.
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://web.facebook.com/profile.php?id=61585986685899"
                            class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group"
                            style="background: rgba(255,255,255,0.05);">
                            <i class="fab fa-facebook-f text-brandTeal group-hover:text-white"></i>
                        </a>
                        <!-- <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
            <i class="fab fa-twitter text-brandTeal group-hover:text-white"></i>
          </a> -->

                        <a href="https://www.tiktok.com/@foodchainempowerm?lang=en"
                            class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group"
                            style="background: rgba(255,255,255,0.05);">
                            <i class="fab fa-tiktok text-brandTeal group-hover:text-white"></i>
                        </a>


                        <a href="https://www.instagram.com/foodchainempowerment/"
                            class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group"
                            style="background: rgba(255,255,255,0.05);">
                            <i class="fab fa-instagram text-brandTeal group-hover:text-white"></i>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group"
                            style="background: rgba(255,255,255,0.05);">
                            <i class="fab fa-whatsapp text-brandTeal group-hover:text-white"></i>
                        </a>
                    </div>
                </div>
                <!-- Quick Links -->
                <div>
                    <h4 class="text-lg font-bold mb-6">Quick Links</h4>
                    <ul class="space-y-3">
                        <li><a href="#home"
                                class="text-gray-400 hover:text-white transition-colors duration-200">Home</a></li>
                        <li><a href="#about"
                                class="text-gray-400 hover:text-white transition-colors duration-200">About Us</a></li>
                        <li><a href="#products"
                                class="text-gray-400 hover:text-white transition-colors duration-200">Products</a></li>
                        <li><a href="#blog"
                                class="text-gray-400 hover:text-white transition-colors duration-200">Blog</a></li>
                    </ul>
                </div>
                <!-- Legal -->
                <div>
                    <h4 class="text-lg font-bold mb-6">Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="/privacy"
                                class="text-gray-400 hover:text-white transition-colors duration-200">Privacy
                                Policy</a></li>
                        <li><a href="/terms"
                                class="text-gray-400 hover:text-white transition-colors duration-200">Terms of
                                Service</a></li>
                        <li><a href="/cookies"
                                class="text-gray-400 hover:text-white transition-colors duration-200">Cookie Policy</a>
                        </li>
                        <li><a href="/compliance"
                                class="text-gray-400 hover:text-white transition-colors duration-200">Compliance</a>
                        </li>
                    </ul>
                </div>
                <!-- Resources -->
                <div>
                    <h4 class="text-lg font-bold mb-6">Resources</h4>
                    <ul class="space-y-3">
                        <li><a href="/help"
                                class="text-gray-400 hover:text-white transition-colors duration-200">Help Center</a>
                        </li>
                        <li><a href="/faq"
                                class="text-gray-400 hover:text-white transition-colors duration-200">FAQ</a></li>
                        <li><a href="/community"
                                class="text-gray-400 hover:text-white transition-colors duration-200">Community</a>
                        </li>
                        <li><a href="/affiliate"
                                class="text-gray-400 hover:text-white transition-colors duration-200">Affiliate
                                Program</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2024 FCCEI. All rights reserved. | Designed By Bassiprog Digital Solution</p>
            </div>
        </div>
    </footer>

    <!-- Script for mobile menu toggle -->
    <script>
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        if (menuButton) {
            menuButton.onclick = () => {
                mobileMenu.classList.toggle('hidden');
            };
        }
    </script>


    <a href="https://wa.me/+2348168018123" target="_blank" class="fixed bottom-8 right-8 z-[60] group">
        <span
            class="absolute right-full mr-4 top-1/2 -translate-y-1/2 bg-gray-900 text-white text-xs font-bold px-4 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow-xl">
            Chat with an Event Manager
        </span>
        <div class="bg-[#25D366] p-4 rounded-full shadow-2xl hover:scale-110 transition duration-300">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
            </svg>
        </div>
    </a>
</body>

</html>

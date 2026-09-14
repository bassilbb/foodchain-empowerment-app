<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blog Post</title>
    <!-- Tailwind CSS CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800 font-sans">

    <!-- Navigation Header -->
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
                    <a href="#home"
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
                            <!-- <a href="#board-members" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Board Members</a> -->
                            <a href="#team" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a>
                        </div>
                    </div>

                    <a href="#products"
                        class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Products</a>
                    <!-- <a href="#team" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Team</a> -->
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

                    <!-- <a href="#blog" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Blog</a> -->
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
                        class="px-6 py-2 text-primary font-medium hover:bg-primary hover:text-white border-2 border-primary rounded-lg transition-all duration-200">Get
                        Started</a>

                    <!-- <a href="register.html" class="px-6 py-2 bg-primary text-white font-medium hover:bg-primary  hover:text-white rounded-lg transition-all duration-200">Get Started</a> -->
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
                        <a href="#team" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a>
                    </div>
                </div>

                <a href="#products" class="block py-2 text-gray-700 hover:text-primary font-medium">Products</a>
                <!-- <a href="#team" class="block py-2 text-gray-700 hover:text-primary font-medium">Team</a> -->
                <!-- <a href="#blog" class="block py-2 text-gray-700 hover:text-primary font-medium">Blog</a> -->

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
                        <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our
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

    <!-- Top Newsletter -->
    <!-- <section class="py-8" style="background: linear-gradient(to right, #4F46E5, #10B981);">
    <div class="max-w-7xl mx-auto px-4 text-center text-white">
      <h2 class="text-3xl font-bold mb-4 font-display">Stay Updated</h2>
      <p class="mb-8 max-w-2xl mx-auto">Subscribe to our newsletter for the latest tips, success stories, and platform updates</p>
      <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
        <input type="email" placeholder="Your email address" class="flex-1 px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white" />
        <button type="submit" class="px-8 py-3 bg-white text-primary font-bold rounded-lg hover:bg-gray-100 transition-all duration-200">Subscribe</button>
      </form>
    </div>
  </section> -->

    <!-- Main Blog Content -->
    <!-- <section class="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg my-8"> -->
    <!-- Back Button -->
    <!-- <a href="blog-list.html" class="text-blue-600 hover:underline mb-4 inline-block">&larr; Back to Blog List</a> -->

    <!-- Blog Title -->
    <!-- <h1 class="text-4xl font-bold mb-4 text-gray-800">Full Blog Title Goes Here</h1> -->

    <!-- Blog Metadata -->
    <!-- <div class="flex items-center space-x-4 mb-6">
      <img src="images/user.jpg" alt="Author" class="w-12 h-12 rounded-full object-cover" />
      <div>
        <p class="text-gray-600">By <strong>Author Name</strong></p>
        <p class="text-gray-400 text-sm">Published on <time datetime="2024-04-27">April 27, 2024</time></p>
      </div>
    </div> -->

    <!-- Blog Image -->
    <!-- <img src="images/about-uss.png" alt="Blog Image" class="w-full h-96 object-cover rounded-lg mb-6" /> -->

    <!-- Blog Content -->
    <!-- <div class="prose max-w-none">
      <p>This is the full content of the blog post. Here, you can add paragraphs, images, and other media to showcase your article in detail.</p>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
      Add more content as needed -->
    <!-- </div>
  </section> -->

    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="flex flex-col lg:flex-row gap-12">

                <div class="lg:w-2/3">
                    <article class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-gray-100">
                        <img src="{{ asset('assets/landing/images/blogpost.jpeg') }}" alt="Main Strategy"
                            class="w-full h-[450px] object-cover rounded-2xl mb-8 shadow-md">


                        <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
                            <div class="flex items-center gap-4">
                                <img src="{{ asset('assets/landing/images/uerss.jpg') }}" alt="Author"
                                    class="w-12 h-12 rounded-full border-2 border-[#26C1C9] object-cover">
                                <div>
                                    <p class="text-sm text-gray-400 uppercase tracking-tighter font-bold">Published By
                                    </p>
                                    <p class="text-lg font-bold text-gray-900 leading-none">David Johnson</p>
                                </div>
                            </div>
                            <div class="hidden sm:block text-right">
                                <p class="text-sm text-gray-400 font-medium">Jan 10, 2026</p>
                                <div class="flex gap-2 mt-1">
                                    <a href="#" class="text-gray-400 hover:text-[#1D5FB5]"><i
                                            class="fab fa-facebook-f text-xs"></i></a>
                                    <a href="#" class="text-gray-400 hover:text-[#26C1C9]"><i
                                            class="fab fa-twitter text-xs"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                            <div class="flex items-center gap-3 mb-6">
                                <span
                                    class="px-3 py-1 bg-[#26C1C9]/10 text-[#26C1C9] text-xs font-bold rounded-full uppercase tracking-widest">Growth
                                    Strategy</span>
                                <span class="text-gray-400 text-sm">January 15, 2026</span>
                            </div>

                            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-6 leading-tight">
                                Build a Strong Food-Secured Community in 2026
                            </h1>

                            <p class="text-lg text-gray-600 mb-6 font-medium">
                                <!-- At FCCEI, success is built on community. By sharing the gift of food security, you aren't just earning commissions; you are stabilizing households across the nation. -->
                                At FCCEI, success is built on community. By strengthening food security, you are not
                                just supporting individuals you are helping stabilize households and uplift communities
                                across the nation.
                            </p>

                            <h2 class="text-2xl font-bold text-[#1D5FB5] mt-10 mb-4">The Power of Building a Community
                            </h2>
                            <p class="mb-6">
                                When you bring a new member into the Bronze stage, you trigger a cycle of empowerment.
                                Focus on sharing the physical reality of the food packs nothing speaks louder than a bag
                                of high quality rice or a carton of oil delivered to a doorstep.
                            </p>

                            <div class="my-10 p-8 bg-gray-900 rounded-2xl text-white relative overflow-hidden">
                                <div class="relative z-10">
                                    <h3 class="text-xl font-bold mb-2 text-[#26C1C9]">Pro Tip for 2026</h3>
                                    <p class="opacity-90 italic">"Use your WhatsApp Status to post unboxing videos of
                                        your monthly food packs. Social proof is your strongest recruitment tool."</p>
                                </div>
                                <i class="fas fa-quote-right absolute right-4 bottom-4 text-white/10 text-6xl"></i>
                            </div>

                            <h2 class="text-2xl font-bold text-[#1D5FB5] mt-10 mb-4">Scaling to the Emerald Stage</h2>
                            <p>
                                <!-- Moving through the tiers requires leadership. Once you have your first 10 referrals, transition from a "recruiter" to a "mentor." Help your downline achieve what you have, and the system will reward you with secondary and tertiary commissions. -->
                                Growth at FCCEI is driven by leadership. As your community expands, you evolve from a
                                participant to a mentor guiding others, sharing knowledge, and helping them thrive.
                                Together, we build a strong, self-sustaining network of empowered individuals
                            </p>
                        </div>
                    </article>
                </div>

                <aside class="lg:w-1/3 space-y-8">
                    <div class="sticky top-24 space-y-8">

                        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-history text-[#26C1C9] mr-2 text-sm"></i> Recent Updates
                            </h3>
                            <div class="space-y-5">
                                <a href="{{ route('blog_details') }}" class="group flex items-center gap-4">
                                    <div
                                        class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 border border-gray-100">
                                        <img src="{{ asset('assets/landing/images/blogpost.jpeg') }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                    </div>
                                    <div>
                                        <h4
                                            class="text-sm font-bold text-gray-800 group-hover:text-[#1D5FB5] transition line-clamp-2">
                                            Success Story: Transitioning to Emerald</h4>
                                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-wider">
                                            Jan 10, 2025</p>
                                    </div>
                                </a>
                                <a href="{{ route('blog_details') }}" class="group flex items-center gap-4 border-t pt-5">
                                    <div
                                        class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 border border-gray-100">
                                        <img src="{{ asset('assets/landing/images/blogsa.jpeg') }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                    </div>
                                    <div>
                                        <h4
                                            class="text-sm font-bold text-gray-800 group-hover:text-[#1D5FB5] transition line-clamp-2">
                                            Direct Sourcing: How We Keep Prices Low</h4>
                                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-wider">
                                            Jan 08, 2025</p>
                                    </div>
                                </a>
                                <a href="{{ route('blog_details') }}" class="group flex items-center gap-4 border-t pt-5">
                                    <div
                                        class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 border border-gray-100">
                                        <img src="{{ asset('assets/landing/images/about-uss.png') }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                    </div>
                                    <div>
                                        <h4
                                            class="text-sm font-bold text-gray-800 group-hover:text-[#1D5FB5] transition line-clamp-2">
                                            Building Strong & Sustainable Communities</h4>
                                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-wider">
                                            Jan 08, 2026</p>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div
                            class="bg-[#26C1C9] p-8 rounded-3xl shadow-xl border-b-8 border-[#1D5FB5] relative overflow-hidden">
                            <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full"></div>

                            <h3
                                class="text-lg font-extrabold text-white mb-6 flex items-center border-b border-white/20 pb-4">
                                <i class="fas fa-lightbulb text-yellow-300 mr-2"></i> FCCEI AT A GLANCE
                            </h3>

                            <ul class="space-y-5">
                                <li class="flex items-start gap-4">
                                    <div
                                        class="mt-1 w-5 h-5 bg-[#1D5FB5] rounded-md flex items-center justify-center shadow-inner">
                                        <i class="fas fa-check text-[8px] text-white"></i>
                                    </div>
                                    <span class="text-white text-sm font-medium">We empower over <strong
                                            class="text-yellow-200">50,000 households</strong> across 36 states.</span>
                                </li>
                                <li class="flex items-start gap-4">
                                    <div
                                        class="mt-1 w-5 h-5 bg-[#1D5FB5] rounded-md flex items-center justify-center shadow-inner">
                                        <i class="fas fa-check text-[8px] text-white"></i>
                                    </div>
                                    <span class="text-white text-sm font-medium">Members save <strong
                                            class="text-yellow-200">35% on average</strong> on monthly food
                                        costs.</span>
                                </li>
                                <li class="flex items-start gap-4">
                                    <div
                                        class="mt-1 w-5 h-5 bg-[#1D5FB5] rounded-md flex items-center justify-center shadow-inner">
                                        <i class="fas fa-check text-[8px] text-white"></i>
                                    </div>
                                    <span class="text-white text-sm font-medium">Our logistics partners deliver to
                                        <strong class="text-yellow-200">774 LGAs</strong> in Nigeria.</span>
                                </li>
                            </ul>

                            <button
                                class="w-full mt-8 py-4 bg-white text-[#1D5FB5] font-black rounded-xl hover:bg-gray-100 transition-all uppercase text-xs tracking-widest shadow-lg">
                                Get Started Now
                            </button>
                        </div>

                    </div>
                </aside>
            </div>
        </div>
    </section>

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
    @include('partials.site-footer')

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

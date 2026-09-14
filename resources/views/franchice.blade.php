<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FCCEI Franchise</title>
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <!-- Tailwind CSS CDN for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                        <span class="text-3xl font-extrabold leading-tight" style="color: #059669;">FCCEI</span>
                        <span class="text-sm font-bold uppercase tracking-widest" style="color: #047857;">
                            Food Chain Community Empowerment Initiative
                        </span>
                    </div>
                </div>
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home"
                        class="text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">Home</a>
                    <a href="#about"
                        class="text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">About</a>
                    <a href="#services"
                        class="text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">Services</a>
                    <a href="#contact"
                        class="text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">Contact</a>
                </div>
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 space-y-4">
                <a href="#home" class="block py-2 text-gray-700 hover:text-emerald-600 font-medium">Home</a>
                <a href="#about" class="block py-2 text-gray-700 hover:text-emerald-600 font-medium">About</a>
                <a href="#services" class="block py-2 text-gray-700 hover:text-emerald-600 font-medium">Services</a>
                <a href="#contact" class="block py-2 text-gray-700 hover:text-emerald-600 font-medium">Contact</a>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="py-12 bg-gray-50 text-center">
        <div class="max-w-4xl mx-auto px-4">
            <h1 class="text-4xl font-bold mb-4 text-gray-900">Join the FCCEI Franchise Network</h1>
            <p class="text-gray-600 mb-6">Empowering communities through sustainable food initiatives. Be part of our
                growing impact.</p>
            <a href="#contact"
                class="px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition">Become
                a Franchise Partner</a>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4 text-gray-900">About FCCEI</h2>
            <p class="text-gray-600 mb-8">
                The Food Chain Community Empowerment Initiative (FCCEI) is dedicated to building resilient communities
                by promoting food security, education, and sustainable growth. Our franchise model enables partners to
                expand our mission across regions, creating lasting impact.
            </p>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-emerald-600 text-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-bold mb-2">Community Focused</h3>
                    <p>We prioritize local empowerment, fostering collaboration at the grassroots level.</p>
                </div>
                <div class="bg-emerald-600 text-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-bold mb-2">Sustainable Growth</h3>
                    <p>Our strategies ensure long-term food security and economic development.</p>
                </div>
                <div class="bg-emerald-600 text-white p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-bold mb-2">Partner Opportunities</h3>
                    <p>Join our franchise network and help expand our impact beyond borders.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Services / Franchise Benefits -->
    <section id="services" class="py-16 bg-gray-50">
        <div class="max-w-5xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-8 text-gray-900">Why Franchise with FCCEI?</h2>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 hover:shadow-xl transition">
                    <i class="fas fa-handshake fa-2x mb-4 text-emerald-600"></i>
                    <h3 class="text-xl font-semibold mb-2">Community Impact</h3>
                    <p>Drive positive change and improve livelihoods in your region.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 hover:shadow-xl transition">
                    <i class="fas fa-chart-line fa-2x mb-4 text-emerald-600"></i>
                    <h3 class="text-xl font-semibold mb-2">Business Growth</h3>
                    <p>Expand your enterprise while aligning with social empowerment goals.</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200 hover:shadow-xl transition">
                    <i class="fas fa-users fa-2x mb-4 text-emerald-600"></i>
                    <h3 class="text-xl font-semibold mb-2">Support & Training</h3>
                    <p>Access comprehensive training, resources, and ongoing support.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact / Call to Action -->
    <section id="contact" class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4 text-gray-900">Get Started with FCCEI</h2>
            <p class="text-gray-600 mb-8">Interested in becoming a franchise partner? Reach out to us today and join our
                mission to empower communities.</p>
            <a href="mailto:franchise@fcce.org"
                class="px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition">Contact
                Us</a>
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
</body>

</html>
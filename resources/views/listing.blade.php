<!DOCTYPE html>
<html lang="en">
<head>
  <base target="_self" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FCCEI Platform</title>
  <meta name="description" content="Join Foodchain Community Empowerment Initiative - Food Items Listing. Empower communities with food security programs." />
  <meta name="keywords" content="food security, empowerment, food items, community, initiative" />
  <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { DEFAULT: "#059669", light: "#10B981", dark: "#047857" },
            secondary: { DEFAULT: "#0D9488", light: "#14B8A6", dark: "#0F766E" },
            brandTeal: "#10B981"
          },
          fontFamily: {
            'sans': ['Inter', 'system-ui', 'sans-serif'],
            'display': ['Poppins', 'system-ui', 'sans-serif']
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen bg-gray-50 font-sans">

<!-- Header -->
<header class="sticky top-0 z-50 bg-white shadow-sm">
  <nav class="container mx-auto px-4 py-4">
    <div class="flex justify-between items-center">
      <!-- Logo & Title -->
      <div class="flex items-center space-x-2">
        <div class="w-20 h-15 flex items-center justify-center overflow-hidden">
          <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full" />
        </div>
        <div class="flex flex-col justify-center">
          <span class="text-3xl font-extrabold leading-tight" style="color: #10B981;">FCCEI</span>
          <span class="text-sm font-bold uppercase tracking-widest" style="color: #047857;">Food Chain Community Empowerment Initiative</span>
        </div>
      </div>
      <!-- Desktop Navigation -->
      <div class="hidden md:flex items-center space-x-8">
        <a href="{{ route('landing') }}" class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200">Home</a>
        <!-- About dropdown -->
        <div class="relative group">
          <a href="{{ route('landing') }}#about" class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200 flex items-center">
            About
            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </a>
          <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
            <a href="{{ route('landing') }}#board-members" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Board Members</a>
            <a href="{{ route('team') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a>
          </div>
        </div>
        <a href="{{ route('landing') }}#products" class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200">Membership</a>
        <!-- Blog dropdown -->
        <div class="relative group">
          <a href="{{ route('landing') }}#blog" class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200 flex items-center">
            Blog
            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </a>
          <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
            <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Gallery</a>
          </div>
        </div>

        <!-- Partner with dropdown -->
        <div class="relative group">
            <a href="#" class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200 flex items-center">
            Partner
            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
            </a>
            <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
              <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our Sponsor</a>
              <a href="{{ route('supplier') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become supplier</a>
              <a href="{{ route('rider') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our Logistics Channel (Rider Only)</a>
            </div>
        </div>

        <a href="{{ route('landing') }}#contact" class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200">Contact</a>
        <!-- Auth buttons -->
        <div class="flex items-center space-x-4">
          <a href="{{ route('login') }}" class="px-6 py-2 text-[#059669] font-medium hover:bg-[#059669] hover:text-white border-2 border-[#059669] rounded-lg transition-all duration-200">Login</a>
          <a href="{{ route('register') }}" class="px-6 py-2 bg-[#059669] text-white font-medium hover:bg-[#047857] rounded-lg transition-all duration-200">Get Started</a>
        </div>
      </div>
      <!-- Mobile menu button -->
      <button id="mobile-menu-button" class="md:hidden text-gray-700">
        <i class="fas fa-bars text-2xl"></i>
      </button>
    </div>
    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden mt-4 space-y-4">
      <a href="{{ route('landing') }}" class="block py-2 text-gray-700 hover:text-[#059669] font-medium">Home</a>
    </div>
  </nav>
</header>

<!-- Banner with Food Products Image -->
<div class="my-8 mx-auto max-w-7xl px-4">
  <div class="relative rounded-lg overflow-hidden shadow-lg">
   <img src="{{ asset('assets/landing/images/fooitmes.png') }}" alt="Food Products Banner" class="w-full h-64 object-cover transition-transform duration-300 hover:scale-105" />
    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
      <h3 class="text-3xl md:text-4xl font-bold text-white text-center px-4" style="background: rgba(0,0,0,0.4); padding: 10px; border-radius: 8px;">
        Quality Food Products for Every Plan
      </h3>
    </div>
  </div>
</div>

<!-- Main Content -->
<main class="max-w-7xl mx-auto px-4 py-10">
    <h2 class="text-4xl font-bold text-center mb-10" style="color:#10B981;">
        Food Items Listing
    </h2>

    <!-- Food Assistance Plans -->
    <section class="mb-12">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold" style="color:#047857;">Food Assistance Plans</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <!-- Bronze Plan -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden">
                <div class="p-4 text-white" style="background:#CD7F32;">
                    <h4 class="text-xl font-bold">Bronze Plan</h4>
                    <p class="text-sm opacity-90">N100,000 Minimum Limit</p>
                </div>
                <div class="p-5">
                    <ul class="space-y-2 text-gray-700 text-sm list-disc list-inside">
                        <li>✓ Rice – 25kg</li>
                        <li>✓ Groundnut – Oil</li>
                        <li>✓ Red – Oil</li>
                        <li>✓ Seasoning Cubes – Maggi</li>
                        <li>✓ Cray – Fish</li>
                        <li>✓ Beverages – Tea</li>
                        <li>✓ Milk </li>
                        <li>✓ Sugar </li>
                        <li>✓ Semovita </li>
                        <li>✓ Spaghetti </li>
                        <li>✓ Noodles </li>
                        <li>✓ Stock – Fish </li>
                        <li>✓ Tomato </li>
                        <li>✓ Salt </li>
                        <li>✓ Curry </li>
                        <li>✓ Thyme </li>
                        <li>✓ Beans </li>
                        <li>✓ Garri</li>
                    </ul>
                </div>
            </div>

            <!-- Silver Plan -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden">
                <div class="p-4 text-white bg-gray-500">
                    <h4 class="text-xl font-bold">Silver Plan</h4>
                    <p class="text-sm opacity-90">N150,000 Limit</p>
                </div>
                <div class="p-5">
                    <ul class="space-y-2 text-gray-700 text-sm list-disc list-inside">
                        <li>✓ Rice – 25kg</li>
                        <li>✓ Groundnut – Oil</li>
                        <li>✓ Red – Oil</li>
                        <li>✓ Seasoning Cubes – Maggi</li>
                        <li>✓ Cray – Fish</li>
                        <li>✓ Beverages – Tea</li>
                        <li>✓ Milk </li>
                        <li>✓ Sugar </li>
                        <li>✓ Semovita </li>
                        <li>✓ Cartoons of – Spaghetti </li>
                        <li>✓ Cartoons of – Noodles </li>
                        <li>✓ Stock – Fish </li>
                        <li>✓ Tomato </li>
                        <li>✓ Salt </li>
                        <li>✓ Curry </li>
                        <li>✓ Thyme </li>
                        <li>✓ Mayonnaise </li>
                        <li>✓ Beans </li>
                        <li>✓ Garri</li>
                        <li>✓ Egg</li>
                    </ul>
                </div>
            </div>

            <!-- Gold Plan -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden border-2 border-yellow-400">
                <div class="p-4 text-white bg-yellow-500">
                    <h4 class="text-xl font-bold">Gold Plan</h4>
                    <p class="text-sm opacity-90">N200,000 Limit</p>
                </div>
                <div class="p-5">
                    <ul class="space-y-2 text-gray-700 text-sm list-disc list-inside">
                        <li>✓ Rice – 50kg</li>
                        <li>✓ Groundnut – Oil</li>
                        <li>✓ Red – Oil</li>
                        <li>✓ Seasoning Cubes – Maggi</li>
                        <li>✓ Cray – Fish</li>
                        <li>✓ Beverages Refill Pack</li>
                        <li>✓ Milk Refill Pack</li>
                        <li>✓ Sugar </li>
                        <li>✓ 2 Semovita </li>
                        <li>✓ 2 Cartoons of – Spaghetti </li>
                        <li>✓ 2 Cartoons of – Noodles </li>
                        <li>✓ Stock – Fish </li>
                        <li>✓ Tomato </li>
                        <li>✓ Salt </li>
                        <li>✓ Curry </li>
                        <li>✓ Thyme </li>
                        <li>✓ Mayonnaise </li>
                        <li>✓ Beans </li>
                        <li>✓ Garri</li>
                        <li>✓ Egg</li>
                        <li>✓ Cereals</li>
                        <li>✓ Custard</li>
                        <li>✓ Other Spices</li>
                        <li>✓ Chicken or Turkey – 2kg</li>
                    </ul>
                </div>
            </div>

            <!-- Emerald Plan -->
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden">
                <div class="p-4 text-white bg-emerald-600">
                    <h4 class="text-xl font-bold">Emerald Plan</h4>
                    <p class="text-sm opacity-90">N250,000 Max Limit</p>
                </div>
                <div class="p-5">
                    <ul class="space-y-2 text-gray-700 text-sm list-disc list-inside">
                        <li>✓ Rice – 50kg</li>
                        <li>✓ Groundnut – Oil</li>
                        <li>✓ Red – Oil</li>
                        <li>✓ Seasoning Cubes – Maggi</li>
                        <li>✓ Cray – Fish</li>
                        <li>✓ Beverages Refill Pack</li>
                        <li>✓ Milk Refill Pack</li>
                        <li>✓ Sugar </li>
                        <li>✓ Semovita </li>
                        <li>✓ Cartoons of – Spaghetti </li>
                        <li>✓ Cartoons of – Noodles </li>
                        <li>✓ Stock – Fish </li>
                        <li>✓ Tomato </li>
                        <li>✓ Salt </li>
                        <li>✓ Curry </li>
                        <li>✓ Thyme </li>
                        <li>✓ Mayonnaise </li>
                        <li>✓ Beans </li>
                        <li>✓ Garri</li>
                        <li>✓ Egg</li>
                        <li>✓ Cereals</li>
                        <li>✓ Custard</li>
                        <li>✓ Other Spices</li>
                        <li>✓ Chicken or Turkey – 3kg</li>
                    </ul>
                </div>
            </div>

        </div>
    </section>

    <!-- Food Support Plans -->
    <!-- <section>
        <h3 class="text-2xl font-bold mb-6" style="color:#047857;">Food Support Plans</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6"> -->

            <!-- Cash Alternatives -->
            <!-- <div class="bg-gradient-to-br from-cyan-500 to-blue-600 text-white rounded-2xl shadow-lg p-6">
                <h4 class="text-xl font-bold mb-4">Cash Alternatives</h4>
                <div class="space-y-4">
                    <div class="bg-white/20 p-3 rounded-lg">
                        <p class="text-sm">N105,000 (for 25-week plan)</p>
                        <p class="text-2xl font-bold">N105,000</p>
                    </div>
                    <div class="bg-white/20 p-3 rounded-lg">
                        <p class="text-sm">N210,000 (for 50-week plan)</p>
                        <p class="text-2xl font-bold">N210,000</p>
                    </div>
                </div>
            </div>

        </div>
    </section> -->
</main>

<!-- Footer -->
<footer class="bg-gray-900 text-white mt-16">
  <div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
      <!-- FCCEI Info -->
      <div>
        <div class="flex items-center space-x-4 mb-4">
          <div class="w-20 h-20 flex items-center justify-center overflow-hidden">
            <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full" />
          </div>
          <div class="flex flex-col">
            <span class="text-3xl font-extrabold leading-none" style="color: #10B981;">FCCEI</span>
            <span class="text-[11px] font-bold uppercase tracking-widest" style="color: #047857;">Food Chain Community Empowerment Initiative</span>
          </div>
        </div>
        <p class="text-gray-400 mb-6 -mt-2">
          Empowering communities to achieve food security and sustainable growth through collaborative local initiatives.
        </p>
        <div class="flex space-x-4">
          <a href="https://web.facebook.com/profile.php?id=61585986685899" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-[#10B981] group" style="background: rgba(255,255,255,0.05);">
            <i class="fab fa-facebook-f text-[#10B981] group-hover:text-white"></i>
          </a>
          <a href="https://www.tiktok.com/@foodchainempowerm?lang=en" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-[#10B981] group" style="background: rgba(255,255,255,0.05);">
            <i class="fab fa-tiktok text-[#10B981] group-hover:text-white"></i>
          </a>
          <a href="https://www.instagram.com/foodchainempowerment/" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-[#10B981] group" style="background: rgba(255,255,255,0.05);">
            <i class="fab fa-instagram text-[#10B981] group-hover:text-white"></i>
          </a>
          <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-[#10B981] group" style="background: rgba(255,255,255,0.05);">
            <i class="fab fa-whatsapp text-[#10B981] group-hover:text-white"></i>
          </a>
        </div>
      </div>
      <!-- Quick Links -->
      <div>
        <h4 class="text-lg font-bold mb-6">Quick Links</h4>
        <ul class="space-y-3">
          <li><a href="#home" class="text-gray-400 hover:text-white transition">Home</a></li>
          <li><a href="#about" class="text-gray-400 hover:text-white transition">About Us</a></li>
          <li><a href="#products" class="text-gray-400 hover:text-white transition">Products</a></li>
          <li><a href="#blog" class="text-gray-400 hover:text-white transition">Blog</a></li>
        </ul>
      </div>
      <!-- Legal -->
      <div>
        <h4 class="text-lg font-bold mb-6">Legal</h4>
        <ul class="space-y-3">
          <li><a href="/privacy" class="text-gray-400 hover:text-white transition">Privacy Policy</a></li>
          <li><a href="/terms" class="text-gray-400 hover:text-white transition">Terms of Service</a></li>
          <li><a href="/cookies" class="text-gray-400 hover:text-white transition">Cookie Policy</a></li>
          <li><a href="/compliance" class="text-gray-400 hover:text-white transition">Compliance</a></li>
        </ul>
      </div>
      <!-- Resources -->
      <div>
        <h4 class="text-lg font-bold mb-6">Resources</h4>
        <ul class="space-y-3">
          <li><a href="/help" class="text-gray-400 hover:text-white transition">Help Center</a></li>
          <li><a href="/faq" class="text-gray-400 hover:text-white transition">FAQ</a></li>
          <li><a href="/community" class="text-gray-400 hover:text-white transition">Community</a></li>
          <li><a href="/affiliate" class="text-gray-400 hover:text-white transition">Affiliate Program</a></li>
        </ul>
      </div>
    </div>
    <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
      <p>&copy; 2024 FCCEI. All rights reserved. | Designed By Bassiprog Digital Solution</p>
    </div>
  </div>
</footer>

<!-- Chat Button -->
<a href="https://wa.me/+2348168018123" target="_blank" class="fixed bottom-8 right-8 z-[60] group">
  <span class="absolute right-full mr-4 top-1/2 -translate-y-1/2 bg-gray-900 text-white text-xs font-bold px-4 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition whitespace-nowrap shadow-xl">
    Chat with an Event Manager
  </span>
  <div class="bg-[#25D366] p-4 rounded-full shadow-2xl hover:scale-110 transition duration-300">
    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
    </svg>
  </div>
</a>

<!-- Optional: Include JavaScript for mobile menu toggle -->
<script>
  document.getElementById('mobile-menu-button').addEventListener('click', () => {
    document.getElementById('mobile-menu').classList.toggle('hidden');
  });
</script>

</body>
</html>

@if(!App\Models\PageContent::isPageActive('supplier'))
    <!DOCTYPE html>
    <html lang="en">
    <head><title>Page Unavailable</title><style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;background:#f1f5f9;color:#475569;text-align:center;padding:20px;}h1{font-size:2rem;margin-bottom:1rem;}.card{background:white;padding:40px;border-radius:20px;box-shadow:0 10px 40px rgba(0,0,0,0.08);}</style></head>
    <body><div class="card"><h1>Page Currently Unavailable</h1><p>This page has been disabled by the administrator. Please check back later.</p></div></body>
    </html>
@else
<!DOCTYPE html>
<html lang="en">
<head>
  <base target="_self" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>{{ App\Models\PageContent::getContent('supplier', 'page_title', 'Register as a Supplier') }}</title>
  <meta name="description" content="{{ App\Models\PageContent::getContent('supplier', 'page_subtitle', 'Partner with FoodChain as a supplier.') }}" />
  <meta name="keywords" content="FCCEI, supplier, partnership, food security, community" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Tailwind Config -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { DEFAULT: "#4F46E5", light: "#6366F1", dark: "#4338CA" },
            secondary: { DEFAULT: "#10B981", light: "#34D399", dark: "#059669" },
            accent: { DEFAULT: "#F59E0B", light: "#FBBF24", dark: "#D97706" }
          },
          fontFamily: {
            'sans': ['Inter', 'system-ui', 'sans-serif'],
            'display': ['Poppins', 'system-ui', 'sans-serif']
          },
          backgroundImage: {
            'slide-1': "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("assets/landing/images/buildi.jpeg") }}')",
            'slide-2': "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("assets/landing/images/empowerment.jpeg") }}')",
            'slide-3': "linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('{{ asset("assets/landing/images/foodchan.jpeg") }}')"
          },
          animation: {
            'slide-in': 'slideIn 0.5s ease-out forwards',
            'fade-in': 'fadeIn 0.8s ease-out forwards',
            'float': 'float 3s ease-in-out infinite'
          },
          keyframes: {
            slideIn: {
              '0%': { transform: 'translateY(20px)', opacity: '0' },
              '100%': { transform: 'translateY(0)', opacity: '1' }
            },
            fadeIn: {
              '0%': { opacity: '0' },
              '100%': { opacity: '1' }
            },
            float: {
              '0%, 100%': { transform: 'translateY(0)' },
              '50%': { transform: 'translateY(-10px)' }
            }
          }
        }
      }
    }
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap');
    /* Additional custom styles */
    .hero-slide { ... }
    /* ... other styles as previously defined ... */
  </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans">

<!-- Header Navigation -->
<header class="sticky top-0 z-50 bg-white shadow-sm">
  <nav class="container mx-auto px-4 py-4">
    <div class="flex justify-between items-center">
      <!-- Logo & Title -->
      <div class="flex items-center space-x-2">
        <div class="w-20 h-15 flex items-center justify-center overflow-hidden">
          <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full" />
        </div>
        <div class="flex flex-col justify-center">
          <span class="text-3xl font-extrabold leading-tight" style="color: #26C1C9;">FCCEI</span>
          <span class="text-sm font-bold uppercase tracking-widest" style="color: #1D5FB5;">Food Chain Community Empowerment Initiative</span>
        </div>
      </div>
      <!-- Desktop Navigation -->
      <div class="hidden md:flex items-center space-x-8">
        <a href="{{ route('landing') }}" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Home</a>
        <!-- About dropdown -->
        <div class="relative group">
          <a href="{{ route('landing') }}#about" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
            About
            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </a>
          <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
            <!-- <a href="#board-members" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Board Members</a> -->
            <a href="{{ route('team') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a>
          </div>
        </div>
        <a href="{{ route('landing') }}#products" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Membership</a>
        <!-- Blog dropdown -->
        <div class="relative group">
          <a href="{{ route('landing') }}#blog" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
            Blog
            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </a>
          <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
            <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Gallary</a>
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
            <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become a Sponsor</a>
            <a href="{{ route('sponsor') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become a Supplier</a>
            <a href="{{ route('rider') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become a Logistics Partner (Riders Only)</a>
            </div>

        </div>


        <a href="{{ route('landing') }}#contact" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Contact</a>
        <!-- Auth buttons -->
        <div class="flex items-center space-x-4">
          <a href="{{ route('login') }}" class="px-6 py-2 text-primary font-medium hover:bg-primary hover:text-white border-2 border-primary rounded-lg transition-all duration-200">Login</a>
          <a href="{{ route('register') }}" class="px-6 py-2 bg-primary text-white font-medium hover:bg-primary-dark rounded-lg transition-all duration-200">Get Started</a>
        </div>
      </div>
      <!-- Mobile menu button -->
      <button id="mobile-menu-button" class="md:hidden text-gray-700">
        <i class="fas fa-bars text-2xl"></i>
      </button>
    </div>
    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden mt-4 space-y-4">
      <a href="{{ route('landing') }}" class="block py-2 text-gray-700 hover:text-primary font-medium">Home</a>
      <!-- Repeat similar structure for mobile menu items with dropdowns as needed -->
      <!-- ... -->
    </div>
  </nav>
</header>

<!-- Main Content -->
<main class="flex-1">

 <section
  class="w-full h-[220px] sm:h-[320px] md:h-[420px] lg:h-[520px] bg-cover bg-center"
  style="background-image: url('{{ asset("assets/landing/images/foodchan.jpeg") }}');">
</section>


<!-- Supplier Section -->
<section class="py-12 max-w-7xl mx-auto px-4">
  <h2 class="text-3xl font-bold mb-8 text-center" style="color: #26C1C9;">Our Esteemed Suppliers</h2>
  <p class="max-w-3xl mx-auto mb-8 text-center text-gray-700">
    Our suppliers are dedicated organizations and individuals who believe in our mission. They have generously contributed resources, funding, and support to help us reach more communities and eradicate hunger. Their commitment drives our efforts forward and inspires positive change.
  </p>
  <!-- Board Members styled images -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
    <!-- Example Board Member -->
    <div class="bg-white p-4 rounded-lg shadow flex flex-col items-center text-center">
      <img src="{{ asset('assets/landing/images/delivery2.png') }}" alt="Board Member 1" class="w-32 h-32 object-cover rounded-full mb-4 border-4 border-gray-300" />
      <h3 class="text-xl font-semibold mb-2">Jane Doe</h3>
      <p class="text-gray-600">Founder of Green Foods, committed to supplying high-quality, sustainably sourced agricultural products while promoting food security.</p>
    </div>
    <!-- Repeat for other board members -->
    <div class="bg-white p-4 rounded-lg shadow flex flex-col items-center text-center">
      <img src="{{ asset('assets/landing/images/imagesdeliv.png') }}" alt="Board Member 2" class="w-32 h-32 object-cover rounded-full mb-4 border-4 border-gray-300" />
      <h3 class="text-xl font-semibold mb-2">John Smith</h3>
      <p class="text-gray-600">CEO of FoodTech Inc. driving supplier success through innovative food supply networks..</p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow flex flex-col items-center text-center">
      <img src="{{ asset('assets/landing/images/imagesdelivery.png') }}" alt="Board Member 3" class="w-32 h-32 object-cover rounded-full mb-4 border-4 border-gray-300" />
      <h3 class="text-xl font-semibold mb-2">Robert Johnson</h3>
      <p class="text-gray-600">Philanthropist supporting food empowerment programs.</p>
    </div>
  </div>
  <!-- Button to open the modal -->
  <div class="text-center">
    <button
      class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg shadow-md hover:bg-green-700 transition"
      onclick="toggleSupplierModal()"
    >
      Register as a Supplier
    </button>
  </div>
</section>

<!-- Modal Overlay -->
<div id="supplierModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
  <!-- Modal Content -->
  <div class="bg-white rounded-lg max-w-2xl w-full p-6 relative shadow-lg">
    <!-- Close Button -->
    <button id="closeSupplierModal" class="absolute top-3 right-3 text-gray-500 hover:text-gray-700">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>
    <h4 class="text-2xl font-semibold mb-4" style="color: #26C1C9;">{{ App\Models\PageContent::getContent('supplier', 'page_title', 'Register as a Supplier') }}</h4>
    <form method="POST" action="{{ route('landing.supplier') }}" class="space-y-4" id="supplierForm">
      @csrf
      <div>
        <label for="name" class="block text-gray-700 mb-2">Name</label>
        <input type="text" id="name" name="name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Your Name" />
      </div>
      <div>
        <label for="organization" class="block text-gray-700 mb-2">Organization</label>
        <input type="text" id="organization" name="organization" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Your Organization" />
      </div>
      <div>
        <label for="email" class="block text-gray-700 mb-2">Email</label>
        <input type="email" id="email" name="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="you@example.com" />
      </div>
      <div>
        <label for="message" class="block text-gray-700 mb-2">Message</label>
        <textarea id="message" name="message" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400" placeholder="Tell us about your sponsorship interest"></textarea>
      </div>
      <button type="submit" class="w-full px-4 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition">Send Inquiry</button>
    </form>
  </div>
</div>

<!-- JavaScript to handle modal open/close -->
<script>
  // Function to toggle supplier modal
  function toggleSupplierModal() {
    const modal = document.getElementById('supplierModal');
    modal.classList.toggle('hidden');
    modal.classList.toggle('flex');
  }

  // Close button event
  document.getElementById('closeSupplierModal').addEventListener('click', () => {
    toggleSupplierModal();
  });

  // Optional: close modal when clicking outside content
  window.addEventListener('click', (e) => {
    const modal = document.getElementById('supplierModal');
    if (e.target === modal) {
      toggleSupplierModal();
    }
  });
</script>

</main>

<!-- Newsletter -->
<section class="py-16 bg-gradient-to-r from-primary to-secondary">
  <div class="container mx-auto px-4 text-center">
    <h2 class="text-3xl font-bold text-white mb-4 font-display">Stay Updated</h2>
    <p class="text-white/90 mb-8 max-w-2xl mx-auto">Subscribe to our newsletter for the latest tips, success stories, and platform updates</p>
    <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
      <input type="email" placeholder="Your email address" class="flex-1 px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white" />
      <button type="submit" class="px-8 py-3 bg-white text-primary font-bold rounded-lg hover:bg-gray-100 transition-all duration-200">Subscribe</button>
    </form>
  </div>
</section>

<!-- Footer (Your provided detailed footer) -->
<footer class="bg-gray-900 text-white">
  <div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
      <!-- FCCEI Info -->
      <div>
          <div class="flex items-center space-x-4">
            <div class="w-32 h-32 flex items-center justify-center overflow-hidden">
              <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full" />
            </div>
            <div class="flex flex-col">
              <span class="text-3xl font-extrabold leading-none" style="color: #26C1C9;">FCCEI</span>
              <span class="text-[11px] font-bold uppercase tracking-widest" style="color: #1D5FB5;">Food Chain Community Empowerment Initiative</span>
            </div>
          </div>
          <p class="text-gray-400 mb-6 -mt-2">
            Empowering communities to achieve food security and sustainable growth through collaborative local initiatives.
          </p>
          <div class="flex space-x-4">
          <a href="https://web.facebook.com/profile.php?id=61585986685899" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
            <i class="fab fa-facebook-f text-brandTeal group-hover:text-white"></i>
          </a>

           <a href="https://www.tiktok.com/@foodchainempowerm?lang=en" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
                <i class="fab fa-tiktok text-brandTeal group-hover:text-white"></i>
            </a>

          <!-- <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
            <i class="fab fa-twitter text-brandTeal group-hover:text-white"></i>
          </a> -->
          <a href="https://www.instagram.com/foodchainempowerment/" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
            <i class="fab fa-instagram text-brandTeal group-hover:text-white"></i>
          </a>
          <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
            <i class="fab fa-whatsapp text-brandTeal group-hover:text-white"></i>
          </a>
        </div>
        </div>
      <!-- Quick Links -->
      <div>
        <h4 class="text-lg font-bold mb-6">Quick Links</h4>
        <ul class="space-y-3">
          <li><a href="{{ route('landing') }}" class="hover:text-white transition">Home</a></li>
          <li><a href="{{ route('landing') }}#about" class="hover:text-white transition">About Us</a></li>
          <li><a href="{{ route('landing') }}#products" class="hover:text-white transition">Products</a></li>
          <li><a href="{{ route('landing') }}#blog" class="hover:text-white transition">Blog</a></li>
        </ul>
      </div>
      <!-- Legal -->
      <div>
        <h4 class="text-lg font-bold mb-6">Legal</h4>
        <ul class="space-y-3">
          <li><a href="/privacy" class="hover:text-white transition">Privacy Policy</a></li>
          <li><a href="/terms" class="hover:text-white transition">Terms of Service</a></li>
          <li><a href="/cookies" class="hover:text-white transition">Cookie Policy</a></li>
          <li><a href="/compliance" class="hover:text-white transition">Compliance</a></li>
        </ul>
      </div>
      <!-- Resources -->
      <div>
        <h4 class="text-lg font-bold mb-6">Resources</h4>
        <ul class="space-y-3">
          <li><a href="/help" class="hover:text-white transition">Help Center</a></li>
          <li><a href="/faq" class="hover:text-white transition">FAQ</a></li>
          <li><a href="/community" class="hover:text-white transition">Community</a></li>
          <li><a href="/affiliate" class="hover:text-white transition">Affiliate Program</a></li>
        </ul>
      </div>
    </div>
    <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
      <p>&copy; 2024 FCCEI. All rights reserved. | Designed By Bassiprog Digital Solution</p>
    </div>
  </div>
</footer>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    var btn = document.getElementById('mobile-menu-button');
    var menu = document.getElementById('mobile-menu');
    if (btn && menu) { btn.addEventListener('click', function() { menu.classList.toggle('hidden'); }); }
});
</script>
</body>
</html>
@endif

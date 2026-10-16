<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>FCCEI CSR Initiatives</title>
@include('partials.seo-meta', [
    'seoTitle' => 'FCCEI CSR Initiatives',
    'seoDesc' => 'Discover FCCEI\'s Corporate Social Responsibility initiatives focused on community development, food security, and sustainable growth across Nigeria.',
    'schema' => [
        [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'FCCEI CSR Initiatives',
            'url' => url('/csr'),
            'description' => 'Discover FCCEI\'s Corporate Social Responsibility initiatives focused on community development and food security.',
        ],
    ],
])

<!-- Tailwind CSS & Font Awesome -->
    @include('partials.vite-css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
<link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">

</head>
<body class="bg-gray-50 font-sans text-gray-800">
@include('partials.skip-link', ['skipTarget' => '#about'])

<!-- HEADER -->
<header class="sticky top-0 bg-white shadow z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <!-- Logo & Title -->
    <div class="flex items-center space-x-3">
      <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="w-16 h-16 object-contain" />
      <div>
        <p class="text-3xl font-bold text-[#10B981] m-0">FCCEI</p>
        <p class="text-sm uppercase tracking-widest text-[#047857]">Corporate Social Responsibility</p>
      </div>
    </div>
    <!-- Navigation -->
    <nav class="hidden md:flex space-x-8 font-medium text-gray-700">
      <a href="#about" class="hover:text-[#10B981] transition">About CSR</a>
      <a href="#initiatives" class="hover:text-[#10B981] transition">Initiatives</a>
      <a href="#impact" class="hover:text-[#10B981] transition">Impact</a>
      <a href="#get-involved" class="hover:text-[#10B981] transition">Get Involved</a>
      <a href="#contact" class="hover:text-[#10B981] transition">Contact</a>
    </nav>
    <!-- Mobile menu button -->
    <div class="md:hidden">
      <button id="menu-toggle" class="text-gray-700 focus:outline-none">
        <i class="fas fa-bars text-2xl"></i>
      </button>
    </div>
  </div>
  <!-- Mobile Menu -->
  <div id="mobile-menu" class="hidden md:hidden px-6 pb-4 space-y-4 bg-white border-t border-gray-200">
    <a href="#about" class="block py-2 hover:text-[#10B981] transition">About CSR</a>
    <a href="#initiatives" class="block py-2 hover:text-[#10B981] transition">Initiatives</a>
    <a href="#impact" class="block py-2 hover:text-[#10B981] transition">Impact</a>
    <a href="#get-involved" class="block py-2 hover:text-[#10B981] transition">Get Involved</a>
    <a href="#contact" class="block py-2 hover:text-[#10B981] transition">Contact</a>
  </div>
</header>

<!-- HERO: About CSR -->
<section class="relative bg-[#F0F9FA] py-20" id="about">
  <div class="max-w-7xl mx-auto px-6 text-center md:text-left">
    <h1 class="text-4xl md:text-5xl font-extrabold mb-4 text-gray-800">
      Our <span class="text-[#10B981]">CSR Commitment</span>
    </h1>
    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto md:mx-0">
      At FCCEI, we believe in giving back to the community by championing initiatives that foster sustainable development, food security, and social well-being. Our CSR efforts aim to create lasting positive change across Nigeria.
    </p>
    <a href="#get-involved" class="px-8 py-3 bg-[#10B981] text-white rounded-lg font-semibold hover:bg-[#047857] transition">Join Our Initiatives</a>
  </div>
</section>

<!-- CSR Initiatives -->
<section id="initiatives" class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <h3 class="text-3xl md:text-4xl font-bold mb-12 text-center text-gray-800">Our Key <span class="text-[#10B981]">CSR Initiatives</span></h3>
    <div class="grid md:grid-cols-3 gap-8 text-center">
      <!-- Initiative Card -->
      <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="flex justify-center mb-4 w-16 h-16 mx-auto bg-[#059669] rounded-full items-center">
          <i class="fas fa-leaf text-white text-3xl"></i>
        </div>
        <h4 class="text-xl font-semibold mb-2">Community Food Programs</h4>
        <p class="text-gray-600 text-sm">Providing nutritious food to underserved communities and reducing hunger across Nigeria.</p>
      </div>
      <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="flex justify-center mb-4 w-16 h-16 mx-auto bg-[#047857] rounded-full items-center">
          <i class="fas fa-school text-white text-3xl"></i>
        </div>
        <h4 class="text-xl font-semibold mb-2">Educational Support</h4>
        <p class="text-gray-600 text-sm">Supporting schools and youth programs to empower the next generation.</p>
      </div>
      <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="flex justify-center mb-4 w-16 h-16 mx-auto bg-[#D97706] rounded-full items-center">
          <i class="fas fa-hand-holding-heart text-white text-3xl"></i>
        </div>
        <h4 class="text-xl font-semibold mb-2">Health & Wellness</h4>
        <p class="text-gray-600 text-sm">Promoting health awareness and access to medical services in vulnerable areas.</p>
      </div>
    </div>
  </div>
</section>

<!-- Impact Highlights -->
<section id="impact" class="py-20 bg-gray-50">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <h3 class="text-3xl md:text-4xl font-bold mb-8 text-gray-800">
      Our <span class="text-[#10B981]">Impact</span>
    </h3>
    <div class="grid md:grid-cols-4 gap-8 mb-12">
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="text-4xl font-bold mb-2 text-[#10B981]">200+</div>
        <p class="text-gray-600 text-sm">Communities Transformed</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="text-4xl font-bold mb-2 text-[#10B981]">50,000+</div>
        <p class="text-gray-600 text-sm">Lives Impacted</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="text-4xl font-bold mb-2 text-[#10B981]">95%</div>
        <p class="text-gray-600 text-sm">Community Satisfaction</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="text-4xl font-bold mb-2 text-[#10B981]">100+</div>
        <p class="text-gray-600 text-sm">Partnerships & Collaborations</p>
      </div>
    </div>
    <!-- Impact Stories -->
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <p class="mb-4 italic">"FCCEI's CSR initiatives have uplifted our community and brought hope to many families."</p>
        <div class="flex items-center space-x-4">
          <img src="{{ asset('assets/landing/images/uerss.jpg') }}" alt="Impact Testimonial - Mrs. Amina" class="w-12 h-12 rounded-full object-cover" loading="lazy" />
          <div>
            <p class="font-semibold mb-1">Mrs. Amina, Community Leader</p>
            <p class="text-sm text-gray-500">Kano, Nigeria</p>
          </div>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <p class="mb-4 italic">"Our partnership with FCCEI has changed lives and created sustainable change."</p>
        <div class="flex items-center space-x-4">
          <img src="{{ asset('assets/landing/images/user.jpg') }}" alt="Impact Testimonial - Mr. Chinedu" class="w-12 h-12 rounded-full object-cover" loading="lazy" />
          <div>
            <p class="font-semibold mb-1">Mr. Chinedu, NGO Partner</p>
            <p class="text-sm text-gray-500">Abuja, Nigeria</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Get Involved -->
<section id="get-involved" class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6 text-center">
    <h3 class="text-3xl md:text-4xl font-bold mb-8 text-gray-800">
      Be a <span class="text-[#10B981]">Changemaker</span>
    </h3>
    <p class="mb-8 text-gray-600 max-w-2xl mx-auto">
      Discover how you can support FCCEI's CSR initiatives through donations, volunteering, or corporate partnerships. Your support helps us reach more communities and create sustainable change.
    </p>
    <a href="#contact" class="px-8 py-3 bg-[#10B981] text-white rounded-lg font-semibold hover:bg-[#047857] transition">Get Involved</a>
  </div>
</section>

<!-- CONTACT -->
<section id="contact" class="py-20 bg-gray-50">
  <div class="max-w-7xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-bold mb-8 text-center text-gray-800">Contact Us</h2>
    <div class="grid md:grid-cols-2 gap-12">
      <!-- Contact Info -->
      <div>
        <h4 class="text-2xl font-semibold mb-4 text-gray-800">Reach Out</h4>
        <div class="space-y-6 mb-8">
          <div class="flex items-start">
            <div class="w-12 h-12 bg-[#059669] rounded-lg flex items-center justify-center mr-4">
              <i class="fas fa-map-marker-alt text-white"></i>
            </div>
            <div>
              <h5 class="font-semibold mb-1">Office Address for Enquiry</h5>
              <p class="text-gray-600 text-sm">No. 9B, Adejumo Tennis Club Road, opposite DE-RESTED PHARMACY, Ilamoshe Estate, Oke Afa, Ejigbo, Lagos.</p>
              <p class="text-gray-600 text-sm mt-1">Central Warehouse 1: 31, Ofada Street, Off Ladipo Rd, Mushin.</p>
              <p class="text-gray-600 text-sm mt-1">Our Distribution Hubs in Lagos: Oyingbo, Ebutte-ero, Igando, Ikorodu (1 &amp; 2), Ladega, Sango, Ejigbo, Ojuwoye &amp; Mushin Post Office.</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="w-12 h-12 bg-[#0D9488] rounded-lg flex items-center justify-center mr-4">
              <i class="fas fa-phone text-white"></i>
            </div>
            <div>
              <h5 class="font-semibold mb-1">Phone</h5>
              <p class="text-gray-600 text-sm">+234 816 801 8123</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="w-12 h-12 bg-[#D97706] rounded-lg flex items-center justify-center mr-4">
              <i class="fas fa-envelope text-white"></i>
            </div>
            <div>
              <h5 class="font-semibold mb-1">Email</h5>
              <p class="text-gray-600 text-sm">csr@foodchainempowerment.com.ng</p>
            </div>
          </div>
        </div>
        <!-- Social Links -->
        <div>
          <h4 class="font-semibold mb-4">Follow Us</h4>
          <div class="flex space-x-4">
            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#10B981] transition">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#10B981] transition">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#10B981] transition">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#10B981] transition">
              <i class="fab fa-linkedin"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- Contact Form -->
      <div class="bg-white p-8 rounded-xl shadow-lg">
        <h4 class="text-2xl font-semibold mb-6 text-gray-800">Send a Message</h4>
        <form method="POST" action="{{ route('landing.contact') }}" class="space-y-4" id="csrForm">
          @csrf
          @include('partials.form-status')
          <input type="hidden" name="subject" value="CSR page enquiry">
          <div>
            <label for="name" class="block mb-2 text-gray-700 font-medium">Full Name</label>
            <input type="text" id="name" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#10B981]" placeholder="Your Name" />
          </div>
          <div>
            <label for="email" class="block mb-2 text-gray-700 font-medium">Email</label>
            <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#10B981]" placeholder="you@example.com" />
          </div>
          <div>
            <label for="message" class="block mb-2 text-gray-700 font-medium">Message</label>
            <textarea id="message" name="message" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#10B981]" placeholder="Your Message"></textarea>
          </div>
          <button type="submit" class="w-full px-4 py-3 bg-[#10B981] text-white rounded-lg font-semibold hover:bg-[#047857] transition">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
@include('partials.site-footer')

<!-- Scripts -->
<script>
  // Toggle mobile menu
  document.getElementById('menu-toggle').addEventListener('click', () => {
    document.getElementById('mobile-menu').classList.toggle('hidden');
  });
</script>

</body>
</html>

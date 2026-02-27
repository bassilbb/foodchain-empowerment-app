<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>FCCEI CSR Initiatives</title>
<meta name="description" content="Discover FCCEI's Corporate Social Responsibility initiatives focused on community development, food security, and sustainable growth." />

<!-- Tailwind CSS & Font Awesome -->
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: { DEFAULT: "#26C1C9" },
        secondary: { DEFAULT: "#1D5FB5" },
        accent: { DEFAULT: "#F59E0B" },
        bgGradient: {
          start: "#26C1C9",
          end: "#1D5FB5"
        }
      }
    }
  }
}
</script>
</head>
<body class="bg-gray-50 font-sans text-gray-800">

<!-- HEADER -->
<header class="sticky top-0 bg-white shadow z-50">
  <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
    <!-- Logo & Title -->
    <div class="flex items-center space-x-3">
      <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="w-16 h-16 object-contain" />
      <div>
        <h1 class="text-3xl font-bold text-[#26C1C9]">FCCEI</h1>
        <p class="text-sm uppercase tracking-widest text-[#1D5FB5]">Corporate Social Responsibility</p>
      </div>
    </div>
    <!-- Navigation -->
    <nav class="hidden md:flex space-x-8 font-medium text-gray-700">
      <a href="#about" class="hover:text-[#26C1C9] transition">About CSR</a>
      <a href="#initiatives" class="hover:text-[#26C1C9] transition">Initiatives</a>
      <a href="#impact" class="hover:text-[#26C1C9] transition">Impact</a>
      <a href="#get-involved" class="hover:text-[#26C1C9] transition">Get Involved</a>
      <a href="#contact" class="hover:text-[#26C1C9] transition">Contact</a>
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
    <a href="#about" class="block py-2 hover:text-[#26C1C9] transition">About CSR</a>
    <a href="#initiatives" class="block py-2 hover:text-[#26C1C9] transition">Initiatives</a>
    <a href="#impact" class="block py-2 hover:text-[#26C1C9] transition">Impact</a>
    <a href="#get-involved" class="block py-2 hover:text-[#26C1C9] transition">Get Involved</a>
    <a href="#contact" class="block py-2 hover:text-[#26C1C9] transition">Contact</a>
  </div>
</header>

<!-- HERO: About CSR -->
<section class="relative bg-[#F0F9FA] py-20" id="about">
  <div class="max-w-7xl mx-auto px-6 text-center md:text-left">
    <h2 class="text-4xl md:text-5xl font-extrabold mb-4 text-gray-800">
      Our <span class="text-[#26C1C9]">CSR Commitment</span>
    </h2>
    <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto md:mx-0">
      At FCCEI, we believe in giving back to the community by championing initiatives that foster sustainable development, food security, and social well-being. Our CSR efforts aim to create lasting positive change across Nigeria.
    </p>
    <a href="#get-involved" class="px-8 py-3 bg-[#26C1C9] text-white rounded-lg font-semibold hover:bg-[#1D5FB5] transition">Join Our Initiatives</a>
  </div>
</section>

<!-- CSR Initiatives -->
<section id="initiatives" class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-6">
    <h3 class="text-3xl md:text-4xl font-bold mb-12 text-center text-gray-800">Our Key <span class="text-[#26C1C9]">CSR Initiatives</span></h3>
    <div class="grid md:grid-cols-3 gap-8 text-center">
      <!-- Initiative Card -->
      <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="flex justify-center mb-4 w-16 h-16 mx-auto bg-[#26C1C9] rounded-full items-center">
          <i class="fas fa-leaf text-white text-3xl"></i>
        </div>
        <h4 class="text-xl font-semibold mb-2">Community Food Programs</h4>
        <p class="text-gray-600 text-sm">Providing nutritious food to underserved communities and reducing hunger across Nigeria.</p>
      </div>
      <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="flex justify-center mb-4 w-16 h-16 mx-auto bg-[#1D5FB5] rounded-full items-center">
          <i class="fas fa-school text-white text-3xl"></i>
        </div>
        <h4 class="text-xl font-semibold mb-2">Educational Support</h4>
        <p class="text-gray-600 text-sm">Supporting schools and youth programs to empower the next generation.</p>
      </div>
      <div class="bg-gray-50 p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="flex justify-center mb-4 w-16 h-16 mx-auto bg-[#F59E0B] rounded-full items-center">
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
      Our <span class="text-[#26C1C9]">Impact</span>
    </h3>
    <div class="grid md:grid-cols-4 gap-8 mb-12">
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="text-4xl font-bold mb-2 text-[#26C1C9]">200+</div>
        <p class="text-gray-600 text-sm">Communities Transformed</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="text-4xl font-bold mb-2 text-[#26C1C9]">50,000+</div>
        <p class="text-gray-600 text-sm">Lives Impacted</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="text-4xl font-bold mb-2 text-[#26C1C9]">95%</div>
        <p class="text-gray-600 text-sm">Community Satisfaction</p>
      </div>
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <div class="text-4xl font-bold mb-2 text-[#26C1C9]">100+</div>
        <p class="text-gray-600 text-sm">Partnerships & Collaborations</p>
      </div>
    </div>
    <!-- Impact Stories -->
    <div class="max-w-4xl mx-auto space-y-6">
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <p class="mb-4 italic">"FCCEI’s CSR initiatives have uplifted our community and brought hope to many families."</p>
        <div class="flex items-center space-x-4">
          <img src="{{ asset('assets/landing/images/testimonial1.jpg') }}" alt="Impact Testimonial" class="w-12 h-12 rounded-full object-cover" />
          <div>
            <p class="font-semibold mb-1">Mrs. Amina, Community Leader</p>
            <p class="text-sm text-gray-500">Kano, Nigeria</p>
          </div>
        </div>
      </div>
      <div class="bg-white p-6 rounded-xl shadow hover:shadow-xl transition">
        <p class="mb-4 italic">"Our partnership with FCCEI has changed lives and created sustainable change."</p>
        <div class="flex items-center space-x-4">
          <img src="{{ asset('assets/landing/images/testimonial2.jpg') }}" alt="Impact Testimonial" class="w-12 h-12 rounded-full object-cover" />
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
      Be a <span class="text-[#26C1C9]">Changemaker</span>
    </h3>
    <p class="mb-8 text-gray-600 max-w-2xl mx-auto">
      Discover how you can support FCCEI’s CSR initiatives through donations, volunteering, or corporate partnerships. Your support helps us reach more communities and create sustainable change.
    </p>
    <a href="#contact" class="px-8 py-3 bg-[#26C1C9] text-white rounded-lg font-semibold hover:bg-[#1D5FB5] transition">Get Involved</a>
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
            <div class="w-12 h-12 bg-[#26C1C9] rounded-lg flex items-center justify-center mr-4">
              <i class="fas fa-map-marker-alt text-white"></i>
            </div>
            <div>
              <h5 class="font-semibold mb-1">Head Office</h5>
              <p class="text-gray-600 text-sm">KM 54, Lagos - Ibadan Expressway, Mowe, Ogun State</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="w-12 h-12 bg-[#10B981] rounded-lg flex items-center justify-center mr-4">
              <i class="fas fa-phone text-white"></i>
            </div>
            <div>
              <h5 class="font-semibold mb-1">Phone</h5>
              <p class="text-gray-600 text-sm">+234 816 801 8123</p>
            </div>
          </div>
          <div class="flex items-start">
            <div class="w-12 h-12 bg-[#F59E0B] rounded-lg flex items-center justify-center mr-4">
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
            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#26C1C9] transition">
              <i class="fab fa-facebook-f"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#26C1C9] transition">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#26C1C9] transition">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#26C1C9] transition">
              <i class="fab fa-linkedin"></i>
            </a>
          </div>
        </div>
      </div>
      <!-- Contact Form -->
      <div class="bg-white p-8 rounded-xl shadow-lg">
        <h4 class="text-2xl font-semibold mb-6 text-gray-800">Send a Message</h4>
        <form class="space-y-4" id="csrForm">
          <div>
            <label for="name" class="block mb-2 text-gray-700 font-medium">Full Name</label>
            <input type="text" id="name" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#26C1C9]" placeholder="Your Name" />
          </div>
          <div>
            <label for="email" class="block mb-2 text-gray-700 font-medium">Email</label>
            <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#26C1C9]" placeholder="you@example.com" />
          </div>
          <div>
            <label for="message" class="block mb-2 text-gray-700 font-medium">Message</label>
            <textarea id="message" name="message" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#26C1C9]" placeholder="Your Message"></textarea>
          </div>
          <button type="submit" class="w-full px-4 py-3 bg-[#26C1C9] text-white rounded-lg font-semibold hover:bg-[#1D5FB5] transition">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="bg-gray-900 text-white mt-12">
  <div class="max-w-7xl mx-auto px-6 py-8 grid md:grid-cols-4 gap-8">
    <!-- Logo & Info -->
    <div>
      <div class="flex items-center mb-4 space-x-3">
        <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="w-16 h-16 object-contain" />
        <div>
          <h3 class="text-2xl font-bold" style="color:#26C1C9;">FCCEI</h3>
          <p class="text-[11px] uppercase tracking-widest" style="color:#1D5FB5;">Food Chain Community Empowerment</p>
        </div>
      </div>
      <p class="text-gray-400 mb-4">Partner with us to create sustainable social change through impactful CSR programs.</p>
      <div class="flex space-x-4">
        <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#26C1C9] transition">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#26C1C9] transition">
          <i class="fab fa-twitter"></i>
        </a>
        <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#26C1C9] transition">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="#" class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center hover:bg-[#26C1C9] transition">
          <i class="fab fa-linkedin"></i>
        </a>
      </div>
    </div>
    <!-- Quick Links -->
    <div>
      <h4 class="font-semibold mb-4">Quick Links</h4>
      <ul class="space-y-2">
        <li><a href="#about" class="hover:text-[#26C1C9] transition">About CSR</a></li>
        <li><a href="#initiatives" class="hover:text-[#26C1C9] transition">Initiatives</a></li>
        <li><a href="#impact" class="hover:text-[#26C1C9] transition">Impact</a></li>
        <li><a href="#get-involved" class="hover:text-[#26C1C9] transition">Get Involved</a></li>
        <li><a href="#contact" class="hover:text-[#26C1C9] transition">Contact</a></li>
      </ul>
    </div>
    <!-- Legal & Resources -->
    <div>
      <h4 class="font-semibold mb-4">Legal</h4>
      <ul class="space-y-2">
        <li><a href="/privacy" class="hover:text-[#26C1C9] transition">Privacy Policy</a></li>
        <li><a href="/terms" class="hover:text-[#26C1C9] transition">Terms of Use</a></li>
        <li><a href="/help" class="hover:text-[#26C1C9] transition">Help Center</a></li>
        <li><a href="/faq" class="hover:text-[#26C1C9] transition">FAQ</a></li>
      </ul>
    </div>
    <!-- Bottom -->
    <div class="col-span-4 border-t border-gray-700 pt-4 text-center text-gray-400 text-sm">
      &copy; 2024 FCCEI. All rights reserved. | Designed by Bassiprog Digital Solutions
    </div>
  </div>
</footer>

<!-- Scripts -->
<script>
  // Toggle mobile menu
  document.getElementById('menu-toggle').addEventListener('click', () => {
    document.getElementById('mobile-menu').classList.toggle('hidden');
  });
</script>

</body>
</html>

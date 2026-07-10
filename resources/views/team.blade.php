@if(!App\Models\PageContent::isPageActive('team'))
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
  <title>{{ App\Models\PageContent::getContent('team', 'page_title', 'Our Team') }}</title>
  <meta name="description" content="{{ App\Models\PageContent::getContent('team', 'page_subtitle', 'Meet the dedicated people behind FoodChain.') }}" />
  <meta name="keywords" content="FCCEI, team, food chain, community, empowerment" />
  <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
  <!-- Font Awesome 6 -->
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
            primary: { DEFAULT: "#059669", light: "#10B981", dark: "#047857" },
            secondary: { DEFAULT: "#0D9488", light: "#14B8A6", dark: "#0F766E" },
            accent: { DEFAULT: "#D97706", light: "#F59E0B", dark: "#B45309" }
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
          <span class="text-3xl font-extrabold leading-tight" style="color: #059669;">FCCEI</span>
          <span class="text-sm font-bold uppercase tracking-widest" style="color: #047857;">Food Chain Community Empowerment Initiative</span>
        </div>
      </div>
      <!-- Desktop Navigation -->
      <div class="hidden md:flex items-center space-x-8">
        <a href="{{ route('landing') }}" class="nav-link text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">Home</a>
        <!-- About dropdown -->
        <div class="relative group">
          <a href="{{ route('landing') }}#about" class="nav-link text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200 flex items-center">
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
        <a href="{{ route('landing') }}#products" class="nav-link text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">Membership</a>
        <!-- Blog dropdown -->
        <div class="relative group">
          <a href="{{ route('landing') }}#blog" class="nav-link text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200 flex items-center">
            Blog
            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
            </svg>
          </a>
          <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
            <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Gallery</a>
          </div>
        </div>

        <!-- About with dropdown -->
        <div class="relative group">
            <a href="#" class="nav-link text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200 flex items-center">
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

        <a href="{{ route('landing') }}#contact" class="nav-link text-gray-700 hover:text-emerald-600 font-medium transition-colors duration-200">Contact</a>
        <!-- Auth buttons -->
        <div class="flex items-center space-x-4">
          <a href="{{ route('login') }}" class="px-6 py-2 text-emerald-600 font-medium hover:bg-emerald-600 hover:text-white border-2 border-emerald-600 rounded-lg transition-all duration-200">Login</a>
          <a href="{{ route('register') }}" class="px-6 py-2 bg-emerald-600 text-white font-medium hover:bg-emerald-700 rounded-lg transition-all duration-200">Get Started</a>
        </div>
      </div>
      <!-- Mobile menu button -->
      <button id="mobile-menu-button" class="md:hidden text-gray-700">
        <i class="fas fa-bars text-2xl"></i>
      </button>
    </div>
    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden mt-4 space-y-4">
      <a href="{{ route('landing') }}" class="block py-2 text-gray-700 hover:text-emerald-600 font-medium">Home</a>
      <!-- Repeat similar structure for mobile menu items with dropdowns as needed -->
      <!-- ... -->
    </div>
  </nav>
</header>

<!-- Main Content -->
<main class="flex-1">

<!-- Sponsor Section -->
<!-- <section class="py-12 max-w-7xl mx-auto px-4">
  <!-- <h2 class="text-3xl font-bold mb-8 text-center" style="color: #059669;">Our Esteemed Sponsors</h2>
  <p class="max-w-3xl mx-auto mb-8 text-center text-gray-700">
    Our sponsors are dedicated organizations and individuals who believe in our mission. They have generously contributed resources, funding, and support to help us reach more communities and eradicate hunger. Their commitment drives our efforts forward and inspires positive change.
  </p> -->

  <!-- <div class="text-center mb-16">
        <h2 class="text-4xl font-bold text-gray-900 mb-4 font-display">{{ App\Models\PageContent::getContent('team', 'page_title', 'Our Team') }}</h2>
        <p class="text-gray-600 max-w-2xl mx-auto">The passionate individuals behind FCCEI's success.</p>
    </div> -->
  <!-- Board Members styled images -->
  <!-- <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8"> -->
    <!-- Example Board Member -->
    <!-- <div class="bg-white p-4 rounded-lg shadow flex flex-col items-center text-center">
      <img src="images/frankojipics.png" alt="Board Member 1" class="w-32 h-32 object-cover rounded-full mb-4 border-4 border-gray-300" />
      <h3 class="text-xl font-semibold mb-2">Frank Ogochukwu Orji</h3>
      <p class="text-gray-600">CEO & Founder.</p>
      <p class="text-gray-600">Frank Ogochukwu Orji is the visionary Founder and Chief Executive Officer of Food Chain Community Empowerment Initiative, a dynamic social enterprise committed to delivering innovative solutions that transform lives of women from Low-income households, Low-income earners, small business owners and students of tertiary institutions.
        With over 15 years of Business and leadership experience, Frank has built a reputation for strategic foresight, operational excellence, and an unwavering commitment to social impacts. Under his leadership, FCCEI has and will continue to achieve its strategic goals of Zero hunger, sustainability and food security for low-income households in Nigeria.
        A passionate advocate for sustainability, innovation and community impact, Frank fosters a culture of collaboration and continuous improvement, empowering teams to exceed expectations and deliver measurable results.
        Beyond corporate success, Frank actively contributes to industry associations and community initiatives, reinforcing a belief that business growth and social responsibility go hand in hand.
        Frank holds a Bachelor of Arts in Philosophy from the prestigious University of Nigeria, Nsukka.</p>
        <p class="text-gray-600">He is currently studying Business Leadership in the Executive Masters in Business Administration from the University of Nigeria Business School, Enugu Campus.
        He is also a member of Global Association of Risk Professionals; member, Advertising Regulatory Council of Nigeria; fellow, Institute of Health insurance and managed care of Nigeria.
        He has other certifications including Health economics from World Bank Academy, Washington DC, USA; Advance Diploma from Digital Marketing Institute and continues to engage in lifelong learning to stay ahead in an ever-evolving business landscape.
        His experience span across Healthcare, Agri-Business, Logistics, FMCG and Insurance.</p>

    </div> -->
    <!-- Repeat for other board members -->
    <!-- <div class="bg-white p-4 rounded-lg shadow flex flex-col items-center text-center">
      <img src="images/basspix.png" alt="Board Member 2" class="w-32 h-32 object-cover rounded-full mb-4 border-4 border-gray-300" />
      <h3 class="text-xl font-semibold mb-2">Bassey Eno</h3>
      <p class="text-gray-600">Chief Operating Officer.</p>
      <p class="text-gray-600">Bassey Eno is the Chief Operating Officer of the Food Chain Community Empowerment Initiative (FCCEI), where he oversees the organization's daily operations and ensures seamless coordination across all departments. He plays a key role in translating strategic goals into effective operational execution while maintaining efficiency, accountability, and organizational cohesion.
        With over 10 years of experience in operations management and organizational strategy, Bassey has successfully implemented systems and processes that improve productivity, optimize resources, and strengthen internal collaboration. His leadership approach is results-driven, focused on continuous improvement, and aligned with the organization's mission.
        Bassey is deeply committed to sustainable growth and community development. He works closely with executive leadership to support long-term planning while ensuring operational integrity and service excellence.</p>
        <p>Under his guidance, FCCEI has enhanced member engagement, improved service delivery, and expanded its impact across both urban and rural communities.
        Known for his hands-on leadership style and strong problem-solving skills, Bassey fosters a transparent, inclusive, and performance-oriented organizational culture. His vision is to ensure that FCCEI consistently delivers meaningful value and lasting positive impact to every member of the communities it serves.</p>

    </div> -->
    <!-- <div class="bg-white p-4 rounded-lg shadow flex flex-col items-center text-center">
      <img src="images/kingsleypics.jpeg" alt="Board Member 3" class="w-32 h-32 object-cover rounded-full mb-4 border-4 border-gray-300" />
      <h3 class="text-xl font-semibold mb-2">Kingsley Johnson</h3>
      <p class="text-gray-600">Director, Investment, Intervention and Sustainability.</p>
      <p class="text-gray-600">Kingsley Johnson is a seasoned Project Manager and development practitioner with over eight years of progressive experience in the non-profit and community development sector. His work spans project design, implementation, monitoring, and institutional strengthening, with a strong focus on women's empowerment, household resilience, and sustainable social interventions.

        Currently serving as Project Manager at the Health Empowerment and Livelihood Promotion (H.E.L.P) Initiative, Kingsley oversees strategic and operational planning, resource management, risk mitigation, and performance reporting across multiple community-based programs. He has successfully coordinated multidisciplinary teams, strengthened partnerships with donors and community stakeholders, and ensured compliance with best practices in program delivery.

        His background in research, data collection, and documentation enables him to support evidence-based decision-making and continuous program improvement—skills that are critical to the success of innovative models such as FCCEI's revolving food loan initiative.

        Kingsley holds a Bachelor's degree in Botany and Ecological Studies from the University of Uyo, a Non-Profit Leadership and Management certificate from Lagos Business School, and professional training in project management and performance improvement.

        As a Director of the Foodchain Community Empowerment Initiative (FCCEI), Kingsley provides strategic leadership, governance oversight, and operational guidance, ensuring that donor and investor resources are protected, programs are effectively implemented, and food security outcomes for women-led households are achieved sustainably.</p>
    </div>
  </div> -->
  <!-- <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8"> -->
    <!-- Example Board Member -->
    <!-- <div class="bg-white p-4 rounded-lg shadow flex flex-col items-center text-center">
      <img src="images/Adenle.jpeg" alt="Board Member 1" class="w-32 h-32 object-cover rounded-full mb-4 border-4 border-gray-300" />
      <h3 class="text-xl font-semibold mb-2">Adenle Olatunbosun Damola</h3>
      <p class="text-gray-600">Non-Executive Director.</p>
      <p class="text-gray-600">Adenle Olatunbosun Damola is a seasoned finance and business strategy professional with over a decade of experience spanning financial services, health insurance, investment management, treasury operations, and business development.

        He brings to the board strong expertise in financial oversight, strategic growth, sales leadership, liquidity management, and client relationship management, having worked across regulated and high-growth environments. His career reflects a consistent ability to improve profitability, strengthen governance processes, and support sustainable business expansion.

        Olatunbosun has held senior roles including Senior Sales Executive at Reliance Health, Head of Sales and Business Development at NOLT Finance, and Business Strategy Manager at Enable Growth Africa, where he led strategic planning, identified operational gaps, and implemented growth frameworks aligned with organizational objectives. Earlier in his career, he worked in treasury, investment administration, and banking roles with Aquila Leasing, ARM Pension Managers, and Guaranty Trust Bank, building a solid foundation in financial controls, risk awareness, and regulatory discipline.

        As a Non-Executive Director, he provides independent judgment, strategic insight, and disciplined financial perspective, supporting management while maintaining robust oversight. He is particularly skilled at evaluating growth opportunities, stress-testing business models, and strengthening stakeholder confidence through sound governance.

        Olatunbosun holds a Bachelor of Science in Computer Science from Olabisi Onabanjo University and combines analytical rigor with commercial pragmatism in board deliberations.</p>

    </div> -->
    <!-- Repeat for other board members -->
    <!-- <div class="bg-white p-4 rounded-lg shadow flex flex-col items-center text-center">
      <img src="images/samuchepics.png" alt="Board Member 2" class="w-32 h-32 object-cover rounded-full mb-4 border-4 border-gray-300" />
      <h3 class="text-xl font-semibold mb-2">Uchendu Samuel</h3>
      <p class="text-gray-600">Director, Technology infrastructure and Corporate Technical support</p>
      <p class="text-gray-600">Uchendu Samuel serves as the Chief Technology Officer (CTO) of the Food Chain Community Empowerment Initiative (FCCEI), where he provides strategic leadership for the planning, development, and management of all technology and digital systems across the organization. In this role, he ensures that FCCEI's technology infrastructure remains secure, scalable, resilient, and fully aligned with the cooperative's long-term vision and operational objectives.
        With over a decade of hands-on experience in software development, systems architecture, and digital transformation, Uchendu delivers strong technical leadership across all digital platforms, including the organization's website, member management systems, payment solutions, and administrative dashboards. He oversees system architecture, cybersecurity frameworks, data governance, and third-party technology integrations, ensuring seamless operations, transparency, and reliability.
        Uchendu works closely with executive leadership and key stakeholders to define and execute FCCEI's digital roadmap, introduce innovative technology solutions, and continuously improve operational efficiency through automation and data-driven decision-making.</p>
        <p>He is deeply committed to leveraging modern technologies to enhance member experience, strengthen data integrity, and support sustainable community growth.
        Recognized for his strategic mindset, hands-on technical expertise, and strong problem-solving capabilities, Uchendu is dedicated to building robust, future-ready digital systems that empower FCCEI, enhance organizational performance, and deliver long-term value to its members.</p>

    </div>
    <div class="bg-white p-4 rounded-lg shadow flex flex-col items-center text-center">
      <img src="images/patrickpics.jpeg" alt="Board Member 3" class="w-32 h-32 object-cover rounded-full mb-4 border-4 border-gray-300" />
      <h3 class="text-xl font-semibold mb-2">Patrick Ubi</h3>
      <p class="text-gray-600">Expert in digital engagement and strengthening brand visibility.</p>
      <p class="text-gray-600">Patrick Ubi  is an experienced Social Media Manager responsible for managing  cooperative's online presence across all social media platforms. With over six years of expertise, he creates engaging content, manages community interactions, and drives digital engagement to strengthen communication and visibility within and beyond the community  while building and nurturing communities across platforms, ensuring every post serves a purpose.</p>
    </div>
  </div> -->
<!-- </section>  -->

<section id="team" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">{{ App\Models\PageContent::getContent('team', 'page_title', 'Our Team') }}</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">The passionate leaders driving food security and community empowerment.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 flex flex-col items-center text-center transform transition hover:shadow-xl">
                <div class="w-32 h-32 mb-6 rounded-full border-4 border-emerald-100 overflow-hidden shadow-md">
                    <img src="{{ asset('assets/landing/images/frankojipicsxe.png') }}" alt="Frank Orji" class="w-full h-full object-cover object-top" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Frank Ogochukwu Orji</h3>
                <p class="text-emerald-700 font-bold text-[10px] uppercase tracking-widest mb-4">CEO & Founder</p>
                <button onclick="openTeamModal('frank')" class="mt-auto bg-emerald-700 text-white py-2.5 px-8 rounded-full text-xs font-bold hover:bg-emerald-800 transition shadow-md">View Full Bio</button>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 flex flex-col items-center text-center transform transition hover:shadow-xl">
                <div class="w-32 h-32 mb-6 rounded-full border-4 border-emerald-100 overflow-hidden shadow-md">
                    <img src="{{ asset('assets/landing/images/basspixxs.png') }}" alt="Bassey Eno" class="w-full h-full object-cover object-top" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Bassey Eno</h3>
                <p class="text-emerald-700 font-bold text-[10px] uppercase tracking-widest mb-4">Chief Operating Officer</p>
                <button onclick="openTeamModal('bassey')" class="mt-auto bg-emerald-700 text-white py-2.5 px-8 rounded-full text-xs font-bold hover:bg-emerald-800 transition shadow-md">View Full Bio</button>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 flex flex-col items-center text-center transform transition hover:shadow-xl">
                <div class="w-32 h-32 mb-6 rounded-full border-4 border-emerald-100 overflow-hidden shadow-md">
                    <img src="{{ asset('assets/landing/images/kingsleypics.jpeg') }}" alt="Kingsley Johnson" class="w-full h-full object-cover object-top" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Kingsley Johnson</h3>
                <p class="text-emerald-700 font-bold text-[10px] uppercase tracking-widest mb-4">Director, Investment, Intervention and Sustainability</p>
                <button onclick="openTeamModal('kingsley')" class="mt-auto bg-emerald-700 text-white py-2.5 px-8 rounded-full text-xs font-bold hover:bg-emerald-800 transition shadow-md">View Full Bio</button>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 flex flex-col items-center text-center transform transition hover:shadow-xl">
                <div class="w-32 h-32 mb-6 rounded-full border-4 border-emerald-100 overflow-hidden shadow-md">
                    <img src="{{ asset('assets/landing/images/Adenle.jpeg') }}" alt="Adenle Damola" class="w-full h-full object-cover object-top" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Adenle Olatunbosun Damola</h3>
                <p class="text-emerald-700 font-bold text-[10px] uppercase tracking-widest mb-4">Non-Executive Director</p>
                <button onclick="openTeamModal('damola')" class="mt-auto bg-emerald-700 text-white py-2.5 px-8 rounded-full text-xs font-bold hover:bg-emerald-800 transition shadow-md">View Full Bio</button>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 flex flex-col items-center text-center transform transition hover:shadow-xl">
                <div class="w-32 h-32 mb-6 rounded-full border-4 border-emerald-100 overflow-hidden shadow-md">
                    <img src="{{ asset('assets/landing/images/sampics.png') }}" alt="Uchendu Samuel" class="w-full h-full object-cover object-top" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Uchendu Samuel</h3>
                <p class="text-emerald-700 font-bold text-[10px] uppercase tracking-widest mb-4">Director, Technology infrastructure and Corporate Technical support</p>
                <button onclick="openTeamModal('samuel')" class="mt-auto bg-emerald-700 text-white py-2.5 px-8 rounded-full text-xs font-bold hover:bg-emerald-800 transition shadow-md">View Full Bio</button>
            </div>

            <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100 flex flex-col items-center text-center transform transition hover:shadow-xl">
                <div class="w-32 h-32 mb-6 rounded-full border-4 border-emerald-100 overflow-hidden shadow-md">
                    <img src="{{ asset('assets/landing/images/patrickpics.jpeg') }}" alt="Patrick Ubi" class="w-full h-full object-cover object-top" />
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Patrick Ubi</h3>
                <p class="text-emerald-700 font-bold text-[10px] uppercase tracking-widest mb-4">Head, Digital Media and communication Strategy</p>
                <button onclick="openTeamModal('patrick')" class="mt-auto bg-emerald-700 text-white py-2.5 px-8 rounded-full text-xs font-bold hover:bg-emerald-800 transition shadow-md">View Full Bio</button>
            </div>
        </div>
    </div>
</section>

<!-- <div id="bioModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/70 backdrop-blur-md">
    <div class="bg-white w-full max-w-5xl rounded-[2rem] overflow-hidden shadow-2xl relative flex flex-col md:flex-row h-auto max-h-[90vh]">
        <button onclick="closeTeamModal()" class="absolute top-5 right-5 z-20 bg-white/90 p-2 rounded-full shadow-lg text-gray-500 hover:text-emerald-700 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
        <div class="w-full md:w-2/5 bg-gray-100 border-r border-gray-50">
            <img id="m-img" src="" alt="Profile" class="w-full h-full object-cover object-top min-h-[300px] md:min-h-full">
        </div>
        <div class="w-full md:w-3/5 p-8 md:p-14 overflow-y-auto bg-white">
            <h3 id="m-name" class="text-3xl font-bold text-gray-900 mb-2"></h3>
            <p id="m-role" class="text-emerald-700 font-bold text-xs uppercase tracking-widest mb-8 border-b border-gray-100 pb-4"></p>
            <div id="m-bio" class="text-gray-600 space-y-5 text-base leading-relaxed"></div>
        </div>
    </div>
</div> -->
<div id="bioModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/70 backdrop-blur-md">
    <div class="bg-white w-full max-w-5xl rounded-[2rem] overflow-hidden shadow-2xl relative flex flex-col md:flex-row h-auto max-h-[90vh]">

        <button onclick="closeTeamModal()" class="absolute top-5 right-5 z-20 bg-white/90 p-2 rounded-full shadow-lg text-gray-500 hover:text-emerald-700 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="w-full md:w-2/5 bg-gray-100 border-r border-gray-50 overflow-hidden">
            <img id="m-img" src="" alt="Profile"
                 class="w-full h-full object-cover object-top min-h-[400px] md:min-h-full transition-transform duration-500">
        </div>

        <div class="w-full md:w-3/5 p-8 md:p-14 bg-white flex flex-col">
            <div class="overflow-y-auto pr-2 custom-scrollbar">
                <h3 id="m-name" class="text-3xl font-bold text-gray-900 mb-2 font-display"></h3>
                <p id="m-role" class="text-emerald-700 font-extrabold text-[11px] uppercase tracking-[0.2em] mb-8 border-b border-gray-100 pb-4"></p>

                <div id="m-bio" class="text-gray-600 space-y-5 text-base leading-relaxed text-left">
                    </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Optional: Makes the scrollbar look cleaner inside the modal */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #047857;
        border-radius: 10px;
    }
</style>

<script>
   const teamData = {
    frank: {
        name: "Frank Ogochukwu Orji",
        role: "CEO & Founder",
        img: "{{ asset('assets/landing/images/frankojipicsxe.png') }}",
        bio: `<p>Frank Ogochukwu Orji is the visionary Founder and Chief Executive Officer of Food Chain Community Empowerment Initiative, a dynamic social enterprise committed to delivering innovative solutions that transform lives of women from Low-income households, Low-income earners, small business owners and students of tertiary institutions. </p>
              <p>With over 15 years of Business and leadership experience, Frank has built a reputation for strategic foresight, operational excellence, and an unwavering commitment to social impacts. Under his leadership, FCCEI has and will continue to achieve its strategic goals of Zero hunger, sustainability and food security for low-income households in Nigeria.</p>
              <p>A passionate advocate for sustainability, innovation and community impact, Frank fosters a culture of collaboration and continuous improvement, empowering teams to exceed expectations and deliver measurable results. Beyond corporate success, Frank actively contributes to industry associations and community initiatives, reinforcing a belief that business growth and social responsibility go hand in hand.</p>
              <p>Frank holds a Bachelor of Arts in Philosophy from the prestigious University of Nigeria, Nsukka. He is currently studying Business Leadership in the Executive Masters in Business Administration from the University of Nigeria Business School, Enugu Campus. He is also a member of Global Association of Risk Professionals; member, Advertising Regulatory Council of Nigeria; fellow, Institute of Health insurance and managed care of Nigeria. </p>
              <p>He has other certifications including Health economics from World Bank Academy, Washington DC, USA; Advance Diploma from Digital Marketing Institute and continues to engage in lifelong learning to stay ahead in an ever-evolving business landscape. His experience span across Healthcare, Agri-Business, Logistics, FMCG and Insurance.</p>`


    },
    bassey: {
        name: "Bassey Eno",
        role: "Chief Operating Officer",
        img: "{{ asset('assets/landing/images/basspixxs.png') }}",
        bio: `<p>Bassey Eno is the Chief Operating Officer (COO) of the Food Chain Community Empowerment Initiative (FCCEI), where he oversees the organization's day-to-day operations and ensures seamless coordination across all departments. He plays a pivotal role in translating FCCEI's strategic vision into effective operational execution while maintaining high standards of efficiency, accountability, and organizational cohesion.</p>
              <p>With over 10 years of experience in operations management and organizational strategy, Bassey brings a strong blend of leadership, business insight, and technical expertise to his role. He holds a Bachelor of Science degree from the National Open University of Nigeria, along with diplomas in IT Management and Software Systems and Executive Strategy and Management, and is a Cisco Certified Network Associate (CCNA – Networks). He is currently pursuing a Master of Business Administration (MBA) at the Atlanta College of Liberal Arts and Sciences, further strengthening his strategic, managerial, and organizational leadership capabilities.</p>
              <p>Throughout his career, Bassey has successfully designed and implemented systems and processes that enhance productivity, optimize resources, and foster collaboration across teams. His leadership approach is results-driven and anchored in continuous improvement, ensuring that organizational goals are met with precision and consistency.</p>
              <p>Bassey is deeply committed to sustainable growth and community development. He works closely with executive leadership to support long-term planning while safeguarding operational integrity and service excellence. Under his guidance, FCCEI has improved member engagement, strengthened service delivery, and expanded its reach and impact across both urban and rural communities.</p>
              <p>Known for his hands-on leadership style, strong problem-solving skills, and commitment to transparency, Bassey is focused on building efficient, inclusive systems that deliver lasting value and measurable impact to the communities FCCEI serves.</p>`
    },
    kingsley: {
        name: "Kingsley Johnson",
        role: "Director, Investment, Intervention and Sustainability",
        img: "{{ asset('assets/landing/images/kingsleypics.jpeg') }}",
        bio: `<p>Kingsley Johnson is a seasoned Project Manager and development practitioner with over eight years of progressive experience in the non-profit and community development sector. His work spans project design, implementation, monitoring, and institutional strengthening, with a strong focus on women's empowerment, household resilience, and sustainable social interventions.</p>
              <p>Currently serving as Project Manager at the Health Empowerment and Livelihood Promotion (H.E.L.P) Initiative, Kingsley oversees strategic and operational planning, resource management, risk mitigation, and performance reporting across multiple community-based programs. He has successfully coordinated multidisciplinary teams, strengthened partnerships with donors and community stakeholders, and ensured compliance with best practices in program delivery.</p>
              <p>His background in research, data collection, and documentation enables him to support evidence-based decision-making and continuous program improvement—skills that are critical to the success of innovative models such as FCCEI's revolving food loan initiative.</p>
              <p>Kingsley holds a Bachelor's degree in Botany and Ecological Studies from the University of Uyo, a Non-Profit Leadership and Management certificate from Lagos Business School, and professional training in project management and performance improvement.</p>
              <p>As a Director of the Foodchain Community Empowerment Initiative (FCCEI), Kingsley provides strategic leadership, governance oversight, and operational guidance, ensuring that donor and investor resources are protected, programs are effectively implemented, and food security outcomes for women-led households are achieved sustainably.</p>`

    },
    damola: {
        name: "Adenle Olatunbosun Damola",
        role: "Non-Executive Director",
        img: "{{ asset('assets/landing/images/Adenle.jpeg') }}",
        bio: `<p>Adenle Olatunbosun Damola is a seasoned finance and business strategy professional with over a decade of experience spanning financial services, health insurance, investment management, treasury operations, and business development.</p>
              <p>He brings to the board strong expertise in financial oversight, strategic growth, sales leadership, liquidity management, and client relationship management, having worked across regulated and high-growth environments. His career reflects a consistent ability to improve profitability, strengthen governance processes, and support sustainable business expansion.</p>
              <p>Olatunbosun has held senior roles including Senior Sales Executive at Reliance Health, Head of Sales and Business Development at NOLT Finance, and Business Strategy Manager at Enable Growth Africa, where he led strategic planning, identified operational gaps, and implemented growth frameworks aligned with organizational objectives. Earlier in his career, he worked in treasury, investment administration, and banking roles with Aquila Leasing, ARM Pension Managers, and Guaranty Trust Bank, building a solid foundation in financial controls, risk awareness, and regulatory discipline.</p>
              <p>As a Non-Executive Director, he provides independent judgment, strategic insight, and disciplined financial perspective, supporting management while maintaining robust oversight. He is particularly skilled at evaluating growth opportunities, stress-testing business models, and strengthening stakeholder confidence through sound governance.</p>
              <p>Olatunbosun holds a Bachelor of Science in Computer Science from Olabisi Onabanjo University and combines analytical rigor with commercial pragmatism in board deliberations.</p>`

    },
    samuel: {
        name: "Uchendu Samuel",
        role: "Director, Technology infrastructure and Corporate Technical support",
        img: "{{ asset('assets/landing/images/sampics.png') }}",
        bio: `<p>Uchendu Samuel serves as the Director, Technology infrastructure and Corporate Technical support of the Food Chain Community Empowerment Initiative (FCCEI), where he provides strategic leadership for the planning, development, and management of all technology and digital systems across the organization. In this capacity, he ensures that FCCEI's technology infrastructure remains secure, scalable, resilient, and fully aligned with the cooperative's long-term vision and operational objectives.</p>
              <p>With over a decade of hands-on experience in software development, systems architecture, and digital transformation, Uchendu brings a strong academic and professional foundation to his role. He holds a Bachelor of Science degree in Computer Science and is professionally trained as a Network Administrator, with advanced expertise as a Cisco Certified Network Associate (CCNA – Networks). He is also a trained SAP SD (Sales and Distribution) personnel, equipping him with deep insight into enterprise systems, process optimization, and data-driven operations.</p>
              <p>Uchendu delivers robust technical leadership across all digital platforms, including the organization's website, member management systems, payment and financial solutions, analytics tools, and administrative dashboards. He oversees system architecture, cybersecurity frameworks, data governance, and third-party technology integrations, ensuring seamless operations, transparency, and long-term reliability.</p>
              <p>Working closely with executive leadership and key stakeholders, Uchendu defines and executes FCCEI's digital roadmap, introduces innovative technology solutions, and drives continuous improvement through automation and intelligent system design. He is deeply committed to leveraging modern technologies to enhance member experience, strengthen data integrity, and support sustainable community growth.</p>
              <p>Recognized for his strategic mindset, hands-on technical expertise, and strong problem-solving capabilities, Uchendu is dedicated to building robust, future-ready digital systems that empower FCCEI, enhance organizational performance, and deliver lasting value to its members.</p>`

    },
    patrick: {
        name: "Patrick Ubi",
        role: "Head, Digital Media and communication Strategy",
        img: "{{ asset('assets/landing/images/patrickpics.jpeg') }}",
        bio: `<p>Patrick Ubi is an experienced Social Media Manager responsible for overseeing the cooperative's online presence across all social media platforms. With over six years of professional experience, he creates compelling, purpose-driven content, manages community interactions, and drives digital engagement to strengthen communication, visibility, and brand consistency within and beyond the community.</p>
              <p>Backed by an academic foundation in Computer Science (HND) and professional training in Business Administration, Patrick combines technical understanding with strategic communication skills. His expertise is further enhanced through Google Skillshop certifications, enabling him to apply data-driven insights, digital marketing best practices, and performance optimization strategies to every campaign.</p>
              <p>Passionate about community building, Patrick prioritizes meaningful engagement across platforms, ensuring that every post delivers value, drives interaction, and supports the cooperative's growth and visibility.</p>`

    }
};

function openTeamModal(key) {
    const data = teamData[key];
    document.getElementById('m-name').innerText = data.name;
    document.getElementById('m-role').innerText = data.role;
    document.getElementById('m-img').src = data.img;
    document.getElementById('m-bio').innerHTML = data.bio;

    const modal = document.getElementById('bioModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeTeamModal() {
    const modal = document.getElementById('bioModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}
</script>
</main>



<!-- Newsletter -->
<section class="py-16 bg-gradient-to-r from-emerald-600 to-teal-600">
  <div class="container mx-auto px-4 text-center">
    <h2 class="text-3xl font-bold text-white mb-4 font-display">Stay Updated</h2>
    <p class="text-white/90 mb-8 max-w-2xl mx-auto">Subscribe to our newsletter for the latest tips, success stories, and platform updates</p>
    <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
      <input type="email" placeholder="Your email address" class="flex-1 px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white" />
      <button type="submit" class="px-8 py-3 bg-white text-emerald-600 font-bold rounded-lg hover:bg-gray-100 transition-all duration-200">Subscribe</button>
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
              <span class="text-3xl font-extrabold leading-none" style="color: #059669;">FCCEI</span>
              <span class="text-[11px] font-bold uppercase tracking-widest" style="color: #047857;">Food Chain Community Empowerment Initiative</span>
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
          <li><a href="#home" class="hover:text-white transition">Home</a></li>
          <li><a href="#about" class="hover:text-white transition">About Us</a></li>
          <li><a href="#products" class="hover:text-white transition">Products</a></li>
          <li><a href="#blog" class="hover:text-white transition">Blog</a></li>
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
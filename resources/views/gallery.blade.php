@if(!App\Models\PageContent::isPageActive('gallery'))
    <!DOCTYPE html>
    <html lang="en">
    <head><title>Page Unavailable</title><style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;background:#f1f5f9;color:#475569;text-align:center;padding:20px;}h1{font-size:2rem;margin-bottom:1rem;}.card{background:white;padding:40px;border-radius:20px;box-shadow:0 10px 40px rgba(0,0,0,0.08);}</style></head>
    <body><div class="card"><h1>Page Currently Unavailable</h1><p>This page has been disabled by the administrator. Please check back later.</p></div></body>
    </html>
@else
<!DOCTYPE html>
<html lang="en">
<head>
    <base target="_self">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ App\Models\PageContent::getContent('gallery', 'page_title', 'Our Gallery') }}</title>
    <meta name="description" content="{{ App\Models\PageContent::getContent('gallery', 'page_subtitle', 'Moments from our community programs and events.') }}">
    <meta name="keywords" content="FCCEI, gallery, food chain, community, events">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: "#4F46E5",
                            light: "#6366F1",
                            dark: "#4338CA"
                        },
                        secondary: {
                            DEFAULT: "#10B981",
                            light: "#34D399",
                            dark: "#059669"
                        },
                        accent: {
                            DEFAULT: "#F59E0B",
                            light: "#FBBF24",
                            dark: "#D97706"
                        }
                    },
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                        'display': ['Poppins', 'system-ui', '-apple-system', 'sans-serif']
                    },
                    animation: {
                        'slide-in': 'slideIn 0.5s ease-out',
                        'fade-in': 'fadeIn 0.8s ease-out',
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
    </script> -->

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
                        /* Dark overlays added directly to config for better text contrast */
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

        .hero-slide {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.9) 0%, rgba(16, 185, 129, 0.9) 100%);
        }

        .stat-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
        }

        .gradient-text {
            background: linear-gradient(90deg, #4F46E5 0%, #10B981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: #4F46E5;
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        .product-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .team-card {
            transition: all 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-10px);
        }

        .blog-card {
            transition: all 0.3s ease;
        }

        .blog-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans">
    <!-- Navigation -->
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
                        <a href="{{ route('landing') }}#about" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
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

                    <a href="{{ route('landing') }}#products" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Membership</a>

                    <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="{{ route('landing') }}#blog" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        Blog
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                        </a>
                        <!-- Dropdown menu -->
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Gallery</a>
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
                    <a href="{{ route('landing') }}#contact" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Contact</a>
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
                <a href="{{ route('landing') }}" class="block py-2 text-gray-700 hover:text-primary font-medium">Home</a>
                <!-- <a href="#about" class="block py-2 text-gray-700 hover:text-primary font-medium">About</a> -->

                 <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="{{ route('landing') }}#about" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
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

                <a href="{{ route('landing') }}#products" class="block py-2 text-gray-700 hover:text-primary font-medium">Membership</a>

                <!-- <a href="#board-members" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200">Board Members</a> -->
                <!-- <a href="#team" class="block py-2 text-gray-700 hover:text-primary font-medium">Team</a> -->

                <!-- <a href="#blog" class="block py-2 text-gray-700 hover:text-primary font-medium">Blog</a> -->

                  <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="{{ route('landing') }}#blog" class="nav-link text-gray-700 hover:text-primary font-medium transition-colors duration-200 flex items-center">
                        Blog
                        <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                        </a>
                        <!-- Dropdown menu -->
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                        <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Gallery</a>
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


    <main class="container mx-auto px-4 py-12 flex-grow">
        <div class="flex justify-between items-end mb-12 border-b border-gray-100 pb-6">
            <div>
                <h2 class="text-4xl font-black text-gray-900">Available Stock</h2>
                <p class="text-gray-500 mt-2">Quality essential food items for our community members.</p>
            </div>
            <div class="hidden md:block">
                <span class="text-sm font-bold text-brandBlue bg-blue-50 px-6 py-2 rounded-full border border-blue-100">
                    500 Items in Stock
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            <div class="product-card bg-white rounded-3xl overflow-hidden">
                <img src="{{ asset('assets/landing/images/rices.png') }}" class="w-full h-48 object-cover" alt="Rice">
                <div class="p-6">
                    <h3 class="font-black text-gray-800 text-lg">Premium Local Rice</h3>
                    <p class="text-xs text-brandTeal font-bold uppercase mb-3">Grains • 50kg</p>
                    <div class="flex items-center justify-between">
                        <!-- <span class="text-brandBlue font-black text-xl">N55,000</span> -->
                        <!-- <button class="bg-brandTeal text-white p-2 rounded-lg hover:bg-brandBlue transition shadow-md">
                            <i class="fas fa-plus"></i>
                        </button> -->
                    </div>
                </div>
            </div>

            <div class="product-card bg-white rounded-3xl overflow-hidden">
                <img src="{{ asset('assets/landing/images/Beanscover.jpg') }}" class="w-full h-48 object-cover" alt="Beans">
                <div class="p-6">
                    <h3 class="font-black text-gray-800 text-lg">Honey Beans (Oloyin)</h3>
                    <p class="text-xs text-brandTeal font-bold uppercase mb-3">Grains • Paint</p>
                    <div class="flex items-center justify-between">
                        <!-- <span class="text-brandBlue font-black text-xl">N4,500</span>
                        <button class="bg-brandTeal text-white p-2 rounded-lg hover:bg-brandBlue transition shadow-md">
                            <i class="fas fa-plus"></i>
                        </button> -->
                    </div>
                </div>
            </div>

            <div class="product-card bg-white rounded-3xl overflow-hidden">
                <img src="{{ asset('assets/landing/images/vegetable.png') }}" class="w-full h-48 object-cover" alt="Oil">
                <div class="p-6">
                    <h3 class="font-black text-gray-800 text-lg">Vegetable Oil</h3>
                    <p class="text-xs text-brandTeal font-bold uppercase mb-3">Oils • 5 Liters</p>
                    <!-- <div class="flex items-center justify-between">
                        <span class="text-brandBlue font-black text-xl">N8,500</span>
                        <button class="bg-brandTeal text-white p-2 rounded-lg hover:bg-brandBlue transition shadow-md">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div> -->
                </div>
            </div>

            <div class="product-card bg-white rounded-3xl overflow-hidden">
                <img src="https://images.unsplash.com/photo-1506976785307-8732e854ad03?auto=format&fit=crop&w=500&q=80" class="w-full h-48 object-cover" alt="Eggs">
                <div class="p-6">
                    <h3 class="font-black text-gray-800 text-lg">Farm Fresh Eggs</h3>
                    <p class="text-xs text-brandTeal font-bold uppercase mb-3">Protein • Crate</p>
                    <!-- <div class="flex items-center justify-between">
                        <span class="text-brandBlue font-black text-xl">N4,200</span>
                        <button class="bg-brandTeal text-white p-2 rounded-lg hover:bg-brandBlue transition shadow-md">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div> -->
                </div>
            </div>

            <div class="product-card bg-white rounded-3xl overflow-hidden">
                <img src="{{ asset('assets/landing/images/semovita.png') }}" class="w-full h-48 object-cover" alt="Semo">
                <div class="p-6">
                    <h3 class="font-black text-gray-800 text-lg">Semovita</h3>
                    <p class="text-xs text-brandTeal font-bold uppercase mb-3">Flour • 10kg</p>
                    <!-- <div class="flex items-center justify-between">
                        <span class="text-brandBlue font-black text-xl">N12,500</span>
                        <button class="bg-brandTeal text-white p-2 rounded-lg hover:bg-brandBlue transition shadow-md">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div> -->
                </div>
            </div>

            <!-- <div class="product-card bg-white rounded-3xl overflow-hidden">
                <img src="images/palmoil.png" class="w-full h-48 object-cover" alt="Tomatoes">
                <div class="p-6">
                    <h3 class="font-black text-gray-800 text-lg">Fresh Palm Oil</h3>
                    <p class="text-xs text-brandTeal font-bold uppercase mb-3">Oils • Liters</p> -->
                    <!-- <div class="flex items-center justify-between">
                        <span class="text-brandBlue font-black text-xl">N2,500</span>
                        <button class="bg-brandTeal text-white p-2 rounded-lg hover:bg-brandBlue transition shadow-md">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div> -->
                <!-- </div>
            </div> -->

            <div class="product-card bg-white rounded-3xl overflow-hidden shadow-md">
                <img src="{{ asset('assets/landing/images/plam.png') }}" class="w-full h-48 object-cover" alt="Palm Oil" />
                <div class="p-6">
                    <h3 class="font-black text-gray-800 text-lg">Fresh Palm Oil</h3>
                    <p class="text-xs text-brandTeal font-bold uppercase mb-3">Oils • Liters</p>
                    <!-- Optional additional content -->
                </div>
            </div>

            <div class="product-card bg-white rounded-3xl overflow-hidden">
                <img src="{{ asset('assets/landing/images/spaghetti.png') }}" class="w-full h-48 object-cover" alt="Pasta">
                <div class="p-6">
                    <h3 class="font-black text-gray-800 text-lg">Spaghetti</h3>
                    <p class="text-xs text-brandTeal font-bold uppercase mb-3">Pasta • Carton</p>
                    <!-- <div class="flex items-center justify-between">
                        <span class="text-brandBlue font-black text-xl">N14,000</span>
                        <button class="bg-brandTeal text-white p-2 rounded-lg hover:bg-brandBlue transition shadow-md">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div> -->
                </div>
            </div>

            <div class="product-card bg-white rounded-3xl overflow-hidden">
                <img src="{{ asset('assets/landing/images/garrirbg.png') }}" class="w-full h-48 object-cover" alt="Garri">
                <div class="p-6">
                    <h3 class="font-black text-gray-800 text-lg">Ijebu Garri</h3>
                    <p class="text-xs text-brandTeal font-bold uppercase mb-3">Grains • Bag</p>
                    <!-- <div class="flex items-center justify-between">
                        <span class="text-brandBlue font-black text-xl">N22,000</span>
                        <button class="bg-brandTeal text-white p-2 rounded-lg hover:bg-brandBlue transition shadow-md">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div> -->
                </div>
            </div>

        </div>
    </main>

     <!-- Newsletter -->
        <section class="py-16 bg-gradient-to-r from-primary to-secondary">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold text-white mb-4 font-display">Stay Updated</h2>
                <p class="text-white/90 mb-8 max-w-2xl mx-auto">Subscribe to our newsletter for the latest tips, success stories, and platform updates</p>

                <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
                    <input type="email" placeholder="Your email address" class="flex-1 px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white">
                    <button type="submit" class="px-8 py-3 bg-white text-primary font-bold rounded-lg hover:bg-gray-100 transition-all duration-200">Subscribe</button>
                </form>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Company Info -->
                <!-- <div>
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-10 h-10 bg-primary rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">RP</span>
                        </div>
                        <span class="text-2xl font-bold text-white">Referral<span class="text-primary">Pro</span></span>
                    </div>
                    <p class="text-gray-400 mb-6">Empowering individuals to build sustainable income through smart referrals.</p>
                </div> -->
                <!-- <div>
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="w-20 h-20 flex items-center justify-center overflow-hidden">
                            <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full">
                        </div>

                        <div class="flex flex-col">
                            <span class="text-3xl font-extrabold leading-none" style="color: #26C1C9;">FCCEI</span>
                            <span class="text-[11px] font-bold text-white uppercase tracking-widest mt-1" style="color: #1D5FB5;">
                                Food Chain Community Empowerment Initiative
                            </span>
                        </div>
                    </div>

                    <p class="text-gray-400 mb-6">
                        Empowering communities to achieve food security and sustainable growth through collaborative local initiatives.
                    </p>
                </div> -->

                <div>
                <div class="flex items-center space-x-4">
                    <div class="w-32 h-32 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full">
                    </div>

                    <div class="flex flex-col">
                        <span class="text-3xl font-extrabold leading-none" style="color: #26C1C9;">FCCEI</span>
                        <span class="text-[11px] font-bold uppercase tracking-widest" style="color: #1D5FB5;">
                            Food Chain Community Empowerment Initiative
                        </span>
                    </div>
                </div>

                <p class="text-gray-400 mb-6 -mt-2">
                    Empowering communities to achieve food security and sustainable growth through collaborative local initiatives.
                </p>
                <div class="flex space-x-4">
                <a href="https://web.facebook.com/profile.php?id=61585986685899" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
                    <i class="fab fa-facebook-f text-brandTeal group-hover:text-white"></i>
                </a>
                <!-- <a href="#" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
                    <i class="fab fa-tiktok text-brandTeal group-hover:text-white"></i>
                </a> -->

                <a href="https://www.tiktok.com/@foodchainempowerm?lang=en" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-gray-700 hover:border-brandTeal group" style="background: rgba(255,255,255,0.05);">
                    <i class="fab fa-tiktok text-brandTeal group-hover:text-white"></i>
                </a>

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
                        <li><a href="#home" class="text-gray-400 hover:text-white transition-colors duration-200">Home</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors duration-200">About Us</a></li>
                        <li><a href="#products" class="text-gray-400 hover:text-white transition-colors duration-200">Products</a></li>
                        <li><a href="#blog" class="text-gray-400 hover:text-white transition-colors duration-200">Blog</a></li>
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h4 class="text-lg font-bold mb-6">Legal</h4>
                    <ul class="space-y-3">
                        <li><a href="/privacy" class="text-gray-400 hover:text-white transition-colors duration-200">Privacy Policy</a></li>
                        <li><a href="/terms" class="text-gray-400 hover:text-white transition-colors duration-200">Terms of Service</a></li>
                        <li><a href="/cookies" class="text-gray-400 hover:text-white transition-colors duration-200">Cookie Policy</a></li>
                        <li><a href="/compliance" class="text-gray-400 hover:text-white transition-colors duration-200">Compliance</a></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h4 class="text-lg font-bold mb-6">Resources</h4>
                    <ul class="space-y-3">
                        <li><a href="/help" class="text-gray-400 hover:text-white transition-colors duration-200">Help Center</a></li>
                        <li><a href="/faq" class="text-gray-400 hover:text-white transition-colors duration-200">FAQ</a></li>
                        <li><a href="/community" class="text-gray-400 hover:text-white transition-colors duration-200">Community</a></li>
                        <li><a href="/affiliate" class="text-gray-400 hover:text-white transition-colors duration-200">Affiliate Program</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; 2026 FCCEI. All rights reserved. | Designed By Bassiprog Digital Solution </p>
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

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ $post?->title ?? 'Blog Post' }}</title>
    @include('partials.seo-meta', [
        'seoTitle' => $post?->title ?? 'Blog Post',
        'seoDesc' => \Illuminate\Support\Str::limit($post?->excerpt ?? '', 158),
        'seoType' => 'article',
        'seoImage' => $post?->image_url ? url($post->image_url) : asset('assets/landing/images/about-uss.webp'),
        'seoAuthor' => $post?->author?->name ?? 'FCCEI',
        'seoPublished' => $post?->published_at?->toISOString(),
        'seoModified' => $post?->updated_at?->toISOString(),
        'seoSection' => $post?->category ?: 'Community',
        'schema' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $post?->title ?? 'Blog Post',
                'description' => \Illuminate\Support\Str::limit($post?->excerpt ?? '', 158),
                'image' => $post ? url($post->image_url) : asset('assets/landing/images/about-uss.webp'),
                'datePublished' => $post?->published_at?->toISOString(),
                'dateModified' => $post?->updated_at?->toISOString(),
                'author' => ['@type' => 'Person', 'name' => $post?->author?->name ?? 'FCCEI'],
                'publisher' => ['@type' => 'Organization', 'name' => config('app.name', 'FCCEI'), 'logo' => asset('assets/landing/images/FCCEILOGO.png')],
                'mainEntityOfPage' => url()->current(),
            ],
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Our Blog', 'item' => url('/blog')],
                    ['@type' => 'ListItem', 'position' => 3, 'name' => $post?->title ?? '', 'item' => url()->current()],
                ],
            ],
        ],
    ])
    <!-- Tailwind CSS CDN for styling -->
    @include('partials.vite-css')
    <!-- Font Awesome for icons (CDN stylesheet; the old kit.fontawesome.com script is dead and CORS-blocked) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800 font-sans">
    @include('partials.skip-link')

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
    <!-- <img src="images/about-uss.webp" alt="Blog Image" class="w-full h-96 object-cover rounded-lg mb-6" /> -->

    <!-- Blog Content -->
    <!-- <div class="prose max-w-none">
      <p>This is the full content of the blog post. Here, you can add paragraphs, images, and other media to showcase your article in detail.</p>
      <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
      Add more content as needed -->
    <!-- </div>
  </section> -->

    <section class="py-12 bg-gray-50" id="main-content">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="flex flex-col lg:flex-row gap-12">

                <div class="lg:w-2/3">
                    <article class="bg-white p-6 md:p-10 rounded-3xl shadow-sm border border-gray-100">
                        @if($post->image_url)
                        <img src="{{ $post->image_url }}" alt="{{ $post->image_alt ?: $post->title }}"
                            class="w-full h-[450px] object-cover rounded-2xl mb-8 shadow-md">
                        @endif

                        <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-100">
                            <div class="flex items-center gap-4">
                                @if($post->author)
                                <div class="w-12 h-12 rounded-full border-2 border-[#26C1C9] bg-[#26C1C9]/10 flex items-center justify-center text-[#26C1C9] font-black text-lg">
                                    {{ strtoupper(mb_substr($post->author->name, 0, 1)) }}
                                </div>
                                @else
                                <img src="{{ asset('assets/landing/images/uerss.jpg') }}" alt="FCCEI"
                                    class="w-12 h-12 rounded-full border-2 border-[#26C1C9] object-cover" loading="lazy" decoding="async">
                                @endif
                                <div>
                                    <p class="text-sm text-gray-400 uppercase tracking-tighter font-bold">Published By
                                    </p>
                                    <p class="text-lg font-bold text-gray-900 leading-none">
                                        {{ $post->author?->name ?? 'FCCEI Team' }}</p>
                                </div>
                            </div>
                            <div class="hidden sm:block text-right">
                                @if($post->published_at)
                                <time datetime="{{ $post->published_at->toDateString() }}"
                                    class="text-sm text-gray-400 font-medium">{{ $post->published_at->format('M j, Y') }}</time>
                                @endif
                                <div class="flex gap-2 mt-1">
                                    <a href="#" class="text-gray-400 hover:text-[#1D5FB5]"><i
                                            class="fab fa-facebook-f text-xs"></i></a>
                                    <a href="#" class="text-gray-400 hover:text-[#26C1C9]"><i
                                            class="fab fa-twitter text-xs"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                            @if($post->category)
                            <div class="flex items-center gap-3 mb-6">
                                <span
                                    class="px-3 py-1 bg-[#26C1C9]/10 text-[#26C1C9] text-xs font-bold rounded-full uppercase tracking-widest">{{ $post->category }}</span>
                                @if($post->published_at)
                                <span class="text-gray-400 text-sm">{{ $post->published_at->format('F j, Y') }}</span>
                                @endif
                            </div>
                            @endif

                            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-6 leading-tight">
                                {{ $post->title }}
                            </h1>

                            @if($post->excerpt)
                            <p class="text-lg text-gray-600 mb-6 font-medium">{{ $post->excerpt }}</p>
                            @endif

                            @if($post->body)
                            <div class="text-gray-700">
                                @foreach(preg_split('/\R\R+/', trim($post->body)) as $paragraph)
                                    @if(trim($paragraph) !== '')
                                    <p class="mb-6">{{ trim($paragraph) }}</p>
                                    @endif
                                @endforeach
                            </div>
                            @endif
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
                                @forelse($related ?? collect() as $item)
                                <a href="{{ route('blog_details', $item->slug) }}" class="group flex items-center gap-4">
                                    <div
                                        class="w-16 h-16 rounded-xl overflow-hidden flex-shrink-0 border border-gray-100">
                                        @if($item->image_url)
                                        <img src="{{ $item->image_url }}" alt="{{ $item->image_alt ?: $item->title }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition duration-300" loading="lazy" decoding="async">
                                        @endif
                                    </div>
                                    <div>
                                        <h4
                                            class="text-sm font-bold text-gray-800 group-hover:text-[#1D5FB5] transition line-clamp-2">
                                            {{ $item->title }}</h4>
                                        @if($item->published_at)
                                        <p class="text-[10px] text-gray-400 mt-1 uppercase font-bold tracking-wider">
                                            {{ $item->published_at->format('M d, Y') }}</p>
                                        @endif
                                    </div>
                                </a>
                                @empty
                                <p class="text-sm text-gray-400">No other posts yet.</p>
                                @endforelse
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
                                    <span class="text-white text-sm font-medium">Over <strong
                                            class="text-yellow-200">50,000 households</strong> shopping across our coverage locations.</span>
                                </li>
                                <li class="flex items-start gap-4">
                                    <div
                                        class="mt-1 w-5 h-5 bg-[#1D5FB5] rounded-md flex items-center justify-center shadow-inner">
                                        <i class="fas fa-check text-[8px] text-white"></i>
                                    </div>
                                    <span class="text-white text-sm font-medium">Shoppers save <strong
                                            class="text-yellow-200">35% on average</strong> with deals &amp; bulk buys.</span>
                                </li>
                                <li class="flex items-start gap-4">
                                    <div
                                        class="mt-1 w-5 h-5 bg-[#1D5FB5] rounded-md flex items-center justify-center shadow-inner">
                                        <i class="fas fa-check text-[8px] text-white"></i>
                                    </div>
                                    <span class="text-white text-sm font-medium">Logistics partners deliver to
                                        <strong class="text-yellow-200">your selected location</strong> for convenient pickup or delivery.</span>
                                </li>
                            </ul>

                            <a href="{{ route('marketplace.shop') }}"
                                class="block w-full mt-8 py-4 bg-white text-[#1D5FB5] font-black rounded-xl hover:bg-gray-100 transition-all uppercase text-xs tracking-widest shadow-lg text-center">
                                Shop the Marketplace
                            </a>
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
<form method="POST" action="{{ route('landing.newsletter') }}" class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
                  @csrf
                  <input type="email" name="email" aria-label="Email address" required placeholder="Your email address"
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

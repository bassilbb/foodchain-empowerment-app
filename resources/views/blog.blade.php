@if(!App\Models\PageContent::isPageActive('blog'))
    <!DOCTYPE html>
    <html lang="en">
    <head><title>Page Unavailable</title><style>body{font-family:sans-serif;display:flex;justify-content:center;align-items:center;min-height:100vh;background:#f1f5f9;color:#475569;text-align:center;padding:20px;}h1{font-size:2rem;margin-bottom:1rem;}.card{background:white;padding:40px;border-radius:20px;box-shadow:0 10px 40px rgba(0,0,0,0.08);}</style></head>
    <body><div class="card"><h1>Page Currently Unavailable</h1><p>This page has been disabled by the administrator. Please check back later.</p></div></body>
    </html>
@else
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ App\Models\PageContent::getContent('blog', 'page_title', 'Our Blog') }}</title>
    @include('partials.seo-meta', [
        'seoTitle' => App\Models\PageContent::getContent('blog', 'page_title', 'Our Blog'),
        'seoDesc' => App\Models\PageContent::getContent('blog', 'page_subtitle', 'Insights, tips, and success stories from the FoodChain marketplace community.'),
        'schema' => [
            [
                '@context' => 'https://schema.org',
                '@type' => 'Blog',
                'name' => App\Models\PageContent::getContent('blog', 'page_title', 'Our Blog'),
                'url' => url('/blog'),
                'description' => App\Models\PageContent::getContent('blog', 'page_subtitle', 'Insights from the FoodChain marketplace community.'),
            ],
        ],
    ])
    <link rel="icon" type="image/svg+xml" href="{{ site_favicon() ?? '/favicon.svg' }}">
    @include('partials.vite-css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="bg-gray-50 text-gray-800 font-sans">
    @include('partials.skip-link')

    <!-- Navigation -->
    <header class="sticky top-0 z-50 bg-white shadow-sm">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo Section -->
                <a href="{{ route('landing') }}" class="flex items-center space-x-4">
                    <div class="w-20 h-20 flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full" />
                    </div>
                    <div class="flex flex-col justify-center">
                        <span class="text-3xl font-extrabold leading-tight" style="color: #10B981;">FCCEI</span>
                        <span class="text-sm font-bold uppercase tracking-widest" style="color: #047857;">
                            Food Chain Community Empowerment Initiative
                        </span>
                    </div>
                </a>
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('landing') }}"
                        class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200">Home</a>
                    <!-- About with dropdown -->
                    <div class="relative group">
                        <a href="{{ route('landing') }}#about"
                            class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200 flex items-center">
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
                            <a href="{{ route('landing') }}#board-members" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Board
                                Members</a>
                            <a href="{{ route('team') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a>
                        </div>
                    </div>

                    <a href="{{ route('landing') }}#products"
                        class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200">Marketplace</a>
                    <!-- Blog dropdown -->
                    <div class="relative group">
                        <a href="{{ route('landing') }}#blog"
                            class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200 flex items-center">
                            Blog
                            <svg class="ml-1 w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 11.293l3.71-4.06a.75.75 0 111.08 1.04l-4.25 4.66a.75.75 0 01-1.08 0l-4.25-4.66a.75.75 0 01.02-1.06z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <div class="absolute hidden group-hover:block bg-white shadow-lg rounded mt-2 py-2 w-48 z-10">
                            <a href="{{ route('gallery') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Gallery</a>
                        </div>
                    </div>

                    <!-- Partner dropdown -->
                    <div class="relative group">
                        <a href="{{ route('sponsor') }}"
                            class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200 flex items-center">
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
                            <a href="{{ route('supplier') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become
                                supplier</a>
                            <a href="{{ route('rider') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our
                                Logistics Channel (Rider Only)</a>
                        </div>

                    </div>

                    <a href="{{ route('landing') }}#contact"
                        class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200">Contact</a>
                </div>
                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('login') }}"
                        class="px-6 py-2 text-[#059669] font-medium hover:bg-[#059669] hover:text-white border-2 border-[#059669] rounded-lg transition-all duration-200">Login</a>
                    <a href="{{ route('register') }}"
                        class="px-6 py-2 bg-[#059669] text-white font-medium hover:bg-[#047857] rounded-lg transition-all duration-200">Get
                        Started</a>
                </div>
                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            <!-- Mobile Navigation -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 space-y-4">
                <a href="{{ route('landing') }}" class="block py-2 text-gray-700 hover:text-[#059669] font-medium">Home</a>
                <!-- About with dropdown -->
                <div class="relative group">
                    <a href="{{ route('landing') }}#about"
                        class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200 flex items-center">
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
                        <a href="{{ route('team') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Team</a>
                    </div>
                </div>

                <a href="{{ route('landing') }}#products" class="block py-2 text-gray-700 hover:text-[#059669] font-medium">Marketplace</a>
                <!-- Blog dropdown -->
                <div class="relative group">
                    <a href="{{ route('landing') }}#blog"
                        class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200 flex items-center">
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
                <!-- Partner dropdown -->
                <div class="relative group">
                    <a href="#"
                        class="nav-link text-gray-700 hover:text-[#059669] font-medium transition-colors duration-200 flex items-center">
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
                        <a href="{{ route('supplier') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become
                            supplier</a>
                        <a href="{{ route('rider') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Become Our
                            Logistics Channel (Rider Only)</a>
                    </div>

                </div>

                <a href="{{ route('landing') }}#contact" class="block py-2 text-gray-700 hover:text-[#059669] font-medium">Contact</a>
                <div class="pt-4 space-y-2">
                    <a href="{{ route('login') }}"
                        class="block py-2 text-center text-[#059669] font-medium border-2 border-[#059669] rounded-lg">Login</a>
                    <a href="{{ route('register') }}"
                        class="block py-2 text-center bg-[#059669] text-white font-medium rounded-lg hover:bg-[#047857] transition-all duration-200">Get
                        Started</a>
                </div>
            </div>
        </nav>
    </header>
     {{-- @include('partials.header') --}}

    <main class="py-12 bg-gray-50" id="main-content">
        <div class="container mx-auto px-4">

            <div class="text-center mb-16">
                <h1 class="text-4xl font-bold text-gray-900 mb-4 font-display">{{ $pageTitle ?: 'Latest Blog Posts' }}</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    {{ $pageSubtitle ?: 'Insights, tips, and success stories from our marketplace community' }}
                </p>
            </div>

            @if(isset($categories) && $categories->isNotEmpty())
            <div class="flex flex-wrap justify-center gap-2 mb-12">
                <a href="{{ route('blog') }}"
                    class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border transition-all {{ ! $activeCategory ? 'bg-[#10B981] text-white border-[#10B981]' : 'border-gray-300 text-gray-600 hover:border-[#10B981] hover:text-[#047857]' }}">
                    All
                </a>
                @foreach($categories as $category)
                <a href="{{ route('blog', ['category' => $category]) }}"
                    class="px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest border transition-all {{ $activeCategory === $category ? 'bg-[#10B981] text-white border-[#10B981]' : 'border-gray-300 text-gray-600 hover:border-[#10B981] hover:text-[#047857]' }}">
                    {{ $category }}
                </a>
                @endforeach
            </div>
            @endif

            <div class="flex flex-col lg:flex-row gap-12">

                <div class="lg:w-3/4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                        @forelse($posts as $post)
                        <article
                            class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                            @if($post->image_url)
                                <img src="{{ $post->image_url }}" alt="{{ $post->image_alt ?: $post->title }}"
                                    class="w-full h-48 object-cover" loading="lazy" decoding="async" />
                            @else
                                <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-400 text-sm uppercase tracking-widest">
                                    No image
                                </div>
                            @endif
                            <div class="p-6">
                                @if($post->category)
                                <div class="text-sm text-[#10B981] font-bold mb-2 uppercase tracking-widest">
                                    {{ $post->category }}</div>
                                @endif
                                <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $post->title }}</h3>
                                @if($post->excerpt)
                                <p class="text-gray-600 mb-4">{{ $post->excerpt }}</p>
                                @endif
                                @if($post->published_at)
                                <p class="text-xs text-gray-400 mb-3">
                                    {{ $post->published_at->format('j F Y') }}
                                    @if($post->author) &middot; {{ $post->author->name }} @endif
                                </p>
                                @endif
                                <a href="{{ route('blog_details', $post->slug) }}"
                                    class="text-[#047857] font-bold hover:underline">Read More
                                    →</a>
                            </div>
                        </article>
                        @empty
                        <div class="col-span-full text-center py-16 text-gray-500">
                            <p class="font-semibold text-gray-700 mb-2">No blog posts published yet.</p>
                            <p class="text-sm">Published posts will appear here.</p>
                        </div>
                        @endforelse
                    </div>

                    @if($posts->hasPages())
                    <div class="flex justify-center mb-12">
                        {{ $posts->onEachSide(1)->links() }}
                    </div>
                    @endif
                </div>


                <aside class="lg:w-1/4 space-y-8">

                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-bolt text-[#10B981] mr-2"></i> Recent Updates
                        </h3>
                        <div class="space-y-6">
                            @forelse($posts->take(4) as $recent)
                            <a href="{{ route('blog_details', $recent->slug) }}" class="group flex items-center gap-3">
                                @if($recent->image_url)
                                <img src="{{ $recent->image_url }}" alt="{{ $recent->image_alt ?: $recent->title }}"
                                    class="w-12 h-12 rounded-lg object-cover" loading="lazy" decoding="async">
                                @else
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image text-xs"></i>
                                </div>
                                @endif
                                <div class="min-w-0">
                                    <h4
                                        class="text-xs font-bold text-gray-800 group-hover:text-[#047857] transition leading-tight">
                                        {{ $recent->title }}</h4>
                                    @if($recent->published_at)
                                    <p class="text-[10px] text-gray-400 mt-1 uppercase">
                                        {{ $recent->published_at->diffForHumans(short: true) }}</p>
                                    @endif
                                </div>
                            </a>
                            @empty
                            <p class="text-sm text-gray-400">No updates yet.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-[#10B981] p-6 rounded-2xl shadow-lg border-b-4 border-[#047857]">
                        <h3 class="text-white font-black text-sm uppercase tracking-widest mb-4 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i> Quick Facts
                        </h3>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-4 h-4 bg-[#047857] rounded flex-shrink-0 flex items-center justify-center mt-1">
                                    <i class="fas fa-check text-[8px] text-white"></i>
                                </div>
                                <span class="text-white text-xs leading-tight">Serving <strong
                                        class="text-yellow-200">your state</strong> with local pricing.</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <div
                                    class="w-4 h-4 bg-[#047857] rounded flex-shrink-0 flex items-center justify-center mt-1">
                                    <i class="fas fa-check text-[8px] text-white"></i>
                                </div>
                                <span class="text-white text-xs leading-tight">Over <strong
                                        class="text-yellow-200">50K+</strong> households shopping on the marketplace.</span>
                            </li>
                        </ul>
                        <a href="{{ route('marketplace.shop') }}"
                            class="block w-full mt-6 py-3 bg-[#047857] text-white text-xs font-bold rounded-lg hover:bg-opacity-90 transition shadow-md text-center">
                            SHOP THE MARKETPLACE
                        </a>
                    </div>
                </aside>

            </div>

            <div class="mt-12 text-center border-t pt-8">
                <a href="{{ route('landing') }}" class="text-gray-500 hover:text-[#10B981] font-medium transition">
                    <i class="fas fa-arrow-left mr-2 text-xs"></i> Back to Home
                </a>
            </div>

        </div>
    </main>

    <!-- Bottom Newsletter -->
    <section class="py-8" style="background: linear-gradient(to right, #059669, #10B981);">
        <div class="max-w-7xl mx-auto px-4 text-center text-white">
            <h2 class="text-3xl font-bold mb-4 font-display">Stay Updated</h2>
            <p class="mb-8 max-w-2xl mx-auto">Subscribe to our newsletter for the latest tips, success stories, and
                platform updates</p>
<form method="POST" action="{{ route('landing.newsletter') }}" class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
                  @csrf
                  <input type="email" name="email" aria-label="Email address" required placeholder="Your email address"
                      class="flex-1 px-6 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-white" />
                  <button type="submit"
                      class="px-8 py-3 bg-white text-[#059669] font-bold rounded-lg hover:bg-gray-100 transition-all duration-200">Subscribe</button>
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
@endif

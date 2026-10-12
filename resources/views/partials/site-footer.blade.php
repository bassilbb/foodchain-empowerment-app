{{-- Unified FCCEI/ FoodChain site footer — matches the marketplace store footer --}}
<footer class="bg-gradient-to-b from-emerald-950 to-emerald-900 text-emerald-200 mt-16 border-t border-emerald-800/60">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-4 mb-6">
                    <span class="w-16 h-16 rounded-2xl bg-white/95 ring-1 ring-emerald-700/40 flex items-center justify-center overflow-hidden shrink-0">
                        <img src="{{ asset('assets/landing/images/FCCEILOGO.png') }}" alt="FCCEI Logo" class="object-contain w-full h-full p-1">
                    </span>
                    <div class="flex flex-col">
                        <span class="text-2xl font-extrabold leading-none text-white">FCCEI</span>
                        <span class="text-[10px] font-bold uppercase tracking-widest text-emerald-300/80">Food Chain Community Empowerment Initiative</span>
                    </div>
                </div>
                <p class="text-emerald-300/80 mb-6 -mt-2 leading-relaxed">
                    Empowering communities to achieve food security and sustainable growth through collaborative local initiatives.
                </p>
                <div class="flex space-x-3">
                    <a href="https://web.facebook.com/profile.php?id=61585986685899" target="_blank" rel="noopener" aria-label="Facebook" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-emerald-700/60 hover:border-emerald-400 bg-white/5 hover:bg-white/10">
                        <i class="fab fa-facebook-f text-emerald-300"></i>
                    </a>
                    <a href="https://www.tiktok.com/@foodchainempowerm?lang=en" target="_blank" rel="noopener" aria-label="TikTok" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-emerald-700/60 hover:border-emerald-400 bg-white/5 hover:bg-white/10">
                        <i class="fab fa-tiktok text-emerald-300"></i>
                    </a>
                    <a href="https://www.instagram.com/foodchainempowerment/" target="_blank" rel="noopener" aria-label="Instagram" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-emerald-700/60 hover:border-emerald-400 bg-white/5 hover:bg-white/10">
                        <i class="fab fa-instagram text-emerald-300"></i>
                    </a>
                    <a href="https://wa.me/+2348168018123" target="_blank" rel="noopener" aria-label="WhatsApp" class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 border border-emerald-700/60 hover:border-emerald-400 bg-white/5 hover:bg-white/10">
                        <i class="fab fa-whatsapp text-emerald-300"></i>
                    </a>
                </div>
                <div class="mt-6 rounded-lg bg-white/5 px-4 py-3 text-xs text-emerald-300/80">
                    <p class="font-semibold text-emerald-200 mb-1">Office Address for Enquiry:</p>
                    <p class="leading-relaxed">No. 9B, Adejumo Tennis Club Road, opposite DE-RESTED PHARMACY, Ilamoshe Estate, Oke Afa, Ejigbo, Lagos.</p>
                    <p class="mt-2"><span class="font-semibold text-emerald-200">Central Warehouse 1:</span> 31, Ofada Street, Off Ladipo Rd, Mushin.</p>
                    <p class="mt-2"><span class="font-semibold text-emerald-200">Our Distribution Hubs in Lagos:</span> Oyingbo, Ebutte-ero, Igando, Ikorodu (1 &amp; 2), Ladega, Sango, Ejigbo, Ojuwoye &amp; Mushin Post Office.</p>
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h4 class="text-lg font-bold text-white mb-6">Quick Links</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('landing') }}" class="hover:text-white transition-colors">Home</a></li>
                    <li><a href="{{ route('marketplace.home') }}" class="hover:text-white transition-colors">Marketplace</a></li>
                    <li><a href="{{ route('gallery') }}" class="hover:text-white transition-colors">Gallery</a></li>
                    <li><a href="{{ route('team') }}" class="hover:text-white transition-colors">Our Team</a></li>
                    <li><a href="{{ route('blog') }}" class="hover:text-white transition-colors">Blog</a></li>
                </ul>
            </div>

            {{-- Company --}}
            <div>
                <h4 class="text-lg font-bold text-white mb-6">Company</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('landing') }}#about" class="hover:text-white transition-colors">About Us</a></li>
                    <li><a href="{{ route('csr') }}" class="hover:text-white transition-colors">CSR</a></li>
                    <li><a href="{{ route('franchise') }}" class="hover:text-white transition-colors">Franchise</a></li>
                    <li><a href="{{ route('sponsor') }}" class="hover:text-white transition-colors">Become a Sponsor</a></li>
                    <li><a href="{{ route('supplier') }}" class="hover:text-white transition-colors">Become a Supplier</a></li>
                    <li><a href="{{ route('rider') }}" class="hover:text-white transition-colors">Become a Rider</a></li>
                </ul>
            </div>

            {{-- Legal & Support --}}
            <div>
                <h4 class="text-lg font-bold text-white mb-6">Legal &amp; Support</h4>
                <ul class="space-y-3">
                    <li><a href="{{ route('pages.privacy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="{{ route('pages.terms') }}" class="hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="{{ route('pages.cookies') }}" class="hover:text-white transition-colors">Cookie Policy</a></li>
                    <li><a href="{{ route('pages.compliance') }}" class="hover:text-white transition-colors">Compliance</a></li>
                    <li><a href="{{ route('pages.help') }}" class="hover:text-white transition-colors">Help Center</a></li>
                    <li><a href="{{ route('pages.faq') }}" class="hover:text-white transition-colors">FAQ</a></li>
                    <li><a href="{{ route('pages.community') }}" class="hover:text-white transition-colors">Community</a></li>
                    <li><a href="{{ route('pages.affiliate') }}" class="hover:text-white transition-colors">Affiliate Program</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-emerald-800/60 mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-3 text-center sm:text-left text-sm text-emerald-300/70">
            <p>&copy; {{ date('Y') }} FCCEI. All rights reserved.</p>
            <p>Designed by Bassiprog Digital Solution</p>
        </div>
    </div>
</footer>

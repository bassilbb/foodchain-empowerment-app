<nav x-data="{ open: false }" style="background: linear-gradient(135deg, #065f46 0%, #064e3b 50%, #022c22 100%); border-bottom: 1px solid rgba(255,255,255,0.08); box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
    <div style="max-width: 1280px; margin: 0 auto; padding: 0 2rem;">
        <div style="display: flex; justify-content: space-between; height: 64px; align-items: center;">
            <div style="display: flex; align-items: center; gap: 2rem;">
                <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                        <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <span style="font-size: 1.1rem; font-weight: 700; color: #ffffff;">{{ config('app.name', 'Laravel') }}</span>
                </a>
                <div style="display: flex; align-items: center; gap: 0.25rem;">
                    <a href="{{ route('dashboard') }}" style="color: {{ request()->routeIs('dashboard') ? '#ffffff' : 'rgba(255,255,255,0.7)' }}; font-weight: 500; font-size: 0.875rem; text-decoration: none; padding: 0.5rem 1rem; border-radius: 8px; background: {{ request()->routeIs('dashboard') ? 'rgba(16,185,129,0.2)' : 'transparent' }}; transition: all 0.2s;">
                        Dashboard
                    </a>
                </div>
            </div>

            <div style="display: flex; align-items: center; gap: 1rem;">
                <div class="relative" x-data="{ dropOpen: false }" @click.outside="dropOpen = false">
                    <button @click="dropOpen = ! dropOpen" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; color: white; cursor: pointer; font-size: 0.875rem; font-weight: 500; transition: all 0.2s;">
                        <span>{{ Auth::user()->name }}</span>
                        <svg style="width: 16px; height: 16px; transition: transform 0.2s;" :style="{ transform: dropOpen ? 'rotate(180deg)' : '' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="dropOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 0.5rem; width: 200px; background: white; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; overflow: hidden; z-index: 100;" @click="dropOpen = false">
                        <a href="{{ route('profile.edit') }}" style="display: block; padding: 0.75rem 1rem; color: #475569; font-size: 0.875rem; text-decoration: none; transition: all 0.15s; border-bottom: 1px solid #f1f5f9;">
                            {{ __('Profile') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" style="display: block; padding: 0.75rem 1rem; color: #dc2626; font-size: 0.875rem; text-decoration: none; transition: all 0.15s;">
                                {{ __('Log Out') }}
                            </a>
                        </form>
                    </div>
                </div>

                <button @click="open = ! open" style="display: none; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.15); border-radius: 10px; cursor: pointer;">
                    <svg :class="{ 'hidden': open, 'inline-flex': ! open }" style="width: 20px; height: 20px; color: white; display: inline-flex;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg :class="{ 'hidden': ! open, 'inline-flex': open }" style="width: 20px; height: 20px; color: white; display: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': ! open }" style="display: none; border-top: 1px solid rgba(255,255,255,0.08); padding: 0.75rem; background: rgba(0,0,0,0.15);">
        <a href="{{ route('dashboard') }}" style="display: block; padding: 0.75rem 1rem; color: {{ request()->routeIs('dashboard') ? '#ffffff' : 'rgba(255,255,255,0.7)' }}; text-decoration: none; font-size: 0.875rem; font-weight: 500; border-radius: 8px; background: {{ request()->routeIs('dashboard') ? 'rgba(16,185,129,0.2)' : 'transparent' }};">
            {{ __('Dashboard') }}
        </a>
        <a href="{{ route('profile.edit') }}" style="display: block; padding: 0.75rem 1rem; color: rgba(255,255,255,0.7); text-decoration: none; font-size: 0.875rem; font-weight: 500; border-radius: 8px;">
            {{ __('Profile') }}
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" style="display: block; padding: 0.75rem 1rem; color: #fca5a5; text-decoration: none; font-size: 0.875rem; font-weight: 500; border-radius: 8px;">
                {{ __('Log Out') }}
            </a>
        </form>
    </div>
</nav>
<style>
    @media (max-width: 768px) {
        .mobile-nav-toggle { display: flex !important; }
    }
</style>

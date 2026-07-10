<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard') }}
    </x-slot>

    <div class="page-card">
        <div class="page-card-body" style="text-align: center; padding: 3rem 2rem;">
            <div style="width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <svg style="width: 40px; height: 40px; color: #059669;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 700; color: #1e293b; margin: 0 0 0.5rem;">{{ __("You're logged in!") }}</h2>
            <p style="color: #64748b; margin: 0 0 2rem; max-width: 400px; margin-left: auto; margin-right: auto;">Welcome to {{ config('app.name') }}. Manage your membership, thrift accounts, and more from your dashboard.</p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('login') }}" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    Go to Member Login
                </a>
                @if(module_enabled('referrals'))
                <a href="{{ route('register') }}" class="btn btn-secondary" style="display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1.25rem; border-radius: 12px; font-weight: 600; font-size: 0.875rem; text-decoration: none; cursor: pointer; transition: all 0.3s; background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%); color: #475569; border: 1px solid #e2e8f0; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Register Now
                </a>
                @endif
            </div>
        </div>
    </div>

    @if(module_enabled('referrals'))
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon emerald">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            <p class="stat-label">Community</p>
            <p class="stat-value" style="font-size: 1.25rem;">Referral Program</p>
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.5rem;">Invite friends and earn rewards</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon emerald">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </div>
            <p class="stat-label">Savings</p>
            <p class="stat-value" style="font-size: 1.25rem;">Thrift Accounts</p>
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.5rem;">Save consistently and earn</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon emerald">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            <p class="stat-label">Loans</p>
            <p class="stat-value" style="font-size: 1.25rem;">Food Loans</p>
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.5rem;">Access affordable credit</p>
        </div>
        <div class="stat-card">
            <div class="stat-icon emerald">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <p class="stat-label">Support</p>
            <p class="stat-value" style="font-size: 1.25rem;">24/7 Assistance</p>
            <p style="font-size: 0.8rem; color: #64748b; margin-top: 0.5rem;">We're here to help</p>
        </div>
    </div>
    @endif
</x-app-layout>

@extends('account.layouts.app')

@section('title', 'My Profile')
@section('header', 'My Profile')
@section('subheader', 'Manage your personal details and account security.')

@section('content')

@php
$initials = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->first_name ?? 'U', 0, 1));
@endphp

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- Summary column --}}
    <div class="space-y-5">
        <x-marketplace.card title="Account">
            <div class="flex flex-col items-center text-center">
                {{-- Avatar --}}
                <div class="relative">
                    @if ($user->avatar)
                    <img src="{{ url('serve-logo/'.$user->avatar) }}" alt="{{ $user->full_name }}"
                        class="h-24 w-24 rounded-2xl object-cover ring-4 ring-emerald-50"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                    <span class="hidden h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-3xl font-extrabold text-white">{{ $initials }}</span>
                    @else
                    <span class="flex h-24 w-24 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-3xl font-extrabold text-white">{{ $initials }}</span>
                    @endif
                </div>

                <h3 class="mt-3 text-base font-extrabold text-slate-800">{{ $user->full_name ?? $user->name }}</h3>
                <p class="text-xs text-slate-500">{{ $user->email }}</p>

                <div class="mt-2 flex flex-wrap items-center justify-center gap-2">
                    @if ($user->is_active)
                    <x-marketplace.status-badge status="active" />
                    @else
                    <x-marketplace.status-badge status="inactive" />
                    @endif
                    @if ($user->currentTier)
                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-800 ring-1 ring-inset ring-amber-200">{{ $user->currentTier->name }}</span>
                    @endif
                </div>

                {{-- Avatar upload --}}
                <form method="POST" action="{{ route('account.profile.update') }}" enctype="multipart/form-data" class="mt-4 w-full">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="first_name" value="{{ $user->first_name }}">
                    <input type="hidden" name="last_name" value="{{ $user->last_name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="phone" value="{{ $user->phone }}">
                    <input type="hidden" name="state" value="{{ $user->state }}">
                    <input type="hidden" name="city" value="{{ $user->city }}">
                    <input type="hidden" name="address" value="{{ $user->address }}">

                    <label for="avatar" class="fc-btn-ghost w-full cursor-pointer justify-center text-xs">
                        Change photo
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="sr-only" onchange="this.form.submit()">
                    </label>
                </form>

                @if ($user->avatar)
                <form method="POST" action="{{ route('account.profile.avatar.destroy') }}" class="mt-2 w-full"
                    data-confirm="Remove your profile photo?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full rounded-xl px-3 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-50">Remove photo</button>
                </form>
                @endif

                <dl class="mt-5 w-full space-y-2 border-t border-slate-100 pt-4 text-left text-xs">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Member since</dt>
                        <dd class="font-semibold text-slate-700">{{ $user->created_at->format('M Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Total orders</dt>
                        <dd class="font-semibold text-slate-700">{{ number_format($stats['orders']) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Lifetime spend</dt>
                        <dd class="font-semibold text-slate-700">{{ format_currency($stats['lifetime_spend']) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Saved addresses</dt>
                        <dd class="font-semibold text-slate-700">{{ $stats['addresses'] }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Wishlist items</dt>
                        <dd class="font-semibold text-slate-700">{{ $stats['wishlist'] }}</dd>
                    </div>
                </dl>
            </div>
        </x-marketplace.card>
    </div>

    {{-- Forms column --}}
    <div class="space-y-5 lg:col-span-2">

        @if ($errors->any())
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">
            <p class="mb-1 text-sm font-bold text-rose-800">Please correct the following:</p>
            <ul class="list-inside list-disc space-y-0.5 text-sm text-rose-700">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <x-marketplace.card title="Personal Information" subtitle="Keep your contact details up to date so deliveries reach you.">
            <form method="POST" action="{{ route('account.profile.update') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="first_name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">First Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label for="last_name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Last Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Email Address <span class="text-rose-500">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        <p class="mt-1 text-[11px] text-amber-600">This is your sign-in email — changing it changes how you log in.</p>
                    </div>

                    <div>
                        <label for="phone" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Phone Number <span class="text-rose-500">*</span></label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label for="state" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">State</label>
                        <select id="state" name="state"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                            <option value="">Select state...</option>
                            @foreach ($states as $state)
                            <option value="{{ $state }}" @selected(old('state', $user->state) === $state)>{{ $state }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="city" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="address" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Primary Address</label>
                        <input type="text" id="address" name="address" value="{{ old('address', $user->address) }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        <p class="mt-1 text-[11px] text-slate-400">
                            For multiple delivery locations, use the
                            <a href="{{ route('account.addresses.index') }}" class="font-semibold text-emerald-600 hover:underline">address book</a>.
                        </p>
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-100 pt-4">
                    <button type="submit" class="fc-btn-primary text-sm">Save changes</button>
                </div>
            </form>
        </x-marketplace.card>

        <x-marketplace.card title="Change Password" subtitle="Use at least 8 characters and keep it unique to FoodChain.">
            <form method="POST" action="{{ route('account.profile.password') }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label for="current_password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Current Password <span class="text-rose-500">*</span></label>
                        <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">New Password <span class="text-rose-500">*</span></label>
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Confirm Password <span class="text-rose-500">*</span></label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                </div>

                <div class="mt-5 border-t border-slate-100 pt-4">
                    <button type="submit" class="fc-btn-primary text-sm">Update password</button>
                </div>
            </form>
        </x-marketplace.card>
    </div>
</div>

@endsection
<section>
    <header>
        <h2 style="font-size: 1.125rem; font-weight: 600; color: #1e293b; margin: 0;">
            {{ __('Profile Information') }}
        </h2>
        <p style="margin-top: 0.25rem; font-size: 0.875rem; color: #64748b;">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" style="margin-top: 1.5rem;">
        @csrf
        @method('patch')

        <div class="form-group">
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div style="margin-top: 0.75rem;">
                    <p style="font-size: 0.875rem; color: #1e293b;">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" style="text-decoration: underline; font-size: 0.875rem; color: #10b981; background: none; border: none; cursor: pointer; padding: 0;">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p style="margin-top: 0.5rem; font-weight: 500; font-size: 0.875rem; color: #16a34a;">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div style="display: flex; align-items: center; gap: 1rem; margin-top: 1.5rem;">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" style="font-size: 0.875rem; color: #16a34a; margin: 0;">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>

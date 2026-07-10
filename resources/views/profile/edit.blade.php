<x-app-layout>
    <x-slot name="header">
        {{ __('Profile') }}
    </x-slot>

    <div style="max-width: 768px; margin: 0 auto;">
        <div class="page-card">
            <div class="page-card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-body">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="page-card" style="border: 1px solid #fecaca;">
            <div class="page-card-body">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>

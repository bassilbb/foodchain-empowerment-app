@extends('vendor.layouts.app')

@section('title', 'Store Settings')
@section('header', 'Store Settings')
@section('subheader', 'Manage how your store appears to buyers.')

@section('content')

@if ($errors->any())
<div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4">
    <p class="mb-1 text-sm font-bold text-rose-800">Please correct the following:</p>
    <ul class="list-inside list-disc space-y-0.5 text-sm text-rose-700">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@php
$v = fn (string $field, $fallback = null) => old($field, $vendor->{$field} ?? $fallback);
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    <div class="lg:col-span-2">
        <form method="POST" action="{{ route('vendor.settings.update') }}" enctype="multipart/form-data"
            class="space-y-5">
            @csrf
            @method('PUT')

            <x-marketplace.card title="Store Details">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Store name <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" required value="{{ $v('name') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div>
                        <label for="business_name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Business name <span class="text-rose-500">*</span></label>
                        <input type="text" id="business_name" name="business_name" required value="{{ $v('business_name') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div>
                        <label for="email" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Business email <span class="text-rose-500">*</span></label>
                        <input type="email" id="email" name="email" required value="{{ $v('email') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div>
                        <label for="phone" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Business phone <span class="text-rose-500">*</span></label>
                        <input type="text" id="phone" name="phone" required value="{{ $v('phone') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Description</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">{{ $v('description') }}</textarea>
                    </div>
                </div>
            </x-marketplace.card>

            <x-marketplace.card title="Location">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-1">
                        <label for="address" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Address</label>
                        <input type="text" id="address" name="address" value="{{ $v('address') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div class="sm:col-span-1">
                        <label for="city" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">City</label>
                        <input type="text" id="city" name="city" value="{{ $v('city') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </div>
                    <div class="sm:col-span-1">
                        <label for="state_id" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">State</label>
                        <select id="state_id" name="state_id"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                            <option value="">Select state</option>
                            @foreach ($states as $state)
                            <option value="{{ $state->id }}" @selected((string) $vendor->state === $state->name)>{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </x-marketplace.card>

            <x-marketplace.card title="Store Logo">
                <div class="flex items-center gap-4">
                    @if ($vendor->logo)
                    <img src="{{ $vendor->logo }}" alt="" class="h-16 w-16 rounded-xl object-cover ring-1 ring-slate-100">
                    @else
                    <span class="flex h-16 w-16 items-center justify-center rounded-xl bg-slate-50 text-sm font-bold text-slate-400">—</span>
                    @endif
                    <div class="flex-1">
                        <label for="logo" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Upload new logo</label>
                        <input type="file" id="logo" name="logo" accept="image/*"
                            class="block w-full text-sm text-slate-500 file:mr-3 file:rounded-lg file:border-0 file:bg-emerald-50 file:px-3 file:py-2 file:text-xs file:font-bold file:text-emerald-700 file:hover:bg-emerald-100">
                    </div>
                </div>
            </x-marketplace.card>

            <div class="flex justify-end">
                <button type="submit" class="fc-btn-primary text-sm">Save Settings</button>
            </div>
        </form>
    </div>

    <div class="space-y-5">
        <x-marketplace.card title="About your store">
            <a href="{{ route('marketplace.vendor', ['vendor' => $vendor->slug]) }}"
                class="mb-3 flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition hover:border-emerald-200 hover:bg-emerald-50/50">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800">View your storefront</p>
                    <p class="truncate text-xs text-slate-400">{{ $vendor->slug }}</p>
                </div>
            </a>
            <p class="text-xs leading-relaxed text-slate-500">
                Your public store page shows your logo, description and location to shoppers.
                Products are only visible once approved by our team.
            </p>
        </x-marketplace.card>

        <x-marketplace.card title="Payout details">
            <p class="text-xs leading-relaxed text-slate-500 mb-3">
                Add your bank details when requesting a payout from the Earnings page.
            </p>
            <a href="{{ route('vendor.payouts.index') }}" class="fc-btn-ghost w-full justify-center text-xs">Go to Earnings</a>
        </x-marketplace.card>
    </div>
</div>
@endsection
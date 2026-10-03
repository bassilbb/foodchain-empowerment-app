@extends('account.layouts.app')

@section('title', $address ? 'Edit Address' : 'Add New Address')
@section('header', $address ? 'Edit Delivery Address' : 'Add New Address')
@section('subheader', 'Saved addresses speed up checkout and keep delivery accurate.')

@section('content')

<div class="mx-auto max-w-3xl">

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

    <form method="POST"
        action="{{ $address ? route('account.addresses.update', $address) : route('account.addresses.store') }}"
        class="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
        @csrf
        @if ($address)
        @method('PUT')
        @endif

        @php
        $value = fn (string $field, $fallback = null) => old($field, $address->{$field} ?? $defaults[$field] ?? $fallback);
        @endphp

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

            {{-- Address label --}}
            <div class="sm:col-span-2">
                <label for="label" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Address Label</label>
                <div class="flex flex-wrap gap-2">
                    @foreach (['Home', 'Office', 'Shop', 'Family'] as $preset)
                    <label class="cursor-pointer">
                        <input type="radio" name="label" value="{{ $preset }}" class="peer sr-only"
                            @checked($value('label')===$preset)>
                        <span class="inline-block rounded-full bg-slate-100 px-3.5 py-1.5 text-xs font-semibold text-slate-600 transition peer-checked:bg-emerald-600 peer-checked:text-white">{{ $preset }}</span>
                    </label>
                    @endforeach
                    <input type="text" name="label" value="{{ $value('label') }}" placeholder="Other..."
                        class="w-32 rounded-full border border-slate-200 px-3.5 py-1.5 text-xs text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
            </div>

            {{-- Full name --}}
            <div>
                <label for="full_name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" id="full_name" name="full_name" value="{{ $value('full_name') }}" required
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Phone Number <span class="text-rose-500">*</span></label>
                <input type="text" id="phone" name="phone" value="{{ $value('phone') }}" required placeholder="08012345678"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>

            {{-- Street address --}}
            <div class="sm:col-span-2">
                <label for="address" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Street Address <span class="text-rose-500">*</span></label>
                <input type="text" id="address" name="address" value="{{ $value('address') }}" required
                    placeholder="House number, street name"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>

            {{-- State (drives delivery zone) --}}
            <div>
                <label for="state" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">State <span class="text-rose-500">*</span></label>
                <select id="state" name="state" required
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <option value="">Select state...</option>
                    @foreach ($states as $state)
                    <option value="{{ $state }}" @selected($value('state')===$state)>{{ $state }}</option>
                    @endforeach
                </select>
                <p class="mt-1 text-[11px] text-slate-400">Delivery fees are calculated from this state.</p>
            </div>

            {{-- LGA --}}
            <div>
                <label for="lga" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">LGA</label>
                <input type="text" id="lga" name="lga" value="{{ $value('lga') }}"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>

            {{-- City --}}
            <div>
                <label for="city" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">City / Town</label>
                <input type="text" id="city" name="city" value="{{ $value('city') }}"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>

            {{-- Landmark --}}
            <div>
                <label for="landmark" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Nearest Landmark</label>
                <input type="text" id="landmark" name="landmark" value="{{ $value('landmark') }}" placeholder="e.g. Beside Zenith Bank"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            </div>

            {{-- Delivery instructions --}}
            <div class="sm:col-span-2">
                <label for="delivery_instructions" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Delivery Instructions</label>
                <textarea id="delivery_instructions" name="delivery_instructions" rows="3"
                    placeholder="Any details that help the rider find you..."
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">{{ $value('delivery_instructions') }}</textarea>
            </div>

            {{-- Default --}}
            <div class="sm:col-span-2">
                <label class="flex cursor-pointer items-start gap-3 rounded-xl bg-slate-50 p-3">
                    <input type="checkbox" name="is_default" value="1" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                        @checked(old('is_default', $address->is_default ?? $isFirstAddress))>
                    <span>
                        <span class="block text-sm font-semibold text-slate-700">Use as my default delivery address</span>
                        <span class="block text-xs text-slate-500">
                            @if ($isFirstAddress)
                            Your first address is automatically set as default.
                            @else
                            This address will be preselected at checkout.
                            @endif
                        </span>
                    </span>
                </label>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-5">
            <button type="submit" class="fc-btn-primary text-sm">
                {{ $address ? 'Save changes' : 'Save address' }}
            </button>
            <a href="{{ route('account.addresses.index') }}" class="fc-btn-ghost text-sm">Cancel</a>
        </div>
    </form>
</div>

@endsection
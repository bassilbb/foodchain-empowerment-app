@extends('account.layouts.app')

@section('title', 'Become a Vendor')
@section('header', 'Become a Vendor')
@section('subheader', 'Open your own store on the FoodChain marketplace.')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
        <p class="text-sm font-bold text-emerald-900">Start selling on FoodChain</p>
        <p class="mt-1 text-xs leading-relaxed text-emerald-700">
            Submit your store details and our team will review your application.
            Once approved you can list products, manage stock and track payouts from your vendor dashboard.
        </p>
    </div>

    <div class="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
        @if ($errors->any())
        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 p-4">
            <ul class="list-inside list-disc space-y-1 text-xs text-rose-700">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('account.become-vendor.store') }}" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Store name</label>
                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                           placeholder="e.g. Fresh Farms NG">
                </div>
                <div>
                    <label for="business_name" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Business / legal name</label>
                    <input type="text" id="business_name" name="business_name" required value="{{ old('business_name') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                           placeholder="Registered business name">
                </div>
                <div>
                    <label for="email" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Business email</label>
                    <input type="email" id="email" name="email" required value="{{ old('email', $user->email) }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div>
                    <label for="phone" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Business phone</label>
                    <input type="text" id="phone" name="phone" required value="{{ old('phone', $user->phone ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                           placeholder="e.g. 0801 234 5678">
                </div>
            </div>

            <div>
                <label for="description" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Store description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                          placeholder="What do you sell?">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="sm:col-span-1">
                    <label for="address" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">Address</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div class="sm:col-span-1">
                    <label for="city" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">City</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}"
                           class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                </div>
                <div class="sm:col-span-1">
                    <label for="state_id" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">State</label>
                    <select id="state_id" name="state_id"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 focus:border-emerald-500 focus:bg-white focus:outline-none">
                        <option value="">Select state</option>
                        @foreach ($states as $state)
                        <option value="{{ $state->id }}" @selected((string) old('state_id') === (string) $state->id)>{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-4">
                <a href="{{ route('account.dashboard') }}" class="fc-btn-ghost text-sm">Cancel</a>
                <button type="submit" class="fc-btn-primary text-sm">Submit Application</button>
            </div>
        </form>
    </div>
</div>
@endsection
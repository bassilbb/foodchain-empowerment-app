@extends('account.layouts.app')

@section('title', 'Delivery Addresses')
@section('header', 'Delivery Addresses')
@section('subheader', 'Manage the locations we can deliver your orders to.')

@section('content')

<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <p class="text-sm text-slate-500">
        {{ $addresses->count() }} saved {{ \Illuminate\Support\Str::plural('address', $addresses->count()) }}
    </p>
    <a href="{{ route('account.addresses.create') }}" class="fc-btn-primary text-sm">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Add New Address
    </a>
</div>

@if ($addresses->isEmpty())
<div class="rounded-2xl border border-slate-100 bg-white shadow-sm">
    <x-marketplace.empty-state
        icon="address"
        title="No delivery addresses yet"
        message="Add an address so we know where to deliver your orders."
        :action-url="route('account.addresses.create')"
        action-label="Add your first address" />
</div>
@else
<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    @foreach ($addresses as $address)
    <div class="relative flex flex-col rounded-2xl border bg-white p-5 shadow-sm transition hover:shadow-md
                            {{ $address->is_default ? 'border-emerald-300 ring-1 ring-emerald-100' : 'border-slate-100' }}">

        <div class="mb-3 flex items-start justify-between gap-3">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="truncate text-sm font-bold text-slate-800">{{ $address->full_name }}</h3>
                    @if ($address->label)
                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $address->label }}</span>
                    @endif
                </div>
                <p class="mt-0.5 text-xs text-slate-500">{{ $address->phone }}</p>
            </div>

            @if ($address->is_default)
            <span class="shrink-0 inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                Default
            </span>
            @endif
        </div>

        <div class="flex-1 space-y-1 text-sm text-slate-600">
            <p>{{ $address->address }}</p>
            @if ($address->landmark)
            <p class="text-xs text-slate-500">Landmark: {{ $address->landmark }}</p>
            @endif
            <p class="text-xs text-slate-500">
                {{ collect([$address->city, $address->lga, $address->state])->filter()->implode(', ') }}
            </p>
            @if ($address->delivery_instructions)
            <p class="mt-2 rounded-lg bg-amber-50 p-2 text-xs text-amber-800 ring-1 ring-inset ring-amber-100">
                {{ $address->delivery_instructions }}
            </p>
            @endif
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3">
            <a href="{{ route('account.addresses.edit', $address) }}" class="fc-btn-ghost text-xs">Edit</a>

            @unless ($address->is_default)
            <form method="POST" action="{{ route('account.addresses.default', $address) }}">
                @csrf
                <button type="submit" class="fc-btn-ghost text-xs">Set as default</button>
            </form>
            @endunless

            <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" class="ml-auto"
                data-confirm="Remove this delivery address?">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg px-3 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-50">Delete</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

<p class="mt-6 rounded-2xl border border-slate-100 bg-white p-4 text-xs text-slate-500 shadow-sm">
    Product prices are shown for the state you are shopping in. Choosing a delivery state here does not change your storefront prices —
    switch your shopping state from the top of the storefront if you need to compare another state's pricing.
</p>

@endsection
{{--
    Section / content card wrapper with optional header actions.

    Usage:
        <x-marketplace.card title="Recent Orders" subtitle="Your last 5 orders">
            <x-slot:actions>
                <a href="..." class="fc-btn-ghost text-xs">View all</a>
            </x-slot:actions>

            ...body...
        </x-marketplace.card>

    Set :flush="true" when the body is a table that needs edge-to-edge layout.
--}}
@props([
'title' => null,
'subtitle' => null,
'flush' => false,
'padded' => true,
])

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm shadow-slate-900/5 transition duration-200 hover:shadow-md hover:shadow-slate-900/5']) }}>
    @if ($title || isset($actions))
    <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 bg-white px-5 py-4 sm:px-6">
        <div class="min-w-0">
            @if ($title)
            <h2 class="text-[0.95rem] font-bold tracking-[-0.01em] text-slate-800">{{ $title }}</h2>
            @endif
            @if ($subtitle)
            <p class="mt-0.5 text-xs text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
        <div class="flex shrink-0 items-center gap-2">{{ $actions }}</div>
        @endisset
    </header>
    @endif

    <div class="{{ $flush ? '' : ($padded ? 'p-5 sm:p-6' : '') }}">
        {{ $slot }}
    </div>
</section>
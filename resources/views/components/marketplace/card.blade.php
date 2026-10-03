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

<section class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white shadow-3d-card transition duration-300 hover:shadow-3d-hover">
    @if ($title || isset($actions))
    <header class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
        <div class="min-w-0">
            @if ($title)
            <h2 class="text-sm font-bold text-slate-800">{{ $title }}</h2>
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

    <div class="{{ $flush ? '' : ($padded ? 'p-5' : '') }}">
        {{ $slot }}
    </div>
</section>
{{--
    Vertical order-status timeline.

    Rendered from the array built by Account\OrderController::buildTimeline(),
    which is derived from marketplace_order_status_histories — only stages the
    order has genuinely passed through are marked complete.

    Usage: <x-marketplace.order-timeline :timeline="$timeline" />
--}}
@props(['timeline' => []])

@if (! empty($timeline))
<ol class="relative space-y-0">
    @foreach ($timeline as $index => $step)
    @php
    $isLast = $index === count($timeline) - 1;
    $done = $step['done'] ?? false;
    $terminal = $step['terminal'] ?? false;
    @endphp

    <li class="relative flex gap-4 pb-6 last:pb-0">
        {{-- Connector --}}
        @unless ($isLast)
        <span class="absolute left-[15px] top-8 h-full w-0.5 {{ $done ? 'bg-emerald-200' : 'bg-slate-100' }}"></span>
        @endunless

        {{-- Marker --}}
        <span class="relative z-10 mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full ring-4 ring-white
                    {{ $terminal
                        ? 'bg-rose-100 text-rose-600'
                        : ($done ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400') }}">
            @if ($terminal)
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            @elseif ($done)
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            @else
            <span class="h-2 w-2 rounded-full bg-slate-300"></span>
            @endif
        </span>

        {{-- Content --}}
        <div class="min-w-0 flex-1 pt-1">
            <p class="text-sm font-bold {{ $done ? 'text-slate-800' : 'text-slate-400' }}">{{ $step['label'] }}</p>

            @if (! empty($step['note']))
            <p class="mt-0.5 text-xs text-slate-500">{{ $step['note'] }}</p>
            @endif

            @if (! empty($step['at']))
            <p class="mt-0.5 text-[11px] text-slate-400">
                {{ \Illuminate\Support\Carbon::parse($step['at'])->format('d M Y, g:i A') }}
            </p>
            @elseif (! $done)
            <p class="mt-0.5 text-[11px] text-slate-300">Pending</p>
            @endif
        </div>
    </li>
    @endforeach
</ol>
@endif
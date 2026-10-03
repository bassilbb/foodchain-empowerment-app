{{--
    Pagination links styled for the dashboard shell.

    Usage: <x-marketplace.pagination :paginator="$orders" />
--}}
@props(['paginator'])

@if ($paginator->hasPages())
<nav class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4" aria-label="Pagination">
    <p class="text-xs text-slate-500">
        Showing <span class="font-bold text-slate-700">{{ $paginator->firstItem() }}</span>
        to <span class="font-bold text-slate-700">{{ $paginator->lastItem() }}</span>
        of <span class="font-bold text-slate-700">{{ $paginator->total() }}</span> results
    </p>

    <div class="flex items-center gap-1">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
        <span class="flex h-9 items-center rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-300">Previous</span>
        @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
            class="flex h-9 items-center rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700">Previous</a>
        @endif

        {{-- Page numbers (windowed) --}}
        @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $page => $url)
        @if ($page == $paginator->currentPage())
        <span class="flex h-9 min-w-9 items-center justify-center rounded-lg bg-gradient-to-r from-emerald-600 to-teal-600 px-3 text-xs font-bold text-white">{{ $page }}</span>
        @else
        <a href="{{ $url }}"
            class="flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700">{{ $page }}</a>
        @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
            class="flex h-9 items-center rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-600 transition hover:border-emerald-300 hover:text-emerald-700">Next</a>
        @else
        <span class="flex h-9 items-center rounded-lg border border-slate-200 px-3 text-xs font-semibold text-slate-300">Next</span>
        @endif
    </div>
</nav>
@endif
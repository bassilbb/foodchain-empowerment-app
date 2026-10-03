@extends('account.layouts.app')

@section('title', 'Notifications')
@section('header', 'Notifications')
@section('subheader', 'Order updates, payment confirmations and account alerts.')

@section('content')

@php
$unreadCount = $notifications->where('is_read', false)->count();
@endphp

{{-- Toolbar --}}
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('account.notifications.index', array_filter(['unread' => $unreadOnly ? null : '1'])) }}"
            class="inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-bold transition
                      {{ $unreadOnly ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white' : 'bg-white text-slate-600 ring-1 ring-inset ring-slate-200 hover:bg-slate-50' }}">
            Unread only
            @if ($unreadCount > 0)
            <span class="rounded-full {{ $unreadOnly ? 'bg-white/25' : 'bg-rose-100' }} px-1.5 py-0.5 text-[10px] {{ $unreadOnly ? 'text-white' : 'text-rose-600' }}">{{ $unreadCount }}</span>
            @endif
        </a>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        @if ($unreadNotifications > 0)
        <form method="POST" action="{{ route('account.notifications.mark-all-read') }}">
            @csrf
            <button type="submit" class="fc-btn-ghost text-xs">Mark all as read</button>
        </form>
        @endif

        @if ($totalCount > 0)
        <form method="POST" action="{{ route('account.notifications.clear-all') }}"
            data-confirm="Delete all notifications? This cannot be undone.">
            @csrf
            @method('DELETE')
            <button type="submit" class="rounded-xl px-3 py-2 text-xs font-bold text-rose-600 transition hover:bg-rose-50">Clear all</button>
        </form>
        @endif
    </div>
</div>

{{-- Category chips --}}
<div class="mb-5 flex flex-wrap gap-2">
    <a href="{{ route('account.notifications.index', array_filter(['unread' => $unreadOnly ? '1' : null])) }}"
        class="rounded-full px-3.5 py-1.5 text-xs font-bold transition
                  {{ ! $currentFilter ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white' : 'bg-white text-slate-600 ring-1 ring-inset ring-slate-200 hover:bg-slate-50' }}">
        All ({{ $totalCount }})
    </a>
    @foreach ($filters as $key => $filter)
    <a href="{{ route('account.notifications.index', array_filter(['type' => $key, 'unread' => $unreadOnly ? '1' : null])) }}"
        class="rounded-full px-3.5 py-1.5 text-xs font-bold transition
                      {{ $currentFilter === $key ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white' : 'bg-white text-slate-600 ring-1 ring-inset ring-slate-200 hover:bg-slate-50' }}">
        {{ $filter['label'] }} ({{ $filterCounts[$key] ?? 0 }})
    </a>
    @endforeach
</div>

<x-marketplace.card :flush="true">
    @if ($notifications->isEmpty())
    <x-marketplace.empty-state
        icon="bell"
        title="{{ $currentFilter || $unreadOnly ? 'No notifications match this filter' : 'No notifications' }}"
        :message="$currentFilter || $unreadOnly
                    ? 'Try a different category or view all notifications.'
                    : 'You are all caught up.'"
        :action-url="$currentFilter || $unreadOnly ? route('account.notifications.index') : null"
        :action-label="$currentFilter || $unreadOnly ? 'View all notifications' : null" />
    @else
    <ul class="divide-y divide-slate-50">
        @foreach ($notifications as $notification)
        <li class="group relative flex gap-4 px-5 py-4 transition hover:bg-slate-50/70 {{ $notification->is_read ? '' : 'bg-emerald-50/40' }}">

            {{-- Unread indicator --}}
            <span class="mt-1.5 flex h-2.5 w-2.5 shrink-0">
                @unless ($notification->is_read)
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></span>
                @else
                <span class="h-2.5 w-2.5 rounded-full bg-slate-200"></span>
                @endunless
            </span>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-start justify-between gap-2">
                    <p class="text-sm {{ $notification->is_read ? 'font-semibold text-slate-700' : 'font-bold text-slate-900' }}">
                        {{ $notification->title }}
                    </p>
                    <time class="shrink-0 text-[11px] text-slate-400" title="{{ $notification->created_at->format('d M Y, g:i A') }}">
                        {{ $notification->created_at->diffForHumans() }}
                    </time>
                </div>

                <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $notification->message }}</p>

                <div class="mt-2 flex flex-wrap items-center gap-2">
                    <span class="rounded-md bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                        {{ str_replace(['_', '.'], ' ', $notification->type) }}
                    </span>

                    @if ($notification->action_url)
                    <form method="POST" action="{{ route('account.notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-emerald-600 hover:underline">View details</button>
                    </form>
                    @elseif (! $notification->is_read)
                    <form method="POST" action="{{ route('account.notifications.read', $notification) }}">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-emerald-600 hover:underline">Mark as read</button>
                    </form>
                    @endif

                    <form method="POST" action="{{ route('account.notifications.destroy', $notification) }}"
                        data-confirm="Delete this notification?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-bold text-rose-500 hover:underline">Delete</button>
                    </form>
                </div>
            </div>
        </li>
        @endforeach
    </ul>

    <x-marketplace.pagination :paginator="$notifications" />
    @endif
</x-marketplace.card>

@endsection
{{-- resources/views/user/support/index.blade.php --}}
@extends('layouts.user')
@section('title', 'Support Tickets')
@section('content')
<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Support Tickets</h1>
        <p class="mt-1 text-sm text-gray-500">
            View your open and resolved support requests
        </p>
    </div>
    <a href="{{ route('support.create') }}"
        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium shadow-sm">
        + New Ticket
    </a>
</div>

@if($tickets->isEmpty())
    <div class="bg-white shadow rounded-lg p-8 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        <h3 class="mt-4 text-lg font-medium text-gray-900">No support tickets yet</h3>
        <p class="mt-1 text-sm text-gray-500">Need help? Create a new ticket and we’ll get back to you soon.</p>
        <div class="mt-4">
            <a href="{{ route('support.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                Create Your First Ticket
            </a>
        </div>
    </div>
@else
    <div class="bg-white shadow overflow-hidden rounded-lg">
        <ul class="divide-y divide-gray-200">
            @foreach($tickets as $ticket)
                <li class="px-6 py-4 hover:bg-gray-50 transition">
                    <div class="flex items-center justify-between">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                @if($ticket->status === 'open')
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </div>
                                @elseif($ticket->status === 'in_progress')
                                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4">
                                <h4 class="text-sm font-medium text-gray-900 truncate max-w-md">
                                    {{ $ticket->subject }}
                                </h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    Created {{ $ticket->created_at->format('M d, Y') }}
                                    @if($ticket->last_activity_at)
                                        • Last update {{ $ticket->last_activity_at->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($ticket->status === 'open') bg-blue-100 text-blue-800
                                @elseif($ticket->status === 'in_progress') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="mt-2 ml-14">
                        <a href="{{ route('support.show', $ticket) }}"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            View Details →
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $tickets->links() }}
        </div>
    </div>
@endif
@endsection

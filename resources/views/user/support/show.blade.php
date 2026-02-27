{{-- resources/views/user/support/show.blade.php --}}
@extends('layouts.user')
@section('title', 'Ticket #' . $ticket->id)
@section('content')
<div class="mb-6">
    <a href="{{ route('support.index') }}" class="flex items-center text-sm text-gray-600 hover:text-gray-900">
        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Tickets
    </a>
</div>

<div class="bg-white shadow rounded-lg overflow-hidden">
    <!-- Ticket Header -->
    <div class="px-6 py-5 border-b border-gray-200">
        <div class="flex justify-between items-start">
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-medium
                    @if($ticket->status === 'open') bg-blue-100 text-blue-800
                    @elseif($ticket->status === 'in_progress') bg-yellow-100 text-yellow-800
                    @else bg-green-100 text-green-800
                    @endif">
                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                </span>
                <h1 class="text-xl font-bold text-gray-900 mt-2">{{ $ticket->subject }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Category: {{ ucfirst($ticket->category) }} • Created {{ $ticket->created_at->format('M d, Y \a\t g:i A') }}
                </p>
            </div>
            @if($ticket->status !== 'resolved')
                <form action="{{ route('support.close', $ticket) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this ticket?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="px-3 py-1.5 text-xs font-medium text-red-600 hover:text-red-800 border border-red-300 rounded-md hover:bg-red-50">
                        Close Ticket
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Messages Timeline -->
    <div class="p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Conversation</h2>

        <div class="space-y-6">
            <!-- Original Message -->
            <div class="flex">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-bold">
                        {{ substr($user->first_name, 0, 1) }}
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <div class="bg-gray-100 rounded-lg p-4">
                        <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $ticket->message }}</p>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">You • {{ $ticket->created_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
            </div>

            <!-- Replies -->
            @foreach($ticket->replies as $reply)
                <div class="flex">
                    <div class="flex-shrink-0">
                        @if($reply->is_support)
                            <div class="h-8 w-8 rounded-full bg-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                S
                            </div>
                        @else
                            <div class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center text-white text-xs font-bold">
                                {{ substr(Auth::user()->first_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="bg-{{ $reply->is_support ? 'purple-50' : 'gray-100' }} rounded-lg p-4">
                            <p class="text-sm text-gray-800 whitespace-pre-wrap">{{ $reply->message }}</p>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            {{ $reply->is_support ? 'Support Team' : 'You' }} • {{ $reply->created_at->format('M d, Y \a\t g:i A') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Reply Form (if open) -->
        @if(in_array($ticket->status, ['open', 'in_progress']))
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Add a Reply</h3>
                <form action="{{ route('support.reply', $ticket) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <textarea name="message" rows="3"
                            class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="Type your message...">{{ old('message') }}</textarea>
                        @error('message')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                        Send Reply
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

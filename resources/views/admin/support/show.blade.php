{{-- resources/views/admin/support/show.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Support Ticket')
@section('page-title', 'Ticket: ' . $ticket->ticket_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Ticket Details & Replies -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Ticket Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-bold mb-2">{{ $ticket->subject }}</h3>
                    <div class="flex gap-2">
                        <span class="px-2 py-1 text-xs font-semibold rounded
                            {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $ticket->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $ticket->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst($ticket->priority) }} Priority
                        </span>
                        <span class="px-2 py-1 text-xs font-semibold rounded
                            {{ $ticket->status === 'open' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $ticket->status === 'in_progress' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $ticket->status === 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $ticket->status === 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>
                </div>
                <p class="text-sm text-gray-500">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="text-gray-700 whitespace-pre-wrap">{{ $ticket->message }}</p>
            </div>
        </div>

        <!-- Replies -->
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold text-lg mb-4">Conversation</h4>

            <div class="space-y-4 mb-6">
                @forelse($ticket->replies as $reply)
                <div class="flex {{ $reply->isFromAdmin() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-3xl {{ $reply->isFromAdmin() ? 'bg-blue-50' : 'bg-gray-50' }} p-4 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <p class="font-medium text-sm {{ $reply->isFromAdmin() ? 'text-blue-900' : 'text-gray-900' }}">
                                {{ $reply->isFromAdmin() ? $reply->sender->name . ' (Admin)' : $reply->sender->full_name }}
                            </p>
                            <p class="text-xs text-gray-500 ml-4">{{ $reply->created_at->format('M d, h:i A') }}</p>
                        </div>
                        <p class="text-gray-700 whitespace-pre-wrap">{{ $reply->message }}</p>
                    </div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No replies yet</p>
                @endforelse
            </div>

            <!-- Reply Form -->
            @if($ticket->status !== 'closed')
            <form method="POST" action="{{ route('admin.support.reply', $ticket) }}" class="border-t pt-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Add Reply</label>
                    <textarea name="message" rows="4" required placeholder="Type your response..."
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>
                <div class="mt-4">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Send Reply
                    </button>
                </div>
            </form>
            @else
            <div class="border-t pt-6 text-center text-gray-500">
                This ticket has been closed. Reopen it to continue the conversation.
            </div>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <!-- User Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold mb-4">User Information</h4>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500">Name</p>
                    <p class="font-medium">{{ $ticket->user->full_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium">{{ $ticket->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <p class="font-medium">{{ $ticket->user->phone }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tier</p>
                    <span class="px-2 py-1 text-xs font-semibold rounded {{ tier_badge_color($ticket->user->currentTier->slug) }} text-white">
                        {{ $ticket->user->currentTier->name }}
                    </span>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t">
                <a href="{{ route('admin.users.show', $ticket->user) }}" class="block text-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    View User Profile
                </a>
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h4 class="font-semibold mb-4">Actions</h4>
            <div class="space-y-3">
                @if(!$ticket->assignedTo)
                <form method="POST" action="{{ route('admin.support.assign', $ticket) }}">
                    @csrf
                    <input type="hidden" name="admin_id" value="{{ auth()->guard('admin')->id() }}">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Assign to Me
                    </button>
                </form>
                @endif

                @if($ticket->status === 'in_progress')
                <form method="POST" action="{{ route('admin.support.resolve', $ticket) }}">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Mark as Resolved
                    </button>
                </form>
                @endif

                @if($ticket->status !== 'closed')
                <form method="POST" action="{{ route('admin.support.close', $ticket) }}"
                      onsubmit="return confirm('Are you sure you want to close this ticket?');">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Close Ticket
                    </button>
                </form>
                @endif

                <a href="{{ route('admin.support.index') }}" class="block text-center px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Back to Tickets
                </a>
            </div>
        </div>

        <!-- Ticket Details -->
        <div class="bg-gray-50 rounded-lg shadow p-6">
            <h4 class="font-semibold mb-4">Ticket Details</h4>
            <div class="space-y-3 text-sm">
                <div>
                    <p class="text-gray-500">Ticket Number</p>
                    <p class="font-mono font-medium">{{ $ticket->ticket_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Created</p>
                    <p class="font-medium">{{ $ticket->created_at->format('M d, Y h:i A') }}</p>
                </div>
                @if($ticket->assignedTo)
                <div>
                    <p class="text-gray-500">Assigned To</p>
                    <p class="font-medium">{{ $ticket->assignedTo->name }}</p>
                </div>
                @endif
                @if($ticket->resolved_at)
                <div>
                    <p class="text-gray-500">Resolved At</p>
                    <p class="font-medium">{{ $ticket->resolved_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

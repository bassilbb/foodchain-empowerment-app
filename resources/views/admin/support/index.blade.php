{{-- resources/views/admin/support/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Support Tickets')
@section('page-title', 'Support Ticket Management')

@section('content')
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ticket #</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($tickets as $ticket)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $ticket->ticket_number }}</td>
                <td class="px-6 py-4">{{ Str::limit($ticket->subject, 40) }}</td>
                <td class="px-6 py-4">
                    <div class="text-sm">{{ $ticket->user->full_name }}</div>
                    <div class="text-xs text-gray-500">{{ $ticket->user->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded
                        {{ $ticket->priority === 'urgent' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $ticket->priority === 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                        {{ $ticket->priority === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $ticket->priority === 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ ucfirst($ticket->priority) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded bg-blue-100 text-blue-800">
                        {{ ucfirst($ticket->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $ticket->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.support.show', $ticket) }}" class="text-blue-600 hover:text-blue-900">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No support tickets found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $tickets->links() }}
</div>
@endsection

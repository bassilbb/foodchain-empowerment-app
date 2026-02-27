{{-- resources/views/user/support/create.blade.php --}}
@extends('layouts.user')
@section('title', 'New Support Ticket')
@section('content')
<div class="mb-6">
    <a href="{{ route('support.index') }}" class="flex items-center text-sm text-gray-600 hover:text-gray-900">
        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Back to Tickets
    </a>
</div>

<div class="bg-white shadow rounded-lg p-6">
    <h1 class="text-2xl font-bold text-gray-900 mb-2">Open a Support Ticket</h1>
    <p class="text-sm text-gray-500 mb-6">We’re here to help! Describe your issue below.</p>

    <form action="{{ route('support.store') }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select name="category" id="category"
                class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500">
                <option value="">Select a category</option>
                <option value="billing">Billing & Payments</option>
                <option value="referrals">Referral System</option>
                <option value="account">Account & Profile</option>
                <option value="technical">Technical Issue</option>
                <option value="other">Other</option>
            </select>
            @error('category')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
        </div>

        <div class="mb-4">
            <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
            <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500"
                placeholder="Brief summary of your issue">
            @error('subject')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
        </div>

        <div class="mb-6">
            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
            <textarea name="message" id="message" rows="5"
                class="block w-full border border-gray-300 rounded-md shadow-sm p-2 focus:ring-green-500 focus:border-green-500"
                placeholder="Please provide as much detail as possible...">{{ old('message') }}</textarea>
            @error('message')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('support.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit"
                class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                Submit Ticket
            </button>
        </div>
    </form>
</div>

<div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
    <h3 class="font-medium text-blue-800 mb-1">💡 Tips for a faster response:</h3>
    <ul class="text-sm text-blue-700 list-disc pl-5 space-y-1">
        <li>Include screenshots if applicable</li>
        <li>Mention your referral ID or transaction reference</li>
        <li>Describe steps to reproduce technical issues</li>
    </ul>
</div>
@endsection

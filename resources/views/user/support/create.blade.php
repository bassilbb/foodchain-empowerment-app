{{-- resources/views/user/support/create.blade.php --}}
@extends('layouts.user')
@section('title', 'New Support Ticket')
@section('header', 'Open a Support Ticket')
@section('subheader', "We're here to help! Describe your issue below.")

@section('content')
<div class="page-card">
    <div class="page-card-header">
        <div>
            <a href="{{ route('support.index') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.75rem;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 14px; height: 14px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Tickets
            </a>
            <h3 class="page-card-title" style="margin-top: 0.5rem;">Open a Support Ticket</h3>
            <p class="page-card-subtitle">We're here to help! Describe your issue below.</p>
        </div>
    </div>
    <div class="page-card-body">
        <form action="{{ route('support.store') }}" method="POST" enctype="multipart/form-data" style="max-width: 600px;">
            @csrf

            <div class="form-group">
                <label class="form-label">Category</label>
                    <select name="category" id="category" class="form-select">
                        <option value="">Select a category</option>
                        <option value="billing" {{ old('category') === 'billing' ? 'selected' : '' }}>Billing & Payments</option>
                        <option value="referrals" {{ old('category') === 'referrals' ? 'selected' : '' }}>Referral System</option>
                        <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>Account & Profile</option>
                        <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>Technical Issue</option>
                        <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('category')
                        <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                    @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" class="form-input" placeholder="Brief summary of your issue">
                @error('subject')
                    <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Message</label>
                <textarea name="message" id="message" rows="5" class="form-input" placeholder="Please provide as much detail as possible...">{{ old('message') }}</textarea>
                @error('message')
                    <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Attachment (optional)</label>
                <input type="file" name="attachment" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp" class="form-input">
                <p style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Upload a screenshot or image to help explain your issue. Max 2MB (jpeg, png, jpg, gif, webp).</p>
                @error('attachment')
                    <p style="color: #dc2626; font-size: 0.75rem; margin-top: 0.25rem;">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <a href="{{ route('support.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Submit Ticket</button>
            </div>
        </form>

        <div style="background: #dbeafe; border: 1px solid #bfdbfe; border-radius: 12px; padding: 1.25rem; margin-top: 1.5rem;">
            <h3 style="font-weight: 600; color: #1e40af; margin: 0 0 0.75rem 0; font-size: 0.9rem;">Tips for a faster response:</h3>
            <ul style="color: #1e3a8a; font-size: 0.875rem; padding-left: 1.25rem; margin: 0;">
                <li style="margin-bottom: 0.25rem;">Include screenshots if applicable</li>
                <li style="margin-bottom: 0.25rem;">Mention your referral ID or transaction reference</li>
                <li>Describe steps to reproduce technical issues</li>
            </ul>
        </div>
    </div>
</div>
@endsection
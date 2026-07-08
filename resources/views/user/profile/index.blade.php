{{-- resources/views/user/profile/index.blade.php --}}
@extends('layouts.user')

@section('title', 'My Profile & Settings')
@section('header', 'My Profile')
@section('subheader', 'Manage your personal information')

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<form action="{{ route('profile.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Personal Information</h3>
        </div>
        <div class="page-card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="form-input">
                    @error('first_name')<span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="form-input">
                    @error('last_name')<span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" value="{{ $user->email }}" class="form-input" disabled style="background: #f8fafc; color: #94a3b8;">
            </div>

            <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input">
                @error('phone')<span style="color: #dc2626; font-size: 0.75rem;">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form-input">
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">City</label>
                    <input type="text" name="city" value="{{ old('city', $user->city) }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">State</label>
                    <input type="text" name="state" value="{{ old('state', $user->state) }}" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Country</label>
                    <input type="text" name="country" value="{{ old('country', $user->country ?? 'Nigeria') }}" class="form-input">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                Save Changes
            </button>
        </div>
    </div>
</form>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Account Information</h3>
    </div>
    <div class="page-card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div>
                <p style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; margin-bottom: 0.25rem;">Referral Code</p>
                <p style="font-size: 1.25rem; font-weight: 700; color: #1e293b; font-family: monospace;">{{ $user->referral_code ?? 'N/A' }}</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; margin-bottom: 0.25rem;">Membership Tier</p>
                <span class="badge" style="background: linear-gradient(135deg, #f97316, #ea580c); color: white;">{{ $user->currentTier?->name ?? 'Bronze' }}</span>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; margin-bottom: 0.25rem;">Member Since</p>
                <p style="font-size: 1rem; font-weight: 600; color: #1e293b;">{{ $user->created_at?->format('M d, Y') ?? 'N/A' }}</p>
            </div>
            <div>
                <p style="font-size: 0.75rem; color: #64748b; text-transform: uppercase; margin-bottom: 0.25rem;">Direct Referrals</p>
                <p style="font-size: 1rem; font-weight: 600; color: #1e293b;">{{ number_format($user->direct_referrals_count ?? 0) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Change Password</h3>
    </div>
    <div class="page-card-body">
        <form action="{{ route('profile.password') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="max-width: 400px;">
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
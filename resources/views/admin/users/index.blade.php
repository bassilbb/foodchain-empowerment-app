@extends('admin.layouts.app')

@section('title', 'Users')
@section('header', 'User Management')
@section('subheader', 'Manage and view all platform users')

@section('content')
<div class="page-card">
    <div class="page-card-body">
        <form method="GET" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            <div class="form-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                <label class="form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..." class="form-input">
            </div>
            <div class="form-group" style="min-width: 150px; margin-bottom: 0;">
                <label class="form-label">Tier</label>
                <select name="tier" class="form-select">
                    <option value="">All Tiers</option>
                    @foreach($tiers as $tier)
                        <option value="{{ $tier->id }}" {{ request('tier') == $tier->id ? 'selected' : '' }}>
                            {{ $tier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="min-width: 150px; margin-bottom: 0;">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" class="btn btn-primary">
                    Filter
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="page-card">
    <div class="page-card-body" style="padding: 0;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email/Phone</th>
                    <th>Tier</th>
                    <th>Referrals</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 12px; background: linear-gradient(135deg, #10b981, #0ea5e9); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.875rem;">
                                {{ substr($user->first_name ?? 'U', 0, 1) }}{{ substr($user->last_name ?? '', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #1e293b;">{{ $user->full_name }}</div>
                                <div style="font-size: 0.75rem; color: #94a3b8; font-family: monospace;">{{ $user->referral_code }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size: 0.875rem; color: #1e293b;">{{ $user->email }}</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">{{ $user->phone ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <span class="badge {{ $user->currentTier?->slug ?? 'bronze' }}">
                            {{ $user->currentTier?->name ?? 'Bronze' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-info">{{ $user->direct_referrals_count }}</span>
                    </td>
                    <td>
                        @if($user->is_active)
                        <span class="badge badge-success">Active</span>
                        @else
                        <span class="badge badge-error">Inactive</span>
                        @endif
                    </td>
                    <td style="color: #64748b; font-size: 0.875rem;">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <a href="{{ route('admin.users.show', ['user' => $user->id]) }}" class="action-link">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            <p>No users found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($users->hasPages())
<div class="pagination">
    {{ $users->links() }}
</div>
@endif
@endsection

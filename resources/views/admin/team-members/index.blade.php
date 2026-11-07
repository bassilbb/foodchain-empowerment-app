@extends('admin.layouts.app')
@section('title', 'Team Members')
@section('page-title', 'Team Members')
@section('content')
<style>
    .tm-card{background:rgba(255,255,255,.85);border:1px solid rgba(255,255,255,.6);box-shadow:0 8px 32px rgba(0,0,0,.06);border-radius:20px;padding:18px}
    .tm-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(min(100%,320px),1fr));gap:18px}
    .tm-item{display:flex;gap:14px;align-items:flex-start;background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:14px}
    .tm-photo{width:72px;height:72px;border-radius:14px;object-fit:cover;object-position:top;background:#e2e8f0;flex:0 0 72px}
    .tm-name{font-size:15px;font-weight:700;color:#0f172a;margin:0}
    .tm-role{font-size:12px;color:#047857;font-weight:700;text-transform:uppercase;letter-spacing:.04em;margin:4px 0 8px}
    .tm-actions{display:flex;flex-wrap:wrap;gap:8px;margin-top:10px}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:10px;font-size:13px;font-weight:600;border:1px solid #cbd5e1;background:#f8fafc;color:#334155;cursor:pointer;text-decoration:none}
    .btn-primary{background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none}
    .btn-danger{background:#fef2f2;border-color:#fecaca;color:#dc2626}
    .badge{padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700;text-transform:uppercase}
    .badge-on{background:rgba(16,185,129,.12);color:#059669}.badge-off{background:rgba(239,68,68,.12);color:#dc2626}
    .alert{border-radius:14px;padding:14px 18px;font-weight:500;font-size:14px;margin-bottom:16px}
    .alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);color:#065f46}
</style>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:18px;">
    <div>
        <h2 style="font-size:20px;font-weight:700;color:#1e293b;margin:0;">Website Team Members</h2>
        <p style="color:#64748b;font-size:14px;margin:4px 0 0;">Add, edit, disable or remove members shown on the public <a href="{{ route('team') }}" target="_blank" rel="noopener" style="color:#059669;font-weight:600;">/team</a> page.</p>
    </div>
    <a href="{{ route('admin.team-members.create') }}" class="btn btn-primary">+ Add Team Member</a>
</div>
<div class="tm-card">
    @if($members->isEmpty())
        <div style="text-align:center;padding:2.5rem;color:#94a3b8;">No team members yet. Click “Add Team Member” to create one.</div>
    @else
        <div class="tm-grid">
            @foreach($members as $member)
                <div class="tm-item" style="{{ $member->is_active ? '' : 'opacity:.65;' }}">
                    @if($member->photo_url)
                        <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="tm-photo">
                    @else
                        <div class="tm-photo" style="display:flex;align-items:center;justify-content:center;color:#94a3b8;font-weight:700;">{{ strtoupper(substr($member->name,0,1)) }}</div>
                    @endif
                    <div style="min-width:0;flex:1;">
                        <p class="tm-name">{{ $member->name }}</p>
                        <p class="tm-role">{{ $member->role }}</p>
                        <span class="badge {{ $member->is_active ? 'badge-on' : 'badge-off' }}">{{ $member->is_active ? 'Active / Visible' : 'Disabled / Hidden' }}</span>
                        <div class="tm-actions">
                            <a href="{{ route('admin.team-members.edit', $member) }}" class="btn">Edit</a>
                            <form method="POST" action="{{ route('admin.team-members.toggle', $member) }}" style="display:inline;">
                                @csrf @method('PUT')
                                <button type="submit" class="btn">{{ $member->is_active ? 'Disable' : 'Enable' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.team-members.destroy', $member) }}" style="display:inline;" onsubmit="return confirm('Delete {{ addslashes($member->name) }}? This cannot be undone.');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
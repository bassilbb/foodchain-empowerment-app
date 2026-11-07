@extends('admin.layouts.app')
@php $editing = $member->exists; @endphp
@section('title', $editing ? 'Edit Team Member' : 'Add Team Member')
@section('page-title', $editing ? 'Edit Team Member' : 'Add Team Member')
@section('content')
<style>
    .tm-panel{max-width:860px;background:rgba(255,255,255,.85);border:1px solid rgba(255,255,255,.6);box-shadow:0 8px 32px rgba(0,0,0,.06);border-radius:20px;padding:22px}
    .row{margin-bottom:14px}.row label{display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px}
    .input,.textarea{width:100%;border:1px solid #cbd5e1;border-radius:10px;padding:10px 12px;font-size:14px;background:#fff;color:#0f172a;font-family:inherit}
    .textarea{resize:vertical;line-height:1.55;min-height:180px}
    .input:focus,.textarea:focus{outline:2px solid rgba(16,185,129,.35);border-color:#10b981}
    .hint{font-size:12px;color:#94a3b8;margin-top:5px}
    .preview{width:120px;height:120px;border-radius:16px;object-fit:cover;object-position:top;background:#e2e8f0;border:1px solid #e2e8f0}
    .btn{display:inline-flex;align-items:center;gap:6px;padding:10px 18px;border-radius:10px;font-size:14px;font-weight:600;border:1px solid #cbd5e1;background:#f8fafc;color:#334155;cursor:pointer;text-decoration:none}
    .btn-primary{background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none}
    .alert{border-radius:14px;padding:14px 18px;font-weight:500;font-size:14px;margin-bottom:16px}
    .alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.25);color:#065f46}
    .alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#991b1b}
    .check{display:flex;align-items:center;gap:8px;font-size:14px;color:#334155;font-weight:500}
    .check input{width:17px;height:17px;accent-color:#10b981}
    .grid2{display:grid;grid-template-columns:1fr 180px;gap:14px}
    @media(max-width:700px){.grid2{grid-template-columns:1fr}}
</style>
@if(session('success'))<div class="alert alert-success" style="max-width:860px;">{{ session('success') }}</div>@endif
@if($errors->any())
<div class="alert alert-error" style="max-width:860px;"><ul style="margin:0;padding-left:18px;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif
<div style="margin-bottom:14px;"><a href="{{ route('admin.team-members.index') }}" class="btn">&larr; Back to Team Members</a></div>
<div class="tm-panel">
    <form method="POST" action="{{ $editing ? route('admin.team-members.update', $member) : route('admin.team-members.store') }}" enctype="multipart/form-data">
        @csrf
        @if($editing) @method('PUT') @endif
        <div class="grid2">
            <div class="row">
                <label for="name">Full name <span style="color:#dc2626">*</span></label>
                <input type="text" id="name" name="name" class="input" value="{{ old('name', $member->name) }}" required maxlength="255">
            </div>
            <div class="row">
                <label for="sort_order">Display order</label>
                <input type="number" id="sort_order" name="sort_order" class="input" value="{{ old('sort_order', $member->sort_order ?? 0) }}" min="0" max="65535">
                <p class="hint">Lower numbers appear first.</p>
            </div>
        </div>
        <div class="row">
            <label for="role">Role / title <span style="color:#dc2626">*</span></label>
            <input type="text" id="role" name="role" class="input" value="{{ old('role', $member->role) }}" required maxlength="255">
        </div>
        <div class="row">
            <label>Photo</label>
            @if($member->photo_url)
                <img id="photoPreview" src="{{ $member->photo_url }}" alt="{{ $member->name }}" class="preview" style="display:block;margin-bottom:10px;">
            @else
                <img id="photoPreview" src="" alt="Preview" class="preview" style="display:none;margin-bottom:10px;">
            @endif
            <input type="file" name="photo" accept="image/jpeg,image/png,image/gif,image/webp,image/avif" class="input" onchange="if(this.files[0]){var p=document.getElementById('photoPreview');p.src=URL.createObjectURL(this.files[0]);p.style.display='block';}">
            <p class="hint">JPG, PNG, GIF, WEBP or AVIF up to 5 MB. Uploading a new photo replaces the current one.</p>
            @if($editing && $member->photo)
                <label class="check" style="margin-top:8px;"><input type="checkbox" name="remove_photo" value="1" @checked(old('remove_photo'))> Remove current photo</label>
            @endif
        </div>
        <div class="row">
            <label for="bio">Bio / profile</label>
            <textarea id="bio" name="bio" class="textarea">{{ old('bio', $member->bio) }}</textarea>
            <p class="hint">Shown in the “View Full Bio” modal on the public team page. You can use simple HTML (e.g. <code>&lt;p&gt;...&lt;/p&gt;</code>).</p>
        </div>
        <div class="row">
            <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $member->is_active ?? true))> Active — visible on the public team page</label>
            <p class="hint">Untick to disable/hide a member who is no longer working with the team (kept in admin, not shown publicly).</p>
        </div>
        <div style="display:flex;gap:10px;flex-wrap:wrap;padding-top:6px;">
            <button type="submit" class="btn btn-primary">{{ $editing ? 'Save Changes' : 'Create Team Member' }}</button>
            <a href="{{ route('admin.team-members.index') }}" class="btn">Cancel</a>
            @if($editing && $member->is_active)
                <a href="{{ route('team') }}" target="_blank" rel="noopener" class="btn">View public page &nearr;</a>
            @endif
        </div>
    </form>
</div>
@endsection
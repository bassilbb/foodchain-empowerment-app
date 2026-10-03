@extends('admin.layouts.app')
@section('title', 'Edit ' . ucfirst($page) . ' Page')
@section('page-title', 'Edit ' . ucfirst($page) . ' Page Content')
@section('content')
<style>
    .content-card {
        position: relative;
        background: rgba(255,255,255,0.85);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255,255,255,0.6);
        box-shadow:
            0 1px 3px rgba(0,0,0,0.04),
            0 8px 32px rgba(0,0,0,0.06),
            0 24px 64px rgba(0,0,0,0.04),
            inset 0 1px 0 rgba(255,255,255,0.8);
        transform: perspective(1200px) rotateX(0deg) translateZ(0);
        transition: all 0.45s cubic-bezier(0.23,1,0.32,1);
        border-radius: 20px;
        padding: 32px;
        overflow: visible;
    }
    .content-card::before {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: 21px;
        padding: 1.5px;
        background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(59,130,246,0.1), rgba(139,92,246,0.15), rgba(16,185,129,0.15));
        background-size: 300% 300%;
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        animation: borderGlow 6s ease-in-out infinite;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    .content-card:hover::before { opacity: 1; }
    .content-card:hover {
        transform: perspective(1200px) rotateX(1deg) translateZ(8px) translateY(-2px);
        box-shadow:
            0 1px 3px rgba(0,0,0,0.04),
            0 12px 48px rgba(0,0,0,0.08),
            0 32px 80px rgba(0,0,0,0.06),
            inset 0 1px 0 rgba(255,255,255,0.9);
        background: rgba(255,255,255,0.95);
    }
    @keyframes borderGlow {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .section-group {
        position: relative;
        background: rgba(248,250,252,0.7);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.5);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 16px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03), inset 0 1px 0 rgba(255,255,255,0.8);
        transition: all 0.3s cubic-bezier(0.23,1,0.32,1);
    }
    .section-group:hover {
        background: rgba(255,255,255,0.9);
        box-shadow: 0 4px 16px rgba(0,0,0,0.05), 0 8px 32px rgba(16,185,129,0.04), inset 0 1px 0 rgba(255,255,255,0.9);
        transform: translateX(4px);
    }

    .section-label {
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
        display: block;
    }

    .section-input, .section-textarea {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        background: rgba(255,255,255,0.8);
        transition: all 0.3s cubic-bezier(0.23,1,0.32,1);
        box-sizing: border-box;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.04);
    }
    .section-input:focus, .section-textarea:focus {
        outline: none;
        border-color: #10b981;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(16,185,129,0.12), inset 0 1px 3px rgba(0,0,0,0.04), 0 4px 16px rgba(16,185,129,0.08);
        transform: translateY(-1px);
    }
    .section-textarea { min-height: 120px; resize: vertical; }

    .section-name {
        font-size: 11px;
        font-weight: 700;
        color: #10b981;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 4px;
    }

    .preview-img {
        max-width: 200px;
        max-height: 200px;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        margin-top: 8px;
        object-fit: cover;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
    }
    .preview-img:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }

    .btn-save {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        font-weight: 600;
        padding: 14px 32px;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.23,1,0.32,1);
        box-shadow: 0 4px 15px rgba(16,185,129,0.35), 0 2px 4px rgba(16,185,129,0.2), inset 0 1px 0 rgba(255,255,255,0.2);
        transform: perspective(800px) translateZ(0);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-save::after {
        content: '';
        position: absolute;
        top: -50%; left: -50%;
        width: 200%; height: 200%;
        background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.15) 50%, transparent 70%);
        animation: shimmer 3s ease-in-out infinite;
        pointer-events: none;
    }
    @keyframes shimmer {
        0% { transform: translateX(-100%) rotate(45deg); }
        100% { transform: translateX(100%) rotate(45deg); }
    }
    .btn-save:hover {
        transform: perspective(800px) translateZ(8px) translateY(-2px);
        box-shadow: 0 8px 25px rgba(16,185,129,0.45), 0 4px 8px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.3);
    }
    .btn-save:active {
        transform: perspective(800px) translateZ(-2px) translateY(1px);
    }

    .btn-back {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #64748b;
        font-weight: 600;
        padding: 14px 28px;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.23,1,0.32,1);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transform: perspective(800px) translateZ(0);
    }
    .btn-back:hover {
        background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
        transform: perspective(800px) translateZ(6px) translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }

    .image-upload-btn {
        background: rgba(241,245,249,0.7);
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.23,1,0.32,1);
    }
    .image-upload-btn:hover {
        border-color: #10b981;
        background: rgba(16,185,129,0.05);
        transform: translateY(-1px);
    }

    .toggle-switch {
        position: relative;
        width: 48px;
        height: 26px;
        display: inline-block;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background: #cbd5e1;
        border-radius: 26px;
        transition: all 0.3s ease;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
    }
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
    }
    .toggle-switch input:checked + .toggle-slider {
        background: #10b981;
        box-shadow: 0 0 8px rgba(16,185,129,0.3), inset 0 1px 3px rgba(0,0,0,0.1);
    }
    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(22px);
    }
    .switch-label {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert-success {
        background: linear-gradient(135deg, rgba(16,185,129,0.1), rgba(5,150,105,0.08));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(16,185,129,0.25);
        border-radius: 14px;
        padding: 16px 20px;
        color: #065f46;
        font-weight: 500;
        font-size: 14px;
        box-shadow: 0 4px 16px rgba(16,185,129,0.1);
        transform: perspective(800px) translateZ(0);
        animation: slideDown 0.4s cubic-bezier(0.23,1,0.32,1);
    }
    @keyframes slideDown {
        from { opacity: 0; transform: perspective(800px) translateZ(-20px) translateY(-16px); }
        to { opacity: 1; transform: perspective(800px) translateZ(0) translateY(0); }
    }
</style>
<div style="max-width: 800px; margin: 0 auto;">
    @if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
    @endif
    <div class="content-card">
        <form method="POST" action="{{ route('admin.page-contents.update', $page) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div style="display: flex; flex-direction: column; gap: 16px;">
                @foreach($sections as $section)
                <div class="section-group">
                    <div class="section-name">{{ str_replace('_', ' ', $section->section) }}</div>
                    <label class="section-label">{{ $section->label ?? ucfirst(str_replace('_', ' ', $section->section)) }}</label>
                    @if($section->type === 'textarea')
                    <textarea name="sections[{{ $section->id }}]" class="section-textarea">{{ old("sections.{$section->id}", $section->value) }}</textarea>
                    @elseif($section->type === 'image')
                    <div>
                        @if($section->value)
                        <img src="{{ asset('storage/' . $section->value) }}" class="preview-img" id="preview-{{ $section->id }}">
                        @endif
                        <input type="file" name="sections[{{ $section->id }}]" accept="image/*" class="section-input" style="margin-top: 8px;" onchange="document.getElementById('preview-{{ $section->id }}').src = URL.createObjectURL(this.files[0])">
                    </div>
                    @elseif($section->type === 'boolean')
                    <label class="switch-label">
                        <span class="toggle-switch">
                            <input type="checkbox" name="sections[{{ $section->id }}]" value="1" {{ $section->value === 'true' ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </span>
                        <span style="color: #64748b; font-size: 14px;">{{ $section->value === 'true' ? 'Enabled' : 'Disabled' }}</span>
                    </label>
                    @else
                    <input type="text" name="sections[{{ $section->id }}]" value="{{ old("sections.{$section->id}", $section->value) }}" class="section-input">
                    @endif
                </div>
                @endforeach
                <div style="display: flex; gap: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                    <button type="submit" class="btn-save">Save Changes</button>
                    <a href="{{ route('admin.page-contents.index') }}" class="btn-back">Back to Pages</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

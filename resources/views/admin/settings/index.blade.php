@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'System Settings')
@section('header', 'Settings')
@section('subheader', 'Configure your FoodChain platform')

@section('content')
<style>
    .page-card {
        position: relative;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(20px) saturate(1.4);
        -webkit-backdrop-filter: blur(20px) saturate(1.4);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow:
            0 1px 3px rgba(0,0,0,0.04),
            0 8px 32px rgba(0,0,0,0.06),
            0 24px 64px rgba(0,0,0,0.04),
            inset 0 1px 0 rgba(255,255,255,0.8);
        transform: perspective(1200px) rotateX(0deg) translateZ(0);
        transition: all 0.45s cubic-bezier(0.23, 1, 0.32, 1);
        margin-bottom: 28px;
        overflow: visible;
    }
    .page-card::before {
        content: '';
        position: absolute;
        inset: -1px;
        border-radius: 20px;
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
    .page-card:hover::before { opacity: 1; }
    .page-card:hover {
        transform: perspective(1200px) rotateX(1.5deg) translateZ(12px) translateY(-3px);
        box-shadow:
            0 1px 3px rgba(0,0,0,0.04),
            0 12px 48px rgba(0,0,0,0.08),
            0 32px 80px rgba(0,0,0,0.06),
            inset 0 1px 0 rgba(255,255,255,0.9);
        background: rgba(255, 255, 255, 0.95);
    }
    @keyframes borderGlow {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .page-card:nth-child(2) { animation-delay: 0.05s; }
    .page-card:nth-child(3) { animation-delay: 0.1s; }
    .page-card:nth-child(4) { animation-delay: 0.15s; }
    .page-card:nth-child(5) { animation-delay: 0.2s; }

    .page-card-title {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 20px;
        text-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }
    .page-card-title > div {
        transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    }
    .page-card:hover .page-card-title > div {
        transform: scale(1.08) rotate(-3deg);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
    }

    .form-input, .form-select {
        background: rgba(248, 250, 252, 0.8);
        backdrop-filter: blur(4px);
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        color: #1e293b;
        transition: all 0.3s cubic-bezier(0.23, 1, 0.32, 1);
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.04);
        width: 100%;
        box-sizing: border-box;
    }
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #10b981;
        background: #ffffff;
        box-shadow:
            0 0 0 4px rgba(16,185,129,0.12),
            inset 0 1px 3px rgba(0,0,0,0.04),
            0 4px 16px rgba(16,185,129,0.08);
        transform: translateY(-1px);
    }
    .form-input:hover, .form-select:hover {
        border-color: #cbd5e1;
        background: rgba(255,255,255,0.95);
    }
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 8px;
        display: block;
        letter-spacing: 0.01em;
    }
    .form-group {
        position: relative;
        padding: 4px 0;
    }
    .form-group p {
        font-size: 12px;
        color: #94a3b8;
        margin-top: 6px;
    }

    .btn-primary {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: white;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.35s cubic-bezier(0.23, 1, 0.32, 1);
        box-shadow:
            0 4px 14px rgba(16,185,129,0.35),
            0 2px 4px rgba(16,185,129,0.2),
            inset 0 1px 0 rgba(255,255,255,0.2);
        transform: perspective(800px) translateZ(0);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-primary::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent 30%,
            rgba(255,255,255,0.15) 50%,
            transparent 70%
        );
        animation: shimmer 3s ease-in-out infinite;
        pointer-events: none;
    }
    @keyframes shimmer {
        0% { transform: translateX(-100%) rotate(45deg); }
        100% { transform: translateX(100%) rotate(45deg); }
    }
    .btn-primary:hover {
        transform: perspective(800px) translateZ(8px) translateY(-2px);
        box-shadow:
            0 8px 28px rgba(16,185,129,0.45),
            0 4px 8px rgba(16,185,129,0.25),
            inset 0 1px 0 rgba(255,255,255,0.3);
        background: linear-gradient(135deg, #059669, #047857);
    }
    .btn-primary:active {
        transform: perspective(800px) translateZ(-2px) translateY(1px);
        box-shadow:
            0 2px 8px rgba(16,185,129,0.3),
            inset 0 1px 2px rgba(0,0,0,0.1);
    }
    .btn-primary svg {
        transition: transform 0.3s ease;
    }
    .btn-primary:hover svg {
        transform: scale(1.15);
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
        animation: slideDown 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    }
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: perspective(800px) translateZ(-20px) translateY(-16px);
        }
        to {
            opacity: 1;
            transform: perspective(800px) translateZ(0) translateY(0);
        }
    }

    @media (max-width: 768px) {
        .page-card { padding: 20px; }
        .page-card:hover { transform: perspective(800px) rotateX(0.5deg) translateY(-2px); }
    }
</style>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="alert alert-error">{{ $errors->first() }}</div>
@endif

{{-- Logo Settings (Separate Form) --}}
<div class="page-card">
    <h3 class="page-card-title">
        <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
            <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        Logo & Branding
    </h3>
    <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">Upload your company logo to replace the default FoodChain logo across the platform.</p>
    
    <div style="display: flex; flex-wrap: wrap; gap: 24px; align-items: flex-start;">
        <div style="flex: 1; min-width: 250px;">
            <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Current Logo</p>
            @php
            $logoUrl = $logoSetting && $logoSetting->value ? asset('storage/' . $logoSetting->value) : null;
            @endphp
            @if($logoUrl)
            <div style="width: 120px; height: 120px; background: #ffffff; border-radius: 16px; display: flex; align-items: center; justify-content: center; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15), 0 8px 16px -4px rgba(0,0,0,0.1), inset 0 -2px 4px rgba(0,0,0,0.05); transform: perspective(500px) rotateX(5deg);">
                <img src="{{ $logoUrl }}" alt="Current Logo" style="width: 120px; height: 120px; object-fit: contain; border-radius: 16px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));">
            </div>
            @else
            <div style="color: #94a3b8; text-align: center;">
                <svg style="width: 64px; height: 64px; margin: 0 auto 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p style="font-size: 14px;">No logo uploaded</p>
            </div>
            @endif
        </div>
        
        <div style="flex: 1; min-width: 250px;">
            <form method="POST" action="{{ route('admin.settings.update-logo') }}" enctype="multipart/form-data" id="logoForm">
                @csrf
                <div style="border: 2px dashed #e2e8f0; border-radius: 16px; padding: 24px; text-align: center;">
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Upload New Logo</p>
                    <input type="file" name="logo" id="logoInput" accept="image/*" style="display: none;" onchange="previewLogo(event)">
                    <label for="logoInput" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #10b981, #059669); color: white; border-radius: 12px; font-weight: 600; cursor: pointer; margin-bottom: 12px;">
                        <svg style="width: 20px; height: 20px; vertical-align: middle; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Choose File
                    </label>
                    <p style="font-size: 12px; color: #94a3b8; margin: 0;">PNG, JPG, SVG (Max 2MB)</p>
                    <div id="logoPreview" style="margin-top: 16px; display: none;">
                        <div style="width: 120px; height: 120px; background: #ffffff; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15); transform: perspective(500px) rotateX(5deg);">
                            <img id="previewImage" src="" alt="Preview" style="width: 120px; height: 120px; object-fit: contain; border-radius: 16px; filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));">
                        </div>
                    </div>
                    <button type="submit" id="uploadBtn" style="display: none; margin-top: 12px; padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Upload Logo</button>
                </div>
            </form>
            @if($logoUrl)
            <form method="POST" action="{{ route('admin.settings.delete-logo') }}" style="margin-top: 16px; text-align: center;">
                @csrf
                <button type="submit" style="padding: 8px 16px; background: #fee2e2; color: #dc2626; border: none; border-radius: 8px; font-size: 13px; cursor: pointer;">Remove Logo</button>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- Favicon Settings --}}
<div class="page-card">
    <h3 class="page-card-title">
        <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #8b5cf6, #7c3aed); box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);">
            <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
        </div>
        Favicon
    </h3>
    <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">Upload a favicon for the browser tab icon. Recommended size: 32x32 or 16x16 (ICO, PNG, SVG).</p>

    <div style="display: flex; flex-wrap: wrap; gap: 24px; align-items: flex-start;">
        <div style="flex: 1; min-width: 250px;">
            <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Current Favicon</p>
            @php
            $faviconUrl = $faviconSetting && $faviconSetting->value ? asset('storage/' . $faviconSetting->value) : null;
            @endphp
            @if($faviconUrl)
            <div style="width: 64px; height: 64px; background: #ffffff; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 20px -5px rgba(0,0,0,0.1);">
                <img src="{{ $faviconUrl }}" alt="Current Favicon" style="width: 32px; height: 32px; object-fit: contain; border-radius: 4px;">
            </div>
            @else
            <div style="color: #94a3b8; text-align: center;">
                <svg style="width: 48px; height: 48px; margin: 0 auto 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                <p style="font-size: 14px;">No favicon uploaded</p>
            </div>
            @endif
        </div>

        <div style="flex: 1; min-width: 250px;">
            <form method="POST" action="{{ route('admin.settings.update-favicon') }}" enctype="multipart/form-data">
                @csrf
                <div style="border: 2px dashed #e2e8f0; border-radius: 16px; padding: 24px; text-align: center;">
                    <p style="font-size: 14px; color: #64748b; margin-bottom: 16px;">Upload New Favicon</p>
                    <input type="file" name="favicon" id="faviconInput" accept="image/*,.ico" style="display: none;" onchange="document.getElementById('faviconUploadBtn').style.display='inline-block'">
                    <label for="faviconInput" style="display: inline-block; padding: 12px 24px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; border-radius: 12px; font-weight: 600; cursor: pointer; margin-bottom: 12px;">
                        <svg style="width: 20px; height: 20px; vertical-align: middle; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Choose File
                    </label>
                    <p style="font-size: 12px; color: #94a3b8; margin: 0;">ICO, PNG, SVG (Max 5MB)</p>
                    <button type="submit" id="faviconUploadBtn" style="display: none; margin-top: 12px; padding: 10px 20px; background: #8b5cf6; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Upload Favicon</button>
                </div>
            </form>
            @if($faviconUrl)
            <form method="POST" action="{{ route('admin.settings.delete-favicon') }}" style="margin-top: 16px; text-align: center;">
                @csrf
                <button type="submit" style="padding: 8px 16px; background: #fee2e2; color: #dc2626; border: none; border-radius: 8px; font-size: 13px; cursor: pointer;">Remove Favicon</button>
            </form>
            @endif
        </div>
    </div>
</div>

<script>
function previewLogo(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImage').src = e.target.result;
            document.getElementById('logoPreview').style.display = 'block';
            document.getElementById('uploadBtn').style.display = 'inline-block';
        };
        reader.readAsDataURL(file);
    }
}
</script>

{{-- General Settings Form --}}
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    <div class="page-card">
        <h3 class="page-card-title">
            <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);">
                <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            General Settings
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            @forelse($settings['general'] ?? [] as $setting)
            <div class="form-group">
                <label class="form-label">{{ ucwords(str_replace('_', ' ', str_replace('general_', '', $setting->key))) }}</label>
                @if($setting->type === 'boolean')
                    <select name="settings[{{ $setting->key }}]" class="form-select">
                        <option value="true" {{ $setting->value == 'true' ? 'selected' : '' }}>Yes</option>
                        <option value="false" {{ $setting->value == 'false' ? 'selected' : '' }}>No</option>
                    </select>
                @else
                    <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-input">
                @endif
                @if($setting->description)<p style="font-size: 12px; color: #94a3b8; margin-top: 6px;">{{ $setting->description }}</p>@endif
            </div>
            @empty
            <p style="color: #64748b;">No general settings found.</p>
            @endforelse
        </div>
    </div>

    {{-- Payment Settings --}}
    <div id="payment-settings" class="page-card">
        <h3 class="page-card-title">
            <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #3b82f6, #2563eb); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);">
                <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            Payment Settings
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            @forelse($settings['payment'] ?? [] as $setting)
            <div class="form-group">
                <label class="form-label">{{ ucwords(str_replace('_', ' ', str_replace('payment_', '', $setting->key))) }}</label>
                @if($setting->type === 'boolean')
                    <select name="settings[{{ $setting->key }}]" class="form-select">
                        <option value="true" {{ $setting->value == 'true' ? 'selected' : '' }}>Yes</option>
                        <option value="false" {{ $setting->value == 'false' ? 'selected' : '' }}>No</option>
                    </select>
                @else
                    <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-input">
                @endif
                @if($setting->description)<p style="font-size: 12px; color: #94a3b8; margin-top: 6px;">{{ $setting->description }}</p>@endif
            </div>
            @empty
            <p style="color: #64748b;">No payment settings found.</p>
            @endforelse
        </div>
    </div>

    {{-- Distribution Settings --}}
    <div class="page-card">
        <h3 class="page-card-title">
            <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #8b5cf6, #7c3aed); box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);">
                <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
            </div>
            Distribution Settings
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            @forelse($settings['distribution'] ?? [] as $setting)
            <div class="form-group">
                <label class="form-label">{{ ucwords(str_replace('_', ' ', str_replace('distribution_', '', $setting->key))) }}</label>
                @if($setting->type === 'boolean')
                    <select name="settings[{{ $setting->key }}]" class="form-select">
                        <option value="true" {{ $setting->value == 'true' ? 'selected' : '' }}>Yes</option>
                        <option value="false" {{ $setting->value == 'false' ? 'selected' : '' }}>No</option>
                    </select>
                @else
                    <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-input">
                @endif
                @if($setting->description)<p style="font-size: 12px; color: #94a3b8; margin-top: 6px;">{{ $setting->description }}</p>@endif
            </div>
            @empty
            <p style="color: #64748b;">No distribution settings found.</p>
            @endforelse
        </div>
    </div>

    {{-- Notification Settings --}}
    <div class="page-card">
        <h3 class="page-card-title">
            <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);">
                <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            Notification Settings
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            @forelse($settings['notifications'] ?? [] as $setting)
            <div class="form-group">
                <label class="form-label">{{ ucwords(str_replace('_', ' ', str_replace('enable_', '', $setting->key))) }}</label>
                @if($setting->type === 'boolean')
                    <select name="settings[{{ $setting->key }}]" class="form-select">
                        <option value="true" {{ $setting->value == 'true' ? 'selected' : '' }}>Yes</option>
                        <option value="false" {{ $setting->value == 'false' ? 'selected' : '' }}>No</option>
                    </select>
                @else
                    <input type="text" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-input">
                @endif
                @if($setting->description)<p style="font-size: 12px; color: #94a3b8; margin-top: 6px;">{{ $setting->description }}</p>@endif
            </div>
            @empty
            <p style="color: #64748b;">No notification settings found.</p>
            @endforelse
        </div>
    </div>

    {{-- System Settings --}}
    <div class="page-card">
        <h3 class="page-card-title">
            <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #64748b, #475569); box-shadow: 0 4px 12px rgba(100, 116, 139, 0.3);">
                <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            System Settings
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            <div class="form-group">
                <label class="form-label">Currency Symbol</label>
                <input type="text" name="settings[currency_symbol]" value="{{ \App\Models\Setting::get('currency_symbol', '₦') }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Timezone</label>
                <select name="settings[timezone]" class="form-select">
                    <option value="Africa/Lagos">Africa/Lagos</option>
                    <option value="Africa/Port Harcourt">Africa/Port Harcourt</option>
                    <option value="UTC">UTC</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Date Format</label>
                <select name="settings[date_format]" class="form-select">
                    <option value="Y-m-d">YYYY-MM-DD</option>
                    <option value="d/m/Y">DD/MM/YYYY</option>
                    <option value="m/d/Y">MM/DD/YYYY</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Voucher Validity (hours)</label>
                <input type="number" name="settings[voucher_validity_hours]" value="{{ \App\Models\Setting::get('voucher_validity_hours', 72) }}" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Maintenance Mode</label>
                <select name="settings[maintenance_mode]" class="form-select">
                    <option value="false">No</option>
                    <option value="true">Yes</option>
                </select>
            </div>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end;">
        <button type="submit" class="btn-primary">
            <svg style="width: 18px; height: 18px; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Save Settings
        </button>
    </div>
</form>

{{-- Deduction Schedule Settings (Separate Form) --}}
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    <div class="page-card">
        <h3 class="page-card-title">
            <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f97316, #ea580c); box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);">
                <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            Deduction Schedule
        </h3>
        <p style="font-size: 14px; color: #64748b; margin-bottom: 20px;">Configure when weekly contribution deductions and retries run. Changes apply after the next server cron cycle.</p>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            <div class="form-group">
                <label class="form-label">Enable Scheduled Deductions</label>
                <select name="settings[deduction_schedule_enabled]" class="form-select">
                    <option value="true" {{ \App\Models\Setting::get('deduction_schedule_enabled', true) ? 'selected' : '' }}>Yes</option>
                    <option value="false" {{ !\App\Models\Setting::get('deduction_schedule_enabled', true) ? 'selected' : '' }}>No</option>
                </select>
                <p style="font-size: 12px; color: #94a3b8; margin-top: 6px;">When disabled, no automatic deductions will run. Manual deduction via command still works.</p>
            </div>
            <div class="form-group">
                <label class="form-label">Deduction Day</label>
                <select name="settings[deduction_day]" class="form-select">
                    @php $deductionDay = (int) \App\Models\Setting::get('deduction_day', 6); @endphp
                    @foreach([0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'] as $val => $label)
                    <option value="{{ $val }}" {{ $deductionDay === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deduction Time</label>
                <select name="settings[deduction_time]" class="form-select">
                    @php $deductionTime = \App\Models\Setting::get('deduction_time', '02:00'); @endphp
                    @for($h = 0; $h <= 23; $h++)
                    @php $t = sprintf('%02d:00', $h); @endphp
                    <option value="{{ $t }}" {{ $deductionTime === $t ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromFormat('H:i', $t)->format('g:i A') }}</option>
                    @endfor
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Retry Day</label>
                <select name="settings[deduction_retry_day]" class="form-select">
                    @php $retryDay = (int) \App\Models\Setting::get('deduction_retry_day', 0); @endphp
                    @foreach([0 => 'Sunday', 1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday'] as $val => $label)
                    <option value="{{ $val }}" {{ $retryDay === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <p style="font-size: 12px; color: #94a3b8; margin-top: 6px;">Retries deductions for accounts that lacked funds on the primary deduction day.</p>
            </div>
            <div class="form-group">
                <label class="form-label">Retry Time</label>
                <select name="settings[deduction_retry_time]" class="form-select">
                    @php $retryTime = \App\Models\Setting::get('deduction_retry_time', '02:00'); @endphp
                    @for($h = 0; $h <= 23; $h++)
                    @php $t = sprintf('%02d:00', $h); @endphp
                    <option value="{{ $t }}" {{ $retryTime === $t ? 'selected' : '' }}>{{ \Carbon\Carbon::createFromFormat('H:i', $t)->format('g:i A') }}</option>
                    @endfor
                </select>
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="submit" class="btn-primary">
                <svg style="width: 18px; height: 18px; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                Save Schedule
            </button>
        </div>
    </div>
</form>

{{-- Security Settings (Separate Form) --}}
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    <div class="page-card">
        <h3 class="page-card-title">
            <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #ef4444, #dc2626); box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);">
                <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            Security Settings
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
            <div class="form-group">
                <label class="form-label">Require Email Verification</label>
                <select name="settings[require_email_verification]" class="form-select">
                    <option value="true">Yes</option>
                    <option value="false">No</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Require KYC Verification</label>
                <select name="settings[require_kyc]" class="form-select">
                    <option value="true">Yes</option>
                    <option value="false">No</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Session Timeout (minutes)</label>
                <input type="number" name="settings[session_timeout]" value="60" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Max Login Attempts</label>
                <input type="number" name="settings[max_login_attempts]" value="5" class="form-input">
            </div>
        </div>
        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="submit" class="btn-primary">
                <svg style="width: 18px; height: 18px; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Save Security Settings
            </button>
        </div>
    </div>
</form>

{{-- Module Management --}}
<div class="page-card">
    <h3 class="page-card-title">
        <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #14b8a6, #0d9488); box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);">
            <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
        </div>
        Module Management
    </h3>
    <p style="font-size: 14px; color: #64748b; margin-bottom: 24px;">Enable or disable user-facing modules. Disabled modules will be hidden from users.</p>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @php
        $moduleConfigs = [
            'module_referrals' => ['name' => 'Referrals', 'desc' => 'Referral system, referral tree, and referral bonuses'],
            'module_distributions' => ['name' => 'Distributions', 'desc' => 'Food package distributions and delivery tracking'],
            'module_food_assistance' => ['name' => 'Food Assistance', 'desc' => 'Food assistance plans and installment payments'],
            'module_food_support' => ['name' => 'Food Support', 'desc' => 'Food support plans and weekly contributions'],
            'module_food_loans' => ['name' => 'Food Loans', 'desc' => 'Food loan applications and repayments'],
            'module_unions' => ['name' => 'Unions', 'desc' => 'Union/cooperative memberships and equity contributions'],
            'module_quarterly_benefits' => ['name' => 'Quarterly Benefits', 'desc' => 'Quarterly benefit claims and payouts'],
            'module_incidents' => ['name' => 'Incidents', 'desc' => 'Incident reporting and tracking'],
            'module_savings_accounts' => ['name' => 'Savings Accounts', 'desc' => 'Savings plan account creation and management'],
            'module_manual_payments' => ['name' => 'Manual Payments', 'desc' => 'Manual payment submission by users'],
        ];

        $moduleGroups = [
            'Referral System' => ['icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z', 'keys' => ['module_referrals']],
            'Food Programs' => ['icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'keys' => ['module_distributions', 'module_food_assistance', 'module_food_support', 'module_food_loans']],
            'Financial' => ['icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'keys' => ['module_unions', 'module_quarterly_benefits', 'module_savings_accounts', 'module_manual_payments']],
            'Support & Safety' => ['icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'keys' => ['module_incidents']],
        ];
        @endphp

        @foreach($moduleGroups as $groupName => $group)
        @php
        $groupId = Str::slug($groupName);
        @endphp
        <div style="margin-bottom: 16px; border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden; background: #f8fafc;">
            <div class="module-group-header" onclick="toggleModuleGroup('{{ $groupId }}')" style="display: flex; justify-content: space-between; align-items: center; padding: 16px 20px; background: #ffffff; border-bottom: 1px solid #e2e8f0; cursor: pointer; user-select: none;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg, #14b8a6, #0d9488); display: flex; align-items: center; justify-content: center;">
                        <svg style="width: 18px; height: 18px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $group['icon'] }}"></path></svg>
                    </div>
                    <span style="font-weight: 600; color: #1e293b; font-size: 14px;">{{ $groupName }}</span>
                    <span style="font-size: 11px; color: #94a3b8; background: #f1f5f9; padding: 2px 10px; border-radius: 10px;">{{ count($group['keys']) }} {{ Str::plural('module', count($group['keys'])) }}</span>
                </div>
                <svg class="module-arrow" style="width: 18px; height: 18px; color: #94a3b8; transition: transform 0.3s ease;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <div id="group-{{ $groupId }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 12px; padding: 16px 20px;">
                @foreach($group['keys'] as $key)
                @php
                $config = $moduleConfigs[$key];
                $settingValue = isset($settings['modules']) ? $settings['modules']->firstWhere('key', $key) : null;
                $currentValue = $settingValue ? $settingValue->value : 'true';
                @endphp
                <div style="background: #ffffff; border: 1px solid #f1f5f9; border-radius: 10px; padding: 14px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px;">
                        <div style="flex: 1; min-width: 0;">
                            <label style="font-weight: 600; color: #1e293b; font-size: 13px; display: block; margin-bottom: 3px;">{{ $config['name'] }}</label>
                            <p style="font-size: 11px; color: #94a3b8; margin: 0; line-height: 1.4;">{{ $config['desc'] }}</p>
                        </div>
                        <label style="position: relative; display: inline-block; width: 40px; height: 22px; flex-shrink: 0;">
                            <input type="hidden" name="settings[{{ $key }}]" value="false">
                            <input type="checkbox" name="settings[{{ $key }}]" value="true" {{ $currentValue === 'true' ? 'checked' : '' }} onchange="toggleModule(this)" style="opacity: 0; width: 0; height: 0;">
                            <span class="toggle-track" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: {{ $currentValue === 'true' ? '#10b981' : '#cbd5e1' }}; border-radius: 22px; transition: all 0.3s;">
                                <span class="toggle-thumb" style="position: absolute; content: ''; height: 16px; width: 16px; left: {{ $currentValue === 'true' ? '20px' : '3px' }}; bottom: 3px; background: white; border-radius: 50%; transition: all 0.3s; box-shadow: 0 1px 3px rgba(0,0,0,0.2);"></span>
                            </span>
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="submit" class="btn-primary" style="padding: 10px 24px; font-size: 13px;">
                <svg style="width: 16px; height: 16px; vertical-align: middle; margin-right: 4px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Save Module Settings
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function toggleModule(checkbox) {
    const hidden = checkbox.previousElementSibling;
    const track = checkbox.nextElementSibling;
    const thumb = track.querySelector('.toggle-thumb');
    const isChecked = checkbox.checked;

    hidden.value = isChecked ? 'true' : 'false';
    track.style.background = isChecked ? '#10b981' : '#cbd5e1';
    thumb.style.left = isChecked ? '20px' : '3px';
}

function toggleModuleGroup(id) {
    const container = document.getElementById('group-' + id);
    const arrow = container.parentElement.querySelector('.module-arrow');
    if (container.style.display === 'none') {
        container.style.display = '';
        arrow.style.transform = 'rotate(180deg)';
    } else {
        container.style.display = 'none';
        arrow.style.transform = '';
    }
}
</script>
@endpush

{{-- Role Configuration (Separate Forms) --}}
<div class="page-card">
    <h3 class="page-card-title">
        <div style="width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #ec4899, #db2777); box-shadow: 0 4px 12px rgba(236, 72, 153, 0.3);">
            <svg style="width: 20px; height: 20px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        Role Configuration
    </h3>
    <p style="font-size: 14px; color: #64748b; margin-bottom: 24px;">Configure default permissions for each admin role in the system.</p>
    
    @php
    $roleConfigs = [
        'super_admin' => ['name' => 'Super Admin', 'badge' => 'error', 'desc' => 'Full system access with complete control over all modules and settings.'],
        'inventory_manager' => ['name' => 'Inventory Manager', 'badge' => 'info', 'desc' => 'Manages inventory, food items, distributions, and can view users.'],
        'customer_support' => ['name' => 'Customer Support', 'badge' => 'success', 'desc' => 'Handles support tickets, views users, payments, and distributions.'],
        'member' => ['name' => 'Member', 'badge' => 'dark', 'desc' => 'Standard member role for registered users on the platform.']
    ];
    @endphp
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        @foreach($roleConfigs as $role => $config)
        <div class="page-card" style="padding: 20px;">
            <form method="POST" action="{{ route('admin.settings.update-role-permissions') }}">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <span class="badge badge-{{ $config['badge'] }}">{{ $config['name'] }}</span>
                    <button type="submit" class="btn-primary btn-sm">Save</button>
                </div>
                <p style="font-size: 13px; color: #64748b; margin-bottom: 16px;">{{ $config['desc'] }}</p>
                <div style="border-top: 1px solid #e2e8f0; padding-top: 12px; max-height: 200px; overflow-y: auto;">
                    @php
                    if (isset($permissions) && $permissions->count() > 0) {
                        $groupedPerms = $permissions->groupBy('module');
                        $currentPerms = isset($rolePermissions[$role]) ? $rolePermissions[$role] : collect();
                        $currentPermIds = $currentPerms->pluck('permission_id')->toArray();
                    } else {
                        $groupedPerms = collect([]);
                        $currentPermIds = [];
                    }
                    @endphp
                    @if($groupedPerms->count() > 0)
                    @foreach($groupedPerms as $module => $perms)
                    <p style="font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 8px 0 4px;">{{ $module }}</p>
                    @foreach($perms as $perm)
                    <label style="display: flex; align-items: center; gap: 8px; padding: 4px 0; font-size: 13px; color: #475569; cursor: pointer;">
                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" {{ in_array($perm->id, $currentPermIds) ? 'checked' : '' }} style="accent-color: #10b981;">
                        <span>{{ $perm->name }}</span>
                    </label>
                    @endforeach
                    @endforeach
                    @else
                    <p style="font-size: 13px; color: #64748b; text-align: center; padding: 20px;">No permissions available</p>
                    @endif
                </div>
            </form>
        </div>
        @endforeach
    </div>
</div>
@endsection
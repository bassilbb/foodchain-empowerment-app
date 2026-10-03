@extends('admin.layouts.app')

@section('title', 'Settings')
@section('page-title', 'Store Settings')
@section('header', 'Settings')
@section('subheader', 'Configure your market store, payment gateways, and delivery settings')

@section('content')
<style>
    /* ------------------------------------------------------------------
       Settings page — scoped design system
       Scoped to .settings-page so nothing here leaks into other admin pages.
       HD  : opaque surfaces, hairline borders, antialiased type, tight shadows.
       4D  : each surface keeps its own perspective so the icon chip and the
             content float at different depths instead of sliding flat.
       Fit : every grid child and input gets min-width:0 + border-box so long
             values wrap or shrink instead of forcing a horizontal scrollbar.
       Edge: deliberately tight outer gutter, comfortable inner card padding.
    ------------------------------------------------------------------ */
    .settings-page {
        --edge: 10px;
        --card-pad: 18px;
        --line: rgba(15, 23, 42, 0.08);
        --line-soft: rgba(15, 23, 42, 0.05);
        --ink: #0f172a;
        --ink-2: #475569;
        --ink-3: #7c8ba1;
        --surface: #ffffff;
        --surface-2: #f8fafc;
        --accent: #10b981;
        --depth: 1000px;

        margin: 0 calc(var(--edge) * -0.5);
        font-size: 14px;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
    }
    .settings-page *,
    .settings-page *::before,
    .settings-page *::after {
        box-sizing: border-box;
    }
    .settings-page p,
    .settings-page label,
    .settings-page span,
    .settings-page code {
        overflow-wrap: anywhere;
    }

    /* ---------- Card: the 4D surface ---------- */
    .settings-page .page-card {
        position: relative;
        margin: 0 0 14px;
        padding: var(--card-pad);
        background: var(--surface);
        border: 1px solid var(--line);
        border-radius: 16px;
        box-shadow:
            0 1px 1px rgba(15, 23, 42, 0.03),
            0 2px 4px rgba(15, 23, 42, 0.04),
            0 8px 20px rgba(15, 23, 42, 0.05);
        transform: perspective(var(--depth)) rotateX(0deg) translateZ(0);
        transform-style: preserve-3d;
        transition:
            transform 0.4s cubic-bezier(0.22, 1, 0.36, 1),
            box-shadow 0.4s cubic-bezier(0.22, 1, 0.36, 1),
            border-color 0.3s ease;
    }
    /* hairline highlight along the top edge — reads as a lit bevel */
    .settings-page .page-card::before {
        content: '';
        position: absolute;
        inset: 0 0 auto;
        height: 1px;
        border-radius: 16px 16px 0 0;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.95), transparent);
        pointer-events: none;
    }
    .settings-page .page-card:hover {
        transform: perspective(var(--depth)) rotateX(1.4deg) translateY(-3px);
        border-color: rgba(16, 185, 129, 0.28);
        box-shadow:
            0 1px 1px rgba(15, 23, 42, 0.04),
            0 6px 12px rgba(15, 23, 42, 0.06),
            0 18px 40px rgba(15, 23, 42, 0.09);
    }

    /* ---------- Title: icon chip floats above the surface ---------- */
    .settings-page .page-card-title {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        margin: 0 0 14px;
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -0.01em;
    }
    .settings-page .page-card-title > div {
        flex: 0 0 auto;
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: translateZ(22px);
        box-shadow: 0 3px 8px rgba(15, 23, 42, 0.16);
        transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .settings-page .page-card:hover .page-card-title > div {
        transform: translateZ(30px) scale(1.06);
    }
    .settings-page .page-card-title > span {
        margin-left: auto;
        font-size: 11px;
        font-weight: 600;
        color: var(--ink-2);
        background: var(--surface-2);
        border: 1px solid var(--line-soft);
        padding: 3px 9px;
        border-radius: 999px;
    }

    .settings-page .section-note {
        font-size: 12.5px;
        color: var(--ink-3);
        margin: -6px 0 14px;
        line-height: 1.55;
    }

    /* ---------- Grids: minmax(0,1fr) is what actually prevents overflow ---------- */
    .settings-page .field-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 240px), 1fr));
        gap: 14px;
    }
    .settings-page .field-grid.wide {
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 280px), 1fr));
    }
    .settings-page .form-group {
        position: relative;
        min-width: 0;
    }

    /* ---------- Inputs: crisp, full width, never overflow ---------- */
    .settings-page .form-input,
    .settings-page .form-select {
        display: block;
        width: 100%;
        min-width: 0;
        max-width: 100%;
        padding: 9px 12px;
        font-size: 13.5px;
        font-family: inherit;
        color: var(--ink);
        background: var(--surface-2);
        border: 1px solid var(--line);
        border-radius: 10px;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.03);
        transition:
            border-color 0.2s ease,
            box-shadow 0.2s ease,
            background 0.2s ease;
    }
    .settings-page .form-input:hover,
    .settings-page .form-select:hover {
        background: #fff;
        border-color: rgba(15, 23, 42, 0.16);
    }
    .settings-page .form-input:focus,
    .settings-page .form-select:focus {
        outline: none;
        background: #fff;
        border-color: var(--accent);
        box-shadow:
            0 0 0 3px rgba(16, 185, 129, 0.14),
            inset 0 1px 2px rgba(15, 23, 42, 0.03);
    }
    .settings-page .form-label {
        display: block;
        margin: 0 0 5px;
        font-size: 12px;
        font-weight: 600;
        color: var(--ink-2);
        letter-spacing: 0.01em;
    }
    .settings-page .form-hint {
        margin: 5px 0 0;
        font-size: 11.5px;
        line-height: 1.5;
        color: var(--ink-3);
    }
    .settings-page .form-hint code {
        font-size: 11px;
        background: #eef2f7;
        border: 1px solid var(--line-soft);
        padding: 1px 5px;
        border-radius: 5px;
        color: var(--ink-2);
    }

    /* ---------- Gateway cards ---------- */
    .settings-page .gateway-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 260px), 1fr));
        gap: 12px;
    }
    .settings-page .gateway {
        min-width: 0;
        border: 1px solid var(--line);
        border-radius: 13px;
        background: var(--surface-2);
        overflow: hidden;
        transition: transform 0.35s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.35s ease;
    }
    .settings-page .gateway:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    }
    .settings-page .gateway-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 13px;
    }
    .settings-page .gateway-badge {
        flex: 0 0 auto;
        width: 30px;
        height: 30px;
        border-radius: 9px;
        background: rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 800;
        color: #fff;
    }
    .settings-page .gateway-name {
        font-weight: 700;
        font-size: 13.5px;
        color: #fff;
        line-height: 1.25;
    }
    .settings-page .gateway-sub {
        font-size: 11px;
        opacity: 0.85;
        line-height: 1.3;
    }
    .settings-page .gateway-body {
        display: grid;
        gap: 12px;
        padding: 13px;
    }

    .settings-page .note-box {
        margin-top: 12px;
        padding: 10px 13px;
        border-radius: 11px;
        font-size: 12.5px;
        line-height: 1.55;
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    /* ---------- Role cards (were nested .page-card inside .page-card) ---------- */
    .settings-page .role-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 250px), 1fr));
        gap: 12px;
    }
    .settings-page .role-card {
        min-width: 0;
        margin: 0;
        padding: 13px;
        background: var(--surface-2);
        border: 1px solid var(--line);
        border-radius: 13px;
        box-shadow: none;
        transform: none;
    }
    .settings-page .role-card::before { display: none; }
    .settings-page .role-card:hover {
        transform: translateY(-2px);
        border-color: rgba(16, 185, 129, 0.3);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.07);
    }
    .settings-page .role-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
    }
    .settings-page .role-perms {
        max-height: 190px;
        overflow-y: auto;
        overscroll-behavior: contain;
        border-top: 1px solid var(--line);
        padding-top: 9px;
        padding-right: 4px;
    }
    .settings-page .role-perms::-webkit-scrollbar { width: 7px; }
    .settings-page .role-perms::-webkit-scrollbar-track { background: transparent; }
    .settings-page .role-perms::-webkit-scrollbar-thumb {
        background: rgba(15, 23, 42, 0.18);
        border-radius: 99px;
    }
    .settings-page .role-perms::-webkit-scrollbar-thumb:hover { background: rgba(15, 23, 42, 0.3); }
    .settings-page .perm-group {
        margin: 8px 0 3px;
        font-size: 10.5px;
        font-weight: 700;
        color: var(--ink-3);
        text-transform: uppercase;
        letter-spacing: 0.06em;
    }
    .settings-page .perm-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 3px 0;
        font-size: 12.5px;
        color: var(--ink-2);
        cursor: pointer;
    }
    .settings-page .perm-row input { accent-color: var(--accent); flex: 0 0 auto; }

    /* ---------- Module groups ---------- */
    .settings-page .module-group {
        margin-bottom: 11px;
        border: 1px solid var(--line);
        border-radius: 13px;
        overflow: hidden;
        background: var(--surface-2);
    }
    .settings-page .module-group-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 14px;
        background: #fff;
        border-bottom: 1px solid var(--line-soft);
        cursor: pointer;
        user-select: none;
    }
    .settings-page .module-group-head-main {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }
    .settings-page .module-group-icon {
        flex: 0 0 auto;
        width: 30px;
        height: 30px;
        border-radius: 9px;
        background: linear-gradient(135deg, #14b8a6, #0d9488);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .settings-page .count-pill {
        font-size: 10.5px;
        font-weight: 600;
        color: var(--ink-3);
        background: var(--surface-2);
        border: 1px solid var(--line-soft);
        padding: 2px 8px;
        border-radius: 999px;
        white-space: nowrap;
    }
    .settings-page .module-arrow {
        flex: 0 0 auto;
        width: 16px;
        height: 16px;
        color: var(--ink-3);
        transition: transform 0.3s ease;
    }
    .settings-page .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 215px), 1fr));
        gap: 10px;
        padding: 12px 14px;
    }
    .settings-page .module-cell {
        min-width: 0;
        background: #fff;
        border: 1px solid var(--line-soft);
        border-radius: 11px;
        padding: 11px 12px;
    }
    .settings-page .module-cell-main {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 10px;
    }
    .settings-page .module-cell-main > div { flex: 1 1 auto; min-width: 0; }
    .settings-page .module-name {
        display: block;
        margin-bottom: 2px;
        font-weight: 600;
        font-size: 12.5px;
        color: var(--ink);
    }
    .settings-page .module-desc {
        margin: 0;
        font-size: 11px;
        line-height: 1.45;
        color: var(--ink-3);
    }
    .settings-page .switch { position: relative; flex: 0 0 auto; display: inline-block; width: 38px; height: 21px; }
    .settings-page .switch input { opacity: 0; width: 0; height: 0; }
    .settings-page .toggle-track {
        position: absolute;
        inset: 0;
        cursor: pointer;
        border-radius: 21px;
        background: #cbd5e1;
        transition: background 0.3s;
    }
    .settings-page .toggle-thumb {
        position: absolute;
        height: 15px;
        width: 15px;
        left: 3px;
        bottom: 3px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.3);
        transition: left 0.3s;
    }

    /* ---------- Save bar: sits flush inside the page gutter ---------- */
    .settings-page .save-bar {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin: 0 0 14px;
        padding: 0 2px;
    }
    .settings-page .save-bar.sticky {
        position: sticky;
        bottom: 10px;
        z-index: 5;
    }
    .settings-page .save-bar-inner {
        display: flex;
        gap: 10px;
        padding: 7px 9px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.86);
        -webkit-backdrop-filter: blur(10px) saturate(1.4);
        backdrop-filter: blur(10px) saturate(1.4);
        border: 1px solid var(--line);
        box-shadow: 0 6px 22px rgba(15, 23, 42, 0.1);
    }

    /* ---------- Buttons ---------- */
    .settings-page .btn-primary {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 9px 18px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(135deg, #10b981, #059669);
        border: 1px solid rgba(5, 150, 105, 0.5);
        border-radius: 10px;
        cursor: pointer;
        overflow: hidden;
        box-shadow:
            0 1px 1px rgba(5, 150, 105, 0.2),
            0 3px 8px rgba(5, 150, 105, 0.24),
            inset 0 1px 0 rgba(255, 255, 255, 0.25);
        transform: perspective(600px) translateZ(0);
        transition: transform 0.25s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.25s ease, background 0.25s ease;
    }
    .settings-page .btn-primary svg { width: 15px; height: 15px; flex: 0 0 auto; }
    .settings-page .btn-primary:hover {
        transform: perspective(600px) translateZ(6px) translateY(-1px);
        background: linear-gradient(135deg, #059669, #047857);
        box-shadow:
            0 2px 2px rgba(5, 150, 105, 0.24),
            0 8px 18px rgba(5, 150, 105, 0.32),
            inset 0 1px 0 rgba(255, 255, 255, 0.3);
    }
    .settings-page .btn-primary:active {
        transform: perspective(600px) translateZ(-1px) translateY(1px);
        box-shadow: 0 1px 4px rgba(5, 150, 105, 0.3);
    }
    .settings-page .btn-primary:focus-visible {
        outline: 2px solid #059669;
        outline-offset: 2px;
    }
    .settings-page .btn-sm { padding: 6px 12px; font-size: 12px; }

    /* ---------- Badges ---------- */
    .settings-page .badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 10px;
        font-size: 11.5px;
        font-weight: 600;
        border-radius: 999px;
        border: 1px solid transparent;
        white-space: nowrap;
    }
    .settings-page .badge-error { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }
    .settings-page .badge-info { background: #eff6ff; border-color: #bfdbfe; color: #1d4ed8; }
    .settings-page .badge-success { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }
    .settings-page .badge-dark { background: #f1f5f9; border-color: #e2e8f0; color: #334155; }

    /* ---------- Alerts ---------- */
    .settings-page .alert {
        margin: 0 0 12px;
        padding: 11px 14px;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.5;
        border-radius: 12px;
        border: 1px solid;
    }
    .settings-page .alert-success { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }
    .settings-page .alert-error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }

    /* ---------- Upload dropzones ---------- */
    .settings-page .dropzone {
        border: 1.5px dashed #cbd5e1;
        border-radius: 13px;
        padding: 18px;
        text-align: center;
        background: var(--surface-2);
        transition: border-color 0.25s ease, background 0.25s ease;
    }
    .settings-page .dropzone:hover { border-color: var(--accent); background: #fff; }
    .settings-page .logo-tile {
        width: 104px;
        height: 104px;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.1);
        transform: perspective(700px) rotateX(6deg);
        transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .settings-page .logo-tile:hover { transform: perspective(700px) rotateX(0deg) scale(1.04); }
    .settings-page .logo-tile img {
        width: 104px;
        height: 104px;
        object-fit: contain;
        border-radius: 15px;
    }
    .settings-page .split-2 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(100%, 210px), 1fr));
        gap: 16px;
        align-items: start;
    }
    .settings-page .stack-sm > * + * { margin-top: 8px; }

    @media (prefers-reduced-motion: reduce) {
        .settings-page *,
        .settings-page *::before,
        .settings-page *::after {
            animation: none !important;
            transition: none !important;
        }
    }
    @media (max-width: 640px) {
        .settings-page { --card-pad: 14px; }
        .settings-page .page-card-title > span { margin-left: 0; }
    }
</style>

<div class="settings-page">

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
        <div style="background: linear-gradient(135deg, #10b981, #059669);">
            <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
        </div>
        Logo &amp; Branding
    </h3>
    <p class="section-note">Upload your company logo to replace the default FoodChain logo across the platform.</p>

    <div class="split-2">
        <div class="stack-sm">
            <p class="form-label">Current Logo</p>
            @php
            $logoUrl = site_logo();
            @endphp
            @if($logoUrl)
            <div class="logo-tile">
                <img src="{{ $logoUrl }}" alt="Current Logo">
            </div>
            @else
            <div style="color: var(--ink-3); text-align: center; padding: 8px 0;">
                <svg style="width: 46px; height: 46px; margin: 0 auto 8px; opacity: .55;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p style="font-size: 12.5px; margin: 0;">No logo uploaded</p>
            </div>
            @endif
        </div>

        <div>
            <form method="POST" action="{{ route('admin.settings.update-logo') }}" enctype="multipart/form-data" id="logoForm">
                @csrf
                <div class="dropzone">
                    <p class="form-label">Upload New Logo</p>
                    <input type="file" name="logo" id="logoInput" accept="image/*" style="display: none;" onchange="previewLogo(event)">
                    <label for="logoInput" class="btn-primary" style="margin-bottom: 10px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Choose File
                    </label>
                    <p class="form-hint" style="margin: 0;">PNG, JPG, SVG (Max 2MB)</p>
                    <div id="logoPreview" style="margin-top: 14px; display: none;">
                        <div class="logo-tile" style="margin: 0 auto;">
                            <img id="previewImage" src="" alt="Preview">
                        </div>
                    </div>
                    <button type="submit" id="uploadBtn" class="btn-primary" style="display: none; margin-top: 12px;">Upload Logo</button>
                </div>
            </form>
            @if($logoUrl)
            <form method="POST" action="{{ route('admin.settings.delete-logo') }}" style="margin-top: 10px; text-align: center;">
                @csrf
                <button type="submit" class="btn-primary" style="background: #fef2f2; color: #b91c1c; border-color: #fecaca; box-shadow: none;">
                    Remove Logo
                </button>
            </form>
            @endif
        </div>
    </div>
</div>

{{-- Favicon Settings --}}
<div class="page-card">
    <h3 class="page-card-title">
        <div style="background: linear-gradient(135deg, #14b8a6, #0d9488);">
            <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
        </div>
        Favicon
    </h3>
    <p class="section-note">Upload a favicon for the browser tab icon. Recommended size: 32x32 or 16x16 (ICO, PNG, SVG).</p>

    <div class="split-2">
        <div class="stack-sm">
            <p class="form-label">Current Favicon</p>
            @php
            $faviconUrl = site_favicon();
            @endphp
            @if($faviconUrl)
            <div class="logo-tile" style="width: 60px; height: 60px; border-radius: 12px;">
                <img src="{{ $faviconUrl }}" alt="Current Favicon" style="width: 32px; height: 32px; border-radius: 4px;">
            </div>
            @else
            <div style="color: var(--ink-3); text-align: center; padding: 8px 0;">
                <svg style="width: 38px; height: 38px; margin: 0 auto 8px; opacity: .55;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                <p style="font-size: 12.5px; margin: 0;">No favicon uploaded</p>
            </div>
            @endif
        </div>

        <div>
            <form method="POST" action="{{ route('admin.settings.update-favicon') }}" enctype="multipart/form-data">
                @csrf
                <div class="dropzone">
                    <p class="form-label">Upload New Favicon</p>
                    <input type="file" name="favicon" id="faviconInput" accept="image/*,.ico" style="display: none;" onchange="document.getElementById('faviconUploadBtn').style.display='inline-flex'">
                    <label for="faviconInput" class="btn-primary" style="margin-bottom: 10px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Choose File
                    </label>
                    <p class="form-hint" style="margin: 0;">ICO, PNG, SVG (Max 5MB)</p>
                    <button type="submit" id="faviconUploadBtn" class="btn-primary" style="display: none; margin-top: 12px;">Upload Favicon</button>
                </div>
            </form>
            @if($faviconUrl)
            <form method="POST" action="{{ route('admin.settings.delete-favicon') }}" style="margin-top: 10px; text-align: center;">
                @csrf
                <button type="submit" class="btn-primary" style="background: #fef2f2; color: #b91c1c; border-color: #fecaca; box-shadow: none;">
                    Remove Favicon
                </button>
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
            <div style="background: linear-gradient(135deg, #10b981, #059669);">
                <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            General Settings
        </h3>
        <div class="field-grid">
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
                @if($setting->description)<p class="form-hint">{{ $setting->description }}</p>@endif
            </div>
            @empty
            <p style="color: var(--ink-3); font-size: 13px;">No general settings found.</p>
            @endforelse
        </div>
    </div>

    {{-- Payment Settings (Marketplace Gateways) --}}
    <div id="payment-settings" class="page-card">
        <h3 class="page-card-title">
            <div style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            </div>
            Payment Settings
            <span>Marketplace Checkout</span>
        </h3>
        <p class="section-note">Connect the payment gateways offered at marketplace checkout. Values saved here are used unless the matching .env variable is already set.</p>

        <div class="gateway-grid">

            {{-- Paystack --}}
            <div class="gateway">
                <div class="gateway-head" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <div class="gateway-badge">PS</div>
                    <div style="min-width: 0;">
                        <div class="gateway-name">Paystack</div>
                        <div class="gateway-sub" style="color: #d1fae5;">Cards, USSD, Transfer</div>
                    </div>
                </div>
                <div class="gateway-body">
                    <div class="form-group">
                        <label class="form-label">Public Key</label>
                        <input type="text" name="settings[paystack_public_key]" value="{{ \App\Models\Setting::get('paystack_public_key') }}" class="form-input" placeholder="pk_live_...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Secret Key</label>
                        <input type="text" name="settings[paystack_secret_key]" value="{{ \App\Models\Setting::get('paystack_secret_key') }}" class="form-input" placeholder="sk_live_...">
                        <p class="form-hint">Webhook: <code>{{ route('paystack.webhook') }}</code></p>
                    </div>
                </div>
            </div>

            {{-- Flutterwave --}}
            <div class="gateway">
                <div class="gateway-head" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                    <div class="gateway-badge">FW</div>
                    <div style="min-width: 0;">
                        <div class="gateway-name">Flutterwave</div>
                        <div class="gateway-sub" style="color: #ede9fe;">Cards, Bank, USSD</div>
                    </div>
                </div>
                <div class="gateway-body">
                    <div class="form-group">
                        <label class="form-label">Secret Key</label>
                        <input type="text" name="settings[flutterwave_secret_key]" value="{{ \App\Models\Setting::get('flutterwave_secret_key') }}" class="form-input" placeholder="FLWSECK-...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Secret Hash</label>
                        <input type="text" name="settings[flutterwave_secret_hash]" value="{{ \App\Models\Setting::get('flutterwave_secret_hash') }}" class="form-input" placeholder="Webhook signature hash">
                        <p class="form-hint">Webhook: <code>{{ route('flutterwave.webhook') }}</code></p>
                    </div>
                </div>
            </div>

            {{-- Mono --}}
            <div class="gateway">
                <div class="gateway-head" style="background: linear-gradient(135deg, #06b6d4, #0891b2);">
                    <div class="gateway-badge">MO</div>
                    <div style="min-width: 0;">
                        <div class="gateway-name">Mono</div>
                        <div class="gateway-sub" style="color: #cffafe;">Direct Bank Debit</div>
                    </div>
                </div>
                <div class="gateway-body">
                    <div class="form-group">
                        <label class="form-label">Secret Key</label>
                        <input type="text" name="settings[mono_secret_key]" value="{{ \App\Models\Setting::get('mono_secret_key') }}" class="form-input" placeholder="live_sk_...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Webhook Secret</label>
                        <input type="text" name="settings[mono_webhook_secret]" value="{{ \App\Models\Setting::get('mono_webhook_secret') }}" class="form-input" placeholder="Webhook signature secret">
                        <p class="form-hint">Webhook: <code>{{ route('mono.webhook') }}</code></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="note-box">
            <strong>Bank Transfer</strong> orders are confirmed manually — you can manage the bank accounts shown to customers from the Payment Management section.
        </div>
    </div>

    {{-- Notification Settings --}}
    <div class="page-card">
        <h3 class="page-card-title">
            <div style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </div>
            Notification Settings
        </h3>
        <div class="field-grid">
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
                @if($setting->description)<p class="form-hint">{{ $setting->description }}</p>@endif
            </div>
            @empty
            <p style="color: var(--ink-3); font-size: 13px;">No notification settings found.</p>
            @endforelse
        </div>
    </div>

    {{-- System Settings --}}
    <div class="page-card">
        <h3 class="page-card-title">
            <div style="background: linear-gradient(135deg, #64748b, #475569);">
                <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-.426-1.756 0-3.35 1.066-2.573-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            </div>
            System Settings
        </h3>
        <div class="field-grid">
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

    <div class="save-bar">
        <div class="save-bar-inner">
            <button type="submit" class="btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Save Settings
            </button>
        </div>
    </div>
</form>

{{-- Security Settings (Separate Form) --}}
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    <div class="page-card">
        <h3 class="page-card-title">
            <div style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            Security Settings
        </h3>
        <div class="field-grid">
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
        <div class="save-bar" style="margin-top: 14px;">
            <div class="save-bar-inner">
                <button type="submit" class="btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save Security Settings
                </button>
            </div>
        </div>
    </div>
</form>

{{-- Module Management --}}
<div class="page-card">
    <h3 class="page-card-title">
        <div style="background: linear-gradient(135deg, #14b8a6, #0d9488);">
            <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
        </div>
        Module Management
    </h3>
    <p class="section-note">Enable or disable user-facing modules. Disabled modules will be hidden from users.</p>

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @php
        $moduleConfigs = [
            'module_referrals' => ['name' => 'Affiliate Referrals', 'desc' => 'Affiliate/referral program and referral bonuses'],
            'module_manual_payments' => ['name' => 'Bank Transfer Checkout', 'desc' => 'Manual bank transfer payment confirmation at checkout'],
            'module_marketplace' => ['name' => 'Marketplace Store', 'desc' => 'Online storefront, product catalogue and search'],
            'module_wallets' => ['name' => 'Wallets', 'desc' => 'Customer and vendor wallet balances and top-ups'],
            'module_delivery' => ['name' => 'Delivery & Riders', 'desc' => 'Rider dispatch, delivery tracking and fulfilment'],
            'module_loyalty' => ['name' => 'Loyalty & Flash Sales', 'desc' => 'Loyalty points, coupons and flash sale campaigns'],
        ];

        $moduleGroups = [
            'Marketplace Store' => ['icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'keys' => ['module_marketplace', 'module_wallets', 'module_delivery', 'module_loyalty']],
            'Growth & Checkout' => ['icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'keys' => ['module_referrals', 'module_manual_payments']],
        ];
        @endphp

        @foreach($moduleGroups as $groupName => $group)
        @php
        $groupId = Str::slug($groupName);
        @endphp
        <div class="module-group">
            <div class="module-group-header" onclick="toggleModuleGroup('{{ $groupId }}')">
                <div class="module-group-head-main">
                    <div class="module-group-icon">
                        <svg style="width: 15px; height: 15px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $group['icon'] }}"></path></svg>
                    </div>
                    <span style="font-weight: 600; color: var(--ink); font-size: 13px;">{{ $groupName }}</span>
                    <span class="count-pill">{{ count($group['keys']) }} {{ Str::plural('module', count($group['keys'])) }}</span>
                </div>
                <svg class="module-arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
            <div id="group-{{ $groupId }}" class="module-grid">
                @foreach($group['keys'] as $key)
                @php
                $config = $moduleConfigs[$key];
                $settingValue = isset($settings['modules']) ? $settings['modules']->firstWhere('key', $key) : null;
                $currentValue = $settingValue ? $settingValue->value : 'true';
                @endphp
                <div class="module-cell">
                    <div class="module-cell-main">
                        <div>
                            <label class="module-name">{{ $config['name'] }}</label>
                            <p class="module-desc">{{ $config['desc'] }}</p>
                        </div>
                        <label class="switch">
                            <input type="hidden" name="settings[{{ $key }}]" value="false">
                            <input type="checkbox" name="settings[{{ $key }}]" value="true" {{ $currentValue === 'true' ? 'checked' : '' }} onchange="toggleModule(this)">
                            <span class="toggle-track" style="background: {{ $currentValue === 'true' ? '#10b981' : '#cbd5e1' }};">
                                <span class="toggle-thumb" style="left: {{ $currentValue === 'true' ? '20px' : '3px' }};"></span>
                            </span>
                        </label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="save-bar" style="margin-top: 4px;">
            <div class="save-bar-inner">
                <button type="submit" class="btn-primary btn-sm">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Save Module Settings
                </button>
            </div>
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
        <div style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <svg style="width: 18px; height: 18px; color: #fff;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
        </div>
        Role Configuration
    </h3>
    <p class="section-note">Configure default permissions for each admin role in the system.</p>
    
    @php
    $roleConfigs = [
        'super_admin' => ['name' => 'Super Admin', 'badge' => 'error', 'desc' => 'Full system access with complete control over all modules and settings.'],
        'inventory_manager' => ['name' => 'Inventory Manager', 'badge' => 'info', 'desc' => 'Manages inventory, food items, distributions, and can view users.'],
        'customer_support' => ['name' => 'Customer Support', 'badge' => 'success', 'desc' => 'Handles support tickets, views users, payments, and distributions.'],
        'member' => ['name' => 'Member', 'badge' => 'dark', 'desc' => 'Standard member role for registered users on the platform.']
    ];
    @endphp
    
    <div class="role-grid">
        @foreach($roleConfigs as $role => $config)
        <div class="role-card">
            <form method="POST" action="{{ route('admin.settings.update-role-permissions') }}">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">
                <div class="role-head">
                    <span class="badge badge-{{ $config['badge'] }}">{{ $config['name'] }}</span>
                    <button type="submit" class="btn-primary btn-sm">Save</button>
                </div>
                <p style="font-size: 12px; color: var(--ink-2); line-height: 1.5; margin: 0 0 11px;">{{ $config['desc'] }}</p>
                <div class="role-perms">
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
                    <p class="perm-group">{{ $module }}</p>
                    @foreach($perms as $perm)
                    <label class="perm-row">
                        <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" {{ in_array($perm->id, $currentPermIds) ? 'checked' : '' }}>
                        <span>{{ $perm->name }}</span>
                    </label>
                    @endforeach
                    @endforeach
                    @else
                    <p style="font-size: 12px; color: var(--ink-3); text-align: center; padding: 16px 0;">No permissions available</p>
                    @endif
                </div>
            </form>
        </div>
        @endforeach
    </div>
</div>
</div>
@endsection
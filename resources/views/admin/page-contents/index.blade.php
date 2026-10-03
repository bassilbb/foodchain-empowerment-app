@extends('admin.layouts.app')
@section('title', 'Page Content Manager')
@section('page-title', 'Page Content Manager')
@section('content')
<style>
    .page-card {
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
        padding: 24px;
        overflow: visible;
    }
    .page-card::before {
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
    .page-card:hover::before { opacity: 1; }
    .page-card:hover {
        transform: perspective(1200px) rotateX(1.5deg) translateZ(12px) translateY(-3px);
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
        transition: all 0.35s cubic-bezier(0.23,1,0.32,1);
        box-shadow: 0 4px 14px rgba(16,185,129,0.35), 0 2px 4px rgba(16,185,129,0.2), inset 0 1px 0 rgba(255,255,255,0.2);
        transform: perspective(800px) translateZ(0);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-primary::after {
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
    .btn-primary:hover {
        transform: perspective(800px) translateZ(8px) translateY(-2px);
        box-shadow: 0 8px 28px rgba(16,185,129,0.45), 0 4px 8px rgba(16,185,129,0.25), inset 0 1px 0 rgba(255,255,255,0.3);
    }
    .btn-primary:active {
        transform: perspective(800px) translateZ(-2px) translateY(1px);
    }
    .btn-secondary {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        border: 1px solid #cbd5e1;
        color: #475569;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.23,1,0.32,1);
        transform: perspective(800px) translateZ(0);
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-secondary:hover {
        transform: perspective(800px) translateZ(6px) translateY(-2px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }
    .btn-sm { padding: 8px 18px; font-size: 13px; }
    .badge-success { background: rgba(16,185,129,0.12); color: #059669; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; backdrop-filter: blur(4px); border: 1px solid rgba(16,185,129,0.2); }
    .badge-error { background: rgba(239,68,68,0.12); color: #dc2626; padding: 4px 12px; border-radius: 999px; font-size: 12px; font-weight: 600; backdrop-filter: blur(4px); border: 1px solid rgba(239,68,68,0.2); }
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
    .alert-error {
        background: linear-gradient(135deg, rgba(239,68,68,0.1), rgba(220,38,38,0.08));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(239,68,68,0.25);
        border-radius: 14px;
        padding: 16px 20px;
        color: #991b1b;
        font-weight: 500;
        font-size: 14px;
        box-shadow: 0 4px 16px rgba(239,68,68,0.1);
        transform: perspective(800px) translateZ(0);
        animation: slideDown 0.4s cubic-bezier(0.23,1,0.32,1);
    }
    @keyframes slideDown {
        from { opacity: 0; transform: perspective(800px) translateZ(-20px) translateY(-16px); }
        to { opacity: 1; transform: perspective(800px) translateZ(0) translateY(0); }
    }
    .empty-state { text-align: center; padding: 3rem 1.5rem; color: #94a3b8; }
</style>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">{{ session('error') }}</div>
@endif
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <h2 style="font-size: 20px; font-weight: 700; color: #1e293b;">Website Pages</h2>
    @if($pages->isEmpty())
    <form method="POST" action="{{ route('admin.page-contents.seed') }}">
        @csrf
        <button type="submit" class="btn-primary">Create Default Content</button>
    </form>
    @endif
</div>
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
    @forelse($sections as $page => $pageSections)
    @php $anyActive = $pageSections->contains(fn($s) => $s->is_active); @endphp
    <div class="page-card" style="{{ $anyActive ? '' : 'opacity: 0.55;' }}">
        <div style="width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; font-size: 24px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb;">
            {{ strtoupper(substr($page, 0, 1)) }}
        </div>
        <div style="font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 4px; text-transform: capitalize;">{{ ucfirst($page) }}</div>
        <div style="font-size: 14px; color: #64748b; margin-bottom: 8px;">{{ $pageSections->count() }} editable sections</div>
        <span class="badge badge-{{ $anyActive ? 'success' : 'error' }}">{{ $anyActive ? 'Enabled' : 'Disabled' }}</span>
        <div style="display: flex; gap: 8px; margin-top: 12px;">
            <a href="{{ route('admin.page-contents.edit', $page) }}" class="btn-primary btn-sm">Edit</a>
            <form method="POST" action="{{ route('admin.page-contents.toggle', $page) }}" style="display:inline;">
                @csrf
                @method('PUT')
                <button type="submit" class="btn-secondary btn-sm">{{ $anyActive ? 'Disable' : 'Enable' }}</button>
            </form>
        </div>
    </div>
    @empty
    <div class="empty-state" style="grid-column: 1 / -1;">
        No page content found. Click "Create Default Content" above to initialize.
    </div>
    @endforelse
</div>
@endsection
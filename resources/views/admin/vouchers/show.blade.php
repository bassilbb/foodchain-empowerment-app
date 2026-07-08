@extends('admin.layouts.app')

@section('title', 'Voucher Details')
@section('page-title', 'Voucher Details')

@section('header', 'Voucher Details')
@section('subheader', 'View and manage voucher')

@section('content')
<style>
@media print {
    @page { size: A4; margin: 0.4in; }
    .no-print { display: none !important; }
    .print\:block { display: block !important; }
    body { background: white !important; padding: 0 !important; font-size: 11px !important; }
    .page-card, .grid, .mb-8, .voucher-card { display: none !important; }
    .print-voucher { max-width: 100% !important; padding: 0 !important; }
    .print-voucher-inner { border-width: 2px !important; padding: 1rem !important; }
    .print-voucher-inner h1 { font-size: 1.5rem !important; }
    .print-voucher-inner .collection-code { font-size: 1.5rem !important; }
    .print-voucher-inner .details-grid { gap: 0.5rem !important; }
    .print-voucher-inner .details-grid > div { padding: 0.5rem !important; }
    .print-voucher-inner .items-table th,
    .print-voucher-inner .items-table td { padding: 0.375rem 0.5rem !important; font-size: 10px !important; }
}
.voucher-card {
    background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
    border-radius: 24px;
    box-shadow: 
        0 20px 40px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.4) inset;
    position: relative;
    overflow: hidden;
}
.voucher-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 120px;
    background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
    border-radius: 24px 24px 0 0;
}
.voucher-card::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
    pointer-events: none;
}
.voucher-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(5, 150, 105, 0.1) 0%, transparent 50%);
    pointer-events: none;
}
.voucher-content {
    position: relative;
    z-index: 1;
}
.voucher-code {
    background: linear-gradient(135deg, #fcd34d 0%, #f59e0b 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.glass-panel {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
}
</style>

<!-- Printable Voucher (1 page) -->
@if($voucher->status === 'issued')
<div class="hidden print:block">
    <div class="print-voucher" style="max-width: 42rem; margin: 0 auto; padding: 0.5rem;">
        <div class="print-voucher-inner" style="border: 3px solid #059669; border-radius: 1rem; padding: 1.25rem;">
            <div style="text-align: center; border-bottom: 2px solid #a7f3d0; padding-bottom: 0.75rem; margin-bottom: 0.75rem;">
                <h1 style="font-size: 1.75rem; font-weight: 900; color: #047857; margin: 0;">FOODCHAIN</h1>
                <p style="color: #4b5563; margin-top: 0.15rem; font-weight: 500; font-size: 0.875rem;">Food Collection Voucher</p>
            </div>

            <div style="background: #ecfdf5; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 0.75rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="color: #047857; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin:0 0 0.15rem 0;">Voucher Number</p>
                        <p style="font-family: monospace; font-weight: 700; font-size: 0.95rem; color: #1f2937; margin:0;">{{ $voucher->voucher_number }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="color: #047857; font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin:0 0 0.15rem 0;">Collection Code</p>
                        <p class="collection-code" style="font-family: monospace; font-weight: 900; font-size: 1.75rem; color: #047857; margin:0; letter-spacing:0.1em;">{{ $voucher->collection_code }}</p>
                    </div>
                </div>
            </div>

            <div class="details-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div style="background: #f9fafb; border-radius: 0.5rem; padding: 0.625rem 0.75rem;">
                    <p style="color: #6b7280; font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin:0 0 0.15rem 0;">Member Name</p>
                    <p style="font-weight: 700; color: #1f2937; margin:0; font-size:0.9rem;">{{ $voucher->user?->full_name ?? 'N/A' }}</p>
                </div>
                <div style="background: #f9fafb; border-radius: 0.5rem; padding: 0.625rem 0.75rem;">
                    <p style="color: #6b7280; font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin:0 0 0.15rem 0;">Phone</p>
                    <p style="font-weight: 600; color: #374151; margin:0; font-size:0.9rem;">{{ $voucher->user?->phone ?? 'Not provided' }}</p>
                </div>
            </div>

            @if($voucher->distribution)
            <div style="background: #f9fafb; border-radius: 0.5rem; padding: 0.625rem 0.75rem; margin-bottom: 0.75rem;">
                <p style="color: #6b7280; font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin:0 0 0.35rem 0;">Items to Collect</p>
                <table class="items-table" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid #e2e8f0;">
                            <th style="padding:0.25rem 0.5rem;text-align:left;font-size:0.6rem;color:#64748b;text-transform:uppercase;">Item</th>
                            <th style="padding:0.25rem 0.5rem;text-align:center;font-size:0.6rem;color:#64748b;text-transform:uppercase;">Qty</th>
                            <th style="padding:0.25rem 0.5rem;text-align:right;font-size:0.6rem;color:#64748b;text-transform:uppercase;">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($voucher->distribution->items as $item)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:0.3rem 0.5rem;font-weight:600;color:#1e293b;font-size:0.8rem;">{{ $item->foodItem?->name ?? 'N/A' }}</td>
                            <td style="padding:0.3rem 0.5rem;text-align:center;color:#475569;font-size:0.8rem;">{{ number_format($item->quantity, 1) }} {{ $item->foodItem?->unit ?? '' }}</td>
                            <td style="padding:0.3rem 0.5rem;text-align:right;font-family:monospace;font-size:0.8rem;color:#475569;">{{ number_format($item->total_value, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="padding:0.5rem;text-align:center;color:#94a3b8;font-size:0.8rem;">No items listed</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif

            <div style="padding-top: 0.5rem; border-top: 2px solid #a7f3d0;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="color: #6b7280; font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin:0 0 0.15rem 0;">Valid Until</p>
                        <p style="font-weight: 700; color: #1f2937; margin:0; font-size:0.85rem;">{{ $voucher->expires_at ? $voucher->expires_at->format('M d, Y') : 'No expiry' }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="color: #4b5563; margin:0; font-size:0.8rem;">Present this voucher at pickup</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Screen Preview -->
@if($voucher->status === 'issued')
<div class="no-print" style="margin-bottom: 2rem;">
    <h3 style="font-size: 1.125rem; font-weight: 700; color: #1e293b; margin-bottom: 1rem;">Voucher Preview</h3>
    <div class="voucher-card" style="padding: 0;">
        <div class="voucher-pattern"></div>
        <div class="voucher-content" style="padding: 1.5rem; padding-top: 4rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <div>
                    <h2 style="font-size: 1.875rem; font-weight: 900; color: white; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">FOODCHAIN</h2>
                    <p style="color: #a7f3d0; font-size: 0.875rem; font-weight: 500;">Food Collection Voucher</p>
                </div>
                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                    <span style="padding: 0.375rem 1rem; background: rgba(255,255,255,0.2); backdrop-filter: blur(4px); border-radius: 9999px; color: white; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Valid</span>
                    <span style="color: rgba(255,255,255,0.7); font-size: 0.75rem;">Voucher</span>
                </div>
            </div>
            
            <div class="glass-panel" style="border-radius: 1rem; padding: 1.25rem; margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <p style="color: #047857; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.25rem;">Voucher Number</p>
                        <p style="font-family: monospace; font-weight: 700; font-size: 1.125rem; color: #1e293b;">{{ $voucher->voucher_number }}</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="color: #047857; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.25rem;">Collection Code</p>
                        <p class="voucher-code" style="font-family: monospace; font-weight: 900; font-size: 2.25rem;">{{ $voucher->collection_code }}</p>
                    </div>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: rgba(255,255,255,0.6); backdrop-filter: blur(4px); border-radius: 0.75rem; padding: 1rem;">
                    <p style="color: #059669; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.25rem;">Member Name</p>
                    <p style="font-weight: 700; color: #1e293b;">{{ $voucher->user?->full_name ?? 'N/A' }}</p>
                </div>
                <div style="background: rgba(255,255,255,0.6); backdrop-filter: blur(4px); border-radius: 0.75rem; padding: 1rem;">
                    <p style="color: #059669; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.25rem;">Phone</p>
                    <p style="font-weight: 600; color: #475569;">{{ $voucher->user?->phone ?? 'Not provided' }}</p>
                </div>
            </div>
            
            @if($voucher->distribution)
            <div style="background: #ecfdf5; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem;">
                <p style="color: #047857; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 0.75rem;">Distribution Details</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem;">
                    <div>
                        <p style="color: #64748b; font-size: 0.75rem;">Event</p>
                        <p style="font-weight: 600; color: #1e293b; font-size: 0.875rem;">{{ $voucher->distribution->name }}</p>
                    </div>
                    <div>
                        <p style="color: #64748b; font-size: 0.75rem;">Date</p>
                        <p style="font-weight: 600; color: #1e293b; font-size: 0.875rem;">{{ $voucher->distribution->distribution_date ? $voucher->distribution->distribution_date->format('M d, Y') : 'TBD' }}</p>
                    </div>
                    <div>
                        <p style="color: #64748b; font-size: 0.75rem;">Location</p>
                        <p style="font-weight: 600; color: #1e293b; font-size: 0.875rem;">{{ $voucher->distribution->pickupLocation->name ?? 'TBD' }}</p>
                    </div>
                </div>
            </div>
            @endif
            
            <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1rem; border-top: 1px solid rgba(16, 185, 129, 0.5);">
                <div>
                    <p style="color: #059669; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Valid Until</p>
                    <p style="font-weight: 700; color: #1e293b;">{{ $voucher->expires_at ? $voucher->expires_at->format('M d, Y') : 'No expiry' }}</p>
                </div>
                <button onclick="window.print()" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #10b981, #059669); color: white; font-weight: 700; border-radius: 0.75rem; border: none; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3); transition: all 0.3s; display: flex; align-items: center; gap: 0.5rem;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Voucher
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<div class="no-print" style="margin-bottom: 1.5rem;">
    <a href="{{ route('admin.vouchers.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #10b981; font-weight: 500; text-decoration: none;">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 16px; height: 16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        Back to Vouchers
    </a>
</div>

<div class="no-print" style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; @media(min-width: 1024px) { grid-template-columns: 2fr 1fr; }">
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Voucher Information</h3>
                <span style="padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600;
                    @if($voucher->status === 'pending') background: #fef3c7; color: #d97706; @endif
                    @if($voucher->status === 'issued') background: #dbeafe; color: #2563eb; @endif
                    @if($voucher->status === 'collected') background: #dcfce7; color: #16a34a; @endif
                    @if($voucher->status === 'expired') background: #f1f5f9; color: #64748b; @endif">
                    {{ ucfirst($voucher->status) }}
                </span>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Voucher Number</p>
                        <p style="font-family: monospace; font-weight: 700; font-size: 1.25rem; color: #1e293b;">{{ $voucher->voucher_number }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Collection Code</p>
                        @if($voucher->collection_code)
                        <p style="font-family: monospace; font-weight: 700; font-size: 1.25rem; color: #10b981;">{{ $voucher->collection_code }}</p>
                        @else
                        <p style="color: #94a3b8;">Not issued yet</p>
                        @endif
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Created</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $voucher->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Expires</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $voucher->expires_at ? $voucher->expires_at->format('M d, Y') : 'No expiry' }}</p>
                    </div>
                </div>

                @if($voucher->notes)
                <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid #f1f5f9;">
                    <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 0.5rem;">Notes</h4>
                    <p style="color: #475569;">{{ $voucher->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($voucher->collected_at)
        <div class="page-card" style="border-left: 4px solid #10b981;">
            <div class="page-card-header">
                <h3 class="page-card-title">Collection Details</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Collected At</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $voucher->collected_at?->format('M d, Y h:i A') ?? 'N/A' }}</p>
                    </div>
                    @if($voucher->collector)
                    <div>
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Collected By</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $voucher->collector?->name ?? 'N/A' }}</p>
                    </div>
                    @endif
                    @if($voucher->collection_notes)
                    <div style="grid-column: span 2;">
                        <p style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.25rem;">Collection Notes</p>
                        <p style="font-weight: 500; color: #1e293b;">{{ $voucher->collection_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">User</h3>
            </div>
            <div class="page-card-body">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 3rem; height: 3rem; border-radius: 50%; background: linear-gradient(135deg, #10b981, #0ea5e9); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700;">
                        {{ substr($voucher->user?->first_name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p style="font-weight: 600; color: #1e293b; margin: 0;">{{ $voucher->user?->full_name ?? 'N/A' }}</p>
                        <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $voucher->user?->email ?? '' }}</p>
                    </div>
                </div>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <a href="{{ $voucher->user ? route('admin.users.show', $voucher->user) : '#' }}" style="color: #10b981; font-weight: 500; text-decoration: none;">View Profile →</a>
                </div>
            </div>
        </div>

        @if($voucher->distribution)
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Distribution</h3>
            </div>
            <div class="page-card-body">
                <p style="font-weight: 500; color: #1e293b; margin: 0;">{{ $voucher->distribution->name }}</p>
                <p style="font-size: 0.875rem; color: #64748b; margin: 0;">{{ $voucher->distribution->distribution_date ? $voucher->distribution->distribution_date->format('M d, Y') : 'Date not set' }}</p>
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;">
                    <a href="{{ $voucher->distribution ? route('admin.distributions.show', $voucher->distribution) : '#' }}" style="color: #10b981; font-weight: 500; text-decoration: none;">View Distribution →</a>
                </div>
            </div>
        </div>
        @endif

        @if($voucher->status === 'pending')
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Actions</h3>
            </div>
            <div class="page-card-body">
                <form method="POST" action="{{ route('admin.vouchers.issue', $voucher) }}">
                    @csrf
                    <button type="submit" style="width: 100%; padding: 0.75rem 1rem; background: linear-gradient(135deg, #10b981, #059669); color: white; font-weight: 500; border-radius: 0.75rem; border: none; cursor: pointer;">
                        Issue Voucher
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($voucher->status === 'issued')
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Collect Voucher</h3>
            </div>
            <div class="page-card-body">
                <form method="POST" action="{{ route('admin.vouchers.collect', $voucher) }}" style="display: flex; flex-direction: column; gap: 1rem;">
                    @csrf
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Collection Code</label>
                        <input type="text" name="collection_code" required style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem;" placeholder="Enter collection code">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 500; color: #475569; margin-bottom: 0.5rem;">Notes (optional)</label>
                        <textarea name="notes" rows="2" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem;"></textarea>
                    </div>
                    <button type="submit" style="width: 100%; padding: 0.75rem 1rem; background: linear-gradient(135deg, #10b981, #059669); color: white; font-weight: 500; border-radius: 0.75rem; border: none; cursor: pointer;">
                        Mark as Collected
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

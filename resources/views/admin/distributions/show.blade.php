@extends('admin.layouts.app')
@section('title', 'Distribution Details')
@section('header', 'Distribution: ' . $distribution->distribution_number)
@section('subheader', 'Manage and track food distribution')

@section('content')
@if(session('success'))
<div class="alert-success" style="padding:1rem 1.5rem;border-radius:12px;background:#dcfce7;color:#166534;border:1px solid #bbf7d0;margin-bottom:1.5rem;font-weight:500;">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert-error" style="padding:1rem 1.5rem;border-radius:12px;background:#fee2e2;color:#991b1b;border:1px solid #fecaca;margin-bottom:1.5rem;font-weight:500;">{{ session('error') }}</div>
@endif

<div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:space-between;align-items:center;margin-bottom:2rem;">
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
        <a href="{{ route('admin.distributions.index') }}" class="btn btn-secondary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
            Back
        </a>
        <a href="{{ route('admin.distributions.edit', $distribution) }}" class="btn btn-primary">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            Edit
        </a>
    </div>
    @php
        $badgeMap = ['pending'=>'warning','approved'=>'info','packaged'=>'info','dispatched'=>'info','delivered'=>'success','completed'=>'success','cancelled'=>'error'];
        $badgeClass = $badgeMap[$distribution->status] ?? 'warning';
    @endphp
    <span class="badge badge-{{ $badgeClass }}" style="font-size:0.875rem;padding:0.5rem 1.25rem;">{{ ucfirst($distribution->status) }}</span>
</div>

<div class="grid-2" style="margin-bottom:1.5rem;">
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Distribution Information</h3>
        </div>
        <div class="page-card-body">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
                <div>
                    <p style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:0.25rem;">Distribution Number</p>
                    <p style="font-family:monospace;font-weight:700;font-size:1.125rem;color:#1e293b;">{{ $distribution->distribution_number }}</p>
                </div>
                <div>
                    <p style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:0.25rem;">Type</p>
                    <p style="font-weight:600;color:#1e293b;">{{ ucfirst(str_replace('_', ' ', $distribution->distribution_type)) }}</p>
                </div>
                <div>
                    <p style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:0.25rem;">Package Value</p>
                    <p style="font-weight:800;font-size:1.5rem;color:#059669;">{{ number_format($distribution->package_value, 2) }}</p>
                </div>
                <div>
                    <p style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:0.25rem;">Delivery Method</p>
                    <p style="font-weight:600;color:#1e293b;">{{ ucfirst($distribution->delivery_method) }}</p>
                </div>
                @if($distribution->scheduled_date)
                <div>
                    <p style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:0.25rem;">Scheduled Date</p>
                    <p style="font-weight:600;color:#1e293b;">{{ $distribution->scheduled_date->format('M d, Y') }}</p>
                </div>
                @endif
                @if($distribution->pickupLocation)
                <div>
                    <p style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:0.25rem;">Pickup Location</p>
                    <p style="font-weight:600;color:#1e293b;">{{ $distribution->pickupLocation->name }}</p>
                </div>
                @endif
                <div>
                    <p style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:0.25rem;">Created</p>
                    <p style="font-weight:600;color:#1e293b;">{{ $distribution->created_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Member Details</h3>
        </div>
        <div class="page-card-body">
            <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                <div class="user-avatar">{{ substr($distribution->user->first_name ?? 'U', 0, 1) }}</div>
                <div>
                    <p style="font-weight:700;font-size:1.125rem;color:#1e293b;margin:0;">{{ $distribution->user->full_name }}</p>
                    <p style="font-size:0.875rem;color:#64748b;margin:0;">{{ $distribution->user->email }}</p>
                    @if($distribution->user->phone)
                    <p style="font-size:0.875rem;color:#64748b;margin:0;">{{ $distribution->user->phone }}</p>
                    @endif
                </div>
            </div>
            @if($distribution->tier)
            <div style="padding-top:1rem;border-top:1px solid #f1f5f9;">
                <p style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:0.25rem;">Membership Tier</p>
                <span class="badge {{ $distribution->tier->slug ?? 'bronze' }}" style="font-size:0.875rem;padding:0.375rem 1rem;">{{ $distribution->tier->name }}</span>
            </div>
            @endif
            @if($distribution->package)
            <div style="padding-top:1rem;border-top:1px solid #f1f5f9;margin-top:1rem;">
                <p style="font-size:0.75rem;color:#64748b;text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:0.25rem;">Package</p>
                <p style="font-weight:600;color:#1e293b;">{{ $distribution->package->name }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Food Items ({{ $distribution->items->count() }})</h3>
    </div>
    <div class="page-card-body" style="padding:0;overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
                    <th style="padding:1rem 1.5rem;text-align:left;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;">Item</th>
                    <th style="padding:1rem 1.5rem;text-align:center;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;">Quantity</th>
                    <th style="padding:1rem 1.5rem;text-align:right;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;">Unit Value</th>
                    <th style="padding:1rem 1.5rem;text-align:right;font-size:0.75rem;font-weight:600;color:#64748b;text-transform:uppercase;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($distribution->items as $item)
                <tr style="border-bottom:1px solid #f1f5f9;transition:background 0.2s;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                    <td style="padding:1rem 1.5rem;font-weight:600;color:#1e293b;">
                        {{ $item->foodItem->name }}
                    </td>
                    <td style="padding:1rem 1.5rem;text-align:center;color:#475569;">
                        {{ number_format($item->quantity, 2) }} {{ $item->foodItem->unit }}
                    </td>
                    <td style="padding:1rem 1.5rem;text-align:right;font-family:monospace;color:#475569;">
                        {{ number_format($item->unit_value, 2) }}
                    </td>
                    <td style="padding:1rem 1.5rem;text-align:right;font-family:monospace;font-weight:700;color:#059669;">
                        {{ number_format($item->total_value, 2) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="padding:3rem;text-align:center;color:#94a3b8;">No items in this distribution</td>
                </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr style="background:#f0fdf4;border-top:2px solid #bbf7d0;">
                    <td colspan="3" style="padding:1rem 1.5rem;text-align:right;font-weight:700;color:#1e293b;font-size:1rem;">Total Package Value</td>
                    <td style="padding:1rem 1.5rem;text-align:right;font-family:monospace;font-weight:800;font-size:1.25rem;color:#059669;">{{ number_format($distribution->getTotalValue(), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@if($distribution->admin_notes || $distribution->user_notes)
<div class="grid-2" style="margin-bottom:1.5rem;">
    @if($distribution->admin_notes)
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Admin Notes</h3>
        </div>
        <div class="page-card-body">
            <p style="color:#475569;margin:0;">{{ $distribution->admin_notes }}</p>
        </div>
    </div>
    @endif
    @if($distribution->user_notes)
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">User Notes</h3>
        </div>
        <div class="page-card-body">
            <p style="color:#475569;margin:0;">{{ $distribution->user_notes }}</p>
        </div>
    </div>
    @endif
</div>
@endif

@if(in_array($distribution->status, ['pending', 'approved', 'packaged', 'dispatched']))
<div class="page-card">
    <div class="page-card-header">
        <h3 class="page-card-title">Actions</h3>
    </div>
    <div class="page-card-body">
        <div style="display:flex;flex-wrap:wrap;gap:1rem;">
            @if($distribution->isPending())
            <form method="POST" action="{{ route('admin.distributions.approve', $distribution) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Approve Distribution
                </button>
            </form>
            @endif
            @if($distribution->isApproved())
            <form method="POST" action="{{ route('admin.distributions.package', $distribution) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Mark as Packaged
                </button>
            </form>
            @endif
            @if($distribution->status === 'packaged' || $distribution->status === 'packaged')
            <form method="POST" action="{{ route('admin.distributions.dispatch', $distribution) }}" style="display:inline;">
                @csrf
                <div style="display:flex;gap:0.75rem;align-items:flex-end;flex-wrap:wrap;">
                    <div>
                        <label style="display:block;font-size:0.75rem;font-weight:500;color:#64748b;margin-bottom:0.25rem;">Tracking #</label>
                        <input type="text" name="tracking_number" style="padding:0.625rem 1rem;border:1px solid #e2e8f0;border-radius:8px;font-size:0.875rem;" placeholder="Optional">
                    </div>
                    <div>
                        <label style="display:block;font-size:0.75rem;font-weight:500;color:#64748b;margin-bottom:0.25rem;">Courier</label>
                        <input type="text" name="courier_name" style="padding:0.625rem 1rem;border:1px solid #e2e8f0;border-radius:8px;font-size:0.875rem;" placeholder="Optional">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Dispatch
                    </button>
                </div>
            </form>
            @endif
            @if($distribution->status === 'dispatched')
            <form method="POST" action="{{ route('admin.distributions.deliver', $distribution) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Mark as Delivered
                </button>
            </form>
            @endif
            @if(!in_array($distribution->status, ['delivered', 'completed', 'cancelled']))
            <form method="POST" action="{{ route('admin.distributions.cancel', $distribution) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this distribution?')">
                @csrf
                <div style="display:flex;gap:0.75rem;align-items:flex-end;flex-wrap:wrap;">
                    <div>
                        <label style="display:block;font-size:0.75rem;font-weight:500;color:#64748b;margin-bottom:0.25rem;">Reason</label>
                        <input type="text" name="reason" style="padding:0.625rem 1rem;border:1px solid #fecaca;border-radius:8px;font-size:0.875rem;" placeholder="Cancellation reason" required>
                    </div>
                    <button type="submit" class="btn btn-secondary" style="color:#dc2626;border-color:#fecaca;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Cancel
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</div>
@endif

@if($distribution->approvedBy || $distribution->packagedBy)
<div class="grid-2" style="margin-bottom:1.5rem;">
    @if($distribution->approvedBy)
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Approved By</h3>
        </div>
        <div class="page-card-body">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#10b981,#0ea5e9);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;">{{ substr($distribution->approvedBy->name ?? 'A', 0, 1) }}</div>
                <div>
                    <p style="font-weight:600;color:#1e293b;margin:0;">{{ $distribution->approvedBy->name ?? 'N/A' }}</p>
                    @if($distribution->approved_at)
                    <p style="font-size:0.875rem;color:#64748b;margin:0;">{{ $distribution->approved_at->format('M d, Y h:i A') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($distribution->packagedBy)
    <div class="page-card">
        <div class="page-card-header">
            <h3 class="page-card-title">Packaged By</h3>
        </div>
        <div class="page-card-body">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#10b981,#0ea5e9);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;">{{ substr($distribution->packagedBy->name ?? 'A', 0, 1) }}</div>
                <div>
                    <p style="font-weight:600;color:#1e293b;margin:0;">{{ $distribution->packagedBy->name ?? 'N/A' }}</p>
                    @if($distribution->packaged_at)
                    <p style="font-size:0.875rem;color:#64748b;margin:0;">{{ $distribution->packaged_at->format('M d, Y h:i A') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif
@endsection

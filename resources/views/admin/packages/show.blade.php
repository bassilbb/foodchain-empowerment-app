@extends('admin.layouts.app')

@section('title', 'Package Details')
@section('page-title', $package->name)

@section('content')
<style>
    .card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 24px; }
    .stat-card { background: linear-gradient(180deg, #f0fdf4, #dcfce7); border: 1px solid #bbf7d0; border-radius: 14px; padding: 20px; }
    .stat-value { font-size: 24px; font-weight: 700; color: #10b981; }
    .badge { padding: 4px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
    .btn-edit { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 12px 24px; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); transition: all 0.3s ease; text-decoration: none; display: block; text-align: center; font-size: 14px; }
    .btn-edit:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45); }
    .btn-back { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 12px 24px; border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: block; text-align: center; font-size: 14px; }
    .btn-back:hover { background: #e2e8f0; }
    .btn-delete { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; font-weight: 600; padding: 12px 24px; border: none; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; width: 100%; font-size: 14px; }
    .btn-delete:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); }
    .table-header { background: linear-gradient(180deg, #f8fafc, #f1f5f9); }
    .table-header th { padding: 12px 16px; font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .table-cell { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; }
    .table-row:hover { background: #f8fafc; }
    .total-label { font-size: 14px; font-weight: 600; color: #475569; }
    .total-amount { font-size: 20px; font-weight: 700; color: #10b981; }
</style>

<div style="display: grid; grid-template-columns: 1fr; gap: 24px;">
    <div style="display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start;">
        {{-- Main Content --}}
        <div style="display: flex; flex-direction: column; gap: 24px;">
            {{-- Package Header Card --}}
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 24px;">
                    <div>
                        <h3 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0 0 8px 0;">{{ $package->name }}</h3>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <span class="badge" style="background: {{ match($package->tier->slug) { 'bronze' => 'linear-gradient(135deg, #d97706, #b45309)', 'silver' => 'linear-gradient(135deg, #9ca3af, #6b7280)', 'gold' => 'linear-gradient(135deg, #f59e0b, #d97706)', 'emerald' => 'linear-gradient(135deg, #10b981, #059669)', default => '#64748b' } }}; color: white;">
                                {{ $package->tier->name }}
                            </span>
                            @if($package->is_default)
                                <span class="badge" style="background: #dcfce7; color: #059669;">Default</span>
                            @endif
                            <span class="badge" style="background: {{ $package->is_active ? '#dbeafe' : '#f1f5f9' }}; color: {{ $package->is_active ? '#2563eb' : '#64748b' }};">
                                {{ $package->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <p style="font-size: 12px; color: #94a3b8; margin: 0 0 4px 0;">Total Value</p>
                        <p style="font-size: 28px; font-weight: 700; color: #10b981; margin: 0;">{{ format_currency($package->total_value) }}</p>
                    </div>
                </div>

                @if($package->description)
                <div style="margin-bottom: 20px; padding: 16px; background: #f8fafc; border-radius: 12px;">
                    <p style="font-size: 13px; color: #64748b; margin: 0; line-height: 1.6;">{{ $package->description }}</p>
                </div>
                @endif

                {{-- Package Items Table --}}
                <div>
                    <h4 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0 0 16px 0;">Package Items</h4>
                    <div style="border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead class="table-header">
                                <tr>
                                    <th style="text-align: left;">Food Item</th>
                                    <th style="text-align: right;">Quantity</th>
                                    <th style="text-align: right;">Unit Value</th>
                                    <th style="text-align: right;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($package->items as $item)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <div style="font-weight: 600; color: #1e293b;">{{ $item->foodItem->name }}</div>
                                        <div style="font-size: 12px; color: #94a3b8;">{{ $item->foodItem->unit }}</div>
                                    </td>
                                    <td class="table-cell" style="text-align: right; font-weight: 600; color: #475569;">
                                        {{ number_format($item->quantity, 2) }}
                                    </td>
                                    <td class="table-cell" style="text-align: right; color: #64748b;">
                                        {{ format_currency($item->unit_value) }}
                                    </td>
                                    <td class="table-cell" style="text-align: right; font-weight: 700; color: #10b981;">
                                        {{ format_currency($item->getTotalValue()) }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot style="background: linear-gradient(180deg, #f0fdf4, #dcfce7);">
                                <tr>
                                    <td colspan="3" style="padding: 14px 16px; text-align: right;">
                                        <span class="total-label">Total Package Value:</span>
                                    </td>
                                    <td style="padding: 14px 16px; text-align: right;">
                                        <span class="total-amount">{{ format_currency($package->total_value) }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Distribution History --}}
            @if($package->distributions->count() > 0)
            <div class="card">
                <h4 style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0 0 4px 0;">Distribution History</h4>
                <p style="font-size: 13px; color: #94a3b8; margin: 0 0 20px 0;">This package has been distributed {{ $package->distributions->count() }} time(s).</p>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @foreach($package->distributions->take(5) as $distribution)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 14px 16px; background: #f8fafc; border-radius: 12px; border: 1px solid #f1f5f9;">
                        <div>
                            <p style="font-weight: 600; color: #1e293b; margin: 0 0 2px 0;">{{ $distribution->user->full_name }}</p>
                            <p style="font-size: 12px; color: #94a3b8; margin: 0;">{{ $distribution->distribution_number }}</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 12px; color: #64748b; margin: 0 0 4px 0;">{{ $distribution->created_at->format('M d, Y') }}</p>
                            <span class="badge" style="background: #dbeafe; color: #2563eb; font-size: 11px;">{{ ucfirst($distribution->status) }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div style="display: flex; flex-direction: column; gap: 20px;">
            {{-- Actions --}}
            <div class="card">
                <h4 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 16px 0;">Actions</h4>
                <div style="display: flex; flex-direction: column; gap: 10px;">
                    <a href="{{ route('admin.packages.edit', $package) }}" class="btn-edit">
                        <svg style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Package
                    </a>

                    <a href="{{ route('admin.packages.index') }}" class="btn-back">
                        <svg style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Back to Packages
                    </a>

                    @if(!$package->distributions->count())
                    <form method="POST" action="{{ route('admin.packages.destroy', $package) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete"
                            data-confirm='{"title":"Delete Package?","text":"Are you sure you want to delete this package?","btnText":"Delete","btnClass":"confirm-danger","iconClass":"danger"}'>
                            <svg style="width: 16px; height: 16px; vertical-align: middle; margin-right: 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete Package
                        </button>
                    </form>
                    @endif
                </div>
            </div>

            {{-- Statistics --}}
            <div class="stat-card">
                <h4 style="font-size: 15px; font-weight: 700; color: #1e293b; margin: 0 0 16px 0;">Package Statistics</h4>
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #bbf7d0;">
                        <span style="font-size: 13px; color: #475569;">Total Items</span>
                        <span class="stat-value" style="font-size: 20px;">{{ $package->items->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 12px; border-bottom: 1px solid #bbf7d0;">
                        <span style="font-size: 13px; color: #475569;">Times Distributed</span>
                        <span class="stat-value" style="font-size: 20px;">{{ $package->distributions->count() }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 13px; color: #475569;">Value Distributed</span>
                        <span class="stat-value" style="font-size: 16px;">{{ format_currency($package->distributions->count() * $package->total_value) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
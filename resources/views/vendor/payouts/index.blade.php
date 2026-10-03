@extends('vendor.layouts.app')

@section('title', 'Earnings & Payouts')
@section('header', 'Earnings & Payouts')
@section('subheader', 'What you have earned and what you can withdraw.')

@section('content')

{{-- Available balance hero --}}
<div class="mb-5 overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-950 via-emerald-900 to-teal-900 p-6 shadow-lg sm:p-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-emerald-300/70">Available for Withdrawal</p>
            <p class="mt-1 text-3xl font-extrabold text-white sm:text-4xl">{{ format_currency($summary['available']) }}</p>
            <p class="mt-1.5 text-xs text-emerald-200/70">
                Minimum request: {{ format_currency($minimumPayout) }}
            </p>
        </div>

        <div class="flex flex-wrap gap-3">
            <div class="rounded-xl bg-white/10 px-4 py-3 ring-1 ring-inset ring-white/15">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-300/70">Net Earnings</p>
                <p class="mt-0.5 text-sm font-bold text-white">{{ format_currency($summary['net_earnings']) }}</p>
            </div>
            <div class="rounded-xl bg-white/10 px-4 py-3 ring-1 ring-inset ring-white/15">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-300/70">Paid Out</p>
                <p class="mt-0.5 text-sm font-bold text-white">{{ format_currency($summary['paid']) }}</p>
            </div>
            <div class="rounded-xl bg-white/10 px-4 py-3 ring-1 ring-inset ring-white/15">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-300/70">Pending</p>
                <p class="mt-0.5 text-sm font-bold text-white">{{ format_currency($summary['pending']) }}</p>
            </div>
        </div>
    </div>

    @if ($canRequest)
    <button type="button" data-modal-open="payout-modal"
        class="mt-5 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 px-5 py-3 text-sm font-bold text-white shadow-lg shadow-amber-900/30 transition hover:brightness-110">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
        </svg>
        Request Payout
    </button>
    @else
    <p class="mt-5 inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-3 text-xs font-semibold text-emerald-200 ring-1 ring-inset ring-white/15">
        You need at least {{ format_currency($minimumPayout) }} available before you can request a payout.
    </p>
    @endif
</div>

{{-- Breakdown + ledger --}}
<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    <div class="space-y-5">
        <x-marketplace.card title="Earnings Breakdown" subtitle="How your balance is calculated">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-slate-500">Gross sales</dt>
                    <dd class="font-semibold text-slate-800">{{ format_currency($summary['gross_sales']) }}</dd>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                    <dt class="text-slate-500">Commission ({{ $summary['commission_rate'] }}%)</dt>
                    <dd class="font-semibold text-rose-600">−{{ format_currency($summary['commission_amount']) }}</dd>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                    <dt class="text-slate-500">Other fees</dt>
                    <dd class="font-semibold text-slate-600">{{ format_currency($summary['other_deductions']) }}</dd>
                </div>
                <div class="flex items-center justify-between border-t-2 border-emerald-100 pt-3">
                    <dt class="font-bold text-slate-800">Net earnings</dt>
                    <dd class="font-extrabold text-emerald-700">{{ format_currency($summary['net_earnings']) }}</dd>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                    <dt class="text-slate-500">Paid out</dt>
                    <dd class="font-semibold text-slate-700">{{ format_currency($summary['paid']) }}</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-slate-500">Pending requests</dt>
                    <dd class="font-semibold text-amber-600">{{ format_currency($summary['pending']) }}</dd>
                </div>
            </dl>

            <p class="mt-4 rounded-xl bg-slate-50 p-3 text-[11px] leading-relaxed text-slate-500">
                Commission is charged at your store rate of <strong>{{ $summary['commission_rate'] }}%</strong>.
                Earnings are recognised when an order item is <strong>delivered</strong>, not when it is placed.
            </p>
        </x-marketplace.card>
    </div>

    <div class="lg:col-span-2">
        <x-marketplace.card title="Payout History" :flush="true">
            <x-slot:actions>
                <div class="flex flex-wrap gap-1">
                    <a href="{{ route('vendor.payouts.index') }}"
                        class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ ! $currentStatus ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600' }}">All</a>
                    @foreach (['requested' => 'Requested', 'paid' => 'Paid'] as $key => $label)
                    <a href="{{ route('vendor.payouts.index', ['status' => $key]) }}"
                        class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $currentStatus === $key ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </x-slot:actions>

            @if ($payouts->isEmpty())
            <x-marketplace.empty-state
                icon="wallet"
                title="{{ $currentStatus ? 'No payouts with this status' : 'No payouts yet' }}"
                message="{{ $currentStatus
                            ? 'Try a different filter to see your payout history.'
                            : 'When you request a payout, it will be listed here with its status.' }}"
                :action-url="$currentStatus ? route('vendor.payouts.index') : null"
                :action-label="$currentStatus ? 'View all payouts' : null" />
            @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/70 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-5 py-3">Reference</th>
                            <th class="px-5 py-3">Requested</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach ($payouts as $payout)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-4">
                                <p class="font-mono text-xs font-bold text-slate-700">{{ $payout->reference }}</p>
                                <p class="text-[11px] text-slate-400">{{ $payout->bank_name }} · {{ $payout->account_number }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                {{ ($payout->requested_at ?? $payout->created_at)->format('d M, Y') }}
                            </td>
                            <td class="px-5 py-4 font-bold text-slate-800">{{ format_currency($payout->amount) }}</td>
                            <td class="px-5 py-4">
                                <x-marketplace.status-badge :status="$payout->status" />
                                @if ($payout->rejection_reason)
                                <p class="mt-1 text-[11px] text-rose-500">{{ $payout->rejection_reason }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if ($payout->status === 'requested')
                                <form method="POST" action="{{ route('vendor.payouts.cancel', $payout) }}"
                                    data-confirm="Cancel this payout request?">
                                    @csrf
                                    <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-bold text-rose-600 transition hover:bg-rose-50">Cancel</button>
                                </form>
                                @else
                                <span class="text-[11px] text-slate-400">
                                    {{ $payout->paid_at ? 'Paid '.$payout->paid_at->format('d M Y') : '—' }}
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <x-marketplace.pagination :paginator="$payouts" />
            @endif
        </x-marketplace.card>
    </div>
</div>

{{-- Request payout modal --}}
@if ($canRequest)
<x-marketplace.modal id="payout-modal" title="Request Payout">
    <form method="POST" action="{{ route('vendor.payouts.store') }}">
        @csrf

        <div class="mb-4">
            <label for="amount" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Amount (₦)</label>
            <input type="number" id="amount" name="amount" step="0.01" min="{{ $minimumPayout }}"
                max="{{ $summary['available'] }}" value="{{ number_format($summary['available'], 2, '.', '') }}" required
                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            <p class="mt-1 text-[11px] text-slate-400">
                Available: {{ format_currency($summary['available']) }} · Minimum: {{ format_currency($minimumPayout) }}
            </p>
        </div>

        <div class="mb-4">
            <label for="payout_method" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Payout Method</label>
            <select id="payout_method" name="payout_method" required
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
                <option value="bank_transfer">Bank Transfer</option>
            </select>
        </div>

        <div class="mb-4 grid grid-cols-1 gap-3 sm:grid-cols-2">
            <div>
                <label for="account_name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Account Name</label>
                <input type="text" id="account_name" name="account_name" required
                    value="{{ old('account_name', $vendor->business_name ?: $vendor->name) }}"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
            </div>
            <div>
                <label for="account_number" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Account Number</label>
                <input type="text" id="account_number" name="account_number" required inputmode="numeric" maxlength="20"
                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
            </div>
        </div>

        <div class="mb-5">
            <label for="bank_name" class="mb-1.5 block text-xs font-bold uppercase tracking-wider text-slate-500">Bank Name</label>
            <input type="text" id="bank_name" name="bank_name" required
                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-emerald-500 focus:outline-none">
        </div>

        <div class="flex gap-2">
            <button type="button" data-modal-close class="fc-btn-ghost flex-1 justify-center text-sm">Cancel</button>
            <button type="submit" class="fc-btn-primary flex-1 justify-center text-sm">Submit Request</button>
        </div>
    </form>
</x-marketplace.modal>
@endif

@endsection
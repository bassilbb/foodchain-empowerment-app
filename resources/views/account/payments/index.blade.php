@extends('account.layouts.app')

@section('title', 'Payment History')
@section('header', 'Payment History')
@section('subheader', 'Every payment recorded against your account.')

@section('content')

<div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-marketplace.stat-card label="Total Paid" :value="format_currency($summary['total_paid'])" icon="wallet" tone="primary" hint="Successful payments" />
    <x-marketplace.stat-card label="Pending" :value="format_currency($summary['total_pending'])" icon="pending" tone="cta" hint="Awaiting confirmation" />
    <x-marketplace.stat-card label="Failed" :value="format_currency($summary['total_failed'])" icon="cancelled" tone="danger" hint="Did not complete" />
</div>

{{-- Filters --}}
<div class="mb-5 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('account.payments.index', array_filter(['from' => $from, 'to' => $to])) }}"
            class="rounded-full px-3.5 py-1.5 text-xs font-bold transition
                      {{ ! $currentStatus ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            All ({{ $summary['count'] }})
        </a>
        @foreach (['successful' => 'Successful', 'pending' => 'Pending', 'failed' => 'Failed'] as $key => $label)
        <a href="{{ route('account.payments.index', array_filter(['status' => $key, 'from' => $from, 'to' => $to])) }}"
            class="rounded-full px-3.5 py-1.5 text-xs font-bold transition
                          {{ $currentStatus === $key ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            {{ $label }} ({{ $counts[$key] ?? 0 }})
        </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('account.payments.index') }}" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        @if ($currentStatus)<input type="hidden" name="status" value="{{ $currentStatus }}">@endif

        <div>
            <label for="from" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">From</label>
            <input type="date" id="from" name="from" value="{{ $from }}"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </div>

        <div>
            <label for="to" class="mb-1 block text-[11px] font-bold uppercase tracking-wider text-slate-400">To</label>
            <input type="date" id="to" name="to" value="{{ $to }}"
                class="w-full rounded-xl border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </div>

        <div class="flex items-end">
            <button type="submit" class="fc-btn-primary w-full justify-center text-sm">Apply filters</button>
        </div>
    </form>
</div>

<x-marketplace.card :flush="true">
    @if ($payments->isEmpty())
    <x-marketplace.empty-state
        icon="wallet"
        title="No payments yet"
        message="Payments you make on FoodChain will appear here."
        :action-url="route('marketplace.shop')"
        action-label="Start shopping" />
    @else
    {{-- Desktop table --}}
    <div class="hidden overflow-x-auto lg:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/70 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Reference</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Purpose</th>
                    <th class="px-5 py-3">Method</th>
                    <th class="px-5 py-3">Amount</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach ($payments as $payment)
                <tr class="transition hover:bg-slate-50/70">
                    <td class="px-5 py-4 font-mono text-xs text-slate-600">{{ $payment->reference }}</td>
                    <td class="px-5 py-4 text-slate-600">{{ $payment->created_at->format('d M, Y') }}</td>
                    <td class="px-5 py-4 capitalize text-slate-700">{{ str_replace('_', ' ', $payment->type) }}</td>
                    <td class="px-5 py-4 capitalize text-slate-600">{{ str_replace('_', ' ', $payment->payment_method ?? '—') }}</td>
                    <td class="px-5 py-4 font-bold text-slate-800">{{ format_currency($payment->amount) }}</td>
                    <td class="px-5 py-4"><x-marketplace.status-badge :status="$payment->status" type="payment" /></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="divide-y divide-slate-100 lg:hidden">
        @foreach ($payments as $payment)
        <div class="p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate font-mono text-xs text-slate-600">{{ $payment->reference }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $payment->created_at->format('d M, Y') }} · <span class="capitalize">{{ str_replace('_', ' ', $payment->type) }}</span></p>
                </div>
                <p class="shrink-0 text-sm font-extrabold text-slate-800">{{ format_currency($payment->amount) }}</p>
            </div>
            <div class="mt-2">
                <x-marketplace.status-badge :status="$payment->status" type="payment" />
            </div>
        </div>
        @endforeach
    </div>

    <x-marketplace.pagination :paginator="$payments" />
    @endif
</x-marketplace.card>

@endsection
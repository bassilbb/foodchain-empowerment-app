@extends('account.layouts.app')

@section('title', 'Wallet Transactions')
@section('header', 'Wallet Transactions')
@section('subheader', 'Complete history of your marketplace wallet.')

@section('content')

{{-- Summary --}}
<div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-3">
    <x-marketplace.stat-card label="Current Balance" :value="format_currency($balance)" icon="wallet" tone="primary" />
    <x-marketplace.stat-card label="Total Credited" :value="format_currency($totalCredit)" icon="delivered" tone="info" />
    <x-marketplace.stat-card label="Total Spent" :value="format_currency($totalDebit)" icon="spend" tone="danger" />
</div>

{{-- Filters --}}
<div class="mb-5 rounded-2xl border border-slate-100 bg-white p-4 shadow-sm">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('account.wallet.transactions', array_filter(['from' => $from, 'to' => $to])) }}"
            class="rounded-full px-3.5 py-1.5 text-xs font-bold transition
                      {{ ! $currentType ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            All
        </a>
        @foreach (['credit' => 'Credits', 'debit' => 'Debits'] as $key => $label)
        <a href="{{ route('account.wallet.transactions', array_filter(['type' => $key, 'from' => $from, 'to' => $to])) }}"
            class="rounded-full px-3.5 py-1.5 text-xs font-bold transition
                          {{ $currentType === $key ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>

    <form method="GET" action="{{ route('account.wallet.transactions') }}" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3">
        @if ($currentType)<input type="hidden" name="type" value="{{ $currentType }}">@endif

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
    @if ($transactions->isEmpty())
    <x-marketplace.empty-state
        icon="wallet"
        title="{{ $currentType || $from || $to ? 'No transactions match your filters' : 'No wallet transactions yet' }}"
        :message="$currentType || $from || $to
                    ? 'Try widening the date range or clearing the type filter.'
                    : 'Credits and debits on your wallet will appear here.'"
        :action-url="$currentType || $from || $to ? route('account.wallet.transactions') : route('account.wallet.index')"
        :action-label="$currentType || $from || $to ? 'Clear filters' : 'Back to wallet'" />
    @else
    {{-- Desktop table --}}
    <div class="hidden overflow-x-auto lg:block">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/70 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Description</th>
                    <th class="px-5 py-3">Reference</th>
                    <th class="px-5 py-3">Order</th>
                    <th class="px-5 py-3 text-right">Amount</th>
                    <th class="px-5 py-3 text-right">Balance</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach ($transactions as $transaction)
                <tr class="transition hover:bg-slate-50/70">
                    <td class="px-5 py-4 text-slate-600">{{ $transaction->created_at->format('d M, Y') }}</td>
                    <td class="px-5 py-4 text-slate-700">{{ $transaction->description ?: 'Wallet transaction' }}</td>
                    <td class="px-5 py-4 font-mono text-xs text-slate-500">{{ $transaction->reference ?: '—' }}</td>
                    <td class="px-5 py-4">
                        @if ($transaction->order)
                        <a href="{{ route('account.orders.show', $transaction->order) }}" class="text-xs font-semibold text-emerald-600 hover:underline">
                            {{ $transaction->order->order_number }}
                        </a>
                        @else
                        <span class="text-xs text-slate-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-right font-bold {{ $transaction->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $transaction->type === 'credit' ? '+' : '−' }}{{ format_currency($transaction->amount) }}
                    </td>
                    <td class="px-5 py-4 text-right text-slate-600">
                        {{ $transaction->balance_after !== null ? format_currency($transaction->balance_after) : '—' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Mobile cards --}}
    <div class="divide-y divide-slate-100 lg:hidden">
        @foreach ($transactions as $transaction)
        <div class="p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $transaction->description ?: 'Wallet transaction' }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $transaction->created_at->format('d M, Y · g:i A') }}</p>
                </div>
                <p class="shrink-0 text-sm font-extrabold {{ $transaction->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                    {{ $transaction->type === 'credit' ? '+' : '−' }}{{ format_currency($transaction->amount) }}
                </p>
            </div>
            @if ($transaction->balance_after !== null)
            <p class="mt-1 text-[11px] text-slate-400">Balance after: {{ format_currency($transaction->balance_after) }}</p>
            @endif
        </div>
        @endforeach
    </div>

    <x-marketplace.pagination :paginator="$transactions" />
    @endif
</x-marketplace.card>

<div class="mt-5">
    <a href="{{ route('account.wallet.index') }}" class="fc-btn-ghost text-sm">Back to wallet</a>
</div>

@endsection
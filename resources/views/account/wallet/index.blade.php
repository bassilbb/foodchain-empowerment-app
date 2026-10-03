@extends('account.layouts.app')

@section('title', 'Wallet')
@section('header', 'FoodChain Wallet')
@section('subheader', 'Your marketplace balance and spending activity.')

@section('content')

{{-- Balance hero --}}
<div class="mb-5 overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-950 via-emerald-900 to-teal-900 p-6 shadow-lg sm:p-8">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-emerald-300/70">Available Balance</p>
            <p class="mt-1 text-3xl font-extrabold text-white sm:text-4xl">{{ format_currency($balance) }}</p>
            <p class="mt-1.5 text-xs text-emerald-200/70">Use your wallet balance at checkout for instant payment.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <div class="rounded-xl bg-white/10 px-4 py-3 ring-1 ring-inset ring-white/15">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-300/70">Total Credited</p>
                <p class="mt-0.5 text-sm font-bold text-white">{{ format_currency($totalCredit) }}</p>
            </div>
            <div class="rounded-xl bg-white/10 px-4 py-3 ring-1 ring-inset ring-white/15">
                <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-300/70">Total Spent</p>
                <p class="mt-0.5 text-sm font-bold text-white">{{ format_currency($totalDebit) }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Funding notice: no funding flow exists in this application yet. --}}
<div class="mb-5 flex flex-wrap items-start gap-3 rounded-2xl border border-sky-200 bg-sky-50 p-4">
    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-600">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </span>
    <div class="min-w-0 flex-1">
        <p class="text-sm font-bold text-sky-900">How your wallet is credited</p>
        <p class="text-xs text-sky-700">
            Your wallet is credited from order refunds and loyalty point redemptions.
            You can redeem loyalty points for wallet credit from the
            <a href="{{ route('marketplace.loyalty.index') }}" class="font-semibold underline">loyalty page</a>.
            Direct wallet top-up is not yet available.
        </p>
    </div>
</div>

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
    {{-- Recent transactions --}}
    <div class="lg:col-span-2">
        <x-marketplace.card title="Recent Transactions" subtitle="Your five most recent wallet movements" :flush="true">
            <x-slot:actions>
                <a href="{{ route('account.wallet.transactions') }}" class="fc-btn-ghost text-xs">View all</a>
            </x-slot:actions>

            @if ($recentTransactions->isEmpty())
            <x-marketplace.empty-state
                icon="wallet"
                title="No wallet activity yet"
                message="Wallet credits and debits will appear here."
                :action-url="route('marketplace.shop')"
                action-label="Start shopping" />
            @else
            <ul class="divide-y divide-slate-50">
                @foreach ($recentTransactions as $transaction)
                <li class="flex items-center gap-4 px-5 py-4">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl
                                    {{ $transaction->type === 'credit' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            @if ($transaction->type === 'credit')
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            @else
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4" />
                            @endif
                        </svg>
                    </span>

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-800">{{ $transaction->description ?: 'Wallet transaction' }}</p>
                        <p class="text-[11px] text-slate-400">
                            {{ $transaction->created_at->format('d M Y, g:i A') }}
                            @if ($transaction->reference) · {{ $transaction->reference }} @endif
                        </p>
                    </div>

                    <div class="shrink-0 text-right">
                        <p class="text-sm font-extrabold {{ $transaction->type === 'credit' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $transaction->type === 'credit' ? '+' : '−' }}{{ format_currency($transaction->amount) }}
                        </p>
                        @if ($transaction->balance_after !== null)
                        <p class="text-[11px] text-slate-400">Bal: {{ format_currency($transaction->balance_after) }}</p>
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
            @endif
        </x-marketplace.card>
    </div>

    {{-- Side panel --}}
    <div class="space-y-5">
        <x-marketplace.card title="Wallet Summary">
            <dl class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-slate-500">Current balance</dt>
                    <dd class="font-extrabold text-slate-800">{{ format_currency($balance) }}</dd>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                    <dt class="text-slate-500">Total credited</dt>
                    <dd class="font-semibold text-emerald-600">{{ format_currency($totalCredit) }}</dd>
                </div>
                <div class="flex items-center justify-between border-t border-slate-100 pt-3">
                    <dt class="text-slate-500">Total spent</dt>
                    <dd class="font-semibold text-rose-600">{{ format_currency($totalDebit) }}</dd>
                </div>
            </dl>
        </x-marketplace.card>

        <x-marketplace.card title="Quick Links">
            <div class="space-y-2">
                <a href="{{ route('account.wallet.transactions') }}" class="fc-btn-ghost w-full justify-center text-xs">All transactions</a>
                <a href="{{ route('marketplace.loyalty.index') }}" class="fc-btn-ghost w-full justify-center text-xs">Redeem loyalty points</a>
                <a href="{{ route('account.payments.index') }}" class="fc-btn-ghost w-full justify-center text-xs">Payment history</a>
            </div>
        </x-marketplace.card>
    </div>
</div>

@endsection
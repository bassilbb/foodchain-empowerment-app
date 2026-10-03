@extends('marketplace.layouts.app')

@section('content')
    <h1 class="text-2xl font-extrabold text-gray-900 mb-6">Marketplace Wallet</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-gradient-to-r from-emerald-800 to-emerald-600 rounded-2xl p-6 text-white">
                <p class="text-emerald-200 text-sm mb-1">Available Balance</p>
                <p class="text-4xl font-extrabold">₦{{ number_format($balance, 2) }}</p>
                <p class="text-emerald-200 text-xs mt-3">Use your wallet to pay for marketplace orders instantly.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-900">Transaction History</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('marketplace.wallet.index') }}" class="text-xs text-emerald-700 hover:underline {{ !isset($currentType) || !$currentType ? 'font-bold' : '' }}">All</a>
                        <a href="{{ route('marketplace.wallet.transactions', ['type' => 'credit']) }}" class="text-xs text-emerald-700 hover:underline {{ isset($currentType) && $currentType === 'credit' ? 'font-bold' : '' }}">Credits</a>
                        <a href="{{ route('marketplace.wallet.transactions', ['type' => 'debit']) }}" class="text-xs text-emerald-700 hover:underline {{ isset($currentType) && $currentType === 'debit' ? 'font-bold' : '' }}">Debits</a>
                    </div>
                </div>

                @if ($transactions->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-8">No transactions yet.</p>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach ($transactions as $txn)
                            <div class="py-3 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full {{ $txn->type === 'credit' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }} flex items-center justify-center text-sm font-bold">{{ $txn->type === 'credit' ? '+' : '−' }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $txn->description }}</p>
                                    <p class="text-xs text-gray-400">{{ $txn->created_at->format('d M Y, g:i A') }} · {{ $txn->reference }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold {{ $txn->type === 'credit' ? 'text-emerald-600' : 'text-red-500' }}">{{ $txn->type === 'credit' ? '+' : '-' }}₦{{ number_format($txn->amount, 2) }}</p>
                                    <p class="text-xs text-gray-400">Bal: ₦{{ number_format($txn->balance_after, 2) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if (!empty($paginated))
                        <div class="mt-4">{{ $transactions->links() }}</div>
                    @endif
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-3">Redeem Loyalty Points</h3>
                <p class="text-xs text-gray-500 mb-3">1 point = ₦1 wallet credit. Earn 1 point per ₦10 spent.</p>
                <form action="{{ route('marketplace.wallet.redeem-points') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="number" name="points" min="10" max="{{ auth()->user()->loyalty_points_balance }}" placeholder="Points" class="flex-1 text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg px-4 py-2 transition-colors">Redeem</button>
                </form>
                <p class="text-xs text-gray-400 mt-2">You have <strong>{{ auth()->user()->loyalty_points_balance }}</strong> points available.</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-3">Top Up Wallet</h3>
                <p class="text-xs text-gray-500 mb-3">Top up your wallet from the member dashboard.</p>
                <a href="{{ route('manual-payments.create', ['type' => 'wallet_topup']) }}" class="block text-center bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg py-2.5 transition-colors">Go to Wallet Top-up</a>
            </div>
        </div>
    </div>
@endsection

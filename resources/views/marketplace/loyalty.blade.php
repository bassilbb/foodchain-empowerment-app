@extends('marketplace.layouts.app')

@section('content')
    <h1 class="text-2xl font-extrabold text-gray-900 mb-6">Loyalty Points</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Points History</h3>

                @if ($history->isEmpty())
                    <p class="text-sm text-gray-500 text-center py-8">No loyalty activity yet. Shop to start earning points!</p>
                @else
                    <div class="divide-y divide-gray-50">
                        @foreach ($history as $entry)
                            <div class="py-3 flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full {{ $entry->points > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-orange-100 text-orange-600' }} flex items-center justify-center text-sm font-bold">{{ $entry->points > 0 ? '+' : '−' }}</div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $entry->description }}</p>
                                    <p class="text-xs text-gray-400">{{ $entry->created_at->format('d M Y, g:i A') }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-bold {{ $entry->points > 0 ? 'text-emerald-600' : 'text-orange-500' }}">{{ $entry->points > 0 ? '+' : '' }}{{ $entry->points }} pts</p>
                                    <p class="text-xs text-gray-400">Bal: {{ $entry->balance_after }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div>
            <div class="bg-gradient-to-r from-orange-500 to-amber-500 rounded-2xl p-6 text-white mb-6">
                <p class="text-orange-100 text-sm mb-1">Your Points Balance</p>
                <p class="text-4xl font-extrabold">{{ $balance }}</p>
                <p class="text-orange-100 text-xs mt-3">Worth ₦{{ number_format($redeemableValue, 2) }} in wallet credit</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-3">Redeem Points</h3>
                <p class="text-xs text-gray-500 mb-3">Convert points to wallet credit at 1 point = ₦1. Minimum 10 points.</p>
                <form action="{{ route('marketplace.loyalty.redeem') }}" method="POST">
                    @csrf
                    <input type="number" name="points" min="10" max="{{ $balance }}" value="{{ min(100, $balance) }}" class="w-full text-sm rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 mb-3" required>
                    <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white text-sm font-bold rounded-lg py-2.5 transition-colors">Redeem to Wallet</button>
                </form>
                <p class="text-xs text-gray-400 mt-3 leading-relaxed">💡 Earn 100 points for every ₦1,000 spent. Points are added automatically after paid orders.</p>
            </div>
        </div>
    </div>
@endsection

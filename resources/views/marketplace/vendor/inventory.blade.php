@extends('marketplace.layouts.app')

@section('content')
    @include('marketplace.vendor.partials.nav', ['vendor' => $vendor])

    <h1 class="text-xl font-extrabold text-gray-900 mb-6">Inventory</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs text-gray-500 border-b border-gray-100 bg-gray-50">
                            <th class="px-4 py-3">Product</th>
                            <th class="px-4 py-3">Stock</th>
                            <th class="px-4 py-3">Threshold</th>
                            <th class="px-4 py-3">Expiry</th>
                            <th class="px-4 py-3 text-right">Adjust</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr class="border-b border-gray-50">
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-800">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $product->sku }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="{{ $product->isLowStock() ? 'text-orange-500 font-bold' : ($product->stock_quantity <= 0 ? 'text-red-500 font-bold' : 'text-gray-700') }}">{{ $product->stock_quantity }}</span>
                                    @if ($product->isLowStock())<span class="text-[10px] text-orange-500 block">LOW</span>@endif
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ $product->low_stock_threshold }}</td>
                                <td class="px-4 py-3 text-gray-500">
                                    {{ $product->expiry_date ? $product->expiry_date->format('d M Y') : '—' }}
                                    @if ($product->isExpiringSoon())<span class="text-[10px] text-orange-500 block">EXPIRING SOON</span>@endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button onclick="openAdjust({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->stock_quantity }})" class="text-emerald-700 hover:underline text-xs font-semibold">Adjust</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-6">{{ $products->links() }}</div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Recent Movements</h3>
                @if ($movements->isEmpty())
                    <p class="text-sm text-gray-500">No stock movements yet.</p>
                @else
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach ($movements as $move)
                            <div class="flex items-start gap-2 text-sm">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0 mt-0.5 {{ $move->quantity > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">{{ $move->type }}</span>
                                <div class="min-w-0">
                                    <p class="text-gray-800 truncate">{{ $move->product->name ?? 'Product' }}</p>
                                    <p class="text-xs text-gray-400">{{ $move->quantity > 0 ? '+' : '' }}{{ $move->quantity }} · {{ $move->reason }}</p>
                                    <p class="text-[10px] text-gray-300">{{ $move->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Adjust modal --}}
    <div id="adjust-modal" class="hidden fixed inset-0 bg-black/50 z-[1000] flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full">
            <h3 class="font-bold text-gray-900 mb-1" id="adjust-title">Adjust Stock</h3>
            <p class="text-xs text-gray-400 mb-4" id="adjust-current"></p>
            <form action="" method="POST" id="adjust-form">
                @csrf
                <div class="mb-3">
                    <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Type</label>
                    <select name="type" id="adjust-type" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500" onchange="onAdjustType()">
                        <option value="stock_in">Stock In (+)</option>
                        <option value="stock_out">Stock Out (−)</option>
                        <option value="adjustment">Set Exact Quantity</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Quantity</label>
                    <input type="number" name="quantity" id="adjust-qty" min="1" required class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div class="mb-4">
                    <label class="text-xs font-semibold text-gray-500 uppercase block mb-1">Reason *</label>
                    <input type="text" name="reason" required placeholder="e.g. New shipment received" class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('adjust-modal').classList.add('hidden')" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg py-2.5 text-sm">Cancel</button>
                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg py-2.5 text-sm">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openAdjust(id, name, current) {
            document.getElementById('adjust-title').textContent = 'Adjust Stock: ' + name;
            document.getElementById('adjust-current').textContent = 'Current stock: ' + current;
            document.getElementById('adjust-form').action = '/marketplace/vendor/inventory/' + id + '/adjust';
            document.getElementById('adjust-modal').classList.remove('hidden');
        }
        function onAdjustType() {
            const t = document.getElementById('adjust-type').value;
            document.getElementById('adjust-qty').min = t === 'adjustment' ? 0 : 1;
        }
    </script>
    @endpush
@endsection

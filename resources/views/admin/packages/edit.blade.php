{{-- resources/views/admin/packages/edit.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Edit Package')
@section('page-title', 'Edit Package: ' . $package->name)

@section('content')
<div class="max-w-4xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.packages.update', $package) }}">
        @csrf
        @method('PUT')
        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Package Name</label>
                    <input type="text" name="name" value="{{ $package->name }}" required
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tier</label>
                    <select name="tier_id" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach($tiers as $tier)
                            <option value="{{ $tier->id }}" {{ $package->tier_id == $tier->id ? 'selected' : '' }}>
                                {{ $tier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $package->description }}</textarea>
            </div>

            <div class="flex gap-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ $package->is_active ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium">Active</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" name="is_default" value="1" {{ $package->is_default ? 'checked' : '' }} class="mr-2">
                    <span class="text-sm font-medium">Set as default package for this tier</span>
                </label>
            </div>

            <!-- Package Items -->
            <div>
                <div class="flex justify-between items-center mb-3">
                    <h3 class="text-lg font-semibold">Package Items</h3>
                    <button type="button" onclick="addPackageItem()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                        + Add Item
                    </button>
                </div>

                <div id="items-container" class="space-y-3">
                    <!-- Header -->
                    <div class="grid grid-cols-12 gap-2 font-medium text-sm text-gray-700">
                        <div class="col-span-5">Food Item</div>
                        <div class="col-span-3">Quantity</div>
                        <div class="col-span-3">Unit Value (₦)</div>
                        <div class="col-span-1"></div>
                    </div>

                    <!-- Existing Items -->
                    @foreach($package->items as $index => $item)
                    <div class="grid grid-cols-12 gap-2 package-item-row">
                        <div class="col-span-5">
                            <select name="items[{{ $index }}][food_item_id]" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                @foreach($foodItems as $foodItem)
                                    <option value="{{ $foodItem->id }}" {{ $item->food_item_id == $foodItem->id ? 'selected' : '' }}>
                                        {{ $foodItem->name }} ({{ $foodItem->unit }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-3">
                            <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" step="0.01" required
                                   class="w-full px-3 py-2 border rounded-lg text-sm" onchange="calculateTotal()">
                        </div>
                        <div class="col-span-3">
                            <input type="number" name="items[{{ $index }}][unit_value]" value="{{ $item->unit_value }}" step="0.01" required
                                   class="w-full px-3 py-2 border rounded-lg text-sm" onchange="calculateTotal()">
                        </div>
                        <div class="col-span-1">
                            <button type="button" onclick="removePackageItem(this)" class="w-full px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                                ×
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Total Value Display -->
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Total Package Value:</span>
                        <span id="total-value" class="text-2xl font-bold text-blue-600">₦{{ number_format($package->total_value, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Update Package
                </button>
                <a href="{{ route('admin.packages.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

<script>
let itemIndex = {{ $package->items->count() }};

function addPackageItem() {
    const container = document.getElementById('items-container');
    const firstRow = document.querySelector('.package-item-row');
    const newRow = firstRow.cloneNode(true);

    newRow.querySelectorAll('input, select').forEach(input => {
        const nameMatch = input.name.match(/\[(\d+)\]/);
        if (nameMatch) {
            input.name = input.name.replace(`[${nameMatch[1]}]`, `[${itemIndex}]`);
        }
        if (input.tagName === 'INPUT') {
            input.value = '';
        } else if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        }
    });

    container.appendChild(newRow);
    itemIndex++;
}

function removePackageItem(button) {
    const rows = document.querySelectorAll('.package-item-row');
    if (rows.length > 1) {
        button.closest('.package-item-row').remove();
        calculateTotal();
    } else {
        alert('You must have at least one item in the package.');
    }
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.package-item-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
        const unitValue = parseFloat(row.querySelector('input[name*="[unit_value]"]').value) || 0;
        total += quantity * unitValue;
    });
    document.getElementById('total-value').textContent = '₦' + total.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Calculate total on page load
document.addEventListener('DOMContentLoaded', calculateTotal);
</script>
@endsection

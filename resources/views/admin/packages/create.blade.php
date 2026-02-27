{{-- resources/views/admin/packages/create.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Create Package')
@section('page-title', 'Create Food Package')

@section('content')
<div class="max-w-4xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.packages.store') }}">
        @csrf
        <div class="space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Package Name</label>
                    <input type="text" name="name" required placeholder="e.g., Silver Annual Package 2024"
                           class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tier</label>
                    <select name="tier_id" required class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Tier</option>
                        @foreach($tiers as $tier)
                            <option value="{{ $tier->id }}">{{ $tier->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                <textarea name="description" rows="3" placeholder="Package description..."
                          class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div>
                <label class="flex items-center">
                    <input type="checkbox" name="is_default" value="1" class="mr-2">
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

                    <!-- First Item Row -->
                    <div class="grid grid-cols-12 gap-2 package-item-row">
                        <div class="col-span-5">
                            <select name="items[0][food_item_id]" required class="w-full px-3 py-2 border rounded-lg text-sm">
                                <option value="">Select Food Item</option>
                                @foreach($foodItems as $item)
                                    <option value="{{ $item->id }}" data-unit="{{ $item->unit }}">{{ $item->name }} ({{ $item->unit }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-3">
                            <input type="number" name="items[0][quantity]" step="0.01" required placeholder="0.00"
                                   class="w-full px-3 py-2 border rounded-lg text-sm" onchange="calculateTotal()">
                        </div>
                        <div class="col-span-3">
                            <input type="number" name="items[0][unit_value]" step="0.01" required placeholder="0.00"
                                   class="w-full px-3 py-2 border rounded-lg text-sm" onchange="calculateTotal()">
                        </div>
                        <div class="col-span-1">
                            <button type="button" onclick="removePackageItem(this)" class="w-full px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                                ×
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Total Value Display -->
                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-700">Total Package Value:</span>
                        <span id="total-value" class="text-2xl font-bold text-blue-600">₦0.00</span>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-4 pt-4 border-t">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Create Package
                </button>
                <a href="{{ route('admin.packages.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemIndex = 1;

function addPackageItem() {
    const container = document.getElementById('items-container');
    const newRow = document.querySelector('.package-item-row').cloneNode(true);

    // Update input names
    newRow.querySelectorAll('input, select').forEach(input => {
        input.name = input.name.replace('[0]', `[${itemIndex}]`);
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
</script>
@endpush
@endsection

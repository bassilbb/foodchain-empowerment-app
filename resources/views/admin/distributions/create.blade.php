{{-- resources/views/admin/distributions/create.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Create Distribution')
@section('page-title', 'Create Discretionary Distribution')
@section('content')
<div class="max-w-4xl bg-white rounded-lg shadow p-6">
    <form method="POST" action="{{ route('admin.distributions.store') }}">
        @csrf
        <div class="space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">User</label>
                    <select name="user_id" required class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Pickup Location</label>
                    <select name="location_id" required class="w-full px-3 py-2 border rounded-lg">
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <h3 class="font-semibold mb-2">Food Items</h3>
                <div id="items-container" class="space-y-2">
                    <div class="grid grid-cols-4 gap-2">
                        <div><label class="text-sm font-medium">Food Item</label></div>
                        <div><label class="text-sm font-medium">Quantity</label></div>
                        <div><label class="text-sm font-medium">Unit Value (₦)</label></div>
                        <div></div>
                    </div>
                    <div class="grid grid-cols-4 gap-2 item-row">
                        <select name="items[0][food_item_id]" required class="px-3 py-2 border rounded-lg">
                            <option value="">Select Item</option>
                            @foreach($foodItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        <input type="number" name="items[0][quantity]" step="0.01" required placeholder="Qty" class="px-3 py-2 border rounded-lg">
                        <input type="number" name="items[0][unit_value]" step="0.01" required placeholder="Value" class="px-3 py-2 border rounded-lg">
                        <button type="button" onclick="removeItem(this)" class="px-3 py-2 bg-red-500 text-white rounded-lg">Remove</button>
                    </div>
                </div>
                <button type="button" onclick="addItem()" class="mt-2 px-4 py-2 bg-green-600 text-white rounded-lg">Add Item</button>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Admin Notes</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Distribution</button>
                <a href="{{ route('admin.distributions.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Cancel</a>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
let itemIndex = 1;
function addItem() {
    const container = document.getElementById('items-container');
    const newRow = document.querySelector('.item-row').cloneNode(true);
    newRow.querySelectorAll('input, select').forEach(input => {
        input.name = input.name.replace('[0]', `[${itemIndex}]`);
        if (input.tagName === 'INPUT') input.value = '';
    });
    container.appendChild(newRow);
    itemIndex++;
}
function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length > 1) {
        btn.closest('.item-row').remove();
    }
}
</script>
@endpush
@endsection

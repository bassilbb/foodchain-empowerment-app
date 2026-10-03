@extends('admin.layouts.app')

@section('title', 'Create Package')
@section('page-title', 'Create Food Package')

@section('content')
<style>
    .form-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 32px; }
    .input-field { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; font-size: 14px; transition: all 0.3s ease; }
    .input-field:focus { background: #ffffff; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); outline: none; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .grid-12 { display: grid; grid-template-columns: 5fr 3fr 3fr 1fr; gap: 12px; align-items: center; }
    .header-row { display: grid; grid-template-columns: 5fr 3fr 3fr 1fr; gap: 12px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 0; }
    .item-row { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; }
    .section-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
    .btn-add { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 10px 20px; border: none; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; }
    .btn-add:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); }
    .btn-remove { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; font-weight: 600; padding: 14px; border: none; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; }
    .btn-remove:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); }
    .total-box { background: linear-gradient(180deg, #f0fdf4, #dcfce7); border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-top: 16px; }
    .total-value { font-size: 24px; font-weight: 700; color: #10b981; }
    .btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 14px 28px; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); transition: all 0.3s ease; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45); }
    .btn-secondary { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 14px 28px; border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
    .btn-secondary:hover { background: #e2e8f0; }
    .label { display: block; font-size: 14px; font-weight: 500; color: #475569; margin-bottom: 8px; }
    .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; cursor: pointer; }
    .checkbox-label input { width: 18px; height: 18px; accent-color: #10b981; }
</style>

<div style="max-width: 900px; margin: 0 auto;">
    <div class="form-card">
        <form method="POST" action="{{ route('admin.packages.store') }}">
            @csrf
            @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; color: #dc2626; font-size: 14px;">{{ $errors->first() }}</div>
            @endif
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div class="grid-2">
                    <div>
                        <label class="label">Package Name</label>
                        <input type="text" name="name" required placeholder="e.g., Silver Annual Package 2024" class="input-field" style="width: 100%; box-sizing: border-box;">
                    </div>
                    <div>
                        <label class="label">Tier</label>
                        <select name="tier_id" required class="input-field" style="width: 100%; box-sizing: border-box;">
                            <option value="">Select Tier</option>
                            @foreach($tiers as $tier)
                                <option value="{{ $tier->id }}">{{ $tier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="label">Description (Optional)</label>
                    <textarea name="description" rows="3" placeholder="Package description..." class="input-field" style="width: 100%; box-sizing: border-box;"></textarea>
                </div>

                <label class="checkbox-label">
                    <input type="checkbox" name="is_default" value="1">
                    <span>Set as default package for this tier</span>
                </label>

                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <h3 class="section-title" style="margin: 0;">Package Items</h3>
                        <button type="button" onclick="addPackageItem()" class="btn-add">+ Add Item</button>
                    </div>

                    <div id="items-container">
                        <div class="header-row">
                            <div>Food Item</div>
                            <div>Quantity</div>
                            <div>Unit Value (₦)</div>
                            <div></div>
                        </div>

                        <div class="grid-12 item-row" style="margin-bottom: 12px;">
                            <select name="items[0][food_item_id]" required class="input-field">
                                <option value="">Select Food Item</option>
                                @foreach($foodItems as $item)
                                    <option value="{{ $item->id }}" data-unit="{{ $item->unit }}">{{ $item->name }} ({{ $item->unit }})</option>
                                @endforeach
                            </select>
                            <input type="number" name="items[0][quantity]" step="0.01" required placeholder="0.00" class="input-field" onchange="calculateTotal()">
                            <input type="number" name="items[0][unit_value]" step="0.01" required placeholder="0.00" class="input-field" onchange="calculateTotal()">
                            <button type="button" onclick="removePackageItem(this)" class="btn-remove">×</button>
                        </div>
                    </div>

                    <div class="total-box">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 14px; font-weight: 500; color: #475569;">Total Package Value:</span>
                            <span id="total-value" class="total-value">₦0.00</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                    <button type="submit" class="btn-primary">Create Package</button>
                    <a href="{{ route('admin.packages.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = 1;

function addPackageItem() {
    const container = document.getElementById('items-container');
    const newRow = document.querySelector('.item-row').cloneNode(true);

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
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) {
        button.closest('.item-row').remove();
        calculateTotal();
    } else {
        alert('You must have at least one item in the package.');
    }
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]').value) || 0;
        const unitValue = parseFloat(row.querySelector('input[name*="[unit_value]"]').value) || 0;
        total += quantity * unitValue;
    });
    document.getElementById('total-value').textContent = '₦' + total.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>
@endsection
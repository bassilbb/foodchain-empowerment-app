@extends('admin.layouts.app')
@section('title', 'Create Distribution')
@section('page-title', 'Create Discretionary Distribution')
@section('content')
<style>
    .form-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 32px; }
    .input-field { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; width: 100%; box-sizing: border-box; transition: all 0.3s ease; font-size: 14px; }
    .input-field:focus { background: #ffffff; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); outline: none; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .grid-4 { display: grid; grid-template-columns: 2fr 1fr 1fr 80px; gap: 12px; align-items: center; }
    .section-title { font-size: 18px; font-weight: 700; color: #1e293b; margin-bottom: 16px; }
    .header-row { display: grid; grid-template-columns: 2fr 1fr 1fr 80px; gap: 12px; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px 0; }
    .item-row { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px; }
    .btn-add { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 10px 20px; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.3s ease; }
    .btn-add:hover { background: linear-gradient(135deg, #059669 0%, #047857 100%); transform: translateY(-2px); }
    .btn-remove { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; font-weight: 600; padding: 14px; border: none; border-radius: 10px; cursor: pointer; transition: all 0.3s ease; }
    .btn-remove:hover { background: linear-gradient(135deg, #dc2626, #b91c1c); }
    .btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 14px 28px; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); transition: all 0.3s ease; }
    .btn-primary:hover { box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45); transform: translateY(-2px); }
    .btn-secondary { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 14px 28px; border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
    .btn-secondary:hover { background: linear-gradient(180deg, #e2e8f0, #cbd5e1); }
    .total-box { background: linear-gradient(180deg, #f0fdf4, #dcfce7); border: 1px solid #bbf7d0; border-radius: 12px; padding: 16px; margin-top: 16px; }
    .label { display: block; font-size: 14px; font-weight: 500; color: #475569; margin-bottom: 8px; }
</style>

<div style="max-width: 900px; margin: 0 auto;">
    <div class="form-card">
        <form method="POST" action="{{ route('admin.distributions.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div class="grid-2">
                    <div>
                        <label class="label">User</label>
                        <select name="user_id" required class="input-field">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Pickup Location</label>
                        <select name="location_id" required class="input-field">
                            @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <h3 class="section-title">Quick Fill from Package</h3>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <select id="package-select" class="input-field" style="flex: 1;">
                            <option value="">— Select a Package —</option>
                            @foreach($packages as $pkg)
                            <option value="{{ $pkg->id }}">{{ $pkg->name }} (₦{{ number_format($pkg->total_value, 2) }})</option>
                            @endforeach
                        </select>
                        <button type="button" id="fill-from-package" class="btn-add" disabled>Fill Items</button>
                    </div>
                    <p style="font-size:13px; color:#94a3b8; margin-top:6px;">Selecting a package will replace the items below with the package contents.</p>
                </div>

                <div>
                    <h3 class="section-title">Food Items</h3>
                    <div id="items-container">
                        <div class="header-row">
                            <div>Food Item</div>
                            <div>Quantity</div>
                            <div>Unit Value (₦)</div>
                            <div></div>
                        </div>
                        <div class="grid-4 item-row" style="margin-bottom: 12px;">
                            <select name="items[0][food_item_id]" required class="input-field">
                                <option value="">Select Item</option>
                                @foreach($foodItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                            <input type="number" name="items[0][quantity]" step="0.01" required class="input-field" placeholder="Qty">
                            <input type="number" name="items[0][unit_value]" step="0.01" required class="input-field" placeholder="Value">
                            <button type="button" onclick="removeItem(this)" class="btn-remove">×</button>
                        </div>
                    </div>
                    <button type="button" onclick="addItem()" class="btn-add" style="margin-top: 12px;">+ Add Item</button>
                </div>

                <div>
                    <label class="label">Admin Notes</label>
                    <textarea name="notes" rows="3" class="input-field"></textarea>
                </div>

                <div style="display: flex; gap: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                    <button type="submit" class="btn-primary">Create Distribution</button>
                    <a href="{{ route('admin.distributions.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
let itemIndex = 1;
const allFoodItems = @json($foodItems->map(fn($i) => ['id' => $i->id, 'name' => $i->name]));

function buildItemRow(index, data) {
    let html = '<div class="grid-4 item-row" style="margin-bottom: 12px;">';
    html += '<select name="items[' + index + '][food_item_id]" required class="input-field">';
    html += '<option value="">Select Item</option>';
    allFoodItems.forEach(function(fi) {
        html += '<option value="' + fi.id + '"' + (data && data.food_item_id == fi.id ? ' selected' : '') + '>' + fi.name + '</option>';
    });
    html += '</select>';
    html += '<input type="number" name="items[' + index + '][quantity]" step="0.01" required placeholder="Qty" class="input-field" value="' + (data ? data.quantity : '') + '">';
    html += '<input type="number" name="items[' + index + '][unit_value]" step="0.01" required placeholder="Value" class="input-field" value="' + (data ? data.unit_value : '') + '">';
    html += '<button type="button" onclick="removeItem(this)" class="btn-remove">×</button>';
    html += '</div>';
    return html;
}

function addItem(data) {
    const container = document.getElementById('items-container');
    const wrapper = document.createElement('div');
    wrapper.innerHTML = buildItemRow(itemIndex, data || null);
    container.appendChild(wrapper.firstElementChild);
    itemIndex++;
}

function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length > 1) {
        btn.closest('.item-row').remove();
    }
}

document.getElementById('package-select').addEventListener('change', function() {
    document.getElementById('fill-from-package').disabled = !this.value;
});

document.getElementById('fill-from-package').addEventListener('click', function() {
    const pkgId = document.getElementById('package-select').value;
    if (!pkgId) return;

    fetch('{{ route("admin.distributions.package-items", "_PKG_") }}'.replace('_PKG_', pkgId))
        .then(function(r) { return r.json(); })
        .then(function(items) {
            const container = document.getElementById('items-container');
            container.innerHTML = '<div class="header-row"><div>Food Item</div><div>Quantity</div><div>Unit Value (₦)</div><div></div></div>';
            itemIndex = 0;
            items.forEach(function(item) { addItem(item); });
        })
        .catch(function() { alert('Failed to load package items.'); });
});
</script>
@endsection
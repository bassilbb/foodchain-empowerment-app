@extends('admin.layouts.app')
@section('title', 'Edit Food Item')
@section('page-title', 'Edit: ' . $foodItem->name)
@section('content')
<style>
    .form-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 32px; }
    .input-field { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; width: 100%; box-sizing: border-box; transition: all 0.3s ease; font-size: 14px; }
    .input-field:focus { background: #ffffff; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1), 0 4px 12px rgba(16, 185, 129, 0.15); outline: none; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 14px 28px; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); transition: all 0.3s ease; }
    .btn-primary:hover { background: linear-gradient(135deg, #059669 0%, #047857 100%); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45); transform: translateY(-2px); }
    .btn-secondary { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 14px 28px; border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
    .btn-secondary:hover { background: linear-gradient(180deg, #e2e8f0, #cbd5e1); transform: translateY(-2px); }
    .label { display: block; font-size: 14px; font-weight: 500; color: #475569; margin-bottom: 8px; }
    .file-input { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 2px dashed #e2e8f0; border-radius: 12px; padding: 24px; width: 100%; box-sizing: border-box; text-align: center; cursor: pointer; transition: all 0.3s ease; }
    .file-input:hover { border-color: #10b981; background: linear-gradient(180deg, #f0fdf4, #dcfce7); }
</style>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="form-card">
        <form method="POST" action="{{ route('admin.food-items.update', $foodItem) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @if($errors->any())
            <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; padding: 12px 16px; margin-bottom: 16px; color: #dc2626; font-size: 14px;">{{ $errors->first() }}</div>
            @endif
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <label class="label">Name</label>
                    <input type="text" name="name" value="{{ $foodItem->name }}" required class="input-field" placeholder="e.g., Rice">
                </div>
                <div>
                    <label class="label">Description</label>
                    <textarea name="description" rows="3" class="input-field" placeholder="Brief description of the food item...">{{ $foodItem->description }}</textarea>
                </div>
                <div class="grid-2">
                    <div>
                        <label class="label">Unit</label>
                        <input type="text" name="unit" value="{{ $foodItem->unit }}" required class="input-field" placeholder="kg, liters, pack">
                    </div>
                    <div>
                        <label class="label">Unit Price (₦)</label>
                        <input type="number" name="unit_price" value="{{ $foodItem->unit_price }}" step="0.01" required class="input-field" placeholder="0.00">
                    </div>
                </div>
                <div>
                    <label class="label">Image</label>
                    <input type="file" name="image" accept="image/*" class="file-input">
                    @if($foodItem->image)
                    <p style="font-size: 12px; color: #94a3b8; margin-top: 8px;">Current image: {{ basename($foodItem->image) }}</p>
                    @endif
                </div>
                <div>
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; cursor: pointer;">
                        <input type="checkbox" name="is_active" value="1" {{ $foodItem->is_active ? 'checked' : '' }} style="width: 18px; height: 18px; accent-color: #10b981;">
                        Active
                    </label>
                </div>
                <div style="display: flex; gap: 16px; padding-top: 16px;">
                    <button type="submit" class="btn-primary">Update Food Item</button>
                    <a href="{{ route('admin.food-items.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@extends('admin.layouts.app')

@section('title', 'Add Location')
@section('page-title', 'Add Pickup Location')

@section('content')
<style>
    .form-card { background: linear-gradient(180deg, #ffffff, #f8fafc); border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); padding: 32px; }
    .input-field { background: linear-gradient(180deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 16px; font-size: 14px; transition: all 0.3s ease; width: 100%; box-sizing: border-box; }
    .input-field:focus { background: #ffffff; border-color: #10b981; box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1); outline: none; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .label { display: block; font-size: 14px; font-weight: 500; color: #475569; margin-bottom: 8px; }
    .checkbox-label { display: flex; align-items: center; gap: 8px; font-size: 14px; color: #64748b; cursor: pointer; }
    .checkbox-label input { width: 18px; height: 18px; accent-color: #10b981; }
    .btn-primary { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; font-weight: 600; padding: 14px 28px; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.35); transition: all 0.3s ease; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(16, 185, 129, 0.45); }
    .btn-secondary { background: linear-gradient(180deg, #f1f5f9, #e2e8f0); color: #64748b; font-weight: 600; padding: 14px 28px; border: 1px solid #e2e8f0; border-radius: 12px; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
    .btn-secondary:hover { background: #e2e8f0; }
    .required { color: #ef4444; }
</style>

<div style="max-width: 720px; margin: 0 auto;">
    <div class="form-card">
        <form method="POST" action="{{ route('admin.locations.store') }}">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <label class="label">Location Name <span class="required">*</span></label>
                    <input type="text" name="name" required placeholder="e.g., Lagos Branch" class="input-field">
                </div>

                <div>
                    <label class="label">Address <span class="required">*</span></label>
                    <textarea name="address" rows="3" required placeholder="Complete address..." class="input-field"></textarea>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="label">City <span class="required">*</span></label>
                        <input type="text" name="city" required placeholder="e.g., Lagos" class="input-field">
                    </div>
                    <div>
                        <label class="label">State <span class="required">*</span></label>
                        <input type="text" name="state" required placeholder="e.g., Lagos" class="input-field">
                    </div>
                </div>

                <div class="grid-2">
                    <div>
                        <label class="label">Phone</label>
                        <input type="text" name="phone" placeholder="+234 800 000 0000" class="input-field">
                    </div>
                    <div>
                        <label class="label">Email</label>
                        <input type="email" name="email" placeholder="location@example.com" class="input-field">
                    </div>
                </div>

                <div>
                    <label class="label">Operating Hours</label>
                    <input type="text" name="operating_hours" placeholder="e.g., Monday - Friday: 9:00 AM - 5:00 PM" class="input-field">
                </div>

                <label class="checkbox-label">
                    <input type="checkbox" name="is_headquarters" value="1">
                    <span>Mark as Headquarters</span>
                </label>

                <div style="display: flex; gap: 16px; padding-top: 16px; border-top: 1px solid #e2e8f0;">
                    <button type="submit" class="btn-primary">Create Location</button>
                    <a href="{{ route('admin.locations.index') }}" class="btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
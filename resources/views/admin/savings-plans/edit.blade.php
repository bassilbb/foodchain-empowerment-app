@extends('admin.layouts.app')

@section('title', 'Edit Savings Plan')
@section('header', 'Edit Savings Plan')
@section('subheader', 'Update savings plan and contribution options')

@section('content')
<div style="max-width: 56rem;">
    <form method="POST" action="{{ route('admin.savings-plans.update', $savingsPlan) }}" style="display: flex; flex-direction: column; gap: 1.5rem;">
        @csrf @method('PUT')

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Plan Details</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Plan Name</label>
                        <input type="text" name="name" value="{{ old('name', $savingsPlan->name) }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem;" required>
                        @error('name') <p style="color: #ef4444; font-size: 0.75rem; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Target Amount (₦)</label>
                        <input type="number" name="target_amount" value="{{ old('target_amount', $savingsPlan->target_amount) }}" step="0.01" min="1" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem;" required>
                        @error('target_amount') <p style="color: #ef4444; font-size: 0.75rem; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>
                    <div style="grid-column: 1 / -1;">
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Description</label>
                        <textarea name="description" rows="2" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem;">{{ old('description', $savingsPlan->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-card">
            <div class="page-card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 class="page-card-title">Contribution Options</h3>
                    <p class="page-card-subtitle">Manage duration options and weekly contributions</p>
                </div>
                <button type="button" onclick="addOption()" style="padding: 0.5rem 1rem; background: #f0fdf4; color: #10b981; font-weight: 600; border: 1px solid #d1fae5; border-radius: 8px; cursor: pointer; font-size: 0.8rem;">
                    + Add Option
                </button>
            </div>
            <div class="page-card-body" id="optionsContainer">
                @forelse($savingsPlan->options as $i => $option)
                <div class="option-row" style="display: grid; grid-template-columns: 1fr 1fr 40px; gap: 1rem; align-items: end; {{ !$loop->first ? 'margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;' : '' }}">
                    <input type="hidden" name="options[{{ $i }}][id]" value="{{ $option->id }}">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.375rem;">Duration (Weeks)</label>
                        <input type="number" name="options[{{ $i }}][duration_weeks]" value="{{ $option->duration_weeks }}" min="1" style="width: 100%; padding: 0.6rem 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.85rem;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.375rem;">Weekly Contribution (₦)</label>
                        <input type="number" name="options[{{ $i }}][weekly_contribution]" value="{{ $option->weekly_contribution }}" min="1" step="0.01" style="width: 100%; padding: 0.6rem 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.85rem;" required>
                    </div>
                    <div style="display: flex; align-items: flex-end; padding-bottom: 4px;">
                        <button type="button" onclick="this.closest('.option-row').remove()" style="width: 36px; height: 36px; border: none; background: #fee2e2; color: #ef4444; border-radius: 8px; cursor: pointer; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">&times;</button>
                    </div>
                </div>
                @empty
                <div class="option-row" style="display: grid; grid-template-columns: 1fr 1fr 40px; gap: 1rem; align-items: end;">
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.375rem;">Duration (Weeks)</label>
                        <input type="number" name="options[0][duration_weeks]" min="1" style="width: 100%; padding: 0.6rem 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.85rem;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.375rem;">Weekly Contribution (₦)</label>
                        <input type="number" name="options[0][weekly_contribution]" min="1" step="0.01" style="width: 100%; padding: 0.6rem 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.85rem;" required>
                    </div>
                    <div></div>
                </div>
                @endforelse
            </div>
            @error('options') <p style="color: #ef4444; font-size: 0.75rem; margin-top: 4px;">{{ $message }}</p> @enderror
        </div>

        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Status</h3>
            </div>
            <div class="page-card-body">
                <label style="display: flex; align-items: center; gap: 0.75rem;">
                    <input type="checkbox" name="is_active" value="1" {{ $savingsPlan->is_active ? 'checked' : '' }} style="width: 20px; height: 20px; border-radius: 6px; accent-color: #10b981;">
                    <span style="font-size: 0.875rem; font-weight: 500; color: #475569;">Active</span>
                </label>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.savings-plans.index') }}" style="padding: 0.75rem 1.5rem; color: #64748b; font-weight: 500; text-decoration: none;">Cancel</a>
            <button type="submit" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #10b981, #059669); color: white; font-weight: 600; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                Update Plan
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
let optionIndex = {{ count($savingsPlan->options) }};
function addOption() {
    const container = document.getElementById('optionsContainer');
    const row = document.createElement('div');
    row.className = 'option-row';
    row.style.cssText = 'display: grid; grid-template-columns: 1fr 1fr 40px; gap: 1rem; align-items: end; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f1f5f9;';
    row.innerHTML = `
        <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.375rem;">Duration (Weeks)</label>
            <input type="number" name="options[${optionIndex}][duration_weeks]" min="1" style="width: 100%; padding: 0.6rem 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.85rem;" required>
        </div>
        <div>
            <label style="display: block; font-size: 0.8rem; font-weight: 600; color: #475569; margin-bottom: 0.375rem;">Weekly Contribution (₦)</label>
            <input type="number" name="options[${optionIndex}][weekly_contribution]" min="1" step="0.01" style="width: 100%; padding: 0.6rem 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; font-size: 0.85rem;" required>
        </div>
        <div style="display: flex; align-items: flex-end; padding-bottom: 4px;">
            <button type="button" onclick="this.closest('.option-row').remove()" style="width: 36px; height: 36px; border: none; background: #fee2e2; color: #ef4444; border-radius: 8px; cursor: pointer; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">&times;</button>
        </div>`;
    container.appendChild(row);
    optionIndex++;
}
</script>
@endpush
@endsection

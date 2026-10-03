@extends('admin.layouts.app')

@section('title', 'Create Membership Tier')
@section('page-title', 'Create Membership Tier')

@section('header', 'Create Membership Tier')
@section('subheader', 'Add a new membership grade level')

@section('content')
<div style="max-width: 56rem;">
    <form method="POST" action="{{ route('admin.membership-tiers.store') }}" style="display: flex; flex-direction: column; gap: 1.5rem;">
        @csrf
        
        <!-- Basic Info -->
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Basic Information</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: repeat(1, 1fr); gap: 1.5rem; @media(min-width: 768px) { grid-template-columns: repeat(2, 1fr); }">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Tier Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" placeholder="e.g., Bronze" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Slug</label>
                        <input type="text" name="slug" value="{{ old('slug') }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" placeholder="e.g., bronze" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Minimum Referrals</label>
                        <input type="number" name="min_referrals" value="{{ old('min_referrals', 0) }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Pricing</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: repeat(1, 1fr); gap: 1.5rem; @media(min-width: 768px) { grid-template-columns: repeat(2, 1fr); }">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Annual Fee (₦)</label>
                        <input type="number" name="annual_fee" value="{{ old('annual_fee') }}" step="0.01" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Annual Fee (Kobo)</label>
                        <input type="number" name="annual_fee_kobo" value="{{ old('annual_fee_kobo') }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Instant Food Pack Price (₦)</label>
                        <input type="number" name="instant_food_pack_price" value="{{ old('instant_food_pack_price') }}" step="0.01" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Instant Food Pack Price (Kobo)</label>
                        <input type="number" name="instant_food_pack_price_kobo" value="{{ old('instant_food_pack_price_kobo') }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Community Settings -->
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Community Settings</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: repeat(1, 1fr); gap: 1.5rem; @media(min-width: 768px) { grid-template-columns: repeat(2, 1fr); }">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Community Min Members</label>
                        <input type="number" name="community_min_members" value="{{ old('community_min_members') }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Community Max Members</label>
                        <input type="number" name="community_max_members" value="{{ old('community_max_members') }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Free Food Pack Type</label>
                        <select name="free_food_pack_type" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                            <option value="mini">Mini</option>
                            <option value="average">Average</option>
                            <option value="standard">Standard</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Settings -->
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Delivery Settings</h3>
            </div>
            <div class="page-card-body">
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <label style="display: flex; align-items: center; gap: 0.75rem;">
                        <input type="checkbox" name="has_delivery" value="1" {{ old('has_delivery') ? 'checked' : '' }} style="width: 20px; height: 20px; border-radius: 6px; border: 1px solid #cbd5e1; accent-color: #10b981;">
                        <span style="font-size: 0.875rem; font-weight: 500; color: #475569;">Enable Delivery</span>
                    </label>
                    <div style="display: grid; grid-template-columns: repeat(1, 1fr); gap: 1.5rem; @media(min-width: 768px) { grid-template-columns: repeat(3, 1fr); }">
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Delivery Fee (₦)</label>
                            <input type="number" name="delivery_fee" value="{{ old('delivery_fee', 0) }}" step="0.01" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Subsidy Percentage</label>
                            <input type="number" name="delivery_subsidy_percentage" value="{{ old('delivery_subsidy_percentage', 0) }}" max="100" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;">
                        </div>
                        <div style="display: flex; align-items: flex-end;">
                            <label style="display: flex; align-items: center; gap: 0.75rem; padding-bottom: 0.75rem;">
                                <input type="checkbox" name="free_delivery" value="1" {{ old('free_delivery') ? 'checked' : '' }} style="width: 20px; height: 20px; border-radius: 6px; border: 1px solid #cbd5e1; accent-color: #10b981;">
                                <span style="font-size: 0.875rem; font-weight: 500; color: #475569;">Free Delivery</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Union Settings -->
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Union Settings</h3>
            </div>
            <div class="page-card-body">
                <div style="display: grid; grid-template-columns: repeat(1, 1fr); gap: 1.5rem; @media(min-width: 768px) { grid-template-columns: repeat(2, 1fr); }">
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Union Min Members</label>
                        <input type="number" name="union_min_members" value="{{ old('union_min_members') }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Union Equity Percentage</label>
                        <input type="number" name="union_equity_percentage" value="{{ old('union_equity_percentage') }}" max="100" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Loan Tenor (Months)</label>
                        <input type="number" name="union_loan_tenor_months" value="{{ old('union_loan_tenor_months') }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.875rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Repayment Frequency (Weeks)</label>
                        <input type="number" name="union_repayment_frequency_weeks" value="{{ old('union_repayment_frequency_weeks') }}" style="width: 100%; padding: 0.75rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; font-size: 0.875rem; transition: all 0.2s;" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="page-card">
            <div class="page-card-header">
                <h3 class="page-card-title">Status</h3>
            </div>
            <div class="page-card-body">
                <label style="display: flex; align-items: center; gap: 0.75rem;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} style="width: 20px; height: 20px; border-radius: 6px; border: 1px solid #cbd5e1; accent-color: #10b981;">
                    <span style="font-size: 0.875rem; font-weight: 500; color: #475569;">Active</span>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div style="display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="{{ route('admin.membership-tiers.index') }}" style="padding: 0.75rem 1.5rem; color: #64748b; font-weight: 500; text-decoration: none;">Cancel</a>
            <button type="submit" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #10b981, #059669); color: white; font-weight: 600; border: none; border-radius: 12px; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); transition: all 0.3s;">
                Create Tier
            </button>
        </div>
    </form>
</div>
@endsection

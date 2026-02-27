@extends('admin.layouts.app')

@section('title', 'Edit ' . $membershipTier->name)
@section('page-title', 'Edit Membership Tier')

@section('header', 'Edit Membership Tier')
@section('subheader', 'Update tier settings and benefits')

@section('content')
<div class="max-w-4xl">
    <form method="POST" action="{{ route('admin.membership-tiers.update', $membershipTier) }}" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Info -->
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tier Name</label>
                    <input type="text" name="name" value="{{ old('name', $membershipTier->name) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug', $membershipTier->slug) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Minimum Referrals</label>
                    <input type="number" name="min_referrals" value="{{ old('min_referrals', $membershipTier->min_referrals) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $membershipTier->sort_order) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white">
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Pricing</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Annual Fee (₦)</label>
                    <input type="number" name="annual_fee" value="{{ old('annual_fee', $membershipTier->annual_fee) }}" step="0.01" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                <div>
                    </div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Annual Fee (Kobo)</label>
                    <input type="number" name="annual_fee_kobo" value="{{ old('annual_fee_kobo', $membershipTier->annual_fee_kobo) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Instant Food Pack Price (₦)</label>
                    <input type="number" name="instant_food_pack_price" value="{{ old('instant_food_pack_price', $membershipTier->instant_food_pack_price) }}" step="0.01" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Instant Food Pack Price (Kobo)</label>
                    <input type="number" name="instant_food_pack_price_kobo" value="{{ old('instant_food_pack_price_kobo', $membershipTier->instant_food_pack_price_kobo) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
            </div>
        </div>

        <!-- Community Settings -->
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Community Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Community Min Members</label>
                    <input type="number" name="community_min_members" value="{{ old('community_min_members', $membershipTier->community_min_members) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Community Max Members</label>
                    <input type="number" name="community_max_members" value="{{ old('community_max_members', $membershipTier->community_max_members) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Free Food Pack Type</label>
                    <select name="free_food_pack_type" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                        <option value="mini" {{ old('free_food_pack_type', $membershipTier->free_food_pack_type) == 'mini' ? 'selected' : '' }}>Mini</option>
                        <option value="average" {{ old('free_food_pack_type', $membershipTier->free_food_pack_type) == 'average' ? 'selected' : '' }}>Average</option>
                        <option value="standard" {{ old('free_food_pack_type', $membershipTier->free_food_pack_type) == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="premium" {{ old('free_food_pack_type', $membershipTier->free_food_pack_type) == 'premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Delivery Settings -->
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Delivery Settings</h3>
            <div class="space-y-4">
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="has_delivery" value="1" {{ old('has_delivery', $membershipTier->has_delivery) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-primary-500 focus:ring-primary-500">
                    <span class="text-sm font-medium text-slate-700">Enable Delivery</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Delivery Fee (₦)</label>
                        <input type="number" name="delivery_fee" value="{{ old('delivery_fee', $membershipTier->delivery_fee) }}" step="0.01" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Subsidy Percentage</label>
                        <input type="number" name="delivery_subsidy_percentage" value="{{ old('delivery_subsidy_percentage', $membershipTier->delivery_subsidy_percentage) }}" max="100" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center gap-3 pb-3">
                            <input type="checkbox" name="free_delivery" value="1" {{ old('free_delivery', $membershipTier->free_delivery) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-primary-500 focus:ring-primary-500">
                            <span class="text-sm font-medium text-slate-700">Free Delivery</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Union Settings -->
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Union Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Union Min Members</label>
                    <input type="number" name="union_min_members" value="{{ old('union_min_members', $membershipTier->union_min_members) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Union Equity Percentage</label>
                    <input type="number" name="union_equity_percentage" value="{{ old('union_equity_percentage', $membershipTier->union_equity_percentage) }}" max="100" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Loan Tenor (Months)</label>
                    <input type="number" name="union_loan_tenor_months" value="{{ old('union_loan_tenor_months', $membershipTier->union_loan_tenor_months) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Repayment Frequency (Weeks)</label>
                    <input type="number" name="union_repayment_frequency_weeks" value="{{ old('union_repayment_frequency_weeks', $membershipTier->union_repayment_frequency_weeks) }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-primary-500 focus:bg-white" required>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="card-3d p-6">
            <h3 class="text-lg font-bold text-slate-800 mb-4">Status</h3>
            <label class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $membershipTier->is_active) ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-primary-500 focus:ring-primary-500">
                <span class="text-sm font-medium text-slate-700">Active</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <form method="POST" action="{{ route('admin.membership-tiers.destroy', $membershipTier) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-red-600 hover:text-red-700 font-medium" onclick="return confirm('Are you sure?')">
                    Delete Tier
                </button>
            </form>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.membership-tiers.index') }}" class="px-6 py-3 text-slate-600 font-medium hover:text-slate-800">Cancel</a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transform hover:-translate-y-0.5 transition-all">
                    Update Tier
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

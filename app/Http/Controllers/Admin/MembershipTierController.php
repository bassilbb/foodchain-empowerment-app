<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MembershipTierController extends Controller
{
    public function index()
    {
        $tiers = MembershipTier::ordered()->get();

        return view('admin.membership-tiers.index', compact('tiers'));
    }

    public function show(MembershipTier $membershipTier)
    {
        $stats = [
            'total_members' => $membershipTier->users()->count(),
            'active_members' => $membershipTier->users()->where('is_active', true)->count(),
            'total_unions' => $membershipTier->unions()->count(),
            'active_loans' => $membershipTier->unions()->whereIn('status', ['disbursed', 'repaying'])->count(),
        ];

        return view('admin.membership-tiers.show', compact('membershipTier', 'stats'));
    }

    public function create()
    {
        return view('admin.membership-tiers.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:membership_tiers,name',
            'slug' => 'required|string|max:50|unique:membership_tiers,slug|regex:/^[a-z0-9-]+$/',
            'min_referrals' => 'required|integer|min:0',
            'annual_fee' => 'required|numeric|min:0',
            'annual_fee_kobo' => 'required|integer|min:0',
            'instant_food_pack_price' => 'required|numeric|min:0',
            'instant_food_pack_price_kobo' => 'required|integer|min:0',
            'community_min_members' => 'required|integer|min:0',
            'community_max_members' => 'required|integer|min:0',
            'free_food_pack_type' => 'required|in:mini,average,standard,premium',
            'has_delivery' => 'boolean',
            'delivery_fee' => 'nullable|numeric|min:0',
            'delivery_subsidy_percentage' => 'nullable|numeric|min:0|max:100',
            'free_delivery' => 'boolean',
            'union_min_members' => 'required|integer|min:1',
            'union_equity_percentage' => 'required|numeric|min:0|max:100',
            'union_loan_tenor_months' => 'required|integer|min:1',
            'union_repayment_frequency_weeks' => 'required|integer|min:1',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['has_delivery'] = $request->boolean('has_delivery', true);
        $data['free_delivery'] = $request->boolean('free_delivery', false);
        $data['benefits'] = json_encode($request->input('benefits', []));
        $data['extended_benefits'] = json_encode($request->input('extended_benefits', []));

        MembershipTier::create($data);

        return redirect()->route('admin.membership-tiers.index')
            ->with('success', 'Membership tier created successfully');
    }

    public function edit(MembershipTier $membershipTier)
    {
        return view('admin.membership-tiers.edit', compact('membershipTier'));
    }

    public function update(Request $request, MembershipTier $membershipTier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:membership_tiers,name,'.$membershipTier->id,
            'slug' => 'required|string|max:50|regex:/^[a-z0-9-]+$/|unique:membership_tiers,slug,'.$membershipTier->id,
            'min_referrals' => 'required|integer|min:0',
            'annual_fee' => 'required|numeric|min:0',
            'annual_fee_kobo' => 'required|integer|min:0',
            'instant_food_pack_price' => 'required|numeric|min:0',
            'instant_food_pack_price_kobo' => 'required|integer|min:0',
            'community_min_members' => 'required|integer|min:0',
            'community_max_members' => 'required|integer|min:0',
            'free_food_pack_type' => 'required|in:mini,average,standard,premium',
            'has_delivery' => 'boolean',
            'delivery_fee' => 'nullable|numeric|min:0',
            'delivery_subsidy_percentage' => 'nullable|numeric|min:0|max:100',
            'free_delivery' => 'boolean',
            'union_min_members' => 'required|integer|min:1',
            'union_equity_percentage' => 'required|numeric|min:0|max:100',
            'union_loan_tenor_months' => 'required|integer|min:1',
            'union_repayment_frequency_weeks' => 'required|integer|min:1',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['has_delivery'] = $request->boolean('has_delivery', true);
        $data['free_delivery'] = $request->boolean('free_delivery', false);
        $data['benefits'] = json_encode($request->input('benefits', []));
        $data['extended_benefits'] = json_encode($request->input('extended_benefits', []));

        $membershipTier->update($data);

        return redirect()->route('admin.membership-tiers.show', $membershipTier->id)
            ->with('success', 'Membership tier updated successfully');
    }

    public function destroy(MembershipTier $membershipTier)
    {
        if ($membershipTier->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete tier with existing members');
        }

        $membershipTier->delete();

        return redirect()->route('admin.membership-tiers.index')
            ->with('success', 'Membership tier deleted successfully');
    }

    public function toggleStatus(MembershipTier $membershipTier)
    {
        $membershipTier->update(['is_active' => ! $membershipTier->is_active]);

        $status = $membershipTier->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "Tier {$status} successfully");
    }

    public function getPricing(Request $request)
    {
        $request->validate(['tier_id' => 'required|integer|exists:membership_tiers,id']);
        $tier = MembershipTier::findOrFail($request->tier_id);

        return response()->json([
            'annual_subscription' => [
                'amount' => (float) $tier->annual_fee,
                'amount_kobo' => (int) $tier->annual_fee_kobo,
            ],
            'instant_food_pack' => [
                'amount' => (float) $tier->instant_food_pack_price,
                'amount_kobo' => (int) $tier->instant_food_pack_price_kobo,
            ],
            'delivery' => [
                'fee' => (float) $tier->delivery_fee,
                'subsidy_percentage' => (float) $tier->delivery_subsidy_percentage,
                'is_free' => $tier->free_delivery,
            ],
        ]);
    }

    public function members(MembershipTier $membershipTier)
    {
        $members = $membershipTier->users()
            ->with(['currentTier', 'referrer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.membership-tiers.members', compact('membershipTier', 'members'));
    }

    public function unions(MembershipTier $membershipTier)
    {
        $unions = $membershipTier->unions()
            ->with(['leader', 'members'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.membership-tiers.unions', compact('membershipTier', 'unions'));
    }
}

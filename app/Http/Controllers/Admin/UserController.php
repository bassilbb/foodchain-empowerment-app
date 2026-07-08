<?php

namespace App\Http\Controllers\Admin;

use App\Exports\UsersExport;
use App\Http\Controllers\Controller;
use App\Models\MembershipTier;
use App\Models\User;
use App\Services\ReferralService;
use App\Services\TierProgressionService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('currentTier');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        // Filter by tier
        if ($request->filled('tier')) {
            $query->where('current_tier_id', $request->tier);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(20);
        $tiers = MembershipTier::ordered()->get();

        return view('admin.users.index', compact('users', 'tiers'));
    }

    public function show(User $user)
    {
        $user->load(['currentTier', 'referrer', 'payments', 'distributions', 'starterPack']);

        $referralService = app(ReferralService::class);
        $referralStats = $referralService->getReferralStats($user);
        $referralTree = $referralService->getReferralTree($user, 2);

        $tierHistory = app(TierProgressionService::class)->getTierHistory($user);
        $tiers = MembershipTier::ordered()->get();

        return view('admin.users.show', compact('user', 'referralStats', 'referralTree', 'tierHistory', 'tiers'));
    }

    public function referrals(User $user)
    {
        $referralService = app(ReferralService::class);
        $referralTree = $referralService->getReferralTree($user, 5);

        return view('admin.users.referrals', compact('user', 'referralTree'));
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);

        return back()->with('success', 'User activated successfully.');
    }

    public function deactivate(User $user)
    {
        $user->update(['is_active' => false]);

        return back()->with('success', 'User deactivated successfully.');
    }

    public function upgradeTier(Request $request, User $user)
    {
        $request->validate([
            'tier_id' => 'required|exists:membership_tiers,id',
        ]);

        $newTier = MembershipTier::findOrFail($request->tier_id);

        $user->update(['current_tier_id' => $newTier->id]);

        if ($newTier->isEmerald()) {
            $user->update(['membership_expires_at' => null]);
        }

        return back()->with('success', "User upgraded to {$newTier->name} tier successfully.");
    }

    public function updateTier(Request $request, User $user)
    {
        $request->validate([
            'tier_id' => 'required|exists:membership_tiers,id',
        ]);

        $newTier = MembershipTier::findOrFail($request->tier_id);
        $oldTier = $user->currentTier;

        $user->update(['current_tier_id' => $newTier->id]);

        if ($newTier->isEmerald()) {
            $user->update(['membership_expires_at' => null]);
        } elseif ($oldTier && $oldTier->isEmerald()) {
            $user->update(['membership_expires_at' => now()->addYear()]);
        }

        return back()->with('success', "User tier changed from {$oldTier->name} to {$newTier->name}.");
    }

    public function exportExcel()
    {
        return Excel::download(new UsersExport, 'users-'.date('Y-m-d').'.xlsx');
    }
}

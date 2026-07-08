<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\ClearanceRequest;
use App\Models\FoodDistribution;
use App\Models\FoodDistributionItem;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountClearanceController extends Controller
{
    public function index(Request $request)
    {
        $query = ClearanceRequest::with('userAccount.savingsPlanOption.plan', 'user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('userAccount', function ($q) use ($search) {
                $q->where('account_name', 'like', "%{$search}%");
            });
        }

        $clearanceRequests = $query->latest()->paginate(20);

        $stats = [
            'pending_payment' => ClearanceRequest::where('status', 'pending_payment')->count(),
            'pending_approval' => ClearanceRequest::where('status', 'pending_approval')->count(),
            'approved' => ClearanceRequest::where('status', 'approved')->count(),
            'total_revenue' => ClearanceRequest::where('status', 'approved')->sum('fee_amount'),
        ];

        return view('admin.clearances.index', compact('clearanceRequests', 'stats'));
    }

    public function show(ClearanceRequest $clearance)
    {
        $clearance->load('userAccount.savingsPlanOption.plan', 'user', 'approvedBy');

        return view('admin.clearances.show', compact('clearance'));
    }

    public function approve(ClearanceRequest $clearance)
    {
        if ($clearance->status !== 'pending_approval') {
            return back()->with('error', 'Only pending approval requests can be approved.');
        }

        DB::transaction(function () use ($clearance) {
            $account = $clearance->userAccount;
            $payoutAmount = $account->available_balance;
            $user = $clearance->user;
            $adminId = auth()->guard('admin')->id();

            $clearance->update([
                'status' => 'approved',
                'approved_by' => $adminId,
                'approved_at' => now(),
            ]);

            $account->update([
                'status' => 'completed',
                'available_balance' => 0,
            ]);

            AccountTransaction::create([
                'primary_user_id' => $account->primary_user_id,
                'user_account_id' => $account->id,
                'amount' => $payoutAmount,
                'type' => 'refund',
                'description' => 'Account clearance payout - '.$account->account_name,
            ]);

            AccountTransaction::create([
                'primary_user_id' => $account->primary_user_id,
                'user_account_id' => $account->id,
                'amount' => $clearance->fee_amount,
                'type' => 'penalty',
                'description' => 'Clearance fee - '.$account->account_name,
            ]);

            $this->createDistributionAndVoucher($user, $clearance, $account, $adminId);
        });

        return redirect()->route('admin.clearances.index')
            ->with('success', 'Clearance request approved. Distribution and voucher created.');
    }

    public function batchApprove(Request $request)
    {
        $request->validate([
            'clearance_ids' => 'required|array',
            'clearance_ids.*' => 'exists:clearance_requests,id',
        ]);

        $clearances = ClearanceRequest::with('userAccount', 'user')
            ->whereIn('id', $request->clearance_ids)
            ->where('status', 'pending_approval')
            ->get();

        if ($clearances->isEmpty()) {
            return back()->with('error', 'No pending approval clearances found.');
        }

        $adminId = auth()->guard('admin')->id();

        DB::transaction(function () use ($clearances, $adminId) {
            $grouped = $clearances->groupBy('user_id');

            foreach ($grouped as $userId => $userClearances) {
                $user = $userClearances->first()->user;
                $totalPayout = 0;

                foreach ($userClearances as $clearance) {
                    $account = $clearance->userAccount;
                    $payoutAmount = $account->available_balance;
                    $totalPayout += $payoutAmount;

                    $clearance->update([
                        'status' => 'approved',
                        'approved_by' => $adminId,
                        'approved_at' => now(),
                    ]);

                    $account->update([
                        'status' => 'completed',
                        'available_balance' => 0,
                    ]);

                    AccountTransaction::create([
                        'primary_user_id' => $account->primary_user_id,
                        'user_account_id' => $account->id,
                        'amount' => $payoutAmount,
                        'type' => 'refund',
                        'description' => 'Account clearance payout - '.$account->account_name,
                    ]);

                    AccountTransaction::create([
                        'primary_user_id' => $account->primary_user_id,
                        'user_account_id' => $account->id,
                        'amount' => $clearance->fee_amount,
                        'type' => 'penalty',
                        'description' => 'Clearance fee - '.$account->account_name,
                    ]);
                }

                // Create one distribution + voucher per user
                $this->createDistributionAndVoucher(
                    $user, $userClearances->first(), null, $adminId,
                    count($userClearances).' account(s) cleared',
                    $totalPayout
                );
            }
        });

        return redirect()->route('admin.clearances.index')
            ->with('success', count($clearances).' clearance(s) approved. One distribution and voucher created.');
    }

    private function createDistributionAndVoucher(
        $user, $clearance, $account = null, $adminId = null,
        $notes = null, $overrideValue = null
    ) {
        $tier = $user->currentTier;
        $package = $tier ? $tier->getDefaultPackage() : null;
        $pickupLocation = \App\Models\PickupLocation::first();

        $packageValue = min(
            $overrideValue ?? ($package?->total_value ?? $tier?->food_package_value ?? 0),
            $tier?->food_package_value ?? PHP_FLOAT_MAX
        );

        $distribution = FoodDistribution::create([
            'user_id' => $user->id,
            'tier_id' => $tier?->id,
            'package_id' => $package?->id,
            'distribution_type' => 'tier_reward',
            'package_value' => $packageValue,
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => now(),
            'pickup_location_id' => $pickupLocation?->id,
            'admin_notes' => $notes ?? 'Auto-created from clearance approval',
            'scheduled_date' => now(),
        ]);

        // Add items from package if available
        if ($package && $package->items->isNotEmpty()) {
            foreach ($package->items as $packageItem) {
                FoodDistributionItem::create([
                    'distribution_id' => $distribution->id,
                    'food_item_id' => $packageItem->food_item_id,
                    'quantity' => $packageItem->quantity,
                    'unit_value' => $packageItem->unit_value,
                ]);
            }
        }

        // Create voucher linked to distribution
        $voucher = Voucher::create([
            'user_id' => $user->id,
            'food_distribution_id' => $distribution->id,
            'voucher_number' => Voucher::generateVoucherNumber(),
            'status' => 'issued',
            'issued_at' => now(),
            'collection_code' => strtoupper(substr(md5(uniqid()), 0, 8)),
            'expires_at' => now()->addHours((int) \App\Models\Setting::get('voucher_validity_hours', 72)),
            'notes' => $notes,
        ]);

        return $distribution;
    }

    public function reject(Request $request, ClearanceRequest $clearance)
    {
        if ($clearance->status !== 'pending_approval') {
            return back()->with('error', 'Only pending approval requests can be rejected.');
        }

        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $clearance->update([
            'status' => 'pending_payment',
            'admin_notes' => $request->admin_notes,
        ]);

        return redirect()->route('admin.clearances.index')
            ->with('success', 'Clearance request rejected. User can retry.');
    }
}

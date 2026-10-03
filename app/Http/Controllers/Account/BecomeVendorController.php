<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Vendor;
use App\Models\NigerianState;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Become-a-vendor application flow.
 *
 * A member without a vendor record applies with store details and is granted a
 * pending Vendor row. The existing `EnsureVendor` gate already redirects a
 * pending vendor to `account.dashboard` with a flash, so once applied the user
 * simply waits for admin approval (admin approves from the vendor list).
 */
class BecomeVendorController extends Controller
{
    public function create()
    {
        $user = auth()->user();

        // Query the relation afresh - a relation cached from an earlier request
        // (or test) would let an applicant see the form again after applying.
        $existing = $user->marketplaceVendor()->first();

        if ($existing && $existing->isApproved()) {
            return redirect()->route('vendor.dashboard')
                ->with('info', 'You already have an active vendor store.');
        }

        if ($existing && $existing->isPending()) {
            return redirect()->route('account.dashboard')
                ->with('info', 'Your vendor application is still awaiting review.');
        }

        return view('account.become-vendor', [
            'states' => NigerianState::active()->ordered()->get(['id', 'name']),
            'user' => $user,
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->marketplaceVendor()->exists()) {
            return back()->with('error', 'You already have a vendor application on file.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'business_name' => 'required|string|max:120',
            'email' => 'required|email|max:190',
            'phone' => 'required|string|max:30',
            'description' => 'nullable|string|max:2000',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:120',
            'state_id' => 'nullable|integer|exists:nigerian_states,id',
        ]);

        $state = ! empty($data['state_id']) ? NigerianState::find($data['state_id']) : null;

        Vendor::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'business_name' => $data['business_name'],
            'slug' => Str::slug($data['business_name']).'-'.strtolower(substr(uniqid(), -4)),
            'email' => $data['email'],
            'phone' => $data['phone'],
            'description' => $data['description'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $state?->name,
            'status' => 'pending',
            // commission_rate intentionally omitted: the schema default (10.00)
            // applies until an admin tunes it for the approved store.
            'wallet_balance' => 0,
        ]);

        return redirect()->route('account.dashboard')
            ->with('success', 'Your vendor application has been submitted. We will review it shortly.');
    }
}
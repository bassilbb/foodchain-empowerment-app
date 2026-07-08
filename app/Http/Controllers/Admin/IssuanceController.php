<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FoodDistribution;
use App\Models\Voucher;
use Illuminate\Http\Request;

class IssuanceController extends Controller
{
    public function index()
    {
        return view('admin.issuance.index', ['voucher' => null, 'distributions' => collect()]);
    }

    public function lookup(Request $request)
    {
        $search = $request->input('search');

        if (!$search) {
            return redirect()->route('admin.issuance.index');
        }

        $voucher = Voucher::with([
            'user',
            'distribution.items.foodItem',
            'distribution.pickupLocation',
            'distribution.tier',
            'collector',
        ])
            ->where('voucher_number', $search)
            ->orWhere('collection_code', $search)
            ->first();

        if (!$voucher) {
            return redirect()->route('admin.issuance.index')
                ->with('error', 'No voucher found with that number or collection code.');
        }

        $distributions = FoodDistribution::where('status', 'approved')
            ->where('user_id', $voucher->user_id)
            ->with('items.foodItem', 'pickupLocation')
            ->latest()
            ->get();

        return view('admin.issuance.index', compact('voucher', 'distributions'));
    }

    public function linkDistribution(Request $request, Voucher $voucher)
    {
        $request->validate([
            'food_distribution_id' => 'required|exists:food_distributions,id',
        ]);

        $voucher->update([
            'food_distribution_id' => $request->food_distribution_id,
        ]);

        $voucher->load('distribution.items.foodItem', 'distribution.pickupLocation');

        return redirect()->route('admin.issuance.lookup', ['search' => $voucher->collection_code ?? $voucher->voucher_number])
            ->with('success', 'Voucher linked to distribution successfully.');
    }

    public function collect(Request $request, Voucher $voucher)
    {
        if (!$voucher->isIssued()) {
            return back()->with('error', 'Only issued vouchers can be collected.');
        }

        $adminId = auth()->guard('admin')->id();
        $voucher->collect($adminId, $request->notes);

        return redirect()->route('admin.issuance.index')
            ->with('success', 'Voucher collected successfully.');
    }
}

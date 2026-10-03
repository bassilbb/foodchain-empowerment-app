<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Voucher;

class VoucherController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $vouchers = $user->vouchers()
            ->with('distribution.items.foodItem', 'distribution.tier', 'distribution.package')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.vouchers.index', compact('vouchers'));
    }

    public function show(Voucher $voucher)
    {
        $user = auth()->user();

        if ($voucher->user_id !== $user->id) {
            abort(403);
        }

        $voucher->load('distribution.items.foodItem', 'distribution.pickupLocation', 'distribution.tier', 'distribution.package');

        return view('user.vouchers.show', compact('voucher'));
    }

    public function acknowledge(Voucher $voucher)
    {
        $user = auth()->user();

        if ($voucher->user_id !== $user->id) {
            abort(403);
        }

        if (! $voucher->isIssued()) {
            return back()->with('error', 'Only issued vouchers can be acknowledged.');
        }

        if ($voucher->isAcknowledged()) {
            return back()->with('info', 'This voucher has already been acknowledged.');
        }

        $voucher->acknowledge();

        return back()->with('success', 'Voucher acknowledged successfully. Please proceed to the pickup location for collection.');
    }
}

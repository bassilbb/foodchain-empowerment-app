<?php
// app/Http/Controllers/User/ReferralController.php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $referralService = app(ReferralService::class);

        $referralStats = $referralService->getReferralStats($user);
        $referrals = $user->referrals()->with('currentTier')->latest()->paginate(20);

        return view('user.referrals.index', compact('user', 'referralStats', 'referrals'));
    }

    public function tree()
    {
        $user = auth()->user();
        $referralService = app(ReferralService::class);

        $referralTree = $referralService->getReferralTree($user, 3);
        $referralStats = $referralService->getReferralStats($user);

        return view('user.referrals.tree', compact('user', 'referralTree', 'referralStats'));
    }
}

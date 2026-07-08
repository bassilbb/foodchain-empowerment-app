<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\QuarterlyBenefitEligibility;
use App\Services\MembershipGradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuarterlyBenefitController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $benefits = QuarterlyBenefitEligibility::where('user_id', $user->id)
            ->with('tier')
            ->orderBy('year', 'desc')
            ->orderBy('quarter', 'desc')
            ->get();

        $currentQuarter = QuarterlyBenefitEligibility::currentQuarter()
            ->where('user_id', $user->id)
            ->with('tier')
            ->first();

        return view('user.quarterly-benefits.index', compact('benefits', 'currentQuarter'));
    }

    public function show(string $id)
    {
        $user = Auth::user();
        
        $benefit = QuarterlyBenefitEligibility::where('user_id', $user->id)
            ->with('tier')
            ->findOrFail($id);

        return view('user.quarterly-benefits.show', compact('benefit'));
    }

    public function claim(Request $request, string $id)
    {
        $user = Auth::user();
        
        $benefit = QuarterlyBenefitEligibility::where('user_id', $user->id)
            ->findOrFail($id);

        if (!$benefit->canClaim()) {
            return back()->with('error', 'This benefit cannot be claimed at this time.');
        }

        try {
            $benefit->claim();
            
            return redirect()->route('quarterly-benefits.show', $benefit->id)
                ->with('success', 'Quarterly benefit claimed successfully!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function getEligibility()
    {
        $user = Auth::user();
        
        $membershipService = app(MembershipGradeService::class);
        $eligibility = $membershipService->checkBenefitEligibility($user);

        return response()->json($eligibility);
    }
}

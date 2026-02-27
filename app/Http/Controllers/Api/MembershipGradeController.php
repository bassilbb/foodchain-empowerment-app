<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MembershipGradeService;
use App\Services\DeliveryService;
use App\Models\MembershipTier;
use App\Models\User;
use App\Models\QuarterlyBenefitEligibility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MembershipGradeController extends Controller
{
    public function __construct(
        private MembershipGradeService $membershipService,
        private DeliveryService $deliveryService
    ) {}

    public function index(): JsonResponse
    {
        $tiers = MembershipTier::active()->ordered()->get();
        
        $grades = $tiers->map(function ($tier) {
            return $this->membershipService->getFullGradeDetails($tier);
        });

        return response()->json([
            'success' => true,
            'data' => $grades,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $tier = MembershipTier::bySlug($slug)->first();
        
        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'Membership grade not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->membershipService->getFullGradeDetails($tier),
        ]);
    }

    public function userMembership(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $summary = $this->membershipService->getUserMembershipSummary($user);

        return response()->json([
            'success' => true,
            'data' => $summary,
        ]);
    }

    public function checkBenefitEligibility(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $eligibility = $this->membershipService->checkBenefitEligibility($user);

        return response()->json([
            'success' => true,
            'data' => $eligibility,
        ]);
    }

    public function getDeliveryOptions(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $options = $this->deliveryService->getDeliveryOptions($user);

        return response()->json([
            'success' => true,
            'data' => $options,
        ]);
    }

    public function calculateDeliveryFee(Request $request): JsonResponse
    {
        $request->validate([
            'base_fee' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();
        $baseFee = $request->input('base_fee', 1500);
        
        $feeDetails = $this->deliveryService->calculateDeliveryFee($user, $baseFee);

        return response()->json([
            'success' => true,
            'data' => $feeDetails,
        ]);
    }

    public function getQuarterlyBenefits(Request $request): JsonResponse
    {
        $user = $request->user();
        $quarter = $request->input('quarter', now()->quarter);
        $year = $request->input('year', now()->year);

        $benefits = QuarterlyBenefitEligibility::forQuarter($quarter, $year)
            ->where('user_id', $user->id)
            ->with('tier')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $benefits,
        ]);
    }

    public function claimQuarterlyBenefit(Request $request): JsonResponse
    {
        $request->validate([
            'eligibility_id' => 'required|integer|exists:quarterly_benefit_eligibilities,id',
        ]);

        $eligibility = QuarterlyBenefitEligibility::find($request->input('eligibility_id'));

        if (!$eligibility->canClaim()) {
            return response()->json([
                'success' => false,
                'message' => 'This benefit cannot be claimed',
            ], 400);
        }

        $eligibility->claim();

        return response()->json([
            'success' => true,
            'message' => 'Benefit claimed successfully',
            'data' => $eligibility,
        ]);
    }

    public function getGradePricing(string $slug): JsonResponse
    {
        $tier = MembershipTier::bySlug($slug)->first();
        
        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'Membership grade not found',
            ], 404);
        }

        $pricing = $this->membershipService->getGradePricing($tier);

        return response()->json([
            'success' => true,
            'data' => $pricing,
        ]);
    }

    public function getDeliveryPricing(string $slug): JsonResponse
    {
        $tier = MembershipTier::bySlug($slug)->first();
        
        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'Membership grade not found',
            ], 404);
        }

        $pricing = $this->membershipService->getDeliveryPricing($tier);

        return response()->json([
            'success' => true,
            'data' => $pricing,
        ]);
    }
}

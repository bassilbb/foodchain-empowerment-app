<?php

namespace App\Http\Middleware;

use App\Models\MembershipTier;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckGradeAccess
{
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $tier = $user->currentTier;
        
        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'No membership tier assigned',
            ], 403);
        }

        $access = $this->checkFeatureAccess($tier, $feature);
        
        if (!$access['allowed']) {
            return response()->json([
                'success' => false,
                'message' => $access['reason'],
                'required_grade' => $access['required_grade'] ?? null,
            ], 403);
        }

        return $next($request);
    }

    private function checkFeatureAccess(MembershipTier $tier, string $feature): array
    {
        $accessRules = [
            'free_delivery' => [
                'allowed' => $tier->hasFreeDelivery(),
                'required_grade' => 'emerald',
                'reason' => 'Free delivery is only available for Emerald members',
            ],
            'subsidized_delivery' => [
                'allowed' => $tier->hasSubsidizedDelivery() || $tier->hasFreeDelivery(),
                'required_grade' => 'gold',
                'reason' => 'Subsidized delivery is only available for Gold and Emerald members',
            ],
            'premium_food_pack' => [
                'allowed' => in_array($tier->slug, ['gold', 'emerald']),
                'required_grade' => 'gold',
                'reason' => 'Premium food packs are only available for Gold and Emerald members',
            ],
            'quarterly_benefit' => [
                'allowed' => true,
                'reason' => 'All members can qualify for quarterly benefits based on community size',
            ],
            'food_loan' => [
                'allowed' => true,
                'reason' => 'All grades have access to food loans',
            ],
            'vip_support' => [
                'allowed' => in_array($tier->slug, ['gold', 'emerald']),
                'required_grade' => 'gold',
                'reason' => 'VIP support is only available for Gold and Emerald members',
            ],
            'community_leader' => [
                'allowed' => $tier->slug === 'emerald',
                'required_grade' => 'emerald',
                'reason' => 'Community Leader status is only available for Emerald members',
            ],
        ];

        return $accessRules[$feature] ?? [
            'allowed' => false,
            'reason' => 'Unknown feature: ' . $feature,
        ];
    }
}

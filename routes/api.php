<?php

use App\Http\Controllers\Api\IncidentApiController;
use App\Http\Controllers\Api\MembershipGradeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Public routes - Membership Grades
    Route::get('/membership-grades', [MembershipGradeController::class, 'index']);
    Route::get('/membership-grades/{slug}', [MembershipGradeController::class, 'show']);
    Route::get('/membership-grades/{slug}/pricing', [MembershipGradeController::class, 'getGradePricing']);
    Route::get('/membership-grades/{slug}/delivery', [MembershipGradeController::class, 'getDeliveryPricing']);

    // Protected routes - Require authentication
    Route::middleware('auth:sanctum')->group(function () {

        // User membership endpoints
        Route::get('/my/membership', [MembershipGradeController::class, 'userMembership']);
        Route::get('/my/benefits/eligibility', [MembershipGradeController::class, 'checkBenefitEligibility']);
        Route::get('/my/delivery/options', [MembershipGradeController::class, 'getDeliveryOptions']);
        Route::get('/my/delivery/fee', [MembershipGradeController::class, 'calculateDeliveryFee']);
        Route::get('/my/quarterly-benefits', [MembershipGradeController::class, 'getQuarterlyBenefits']);
        Route::post('/my/quarterly-benefits/claim', [MembershipGradeController::class, 'claimQuarterlyBenefit']);
    });
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Incident API routes for cascading dropdowns
Route::prefix('v1/incidents')->group(function () {
    Route::get('/branches/{branchId}/departments', [IncidentApiController::class, 'getDepartments']);
    Route::get('/departments/{departmentId}/staff', [IncidentApiController::class, 'getStaff']);
});

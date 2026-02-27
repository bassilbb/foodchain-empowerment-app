<?php
// app/Http/Controllers/Admin/ReportController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\FoodDistribution;
use App\Models\FoodItem;
use App\Models\MembershipTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function overview(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonths(6)->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // User growth
        $userGrowth = User::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Revenue trend
        $revenueTrend = Payment::successful()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Distribution trend
        $distributionTrend = FoodDistribution::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(package_value) as total_value')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports.overview', compact(
            'userGrowth',
            'revenueTrend',
            'distributionTrend',
            'startDate',
            'endDate'
        ));
    }

    public function users(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Total users
        $totalUsers = User::count();
        $activeUsers = User::active()->count();
        $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();

        // Users by tier
        $usersByTier = User::select('current_tier_id', DB::raw('COUNT(*) as count'))
            ->with('currentTier')
            ->groupBy('current_tier_id')
            ->get();

        // Top referrers
        $topReferrers = User::orderBy('direct_referrals_count', 'desc')
            ->limit(10)
            ->get();

        // Users by state
        $usersByState = User::select('state', DB::raw('COUNT(*) as count'))
            ->whereNotNull('state')
            ->groupBy('state')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.users', compact(
            'totalUsers',
            'activeUsers',
            'newUsers',
            'usersByTier',
            'topReferrers',
            'usersByState',
            'startDate',
            'endDate'
        ));
    }

    public function payments(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Payment summary
        $totalRevenue = Payment::successful()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $membershipRevenue = Payment::successful()
            ->membership()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        $starterPackRevenue = Payment::successful()
            ->starterPack()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Payment status breakdown
        $paymentsByStatus = Payment::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as total'))
            ->groupBy('status')
            ->get();

        // Daily revenue
        $dailyRevenue = Payment::successful()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(amount) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports.payments', compact(
            'totalRevenue',
            'membershipRevenue',
            'starterPackRevenue',
            'paymentsByStatus',
            'dailyRevenue',
            'startDate',
            'endDate'
        ));
    }

    public function inventory()
    {
        // Current stock levels
        $foodItems = FoodItem::with('inventory')->active()->get();

        $totalInventoryValue = $foodItems->sum(function ($item) {
            return $item->getTotalStockValue();
        });

        // Low stock items
        $lowStockItems = $foodItems->filter(function ($item) {
            return $item->isLowStock();
        });

        // Recent transactions
        $recentTransactions = \App\Models\InventoryTransaction::with(['foodItem', 'location', 'performedBy'])
            ->latest()
            ->limit(50)
            ->get();

        return view('admin.reports.inventory', compact(
            'foodItems',
            'totalInventoryValue',
            'lowStockItems',
            'recentTransactions'
        ));
    }

    public function distributions(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        // Distribution summary
        $totalDistributions = FoodDistribution::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalValue = FoodDistribution::whereBetween('created_at', [$startDate, $endDate])->sum('package_value');

        // By status
        $distributionsByStatus = FoodDistribution::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // By tier
        $distributionsByTier = FoodDistribution::whereBetween('created_at', [$startDate, $endDate])
            ->with('tier')
            ->select('tier_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(package_value) as total_value'))
            ->groupBy('tier_id')
            ->get();

        // By type
        $distributionsByType = FoodDistribution::whereBetween('created_at', [$startDate, $endDate])
            ->select('distribution_type', DB::raw('COUNT(*) as count'))
            ->groupBy('distribution_type')
            ->get();

        return view('admin.reports.distributions', compact(
            'totalDistributions',
            'totalValue',
            'distributionsByStatus',
            'distributionsByTier',
            'distributionsByType',
            'startDate',
            'endDate'
        ));
    }
}

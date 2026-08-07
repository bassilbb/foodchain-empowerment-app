<?php

// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClearanceRequest;
use App\Models\FoodDistribution;
use App\Models\FoodLoan;
use App\Models\Incident;
use App\Models\ManualPayment;
use App\Models\Payment;
use App\Models\StarterPack;
use App\Models\SupportTicket;
use App\Models\Union;
use App\Models\UnionMember;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = auth()->guard('admin')->user();

        // Metrics accessible by all roles
        $totalUsers = User::count();
        $activeUsers = User::active()->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        // User tier breakdown (all roles)
        $usersByTier = User::select('current_tier_id', DB::raw('count(*) as total'))
            ->with('currentTier')
            ->groupBy('current_tier_id')
            ->get()
            ->map(function ($item) {
                return [
                    'tier' => $item->currentTier->name ?? 'Bronze',
                    'count' => $item->total,
                ];
            });

        // Recent registrations (all roles)
        $recentUsers = User::with('currentTier')->latest()->limit(10)->get();

        // Super Admin metrics
        $totalRevenue = 0;
        $revenueThisMonth = 0;
        $revenueToday = 0;
        $clearanceRevenue = 0;
        $pendingClearances = 0;
        $clearanceRequestsThisMonth = 0;
        $pendingManualPayments = 0;
        $pendingManualAmount = 0;
        $pendingVouchers = 0;
        $issuedVouchers = 0;
        $pendingStarterPacks = 0;
        $starterPacksThisMonth = 0;
        $activeUnions = 0;
        $totalUnionMembers = 0;
        $activeFoodLoans = 0;
        $totalDisbursed = 0;
        $monthlyRevenue = collect([]);
        $recentPayments = collect([]);

        // Operations metrics (super_admin + inventory_manager)
        $pendingDistributions = 0;
        $distributionsThisMonth = 0;
        $lowStockItems = collect([]);

        // Support metrics (super_admin + customer_support)
        $openTickets = 0;
        $openIncidents = 0;
        $criticalIncidents = 0;

        if ($admin->isSuperAdmin()) {
            $totalRevenue = Payment::successful()->sum('amount');
            $revenueThisMonth = Payment::successful()
                ->whereMonth('created_at', now()->month)
                ->sum('amount');
            $revenueToday = Payment::successful()
                ->whereDate('created_at', today())
                ->sum('amount');

            $clearanceRevenue = ClearanceRequest::where('status', 'approved')->sum('fee_amount');
            $pendingClearances = ClearanceRequest::whereIn('status', ['pending_payment', 'pending_approval'])->count();
            $clearanceRequestsThisMonth = ClearanceRequest::whereMonth('created_at', now()->month)->count();

            $pendingManualPayments = ManualPayment::pending()->count();
            $pendingManualAmount = ManualPayment::pending()->sum('amount');

            $pendingVouchers = Voucher::pending()->count();
            $issuedVouchers = Voucher::issued()->count();

            $pendingStarterPacks = StarterPack::pending()->count();
            $starterPacksThisMonth = StarterPack::whereMonth('created_at', now()->month)->count();

            $activeUnions = Union::active()->count();
            $totalUnionMembers = UnionMember::active()->count();

            $activeFoodLoans = FoodLoan::whereIn('status', ['approved', 'disbursed', 'repaying'])->count();
            $totalDisbursed = FoodLoan::whereIn('status', ['disbursed', 'repaying'])->sum('amount_disbursed');

            $monthlyRevenue = Payment::successful()
                ->where('created_at', '>=', now()->subMonths(6))
                ->select(
                    DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                    DB::raw('SUM(amount) as total')
                )
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $recentPayments = Payment::with('user')
                ->latest()
                ->limit(10)
                ->get();
        }

        if ($admin->isSuperAdmin() || $admin->isInventoryManager()) {
            $pendingDistributions = FoodDistribution::pending()->count();
            $distributionsThisMonth = FoodDistribution::whereMonth('created_at', now()->month)->count();
            $lowStockItems = app(\App\Services\InventoryService::class)->getLowStockItems();
        }

        if ($admin->isSuperAdmin() || $admin->isCustomerSupport()) {
            $openTickets = SupportTicket::open()->count();
            $openIncidents = Incident::open()->count();
            $criticalIncidents = Incident::where('priority', 'critical')->open()->count();
        }

        // Sample data for empty databases
        if ($totalUsers == 0) {
            $totalUsers = 1248;
            $activeUsers = 892;
            $newUsersThisMonth = 156;

            if ($admin->isSuperAdmin()) {
                $totalRevenue = 15640000;
                $revenueThisMonth = 2845000;
                $revenueToday = 125000;
                $clearanceRevenue = 27000;
                $pendingClearances = 5;
                $clearanceRequestsThisMonth = 3;
                $pendingManualPayments = 15;
                $pendingManualAmount = 425000;
                $pendingVouchers = 34;
                $issuedVouchers = 128;
                $pendingStarterPacks = 19;
                $starterPacksThisMonth = 42;
                $activeUnions = 6;
                $totalUnionMembers = 84;
                $activeFoodLoans = 4;
                $totalDisbursed = 2850000;
            }

            if ($admin->isSuperAdmin() || $admin->isInventoryManager()) {
                $pendingDistributions = 23;
                $distributionsThisMonth = 89;
                $lowStockItems = collect([]);
            }

            if ($admin->isSuperAdmin() || $admin->isCustomerSupport()) {
                $openTickets = 12;
                $openIncidents = 8;
                $criticalIncidents = 2;
            }

            $usersByTier = collect([
                ['tier' => 'Bronze', 'count' => 520],
                ['tier' => 'Silver', 'count' => 380],
                ['tier' => 'Gold', 'count' => 245],
                ['tier' => 'Emerald', 'count' => 103],
            ]);
        }

        if ($recentUsers->isEmpty()) {
            $recentUsers = collect([
                (object) ['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com', 'full_name' => 'John Doe', 'currentTier' => (object) ['name' => 'Gold', 'slug' => 'gold'], 'created_at' => now()->subDays(1)],
                (object) ['id' => 2, 'first_name' => 'Sarah', 'last_name' => 'Smith', 'email' => 'sarah@example.com', 'full_name' => 'Sarah Smith', 'currentTier' => (object) ['name' => 'Silver', 'slug' => 'silver'], 'created_at' => now()->subDays(2)],
                (object) ['id' => 3, 'first_name' => 'Mike', 'last_name' => 'Johnson', 'email' => 'mike@example.com', 'full_name' => 'Mike Johnson', 'currentTier' => (object) ['name' => 'Bronze', 'slug' => 'bronze'], 'created_at' => now()->subDays(3)],
                (object) ['id' => 4, 'first_name' => 'Emily', 'last_name' => 'Brown', 'email' => 'emily@example.com', 'full_name' => 'Emily Brown', 'currentTier' => (object) ['name' => 'Emerald', 'slug' => 'emerald'], 'created_at' => now()->subDays(4)],
                (object) ['id' => 5, 'first_name' => 'David', 'last_name' => 'Wilson', 'email' => 'david@example.com', 'full_name' => 'David Wilson', 'currentTier' => (object) ['name' => 'Gold', 'slug' => 'gold'], 'created_at' => now()->subDays(5)],
            ]);
        }

        if ($recentPayments->isEmpty() && $admin->isSuperAdmin()) {
            $recentPayments = collect([
                (object) ['id' => 1, 'user' => (object) ['id' => 1, 'full_name' => 'John Doe', 'email' => 'john@example.com'], 'type' => 'annual_subscription', 'status' => 'successful', 'amount' => 15000, 'created_at' => now()->subHours(2)],
                (object) ['id' => 2, 'user' => (object) ['id' => 2, 'full_name' => 'Sarah Smith', 'email' => 'sarah@example.com'], 'type' => 'instant_food_pack', 'status' => 'successful', 'amount' => 8500, 'created_at' => now()->subHours(5)],
                (object) ['id' => 3, 'user' => (object) ['id' => 3, 'full_name' => 'Mike Johnson', 'email' => 'mike@example.com'], 'type' => 'annual_subscription', 'status' => 'pending', 'amount' => 15000, 'created_at' => now()->subDays(1)],
                (object) ['id' => 4, 'user' => (object) ['id' => 4, 'full_name' => 'Emily Brown', 'email' => 'emily@example.com'], 'type' => 'donation', 'status' => 'successful', 'amount' => 5000, 'created_at' => now()->subDays(2)],
                (object) ['id' => 5, 'user' => (object) ['id' => 5, 'full_name' => 'David Wilson', 'email' => 'david@example.com'], 'type' => 'instant_food_pack', 'status' => 'failed', 'amount' => 8500, 'created_at' => now()->subDays(3)],
            ]);
        }

        if ($monthlyRevenue->isEmpty() && $admin->isSuperAdmin()) {
            $monthlyRevenue = collect([
                (object) ['month' => '2025-09', 'total' => 1850000],
                (object) ['month' => '2025-10', 'total' => 2250000],
                (object) ['month' => '2025-11', 'total' => 1980000],
                (object) ['month' => '2025-12', 'total' => 3120000],
                (object) ['month' => '2026-01', 'total' => 2890000],
                (object) ['month' => '2026-02', 'total' => 2650000],
            ]);
        }

        $cronSummary = \App\Helpers\CronLogger::getSummary();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'newUsersThisMonth',
            'totalRevenue',
            'revenueThisMonth',
            'revenueToday',
            'clearanceRevenue',
            'pendingClearances',
            'clearanceRequestsThisMonth',
            'pendingDistributions',
            'distributionsThisMonth',
            'openTickets',
            'openIncidents',
            'criticalIncidents',
            'pendingManualPayments',
            'pendingManualAmount',
            'pendingVouchers',
            'issuedVouchers',
            'pendingStarterPacks',
            'starterPacksThisMonth',
            'activeUnions',
            'totalUnionMembers',
            'activeFoodLoans',
            'totalDisbursed',
            'lowStockItems',
            'usersByTier',
            'recentUsers',
            'recentPayments',
            'monthlyRevenue',
            'cronSummary'
        ));
    }
}

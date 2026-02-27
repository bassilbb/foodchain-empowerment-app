<?php
// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payment;
use App\Models\FoodDistribution;
use App\Models\FoodItem;
use App\Models\SupportTicket;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Key metrics
        $totalUsers = User::count();
        $activeUsers = User::active()->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        // Revenue metrics
        $totalRevenue = Payment::successful()->sum('amount');
        $revenueThisMonth = Payment::successful()
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        $revenueToday = Payment::successful()
            ->whereDate('created_at', today())
            ->sum('amount');

        // Distribution metrics
        $pendingDistributions = FoodDistribution::pending()->count();
        $distributionsThisMonth = FoodDistribution::whereMonth('created_at', now()->month)->count();

        // Support metrics
        $openTickets = SupportTicket::open()->count();

        // Low stock alerts
        $lowStockItems = app(\App\Services\InventoryService::class)->getLowStockItems();

        // User tier breakdown
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

        // Recent registrations
        $recentUsers = User::latest()->limit(10)->get();

        // Recent payments
        $recentPayments = Payment::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Monthly revenue chart data (last 6 months)
        $monthlyRevenue = Payment::successful()
            ->where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // If no data, use sample data for demo
        if ($totalUsers == 0) {
            $totalUsers = 1248;
            $activeUsers = 892;
            $newUsersThisMonth = 156;
            $totalRevenue = 15640000;
            $revenueThisMonth = 2845000;
            $revenueToday = 125000;
            $pendingDistributions = 23;
            $distributionsThisMonth = 89;
            $openTickets = 12;
            $lowStockItems = collect([]);
            $usersByTier = collect([
                ['tier' => 'Bronze', 'count' => 520],
                ['tier' => 'Silver', 'count' => 380],
                ['tier' => 'Gold', 'count' => 245],
                ['tier' => 'Emerald', 'count' => 103],
            ]);
        }

        if ($recentUsers->isEmpty()) {
            $recentUsers = collect([
                (object)['id' => 1, 'first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john@example.com', 'full_name' => 'John Doe', 'currentTier' => (object)['name' => 'Gold', 'slug' => 'gold'], 'created_at' => now()->subDays(1)],
                (object)['id' => 2, 'first_name' => 'Sarah', 'last_name' => 'Smith', 'email' => 'sarah@example.com', 'full_name' => 'Sarah Smith', 'currentTier' => (object)['name' => 'Silver', 'slug' => 'silver'], 'created_at' => now()->subDays(2)],
                (object)['id' => 3, 'first_name' => 'Mike', 'last_name' => 'Johnson', 'email' => 'mike@example.com', 'full_name' => 'Mike Johnson', 'currentTier' => (object)['name' => 'Bronze', 'slug' => 'bronze'], 'created_at' => now()->subDays(3)],
                (object)['id' => 4, 'first_name' => 'Emily', 'last_name' => 'Brown', 'email' => 'emily@example.com', 'full_name' => 'Emily Brown', 'currentTier' => (object)['name' => 'Emerald', 'slug' => 'emerald'], 'created_at' => now()->subDays(4)],
                (object)['id' => 5, 'first_name' => 'David', 'last_name' => 'Wilson', 'email' => 'david@example.com', 'full_name' => 'David Wilson', 'currentTier' => (object)['name' => 'Gold', 'slug' => 'gold'], 'created_at' => now()->subDays(5)],
            ]);
        }

        if ($recentPayments->isEmpty()) {
            $recentPayments = collect([
                (object)['id' => 1, 'user' => (object)['id' => 1, 'full_name' => 'John Doe', 'email' => 'john@example.com'], 'type' => 'annual_subscription', 'status' => 'successful', 'amount' => 15000, 'created_at' => now()->subHours(2)],
                (object)['id' => 2, 'user' => (object)['id' => 2, 'full_name' => 'Sarah Smith', 'email' => 'sarah@example.com'], 'type' => 'instant_food_pack', 'status' => 'successful', 'amount' => 8500, 'created_at' => now()->subHours(5)],
                (object)['id' => 3, 'user' => (object)['id' => 3, 'full_name' => 'Mike Johnson', 'email' => 'mike@example.com'], 'type' => 'annual_subscription', 'status' => 'pending', 'amount' => 15000, 'created_at' => now()->subDays(1)],
                (object)['id' => 4, 'user' => (object)['id' => 4, 'full_name' => 'Emily Brown', 'email' => 'emily@example.com'], 'type' => 'donation', 'status' => 'successful', 'amount' => 5000, 'created_at' => now()->subDays(2)],
                (object)['id' => 5, 'user' => (object)['id' => 5, 'full_name' => 'David Wilson', 'email' => 'david@example.com'], 'type' => 'instant_food_pack', 'status' => 'failed', 'amount' => 8500, 'created_at' => now()->subDays(3)],
            ]);
        }

        if ($monthlyRevenue->isEmpty()) {
            $monthlyRevenue = collect([
                (object)['month' => '2025-09', 'total' => 1850000],
                (object)['month' => '2025-10', 'total' => 2250000],
                (object)['month' => '2025-11', 'total' => 1980000],
                (object)['month' => '2025-12', 'total' => 3120000],
                (object)['month' => '2026-01', 'total' => 2890000],
                (object)['month' => '2026-02', 'total' => 2650000],
            ]);
        }

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'newUsersThisMonth',
            'totalRevenue',
            'revenueThisMonth',
            'revenueToday',
            'pendingDistributions',
            'distributionsThisMonth',
            'openTickets',
            'lowStockItems',
            'usersByTier',
            'recentUsers',
            'recentPayments',
            'monthlyRevenue'
        ));
    }
}

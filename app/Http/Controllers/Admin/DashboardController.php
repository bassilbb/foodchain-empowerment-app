<?php

// app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\BulkOrder;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Review;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\VendorPayout;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::earning()->sum('total');
        $orderCount = Order::earning()->count();
        $allOrderCount = Order::count();
        $pendingOrders = Order::whereIn('status', ['pending', 'paid'])->count();
        $productCount = Product::count();
        $vendorCount = Vendor::approved()->count();
        $customerCount = Order::distinct('user_id')->count('user_id');
        $avgOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;

        $salesData = Order::earning()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topProducts = Product::with('category', 'vendor')->orderBy('sold_count', 'desc')->take(5)->get();

        $recentOrders = Order::with('user')->latest()->take(8)->get();

        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
        $pendingReviews = Review::pending()->count();
        $pendingVendors = Vendor::pending()->count();
        $openBulkOrders = BulkOrder::open()->count();
        $pendingPayouts = VendorPayout::whereIn('status', ['requested', 'approved', 'processing'])->count();

        $statusCounts = Order::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');

        $revenueByStatus = Order::earning()
            ->select('status', DB::raw('SUM(total) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $salesChartData = $salesData->map(fn ($row) => [
            'date' => (string) $row->date,
            'orders' => (int) $row->orders,
            'revenue' => (float) $row->revenue,
        ])->values();

        $recentVendors = Vendor::approved()->latest()->take(6)->get();

        return view('admin.dashboard', compact(
            'totalRevenue', 'orderCount', 'allOrderCount', 'pendingOrders', 'productCount', 'vendorCount',
            'customerCount', 'avgOrderValue', 'salesData', 'salesChartData', 'topProducts', 'recentOrders',
            'lowStockCount', 'pendingReviews', 'pendingVendors', 'openBulkOrders', 'pendingPayouts',
            'statusCounts', 'revenueByStatus', 'recentVendors'
        ));
    }
}
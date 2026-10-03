<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\VendorPayout;
use App\Models\Marketplace\WalletTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Store reporting. Every figure is derived from marketplace tables so the admin
 * reports describe the shop that actually trades, rather than the retired
 * membership/food-programme ledgers.
 *
 * Month bucketing happens in PHP because DATE_FORMAT() is MySQL-only and this
 * project also runs on SQLite.
 */
class MarketplaceReportController extends Controller
{
    private const RANGES = [7, 30, 90, 365];

    public function overview()
    {
        $revenue = Order::earning()->sum('total');
        $orderCount = Order::earning()->count();
        $refunded = Order::where('status', 'refunded')->sum('refund_amount');

        $topCategories = $this->categorySales(6);
        $topVendors = $this->vendorSales(6);
        $topProducts = $this->productSales(6);

        $monthlySales = $this->monthlySeries(
            Order::earning()->where('created_at', '>=', now()->subMonths(11)->startOfMonth()),
            fn (Collection $group) => [
                'orders' => $group->count(),
                'revenue' => (float) $group->sum('total'),
            ],
        );

        return view('admin.reports.overview', compact(
            'revenue',
            'orderCount',
            'refunded',
            'topCategories',
            'topVendors',
            'topProducts',
            'monthlySales'
        ) + [
            'avgOrderValue' => $orderCount > 0 ? $revenue / $orderCount : 0,
            'customerCount' => Order::distinct('user_id')->count('user_id'),
            'productCount' => Product::count(),
            'vendorCount' => Vendor::approved()->count(),
            'monthlyChart' => $monthlySales->map(fn (array $row) => [
                'month' => Carbon::createFromFormat('Y-m', $row['month'])->format('M %y'),
                'revenue' => $row['revenue'],
                'orders' => $row['orders'],
            ])->values()->all(),
        ]);
    }

    public function sales(Request $request)
    {
        $days = $this->range($request);

        $scope = Order::earning()->where('created_at', '>=', now()->subDays($days));
        $recognisedSales = (float) (clone $scope)->sum('total');
        $orderCount = (clone $scope)->count();

        $refundedOrders = Order::where('status', 'refunded')
            ->where('created_at', '>=', now()->subDays($days));
        $refundTotal = (float) (clone $refundedOrders)->sum('refund_amount');
        $refundedOrderValue = (float) (clone $refundedOrders)->sum('total');

        // Gross counts refunded orders at their original value so that the
        // "Gross - Refunds = Net" arithmetic shown on the page is truthful.
        // Cancelled orders were never charged, so they stay out entirely.
        $grossSales = $recognisedSales + $refundedOrderValue;
        $netSales = $grossSales - $refundTotal;

        $shipping = (float) (clone $scope)->sum('shipping_fee');
        $discounts = (float) (clone $scope)->sum('discount_amount');

        $series = $this->salesSeries($days);
        $hasSeriesRevenue = collect($series)->sum('revenue') > 0;

        $salesByStatus = Order::select('status', DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as revenue'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('status')
            ->get()
            ->sortByDesc('revenue')
            ->values();

        $byPaymentMethod = (clone $scope)
            ->select('payment_method', DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as revenue'))
            ->groupBy('payment_method')
            ->orderByDesc('revenue')
            ->get();

        $byState = Order::earning()
            ->select('delivery_state', DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as revenue'))
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('delivery_state')
            ->groupBy('delivery_state')
            ->orderByDesc('revenue')
            ->limit(10)
            ->get();

        return view('admin.reports.sales', [
            'days' => $days,
            'ranges' => self::RANGES,
            'grossSales' => $grossSales,
            'orderCount' => $orderCount,
            'avgOrderValue' => $orderCount > 0 ? $recognisedSales / $orderCount : 0,
            'netSales' => $grossSales - $refundTotal,
            'refundTotal' => $refundTotal,
            'shipping' => $shipping,
            'discounts' => $discounts,
            'salesByDay' => $series,
            'hasSeriesRevenue' => $hasSeriesRevenue,
            'salesByStatus' => $salesByStatus,
            'byPaymentMethod' => $byPaymentMethod,
            'byState' => $byState,
            'seriesInterval' => $this->seriesIntervalLabel($days),
            'dailyChart' => $series,            'statusChart' => $salesByStatus->map(fn ($row) => [
                'status' => Order::STATUSES[$row->status] ?? ucfirst($row->status),
                'revenue' => (float) $row->revenue,
            ])->values()->all(),
        ]);
    }

    public function customers(Request $request)
    {
        $days = $this->range($request);

        $buyers = Order::distinct('user_id')->count('user_id');
        $periodOrders = Order::earning()->where('created_at', '>=', now()->subDays($days));
        $periodRevenue = (float) (clone $periodOrders)->sum('total');
        $periodBuyers = (clone $periodOrders)->distinct('user_id')->count('user_id');

        $repeatBuyerIds = Order::earning()
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('user_id');

        $topSpenders = DB::table('users')
            ->join('marketplace_orders', 'marketplace_orders.user_id', '=', 'users.id')
            ->where('marketplace_orders.created_at', '>=', now()->subDays($days))
            ->whereNotIn('marketplace_orders.status', Order::VOID_STATUSES)
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.state', 'users.wallet_balance', 'users.loyalty_points_balance')
            ->select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.state',
                'users.wallet_balance',
                'users.loyalty_points_balance'
            )
            ->selectRaw('COUNT(*) as period_orders, SUM(marketplace_orders.total) as period_spend, MAX(marketplace_orders.created_at) as last_order_at')
            ->orderByDesc('period_spend')
            ->limit(10)
            ->get();

        $byState = Order::earning()
            ->select('delivery_state', DB::raw('COUNT(DISTINCT user_id) as buyers'), DB::raw('SUM(total) as revenue'))
            ->whereNotNull('delivery_state')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('delivery_state')
            ->orderByDesc('buyers')
            ->limit(10)
            ->get();

        // First-time buyers ordered during the period and have no earlier order.
        $newBuyers = User::whereHas('marketplaceOrders', fn ($q) => $q->where('created_at', '>=', now()->subDays($days)))
            ->whereDoesntHave('marketplaceOrders', fn ($q) => $q->where('created_at', '<', now()->subDays($days)))
            ->count();

        $lifetimeBuyers = User::whereHas('marketplaceOrders')->count();

        $registeredNoOrders = User::doesntHave('marketplaceOrders')->count();

        $walletLiability = (float) User::sum('wallet_balance');
        $loyaltyOutstanding = (int) User::sum('loyalty_points_balance');

        return view('admin.reports.customers', [
            'days' => $days,
            'ranges' => self::RANGES,
            'buyers' => $buyers,
            'periodBuyers' => $periodBuyers,
            'periodRevenue' => $periodRevenue,
            'avgLifetimeValue' => $buyers > 0 ? $this->lifetimeRevenue() / $buyers : 0,
            'repeatBuyers' => $repeatBuyerIds->count(),
            'repeatRate' => $periodBuyers > 0 ? round($repeatBuyerIds->count() / $periodBuyers * 100, 1) : 0,
            'newBuyers' => $newBuyers,
            'lifetimeBuyers' => $lifetimeBuyers,
            'registeredNoOrders' => $registeredNoOrders,
            'topSpenders' => $topSpenders,
            'byState' => $byState,
            'walletLiability' => $walletLiability,
            'loyaltyOutstanding' => $loyaltyOutstanding,
        ]);
    }

    public function products(Request $request)
    {
        $days = $this->range($request);

        $bestSellers = $this->productSales(15, $days);
        $revenueLeaders = $this->productSales(15, $days, 'revenue');
        $lowStock = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->with('vendor', 'category')
            ->orderBy('stock_quantity')
            ->limit(15)
            ->get();
        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
        $outOfStock = Product::where('stock_quantity', 0)->count();

        return view('admin.reports.products', [
            'days' => $days,
            'ranges' => self::RANGES,
            'bestSellers' => $bestSellers,
            'revenueLeaders' => $revenueLeaders,
            'lowStock' => $lowStock,
            'lowStockCount' => $lowStockCount,
            'outOfStock' => $outOfStock,
            'activeCount' => Product::where('is_active', true)->count(),
            'inactiveCount' => Product::where('is_active', false)->count(),
            'inventoryValue' => (float) Product::selectRaw('COALESCE(SUM(stock_quantity * price), 0) as value')->value('value'),
            'topCategories' => $this->categorySales(8, $days),
        ]);
    }

    public function vendors(Request $request)
    {
        $days = $this->range($request);

        $leaders = $this->vendorSales(20, $days);

        $openPayouts = ['requested', 'approved', 'processing'];
        $commissionOwed = (float) VendorPayout::whereIn('status', $openPayouts)->sum('commission_amount');
        $commissionPaid = (float) VendorPayout::where('status', 'paid')->sum('commission_amount');

        return view('admin.reports.vendors', [
            'days' => $days,
            'ranges' => self::RANGES,
            'leaders' => $leaders,
            'commissionOwed' => $commissionOwed,
            'commissionPaid' => $commissionPaid,
            'approvedCount' => Vendor::approved()->count(),
            'pendingCount' => Vendor::pending()->count(),
            'suspendedCount' => Vendor::where('status', 'suspended')->count(),
            'walletHeld' => (float) Vendor::sum('wallet_balance'),
        ]);
    }

    public function payouts(Request $request)
    {
        $days = $this->range($request);

        $byStatus = VendorPayout::select('status', DB::raw('COUNT(*) as payouts'), DB::raw('SUM(amount) as amount'), DB::raw('SUM(commission_amount) as commission'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('status')
            ->get();

        $topVendors = VendorPayout::select('vendor_id', DB::raw('COUNT(*) as payouts'), DB::raw('SUM(amount) as amount'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('vendor_id')
            ->orderByDesc('amount')
            ->limit(10)
            ->get()
            ->map(fn ($row) => [
                'vendor' => Vendor::find($row->vendor_id),
                'payouts' => (int) $row->payouts,
                'amount' => (float) $row->amount,
            ]);

        $walletByType = WalletTransaction::select('type', DB::raw('COUNT(*) as transactions'), DB::raw('SUM(amount) as amount'))
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('type')
            ->get();

        return view('admin.reports.payouts', [
            'days' => $days,
            'ranges' => self::RANGES,
            'byStatus' => $byStatus,
            'topVendors' => $topVendors,
            'walletByType' => $walletByType,
            'requestedAmount' => (float) VendorPayout::where('status', 'requested')->sum('amount'),
            'approvedAmount' => (float) VendorPayout::where('status', 'approved')->sum('amount'),
            'processingAmount' => (float) VendorPayout::where('status', 'processing')->sum('amount'),
            'paidAmount' => (float) VendorPayout::where('status', 'paid')->sum('amount'),
            'commissionOwed' => (float) VendorPayout::whereIn('status', ['requested', 'approved', 'processing'])->sum('commission_amount'),
            'vendorWalletHeld' => (float) Vendor::sum('wallet_balance'),
            'customerWalletHeld' => (float) User::sum('wallet_balance'),
            'vendorCount' => Vendor::count(),
            'customerWalletHolderCount' => (int) User::where('wallet_balance', '>', 0)->count(),
            'statusChart' => $byStatus->map(fn ($row) => [
                'status' => ucfirst($row->status),
                'amount' => (float) $row->amount,
            ])->values()->all(),
        ]);
    }

    private function range(Request $request): int
    {
        $days = (int) $request->get('days', 30);

        return in_array($days, self::RANGES, true) ? $days : 30;
    }

    /** Human label for the chart bucket width chosen for a range. */
    private function seriesIntervalLabel(int $days): string
    {
        return match (true) {
            $days <= 31 => 'day',
            $days <= 120 => 'week',
            default => 'month',
        };
    }

    private function lifetimeRevenue(): float
    {
        return (float) Order::earning()->sum('total');
    }

    /**
     * Group rows into calendar months in PHP. DATE_FORMAT() is MySQL-only.
     *
     * @return Collection<int, array{month: string}>
     */
    private function monthlySeries(Builder $query, callable $summary): Collection
    {
        return $query->get(['created_at'])
            ->groupBy(fn ($row) => $row->created_at->format('Y-m'))
            ->map(fn (Collection $group, string $month) => ['month' => $month] + $summary($group))
            ->sortKeys()
            ->values();
    }

    /**
     * A continuous, readable revenue series for the sales chart.
     *
     * The bucket width adapts to the selected range — daily up to a month,
     * weekly to four months, monthly beyond that — so the chart never tries to
     * draw 365 individual bars. Days with no orders are emitted as zero so the
     * horizontal axis is a true timeline instead of skipping empty periods.
     *
     * @return array<int, array{label: string, revenue: float, orders: int}>
     */
    private function salesSeries(int $days): array
    {
        $interval = match (true) {
            $days <= 31 => 'day',
            $days <= 120 => 'week',
            default => 'month',
        };

        $keyFormat = match ($interval) {
            'day' => 'Y-m-d',
            'week' => 'o-\WW',
            default => 'Y-m',
        };

        $labelFormat = match ($interval) {
            'day' => 'j M',
            'week' => 'j M',
            default => 'M y',
        };

        $start = now()->subDays($days)->startOfDay();
        $buckets = [];

        for ($cursor = $start->copy(); $cursor->lte(now()); $cursor = $cursor->add(1, $interval)) {
            $buckets[$cursor->format($keyFormat)] = [
                'label' => $cursor->format($labelFormat),
                'revenue' => 0.0,
                'orders' => 0,
            ];
        }

        $orders = Order::earning()
            ->where('created_at', '>=', $start)
            ->get(['created_at', 'total']);

        foreach ($orders as $order) {
            $key = $order->created_at->format($keyFormat);

            if (isset($buckets[$key])) {
                $buckets[$key]['revenue'] += (float) $order->total;
                $buckets[$key]['orders']++;
            }
        }

        return array_values($buckets);
    }

    /**
     * Category leaderboard ranked by real sales, not by how many products a
     * category happens to hold.
     */
    private function categorySales(int $limit, ?int $days = null): Collection
    {
        return $this->itemSales($days)
            ->join('marketplace_categories', 'marketplace_categories.id', '=', 'marketplace_products.category_id')
            ->selectRaw('marketplace_categories.id, marketplace_categories.name, marketplace_categories.icon, SUM(marketplace_order_items.quantity) as units, SUM(marketplace_order_items.total_price) as revenue')
            ->groupBy('marketplace_categories.id', 'marketplace_categories.name', 'marketplace_categories.icon')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    /** Vendor leaderboard ranked by units sold and revenue earned. */
    private function vendorSales(int $limit, ?int $days = null): Collection
    {
        return DB::table('marketplace_order_items')
            ->join('marketplace_orders', 'marketplace_orders.id', '=', 'marketplace_order_items.order_id')
            ->join('marketplace_vendors', 'marketplace_vendors.id', '=', 'marketplace_order_items.vendor_id')
            ->whereNotIn('marketplace_orders.status', Order::VOID_STATUSES)
            ->when($days, fn ($q) => $q->where('marketplace_order_items.created_at', '>=', now()->subDays($days)))
            ->selectRaw('marketplace_vendors.id, marketplace_vendors.business_name, marketplace_vendors.name, marketplace_vendors.status, SUM(marketplace_order_items.quantity) as units, SUM(marketplace_order_items.total_price) as revenue')
            ->groupBy('marketplace_vendors.id', 'marketplace_vendors.business_name', 'marketplace_vendors.name', 'marketplace_vendors.status')
            ->orderByDesc('revenue')
            ->limit($limit)
            ->get();
    }

    /**
     * Product leaderboard from the order items ledger, so it reflects what was
     * actually bought rather than the denormalised sold_count column.
     */
    private function productSales(int $limit, ?int $days = null, string $orderBy = 'units'): Collection
    {
        return $this->itemSales($days)
            ->selectRaw('marketplace_products.id, marketplace_products.name, marketplace_products.sku, marketplace_products.stock_quantity, marketplace_products.price, SUM(marketplace_order_items.quantity) as units, SUM(marketplace_order_items.total_price) as revenue')
            ->groupBy(
                'marketplace_products.id',
                'marketplace_products.name',
                'marketplace_products.sku',
                'marketplace_products.stock_quantity',
                'marketplace_products.price'
            )
            ->orderByDesc($orderBy === 'revenue' ? 'revenue' : 'units')
            ->limit($limit)
            ->get();
    }

    /**
     * Order items belonging to orders that still count as sales.
     */
    private function itemSales(?int $days = null): QueryBuilder
    {
        return DB::table('marketplace_order_items')
            ->join('marketplace_orders', 'marketplace_orders.id', '=', 'marketplace_order_items.order_id')
            ->join('marketplace_products', 'marketplace_products.id', '=', 'marketplace_order_items.product_id')
            ->whereNotIn('marketplace_orders.status', Order::VOID_STATUSES)
            ->when($days, fn ($q) => $q->where('marketplace_order_items.created_at', '>=', now()->subDays($days)));
    }
}

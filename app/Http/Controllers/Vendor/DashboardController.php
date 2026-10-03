<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Vendor;
use App\Services\Marketplace\VendorEarningsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct(
        private VendorEarningsService $earnings,
    ) {}

    /**
     * Resolve the vendor profile attached to the signed-in user.
     *
     * Aborts when there is no linked vendor record, so a plain customer can never
     * reach vendor pages by guessing the URL.
     */
    protected function vendor(): Vendor
    {
        $vendor = auth()->user()->marketplaceVendor;

        abort_unless($vendor, 403, 'No vendor account is linked to your profile.');

        return $vendor;
    }

    public function index()
    {
        $vendor = $this->vendor();

        // --- Product counters, one grouped pass rather than four COUNTs -------
        $productStats = Product::query()
            ->where('vendor_id', $vendor->id)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock')
            ->selectRaw('SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= low_stock_threshold THEN 1 ELSE 0 END) as low_stock')
            ->first();

        // --- Order-item counters, grouped by status in one query --------------
        $itemCounts = OrderItem::query()
            ->where('vendor_id', $vendor->id)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // --- Sales windows (delivered items only, per earnings rules) ---------
        // One grouped query covering all three windows instead of three separate
        // aggregate passes over the same table.
        $windows = OrderItem::query()
            ->where('vendor_id', $vendor->id)
            ->whereIn('status', VendorEarningsService::PAYABLE_ITEM_STATUSES)
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN total_price ELSE 0 END), 0) as today', [now()->startOfDay()])
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN total_price ELSE 0 END), 0) as week', [now()->startOfWeek()])
            ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN total_price ELSE 0 END), 0) as month', [now()->startOfMonth()])
            ->first();

        $todaySales = (float) ($windows->today ?? 0);
        $weekSales = (float) ($windows->week ?? 0);
        $monthSales = (float) ($windows->month ?? 0);

        $lifetime = $this->earnings->summary($vendor);

        $stats = [
            'products' => (int) ($productStats->total ?? 0),
            'active_products' => (int) ($productStats->active ?? 0),
            'out_of_stock' => (int) ($productStats->out_of_stock ?? 0),
            'low_stock' => (int) ($productStats->low_stock ?? 0),

            'pending_orders' => (int) $itemCounts->only(['pending', 'confirmed'])->sum(),
            'processing_orders' => (int) $itemCounts->only(['processing', 'packed'])->sum(),
            'shipped_orders' => (int) ($itemCounts['shipped'] ?? 0),
            'delivered_orders' => (int) ($itemCounts['delivered'] ?? 0),
            'cancelled_orders' => (int) ($itemCounts['cancelled'] ?? 0),

            'today_sales' => $todaySales,
            'week_sales' => $weekSales,
            'month_sales' => $monthSales,
            'total_sales' => $lifetime['gross_sales'],
            'net_earnings' => $lifetime['net_earnings'],
            'available' => $lifetime['available'],
            'pending_payout' => $lifetime['pending'],
            'paid_out' => $lifetime['paid'],
        ];

        $recentItems = OrderItem::query()
            ->where('vendor_id', $vendor->id)
            ->with(['order:id,order_number,user_id,status,payment_status,created_at', 'order.user:id,first_name,last_name,email'])
            ->latest()
            ->limit(8)
            ->get();

        $topProducts = $this->earnings->bestSellers($vendor, 5);

        $lowStockProducts = Product::query()
            ->where('vendor_id', $vendor->id)
            ->where('stock_quantity', '>', 0)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold')
            ->orderBy('stock_quantity')
            ->limit(5)
            ->get(['id', 'name', 'slug', 'stock_quantity', 'low_stock_threshold']);

        return view('vendor.dashboard', [
            'vendor' => $vendor,
            'stats' => $stats,
            'recentItems' => $recentItems,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'chart' => [
                'daily' => $this->earnings->dailySeries($vendor, now()->subDays(13), now()),
                'monthly' => $this->earnings->monthlySeries($vendor, 6),
            ],
        ]);
    }

    /**
     * Sales analytics with date-range presets.
     *
     * Every figure is scoped to this vendor by VendorEarningsService, so a vendor
     * can never see another vendor's sales data.
     */
    public function reports(Request $request)
    {
        $vendor = $this->vendor();

        // Resolve the requested range, defaulting to the current month.
        $preset = $request->input('preset', 'month');

        [$from, $to] = match ($preset) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'yesterday' => [now()->subDay()->startOfDay(), now()->subDay()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            '7days' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            '30days' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'last_month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            'custom' => [
                $request->input('from') ? \Illuminate\Support\Carbon::parse($request->input('from'))->startOfDay() : now()->startOfMonth(),
                $request->input('to') ? \Illuminate\Support\Carbon::parse($request->input('to'))->endOfDay() : now()->endOfDay(),
            ],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };

        $summary = $this->earnings->summary($vendor, $from->toDateString(), $to->toDateString());
        $daily = $this->earnings->dailySeries($vendor, $from, $to);
        $monthly = $this->earnings->monthlySeries($vendor, 6);
        $bestSellers = $this->earnings->bestSellers($vendor, 8, $from->toDateString(), $to->toDateString());

        // Average order value for the range.
        $sales = $this->earnings->salesBetween($vendor, $from, $to);
        $averageOrderValue = $sales['orders'] > 0
            ? round($sales['gross'] / $sales['orders'], 2)
            : 0.0;

        return view('vendor.reports', [
            'vendor' => $vendor,
            'summary' => $summary,
            'sales' => $sales,
            'averageOrderValue' => $averageOrderValue,
            'daily' => $daily,
            'monthly' => $monthly,
            'bestSellers' => $bestSellers,
            'preset' => $preset,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
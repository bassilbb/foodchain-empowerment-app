<?php

namespace App\Services\Marketplace;

use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\VendorPayout;
use Illuminate\Support\Facades\DB;

/**
 * Vendor earnings and payout maths.
 *
 * Single source of truth so the dashboard, analytics and payout screens can
 * never disagree about what a vendor has earned.
 *
 * Earnings are DERIVED from delivered order items — the application never
 * credits `marketplace_vendors.wallet_balance`, so that column must not be
 * treated as a source of truth for money owed.
 *
 * Commission comes from `marketplace_vendors.commission_rate` (the existing
 * business configuration), never a hardcoded rate.
 */
class VendorEarningsService
{
    /** Item statuses that represent a completed, payable sale. */
    public const PAYABLE_ITEM_STATUSES = ['delivered'];

    /**
     * Order-item query scoped to one vendor's payable sales.
     *
     * @return \Illuminate\Database\Eloquent\Builder<OrderItem>
     */
    private function payableItems(int $vendorId, ?string $from = null, ?string $to = null)
    {
        return OrderItem::query()
            ->where('marketplace_order_items.vendor_id', $vendorId)
            ->whereIn('marketplace_order_items.status', self::PAYABLE_ITEM_STATUSES)
            ->when($from, fn($q, $from) => $q->whereDate('marketplace_order_items.created_at', '>=', $from))
            ->when($to, fn($q, $to) => $q->whereDate('marketplace_order_items.created_at', '<=', $to));
    }

    /**
     * Full earnings breakdown for a vendor.
     *
     * @return array{
     *     gross_sales: float, commission_rate: float, commission_amount: float,
     *     other_deductions: float, net_earnings: float, paid: float,
     *     pending: float, available: float, items_sold: int, order_count: int
     * }
     */
    public function summary(Vendor $vendor, ?string $from = null, ?string $to = null): array
    {
        $rate = (float) $vendor->commission_rate;

        // One aggregate pass rather than separate queries per figure.
        $aggregate = $this->payableItems($vendor->id, $from, $to)
            ->selectRaw('COALESCE(SUM(total_price), 0) as gross')
            ->selectRaw('COALESCE(SUM(quantity), 0) as units')
            ->selectRaw('COUNT(DISTINCT order_id) as orders')
            ->first();

        $gross = (float) ($aggregate->gross ?? 0);
        $commission = round($gross * ($rate / 100), 2);

        // One grouped pass over the payout ledger.
        $payoutTotals = VendorPayout::forVendor($vendor->id)
            ->select('status', DB::raw('COALESCE(SUM(amount), 0) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $paid = (float) $payoutTotals->only(['paid'])->sum();
        $pending = (float) $payoutTotals->only(['requested', 'approved', 'processing'])->sum();
        $otherDeductions = 0.0;

        $net = round($gross - $commission - $otherDeductions, 2);

        // Never allow a negative "available" figure to show as withdrawable.
        $available = max(0, round($net - $paid - $pending, 2));

        return [
            'gross_sales' => $gross,
            'commission_rate' => $rate,
            'commission_amount' => $commission,
            'other_deductions' => $otherDeductions,
            'net_earnings' => $net,
            'paid' => $paid,
            'pending' => $pending,
            'available' => $available,
            'items_sold' => (int) ($aggregate->units ?? 0),
            'order_count' => (int) ($aggregate->orders ?? 0),
        ];
    }

    /**
     * Sales totals for a date range, used by the dashboard KPI cards.
     *
     * @return array{gross: float, orders: int, units: int}
     */
    public function salesBetween(Vendor $vendor, $from, $to): array
    {
        $row = $this->payableItems($vendor->id)
            ->whereBetween('marketplace_order_items.created_at', [$from, $to])
            ->selectRaw('COALESCE(SUM(total_price), 0) as gross')
            ->selectRaw('COALESCE(SUM(quantity), 0) as units')
            ->selectRaw('COUNT(DISTINCT order_id) as orders')
            ->first();

        return [
            'gross' => (float) ($row->gross ?? 0),
            'orders' => (int) ($row->orders ?? 0),
            'units' => (int) ($row->units ?? 0),
        ];
    }

    /**
     * Daily sales series for charts, as [['date' => 'Y-m-d', 'total' => x], ...].
     *
     * Grouped in PHP from one range query so the grouping stays portable across
     * MySQL and SQLite.
     */
    public function dailySeries(Vendor $vendor, $from, $to): array
    {
        return $this->payableItems($vendor->id)
            ->whereBetween('marketplace_order_items.created_at', [$from, $to])
            ->select('marketplace_order_items.created_at', 'marketplace_order_items.total_price')
            ->get()
            ->groupBy(fn($item) => $item->created_at->toDateString())
            ->map(fn($group, $day) => ['date' => $day, 'total' => (float) $group->sum('total_price')])
            ->sortKeys()
            ->values()
            ->all();
    }

    /**
     * Monthly sales series for the last N months.
     *
     * Grouped in PHP rather than with DATE_FORMAT, which is MySQL-only and would
     * break on SQLite (the default local driver).
     */
    public function monthlySeries(Vendor $vendor, int $months = 6): array
    {
        $start = now()->subMonths($months - 1)->startOfMonth();

        return $this->payableItems($vendor->id)
            ->where('marketplace_order_items.created_at', '>=', $start)
            ->select('marketplace_order_items.created_at', 'marketplace_order_items.total_price')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m'))
            ->map(fn($group, $period) => ['period' => $period, 'total' => (float) $group->sum('total_price')])
            ->sortKeys()
            ->values()
            ->all();
    }

    /** Best selling products for a vendor by units sold. */
    public function bestSellers(Vendor $vendor, int $limit = 5, ?string $from = null, ?string $to = null)
    {
        return $this->payableItems($vendor->id, $from, $to)
            ->select('product_id')
            ->selectRaw('COALESCE(SUM(quantity), 0) as units')
            ->selectRaw('COALESCE(SUM(total_price), 0) as revenue')
            ->groupBy('product_id')
            ->orderByDesc('units')
            ->limit($limit)
            ->with('product:id,name,slug')
            ->get();
    }
}

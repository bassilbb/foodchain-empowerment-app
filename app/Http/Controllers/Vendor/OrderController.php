<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Item statuses a vendor may set.
     *
     * `delivered` is deliberately EXCLUDED: delivery is confirmed by the customer
     * (OrderService::markDelivered) or an administrator. A vendor marking their own
     * goods delivered would let them trigger their own payout.
     */
    public const VENDOR_ALLOWED_STATUSES = ['confirmed', 'processing', 'packed', 'shipped', 'cancelled'];

    private function vendor(): Vendor
    {
        $vendor = auth()->user()->marketplaceVendor;

        abort_unless($vendor, 403, 'No vendor account is linked to your profile.');

        return $vendor;
    }

    /**
     * A vendor only ever sees order items allocated to them.
     *
     * The base query is hard-scoped by vendor_id, so no filter or search term can
     * widen it to another vendor's sales.
     */
    public function index(Request $request)
    {
        $vendor = $this->vendor();

        $query = OrderItem::query()
            ->where('marketplace_order_items.vendor_id', $vendor->id)
            ->with([
                'order:id,order_number,user_id,status,payment_status,delivery_state,created_at',
                'order.user:id,first_name,last_name,email',
                'product:id,name,slug,sku',
            ]);

        if ($status = $request->input('status')) {
            if (array_key_exists($status, $this->statusGroups())) {
                $query->whereIn('status', $this->statusGroups()[$status]['statuses']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($search = trim((string) $request->input('q'))) {
            $like = '%' . $search . '%';
            $query->where(function ($q) use ($like) {
                $q->where('product_name', 'like', $like)
                    ->orWhere('product_sku', 'like', $like)
                    ->orWhereHas('order', fn($o) => $o->where('order_number', 'like', $like));
            });
        }

        if ($from = $request->input('from')) {
            $query->whereDate('marketplace_order_items.created_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('marketplace_order_items.created_at', '<=', $to);
        }

        $items = $query->latest('marketplace_order_items.created_at')->paginate(20)->withQueryString();

        $counts = OrderItem::where('vendor_id', $vendor->id)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $groups = $this->statusGroups();

        // Each group value is ['label' => ..., 'statuses' => [...]]; the counts
        // must iterate the nested `statuses` list, not the group array itself.
        $groupCounts = collect($groups)->map(
            fn($group) => (int) collect($group['statuses'])->sum(fn($s) => (int) ($counts[$s] ?? 0))
        )->all();

        return view('vendor.orders.index', [
            'vendor' => $vendor,
            'items' => $items,
            'groups' => $groups,
            'groupCounts' => $groupCounts,
            'totalCount' => (int) $counts->sum(),
            'currentStatus' => $request->input('status'),
            'search' => $search,
            'from' => $from,
            'to' => $to,
            'allowedStatuses' => self::VENDOR_ALLOWED_STATUSES,
        ]);
    }

    /** Customer-facing status groups, mapped onto the real item statuses. */
    private function statusGroups(): array
    {
        return [
            'new' => ['label' => 'New', 'statuses' => ['pending']],
            'processing' => ['label' => 'Processing', 'statuses' => ['confirmed', 'processing', 'packed']],
            'shipped' => ['label' => 'Shipped', 'statuses' => ['shipped']],
            'delivered' => ['label' => 'Delivered', 'statuses' => ['delivered']],
            'cancelled' => ['label' => 'Cancelled', 'statuses' => ['cancelled']],
        ];
    }

    /**
     * Update the status of one allocated order item.
     *
     * The item must belong to this vendor, and the requested status must be one
     * the vendor is allowed to set.
     */
    public function updateStatus(Request $request, OrderItem $item)
    {
        $vendor = $this->vendor();

        abort_unless($item->vendor_id === $vendor->id, 404);

        $data = $request->validate([
            'status' => ['required', 'string', \Illuminate\Validation\Rule::in(self::VENDOR_ALLOWED_STATUSES)],
        ]);

        $item->update(['status' => $data['status']]);

        // When every sibling item has shipped, roll the parent order up — the
        // same rule the existing vendor controller applied.
        $order = $item->order;

        if ($order && $data['status'] === 'shipped') {
            $siblingStatuses = $order->items()->where('id', '!=', $item->id)->pluck('status')->unique();

            if ($siblingStatuses->count() === 1 && $siblingStatuses->first() === 'shipped') {
                $order->updateStatus('shipped', 'All items shipped.');
            }
        }

        return back()->with('success', 'Item marked as ' . str_replace('_', ' ', $data['status']) . '.');
    }

    /** Packing slips for items the vendor needs to prepare. */
    public function packingSlips()
    {
        $vendor = $this->vendor();

        $items = OrderItem::where('vendor_id', $vendor->id)
            ->whereIn('status', ['packed', 'processing', 'confirmed'])
            ->with(['order:id,order_number,delivery_address,delivery_city,delivery_state,created_at', 'order.user:id,first_name,last_name,phone'])
            ->latest()
            ->paginate(20);

        return view('vendor.orders.packing-slips', [
            'vendor' => $vendor,
            'items' => $items,
        ]);
    }
}

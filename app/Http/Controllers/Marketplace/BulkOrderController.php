<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\BulkOrder;
use App\Models\Marketplace\BulkOrderContribution;
use App\Models\Marketplace\BulkOrderItem;
use App\Models\Marketplace\Product;
use App\Services\Marketplace\MarketplaceNotificationService;
use App\Services\Marketplace\MarketplaceWalletService;
use Illuminate\Http\Request;

class BulkOrderController extends Controller
{
    public function __construct(
        private MarketplaceWalletService $walletService,
        private MarketplaceNotificationService $notificationService,
    ) {
    }

    public function index(Request $request)
    {
        $query = BulkOrder::with('creator', 'items.product', 'contributions')->open();

        if ($request->input('q')) {
            $query->where('title', 'like', '%'.$request->input('q').'%');
        }

        $bulkOrders = $query->latest()->paginate(10)->withQueryString();

        return view('marketplace.bulk.index', [
            'bulkOrders' => $bulkOrders,
            'title' => 'Community Bulk Purchase',
        ]);
    }

    public function create()
    {
        $products = Product::active()->inStock()->with('category', 'images')->orderBy('name')->get();

        return view('marketplace.bulk.create', [
            'products' => $products,
            'title' => 'Start a Bulk Order',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string|max:1000',
            'target_date' => 'required|date|after:today',
            'delivery_type' => 'required|in:community_delivery,pickup',
            'delivery_location' => 'nullable|string|max:255',
            'discount_percentage' => 'nullable|numeric|between:0,50',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:marketplace_products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $items = collect($request->input('items'))->filter(fn ($i) => ! empty($i['product_id']));

        if ($items->isEmpty()) {
            return back()->withInput()->with('error', 'Add at least one product.');
        }

        $bulkOrder = BulkOrder::create([
            'created_by' => auth()->id(),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'status' => 'open',
            'target_date' => $request->input('target_date'),
            'delivery_type' => $request->input('delivery_type'),
            'delivery_location' => $request->input('delivery_location'),
            'discount_percentage' => $request->input('discount_percentage') ?? 0,
        ]);

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            BulkOrderItem::create([
                'bulk_order_id' => $bulkOrder->id,
                'product_id' => $product->id,
                'required_quantity' => $item['quantity'],
                'contributed_quantity' => 0,
                'unit_price' => $product->selling_price,
            ]);
        }

        $this->notificationService->bulkOrderCreated($bulkOrder);

        return redirect()->route('marketplace.bulk.show', $bulkOrder)->with('success', 'Bulk order created. Share it with your community!');
    }

    public function show(BulkOrder $bulkOrder)
    {
        $bulkOrder->load('creator', 'items.product.images', 'contributions.user');

        return view('marketplace.bulk.show', [
            'bulkOrder' => $bulkOrder,
            'title' => $bulkOrder->title,
        ]);
    }

    public function contribute(Request $request, BulkOrder $bulkOrder)
    {
        abort_unless($bulkOrder->isOpen(), 403);

        $request->validate([
            'item_id' => 'required|integer|exists:marketplace_bulk_order_items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = BulkOrderItem::with('product')->findOrFail($request->integer('item_id'));
        abort_unless($item->bulk_order_id === $bulkOrder->id, 403);

        $quantity = $request->integer('quantity');
        $unitPrice = (float) $item->unit_price;
        $discountedUnitPrice = $unitPrice * (1 - ((float) $bulkOrder->discount_percentage / 100));
        $amount = round($discountedUnitPrice * $quantity, 2);

        $user = auth()->user();

        if (! $user->hasSufficientWalletBalance($amount)) {
            return back()->with('error', 'Insufficient wallet balance for this contribution.');
        }

        $user->deductFromWallet($amount);

        $contribution = BulkOrderContribution::create([
            'bulk_order_id' => $bulkOrder->id,
            'bulk_order_item_id' => $item->id,
            'user_id' => $user->id,
            'quantity' => $quantity,
            'amount' => $amount,
            'status' => 'paid',
            'payment_method' => 'wallet',
        ]);

        $item->increment('contributed_quantity', $quantity);

        $this->walletService->logTransaction($user, null, $amount, 'debit', 'Contribution to bulk order '.$bulkOrder->title);

        $bulkOrder->recalculateTotals();

        if ($bulkOrder->status === 'completed') {
            $this->notificationService->bulkOrderCompleted($bulkOrder);
        }

        return back()->with('success', 'Contribution of ₦'.number_format($amount, 2).' recorded. Thank you!');
    }
}

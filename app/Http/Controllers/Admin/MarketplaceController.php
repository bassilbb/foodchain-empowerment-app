<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\BulkOrder;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Coupon;
use App\Models\Marketplace\FlashSale;
use App\Models\Marketplace\FlashSaleItem;
use App\Models\Marketplace\Order;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Review;
use App\Models\Marketplace\Vendor;
use App\Models\Marketplace\WalletTransaction;
use App\Models\Marketplace\Brand;
use App\Models\Marketplace\DeliveryZone;
use App\Models\Marketplace\ProductImage;
use App\Models\Marketplace\ProductStatePrice;
use App\Models\NigerianState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    public function dashboard()
    {
        $totalRevenue = Order::whereIn('status', ['delivered', 'shipped', 'processing', 'confirmed'])->sum('total');
        $orderCount = Order::count();
        $pendingOrders = Order::whereIn('status', ['pending', 'paid'])->count();
        $productCount = Product::count();
        $vendorCount = Vendor::approved()->count();
        $customerCount = Order::distinct('user_id')->count('user_id');
        $avgOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;

        $salesData = Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topProducts = Product::with('category')->orderBy('sold_count', 'desc')->take(5)->get();

        $recentOrders = Order::with('user')->latest()->take(8)->get();

        $lowStockCount = Product::whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
        $pendingReviews = Review::pending()->count();
        $pendingVendors = Vendor::pending()->count();
        $openBulkOrders = BulkOrder::open()->count();

        $statusCounts = Order::select('status', DB::raw('count(*) as total'))->groupBy('status')->pluck('total', 'status');

        $salesChartData = $salesData->map(fn ($row) => [
            'date' => (string) $row->date,
            'orders' => (int) $row->orders,
            'revenue' => (float) $row->revenue,
        ])->values();

        return view('admin.marketplace.dashboard', compact(
            'totalRevenue', 'orderCount', 'pendingOrders', 'productCount', 'vendorCount',
            'customerCount', 'avgOrderValue', 'salesData', 'salesChartData', 'topProducts', 'recentOrders',
            'lowStockCount', 'pendingReviews', 'pendingVendors', 'openBulkOrders', 'statusCounts'
        ));
    }

    // ---- Products ----
    public function products(Request $request)
    {
        $query = Product::with('category', 'vendor', 'brand');

        if ($request->input('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->input('search').'%')->orWhere('sku', 'like', '%'.$request->input('search').'%'));
        }

        if ($request->input('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $products = $query->latest()->paginate(20)->withQueryString();

        return view('admin.marketplace.products', [
            'products' => $products,
            'categories' => Category::active()->get(),
        ]);
    }

    public function productToggle(Product $product)
    {
        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', 'Product '.( $product->is_active ? 'activated' : 'deactivated' ).'.');
    }

    public function productShow(Product $product)
    {
        $product->load('category', 'brand', 'vendor', 'images', 'inventoryMovements');

        return view('admin.marketplace.product-show', ['product' => $product]);
    }

    public function productEdit(Product $product)
    {
        return view('admin.marketplace.product-form', [
            'product' => $product->load('images'),
            'categories' => Category::active()->parents()->with('children')->get(),
            'brands' => Brand::active()->orderBy('name')->get(),
            'vendors' => Vendor::approved()->get(),
            'states' => NigerianState::active()->ordered()->get(['id', 'name']),
            'statePrices' => $product->statePrices()->pluck('price', 'state_id'),
        ]);
    }

    public function productUpdate(Request $request, Product $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|integer|exists:marketplace_categories,id',
            'vendor_id' => 'required|integer|exists:marketplace_vendors,id',
            'brand_id' => 'nullable|integer|exists:marketplace_brands,id',
            'unit' => 'required|string|max:30',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|max:10',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'member_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'expiry_date' => 'nullable|date',
            'manufacturing_date' => 'nullable|date',
            'country_of_origin' => 'nullable|string|max:60',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'nutritional_info' => 'nullable|string',
            'storage_instructions' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product->update($data + [
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        if ($request->hasFile('images')) {
            $hasPrimary = $product->images()->where('is_primary', true)->exists();
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('marketplace/products/'.$product->id, 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'is_primary' => ! $hasPrimary && $index === 0,
                    'sort_order' => $product->images()->count() + $index,
                ]);
            }
        }

        return back()->with('success', 'Product updated.');
    }

    public function productStatePricesUpdate(Request $request, Product $product)
    {
        $validStateIds = NigerianState::active()->pluck('id')->all();
        $prices = $request->input('prices', []);

        if (! is_array($prices)) {
            $prices = [];
        }

        $rows = [];
        $now = now();

        foreach ($prices as $stateId => $price) {
            $stateId = (int) $stateId;

            if (! in_array($stateId, $validStateIds, true)) {
                continue;
            }

            $value = is_numeric($price) ? round((float) $price, 2) : null;

            if ($value === null || $value < 0) {
                continue;
            }

            $rows[] = [
                'product_id' => $product->id,
                'state_id' => $stateId,
                'price' => $value,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($rows)) {
            ProductStatePrice::upsert($rows, ['product_id', 'state_id'], ['price', 'is_active', 'updated_at']);
        }

        return back()->with('success', 'State prices updated.');
    }

    // ---- Vendors ----
    public function vendors(Request $request)
    {
        $vendors = Vendor::withCount('products')
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('search'), fn ($q, $s) => $q->where(fn ($qq) => $qq->where('name', 'like', '%'.$s.'%')->orWhere('business_name', 'like', '%'.$s.'%')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.marketplace.vendors', ['vendors' => $vendors]);
    }

    public function vendorShow(Vendor $vendor)
    {
        $vendor->load('products.category', 'user');

        $revenue = OrderItem::where('vendor_id', $vendor->id)->whereHas('order', fn ($q) => $q->where('status', 'delivered'))->sum('total_price');

        return view('admin.marketplace.vendor-show', ['vendor' => $vendor, 'revenue' => $revenue]);
    }

    public function vendorUpdateStatus(Request $request, Vendor $vendor)
    {
        $request->validate(['status' => 'required|in:pending,approved,suspended']);

        $vendor->update(['status' => $request->input('status')]);

        return back()->with('success', 'Vendor status updated.');
    }

    // ---- Orders ----
    public function orders(Request $request)
    {
        $query = Order::with('user', 'items');

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->input('search')) {
            $query->where(fn ($q) => $q->where('order_number', 'like', '%'.$request->input('search').'%')->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$request->input('search').'%')));
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.marketplace.orders', [
            'orders' => $orders,
            'statuses' => Order::STATUSES,
        ]);
    }

    public function orderShow(Order $order)
    {
        $order->load('items.product.images', 'items.vendor', 'user', 'statusHistories', 'coupon');

        return view('admin.marketplace.order-show', ['order' => $order]);
    }

    public function orderUpdateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:'.implode(',', array_keys(Order::STATUSES))]);

        $order->updateStatus($request->input('status'), $request->input('note'));

        if ($request->input('status') === 'refunded' && $order->wallet_amount > 0) {
            app(\App\Services\Marketplace\MarketplaceWalletService::class)->refund(
                $order->user,
                $order,
                $order->wallet_amount,
                'Refund for order '.$order->order_number
            );
        }

        app(\App\Services\Marketplace\MarketplaceNotificationService::class)->orderStatusChanged($order, $request->input('status'));

        return back()->with('success', 'Order status updated.');
    }

    // ---- Categories ----
    public function categories()
    {
        $categories = Category::with('parent', 'children')->withCount('products')->orderBy('sort_order')->get();

        return view('admin.marketplace.categories', [
            'categories' => $categories,
            'parents' => Category::parents()->get(),
        ]);
    }

    public function categoryStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|integer|exists:marketplace_categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        Category::create($data + [
            'slug' => Str::slug($data['name']).'-'.strtolower(substr(uniqid(), -4)),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Category created.');
    }

    public function categoryUpdate(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'parent_id' => 'nullable|integer|exists:marketplace_categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $category->update($data + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Category updated.');
    }

    // ---- Brands ----
    public function brands()
    {
        $brands = Brand::withCount('products')->orderBy('name')->get();

        return view('admin.marketplace.brands', ['brands' => $brands]);
    }

    public function brandStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        Brand::create([
            'name' => $request->input('name'),
            'slug' => Str::slug($request->input('name')).'-'.strtolower(substr(uniqid(), -4)),
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Brand created.');
    }

    public function brandUpdate(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $brand->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Brand updated.');
    }

    // ---- Coupons ----
    public function coupons()
    {
        $coupons = Coupon::latest()->get();

        return view('admin.marketplace.coupons', ['coupons' => $coupons]);
    }

    public function couponStore(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:40|unique:marketplace_coupons,code',
            'name' => 'nullable|string|max:100',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'is_member_only' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        Coupon::create($data + [
            'code' => strtoupper($request->input('code')),
            'is_member_only' => $request->boolean('is_member_only'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Coupon created.');
    }

    public function couponUpdate(Request $request, Coupon $coupon)
    {
        $data = $request->validate([
            'code' => 'required|string|max:40|unique:marketplace_coupons,code,'.$coupon->id,
            'name' => 'nullable|string|max:100',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'per_user_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'is_member_only' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        $coupon->update($data + [
            'code' => strtoupper($request->input('code')),
            'is_member_only' => $request->boolean('is_member_only'),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Coupon updated.');
    }

    // ---- Flash Sales ----
    public function flashSales()
    {
        $flashSales = FlashSale::withCount('items')->latest()->get();

        return view('admin.marketplace.flash-sales', ['flashSales' => $flashSales]);
    }

    public function flashSaleStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'is_active' => 'nullable|boolean',
        ]);

        FlashSale::create($data + [
            'slug' => Str::slug($data['name']).'-'.strtolower(substr(uniqid(), -4)),
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Flash sale created.');
    }

    public function flashSaleShow(FlashSale $flashSale)
    {
        $flashSale->load('items.product');

        $available = Product::active()->inStock()->whereDoesntHave('flashSaleItems', fn ($q) => $q->where('flash_sale_id', $flashSale->id))->get();

        return view('admin.marketplace.flash-sale-show', ['flashSale' => $flashSale, 'available' => $available]);
    }

    public function flashSaleToggle(FlashSale $flashSale)
    {
        $flashSale->update(['is_active' => ! $flashSale->is_active]);

        return back()->with('success', 'Flash sale '.($flashSale->is_active ? 'activated' : 'deactivated').'.');
    }

    public function flashSaleAddItem(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:marketplace_products,id',
            'sale_price' => 'required|numeric|min:1',
            'quantity_limit' => 'nullable|integer|min:1',
        ]);

        FlashSaleItem::updateOrCreate(
            ['flash_sale_id' => $flashSale->id, 'product_id' => $request->integer('product_id')],
            [
                'sale_price' => $request->input('sale_price'),
                'quantity_limit' => $request->input('quantity_limit'),
            ]
        );

        return back()->with('success', 'Product added to flash sale.');
    }

    public function flashSaleRemoveItem(FlashSaleItem $item)
    {
        $item->delete();

        return back()->with('success', 'Product removed from flash sale.');
    }

    // ---- Reviews ----
    public function reviews(Request $request)
    {
        $query = Review::with('product', 'user');

        if ($request->has('pending')) {
            $query->where('is_approved', false);
        }

        $reviews = $query->latest()->paginate(20)->withQueryString();

        return view('admin.marketplace.reviews', ['reviews' => $reviews]);
    }

    public function reviewToggle(Review $review)
    {
        $review->update(['is_approved' => ! $review->is_approved]);
        $review->product->updateRatingStats();

        return back()->with('success', 'Review '.($review->is_approved ? 'approved' : 'unapproved').'.');
    }

    // ---- Delivery Zones ----
    public function deliveryZones()
    {
        $zones = DeliveryZone::orderBy('state')->orderBy('lga')->get();

        return view('admin.marketplace.delivery-zones', ['zones' => $zones]);
    }

    public function deliveryZoneStore(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'state' => 'required|string|max:60',
            'lga' => 'nullable|string|max:120',
            'delivery_fee' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'estimated_days' => 'required|integer|min:1|max:14',
            'is_active' => 'nullable|boolean',
        ]);

        DeliveryZone::create($data + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Delivery zone created.');
    }

    public function deliveryZoneUpdate(Request $request, DeliveryZone $zone)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'state' => 'required|string|max:60',
            'lga' => 'nullable|string|max:120',
            'delivery_fee' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'estimated_days' => 'required|integer|min:1|max:14',
            'is_active' => 'nullable|boolean',
        ]);

        $zone->update($data + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'Delivery zone updated.');
    }

    // ---- Bulk Orders ----
    public function bulkOrders(Request $request)
    {
        $bulkOrders = BulkOrder::with('creator', 'items.product')
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.marketplace.bulk-orders', ['bulkOrders' => $bulkOrders]);
    }

    public function bulkOrderUpdateStatus(Request $request, BulkOrder $bulkOrder)
    {
        $request->validate(['status' => 'required|in:open,in_progress,completed,cancelled']);

        $bulkOrder->update(['status' => $request->input('status')]);

        return back()->with('success', 'Bulk order status updated.');
    }

    // ---- Wallet / Loyalty (admin view) ----
    public function walletTransactions(Request $request)
    {
        $transactions = WalletTransaction::with('user', 'order')
            ->when($request->input('type'), fn ($q, $t) => $q->where('type', $t))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.marketplace.wallet-transactions', ['transactions' => $transactions]);
    }

    public function reports()
    {
        $salesByDay = Order::selectRaw('DATE(created_at) as date, COUNT(*) as orders, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')->orderBy('date')->get();

        $salesByStatus = Order::select('status', DB::raw('count(*) as total'), DB::raw('SUM(total) as revenue'))
            ->groupBy('status')->get();

        $topVendors = Vendor::withSum('products', 'sold_count')->orderByDesc('products_sum_sold_count')->take(5)->get();

        $topCategories = Category::withCount('products')->orderByDesc('products_count')->take(8)->get();

        $dailySalesChartData = $salesByDay->map(fn ($row) => [
            'date' => (string) $row->date,
            'orders' => (int) $row->orders,
            'revenue' => (float) $row->revenue,
        ])->values();

        $statusChartData = $salesByStatus->map(fn ($row) => [
            'status' => $row->status,
            'orders' => (int) $row->total,
            'revenue' => (float) $row->revenue,
        ])->values();

        return view('admin.marketplace.reports', compact('salesByDay', 'salesByStatus', 'topVendors', 'topCategories', 'dailySalesChartData', 'statusChartData'));
    }
}

<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Brand;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\InventoryMovement;
use App\Models\Marketplace\OrderItem;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductImage;
use App\Models\Marketplace\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    private function vendor(): Vendor
    {
        $vendor = auth()->user()->marketplaceVendor;

        abort_unless($vendor, 403, 'No vendor account linked to your profile.');

        return $vendor;
    }

    public function dashboard()
    {
        $vendor = $this->vendor();

        $productCount = $vendor->products()->count();
        $orderItems = OrderItem::where('vendor_id', $vendor->id);
        $orderCount = $orderItems->distinct('order_id')->count('order_id');
        $revenue = (clone $orderItems)->whereHas('order', fn ($q) => $q->whereIn('status', ['delivered']))->sum('total_price');
        $pendingItems = (clone $orderItems)->where('status', 'pending')->count();
        $lowStock = $vendor->products()->whereColumn('stock_quantity', '<=', 'low_stock_threshold')->count();
        $expiringSoon = $vendor->products()->whereNotNull('expiry_date')->where('expiry_date', '<=', now()->addDays(30))->count();

        $recentOrders = OrderItem::with('order.user', 'product')
            ->where('vendor_id', $vendor->id)
            ->latest()
            ->take(10)
            ->get();

        $topProducts = $vendor->products()->orderBy('sold_count', 'desc')->take(5)->get();

        return view('marketplace.vendor.dashboard', compact(
            'vendor', 'productCount', 'orderCount', 'revenue', 'pendingItems',
            'lowStock', 'expiringSoon', 'recentOrders', 'topProducts'
        ));
    }

    public function products(Request $request)
    {
        $vendor = $this->vendor();

        $query = $vendor->products()->with('category', 'brand');

        if ($request->input('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->input('search').'%')->orWhere('sku', 'like', '%'.$request->input('search').'%'));
        }

        if ($request->has('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        return view('marketplace.vendor.products', [
            'vendor' => $vendor,
            'products' => $products,
        ]);
    }

    public function productCreate()
    {
        $vendor = $this->vendor();

        return view('marketplace.vendor.product-form', [
            'vendor' => $vendor,
            'categories' => Category::active()->parents()->with('children')->get(),
            'brands' => Brand::active()->orderBy('name')->get(),
            'product' => null,
        ]);
    }

    public function productStore(Request $request)
    {
        $vendor = $this->vendor();

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|integer|exists:marketplace_categories,id',
            'brand_id' => 'nullable|integer|exists:marketplace_brands,id',
            'unit' => 'required|string|max:30',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|max:10',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'member_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'nutritional_info' => 'nullable|string',
            'storage_instructions' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'manufacturing_date' => 'nullable|date',
            'country_of_origin' => 'nullable|string|max:60',
            'video_url' => 'nullable|url',
            'batch_number' => 'nullable|string|max:60',
            'barcode' => 'nullable|string|max:60',
            'is_featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product = Product::create([
            'vendor_id' => $vendor->id,
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'] ?? null,
            'name' => $data['name'],
            'slug' => \Illuminate\Support\Str::slug($data['name']).'-'.strtolower(substr(uniqid(), -5)),
            'sku' => Product::generateSku(),
            'barcode' => $data['barcode'] ?? null,
            'unit' => $data['unit'],
            'weight' => $data['weight'] ?? null,
            'weight_unit' => $data['weight_unit'] ?? null,
            'price' => $data['price'],
            'discount_price' => $data['discount_price'] ?? null,
            'member_price' => $data['member_price'] ?? null,
            'stock_quantity' => $data['stock_quantity'],
            'low_stock_threshold' => $data['low_stock_threshold'] ?? 5,
            'description' => $data['description'] ?? null,
            'ingredients' => $data['ingredients'] ?? null,
            'nutritional_info' => $data['nutritional_info'] ?? null,
            'storage_instructions' => $data['storage_instructions'] ?? null,
            'expiry_date' => $data['expiry_date'] ?? null,
            'manufacturing_date' => $data['manufacturing_date'] ?? null,
            'country_of_origin' => $data['country_of_origin'] ?? 'Nigeria',
            'video_url' => $data['video_url'] ?? null,
            'batch_number' => $data['batch_number'] ?? null,
            'is_active' => true,
            'is_featured' => ! empty($data['is_featured']),
            'meta' => ['vendor_created' => true],
        ]);

        $this->storeImages($request, $product);

        return redirect()->route('marketplace.vendor.products')->with('success', 'Product created successfully.');
    }

    public function productEdit(Product $product)
    {
        $vendor = $this->vendor();
        abort_unless($product->vendor_id === $vendor->id, 403);

        return view('marketplace.vendor.product-form', [
            'vendor' => $vendor,
            'categories' => Category::active()->parents()->with('children')->get(),
            'brands' => Brand::active()->orderBy('name')->get(),
            'product' => $product->load('images'),
        ]);
    }

    public function productUpdate(Request $request, Product $product)
    {
        $vendor = $this->vendor();
        abort_unless($product->vendor_id === $vendor->id, 403);

        $data = $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|integer|exists:marketplace_categories,id',
            'brand_id' => 'nullable|integer|exists:marketplace_brands,id',
            'unit' => 'required|string|max:30',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|max:10',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'member_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'nutritional_info' => 'nullable|string',
            'storage_instructions' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'manufacturing_date' => 'nullable|date',
            'country_of_origin' => 'nullable|string|max:60',
            'video_url' => 'nullable|url',
            'batch_number' => 'nullable|string|max:60',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $product->update($data + [
            'is_active' => $request->boolean('is_active'),
            'is_featured' => $request->boolean('is_featured'),
        ]);

        $this->storeImages($request, $product);

        return redirect()->route('marketplace.vendor.products')->with('success', 'Product updated successfully.');
    }

    private function storeImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $hasRealPrimary = $product->images()->where('is_placeholder', false)->where('is_primary', true)->exists();

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('marketplace/products/'.$product->id, 'public');

            $isPrimary = ! $hasRealPrimary && $index === 0;

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $path,
                'is_primary' => $isPrimary,
                'is_placeholder' => false,
                'sort_order' => $product->images()->count() + $index,
            ]);

            if ($isPrimary) {
                $hasRealPrimary = true;
                $product->images()->where('is_placeholder', true)->update(['is_primary' => false]);
            }
        }
    }

    public function inventory()
    {
        $vendor = $this->vendor();

        $products = $vendor->products()->with('category')
            ->orderByRaw('stock_quantity <= low_stock_threshold DESC, expiry_date ASC')
            ->paginate(20);

        $movements = InventoryMovement::where('vendor_id', $vendor->id)
            ->with('product')
            ->latest()
            ->take(50)
            ->get();

        return view('marketplace.vendor.inventory', [
            'vendor' => $vendor,
            'products' => $products,
            'movements' => $movements,
        ]);
    }

    public function adjustStock(Request $request, Product $product)
    {
        $vendor = $this->vendor();
        abort_unless($product->vendor_id === $vendor->id, 403);

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:stock_in,stock_out,adjustment',
            'reason' => 'required|string|max:255',
        ]);

        $qty = $request->integer('quantity');
        $type = $request->input('type');

        if ($type === 'stock_out') {
            $qty = -$qty;
        }

        if ($type === 'adjustment') {
            $newStock = $request->integer('quantity');
            $delta = $newStock - $product->stock_quantity;
            $product->update(['stock_quantity' => $newStock]);
            $qty = $delta;
        } else {
            $product->update(['stock_quantity' => max(0, $product->stock_quantity + $qty)]);
        }

        InventoryMovement::create([
            'product_id' => $product->id,
            'vendor_id' => $vendor->id,
            'type' => $type,
            'quantity' => $qty,
            'reason' => $request->input('reason'),
            'changed_by_type' => \App\Models\User::class,
            'changed_by_id' => auth()->id(),
        ]);

        return back()->with('success', 'Stock adjusted successfully.');
    }

    public function orders(Request $request)
    {
        $vendor = $this->vendor();

        $query = OrderItem::with('order.user', 'product', 'order.statusHistories')
            ->where('vendor_id', $vendor->id);

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $items = $query->latest()->paginate(20)->withQueryString();

        $grouped = $items->groupBy(fn ($item) => $item->order_id);

        return view('marketplace.vendor.orders', [
            'vendor' => $vendor,
            'items' => $items,
            'grouped' => $grouped,
            'currentStatus' => $request->input('status'),
        ]);
    }

    public function updateItemStatus(Request $request, OrderItem $item)
    {
        $vendor = $this->vendor();
        abort_unless($item->vendor_id === $vendor->id, 403);

        $request->validate([
            'status' => 'required|in:confirmed,processing,packed,shipped,delivered,cancelled',
        ]);

        $item->update(['status' => $request->input('status')]);

        $order = $item->order;

        $siblingStatuses = $order->items->where('id', '!=', $item->id)->pluck('status')->unique();

        if ($request->input('status') === 'shipped' && $siblingStatuses->count() === 1 && $siblingStatuses->first() === 'shipped') {
            $order->updateStatus('shipped', 'All items shipped.');
        }

        return back()->with('success', 'Item status updated.');
    }

    public function packingSlips(Request $request)
    {
        $vendor = $this->vendor();

        $query = OrderItem::with('order.user', 'product')
            ->where('vendor_id', $vendor->id)
            ->where('status', 'packed');

        $items = $query->latest()->paginate(20)->withQueryString();

        return view('marketplace.vendor.packing-slips', [
            'vendor' => $vendor,
            'items' => $items,
        ]);
    }

    public function reports(Request $request)
    {
        $vendor = $this->vendor();

        $from = $request->input('from') ? now()->parse($request->input('from'))->startOfDay() : now()->startOfMonth();
        $to = $request->input('to') ? now()->parse($request->input('to'))->endOfDay() : now()->endOfDay();

        $items = OrderItem::with('order')
            ->where('vendor_id', $vendor->id)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $totalSales = $items->whereIn('order.status', ['delivered'])->sum('total_price');
        $totalItems = $items->sum('quantity');
        $orderCount = $items->pluck('order_id')->unique()->count();
        $commission = $totalSales * ((float) $vendor->commission_rate / 100);
        $net = $totalSales - $commission;

        $daily = $items->whereIn('order.status', ['delivered'])
            ->groupBy(fn ($i) => $i->created_at->toDateString())
            ->map(fn ($group) => [
                'sales' => $group->sum('total_price'),
                'items' => $group->sum('quantity'),
            ]);

        return view('marketplace.vendor.reports', [
            'vendor' => $vendor,
            'items' => $items,
            'totalSales' => $totalSales,
            'totalItems' => $totalItems,
            'orderCount' => $orderCount,
            'commission' => $commission,
            'net' => $net,
            'daily' => $daily,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }

    public function deleteProduct(Product $product)
    {
        $vendor = $this->vendor();
        abort_unless($product->vendor_id === $vendor->id, 403);

        $product->update(['is_active' => false]);

        return back()->with('success', 'Product deactivated.');
    }
}

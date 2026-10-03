<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Brand;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\InventoryMovement;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\ProductStatePrice;
use App\Models\Marketplace\Vendor;
use App\Models\NigerianState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class ProductController extends Controller
{
    private function vendor(): Vendor
    {
        $vendor = auth()->user()->marketplaceVendor;

        abort_unless($vendor, 403, 'No vendor account is linked to your profile.');

        return $vendor;
    }

    /**
     * Ownership guard for a single product.
     *
     * Every mutating action routes through here. `vendor_id` is always taken from
     * the authenticated user's vendor record — never from the request — so a
     * crafted form post cannot reassign or edit another vendor's catalogue.
     * Returns 404 so an id probe cannot confirm the product exists.
     */
    private function authorizeProduct(Product $product): void
    {
        abort_unless($product->vendor_id === $this->vendor()->id, 404);
    }

    public function index(Request $request)
    {
        $vendor = $this->vendor();

        $query = Product::query()
            ->where('vendor_id', $vendor->id)
            ->with([
                'category:id,name',
                'brand:id,name',
                'images',
                // `selling_price` consults active flash sales, so they must be
                // eager loaded; otherwise each row issues its own query.
                'flashSaleItems.flashSale',
            ]);

        if ($search = trim((string) $request->input('search'))) {
            $like = '%' . $search . '%';
            $query->where(fn($q) => $q->where('name', 'like', $like)->orWhere('sku', 'like', $like));
        }

        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }

        // Stock filter operates on the same per-product threshold the storefront uses.
        match ($request->input('stock')) {
            'in' => $query->where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '>', 'low_stock_threshold'),
            'low' => $query->where('stock_quantity', '>', 0)
                ->whereColumn('stock_quantity', '<=', 'low_stock_threshold'),
            'out' => $query->where('stock_quantity', '<=', 0),
            default => null,
        };

        if ($request->input('status') !== null && $request->input('status') !== '') {
            $query->where('is_active', $request->input('status') === 'active');
        }

        if ($request->input('approval') && in_array($request->input('approval'), Product::APPROVAL_STATUSES, true)) {
            $query->where('approval_status', $request->input('approval'));
        }

        match ($request->input('sort')) {
            'name' => $query->orderBy('name'),
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'stock' => $query->orderBy('stock_quantity'),
            'sold' => $query->orderByDesc('sold_count'),
            default => $query->latest(),
        };

        $products = $query->paginate(15)->withQueryString();

        // One grouped pass for the filter counters.
        $counts = Product::where('vendor_id', $vendor->id)
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive')
            ->selectRaw('SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock')
            ->selectRaw('SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= low_stock_threshold THEN 1 ELSE 0 END) as low_stock')
            ->selectRaw('SUM(CASE WHEN approval_status = \'pending\' THEN 1 ELSE 0 END) as pending_approval')
            ->first();

        return view('vendor.products.index', [
            'vendor' => $vendor,
            'products' => $products,
            'categories' => Category::active()->parents()->orderBy('name')->get(['id', 'name']),
            'counts' => $counts,
            'filters' => [
                'search' => $search,
                'category' => $request->input('category'),
                'status' => $request->input('status'),
                'stock' => $request->input('stock'),
                'approval' => $request->input('approval'),
                'sort' => $request->input('sort'),
            ],
        ]);
    }

    public function create()
    {
        return view('vendor.products.form', [
            'vendor' => $this->vendor(),
            'product' => null,
            'categories' => Category::active()->parents()->with('children')->orderBy('name')->get(),
            'brands' => Brand::active()->orderBy('name')->get(['id', 'name']),
            'states' => NigerianState::active()->ordered()->get(['id', 'name']),
            'statePrices' => [],
        ]);
    }

    public function store(Request $request)
    {
        $vendor = $this->vendor();
        $data = $this->validated($request);

        $product = Product::create($data + [
            // Ownership is assigned from the authenticated vendor, never the request.
            'vendor_id' => $vendor->id,
            'slug' => Str::slug($data['name']) . '-' . strtolower(substr(uniqid(), -5)),
            'sku' => Product::generateSku(),
            'country_of_origin' => $data['country_of_origin'] ?? 'Nigeria',
            // Vendor-submitted products enter the catalogue hidden until an
            // admin approves them for the storefront.
            'approval_status' => 'pending',
            'meta' => ['vendor_created' => true],
        ]);

        $this->storeImages($request, $product);
        $this->syncStatePrices($request, $product);

        // Record opening stock so the movement log stays complete.
        if ($product->stock_quantity > 0) {
            InventoryMovement::create([
                'product_id' => $product->id,
                'vendor_id' => $vendor->id,
                'type' => 'stock_in',
                'quantity' => $product->stock_quantity,
                'reason' => 'Opening stock on product creation',
                'changed_by_type' => \App\Models\User::class,
                'changed_by_id' => auth()->id(),
            ]);
        }

        return redirect()
            ->route('vendor.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $this->authorizeProduct($product);

        return view('vendor.products.form', [
            'vendor' => $this->vendor(),
            'product' => $product->load('images'),
            'categories' => Category::active()->parents()->with('children')->orderBy('name')->get(),
            'brands' => Brand::active()->orderBy('name')->get(['id', 'name']),
            'states' => NigerianState::active()->ordered()->get(['id', 'name']),
            'statePrices' => $product->statePrices()->pluck('price', 'state_id'),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $data = $this->validated($request);

        // `vendor_id` is intentionally absent from the validated set, so a
        // submitted vendor_id is simply ignored rather than honoured.
        $product->update($data);

        // Edits to an approved product keep its approval; a rejected product is
        // resubmitted for review when the vendor makes changes.
        if ($product->isRejected()) {
            $product->forceFill(['approval_status' => 'pending', 'rejection_reason' => null, 'approved_at' => null])->save();
        }

        $this->storeImages($request, $product);
        $this->syncStatePrices($request, $product);

        return redirect()
            ->route('vendor.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeProduct($product);

        // Deactivate rather than delete: order history references this product.
        $product->update(['is_active' => false]);

        return back()->with('success', 'Product deactivated. It is hidden from the storefront.');
    }

    public function toggleActive(Product $product)
    {
        $this->authorizeProduct($product);

        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('success', $product->is_active ? 'Product activated.' : 'Product deactivated.');
    }

    /** Inline stock update used by the inventory screen. */
    public function adjustStock(Request $request, Product $product)
    {
        $this->authorizeProduct($product);

        $data = $request->validate([
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        $previous = $product->stock_quantity;
        $new = $data['quantity'];
        $delta = $new - $previous;

        if ($delta === 0) {
            return back()->with('info', 'Stock is already ' . $new . '.');
        }

        $product->update(['stock_quantity' => $new]);

        InventoryMovement::create([
            'product_id' => $product->id,
            'vendor_id' => $product->vendor_id,
            'type' => $delta > 0 ? 'stock_in' : 'stock_out',
            'quantity' => abs($delta),
            'reason' => $data['reason'] ?? 'Manual stock adjustment',
            'changed_by_type' => \App\Models\User::class,
            'changed_by_id' => auth()->id(),
        ]);

        return back()->with('success', "Stock updated from {$previous} to {$new}.");
    }

    /**
     * Persist the per-state price overrides submitted with the product form.
     *
     * Ownership is re-checked here rather than trusted from the request, and only
     * ids that belong to an active state survive — the same guard the admin form
     * uses, so a crafted `prices[<id>]` entry cannot write arbitrary rows.
     * A blank input deactivates an existing override (falling back to the
     * national selling price) instead of silently keeping a stale value.
     */
    private function syncStatePrices(Request $request, Product $product): void
    {
        if (! $request->has('prices')) {
            return;
        }

        abort_unless($product->vendor_id === $this->vendor()->id, 404);

        $validStateIds = NigerianState::active()->pluck('id')->all();
        $prices = $request->input('prices', []);

        if (! is_array($prices)) {
            return;
        }

        $rows = [];
        $deactivate = [];
        $now = now();

        foreach ($prices as $stateId => $price) {
            $stateId = (int) $stateId;

            if (! in_array($stateId, $validStateIds, true)) {
                continue;
            }

            // A blank field means "no override for this state".
            if ($price === null || $price === '') {
                $deactivate[] = $stateId;

                continue;
            }

            if (! is_numeric($price) || (float) $price < 0) {
                continue;
            }

            $rows[] = [
                'product_id' => $product->id,
                'state_id' => $stateId,
                'price' => round((float) $price, 2),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (! empty($rows)) {
            ProductStatePrice::upsert($rows, ['product_id', 'state_id'], ['price', 'is_active', 'updated_at']);
        }

        if (! empty($deactivate)) {
            $product->statePrices()->whereIn('state_id', $deactivate)->update(['is_active' => false]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:200',
            'category_id' => 'required|integer|exists:marketplace_categories,id',
            'brand_id' => 'nullable|integer|exists:marketplace_brands,id',
            'unit' => 'nullable|string|max:30',
            'weight' => 'nullable|numeric|min:0',
            'weight_unit' => 'nullable|string|max:10',

            'price' => 'required|numeric|min:0',
            // A discount must genuinely be below the list price and never negative.
            'discount_price' => 'nullable|numeric|min:0|lt:price',
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
            'video_url' => 'nullable|url|max:255',
            'batch_number' => 'nullable|string|max:60',
            'barcode' => 'nullable|string|max:60',

            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',

            'images' => 'nullable|array|max:6',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]) + [
            'is_active' => $request->boolean('is_active', true),
            'is_featured' => $request->boolean('is_featured'),
        ];
    }

    private function storeImages(Request $request, Product $product): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        // Confirm ownership again before attaching files.
        abort_unless($product->vendor_id === $this->vendor()->id, 404);

        $hasPrimary = $product->images()->where('is_placeholder', false)->where('is_primary', true)->exists();

        foreach ($request->file('images') as $index => $image) {
            $path = $image->store('marketplace/products/' . $product->id, 'public');

            if (! $path) {
                continue;
            }

            $isPrimary = ! $hasPrimary && $index === 0;

            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $isPrimary,
                'is_placeholder' => false,
                'sort_order' => $product->images()->count(),
            ]);

            if ($isPrimary) {
                $hasPrimary = true;
                $product->images()->where('is_placeholder', true)->update(['is_primary' => false]);
            }
        }
    }

    public function destroyImage(Product $product, int $imageId)
    {
        $this->authorizeProduct($product);

        $image = $product->images()->findOrFail($imageId);

        if (! $image->is_placeholder && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Image removed.');
    }
}

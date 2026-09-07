<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Brand;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Review;
use App\Models\Marketplace\Vendor;
use App\Services\Marketplace\CartService;
use App\Services\Marketplace\MarketplaceDeliveryService;
use App\Services\Marketplace\MarketplacePricingService;
use App\Services\Marketplace\ProductService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private CartService $cartService,
        private MarketplaceDeliveryService $deliveryService,
    ) {
    }

    public function index(Request $request)
    {
        $data = $this->productService->getHomepageData(auth()->user());

        $cartCount = auth()->check() ? $this->cartService->getCartCount(auth()->user()) : 0;

        return view('marketplace.home', array_merge($data, [
            'cartCount' => $cartCount,
            'title' => 'FoodChain Marketplace',
        ]));
    }

    public function shop(Request $request)
    {
        $filters = [
            'category' => $request->integer('category') ?: null,
            'brand' => $request->integer('brand') ?: null,
            'vendor' => $request->integer('vendor') ?: null,
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'rating' => $request->input('rating'),
            'in_stock' => $request->has('in_stock') ? true : null,
            'discounted' => $request->has('discounted') ? true : null,
            'sort' => $request->input('sort'),
        ];

        $query = Product::query()
            ->active()
            ->with('category', 'brand', 'vendor', 'images')
            ->filter($filters);

        if ($term = trim((string) $request->input('q'))) {
            $query->search($term);
        }

        $products = $query->paginate(24)->withQueryString();

        return view('marketplace.shop', [
            'products' => $products,
            'categories' => $this->productService->getCategories(),
            'brands' => $this->productService->getBrands(),
            'filters' => $filters,
            'term' => $term ?? '',
            'title' => $term ? 'Search: '.$term : 'Shop',
        ]);
    }

    public function category(Request $request, Category $category)
    {
        if (! $category->is_active) {
            abort(404);
        }

        $childIds = $category->children()->pluck('id');

        $filters = [
            'brand' => $request->integer('brand') ?: null,
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'rating' => $request->input('rating'),
            'in_stock' => $request->has('in_stock') ? true : null,
            'sort' => $request->input('sort'),
        ];

        $products = Product::active()
            ->with('category', 'brand', 'images')
            ->where(function ($q) use ($category, $childIds) {
                $q->where('category_id', $category->id)
                    ->when($childIds->isNotEmpty(), fn ($q2) => $q2->orWhereIn('category_id', $childIds));
            })
            ->filter($filters)
            ->paginate(24)
            ->withQueryString();

        return view('marketplace.category', [
            'category' => $category,
            'products' => $products,
            'brands' => $this->productService->getBrands(),
            'filters' => $filters,
            'title' => $category->name,
        ]);
    }

    public function brand(Request $request, Brand $brand)
    {
        $products = Product::active()
            ->with('category', 'brand', 'images')
            ->where('brand_id', $brand->id)
            ->filter([
                'sort' => $request->input('sort'),
                'in_stock' => $request->has('in_stock') ? true : null,
            ])
            ->paginate(24)
            ->withQueryString();

        return view('marketplace.shop', [
            'products' => $products,
            'categories' => $this->productService->getCategories(),
            'brands' => $this->productService->getBrands(),
            'filters' => [],
            'term' => '',
            'title' => $brand->name,
        ]);
    }

    public function show(Request $request, Product $product)
    {
        abort_unless($product->is_active, 404);

        $this->productService->recordView($product, auth()->user());

        $related = $this->productService->getRelatedProducts($product);
        $reviews = Review::with('user')->approved()->where('product_id', $product->id)->latest()->paginate(8);
        $vendorProducts = $product->vendor ? $this->productService->getVendorProducts($product->vendor_id) : collect();
        $cartCount = auth()->check() ? $this->cartService->getCartCount(auth()->user()) : 0;

        $inCartQuantity = auth()->check()
            ? \App\Models\Marketplace\CartItem::where('user_id', auth()->id())->where('product_id', $product->id)->where('saved_for_later', false)->value('quantity')
            : 0;

        return view('marketplace.product', [
            'product' => $product->load('category', 'brand', 'vendor', 'images'),
            'related' => $related,
            'reviews' => $reviews,
            'vendorProducts' => $vendorProducts,
            'cartCount' => $cartCount,
            'inCartQuantity' => (int) $inCartQuantity,
            'deliveryEstimate' => $this->deliveryService->estimate($request->input('state', 'Lagos')),
            'states' => $this->deliveryService->getStates(),
            'title' => $product->name,
        ]);
    }

    public function vendors(Request $request)
    {
        $vendors = Vendor::approved()
            ->withCount('products')
            ->when($request->input('q'), fn ($q, $term) => $q->where(fn ($qq) => $qq->where('name', 'like', '%'.$term.'%')->orWhere('business_name', 'like', '%'.$term.'%')))
            ->paginate(12)
            ->withQueryString();

        return view('marketplace.vendors', [
            'vendors' => $vendors,
            'title' => 'Our Vendors',
        ]);
    }

    public function vendor(Request $request, Vendor $vendor)
    {
        abort_unless($vendor->isApproved(), 404);

        $products = Product::active()->with('category', 'images')->where('vendor_id', $vendor->id)
            ->filter(['sort' => $request->input('sort')])
            ->paginate(24)
            ->withQueryString();

        return view('marketplace.vendor', [
            'vendor' => $vendor,
            'products' => $products,
            'title' => $vendor->business_name ?: $vendor->name,
        ]);
    }

    public function setState(Request $request, string $state)
    {
        $canonical = app(MarketplacePricingService::class)->setState($state);

        if (! $canonical) {
            return back()->with('info', 'State not recognised, default pricing applied.');
        }

        return back()->with('success', 'Prices updated for '.$canonical.'.');
    }

    public function autocomplete(Request $request)
    {
        $term = trim((string) $request->input('q'));

        if (strlen($term) < 2) {
            return response()->json(['products' => [], 'categories' => [], 'brands' => []]);
        }

        return response()->json($this->productService->searchSuggestions($term));
    }

    public function search(Request $request)
    {
        $term = trim((string) $request->input('q'));

        if (empty($term)) {
            return redirect()->route('marketplace.shop');
        }

        return $this->shop($request);
    }

    public function flashSale()
    {
        $flashSale = \App\Models\Marketplace\FlashSale::live()->with(['items' => fn ($q) => $q->with('product.category', 'product.images')])->latest()->first();

        if (! $flashSale) {
            return redirect()->route('marketplace.shop')->with('info', 'No active flash sale right now. Check back soon!');
        }

        $products = $flashSale->items->map(fn ($item) => $item->product)->filter(fn ($p) => $p && $p->is_active);

        return view('marketplace.flash-sale', [
            'flashSale' => $flashSale,
            'products' => $products,
            'title' => $flashSale->name,
        ]);
    }
}

<?php

namespace App\Services\Marketplace;

use App\Models\Marketplace\Brand;
use App\Models\Marketplace\Category;
use App\Models\Marketplace\FlashSale;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\RecentView;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function getHomepageData(?User $user = null): array
    {
        $categories = Category::active()->with('children')->parents()->orderBy('sort_order')->get();

        $flashSale = FlashSale::with(['items' => fn ($q) => $q->with('product.category', 'product.images')])
            ->live()
            ->latest()
            ->first();

        $flashSaleProducts = $flashSale?->items->map(fn ($item) => $item->product)->filter(fn ($p) => $p && $p->is_active)->take(12);

        $featured = Product::active()->inStock()->featured()->with('category', 'images')->latest()->take(12)->get();
        $newArrivals = Product::active()->inStock()->with('category', 'images')->latest()->take(12)->get();
        $bestSellers = Product::active()->inStock()->with('category', 'images')->orderBy('sold_count', 'desc')->take(12)->get();
        $todayDeals = Product::active()->onSale()->with('category', 'images')->latest()->take(8)->get();
        $recommended = Product::active()->inStock()->where('average_rating', '>=', 3.5)->with('category', 'images')->inRandomOrder()->take(12)->get();
        $specials = Product::active()->whereNotNull('member_price')->with('category', 'images')->latest()->take(8)->get();

        $communityChoice = Product::active()->where('rating_count', '>', 0)->orderBy('average_rating', 'desc')->orderBy('rating_count', 'desc')->with('category', 'images')->take(8)->get();

        $recentlyViewed = collect();
        if ($user) {
            $recentlyViewed = RecentView::with('product.category', 'product.images')
                ->where('user_id', $user->id)
                ->orderBy('viewed_at', 'desc')
                ->take(10)
                ->get()
                ->pluck('product')
                ->filter()
                ->filter(fn ($p) => $p->is_active);
        }

        return compact(
            'categories', 'flashSale', 'flashSaleProducts', 'featured',
            'newArrivals', 'bestSellers', 'todayDeals', 'recommended',
            'specials', 'communityChoice', 'recentlyViewed',
        );
    }

    public function getBrands(): \Illuminate\Support\Collection
    {
        return Cache::remember('marketplace_brands', 3600, function () {
            return Brand::active()->withCount('products')->orderBy('name')->get();
        });
    }

    public function getCategories(): \Illuminate\Support\Collection
    {
        return Cache::remember('marketplace_categories', 3600, function () {
            return Category::active()->with('children')->parents()->orderBy('sort_order')->get();
        });
    }

    public static function getCategoriesStatic(): \Illuminate\Support\Collection
    {
        return Cache::remember('marketplace_categories_static', 3600, function () {
            return Category::active()->with('children')->parents()->orderBy('sort_order')->get();
        });
    }

    public function searchSuggestions(string $term): array
    {
        $term = trim($term);

        if (strlen($term) < 2) {
            return ['products' => collect(), 'categories' => collect(), 'brands' => collect()];
        }

        $products = Product::active()
            ->search($term)
            ->with('category', 'images')
            ->limit(6)
            ->get()
            ->map(function (Product $product) {
                $pricing = app(MarketplacePricingService::class);
                $product->setAttribute('selling_price', $pricing->priceFor($product));
                $product->setAttribute('image', $product->image);

                return $product;
            });

        $categories = Category::active()
            ->where('name', 'like', '%'.$term.'%')
            ->limit(5)
            ->get();

        $brands = Brand::active()
            ->where('name', 'like', '%'.$term.'%')
            ->limit(4)
            ->get();

        return compact('products', 'categories', 'brands');
    }

    public function getRelatedProducts(Product $product, int $limit = 8): \Illuminate\Support\Collection
    {
        return Product::active()
            ->inStock()
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->with('category', 'images')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function getVendorProducts(int $vendorId, int $limit = 8): \Illuminate\Support\Collection
    {
        return Product::active()
            ->inStock()
            ->where('vendor_id', $vendorId)
            ->with('category', 'images')
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function recordView(Product $product, ?User $user = null): void
    {
        $product->recordView();

        if ($user) {
            RecentView::updateOrCreate(
                ['user_id' => $user->id, 'product_id' => $product->id],
                ['viewed_at' => now(), 'updated_at' => now()]
            );
        }
    }

    public function getRecentSearches(User $user): array
    {
        $searches = $user->recentProductViews()
            ->with('product.category')
            ->latest('viewed_at')
            ->take(5)
            ->get()
            ->map(fn ($view) => $view->product?->category?->name)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return $searches;
    }
}

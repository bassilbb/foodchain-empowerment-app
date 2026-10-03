<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\SavedProduct;
use App\Services\Marketplace\CartService;
use App\Services\Marketplace\MarketplacePricingService;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private MarketplacePricingService $pricing,
    ) {}

    public function index()
    {
        $user = auth()->user();

        $items = SavedProduct::query()
            ->where('user_id', $user->id)
            ->with([
                'product:id,vendor_id,category_id,name,slug,price,discount_price,stock_quantity,low_stock_threshold,is_active',
                'product.vendor:id,name,business_name,slug,status',
                'product.images',
            ])
            ->latest()
            ->paginate(12);

        return view('account.wishlist.index', [
            'items' => $items,
            'wishlistCount' => $items->total(),
            'unreadNotifications' => $user->notifications()->where('is_read', false)->count(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:marketplace_products,id',
        ]);

        $user = auth()->user();

        // firstOrCreate keeps the (user_id, product_id) unique index satisfied,
        // so double-clicking "save" cannot create duplicates.
        SavedProduct::firstOrCreate([
            'user_id' => $user->id,
            'product_id' => $request->integer('product_id'),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['saved' => true]);
        }

        return back()->with('success', 'Added to your wishlist.');
    }

    public function destroy(SavedProduct $wishlist)
    {
        abort_unless($wishlist->user_id === auth()->id(), 404);

        $wishlist->delete();

        return back()->with('success', 'Removed from your wishlist.');
    }

    /**
     * Remove by product id — used from product pages where the wishlist row id
     * is not known to the client.
     */
    public function destroyByProduct(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
        ]);

        SavedProduct::where('user_id', auth()->id())
            ->where('product_id', $request->integer('product_id'))
            ->delete();

        return back()->with('success', 'Removed from your wishlist.');
    }

    /** Move a saved product into the cart using the existing CartService. */
    public function moveToCart(SavedProduct $wishlist)
    {
        abort_unless($wishlist->user_id === auth()->id(), 404);

        $product = Product::active()->find($wishlist->product_id);

        if (! $product) {
            return back()->with('error', 'This product is no longer available.');
        }

        if (! $product->isInStock()) {
            return back()->with('error', 'This product is currently out of stock.');
        }

        $result = $this->cartService->add(auth()->user(), $product->id, 1);

        if (! $result['success']) {
            return back()->with('error', $result['message'] ?? 'Unable to add to cart.');
        }

        $wishlist->delete();

        return redirect()
            ->route('marketplace.cart.index')
            ->with('success', 'Added to your cart.');
    }
}

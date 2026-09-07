<?php

namespace App\Services\Marketplace;

use App\Models\Marketplace\CartItem;
use App\Models\Marketplace\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getCart(User $user): \Illuminate\Support\Collection
    {
        return CartItem::with('product.category', 'product.images')
            ->where('user_id', $user->id)
            ->where('saved_for_later', false)
            ->get()
            ->filter(fn ($item) => $item->product);
    }

    public function getSavedItems(User $user): \Illuminate\Support\Collection
    {
        return CartItem::with('product.category', 'product.images')
            ->where('user_id', $user->id)
            ->where('saved_for_later', true)
            ->get()
            ->filter(fn ($item) => $item->product);
    }

    public function getCartCount(User $user): int
    {
        return CartItem::where('user_id', $user->id)
            ->where('saved_for_later', false)
            ->sum('quantity');
    }

    public function getSubtotal(User $user): float
    {
        return $this->getCart($user)->sum(fn ($item) => $this->priceFor($item->product) * $item->quantity);
    }

    public function priceFor(Product $product): float
    {
        return app(MarketplacePricingService::class)->priceFor($product);
    }

    public function add(User $user, int $productId, int $quantity = 1): array
    {
        $product = Product::active()->find($productId);

        if (! $product) {
            return ['success' => false, 'message' => 'Product not found.'];
        }

        if (! $product->isInStock() && ! ($product->meta['allow_backorder'] ?? false)) {
            return ['success' => false, 'message' => 'This product is out of stock.'];
        }

        $item = CartItem::firstOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id, 'saved_for_later' => false],
            ['quantity' => 0]
        );

        $newQuantity = $item->quantity + $quantity;

        if ($newQuantity > $product->stock_quantity && ! ($product->meta['allow_backorder'] ?? false)) {
            $newQuantity = max(1, $product->stock_quantity);
        }

        if ($product->stock_quantity <= 0 && ! ($product->meta['allow_backorder'] ?? false)) {
            return ['success' => false, 'message' => 'Not enough stock available.'];
        }

        $item->update(['quantity' => $newQuantity, 'saved_for_later' => false]);

        return [
            'success' => true,
            'message' => 'Added to cart.',
            'cart_count' => $this->getCartCount($user),
        ];
    }

    public function updateQuantity(User $user, int $productId, int $quantity): array
    {
        $item = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('saved_for_later', false)
            ->first();

        if (! $item) {
            return ['success' => false, 'message' => 'Item not in cart.'];
        }

        $quantity = max(1, $quantity);

        if ($quantity > $item->product->stock_quantity && ! ($item->product->meta['allow_backorder'] ?? false)) {
            $quantity = $item->product->stock_quantity;
        }

        $item->update(['quantity' => $quantity]);

        return [
            'success' => true,
            'message' => 'Cart updated.',
            'cart_count' => $this->getCartCount($user),
            'line_total' => $this->priceFor($item->product) * $quantity,
            'subtotal' => $this->getSubtotal($user),
        ];
    }

    public function remove(User $user, int $productId): array
    {
        CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();

        return [
            'success' => true,
            'message' => 'Item removed from cart.',
            'cart_count' => $this->getCartCount($user),
            'subtotal' => $this->getSubtotal($user),
        ];
    }

    public function saveForLater(User $user, int $productId): array
    {
        $item = CartItem::where('user_id', $user->id)->where('product_id', $productId)->first();

        if ($item) {
            $item->update(['saved_for_later' => true]);
        }

        return [
            'success' => true,
            'message' => 'Saved for later.',
            'cart_count' => $this->getCartCount($user),
        ];
    }

    public function moveToCart(User $user, int $productId): array
    {
        $item = CartItem::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->where('saved_for_later', true)
            ->first();

        if ($item) {
            $item->update(['saved_for_later' => false]);
        }

        return [
            'success' => true,
            'message' => 'Moved to cart.',
            'cart_count' => $this->getCartCount($user),
        ];
    }

    public function clear(User $user): void
    {
        CartItem::where('user_id', $user->id)->delete();
    }
}

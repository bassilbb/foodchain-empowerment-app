<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Services\Marketplace\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService,
    ) {
    }

    public function index()
    {
        $user = auth()->user();

        return view('marketplace.cart', [
            'cartItems' => $this->cartService->getCart($user),
            'savedItems' => $this->cartService->getSavedItems($user),
            'subtotal' => $this->cartService->getSubtotal($user),
            'cartCount' => $this->cartService->getCartCount($user),
            'title' => 'Shopping Cart',
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $result = $this->cartService->add(auth()->user(), $request->integer('product_id'), $request->integer('quantity'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        return back()->with('success', $result['message']);
    }

    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        $result = $this->cartService->updateQuantity(auth()->user(), $request->integer('product_id'), $request->integer('quantity'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }

    public function remove(Request $request)
    {
        $request->validate(['product_id' => 'required|integer']);

        $result = $this->cartService->remove(auth()->user(), $request->integer('product_id'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result);
        }

        return back()->with('success', $result['message']);
    }

    public function saveForLater(Request $request)
    {
        $request->validate(['product_id' => 'required|integer']);

        $this->cartService->saveForLater(auth()->user(), $request->integer('product_id'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Item saved for later.');
    }

    public function moveToCart(Request $request)
    {
        $request->validate(['product_id' => 'required|integer']);

        $this->cartService->moveToCart(auth()->user(), $request->integer('product_id'));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Item moved to cart.');
    }

    public function count()
    {
        return response()->json(['count' => $this->cartService->getCartCount(auth()->user())]);
    }
}

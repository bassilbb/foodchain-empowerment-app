<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Marketplace\Product;
use App\Models\Marketplace\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $user = auth()->user();

        $purchased = $user->marketplaceOrders()
            ->whereHas('items', fn ($q) => $q->where('product_id', $product->id))
            ->whereIn('status', ['delivered'])
            ->exists();

        if (! $purchased) {
            return back()->with('error', 'You can only review products you have purchased.');
        }

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'title' => 'nullable|string|max:120',
            'comment' => 'nullable|string|max:2000',
        ]);

        Review::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            [
                'rating' => $request->integer('rating'),
                'title' => $request->input('title'),
                'comment' => $request->input('comment'),
                'is_approved' => false,
            ]
        );

        return back()->with('success', 'Thank you! Your review has been submitted for approval.');
    }
}

<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\FoodDistribution;
use Illuminate\Http\Request;

class DistributionController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $distributions = $user->distributions()
            ->with(['tier', 'items.foodItem'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.distributions.index', compact('distributions'));
    }

    public function show(FoodDistribution $distribution)
    {
        $user = auth()->user();

        if ($distribution->user_id !== $user->id) {
            abort(403);
        }

        $distribution->load(['tier', 'items.foodItem']);

        return view('user.distributions.show', compact('distribution'));
    }
}

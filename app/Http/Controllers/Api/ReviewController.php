<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bar;
use App\Models\BarReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    /**
     * Get reviews for a bar
     */
    public function index($barId, Request $request)
    {
        $bar = Bar::findOrFail($barId);
        
        $page = $request->get('page', 1);
        $cacheKey = "api.bars.{$barId}.reviews.page.{$page}";
        
        // Cache for 5 minutes
        $response = Cache::remember($cacheKey, 300, function () use ($barId) {
            $reviews = BarReview::with('user')
                ->where('bar_id', $barId)
                ->where('status', 'approved')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return [
                'data' => $reviews->items(),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'last_page' => $reviews->lastPage(),
                    'per_page' => $reviews->perPage(),
                    'total' => $reviews->total(),
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $response['data'],
            'pagination' => $response['pagination'],
        ]);
    }

    /**
     * Create a new review (requires authentication)
     */
    public function store(Request $request, $barId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:1000',
        ]);

        $bar = Bar::findOrFail($barId);

        // Check if user already reviewed this bar
        $existingReview = BarReview::where('bar_id', $barId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this bar.',
            ], 422);
        }

        $review = BarReview::create([
            'bar_id' => $barId,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending', // Reviews need admin approval
        ]);

        // Clear bar cache when review is created
        Cache::forget("api.bars.show.{$barId}");
        // Clear review caches for this bar
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("api.bars.{$barId}.reviews.page.{$i}");
        }

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully. It will be visible after approval.',
            'data' => $review->load('user'),
        ], 201);
    }
}


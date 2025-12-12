<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ReviewResource;
use App\Http\Requests\Api\StoreReviewRequest;
use App\Models\Bar;
use App\Models\BarReview;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ReviewController extends Controller
{
    use ApiResponse;

    /**
     * Get reviews for a bar
     */
    public function index($barId, Request $request)
    {
        try {
            // Verify bar exists
            $bar = Bar::where('status', 1)->findOrFail($barId);
            
            $page = max(1, (int) $request->get('page', 1));
            $perPage = min(max(1, (int) $request->get('per_page', 10)), 50);
            $cacheKey = "api.bars.{$barId}.reviews.page.{$page}.per_page.{$perPage}";
            
            // Cache for 5 minutes
            $response = Cache::remember($cacheKey, 300, function () use ($barId, $perPage) {
                $reviews = BarReview::select('id', 'bar_id', 'user_id', 'rating', 'comment', 
                        'status', 'created_at', 'updated_at')
                    ->with(['user:id,name,image'])
                    ->where('bar_id', $barId)
                    ->where('status', 'approved')
                    ->orderBy('created_at', 'desc')
                    ->paginate($perPage);

                return [
                    'data' => ReviewResource::collection($reviews->items()),
                    'pagination' => [
                        'current_page' => $reviews->currentPage(),
                        'last_page' => $reviews->lastPage(),
                        'per_page' => $reviews->perPage(),
                        'total' => $reviews->total(),
                        'from' => $reviews->firstItem(),
                        'to' => $reviews->lastItem(),
                    ],
                ];
            });

            // Return response with pagination
            return $this->successResponse(
                $response['data'],
                'Reviews retrieved successfully',
                200,
                $response['pagination']
            )->withHeaders([
                'X-Pagination-Current-Page' => $response['pagination']['current_page'],
                'X-Pagination-Last-Page' => $response['pagination']['last_page'],
                'X-Pagination-Per-Page' => $response['pagination']['per_page'],
                'X-Pagination-Total' => $response['pagination']['total'],
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Bar not found');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve reviews: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create a new review (requires authentication)
     */
    public function store(StoreReviewRequest $request, $barId)
    {
        try {
            // Verify bar exists and is active
            $bar = Bar::where('status', 1)->findOrFail($barId);

            // Check if user already reviewed this bar
            $existingReview = BarReview::where('bar_id', $barId)
                ->where('user_id', Auth::id())
                ->first();

            if ($existingReview) {
                return $this->errorResponse('You have already reviewed this bar.', 422);
            }

            $review = BarReview::create([
                'bar_id' => $barId,
                'user_id' => Auth::id(),
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'pending', // Reviews need admin approval
            ]);

            // Clear caches
            $this->clearBarCaches($barId);

            return $this->successResponse(
                new ReviewResource($review->load('user')),
                'Review submitted successfully. It will be visible after approval.',
                201
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Bar not found');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to submit review: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Clear bar-related caches
     */
    private function clearBarCaches($barId): void
    {
        // Clear bar detail cache
        Cache::forget("api.bars.show.{$barId}");
        
        // Clear bar list caches (pattern matching would be better with Redis tags)
        // For now, clear common pages
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("api.bars.{$barId}.reviews.page.{$i}.per_page.10");
            Cache::forget("api.bars.{$barId}.reviews.page.{$i}.per_page.20");
        }
    }
}

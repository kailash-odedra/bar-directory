<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\BarResource;
use App\Models\Bar;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BarController extends Controller
{
    use ApiResponse;

    /**
     * Get list of bars with filters
     */
    public function index(Request $request)
    {
        try {
            // Build cache key from all request parameters
            $cacheKey = $this->buildCacheKey('api.bars.list', $request->all());
            
            // Cache for 5 minutes
            $response = Cache::remember($cacheKey, 300, function () use ($request) {
                $query = $this->buildBarQuery($request);
                
                // Pagination
                $perPage = min($request->get('per_page', 12), 50); // Max 50 per page
                $bars = $query->paginate($perPage);
                
                return [
                    'data' => BarResource::collection($bars->items()),
                    'pagination' => [
                        'current_page' => $bars->currentPage(),
                        'last_page' => $bars->lastPage(),
                        'per_page' => $bars->perPage(),
                        'total' => $bars->total(),
                        'from' => $bars->firstItem(),
                        'to' => $bars->lastItem(),
                    ],
                ];
            });

            // Return response with pagination
            return $this->successResponse(
                $response['data'], 
                'Bars retrieved successfully',
                200,
                $response['pagination']
            )->withHeaders([
                'X-Pagination-Current-Page' => $response['pagination']['current_page'],
                'X-Pagination-Last-Page' => $response['pagination']['last_page'],
                'X-Pagination-Per-Page' => $response['pagination']['per_page'],
                'X-Pagination-Total' => $response['pagination']['total'],
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve bars: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get single bar details
     */
    public function show($id)
    {
        try {
            $cacheKey = "api.bars.show.{$id}";
            
            // Cache for 10 minutes
            $bar = Cache::remember($cacheKey, 600, function () use ($id) {
                return Bar::select('id', 'name', 'slug', 'short_description', 'full_description', 
                        'cover_image', 'logo', 'is_featured', 'status', 'created_at', 'updated_at')
                    ->with([
                        'location:id,bar_id,city,address,zipcode,region,latitude,longitude,state_id,country_id',
                        'location.state:id,name',
                        'location.country:id,name',
                        'tags:id,name,slug',
                        'images:id,bar_id,path,type,alt',
                        'events:id,bar_id,title,description,start_time,end_time,image,ticket_link,type'
                    ])
                    ->withCount(['reviews as approved_reviews_count' => function ($q) {
                        $q->where('status', 'approved');
                    }])
                    ->withAvg(['reviews as avg_rating' => function ($q) {
                        $q->where('status', 'approved');
                    }], 'rating')
                    ->where('status', 1)
                    ->findOrFail($id);
            });

            return $this->successResponse(new BarResource($bar), 'Bar retrieved successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('Bar not found');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve bar: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Build optimized query for bars list
     */
    private function buildBarQuery(Request $request)
    {
        $query = Bar::select('id', 'name', 'slug', 'short_description', 'cover_image', 
                'logo', 'is_featured', 'status', 'created_at', 'updated_at')
            ->with([
                'location:id,bar_id,city,state_id,country_id',
                'location.state:id,name',
                'location.country:id,name',
                'tags:id,name,slug'
            ])
            ->withCount(['reviews as approved_reviews_count' => function ($q) {
                $q->where('status', 'approved');
            }])
            ->withAvg(['reviews as avg_rating' => function ($q) {
                $q->where('status', 'approved');
            }], 'rating')
            ->where('status', 1);

        // Search by name
        if ($request->has('search') && $request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%");
            });
        }

        // Filter by featured
        if ($request->has('featured')) {
            $featured = filter_var($request->get('featured'), FILTER_VALIDATE_BOOLEAN);
            $query->where('is_featured', $featured);
        }

        // Filter by city
        if ($request->has('city') && $request->filled('city')) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('city', 'like', "%{$request->get('city')}%");
            });
        }

        // Filter by state
        if ($request->has('state_id') && $request->filled('state_id')) {
            $query->whereHas('location', function($q) use ($request) {
                $q->where('state_id', $request->get('state_id'));
            });
        }

        // Filter by tags
        if ($request->has('tags') && is_array($request->get('tags'))) {
            $tagIds = array_filter($request->get('tags'));
            if (!empty($tagIds)) {
                $query->whereHas('tags', function($q) use ($tagIds) {
                    $q->whereIn('bar_tags.id', $tagIds);
                });
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['created_at', 'name', 'avg_rating', 'is_featured'];
        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'avg_rating') {
                $query->orderByRaw('(SELECT AVG(rating) FROM bar_reviews WHERE bar_reviews.bar_id = bars.id AND bar_reviews.status = "approved") ' . strtoupper($sortOrder));
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * Build cache key from request parameters
     */
    private function buildCacheKey(string $prefix, array $params): string
    {
        // Remove null/empty values and sort for consistent keys
        $filtered = array_filter($params, fn($value) => $value !== null && $value !== '');
        ksort($filtered);
        
        return $prefix . '.' . md5(json_encode($filtered));
    }
}

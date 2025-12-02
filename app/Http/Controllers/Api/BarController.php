<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BarController extends Controller
{
    /**
     * Get list of bars
     */
    public function index(Request $request)
    {
        // Create cache key based on request parameters
        $cacheKey = 'api.bars.list.' . md5(json_encode([
            'search' => $request->get('search'),
            'featured' => $request->get('featured'),
            'page' => $request->get('page', 1),
            'per_page' => $request->get('per_page', 12),
        ]));

        // Cache for 5 minutes
        $response = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Bar::with(['location.state', 'location.country', 'tags'])
                ->withCount(['reviews as approved_reviews_count' => function ($q) {
                    $q->where('status', 'approved');
                }])
                ->withAvg(['reviews as avg_rating' => function ($q) {
                    $q->where('status', 'approved');
                }], 'rating')
                ->where('status', 1)
                ->orderBy('created_at', 'desc');

            // Search by name
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // Filter by featured
            if ($request->has('featured')) {
                $query->where('is_featured', $request->featured === 'true');
            }

            // Pagination
            $perPage = $request->get('per_page', 12);
            $bars = $query->paginate($perPage);

            // Format bars data with full image URLs and location info
            $formattedBars = $bars->getCollection()->map(function ($bar) {
                return $this->formatBarDataForList($bar);
            });

            return [
                'data' => $formattedBars->values()->all(),
                'pagination' => [
                    'current_page' => $bars->currentPage(),
                    'last_page' => $bars->lastPage(),
                    'per_page' => $bars->perPage(),
                    'total' => $bars->total(),
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
     * Format bar data for list view (optimized - no reviews loaded)
     */
    private function formatBarDataForList($bar)
    {
        // Format cover image URL (no file system check - just generate URL)
        $coverImageUrl = $bar->cover_image ? Storage::url($bar->cover_image) : null;

        // Format location data
        $locationData = null;
        if ($bar->location) {
            $location = $bar->location;
            $locationData = [
                'city' => $location->city ?? null,
                'city_name' => $location->city ?? null,
                'state_name' => $location->state->name ?? null,
                'country_name' => $location->country->name ?? null,
                'address' => $location->address ?? null,
                'zipcode' => $location->zipcode ?? null,
                'region' => $location->region ?? null,
            ];
        }

        return [
            'id' => $bar->id,
            'name' => $bar->name,
            'slug' => $bar->slug,
            'short_description' => $bar->short_description,
            'cover_image' => $coverImageUrl,
            'logo' => $bar->logo ? Storage::url($bar->logo) : null,
            'location' => $locationData,
            'tags' => $bar->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ];
            }),
            'avg_rating' => round($bar->avg_rating ?? 0, 1),
            'approved_reviews_count' => $bar->approved_reviews_count ?? 0,
            'is_featured' => $bar->is_featured,
            'status' => $bar->status,
            'created_at' => $bar->created_at,
            'updated_at' => $bar->updated_at,
        ];
    }

    /**
     * Format bar data for detail view (with full data)
     */
    private function formatBarData($bar)
    {
        // Format cover image URL (no file system check)
        $coverImageUrl = $bar->cover_image ? Storage::url($bar->cover_image) : null;

        // Format location data
        $locationData = null;
        if ($bar->location) {
            $location = $bar->location;
            $locationData = [
                'city' => $location->city ?? null,
                'city_name' => $location->city ?? null,
                'state_name' => $location->state->name ?? null,
                'country_name' => $location->country->name ?? null,
                'address' => $location->address ?? null,
                'zipcode' => $location->zipcode ?? null,
                'region' => $location->region ?? null,
            ];
        }

        return [
            'id' => $bar->id,
            'name' => $bar->name,
            'slug' => $bar->slug,
            'short_description' => $bar->short_description,
            'full_description' => $bar->full_description,
            'cover_image' => $coverImageUrl,
            'logo' => $bar->logo ? Storage::url($bar->logo) : null,
            'location' => $locationData,
            'tags' => $bar->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ];
            }),
            'is_featured' => $bar->is_featured,
            'status' => $bar->status,
            'created_at' => $bar->created_at,
            'updated_at' => $bar->updated_at,
        ];
    }

    /**
     * Get single bar details
     */
    public function show($id)
    {
        $cacheKey = 'api.bars.show.' . $id;
        
        // Cache for 10 minutes
        $formattedBar = Cache::remember($cacheKey, 600, function () use ($id) {
            $bar = Bar::with(['location.state', 'location.country', 'tags', 'images', 'events'])
                ->withCount(['reviews as approved_reviews_count' => function ($q) {
                    $q->where('status', 'approved');
                }])
                ->withAvg(['reviews as avg_rating' => function ($q) {
                    $q->where('status', 'approved');
                }], 'rating')
                ->where('status', 1)
                ->findOrFail($id);

            $formattedBar = $this->formatBarData($bar);

            // Use calculated values from eager loading
            $formattedBar['average_rating'] = round($bar->avg_rating ?? 0, 1);
            $formattedBar['total_reviews'] = $bar->approved_reviews_count ?? 0;

            // Format images (no file system check)
            $formattedBar['images'] = $bar->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => $image->path ? (filter_var($image->path, FILTER_VALIDATE_URL) ? $image->path : Storage::url($image->path)) : null,
                    'type' => $image->type,
                    'alt' => $image->alt,
                ];
            });

            // Format events
            $formattedBar['events'] = $bar->events->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'start_time' => $event->start_time,
                    'end_time' => $event->end_time,
                    'image' => $event->image ? Storage::url($event->image) : null,
                    'ticket_link' => $event->ticket_link,
                    'type' => $event->type,
                ];
            });

            return $formattedBar;
        });

        return response()->json([
            'success' => true,
            'data' => $formattedBar,
        ]);
    }
}


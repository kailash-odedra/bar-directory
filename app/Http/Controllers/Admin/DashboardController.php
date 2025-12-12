<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bar;
use App\Models\Claim;
use App\Models\BarReview;
use App\Models\BarTag;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function analytics()
    {
        // Use single cache key for all dashboard stats to reduce cache calls
        $cacheKey = 'dashboard.analytics.all';
        $cacheTime = 300; // 5 minutes
        
        $stats = Cache::remember($cacheKey, $cacheTime, function() {
            // Calculate all statistics in one go to reduce database queries
            return [
                'totalBars' => Bar::count(),
                'claimedBars' => Bar::where('claimed', true)->count(),
                'pendingClaims' => Claim::where('verification_status', 'pending')->count(),
                'activeReviews' => BarReview::where('status', 'approved')->count(),
                'featuredBars' => Bar::where('is_featured', true)->count(),
                'reviewsByStatus' => BarReview::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get()
                    ->pluck('count', 'status'),
                'barsByStatus' => Bar::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get()
                    ->pluck('count', 'status'),
            ];
        });
        
        // Extract cached values
        $totalBars = $stats['totalBars'];
        $claimedBars = $stats['claimedBars'];
        $pendingClaims = $stats['pendingClaims'];
        $activeReviews = $stats['activeReviews'];
        $featuredBars = $stats['featuredBars'];
        $reviewsByStatus = $stats['reviewsByStatus'];
        $barsByStatus = $stats['barsByStatus'];

        // Top-rated bars (4+ stars) - Cached separately for longer (10 minutes)
        $topRatedBars = Cache::remember('dashboard.top_rated_bars', 600, function() {
            // Single optimized query to get top rated bars with ratings
            $topRatedData = DB::table('bar_reviews')
                ->where('status', 'approved')
                ->select('bar_id', DB::raw('AVG(rating) as avg_rating'))
                ->groupBy('bar_id')
                ->havingRaw('AVG(rating) >= 4')
                ->orderBy('avg_rating', 'desc')
                ->limit(10)
                ->get();

            if ($topRatedData->isEmpty()) {
                return collect([]);
            }

            $topRatedBarIds = $topRatedData->pluck('bar_id');
            $ratingsMap = $topRatedData->keyBy('bar_id');

            return Bar::select('id', 'name', 'slug', 'cover_image')
                ->with(['location:id,bar_id,city,state_id,country_id', 'location.state:id,name', 'location.country:id,name'])
                ->whereIn('id', $topRatedBarIds)
                ->get()
                ->map(function($bar) use ($ratingsMap) {
                    $bar->avg_rating = round($ratingsMap[$bar->id]->avg_rating ?? 0, 1);
                    return $bar;
                })
                ->sortByDesc('avg_rating')
                ->values();
        });

        // Most searched cities (based on bar count) - Cached for 10 minutes
        $mostSearchedCities = Cache::remember('dashboard.most_searched_cities', 600, function() {
            return Location::select('city', DB::raw('count(*) as bar_count'))
                ->whereNotNull('city')
                ->groupBy('city')
                ->orderBy('bar_count', 'desc')
                ->limit(10)
                ->get();
        });

        // Trending tags (most used tags) - Cached for 10 minutes, optimize with select
        $trendingTags = Cache::remember('dashboard.trending_tags', 600, function() {
            return BarTag::select('id', 'name', 'slug')
                ->withCount('bars')
                ->orderBy('bars_count', 'desc')
                ->limit(10)
                ->get();
        });

        return view('admin.dashboard.analytics', [
            'catName' => 'dashboard',
            'title' => 'Dashboard Overview',
            'breadcrumbs' => ['Dashboard', 'Analytics'],
            'scrollspy' => 0,
            'simplePage' => 0,
            'totalBars' => $totalBars,
            'claimedBars' => $claimedBars,
            'pendingClaims' => $pendingClaims,
            'activeReviews' => $activeReviews,
            'featuredBars' => $featuredBars,
            'topRatedBars' => $topRatedBars,
            'mostSearchedCities' => $mostSearchedCities,
            'trendingTags' => $trendingTags,
            'reviewsByStatus' => $reviewsByStatus,
            'barsByStatus' => $barsByStatus,
        ]);
    }
}

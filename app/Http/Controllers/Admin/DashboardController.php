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
        // Calculate statistics with caching (5 minutes)
        $totalBars = Cache::remember('dashboard.total_bars', 300, fn() => Bar::count());
        $claimedBars = Cache::remember('dashboard.claimed_bars', 300, fn() => Bar::where('claimed', true)->count());
        $pendingClaims = Cache::remember('dashboard.pending_claims', 60, fn() => Claim::where('verification_status', 'pending')->count());
        $activeReviews = Cache::remember('dashboard.active_reviews', 300, fn() => BarReview::where('status', 'approved')->count());
        $featuredBars = Cache::remember('dashboard.featured_bars', 300, fn() => Bar::where('is_featured', true)->count());

        // Top-rated bars (4+ stars) - Cached for 10 minutes
        $topRatedBars = Cache::remember('dashboard.top_rated_bars', 600, function() {
            $topRatedBarsData = DB::table('bars')
                ->join('bar_reviews', 'bars.id', '=', 'bar_reviews.bar_id')
                ->where('bar_reviews.status', 'approved')
                ->select('bars.id', DB::raw('AVG(bar_reviews.rating) as avg_rating'))
                ->groupBy('bars.id')
                ->havingRaw('AVG(bar_reviews.rating) >= 4')
                ->orderBy('avg_rating', 'desc')
                ->limit(10)
                ->get()
                ->keyBy('id');

            return Bar::with(['location.state', 'location.country'])
                ->whereIn('id', $topRatedBarsData->pluck('id'))
                ->get()
                ->map(function($bar) use ($topRatedBarsData) {
                    $bar->avg_rating = $topRatedBarsData[$bar->id]->avg_rating ?? 0;
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

        // Trending tags (most used tags) - Cached for 10 minutes
        $trendingTags = Cache::remember('dashboard.trending_tags', 600, function() {
            return BarTag::withCount('bars')
                ->orderBy('bars_count', 'desc')
                ->limit(10)
                ->get();
        });

        // Reviews by status - Cached for 5 minutes
        $reviewsByStatus = Cache::remember('dashboard.reviews_by_status', 300, function() {
            return BarReview::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');
        });

        // Bars by status - Cached for 5 minutes
        $barsByStatus = Cache::remember('dashboard.bars_by_status', 300, function() {
            return Bar::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status');
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

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

class DashboardController extends Controller
{
    public function analytics()
    {
        // Calculate statistics
        $totalBars = Bar::count();
        $claimedBars = Bar::where('claimed', true)->count();
        $pendingClaims = Claim::where('verification_status', 'pending')->count();
        $activeReviews = BarReview::where('status', 'approved')->count();
        $featuredBars = Bar::where('is_featured', true)->count();

        // Top-rated bars (4+ stars) - Using raw query to avoid GROUP BY issues
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

        $topRatedBars = Bar::with(['location.state', 'location.country'])
            ->whereIn('id', $topRatedBarsData->pluck('id'))
            ->get()
            ->map(function($bar) use ($topRatedBarsData) {
                $bar->avg_rating = $topRatedBarsData[$bar->id]->avg_rating ?? 0;
                return $bar;
            })
            ->sortByDesc('avg_rating')
            ->values();

        // Most searched cities (based on bar count)
        $mostSearchedCities = Location::select('city', DB::raw('count(*) as bar_count'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('bar_count', 'desc')
            ->limit(10)
            ->get();

        // Trending tags (most used tags)
        $trendingTags = BarTag::withCount('bars')
            ->orderBy('bars_count', 'desc')
            ->limit(10)
            ->get();

        // Reviews by status
        $reviewsByStatus = BarReview::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Bars by status
        $barsByStatus = Bar::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

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

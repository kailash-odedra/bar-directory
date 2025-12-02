<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarReview;
use Illuminate\Support\Facades\Cache;

class BarReviewController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $status = $request->get('status');

        // Optimize: Select only needed columns
        $reviews = BarReview::select('bar_reviews.id', 'bar_reviews.bar_id', 'bar_reviews.user_id', 'bar_reviews.rating', 'bar_reviews.comment', 'bar_reviews.status', 'bar_reviews.created_at')
            ->with(['bar:id,name', 'user:id,name'])
            ->when($q, fn($q1) => $q1->where('comment', 'like', "%{$q}%"))
            ->when($status, fn($q2) => $q2->where('status', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'title' => 'Bar Reviews',
            'catName' => 'bar',
            'subCatName' => 'reviews',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(Request $request, BarReview $barReview)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'status' => 'required|in:pending,approved,hidden',
        ]);

        $barReview->update($data);
        
        // Clear caches
        Cache::forget('dashboard.active_reviews');
        Cache::forget('dashboard.reviews_by_status');
        Cache::forget('dashboard.top_rated_bars');
        Cache::forget("api.bars.show.{$barReview->bar_id}");
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("api.bars.{$barReview->bar_id}.reviews.page.{$i}");
        }
        
        return back()->with('success', 'Review updated successfully.');
    }

    public function destroy(BarReview $barReview)
    {
        $barId = $barReview->bar_id;
        $barReview->delete();
        
        // Clear caches
        Cache::forget('dashboard.active_reviews');
        Cache::forget('dashboard.reviews_by_status');
        Cache::forget('dashboard.top_rated_bars');
        Cache::forget("api.bars.show.{$barId}");
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("api.bars.{$barId}.reviews.page.{$i}");
        }
        
        return back()->with('success', 'Review removed.');
    }

    // AJAX Approve
    public function approve(BarReview $barReview)
    {
        $barReview->status = 'approved';
        $barReview->save();

        // Clear caches
        Cache::forget('dashboard.active_reviews');
        Cache::forget('dashboard.reviews_by_status');
        Cache::forget('dashboard.top_rated_bars');
        Cache::forget("api.bars.show.{$barReview->bar_id}");
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("api.bars.{$barReview->bar_id}.reviews.page.{$i}");
        }

        return response()->json(['success' => true]);
    }

    // AJAX Hide
    public function hide(BarReview $barReview)
    {
        $barReview->status = 'hidden';
        $barReview->save();

        // Clear caches
        Cache::forget('dashboard.active_reviews');
        Cache::forget('dashboard.reviews_by_status');
        Cache::forget('dashboard.top_rated_bars');
        Cache::forget("api.bars.show.{$barReview->bar_id}");
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget("api.bars.{$barReview->bar_id}.reviews.page.{$i}");
        }

        return response()->json(['success' => true]);
    }
}

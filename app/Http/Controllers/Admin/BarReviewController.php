<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarReview;

class BarReviewController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $status = $request->get('status');

        $reviews = BarReview::with(['bar', 'user'])
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
        return back()->with('success', 'Review updated successfully.');
    }

    public function destroy(BarReview $barReview)
    {
        $barReview->delete();
        return back()->with('success', 'Review removed.');
    }

    // AJAX Approve
    public function approve($id)
    {
        $review = BarReview::findOrFail($id);
        $review->status = 'approved';
        $review->save();

        return response()->json(['success' => true]);
    }

    // AJAX Hide
    public function hide($id)
    {
        $review = BarReview::findOrFail($id);
        $review->status = 'hidden';
        $review->save();

        return response()->json(['success' => true]);
    }
}

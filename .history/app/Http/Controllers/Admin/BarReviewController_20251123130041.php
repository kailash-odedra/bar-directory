<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarReview;
use App\Models\Bar;

class BarReviewController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $status = $request->get('status');

        $reviews = BarReview::with(['bar','user'])
                    ->when($q, fn($b) => $b->where('comment','like', "%{$q}%"))
                    ->when($status, fn($b) => $b->where('status', $status))
                    ->orderBy('created_at','desc')
                    ->paginate(25)->withQueryString();

        return view('admin.reviews.index', compact('reviews'))->with('catName','bar');
    }

    public function show(BarReview $barReview)
    {
        return view('admin.reviews.show', compact('barReview'))->with('catName','bar');
    }

    public function edit(BarReview $barReview)
    {
        return view('admin.reviews.edit', compact('barReview'))->with('catName','bar');
    }

    public function update(Request $request, BarReview $barReview)
    {
        $data = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
            'status' => 'required|in:pending,approved,hidden',
        ]);

        $barReview->update($data);

        return redirect(url('admin/bar-reviews'))->with('success','Review updated.');
    }

    public function destroy(BarReview $barReview)
    {
        $barReview->delete();
        return redirect(url('admin/bar-reviews'))->with('success','Review deleted.');
    }

    // quick approve endpoint (optional)
    public function approve(BarReview $barReview)
    {
        $barReview->update(['status' => 'approved']);
        return back()->with('success','Review approved.');
    }
}

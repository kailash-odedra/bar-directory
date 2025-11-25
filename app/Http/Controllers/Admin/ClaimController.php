<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Bar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $claims = Claim::with(['bar','user'])
            ->when($q, fn($query) =>
                $query->whereHas('bar', fn($q2) =>
                    $q2->where('name', 'like', "%{$q}%")
                )
            )
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.claims.index', [
            'claims' => $claims,
            'title' => 'Claims List',
            'catName' => 'bar',
            'subCatName' => 'claims',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function show(Claim $claim)
    {
        return view('admin.claims.show', [
            'claim' => $claim,
            'title' => 'View Claim',
            'catName' => 'bar',
            'subCatName' => 'claims',
        ]);
    }

    public function approve($id)
    {
        $claim = Claim::findOrFail($id);
        $claim->status = 'approved';
        $claim->save();

        return response()->json(['success' => true, 'status' => 'approved']);
    }

    public function reject($id)
    {
        $claim = Claim::findOrFail($id);
        $claim->status = 'rejected';
        $claim->save();

        return response()->json(['success' => true, 'status' => 'rejected']);
    }

    public function destroy(Claim $claim)
    {
        if ($claim->verify_document) {
            Storage::disk('public')->delete($claim->verify_document);
        }

        $claim->delete();
        return back()->with('success', 'Claim deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Claim;
use App\Models\Bar;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $status = $request->get('status');

        $claims = Claim::with(['bar','user'])
            ->when($q, fn($b) => $b->where('email','like', "%{$q}%"))
            ->when($status, fn($b) => $b->where('status', $status))
            ->orderBy('created_at','desc')
            ->paginate(25)->withQueryString();

        return view('admin.claims.index', compact('claims'))->with('catName','bar');
    }

    public function show(Claim $claim)
    {
        return view('admin.claims.show', compact('claim'))->with('catName','bar');
    }

    public function edit(Claim $claim)
    {
        return view('admin.claims.edit', compact('claim'))->with('catName','bar');
    }

    public function update(Request $request, Claim $claim)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $claim->update($data);

        // if approved: mark bar claimed and set claimed_by
        if ($data['status'] === 'approved') {
            $claim->bar->update(['claimed' => true, 'claimed_by' => $claim->user_id, 'verified' => true]);
        }

        return redirect(url('admin/claims'))->with('success','Claim updated.');
    }

    public function destroy(Claim $claim)
    {
        if ($claim->verify_document) {
            Storage::disk('public')->delete($claim->verify_document);
        }
        $claim->delete();
        return redirect(url('admin/claims'))->with('success','Claim removed.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Bar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ClaimController extends Controller
{
    // Public Claim Form Methods
    public function createPublic($barId = null)
    {
        $bar = null;
        if ($barId) {
            $bar = Bar::find($barId);
        }

        return view('claims.create', [
            'bar' => $bar,
            'title' => 'Claim This Bar'
        ]);
    }

    public function storePublic(Request $request)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email_address' => 'required|email|max:255',
            'role' => 'required|in:Owner,Manager,Marketing Lead',
            'relationship_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
            'comments' => 'nullable|string',
        ]);

        // Create or get user account
        $user = User::firstOrCreate(
            ['email' => $data['email_address']],
            [
                'name' => $data['full_name'],
                'password' => Hash::make(Str::random(16)), // Random password
            ]
        );

        // Handle file upload
        if ($request->hasFile('relationship_proof')) {
            $data['relationship_proof'] = $request->file('relationship_proof')->store('claims/proofs', 'public');
        }

        // Create claim
        $claim = Claim::create([
            'bar_id' => $data['bar_id'],
            'user_id' => $user->id,
            'full_name' => $data['full_name'],
            'phone_number' => $data['phone_number'],
            'email_address' => $data['email_address'],
            'role' => $data['role'],
            'relationship_proof' => $data['relationship_proof'] ?? null,
            'comments' => $data['comments'] ?? null,
            'status' => 'pending',
            'verification_status' => 'pending',
        ]);

        return redirect()->route('claims.success', $claim->claim_request_id)
            ->with('success', 'Your claim request has been submitted successfully! Claim ID: ' . $claim->claim_request_id);
    }

    public function successPublic($claimRequestId)
    {
        $claim = Claim::where('claim_request_id', $claimRequestId)->firstOrFail();
        
        return view('claims.success', [
            'claim' => $claim,
            'title' => 'Claim Submitted Successfully'
        ]);
    }

    // Admin Methods
    public function index(Request $request)
    {
        $q = $request->get('q');
        $status = $request->get('status');

        // Optimize: Select only needed columns
        $claims = Claim::select('claims.id', 'claims.bar_id', 'claims.user_id', 'claims.full_name', 'claims.email_address', 'claims.claim_request_id', 'claims.verification_status', 'claims.verified_by', 'claims.created_at')
            ->with(['bar:id,name', 'user:id,name', 'verifiedBy:id,name'])
            ->when($q, fn($query) =>
                $query->where(function($queryInner) use ($q) {
                    $queryInner->whereHas('bar', fn($q2) => $q2->where('name', 'like', "%{$q}%"))
                      ->orWhere('full_name', 'like', "%{$q}%")
                      ->orWhere('email_address', 'like', "%{$q}%")
                      ->orWhere('claim_request_id', 'like', "%{$q}%");
                })
            )
            ->when($status, fn($query) => $query->where('verification_status', $status))
            ->orderBy('created_at', 'desc')
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
        $claim->load(['bar.location', 'user', 'verifiedBy']);
        
        return view('admin.claims.show', [
            'claim' => $claim,
            'title' => 'View Claim - ' . $claim->claim_request_id,
            'catName' => 'bar',
            'subCatName' => 'claims',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function approve(Claim $claim)
    {
        $claim->load('bar');
        
        // Update claim status
        $claim->verification_status = 'approved';
        $claim->status = 'approved';
        $claim->verified_at = now();
        $claim->verified_by = auth()->id();
        $claim->save();

        // Update bar
        if ($claim->bar) {
            $claim->bar->claimed = true;
            $claim->bar->claimed_by = $claim->user_id;
            $claim->bar->claim_verification_status = 'approved';
            $claim->bar->save();
        }

        // Update or create user account if needed
        if ($claim->email_address && !$claim->user_id) {
            $user = User::firstOrCreate(
                ['email' => $claim->email_address],
                [
                    'name' => $claim->full_name,
                    'password' => Hash::make(Str::random(16)), // Random password, user will reset
                ]
            );
            $claim->user_id = $user->id;
            $claim->save();
        }

        return response()->json([
            'success' => true, 
            'status' => 'approved',
            'message' => 'Claim approved successfully. Owner account created/updated.'
        ]);
    }

    public function reject(Claim $claim)
    {
        $claim->verification_status = 'rejected';
        $claim->status = 'rejected';
        $claim->verified_at = now();
        $claim->verified_by = auth()->id();
        $claim->save();

        return response()->json([
            'success' => true, 
            'status' => 'rejected',
            'message' => 'Claim rejected.'
        ]);
    }

    public function requestMoreInfo(Request $request, Claim $claim)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $claim->verification_status = 'needs_info';
        $claim->admin_notes = $request->admin_notes;
        $claim->save();

        return response()->json([
            'success' => true,
            'message' => 'Request for more information sent.'
        ]);
    }

    public function addNotes(Request $request, Claim $claim)
    {
        $request->validate([
            'admin_notes' => 'required|string',
        ]);

        $claim->admin_notes = $request->admin_notes;
        $claim->save();

        return response()->json([
            'success' => true,
            'message' => 'Notes updated.'
        ]);
    }

    public function attachDocuments(Request $request, Claim $claim)
    {
        $request->validate([
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:5120',
        ]);

        $documents = $claim->admin_documents ?? [];

        foreach ($request->file('documents') as $file) {
            $path = $file->store('claims/documents', 'public');
            $documents[] = $path;
        }

        $claim->admin_documents = $documents;
        $claim->save();

        return response()->json([
            'success' => true,
            'message' => 'Documents attached successfully.',
            'documents' => $documents
        ]);
    }

    public function destroy(Claim $claim)
    {
        if ($claim->relationship_proof) {
            Storage::disk('public')->delete($claim->relationship_proof);
        }

        if ($claim->admin_documents) {
            foreach ($claim->admin_documents as $doc) {
                Storage::disk('public')->delete($doc);
            }
        }

        $claim->delete();
        return back()->with('success', 'Claim deleted successfully.');
    }
}

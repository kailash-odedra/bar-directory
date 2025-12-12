<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bar;
use App\Models\BarImage;
use App\Models\BarTiming;
use App\Models\Country;
use App\Models\State;
use App\Models\Location;
use App\Models\BarTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BarController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $featured = $request->get('featured');
        $claimed = $request->get('claimed');
        
        // Optimize: Select only needed columns and limit relationship loading
        $bars = Bar::select('bars.id', 'bars.name', 'bars.slug', 'bars.status', 'bars.is_featured', 'bars.claimed', 'bars.claimed_by', 'bars.created_by', 'bars.last_updated_by', 'bars.created_at', 'bars.updated_at')
            ->with([
                'location:id,bar_id,city,state_id,country_id',
                'location.state:id,name',
                'location.country:id,name',
                'tags:id,name,slug',
                'claimedBy:id,name',
                'createdBy:id,name',
                'lastUpdatedBy:id,name'
            ])
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($featured !== null, fn($q) => $q->where('is_featured', $featured))
            ->when($claimed !== null, fn($q) => $q->where('claimed', $claimed))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();
            
        return view('admin.bars.index', compact('bars'))
            ->with(['title'=>'Bars List','catName'=>'bar','scrollspy'=>false,'simplePage'=>false]);
    }

    public function pendingApproval()
    {
        // Optimize: Select only needed columns
        $bars = Bar::select('bars.id', 'bars.name', 'bars.slug', 'bars.status', 'bars.claimed_by', 'bars.created_by', 'bars.last_updated_by', 'bars.created_at', 'bars.updated_at')
            ->with([
                'location:id,bar_id,city,state_id,country_id',
                'location.state:id,name',
                'location.country:id,name',
                'tags:id,name,slug',
                'claimedBy:id,name',
                'createdBy:id,name',
                'lastUpdatedBy:id,name'
            ])
            ->whereIn('status', [0, 2]) // Status 0 or 2 = pending approval
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.bars.pending-approval', compact('bars'))
            ->with(['title'=>'Pending Bar Approvals','catName'=>'bar','scrollspy'=>false,'simplePage'=>false]);
    }

    public function create()
    {
        // Cache static data that doesn't change often
        $countries = Cache::remember('countries.all', 3600, fn() => Country::orderBy('name')->get());
        $states = Cache::remember('states.all', 3600, fn() => State::with('country')->orderBy('name')->get());
        $tags = Cache::remember('bar_tags.active', 1800, fn() => BarTag::where('status', 1)->orderBy('name')->get());

        return view('admin.bars.create', compact('countries','states','tags'))
            ->with(['title'=>'Create Bar','catName'=>'bar','scrollspy'=>false,'simplePage'=>false]);
    }

    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bars,slug',
            'short_description' => 'nullable|string|max:500',
            'full_description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:4096',
            'gallery.*' => 'nullable|image|max:4096',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'facebook'=>'nullable|string',
            'instagram'=>'nullable|string',
            'tiktok'=>'nullable|string',
            'youtube'=>'nullable|string',
            'website'=>'nullable|string',
            'tags'=>'nullable|array',
            'tags.*'=>'exists:tags,id'
        ]);
        $data = $request->only([
            'name','slug','short_description','full_description','video_url',
            'meta_title','meta_description','meta_keywords',
            'facebook','instagram','tiktok','youtube','website','is_featured'
        ]);
        $data['status'] = 1;
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;
        $data['slug'] = $data['slug'] ?: Str::slug($request->name.'-'.uniqid());
        
        // Set created_by to current admin user
        $data['created_by'] = Auth::guard('admin')->id();

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeImage($request->file('logo'), 'bars/logo');
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'bars/cover');
        }

        $bar = Bar::create($data);
        
        // Clear dashboard cache when new bar is created
        Cache::forget('dashboard.total_bars');
        Cache::forget('dashboard.claimed_bars');
        Cache::forget('dashboard.featured_bars');
        Cache::forget('dashboard.bars_by_status');
        Cache::forget('dashboard.top_rated_bars');
        // Clear API caches
        $this->clearApiCaches();

        if ($request->filled('tags')) {
            $bar->tags()->sync($request->tags);
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $path = $this->storeImage($image, 'bars/gallery');
                $bar->images()->create(['bar_id'=>$bar->id,'path'=>$path]);
            }
        }

        $this->saveLocation($bar, $request);
        $this->saveTimings($bar, $request->timing ?? []);

        return redirect()->route('admin.bar.index')->with('success', 'Bar created successfully');
    }

    public function edit(Bar $bar)
    {
        $bar->load(['location','tags','timings','images']);
        // Cache static data that doesn't change often
        $countries = Cache::remember('countries.all', 3600, fn() => Country::orderBy('name')->get());
        $states = Cache::remember('states.all', 3600, fn() => State::with('country')->orderBy('name')->get());
        $tags = Cache::remember('bar_tags.active', 1800, fn() => BarTag::where('status', 1)->orderBy('name')->get());
        return view('admin.bars.create', compact('bar','countries','states','tags'))
            ->with(['title'=>'Edit Bar','catName'=>'bar','scrollspy'=>false,'simplePage'=>false]);
    }

    public function update(Request $request, Bar $bar)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:bars,slug,'.$bar->id,
            'short_description' => 'nullable|string|max:500',
            'full_description' => 'nullable|string',
            'video_url' => 'nullable|url',
            'logo' => 'nullable|image|max:2048',
            'cover_image' => 'nullable|image|max:4096',
            'gallery.*' => 'nullable|image|max:4096',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
            'facebook'=>'nullable|string',
            'instagram'=>'nullable|string',
            'tiktok'=>'nullable|string',
            'youtube'=>'nullable|string',
            'website'=>'nullable|string',
            'tags'=>'nullable|array',
            'tags.*'=>'exists:tags,id'
        ]);

        $data = $request->only([
            'name','slug','short_description','full_description','video_url',
            'meta_title','meta_description','meta_keywords',
            'facebook','instagram','tiktok','youtube','website','is_featured'
        ]);
        $data['is_featured'] = $request->has('is_featured') ? 1 : 0;
        
        // Update last_updated_by to current admin user
        $data['last_updated_by'] = Auth::guard('admin')->id();
        
        // If created_by is not set (old bars), set it to current admin
        // Don't update if bar is claimed by owner (owner should remain owner)
        if (!$bar->created_by && !$bar->claimed_by) {
            $data['created_by'] = Auth::guard('admin')->id();
        }

        if ($request->hasFile('logo')) {
            if ($bar->logo) Storage::disk('public')->delete($bar->logo);
            $data['logo'] = $this->storeImage($request->file('logo'), 'bars/logo');
        }

        if ($request->hasFile('cover_image')) {
            if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'bars/cover');
        }

        $bar->update($data);
        
        // Clear dashboard cache when bar is updated
        Cache::forget('dashboard.total_bars');
        Cache::forget('dashboard.claimed_bars');
        Cache::forget('dashboard.featured_bars');
        Cache::forget('dashboard.bars_by_status');
        Cache::forget('dashboard.top_rated_bars');
        // Clear API caches
        $this->clearApiCaches($bar->id);

        if ($request->has('tags')) {
            $bar->tags()->sync($request->tags);
        }

        $this->saveLocation($bar, $request);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $image) {
                $path = $this->storeImage($image, 'bars/gallery');
                $bar->images()->create(['bar_id'=>$bar->id,'path'=>$path]);
            }
        }

        $bar->timings()->delete();
        $this->saveTimings($bar, $request->timing ?? []);

        return redirect()->route('admin.bar.index')->with('success', 'Bar updated successfully');
    }

    public function destroy(Bar $bar)
    {
        if ($bar->logo) Storage::disk('public')->delete($bar->logo);
        if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);

        foreach ($bar->images as $img) {
            Storage::disk('public')->delete($img->path);
        }

        $bar->delete();
        
        // Clear dashboard cache when bar is deleted
        Cache::forget('dashboard.total_bars');
        Cache::forget('dashboard.claimed_bars');
        Cache::forget('dashboard.featured_bars');
        Cache::forget('dashboard.bars_by_status');
        Cache::forget('dashboard.top_rated_bars');
        // Clear API caches
        $this->clearApiCaches($bar->id);
        
        return back()->with('success','Bar deleted');
    }

    // Toggle status
    public function toggleStatus(Bar $bar)
    {
        $bar->status = $bar->status == 1 ? 2 : 1;
        $bar->save();
        
        // Clear caches
        Cache::forget('dashboard.bars_by_status');
        $this->clearApiCaches($bar->id);
        
        return response()->json([
            'success' => true,
            'status' => $bar->status
        ]);
    }

    // Toggle featured status
    public function toggleFeatured(Bar $bar)
    {
        $bar->is_featured = $bar->is_featured ? 0 : 1;
        $bar->save();
        
        // Clear caches
        Cache::forget('dashboard.featured_bars');
        $this->clearApiCaches($bar->id);
        
        return response()->json([
            'success' => true,
            'is_featured' => $bar->is_featured
        ]);
    }

    // Approve bar (admin action)
    public function approve(Bar $bar)
    {
        $bar->status = 1; // Active
        $bar->save();
        
        // Clear dashboard cache when bar is approved
        Cache::forget('dashboard.bars_by_status');
        Cache::forget('dashboard.total_bars');
        Cache::forget('dashboard.pending_claims');
        // Clear API caches
        $this->clearApiCaches($bar->id);
        
        return redirect()->back()->with('success','Bar approved successfully');
    }

    // Bulk approve bars
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'bar_ids' => 'required|array',
            'bar_ids.*' => 'exists:bars,id'
        ]);

        Bar::whereIn('id', $request->bar_ids)->update(['status' => 1]);
        
        // Clear dashboard cache when bars are bulk approved
        Cache::forget('dashboard.bars_by_status');
        
        return response()->json([
            'success' => true,
            'message' => count($request->bar_ids) . ' bars approved successfully'
        ]);
    }

    protected function saveLocation(Bar $bar, Request $r)
    {
        $loc = $bar->location ?: new Location(['bar_id' => $bar->id]);

        $loc->address = $r->address;
        $loc->city = $r->city;
        $loc->region = $r->region;
        $loc->state_id = $r->state_id;
        $loc->country_id = $r->country_id;
        $loc->zipcode = $r->zip;
        $loc->phone = $r->phone;
        $loc->email = $r->email;
        $loc->latitude = $r->latitude;
        $loc->longitude = $r->longitude;

        $loc->save();
    }

    protected function saveTimings(Bar $bar, array $timings)
    {
        foreach ($timings as $weekday => $row) {
            BarTiming::create([
                'bar_id'     => $bar->id,
                'weekday'    => $weekday,
                'open_time'  => $row['open_time'] ?? null,
                'close_time' => $row['close_time'] ?? null,
                'is_closed'  => isset($row['is_closed']),
            ]);
        }
    }

    protected function storeImage($file, $folder)
    {
        $path = $file->storePublicly($folder, 'public');

        if (class_exists(Image::class)) {
            try {
                Image::make(storage_path("app/public/$path"))
                    ->fit(1200, 800, fn($c)=>$c->upsize())
                    ->save();
            } catch (\Throwable $e) {}
        }

        return $path;
    }

    /**
     * Clear API caches when bar data changes
     */
    protected function clearApiCaches($barId = null)
    {
        // Clear specific bar cache if ID provided
        if ($barId) {
            Cache::forget("api.bars.show.{$barId}");
            // Clear review caches for this bar (clear first 10 pages)
            for ($i = 1; $i <= 10; $i++) {
                Cache::forget("api.bars.{$barId}.reviews.page.{$i}");
            }
        }
        
        // Note: List caches will expire naturally (5 min TTL)
        // For immediate clearing, we'd need Redis or to track cache keys
    }

    public function getStates(Request $request)
    {
        return State::where('country_id', $request->country_id)->get();
    }
}

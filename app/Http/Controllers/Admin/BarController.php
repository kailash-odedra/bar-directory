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
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class BarController extends Controller
{
    public function index()
    {
        $bars = Bar::with(['location.country','location.state','tags'])->paginate(20);
        return view('admin.bars.index', compact('bars'))
            ->with(['title'=>'Bars List','catName'=>'bar','scrollspy'=>false,'simplePage'=>false]);
    }

    public function create()
    {
        $countries = Country::all();
        $states = State::all();
        $tags = BarTag::all();

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
            'facebook','instagram','tiktok','youtube','website'
        ]);
        $data['status'] = 1;
        $data['slug'] = $data['slug'] ?: Str::slug($request->name.'-'.uniqid());

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeImage($request->file('logo'), 'bars/logo');
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'bars/cover');
        }

        $bar = Bar::create($data);

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

    public function edit($id)
    {
        $bar = Bar::with(['location','tags','timings','images'])->findOrFail($id);
        $countries = Country::all();
        $states = State::all();
        $tags = BarTag::all();
        return view('admin.bars.create', compact('bar','countries','states','tags'))
            ->with(['title'=>'Edit Bar','catName'=>'bar','scrollspy'=>false,'simplePage'=>false]);
    }

    public function update(Request $request, Bar $bar)
    {
        // Validation
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
            'facebook','instagram','tiktok','youtube','website'
        ]);

        if ($request->hasFile('logo')) {
            if ($bar->logo) Storage::disk('public')->delete($bar->logo);
            $data['logo'] = $this->storeImage($request->file('logo'), 'bars/logo');
        }

        if ($request->hasFile('cover_image')) {
            if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'bars/cover');
        }

        $bar->update($data);

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
        return back()->with('success','Bar deleted');
    }

    // Toggle status
    public function toggleStatus($id)
    {
        $bar = Bar::findOrFail($id);
        $bar->status = $bar->status == 1 ? 2 : 1;
        $bar->save();
        return response()->json([
            'success' => true,
            'status' => $bar->status
        ]);
    }

    // Approve bar (admin action)
    public function approve($id)
    {
        $bar = Bar::findOrFail($id);
        $bar->status = 1; // Active
        $bar->save();
        return redirect()->back()->with('success','Bar approved successfully');
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

    public function getStates(Request $request)
    {
        return State::where('country_id', $request->country_id)->get();
    }
}

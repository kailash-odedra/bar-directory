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
        return view('admin.bars.index', [
            'bars' => $bars,
            'title' => 'Bars List',
            'catName' => 'All Bars',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        $countries = Country::all();
        $states = State::all();
        $tags = BarTag::all();

        return view('admin.bars.create', [
            'countries' => $countries,
            'states' => $states,
            'tags' => $tags,
            'title' => 'Create Bar',
            'catName' => 'Add New Bar',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->only([
            'name','slug','short_description','full_description','video_url',
            'meta_title','meta_description','meta_keywords',
            'facebook','instagram','tiktok','youtube','website'
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($request->name.'-'.uniqid());

        // ================= LOGO ====================
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeImage($request->file('logo'), 'bars/logo');
        }

        // ================= COVER ===================
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'bars/cover');
        }

        // CREATE BAR
        $bar = Bar::create($data);

        // ================= TAGS ===================
        if ($request->filled('tags')) {
            $bar->tags()->sync($request->tags);
        }

        // ================= LOCATION ===============
        $this->saveLocation($bar, $request);

        // =============== GALLERY IMAGES ===========
        if ($request->hasFile('gallery')) {
            foreach ($request->gallery as $img) {
                $path = $this->storeImage($img, 'bars/gallery');
                $bar->images()->create(['path' => $path]);
            }
        }

        // =============== TIMINGS ==================
        $this->saveTimings($bar, $request->timing ?? []);

        return redirect()->route('admin.bar.index')->with('success', 'Bar created successfully');
    }

    public function edit($id)
    {
        $bar = Bar::with(['location','tags','timings'])->findOrFail($id);
        $countries = Country::all();
        $states = State::all();
        $tags = BarTag::all();
        return view('admin.bars.create', [
            'bar' => $bar,
            'countries' => $countries,
            'states' => $states,
            'tags' => $tags,
            'title' => 'Edit Bar',
            'catName' => 'Edit Bar Details',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(Request $request, Bar $bar)
    {
        $data = $request->only([
            'name','slug','short_description','full_description','video_url',
            'meta_title','meta_description','meta_keywords',
            'facebook','instagram','tiktok','youtube','website'
        ]);

        // REPLACE LOGO
        if ($request->hasFile('logo')) {
            if ($bar->logo) Storage::disk('public')->delete($bar->logo);
            $data['logo'] = $this->storeImage($request->file('logo'), 'bars/logo');
        }

        // REPLACE COVER
        if ($request->hasFile('cover_image')) {
            if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'bars/cover');
        }

        $bar->update($data);

        // UPDATE TAGS
        if ($request->has('tags')) {
            $bar->tags()->sync($request->tags);
        }

        // UPDATE LOCATION
        $this->saveLocation($bar, $request);

        // ADD NEW GALLERY IMAGES
        if ($request->hasFile('gallery')) {
            foreach ($request->gallery as $img) {
                $path = $this->storeImage($img, 'bars/gallery');
                $bar->images()->create(['path' => $path]);
            }
        }

        // UPDATE TIMINGS
        $bar->timings()->delete();
        $this->saveTimings($bar, $request->timing ?? []);

        return redirect()->route('admin.bar.index')->with('success', 'Bar updated successfully');
    }

    // ====================== DELETE ======================
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

    protected function saveLocation(Bar $bar, Request $r)
    {
        $loc = $bar->location ?: new Location(['bar_id' => $bar->id]);

        $loc->address = $r->address;
        $loc->city = $r->city;
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
                'is_closed'  => isset($row['is_closed']), // checkbox returns 'on' if checked
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

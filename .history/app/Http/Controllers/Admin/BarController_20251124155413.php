<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBarRequest;
use App\Http\Requests\Admin\UpdateBarRequest;
use App\Models\Bar;
use App\Models\BarImage;
use App\Models\BarTiming;
use App\Models\Country;
use App\Models\State;
use App\Models\Location;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class BarController extends Controller
{
    public function index()
    {
        $bars = Bar::with(['location.country','location.state','tags'])->paginate(20);
        return view('admin.bars.index', compact('bars'));
    }

    public function create()
    {
        $countries = Country::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        return view('admin.bars.create', compact('countries','tags'));
    }

    public function store(StoreBarRequest $request)
    {
        $data = $request->only([
            'name','slug','short_description','full_description',
            'video_url','meta_title','meta_description','meta_keywords',
            'facebook','instagram','tiktok','youtube','website'
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name'] . '-' . uniqid());

        // logo
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeImage($request->file('logo'), 'bars/logo');
        }

        // cover
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'bars/cover');
        }

        $bar = Bar::create($data);

        // tags
        if ($request->filled('tags')) {
            $bar->tags()->sync($request->input('tags'));
        }

        // location (create)
        $this->saveLocation($bar, $request);

        // gallery images
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $img) {
                $path = $this->storeImage($img, 'bars/gallery');
                $bar->images()->create(['path' => $path]);
            }
        }

        // timings
        $this->saveTimings($bar, $request->input('timing', []));

        return redirect()->route('admin.bar.index')->with('success','Bar created successfully');
    }

    public function show(Bar $bar)
    {
        $bar->load(['location.country','location.state','images','timings','tags']);
        return view('admin.bars.show', compact('bar'));
    }

    public function edit(Bar $bar)
    {
        $countries = Country::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $bar->load(['location','timings','images','tags']);
        return view('admin.bars.edit', compact('bar','countries','tags'));
    }

    public function update(UpdateBarRequest $request, Bar $bar)
    {
        $data = $request->only([
            'name','slug','short_description','full_description',
            'video_url','meta_title','meta_description','meta_keywords',
            'facebook','instagram','tiktok','youtube','website'
        ]);

        // replace logo
        if ($request->hasFile('logo')) {
            if ($bar->logo) Storage::disk('public')->delete($bar->logo);
            $data['logo'] = $this->storeImage($request->file('logo'), 'bars/logo');
        }

        if ($request->hasFile('cover_image')) {
            if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'bars/cover');
        }

        $bar->update($data);

        // tags
        if ($request->has('tags')) {
            $bar->tags()->sync($request->input('tags'));
        }

        // location update
        $this->saveLocation($bar, $request);

        // gallery images (append)
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $img) {
                $path = $this->storeImage($img, 'bars/gallery');
                $bar->images()->create(['path' => $path]);
            }
        }

        // timings (replace existing)
        $bar->timings()->delete();
        $this->saveTimings($bar, $request->input('timing', []));

        return redirect()->route('admin.bar.index')->with('success','Bar updated successfully');
    }

    public function destroy(Bar $bar)
    {
        // delete files
        if ($bar->logo) Storage::disk('public')->delete($bar->logo);
        if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);
        foreach ($bar->images as $img) {
            if ($img->path && !filter_var($img->path, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($img->path);
            }
        }

        $bar->delete();
        return back()->with('success','Bar deleted');
    }

    // AJAX: states by country
    public function getStates(Request $request)
    {
        $countryId = $request->query('country_id');
        if (!$countryId) return response()->json([], 200);
        $states = State::where('country_id',$countryId)->orderBy('name')->get();
        return response()->json($states);
    }

    // PRIVATE HELPERS
    protected function saveLocation(Bar $bar, Request $request)
    {
        $loc = $bar->location ?: new Location(['bar_id' => $bar->id]);
        $loc->address = $request->input('address');
        $loc->city = $request->input('city');
        $loc->state_id = $request->input('state_id');
        $loc->country_id = $request->input('country_id');
        $loc->region = $request->input('region');
        $loc->zipcode = $request->input('zip');
        $loc->latitude = $request->input('latitude');
        $loc->longitude = $request->input('longitude');
        $loc->phone = $request->input('phone');
        $loc->email = $request->input('email');
        $loc->bar_id = $bar->id;
        $loc->save();
    }

    protected function saveTimings(Bar $bar, array $timings)
    {
        // $timings expected structure: ['Monday' => ['open'=>'10:00','close'=>'23:00','closed'=>false], ...]
        foreach ($timings as $weekday => $data) {
            $open = $data['open'] ?? null;
            $close = $data['close'] ?? null;
            $isClosed = isset($data['closed']) && $data['closed'];
            BarTiming::create([
                'bar_id' => $bar->id,
                'weekday' => $weekday,
                'open_time' => $open,
                'close_time' => $close,
                'is_closed' => $isClosed
            ]);
        }
    }

    protected function storeImage($file, $folder)
    {
        // store on public disk; adjust to s3 if needed
        $path = $file->storePublicly($folder, 'public');

        // optional resizing if Intervention installed
        if (class_exists(Image::class)) {
            $full = storage_path('app/public/' . $path);
            try {
                Image::make($full)->fit(1200, 800, function ($c) { $c->upsize(); })->save();
            } catch (\Throwable $e) {
                // skip image processing errors
            }
        }
        return $path;
    }
}

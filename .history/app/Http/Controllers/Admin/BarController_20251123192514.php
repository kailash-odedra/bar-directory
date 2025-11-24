<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bar;
use App\Models\BarTag;
use App\Models\BarImage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image; // if using intervention

class BarController extends Controller
{
    public function index()
    {
        $bars = Bar::with(['tags', 'location', 'reviews'])->get();
        $tags = BarTag::all();
        return view('admin.bars.index', [
            'bars' => $bars,
            'tags' => $tags,
            'title' => 'Bars List',
            'catName' => 'All Bars',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }
    public function show(Bar $bar)
    {
        return view('admin.bars.show', compact('bar'))
            ->with('catName', 'bar');
    }
    public function create()
    {
        $tags = BarTag::all();

        return view('admin.bars.create', [
            'tags' => $tags,
            'title' => 'Create Bar',
            'catName' => 'Add Bar',
            'scrollspy' => false,
            'simplePage' => false
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'slug'              => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'full_description'  => 'nullable|string',

            'website'           => 'nullable|url',
            'facebook'          => 'nullable|url',
            'instagram'         => 'nullable|url',

            'meta_title'        => 'nullable|string',
            'meta_description'  => 'nullable|string',

            'status'            => 'nullable|boolean',
            'tags'              => 'nullable|array',

            'logo'              => 'nullable|image|max:2048',
            'cover_image'       => 'nullable|image|max:4096',
        ]);

        // auto slug
        $data['slug'] = $data['slug'] ?: Str::slug($data['name'] . '-' . uniqid());

        // Upload logo
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeImage($request->file('logo'), 'bars/logo');
        }

        // Upload cover
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'bars/cover');
        }

        // Create bar
        $bar = Bar::create($data);

        // Sync tags
        if ($request->filled('tags')) {
            $bar->tags()->sync($request->input('tags'));
        }

        // Auto create empty location row
        if (!$bar->location) {
            $bar->location()->create([]);
        }
        return redirect(url('admin/bar'))->with('success', 'Bar created successfully');
    }

    public function edit(Bar $bar)
    {
        $tags = BarTag::all();
        return view('admin.bar.edit', compact('bar', 'tags'));
    }

    public function update(Request $request, Bar $bar)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'short_description' => 'nullable|string',
            'full_description'  => 'nullable|string',

            'website'           => 'nullable|url',
            'facebook'          => 'nullable|url',
            'instagram'         => 'nullable|url',

            'meta_title'        => 'nullable|string',
            'meta_description'  => 'nullable|string',

            'status'            => 'nullable|boolean',
            'tags'              => 'nullable|array',

            'logo'              => 'nullable|image|max:2048',
            'cover_image'       => 'nullable|image|max:4096',
        ]);

        // Replace logo
        if ($request->hasFile('logo')) {
            if ($bar->logo) Storage::disk('public')->delete($bar->logo);
            $data['logo'] = $this->storeImage($request->file('logo'), 'bars/logo');
        }

        // Replace cover
        if ($request->hasFile('cover_image')) {
            if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'bars/cover');
        }

        // Update bar
        $bar->update($data);

        // Sync tags
        if ($request->has('tags')) {
            $bar->tags()->sync($request->input('tags'));
        }

        return redirect(url('admin/bar'))->with('success', 'Bar updated successfully');

    }

    public function destroy(Bar $bar)
    {
        if ($bar->logo) Storage::disk('public')->delete($bar->logo);
        if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);

        foreach ($bar->images as $img) {
            if ($img->path && !filter_var($img->path, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($img->path);
            }
        }

        $bar->delete();

        return back()->with('success', 'Bar deleted successfully');
    }
    protected function storeImage($file, $folder)
    {
        $path = $file->storePublicly($folder, 'public');

        if (class_exists(Image::class)) {
            $full = storage_path('app/public/' . $path);
            Image::make($full)
                ->fit(1200, 800, function ($c) {
                    $c->upsize();
                })
                ->save();
        }

        return $path;
    }
}


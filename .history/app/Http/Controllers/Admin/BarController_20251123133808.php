<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bar;
use App\Models\BarTag;
use App\Models\BarImage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class BarController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $bars = Bar::with(['location', 'tags'])
            ->when($q, fn($q2) => $q2->where('name', 'like', "%{$q}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.bars.index', compact('bars'))
            ->with('catName', 'bar');
    }

    /* ===============================
        CREATE PAGE
    =============================== */
    public function create()
    {
        $tags = BarTag::orderBy('name')->get();
        return view('admin.bars.create', compact('tags'))
            ->with('catName', 'bar');
    }

    /* ===============================
        STORE NEW BAR
    =============================== */
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

    /* ===============================
        SHOW DETAIL PAGE
    =============================== */
    public function show(Bar $bar)
    {
        return view('admin.bars.show', compact('bar'))
            ->with('catName', 'bar');
    }

    /* ===============================
        EDIT PAGE
    =============================== */
    public function edit(Bar $bar)
    {
        $tags = BarTag::orderBy('name')->get();
        return view('admin.bars.edit', compact('bar', 'tags'))
            ->with('catName', 'bar');
    }

    /* ===============================
        UPDATE BAR
    =============================== */
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

    /* ===============================
        DELETE BAR
    =============================== */
    public function destroy(Bar $bar)
    {
        // Delete images
        if ($bar->logo) Storage::disk('public')->delete($bar->logo);
        if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);

        foreach ($bar->images as $img) {
            if ($img->path && !filter_var($img->path, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($img->path);
            }
        }

        $bar->delete();

        return redirect(url('admin/bar'))->with('success', 'Bar deleted successfully');
    }

    /* ===============================
        OPTIMIZED IMAGE UPLOAD
    =============================== */
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

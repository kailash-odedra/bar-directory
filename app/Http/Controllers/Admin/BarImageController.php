<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarImage;
use App\Models\Bar;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BarImageController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $images = BarImage::with('bar')
            ->when($q, fn($b) => $b->where('type','like', "%{$q}%"))
            ->orderBy('created_at','desc')
            ->paginate(30)->withQueryString();

        return view('admin.images.index', compact('images'))->with('catName','bar');
    }

    public function create()
    {
        $bars = Cache::remember('bars.for_dropdown', 1800, fn() => Bar::select('id', 'name')->orderBy('name')->get());
        return view('admin.images.create', compact('bars'))->with('catName','bar');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'type' => 'required|in:logo,cover,gallery,video',
            'image' => 'nullable|image|max:8192',
            'video_url' => 'nullable|url',
            'alt' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->storePublicly('bars/images', 'public');
            if (class_exists(Image::class)) {
                $full = storage_path('app/public/'.$path);
                Image::make($full)->fit(1200,800,function($c){ $c->upsize(); })->save();
            }
            $data['path'] = $path;
        } elseif ($request->filled('video_url')) {
            $data['path'] = $request->input('video_url');
        } else {
            return back()->withErrors(['image' => 'Image or video URL is required'])->withInput();
        }

        BarImage::create([
            'bar_id' => $data['bar_id'],
            'type' => $data['type'],
            'path' => $data['path'],
            'alt' => $data['alt'] ?? null,
        ]);

        return redirect(url('admin/bar-images'))->with('success','Image added.');
    }

    public function edit(BarImage $barImage)
    {
        $bars = Cache::remember('bars.for_dropdown', 1800, fn() => Bar::select('id', 'name')->orderBy('name')->get());
        return view('admin.images.edit', compact('barImage','bars'))->with('catName','bar');
    }

    public function update(Request $request, BarImage $barImage)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'type' => 'required|in:logo,cover,gallery,video',
            'image' => 'nullable|image|max:8192',
            'video_url' => 'nullable|url',
            'alt' => 'nullable|string|max:255',
        ]);

        if ($request->hasFile('image')) {
            // delete old if local
            if ($barImage->path && !filter_var($barImage->path, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($barImage->path);
            }
            $path = $request->file('image')->storePublicly('bars/images', 'public');
            if (class_exists(Image::class)) {
                $full = storage_path('app/public/'.$path);
                Image::make($full)->fit(1200,800,function($c){ $c->upsize(); })->save();
            }
            $barImage->path = $path;
        } elseif ($request->filled('video_url')) {
            // if switching to URL, delete old file if stored locally
            if ($barImage->path && !filter_var($barImage->path, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($barImage->path);
            }
            $barImage->path = $request->input('video_url');
        }

        $barImage->bar_id = $data['bar_id'];
        $barImage->type = $data['type'];
        $barImage->alt = $data['alt'] ?? $barImage->alt;
        $barImage->save();

        return redirect(url('admin/bar-images'))->with('success','Image updated.');
    }

    public function destroy(BarImage $barImage)
    {
        if ($barImage->path && !filter_var($barImage->path, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($barImage->path);
        }
        $barImage->delete();
        return redirect(url('admin/bar-images'))->with('success','Image removed.');
    }
}

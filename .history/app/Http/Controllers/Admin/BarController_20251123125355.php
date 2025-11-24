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
        $bars = Bar::withCount('reviews')->latest()->paginate(20);
        return view('admin.bars.index', compact('bars'))->with('catName','bar');
    }

    public function create()
    {
        $tags = BarTag::all();
        return view('admin.bars.create', compact('tags'))->with('catName','bar');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'short_description'=>'nullable|string',
            'full_description'=>'nullable|string',
            'website'=>'nullable|url',
            'facebook'=>'nullable|url',
            'instagram'=>'nullable|url',
            'meta_title'=>'nullable|string',
            'meta_description'=>'nullable|string',
            'status'=>'nullable|boolean',
            'tags'=>'nullable|array',
            'logo'=>'nullable|image|max:2048',
            'cover_image'=>'nullable|image|max:4096',
        ]);

        // handle images
        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeImage($request->file('logo'),'bars/logo');
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image'),'bars/cover');
        }

        $bar = Bar::create($data);

        // attach tags if provided
        if ($request->filled('tags')) $bar->tags()->sync($request->input('tags'));

        return redirect(url('admin/bar'))->with('success','Bar created');
    }

    public function show(Bar $bar)
    {
        return view('admin.bars.show', compact('bar'))->with('catName','bar');
    }

    public function edit(Bar $bar)
    {
        $tags = BarTag::all();
        return view('admin.bars.edit', compact('bar','tags'))->with('catName','bar');
    }

    public function update(Request $request, Bar $bar)
    {
        $data = $request->validate([
            'name'=>'required|string|max:255',
            'short_description'=>'nullable|string',
            'full_description'=>'nullable|string',
            'website'=>'nullable|url',
            'facebook'=>'nullable|url',
            'instagram'=>'nullable|url',
            'meta_title'=>'nullable|string',
            'meta_description'=>'nullable|string',
            'status'=>'nullable|boolean',
            'tags'=>'nullable|array',
            'logo'=>'nullable|image|max:2048',
            'cover_image'=>'nullable|image|max:4096',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $this->storeImage($request->file('logo'),'bars/logo');
        }
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image'),'bars/cover');
        }

        $bar->update($data);
        if ($request->has('tags')) $bar->tags()->sync($request->input('tags'));
        return redirect(url('admin/bar'))->with('success','Bar updated');
    }

    public function destroy(Bar $bar)
    {
        $bar->delete();
        return redirect(url('admin/bar'))->with('success','Bar deleted');
    }

    // helper: store image and optionally resize
    protected function storeImage($file, $folder)
    {
        $path = $file->storePublicly($folder, 'public'); // storage/app/public/{folder}
        // optional resize
        if (class_exists(\Intervention\Image\ImageManagerStatic::class)) {
            $fullPath = storage_path('app/public/'.$path);
            Image::make($fullPath)->fit(1200,800,function($c){ $c->upsize(); })->save();
        }
        return $path;
    }
}


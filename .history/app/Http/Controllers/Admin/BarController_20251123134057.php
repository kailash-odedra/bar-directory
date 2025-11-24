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
            'address'           => 'nullable|string',
            'city'              => 'nullable|string',
            'state'             => 'nullable|string',
            'country'           => 'nullable|string',
            'zip'               => 'nullable|string',
            'website'           => 'nullable|string',
            'phone'             => 'nullable|string',
            'email'             => 'nullable|email',
            'opening_time'      => 'nullable',
            'closing_time'      => 'nullable',
            'latitude'          => 'nullable|numeric',
            'longitude'         => 'nullable|numeric',
            'tags'              => 'nullable|array',
            'logo'              => 'nullable|image',
            'cover_image'       => 'nullable|image'
        ]);

        // Slug auto-create
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.uniqid();
        }

        // Logo upload
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('bars/logos', 'public');
        }

        // Cover upload
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('bars/covers', 'public');
        }

        $bar = Bar::create($data);

        if (isset($data['tags'])) {
            $bar->tags()->sync($data['tags']);
        }

        return redirect()->route('admin.bars.index')->with('success', 'Bar created successfully');
    }

    public function edit(Bar $bar)
    {
        $tags = BarTag::all();
        return view('admin.bars.edit', compact('bar', 'tags'));
    }

    public function update(Request $request, Bar $bar)
    {
        $data = $request->validate([
            'name'              => 'required|string|max:255',
            'slug'              => 'nullable|string|max:255',
            'short_description' => 'nullable|string',
            'full_description'  => 'nullable|string',
            'address'           => 'nullable|string',
            'city'              => 'nullable|string',
            'state'             => 'nullable|string',
            'country'           => 'nullable|string',
            'zip'               => 'nullable|string',
            'website'           => 'nullable|string',
            'phone'             => 'nullable|string',
            'email'             => 'nullable|email',
            'opening_time'      => 'nullable',
            'closing_time'      => 'nullable',
            'latitude'          => 'nullable|numeric',
            'longitude'         => 'nullable|numeric',
            'status'            => 'nullable|string',
            'tags'              => 'nullable|array',
            'logo'              => 'nullable|image|max:2048',
            'cover_image'       => 'nullable|image|max:4096',
        ]);

        // Logo
        if ($request->hasFile('logo')) {
            if ($bar->logo) Storage::disk('public')->delete($bar->logo);
            $data['logo'] = $request->file('logo')->store('bars/logos', 'public');
        }

        // Cover
        if ($request->hasFile('cover_image')) {
            if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('bars/covers', 'public');
        }

        // Slug regenerate if empty
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']).'-'.uniqid();
        }

        $bar->update($data);

        $bar->tags()->sync($data['tags'] ?? []);

        return redirect()->route('admin.bars.index')->with('success', 'Bar updated successfully');
    }

    public function destroy(Bar $bar)
    {
        if ($bar->logo) Storage::disk('public')->delete($bar->logo);
        if ($bar->cover_image) Storage::disk('public')->delete($bar->cover_image);

        $bar->delete();

        return back()->with('success', 'Bar deleted successfully');
    }
}


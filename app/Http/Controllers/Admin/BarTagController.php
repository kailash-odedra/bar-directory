<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarTag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BarTagController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $tags = BarTag::when($q, fn($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.tags.index', [
            'tags' => $tags,
            'title' => 'Tags List',
            'catName' => 'bar',
            'subCatName' => 'tags',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        return view('admin.tags.create', [
            'title' => 'Add New Tag',
            'catName' => 'bar',
            'subCatName' => 'tags',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:tags,name',
            'slug' => 'nullable|string|max:150|unique:tags,slug',
        ]);
        $data['status'] = 1;
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        BarTag::create($data);

        return redirect()->route('admin.bar-tags.index')->with('success', 'Tag created successfully.');
    }

    public function edit(BarTag $barTag)
    {
        return view('admin.tags.create', [ // reuse create view
            'barTag' => $barTag,
            'title' => 'Edit Tag',
            'catName' => 'bar',
            'subCatName' => 'tags',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(Request $request, BarTag $barTag)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:tags,name,'.$barTag->id,
            'slug' => 'nullable|string|max:150|unique:tags,slug,'.$barTag->id,
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        $barTag->update($data);

        return redirect()->route('admin.bar-tags.index')->with('success', 'Tag updated successfully.');
    }
    public function toggleStatus($id)
    {
        $barTag = BarTag::findOrFail($id);
        $barTag->status = $barTag->status == 1 ? 2 : 1;
        $barTag->save();
        return response()->json([
            'success' => true,
            'status' => $barTag->status
        ]);
    }
    public function destroy(BarTag $barTag)
    {
        $barTag->delete();
        return back()->with('success','Tag deleted successfully.');
    }
}

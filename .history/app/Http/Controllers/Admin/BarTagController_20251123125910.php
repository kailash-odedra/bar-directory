<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarTag;

class BarTagController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $tags = BarTag::when($q, fn($b) => $b->where('name', 'like', "%{$q}%"))
                     ->orderBy('name')
                     ->paginate(20)
                     ->withQueryString();

        return view('admin.tags.index', compact('tags'))->with('catName','tags');
    }

    public function create()
    {
        return view('admin.tags.create')->with('catName','tags');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:bar_tags,name',
            'slug' => 'nullable|string|max:150|unique:bar_tags,slug',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        BarTag::create($data);

        return redirect(url('admin/bar-tags'))->with('success', 'Tag created successfully.');
    }

    public function edit(BarTag $barTag)
    {
        return view('admin.tags.edit', ['tag' => $barTag])->with('catName','tags');
    }

    public function update(Request $request, BarTag $barTag)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:bar_tags,name,'.$barTag->id,
            'slug' => 'nullable|string|max:150|unique:bar_tags,slug,'.$barTag->id,
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = \Str::slug($data['name']);
        }

        $barTag->update($data);

        return redirect(url('admin/bar-tags'))->with('success', 'Tag updated successfully.');
    }

    public function destroy(BarTag $barTag)
    {
        $barTag->delete();
        return redirect(url('admin/bar-tags'))->with('success', 'Tag deleted.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarMenuCategory;
use App\Models\Bar;

class BarMenuCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $categories = BarMenuCategory::with('bar')
            ->when($q, fn($b) => $b->where('name','like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(20)->withQueryString();

        return view('admin.menu_categories.index', compact('categories'))->with('catName','menu');
    }

    public function create()
    {
        $bars = Bar::orderBy('name')->get();
        return view('admin.menu_categories.create', compact('bars'))->with('catName','menu');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'name'   => 'required|string|max:150',
        ]);

        BarMenuCategory::create($data);

        return redirect(url('admin/bar-menu-categories'))->with('success','Menu category created.');
    }

    public function edit(BarMenuCategory $barMenuCategory)
    {
        $bars = Bar::orderBy('name')->get();
        return view('admin.menu_categories.edit', compact('barMenuCategory','bars'))->with('catName','menu');
    }

    public function update(Request $request, BarMenuCategory $barMenuCategory)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'name'   => 'required|string|max:150',
        ]);

        $barMenuCategory->update($data);

        return redirect(url('admin/bar-menu-categories'))->with('success','Menu category updated.');
    }

    public function destroy(BarMenuCategory $barMenuCategory)
    {
        $barMenuCategory->delete();
        return redirect(url('admin/bar-menu-categories'))->with('success','Menu category deleted.');
    }
}
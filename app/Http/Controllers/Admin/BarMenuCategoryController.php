<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarMenuCategory;
use App\Models\Bar;
use Illuminate\Http\Request;

class BarMenuCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $categories = BarMenuCategory::when($q, fn($query) =>
                $query->where('name', 'like', "%{$q}%")
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.menu-categories.index', [
            'categories' => $categories,
            'title' => 'Menu Categories List',
            'catName' => 'bar',
            'subCatName' => 'menu-categories',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        $bars = Bar::orderBy('name')->get();

        return view('admin.menu-categories.create', [
            'bars' => $bars,
            'menuCategory' => null,
            'title' => 'Add Menu Category',
            'catName' => 'bar',
            'subCatName' => 'menu-categories',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'name' => 'required|string|max:120|unique:bar_menu_categories,name',
        ]);

        $data['status'] = 1;

        BarMenuCategory::create($data);

        return redirect()
            ->route('admin.bar-menu-categories.index')
            ->with('success', 'Menu Category created successfully.');
    }

    public function edit(BarMenuCategory $bar_menu_category)
    {
        $bars = Bar::orderBy('name')->get();

        return view('admin.menu-categories.create', [
            'menuCategory' => $bar_menu_category,
            'bars' => $bars,
            'title' => 'Edit Menu Category',
            'catName' => 'bar',
            'subCatName' => 'menu-categories',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(Request $request, BarMenuCategory $bar_menu_category)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'name' => 'required|string|max:120|unique:bar_menu_categories,name,' . $bar_menu_category->id,
        ]);

        $bar_menu_category->update($data);

        return redirect()
            ->route('admin.bar-menu-categories.index')
            ->with('success', 'Menu Category updated successfully.');
    }

    public function toggleStatus(BarMenuCategory $barMenuCategory)
    {
        $barMenuCategory->status = $barMenuCategory->status == 1 ? 2 : 1;
        $barMenuCategory->save();

        return response()->json([
            'success' => true,
            'status' => $category->status
        ]);
    }

    public function destroy(BarMenuCategory $bar_menu_category)
    {
        $bar_menu_category->delete();

        return back()->with('success', 'Menu Category deleted successfully.');
    }
}

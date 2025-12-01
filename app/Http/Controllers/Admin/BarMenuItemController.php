<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BarMenuItem;
use App\Models\Bar;
use App\Models\BarMenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BarMenuItemController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');

        $items = BarMenuItem::with(['bar', 'category'])
            ->when($q, fn($query) =>
                $query->where('name', 'like', "%{$q}%")
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.bar-menu-items.index', [
            'items' => $items,
            'title' => 'Menu Items List',
            'catName' => 'bar',
            'subCatName' => 'bar-menu-items',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        $bars = Bar::orderBy('name')->get();
        $categories = BarMenuCategory::orderBy('name')->get();

        return view('admin.bar-menu-items.create', [
            'bars' => $bars,
            'categories' => $categories,
            'title' => 'Add Menu Item',
            'catName' => 'bar',
            'subCatName' => 'bar-menu-items',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'bar_menu_category_id' => 'required|exists:bar_menu_categories,id',
            'name' => 'required|string|max:150|unique:bar_menu_items,name',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'nullable|in:0,1',
        ]);

        // Upload image if provided
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu-items', 'public');
        }

        // default status if not provided
        $data['status'] = $data['status'] ?? 1;

        BarMenuItem::create($data);

        return redirect()->route('admin.bar-menu-items.index')
            ->with('success', 'Menu Item created successfully.');
    }

    public function edit(BarMenuItem $bar_menu_item)
    {
        $bars = Bar::orderBy('name')->get();
        $categories = BarMenuCategory::orderBy('name')->get();

        return view('admin.bar-menu-items.create', [
            'barMenuItem' => $bar_menu_item,
            'bars' => $bars,
            'categories' => $categories,
            'title' => 'Edit Menu Item',
            'catName' => 'bar',
            'subCatName' => 'bar-menu-items',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(Request $request, BarMenuItem $bar_menu_item)
    {
        $data = $request->validate([
            'bar_id' => 'required|exists:bars,id',
            'bar_menu_category_id' => 'required|exists:bar_menu_categories,id',
            'name' => 'required|string|max:150|unique:bar_menu_items,name,' . $bar_menu_item->id,
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'status' => 'nullable|in:0,1',
        ]);

        // If new image uploaded, delete old image
        if ($request->hasFile('image')) {
            if ($bar_menu_item->image && Storage::disk('public')->exists($bar_menu_item->image)) {
                Storage::disk('public')->delete($bar_menu_item->image);
            }

            $data['image'] = $request->file('image')->store('menu-items', 'public');
        }

        $bar_menu_item->update($data);

        return redirect()->route('admin.bar-menu-items.index')
            ->with('success', 'Menu Item updated successfully.');
    }

    public function toggleStatus(BarMenuItem $barMenuItem)
    {
        $barMenuItem->status = $barMenuItem->status == 1 ? 0 : 1;
        $barMenuItem->save();

        return response()->json([
            'success' => true,
            'status' => $item->status
        ]);
    }

    public function destroy(BarMenuItem $bar_menu_item)
    {
        if ($bar_menu_item->image && Storage::disk('public')->exists($bar_menu_item->image)) {
            Storage::disk('public')->delete($bar_menu_item->image);
        }

        $bar_menu_item->delete();

        return back()->with('success', 'Menu Item deleted successfully.');
    }
}

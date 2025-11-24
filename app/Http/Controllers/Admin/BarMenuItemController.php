<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarMenuItem;
use App\Models\BarMenuCategory;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class BarMenuItemController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $items = BarMenuItem::with('category.bar')
            ->when($q, fn($b) => $b->where('name','like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(20)->withQueryString();

        return view('admin.menu_items.index', compact('items'))->with('catName','menu');
    }

    public function create()
    {
        $categories = BarMenuCategory::with('bar')->orderBy('name')->get();
        return view('admin.menu_items.create', compact('categories'))->with('catName','menu');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bar_menu_category_id' => 'required|exists:bar_menu_categories,id',
            'name' => 'required|string|max:191',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = $file->storePublicly('bars/menu-items', 'public');

            // optional resize with intervention
            if (class_exists(Image::class)) {
                $full = storage_path('app/public/'.$path);
                Image::make($full)->fit(800,600,function($c){ $c->upsize(); })->save();
            }

            $data['image'] = $path;
        }

        BarMenuItem::create($data);

        return redirect(url('admin/bar-menu-items'))->with('success','Menu item created.');
    }

    public function edit(BarMenuItem $barMenuItem)
    {
        $categories = BarMenuCategory::with('bar')->orderBy('name')->get();
        return view('admin.menu_items.edit', compact('barMenuItem','categories'))->with('catName','menu');
    }

    public function update(Request $request, BarMenuItem $barMenuItem)
    {
        $data = $request->validate([
            'bar_menu_category_id' => 'required|exists:bar_menu_categories,id',
            'name' => 'required|string|max:191',
            'price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('image')) {
            // delete old
            if ($barMenuItem->image) Storage::disk('public')->delete($barMenuItem->image);

            $file = $request->file('image');
            $path = $file->storePublicly('bars/menu-items', 'public');

            if (class_exists(Image::class)) {
                $full = storage_path('app/public/'.$path);
                Image::make($full)->fit(800,600,function($c){ $c->upsize(); })->save();
            }

            $data['image'] = $path;
        }

        $barMenuItem->update($data);

        return redirect(url('admin/bar-menu-items'))->with('success','Menu item updated.');
    }

    public function destroy(BarMenuItem $barMenuItem)
    {
        if ($barMenuItem->image) Storage::disk('public')->delete($barMenuItem->image);
        $barMenuItem->delete();
        return redirect(url('admin/bar-menu-items'))->with('success','Menu item deleted.');
    }
}

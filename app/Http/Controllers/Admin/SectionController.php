<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $page = $request->get('page');
        $type = $request->get('type');
        
        $sections = Section::query()
            ->when($q, fn($query) => $query->where('title', 'like', "%{$q}%")
                ->orWhere('slug', 'like', "%{$q}%"))
            ->when($page, fn($query) => $query->where('page', $page))
            ->when($type, fn($query) => $query->where('section_type', $type))
            ->orderBy('order', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.sections.index', [
            'sections' => $sections,
            'title' => 'Sections List',
            'catName' => 'cms',
            'subCatName' => 'sections',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        return view('admin.sections.create', [
            'title' => 'Add New Section',
            'catName' => 'cms',
            'subCatName' => 'sections',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:sections,slug',
            'content' => 'nullable|string',
            'section_type' => 'required|string|max:100',
            'page' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $data['slug'] = $data['slug'] ?: Section::generateUniqueSlug(Str::slug($request->title));
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('sections', 'public');
        }

        // Handle settings if provided
        if ($request->has('settings')) {
            $data['settings'] = json_decode($request->settings, true);
        }

        Section::create($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section created successfully.');
    }

    public function edit(Section $section)
    {
        return view('admin.sections.create', [ // reuse create view
            'section' => $section,
            'title' => 'Edit Section',
            'catName' => 'cms',
            'subCatName' => 'sections',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(Request $request, Section $section)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:sections,slug,' . $section->id,
            'content' => 'nullable|string',
            'section_type' => 'required|string|max:100',
            'page' => 'nullable|string|max:100',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        // Generate unique slug if not provided or if it changed
        if (empty($data['slug']) || $data['slug'] !== $section->slug) {
            $baseSlug = $data['slug'] ?: Str::slug($request->title);
            $data['slug'] = Section::generateUniqueSlug($baseSlug, $section->id);
        }
        
        $data['is_active'] = $request->has('is_active') ? 1 : 0;
        $data['order'] = $data['order'] ?? 0;

        if ($request->hasFile('image')) {
            if ($section->image) {
                Storage::disk('public')->delete($section->image);
            }
            $data['image'] = $request->file('image')->store('sections', 'public');
        }

        // Handle settings if provided
        if ($request->has('settings')) {
            $data['settings'] = json_decode($request->settings, true);
        }

        $section->update($data);

        return redirect()->route('admin.sections.index')->with('success', 'Section updated successfully.');
    }

    public function toggleStatus(Section $section)
    {
        $section->is_active = $section->is_active == 1 ? 0 : 1;
        $section->save();
        return response()->json([
            'success' => true,
            'status' => $section->is_active
        ]);
    }

    public function destroy(Section $section)
    {
        if ($section->image) {
            Storage::disk('public')->delete($section->image);
        }

        $section->delete();
        return back()->with('success', 'Section deleted successfully.');
    }
}

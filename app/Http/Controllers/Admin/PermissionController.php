<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(Request $request)
    {
        $permissions = Permission::query()
            ->orderBy('group', 'asc')
            ->orderBy('name', 'asc')
            ->get();

        $groups = Permission::distinct()->pluck('group')->filter()->sort();

        return view('admin.permissions.index', [
            'permissions' => $permissions,
            'groups' => $groups,
            'title' => 'Permissions List',
            'catName' => 'auth',
            'subCatName' => 'permissions',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        return view('admin.permissions.create', [
            'title' => 'Add New Permission',
            'catName' => 'auth',
            'subCatName' => 'permissions',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:permissions,slug',
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        Permission::create($data);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function edit(Permission $permission)
    {
        return view('admin.permissions.create', [ // reuse create view
            'permission' => $permission,
            'title' => 'Edit Permission',
            'catName' => 'auth',
            'subCatName' => 'permissions',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:permissions,slug,' . $permission->id,
            'group' => 'nullable|string|max:100',
            'description' => 'nullable|string',
        ]);

        $permission->update($data);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function destroy(Permission $permission)
    {
        $permission->roles()->detach();
        $permission->delete();
        return back()->with('success', 'Permission deleted successfully.');
    }
}

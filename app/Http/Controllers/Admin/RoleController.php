<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        
        $roles = Role::withCount(['users', 'permissions'])
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%")
                ->orWhere('slug', 'like', "%{$q}%"))
            ->orderBy('name', 'asc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.roles.index', [
            'roles' => $roles,
            'title' => 'Roles List',
            'catName' => 'auth',
            'subCatName' => 'roles',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        
        return view('admin.roles.create', [
            'permissions' => $permissions,
            'title' => 'Add New Role',
            'catName' => 'auth',
            'subCatName' => 'roles',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $role = Role::create($data);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('group')->orderBy('name')->get()->groupBy('group');
        $role->load('permissions');
        
        return view('admin.roles.create', [ // reuse create view
            'role' => $role,
            'permissions' => $permissions,
            'title' => 'Edit Role',
            'catName' => 'auth',
            'subCatName' => 'roles',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $role->update($data);

        if ($request->has('permissions')) {
            $role->permissions()->sync($request->permissions);
        } else {
            $role->permissions()->detach();
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully.');
    }

    public function toggleStatus(Role $role)
    {
        $role->is_active = $role->is_active == 1 ? 0 : 1;
        $role->save();
        return response()->json([
            'success' => true,
            'status' => $role->is_active
        ]);
    }

    public function destroy(Role $role)
    {
        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();
        return back()->with('success', 'Role deleted successfully.');
    }
}

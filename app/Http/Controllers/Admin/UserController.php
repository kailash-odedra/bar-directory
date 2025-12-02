<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        
        // Optimize: Select only needed columns
        $users = User::select('users.id', 'users.name', 'users.email', 'users.created_at', 'users.updated_at')
            ->with('roles:id,name,slug')
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'title' => 'Users List',
            'catName' => 'auth',
            'subCatName' => 'users',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function create()
    {
        // Only show admin role for assignment
        $roles = Role::where('slug', 'admin')->where('is_active', true)->orderBy('name')->get();
        
        return view('admin.users.create', [
            'roles' => $roles,
            'title' => 'Add New User',
            'catName' => 'auth',
            'subCatName' => 'users',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $data['password'] = Hash::make($data['password']);
        unset($data['roles']);

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->storePublicly('users/images', 'public');
        }

        $user = User::create($data);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        // Only show admin role for assignment
        $roles = Role::where('slug', 'admin')->where('is_active', true)->orderBy('name')->get();
        
        $user->load('roles');
        
        return view('admin.users.create', [ // reuse create view
            'user' => $user,
            'roles' => $roles,
            'title' => 'Edit User',
            'catName' => 'auth',
            'subCatName' => 'users',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $data['image'] = $request->file('image')->storePublicly('users/images', 'public');
        }

        unset($data['roles']);
        $user->update($data);

        if ($request->has('roles')) {
            $user->roles()->sync($request->roles);
        } else {
            $user->roles()->detach();
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Delete user image if exists
        if ($user->image && Storage::disk('public')->exists($user->image)) {
            Storage::disk('public')->delete($user->image);
        }
        
        $user->roles()->detach();
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }
}

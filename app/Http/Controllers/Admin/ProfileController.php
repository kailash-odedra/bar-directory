<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile page
     */
    public function show()
    {
        $user = Auth::guard('admin')->user();
        // Roles are already cached, but load if not already loaded
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        return view('admin.profile.show', [
            'user' => $user,
            'title' => 'My Profile',
            'catName' => 'profile',
            'subCatName' => 'profile',
            'scrollspy' => false,
            'simplePage' => false,
        ]);
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $user = Auth::guard('admin')->user();
        
        // Clear role cache if roles are updated (though not in this method, but good practice)
        $user->clearRoleCache();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            
            // Store new image
            $data['image'] = $request->file('image')->storePublicly('users/images', 'public');
        }

        // Update password if provided
        if ($request->filled('password')) {
            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }

            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully.');
    }
}

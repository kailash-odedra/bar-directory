<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // If already logged in as admin, redirect to dashboard
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            // Only redirect if user is actually an admin
            if ($user->isAdmin()) {
                return redirect()->route('analytics');
            }
        }

        return view('admin.auth.login', [
            'title' => 'Admin Login',
            'catName' => 'auth',
            'scrollspy' => false,
            'simplePage' => true,
        ]);
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'nullable|boolean',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Use admin guard for admin login
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $user = Auth::guard('admin')->user();
            
            // Verify user is admin before allowing login
            if (!$user->isAdmin()) {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'email' => 'You do not have admin access.',
                ])->withInput($request->only('email'));
            }
            
            // Eager load roles to avoid N+1 queries
            $user->load('roles');
            
            $request->session()->regenerate();

            return redirect()->intended(route('analytics'))->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }
}

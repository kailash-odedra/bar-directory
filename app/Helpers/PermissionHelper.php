<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class PermissionHelper
{
    /**
     * Check if the authenticated user has a permission
     * Admin users automatically have all permissions
     */
    public static function can($permissionSlug)
    {
        // Try admin guard first (for admin panel)
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            
            // Eager load roles if not already loaded
            if (!$user->relationLoaded('roles')) {
                $user->load('roles');
            }
            
            // Admin users have all permissions
            if ($user->isAdmin()) {
                return true;
            }

            return $user->hasPermission($permissionSlug);
        }
        
        // Fallback to web guard (for frontend)
        if (Auth::check()) {
            $user = Auth::user();
            
            // Eager load roles if not already loaded
            if (!$user->relationLoaded('roles')) {
                $user->load('roles');
            }
            
            // Admin users have all permissions
            if ($user->isAdmin()) {
                return true;
            }

            return $user->hasPermission($permissionSlug);
        }

        return false;
    }

    /**
     * Check if the authenticated user has any of the given permissions
     */
    public static function canAny(array $permissionSlugs)
    {
        foreach ($permissionSlugs as $permission) {
            if (self::can($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if the authenticated user has all of the given permissions
     */
    public static function canAll(array $permissionSlugs)
    {
        foreach ($permissionSlugs as $permission) {
            if (!self::can($permission)) {
                return false;
            }
        }
        return true;
    }
}


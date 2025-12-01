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
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();
        
        // Admin users have all permissions
        if ($user->isAdmin()) {
            return true;
        }

        return $user->hasPermission($permissionSlug);
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


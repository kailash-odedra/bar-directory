<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasEncryptedRouteKey, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')->withTimestamps();
    }

    /**
     * Get cached roles for the user (avoids N+1 queries)
     */
    protected function getCachedRoles()
    {
        // If roles are already loaded, use them
        if ($this->relationLoaded('roles')) {
            return $this->roles;
        }
        
        $cacheKey = "user.{$this->id}.roles";
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () {
            return $this->roles()->get();
        });
    }

    /**
     * Clear cached roles and permissions for the user
     */
    public function clearRoleCache()
    {
        \Illuminate\Support\Facades\Cache::forget("user.{$this->id}.roles");
        \Illuminate\Support\Facades\Cache::forget("user.{$this->id}.is_admin");
        
        // Clear all permission caches for this user (pattern matching)
        $cachePrefix = "user.{$this->id}.permission.";
        // Note: Laravel cache doesn't support pattern deletion natively,
        // but individual permission caches will expire naturally (5 min TTL)
        // For immediate clearing, we'd need to track permission checks or use tags
    }

    // Helper methods
    public function hasRole($roleSlug)
    {
        // Use cached roles to avoid N+1 queries
        $roles = $this->getCachedRoles();
        return $roles->contains('slug', $roleSlug);
    }

    public function hasPermission($permissionSlug)
    {
        // Admin users have all permissions
        if ($this->isAdmin()) {
            return true;
        }
        
        // Cache permissions check
        $cacheKey = "user.{$this->id}.permission.{$permissionSlug}";
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () use ($permissionSlug) {
            // Use cached roles
            $roles = $this->getCachedRoles();
            
            // Check if any role has the permission
            foreach ($roles as $role) {
                if ($role->permissions()->where('slug', $permissionSlug)->exists()) {
                    return true;
                }
            }
            
            return false;
        });
    }

    /**
     * Check if user is admin (cached)
     */
    public function isAdmin()
    {
        // If roles are already loaded, use them directly
        if ($this->relationLoaded('roles')) {
            return $this->roles->contains('slug', 'admin') || $this->roles->contains('slug', 'super-admin');
        }
        
        $cacheKey = "user.{$this->id}.is_admin";
        
        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 300, function () {
            $roles = $this->getCachedRoles();
            return $roles->contains('slug', 'admin') || $roles->contains('slug', 'super-admin');
        });
    }

    public function assignRole($roleId)
    {
        if (!$this->roles()->where('role_id', $roleId)->exists()) {
            $this->roles()->attach($roleId);
            $this->clearRoleCache();
        }
    }

    public function removeRole($roleId)
    {
        $this->roles()->detach($roleId);
        $this->clearRoleCache();
    }

    /**
     * Get the user's profile image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return \Illuminate\Support\Facades\Storage::url($this->image);
        }
        return asset('build/resources/images/profile-30.png');
    }
}

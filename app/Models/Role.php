<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Role extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($role) {
            if (empty($role->slug)) {
                $role->slug = Role::generateUniqueSlug(Str::slug($role->name));
            }
        });
    }

    /**
     * Generate a unique slug by appending a number if the slug already exists
     */
    public static function generateUniqueSlug($baseSlug, $excludeId = null)
    {
        $slug = $baseSlug;
        $counter = 1;
        
        while (static::where('slug', $slug)
            ->when($excludeId, fn($query) => $query->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active;
    }

    public function hasPermission($permissionSlug)
    {
        return $this->permissions()->where('slug', $permissionSlug)->exists();
    }
}

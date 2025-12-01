<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Permission extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'name',
        'slug',
        'group',
        'description',
    ];

    protected static function booted()
    {
        static::creating(function ($permission) {
            if (empty($permission->slug)) {
                $permission->slug = Permission::generateUniqueSlug(Str::slug($permission->name));
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
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // Scopes
    public function scopeByGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}

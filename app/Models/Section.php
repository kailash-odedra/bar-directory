<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Section extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'title',
        'slug',
        'content',
        'section_type',
        'page',
        'order',
        'is_active',
        'settings',
        'image',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'order' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function ($section) {
            if (empty($section->slug)) {
                $section->slug = static::generateUniqueSlug(Str::slug($section->title));
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

    // Scope for active sections
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for sections by page
    public function scopeForPage($query, $page)
    {
        return $query->where(function($q) use ($page) {
            $q->where('page', $page)
              ->orWhereNull('page'); // Global sections
        });
    }

    // Scope for sections by type
    public function scopeOfType($query, $type)
    {
        return $query->where('section_type', $type);
    }

    // Helper methods
    public function isActive()
    {
        return $this->is_active;
    }

    public function isGlobal()
    {
        return is_null($this->page);
    }
}

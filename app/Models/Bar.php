<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bar extends Model
{
    use HasFactory, HasEncryptedRouteKey;

    protected $fillable = [
        'name','slug','short_description','full_description','logo','cover_image',
        'claimed','claimed_by','verified','meta_title','meta_description','meta_keywords',
        'facebook','instagram','tiktok','youtube','website','status','is_featured','created_by'
    ];

    // Auto-generate slug on creating if not provided
    protected static function booted()
    {
        static::creating(function ($bar) {
            if (empty($bar->slug)) {
                $bar->slug = Str::slug($bar->name) . '-' . Str::random(5);
            }
        });
    }

    public function location() { return $this->hasOne(Location::class); }
    public function images() { return $this->hasMany(BarImage::class); }
    public function menuCategories() { return $this->hasMany(BarMenuCategory::class); }
    public function tags()
    {
        return $this->belongsToMany(BarTag::class, 'bar_tag', 'bar_id', 'tag_id');
    }

    public function reviews() { return $this->hasMany(BarReview::class); }
    public function events() { return $this->hasMany(Event::class); }
    public function timings() { return $this->hasMany(BarTiming::class); }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function claimedBy()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastUpdatedBy()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    /**
     * Get the owner/creator name
     * Priority: claimed_by (owner) > created_by (admin/creator) > last_updated_by (admin who updated)
     */
    public function getOwnerNameAttribute()
    {
        // If bar is claimed, show the owner who claimed it
        if ($this->claimed_by && $this->claimedBy) {
            return $this->claimedBy->name;
        }
        
        // Otherwise, show who created it (admin or frontend user)
        if ($this->created_by && $this->createdBy) {
            return $this->createdBy->name;
        }
        
        // Fallback to who last updated it (admin)
        if ($this->last_updated_by && $this->lastUpdatedBy) {
            return $this->lastUpdatedBy->name;
        }
        
        return '-';
    }

    public function owner()
    {
        return $this->claimedBy();
    }

    public function isActive() {
        return $this->status == 1;
    }

}


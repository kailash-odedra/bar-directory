<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Bar extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','slug','short_description','full_description','logo','cover_image',
        'claimed','claimed_by','verified','meta_title','meta_description','meta_keywords',
        'facebook','instagram','tiktok','youtube','website','status'
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

}


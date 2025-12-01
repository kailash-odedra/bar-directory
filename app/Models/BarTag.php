<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;

class BarTag extends Model
{
    use HasEncryptedRouteKey;
    protected $table = 'tags';
    protected $fillable = ['name', 'slug','status'];

    public function bars()
    {
        return $this->belongsToMany(
            Bar::class,
            'bar_tag',   // pivot table
            'tag_id',    // this model's key in pivot
            'bar_id'     // related model key in pivot
        );
    }
    public function isActive() {
        return $this->status == 1;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarTag extends Model
{
    protected $table = 'tags';
    protected $fillable = ['name', 'slug'];

    public function bars()
    {
        return $this->belongsToMany(
            Bar::class,
            'bar_tag',   // pivot table
            'tag_id',    // this model's key in pivot
            'bar_id'     // related model key in pivot
        );
    }
}

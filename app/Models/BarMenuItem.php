<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarMenuItem extends Model
{
    protected $fillable = [
        'bar_id', 'bar_menu_category_id', 'name', 'price', 'image', 'description'
    ];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }

    public function category()
    {
        return $this->belongsTo(BarMenuCategory::class);
    }
}

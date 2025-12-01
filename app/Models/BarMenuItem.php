<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;

class BarMenuItem extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'bar_id',
        'bar_menu_category_id',
        'name',
        'price',
        'image',
        'description',
        'status'
    ];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }

    public function category()
    {
        return $this->belongsTo(BarMenuCategory::class, 'bar_menu_category_id');
    }
}


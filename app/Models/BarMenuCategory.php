<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarMenuCategory extends Model
{
    protected $fillable = ['bar_id', 'name'];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }

    public function items()
    {
        return $this->hasMany(BarMenuItem::class);
    }
}

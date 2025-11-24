<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarTag extends Model
{
    protected $fillable = ['name', 'slug'];

    public function bars()
    {
        return $this->belongsToMany(Bar::class);
    }
}

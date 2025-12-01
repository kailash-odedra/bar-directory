<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'state_id',
        'name',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function regions()
    {
        return $this->hasMany(Region::class);
    }
}


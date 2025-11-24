<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'country', 'state', 'city', 'region', 'address', 'zipcode',
        'latitude', 'longitude'
    ];

    public function bars()
    {
        return $this->hasMany(Bar::class);
    }
}

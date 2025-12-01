<?php
namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'name',
        'slug',
        'iso_code',
        'iso2',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function cities()
    {
        return $this->hasManyThrough(City::class, State::class);
    }
}

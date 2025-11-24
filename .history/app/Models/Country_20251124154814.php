<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = ['name','iso2'];

    public function states()
    {
        return $this->hasMany(State::class);
    }
}

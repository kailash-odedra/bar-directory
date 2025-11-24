<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'bar_id','address','city','state_id','country_id','region',
        'zipcode','latitude','longitude','phone','email'
    ];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}

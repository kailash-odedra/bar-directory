<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarImage extends Model
{
    protected $fillable = ['bar_id','path','type','caption','order'];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }
}

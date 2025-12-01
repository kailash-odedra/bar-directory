<?php
namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;

class BarImage extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = ['bar_id','path','type','caption','order'];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }
}

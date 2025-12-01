<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;
class BarReview extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'bar_id',
        'user_id',
        'rating',
        'comment',
        'status'
    ];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

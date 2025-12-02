<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'bar_id', 'title', 'description', 'start_time', 'end_time','image', 'ticket_link','status','type'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }
    public function isActive() {
        return $this->status == 1;
    }
}

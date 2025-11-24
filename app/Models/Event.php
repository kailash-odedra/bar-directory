<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'bar_id', 'title', 'description', 'start_time', 'end_time','image', 'ticket_link'
    ];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }
}

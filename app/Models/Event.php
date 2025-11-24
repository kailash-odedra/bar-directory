<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'bar_id', 'title', 'description', 'start_date', 'end_date', 'ticket_url'
    ];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }
}

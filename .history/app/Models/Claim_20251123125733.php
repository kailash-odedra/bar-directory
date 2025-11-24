<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Claim extends Model
{
    protected $fillable = ['bar_id', 'user_id', 'status', 'verify_document'];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

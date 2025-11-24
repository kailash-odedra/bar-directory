<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarTiming extends Model
{
    protected $fillable = ['bar_id','weekday','open_time','close_time','is_closed'];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }
}

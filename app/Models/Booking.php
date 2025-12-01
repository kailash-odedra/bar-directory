<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory, HasEncryptedRouteKey;

    protected $fillable = [
        'bar_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'people_count',
        'booking_date',
        'booking_time',
        'duration_minutes',
        'ends_at',
        'table_area',
        'special_request',
        'price',
        'status',
        'created_by_admin',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'booking_time' => 'string', // we'll handle time as string
        'ends_at' => 'datetime',
        'created_by_admin' => 'boolean',
    ];

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // convenience helpers
    public function isPending() { return $this->status === 'pending'; }
    public function isConfirmed() { return $this->status === 'confirmed'; }
    public function isCancelled() { return $this->status === 'cancelled'; }
}

<?php

namespace App\Models;

use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Claim extends Model
{
    use HasEncryptedRouteKey;
    protected $fillable = [
        'bar_id', 
        'user_id', 
        'status', 
        'attachment',
        'full_name',
        'phone_number',
        'email_address',
        'role',
        'relationship_proof',
        'comments',
        'claim_request_id',
        'admin_notes',
        'admin_documents',
        'verification_status',
        'verified_at',
        'verified_by'
    ];

    protected $casts = [
        'admin_documents' => 'array',
        'verified_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($claim) {
            if (empty($claim->claim_request_id)) {
                $claim->claim_request_id = 'CLM-' . strtoupper(Str::random(8));
            }
        });
    }

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function isPending()
    {
        return $this->verification_status === 'pending';
    }

    public function needsInfo()
    {
        return $this->verification_status === 'needs_info';
    }

    public function isApproved()
    {
        return $this->verification_status === 'approved';
    }

    public function isRejected()
    {
        return $this->verification_status === 'rejected';
    }
}

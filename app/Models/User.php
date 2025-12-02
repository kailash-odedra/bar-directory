<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasEncryptedRouteKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasEncryptedRouteKey, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    // Helper methods
    public function hasRole($roleSlug)
    {
        return $this->roles()->where('slug', $roleSlug)->exists();
    }

    public function hasPermission($permissionSlug)
    {
        // Admin users have all permissions
        if ($this->isAdmin()) {
            return true;
        }
        
        return $this->roles()->whereHas('permissions', function($query) use ($permissionSlug) {
            $query->where('slug', $permissionSlug);
        })->exists();
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin') || $this->hasRole('super-admin');
    }

    public function assignRole($roleId)
    {
        if (!$this->roles()->where('role_id', $roleId)->exists()) {
            $this->roles()->attach($roleId);
        }
    }

    public function removeRole($roleId)
    {
        $this->roles()->detach($roleId);
    }

    /**
     * Get the user's profile image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return \Illuminate\Support\Facades\Storage::url($this->image);
        }
        return asset('build/resources/images/profile-30.png');
    }
}

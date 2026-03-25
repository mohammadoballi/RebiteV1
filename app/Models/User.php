<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laratrust\Contracts\LaratrustUser;
use Laratrust\Traits\HasRolesAndPermissions;

class User extends Authenticatable implements LaratrustUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRolesAndPermissions;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'status',
        'address',
        'city',
        'role_type',
        'health_certificate',
        'organization_name',
        'organization_license',
        'rejection_reason',
        'locale',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ── Relationships ──

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function donationRequests()
    {
        return $this->hasMany(DonationRequest::class, 'charity_id');
    }

    public function assignments()
    {
        return $this->hasMany(DonationAssignment::class, 'volunteer_id');
    }

    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'rater_id');
    }

    public function ratingsReceived()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role_type', $role);
    }

    // ── Helpers ──

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }
}

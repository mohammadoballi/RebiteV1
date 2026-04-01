<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Donation extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_ASSIGNED = 'assigned';
    const STATUS_IN_TRANSIT = 'in_transit';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'user_id',
        'city_id',
        'town_id',
        'food_type',
        'description',
        'quantity',
        'quantity_unit',
        'pickup_address',
        'latitude',
        'longitude',
        'pickup_time',
        'expiry_time',
        'status',
        'notes',
        'image',
        'volunteers_needed',
        'delivery_volunteers_needed',
        'packaging_volunteers_needed',
        'volunteers_count',
    ];

    protected $casts = [
        'pickup_time' => 'datetime',
        'expiry_time' => 'datetime',
        'volunteers_needed' => 'integer',
        'delivery_volunteers_needed' => 'integer',
        'packaging_volunteers_needed' => 'integer',
        'volunteers_count' => 'integer',
    ];

    protected $appends = ['is_full', 'items_summary'];

    // ── Relationships ──

    public function donor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cityRelation()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function town()
    {
        return $this->belongsTo(Town::class, 'town_id');
    }

    public function items()
    {
        return $this->hasMany(DonationItem::class);
    }

    public function requests()
    {
        return $this->hasMany(DonationRequest::class);
    }

    public function assignments()
    {
        return $this->hasMany(DonationAssignment::class);
    }

    public function ratings()
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    // ── Accessors ──

    public function getIsFullAttribute(): bool
    {
        return $this->volunteers_count >= $this->volunteers_needed;
    }

    public function getItemsSummaryAttribute(): string
    {
        if ($this->relationLoaded('items') && $this->items->count() > 0) {
            return $this->items->map(fn ($i) => $i->food_type)->implode(', ');
        }
        return $this->food_type ?? '';
    }

    // ── Scopes ──

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED)
                     ->where(function ($q) {
                         $q->whereNull('expiry_time')
                           ->orWhere('expiry_time', '>', now());
                     })
                     ->whereColumn('volunteers_count', '<', 'volunteers_needed');
    }

    public function scopeNotFull($query)
    {
        return $query->whereColumn('volunteers_count', '<', 'volunteers_needed');
    }

    public function scopeNeedsVolunteerType($query, string $type)
    {
        $col = $type === 'packaging' ? 'packaging_volunteers_needed' : 'delivery_volunteers_needed';

        return $query->where($col, '>', 0)
            ->where($col, '>', function ($sub) use ($type) {
                $sub->selectRaw('COUNT(*)')
                    ->from('donation_assignments')
                    ->whereColumn('donation_assignments.donation_id', 'donations.id')
                    ->where('donation_assignments.assignment_type', $type)
                    ->whereNotIn('donation_assignments.status', ['cancelled']);
            });
    }
}

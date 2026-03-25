<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationAssignment extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    const TYPE_DELIVERY = 'delivery';
    const TYPE_PACKAGING = 'packaging';

    protected $fillable = [
        'donation_id',
        'donation_request_id',
        'volunteer_id',
        'assignment_type',
        'status',
        'pickup_at',
        'delivered_at',
        'notes',
        'is_external_delivery',
    ];

    protected $casts = [
        'pickup_at' => 'datetime',
        'delivered_at' => 'datetime',
        'is_external_delivery' => 'boolean',
    ];

    // ── Relationships ──

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function donationRequest()
    {
        return $this->belongsTo(DonationRequest::class);
    }

    public function volunteer()
    {
        return $this->belongsTo(User::class, 'volunteer_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationRequest extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'donation_id',
        'charity_id',
        'status',
        'message',
    ];

    // ── Relationships ──

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }

    public function charity()
    {
        return $this->belongsTo(User::class, 'charity_id');
    }
}

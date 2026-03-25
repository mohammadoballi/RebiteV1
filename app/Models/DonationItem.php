<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_id',
        'food_type',
        'quantity',
        'quantity_unit',
        'description',
    ];

    public function donation()
    {
        return $this->belongsTo(Donation::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'rater_id',
        'rateable_id',
        'rateable_type',
        'rating',
        'comment',
    ];

    // ── Relationships ──

    public function rater()
    {
        return $this->belongsTo(User::class, 'rater_id');
    }

    public function rateable()
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Repositories;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class RatingRepository extends BaseRepository
{
    public function __construct(Rating $model)
    {
        parent::__construct($model);
    }

    public function getByRater(int $raterId): Collection
    {
        return $this->query()
            ->where('rater_id', $raterId)
            ->with('rateable')
            ->latest()
            ->get();
    }

    public function getForRateable(Model $rateable): Collection
    {
        return $rateable->morphMany(Rating::class, 'rateable')->latest()->get();
    }
}

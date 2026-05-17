<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRatingRequest;
use App\Models\Rating;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RatingController extends Controller
{
    public function store(StoreRatingRequest $request): JsonResponse
    {
        $data = $request->validated();
        $targetUser = User::findOrFail($data['rateable_id']);

        Rating::create([
            'rater_id'      => auth()->id(),
            'donation_id'   => $data['donation_id'] ?? null,
            'rateable_id'   => $targetUser->id,
            'rateable_type' => User::class,
            'rating'        => $data['rating'],
            'comment'       => $data['comment'] ?? null,
        ]);

        $pointsKey = $targetUser->hasRole('volunteer')
            ? 'volunteer_rating_points'
            : 'donor_rating_points';
        $defaultPoints = $targetUser->hasRole('volunteer') ? 3 : 0;
        $points = Setting::getInt($pointsKey, $defaultPoints);

        if ($points > 0) {
            $targetUser->addPoints($points);
        }

        return response()->json([
            'message'     => __('Rating submitted successfully.'),
            'rateable_id' => $targetUser->id,
        ], 201);
    }

    public function myRatings(): JsonResponse
    {
        $ratings = Rating::where('rateable_id', auth()->id())
            ->where('rateable_type', User::class)
            ->with('rater:id,name')
            ->latest()
            ->get();

        $avg = round((float) $ratings->avg('rating'), 1);
        $count = $ratings->count();

        return response()->json([
            'ratings'       => $ratings,
            'average'       => $avg,
            'count'         => $count,
            'points'        => auth()->user()->points,
            'level'         => auth()->user()->getPointsLevel(),
            'level_color'   => auth()->user()->getPointsLevelColor(),
        ]);
    }
}

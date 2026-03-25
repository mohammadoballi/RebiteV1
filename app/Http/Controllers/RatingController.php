<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'rateable_id'   => ['required', 'integer'],
            'rateable_type' => ['required', 'string', 'in:user'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'comment'       => ['nullable', 'string', 'max:500'],
        ]);

        $targetUser = User::findOrFail($request->rateable_id);

        $existing = Rating::where('rater_id', auth()->id())
            ->where('rateable_id', $targetUser->id)
            ->where('rateable_type', User::class)
            ->first();

        if ($existing) {
            $existing->update([
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]);
            $msg = __('Rating updated successfully.');
        } else {
            Rating::create([
                'rater_id'      => auth()->id(),
                'rateable_id'   => $targetUser->id,
                'rateable_type' => User::class,
                'rating'        => $request->rating,
                'comment'       => $request->comment,
            ]);

            $targetUser->addPoints(3);
            $msg = __('Rating submitted successfully.');
        }

        return response()->json(['message' => $msg]);
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

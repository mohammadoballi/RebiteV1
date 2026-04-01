<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckCharitySubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('charity') && !$user->hasActiveSubscription()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => __('An active subscription is required to perform this action.'),
                ], 403);
            }

            return redirect()->route('charity.subscription.index')
                ->with('error', __('Please subscribe to access this feature.'));
        }

        return $next($request);
    }
}

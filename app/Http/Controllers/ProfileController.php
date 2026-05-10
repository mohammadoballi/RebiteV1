<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\City;
use App\Services\UserService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function show(Request $request)
    {
        if ($response = $this->blockedCharityProfileResponse($request)) {
            return $response;
        }

        $cities = City::orderBy('name')->get();

        return view('profile.show', [
            'user'   => auth()->user()->load('roles'),
            'cities' => $cities,
        ]);
    }

    public function update(UpdateProfileRequest $request)
    {
        if ($response = $this->blockedCharityProfileResponse($request)) {
            return $response;
        }

        $data = $request->validated();
        unset($data['avatar']);

        if ($request->hasFile('avatar')) {
            $this->userService->uploadAvatar(auth()->id(), $request->file('avatar'));
        }

        $this->userService->updateProfile(auth()->id(), $data);

        if ($request->expectsJson()) {
            return response()->json(['message' => __('Profile updated successfully.')]);
        }

        return redirect()->back()->with('success', __('Profile updated successfully.'));
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        if ($response = $this->blockedCharityProfileResponse($request)) {
            return $response;
        }

        $this->userService->changePassword(auth()->id(), $request->validated('password'));

        if ($request->expectsJson()) {
            return response()->json(['message' => __('Password changed successfully.')]);
        }

        return redirect()->back()->with('success', __('Password changed successfully.'));
    }

    public function uploadAvatar(Request $request)
    {
        if ($response = $this->blockedCharityProfileResponse($request)) {
            return $response;
        }

        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $path = $this->userService->uploadAvatar(auth()->id(), $request->file('avatar'));

        return response()->json([
            'message' => __('Avatar uploaded successfully.'),
            'path'    => asset('storage/' . $path),
        ]);
    }

    /**
     * Charities must have an active subscription before using profile routes.
     */
    protected function blockedCharityProfileResponse(Request $request): ?\Symfony\Component\HttpFoundation\Response
    {
        $user = auth()->user();
        if (!$user || !$user->hasRole('charity') || $user->hasActiveSubscription()) {
            return null;
        }

        $message = __('general.subscription_required_for_profile');

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 403);
        }

        return redirect()->route('charity.subscription.index')->with('error', $message);
    }
}

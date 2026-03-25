<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\ChangePasswordRequest;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function show()
    {
        return view('profile.show', ['user' => auth()->user()]);
    }

    public function update(UpdateProfileRequest $request)
    {
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
        $this->userService->changePassword(auth()->id(), $request->validated('password'));

        if ($request->expectsJson()) {
            return response()->json(['message' => __('Password changed successfully.')]);
        }

        return redirect()->back()->with('success', __('Password changed successfully.'));
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:2048'],
        ]);

        $path = $this->userService->uploadAvatar(auth()->id(), $request->file('avatar'));

        return response()->json([
            'message' => __('Avatar uploaded successfully.'),
            'path'    => asset('storage/' . $path),
        ]);
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\UserService;

class RegisterController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('health_certificate')) {
            $data['health_certificate'] = $request->file('health_certificate')
                ->store('certificates', 'public');
        }

        if ($request->hasFile('organization_license')) {
            $data['organization_license'] = $request->file('organization_license')
                ->store('licenses', 'public');
        }

        $user = $this->userService->register($data);

        return redirect()->route('login')
            ->with('success', __('Registration successful! Your account is pending approval.'));
    }
}

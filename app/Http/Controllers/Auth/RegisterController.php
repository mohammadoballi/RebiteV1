<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\City;
use App\Services\UserService;

class RegisterController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function showRegistrationForm()
    {
        $cities = City::orderBy('name')->get();

        return view('auth.register', compact('cities'));
    }

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        if (($data['role'] ?? '') === 'volunteer' && empty($data['role_type'])) {
            $data['role_type'] = 'delivery';
        }

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

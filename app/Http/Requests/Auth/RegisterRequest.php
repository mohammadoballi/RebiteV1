<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'email', 'unique:users,email'],
            'password'             => ['required', 'confirmed', 'min:8'],
            'phone'                => ['required', 'string'],
            'role'                 => ['required', 'in:donor,charity,volunteer'],
            'role_type'            => ['nullable', 'in:delivery,packaging'],
            'health_certificate'   => ['required_if:role,donor', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
            'organization_name'    => ['required_if:role,charity'],
            'organization_license' => ['required_if:role,charity', 'file', 'mimes:pdf,jpg,png', 'max:5120'],
            'address'              => ['nullable', 'string'],
            'city'                 => ['nullable', 'string'],
            'city_id'              => ['nullable', 'exists:cities,id'],
            'town_id'              => ['nullable', 'exists:towns,id'],
            'safety_guidelines'    => ['required', 'accepted'],
        ];
    }
}

<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'city'    => ['nullable', 'string'],
            'avatar'  => ['nullable', 'image', 'max:2048'],
        ];
    }
}

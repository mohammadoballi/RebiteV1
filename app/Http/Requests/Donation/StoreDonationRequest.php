<?php

namespace App\Http\Requests\Donation;

use Illuminate\Foundation\Http\FormRequest;

class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id'           => ['nullable', 'exists:cities,id'],
            'town_id'           => ['nullable', 'exists:towns,id'],
            'description'       => ['nullable', 'string'],
            'pickup_address'    => ['required', 'string'],
            'latitude'          => ['nullable', 'numeric'],
            'longitude'         => ['nullable', 'numeric'],
            'pickup_time'       => ['required', 'date', 'after:now'],
            'expiry_time'       => ['nullable', 'date', 'after:pickup_time'],
            'notes'             => ['nullable', 'string'],
            'image'             => ['nullable', 'image', 'max:5120'],
            'volunteers_needed' => ['required', 'integer', 'min:1', 'max:50'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.food_type'     => ['required', 'string', 'max:255'],
            'items.*.quantity'      => ['required', 'string'],
            'items.*.quantity_unit' => ['required', 'string', 'in:kg,pieces,boxes,bags,plates'],
            'items.*.description'   => ['nullable', 'string', 'max:500'],
        ];
    }
}

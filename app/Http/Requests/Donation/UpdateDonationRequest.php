<?php

namespace App\Http\Requests\Donation;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDonationRequest extends FormRequest
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
            'pickup_address'    => ['nullable', 'string'],
            'latitude'          => ['nullable', 'numeric'],
            'longitude'         => ['nullable', 'numeric'],
            'pickup_time'       => ['nullable', 'date', 'after:now'],
            'expiry_time'       => ['nullable', 'date', 'after:pickup_time'],
            'notes'             => ['nullable', 'string'],
            'image'             => ['nullable', 'image', 'max:5120'],
            'delivery_volunteers_needed'  => ['nullable', 'integer', 'min:0', 'max:50'],
            'packaging_volunteers_needed' => ['nullable', 'integer', 'min:0', 'max:50'],
            'items'             => ['nullable', 'array', 'min:1'],
            'items.*.food_type'     => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity'      => ['required_with:items', 'string'],
            'items.*.quantity_unit' => ['required_with:items', 'string', 'in:kg,pieces,boxes,bags,plates'],
            'items.*.description'   => ['nullable', 'string', 'max:500'],
        ];
    }
}

<?php

namespace App\Http\Requests\Donation;

use App\Models\FoodCategory;
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
            'city_id' => ['required', 'exists:cities,id'],
            'town_id' => ['required', 'exists:towns,id'],
            'pickup_address' => ['required', 'string'],
            'food_category_id' => ['required', 'exists:food_categories,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'pickup_time' => ['required', 'date', 'after:now'],
            'expiry_time' => ['nullable', 'date', 'after:pickup_time'],
            'notes' => ['nullable', 'string'],
            'delivery_volunteers_needed' => ['required', 'integer', 'min:0', 'max:50'],
            'packaging_volunteers_needed' => ['required', 'integer', 'min:0', 'max:50'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.food_type' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'string'],
            'items.*.quantity_unit' => ['required', 'string', 'in:kg,pieces,boxes,bags,plates'],
            'items.*.description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $id = $this->input('food_category_id');
            if (!$id) {
                return;
            }
            $leaf = FoodCategory::query()->where('id', $id)->whereNotNull('parent_id')->exists();
            if (!$leaf) {
                $validator->errors()->add('food_category_id', __('Select a specific food subcategory.'));
            }
        });
    }
}

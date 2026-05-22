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
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.quantity_unit' => ['required', 'string', 'in:kg,pieces,boxes,bags,plates'],
            'items.*.description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            if (!$user->city_id || !$user->town_id) {
                $validator->errors()->add('profile', __('Please set your city and town in your profile before creating a donation.'));
            }

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

<?php

namespace App\Http\Requests\Donation;

use App\Models\FoodCategory;
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
            'pickup_address' => ['nullable', 'string'],
            'food_category_id' => ['nullable', 'exists:food_categories,id'],
            'latitude'          => ['nullable', 'numeric'],
            'longitude'         => ['nullable', 'numeric'],
            'pickup_time'       => ['nullable', 'date', 'after:now'],
            'expiry_time'       => ['nullable', 'date', 'after:pickup_time'],
            'notes'             => ['nullable', 'string'],
            'delivery_volunteers_needed'  => ['nullable', 'integer', 'min:0', 'max:50'],
            'packaging_volunteers_needed' => ['nullable', 'integer', 'min:0', 'max:50'],
            'items'             => ['nullable', 'array', 'min:1'],
            'items.*.food_type'     => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity'      => ['required_with:items', 'numeric', 'gt:0'],
            'items.*.quantity_unit' => ['required_with:items', 'string', 'in:kg,pieces,boxes,bags,plates'],
            'items.*.description'   => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $id = $this->input('food_category_id');
            if ($id === null || $id === '') {
                return;
            }
            $leaf = FoodCategory::query()->where('id', $id)->whereNotNull('parent_id')->exists();
            if (!$leaf) {
                $validator->errors()->add('food_category_id', __('Select a specific food subcategory.'));
            }
        });
    }
}

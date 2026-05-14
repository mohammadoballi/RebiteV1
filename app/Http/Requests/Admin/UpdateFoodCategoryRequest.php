<?php

namespace App\Http\Requests\Admin;

use App\Models\FoodCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFoodCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin') ?? false;
    }

    public function rules(): array
    {
        $foodCategory = $this->route('food_category');
        $id = $foodCategory instanceof FoodCategory ? $foodCategory->id : (int) $foodCategory;

        return [
            'parent_id' => [
                'nullable',
                'exists:food_categories,id',
                Rule::notIn([$id]),
            ],
            'name' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $parent = $this->input('parent_id');
        if ($parent === '' || $parent === null) {
            $this->merge(['parent_id' => null]);
        }
    }
}

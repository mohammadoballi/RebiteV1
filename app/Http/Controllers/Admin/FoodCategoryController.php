<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFoodCategoryRequest;
use App\Http\Requests\Admin\UpdateFoodCategoryRequest;
use App\Models\FoodCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FoodCategoryController extends Controller
{
    public function index(): View
    {
        $parents = FoodCategory::roots()->with('children')->get();

        return view('admin.food-categories.index', compact('parents'));
    }

    public function store(StoreFoodCategoryRequest $request): RedirectResponse
    {
        FoodCategory::create($request->validated());

        return back()->with('success', __('food_categories.created'));
    }

    public function update(UpdateFoodCategoryRequest $request, FoodCategory $foodCategory): RedirectResponse
    {
        $foodCategory->update($request->validated());

        return back()->with('success', __('food_categories.updated'));
    }

    public function destroy(FoodCategory $foodCategory): RedirectResponse
    {
        if ($foodCategory->children()->exists()) {
            return back()->with('error', __('food_categories.remove_children_error'));
        }
        if ($foodCategory->donations()->exists()) {
            return back()->with('error', __('food_categories.cannot_delete_in_use'));
        }

        $foodCategory->delete();

        return back()->with('success', __('food_categories.deleted'));
    }
}

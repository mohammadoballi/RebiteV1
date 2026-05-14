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

        return back()->with('success', __('Category created.'));
    }

    public function update(UpdateFoodCategoryRequest $request, FoodCategory $foodCategory): RedirectResponse
    {
        $foodCategory->update($request->validated());

        return back()->with('success', __('Category updated.'));
    }

    public function destroy(FoodCategory $foodCategory): RedirectResponse
    {
        if ($foodCategory->children()->exists()) {
            return back()->with('error', __('Remove child categories first.'));
        }
        if ($foodCategory->donations()->exists()) {
            return back()->with('error', __('Cannot delete: donations use this category.'));
        }

        $foodCategory->delete();

        return back()->with('success', __('Category deleted.'));
    }
}

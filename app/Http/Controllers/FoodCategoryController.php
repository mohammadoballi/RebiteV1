<?php

namespace App\Http\Controllers;

use App\Models\FoodCategory;
use Illuminate\Http\JsonResponse;

class FoodCategoryController extends Controller
{
    /** Tree of parent categories with children (for donor/charity forms). */
    public function tree(): JsonResponse
    {
        return response()->json(
            FoodCategory::roots()->with('children')->orderBy('sort_order')->orderBy('name')->get()
        );
    }
}

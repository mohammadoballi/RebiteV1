<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function cities(): JsonResponse
    {
        $cities = City::orderBy('name')->get(['id', 'name']);

        return response()->json($cities);
    }

    public function towns(City $city): JsonResponse
    {
        $towns = $city->towns()->orderBy('name')->get(['id', 'name']);

        return response()->json($towns);
    }
}

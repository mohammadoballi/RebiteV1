<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('id')->get();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings'        => ['required', 'array'],
            'settings.*.key'  => ['required', 'string'],
            'settings.*.value'=> ['required'],
        ]);

        foreach ($request->input('settings') as $item) {
            Setting::set($item['key'], $item['value']);
        }

        return response()->json(['message' => __('Settings updated successfully.')]);
    }
}

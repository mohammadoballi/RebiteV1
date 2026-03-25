<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class DashboardController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function index()
    {
        $data = $this->analyticsService->getDashboardData();

        return view('admin.dashboard', compact('data'));
    }
}

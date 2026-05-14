<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function index(Request $request)
    {
        $year = max(2000, min(2100, (int) $request->query('year', now()->year)));
        $month = max(1, min(12, (int) $request->query('month', now()->month)));

        $data = $this->analyticsService->getDashboardData($year, $month);

        return view('admin.dashboard', compact('data'));
    }
}

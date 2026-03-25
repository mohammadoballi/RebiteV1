<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;

class ReportController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function index()
    {
        $data = $this->analyticsService->getReportsData();

        return view('admin.reports.index', compact('data'));
    }
}

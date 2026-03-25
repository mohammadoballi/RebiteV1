<?php

namespace App\Http\Controllers\Charity;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $charityId = auth()->id();

        $stats = [
            'available' => Donation::available()->count(),
            'my_requests' => DonationRequest::where('charity_id', $charityId)->count(),
            'approved_requests' => DonationRequest::where('charity_id', $charityId)
                ->where('status', 'approved')->count(),
        ];

        $recentRequests = DonationRequest::where('charity_id', $charityId)
            ->with('donation')
            ->latest()
            ->limit(5)
            ->get();

        return view('charity.dashboard', compact('stats', 'recentRequests'));
    }
}

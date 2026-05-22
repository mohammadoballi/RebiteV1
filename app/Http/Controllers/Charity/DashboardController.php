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
        ];

        $recentRequests = DonationRequest::where('charity_id', $charityId)
            ->with(['donation.items'])
            ->latest()
            ->limit(5)
            ->get();

        return view('charity.dashboard', [
            'availableDonations' => $stats['available'],
            'myRequestsCount' => $stats['my_requests'],
            'recentRequests' => $recentRequests,
        ]);
    }
}

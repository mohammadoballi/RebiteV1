<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Models\Donation;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $donations = Donation::where('user_id', $userId);

        $stats = [
            'total' => (clone $donations)->count(),
            'pending' => (clone $donations)->where('status', 'pending')->count(),
            'completed' => (clone $donations)->where('status', 'completed')->count(),
            'accepted' => (clone $donations)->where('status', 'accepted')->count(),
        ];

        $recentDonations = Donation::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get();

        return view('donor.dashboard', compact('stats', 'recentDonations'));
    }
}

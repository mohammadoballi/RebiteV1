<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\DonationAssignment;

class DashboardController extends Controller
{
    public function index()
    {
        $volunteerId = auth()->id();
        $assignments = DonationAssignment::where('volunteer_id', $volunteerId);

        $stats = [
            'total' => (clone $assignments)->count(),
            'pending' => (clone $assignments)->where('status', 'pending')->count(),
            'in_progress' => (clone $assignments)->whereIn('status', ['accepted', 'in_progress'])->count(),
            'completed' => (clone $assignments)->where('status', 'completed')->count(),
        ];

        $recentAssignments = DonationAssignment::where('volunteer_id', $volunteerId)
            ->with('donation')
            ->latest()
            ->limit(5)
            ->get();

        return view('volunteer.dashboard', compact('stats', 'recentAssignments'));
    }
}

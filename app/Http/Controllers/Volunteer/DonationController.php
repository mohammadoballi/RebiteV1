<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationAssignment;
use App\Services\DonationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'city', 'food_type', 'date_from', 'date_to']);
        $donations = $this->donationService->getMarketplaceData($filters);

        $cities = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'donor'))
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city');

        return view('volunteer.donations.index', compact('donations', 'filters', 'cities'));
    }

    public function show(int $id): JsonResponse
    {
        $donation = Donation::with(['donor:id,name,city,phone,avatar', 'items'])->findOrFail($id);

        return response()->json($donation);
    }

    public function selfAssign(Request $request, int $id): JsonResponse
    {
        $donation = Donation::findOrFail($id);

        if ($donation->is_full) {
            return response()->json(
                ['message' => __('This donation already has enough volunteers.')],
                422
            );
        }

        $existing = DonationAssignment::where('donation_id', $id)
            ->where('volunteer_id', auth()->id())
            ->whereIn('status', ['pending', 'accepted', 'in_progress'])
            ->exists();

        if ($existing) {
            return response()->json(
                ['message' => __('You are already assigned to this donation.')],
                422
            );
        }

        $type = auth()->user()->role_type ?? 'delivery';

        DonationAssignment::create([
            'donation_id'     => $donation->id,
            'volunteer_id'    => auth()->id(),
            'assignment_type' => $type,
            'status'          => 'accepted',
        ]);

        $donation->increment('volunteers_count');

        auth()->user()->addPoints(5);

        return response()->json([
            'message' => __('You have been assigned to this donation successfully.'),
        ], 201);
    }
}

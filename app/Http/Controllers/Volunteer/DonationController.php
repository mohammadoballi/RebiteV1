<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Donation;
use App\Models\DonationAssignment;
use App\Models\DonationRequest;
use App\Models\Setting;
use App\Models\Town;
use App\Services\DonationService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService,
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();
        $filters = $request->only(['search', 'city_id', 'town_id', 'food_type', 'date_from', 'date_to']);

        if (!$request->has('city_id') && !$request->has('search') && !$request->has('food_type')) {
            if ($user->city_id) {
                $filters['city_id'] = $user->city_id;
            }
            if ($user->town_id) {
                $filters['town_id'] = $user->town_id;
            }
        }

        $volunteerType = $user->role_type ?? 'delivery';
        $filters['volunteer_type'] = $volunteerType;
        $donations = $this->donationService->getMarketplaceData($filters, forVolunteerBrowse: true);

        $cities = City::orderBy('name')->get();
        $towns = [];
        $selectedCityId = $filters['city_id'] ?? null;
        if ($selectedCityId) {
            $towns = Town::where('city_id', $selectedCityId)->orderBy('name')->get();
        }

        $assignedDonationIds = DonationAssignment::where('volunteer_id', auth()->id())
            ->whereIn('status', ['pending', 'accepted', 'in_progress'])
            ->pluck('donation_id')
            ->toArray();

        return view('volunteer.donations.index', compact('donations', 'filters', 'cities', 'towns', 'assignedDonationIds'));
    }

    public function show(int $id): JsonResponse
    {
        $donation = Donation::with([
            'donor:id,name,city,city_id,town_id,phone,avatar',
            'items',
            'cityRelation:id,name',
            'town:id,name',
        ])
            ->withExists('approvedCharityRequest')
            ->withExists('charityLinkedAssignments')
            ->findOrFail($id);

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

        $approvedRequest = DonationRequest::where('donation_id', $donation->id)
            ->where('status', DonationRequest::STATUS_APPROVED)
            ->first();

        $assignment = DonationAssignment::create([
            'donation_id'          => $donation->id,
            'donation_request_id'   => $approvedRequest?->id,
            'volunteer_id'         => auth()->id(),
            'assignment_type'      => $type,
            'status'               => 'accepted',
        ]);

        $donation->increment('volunteers_count');

        auth()->user()->addPoints(Setting::getInt('volunteer_signup_points', 5));

        $this->notificationService->notifyVolunteerAssigned($assignment);
        $this->notificationService->notifyDonorVolunteerAssigned($assignment);

        return response()->json([
            'message' => __('You have been assigned to this donation successfully.'),
        ], 201);
    }
}

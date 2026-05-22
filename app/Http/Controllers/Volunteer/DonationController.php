<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Donation;
use App\Models\DonationAssignment;
use App\Models\DonationRequest;
use App\Models\FoodCategory;
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
        $filters = $request->only(['city_id', 'town_id', 'food_type', 'food_category_id', 'food_category_parent_id', 'date_from', 'date_to']);

        if (!$request->has('city_id') && !$request->has('food_type') && !$request->has('food_category_id') && !$request->has('food_category_parent_id')) {
            if ($user->city_id) {
                $filters['city_id'] = $user->city_id;
            }
            if ($user->town_id) {
                $filters['town_id'] = $user->town_id;
            }
        }

        if ($request->filled('assignment_type') && in_array($request->assignment_type, ['delivery', 'packaging'], true)) {
            $filters['volunteer_type'] = $request->assignment_type;
        }

        $donations = $this->donationService->getMarketplaceData($filters, forVolunteerBrowse: true);
        $defaultAssignmentType = in_array($user->role_type, ['delivery', 'packaging'], true)
            ? $user->role_type
            : 'delivery';

        $cities = City::orderBy('name')->get();
        $towns = [];
        $selectedCityId = $filters['city_id'] ?? null;
        if ($selectedCityId) {
            $towns = Town::where('city_id', $selectedCityId)->orderBy('name')->get();
        }

        $foodCategoryParents = FoodCategory::roots()->with('children')->get();

        $assignedDonationIds = DonationAssignment::where('volunteer_id', auth()->id())
            ->whereIn('status', ['pending', 'accepted', 'in_progress'])
            ->pluck('donation_id')
            ->toArray();

        return view('volunteer.donations.index', compact(
            'donations',
            'filters',
            'cities',
            'towns',
            'assignedDonationIds',
            'foodCategoryParents',
            'defaultAssignmentType'
        ));
    }

    public function show(int $id): JsonResponse
    {
        $donation = Donation::with([
            'donor:id,name,city,city_id,town_id,phone,avatar',
            'items',
            'cityRelation:id,name',
            'town:id,name',
            'foodCategory.parent:id,name',
        ])
            ->withExists('approvedCharityRequest')
            ->withExists('charityLinkedAssignments')
            ->withCount([
                'assignments as delivery_assignments_count' => fn ($q) => $q
                    ->where('assignment_type', DonationAssignment::TYPE_DELIVERY)
                    ->whereNotIn('status', [DonationAssignment::STATUS_CANCELLED]),
                'assignments as packaging_assignments_count' => fn ($q) => $q
                    ->where('assignment_type', DonationAssignment::TYPE_PACKAGING)
                    ->whereNotIn('status', [DonationAssignment::STATUS_CANCELLED]),
            ])
            ->findOrFail($id);

        $user = auth()->user();
        $defaultType = in_array($user->role_type, ['delivery', 'packaging'], true)
            ? $user->role_type
            : 'delivery';

        $donation->setAttribute('default_assignment_type', $defaultType);
        $donation->setAttribute('can_assign_delivery', $donation->hasOpenSlotForType(DonationAssignment::TYPE_DELIVERY));
        $donation->setAttribute('can_assign_packaging', $donation->hasOpenSlotForType(DonationAssignment::TYPE_PACKAGING));

        return response()->json($donation);
    }

    public function selfAssign(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'assignment_type' => ['required', 'in:delivery,packaging'],
        ]);

        $donation = Donation::findOrFail($id);
        $user = auth()->user();
        $type = $request->input('assignment_type');

        if ($donation->volunteersNeededForType($type) <= 0) {
            return response()->json(
                ['message' => __('This donation does not need :type volunteers.', ['type' => $type])],
                422
            );
        }

        if ($donation->isFullForType($type)) {
            return response()->json(
                ['message' => __('All :type volunteer slots for this donation are filled.', ['type' => $type])],
                422
            );
        }

        if ($donation->volunteers_count >= $donation->volunteers_needed) {
            return response()->json(
                ['message' => __('This donation already has enough volunteers.')],
                422
            );
        }

        $existing = DonationAssignment::where('donation_id', $id)
            ->where('volunteer_id', $user->id)
            ->whereIn('status', ['pending', 'accepted', 'in_progress'])
            ->exists();

        if ($existing) {
            return response()->json(
                ['message' => __('You are already assigned to this donation.')],
                422
            );
        }

        $approvedRequest = DonationRequest::where('donation_id', $donation->id)
            ->when($donation->accepted_charity_id, fn ($q) => $q->where('charity_id', $donation->accepted_charity_id))
            ->first();

        $assignment = DonationAssignment::create([
            'donation_id' => $donation->id,
            'donation_request_id' => $approvedRequest?->id,
            'volunteer_id' => auth()->id(),
            'assignment_type' => $type,
            'status' => 'accepted',
        ]);

        $donation->increment('volunteers_count');

        auth()->user()->addPoints(Setting::getInt('volunteer_signup_points', 5));

        $this->notificationService->notifyVolunteerAssigned($assignment);
        $this->notificationService->notifyDonorVolunteerAssigned($assignment);

        return response()->json([
            'message' => __('You have been assigned to this donation as :type.', ['type' => ucfirst($type)]),
            'assignment_type' => $type,
        ], 201);
    }
}

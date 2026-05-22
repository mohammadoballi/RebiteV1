<?php

namespace App\Http\Controllers\Charity;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Donation;
use App\Models\DonationAssignment;
use App\Models\FoodCategory;
use App\Models\Rating;
use App\Models\Town;
use App\Models\User;
use App\Services\DonationRequestService;
use App\Services\DonationService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Yajra\DataTables\Facades\DataTables;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService,
        protected DonationRequestService $donationRequestService,
        protected NotificationService $notificationService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['city_id', 'town_id', 'food_type', 'food_category_id', 'food_category_parent_id', 'date_from', 'date_to']);
        $donations = $this->donationService->getMarketplaceData($filters);

        $cities = City::orderBy('name')->get();
        $towns = [];
        $selectedCityId = $filters['city_id'] ?? null;
        if ($selectedCityId) {
            $towns = Town::where('city_id', $selectedCityId)->orderBy('name')->get();
        }

        $foodCategoryParents = FoodCategory::roots()->with('children')->get();

        $requestedDonationIds = Donation::query()
            ->where('accepted_charity_id', auth()->id())
            ->pluck('id')
            ->toArray();

        return view('charity.donations.index', compact('donations', 'filters', 'cities', 'towns', 'requestedDonationIds', 'foodCategoryParents'));
    }

    public function show(int $id): JsonResponse
    {
        $charityId = (int) auth()->id();

        $donation = Donation::with([
            'donor:id,name,city,city_id,town_id,phone,avatar',
            'items',
            'assignments' => fn ($q) => $q->where('status', '!=', DonationAssignment::STATUS_CANCELLED),
            'assignments.volunteer:id,name',
            'cityRelation:id,name',
            'town:id,name',
            'foodCategory.parent:id,name',
            'acceptedCharity:id,name',
        ])->findOrFail($id);

        $ratedUserIds = Rating::query()
            ->where('rater_id', $charityId)
            ->where('donation_id', $donation->id)
            ->where('rateable_type', User::class)
            ->pluck('rateable_id')
            ->all();

        $hasApprovedRequest = (int) $donation->accepted_charity_id === $charityId;

        $donation->setAttribute('rated_user_ids', $ratedUserIds);
        $donation->setAttribute('can_accept', $this->canCharityAccept($donation, $charityId));
        $donation->setAttribute('is_claimed_by_me', $hasApprovedRequest);
        $requiredRateableIds = $this->getRequiredRateableIds($donation);
        $allRated = collect($requiredRateableIds)->every(fn (int $id) => in_array($id, $ratedUserIds, true));
        $canComplete = $hasApprovedRequest
            && $donation->status === Donation::STATUS_IN_PROGRESS
            && $allRated;
        $donation->setAttribute('required_rateable_ids', $requiredRateableIds);
        $donation->setAttribute('all_rated', $allRated);
        $donation->setAttribute('can_complete', $canComplete);

        return response()->json($donation);
    }

    public function accept(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $donation = Donation::findOrFail($id);
        $charityId = (int) auth()->id();

        if (!$this->canCharityAccept($donation, $charityId)) {
            if ($donation->accepted_charity_id && $donation->accepted_charity_id !== $charityId) {
                return response()->json(
                    ['message' => __('This donation has already been accepted by another charity.')],
                    409
                );
            }

            if ((int) $donation->accepted_charity_id === $charityId) {
                return response()->json(
                    ['message' => __('You have already accepted this donation.')],
                    409
                );
            }

            return response()->json(
                ['message' => __('This donation is no longer available.')],
                422
            );
        }

        try {
            $donationRequest = $this->donationRequestService->charityAcceptDonation(
                $donation->id,
                $charityId,
                $request->input('message')
            );
        } catch (RuntimeException $e) {
            $status = str_contains($e->getMessage(), 'already been accepted') ? 409 : 422;

            return response()->json(['message' => $e->getMessage()], $status);
        }

        $this->notificationService->notifyDonationClaimedByCharity($donationRequest);

        return response()->json([
            'message' => __('Donation accepted successfully.'),
            'request' => $donationRequest,
            'donation_status' => Donation::STATUS_IN_PROGRESS,
        ], 201);
    }

    public function complete(int $id): JsonResponse
    {
        $charityId = (int) auth()->id();
        $donation = Donation::with([
            'donor:id,name',
            'assignments' => fn ($q) => $q->where('status', '!=', DonationAssignment::STATUS_CANCELLED),
        ])->findOrFail($id);

        $hasApprovedRequest = (int) $donation->accepted_charity_id === $charityId;

        if (! $hasApprovedRequest) {
            return response()->json(['message' => __('You can only complete donations you have accepted.')], 403);
        }

        if ($donation->status !== Donation::STATUS_IN_PROGRESS) {
            return response()->json(['message' => __('Only in-progress donations can be completed.')], 422);
        }

        $requiredRateableIds = $this->getRequiredRateableIds($donation);
        $ratedUserIds = Rating::query()
            ->where('rater_id', $charityId)
            ->where('donation_id', $donation->id)
            ->where('rateable_type', User::class)
            ->pluck('rateable_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $allRated = collect($requiredRateableIds)->every(fn (int $userId) => in_array($userId, $ratedUserIds, true));
        if (! $allRated) {
            return response()->json(['message' => __('Rate the donor and all assigned volunteers before completing this donation.')], 422);
        }

        $donation->update(['status' => Donation::STATUS_COMPLETED]);

        $this->notificationService->notifyDonationStatusChanged($donation->fresh());

        return response()->json([
            'message' => __('Donation marked as completed.'),
            'status' => Donation::STATUS_COMPLETED,
        ]);
    }

    public function myRequests()
    {
        return view('charity.requests.index');
    }

    public function myRequestsDatatable(): JsonResponse
    {
        $charityId = auth()->id();

        $query = Donation::query()
            ->where('accepted_charity_id', $charityId)
            ->with('items');

        return DataTables::eloquent($query)
            ->addColumn('donation_food_type', function (Donation $d) {
                return $d->items_summary ?: ($d->food_type ?? '-');
            })
            ->addColumn('donation_quantity', function (Donation $d) {
                return $d->quantities_summary ?: trim(($d->quantity ?? '-') . ' ' . ($d->quantity_unit ?? ''));
            })
            ->addColumn('display_status', function (Donation $d) {
                if ($d->status === Donation::STATUS_COMPLETED) {
                    return __('donations.completed');
                }

                return __('donations.in_progress');
            })
            ->addColumn('actions', function (Donation $d) {
                return '<button class="btn btn-sm btn-outline-success btn-view-request" data-donation-id="'.$d->id.'" title="View"><i class="fas fa-eye"></i></button>';
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    private function canCharityAccept(Donation $donation, int $charityId): bool
    {
        if ($donation->status !== Donation::STATUS_PENDING) {
            return false;
        }

        if ($donation->accepted_charity_id !== null) {
            return false;
        }

        return true;
    }

    /**
     * @return array<int,int>
     */
    private function getRequiredRateableIds(Donation $donation): array
    {
        $donorId = (int) ($donation->user_id ?? 0);
        $volunteerIds = $donation->assignments
            ->pluck('volunteer_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        return collect(array_merge([$donorId], $volunteerIds))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}

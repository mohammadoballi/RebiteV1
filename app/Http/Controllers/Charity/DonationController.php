<?php

namespace App\Http\Controllers\Charity;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Donation;
use App\Models\DonationRequest;
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

        $requestedDonationIds = DonationRequest::where('charity_id', auth()->id())
            ->where('status', DonationRequest::STATUS_APPROVED)
            ->pluck('donation_id')
            ->toArray();

        return view('charity.donations.index', compact('donations', 'filters', 'cities', 'towns', 'requestedDonationIds', 'foodCategoryParents'));
    }

    public function show(int $id): JsonResponse
    {
        $charityId = (int) auth()->id();

        $donation = Donation::with([
            'donor:id,name,city,city_id,town_id,phone,avatar',
            'items',
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

        $hasApprovedRequest = DonationRequest::query()
            ->where('donation_id', $donation->id)
            ->where('charity_id', $charityId)
            ->where('status', DonationRequest::STATUS_APPROVED)
            ->exists();

        $donation->setAttribute('rated_user_ids', $ratedUserIds);
        $donation->setAttribute('can_accept', $this->canCharityAccept($donation, $charityId));
        $donation->setAttribute('is_claimed_by_me', $hasApprovedRequest);

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

            if (DonationRequest::query()
                ->where('donation_id', $donation->id)
                ->where('charity_id', $charityId)
                ->where('status', DonationRequest::STATUS_APPROVED)
                ->exists()) {
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
            'donation_status' => Donation::STATUS_ACCEPTED,
        ], 201);
    }

    public function myRequests()
    {
        return view('charity.requests.index');
    }

    public function myRequestsDatatable(): JsonResponse
    {
        $charityId = auth()->id();

        $query = DonationRequest::where('charity_id', $charityId)
            ->with(['donation.items']);

        return DataTables::eloquent($query)
            ->addColumn('donation_food_type', function ($r) {
                $donation = $r->donation;
                if (!$donation) {
                    return '-';
                }

                return $donation->items_summary ?: ($donation->food_type ?? '-');
            })
            ->addColumn('donation_quantity', function ($r) {
                $donation = $r->donation;
                if (!$donation) {
                    return '-';
                }

                return $donation->quantities_summary ?: trim(($donation->quantity ?? '-') . ' ' . ($donation->quantity_unit ?? ''));
            })
            ->addColumn('display_status', function ($r) use ($charityId) {
                if ($r->status !== DonationRequest::STATUS_APPROVED) {
                    return ucfirst($r->status);
                }

                $donorId = $r->donation?->user_id;
                if (!$donorId) {
                    return __('Accepted');
                }

                $rated = Rating::query()
                    ->where('rater_id', $charityId)
                    ->where('donation_id', $r->donation_id)
                    ->where('rateable_id', $donorId)
                    ->where('rateable_type', User::class)
                    ->exists();

                return $rated ? __('Rated') : __('Accepted');
            })
            ->addColumn('actions', function ($r) {
                $badge = '<span class="badge bg-' . match ($r->status) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary'
                } . '">' . ucfirst($r->status) . '</span>';

                $badge .= ' <button class="btn btn-sm btn-outline-success btn-view-request" data-donation-id="'.$r->donation_id.'" title="View"><i class="fas fa-eye"></i></button>';

                return $badge;
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

        if (DonationRequest::query()
            ->where('donation_id', $donation->id)
            ->where('status', DonationRequest::STATUS_APPROVED)
            ->exists()) {
            return false;
        }

        if (DonationRequest::query()
            ->where('donation_id', $donation->id)
            ->where('charity_id', $charityId)
            ->where('status', DonationRequest::STATUS_APPROVED)
            ->exists()) {
            return false;
        }

        return true;
    }
}

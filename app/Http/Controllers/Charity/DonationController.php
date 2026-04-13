<?php

namespace App\Http\Controllers\Charity;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Donation;
use App\Models\DonationRequest;
use App\Models\Setting;
use App\Models\Town;
use App\Services\DonationRequestService;
use App\Services\DonationService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        $filters = $request->only(['search', 'city_id', 'town_id', 'food_type', 'date_from', 'date_to']);
        $donations = $this->donationService->getMarketplaceData($filters);

        $cities = City::orderBy('name')->get();
        $towns = [];
        $selectedCityId = $filters['city_id'] ?? null;
        if ($selectedCityId) {
            $towns = Town::where('city_id', $selectedCityId)->orderBy('name')->get();
        }

        $requestedDonationIds = DonationRequest::where('charity_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->pluck('donation_id')
            ->toArray();

        return view('charity.donations.index', compact('donations', 'filters', 'cities', 'towns', 'requestedDonationIds'));
    }

    public function show(int $id): JsonResponse
    {
        $donation = Donation::with(['donor:id,name,city,city_id,town_id,phone,avatar', 'items', 'assignments.volunteer:id,name', 'cityRelation:id,name', 'town:id,name'])->findOrFail($id);

        return response()->json($donation);
    }

    public function request(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $donation = Donation::findOrFail($id);

        if ($donation->is_full) {
            return response()->json(
                ['message' => __('This donation already has enough volunteers.')],
                422
            );
        }

        $existing = DonationRequest::where('donation_id', $id)
            ->where('charity_id', auth()->id())
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($existing) {
            return response()->json(
                ['message' => __('You have already requested this donation.')],
                422
            );
        }

        $maxPending = Setting::getInt('max_pending_requests_per_charity', 3);
        $pendingCount = DonationRequest::where('charity_id', auth()->id())
            ->where('status', 'pending')
            ->count();

        if ($pendingCount >= $maxPending) {
            return response()->json(
                ['message' => __('You have reached the maximum number of pending requests (:max). Please wait for approval.', ['max' => $maxPending])],
                422
            );
        }

        $donationRequest = $this->donationRequestService->create([
            'donation_id' => $donation->id,
            'charity_id'  => auth()->id(),
            'status'      => 'pending',
            'message'     => $request->input('message'),
        ]);

        $this->notificationService->notifyNewDonationRequest($donationRequest);

        return response()->json([
            'message' => __('Donation requested successfully.'),
            'request' => $donationRequest,
        ], 201);
    }

    public function myRequests()
    {
        return view('charity.requests.index');
    }

    public function myRequestsDatatable(): JsonResponse
    {
        $query = DonationRequest::where('charity_id', auth()->id())
            ->with('donation');

        return DataTables::eloquent($query)
            ->addColumn('donation_food_type', fn ($r) => $r->donation->food_type ?? '-')
            ->addColumn('donation_quantity', fn ($r) => ($r->donation->quantity ?? '-') . ' ' . ($r->donation->quantity_unit ?? ''))
            ->addColumn('actions', function ($r) {
                $badge = '<span class="badge bg-' . match($r->status) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary'
                } . '">' . ucfirst($r->status) . '</span>';

                if ($r->status === 'approved') {
                    $badge .= ' <button class="btn btn-sm btn-outline-success btn-view-request" data-donation-id="'.$r->donation_id.'" title="View"><i class="fas fa-eye"></i></button>';
                }
                return $badge;
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
}

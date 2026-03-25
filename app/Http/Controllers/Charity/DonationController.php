<?php

namespace App\Http\Controllers\Charity;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationRequest;
use App\Services\DonationRequestService;
use App\Services\DonationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService,
        protected DonationRequestService $donationRequestService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'city', 'food_type', 'date_from', 'date_to']);
        $donations = $this->donationService->getMarketplaceData($filters);

        $cities = \App\Models\User::whereHas('roles', fn($q) => $q->where('name', 'donor'))
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city');

        return view('charity.donations.index', compact('donations', 'filters', 'cities'));
    }

    public function show(int $id): JsonResponse
    {
        $donation = Donation::with(['donor:id,name,city,phone,avatar', 'items'])->findOrFail($id);

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

        $donationRequest = $this->donationRequestService->create([
            'donation_id' => $donation->id,
            'charity_id'  => auth()->id(),
            'status'      => 'pending',
            'message'     => $request->input('message'),
        ]);

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
                return '<span class="badge bg-' . match($r->status) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary'
                } . '">' . ucfirst($r->status) . '</span>';
            })
            ->rawColumns(['actions'])
            ->toJson();
    }
}

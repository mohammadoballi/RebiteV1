<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Donation\StoreDonationRequest;
use App\Http\Requests\Donation\UpdateDonationRequest;
use App\Models\Donation;
use App\Models\FoodCategory;
use App\Models\Rating;
use App\Models\User;
use App\Services\DonationService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService,
        protected NotificationService $notificationService
    ) {}

    public function index()
    {
        $foodCategoryParents = FoodCategory::roots()->with('children')->get();

        return view('donor.donations.index', compact('foodCategoryParents'));
    }

    public function datatable(): JsonResponse
    {
        $query = $this->donationService->getDatatableData(auth()->id());

        return DataTables::eloquent($query)
            ->addColumn('food_category_label', function (Donation $d) {
                $d->loadMissing('foodCategory.parent');
                if (!$d->foodCategory) {
                    return '—';
                }
                $parent = $d->foodCategory->parent;

                return ($parent ? $parent->name.' — ' : '').$d->foodCategory->name;
            })
            ->addColumn('items_summary', function (Donation $d) {
                $d->loadMissing('items');
                return $d->items_summary;
            })
            ->addColumn('quantities_summary', function (Donation $d) {
                $d->loadMissing('items');

                return $d->quantities_summary ?: '—';
            })
            ->addColumn('volunteer_info', function (Donation $d) {
                $color = $d->volunteers_count >= $d->volunteers_needed ? 'success' : 'warning';
                $delivery = $d->delivery_volunteers_needed ?? 0;
                $packaging = $d->packaging_volunteers_needed ?? 0;
                return '<span class="badge bg-'.$color.'">'.$d->volunteers_count.'/'.$d->volunteers_needed.'</span>'
                     . '<br><small class="text-muted"><i class="fas fa-truck"></i> '.$delivery.' · <i class="fas fa-box"></i> '.$packaging.'</small>';
            })
            ->addColumn('actions', function (Donation $d) {
                $btns = '<button class="btn btn-sm btn-outline-success btn-view-donation" data-id="'.$d->id.'" title="View"><i class="fas fa-eye"></i></button> ';
                if ($d->status === 'pending') {
                    $btns .= '<button class="btn btn-sm btn-outline-primary btn-edit-donation" data-id="'.$d->id.'" title="Edit"><i class="fas fa-edit"></i></button> ';
                    $btns .= '<button class="btn btn-sm btn-outline-danger btn-delete-donation" data-id="'.$d->id.'" title="Delete"><i class="fas fa-trash"></i></button>';
                }
                return $btns;
            })
            ->rawColumns(['volunteer_info', 'actions'])
            ->toJson();
    }

    public function store(StoreDonationRequest $request): JsonResponse
    {
        $donor = auth()->user();
        $data = $request->validated();
        $data['user_id'] = $donor->id;
        $data['city_id'] = $donor->city_id;
        $data['town_id'] = $donor->town_id;
        $data['pickup_address'] = trim((string) ($donor->address ?: $donor->city ?: __('Address not provided')));

        $donation = $this->donationService->create($data);

        $this->notificationService->notifyAdminsNewDonation($donation);

        $donor->refresh();

        return response()->json([
            'message'        => __('Donation created successfully.'),
            'donation'       => $donation,
            'points_awarded' => $donation->points_awarded ?? 0,
            'donor_points'   => $donor->points,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $donation = Donation::where('user_id', auth()->id())
            ->with(['items', 'requests.charity', 'assignments.volunteer', 'cityRelation:id,name', 'town:id,name', 'foodCategory.parent:id,name', 'acceptedCharity:id,name'])
            ->findOrFail($id);

        $charityRatings = Rating::query()
            ->with('rater:id,name')
            ->where('donation_id', $donation->id)
            ->where('rateable_type', User::class)
            ->where('rateable_id', $donation->user_id)
            ->when($donation->accepted_charity_id, fn ($q) => $q->where('rater_id', $donation->accepted_charity_id))
            ->latest()
            ->get();

        $donation->setAttribute('charity_ratings', $charityRatings);

        return response()->json($donation);
    }

    public function update(UpdateDonationRequest $request, int $id): JsonResponse
    {
        $donation = Donation::where('user_id', auth()->id())->findOrFail($id);

        $data = $request->validated();

        $this->donationService->update($donation->id, $data);

        return response()->json(['message' => __('Donation updated successfully.')]);
    }

    public function destroy(int $id): JsonResponse
    {
        $donation = Donation::where('user_id', auth()->id())->findOrFail($id);

        $this->donationService->delete($donation->id);

        return response()->json(['message' => __('Donation deleted.')]);
    }
}

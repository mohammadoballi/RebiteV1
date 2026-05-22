<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\DonationAssignment;
use App\Models\Rating;
use App\Models\User;
use App\Services\AssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AssignmentController extends Controller
{
    public function __construct(
        protected AssignmentService $assignmentService
    ) {}

    public function index()
    {
        return view('volunteer.assignments.index');
    }

    public function datatable(): JsonResponse
    {
        $query = DonationAssignment::where('volunteer_id', auth()->id())
            ->with(['donation.cityRelation:id,name', 'donation.town:id,name']);

        return DataTables::eloquent($query)
            ->addColumn('donation_food_type', fn ($a) => $a->donation->food_type ?? '-')
            ->addColumn('pickup_city', function ($a) {
                $d = $a->donation;
                if (!$d) {
                    return '-';
                }
                $parts = array_filter([$d->cityRelation?->name, $d->town?->name]);

                return $parts ? implode(' / ', $parts) : '-';
            })
            ->addColumn('pickup_address_short', fn ($a) => $a->donation ? \Illuminate\Support\Str::limit((string) $a->donation->pickup_address, 48) : '-')
            ->addColumn('actions', function (DonationAssignment $a) {
                return '<button class="btn btn-sm btn-outline-success btn-view-assignment" data-id="'.$a->id.'" title="View"><i class="fas fa-eye"></i></button>';
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function show(int $id): JsonResponse
    {
        $assignment = DonationAssignment::where('volunteer_id', auth()->id())
            ->with(['donation.donor', 'donation.cityRelation:id,name', 'donation.town:id,name', 'donation.foodCategory.parent:id,name', 'donationRequest.charity'])
            ->findOrFail($id);

        $charityRating = Rating::query()
            ->with('rater:id,name')
            ->where('donation_id', $assignment->donation_id)
            ->where('rateable_type', User::class)
            ->where('rateable_id', (int) auth()->id())
            ->when($assignment->donation?->accepted_charity_id, fn ($q) => $q->where('rater_id', $assignment->donation->accepted_charity_id))
            ->latest()
            ->first();

        $assignment->setAttribute('charity_rating', $charityRating);

        return response()->json($assignment);
    }

    public function accept(int $id): JsonResponse
    {
        $assignment = DonationAssignment::where('volunteer_id', auth()->id())
            ->findOrFail($id);

        $this->assignmentService->updateStatus($assignment->id, DonationAssignment::STATUS_ACCEPTED);

        return response()->json(['message' => __('Assignment accepted.')]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $assignment = DonationAssignment::where('volunteer_id', auth()->id())
            ->findOrFail($id);

        $this->assignmentService->updateStatus($assignment->id, $request->input('status'));

        return response()->json(['message' => __('Assignment status updated.')]);
    }

    public function markPickedUp(int $id): JsonResponse
    {
        $assignment = DonationAssignment::where('volunteer_id', auth()->id())
            ->findOrFail($id);

        $this->assignmentService->markPickedUp($assignment->id);

        return response()->json(['message' => __('Marked as picked up.')]);
    }

    public function markDelivered(int $id): JsonResponse
    {
        $assignment = DonationAssignment::where('volunteer_id', auth()->id())
            ->findOrFail($id);

        $this->assignmentService->markDelivered($assignment->id);

        return response()->json(['message' => __('Marked as delivered.')]);
    }
}

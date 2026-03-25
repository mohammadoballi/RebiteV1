<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\DonationAssignment;
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
            ->with('donation');

        return DataTables::eloquent($query)
            ->addColumn('donation_food_type', fn ($a) => $a->donation->food_type ?? '-')
            ->addColumn('actions', function (DonationAssignment $a) {
                return '<button class="btn btn-sm btn-outline-success btn-view-assignment" data-id="'.$a->id.'" title="View"><i class="fas fa-eye"></i></button>';
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function show(int $id): JsonResponse
    {
        $assignment = DonationAssignment::where('volunteer_id', auth()->id())
            ->with(['donation.donor', 'donationRequest.charity'])
            ->findOrFail($id);

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

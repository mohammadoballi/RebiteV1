<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\DonationService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService,
        protected NotificationService $notificationService
    ) {}

    public function index()
    {
        return view('admin.donations.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        $query = $this->donationService->getDatatableData();

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        return DataTables::eloquent($query)
            ->addColumn('donor_name', fn (Donation $d) => $d->donor->name ?? '-')
            ->addColumn('items_summary', function (Donation $d) {
                $d->loadMissing('items');

                return $d->items_summary;
            })
            ->addColumn('quantities_summary', function (Donation $d) {
                $d->loadMissing('items');

                return $d->quantities_summary ?: '—';
            })
            ->addColumn('actions', function (Donation $d) {
                return '<button class="btn btn-sm btn-outline-success btn-view-donation" data-id="'.$d->id.'" title="View"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-danger btn-delete-donation" data-id="'.$d->id.'" title="Delete"><i class="fas fa-trash"></i></button>';
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function show(int $id): JsonResponse
    {
        $donation = Donation::with([
            'donor',
            'items',
            'requests.charity',
            'assignments.volunteer',
            'cityRelation:id,name',
            'town:id,name',
            'foodCategory.parent:id,name',
        ])
            ->findOrFail($id);

        return response()->json($donation);
    }

    public function approve(int $id): JsonResponse
    {
        $donation = Donation::findOrFail($id);

        if ($donation->accepted_charity_id) {
            return response()->json([
                'message' => __('This donation has already been claimed by a charity.'),
            ], 422);
        }

        $donation->update([
            'admin_approved_at' => now(),
            'status' => Donation::STATUS_PENDING,
        ]);

        $this->notificationService->notifyDonationStatusChanged($donation->fresh());

        return response()->json([
            'message' => __('Donation approved and published for charities.'),
            'donation' => $donation->fresh(),
        ]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $donation = Donation::findOrFail($id);
        $status = $request->input('status');

        // Admin "accepted" without a charity claim = publish to marketplace (stay pending).
        if ($status === Donation::STATUS_ACCEPTED && $donation->accepted_charity_id === null) {
            $donation->update([
                'admin_approved_at' => now(),
                'status' => Donation::STATUS_PENDING,
            ]);
            $this->notificationService->notifyDonationStatusChanged($donation->fresh());

            return response()->json([
                'message' => __('Donation approved and published for charities.'),
                'status' => Donation::STATUS_PENDING,
            ]);
        }

        $this->donationService->updateStatus($id, $status);

        if ($donation = Donation::find($id)) {
            $this->notificationService->notifyDonationStatusChanged($donation);
        }

        return response()->json(['message' => __('Donation status updated.')]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->donationService->delete($id);

        return response()->json(['message' => __('Donation deleted.')]);
    }
}

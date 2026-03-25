<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\DonationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService
    ) {}

    public function index()
    {
        return view('admin.donations.index');
    }

    public function datatable(Request $request): JsonResponse
    {
        $query = $this->donationService->getDatatableData();

        return DataTables::eloquent($query)
            ->addColumn('donor_name', fn (Donation $d) => $d->donor->name ?? '-')
            ->addColumn('actions', function (Donation $d) {
                return '<button class="btn btn-sm btn-outline-success btn-view-donation" data-id="'.$d->id.'" title="View"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-danger btn-delete-donation" data-id="'.$d->id.'" title="Delete"><i class="fas fa-trash"></i></button>';
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function show(int $id): JsonResponse
    {
        $donation = Donation::with(['donor', 'requests.charity', 'assignments.volunteer'])
            ->findOrFail($id);

        return response()->json($donation);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'string'],
        ]);

        $this->donationService->updateStatus($id, $request->input('status'));

        return response()->json(['message' => __('Donation status updated.')]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->donationService->delete($id);

        return response()->json(['message' => __('Donation deleted.')]);
    }
}

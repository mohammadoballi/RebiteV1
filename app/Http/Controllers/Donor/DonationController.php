<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Donation\StoreDonationRequest;
use App\Http\Requests\Donation\UpdateDonationRequest;
use App\Models\City;
use App\Models\Donation;
use App\Services\DonationService;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class DonationController extends Controller
{
    public function __construct(
        protected DonationService $donationService
    ) {}

    public function index()
    {
        $cities = City::orderBy('name')->get();

        return view('donor.donations.index', compact('cities'));
    }

    public function datatable(): JsonResponse
    {
        $query = $this->donationService->getDatatableData(auth()->id());

        return DataTables::eloquent($query)
            ->addColumn('items_summary', function (Donation $d) {
                $d->loadMissing('items');
                return $d->items_summary;
            })
            ->addColumn('volunteer_info', function (Donation $d) {
                $color = $d->volunteers_count >= $d->volunteers_needed ? 'success' : 'warning';
                return '<span class="badge bg-'.$color.'">'.$d->volunteers_count.'/'.$d->volunteers_needed.'</span>';
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
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('donations', 'public');
        }

        $donation = $this->donationService->create($data);

        return response()->json([
            'message'  => __('Donation created successfully.'),
            'donation' => $donation,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $donation = Donation::where('user_id', auth()->id())
            ->with(['items', 'requests.charity', 'assignments.volunteer', 'cityRelation:id,name', 'town:id,name'])
            ->findOrFail($id);

        return response()->json($donation);
    }

    public function update(UpdateDonationRequest $request, int $id): JsonResponse
    {
        $donation = Donation::where('user_id', auth()->id())->findOrFail($id);

        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('donations', 'public');
        }

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

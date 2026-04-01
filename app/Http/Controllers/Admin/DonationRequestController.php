<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationRequest;
use App\Services\DonationRequestService;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class DonationRequestController extends Controller
{
    public function __construct(
        protected DonationRequestService $donationRequestService,
        protected NotificationService $notificationService
    ) {}

    public function index()
    {
        return view('admin.donation-requests.index');
    }

    public function datatable(): JsonResponse
    {
        $query = DonationRequest::with(['donation', 'charity:id,name,organization_name']);

        return DataTables::eloquent($query)
            ->addColumn('charity_name', fn ($r) => $r->charity->organization_name ?? $r->charity->name ?? '-')
            ->addColumn('donation_food', fn ($r) => $r->donation->food_type ?? '-')
            ->addColumn('actions', function ($r) {
                if ($r->status !== 'pending') {
                    return '<span class="badge bg-' . match($r->status) {
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary'
                    } . '">' . ucfirst($r->status) . '</span>';
                }

                return '<button class="btn btn-sm btn-success btn-approve-request" data-id="'.$r->id.'" title="Approve"><i class="fas fa-check"></i></button> '
                     . '<button class="btn btn-sm btn-danger btn-reject-request" data-id="'.$r->id.'" title="Reject"><i class="fas fa-times"></i></button>';
            })
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function approve(int $id): JsonResponse
    {
        $this->donationRequestService->approve($id);

        $request = DonationRequest::find($id);
        if ($request) {
            $this->notificationService->notifyCharityRequestApproved($request);
        }

        return response()->json(['message' => __('Request approved successfully.')]);
    }

    public function reject(int $id): JsonResponse
    {
        $this->donationRequestService->reject($id);

        $request = DonationRequest::find($id);
        if ($request) {
            $this->notificationService->notifyCharityRequestRejected($request);
        }

        return response()->json(['message' => __('Request rejected.')]);
    }
}

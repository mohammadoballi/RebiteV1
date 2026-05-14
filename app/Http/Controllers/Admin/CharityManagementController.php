<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationRequest;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Facades\DataTables;

class CharityManagementController extends Controller
{
    /**
     * Admin oversight of charity–donation links (read-only; charities accept donations directly).
     */
    public function index()
    {
        return view('admin.charity-management.index');
    }

    public function datatable(): JsonResponse
    {
        $query = DonationRequest::with(['donation', 'charity:id,name,organization_name']);

        return DataTables::eloquent($query)
            ->addColumn('charity_name', fn ($r) => $r->charity->organization_name ?? $r->charity->name ?? '-')
            ->addColumn('donation_id', fn ($r) => $r->donation_id)
            ->addColumn('donation_food', fn ($r) => $r->donation->food_type ?? '-')
            ->addColumn('message', fn ($r) => \Illuminate\Support\Str::limit((string) $r->message, 80))
            ->addColumn('donation_status', fn ($r) => $r->donation->status ?? '-')
            ->addColumn('status_badge', function ($r) {
                return '<span class="badge bg-' . match ($r->status) {
                    'pending' => 'warning',
                    'approved' => 'success',
                    'rejected' => 'danger',
                    default => 'secondary'
                } . '">' . ucfirst($r->status) . '</span>';
            })
            ->rawColumns(['status_badge'])
            ->toJson();
    }
}

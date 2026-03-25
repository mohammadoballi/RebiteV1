<?php

namespace App\Repositories;

use App\Models\DonationRequest;
use Illuminate\Database\Eloquent\Collection;

class DonationRequestRepository extends BaseRepository
{
    public function __construct(DonationRequest $model)
    {
        parent::__construct($model);
    }

    public function getByCharity(int $charityId): Collection
    {
        return $this->query()
            ->where('charity_id', $charityId)
            ->with('donation')
            ->latest()
            ->get();
    }

    public function getByDonation(int $donationId): Collection
    {
        return $this->query()
            ->where('donation_id', $donationId)
            ->with('charity:id,name,organization_name')
            ->latest()
            ->get();
    }

    public function approve(int $id): bool
    {
        return $this->model->findOrFail($id)->update([
            'status' => 'approved',
        ]);
    }

    public function reject(int $id): bool
    {
        return $this->model->findOrFail($id)->update([
            'status' => 'rejected',
        ]);
    }
}

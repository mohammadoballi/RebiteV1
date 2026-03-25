<?php

namespace App\Services;

use App\Models\DonationRequest;
use App\Repositories\DonationRequestRepository;
use Illuminate\Database\Eloquent\Collection;

class DonationRequestService
{
    public function __construct(
        protected DonationRequestRepository $donationRequestRepository
    ) {}

    public function create(array $data): DonationRequest
    {
        return $this->donationRequestRepository->create($data);
    }

    public function approve(int $id): bool
    {
        $request = $this->donationRequestRepository->findOrFail($id);

        $this->donationRequestRepository
            ->query()
            ->where('donation_id', $request->donation_id)
            ->where('id', '!=', $id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        return $this->donationRequestRepository->approve($id);
    }

    public function reject(int $id): bool
    {
        return $this->donationRequestRepository->reject($id);
    }

    public function getByCharity(int $charityId): Collection
    {
        return $this->donationRequestRepository->getByCharity($charityId);
    }
}

<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationRequest;
use App\Repositories\DonationRequestRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DonationRequestService
{
    public function __construct(
        protected DonationRequestRepository $donationRequestRepository
    ) {}

    public function create(array $data): DonationRequest
    {
        return $this->donationRequestRepository->create($data);
    }

    /**
     * Charity directly claims a donation and moves it to in progress.
     */
    public function charityAcceptDonation(int $donationId, int $charityId, ?string $message = null): DonationRequest
    {
        return DB::transaction(function () use ($donationId, $charityId, $message) {
            /** @var Donation $donation */
            $donation = Donation::query()->lockForUpdate()->findOrFail($donationId);

            if ($donation->accepted_charity_id !== null && (int) $donation->accepted_charity_id !== $charityId) {
                throw new RuntimeException(__('This donation has already been accepted by another charity.'));
            }

            if ((int) $donation->accepted_charity_id === $charityId || $donation->status === Donation::STATUS_IN_PROGRESS) {
                throw new RuntimeException(__('You have already accepted this donation.'));
            }

            if ($donation->status !== Donation::STATUS_PENDING) {
                throw new RuntimeException(__('This donation is no longer available.'));
            }

            $existing = DonationRequest::query()
                ->where('donation_id', $donationId)
                ->where('charity_id', $charityId)
                ->first();

            if ($existing) {
                $existing->update([
                    'status' => DonationRequest::STATUS_PENDING,
                    'message' => $message ?? $existing->message,
                ]);
                $req = $existing->fresh();
            } else {
                $req = DonationRequest::create([
                    'donation_id' => $donationId,
                    'charity_id' => $charityId,
                    'status' => DonationRequest::STATUS_PENDING,
                    'message' => $message,
                ]);
            }

            $donation->update([
                'status' => Donation::STATUS_IN_PROGRESS,
                'accepted_charity_id' => $charityId,
            ]);

            return $req;
        });
    }

    public function getByCharity(int $charityId): Collection
    {
        return $this->donationRequestRepository->getByCharity($charityId);
    }
}
